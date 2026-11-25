<?php
// Ganti kata sandi dengan token reset dari OTP
// (POST {email, token_reset, password_baru, konfirmasi_password}).
// Token sekali pakai, berlaku 10 menit sejak kode OTP terverifikasi.
// Token disimpan sebagai hash di otp_kode.token_hash, jadi dicari lewat
// email lalu dicek dengan password_verify (pola yang sama dengan
// lupa_reset.php lama). Skema v2: kata sandi di pengguna.password.
require_once __DIR__ . '/../../lib/db.php';
require_once __DIR__ . '/../../lib/response.php';
require_once __DIR__ . '/../../lib/otp.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_error('Metode tidak diizinkan', 405);
}

$input = json_input();
$email = strtolower(trim($input['email'] ?? ''));
$token = trim($input['token_reset'] ?? '');
$baru = $input['password_baru'] ?? '';
$konfirmasi = $input['konfirmasi_password'] ?? '';

if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    json_error('Format email tidak valid', 400);
}
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
        "SELECT id, token_hash, token_expires_at FROM otp_kode WHERE email = ? AND tujuan = 'reset_sandi' AND dipakai = 1 AND token_hash IS NOT NULL ORDER BY id DESC LIMIT 1"
    );
    $stmt->execute([$email]);
    $baris = $stmt->fetch();
    if (!$baris || !password_verify($token, (string) $baris['token_hash'])) {
        json_error('Token tidak valid atau sudah kedaluwarsa. Ulangi dari halaman lupa kata sandi.', 410);
    }
    if (strtotime((string) $baris['token_expires_at']) < time()) {
        json_error('Token tidak valid atau sudah kedaluwarsa. Ulangi dari halaman lupa kata sandi.', 410);
    }

    $stmt = $pdo->prepare('SELECT id_pengguna FROM pengguna WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if (!$user) {
        json_error('Akun tidak ditemukan', 404);
    }

    $pdo->beginTransaction();
    $stmt = $pdo->prepare('UPDATE pengguna SET password = ? WHERE id_pengguna = ?');
    $stmt->execute([password_hash($baru, PASSWORD_DEFAULT), (int) $user['id_pengguna']]);
    // Token hangus setelah dipakai.
    $stmt = $pdo->prepare('UPDATE otp_kode SET token_hash = NULL WHERE id = ?');
    $stmt->execute([(int) $baris['id']]);
    $pdo->commit();
} catch (Throwable $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    json_error('Database belum siap. Jalankan online_marketplace_v2.sql dulu.', 500);
}

json_ok(['email_tersamar' => otp_mask_email($email)]);
