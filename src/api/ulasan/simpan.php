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
    // Resolve id profil pembeli dari session (id_pengguna != id_pembeli).
    $stmt = $pdo->prepare('SELECT id_pembeli FROM pengguna WHERE id_pengguna = ? LIMIT 1');
    $stmt->execute([(int) $user['id']]);
    $profil = $stmt->fetch();
    $idPembeli = $profil ? (int) $profil['id_pembeli'] : 0;
    $stmt = $pdo->prepare('SELECT id_pesanan, id_pembeli, status, ulasan FROM pesanan WHERE id_pesanan = ? LIMIT 1');
    $stmt->execute([$orderId]);
    $order = $stmt->fetch();
    if (!$order) {
        json_error('Order tidak ditemukan', 404);
    }
    if ((int) $order['id_pembeli'] !== $idPembeli) {
        json_error('Order ini bukan milik Anda', 403);
    }
    if ($order['status'] !== 'selesai') {
        json_error('Ulasan hanya bisa diberikan setelah order selesai', 409);
    }
    if ($order['ulasan'] !== null) {
        json_error('Order ini sudah diberi ulasan', 409);
    }
    // Tidak ada lagi tabel ulasan di v2: rating + ulasan disimpan di pesanan.
    $stmt = $pdo->prepare('UPDATE pesanan SET rating = ?, ulasan = ? WHERE id_pesanan = ?');
    $stmt->execute([$rating, $komentar !== '' ? $komentar : null, $orderId]);
} catch (Throwable $e) {
    json_error('Database belum siap. Jalankan online_marketplace_v2.sql dulu.', 500);
}

// Tidak ada baris ulasan terpisah di v2: ulasan_id kini sama dengan id pesanan.
json_ok(['ulasan_id' => $orderId, 'rating' => $rating]);
