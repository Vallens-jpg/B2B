-- Migrasi 01: buat tabel users (Sprint 1).
-- Tabel lain (produk, orders, escrow, invoice, dispute, ulasan, riwayat_status)
-- masih TODO BE-1, spek revisi dari Len.
-- Aturan: foreign key + RESTRICT supaya data tidak bisa dihapus sembarangan.

CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(100) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('pembeli', 'perusahaan', 'admin') NOT NULL DEFAULT 'pembeli',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
