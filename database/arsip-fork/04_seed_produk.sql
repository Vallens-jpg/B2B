-- Seed produk contoh - jalankan setelah 03_create_tables.sql.
-- Harga per kg dalam Rupiah, hanya contoh untuk demo di laptop sendiri.
-- Idempotent: kalau nama produk sudah ada, harganya diperbarui, tidak dobel.

INSERT INTO produk (nama, deskripsi, harga_per_kg, stok_kg, satuan, gambar) VALUES
  ('Telur Ayam Grade A', 'Telur ayam segar grade A, cangkang bersih, ukuran besar.', 28000, 500, 'kg', 'prod_grade_a.png'),
  ('Telur Ayam Grade B', 'Telur ayam segar grade B, ukuran sedang.', 26000, 500, 'kg', 'prod_grade_b.png'),
  ('Telur Ayam Grade C', 'Telur ayam segar grade C, ukuran kecil.', 24000, 500, 'kg', 'prod_grade_c.png'),
  ('Telur Ayam Kampung', 'Telur ayam kampung, kuning telur lebih pekat.', 45000, 200, 'kg', 'prod_kampung.png'),
  ('Telur Omega', 'Telur ayam omega, kaya omega-3.', 35000, 300, 'kg', 'prod_omega.png')
ON DUPLICATE KEY UPDATE
  deskripsi = VALUES(deskripsi),
  harga_per_kg = VALUES(harga_per_kg),
  stok_kg = VALUES(stok_kg);
