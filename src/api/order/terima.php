<?php
// Buyer: dikirim -> diterima -> selesai (order_id). Sisa dana cair ke perusahaan.
// Panggilan pertama: dikirim -> diterima. Panggilan kedua: diterima -> selesai + sisa dana cair.
require_once __DIR__ . '/../../lib/db.php';
require_once __DIR__ . '/../../lib/response.php';
require_once __DIR__ . '/../../lib/auth.php';

$user = auth_user();
if (!$user || ($user['role'] ?? '') !== 'pembeli') {
    json_error('Hanya pembeli yang boleh mengonfirmasi penerimaan', 403);
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
    $stmt = $pdo->prepare('SELECT id, buyer_id, total, fee, status FROM orders WHERE id = ? FOR UPDATE');
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
    if ($order['status'] === 'dikirim') {
        $statusBaru = 'diterima';
    } elseif ($order['status'] === 'diterima') {
        $statusBaru = 'selesai';
    } else {
        $pdo->rollBack();
        json_error('Order harus berstatus dikirim atau diterima dulu', 409);
    }
    $stmt = $pdo->prepare('UPDATE orders SET status = ? WHERE id = ?');
    $stmt->execute([$statusBaru, $orderId]);
    $sisaCair = 0.0;
    if ($statusBaru === 'selesai') {
        $totalBayar = (float) $order['total'] + (float) $order['fee'];
        $stmt = $pdo->prepare('SELECT dp_cair FROM escrow WHERE order_id = ? LIMIT 1');
        $stmt->execute([$orderId]);
        $escrow = $stmt->fetch();
        $dp = $escrow ? (float) $escrow['dp_cair'] : round($totalBayar * 0.5, 2);
        $sisaCair = round($totalBayar - $dp, 2);
        $stmt = $pdo->prepare("UPDATE escrow SET sisa_cair = ?, status = 'cair_sisa' WHERE order_id = ?");
        $stmt->execute([$sisaCair, $orderId]);
    }
    $stmt = $pdo->prepare('INSERT INTO riwayat_status (order_id, status_lama, status_baru, dicatat_oleh) VALUES (?, ?, ?, ?)');
    $stmt->execute([$orderId, $order['status'], $statusBaru, (int) $user['id']]);
    $pdo->commit();
} catch (Throwable $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    json_error('Database belum siap. Jalankan migrasi 01 dan 03 dulu.', 500);
}

json_ok(['order_id' => $orderId, 'status' => $statusBaru, 'sisa_cair' => $sisaCair]);
