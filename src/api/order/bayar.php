<?php
// Buat pembayaran (order_id).
// Bila XENDIT_SECRET_KEY ada di .env: buat invoice Xendit sungguhan (POST /v2/invoices),
// jawab dengan invoice_url, dan order TETAP menunggu_bayar sampai webhook Xendit
// (src/api/webhook/xendit.php) menandainya dibayar.
// Bila kunci TIDAK ada: pembayaran DITOLAK, kecuali DEMO_MODE=true di .env secara
// eksplisit (hanya untuk latihan lokal — jangan pernah aktif di server bersama).
// Mode demo diam-diam dilarang: tanpa kunci dan tanpa flag, order tidak boleh
// berubah jadi dibayar.
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
    // Session menyimpan pengguna.id_pengguna, bukan id profil pembeli.
    $stmt = $pdo->prepare('SELECT id_pembeli FROM pengguna WHERE id_pengguna = ?');
    $stmt->execute([(int) $user['id']]);
    $profil = $stmt->fetch();
    $idPembeli = $profil ? (int) ($profil['id_pembeli'] ?? 0) : 0;
    if ($idPembeli <= 0) {
        json_error('Profil pembeli tidak ditemukan', 403);
    }

    $pdo->beginTransaction();
    $stmt = $pdo->prepare(
        'SELECT ps.id_pesanan AS id, ps.id_pembeli, ps.status, ps.total_harga AS total, ps.fee_nominal AS fee, pr.nama_produk, ' .
        'b.xendit_id AS nomor_invoice, u.email AS email_buyer ' .
        'FROM pesanan ps JOIN penawaran pn ON pn.id_penawaran = ps.id_penawaran ' .
        'JOIN produk pr ON pr.id_produk = pn.id_produk ' .
        'LEFT JOIN pembayaran b ON b.id_pesanan = ps.id_pesanan ' .
        'JOIN pengguna u ON u.id_pembeli = ps.id_pembeli WHERE ps.id_pesanan = ? FOR UPDATE'
    );
    $stmt->execute([$orderId]);
    $order = $stmt->fetch();
    if (!$order) {
        $pdo->rollBack();
        json_error('Order tidak ditemukan', 404);
    }
    if ((int) $order['id_pembeli'] !== $idPembeli) {
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
        // Tanpa kunci Xendit, mode demo HANYA boleh jalan bila DEMO_MODE=true
        // ditulis eksplisit di .env. Kalau tidak, tolak supaya order tidak
        // bisa dibayar gratis diam-diam.
        $demoMode = strtolower(trim((string) (getenv('DEMO_MODE') ?: ''))) === 'true';
        if (!$demoMode) {
            $pdo->rollBack();
            json_error('Pembayaran belum dikonfigurasi: isi XENDIT_SECRET_KEY di .env, atau aktifkan DEMO_MODE=true hanya untuk latihan lokal.', 503);
        }
        // MODE DEMO eksplisit (DEMO_MODE=true): langsung tandai dibayar.
        $stmt = $pdo->prepare("UPDATE pesanan SET status = 'dibayar' WHERE id_pesanan = ?");
        $stmt->execute([$orderId]);
        $stmt = $pdo->prepare("UPDATE pembayaran SET status = 'paid', paid_at = NOW() WHERE id_pesanan = ?");
        $stmt->execute([$orderId]);
        $stmt = $pdo->prepare('INSERT INTO riwayat_status (id_pesanan, status_lama, status_baru, id_pengguna) VALUES (?, ?, ?, ?)');
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
    // Skema v2: pembeli membayar total_harga saja; fee tidak ditambahkan ke tagihan
    // (fee_nominal hanya dicatat untuk dipotong dari dana perusahaan nanti).
    $totalBayar = (float) $order['total'];
    $namaProduk = (string) $order['nama_produk'];
    $emailBuyer = $order['email_buyer'] ?? null;
    $pdo->rollBack();
} catch (Throwable $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    json_error('Database belum siap. Jalankan online_marketplace_v2.sql dulu.', 500);
}

// Panggil Xendit di luar transaksi database.
$hasil = xendit_buat_invoice($secretKey, $nomorInvoice, $totalBayar, $namaProduk, $orderId, $emailBuyer);
if (!$hasil['ok']) {
    // Pesan Xendit diteruskan apa adanya; status order tetap menunggu_bayar.
    json_error($hasil['error'], 502);
}
// Simpan id invoice + URL Xendit ke baris pembayaran (status tetap pending
// sampai webhook menandai paid).
try {
    $pdo = db();
    $stmt = $pdo->prepare('UPDATE pembayaran SET xendit_invoice_id = ?, invoice_url = ? WHERE id_pesanan = ?');
    $stmt->execute([$hasil['invoice_id'] ?? null, $hasil['invoice_url'], $orderId]);
} catch (Throwable $e) {
    json_error('Database belum siap. Jalankan online_marketplace_v2.sql dulu.', 500);
}
json_ok(['order_id' => $orderId, 'status' => 'menunggu_bayar', 'invoice_url' => $hasil['invoice_url']]);

// Buat satu invoice Xendit. Kembalikan ['ok' => true, 'invoice_url' => ..., 'invoice_id' => ...]
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
        return ['ok' => true, 'invoice_url' => $data['invoice_url'], 'invoice_id' => $data['id'] ?? null];
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
