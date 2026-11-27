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
| phpMyAdmin | http://localhost:8081 | Alat untuk melihat isi database lewat browser. Login `root` / `rootpass` untuk import database, login `app_user` / `app_pass` untuk melihat data |
| Database MySQL | Port 3307 (di dalam Docker namanya `db`) | Database `staplr` (huruf kecil semua), 19 tabel + data demo |

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
3. Cek kamu sudah di branch `main`:
   ```
   git branch --show-current
   ```
   Harus menampilkan `main`. Kalau di folder ada file `README.md`, `docker-compose.yml`, dan folder `src/`, berarti clone berhasil.

## 5. Siapkan konfigurasi (.env)

File `.env` berisi **kata sandi dan nama database untuk laptopmu sendiri**. File ini **tidak boleh dikirim ke GitHub** (sudah masuk `.gitignore`), karena tiap anggota tim memakai database lokalnya masing-masing. **Jangan copy file `.env` orang lain — tiap laptop pakai `.env` sendiri.**

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
2. **Jangan buat file `.env` manual lewat Notepad.** Windows sering menyimpannya sebagai `.env.txt` (akhiran `.txt` disembunyikan), dan Docker tidak akan mengenalinya — hasilnya error `.env not found` walau filenya terlihat ada. Selalu pakai perintah salin di atas.
3. **Ubah tiga baris ini di `.env`** (buka dengan Notepad atau editor teks apa saja, lalu simpan):

   | Baris di `.env` | Isi yang benar | Kenapa |
   |---|---|---|
   | `DB_NAME=app_db` | `DB_NAME=staplr` | **Paling penting.** File contoh masih tertulis `app_db`, harus diganti `staplr` (huruf kecil semua). Kalau tidak diganti, database `staplr` tidak terbentuk dan import di langkah 7 gagal dengan error `#1044 Access denied`. |
   | `DEMO_MODE=false` | `DEMO_MODE=true` | Supaya tombol Bayar langsung menandai pesanan dibayar tanpa Xendit. Tim tidak perlu akun Xendit dan tidak perlu ngrok. |
   | `OTP_TEST_MODE=false` | `OTP_TEST_MODE=true` | Supaya kode OTP tampil di layar saat login. Tim tidak perlu email asli. |

   Baris lain tidak perlu diubah. Yang Xendit (`XENDIT_SECRET_KEY`, `XENDIT_WEBHOOK_TOKEN`) dikosongkan saja — cuma perlu kalau mau tes bayar Xendit beneran, tanya PM dulu.
4. Cek file `.env` sudah ada:
   ```
   dir .env
   ```
   Harus muncul nama file `.env`. Kalau muncul "File Not Found", ulangi langkah 1 (pastikan kamu masih di folder `B2B`).

**Catatan penting:** `.env` cuma dibaca waktu container pertama dibuat. Kalau `.env` diubah sesudah container jalan, harus buat ulang containernya (`docker compose down` lalu `docker compose up -d`, tanpa `-v`) supaya setting baru kebaca. Lihat juga "Kalau error" nomor 10.

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
4. **Catatan penting:** MySQL butuh 10–30 detik untuk siap pertama kali. Kalau langkah 8 langsung dicek dan gagal, tunggu 30 detik lalu coba lagi, jangan langsung mengira setup rusak.

## 7. Import database

1. Buka phpMyAdmin di http://localhost:8081, login sebagai **root** dengan password **rootpass**. (Bukan `app_user` — `app_user` tidak punya hak membuat database baru, jadi import akan ditolak.)
2. Klik tab **Import**, pilih file `database/online_marketplace_v2.sql` (di folder `B2B`), lalu klik **Kirim**. Tunggu sampai muncul tulisan berhasil. File ini membuat database `staplr` beserta 19 tabelnya.
3. Klik tab **Import** lagi, pilih file `database/online_marketplace_v2_seed.sql`, lalu klik **Kirim**. File ini mengisi data demo: 3 akun demo, 3 produk telur, dan stoknya.
4. Cek di panel kiri phpMyAdmin: database `staplr` sudah muncul dengan 19 tabel, termasuk `pengguna` dan `otp_kode`.

**Catatan:** kedua file di atas sudah berisi perintah `USE staplr`, jadi tidak perlu memilih database dulu sebelum import. Jangan import file di folder `database/arsip-fork/` — itu arsip skema lama, tidak dipakai. File `database/otp_kode_login.sql` juga tidak perlu — itu hanya untuk database lama yang sudah ada sebelum OTP login, sedangkan database baru sudah berisi perubahannya.

## 8. Cek hasilnya (semua harus centang)

Buka satu per satu di browser dan cocokkan dengan tabel ini:

| # | Yang dicek | Alamat | Hasil yang benar | Kalau tidak seperti itu |
|---|---|---|---|---|
| 1 | Halaman utama | http://localhost:8080 | Muncul halaman utama Staplr (logo + menu) | Lihat "Kalau error" nomor 5 |
| 2 | Halaman login | http://localhost:8080/pages/login.php | Muncul form login (email + password). Habis isi password, muncul langkah OTP (6 kotak) dengan kodenya tampil di layar | Lihat "Kalau error" nomor 11 |
| 3 | Koneksi database | http://localhost:8080/api/health.php | Tulisan **"Koneksi DB berhasil"** | Tunggu 30 detik, refresh; kalau masih gagal, lihat "Kalau error" nomor 4 |
| 4 | phpMyAdmin | http://localhost:8081 | Bisa login dengan user `root`, password `rootpass`, lalu terlihat database `staplr` dengan 19 tabel | Lihat "Kalau error" nomor 8 dan 10 |
| 5 | Login demo | Login sebagai `guest@staplr.com` / `guest123` | Masuk ke dashboard pembeli | Lihat "Kalau error" nomor 11 |

**Akun demo (dari data seed):**

| Peran | Email | Password | Masuk ke |
|---|---|---|---|
| Pembeli | `guest@staplr.com` | `guest123` | Dashboard pembeli |
| Perusahaan | `perusahaan@staplr.com` | `perusahaan123` | Dashboard perusahaan |
| Admin | `admin@staplr.com` | `admin123` | Dashboard admin |

**Tiga hal yang perlu dipahami dari hasil cek ini:**

- **Login sudah asli** (dicek ke database), bukan pura-pura lagi. Habis login dan OTP, aplikasi otomatis pindah ke dashboard sesuai peranmu. Halaman tiap peran terkunci: kalau membuka halaman peran lain tanpa login yang sesuai, kamu dilempar ke halaman login.
- **Pembayaran di laptop tim itu simulasi** karena `DEMO_MODE=true`: klik Bayar Sekarang, pesanan langsung jadi Dibayar tanpa halaman Xendit. Alur sesudahnya (setujui, kirim foto, terima, escrow, dispute, pencairan) jalan persis sama seperti aslinya.
- **Database `staplr` sudah berisi data demo** (3 akun, 3 produk, stok). Pesanan yang kamu buat di laptopmu hanya ada di laptopmu — tiap anggota punya database sendiri-sendiri.

Kalau kelima cek di atas berhasil, **setup selesai**. Simpan alamat-alamat di atas, kamu akan sering membukanya.

## 9. Perintah harian

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

**Jangan jalankan `docker compose down -v`.** Perintah itu menghapus **semua data database** di laptopmu. Pakai `docker compose down` biasa saja. Satu-satunya pengecualian: untuk memperbaiki database yang rusak seperti di "Kalau error" nomor 10 — dan hanya di laptop yang datanya memang boleh hilang.

## 10. Kalau tidak pakai Docker (cara cadangan)

Kalau Docker bermasalah dan belum sempat diperbaiki, website bisa dijalankan sementara dengan cara ini:

1. Pastikan **PHP sudah terpasang** di laptop (`php --version` harus muncul nomor versi).
2. Di folder `B2B`, klik dua kali file `start-server.bat`.
3. Buka http://localhost:8000 di browser.

**Batasan cara ini:** hanya websitenya yang jalan, database tidak jalan. Jadi halaman utama dan login tetap muncul, tapi cek `api/health.php` akan menampilkan error koneksi. Cara ini hanya untuk melihat tampilan sementara. **Kabari PM** kalau kamu terpaksa memakai cara ini, supaya masalah Docker-nya dibantu.

## 11. Kalau error (cari errormu di tabel ini)

| # | Gejala | Penyebab | Perbaikan |
|---|---|---|---|
| 1 | `docker: command not found` atau `docker compose` tidak dikenali | Docker belum terpasang, atau terminal dibuka sebelum install selesai | Tutup terminal, buka lagi, ulangi perintah. Kalau masih gagal, install ulang Docker Desktop (langkah 3). Kalau Docker Desktop-mu sangat lama, coba `docker-compose` (dengan tanda strip) sebagai pengganti `docker compose`. |
| 2 | `Cannot connect to the Docker daemon` atau `error during connect` | Docker Desktop belum jalan | Buka Docker Desktop, tunggu sampai tertulis "Engine running" (langkah 3 nomor 4), lalu ulangi perintah. |
| 3 | `Bind for 0.0.0.0:8080 failed: port is already allocated` | Port 8080 sudah dipakai aplikasi lain | Cari siapa yang memakai: `netstat -ano \| findstr :8080`, lalu matikan aplikasi itu. Atau ganti nomor port di `docker-compose.yml` — tapi **tanya PM dulu**, karena alamat di dokumen ini ikut berubah. |
| 4 | `api/health.php` tidak menampilkan "Koneksi DB berhasil" | Database belum siap, atau container `db` tidak jalan | Tunggu 30 detik lalu refresh. Cek `docker compose ps` — baris `db` harus Up. Kalau tidak Up, cek `docker compose logs db` dan kirim hasilnya ke grup. |
| 5 | `localhost:8080` tidak bisa dibuka (`refused to connect`) | Container `web` tidak jalan | Jalankan `docker compose ps`. Kalau `web` tidak ada atau statusnya Exited, cek `docker compose logs web`, lalu jalankan `docker compose up -d` lagi. |
| 6 | `Virtualization is not enabled` saat membuka Docker | Virtualisasi mati di BIOS | Aktifkan di BIOS (SVM Mode untuk AMD, Intel VT-x untuk Intel). Kalau ragu, minta bantuan PM. |
| 7 | File `.env` tidak ditemukan saat `up` | Langkah 5 terlewat, salah folder, atau file-nya sebenarnya bernama `.env.txt` | Pastikan kamu di folder `B2B`, ulangi langkah 5. Kalau file terlihat ada tapi tetap error, cek dengan `dir` — kalau yang muncul `.env.txt`, ganti namanya: `ren .env.txt .env`. |
| 8 | phpMyAdmin tidak bisa login | Username/password salah | Untuk import database pakai user `root`, password `rootpass`. Untuk melihat data pakai user `app_user`, password `app_pass` (dari `.env`). |
| 9 | Laptop jadi sangat lambat saat Docker jalan | RAM kurang atau terlalu banyak aplikasi terbuka | Tutup aplikasi berat. RAM minimal 8 GB (lihat langkah 1). |
| 10 | `#1044 Access denied for user 'app_user'` saat import, atau database `staplr` tidak muncul di phpMyAdmin | Dua kemungkinan: (a) import login sebagai `app_user`, padahal yang boleh membuat database baru cuma `root`; (b) `DB_NAME` di `.env` masih `app_db`, jadi container pertama membuat database `app_db`, bukan `staplr` | (a) Keluar dari phpMyAdmin, login lagi sebagai `root` / `rootpass`, lalu import ulang. (b) Kalau `staplr` tetap tidak muncul: ubah `DB_NAME` jadi `staplr` di `.env`, lalu `docker compose down -v` dan `docker compose up -d`, tunggu 30 detik, import ulang sebagai root. **Awas:** `down -v` menghapus isi database — cuma boleh di laptop baru yang datanya memang boleh hilang, jangan di laptop yang sudah ada pesanan tesnya. |
| 11 | Kode OTP tidak muncul saat login | `OTP_TEST_MODE` belum `true`, atau container masih jalan dengan setting lama | Cek `.env`, pastikan ada baris `OTP_TEST_MODE=true`. Kalau sudah benar tapi kode tetap tidak muncul, buat ulang containernya: `docker compose down` lalu `docker compose up -d` (tanpa `-v`), lalu login lagi. |
| 12 | Tombol Bayar error atau membuka halaman Xendit | `DEMO_MODE` masih `false`, jadi pembayaran mencari Xendit beneran | Ubah `DEMO_MODE` jadi `true` di `.env`, lalu `docker compose down` dan `docker compose up -d` (tanpa `-v`). |
| 13 | Buka alamat `/pages/dashboard.php` → 404 | File itu tidak ada — dashboard sudah dipisah per peran | Jangan ketik alamat itu. Buka `localhost:8080` saja lalu login; aplikasi otomatis pindah ke dashboard yang sesuai akunmu. |
| 14 | Di phpMyAdmin ada database `Staplr` huruf besar, selain `staplr` huruf kecil | MySQL membedakan huruf besar dan kecil — itu dua database yang berbeda | Yang dipakai aplikasi adalah `staplr` huruf kecil. Abaikan yang huruf besar. Kalau mau rapi, hapus yang huruf besar setelah tanya PM. |

Kalau errormu tidak ada di tabel ini, kirim ke grup **tiga hal ini**:
1. Screenshot error-nya.
2. Hasil perintah `docker compose logs web` dan `docker compose logs db`.
3. Langkah terakhir yang kamu lakukan sebelum error muncul.

## Daftar halaman dan cara membukanya

Semua halaman bisa dibuka dengan mengetik alamatnya di browser, dengan alamat dasar `http://localhost:8080`. **Login sudah asli dan halaman tiap peran terkunci:** halaman buyer hanya bisa dibuka akun pembeli, halaman perusahaan hanya akun perusahaan, halaman admin hanya akun admin. Kalau membuka halaman peran lain, kamu dilempar ke halaman login.

### Halaman utama dan akun

| Halaman | Alamat | Gunanya |
|---|---|---|
| Halaman utama | `/` | Halaman pertama yang muncul; berisi penjelasan Staplr dan tombol masuk/daftar |
| Masuk (login) | `/pages/login.php` | Form login (email + password), lalu langkah OTP 6 kotak |
| Daftar akun | `/pages/daftar.php` | Form pendaftaran akun baru (buyer/perusahaan), lalu verifikasi OTP |
| Lupa kata sandi | `/pages/lupa_sandi.php` | Form minta reset kata sandi lewat OTP email |
| Verifikasi OTP email | `/pages/verifikasi_otp.php` | Masukkan kode OTP saat daftar atau reset kata sandi |
| Profil pengguna | `/pages/profil.php` | Melihat dan mengubah profil, termasuk rekening bank (pembeli/perusahaan) dan NIB/NPWP (perusahaan) |
| Semua notifikasi | `/pages/notifikasi.php` | Daftar pemberitahuan (pesanan baru, status berubah, dll) |

### Halaman buyer (pembeli)

| Halaman | Alamat | Gunanya |
|---|---|---|
| Dashboard buyer | `/pages/pembeli/dashboard_buyer.php` | Ringkasan untuk buyer: pesanan, status, notifikasi |
| Detail produk | `/pages/pembeli/produk_detail.php` | Detail satu produk (harga per kg, stok, penjual) |
| Detail penjual | `/pages/pembeli/detail_penjual.php` | Profil perusahaan penjual |
| Ajukan pesanan (checkout) | `/pages/pembeli/checkout.php` | Form ajukan pesanan: pilih jumlah (kg), lalu buat pesanan |
| Status pesanan | `/pages/pembeli/order_list.php` | Daftar semua pesanan buyer beserta statusnya |
| Detail pesanan | `/pages/order_detail.php` | Detail satu pesanan + riwayat status + tombol yang boleh ditekan |
| Konfirmasi pesanan | `/pages/pembeli/konfirmasi_pesanan.php` | Halaman konfirmasi setelah pesanan dibuat |
| Status pesanan (varian) | `/pages/pembeli/status_pesanan.php` | Sama seperti order_list (judul sama), sisa dari branch lama |
| Ajukan permintaan | `/pages/pembeli/ajukan_permintaan.php` | Buyer mengajukan permintaan barang ke perusahaan |
| Permintaan disetujui | `/pages/pembeli/permintaan_disetujui.php` | Daftar permintaan yang disetujui |
| Pesanan disetujui | `/pages/pembeli/pesanan_disetujui.php` | Daftar pesanan yang disetujui |

### Halaman perusahaan (penjual)

| Halaman | Alamat | Gunanya |
|---|---|---|
| Dashboard perusahaan | `/pages/perusahaan/dashboard_perusahaan.php` | Ringkasan untuk perusahaan: produk, pesanan masuk, stok, keuangan escrow (Siap Cair / Tertahan / Sudah dicairkan) |

### Halaman dispute dan ulasan

| Halaman | Alamat | Gunanya |
|---|---|---|
| Daftar dispute | `/pages/dispute_list.php` | Daftar tiket dispute buyer |
| Diskusi dispute | `/pages/dispute_detail.php` | Diskusi 3 pihak (buyer, perusahaan, admin) dalam satu tiket |
| Pengajuan dispute | `/pages/pengajuan_dispute.php` | Form buyer membuka dispute + upload bukti foto |
| Ulasan produk | `/pages/pembeli/ulasan.php` | Form buyer memberi rating/ulasan setelah pesanan selesai |

### Halaman admin

Semua halaman admin ada di `/pages/admin/`.

| Halaman | Alamat | Gunanya |
|---|---|---|
| Dashboard admin | `/pages/admin/dashboard.php` | Ringkasan admin |
| Manajemen user | `/pages/admin/manajemen-user.php` | Daftar dan kelola pengguna |
| Verifikasi perusahaan | `/pages/admin/verifikasi.php` | Verifikasi perusahaan baru |
| Detail verifikasi | `/pages/admin/detail-verifikasi.php` | Detail satu perusahaan yang diverifikasi |
| Verifikasi dispute | `/pages/admin/verifikasi-dispute.php` | Admin memeriksa dan memverifikasi dispute |
| Pencairan dana | `/pages/admin/keuangan.php` | Melihat dan mencatat pencairan dana escrow |
| Riwayat transaksi | `/pages/admin/riwayat.php` | Riwayat semua transaksi |
| Detail riwayat | `/pages/admin/detail-riwayat.php` | Detail satu riwayat transaksi |
| Daftar dispute | `/pages/admin/dispute.php` | Daftar tiket dispute |
| Detail dispute | `/pages/admin/detail-dispute.php` | Detail satu tiket dispute + diskusi + putusan |

## Aturan penting

- Host database di kode PHP adalah `db`, bukan `localhost`. Di dalam Docker, `db` itu nama container database. Kalau kode PHP memakai `localhost`, artinya container web itu sendiri, bukan database — koneksi akan gagal.
- Jangan commit file `.env`. File itu sudah masuk `.gitignore`.
- Kerja di branch sendiri dari `main`, jangan langsung ke `main`.
- Sebelum mulai kerja tiap hari: `git pull origin main` dulu, supaya kodemu tidak ketinggalan dari teman lain.
- Kalau mengubah `.env` sesudah container jalan, buat ulang containernya (`docker compose down` lalu `docker compose up -d`, tanpa `-v`) supaya setting baru kebaca.
- Jangan jalankan `docker compose down -v` di laptop yang datanya mau disimpan — perintah itu menghapus semua isi database.

## Struktur folder

- `src/` — web root (yang dijalankan Docker dan `start-server.bat`)
  - `src/pages/` — halaman yang dilihat pengguna (login, katalog, order, dispute, dashboard per peran)
  - `src/pages/admin/` — halaman admin
  - `src/templates/` — bagian yang dipakai ulang (header, footer)
  - `src/assets/css/` dan `src/assets/img/` — tampilan dan gambar
  - `src/api/` — endpoint BE (auth, produk, order, webhook, invoice, dispute, ulasan, admin)
  - `src/lib/` — kode bersama BE (koneksi DB, cek login, format jawaban standar, helper OTP, Xendit/escrow)
  - `src/uploads/` — bukti upload (jangan commit isinya)
- `database/` — file SQL
  - `database/online_marketplace_v2.sql` — skema database `staplr` (19 tabel)
  - `database/online_marketplace_v2_seed.sql` — data demo (3 akun, 3 produk, stok)
  - `database/otp_kode_login.sql` — hanya untuk database lama (database baru tidak perlu)
  - `database/arsip-fork/` — arsip skema lama, tidak dipakai
- `docs/` — referensi desain (screenshot Figma, file hi-fi, catatan Xendit)

## Aturan kerja

- Satu branch per fitur dari `main`, contoh: `be-1/auth-login`, `fe-1/login`.
- Satu file, satu pemilik. File bersama hanya diubah lewat PR yang disetujui pemiliknya.
- Branch lama sudah dihapus, yang tersisa `main` dan `pembeli`. Branch `pembeli` disimpan sebagai catatan saja — jangan kerjakan fitur baru di sana, kerja dari `main`.
