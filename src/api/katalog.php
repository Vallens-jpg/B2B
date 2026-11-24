<?php
// Daftar produk untuk halaman katalog (?cari=&halaman=). Katalog kosong jawab data: [], BUKAN error.
require_once __DIR__ . '/../lib/db.php';
require_once __DIR__ . '/../lib/response.php';

$cari = trim($_GET['cari'] ?? '');
$perusahaanFilter = trim($_GET['perusahaan_id'] ?? '');
$halaman = max(1, (int) ($_GET['halaman'] ?? 1));
$perHalaman = 12;
$offset = ($halaman - 1) * $perHalaman;

try {
    $pdo = db();
    $where = '';
    $params = [];
    // Filter pemilik produk: ?perusahaan_id=ID angka, atau ?perusahaan_id=saya
    // (perusahaan yang sedang login — dipakai dashboard perusahaan).
    if ($perusahaanFilter !== '') {
        $pemilikId = 0;
        if ($perusahaanFilter === 'saya') {
            require_once __DIR__ . '/../lib/auth.php';
            $me = auth_user();
            $pemilikId = -1;
            if ($me && ($me['role'] ?? '') === 'perusahaan') {
                // Resolve id profil perusahaan dari session (id_pengguna != id_perusahaan).
                $stmtProfil = $pdo->prepare('SELECT id_perusahaan FROM pengguna WHERE id_pengguna = ? LIMIT 1');
                $stmtProfil->execute([(int) $me['id']]);
                $profil = $stmtProfil->fetch();
                $pemilikId = $profil && $profil['id_perusahaan'] !== null ? (int) $profil['id_perusahaan'] : -1;
            }
        } else {
            $pemilikId = (int) $perusahaanFilter;
        }
        $where = 'WHERE p.id_perusahaan = ?';
        $params = [$pemilikId];
    }
    if ($cari !== '') {
        $where .= ($where === '' ? 'WHERE ' : ' AND ') . '(p.nama_produk LIKE ? OR p.deskripsi LIKE ?)';
        $params[] = "%$cari%";
        $params[] = "%$cari%";
    }
    $stmt = $pdo->prepare("SELECT COUNT(*) AS total FROM produk p $where");
    $stmt->execute($params);
    $total = (int) $stmt->fetchColumn();

    $stmt = $pdo->prepare(
        "SELECT p.id_produk AS id, p.id_perusahaan AS perusahaan_id, p.nama_produk AS nama, p.deskripsi, p.harga_standar AS harga_per_kg, " .
        "COALESCE((SELECT s.jumlah_stok FROM stok s WHERE s.id_produk = p.id_produk ORDER BY s.tanggal_stok DESC LIMIT 1), 0) AS stok_kg, " .
        "p.satuan, p.gambar, " .
        "COALESCE(AVG(ps.rating), 0) AS rating, COUNT(ps.rating) AS jumlah_ulasan " .
        "FROM produk p LEFT JOIN penawaran pn ON pn.id_produk = p.id_produk LEFT JOIN pesanan ps ON ps.id_penawaran = pn.id_penawaran AND ps.rating IS NOT NULL $where " .
        "GROUP BY p.id_produk ORDER BY p.id_produk LIMIT $perHalaman OFFSET $offset"
    );
    $stmt->execute($params);
    $produk = $stmt->fetchAll();
    foreach ($produk as &$row) {
        $row['id'] = (int) $row['id'];
        $row['perusahaan_id'] = $row['perusahaan_id'] === null ? null : (int) $row['perusahaan_id'];
        $row['harga_per_kg'] = (float) $row['harga_per_kg'];
        $row['stok_kg'] = (float) $row['stok_kg'];
        $row['rating'] = round((float) $row['rating'], 1);
        $row['jumlah_ulasan'] = (int) $row['jumlah_ulasan'];
    }
} catch (Throwable $e) {
    json_error('Database belum siap. Jalankan online_marketplace_v2.sql dulu.', 500);
}

json_ok([
    'produk' => $produk,
    'halaman' => $halaman,
    'total' => $total,
    'total_halaman' => $total > 0 ? (int) ceil($total / $perHalaman) : 0,
]);
