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
git clone LINK_REPO
cd NAMA_FOLDER

## 4. Siapkan konfigurasi
Salin file `.env.example` menjadi `.env` (isinya tidak perlu diubah):
copy .env.example .env

## 5. Jalankan
docker compose up -d --build
(Pertama kali butuh beberapa menit.)

## 6. Cek hasilnya
- Aplikasi: http://localhost:8080 → harus muncul "Koneksi DB berhasil"
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
