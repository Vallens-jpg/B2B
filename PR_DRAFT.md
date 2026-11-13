# PR draft — revisi-admin-samakan-perusahaan → admin

Status: DRAFT, prepared 2026-11-10 (Asia/Jakarta). Not created on GitHub.
Branch (to create from `admin`): `revisi-admin-samakan-perusahaan`
Base: `admin` — Vallens-jpg/B2B

## Title
Samakan desain admin dengan perusahaan (sidebar, logo, ikon, topbar, kartu, tabel, tombol)

## Body (ID)
Menyamakan desain halaman admin dengan halaman perusahaan, sesuai preview yang sudah disetujui di chat.

**Yang diubah (19 file):** `css/admin.css` + 18 halaman di `html/admin/`
- Font: Plus Jakarta Sans (dari Segoe UI)
- Sidebar: 260px → 215px; gradasi diganti persis gradasi perusahaan (5 stop: `#87ab95 0%, #afc5aa 30%, #ebedc8 55%, #d2dcba 75%, #82a78f 100%`)
- Logo: `staplr-logo.png` 56px → logo transparan 34px (`assets/img/logo-transparent.png`, sudah ada di branch `admin`) + teks "Staplr."
- Ikon menu: 6 ikon outline kuning 20px, stroke `#ffc700`, konsisten dengan perusahaan (Verifikasi dan Manajemen User dibuat baru dengan gaya yang sama)
- Topbar, kartu, tabel, dan tombol disamakan dengan perusahaan
- Menu, data, dan struktur halaman tidak berubah

**Catatan:**
- `css/admin.css` saat ini tidak di-link oleh halaman mana pun (semua halaman pakai inline `<style>`), jadi token yang sama juga diterapkan ke inline style tiap halaman.
- Tidak ada perubahan ke branch `perusahaan`, `main`, atau file lain.

**Cara cek:** bandingkan tab Admin Baru vs Perusahaan di preview chat; gradasi sidebar sudah identik.
