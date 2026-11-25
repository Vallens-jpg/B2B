-- Tabel pesan diskusi per dispute (dipakai halaman detail dispute).
-- Jalankan setelah 01 dan 03 (tabel users dan dispute harus sudah ada).
CREATE TABLE IF NOT EXISTS dispute_pesan (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  dispute_id INT UNSIGNED NOT NULL,
  user_id INT UNSIGNED NOT NULL,
  pesan TEXT NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_pesan_dispute FOREIGN KEY (dispute_id) REFERENCES dispute (id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_pesan_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
