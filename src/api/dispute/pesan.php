<?php
// Diskusi dispute: GET ?dispute_id= (daftar pesan) / POST {dispute_id, pesan} (kirim pesan).
require_once __DIR__ . '/../../lib/db.php';
require_once __DIR__ . '/../../lib/response.php';
require_once __DIR__ . '/../../lib/auth.php';

$user = auth_user();
if (!$user) {
    json_error('Silakan login dulu', 401);
}
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$disputeId = (int) ($_GET['dispute_id'] ?? ($_POST['dispute_id'] ?? 0));
if ($method === 'POST') {
    $input = json_input();
    $disputeId = (int) ($input['dispute_id'] ?? $disputeId);
}
if ($disputeId <= 0) {
    json_error('Dispute tidak ditemukan', 404);
}

try {
    $pdo = db();
    $stmt = $pdo->prepare(
        'SELECT d.*, o.produk_id, p.perusahaan_id FROM dispute d JOIN orders o ON o.id = d.order_id JOIN produk p ON p.id = o.produk_id WHERE d.id = ? LIMIT 1'
    );
    $stmt->execute([$disputeId]);
    $dispute = $stmt->fetch();
    if (!$dispute) {
        json_error('Dispute tidak ditemukan', 404);
    }
    $role = $user['role'] ?? '';
    $boleh = (int) $dispute['buyer_id'] === (int) $user['id']
        || ($role === 'perusahaan' && (int) $dispute['perusahaan_id'] === (int) $user['id'])
        || $role === 'admin';
    if (!$boleh) {
        json_error('Dispute ini bukan milik Anda', 403);
    }
    if ($method === 'POST') {
        $input = json_input();
        $pesan = trim($input['pesan'] ?? '');
        if ($pesan === '') {
            json_error('Pesan tidak boleh kosong', 400);
        }
        if (($dispute['status'] ?? '') === 'diputuskan') {
            json_error('Dispute sudah diputuskan, diskusi ditutup', 409);
        }
        $stmt = $pdo->prepare('INSERT INTO dispute_pesan (dispute_id, user_id, pesan) VALUES (?, ?, ?)');
        $stmt->execute([$disputeId, (int) $user['id'], $pesan]);
        $pesanId = (int) $pdo->lastInsertId();
        json_ok(['pesan_id' => $pesanId]);
    }
    $stmt = $pdo->prepare(
        'SELECT dp.id, dp.pesan, dp.created_at, u.nama, u.role FROM dispute_pesan dp JOIN users u ON u.id = dp.user_id WHERE dp.dispute_id = ? ORDER BY dp.created_at ASC, dp.id ASC LIMIT 100'
    );
    $stmt->execute([$disputeId]);
    $rows = $stmt->fetchAll();
    foreach ($rows as &$r) {
        $r['id'] = (int) $r['id'];
    }
    unset($r);
} catch (Throwable $e) {
    json_error('Database belum siap. Jalankan migrasi 01, 03, dan 08 dulu.', 500);
}
json_ok(['pesan' => $rows]);
