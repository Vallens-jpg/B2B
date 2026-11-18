<?php
// Tampilkan invoice (?id= nomor invoice).
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
    $stmt = $pdo->prepare(
        'SELECT i.*, o.buyer_id, o.status AS status_order, p.nama AS nama_produk, p.perusahaan_id, u.nama AS nama_buyer ' .
        'FROM invoice i JOIN orders o ON o.id = i.order_id JOIN produk p ON p.id = o.produk_id JOIN users u ON u.id = o.buyer_id WHERE i.id = ? LIMIT 1'
    );
    $stmt->execute([$id]);
    $invoice = $stmt->fetch();
    if (!$invoice) {
        json_error('Invoice tidak ditemukan', 404);
    }
    $role = $user['role'] ?? '';
    $boleh = (int) $invoice['buyer_id'] === (int) $user['id']
        || ($role === 'perusahaan' && (int) $invoice['perusahaan_id'] === (int) $user['id'])
        || $role === 'admin';
    if (!$boleh) {
        json_error('Invoice ini bukan milik Anda', 403);
    }
} catch (Throwable $e) {
    json_error('Database belum siap. Jalankan migrasi 01 dan 03 dulu.', 500);
}

$invoice['id'] = (int) $invoice['id'];
$invoice['total'] = (float) $invoice['total'];

json_ok($invoice);
