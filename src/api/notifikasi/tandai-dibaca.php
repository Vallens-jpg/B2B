<?php
// Tandai notifikasi sebagai sudah dibaca (per pengguna). POST saja.
// Tanpa body: tandai semua notifikasi yang terlihat oleh peran ini.
// Dengan {"riwayat_id": N}: tandai satu notifikasi itu saja.
// Dipakai halaman notifikasi.php (otomatis saat dilihat + tombol Tandai semua dibaca).
require_once __DIR__ . '/../../lib/db.php';
require_once __DIR__ . '/../../lib/response.php';
require_once __DIR__ . '/../../lib/auth.php';

$user = auth_user();
if (!$user) {
    json_error('Silakan login dulu', 401);
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_error('Metode tidak diizinkan', 405);
}
$role = $user['role'] ?? '';
if (!in_array($role, ['pembeli', 'perusahaan', 'admin'], true)) {
    json_error('Peran Anda tidak boleh melihat notifikasi', 403);
}

$input = json_input();
$riwayatId = (int) ($input['riwayat_id'] ?? 0);

$base =
    'FROM riwayat_status r ' .
    'JOIN pesanan ps ON ps.id_pesanan = r.id_pesanan ' .
    'JOIN penawaran pn ON pn.id_penawaran = ps.id_penawaran ' .
    'JOIN produk pr ON pr.id_produk = pn.id_produk ';
try {
    $pdo = db();
    // Resolve id profil dari session (rule global 2): $user['id'] = id_pengguna.
    $stmtProfil = $pdo->prepare('SELECT id_pembeli, id_perusahaan, id_admin FROM pengguna WHERE id_pengguna = ? LIMIT 1');
    $stmtProfil->execute([(int) $user['id']]);
    $profil = $stmtProfil->fetch();
    $idPembeli = (int) ($profil['id_pembeli'] ?? 0);
    $idPerusahaan = (int) ($profil['id_perusahaan'] ?? 0);
    if ($riwayatId > 0) {
        // Satu notifikasi, hanya bila terlihat oleh peran ini.
        if ($role === 'perusahaan') {
            $stmt = $pdo->prepare('INSERT IGNORE INTO notifikasi_dibaca (id_pengguna, id_riwayat) SELECT ?, r.id_riwayat ' . $base . 'WHERE ps.id_perusahaan = ? AND r.id_riwayat = ?');
            $stmt->execute([(int) $user['id'], $idPerusahaan, $riwayatId]);
        } elseif ($role === 'pembeli') {
            $stmt = $pdo->prepare('INSERT IGNORE INTO notifikasi_dibaca (id_pengguna, id_riwayat) SELECT ?, r.id_riwayat ' . $base . 'WHERE ps.id_pembeli = ? AND r.id_riwayat = ?');
            $stmt->execute([(int) $user['id'], $idPembeli, $riwayatId]);
        } else {
            $stmt = $pdo->prepare('INSERT IGNORE INTO notifikasi_dibaca (id_pengguna, id_riwayat) SELECT ?, r.id_riwayat ' . $base . 'WHERE r.id_riwayat = ?');
            $stmt->execute([(int) $user['id'], $riwayatId]);
        }
    } else {
        // Semua notifikasi yang terlihat oleh peran ini.
        if ($role === 'perusahaan') {
            $stmt = $pdo->prepare('INSERT IGNORE INTO notifikasi_dibaca (id_pengguna, id_riwayat) SELECT ?, r.id_riwayat ' . $base . 'WHERE ps.id_perusahaan = ?');
            $stmt->execute([(int) $user['id'], $idPerusahaan]);
        } elseif ($role === 'pembeli') {
            $stmt = $pdo->prepare('INSERT IGNORE INTO notifikasi_dibaca (id_pengguna, id_riwayat) SELECT ?, r.id_riwayat ' . $base . 'WHERE ps.id_pembeli = ?');
            $stmt->execute([(int) $user['id'], $idPembeli]);
        } else {
            $stmt = $pdo->prepare('INSERT IGNORE INTO notifikasi_dibaca (id_pengguna, id_riwayat) SELECT ?, r.id_riwayat ' . $base);
            $stmt->execute([(int) $user['id']]);
        }
    }
    json_ok(['ditandai' => $stmt->rowCount()]);
} catch (Throwable $e) {
    json_error('Database belum siap. Jalankan online_marketplace_v2.sql dulu.', 500);
}
