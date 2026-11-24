<?php
// Login (email, password). Jawab {"ok": true, "data": {"nama": ..., "role": ...}} atau {"ok": false, "error": "Email/password salah"}.
require_once __DIR__ . '/../../lib/db.php';
require_once __DIR__ . '/../../lib/response.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_error('Metode tidak diizinkan', 405);
}

$input = json_input();
$email = trim($input['email'] ?? '');
$password = $input['password'] ?? '';

if ($email === '' || $password === '') {
    json_error('Email dan password wajib diisi', 400);
}

try {
    $stmt = db()->prepare('SELECT id_pengguna AS id, nama_pengguna AS nama, email, password AS password_hash, role, status FROM pengguna WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();
} catch (Throwable $e) {
    json_error('Database belum siap. Jalankan online_marketplace_v2.sql dulu.', 500);
}

if (!$user || !password_verify($password, $user['password_hash'])) {
    json_error('Email/password salah', 401);
}

// Akun yang dikunci admin (status nonaktif, tabel pengguna skema v2) tidak boleh login,
// walau passwordnya benar.
if (($user['status'] ?? 'aktif') === 'nonaktif') {
    json_error('Akun ini dikunci oleh admin. Hubungi admin untuk mengaktifkan kembali.', 403);
}

$_SESSION['user'] = [
    'id' => (int) $user['id'],
    'nama' => $user['nama'],
    'email' => $user['email'],
    'role' => $user['role'],
    // Kompatibel dengan halaman lama yang memakai 'name'.
    'name' => $user['nama'],
];

json_ok([
    'user_id' => (int) $user['id'],
    'nama' => $user['nama'],
    'email' => $user['email'],
    'role' => $user['role'],
]);
