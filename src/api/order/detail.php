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
    $stmt = $pdo->prepare(
        'SELECT o.*, p.nama AS nama_produk, p.perusahaan_id, u.nama AS nama_buyer, u2.nama AS nama_perusahaan, i.nomor AS nomor_invoice ' .
        'FROM orders o JOIN produk p ON p.id = o.produk_id JOIN users u ON u.id = o.buyer_id LEFT JOIN users u2 ON u2.id = p.perusahaan_id LEFT JOIN invoice i ON i.order_id = o.id WHERE o.id = ? LIMIT 1'
    );
    $stmt->execute([$id]);
    $order = $stmt->fetch();
    if (!$order) {
        json_error('Order tidak ditemukan', 404);
    }
    $role = $user['role'] ?? '';
    $isBuyer = (int) $order['buyer_id'] === (int) $user['id'];
    $isPerusahaan = $role === 'perusahaan' && (int) $order['perusahaan_id'] === (int) $user['id'];
    $isAdmin = $role === 'admin';
    if (!$isBuyer && !$isPerusahaan && !$isAdmin) {
        json_error('Order ini bukan milik Anda', 403);
    }
    $stmt = $pdo->prepare('SELECT status_lama, status_baru, created_at FROM riwayat_status WHERE order_id = ? ORDER BY id');
    $stmt->execute([$id]);
    $riwayat = $stmt->fetchAll();
} catch (Throwable $e) {
    json_error('Database belum siap. Jalankan migrasi 01 dan 03 dulu.', 500);
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
    $tombol[] = 'ulasan';
    $tombol[] = 'dispute';
}

$order['id'] = (int) $order['id'];
$order['jumlah_kg'] = (float) $order['jumlah_kg'];
$order['harga_satuan'] = (float) $order['harga_satuan'];
$order['total'] = (float) $order['total'];
$order['fee'] = (float) $order['fee'];
$order['total_bayar'] = (float) $order['total'] + (float) $order['fee'];

json_ok(['order' => $order, 'riwayat' => $riwayat, 'tombol' => $tombol]);
