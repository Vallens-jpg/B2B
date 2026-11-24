-- Migrasi 03: buat tabel produk, orders, escrow, invoice, dispute, ulasan, riwayat_status.
-- Jalankan setelah 01_create_tables.sql (tabel users harus sudah ada).
-- Aturan: foreign key + RESTRICT supaya data yang masih dipakai tidak bisa dihapus sembarangan.
-- Status order: menunggu_bayar -> dibayar -> diproses -> dikirim -> diterima -> selesai
--   (atau dibatalkan dari menunggu_bayar / dibayar).

CREATE TABLE IF NOT EXISTS produk (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  perusahaan_id INT UNSIGNED NULL,
  nama VARCHAR(150) NOT NULL,
  deskripsi TEXT NULL,
  harga_per_kg DECIMAL(12,2) NOT NULL,
  stok_kg DECIMAL(10,2) NOT NULL DEFAULT 0,
  satuan VARCHAR(20) NOT NULL DEFAULT 'kg',
  gambar VARCHAR(255) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uk_produk_nama (nama),
  CONSTRAINT fk_produk_perusahaan FOREIGN KEY (perusahaan_id) REFERENCES users (id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS orders (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  buyer_id INT UNSIGNED NOT NULL,
  produk_id INT UNSIGNED NOT NULL,
  jumlah_kg DECIMAL(10,2) NOT NULL,
  harga_satuan DECIMAL(12,2) NOT NULL,
  total DECIMAL(12,2) NOT NULL,
  fee DECIMAL(12,2) NOT NULL DEFAULT 0,
  status ENUM('menunggu_bayar','dibayar','diproses','dikirim','diterima','selesai','dibatalkan') NOT NULL DEFAULT 'menunggu_bayar',
  resi VARCHAR(100) NULL,
  alasan_batal TEXT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_orders_buyer FOREIGN KEY (buyer_id) REFERENCES users (id) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_orders_produk FOREIGN KEY (produk_id) REFERENCES produk (id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS escrow (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_id INT UNSIGNED NOT NULL UNIQUE,
  jumlah_ditahan DECIMAL(12,2) NOT NULL,
  dp_cair DECIMAL(12,2) NOT NULL DEFAULT 0,
  sisa_cair DECIMAL(12,2) NOT NULL DEFAULT 0,
  status ENUM('ditahan','cair_dp','cair_sisa','kembali') NOT NULL DEFAULT 'ditahan',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_escrow_order FOREIGN KEY (order_id) REFERENCES orders (id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS invoice (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_id INT UNSIGNED NOT NULL UNIQUE,
  nomor VARCHAR(50) NOT NULL UNIQUE,
  total DECIMAL(12,2) NOT NULL,
  status ENUM('belum_bayar','dibayar','dibatalkan') NOT NULL DEFAULT 'belum_bayar',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_invoice_order FOREIGN KEY (order_id) REFERENCES orders (id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS dispute (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_id INT UNSIGNED NOT NULL,
  buyer_id INT UNSIGNED NOT NULL,
  judul VARCHAR(200) NOT NULL,
  deskripsi TEXT NOT NULL,
  bukti_foto VARCHAR(255) NULL,
  status ENUM('dibuka','diproses','diputuskan') NOT NULL DEFAULT 'dibuka',
  putusan ENUM('uang_kembali','lanjut_perusahaan') NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_dispute_order FOREIGN KEY (order_id) REFERENCES orders (id) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_dispute_buyer FOREIGN KEY (buyer_id) REFERENCES users (id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS ulasan (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_id INT UNSIGNED NOT NULL UNIQUE,
  produk_id INT UNSIGNED NOT NULL,
  buyer_id INT UNSIGNED NOT NULL,
  rating TINYINT UNSIGNED NOT NULL,
  komentar TEXT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_ulasan_order FOREIGN KEY (order_id) REFERENCES orders (id) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_ulasan_produk FOREIGN KEY (produk_id) REFERENCES produk (id) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_ulasan_buyer FOREIGN KEY (buyer_id) REFERENCES users (id) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT chk_ulasan_rating CHECK (rating BETWEEN 1 AND 5)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS riwayat_status (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_id INT UNSIGNED NOT NULL,
  status_lama VARCHAR(30) NULL,
  status_baru VARCHAR(30) NOT NULL,
  dicatat_oleh INT UNSIGNED NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_riwayat_order FOREIGN KEY (order_id) REFERENCES orders (id) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_riwayat_user FOREIGN KEY (dicatat_oleh) REFERENCES users (id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
