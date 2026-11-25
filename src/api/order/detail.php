<?php
// Detail order (?id=) + status + riwayat + tombol yang boleh muncul. Tolak kalau order milik orang lain.
require_once __DIR__ . '/../../lib/db.php';
require_once __DIR__ . '/../../lib/response.php';
require_once __DIR__ . '/../../lib/auth.php';

$user = auth_user();
if (!$user) {
    json_error('Silakan login dulu', 401);
}
$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) {
    json_error('Order tidak ditemukan', 404);
}

try {
    $pdo = db();
    // $user['id'] adalah pengguna.id_pengguna, bukan id profil: resolve dulu
    // ke id_pembeli / id_perusahaan sebelum membandingkan dengan pesanan.
    $stmt = $pdo->prepare('SELECT id_pembeli, id_perusahaan FROM pengguna WHERE id_pengguna = ? LIMIT 1');
    $stmt->execute([(int) $user['id']]);
    $profil = $stmt->fetch();
    $idPembeli = $profil['id_pembeli'] !== null ? (int) $profil['id_pembeli'] : null;
    $idPerusahaan = $profil['id_perusahaan'] !== null ? (int) $profil['id_perusahaan'] : null;

    $stmt = $pdo->prepare(
        'SELECT ps.id_pesanan AS id, ps.id_pembeli AS buyer_id, ps.id_perusahaan AS perusahaan_id, pn.id_produk AS produk_id, ps.jumlah AS jumlah_kg, ps.harga_satuan, ps.total_harga AS total, ps.fee_nominal AS fee, ps.status, ps.rating, ps.ulasan, ps.tanggal_pesanan AS created_at, ps.alasan_batal, pg.tanggal_pengiriman AS tanggal_kirim, pg.jasa_pengiriman AS jasa_kirim, pg.no_resi AS resi, pg.foto_bukti AS foto_bukti, pr.nama_produk AS nama_produk, pb.nama_pembeli AS nama_buyer, per.nama_perusahaan AS nama_perusahaan, byr.xendit_id AS nomor_invoice ' .
        'FROM pesanan ps JOIN penawaran pn ON pn.id_penawaran = ps.id_penawaran JOIN produk pr ON pr.id_produk = pn.id_produk JOIN pembeli pb ON pb.id_pembeli = ps.id_pembeli LEFT JOIN perusahaan per ON per.id_perusahaan = ps.id_perusahaan LEFT JOIN pembayaran byr ON byr.id_pesanan = ps.id_pesanan LEFT JOIN pengiriman pg ON pg.id_pesanan = ps.id_pesanan WHERE ps.id_pesanan = ? LIMIT 1'
    );
    $stmt->execute([$id]);
    $order = $stmt->fetch();
    if (!$order) {
        json_error('Order tidak ditemukan', 404);
    }
    $role = $user['role'] ?? '';
    $isBuyer = $idPembeli !== null && (int) $order['buyer_id'] === $idPembeli;
    $isPerusahaan = $role === 'perusahaan' && $idPerusahaan !== null && (int) $order['perusahaan_id'] === $idPerusahaan;
    $isAdmin = $role === 'admin';
    if (!$isBuyer && !$isPerusahaan && !$isAdmin) {
        json_error('Order ini bukan milik Anda', 403);
    }
    $stmt = $pdo->prepare('SELECT status_lama, status_baru, dibuat_pada AS created_at FROM riwayat_status WHERE id_pesanan = ? ORDER BY id_riwayat');
    $stmt->execute([$id]);
    $riwayat = $stmt->fetchAll();
} catch (Throwable $e) {
    json_error('Database belum siap. Jalankan online_marketplace_v2.sql dulu.', 500);
}

$tombol = [];
$status = $order['status'];
if ($isBuyer && in_array($status, ['menunggu_bayar'], true)) {
    $tombol[] = 'bayar';
}
if ($isBuyer && in_array($status, ['menunggu_bayar', 'dibayar'], true)) {
    $tombol[] = 'batal';
}
if ($isPerusahaan && $status === 'dibayar') {
    $tombol[] = 'proses';
}
if ($isPerusahaan && $status === 'diproses') {
    $tombol[] = 'kirim';
}
if ($isBuyer && $status === 'dikirim') {
    $tombol[] = 'terima';
}
if ($isBuyer && $status === 'selesai') {
    // Tombol ulasan hanya kalau pesanan ini belum pernah diberi ulasan
    // (rating null = belum ada ulasan; komentar boleh kosong).
    if ($order['rating'] === null) {
        $tombol[] = 'ulasan';
    }
    $tombol[] = 'dispute';
}

$order['id'] = (int) $order['id'];
$order['jumlah_kg'] = (float) $order['jumlah_kg'];
$order['harga_satuan'] = (float) $order['harga_satuan'];
$order['total'] = (float) $order['total'];
$order['fee'] = (float) $order['fee'];
$order['rating'] = $order['rating'] !== null ? (int) $order['rating'] : null;
// Pembeli hanya membayar total_harga; fee platform dipotong dari dana
// perusahaan, jadi fee TIDAK ditambahkan ke total bayar pembeli.
$order['total_bayar'] = (float) $order['total'];

json_ok(['order' => $order, 'riwayat' => $riwayat, 'tombol' => $tombol]);
