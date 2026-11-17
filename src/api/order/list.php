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
    if (($user['role'] ?? '') === 'perusahaan') {
        // Perusahaan: order atas produk miliknya (sinkron dengan pembeli).
        $stmt = db()->prepare(
            'SELECT o.id, o.jumlah_kg, o.harga_satuan, o.total, o.fee, o.status, o.created_at, p.nama AS nama_produk, u.nama AS nama_buyer ' .
            'FROM orders o JOIN produk p ON p.id = o.produk_id JOIN users u ON u.id = o.buyer_id WHERE p.perusahaan_id = ? ORDER BY o.id DESC LIMIT 50'
        );
        $stmt->execute([(int) $user['id']]);
    } else {
        $stmt = db()->prepare(
            'SELECT o.id, o.jumlah_kg, o.harga_satuan, o.total, o.fee, o.status, o.created_at, p.nama AS nama_produk ' .
            'FROM orders o JOIN produk p ON p.id = o.produk_id WHERE o.buyer_id = ? ORDER BY o.id DESC LIMIT 50'
        );
        $stmt->execute([(int) $user['id']]);
    }
    $orders = $stmt->fetchAll();
} catch (Throwable $e) {
    json_error('Database belum siap. Jalankan migrasi 01 dan 03 dulu.', 500);
}

foreach ($orders as &$o) {
    $o['id'] = (int) $o['id'];
    $o['jumlah_kg'] = (float) $o['jumlah_kg'];
    $o['harga_satuan'] = (float) $o['harga_satuan'];
    $o['total'] = (float) $o['total'];
    $o['fee'] = (float) $o['fee'];
    $o['total_bayar'] = $o['total'] + $o['fee'];
}
json_ok(['orders' => $orders]);
