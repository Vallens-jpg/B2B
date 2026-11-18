<?php
// Ubah profil user yang sedang login (nama, email). POST saja.
// Dipakai halaman profil.php. Email harus tetap unik antar akun.
require_once __DIR__ . '/../../lib/db.php';
require_once __DIR__ . '/../../lib/response.php';
require_once __DIR__ . '/../../lib/auth.php';

$user = auth_user();
if (!$user) {
    json_error('Silakan login dulu', 401);
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_error('Metode tidak diizinkan', 405);
}

$input = json_input();
$nama = trim((string) ($input['nama'] ?? ''));
$email = trim((string) ($input['email'] ?? ''));
if ($nama === '' || $email === '') {
    json_error('Nama dan email wajib diisi', 422);
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    json_error('Format email tidak valid', 422);
}

try {
    $pdo = db();
    $stmt = $pdo->prepare('UPDATE users SET nama = ?, email = ? WHERE id = ?');
    $stmt->execute([$nama, $email, (int) $user['id']]);
    // Perbarui session supaya nama baru langsung tampil di semua halaman.
    $_SESSION['user']['nama'] = $nama;
    $_SESSION['user']['email'] = $email;
} catch (Throwable $e) {
    if ((string) ($e->getCode() ?? '') === '23000') {
        json_error('Email sudah dipakai akun lain', 409);
    }
    json_error('Database belum siap. Jalankan migrasi 01 dulu.', 500);
}

json_ok([
    'id' => (int) $user['id'],
    'nama' => $nama,
    'email' => $email,
    'role' => $user['role'] ?? '',
]);
