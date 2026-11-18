-- Seed akun demo - jalankan setelah 01_create_tables.sql.
-- Akun demo guest:
--   Email: guest@staplr.com
--   Password: guest123
--   Role: pembeli
-- Password disimpan sebagai hash bcrypt, bukan teks biasa.
-- File ini hanya untuk development lokal, jangan dipakai di server asli.

INSERT INTO users (nama, email, password_hash, role)
VALUES (
  'Guest Demo',
  'guest@staplr.com',
  '$2b$12$0VLSsO8M4AmEh0E6oupNROmtbEOik1ygszThGkA5LQg/cUP/HUo.2',
  'pembeli'
)
ON DUPLICATE KEY UPDATE
  nama = VALUES(nama),
  password_hash = VALUES(password_hash),
  role = VALUES(role);
