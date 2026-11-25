-- Tambah tujuan 'login' ke tabel otp_kode (untuk OTP saat login).
-- Jalankan sekali di phpMyAdmin (database staplr) setelah git pull.
-- Untuk database baru, online_marketplace_v2.sql sudah berisi ENUM ini.
ALTER TABLE otp_kode
  MODIFY tujuan ENUM('reset_sandi','registrasi','login') NOT NULL DEFAULT 'reset_sandi';
