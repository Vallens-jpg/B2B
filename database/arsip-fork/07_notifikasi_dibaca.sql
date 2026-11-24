-- Migrasi 07: status dibaca notifikasi per pengguna.
-- Satu baris riwayat_status bisa dilihat pembeli, perusahaan, dan admin,
-- jadi status dibaca disimpan per (user_id, riwayat_id), bukan di riwayat_status.
CREATE TABLE IF NOT EXISTS notifikasi_dibaca (
  user_id INT UNSIGNED NOT NULL,
  riwayat_id INT UNSIGNED NOT NULL,
  dibaca_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id, riwayat_id),
  CONSTRAINT fk_notif_dibaca_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_notif_dibaca_riwayat FOREIGN KEY (riwayat_id) REFERENCES riwayat_status (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
