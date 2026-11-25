<?php
// POST {email, kode}: verifikasi OTP reset kata sandi.
// Maks 5 percobaan salah, lalu kode terkunci (minta kode baru).
// Berhasil: kembalikan token reset sekali pakai (berlaku 15 menit).
require_once __DIR__ . '/../../lib/db.php';
require_once __DIR__ . '/../../lib/response.php';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    json_error('Metode tidak diizinkan', 405);
}
$input = json_input();
$email = strtolower(trim((string) ($input['email'] ?? '')));
$kode = trim((string) ($input['kode'] ?? ''));
if ($email === '' || $kode === '' || !preg_match('/^\d{6}$/', $kode)) {
    json_error('Kode OTP harus 6 digit angka', 422);
}

try {
    $pdo = db();
    $stmt = $pdo->prepare("SELECT id, kode_hash, expires_at, percobaan FROM otp_kode WHERE email = ? AND tujuan = 'reset_sandi' AND dipakai = 0 ORDER BY id DESC LIMIT 1");
    $stmt->execute([$email]);
    $baris = $stmt->fetch();
    if (!$baris) {
        json_error('Kode OTP tidak ditemukan. Minta kode baru.', 404);
    }
    if ((int) $baris['percobaan'] >= 5) {
        json_error('Kode sudah terkunci karena terlalu banyak percobaan. Minta kode baru.', 429);
    }
    if (strtotime((string) $baris['expires_at']) < time()) {
        json_error('Kode OTP sudah kedaluwarsa. Minta kode baru.', 410);
    }
    if (!password_verify($kode, (string) $baris['kode_hash'])) {
        $stmt = $pdo->prepare('UPDATE otp_kode SET percobaan = percobaan + 1 WHERE id = ?');
        $stmt->execute([(int) $baris['id']]);
        json_error('Kode OTP salah', 401);
    }
    // Cocok: tandai dipakai + buat token reset sekali pakai (15 menit).
    $token = bin2hex(random_bytes(16));
    $stmt = $pdo->prepare('UPDATE otp_kode SET dipakai = 1, token_hash = ?, token_expires_at = DATE_ADD(NOW(), INTERVAL 15 MINUTE) WHERE id = ?');
    $stmt->execute([password_hash($token, PASSWORD_DEFAULT), (int) $baris['id']]);
    json_ok(['token' => $token]);
} catch (Throwable $e) {
    json_error('Database belum siap. Jalankan online_marketplace_v2.sql dulu.', 500);
}
