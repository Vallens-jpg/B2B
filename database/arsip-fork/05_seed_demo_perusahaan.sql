-- Seed akun demo perusahaan + hubungkan produk contoh ke akun itu.
-- Jalankan setelah 02_seed_demo_users.sql dan 04_seed_produk.sql.
-- Akun demo perusahaan:
--   Email: perusahaan@staplr.com
--   Password: guest123 (sama seperti akun guest pembeli)
--   Role: perusahaan
--   Nama tampilan: Perusahaan Demo
-- Password disimpan sebagai hash bcrypt, bukan teks biasa.
-- File ini hanya untuk development lokal, jangan dipakai di server asli.
-- Setelah migrasi ini, 5 produk contoh dimiliki akun perusahaan demo,
-- jadi order yang dibuat guest pembeli bisa dilihat dan diproses
-- dari dashboard perusahaan dengan akun yang sama-sama demo.

INSERT INTO users (nama, email, password_hash, role)
VALUES (
  'Perusahaan Demo',
  'perusahaan@staplr.com',
  '$2b$12$A/Uz.5D14bJPaQupAx5zKuDI4A2.TE0V.3X5gu71hrrN3FeyGTJz.',
  'perusahaan'
)
ON DUPLICATE KEY UPDATE
  nama = VALUES(nama),
  password_hash = VALUES(password_hash),
  role = VALUES(role);

-- Hubungkan produk yang belum punya pemilik ke akun perusahaan demo.
-- Idempotent: produk yang sudah punya perusahaan_id tidak diubah.
UPDATE produk
SET perusahaan_id = (SELECT id FROM users WHERE email = 'perusahaan@staplr.com' LIMIT 1)
WHERE perusahaan_id IS NULL;
