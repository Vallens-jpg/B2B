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
    // $user['id'] adalah pengguna.id_pengguna, bukan id profil: resolve dulu
    // ke id_perusahaan sebelum membandingkan dengan pesanan.id_perusahaan.
    $stmt = $pdo->prepare('SELECT id_perusahaan FROM pengguna WHERE id_pengguna = ? LIMIT 1');
    $stmt->execute([(int) $user['id']]);
    $profil = $stmt->fetch();
    $idPerusahaan = $profil['id_perusahaan'] !== null ? (int) $profil['id_perusahaan'] : null;

    $stmt = $pdo->prepare('SELECT status, id_perusahaan FROM pesanan WHERE id_pesanan = ? LIMIT 1');
    $stmt->execute([$orderId]);
    $order = $stmt->fetch();
    if (!$order) {
        json_error('Order tidak ditemukan', 404);
    }
    if ($idPerusahaan === null || (int) $order['id_perusahaan'] !== $idPerusahaan) {
        json_error('Order ini bukan milik perusahaan Anda', 403);
    }
    if ($order['status'] !== 'dibayar') {
        json_error('Order harus berstatus dibayar dulu', 409);
    }
    $pdo->beginTransaction();
    $stmt = $pdo->prepare("UPDATE pesanan SET status = 'diproses' WHERE id_pesanan = ?");
    $stmt->execute([$orderId]);
    $stmt = $pdo->prepare('INSERT INTO riwayat_status (id_pesanan, status_lama, status_baru, id_pengguna) VALUES (?, ?, ?, ?)');
    $stmt->execute([$orderId, 'dibayar', 'diproses', (int) $user['id']]);
    $pdo->commit();
} catch (Throwable $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    json_error('Database belum siap. Jalankan online_marketplace_v2.sql dulu.', 500);
}

json_ok(['order_id' => $orderId, 'status' => 'diproses']);
