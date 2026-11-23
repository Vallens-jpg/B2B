<?php
// Daftar user baru dua langkah (nama, email, password, role pembeli/perusahaan).
// Langkah 1: data valid dan email belum terdaftar -> kode OTP dikirim ke email,
// data pending disimpan di session (password hanya sebagai hash, bukan polos),
// jawab {"ok": true, "data": {"butuh_otp": true, "email_tersamar": ...}}.
// Langkah 2: verifikasi kode lewat otp_verifikasi.php (tujuan='registrasi'),
// akun baru dibuat di sana. Email ganda tetap 409.
require_once __DIR__ . '/../../lib/db.php';
require_once __DIR__ . '/../../lib/response.php';
require_once __DIR__ . '/../../lib/otp.php';

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

    $minta = otp_minta_kode($pdo, $email, 'registrasi');
    if (!$minta['ok']) {
        json_error($minta['error'], $minta['kode_http']);
    }
    $kode = $minta['kode'];
} catch (Throwable $e) {
    json_error('Database belum siap. Jalankan migrasi 01 dulu.', 500);
}

// Akun BELUM dibuat: tunggu verifikasi OTP.
// Simpan data pending di session; password hanya sebagai hash.
$_SESSION['otp_registrasi'] = [
    'nama' => $nama,
    'email' => $email,
    'password_hash' => password_hash($password, PASSWORD_DEFAULT),
    'role' => $role,
];

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
