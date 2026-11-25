<?php
// Tampilkan invoice (?id= pembayaran.id_pembayaran).
require_once __DIR__ . '/../../lib/db.php';
require_once __DIR__ . '/../../lib/response.php';
require_once __DIR__ . '/../../lib/auth.php';

$user = auth_user();
if (!$user) {
    json_error('Silakan login dulu', 401);
}
$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) {
    json_error('Invoice tidak ditemukan', 404);
}

try {
    $pdo = db();
    // Session menyimpan pengguna.id_pengguna, bukan id_pembeli/id_perusahaan: resolve dulu.
    $stmt = $pdo->prepare('SELECT id_pembeli, id_perusahaan FROM pengguna WHERE id_pengguna = ? LIMIT 1');
    $stmt->execute([(int) $user['id']]);
    $profil = $stmt->fetch();
    $idPembeli = ($profil && $profil['id_pembeli'] !== null) ? (int) $profil['id_pembeli'] : 0;
    $idPerusahaan = ($profil && $profil['id_perusahaan'] !== null) ? (int) $profil['id_perusahaan'] : 0;

    $stmt = $pdo->prepare(
        'SELECT b.id_pembayaran AS id, b.xendit_id AS nomor, b.jumlah AS total, b.status, b.invoice_url, b.paid_at, b.tanggal_pembayaran, ' .
        'b.id_pesanan AS order_id, ps.status AS status_order, ps.id_pembeli AS buyer_id, ps.id_perusahaan AS perusahaan_id, ' .
        'pr.nama_produk AS nama_produk, pb.nama_pembeli AS nama_buyer ' .
        'FROM pembayaran b JOIN pesanan ps ON ps.id_pesanan = b.id_pesanan ' .
        'JOIN penawaran pn ON pn.id_penawaran = ps.id_penawaran ' .
        'JOIN produk pr ON pr.id_produk = pn.id_produk ' .
        'JOIN pembeli pb ON pb.id_pembeli = ps.id_pembeli WHERE b.id_pembayaran = ? LIMIT 1'
    );
    $stmt->execute([$id]);
    $invoice = $stmt->fetch();
    if (!$invoice) {
        json_error('Invoice tidak ditemukan', 404);
    }
    $role = $user['role'] ?? '';
    $boleh = (int) $invoice['buyer_id'] === $idPembeli
        || ($role === 'perusahaan' && (int) $invoice['perusahaan_id'] === $idPerusahaan)
        || $role === 'admin';
    if (!$boleh) {
        json_error('Invoice ini bukan milik Anda', 403);
    }
} catch (Throwable $e) {
    json_error('Database belum siap. Jalankan online_marketplace_v2.sql dulu.', 500);
}

$invoice['id'] = (int) $invoice['id'];
$invoice['total'] = (float) $invoice['total'];

json_ok($invoice);
