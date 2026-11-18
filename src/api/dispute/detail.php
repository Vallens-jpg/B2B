<?php
// Detail tiket + bukti + diskusi (?id=).
require_once __DIR__ . '/../../lib/db.php';
require_once __DIR__ . '/../../lib/response.php';
require_once __DIR__ . '/../../lib/auth.php';

$user = auth_user();
if (!$user) {
    json_error('Silakan login dulu', 401);
}
$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) {
    json_error('Dispute tidak ditemukan', 404);
}

try {
    $pdo = db();
    $stmt = $pdo->prepare(
        'SELECT d.*, o.produk_id, o.total_bayar, o.jumlah_kg, p.perusahaan_id, p.nama AS nama_produk, u.nama AS nama_perusahaan, ub.nama AS nama_buyer FROM dispute d JOIN orders o ON o.id = d.order_id JOIN produk p ON p.id = o.produk_id LEFT JOIN users u ON u.id = p.perusahaan_id LEFT JOIN users ub ON ub.id = d.buyer_id WHERE d.id = ? LIMIT 1'
    );
    $stmt->execute([$id]);
    $dispute = $stmt->fetch();
    if (!$dispute) {
        json_error('Dispute tidak ditemukan', 404);
    }
    $role = $user['role'] ?? '';
    $boleh = (int) $dispute['buyer_id'] === (int) $user['id']
        || ($role === 'perusahaan' && (int) $dispute['perusahaan_id'] === (int) $user['id'])
        || $role === 'admin';
    if (!$boleh) {
        json_error('Dispute ini bukan milik Anda', 403);
    }
} catch (Throwable $e) {
    json_error('Database belum siap. Jalankan migrasi 01 dan 03 dulu.', 500);
}

$dispute['id'] = (int) $dispute['id'];
$dispute['total_bayar'] = (float) ($dispute['total_bayar'] ?? 0);
json_ok($dispute);
