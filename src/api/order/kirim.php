<?php
// Perusahaan: diproses -> dikirim (order_id, resi boleh kosong). DP cair ke perusahaan.
// DP = 50% dari total bayar.
require_once __DIR__ . '/../../lib/db.php';
require_once __DIR__ . '/../../lib/response.php';
require_once __DIR__ . '/../../lib/auth.php';

$user = auth_user();
if (!$user || ($user['role'] ?? '') !== 'perusahaan') {
    json_error('Hanya perusahaan yang boleh mengirim order', 403);
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_error('Metode tidak diizinkan', 405);
}

$input = json_input();
$orderId = (int) ($input['order_id'] ?? 0);
$resi = trim($input['resi'] ?? '');
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
    if ((int) $order['perusahaan_id'] !== (int) $user['id']) {
        $pdo->rollBack();
        json_error('Order ini bukan milik perusahaan Anda', 403);
    }
    if ($order['status'] !== 'diproses') {
        $pdo->rollBack();
        json_error('Order harus berstatus diproses dulu', 409);
    }
    $totalBayar = (float) $order['total'] + (float) $order['fee'];
    $dp = round($totalBayar * 0.5, 2);
    $stmt = $pdo->prepare("UPDATE orders SET status = 'dikirim', resi = ? WHERE id = ?");
    $stmt->execute([$resi !== '' ? $resi : null, $orderId]);
    $stmt = $pdo->prepare("UPDATE escrow SET dp_cair = ?, status = 'cair_dp' WHERE order_id = ?");
    $stmt->execute([$dp, $orderId]);
    $stmt = $pdo->prepare('INSERT INTO riwayat_status (order_id, status_lama, status_baru, dicatat_oleh) VALUES (?, ?, ?, ?)');
    $stmt->execute([$orderId, 'diproses', 'dikirim', (int) $user['id']]);
    $pdo->commit();
} catch (Throwable $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    json_error('Database belum siap. Jalankan migrasi 01 dan 03 dulu.', 500);
}

json_ok(['order_id' => $orderId, 'status' => 'dikirim', 'dp_cair' => $dp, 'resi' => $resi !== '' ? $resi : null]);
