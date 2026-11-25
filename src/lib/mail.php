<?php
// Pengirim email minimal via SMTP (tanpa library eksternal).
// Konfigurasi dibaca dari environment / .env (lihat .env.example):
//   SMTP_HOST, SMTP_PORT (default 587), SMTP_USER, SMTP_PASS, SMTP_FROM,
//   SMTP_SECURE (tls|ssl|kosong), SMTP_TIMEOUT (detik, default 10).
// kirim_mail() mengembalikan ['ok' => true] atau
// ['ok' => false, 'error' => 'alasan']. Bila SMTP belum dikonfigurasi,
// hasilnya error yang jujur (bukan pura-pura terkirim) supaya pemanggil
// bisa memberi pesan yang benar ke pengguna.
// Catatan: db() harus sudah dipanggil sekali sebelum kirim_mail() supaya
// nilai .env masuk ke environment (db.php memuat .env lewat putenv).

function smtp_config(): array
{
    return [
        'host' => trim((string) (getenv('SMTP_HOST') ?: '')),
        'port' => (int) (getenv('SMTP_PORT') ?: 587),
        'user' => trim((string) (getenv('SMTP_USER') ?: '')),
        'pass' => (string) (getenv('SMTP_PASS') ?: ''),
        'from' => trim((string) (getenv('SMTP_FROM') ?: '')),
        'secure' => strtolower(trim((string) (getenv('SMTP_SECURE') ?: 'tls'))),
        'timeout' => (int) (getenv('SMTP_TIMEOUT') ?: 10),
    ];
}

// Baca satu balasan SMTP (bisa multi-baris: baris lanjutan ditandai '-'
// di karakter ke-4). $kodeHarapan 0 berarti tidak dicek.
function smtp_baca($fp, int $kodeHarapan): array
{
    $balasan = '';
    do {
        $potong = fgets($fp, 512);
        if ($potong === false) {
            return [false, $balasan];
        }
        $balasan .= $potong;
    } while (strlen($potong) >= 4 && $potong[3] === '-');
    if ($kodeHarapan === 0) {
        return [true, $balasan];
    }
    return [(int) substr($balasan, 0, 3) === $kodeHarapan, $balasan];
}

function smtp_perintah($fp, string $perintah, int $kodeHarapan, string &$balasan): bool
{
    fwrite($fp, $perintah . "\r\n");
    [$ok, $balasan] = smtp_baca($fp, $kodeHarapan);
    return $ok;
}

function kirim_mail(string $ke, string $subjek, string $isiTeks): array
{
    $cfg = smtp_config();
    if ($cfg['host'] === '' || $cfg['user'] === '' || $cfg['pass'] === '' || $cfg['from'] === '') {
        return ['ok' => false, 'error' => 'SMTP belum dikonfigurasi (isi SMTP_HOST, SMTP_USER, SMTP_PASS, dan SMTP_FROM di .env)'];
    }
    if (!filter_var($ke, FILTER_VALIDATE_EMAIL)) {
        return ['ok' => false, 'error' => 'Alamat email tujuan tidak valid'];
    }

    $target = $cfg['secure'] === 'ssl'
        ? 'ssl://' . $cfg['host'] . ':' . $cfg['port']
        : 'tcp://' . $cfg['host'] . ':' . $cfg['port'];
    $fp = @fsockopen($target, $cfg['port'], $errno, $errstr, $cfg['timeout']);
    if ($fp === false) {
        return ['ok' => false, 'error' => 'Tidak bisa terhubung ke server SMTP (' . $errstr . ')'];
    }
    stream_set_timeout($fp, $cfg['timeout']);
    $tutup = function (string $pesan) use ($fp): array {
        @fwrite($fp, "QUIT\r\n");
        @fclose($fp);
        return ['ok' => false, 'error' => $pesan];
    };

    [$ok, $balasan] = smtp_baca($fp, 220);
    if (!$ok) {
        return $tutup('Server SMTP tidak menjawab salam pembuka: ' . trim($balasan));
    }
    $hostLokal = gethostname() ?: 'localhost';
    if (!smtp_perintah($fp, 'EHLO ' . $hostLokal, 250, $balasan)) {
        return $tutup('EHLO ditolak server SMTP: ' . trim($balasan));
    }
    if ($cfg['secure'] === 'tls') {
        if (!smtp_perintah($fp, 'STARTTLS', 220, $balasan)) {
            return $tutup('STARTTLS ditolak server SMTP: ' . trim($balasan));
        }
        if (!stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
            return $tutup('Gagal mengaktifkan TLS ke server SMTP');
        }
        if (!smtp_perintah($fp, 'EHLO ' . $hostLokal, 250, $balasan)) {
            return $tutup('EHLO setelah TLS ditolak: ' . trim($balasan));
        }
    }
    if (!smtp_perintah($fp, 'AUTH LOGIN', 334, $balasan)
        || !smtp_perintah($fp, base64_encode($cfg['user']), 334, $balasan)
        || !smtp_perintah($fp, base64_encode($cfg['pass']), 235, $balasan)) {
        return $tutup('Login SMTP gagal (periksa SMTP_USER / SMTP_PASS): ' . trim($balasan));
    }
    if (!smtp_perintah($fp, 'MAIL FROM:<' . $cfg['from'] . '>', 250, $balasan)) {
        return $tutup('MAIL FROM ditolak: ' . trim($balasan));
    }
    if (!smtp_perintah($fp, 'RCPT TO:<' . $ke . '>', 250, $balasan)) {
        return $tutup('RCPT TO ditolak: ' . trim($balasan));
    }
    if (!smtp_perintah($fp, 'DATA', 354, $balasan)) {
        return $tutup('DATA ditolak: ' . trim($balasan));
    }
    // Dot-stuffing: baris yang diawali titik harus digandakan.
    $isiAman = preg_replace('/^\./m', '..', $isiTeks);
    $pesan = 'From: ' . $cfg['from'] . "\r\n"
        . 'To: ' . $ke . "\r\n"
        . 'Subject: ' . $subjek . "\r\n"
        . "MIME-Version: 1.0\r\n"
        . "Content-Type: text/plain; charset=utf-8\r\n"
        . "\r\n"
        . $isiAman . "\r\n.\r\n";
    fwrite($fp, $pesan);
    [$ok, $balasan] = smtp_baca($fp, 250);
    if (!$ok) {
        return $tutup('Server SMTP menolak isi email: ' . trim($balasan));
    }
    @fwrite($fp, "QUIT\r\n");
    @fclose($fp);
    return ['ok' => true];
}
