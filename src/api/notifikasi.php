<?php
// Daftar notifikasi perubahan status pesanan (20 terakhir, terbaru dulu).
// Sumber: riwayat_status JOIN orders (+ produk & pembeli).
// - pembeli: hanya riwayat order miliknya.
// - perusahaan: hanya riwayat order atas produk miliknya.
// - admin: semua riwayat.
// Belum login -> 401. Dipakai halaman notifikasi.php.
require_once __DIR__ . '/../lib/db.php';
require_once __DIR__ . '/../lib/response.php';
require_once __DIR__ . '/../lib/auth.php';

$user = auth_user();
if (!$user) {
    json_error('Silakan login dulu', 401);
}
$role = $user['role'] ?? '';
if (!in_array($role, ['pembeli', 'perusahaan', 'admin'], true)) {
    json_error('Peran Anda tidak boleh melihat notifikasi', 403);
}

$labelStatus = [
    'menunggu_bayar' => 'Menunggu Pembayaran',
    'dibayar' => 'Dibayar',
    'diproses' => 'Diproses',
    'dikirim' => 'Dikirim',
    'diterima' => 'Diterima',
    'selesai' => 'Selesai',
    'dibatalkan' => 'Dibatalkan',
];

$select =
    'SELECT r.id AS riwayat_id, r.status_lama, r.status_baru, r.created_at, o.id AS order_id, ' .
    'p.nama AS nama_produk, u.nama AS nama_buyer, ' .
    'CASE WHEN d.riwayat_id IS NULL THEN 0 ELSE 1 END AS dibaca ' .
    'FROM riwayat_status r ' .
    'JOIN orders o ON o.id = r.order_id ' .
    'JOIN produk p ON p.id = o.produk_id ' .
    'JOIN users u ON u.id = o.buyer_id ' .
    'LEFT JOIN notifikasi_dibaca d ON d.riwayat_id = r.id AND d.user_id = ? ';

try {
    if ($role === 'perusahaan') {
        $stmt = db()->prepare($select . 'WHERE p.perusahaan_id = ? ORDER BY r.id DESC LIMIT 20');
        $stmt->execute([(int) $user['id'], (int) $user['id']]);
    } elseif ($role === 'pembeli') {
        $stmt = db()->prepare($select . 'WHERE o.buyer_id = ? ORDER BY r.id DESC LIMIT 20');
        $stmt->execute([(int) $user['id'], (int) $user['id']]);
    } else {
        $stmt = db()->prepare($select . 'ORDER BY r.id DESC LIMIT 20');
        $stmt->execute([(int) $user['id']]);
    }
    $rows = $stmt->fetchAll();
} catch (Throwable $e) {
    json_error('Database belum siap. Jalankan migrasi 01, 03, dan 07 dulu.', 500);
}

$notifikasi = [];
foreach ($rows as $r) {
    $baru = $labelStatus[$r['status_baru']] ?? $r['status_baru'];
    $lama = $r['status_lama'] ? ($labelStatus[$r['status_lama']] ?? $r['status_lama']) : 'Baru dibuat';
    $notifikasi[] = [
        'riwayat_id' => (int) $r['riwayat_id'],
        'order_id' => (int) $r['order_id'],
        'dibaca' => ((int) $r['dibaca'] === 1),
        'nama_produk' => $r['nama_produk'],
        'nama_buyer' => $r['nama_buyer'],
        'status_lama' => $r['status_lama'],
        'status_baru' => $r['status_baru'],
        'created_at' => $r['created_at'],
        'judul' => 'Pesanan #' . (int) $r['order_id'] . ' — ' . $baru,
        'isi' => 'Pesanan ' . $r['nama_produk'] . ' berubah status dari ' . $lama . ' menjadi ' . $baru . '.',
    ];
}

json_ok(['notifikasi' => $notifikasi]);
