<?php
// Detail 1 produk (?id=) + stok + label rating/ulasan.
require_once __DIR__ . '/../../lib/db.php';
require_once __DIR__ . '/../../lib/response.php';

$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) {
    json_error('Produk tidak ditemukan', 404);
}

try {
    $stmt = db()->prepare(
        "SELECT p.id_produk AS id, p.id_perusahaan, p.id_kategori, p.nama_produk AS nama, p.nama_produk, p.deskripsi, p.gambar, p.satuan, " .
        "p.harga_standar AS harga_per_kg, p.harga_standar, " .
        "COALESCE((SELECT s.jumlah_stok FROM stok s WHERE s.id_produk = p.id_produk ORDER BY s.tanggal_stok DESC LIMIT 1), 0) AS stok_kg, " .
        "p.status, p.dibuat_pada, " .
        "(SELECT per2.nama_perusahaan FROM perusahaan per2 WHERE per2.id_perusahaan = p.id_perusahaan LIMIT 1) AS nama_perusahaan, " .
        "(SELECT u2.email FROM pengguna u2 WHERE u2.id_perusahaan = p.id_perusahaan LIMIT 1) AS email_perusahaan, " .
        "COALESCE(AVG(ps.rating), 0) AS rating, COUNT(ps.rating) AS jumlah_ulasan " .
        "FROM produk p LEFT JOIN penawaran pn ON pn.id_produk = p.id_produk LEFT JOIN pesanan ps ON ps.id_penawaran = pn.id_penawaran AND ps.rating IS NOT NULL WHERE p.id_produk = ? GROUP BY p.id_produk"
    );
    $stmt->execute([$id]);
    $produk = $stmt->fetch();
} catch (Throwable $e) {
    json_error('Database belum siap. Jalankan online_marketplace_v2.sql dulu.', 500);
}

if (!$produk) {
    json_error('Produk tidak ditemukan', 404);
}

$produk['id'] = (int) $produk['id'];
$produk['harga_per_kg'] = (float) $produk['harga_per_kg'];
$produk['stok_kg'] = (float) $produk['stok_kg'];
$produk['rating'] = round((float) $produk['rating'], 1);
$produk['jumlah_ulasan'] = (int) $produk['jumlah_ulasan'];

json_ok($produk);
