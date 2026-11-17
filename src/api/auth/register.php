<?php
// Daftar user baru (nama, email, password, role pembeli/perusahaan). Jawab {"ok": true, "data": {"user_id": ...}} atau {"ok": false, "error": "..."}.
require_once __DIR__ . '/../../lib/db.php';
require_once __DIR__ . '/../../lib/response.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_error('Metode tidak diizinkan', 405);
}

$input = json_input();
$nama = trim($input['nama'] ?? $input['name'] ?? '');
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

$_SESSION['user'] = [
    'id' => $userId,
    'nama' => $nama,
    'email' => $email,
    'role' => $role,
    'name' => $nama,
];

json_ok(['user_id' => $userId, 'nama' => $nama, 'email' => $email, 'role' => $role]);
