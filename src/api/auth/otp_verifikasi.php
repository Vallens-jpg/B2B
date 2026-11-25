<?php
// Verifikasi kode OTP (POST {email, kode, tujuan}).
// tujuan default 'reset_sandi' agar halaman lupa password lama tetap jalan.
// - reset_sandi: <redacted>
// - login: bila kode benar, buat $_SESSION['user'] dari user pending
//   (disimpan login.php saat password benar) dan jawab data user.
// - registrasi: bila kode benar, buat akun dari data pending
//   (disimpan register.php) dan jawab user_id.
// Kode berlaku 10 menit, maksimal 5 kali percobaan.
// Skema v2: tabel otp_kode (kolom expires_at, token_hash, token_expires_at)
// dan akun di pengguna (+ profil pembeli/perusahaan).
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
$tujuan = trim($input['tujuan'] ?? 'reset_sandi');

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
        "SELECT id, kode_hash, percobaan FROM otp_kode WHERE email = ? AND tujuan = ? AND dipakai = 0 AND expires_at > NOW() ORDER BY id DESC LIMIT 1"
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
        $stmt = $pdo->prepare('UPDATE otp_kode SET percobaan = percobaan + 1 WHERE id = ?');
        $stmt->execute([$baris['id']]);
        json_error('Kode salah. Sisa percobaan: ' . max($sisa, 0), 400);
    }

    // Kode benar: hanguskan supaya tidak bisa dipakai ulang.
    $stmt = $pdo->prepare('UPDATE otp_kode SET dipakai = 1 WHERE id = ?');
    $stmt->execute([$baris['id']]);

    if ($tujuan === 'reset_sandi') {
        $token = bin2hex(random_bytes(32));
        // Token disimpan sebagai hash (kolom token_hash CHAR(60)), jadi
        // reset_password.php mencarinya lewat email + password_verify.
        $stmt = $pdo->prepare(
            "UPDATE otp_kode SET token_hash = ?, token_expires_at = DATE_ADD(NOW(), INTERVAL " . OTP_TOKEN_BERLAKU_DETIK . " SECOND) WHERE id = ?"
        );
        $stmt->execute([password_hash($token, PASSWORD_DEFAULT), $baris['id']]);

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
        $stmt = $pdo->prepare('SELECT id_pengguna AS id, nama_pengguna AS nama, email, role, status FROM pengguna WHERE id_pengguna = ? LIMIT 1');
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
        if (!$user || $user['email'] !== $email) {
            json_error('Sesi login berakhir. Ulangi dari halaman masuk.', 400);
        }
        if (($user['status'] ?? 'aktif') === 'nonaktif') {
            json_error('Akun ini dikunci oleh admin. Hubungi admin untuk mengaktifkan kembali.', 403);
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
    $stmt = $pdo->prepare('SELECT id_pengguna FROM pengguna WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        unset($_SESSION['otp_registrasi']);
        json_error('Email sudah terdaftar', 409);
    }

    // Skema v2: akun login (pengguna) harus punya profil (pembeli/perusahaan)
    // dulu, karena pengguna.id_pembeli/id_perusahaan adalah FK ke profil.
    // password_hash di pending sudah di-hash sekali di register.php.
    $role = $pending['role'] ?? 'pembeli';
    if (!in_array($role, ['pembeli', 'perusahaan'], true)) {
        $role = 'pembeli';
    }
    $pdo->beginTransaction();
    if ($role === 'perusahaan') {
        $stmt = $pdo->prepare('INSERT INTO perusahaan (nama_perusahaan) VALUES (?)');
        $stmt->execute([$pending['nama']]);
        $idProfil = (int) $pdo->lastInsertId();
        $stmt = $pdo->prepare('INSERT INTO pengguna (nama_pengguna, email, password, role, id_perusahaan) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$pending['nama'], $email, $pending['password_hash'], $role, $idProfil]);
    } else {
        $stmt = $pdo->prepare('INSERT INTO pembeli (nama_pembeli) VALUES (?)');
        $stmt->execute([$pending['nama']]);
        $idProfil = (int) $pdo->lastInsertId();
        $stmt = $pdo->prepare('INSERT INTO pengguna (nama_pengguna, email, password, role, id_pembeli) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$pending['nama'], $email, $pending['password_hash'], $role, $idProfil]);
    }
    $userId = (int) $pdo->lastInsertId();
    $pdo->commit();
    unset($_SESSION['otp_registrasi']);

    $_SESSION['user'] = [
        'id' => $userId,
        'nama' => $pending['nama'],
        'email' => $email,
        'role' => $role,
        'name' => $pending['nama'],
    ];

    json_ok([
        'user_id' => $userId,
        'nama' => $pending['nama'],
        'email' => $email,
        'role' => $role,
    ]);
} catch (Throwable $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    json_error('Database belum siap. Jalankan online_marketplace_v2.sql dulu.', 500);
}
