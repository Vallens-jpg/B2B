<?php
// Daftar dispute milik pengguna (pembeli: miliknya; perusahaan: produknya; admin: semua).
require_once __DIR__ . '/../../lib/db.php';
require_once __DIR__ . '/../../lib/response.php';
require_once __DIR__ . '/../../lib/auth.php';

$user = auth_user();
if (!$user) {
    json_error('Silakan login dulu', 401);
}
$role = $user['role'] ?? '';
try {
    $pdo = db();
    $sql = 'SELECT d.id, d.order_id, d.judul, d.status, d.putusan, d.created_at,'
        . ' o.total_bayar, o.jumlah_kg, p.nama AS nama_produk,'
        . ' u.nama AS nama_perusahaan, ub.nama AS nama_buyer'
        . ' FROM dispute d'
        . ' JOIN orders o ON o.id = d.order_id'
        . ' JOIN produk p ON p.id = o.produk_id'
        . ' LEFT JOIN users u ON u.id = p.perusahaan_id'
        . ' LEFT JOIN users ub ON ub.id = d.buyer_id';
    $params = [];
    if ($role === 'pembeli') {
        $sql .= ' WHERE d.buyer_id = ?';
        $params[] = (int) $user['id'];
    } elseif ($role === 'perusahaan') {
        $sql .= ' WHERE p.perusahaan_id = ?';
        $params[] = (int) $user['id'];
    }
    $sql .= ' ORDER BY d.created_at DESC LIMIT 50';
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll();
    foreach ($rows as &$r) {
        $r['id'] = (int) $r['id'];
        $r['order_id'] = (int) $r['order_id'];
        $r['total_bayar'] = (float) $r['total_bayar'];
    }
    unset($r);
} catch (Throwable $e) {
    json_error('Database belum siap. Jalankan migrasi 01 dan 03 dulu.', 500);
}
json_ok(['dispute' => $rows]);
