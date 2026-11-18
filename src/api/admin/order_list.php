<?php
// Panel admin: daftar order.
require_once __DIR__ . '/../../lib/db.php';
require_once __DIR__ . '/../../lib/response.php';
require_once __DIR__ . '/../../lib/auth.php';

$user = auth_user();
if (!$user || ($user['role'] ?? '') !== 'admin') {
    json_error('Hanya admin yang boleh melihat daftar order', 403);
}

try {
    $stmt = db()->query(
        'SELECT o.id, o.buyer_id, u.nama AS nama_buyer, o.produk_id, p.nama AS nama_produk, ' .
        'o.jumlah_kg, o.total, o.fee, o.status, o.created_at, pu.nama AS nama_perusahaan FROM orders o ' .
        'JOIN users u ON u.id = o.buyer_id JOIN produk p ON p.id = o.produk_id ' .
        'LEFT JOIN users pu ON pu.id = p.perusahaan_id ORDER BY o.id DESC'
    );
    $orders = $stmt->fetchAll();
    foreach ($orders as &$row) {
        $row['id'] = (int) $row['id'];
        $row['jumlah_kg'] = (float) $row['jumlah_kg'];
        $row['total'] = (float) $row['total'];
        $row['fee'] = (float) $row['fee'];
    }
} catch (Throwable $e) {
    json_error('Database belum siap. Jalankan migrasi 01 dan 03 dulu.', 500);
}

json_ok($orders);
