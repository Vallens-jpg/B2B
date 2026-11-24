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
    // Resolve id profil dari session (id_pengguna != id_pembeli/id_perusahaan).
    $stmt = $pdo->prepare('SELECT id_pembeli, id_perusahaan FROM pengguna WHERE id_pengguna = ? LIMIT 1');
    $stmt->execute([(int) $user['id']]);
    $profil = $stmt->fetch();
    $idPembeli = $profil && $profil['id_pembeli'] !== null ? (int) $profil['id_pembeli'] : null;
    $idPerusahaan = $profil && $profil['id_perusahaan'] !== null ? (int) $profil['id_perusahaan'] : null;
    $stmt = $pdo->prepare(
        'SELECT d.id_dispute AS id, d.id_pesanan AS order_id, ps.id_pembeli AS buyer_id, ps.id_perusahaan AS perusahaan_id, pn.id_produk AS produk_id, ps.total_harga + ps.fee_nominal AS total_bayar, ps.jumlah AS jumlah_kg, pr.nama_produk AS nama_produk, per.nama_perusahaan AS nama_perusahaan, pb.nama_pembeli AS nama_buyer, d.tanggal_mulai, d.tanggal_mulai AS created_at, d.nama_barang, NULL AS judul, d.keterangan AS deskripsi, d.foto_bukti, d.status, d.keputusan AS putusan, d.catatan_admin, d.tanggal_selesai, d.id_admin, d.kondisi_barang, d.tanggal_upload FROM dispute d JOIN pesanan ps ON ps.id_pesanan = d.id_pesanan JOIN penawaran pn ON pn.id_penawaran = ps.id_penawaran JOIN produk pr ON pr.id_produk = pn.id_produk LEFT JOIN pembeli pb ON pb.id_pembeli = ps.id_pembeli LEFT JOIN perusahaan per ON per.id_perusahaan = ps.id_perusahaan WHERE d.id_dispute = ? LIMIT 1'
    );
    $stmt->execute([$id]);
    $dispute = $stmt->fetch();
    if (!$dispute) {
        json_error('Dispute tidak ditemukan', 404);
    }
    $role = $user['role'] ?? '';
    $boleh = ($idPembeli !== null && (int) $dispute['buyer_id'] === $idPembeli)
        || ($role === 'perusahaan' && $idPerusahaan !== null && (int) $dispute['perusahaan_id'] === $idPerusahaan)
        || $role === 'admin';
    if (!$boleh) {
        json_error('Dispute ini bukan milik Anda', 403);
    }
    // Escrow v2: diturunkan dari pesanan + pencairan (tahap 1 = DP, tahap 2 = pelunasan).
    $esc = $pdo->prepare(
        "SELECT ps.total_harga AS jumlah_ditahan, COALESCE(c1.nominal, 0) AS dp_cair, COALESCE(c2.nominal, 0) AS sisa_cair, CASE WHEN ps.status = 'dibatalkan' THEN 'kembali' WHEN c2.id_pencairan IS NOT NULL THEN 'cair_sisa' WHEN c1.id_pencairan IS NOT NULL THEN 'cair_dp' ELSE 'ditahan' END AS status FROM pesanan ps LEFT JOIN pencairan c1 ON c1.id_pesanan = ps.id_pesanan AND c1.tahap = 1 LEFT JOIN pencairan c2 ON c2.id_pesanan = ps.id_pesanan AND c2.tahap = 2 WHERE ps.id_pesanan = ? LIMIT 1"
    );
    $esc->execute([(int) $dispute['order_id']]);
    $dispute['escrow'] = $esc->fetch() ?: null;
} catch (Throwable $e) {
    json_error('Database belum siap. Jalankan online_marketplace_v2.sql dulu.', 500);
}

$dispute['id'] = (int) $dispute['id'];
$dispute['total_bayar'] = (float) ($dispute['total_bayar'] ?? 0);
if (!empty($dispute['escrow'])) {
    $dispute['escrow']['jumlah_ditahan'] = (float) $dispute['escrow']['jumlah_ditahan'];
    $dispute['escrow']['dp_cair'] = (float) $dispute['escrow']['dp_cair'];
    $dispute['escrow']['sisa_cair'] = (float) $dispute['escrow']['sisa_cair'];
}
json_ok($dispute);
