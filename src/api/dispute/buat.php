<?php
// Buyer buka tiket dispute + upload bukti foto.
require_once __DIR__ . '/../../lib/db.php';
require_once __DIR__ . '/../../lib/response.php';
require_once __DIR__ . '/../../lib/auth.php';

$user = auth_user();
if (!$user || ($user['role'] ?? '') !== 'pembeli') {
    json_error('Hanya pembeli yang boleh membuka dispute', 403);
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_error('Metode tidak diizinkan', 405);
}

$input = json_input();
$orderId = (int) ($input['order_id'] ?? 0);
$deskripsi = trim($input['deskripsi'] ?? '');
$buktiFoto = trim($input['bukti_foto'] ?? '');
if ($orderId <= 0 || $deskripsi === '') {
    json_error('Order dan deskripsi dispute wajib diisi', 400);
}

try {
    $pdo = db();
    // Resolve id profil pembeli dari session (id_pengguna != id_pembeli).
    $stmt = $pdo->prepare('SELECT id_pembeli FROM pengguna WHERE id_pengguna = ? LIMIT 1');
    $stmt->execute([(int) $user['id']]);
    $profil = $stmt->fetch();
    $idPembeli = $profil ? (int) $profil['id_pembeli'] : 0;
    $stmt = $pdo->prepare('SELECT ps.id_pesanan, ps.id_pembeli, pr.nama_produk FROM pesanan ps JOIN penawaran pn ON pn.id_penawaran = ps.id_penawaran JOIN produk pr ON pr.id_produk = pn.id_produk WHERE ps.id_pesanan = ? LIMIT 1');
    $stmt->execute([$orderId]);
    $order = $stmt->fetch();
    if (!$order) {
        json_error('Order tidak ditemukan', 404);
    }
    if ((int) $order['id_pembeli'] !== $idPembeli) {
        json_error('Order ini bukan milik Anda', 403);
    }
    $stmt = $pdo->prepare("SELECT id_dispute FROM dispute WHERE id_pesanan = ? AND status IN ('diajukan', 'diproses') LIMIT 1");
    $stmt->execute([$orderId]);
    if ($stmt->fetch()) {
        json_error('Order ini sudah punya dispute yang masih dibuka', 409);
    }
    // Judul dispute otomatis dari nama produk (nama_barang); tidak ada kolom judul di skema v2.
    $stmt = $pdo->prepare("INSERT INTO dispute (id_pesanan, nama_barang, kondisi_barang, keterangan, foto_bukti, status) VALUES (?, ?, NULL, ?, ?, 'diajukan')");
    $stmt->execute([$orderId, $order['nama_produk'], $deskripsi, $buktiFoto !== '' ? $buktiFoto : null]);
    $disputeId = (int) $pdo->lastInsertId();
} catch (Throwable $e) {
    json_error('Database belum siap. Jalankan online_marketplace_v2.sql dulu.', 500);
}

json_ok(['dispute_id' => $disputeId, 'status' => 'diajukan']);
