-- =====================================================================
--  DATA DEMO STAPLR  -  VERSI 2
--  Jalankan SETELAH online_marketplace_v2.sql
--  Hanya untuk pengembangan/demo. Semua data di bawah fiktif.
--
--  Akun login demo (kata sandi tersimpan sebagai hash bcrypt):
--    Pembeli    : guest@staplr.com       / guest123
--    Perusahaan : perusahaan@staplr.com  / perusahaan123
--    Admin      : admin@staplr.com       / admin123
-- =====================================================================

USE staplr;

-- Kategori
INSERT INTO kategori_produk (id_kategori, nama_kategori, deskripsi) VALUES
  (1, 'Telur', 'Telur konsumsi partai besar');

-- Admin (profil)
INSERT INTO admin (id_admin, nama) VALUES
  (1, 'Admin Staplr');

-- Perusahaan (profil, sudah terverifikasi, rekening tujuan pencairan fiktif)
INSERT INTO perusahaan
  (id_perusahaan, id_admin, nama_perusahaan, jenis_perusahaan, alamat, kota, provinsi,
   telepon, kapasitas_produk, status_verifikasi, nama_bank, no_rekening, atas_nama, skor_kepercayaan)
VALUES
  (1, 1, 'Peternakan Makmur Jaya', 'Peternakan', 'Jl. Peternak No. 10', 'Sukoharjo', 'Jawa Tengah',
   '081200000001', 5000, 'terverifikasi', 'BCA', '1234567890', 'Peternakan Makmur Jaya', 80.00);

-- Pembeli (profil, sudah terverifikasi)
INSERT INTO pembeli
  (id_pembeli, nama_pembeli, jenis_pembeli, alamat, kota, provinsi, telepon, status_verifikasi)
VALUES
  (1, 'Toko Sembako Berkah', 'Toko', 'Jl. Pasar No. 5', 'Surakarta', 'Jawa Tengah',
   '081200000002', 'terverifikasi');

-- Pengguna (akun login; satu profil per akun sesuai role)
INSERT INTO pengguna
  (id_pengguna, id_perusahaan, id_pembeli, id_admin, nama_pengguna, email, password, role)
VALUES
  (1, NULL, 1,    NULL, 'Guest Demo',      'guest@staplr.com',
   '$2y$10$nOGlsWJgRtJS9AchKPU7T.Re7HphT0nPXqLdU/pfzlImKGtIC8fPK', 'pembeli'),
  (2, 1,    NULL, NULL, 'Perusahaan Demo', 'perusahaan@staplr.com',
   '$2y$10$WDzJZAsDGt3BuMOaAhCpoOSj6LWFSgY6khMPGufyMuxnaKqoV6p66', 'perusahaan'),
  (3, NULL, NULL, 1,    'Admin Demo',      'admin@staplr.com',
   '$2y$10$Y99gtvY2QdFl0fdZ9dETEuM5Q.CNR2Gsx1u8uy4K5Cj6wheODuicW', 'admin');

-- Produk (harga per kg)
INSERT INTO produk
  (id_produk, id_perusahaan, id_kategori, nama_produk, deskripsi, satuan, harga_standar)
VALUES
  (1, 1, 1, 'Telur Ayam Ras',    'Telur ayam ras segar, sortir ukuran sedang', 'kg', 28000.00),
  (2, 1, 1, 'Telur Ayam Kampung','Telur ayam kampung, kemasan curah',          'kg', 42000.00),
  (3, 1, 1, 'Telur Bebek',       'Telur bebek segar, kemasan curah',           'kg', 36000.00);

-- Stok awal (kg)
INSERT INTO stok (id_produk, tanggal_stok, jumlah_stok, stok_minimum, status) VALUES
  (1, CURDATE(), 2000.00, 200.00, 'tersedia'),
  (2, CURDATE(),  800.00, 100.00, 'tersedia'),
  (3, CURDATE(), 1200.00, 150.00, 'tersedia');
