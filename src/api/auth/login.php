<?php
// Login dua langkah (email, password, lalu OTP).
// Langkah 1: password benar -> kode OTP dikirim ke email, jawab
// {"ok": true, "data": {"butuh_otp": true, "email_tersamar": ...}}.
// Langkah 2: verifikasi kode lewat otp_verifikasi.php (tujuan='login'),
// session baru dibuat di sana. Password salah tetap 401.
require_once __DIR__ . '/../../lib/db.php';
require_once __DIR__ . '/../../lib/response.php';
require_once __DIR__ . '/../../lib/otp.php';

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
    $pdo = db();
    $stmt = $pdo->prepare('SELECT id, nama, email, password_hash, role FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();
} catch (Throwable $e) {
    json_error('Database belum siap. Jalankan migrasi 01 dan 02 dulu.', 500);
}

if (!$user || !password_verify($password, $user['password_hash'])) {
    json_error('Email/password salah', 401);
}

try {
    $minta = otp_minta_kode($pdo, $email, 'login');
    if (!$minta['ok']) {
        json_error($minta['error'], $minta['kode_http']);
    }
    $kode = $minta['kode'];
} catch (Throwable $e) {
    json_error('Database belum siap. Jalankan migrasi 10 dulu.', 500);
}

// Password benar, tapi session BELUM dibuat: tunggu verifikasi OTP.
// Simpan id user pending di session (bukan password).
$_SESSION['otp_login_user_id'] = (int) $user['id'];

$data = [
    'butuh_otp' => true,
    'email_tersamar' => otp_mask_email($email),
    'kedaluwarsa_detik' => OTP_BERLAKU_DETIK,
    'mode_test' => OTP_TEST_MODE,
];

if (OTP_TEST_MODE) {
    // MODE TES: kode dikembalikan supaya bisa diuji tanpa email.
    $data['kode_uji'] = $kode;
} elseif (!otp_kirim_email($email, $kode)) {
    json_error('Gagal mengirim email. Coba lagi.', 500);
}

json_ok($data);
