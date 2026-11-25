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
    $stmt = $pdo->prepare('SELECT id_pengguna FROM pengguna WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        json_error('Email sudah terdaftar', 409);
    }

    // Skema v2: buat profil dulu (pembeli/perusahaan), lalu pengguna dengan
    // FK ke profil itu. Tanpa profil, akun baru tidak bisa dipakai di v2.
    $pdo->beginTransaction();
    if ($role === 'perusahaan') {
        $stmt = $pdo->prepare('INSERT INTO perusahaan (nama_perusahaan) VALUES (?)');
        $stmt->execute([$nama]);
        $idProfil = (int) $pdo->lastInsertId();
        $stmt = $pdo->prepare('INSERT INTO pengguna (nama_pengguna, email, password, role, id_perusahaan) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$nama, $email, password_hash($password, PASSWORD_DEFAULT), $role, $idProfil]);
    } else {
        $stmt = $pdo->prepare('INSERT INTO pembeli (nama_pembeli) VALUES (?)');
        $stmt->execute([$nama]);
        $idProfil = (int) $pdo->lastInsertId();
        $stmt = $pdo->prepare('INSERT INTO pengguna (nama_pengguna, email, password, role, id_pembeli) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$nama, $email, password_hash($password, PASSWORD_DEFAULT), $role, $idProfil]);
    }
    $userId = (int) $pdo->lastInsertId();
    $pdo->commit();
} catch (Throwable $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    json_error('Database belum siap. Jalankan online_marketplace_v2.sql dulu.', 500);
}

json_ok(['user_id' => $userId, 'nama' => $nama, 'email' => $email, 'role' => $role]);
