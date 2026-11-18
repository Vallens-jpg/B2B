<?php
// Profil user yang sedang login (dari session). GET saja.
// Jawab 401 bila belum login. Dipakai halaman profil.php.
// Pola sama seperti endpoint lain: auth_user() dari lib/auth.php + json_ok/json_error.
require_once __DIR__ . '/../../lib/response.php';
require_once __DIR__ . '/../../lib/auth.php';

$user = auth_user();
if (!$user) {
    json_error('Silakan login dulu', 401);
}

json_ok([
    'id' => (int) ($user['id'] ?? 0),
    'nama' => $user['nama'] ?? '',
    'email' => $user['email'] ?? '',
    'role' => $user['role'] ?? '',
]);
