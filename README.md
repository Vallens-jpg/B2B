# Staplr — Panduan Setup Lingkungan Development

Dokumen ini untuk semua anggota tim (BE, FE, PM + QA). Ikuti langkahnya **urut dari atas ke bawah, jangan lompat**. Kalau mentok di satu langkah, jangan lanjut dulu — selesaikan dulu, atau tanya di grup.

**Waktu yang dibutuhkan:** pertama kali sekitar 30–60 menit (paling lama menunggu download Docker dan build pertama). Setelah itu, menyalakan proyek tiap hari cuma 1 menit.

## Yang akan kamu pasang

| Alat | Gunanya |
|---|---|
| Git | Mengambil kode proyek dari GitHub dan mengirim perubahanmu kembali |
| Docker Desktop | Menjalankan website + database di laptopmu, tanpa install PHP dan MySQL satu per satu |
| WSL 2 | Bagian dalam Docker Desktop di Windows, supaya Docker bisa jalan |

## Yang akan jalan di laptopmu setelah setup selesai

| Layanan | Alamat | Isinya |
|---|---|---|
| Website Staplr | http://localhost:8080 | Halaman utama, login, katalog, dan semua halaman lain |
| Tes koneksi database | http://localhost:8080/api/health.php | Tulisan "Koneksi DB berhasil" kalau database tersambung |
| phpMyAdmin | http://localhost:8081 | Alat untuk melihat isi database lewat browser (user: `app_user`, password: `app_pass`) |
| Database MySQL | Port 3307 (di dalam Docker namanya `db`) | Datanya nanti diisi migrasi Sprint 1 |

Angka 8080, 8081, dan 3307 itu nomor "pintu" di laptopmu. Kalau ada aplikasi lain yang sudah memakai nomor itu, setup akan error — lihat bagian "Kalau error" nomor 3.

---

## 1. Cek laptop dulu

Buka Task Manager (Ctrl+Shift+Esc) → tab Performance:

- **Memory minimal 8 GB.** Kalau RAM hanya 4 GB, kabari PM, nanti pakai Laragon.
- **CPU → Virtualization harus Enabled.** Kalau Disabled, aktifkan di BIOS (SVM Mode untuk AMD, Intel VT-x untuk Intel). Kalau ragu, minta bantuan PM, jangan ubah BIOS sendiri.
- **Sisa ruang hard disk minimal 5 GB** untuk Docker dan image-nya.
- **Windows 10 atau 11, 64-bit.** Windows 32-bit tidak bisa menjalankan Docker Desktop.

## 2. Install Git

1. Buka https://git-scm.com/download/win di browser. Download akan mulai otomatis.
2. Jalankan file installernya. Klik **Next** terus, biarkan semua pilihan tetap default, sampai **Finish**.
3. **Tutup semua jendela terminal yang sedang terbuka**, lalu buka terminal baru (boleh Command Prompt, PowerShell, atau Git Bash). Cek Git sudah terpasang:
   ```
   git --version
   ```
   Harus muncul tulisan seperti `git version 2.4x.x` (angkanya boleh beda, yang penting muncul). Kalau muncul "git is not recognized", berarti install belum selesai atau terminalnya belum dibuka ulang — ulangi langkah 3.

## 3. Install Docker Desktop

1. Buka https://www.docker.com/products/docker-desktop di browser, klik **Download for Windows**.
2. Jalankan installernya. Saat ada pilihan **"Use WSL 2 instead of Hyper-V"**, pastikan tercentang, lalu klik **OK**.
3. Restart laptop kalau diminta.
4. Buka **Docker Desktop** dari Start Menu. Tunggu sampai ikon paus di pojok kanan bawah taskbar berhenti berputar dan tertulis **"Engine running"**. Ini bisa makan waktu 1–2 menit pertama kali.
5. Cek di terminal:
   ```
   docker --version
   docker compose version
   ```
   Harus muncul nomor versi dari keduanya, contoh `Docker version 2x.x.x` dan `Docker Compose version v2.x.x`. Kalau muncul error "Docker Desktop requires WSL 2", jalankan ini di PowerShell **sebagai Administrator**, lalu restart laptop dan ulangi langkah 4:
   ```
   wsl --install
   ```

## 4. Ambil proyek dari GitHub

1. Buka terminal, pindah ke folder tempatmu menyimpan proyek, contoh:
   ```
   cd D:\Coding
   ```
   Kalau foldernya belum ada, buat dulu lewat File Explorer.
2. Clone proyek (perintah ini mengunduh semua file dari GitHub):
   ```
   git clone https://github.com/Vallens-jpg/B2B.git
   cd B2B
   ```
3. Cek kamu sudah di branch `main` dan filenya lengkap:
   ```
   git branch
   git status
   ```
   `git branch` harus menampilkan `* main`. `git status` harus menampilkan `nothing to commit, working tree clean`. Kalau di folder ada file `README.md`, `docker-compose.yml`, dan folder `src/`, berarti clone berhasil.

## 5. Siapkan konfigurasi (.env)

File `.env` berisi **kata sandi dan nama database untuk laptopmu sendiri**. File ini **tidak boleh dikirim ke GitHub** (sudah masuk `.gitignore`), karena tiap anggota tim memakai database lokalnya masing-masing.

1. Masih di folder `B2B`, salin file contoh menjadi file `.env`:
   - Command Prompt:
     ```
     copy .env.example .env
     ```
   - PowerShell:
     ```
     Copy-Item .env.example .env
     ```
   - Git Bash:
     ```
     cp .env.example .env
     ```
2. Isinya tidak perlu diubah. Supaya tahu, ini arti tiap barisnya:

   | Baris di `.env` | Artinya |
   |---|---|
   | `DB_ROOT_PASSWORD=root_pass` | Password akun root MySQL (hanya dipakai Docker, bukan untuk login aplikasi) |
   | `DB_NAME=app_db` | Nama database Staplr |
   | `DB_USER=app_user` | Username yang dipakai aplikasi untuk bicara ke database |
   | `DB_PASSWORD=app_pass` | Password username tersebut (juga dipakai untuk login phpMyAdmin) |

   Password di atas hanya untuk pengembangan di laptop sendiri. **Jangan dipakai untuk server asli nanti.**

3. Cek file `.env` sudah ada:
   ```
   dir .env
   ```
   Harus muncul nama file `.env`. Kalau muncul "File Not Found", ulangi langkah 1 (pastikan kamu masih di folder `B2B`).

## 6. Jalankan untuk pertama kali

1. Masih di folder `B2B`, jalankan:
   ```
   docker compose up -d --build
   ```
   Arti perintahnya:
   - `up` = nyalakan semua layanan (website, database, phpMyAdmin).
   - `-d` = jalankan di background, supaya terminalmu bisa dipakai lagi.
   - `--build` = bangun image website dari `Dockerfile` (PHP 8.2 + Apache). **Dipakai saat pertama kali**, dan setiap kali `Dockerfile` atau `docker-compose.yml` berubah. Sehari-hari cukup `docker compose up -d` saja.
2. Pertama kali perintah ini akan **mengunduh image** PHP, MySQL 8.0, dan phpMyAdmin dari internet. Tunggu sampai selesai, bisa beberapa menit tergantung kecepatan internet. Jangan tutup terminal atau Docker Desktop selama proses ini.
3. Setelah selesai, cek semua layanan sudah jalan:
   ```
   docker compose ps
   ```
   Harus ada 3 baris (web, db, phpmyadmin) dengan status **Up** atau **running**. Contoh nama container-nya: `b2b-web-1`, `b2b-db-1`, `b2b-phpmyadmin-1` (awalan `b2b` bisa berbeda di laptopmu, tidak masalah).
4. **Catatan penting:** MySQL butuh 10–30 detik untuk siap pertama kali. Kalau langkah 7 langsung dicek dan gagal, tunggu 30 detik lalu coba lagi, jangan langsung mengira setup rusak.

## 7. Cek hasilnya (semua harus centang)

Buka satu per satu di browser dan cocokkan dengan tabel ini:

| # | Yang dicek | Alamat | Hasil yang benar | Kalau tidak seperti itu |
|---|---|---|---|---|
| 1 | Halaman utama | http://localhost:8080 | Muncul halaman utama Staplr (logo + menu) | Lihat "Kalau error" nomor 5 |
| 2 | Halaman login | http://localhost:8080/pages/login.php | Muncul form login (email + password) | Lihat "Kalau error" nomor 5 |
| 3 | Koneksi database | http://localhost:8080/api/health.php | Tulisan **"Koneksi DB berhasil"** | Tunggu 30 detik, refresh; kalau masih gagal, lihat "Kalau error" nomor 4 |
| 4 | phpMyAdmin | http://localhost:8081 | Bisa login dengan user `app_user`, password `app_pass`, lalu terlihat database `app_db` | Lihat "Kalau error" nomor 8 |

**Dua hal yang perlu dipahami dari hasil cek ini:**

- **Login di langkah 2 masih pura-pura** (menerima email apa saja). Itu normal. Login asli yang tersambung ke database dibuat di Sprint 1. Yang penting sekarang halamannya muncul.
- **Database `app_db` di langkah 4 masih kosong** (belum ada tabel). Itu juga normal. Tabel dibuat dari file migrasi di Sprint 1.

Kalau keempat cek di atas berhasil, **setup selesai**. Simpan alamat-alamat di atas, kamu akan sering membukanya.

## 8. Perintah harian

Semua perintah di bawah dijalankan dari folder `B2B` (tempat file `docker-compose.yml` berada).

| Keperluan | Perintah |
|---|---|
| Menyalakan proyek | `docker compose up -d` |
| Mematikan proyek | `docker compose down` |
| Melihat layanan yang jalan | `docker compose ps` |
| Melihat error website | `docker compose logs web` |
| Melihat error database | `docker compose logs db` |
| Melihat log terus-menerus (berhenti dengan Ctrl+C) | `docker compose logs -f web` |
| Restart satu layanan saja | `docker compose restart web` |
| Mengambil kode terbaru dari GitHub (lakukan SEBELUM mulai kerja tiap hari) | `git pull origin main` |

**Jangan jalankan `docker compose down -v`.** Perintah itu menghapus **semua data database** di laptopmu. Pakai `docker compose down` biasa saja.

## 9. Kalau tidak pakai Docker (cara cadangan)

Kalau Docker bermasalah dan belum sempat diperbaiki, website bisa dijalankan sementara dengan cara ini:

1. Pastikan **PHP sudah terpasang** di laptop (`php --version` harus muncul nomor versi).
2. Di folder `B2B`, klik dua kali file `start-server.bat`.
3. Buka http://localhost:8000 di browser.

**Batasan cara ini:** hanya websitenya yang jalan, database tidak jalan. Jadi halaman utama dan login tetap muncul, tapi cek `api/health.php` akan menampilkan error koneksi. Cara ini hanya untuk melihat tampilan sementara. **Kabari PM** kalau kamu terpaksa memakai cara ini, supaya masalah Docker-nya dibantu.

## 10. Kalau error (cari errormu di tabel ini)

| # | Gejala | Penyebab | Perbaikan |
|---|---|---|---|
| 1 | `docker: command not found` atau `docker compose` tidak dikenali | Docker belum terpasang, atau terminal dibuka sebelum install selesai | Tutup terminal, buka lagi, ulangi perintah. Kalau masih gagal, install ulang Docker Desktop (langkah 3). Kalau Docker Desktop-mu sangat lama, coba `docker-compose` (dengan tanda strip) sebagai pengganti `docker compose`. |
| 2 | `Cannot connect to the Docker daemon` atau `error during connect` | Docker Desktop belum jalan | Buka Docker Desktop, tunggu sampai tertulis "Engine running" (langkah 3 nomor 4), lalu ulangi perintah. |
| 3 | `Bind for 0.0.0.0:8080 failed: port is already allocated` | Port 8080 sudah dipakai aplikasi lain | Cari siapa yang memakai: `netstat -ano \| findstr :8080`, lalu matikan aplikasi itu. Atau ganti nomor port di `docker-compose.yml` — tapi **tanya PM dulu**, karena alamat di dokumen ini ikut berubah. |
| 4 | `api/health.php` tidak menampilkan "Koneksi DB berhasil" | Database belum siap, atau container `db` tidak jalan | Tunggu 30 detik lalu refresh. Cek `docker compose ps` — baris `db` harus Up. Kalau tidak Up, cek `docker compose logs db` dan kirim hasilnya ke grup. |
| 5 | `localhost:8080` tidak bisa dibuka (`refused to connect`) | Container `web` tidak jalan | Jalankan `docker compose ps`. Kalau `web` tidak ada atau statusnya Exited, cek `docker compose logs web`, lalu jalankan `docker compose up -d` lagi. |
| 6 | `Virtualization is not enabled` saat membuka Docker | Virtualisasi mati di BIOS | Aktifkan di BIOS (SVM Mode untuk AMD, Intel VT-x untuk Intel). Kalau ragu, minta bantuan PM. |
| 7 | File `.env` tidak ditemukan saat `up` | Langkah 5 terlewat atau salah folder | Pastikan kamu di folder `B2B`, ulangi langkah 5. |
| 8 | phpMyAdmin tidak bisa login | Username/password salah | Pakai user `app_user` dan password `app_pass` (dari `.env`), **bukan** password root. |
| 9 | Laptop jadi sangat lambat saat Docker jalan | RAM kurang atau terlalu banyak aplikasi terbuka | Tutup aplikasi berat. RAM minimal 8 GB (lihat langkah 1). |

Kalau errormu tidak ada di tabel ini, kirim ke grup **tiga hal ini**:
1. Screenshot error-nya.
2. Hasil perintah `docker compose logs web` dan `docker compose logs db`.
3. Langkah terakhir yang kamu lakukan sebelum error muncul.

## Aturan penting

- Host database di kode PHP adalah `db`, bukan `localhost`. Di dalam Docker, `db` itu nama container database. Kalau kode PHP memakai `localhost`, artinya container web itu sendiri, bukan database — koneksi akan gagal.
- Jangan commit file `.env`. File itu sudah masuk `.gitignore`.
- Kerja di branch sendiri dari `main`, jangan langsung ke `main`.
- Sebelum mulai kerja tiap hari: `git pull origin main` dulu, supaya kodemu tidak ketinggalan dari teman lain.

## Struktur folder

- `src/` — web root (yang dijalankan Docker dan `start-server.bat`)
  - `src/pages/` — halaman yang dilihat pengguna (login, katalog, order, dispute, dashboard per peran)
  - `src/pages/admin/` — halaman admin
  - `src/templates/` — bagian yang dipakai ulang (header, footer)
  - `src/assets/css/` dan `src/assets/img/` — tampilan dan gambar
  - `src/api/` — endpoint BE (auth, produk, order, webhook, invoice, dispute, ulasan, admin)
  - `src/lib/` — kode bersama BE (koneksi DB, cek login, format jawaban standar; helper Xendit/escrow ditambah nanti bila perlu)
  - `src/uploads/` — bukti upload (jangan commit isinya)
- `database/migrations/` — file SQL migrasi (ditulis BE dari spesifikasi PM)
- `docs/` — referensi desain (screenshot Figma, file hi-fi)

## Aturan kerja

- Satu branch per fitur dari `main`, contoh: `be-1/auth-login`, `fe-1/login`.
- Satu file, satu pemilik. File bersama hanya diubah lewat PR yang disetujui pemiliknya.
- Branch role (`pembeli`, `perusahaan`, `admin`) sudah digabung dan ditutup. Jangan kerjakan fitur baru di sana.
