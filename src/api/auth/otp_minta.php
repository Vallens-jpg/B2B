<?php
// Minta kode OTP reset kata sandi (POST {email}).
// Jawab {"ok": true, "data": {"email_tersamar": ..., "kedaluwarsa_detik": 600, ...}}.
// Kode dikirim ke email pengguna (bukan lagi ditampilkan di layar).
require_once __DIR__ . '/../../lib/db.php';
require_once __DIR__ . '/../../lib/response.php';
require_once __DIR__ . '/../../lib/otp.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_error('Metode tidak diizinkan', 405);
}

$input = json_input();
$email = trim($input['email'] ?? '');

if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    json_error('Format email tidak valid', 400);
}

try {
    $pdo = db();

    $stmt = $pdo->prepare('SELECT id_pengguna FROM pengguna WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    if (!$stmt->fetch()) {
        json_error('Email tidak terdaftar', 404);
    }

    $minta = otp_minta_kode($pdo, $email, 'reset_sandi');
    if (!$minta['ok']) {
        json_error($minta['error'], $minta['kode_http']);
    }
    $kode = $minta['kode'];
} catch (Throwable $e) {
    json_error('Database belum siap. Jalankan online_marketplace_v2.sql dulu.', 500);
}

$data = [
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
