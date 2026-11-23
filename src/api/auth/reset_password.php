<?php
// Ganti kata sandi dengan token reset dari OTP (POST {token_reset, password_baru, konfirmasi_password}).
// Token sekali pakai, berlaku 10 menit sejak kode OTP terverifikasi.
require_once __DIR__ . '/../../lib/db.php';
require_once __DIR__ . '/../../lib/response.php';
require_once __DIR__ . '/../../lib/otp.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_error('Metode tidak diizinkan', 405);
}

$input = json_input();
$token = trim($input['token_reset'] ?? '');
$baru = $input['password_baru'] ?? '';
$konfirmasi = $input['konfirmasi_password'] ?? '';

if ($token === '') {
    json_error('Token reset tidak ditemukan. Ulangi dari halaman lupa kata sandi.', 400);
}
if ($baru === '' || $konfirmasi === '') {
    json_error('Kata sandi baru wajib diisi', 400);
}
if ($baru !== $konfirmasi) {
    json_error('Konfirmasi kata sandi tidak cocok', 400);
}
if (strlen($baru) < 6) {
    json_error('Password minimal 6 karakter', 400);
}

try {
    $pdo = db();

    $stmt = $pdo->prepare(
        'SELECT id, email FROM otp_codes WHERE token_reset = ? AND token_dipakai = 0 AND token_kedaluwarsa > NOW() LIMIT 1'
    );
    $stmt->execute([$token]);
    $baris = $stmt->fetch();
    if (!$baris) {
        json_error('Token tidak valid atau sudah kedaluwarsa. Ulangi dari halaman lupa kata sandi.', 410);
    }

    $stmt = $pdo->prepare('UPDATE users SET password_hash = ? WHERE email = ?');
    $stmt->execute([password_hash($baru, PASSWORD_DEFAULT), $baris['email']]);

    $stmt = $pdo->prepare('UPDATE otp_codes SET token_dipakai = 1 WHERE id = ?');
    $stmt->execute([$baris['id']]);
} catch (Throwable $e) {
    json_error('Database belum siap. Jalankan migrasi 10 dulu.', 500);
}

json_ok(['email_tersamar' => otp_mask_email($baris['email'])]);
