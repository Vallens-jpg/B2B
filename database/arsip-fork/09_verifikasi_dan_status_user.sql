-- Verifikasi perusahaan + status aktif user (dipakai halaman admin).
-- Jalankan dari folder B2B-fork:
--   Get-Content database/migrations/09_verifikasi_dan_status_user.sql | docker compose exec -T db mysql -uapp_user -papp_pass app_db
ALTER TABLE users
  ADD COLUMN verifikasi_status ENUM('belum','terverifikasi','ditolak') NOT NULL DEFAULT 'belum',
  ADD COLUMN verifikasi_catatan TEXT NULL,
  ADD COLUMN status ENUM('aktif','nonaktif') NOT NULL DEFAULT 'aktif';
