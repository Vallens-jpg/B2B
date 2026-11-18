<?php
// Daftar perusahaan (penjual) + statistik produknya. ?id= untuk 1 penjual.
require_once __DIR__ . '/../lib/db.php';
require_once __DIR__ . '/../lib/response.php';

$id = (int) ($_GET['id'] ?? 0);
try {
    $pdo = db();
    $select = "SELECT u.id, u.nama, u.email, u.created_at, COUNT(p.id) AS jumlah_produk, "
        . "MIN(p.harga_per_kg) AS harga_termurah, SUM(p.stok_kg) AS total_stok, "
        . "GROUP_CONCAT(p.nama ORDER BY p.id SEPARATOR ', ') AS produk_nama "
        . "FROM users u LEFT JOIN produk p ON p.perusahaan_id = u.id WHERE u.role = 'perusahaan'";
    if ($id > 0) {
        $stmt = $pdo->prepare($select . ' AND u.id = ? GROUP BY u.id LIMIT 1');
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
    $stmt = $pdo->query($select . ' GROUP BY u.id ORDER BY u.id');
    $rows = $stmt->fetchAll();
    foreach ($rows as &$r) {
        $r['id'] = (int) $r['id'];
        $r['jumlah_produk'] = (int) $r['jumlah_produk'];
        $r['harga_termurah'] = $r['harga_termurah'] === null ? null : (float) $r['harga_termurah'];
        $r['total_stok'] = $r['total_stok'] === null ? 0 : (float) $r['total_stok'];
    }
    unset($r);
} catch (Throwable $e) {
    json_error('Database belum siap. Jalankan migrasi 01 dan 03 dulu.', 500);
}
json_ok(['penjual' => $rows]);
