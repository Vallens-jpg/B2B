<?php
// Panel admin: tambah user baru (nama, email, password, role pembeli/perusahaan).
// Sama seperti api/auth/register.php, tetapi TIDAK mengganti session admin
// yang sedang login. Jawab {"ok": true, "data": {"user_id": ...}} atau error.
require_once __DIR__ . '/../../lib/db.php';
require_once __DIR__ . '/../../lib/response.php';
require_once __DIR__ . '/../../lib/auth.php';

$user = auth_user();
if (!$user || ($user['role'] ?? '') !== 'admin') {
    json_error('Hanya admin yang boleh menambah user', 403);
}
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    json_error('Metode tidak diizinkan', 405);
}

$input = json_input();
$nama = trim($input['nama'] ?? '');
$email = trim($input['email'] ?? '');
$password = $input['password'] ?? '';
$role = $input['role'] ?? 'pembeli';

if ($nama === '' || $email === '' || $password === '') {
    json_error('Nama, email, dan password wajib diisi', 400);
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    json_error('Format email tidak valid', 400);
}
if (strlen($password) < 6) {
    json_error('Password minimal 6 karakter', 400);
}
if (!in_array($role, ['pembeli', 'perusahaan'], true)) {
    $role = 'pembeli';
}

try {
    $pdo = db();
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        json_error('Email sudah terdaftar', 409);
    }
    $stmt = $pdo->prepare('INSERT INTO users (nama, email, password_hash, role) VALUES (?, ?, ?, ?)');
    $stmt->execute([$nama, $email, password_hash($password, PASSWORD_DEFAULT), $role]);
    $userId = (int) $pdo->lastInsertId();
} catch (Throwable $e) {
    json_error('Database belum siap. Jalankan migrasi 01 dulu.', 500);
}

json_ok(['user_id' => $userId, 'nama' => $nama, 'email' => $email, 'role' => $role]);
