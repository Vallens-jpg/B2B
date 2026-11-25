# Xendit (Test Mode) — Pembayaran & Webhook

Staplr memakai Xendit **Test Mode**. Tidak ada uang sungguhan yang berpindah.

## Alur bayar

1. Pembeli membuat pesanan → status `menunggu_bayar`.
2. Pembeli klik "Bayar Sekarang" → `api/order/bayar.php` membuat invoice Xendit.
   - `external_id` invoice: `INV-YYYYMMDD-XXXXXX` (disimpan di `pembayaran.xendit_id`).
   - Jumlah yang ditagih ke pembeli = `total_harga` saja. Fee platform 10%
     dipotong dari dana perusahaan, bukan ditambahkan ke tagihan pembeli.
3. Pembeli membayar di halaman checkout Xendit (kartu tes Xendit).
4. Xendit mengirim webhook ke aplikasi → pesanan berubah menjadi `dibayar`.

## Webhook

- **Endpoint yang terdaftar di dashboard Xendit:**
  `POST /api/webhook/xendit.php` (`src/api/webhook/xendit.php`).
- **Token:** `XENDIT_WEBHOOK_TOKEN` di `.env` — harus sama persis dengan
  callback token yang terdaftar di dashboard Xendit (Settings → Webhooks).
  - Token kosong → server menjawab **501**.
  - Token tidak cocok → **403**.
- **Yang dilakukan webhook saat menerima status `PAID`:**
  - `pembayaran` → `paid` + `paid_at` terisi.
  - `pesanan` → `dibayar`.
  - Satu baris `riwayat_status` ditambahkan.
  - Sudah idempotent: webhook kedua untuk pesanan yang sama tidak mengubah
    apa-apa.
- Webhook mencari pesanan lewat `pembayaran.xendit_id` (= `external_id`
  invoice). Invoice tanpa baris `pembayaran` (misalnya invoice tes manual)
  akan dijawab **404** — itu perilaku yang benar.

## Menjalankan webhook di lokal

1. Isi `.env`: `XENDIT_SECRET_KEY` dan `XENDIT_WEBHOOK_TOKEN`.
2. Jalankan tunnel: `ngrok http 8080`.
3. Daftarkan URL webhook di dashboard Xendit:
   `https://<url-ngrok>/api/webhook/xendit.php`.
4. Setelah mengubah `.env`, jalankan ulang container
   (`docker compose down` lalu `docker compose up -d`).

## Catatan

- `src/webhook/xendit.php` adalah file lama yang hanya mencatat log —
  **bukan** endpoint yang terdaftar di dashboard Xendit. Jangan daftarkan
  file itu sebagai webhook.
- Kartu tes Xendit (dari dokumentasi resmi Xendit):
  Visa `4000000000001000`, tanggal bebas di masa depan, CVC `123`.
