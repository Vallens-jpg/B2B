<?php
// Daftar produk untuk halaman katalog (?cari=&halaman=). Katalog kosong jawab data: [], BUKAN error.
require_once __DIR__ . '/../lib/db.php';
require_once __DIR__ . '/../lib/response.php';

$cari = trim($_GET['cari'] ?? '');
$halaman = max(1, (int) ($_GET['halaman'] ?? 1));
$perHalaman = 12;
$offset = ($halaman - 1) * $perHalaman;

try {
    $pdo = db();
    $where = '';
    $params = [];
    if ($cari !== '') {
        $where = 'WHERE nama LIKE ? OR deskripsi LIKE ?';
        $params = ["%$cari%", "%$cari%"];
    }
    $stmt = $pdo->prepare("SELECT COUNT(*) AS total FROM produk $where");
    $stmt->execute($params);
    $total = (int) $stmt->fetchColumn();

    $stmt = $pdo->prepare(
        "SELECT p.id, p.nama, p.deskripsi, p.harga_per_kg, p.stok_kg, p.satuan, p.gambar, " .
        "COALESCE(AVG(u.rating), 0) AS rating, COUNT(u.id) AS jumlah_ulasan " .
        "FROM produk p LEFT JOIN ulasan u ON u.produk_id = p.id $where " .
        "GROUP BY p.id ORDER BY p.id LIMIT $perHalaman OFFSET $offset"
    );
    $stmt->execute($params);
    $produk = $stmt->fetchAll();
    foreach ($produk as &$row) {
        $row['id'] = (int) $row['id'];
        $row['harga_per_kg'] = (float) $row['harga_per_kg'];
        $row['stok_kg'] = (float) $row['stok_kg'];
        $row['rating'] = round((float) $row['rating'], 1);
        $row['jumlah_ulasan'] = (int) $row['jumlah_ulasan'];
    }
} catch (Throwable $e) {
    json_error('Database belum siap. Jalankan migrasi 01 dan 03 dulu.', 500);
}

json_ok([
    'produk' => $produk,
    'halaman' => $halaman,
    'total' => $total,
    'total_halaman' => $total > 0 ? (int) ceil($total / $perHalaman) : 0,
]);
