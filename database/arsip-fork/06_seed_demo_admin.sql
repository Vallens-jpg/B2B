-- Seed akun demo admin + jalankan setelah 01_create_tables.sql.
-- Akun demo admin:
--   Email: admin@staplr.com
--   Password: guest123 (sama seperti akun guest pembeli/perusahaan)
--   Role: admin
--   Nama tampilan: Admin Demo
-- Password disimpan sebagai hash bcrypt, bukan teks biasa.
-- Hash di bawah ini SUDAH terverifikasi untuk password guest123
-- (cek: password_verify('guest123', hash) = True,
--  password_verify('guest124', hash) = False) — jangan diganti.
-- File ini hanya untuk development lokal, jangan dipakai di server asli.

INSERT INTO users (nama, email, password_hash, role)
VALUES (
  'Admin Demo',
  'admin@staplr.com',
  '$2b$12$Arb3drLC9lWiZ82IxShd8euAWVUOlEys2IKeDBVfqPkh9BlHB0F0u',
  'admin'
)
ON DUPLICATE KEY UPDATE
  nama = VALUES(nama),
  password_hash = VALUES(password_hash),
  role = VALUES(role);
