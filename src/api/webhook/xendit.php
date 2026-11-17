<?php
// Dipanggil Xendit (BUKAN tombol). Cek token webhook; ubah menunggu_bayar -> dibayar; kirim 2x jangan diproses 2x.
require_once __DIR__ . '/../../lib/db.php';
require_once __DIR__ . '/../../lib/response.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_error('Metode tidak diizinkan', 405);
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
$orderId = (int) ($input['order_id'] ?? $input['external_id'] ?? 0);
if ($orderId <= 0) {
    json_error('Order tidak ditemukan', 404);
}

try {
    $pdo = db();
    $stmt = $pdo->prepare('SELECT id, status FROM orders WHERE id = ? LIMIT 1');
    $stmt->execute([$orderId]);
    $order = $stmt->fetch();
    if (!$order) {
        json_error('Order tidak ditemukan', 404);
    }
    // Idempotent: kalau sudah dibayar, jangan proses 2x.
    if ($order['status'] === 'dibayar') {
        json_ok(['order_id' => $orderId, 'status' => 'dibayar']);
    }
    if ($order['status'] !== 'menunggu_bayar') {
        json_error('Order ini sudah tidak bisa dibayar lagi', 409);
    }
    $pdo->beginTransaction();
    $stmt = $pdo->prepare("UPDATE orders SET status = 'dibayar' WHERE id = ?");
    $stmt->execute([$orderId]);
    $stmt = $pdo->prepare("UPDATE invoice SET status = 'dibayar' WHERE order_id = ?");
    $stmt->execute([$orderId]);
    $stmt = $pdo->prepare('INSERT INTO riwayat_status (order_id, status_lama, status_baru, dicatat_oleh) VALUES (?, ?, ?, ?)');
    $stmt->execute([$orderId, 'menunggu_bayar', 'dibayar', null]);
    $pdo->commit();
} catch (Throwable $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    json_error('Database belum siap. Jalankan migrasi 01 dan 03 dulu.', 500);
}

json_ok(['order_id' => $orderId, 'status' => 'dibayar']);
