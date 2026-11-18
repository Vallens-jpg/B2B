<?php
// Panel admin: daftar perusahaan + putusan verifikasi.
// GET: daftar users role=perusahaan (id, nama, email, created_at, verifikasi_status, verifikasi_catatan).
// POST {user_id, status: belum|terverifikasi|ditolak, catatan?}: ubah status verifikasi.
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
        $stmt = $pdo->prepare("UPDATE users SET verifikasi_status = ?, verifikasi_catatan = ? WHERE id = ? AND role = 'perusahaan'");
        $stmt->execute([$status, $catatan === '' ? null : $catatan, $userId]);
        if ($stmt->rowCount() === 0) {
            json_error('Perusahaan tidak ditemukan', 404);
        }
        json_ok(['user_id' => $userId, 'verifikasi_status' => $status]);
    }

    $stmt = $pdo->query("SELECT id, nama, email, created_at, verifikasi_status, verifikasi_catatan FROM users WHERE role = 'perusahaan' ORDER BY created_at DESC, id DESC");
    $rows = $stmt->fetchAll();
    foreach ($rows as &$row) {
        $row['id'] = (int) $row['id'];
    }
    json_ok(['perusahaan' => $rows]);
} catch (Throwable $e) {
    json_error('Database belum siap. Jalankan migrasi 01 dan 09 dulu.', 500);
}
