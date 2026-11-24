<?php
// Panel admin: ringkasan escrow seluruh perusahaan + daftar escrow per order.
// GET saja. Saldo yang tersedia = total dana yang masih ditahan di escrow.
// Skema v2: tidak ada lagi tabel escrow; angka diturunkan dari pesanan +
// pencairan (tahap 1 = DP, tahap 2 = pelunasan).
require_once __DIR__ . '/../../lib/db.php';
require_once __DIR__ . '/../../lib/response.php';
require_once __DIR__ . '/../../lib/auth.php';

$user = auth_user();
if (!$user || ($user['role'] ?? '') !== 'admin') {
    json_error('Hanya admin yang boleh melihat keuangan', 403);
}

try {
    $pdo = db();
    $ringkasan = $pdo->query(
        'SELECT COUNT(*) AS jumlah_order,'
        . ' COALESCE(SUM(ps.total_harga), 0) AS total_ditahan,'
        . ' COALESCE(SUM(COALESCE(c1.nominal, 0)), 0) AS total_cair_dp,'
        . ' COALESCE(SUM(COALESCE(c2.nominal, 0)), 0) AS total_cair_sisa,'
        . ' COALESCE(SUM(COALESCE(c3.nominal, 0)), 0) AS total_refund,'
        // Dana yang masih ditahan = total pesanan yang SUDAH dibayar,
        // dikurangi DP (tahap 1), pelunasan (tahap 2), dan refund (tahap 3).
        // Pesanan yang belum dibayar (menunggu_bayar) tidak dihitung, karena
        // dananya belum pernah masuk escrow.
        . ' COALESCE(SUM(CASE WHEN EXISTS (SELECT 1 FROM pembayaran pb2 WHERE pb2.id_pesanan = ps.id_pesanan AND pb2.status = \'paid\') THEN ps.total_harga - COALESCE(c1.nominal, 0) - COALESCE(c2.nominal, 0) - COALESCE(c3.nominal, 0) ELSE 0 END), 0) AS masih_ditahan'
        . ' FROM pesanan ps'
        . ' LEFT JOIN pencairan c1 ON c1.id_pesanan = ps.id_pesanan AND c1.tahap = 1'
        . ' LEFT JOIN pencairan c2 ON c2.id_pesanan = ps.id_pesanan AND c2.tahap = 2'
        . ' LEFT JOIN pencairan c3 ON c3.id_pesanan = ps.id_pesanan AND c3.tahap = 3'
    )->fetch();
    $stmt = $pdo->query(
        'SELECT ps.id_pesanan AS id, ps.id_pesanan AS order_id, ps.total_harga AS jumlah_ditahan,'
        . ' COALESCE(c1.nominal, 0) AS dp_cair, COALESCE(c2.nominal, 0) AS sisa_cair, COALESCE(c3.nominal, 0) AS refund,'
        . " CASE WHEN ps.status = 'dibatalkan' THEN 'kembali' WHEN c2.id_pencairan IS NOT NULL THEN 'cair_sisa' WHEN c1.id_pencairan IS NOT NULL THEN 'cair_dp' ELSE 'ditahan' END AS status,"
        . ' ps.tanggal_pesanan AS created_at,'
        . ' ps.total_harga AS total, ps.status AS status_order, pb.nama_pembeli AS nama_buyer, pr.nama_produk AS nama_produk, per.nama_perusahaan AS nama_perusahaan'
        . ' FROM pesanan ps JOIN penawaran pn ON pn.id_penawaran = ps.id_penawaran'
        . ' JOIN produk pr ON pr.id_produk = pn.id_produk'
        . ' LEFT JOIN pembeli pb ON pb.id_pembeli = ps.id_pembeli'
        . ' LEFT JOIN perusahaan per ON per.id_perusahaan = ps.id_perusahaan'
        . ' LEFT JOIN pencairan c1 ON c1.id_pesanan = ps.id_pesanan AND c1.tahap = 1'
        . ' LEFT JOIN pencairan c2 ON c2.id_pesanan = ps.id_pesanan AND c2.tahap = 2'
        . ' LEFT JOIN pencairan c3 ON c3.id_pesanan = ps.id_pesanan AND c3.tahap = 3'
        . ' ORDER BY ps.id_pesanan DESC LIMIT 100'
    );
    $rows = $stmt->fetchAll();
    foreach ($rows as &$row) {
        $row['id'] = (int) $row['id'];
        $row['order_id'] = (int) $row['order_id'];
        $row['jumlah_ditahan'] = (float) $row['jumlah_ditahan'];
        $row['dp_cair'] = (float) $row['dp_cair'];
        $row['sisa_cair'] = (float) $row['sisa_cair'];
        $row['refund'] = (float) $row['refund'];
        $row['total'] = (float) $row['total'];
    }
    $ringkasan['jumlah_order'] = (int) $ringkasan['jumlah_order'];
    $ringkasan['total_ditahan'] = (float) $ringkasan['total_ditahan'];
    $ringkasan['total_cair_dp'] = (float) $ringkasan['total_cair_dp'];
    $ringkasan['total_cair_sisa'] = (float) $ringkasan['total_cair_sisa'];
    $ringkasan['total_refund'] = (float) $ringkasan['total_refund'];
    $ringkasan['masih_ditahan'] = (float) $ringkasan['masih_ditahan'];
    json_ok(['ringkasan' => $ringkasan, 'escrow' => $rows]);
} catch (Throwable $e) {
    json_error('Database belum siap. Jalankan online_marketplace_v2.sql dulu.', 500);
}
