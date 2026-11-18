<?php
// Buat pembayaran (order_id).
// Bila XENDIT_SECRET_KEY ada di .env: buat invoice Xendit sungguhan (POST /v2/invoices),
// jawab dengan invoice_url, dan order TETAP menunggu_bayar sampai webhook Xendit
// (src/api/webhook/xendit.php) menandainya dibayar.
// Bila kunci TIDAK ada: MODE DEMO — pembayaran dicatat langsung sebagai dibayar.
require_once __DIR__ . '/../../lib/db.php';
require_once __DIR__ . '/../../lib/response.php';
require_once __DIR__ . '/../../lib/auth.php';

$user = auth_user();
if (!$user || ($user['role'] ?? '') !== 'pembeli') {
    json_error('Hanya pembeli yang boleh membayar', 403);
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_error('Metode tidak diizinkan', 405);
}

$input = json_input();
$orderId = (int) ($input['order_id'] ?? 0);
if ($orderId <= 0) {
    json_error('Order tidak ditemukan', 404);
}

try {
    $pdo = db();
    $pdo->beginTransaction();
    $stmt = $pdo->prepare(
        'SELECT o.id, o.buyer_id, o.status, o.total, o.fee, p.nama AS nama_produk, ' .
        'i.nomor AS nomor_invoice, u.email AS email_buyer ' .
        'FROM orders o JOIN produk p ON p.id = o.produk_id ' .
        'LEFT JOIN invoice i ON i.order_id = o.id ' .
        'JOIN users u ON u.id = o.buyer_id WHERE o.id = ? FOR UPDATE'
    );
    $stmt->execute([$orderId]);
    $order = $stmt->fetch();
    if (!$order) {
        $pdo->rollBack();
        json_error('Order tidak ditemukan', 404);
    }
    if ((int) $order['buyer_id'] !== (int) $user['id']) {
        $pdo->rollBack();
        json_error('Order ini bukan milik Anda', 403);
    }
    if ($order['status'] === 'dibayar') {
        $pdo->commit();
        json_ok(['order_id' => $orderId, 'status' => 'dibayar', 'catatan' => 'Order ini sudah dibayar sebelumnya.']);
    }
    if ($order['status'] !== 'menunggu_bayar') {
        $pdo->rollBack();
        json_error('Order ini sudah tidak bisa dibayar lagi', 409);
    }
    // db() sudah memuat .env ke environment (lihat src/lib/db.php),
    // jadi kunci Xendit dibaca dari sana dengan cara yang sama.
    $secretKey = trim((string) (getenv('XENDIT_SECRET_KEY') ?: ''));
    if ($secretKey === '') {
        // MODE DEMO (tanpa kunci Xendit): langsung tandai dibayar, seperti semula.
        $stmt = $pdo->prepare("UPDATE orders SET status = 'dibayar' WHERE id = ?");
        $stmt->execute([$orderId]);
        $stmt = $pdo->prepare("UPDATE invoice SET status = 'dibayar' WHERE order_id = ?");
        $stmt->execute([$orderId]);
        $stmt = $pdo->prepare('INSERT INTO riwayat_status (order_id, status_lama, status_baru, dicatat_oleh) VALUES (?, ?, ?, ?)');
        $stmt->execute([$orderId, 'menunggu_bayar', 'dibayar', (int) $user['id']]);
        $pdo->commit();
        json_ok(['order_id' => $orderId, 'status' => 'dibayar']);
    }
    // MODE XENDIT: jangan ubah apa pun di database. Lepas kunci baris dulu,
    // baru hubungi Xendit di luar transaksi.
    $nomorInvoice = (string) ($order['nomor_invoice'] ?? '');
    if ($nomorInvoice === '') {
        $pdo->rollBack();
        json_error('Nomor invoice order tidak ditemukan', 500);
    }
    $totalBayar = (float) $order['total'] + (float) $order['fee'];
    $namaProduk = (string) $order['nama_produk'];
    $emailBuyer = $order['email_buyer'] ?? null;
    $pdo->rollBack();
} catch (Throwable $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    json_error('Database belum siap. Jalankan migrasi 01 dan 03 dulu.', 500);
}

// Panggil Xendit di luar transaksi database.
$hasil = xendit_buat_invoice($secretKey, $nomorInvoice, $totalBayar, $namaProduk, $orderId, $emailBuyer);
if (!$hasil['ok']) {
    // Pesan Xendit diteruskan apa adanya; status order tetap menunggu_bayar.
    json_error($hasil['error'], 502);
}
json_ok(['order_id' => $orderId, 'status' => 'menunggu_bayar', 'invoice_url' => $hasil['invoice_url']]);

// Buat satu invoice Xendit. Kembalikan ['ok' => true, 'invoice_url' => ...]
// atau ['ok' => false, 'error' => <pesan Xendit apa adanya>].
function xendit_buat_invoice(string $secretKey, string $externalId, float $totalBayar, string $namaProduk, int $orderId, $emailBuyer): array
{
    $body = [
        'external_id' => $externalId,
        // Xendit memakai amount bilangan bulat; total_bayar dibulatkan ke rupiah.
        'amount' => (int) round($totalBayar),
        'currency' => 'IDR',
        'description' => $namaProduk . ' - Order #' . $orderId,
    ];
    if (!empty($emailBuyer)) {
        $body['payer_email'] = $emailBuyer;
    }
    $ch = curl_init('https://api.xendit.co/v2/invoices');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($body, JSON_UNESCAPED_UNICODE),
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_USERPWD => $secretKey . ':',
        CURLOPT_TIMEOUT => 30,
    ]);
    $raw = curl_exec($ch);
    if ($raw === false) {
        $err = curl_error($ch);
        curl_close($ch);
        return ['ok' => false, 'error' => 'Gagal menghubungi Xendit: ' . $err];
    }
    $http = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $data = json_decode($raw, true);
    if ($http >= 200 && $http < 300 && is_array($data) && !empty($data['invoice_url'])) {
        return ['ok' => true, 'invoice_url' => $data['invoice_url']];
    }
    $pesan = '';
    if (is_array($data)) {
        $pesan = (string) ($data['message'] ?? $data['error'] ?? '');
    }
    if ($pesan === '') {
        $pesan = is_string($raw) && $raw !== '' ? $raw : 'Xendit menjawab HTTP ' . $http . ' tanpa pesan.';
    }
    return ['ok' => false, 'error' => $pesan];
}
