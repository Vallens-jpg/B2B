<?php
// Buyer beri rating/ulasan setelah order selesai.
require_once __DIR__ . '/../../lib/db.php';
require_once __DIR__ . '/../../lib/response.php';
require_once __DIR__ . '/../../lib/auth.php';

$user = auth_user();
if (!$user || ($user['role'] ?? '') !== 'pembeli') {
    json_error('Hanya pembeli yang boleh memberi ulasan', 403);
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_error('Metode tidak diizinkan', 405);
}

$input = json_input();
$orderId = (int) ($input['order_id'] ?? 0);
$rating = (int) ($input['rating'] ?? 0);
$komentar = trim($input['komentar'] ?? '');
if ($orderId <= 0 || $rating < 1 || $rating > 5) {
    json_error('Order dan rating 1-5 wajib diisi', 400);
}

try {
    $pdo = db();
    $stmt = $pdo->prepare('SELECT id, buyer_id, produk_id, status FROM orders WHERE id = ? LIMIT 1');
    $stmt->execute([$orderId]);
    $order = $stmt->fetch();
    if (!$order) {
        json_error('Order tidak ditemukan', 404);
    }
    if ((int) $order['buyer_id'] !== (int) $user['id']) {
        json_error('Order ini bukan milik Anda', 403);
    }
    if ($order['status'] !== 'selesai') {
        json_error('Ulasan hanya bisa diberikan setelah order selesai', 409);
    }
    $stmt = $pdo->prepare('SELECT id FROM ulasan WHERE order_id = ? LIMIT 1');
    $stmt->execute([$orderId]);
    if ($stmt->fetch()) {
        json_error('Order ini sudah diberi ulasan', 409);
    }
    $stmt = $pdo->prepare('INSERT INTO ulasan (order_id, produk_id, buyer_id, rating, komentar) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([$orderId, (int) $order['produk_id'], (int) $user['id'], $rating, $komentar !== '' ? $komentar : null]);
    $ulasanId = (int) $pdo->lastInsertId();
} catch (Throwable $e) {
    json_error('Database belum siap. Jalankan migrasi 01 dan 03 dulu.', 500);
}

json_ok(['ulasan_id' => $ulasanId, 'rating' => $rating]);
