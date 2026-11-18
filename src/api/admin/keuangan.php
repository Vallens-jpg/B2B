<?php
// Panel admin: ringkasan escrow seluruh perusahaan + daftar escrow per order.
// GET saja. Saldo yang tersedia = total dana yang masih ditahan di escrow.
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
        . ' COALESCE(SUM(jumlah_ditahan), 0) AS total_ditahan,'
        . ' COALESCE(SUM(dp_cair), 0) AS total_cair_dp,'
        . ' COALESCE(SUM(sisa_cair), 0) AS total_cair_sisa,'
        . ' COALESCE(SUM(jumlah_ditahan - dp_cair - sisa_cair), 0) AS masih_ditahan'
        . ' FROM escrow'
    )->fetch();
    $stmt = $pdo->query(
        'SELECT e.id, e.order_id, e.jumlah_ditahan, e.dp_cair, e.sisa_cair, e.status, e.created_at,'
        . ' o.total, o.status AS status_order, ub.nama AS nama_buyer, p.nama AS nama_produk, pu.nama AS nama_perusahaan'
        . ' FROM escrow e JOIN orders o ON o.id = e.order_id'
        . ' JOIN produk p ON p.id = o.produk_id'
        . ' LEFT JOIN users ub ON ub.id = o.buyer_id'
        . ' LEFT JOIN users pu ON pu.id = p.perusahaan_id'
        . ' ORDER BY e.id DESC LIMIT 100'
    );
    $rows = $stmt->fetchAll();
    foreach ($rows as &$row) {
        $row['id'] = (int) $row['id'];
        $row['order_id'] = (int) $row['order_id'];
        $row['jumlah_ditahan'] = (float) $row['jumlah_ditahan'];
        $row['dp_cair'] = (float) $row['dp_cair'];
        $row['sisa_cair'] = (float) $row['sisa_cair'];
        $row['total'] = (float) $row['total'];
    }
    $ringkasan['jumlah_order'] = (int) $ringkasan['jumlah_order'];
    $ringkasan['total_ditahan'] = (float) $ringkasan['total_ditahan'];
    $ringkasan['total_cair_dp'] = (float) $ringkasan['total_cair_dp'];
    $ringkasan['total_cair_sisa'] = (float) $ringkasan['total_cair_sisa'];
    $ringkasan['masih_ditahan'] = (float) $ringkasan['masih_ditahan'];
    json_ok(['ringkasan' => $ringkasan, 'escrow' => $rows]);
} catch (Throwable $e) {
    json_error('Database belum siap. Jalankan migrasi 01 dan 03 dulu.', 500);
}
