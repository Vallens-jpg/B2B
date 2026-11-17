<?php
// Admin saja: putusan 'uang kembali ke buyer' atau 'lanjut ke perusahaan'.
require_once __DIR__ . '/../../lib/db.php';
require_once __DIR__ . '/../../lib/response.php';
require_once __DIR__ . '/../../lib/auth.php';

$user = auth_user();
if (!$user || ($user['role'] ?? '') !== 'admin') {
    json_error('Hanya admin yang boleh memutuskan dispute', 403);
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_error('Metode tidak diizinkan', 405);
}

$input = json_input();
$id = (int) ($input['dispute_id'] ?? $input['id'] ?? 0);
$putusan = trim($input['putusan'] ?? '');
if ($id <= 0 || !in_array($putusan, ['uang_kembali', 'lanjut_perusahaan'], true)) {
    json_error('Putusan harus uang_kembali atau lanjut_perusahaan', 400);
}

try {
    $pdo = db();
    $pdo->beginTransaction();
    $stmt = $pdo->prepare('SELECT id, order_id, status FROM dispute WHERE id = ? FOR UPDATE');
    $stmt->execute([$id]);
    $dispute = $stmt->fetch();
    if (!$dispute) {
        $pdo->rollBack();
        json_error('Dispute tidak ditemukan', 404);
    }
    if ($dispute['status'] === 'diputuskan') {
        $pdo->rollBack();
        json_error('Dispute ini sudah diputuskan', 409);
    }
    $stmt = $pdo->prepare("UPDATE dispute SET status = 'diputuskan', putusan = ? WHERE id = ?");
    $stmt->execute([$putusan, $id]);
    if ($putusan === 'uang_kembali') {
        // Dana escrow kembali ke buyer, order dibatalkan, stok dikembalikan.
        $stmt = $pdo->prepare('SELECT id, produk_id, jumlah_kg, status FROM orders WHERE id = ? LIMIT 1');
        $stmt->execute([(int) $dispute['order_id']]);
        $order = $stmt->fetch();
        if ($order && $order['status'] !== 'dibatalkan') {
            $stmt = $pdo->prepare("UPDATE orders SET status = 'dibatalkan', alasan_batal = 'Putusan dispute: uang kembali ke buyer' WHERE id = ?");
            $stmt->execute([(int) $order['id']]);
            $stmt = $pdo->prepare('UPDATE produk SET stok_kg = stok_kg + ? WHERE id = ?');
            $stmt->execute([(float) $order['jumlah_kg'], (int) $order['produk_id']]);
            $stmt = $pdo->prepare("UPDATE escrow SET status = 'kembali' WHERE order_id = ?");
            $stmt->execute([(int) $order['id']]);
            $stmt = $pdo->prepare("UPDATE invoice SET status = 'dibatalkan' WHERE order_id = ?");
            $stmt->execute([(int) $order['id']]);
        }
    }
    $pdo->commit();
} catch (Throwable $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    json_error('Database belum siap. Jalankan migrasi 01 dan 03 dulu.', 500);
}

json_ok(['dispute_id' => $id, 'status' => 'diputuskan', 'putusan' => $putusan]);
