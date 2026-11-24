-- =====================================================================
--  DATABASE STAPLR  -  VERSI 2 (skema)
--  Target : MySQL 8.0.16+ / MariaDB 10.2+ (InnoDB)
--  Isi    : 1) CREATE DATABASE
--           2) DROP TABLE IF EXISTS  (agar file bisa dijalankan ulang)
--           3) CREATE TABLE (18 tabel)
--           4) ALTER TABLE ... ADD CONSTRAINT (foreign key)
--
--  PERINGATAN: file ini MENGHAPUS tabel lama dengan nama yang sama
--  beserta isinya. Hanya untuk lingkungan pengembangan/demo.
--  Data demo ada di file terpisah: online_marketplace_v2_seed.sql
--
--  ALUR YANG DIDUKUNG
--   1. Pembeli memesan produk                -> permintaan   (terbuka)
--   2. Perusahaan menyetujui / menolak       -> permintaan   (disetujui / ditolak)
--   3. Saat disetujui, DALAM SATU TRANSAKSI  -> penawaran    (disetujui)
--                                            -> pesanan      (menunggu_bayar)
--   4. Pembeli membayar lewat invoice Xendit -> pembayaran   (pending)
--   5. Webhook Xendit status PAID            -> pembayaran   (paid)
--                                            -> pesanan      (dibayar)
--   6. Perusahaan mengirim barang            -> pengiriman, pencairan tahap 1 (DP)
--   7. Pembeli menerima barang               -> pencairan tahap 2 (pelunasan)
--   8. Dispute aktif menahan tahap 2; keputusan admin menentukan
--      pelunasan ke perusahaan atau refund ke pembeli (pencairan tahap 3)
--
--  CATATAN DESAIN
--   * Semua jumlah barang dalam kg (DECIMAL 12,2); uang DECIMAL(15,2).
--   * Fee platform DIPOTONG dari dana perusahaan, bukan ditambahkan ke
--     tagihan pembeli: pesanan.fee_* menyimpan fee, pencairan menyimpan
--     nominal kotor, potongan fee, dan nominal bersih per tahap.
--   * pembayaran.xendit_id        = nomor invoice milik kita
--                                   (dikirim ke Xendit sebagai external_id,
--                                    dikembalikan lagi oleh webhook).
--     pembayaran.xendit_invoice_id = ID invoice dari Xendit.
--   * pencairan.tahap: 1 = DP, 2 = pelunasan, 3 = refund ke pembeli.
--   * Typo ERD diperbaiki: `stasus` -> `status`, `id_admiin` -> `id_admin`.
-- =====================================================================

CREATE DATABASE IF NOT EXISTS staplr
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE staplr;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS
  notifikasi_dibaca, riwayat_status, dispute_pesan, dispute, pengiriman,
  pencairan, pembayaran, pesanan, penawaran, permintaan, stok, produk,
  kategori_produk, dompet_sistem, pengguna, pembeli, perusahaan, admin;

-- =====================================================================
-- BAGIAN 1 : CREATE TABLE
-- =====================================================================

-- 1. ADMIN
CREATE TABLE admin (
  id_admin   INT AUTO_INCREMENT PRIMARY KEY,
  nama       VARCHAR(100) NOT NULL,
  status     ENUM('aktif','nonaktif') NOT NULL DEFAULT 'aktif'
) ENGINE=InnoDB;

-- 2. PERUSAHAAN
CREATE TABLE perusahaan (
  id_perusahaan       INT AUTO_INCREMENT PRIMARY KEY,
  id_admin            INT NULL,                     -- admin yang memverifikasi
  nama_perusahaan     VARCHAR(150) NOT NULL,
  jenis_perusahaan    VARCHAR(50),
  alamat              TEXT,
  kota                VARCHAR(100),
  provinsi            VARCHAR(100),
  telepon             VARCHAR(20),
  kapasitas_produk    INT,
  status_verifikasi   ENUM('menunggu','terverifikasi','ditolak') NOT NULL DEFAULT 'menunggu',
  catatan_verifikasi  TEXT NULL,
  status_akun         ENUM('aktif','nonaktif','blokir') NOT NULL DEFAULT 'aktif',
  nama_bank           VARCHAR(50),                  -- rekening tujuan pencairan
  no_rekening         VARCHAR(30),
  atas_nama           VARCHAR(100),
  skor_kepercayaan    DECIMAL(5,2) NOT NULL DEFAULT 0
) ENGINE=InnoDB;

-- 3. PEMBELI
CREATE TABLE pembeli (
  id_pembeli          INT AUTO_INCREMENT PRIMARY KEY,
  nama_pembeli        VARCHAR(150) NOT NULL,
  jenis_pembeli       VARCHAR(50),
  alamat              TEXT,
  kota                VARCHAR(100),
  provinsi            VARCHAR(100),
  telepon             VARCHAR(20),
  status_verifikasi   ENUM('menunggu','terverifikasi','ditolak') NOT NULL DEFAULT 'menunggu',
  catatan_verifikasi  TEXT NULL,
  status_akun         ENUM('aktif','nonaktif','blokir') NOT NULL DEFAULT 'aktif'
) ENGINE=InnoDB;

-- 4. PENGGUNA  (akun login; tepat satu dari id_pembeli / id_perusahaan /
--               id_admin terisi sesuai role -> divalidasi di aplikasi)
CREATE TABLE pengguna (
  id_pengguna     INT AUTO_INCREMENT PRIMARY KEY,
  id_perusahaan   INT NULL,
  id_pembeli      INT NULL,
  id_admin        INT NULL,
  nama_pengguna   VARCHAR(100) NOT NULL,
  email           VARCHAR(100) NOT NULL,
  password        VARCHAR(255) NOT NULL,            -- simpan hash (password_hash), bukan teks asli
  role            ENUM('admin','perusahaan','pembeli') NOT NULL,
  status          ENUM('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  terakhir_login  DATETIME NULL,
  dibuat_pada     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT uq_pengguna_email UNIQUE (email)
) ENGINE=InnoDB;

-- 5. KATEGORI_PRODUK
CREATE TABLE kategori_produk (
  id_kategori    INT AUTO_INCREMENT PRIMARY KEY,
  nama_kategori  VARCHAR(100) NOT NULL,
  deskripsi      TEXT,
  status         ENUM('aktif','nonaktif') NOT NULL DEFAULT 'aktif'
) ENGINE=InnoDB;

-- 6. PRODUK
CREATE TABLE produk (
  id_produk      INT AUTO_INCREMENT PRIMARY KEY,
  id_perusahaan  INT NOT NULL,
  id_kategori    INT NOT NULL,
  nama_produk    VARCHAR(150) NOT NULL,
  deskripsi      TEXT NULL,
  gambar         VARCHAR(255) NULL,
  satuan         VARCHAR(20) NOT NULL DEFAULT 'kg',
  harga_standar  DECIMAL(15,2) NOT NULL DEFAULT 0,  -- harga per satuan (kg)
  status         ENUM('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  dibuat_pada    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 7. STOK  (satu baris per produk per tanggal; stok terkini = tanggal terbaru)
CREATE TABLE stok (
  id_stok       INT AUTO_INCREMENT PRIMARY KEY,
  id_produk     INT NOT NULL,
  tanggal_stok  DATE NOT NULL,
  jumlah_stok   DECIMAL(12,2) NOT NULL DEFAULT 0,   -- kg
  stok_minimum  DECIMAL(12,2) NOT NULL DEFAULT 0,   -- kg
  status        VARCHAR(20),
  CONSTRAINT uq_stok_produk_tanggal UNIQUE (id_produk, tanggal_stok),
  CONSTRAINT ck_stok_jumlah CHECK (jumlah_stok >= 0)
) ENGINE=InnoDB;

-- 8. PERMINTAAN  (= pesanan yang diajukan pembeli, menunggu keputusan perusahaan;
--                 perusahaan tujuan ditentukan lewat produk)
CREATE TABLE permintaan (
  id_permintaan       INT AUTO_INCREMENT PRIMARY KEY,
  id_pembeli          INT NOT NULL,
  id_produk           INT NOT NULL,
  jenis_komoditas     VARCHAR(100) NOT NULL,        -- salinan nama produk saat memesan
  tanggal_permintaan  DATE NOT NULL,
  jumlah_permintaan   DECIMAL(12,2) NOT NULL,       -- kg
  alamat_pengiriman   TEXT NULL,
  catatan             TEXT NULL,
  status              ENUM('terbuka','ditanggapi','disetujui','ditolak','ditutup') NOT NULL DEFAULT 'terbuka',
  alasan_penolakan    TEXT NULL,
  CONSTRAINT ck_permintaan_jumlah CHECK (jumlah_permintaan > 0)
) ENGINE=InnoDB;

-- 9. PENAWARAN  (dibuat otomatis saat perusahaan menyetujui permintaan)
CREATE TABLE penawaran (
  id_penawaran       INT AUTO_INCREMENT PRIMARY KEY,
  id_permintaan      INT NOT NULL,
  id_perusahaan      INT NOT NULL,
  id_produk          INT NOT NULL,
  tanggal_penawaran  DATE NOT NULL,
  jumlah             DECIMAL(12,2) NOT NULL,        -- kg
  harga_satuan       DECIMAL(15,2) NOT NULL,
  harga_total        DECIMAL(15,2) NOT NULL,        -- jumlah x harga_satuan
  masa_berlaku       DATE,
  status             ENUM('menunggu','disetujui','ditolak','kedaluwarsa') NOT NULL DEFAULT 'menunggu',
  CONSTRAINT ck_penawaran_jumlah CHECK (jumlah > 0)
) ENGINE=InnoDB;

-- 10. PESANAN
CREATE TABLE pesanan (
  id_pesanan         INT AUTO_INCREMENT PRIMARY KEY,
  id_penawaran       INT NOT NULL,
  id_pembeli         INT NOT NULL,
  id_perusahaan      INT NOT NULL,
  tanggal_pesanan    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  alamat_pengiriman  TEXT,
  jumlah             DECIMAL(12,2) NOT NULL,        -- kg
  harga_satuan       DECIMAL(15,2) NOT NULL,
  total_harga        DECIMAL(15,2) NOT NULL,        -- yang dibayar pembeli (tanpa tambahan fee)
  fee_persen         DECIMAL(5,2)  NOT NULL DEFAULT 0,
  fee_nominal        DECIMAL(15,2) NOT NULL DEFAULT 0,  -- dipotong dari dana perusahaan
  persen_dp          DECIMAL(5,2)  NOT NULL DEFAULT 0,
  status             ENUM('menunggu_bayar','dibayar','diproses','dikirim','diterima','selesai','dibatalkan')
                     NOT NULL DEFAULT 'menunggu_bayar',
  alasan_batal       TEXT NULL,
  tanggal_diterima   DATE NULL,
  ulasan             TEXT NULL,
  rating             TINYINT NULL,
  diperbarui_pada    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT uq_pesanan_penawaran UNIQUE (id_penawaran),   -- relasi 1:1 dengan penawaran
  CONSTRAINT ck_pesanan_rating    CHECK (rating IS NULL OR rating BETWEEN 1 AND 5),
  CONSTRAINT ck_pesanan_dp        CHECK (persen_dp BETWEEN 0 AND 100),
  CONSTRAINT ck_pesanan_fee       CHECK (fee_persen BETWEEN 0 AND 100),
  CONSTRAINT ck_pesanan_jumlah    CHECK (jumlah > 0)
) ENGINE=InnoDB;

-- 11. PEMBAYARAN  (invoice Xendit milik pembeli)
CREATE TABLE pembayaran (
  id_pembayaran       INT AUTO_INCREMENT PRIMARY KEY,
  id_pesanan          INT NOT NULL,
  xendit_id           VARCHAR(100) NOT NULL,        -- nomor invoice kita = external_id Xendit
  xendit_invoice_id   VARCHAR(100) NULL,            -- ID invoice dari Xendit
  invoice_url         VARCHAR(500) NULL,            -- tautan halaman bayar Xendit
  tanggal_pembayaran  DATETIME,
  no_rekening         VARCHAR(30),
  jumlah              DECIMAL(15,2) NOT NULL,
  bukti_pembayaran    VARCHAR(255) NULL,
  status              ENUM('pending','paid','expired','failed') NOT NULL DEFAULT 'pending',
  metode_bayar        VARCHAR(50),
  paid_at             DATETIME NULL,
  CONSTRAINT uq_pembayaran_xendit         UNIQUE (xendit_id),
  CONSTRAINT uq_pembayaran_xendit_invoice UNIQUE (xendit_invoice_id)
) ENGINE=InnoDB;

-- 12. PENCAIRAN  (dana keluar: DP, pelunasan, refund)
CREATE TABLE pencairan (
  id_pencairan        INT AUTO_INCREMENT PRIMARY KEY,
  id_pembayaran       INT NOT NULL,
  id_pesanan          INT NOT NULL,
  id_pengguna         INT NOT NULL,                  -- akun penerima dana
  penerima            ENUM('perusahaan','pembeli') NOT NULL DEFAULT 'perusahaan',
  external_id         VARCHAR(100) NOT NULL,
  tahap               TINYINT NOT NULL,              -- 1 = DP, 2 = pelunasan, 3 = refund
  nominal_kotor       DECIMAL(15,2) NOT NULL,
  potongan_fee        DECIMAL(15,2) NOT NULL DEFAULT 0,
  nominal             DECIMAL(15,2) NOT NULL,        -- bersih yang dikirim = kotor - fee
  nama_bank           VARCHAR(50),
  no_rekening         VARCHAR(30),
  atas_nama           VARCHAR(100),
  xendit_payout_id    VARCHAR(100) NULL,
  status              ENUM('pending','berhasil','gagal') NOT NULL DEFAULT 'pending',
  alasan_gagal        TEXT NULL,
  tanggal_permintaan  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  tanggal_cair        DATETIME NULL,
  CONSTRAINT uq_pencairan_external      UNIQUE (external_id),
  CONSTRAINT uq_pencairan_pesanan_tahap UNIQUE (id_pesanan, tahap),   -- cegah pencairan ganda
  CONSTRAINT ck_pencairan_tahap         CHECK (tahap BETWEEN 1 AND 3),
  CONSTRAINT ck_pencairan_nominal       CHECK (nominal = nominal_kotor - potongan_fee)
) ENGINE=InnoDB;

-- 13. PENGIRIMAN
CREATE TABLE pengiriman (
  id_pengiriman       INT AUTO_INCREMENT PRIMARY KEY,
  id_pesanan          INT NOT NULL,
  tanggal_pengiriman  DATE,
  jasa_pengiriman     VARCHAR(100),
  no_resi             VARCHAR(50),
  alamat_pengiriman   TEXT,
  status              VARCHAR(30)
) ENGINE=InnoDB;

-- 14. DISPUTE
CREATE TABLE dispute (
  id_dispute       INT AUTO_INCREMENT PRIMARY KEY,
  id_pesanan       INT NOT NULL,
  id_admin         INT NULL,                         -- admin penengah
  tanggal_mulai    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  nama_barang      VARCHAR(150),
  kondisi_barang   VARCHAR(100),
  keterangan       TEXT,
  status           ENUM('diajukan','diproses','selesai') NOT NULL DEFAULT 'diajukan',
  keputusan        ENUM('uang_kembali','lanjut_perusahaan') NULL,   -- menentukan pencairan tahap 2 atau 3
  catatan_admin    TEXT NULL,
  tanggal_upload   DATETIME NULL,
  foto_bukti       VARCHAR(255) NULL,
  tanggal_selesai  DATETIME NULL
) ENGINE=InnoDB;

-- 15. DISPUTE_PESAN  (percakapan dalam dispute)
CREATE TABLE dispute_pesan (
  id_pesan     INT AUTO_INCREMENT PRIMARY KEY,
  id_dispute   INT NOT NULL,
  id_pengguna  INT NOT NULL,
  pesan        TEXT NOT NULL,
  dibuat_pada  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 16. RIWAYAT_STATUS  (audit perubahan status pesanan; dasar timeline & notifikasi)
CREATE TABLE riwayat_status (
  id_riwayat   INT AUTO_INCREMENT PRIMARY KEY,
  id_pesanan   INT NOT NULL,
  status_lama  VARCHAR(30) NULL,
  status_baru  VARCHAR(30) NOT NULL,
  id_pengguna  INT NULL,                             -- pencatat; NULL = sistem/webhook
  dibuat_pada  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 17. NOTIFIKASI_DIBACA
CREATE TABLE notifikasi_dibaca (
  id_pengguna  INT NOT NULL,
  id_riwayat   INT NOT NULL,
  dibaca_pada  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_pengguna, id_riwayat)
) ENGINE=InnoDB;

-- 18. DOMPET_SISTEM  (buku mutasi saldo)
CREATE TABLE dompet_sistem (
  id_dompet      INT AUTO_INCREMENT PRIMARY KEY,
  id_pengguna    INT NOT NULL,
  id_pesanan     INT NULL,
  id_pencairan   INT NULL,
  jenis_mutasi   ENUM('debit','kredit') NOT NULL,
  jumlah         DECIMAL(15,2) NOT NULL,
  saldo_sebelum  DECIMAL(15,2) NOT NULL DEFAULT 0,
  saldo_sesudah  DECIMAL(15,2) NOT NULL DEFAULT 0,
  keterangan     VARCHAR(255) NULL,
  tanggal        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  status         VARCHAR(20)
) ENGINE=InnoDB;

-- =====================================================================
-- BAGIAN 2 : CREATE RELATIONSHIP (FOREIGN KEY)
-- =====================================================================

-- ADMIN 1 --- * PERUSAHAAN
ALTER TABLE perusahaan
  ADD CONSTRAINT fk_perusahaan_admin
  FOREIGN KEY (id_admin) REFERENCES admin (id_admin)
  ON UPDATE CASCADE ON DELETE SET NULL;

-- PERUSAHAAN / PEMBELI / ADMIN 1 --- * PENGGUNA
ALTER TABLE pengguna
  ADD CONSTRAINT fk_pengguna_perusahaan
  FOREIGN KEY (id_perusahaan) REFERENCES perusahaan (id_perusahaan)
  ON UPDATE CASCADE ON DELETE SET NULL,
  ADD CONSTRAINT fk_pengguna_pembeli
  FOREIGN KEY (id_pembeli) REFERENCES pembeli (id_pembeli)
  ON UPDATE CASCADE ON DELETE SET NULL,
  ADD CONSTRAINT fk_pengguna_admin
  FOREIGN KEY (id_admin) REFERENCES admin (id_admin)
  ON UPDATE CASCADE ON DELETE SET NULL;

-- PERUSAHAAN / KATEGORI_PRODUK 1 --- * PRODUK
ALTER TABLE produk
  ADD CONSTRAINT fk_produk_perusahaan
  FOREIGN KEY (id_perusahaan) REFERENCES perusahaan (id_perusahaan)
  ON UPDATE CASCADE ON DELETE RESTRICT,
  ADD CONSTRAINT fk_produk_kategori
  FOREIGN KEY (id_kategori) REFERENCES kategori_produk (id_kategori)
  ON UPDATE CASCADE ON DELETE RESTRICT;

-- PRODUK 1 --- * STOK
ALTER TABLE stok
  ADD CONSTRAINT fk_stok_produk
  FOREIGN KEY (id_produk) REFERENCES produk (id_produk)
  ON UPDATE CASCADE ON DELETE CASCADE;

-- PEMBELI / PRODUK 1 --- * PERMINTAAN
ALTER TABLE permintaan
  ADD CONSTRAINT fk_permintaan_pembeli
  FOREIGN KEY (id_pembeli) REFERENCES pembeli (id_pembeli)
  ON UPDATE CASCADE ON DELETE RESTRICT,
  ADD CONSTRAINT fk_permintaan_produk
  FOREIGN KEY (id_produk) REFERENCES produk (id_produk)
  ON UPDATE CASCADE ON DELETE RESTRICT;

-- PERMINTAAN / PERUSAHAAN / PRODUK 1 --- * PENAWARAN
ALTER TABLE penawaran
  ADD CONSTRAINT fk_penawaran_permintaan
  FOREIGN KEY (id_permintaan) REFERENCES permintaan (id_permintaan)
  ON UPDATE CASCADE ON DELETE RESTRICT,
  ADD CONSTRAINT fk_penawaran_perusahaan
  FOREIGN KEY (id_perusahaan) REFERENCES perusahaan (id_perusahaan)
  ON UPDATE CASCADE ON DELETE RESTRICT,
  ADD CONSTRAINT fk_penawaran_produk
  FOREIGN KEY (id_produk) REFERENCES produk (id_produk)
  ON UPDATE CASCADE ON DELETE RESTRICT;

-- PENAWARAN 1 --- 0..1 PESANAN ; PEMBELI / PERUSAHAAN 1 --- * PESANAN
ALTER TABLE pesanan
  ADD CONSTRAINT fk_pesanan_penawaran
  FOREIGN KEY (id_penawaran) REFERENCES penawaran (id_penawaran)
  ON UPDATE CASCADE ON DELETE RESTRICT,
  ADD CONSTRAINT fk_pesanan_pembeli
  FOREIGN KEY (id_pembeli) REFERENCES pembeli (id_pembeli)
  ON UPDATE CASCADE ON DELETE RESTRICT,
  ADD CONSTRAINT fk_pesanan_perusahaan
  FOREIGN KEY (id_perusahaan) REFERENCES perusahaan (id_perusahaan)
  ON UPDATE CASCADE ON DELETE RESTRICT;

-- PESANAN 1 --- * PEMBAYARAN
ALTER TABLE pembayaran
  ADD CONSTRAINT fk_pembayaran_pesanan
  FOREIGN KEY (id_pesanan) REFERENCES pesanan (id_pesanan)
  ON UPDATE CASCADE ON DELETE RESTRICT;

-- PEMBAYARAN / PESANAN / PENGGUNA 1 --- * PENCAIRAN
ALTER TABLE pencairan
  ADD CONSTRAINT fk_pencairan_pembayaran
  FOREIGN KEY (id_pembayaran) REFERENCES pembayaran (id_pembayaran)
  ON UPDATE CASCADE ON DELETE RESTRICT,
  ADD CONSTRAINT fk_pencairan_pesanan
  FOREIGN KEY (id_pesanan) REFERENCES pesanan (id_pesanan)
  ON UPDATE CASCADE ON DELETE RESTRICT,
  ADD CONSTRAINT fk_pencairan_pengguna
  FOREIGN KEY (id_pengguna) REFERENCES pengguna (id_pengguna)
  ON UPDATE CASCADE ON DELETE RESTRICT;

-- PESANAN 1 --- * PENGIRIMAN
ALTER TABLE pengiriman
  ADD CONSTRAINT fk_pengiriman_pesanan
  FOREIGN KEY (id_pesanan) REFERENCES pesanan (id_pesanan)
  ON UPDATE CASCADE ON DELETE RESTRICT;

-- PESANAN / ADMIN 1 --- * DISPUTE
ALTER TABLE dispute
  ADD CONSTRAINT fk_dispute_pesanan
  FOREIGN KEY (id_pesanan) REFERENCES pesanan (id_pesanan)
  ON UPDATE CASCADE ON DELETE RESTRICT,
  ADD CONSTRAINT fk_dispute_admin
  FOREIGN KEY (id_admin) REFERENCES admin (id_admin)
  ON UPDATE CASCADE ON DELETE SET NULL;

-- DISPUTE / PENGGUNA 1 --- * DISPUTE_PESAN
ALTER TABLE dispute_pesan
  ADD CONSTRAINT fk_dispute_pesan_dispute
  FOREIGN KEY (id_dispute) REFERENCES dispute (id_dispute)
  ON UPDATE CASCADE ON DELETE CASCADE,
  ADD CONSTRAINT fk_dispute_pesan_pengguna
  FOREIGN KEY (id_pengguna) REFERENCES pengguna (id_pengguna)
  ON UPDATE CASCADE ON DELETE RESTRICT;

-- PESANAN / PENGGUNA 1 --- * RIWAYAT_STATUS
ALTER TABLE riwayat_status
  ADD CONSTRAINT fk_riwayat_pesanan
  FOREIGN KEY (id_pesanan) REFERENCES pesanan (id_pesanan)
  ON UPDATE CASCADE ON DELETE RESTRICT,
  ADD CONSTRAINT fk_riwayat_pengguna
  FOREIGN KEY (id_pengguna) REFERENCES pengguna (id_pengguna)
  ON UPDATE CASCADE ON DELETE SET NULL;

-- PENGGUNA / RIWAYAT_STATUS 1 --- * NOTIFIKASI_DIBACA
ALTER TABLE notifikasi_dibaca
  ADD CONSTRAINT fk_notif_pengguna
  FOREIGN KEY (id_pengguna) REFERENCES pengguna (id_pengguna)
  ON UPDATE CASCADE ON DELETE CASCADE,
  ADD CONSTRAINT fk_notif_riwayat
  FOREIGN KEY (id_riwayat) REFERENCES riwayat_status (id_riwayat)
  ON UPDATE CASCADE ON DELETE CASCADE;

-- PENGGUNA / PESANAN / PENCAIRAN 1 --- * DOMPET_SISTEM
ALTER TABLE dompet_sistem
  ADD CONSTRAINT fk_dompet_pengguna
  FOREIGN KEY (id_pengguna) REFERENCES pengguna (id_pengguna)
  ON UPDATE CASCADE ON DELETE RESTRICT,
  ADD CONSTRAINT fk_dompet_pesanan
  FOREIGN KEY (id_pesanan) REFERENCES pesanan (id_pesanan)
  ON UPDATE CASCADE ON DELETE RESTRICT,
  ADD CONSTRAINT fk_dompet_pencairan
  FOREIGN KEY (id_pencairan) REFERENCES pencairan (id_pencairan)
  ON UPDATE CASCADE ON DELETE RESTRICT;

SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================================
-- (Opsional) Cek relasi yang sudah terbentuk
-- =====================================================================
-- SELECT TABLE_NAME, COLUMN_NAME, CONSTRAINT_NAME,
--        REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
-- FROM information_schema.KEY_COLUMN_USAGE
-- WHERE TABLE_SCHEMA = 'staplr'
--   AND REFERENCED_TABLE_NAME IS NOT NULL
-- ORDER BY TABLE_NAME;
