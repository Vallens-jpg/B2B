<?php
// POST {email}: kirim kode OTP 6 digit untuk reset kata sandi.
// Anti-enumerasi: jawaban selalu sama, entah email terdaftar atau tidak.
// Batas: maks 3 kirim/jam per email (429 bila lebih).
// DEMO_MODE=true: kode ikut dikembalikan di respons (hanya untuk demo lokal,
// supaya bisa dites tanpa email — jangan aktifkan di server yang bisa diakses orang lain).
require_once __DIR__ . '/../../lib/db.php';
require_once __DIR__ . '/../../lib/response.php';
require_once __DIR__ . '/../../lib/mail.php';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    json_error('Metode tidak diizinkan', 405);
}
$input = json_input();
$email = strtolower(trim((string) ($input['email'] ?? '')));
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    json_error('Alamat email tidak valid', 422);
}

try {
    $pdo = db();
    // Batas kirim: 3/jam per email (dihitung dari kode yang dibuat 1 jam terakhir).
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM otp_kode WHERE email = ? AND tujuan = 'reset_sandi' AND created_at >= (NOW() - INTERVAL 1 HOUR)");
    $stmt->execute([$email]);
    if ((int) $stmt->fetchColumn() >= 3) {
        json_error('Terlalu banyak permintaan. Coba lagi satu jam lagi.', 429);
    }
    // Batalkan kode lama yang belum dipakai supaya hanya satu kode yang aktif.
    $stmt = $pdo->prepare("UPDATE otp_kode SET dipakai = 1 WHERE email = ? AND tujuan = 'reset_sandi' AND dipakai = 0");
    $stmt->execute([$email]);

    $kode = (string) random_int(100000, 999999);
    $stmt = $pdo->prepare("INSERT INTO otp_kode (email, tujuan, kode_hash, expires_at) VALUES (?, 'reset_sandi', ?, DATE_ADD(NOW(), INTERVAL 10 MINUTE))");
    $stmt->execute([$email, password_hash($kode, PASSWORD_DEFAULT)]);

    // Email hanya dikirim bila akunnya memang ada (anti-enumerasi di luar).
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM pengguna WHERE email = ?');
    $stmt->execute([$email]);
    $ada = (int) $stmt->fetchColumn() > 0;

    $demo = strtolower(trim((string) (getenv('DEMO_MODE') ?: 'false'))) === 'true';
    $terkirim = false;
    $alasanGagal = '';
    if ($ada) {
        $hasil = kirim_mail(
            $email,
            'Kode Reset Kata Sandi Staplr',
            "Kode OTP Anda: $kode\n\nKode berlaku 10 menit. Jangan berikan kode ini kepada siapa pun."
        );
        $terkirim = (bool) ($hasil['ok'] ?? false);
        $alasanGagal = (string) ($hasil['error'] ?? '');
    }
    $balasan = ['email' => $email, 'terkirim' => $terkirim];
    if ($demo) {
        // DEMO LOKAL SAJA: tampilkan kode supaya bisa dites tanpa email.
        $balasan['kode_demo'] = $kode;
    }
    if ($ada && !$terkirim && !$demo) {
        // Email gagal terkirim (mis. SMTP belum dikonfigurasi): katakan jujur.
        json_ok(array_merge($balasan, ['peringatan' => $alasanGagal !== '' ? $alasanGagal : 'Email gagal dikirim.']));
    }
    json_ok($balasan);
} catch (Throwable $e) {
    json_error('Database belum siap. Jalankan online_marketplace_v2.sql dulu.', 500);
}
