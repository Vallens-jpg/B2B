<?php
// Perusahaan: dibayar -> diproses (order_id).
require_once __DIR__ . '/../../lib/db.php';
require_once __DIR__ . '/../../lib/response.php';
require_once __DIR__ . '/../../lib/auth.php';

$user = auth_user();
if (!$user || ($user['role'] ?? '') !== 'perusahaan') {
    json_error('Hanya perusahaan yang boleh memproses order', 403);
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
    $stmt = $pdo->prepare('SELECT o.status, p.perusahaan_id FROM orders o JOIN produk p ON p.id = o.produk_id WHERE o.id = ? LIMIT 1');
    $stmt->execute([$orderId]);
    $order = $stmt->fetch();
    if (!$order) {
        json_error('Order tidak ditemukan', 404);
    }
    if ((int) $order['perusahaan_id'] !== (int) $user['id']) {
        json_error('Order ini bukan milik perusahaan Anda', 403);
    }
    if ($order['status'] !== 'dibayar') {
        json_error('Order harus berstatus dibayar dulu', 409);
    }
    $pdo->beginTransaction();
    $stmt = $pdo->prepare("UPDATE orders SET status = 'diproses' WHERE id = ?");
    $stmt->execute([$orderId]);
    $stmt = $pdo->prepare('INSERT INTO riwayat_status (order_id, status_lama, status_baru, dicatat_oleh) VALUES (?, ?, ?, ?)');
    $stmt->execute([$orderId, 'dibayar', 'diproses', (int) $user['id']]);
    $pdo->commit();
} catch (Throwable $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    json_error('Database belum siap. Jalankan migrasi 01 dan 03 dulu.', 500);
}

json_ok(['order_id' => $orderId, 'status' => 'diproses']);
