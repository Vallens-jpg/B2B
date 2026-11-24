<?php
// Dipanggil Xendit (BUKAN tombol). Cek token webhook; ubah menunggu_bayar -> dibayar; kirim 2x jangan diproses 2x.
// Xendit mengirim callback invoice: {"external_id": "<nomor invoice>", "status": "PAID", ...}.
// external_id = nomor invoice (INV-YYYYMMDD-000001), BUKAN id order numerik.
require_once __DIR__ . '/../../lib/db.php';
require_once __DIR__ . '/../../lib/response.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_error('Metode tidak diizinkan', 405);
}

// db() memuat .env ke environment (lihat src/lib/db.php) — token webhook HARUS
// dibaca setelah ini, karena token hanya ada di file .env bila tidak di-set
// sebagai environment variable.
try {
    $pdo = db();
} catch (Throwable $e) {
    json_error('Database belum siap. Jalankan online_marketplace_v2.sql dulu.', 500);
}

$token = getenv('XENDIT_WEBHOOK_TOKEN') ?: '';
if ($token === '') {
    json_error('Webhook Xendit belum dikonfigurasi (XENDIT_WEBHOOK_TOKEN kosong)', 501);
}
$dikirim = $_SERVER['HTTP_X_CALLBACK_TOKEN'] ?? '';
if (!hash_equals($token, $dikirim)) {
    json_error('Token webhook tidak valid', 403);
}

$input = json_input();
$statusCallback = strtoupper((string) ($input['status'] ?? ''));
$externalId = trim((string) ($input['external_id'] ?? ''));
// Kompatibilitas: callback lama yang membawa order_id numerik.
$orderIdLama = (int) ($input['order_id'] ?? 0);

if ($statusCallback === '') {
    json_error('Status callback Xendit tidak dikenali', 400);
}
if ($statusCallback !== 'PAID') {
    // Callback selain lunas (mis. PENDING/EXPIRED): diakui tapi tidak mengubah apa pun.
    json_ok(['status' => 'diabaikan', 'callback_status' => $statusCallback]);
}
if ($externalId === '' && $orderIdLama <= 0) {
    json_error('Order tidak ditemukan', 404);
}

try {
    $pdo->beginTransaction();
    if ($externalId !== '') {
        // Cari pesanan lewat nomor invoice (pembayaran.xendit_id = external_id Xendit).
        $stmt = $pdo->prepare('SELECT p.id_pesanan AS id, p.status FROM pesanan p JOIN pembayaran b ON b.id_pesanan = p.id_pesanan WHERE b.xendit_id = ? LIMIT 1 FOR UPDATE');
        $stmt->execute([$externalId]);
    } else {
        $stmt = $pdo->prepare('SELECT id_pesanan AS id, status FROM pesanan WHERE id_pesanan = ? LIMIT 1 FOR UPDATE');
        $stmt->execute([$orderIdLama]);
    }
    $order = $stmt->fetch();
    if (!$order) {
        $pdo->rollBack();
        json_error('Order tidak ditemukan', 404);
    }
    $orderId = (int) $order['id'];
    // Idempotent: kalau sudah dibayar, jangan proses 2x.
    if ($order['status'] === 'dibayar') {
        $pdo->commit();
        json_ok(['order_id' => $orderId, 'status' => 'dibayar']);
    }
    if ($order['status'] !== 'menunggu_bayar') {
        $pdo->rollBack();
        json_error('Order ini sudah tidak bisa dibayar lagi', 409);
    }
    $stmt = $pdo->prepare("UPDATE pesanan SET status = 'dibayar' WHERE id_pesanan = ?");
    $stmt->execute([$orderId]);
    $stmt = $pdo->prepare("UPDATE pembayaran SET status = 'paid', paid_at = NOW() WHERE id_pesanan = ?");
    $stmt->execute([$orderId]);
    $stmt = $pdo->prepare('INSERT INTO riwayat_status (id_pesanan, status_lama, status_baru, id_pengguna) VALUES (?, ?, ?, ?)');
    $stmt->execute([$orderId, 'menunggu_bayar', 'dibayar', null]);
    $pdo->commit();
} catch (Throwable $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    json_error('Database belum siap. Jalankan online_marketplace_v2.sql dulu.', 500);
}

json_ok(['order_id' => $orderId, 'status' => 'dibayar']);
