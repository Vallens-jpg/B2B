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
    // Resolve id profil dari session (rule global 2): $user['id'] = id_pengguna.
    $stmtProfil = $pdo->prepare('SELECT id_pembeli, id_perusahaan, id_admin FROM pengguna WHERE id_pengguna = ? LIMIT 1');
    $stmtProfil->execute([(int) $user['id']]);
    $profil = $stmtProfil->fetch();
    $idPembeli = (int) ($profil['id_pembeli'] ?? 0);
    $idPerusahaan = (int) ($profil['id_perusahaan'] ?? 0);
    $stmt = $pdo->prepare(
        'SELECT d.*, ps.id_pembeli AS buyer_id, ps.id_perusahaan AS perusahaan_id, pn.id_produk AS produk_id FROM dispute d JOIN pesanan ps ON ps.id_pesanan = d.id_pesanan JOIN penawaran pn ON pn.id_penawaran = ps.id_penawaran WHERE d.id_dispute = ? LIMIT 1'
    );
    $stmt->execute([$disputeId]);
    $dispute = $stmt->fetch();
    if (!$dispute) {
        json_error('Dispute tidak ditemukan', 404);
    }
    $role = $user['role'] ?? '';
    $boleh = (int) $dispute['buyer_id'] === $idPembeli
        || ($role === 'perusahaan' && (int) $dispute['perusahaan_id'] === $idPerusahaan)
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
        if (($dispute['status'] ?? '') === 'selesai') {
            json_error('Dispute sudah diputuskan, diskusi ditutup', 409);
        }
        $stmt = $pdo->prepare('INSERT INTO dispute_pesan (id_dispute, id_pengguna, pesan) VALUES (?, ?, ?)');
        $stmt->execute([$disputeId, (int) $user['id'], $pesan]);
        $pesanId = (int) $pdo->lastInsertId();
        json_ok(['pesan_id' => $pesanId]);
    }
    $stmt = $pdo->prepare(
        'SELECT dp.id_pesan AS id, dp.pesan, dp.dibuat_pada AS created_at, u.nama_pengguna AS nama, u.role FROM dispute_pesan dp JOIN pengguna u ON u.id_pengguna = dp.id_pengguna WHERE dp.id_dispute = ? ORDER BY dp.dibuat_pada ASC, dp.id_pesan ASC LIMIT 100'
    );
    $stmt->execute([$disputeId]);
    $rows = $stmt->fetchAll();
    foreach ($rows as &$r) {
        $r['id'] = (int) $r['id'];
    }
    unset($r);
} catch (Throwable $e) {
    json_error('Database belum siap. Jalankan online_marketplace_v2.sql dulu.', 500);
}
json_ok(['pesan' => $rows]);
