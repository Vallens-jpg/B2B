<?php
// Buat pesanan (produk_id, jumlah kg). Status awal menunggu_bayar, total + fee dihitung server.
// Fee layanan 2,5% dari total barang.
require_once __DIR__ . '/../../lib/db.php';
require_once __DIR__ . '/../../lib/response.php';
require_once __DIR__ . '/../../lib/auth.php';

$user = auth_user();
if (!$user || ($user['role'] ?? '') !== 'pembeli') {
    json_error('Hanya pembeli yang boleh membuat pesanan', 403);
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_error('Metode tidak diizinkan', 405);
}

$input = json_input();
$produkId = (int) ($input['produk_id'] ?? 0);
$jumlahKg = (float) ($input['jumlah_kg'] ?? $input['jumlah'] ?? 0);
if ($produkId <= 0 || $jumlahKg <= 0) {
    json_error('Produk dan jumlah kg wajib diisi dengan benar', 400);
}

try {
    $pdo = db();
    $pdo->beginTransaction();
    $stmt = $pdo->prepare('SELECT id, harga_per_kg, stok_kg FROM produk WHERE id = ? FOR UPDATE');
    $stmt->execute([$produkId]);
    $produk = $stmt->fetch();
    if (!$produk) {
        $pdo->rollBack();
        json_error('Produk tidak ditemukan', 404);
    }
    if ((float) $produk['stok_kg'] < $jumlahKg) {
        $pdo->rollBack();
        json_error('Stok produk tidak cukup', 409);
    }
    $hargaSatuan = (float) $produk['harga_per_kg'];
    $total = round($hargaSatuan * $jumlahKg, 2);
    $fee = round($total * 0.025, 2);
    $totalBayar = round($total + $fee, 2);

    $stmt = $pdo->prepare('UPDATE produk SET stok_kg = stok_kg - ? WHERE id = ?');
    $stmt->execute([$jumlahKg, $produkId]);

    $stmt = $pdo->prepare('INSERT INTO orders (buyer_id, produk_id, jumlah_kg, harga_satuan, total, fee, status) VALUES (?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([(int) $user['id'], $produkId, $jumlahKg, $hargaSatuan, $total, $fee, 'menunggu_bayar']);
    $orderId = (int) $pdo->lastInsertId();

    $nomorInvoice = 'INV-' . date('Ymd') . '-' . str_pad((string) $orderId, 6, '0', STR_PAD_LEFT);
    $stmt = $pdo->prepare('INSERT INTO invoice (order_id, nomor, total, status) VALUES (?, ?, ?, ?)');
    $stmt->execute([$orderId, $nomorInvoice, $totalBayar, 'belum_bayar']);

    $stmt = $pdo->prepare('INSERT INTO escrow (order_id, jumlah_ditahan, status) VALUES (?, ?, ?)');
    $stmt->execute([$orderId, $totalBayar, 'ditahan']);

    $stmt = $pdo->prepare('INSERT INTO riwayat_status (order_id, status_lama, status_baru, dicatat_oleh) VALUES (?, ?, ?, ?)');
    $stmt->execute([$orderId, null, 'menunggu_bayar', (int) $user['id']]);
    $pdo->commit();
} catch (Throwable $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    json_error('Database belum siap. Jalankan migrasi 01 dan 03 dulu.', 500);
}

json_ok([
    'order_id' => $orderId,
    'nomor_invoice' => $nomorInvoice,
    'total' => $total,
    'fee' => $fee,
    'total_bayar' => $totalBayar,
    'status' => 'menunggu_bayar',
]);
