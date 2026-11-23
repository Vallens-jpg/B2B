<?php
// Verifikasi kode OTP (POST {email, kode, tujuan}).
// tujuan default 'reset_password' agar halaman lupa password lama tetap jalan.
// - reset_password: jawab {"token_reset": ...} untuk reset_password.php.
// - login: bila kode benar, buat $_SESSION['user'] dari user pending
//   (disimpan login.php saat password benar) dan jawab data user.
// - registrasi: bila kode benar, buat akun dari data pending
//   (disimpan register.php) dan jawab user_id.
// Kode berlaku 10 menit, maksimal 5 kali percobaan.
session_start();
require_once __DIR__ . '/../../lib/db.php';
require_once __DIR__ . '/../../lib/response.php';
require_once __DIR__ . '/../../lib/otp.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_error('Metode tidak diizinkan', 405);
}

$input = json_input();
$email = trim($input['email'] ?? '');
$kode = trim($input['kode'] ?? '');
$tujuan = trim($input['tujuan'] ?? 'reset_password');

if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    json_error('Format email tidak valid', 400);
}
if (!preg_match('/^\d{6}$/', $kode)) {
    json_error('Kode OTP harus 6 digit angka', 400);
}
if (!in_array($tujuan, OTP_TUJUAN_SAH, true)) {
    json_error('Tujuan OTP tidak dikenal', 400);
}

try {
    $pdo = db();

    $stmt = $pdo->prepare(
        "SELECT id, kode_hash, percobaan FROM otp_codes WHERE email = ? AND tujuan = ? AND dipakai = 0 AND kedaluwarsa > NOW() ORDER BY id DESC LIMIT 1"
    );
    $stmt->execute([$email, $tujuan]);
    $baris = $stmt->fetch();
    if (!$baris) {
        json_error('Kode sudah kedaluwarsa atau tidak ditemukan. Minta kode baru.', 410);
    }
    if ((int) $baris['percobaan'] >= OTP_MAKS_PERCOBAAN) {
        json_error('Terlalu banyak percobaan. Minta kode baru.', 429);
    }
    if (!password_verify($kode, $baris['kode_hash'])) {
        $sisa = OTP_MAKS_PERCOBAAN - (int) $baris['percobaan'] - 1;
        $stmt = $pdo->prepare('UPDATE otp_codes SET percobaan = percobaan + 1 WHERE id = ?');
        $stmt->execute([$baris['id']]);
        json_error('Kode salah. Sisa percobaan: ' . max($sisa, 0), 400);
    }

    // Kode benar: hanguskan supaya tidak bisa dipakai ulang.
    $stmt = $pdo->prepare('UPDATE otp_codes SET dipakai = 1 WHERE id = ?');
    $stmt->execute([$baris['id']]);

    if ($tujuan === 'reset_password') {
        $token = bin2hex(random_bytes(32));
        $stmt = $pdo->prepare(
            "UPDATE otp_codes SET token_reset = ?, token_kedaluwarsa = DATE_ADD(NOW(), INTERVAL " . OTP_TOKEN_BERLAKU_DETIK . " SECOND), token_dipakai = 0 WHERE id = ?"
        );
        $stmt->execute([$token, $baris['id']]);

        json_ok([
            'token_reset' => $token,
            'email_tersamar' => otp_mask_email($email),
        ]);
    }

    if ($tujuan === 'login') {
        $userId = (int) ($_SESSION['otp_login_user_id'] ?? 0);
        if ($userId <= 0) {
            json_error('Sesi login berakhir. Ulangi dari halaman masuk.', 400);
        }
        $stmt = $pdo->prepare('SELECT id, nama, email, role FROM users WHERE id = ? LIMIT 1');
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
        if (!$user || $user['email'] !== $email) {
            json_error('Sesi login berakhir. Ulangi dari halaman masuk.', 400);
        }
        unset($_SESSION['otp_login_user_id']);

        $_SESSION['user'] = [
            'id' => (int) $user['id'],
            'nama' => $user['nama'],
            'email' => $user['email'],
            'role' => $user['role'],
            // Kompatibel dengan halaman lama yang memakai 'name'.
            'name' => $user['nama'],
        ];

        json_ok([
            'user_id' => (int) $user['id'],
            'nama' => $user['nama'],
            'email' => $user['email'],
            'role' => $user['role'],
        ]);
    }

    // tujuan === 'registrasi'
    $pending = $_SESSION['otp_registrasi'] ?? null;
    if (!is_array($pending) || ($pending['email'] ?? '') !== $email) {
        json_error('Sesi pendaftaran berakhir. Ulangi dari halaman daftar.', 400);
    }

    // Email bisa saja sudah terdaftar sementara menunggu verifikasi.
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        unset($_SESSION['otp_registrasi']);
        json_error('Email sudah terdaftar', 409);
    }

    $stmt = $pdo->prepare('INSERT INTO users (nama, email, password_hash, role) VALUES (?, ?, ?, ?)');
    $stmt->execute([$pending['nama'], $email, $pending['password_hash'], $pending['role']]);
    $userId = (int) $pdo->lastInsertId();
    unset($_SESSION['otp_registrasi']);

    $_SESSION['user'] = [
        'id' => $userId,
        'nama' => $pending['nama'],
        'email' => $email,
        'role' => $pending['role'],
        'name' => $pending['nama'],
    ];

    json_ok([
        'user_id' => $userId,
        'nama' => $pending['nama'],
        'email' => $email,
        'role' => $pending['role'],
    ]);
} catch (Throwable $e) {
    json_error('Database belum siap. Jalankan migrasi 10 dulu.', 500);
}
