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
        "SELECT p.*, u2.nama AS nama_perusahaan, u2.email AS email_perusahaan, COALESCE(AVG(u.rating), 0) AS rating, COUNT(u.id) AS jumlah_ulasan " .
        "FROM produk p LEFT JOIN ulasan u ON u.produk_id = p.id LEFT JOIN users u2 ON u2.id = p.perusahaan_id WHERE p.id = ? GROUP BY p.id"
    );
    $stmt->execute([$id]);
    $produk = $stmt->fetch();
} catch (Throwable $e) {
    json_error('Database belum siap. Jalankan migrasi 01 dan 03 dulu.', 500);
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
