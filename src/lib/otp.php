<?php
// Pembantu OTP untuk reset kata sandi, login, dan registrasi.
// Alur lupa password: otp_minta.php -> otp_verifikasi.php -> reset_password.php
// Alur login: login.php (password benar -> kode OTP) -> otp_verifikasi.php (tujuan='login')
// Alur registrasi: register.php (data valid -> kode OTP) -> otp_verifikasi.php (tujuan='registrasi')
// Lihat desain Figma "Lupa Sandi" dan "V OTP" untuk tampilan halamannya.

require_once __DIR__ . '/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/SMTP.php';
require_once __DIR__ . '/PHPMailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// MODE TES: true berarti kode OTP dikembalikan di jawaban API (data.kode_uji)
// dan ditampilkan di layar supaya alur bisa diuji tanpa email.
// Sekarang false: kode hanya dikirim lewat email sungguhan (SMTP).
define('OTP_TEST_MODE', false);
define('OTP_BERLAKU_DETIK', 600);            // Kode berlaku 10 menit.
define('OTP_MAKS_PERCOBAAN', 5);             // Salah 5 kali = kode hangus.
define('OTP_JEDA_KIRIM_ULANG_DETIK', 60);    // Minta kode lagi setelah 60 detik.
define('OTP_TOKEN_BERLAKU_DETIK', 600);      // Token reset berlaku 10 menit.

// Tujuan OTP yang sah. Dipakai kolom tujuan di tabel otp_codes.
define('OTP_TUJUAN_SAH', ['reset_password', 'login', 'registrasi']);

function otp_buat_kode(): string
{
    return (string) random_int(100000, 999999);
}

function otp_mask_email(string $email): string
{
    $parts = explode('@', $email, 2);
    if (count($parts) !== 2) {
        return $email;
    }
    [$nama, $domain] = $parts;
    $awal = substr($nama, 0, 2);
    return $awal . '***@' . $domain;
}

// Minta kode OTP baru untuk sepasang email+tujuan.
// Aturan bersama: jeda kirim ulang 60 detik, kode lama yang belum dipakai
// untuk pasangan yang sama dihanguskan, kode disimpan sebagai hash.
// Mengembalikan ['ok' => true, 'kode' => '123456'] atau
// ['ok' => false, 'error' => '...', 'kode_http' => 429].
function otp_minta_kode(PDO $pdo, string $email, string $tujuan): array
{
    if (!in_array($tujuan, OTP_TUJUAN_SAH, true)) {
        return ['ok' => false, 'error' => 'Tujuan OTP tidak dikenal', 'kode_http' => 400];
    }

    // Jeda kirim ulang: kode terakhir yang masih aktif belum boleh diganti.
    $stmt = $pdo->prepare("SELECT dibuat FROM otp_codes WHERE email = ? AND tujuan = ? AND dipakai = 0 ORDER BY id DESC LIMIT 1");
    $stmt->execute([$email, $tujuan]);
    $terakhir = $stmt->fetch();
    if ($terakhir) {
        $jeda = OTP_JEDA_KIRIM_ULANG_DETIK - (time() - strtotime($terakhir['dibuat']));
        if ($jeda > 0) {
            return ['ok' => false, 'error' => 'Tunggu ' . $jeda . ' detik sebelum meminta kode lagi', 'kode_http' => 429];
        }
    }

    // Hanguskan kode lama yang belum dipakai untuk email+tujuan ini.
    $stmt = $pdo->prepare('UPDATE otp_codes SET dipakai = 1 WHERE email = ? AND tujuan = ? AND dipakai = 0');
    $stmt->execute([$email, $tujuan]);

    $kode = otp_buat_kode();
    $stmt = $pdo->prepare(
        "INSERT INTO otp_codes (email, tujuan, kode_hash, kedaluwarsa) VALUES (?, ?, ?, DATE_ADD(NOW(), INTERVAL " . OTP_BERLAKU_DETIK . " SECOND))"
    );
    $stmt->execute([$email, $tujuan, password_hash($kode, PASSWORD_DEFAULT)]);

    return ['ok' => true, 'kode' => $kode];
}

// Kirim kode OTP ke email pengguna lewat SMTP (PHPMailer).
// Pengaturan dibaca dari environment (.env di root proyek):
// SMTP_HOST (default smtp.gmail.com), SMTP_PORT (default 587),
// SMTP_SECURE (default 'tls'; 'ssl' untuk port 465; 'none' HANYA untuk
// catcher SMTP lokal saat pengujian — tanpa enkripsi),
// SMTP_USER, SMTP_PASS, SMTP_FROM (default = SMTP_USER),
// SMTP_FROM_NAME (default 'Staplr').
// Mengembalikan true bila email diterima server SMTP.
function otp_kirim_email(string $email, string $kode): bool
{
    $host = getenv('SMTP_HOST') ?: 'smtp.gmail.com';
    $port = (int) (getenv('SMTP_PORT') ?: 587);
    $secure = strtolower(trim((string) (getenv('SMTP_SECURE') ?: 'tls')));
    $user = getenv('SMTP_USER') ?: '';
    $pass = getenv('SMTP_PASS') ?: '';
    $from = getenv('SMTP_FROM') ?: $user;
    $fromName = getenv('SMTP_FROM_NAME') ?: 'Staplr';

    if ($user === '' || $pass === '' || $from === '') {
        error_log('OTP: SMTP_USER/SMTP_PASS/SMTP_FROM belum diatur di .env.');
        return false;
    }

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = $host;
        $mail->Port = $port;
        $mail->SMTPAuth = true;
        $mail->Username = $user;
        $mail->Password = $pass;
        if ($secure === 'tls') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        } elseif ($secure === 'ssl') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        }
        // 'none': tanpa enkripsi, hanya untuk catcher SMTP lokal di VM.
        $mail->CharSet = 'UTF-8';

        $mail->setFrom($from, $fromName);
        $mail->addAddress($email);
        $mail->Subject = 'Kode OTP Staplr';
        $mail->Body = "Kode OTP Anda: $kode\n\n"
            . "Kode berlaku 10 menit dan hanya bisa dipakai sekali. "
            . "Jangan berikan kode ini kepada siapa pun.\n\n"
            . "— Staplr";
        $mail->AltBody = $mail->Body;

        return $mail->send();
    } catch (Exception $e) {
        error_log('OTP: gagal kirim email ke ' . $email . ': ' . $e->getMessage());
        return false;
    }
}
