<?php
// POST {email, token, password_baru}: ganti kata sandi dengan token reset.
// Token sekali pakai, berlaku 15 menit; kata sandi baru minimal 8 karakter.
require_once __DIR__ . '/../../lib/db.php';
require_once __DIR__ . '/../../lib/response.php';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    json_error('Metode tidak diizinkan', 405);
}
$input = json_input();
$email = strtolower(trim((string) ($input['email'] ?? '')));
$token = trim((string) ($input['token'] ?? ''));
$sandiBaru = (string) ($input['password_baru'] ?? '');
if ($email === '' || $token === '' || mb_strlen($sandiBaru) < 8) {
    json_error('Kata sandi baru minimal 8 karakter', 422);
}

try {
    $pdo = db();
    $stmt = $pdo->prepare("SELECT id, token_hash, token_expires_at FROM otp_kode WHERE email = ? AND tujuan = 'reset_sandi' AND dipakai = 1 AND token_hash IS NOT NULL ORDER BY id DESC LIMIT 1");
    $stmt->execute([$email]);
    $baris = $stmt->fetch();
    if (!$baris || !password_verify($token, (string) $baris['token_hash'])) {
        json_error('Token reset tidak valid', 401);
    }
    if (strtotime((string) $baris['token_expires_at']) < time()) {
        json_error('Token reset sudah kedaluwarsa. Minta kode baru.', 410);
    }
    $stmt = $pdo->prepare('SELECT id_pengguna FROM pengguna WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if (!$user) {
        json_error('Akun tidak ditemukan', 404);
    }
    $pdo->beginTransaction();
    $stmt = $pdo->prepare('UPDATE pengguna SET password = ? WHERE id_pengguna = ?');
    $stmt->execute([password_hash($sandiBaru, PASSWORD_DEFAULT), (int) $user['id_pengguna']]);
    // Token hangus setelah dipakai.
    $stmt = $pdo->prepare('UPDATE otp_kode SET token_hash = NULL WHERE id = ?');
    $stmt->execute([(int) $baris['id']]);
    $pdo->commit();
    json_ok(['berhasil' => true]);
} catch (Throwable $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    json_error('Database belum siap. Jalankan online_marketplace_v2.sql dulu.', 500);
}
