<?php
// Buyer buka tiket dispute + upload bukti foto.
require_once __DIR__ . '/../../lib/db.php';
require_once __DIR__ . '/../../lib/response.php';
require_once __DIR__ . '/../../lib/auth.php';

$user = auth_user();
if (!$user || ($user['role'] ?? '') !== 'pembeli') {
    json_error('Hanya pembeli yang boleh membuka dispute', 403);
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_error('Metode tidak diizinkan', 405);
}

$input = json_input();
$orderId = (int) ($input['order_id'] ?? 0);
$judul = trim($input['judul'] ?? '');
$deskripsi = trim($input['deskripsi'] ?? '');
$buktiFoto = trim($input['bukti_foto'] ?? '');
if ($orderId <= 0 || $judul === '' || $deskripsi === '') {
    json_error('Order, judul, dan deskripsi dispute wajib diisi', 400);
}

try {
    $pdo = db();
    $stmt = $pdo->prepare('SELECT id, buyer_id FROM orders WHERE id = ? LIMIT 1');
    $stmt->execute([$orderId]);
    $order = $stmt->fetch();
    if (!$order) {
        json_error('Order tidak ditemukan', 404);
    }
    if ((int) $order['buyer_id'] !== (int) $user['id']) {
        json_error('Order ini bukan milik Anda', 403);
    }
    $stmt = $pdo->prepare("SELECT id FROM dispute WHERE order_id = ? AND status IN ('dibuka', 'diproses') LIMIT 1");
    $stmt->execute([$orderId]);
    if ($stmt->fetch()) {
        json_error('Order ini sudah punya dispute yang masih dibuka', 409);
    }
    $stmt = $pdo->prepare('INSERT INTO dispute (order_id, buyer_id, judul, deskripsi, bukti_foto, status) VALUES (?, ?, ?, ?, ?, ?)');
    $stmt->execute([$orderId, (int) $user['id'], $judul, $deskripsi, $buktiFoto !== '' ? $buktiFoto : null, 'dibuka']);
    $disputeId = (int) $pdo->lastInsertId();
} catch (Throwable $e) {
    json_error('Database belum siap. Jalankan migrasi 01 dan 03 dulu.', 500);
}

json_ok(['dispute_id' => $disputeId, 'status' => 'dibuka']);
