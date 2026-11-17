<?php
// Batalkan pesanan (order_id, alasan). Kalau sudah dibayar, dana kembali ke buyer.
require_once __DIR__ . '/../../lib/db.php';
require_once __DIR__ . '/../../lib/response.php';
require_once __DIR__ . '/../../lib/auth.php';

$user = auth_user();
if (!$user) {
    json_error('Silakan login dulu', 401);
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_error('Metode tidak diizinkan', 405);
}

$input = json_input();
$orderId = (int) ($input['order_id'] ?? 0);
$alasan = trim($input['alasan'] ?? '');
if ($orderId <= 0) {
    json_error('Order tidak ditemukan', 404);
}

try {
    $pdo = db();
    $pdo->beginTransaction();
    $stmt = $pdo->prepare('SELECT o.*, p.perusahaan_id FROM orders o JOIN produk p ON p.id = o.produk_id WHERE o.id = ? FOR UPDATE');
    $stmt->execute([$orderId]);
    $order = $stmt->fetch();
    if (!$order) {
        $pdo->rollBack();
        json_error('Order tidak ditemukan', 404);
    }
    $isBuyer = (int) $order['buyer_id'] === (int) $user['id'];
    $isAdmin = ($user['role'] ?? '') === 'admin';
    if (!$isBuyer && !$isAdmin) {
        $pdo->rollBack();
        json_error('Order ini bukan milik Anda', 403);
    }
    if (!in_array($order['status'], ['menunggu_bayar', 'dibayar'], true)) {
        $pdo->rollBack();
        json_error('Order ini sudah tidak bisa dibatalkan lagi', 409);
    }
    $sudahDibayar = $order['status'] === 'dibayar';
    $stmt = $pdo->prepare("UPDATE orders SET status = 'dibatalkan', alasan_batal = ? WHERE id = ?");
    $stmt->execute([$alasan !== '' ? $alasan : null, $orderId]);
    // Kembalikan stok.
    $stmt = $pdo->prepare('UPDATE produk SET stok_kg = stok_kg + ? WHERE id = ?');
    $stmt->execute([(float) $order['jumlah_kg'], (int) $order['produk_id']]);
    $stmt = $pdo->prepare("UPDATE invoice SET status = 'dibatalkan' WHERE order_id = ?");
    $stmt->execute([$orderId]);
    if ($sudahDibayar) {
        $stmt = $pdo->prepare("UPDATE escrow SET status = 'kembali' WHERE order_id = ?");
        $stmt->execute([$orderId]);
    }
    $stmt = $pdo->prepare('INSERT INTO riwayat_status (order_id, status_lama, status_baru, dicatat_oleh) VALUES (?, ?, ?, ?)');
    $stmt->execute([$orderId, $order['status'], 'dibatalkan', (int) $user['id']]);
    $pdo->commit();
} catch (Throwable $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    json_error('Database belum siap. Jalankan migrasi 01 dan 03 dulu.', 500);
}

json_ok(['order_id' => $orderId, 'status' => 'dibatalkan', 'dana_kembali' => $sudahDibayar]);
