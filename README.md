# Setup Lingkungan Development

Ikuti langkah ini satu per satu. Jangan lompat.

## 1. Cek laptop dulu
Buka Task Manager (Ctrl+Shift+Esc) → Performance:
- Memory minimal 8 GB
- CPU → Virtualization harus **Enabled**
Kalau Disabled, aktifkan di BIOS (SVM Mode untuk AMD, Intel VT-x untuk Intel).
Kalau RAM hanya 4 GB, kabari PM, nanti pakai Laragon.

## 2. Install
- Docker Desktop (pilih opsi WSL 2), lalu restart laptop
- Git

## 3. Ambil proyek
git clone https://github.com/Vallens-jpg/B2B.git
cd B2B

## 4. Siapkan konfigurasi
Salin file `.env.example` menjadi `.env` (isinya tidak perlu diubah):
copy .env.example .env

## 5. Jalankan
docker compose up -d --build
(Pertama kali butuh beberapa menit.)

## 6. Cek hasilnya
- Aplikasi: http://localhost:8080 → harus muncul halaman utama Staplr
- Tes koneksi DB: http://localhost:8080/api/health.php → harus muncul "Koneksi DB berhasil"
- phpMyAdmin: http://localhost:8081 (user: app_user, password: app_pass)

## Perintah harian
- Nyalakan: docker compose up -d
- Matikan: docker compose down
- Lihat error: docker compose logs web

## Kalau error
Kirim ke grup: (1) screenshot error, (2) hasil `docker compose logs web`.

## Aturan penting
- Host database di kode PHP adalah `db`, bukan `localhost`.
- Jangan commit file `.env`.
- Kerja di branch sendiri, jangan langsung ke `main`.

## Struktur folder
- `src/` — web root (yang dijalankan Docker dan `start-server.bat`)
  - `src/pages/` — halaman yang dilihat pengguna (login, katalog, order, dispute, dashboard per peran)
  - `src/pages/admin/` — halaman admin
  - `src/templates/` — bagian yang dipakai ulang (header, footer, kartu produk)
  - `src/assets/css/` dan `src/assets/img/` — tampilan dan gambar
  - `src/api/` — endpoint BE (auth, produk, order, webhook, invoice, dispute, ulasan, admin)
  - `src/lib/` — kode bersama BE (koneksi DB, auth, validasi, Xendit, escrow)
  - `src/uploads/` — bukti upload (jangan commit isinya)
- `database/migrations/` — file SQL migrasi (ditulis BE dari spesifikasi PM)
- `docs/` — referensi desain (screenshot Figma, file hi-fi)

## Aturan kerja
- Satu branch per fitur dari `main`, contoh: `be-1/auth-login`, `fe-1/login`.
- Satu file, satu pemilik. File bersama hanya diubah lewat PR yang disetujui pemiliknya.
- Branch role (`pembeli`, `perusahaan`, `admin`) sudah digabung dan ditutup. Jangan kerjakan fitur baru di sana.
