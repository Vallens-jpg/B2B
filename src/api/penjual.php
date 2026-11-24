<?php
// Daftar perusahaan (penjual) + statistik produknya. ?id= untuk 1 penjual.
require_once __DIR__ . '/../lib/db.php';
require_once __DIR__ . '/../lib/response.php';

$id = (int) ($_GET['id'] ?? 0);
try {
    $pdo = db();
    // v2: perusahaan adalah sumber utama; email dari pengguna (LEFT JOIN, bisa
    // lebih dari satu akun per perusahaan jadi diambil MAX agar tidak menggandakan
    // baris produk); total_stok = jumlah baris stok TERBARU per produk.
    $select = "SELECT per.id_perusahaan AS id, per.nama_perusahaan AS nama, MAX(u.email) AS email, NULL AS created_at, "
        . "COUNT(pr.id_produk) AS jumlah_produk, "
        . "MIN(pr.harga_standar) AS harga_termurah, SUM(s.jumlah_stok) AS total_stok, "
        . "GROUP_CONCAT(pr.nama_produk ORDER BY pr.id_produk SEPARATOR ', ') AS produk_nama "
        . "FROM perusahaan per LEFT JOIN pengguna u ON u.id_perusahaan = per.id_perusahaan "
        . "LEFT JOIN produk pr ON pr.id_perusahaan = per.id_perusahaan "
        . "LEFT JOIN stok s ON s.id_produk = pr.id_produk AND s.tanggal_stok = "
        . "(SELECT MAX(s2.tanggal_stok) FROM stok s2 WHERE s2.id_produk = pr.id_produk)";
    if ($id > 0) {
        $stmt = $pdo->prepare($select . ' WHERE per.id_perusahaan = ? GROUP BY per.id_perusahaan LIMIT 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if (!$row) {
            json_error('Penjual tidak ditemukan', 404);
        }
        $row['id'] = (int) $row['id'];
        $row['jumlah_produk'] = (int) $row['jumlah_produk'];
        $row['harga_termurah'] = $row['harga_termurah'] === null ? null : (float) $row['harga_termurah'];
        $row['total_stok'] = $row['total_stok'] === null ? 0 : (float) $row['total_stok'];
        json_ok($row);
    }
    $stmt = $pdo->query($select . ' GROUP BY per.id_perusahaan ORDER BY per.id_perusahaan');
    $rows = $stmt->fetchAll();
    foreach ($rows as &$r) {
        $r['id'] = (int) $r['id'];
        $r['jumlah_produk'] = (int) $r['jumlah_produk'];
        $r['harga_termurah'] = $r['harga_termurah'] === null ? null : (float) $r['harga_termurah'];
        $r['total_stok'] = $r['total_stok'] === null ? 0 : (float) $r['total_stok'];
    }
    unset($r);
} catch (Throwable $e) {
    json_error('Database belum siap. Jalankan online_marketplace_v2.sql dulu.', 500);
}
json_ok(['penjual' => $rows]);
