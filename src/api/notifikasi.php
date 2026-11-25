<?php
// Daftar notifikasi perubahan status pesanan (20 terakhir, terbaru dulu).
// Sumber: riwayat_status JOIN pesanan (+ penawaran -> produk & pembeli).
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
    'SELECT r.id_riwayat AS riwayat_id, r.status_lama, r.status_baru, r.dibuat_pada AS created_at, ps.id_pesanan AS order_id, ' .
    'pr.nama_produk AS nama_produk, pb.nama_pembeli AS nama_buyer, ' .
    'CASE WHEN d.id_riwayat IS NULL THEN 0 ELSE 1 END AS dibaca ' .
    'FROM riwayat_status r ' .
    'JOIN pesanan ps ON ps.id_pesanan = r.id_pesanan ' .
    'JOIN penawaran pn ON pn.id_penawaran = ps.id_penawaran ' .
    'JOIN produk pr ON pr.id_produk = pn.id_produk ' .
    'JOIN pembeli pb ON pb.id_pembeli = ps.id_pembeli ' .
    'LEFT JOIN notifikasi_dibaca d ON d.id_riwayat = r.id_riwayat AND d.id_pengguna = ? ';

try {
    // Resolve id profil dari session (rule global 2): $user['id'] = id_pengguna.
    $stmtProfil = db()->prepare('SELECT id_pembeli, id_perusahaan, id_admin FROM pengguna WHERE id_pengguna = ? LIMIT 1');
    $stmtProfil->execute([(int) $user['id']]);
    $profil = $stmtProfil->fetch();
    $idPembeli = (int) ($profil['id_pembeli'] ?? 0);
    $idPerusahaan = (int) ($profil['id_perusahaan'] ?? 0);
    if ($role === 'perusahaan') {
        $stmt = db()->prepare($select . 'WHERE ps.id_perusahaan = ? ORDER BY r.id_riwayat DESC LIMIT 20');
        $stmt->execute([(int) $user['id'], $idPerusahaan]);
    } elseif ($role === 'pembeli') {
        $stmt = db()->prepare($select . 'WHERE ps.id_pembeli = ? ORDER BY r.id_riwayat DESC LIMIT 20');
        $stmt->execute([(int) $user['id'], $idPembeli]);
    } else {
        $stmt = db()->prepare($select . 'ORDER BY r.id_riwayat DESC LIMIT 20');
        $stmt->execute([(int) $user['id']]);
    }
    $rows = $stmt->fetchAll();
} catch (Throwable $e) {
    json_error('Database belum siap. Jalankan online_marketplace_v2.sql dulu.', 500);
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
