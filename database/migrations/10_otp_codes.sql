-- Migrasi 10: kode OTP untuk reset kata sandi (lupa password).
-- Satu baris = satu kode 6 digit. Kode disimpan sebagai hash (bukan angka polos),
-- berlaku 10 menit, maksimal 5 kali percobaan. Setelah kode terverifikasi,
-- pemohon mendapat token reset sekali pakai (berlaku 10 menit) untuk mengganti password.
CREATE TABLE IF NOT EXISTS otp_codes (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(255) NOT NULL,
  tujuan VARCHAR(50) NOT NULL DEFAULT 'reset_password',
  kode_hash VARCHAR(255) NOT NULL,
  token_reset VARCHAR(64) NULL,
  token_kedaluwarsa DATETIME NULL,
  token_dipakai TINYINT(1) NOT NULL DEFAULT 0,
  dipakai TINYINT(1) NOT NULL DEFAULT 0,
  percobaan INT NOT NULL DEFAULT 0,
  kedaluwarsa DATETIME NOT NULL,
  dibuat DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_otp_email_tujuan (email, tujuan),
  INDEX idx_otp_token (token_reset)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
