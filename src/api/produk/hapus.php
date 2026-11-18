<?php
// Hapus produk (produk_id). Tolak kalau produk ada di order yang masih jalan.
require_once __DIR__ . '/../../lib/db.php';
require_once __DIR__ . '/../../lib/response.php';
require_once __DIR__ . '/../../lib/auth.php';

$user = auth_user();
if (!$user || ($user['role'] ?? '') !== 'perusahaan') {
    json_error('Hanya perusahaan yang boleh menghapus produk', 403);
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_error('Metode tidak diizinkan', 405);
}

$input = json_input();
$id = (int) ($input['produk_id'] ?? $input['id'] ?? 0);
if ($id <= 0) {
    json_error('Produk tidak ditemukan', 404);
}

try {
    $pdo = db();
    $stmt = $pdo->prepare('SELECT id, perusahaan_id FROM produk WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    $produk = $stmt->fetch();
    if (!$produk) {
        json_error('Produk tidak ditemukan', 404);
    }
    if (!empty($produk['perusahaan_id']) && (int) $produk['perusahaan_id'] !== (int) $user['id']) {
        json_error('Produk ini bukan milik perusahaan Anda', 403);
    }
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE produk_id = ? AND status NOT IN ('selesai', 'dibatalkan')");
    $stmt->execute([$id]);
    if ((int) $stmt->fetchColumn() > 0) {
        json_error('Produk tidak bisa dihapus karena masih ada di order yang berjalan', 409);
    }
    $stmt = $pdo->prepare('DELETE FROM produk WHERE id = ?');
    $stmt->execute([$id]);
} catch (Throwable $e) {
    if ($e instanceof PDOException && str_contains($e->getMessage(), '1451')) {
        json_error('Produk tidak bisa dihapus karena masih dipakai di order', 409);
    }
    json_error('Database belum siap. Jalankan migrasi 01 dan 03 dulu.', 500);
}

json_ok(['produk_id' => $id]);
