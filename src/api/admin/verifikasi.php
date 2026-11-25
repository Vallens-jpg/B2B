<?php
// Panel admin: daftar perusahaan + putusan verifikasi.
// GET: daftar perusahaan (id, nama, email, created_at, verifikasi_status, verifikasi_catatan).
// POST {user_id (= id_perusahaan), status: belum|terverifikasi|ditolak, catatan?}: ubah status verifikasi.
require_once __DIR__ . '/../../lib/db.php';
require_once __DIR__ . '/../../lib/response.php';
require_once __DIR__ . '/../../lib/auth.php';

$user = auth_user();
if (!$user || ($user['role'] ?? '') !== 'admin') {
    json_error('Hanya admin yang boleh mengelola verifikasi', 403);
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

try {
    $pdo = db();
    if ($method === 'POST') {
        $input = json_input();
        $userId = (int) ($input['user_id'] ?? 0);
        $status = $input['status'] ?? '';
        $catatan = trim($input['catatan'] ?? '');
        if ($userId <= 0 || !in_array($status, ['belum', 'terverifikasi', 'ditolak'], true)) {
            json_error('Data putusan tidak valid', 400);
        }
        // Skema v2: verifikasi tinggal di perusahaan.status_verifikasi
        // ('menunggu','terverifikasi','ditolak'); input lama 'belum' = 'menunggu'.
        $statusSimpan = $status === 'belum' ? 'menunggu' : $status;
        $stmt = $pdo->prepare('UPDATE perusahaan SET status_verifikasi = ?, catatan_verifikasi = ? WHERE id_perusahaan = ?');
        $stmt->execute([$statusSimpan, $catatan === '' ? null : $catatan, $userId]);
        if ($stmt->rowCount() === 0) {
            json_error('Perusahaan tidak ditemukan', 404);
        }
        json_ok(['user_id' => $userId, 'verifikasi_status' => $statusSimpan]);
    }

    $stmt = $pdo->query('SELECT per.id_perusahaan AS id, per.nama_perusahaan AS nama, u.email, u.dibuat_pada AS created_at, per.status_verifikasi AS verifikasi_status, per.catatan_verifikasi AS verifikasi_catatan, per.nib, per.npwp, per.telepon, per.alamat FROM perusahaan per LEFT JOIN pengguna u ON u.id_perusahaan = per.id_perusahaan ORDER BY per.id_perusahaan DESC');
    $rows = $stmt->fetchAll();
    foreach ($rows as &$row) {
        $row['id'] = (int) $row['id'];
    }
    json_ok(['perusahaan' => $rows]);
} catch (Throwable $e) {
    json_error('Database belum siap. Jalankan online_marketplace_v2.sql dulu.', 500);
}
