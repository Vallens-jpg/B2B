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
    'JOIN orders o ON o.id = r.order_id ' .
    'JOIN produk p ON p.id = o.produk_id ';
try {
    $pdo = db();
    if ($riwayatId > 0) {
        // Satu notifikasi, hanya bila terlihat oleh peran ini.
        if ($role === 'perusahaan') {
            $stmt = $pdo->prepare('INSERT IGNORE INTO notifikasi_dibaca (user_id, riwayat_id) SELECT ?, r.id ' . $base . 'WHERE p.perusahaan_id = ? AND r.id = ?');
            $stmt->execute([(int) $user['id'], (int) $user['id'], $riwayatId]);
        } elseif ($role === 'pembeli') {
            $stmt = $pdo->prepare('INSERT IGNORE INTO notifikasi_dibaca (user_id, riwayat_id) SELECT ?, r.id ' . $base . 'WHERE o.buyer_id = ? AND r.id = ?');
            $stmt->execute([(int) $user['id'], (int) $user['id'], $riwayatId]);
        } else {
            $stmt = $pdo->prepare('INSERT IGNORE INTO notifikasi_dibaca (user_id, riwayat_id) SELECT ?, r.id ' . $base . 'WHERE r.id = ?');
            $stmt->execute([(int) $user['id'], $riwayatId]);
        }
    } else {
        // Semua notifikasi yang terlihat oleh peran ini.
        if ($role === 'perusahaan') {
            $stmt = $pdo->prepare('INSERT IGNORE INTO notifikasi_dibaca (user_id, riwayat_id) SELECT ?, r.id ' . $base . 'WHERE p.perusahaan_id = ?');
            $stmt->execute([(int) $user['id'], (int) $user['id']]);
        } elseif ($role === 'pembeli') {
            $stmt = $pdo->prepare('INSERT IGNORE INTO notifikasi_dibaca (user_id, riwayat_id) SELECT ?, r.id ' . $base . 'WHERE o.buyer_id = ?');
            $stmt->execute([(int) $user['id'], (int) $user['id']]);
        } else {
            $stmt = $pdo->prepare('INSERT IGNORE INTO notifikasi_dibaca (user_id, riwayat_id) SELECT ?, r.id ' . $base);
            $stmt->execute([(int) $user['id']]);
        }
    }
    json_ok(['ditandai' => $stmt->rowCount()]);
} catch (Throwable $e) {
    json_error('Database belum siap. Jalankan migrasi 07 dulu.', 500);
}
