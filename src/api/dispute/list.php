<?php
// Daftar dispute milik pengguna (pembeli: miliknya; perusahaan: produknya; admin: semua).
require_once __DIR__ . '/../../lib/db.php';
require_once __DIR__ . '/../../lib/response.php';
require_once __DIR__ . '/../../lib/auth.php';

$user = auth_user();
if (!$user) {
    json_error('Silakan login dulu', 401);
}
$role = $user['role'] ?? '';
try {
    $pdo = db();
    // Resolve id profil dari session (rule global 2): $user['id'] = id_pengguna.
    $stmtProfil = $pdo->prepare('SELECT id_pembeli, id_perusahaan, id_admin FROM pengguna WHERE id_pengguna = ? LIMIT 1');
    $stmtProfil->execute([(int) $user['id']]);
    $profil = $stmtProfil->fetch();
    $idPembeli = (int) ($profil['id_pembeli'] ?? 0);
    $idPerusahaan = (int) ($profil['id_perusahaan'] ?? 0);
    $sql = 'SELECT d.id_dispute AS id, d.id_pesanan AS order_id, d.nama_barang AS judul, d.status, d.keputusan AS putusan, d.tanggal_mulai AS created_at,'
        . ' ps.total_harga AS total_bayar, ps.jumlah AS jumlah_kg, pr.nama_produk AS nama_produk,'
        . ' per.nama_perusahaan AS nama_perusahaan, pb.nama_pembeli AS nama_buyer'
        . ' FROM dispute d'
        . ' JOIN pesanan ps ON ps.id_pesanan = d.id_pesanan'
        . ' JOIN penawaran pn ON pn.id_penawaran = ps.id_penawaran'
        . ' JOIN produk pr ON pr.id_produk = pn.id_produk'
        . ' LEFT JOIN perusahaan per ON per.id_perusahaan = ps.id_perusahaan'
        . ' LEFT JOIN pembeli pb ON pb.id_pembeli = ps.id_pembeli';
    $params = [];
    if ($role === 'pembeli') {
        $sql .= ' WHERE ps.id_pembeli = ?';
        $params[] = $idPembeli;
    } elseif ($role === 'perusahaan') {
        $sql .= ' WHERE ps.id_perusahaan = ?';
        $params[] = $idPerusahaan;
    }
    $sql .= ' ORDER BY d.tanggal_mulai DESC LIMIT 50';
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll();
    foreach ($rows as &$r) {
        $r['id'] = (int) $r['id'];
        $r['order_id'] = (int) $r['order_id'];
        $r['total_bayar'] = (float) $r['total_bayar'];
    }
    unset($r);
} catch (Throwable $e) {
    json_error('Database belum siap. Jalankan online_marketplace_v2.sql dulu.', 500);
}
json_ok(['dispute' => $rows]);
