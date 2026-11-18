<?php
// Panel admin: daftar pengguna + kunci/buka user.
// GET: daftar users (id, nama, email, role, status, created_at).
// POST {user_id, status: aktif|nonaktif}: ubah status user (dipakai tombol Lock/Unlock).
require_once __DIR__ . '/../../lib/db.php';
require_once __DIR__ . '/../../lib/response.php';
require_once __DIR__ . '/../../lib/auth.php';

$user = auth_user();
if (!$user || ($user['role'] ?? '') !== 'admin') {
    json_error('Hanya admin yang boleh melihat daftar pengguna', 403);
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

try {
    $pdo = db();
    if ($method === 'POST') {
        $input = json_input();
        $userId = (int) ($input['user_id'] ?? 0);
        $status = $input['status'] ?? '';
        if ($userId <= 0 || !in_array($status, ['aktif', 'nonaktif'], true)) {
            json_error('Data status tidak valid', 400);
        }
        if ($userId === (int) $user['id']) {
            json_error('Admin tidak boleh mengunci akun sendiri', 400);
        }
        $stmt = $pdo->prepare('UPDATE users SET status = ? WHERE id = ?');
        $stmt->execute([$status, $userId]);
        if ($stmt->rowCount() === 0) {
            json_error('User tidak ditemukan', 404);
        }
        json_ok(['user_id' => $userId, 'status' => $status]);
    }

    $stmt = $pdo->query('SELECT id, nama, email, role, status, created_at FROM users ORDER BY id');
    $pengguna = $stmt->fetchAll();
    foreach ($pengguna as &$row) {
        $row['id'] = (int) $row['id'];
    }
} catch (Throwable $e) {
    json_error('Database belum siap. Jalankan migrasi 01 dan 09 dulu.', 500);
}

json_ok($pengguna);
