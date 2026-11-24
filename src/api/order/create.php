<?php
// Buat pesanan (produk_id, jumlah kg). Status awal menunggu_bayar, total dihitung server.
// Skema v2: fee platform 0 (fee dipotong dari dana perusahaan nanti, bukan dari pembeli);
// pesanan dibuat lewat rantai permintaan -> penawaran -> pesanan, ketiganya dibuat
// otomatis berstatus disetujui dalam satu transaksi (tidak ada endpoint persetujuan
// permintaan di repo ini).
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
    // Session menyimpan pengguna.id_pengguna, bukan id profil pembeli.
    $stmt = $pdo->prepare('SELECT id_pembeli FROM pengguna WHERE id_pengguna = ?');
    $stmt->execute([(int) $user['id']]);
    $profil = $stmt->fetch();
    $idPembeli = $profil ? (int) ($profil['id_pembeli'] ?? 0) : 0;
    if ($idPembeli <= 0) {
        json_error('Profil pembeli tidak ditemukan', 403);
    }

    $pdo->beginTransaction();
    $stmt = $pdo->prepare('SELECT id_produk, id_perusahaan, nama_produk, harga_standar FROM produk WHERE id_produk = ? FOR UPDATE');
    $stmt->execute([$produkId]);
    $produk = $stmt->fetch();
    if (!$produk) {
        $pdo->rollBack();
        json_error('Produk tidak ditemukan', 404);
    }
    // Stok v2: baris TERBARU per produk di tabel stok (tidak ada lagi kolom stok di produk).
    $stmt = $pdo->prepare('SELECT id_stok, jumlah_stok FROM stok WHERE id_produk = ? ORDER BY tanggal_stok DESC LIMIT 1 FOR UPDATE');
    $stmt->execute([$produkId]);
    $stok = $stmt->fetch();
    if ($stok && (float) $stok['jumlah_stok'] < $jumlahKg) {
        $pdo->rollBack();
        json_error('Stok produk tidak cukup', 409);
    }
    $hargaSatuan = (float) $produk['harga_standar'];
    $total = round($hargaSatuan * $jumlahKg, 2);
    $fee = 0;
    $totalBayar = $total;

    if ($stok) {
        $stmt = $pdo->prepare('UPDATE stok SET jumlah_stok = jumlah_stok - ? WHERE id_stok = ?');
        $stmt->execute([$jumlahKg, (int) $stok['id_stok']]);
    }

    $stmt = $pdo->prepare("INSERT INTO permintaan (id_pembeli, id_produk, jenis_komoditas, tanggal_permintaan, jumlah_permintaan, status) VALUES (?, ?, ?, CURDATE(), ?, 'disetujui')");
    $stmt->execute([$idPembeli, $produkId, (string) $produk['nama_produk'], $jumlahKg]);
    $permintaanId = (int) $pdo->lastInsertId();

    $stmt = $pdo->prepare("INSERT INTO penawaran (id_permintaan, id_perusahaan, id_produk, tanggal_penawaran, jumlah, harga_satuan, harga_total, status) VALUES (?, ?, ?, CURDATE(), ?, ?, ?, 'disetujui')");
    $stmt->execute([$permintaanId, (int) $produk['id_perusahaan'], $produkId, $jumlahKg, $hargaSatuan, $total]);
    $penawaranId = (int) $pdo->lastInsertId();

    $stmt = $pdo->prepare("INSERT INTO pesanan (id_penawaran, id_pembeli, id_perusahaan, jumlah, harga_satuan, total_harga, fee_persen, fee_nominal, persen_dp, status) VALUES (?, ?, ?, ?, ?, ?, 0, 0, 0, 'menunggu_bayar')");
    $stmt->execute([$penawaranId, $idPembeli, (int) $produk['id_perusahaan'], $jumlahKg, $hargaSatuan, $total]);
    $orderId = (int) $pdo->lastInsertId();

    $nomorInvoice = 'INV-' . date('Ymd') . '-' . str_pad((string) $orderId, 6, '0', STR_PAD_LEFT);
    $stmt = $pdo->prepare("INSERT INTO pembayaran (id_pesanan, xendit_id, jumlah, status) VALUES (?, ?, ?, 'pending')");
    $stmt->execute([$orderId, $nomorInvoice, $total]);

    $stmt = $pdo->prepare('INSERT INTO riwayat_status (id_pesanan, status_lama, status_baru, id_pengguna) VALUES (?, ?, ?, ?)');
    $stmt->execute([$orderId, null, 'menunggu_bayar', (int) $user['id']]);
    $pdo->commit();
} catch (Throwable $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    json_error('Database belum siap. Jalankan online_marketplace_v2.sql dulu.', 500);
}

json_ok([
    'order_id' => $orderId,
    'nomor_invoice' => $nomorInvoice,
    'total' => $total,
    'fee' => $fee,
    'total_bayar' => $totalBayar,
    'status' => 'menunggu_bayar',
]);
