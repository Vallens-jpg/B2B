<?php
// Panel admin: daftar pengguna.
require_once __DIR__ . '/../../lib/db.php';
require_once __DIR__ . '/../../lib/response.php';
require_once __DIR__ . '/../../lib/auth.php';

$user = auth_user();
if (!$user || ($user['role'] ?? '') !== 'admin') {
    json_error('Hanya admin yang boleh melihat daftar pengguna', 403);
}

try {
    $stmt = db()->query('SELECT id, nama, email, role, created_at FROM users ORDER BY id');
    $pengguna = $stmt->fetchAll();
    foreach ($pengguna as &$row) {
        $row['id'] = (int) $row['id'];
    }
} catch (Throwable $e) {
    json_error('Database belum siap. Jalankan migrasi 01 dulu.', 500);
}

json_ok($pengguna);
