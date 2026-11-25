<?php
// Daftar order milik pembeli yang login, atau order atas produk milik perusahaan
// yang login (terbaru dulu). Dipakai halaman order_list.php dan dashboard perusahaan.
require_once __DIR__ . '/../../lib/db.php';
require_once __DIR__ . '/../../lib/response.php';
require_once __DIR__ . '/../../lib/auth.php';

$user = auth_user();
if (!$user || !in_array($user['role'] ?? '', ['pembeli', 'perusahaan'], true)) {
    json_error('Hanya pembeli atau perusahaan yang boleh melihat daftar pesanan', 403);
}

try {
    // $user['id'] adalah pengguna.id_pengguna, bukan id profil: resolve dulu
    // ke id_pembeli / id_perusahaan sebelum membandingkan dengan pesanan.
    $stmt = db()->prepare('SELECT id_pembeli, id_perusahaan FROM pengguna WHERE id_pengguna = ? LIMIT 1');
    $stmt->execute([(int) $user['id']]);
    $profil = $stmt->fetch();
    $idPembeli = $profil['id_pembeli'] !== null ? (int) $profil['id_pembeli'] : null;
    $idPerusahaan = $profil['id_perusahaan'] !== null ? (int) $profil['id_perusahaan'] : null;

    if (($user['role'] ?? '') === 'perusahaan') {
        // Perusahaan: order atas produk miliknya (sinkron dengan pembeli).
        // Kolom escrow lama diganti turunan dari pencairan: siap cair =
        // dp_cair + sisa_cair, tertahan = jumlah_ditahan - dp_cair - sisa_cair.
        // escrow_status: dibatalkan -> 'kembali'; ada tahap 2 -> 'cair_sisa';
        // ada tahap 1 -> 'cair_dp'; kalau tidak ada -> 'ditahan'.
        $stmt = db()->prepare(
            'SELECT ps.id_pesanan AS id, ps.jumlah AS jumlah_kg, ps.harga_satuan, ps.total_harga AS total, ps.fee_nominal AS fee, ps.status, ps.tanggal_pesanan AS created_at, pr.nama_produk AS nama_produk, pb.nama_pembeli AS nama_buyer, ' .
            'ps.total_harga AS jumlah_ditahan, COALESCE(c1.nominal, 0) AS dp_cair, COALESCE(c2.nominal, 0) AS sisa_cair, ' .
            // dp_pending/sisa_pending: hanya baris yang masih 'pending' (belum
            // dicatat cair oleh admin) — dipakai Saldo Siap Cair perusahaan.
            // sudah_cair: baris yang sudah 'berhasil' dicatat cair.
            'COALESCE(c1p.nominal, 0) AS dp_pending, COALESCE(c2p.nominal, 0) AS sisa_pending, COALESCE(c1b.nominal, 0) + COALESCE(c2b.nominal, 0) AS sudah_cair, ' .
            "CASE WHEN ps.status = 'dibatalkan' THEN 'kembali' WHEN c2.id_pencairan IS NOT NULL THEN 'cair_sisa' WHEN c1.id_pencairan IS NOT NULL THEN 'cair_dp' ELSE 'ditahan' END AS escrow_status " .
            'FROM pesanan ps JOIN penawaran pn ON pn.id_penawaran = ps.id_penawaran JOIN produk pr ON pr.id_produk = pn.id_produk JOIN pembeli pb ON pb.id_pembeli = ps.id_pembeli ' .
            'LEFT JOIN pencairan c1 ON c1.id_pesanan = ps.id_pesanan AND c1.tahap = 1 LEFT JOIN pencairan c2 ON c2.id_pesanan = ps.id_pesanan AND c2.tahap = 2 LEFT JOIN pencairan c1p ON c1p.id_pesanan = ps.id_pesanan AND c1p.tahap = 1 AND c1p.status = \'pending\' LEFT JOIN pencairan c2p ON c2p.id_pesanan = ps.id_pesanan AND c2p.tahap = 2 AND c2p.status = \'pending\' LEFT JOIN pencairan c1b ON c1b.id_pesanan = ps.id_pesanan AND c1b.tahap = 1 AND c1b.status = \'berhasil\' LEFT JOIN pencairan c2b ON c2b.id_pesanan = ps.id_pesanan AND c2b.tahap = 2 AND c2b.status = \'berhasil\' WHERE ps.id_perusahaan = ? ORDER BY ps.id_pesanan DESC LIMIT 50'
        );
        $stmt->execute([$idPerusahaan]);
        // Baris pencairan tahap 1/2 milik perusahaan ini (untuk riwayat
        // pencairan di dashboard perusahaan): pending + berhasil.
        $pencairanRows = [];
        if ($idPerusahaan !== null) {
            $stmtPc = db()->prepare(
                'SELECT pc.id_pencairan AS id, pc.external_id, pc.id_pesanan AS order_id, pc.tahap, pc.nominal, pc.status, pc.tanggal_permintaan AS created_at, pc.tanggal_cair, pc.xendit_payout_id AS referensi, pc.nama_bank, pc.no_rekening, pc.atas_nama'
                . ' FROM pencairan pc JOIN pesanan ps ON ps.id_pesanan = pc.id_pesanan'
                . " WHERE ps.id_perusahaan = ? AND pc.tahap IN (1, 2) AND pc.penerima = 'perusahaan' ORDER BY pc.id_pencairan DESC LIMIT 100"
            );
            $stmtPc->execute([$idPerusahaan]);
            $pencairanRows = $stmtPc->fetchAll();
        }
    } else {
        $stmt = db()->prepare(
            'SELECT ps.id_pesanan AS id, ps.jumlah AS jumlah_kg, ps.harga_satuan, ps.total_harga AS total, ps.fee_nominal AS fee, ps.status, ps.tanggal_pesanan AS created_at, pr.nama_produk AS nama_produk, per.nama_perusahaan AS nama_perusahaan ' .
            'FROM pesanan ps JOIN penawaran pn ON pn.id_penawaran = ps.id_penawaran JOIN produk pr ON pr.id_produk = pn.id_produk LEFT JOIN perusahaan per ON per.id_perusahaan = ps.id_perusahaan WHERE ps.id_pembeli = ? ORDER BY ps.id_pesanan DESC LIMIT 50'
        );
        $stmt->execute([$idPembeli]);
    }
    $orders = $stmt->fetchAll();
} catch (Throwable $e) {
    json_error('Database belum siap. Jalankan online_marketplace_v2.sql dulu.', 500);
}

foreach ($orders as &$o) {
    $o['id'] = (int) $o['id'];
    $o['jumlah_kg'] = (float) $o['jumlah_kg'];
    $o['harga_satuan'] = (float) $o['harga_satuan'];
    $o['total'] = (float) $o['total'];
    $o['fee'] = (float) $o['fee'];
    // Pembeli hanya membayar total_harga; fee platform dipotong dari dana
    // perusahaan, jadi fee TIDAK ditambahkan ke total bayar pembeli.
    $o['total_bayar'] = $o['total'];
    // Kolom escrow hanya ada di cabang perusahaan; cabang pembeli
    // mengembalikan null (isset aman untuk key yang tidak ada).
    $o['jumlah_ditahan'] = isset($o['jumlah_ditahan']) ? (float) $o['jumlah_ditahan'] : null;
    $o['dp_cair'] = isset($o['dp_cair']) ? (float) $o['dp_cair'] : null;
    $o['sisa_cair'] = isset($o['sisa_cair']) ? (float) $o['sisa_cair'] : null;
    $o['dp_pending'] = isset($o['dp_pending']) ? (float) $o['dp_pending'] : null;
    $o['sisa_pending'] = isset($o['sisa_pending']) ? (float) $o['sisa_pending'] : null;
    $o['sudah_cair'] = isset($o['sudah_cair']) ? (float) $o['sudah_cair'] : null;
}
if (($user['role'] ?? '') === 'perusahaan') {
    foreach ($pencairanRows as &$pc) {
        $pc['id'] = (int) $pc['id'];
        $pc['order_id'] = (int) $pc['order_id'];
        $pc['tahap'] = (int) $pc['tahap'];
        $pc['nominal'] = (float) $pc['nominal'];
    }
    unset($pc);
    json_ok(['orders' => $orders, 'pencairan' => $pencairanRows]);
}
json_ok(['orders' => $orders]);
