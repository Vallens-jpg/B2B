<?php
// Buat pembayaran (order_id). MODE DEMO tanpa Xendit: pembayaran dicatat langsung sebagai dibayar.
// Status TETAP menunggu_bayar sampai pembayaran ini berhasil; webhook Xendit menggantikan
// langkah ini saat kunci Xendit sudah tersedia (lihat src/api/webhook/xendit.php).
require_once __DIR__ . '/../../lib/db.php';
require_once __DIR__ . '/../../lib/response.php';
require_once __DIR__ . '/../../lib/auth.php';

$user = auth_user();
if (!$user || ($user['role'] ?? '') !== 'pembeli') {
    json_error('Hanya pembeli yang boleh membayar', 403);
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_error('Metode tidak diizinkan', 405);
}

$input = json_input();
$orderId = (int) ($input['order_id'] ?? 0);
if ($orderId <= 0) {
    json_error('Order tidak ditemukan', 404);
}

try {
    $pdo = db();
    $pdo->beginTransaction();
    $stmt = $pdo->prepare('SELECT id, buyer_id, status FROM orders WHERE id = ? FOR UPDATE');
    $stmt->execute([$orderId]);
    $order = $stmt->fetch();
    if (!$order) {
        $pdo->rollBack();
        json_error('Order tidak ditemukan', 404);
    }
    if ((int) $order['buyer_id'] !== (int) $user['id']) {
        $pdo->rollBack();
        json_error('Order ini bukan milik Anda', 403);
    }
    if ($order['status'] === 'dibayar') {
        $pdo->commit();
        json_ok(['order_id' => $orderId, 'status' => 'dibayar', 'catatan' => 'Order ini sudah dibayar sebelumnya.']);
    }
    if ($order['status'] !== 'menunggu_bayar') {
        $pdo->rollBack();
        json_error('Order ini sudah tidak bisa dibayar lagi', 409);
    }
    $stmt = $pdo->prepare("UPDATE orders SET status = 'dibayar' WHERE id = ?");
    $stmt->execute([$orderId]);
    $stmt = $pdo->prepare("UPDATE invoice SET status = 'dibayar' WHERE order_id = ?");
    $stmt->execute([$orderId]);
    $stmt = $pdo->prepare('INSERT INTO riwayat_status (order_id, status_lama, status_baru, dicatat_oleh) VALUES (?, ?, ?, ?)');
    $stmt->execute([$orderId, 'menunggu_bayar', 'dibayar', (int) $user['id']]);
    $pdo->commit();
} catch (Throwable $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    json_error('Database belum siap. Jalankan migrasi 01 dan 03 dulu.', 500);
}

json_ok(['order_id' => $orderId, 'status' => 'dibayar']);
