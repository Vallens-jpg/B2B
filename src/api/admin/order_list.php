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
        'SELECT ps.id_pesanan AS id, ps.id_pembeli AS buyer_id, pb.nama_pembeli AS nama_buyer, ' .
        'pn.id_produk AS produk_id, pr.nama_produk AS nama_produk, ' .
        'ps.jumlah AS jumlah_kg, ps.total_harga AS total, ps.fee_nominal AS fee, ps.status, ' .
        'ps.tanggal_pesanan AS created_at, per.nama_perusahaan AS nama_perusahaan FROM pesanan ps ' .
        'JOIN pembeli pb ON pb.id_pembeli = ps.id_pembeli ' .
        'JOIN penawaran pn ON pn.id_penawaran = ps.id_penawaran ' .
        'JOIN produk pr ON pr.id_produk = pn.id_produk ' .
        'LEFT JOIN perusahaan per ON per.id_perusahaan = ps.id_perusahaan ORDER BY ps.id_pesanan DESC'
    );
    $orders = $stmt->fetchAll();
    foreach ($orders as &$row) {
        $row['id'] = (int) $row['id'];
        $row['jumlah_kg'] = (float) $row['jumlah_kg'];
        $row['total'] = (float) $row['total'];
        $row['fee'] = (float) $row['fee'];
    }
} catch (Throwable $e) {
    json_error('Database belum siap. Jalankan online_marketplace_v2.sql dulu.', 500);
}

json_ok($orders);
