<?php
// Buyer: dikirim -> diterima -> selesai (order_id). Sisa dana cair ke perusahaan.
// Panggilan pertama: dikirim -> diterima. Panggilan kedua: diterima -> selesai + sisa dana cair.
// Sisa = total_harga - DP tahap 1; fee_nominal dipotong dari sisa di tahap 2.
require_once __DIR__ . '/../../lib/db.php';
require_once __DIR__ . '/../../lib/response.php';
require_once __DIR__ . '/../../lib/auth.php';

$user = auth_user();
if (!$user || ($user['role'] ?? '') !== 'pembeli') {
    json_error('Hanya pembeli yang boleh mengonfirmasi penerimaan', 403);
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_error('Metode tidak diizinkan', 405);
}

$input = json_input();
$orderId = (int) ($input['order_id'] ?? 0);
if ($orderId <= 0) {
    json_error('Order tidak ditemukan', 404);
}

try {
    $pdo = db();
    // Session menyimpan pengguna.id_pengguna, bukan id_pembeli: resolve dulu.
    $stmt = $pdo->prepare('SELECT id_pembeli FROM pengguna WHERE id_pengguna = ? LIMIT 1');
    $stmt->execute([(int) $user['id']]);
    $profil = $stmt->fetch();
    $idPembeli = ($profil && $profil['id_pembeli'] !== null) ? (int) $profil['id_pembeli'] : 0;

    $pdo->beginTransaction();
    $stmt = $pdo->prepare('SELECT id_pesanan, id_pembeli, id_perusahaan, total_harga, fee_nominal, persen_dp, status FROM pesanan WHERE id_pesanan = ? FOR UPDATE');
    $stmt->execute([$orderId]);
    $order = $stmt->fetch();
    if (!$order) {
        $pdo->rollBack();
        json_error('Order tidak ditemukan', 404);
    }
    if ((int) $order['id_pembeli'] !== $idPembeli) {
        $pdo->rollBack();
        json_error('Order ini bukan milik Anda', 403);
    }
    if ($order['status'] === 'dikirim') {
        $statusBaru = 'diterima';
    } elseif ($order['status'] === 'diterima') {
        $statusBaru = 'selesai';
    } else {
        $pdo->rollBack();
        json_error('Order harus berstatus dikirim atau diterima dulu', 409);
    }
    if ($statusBaru === 'diterima') {
        $stmt = $pdo->prepare("UPDATE pesanan SET status = 'diterima', tanggal_diterima = CURDATE() WHERE id_pesanan = ?");
    } else {
        $stmt = $pdo->prepare("UPDATE pesanan SET status = 'selesai' WHERE id_pesanan = ?");
    }
    $stmt->execute([$orderId]);
    $sisaCair = 0.0;
    if ($statusBaru === 'selesai') {
        // Bila tahap 2 sudah ada, pakai nominal-nya (idempotent: panggilan ulang tidak membuat ganda).
        $stmt = $pdo->prepare('SELECT nominal FROM pencairan WHERE id_pesanan = ? AND tahap = 2 LIMIT 1');
        $stmt->execute([$orderId]);
        $tahap2 = $stmt->fetch();
        if ($tahap2) {
            $sisaCair = (float) $tahap2['nominal'];
        } else {
            $stmt = $pdo->prepare('SELECT nominal FROM pencairan WHERE id_pesanan = ? AND tahap = 1 LIMIT 1');
            $stmt->execute([$orderId]);
            $tahap1 = $stmt->fetch();
            $dp = $tahap1 ? (float) $tahap1['nominal'] : round((float) $order['total_harga'] * (float) $order['persen_dp'] / 100, 2);
            $sisaKotor = round((float) $order['total_harga'] - $dp, 2);
            $sisaCair = round($sisaKotor - (float) $order['fee_nominal'], 2);
            // Pencairan tahap 2 (pelunasan) bila ada baris pembayaran yang sudah paid.
            $stmt = $pdo->prepare("SELECT id_pembayaran FROM pembayaran WHERE id_pesanan = ? AND status = 'paid' LIMIT 1");
            $stmt->execute([$orderId]);
            $pembayaran = $stmt->fetch();
            if ($pembayaran) {
                $stmt = $pdo->prepare('SELECT nama_bank, no_rekening, atas_nama FROM perusahaan WHERE id_perusahaan = ? LIMIT 1');
                $stmt->execute([(int) $order['id_perusahaan']]);
                $perusahaan = $stmt->fetch();
                $stmt = $pdo->prepare('SELECT id_pengguna FROM pengguna WHERE id_perusahaan = ? LIMIT 1');
                $stmt->execute([(int) $order['id_perusahaan']]);
                $penerima = $stmt->fetch();
                if ($perusahaan && $penerima) {
                    $stmt = $pdo->prepare(
                        'INSERT INTO pencairan (id_pembayaran, id_pesanan, id_pengguna, penerima, external_id, tahap, nominal_kotor, potongan_fee, nominal, nama_bank, no_rekening, atas_nama, status) ' .
                        "VALUES (?, ?, ?, 'perusahaan', ?, 2, ?, ?, ?, ?, ?, ?, 'pending')"
                    );
                    $stmt->execute([(int) $pembayaran['id_pembayaran'], $orderId, (int) $penerima['id_pengguna'], 'CAIR-2-' . $orderId, $sisaKotor, (float) $order['fee_nominal'], $sisaCair, $perusahaan['nama_bank'], $perusahaan['no_rekening'], $perusahaan['atas_nama']]);
                }
            }
        }
    }
    $stmt = $pdo->prepare('INSERT INTO riwayat_status (id_pesanan, status_lama, status_baru, id_pengguna) VALUES (?, ?, ?, ?)');
    $stmt->execute([$orderId, $order['status'], $statusBaru, (int) $user['id']]);
    $pdo->commit();
} catch (Throwable $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    json_error('Database belum siap. Jalankan online_marketplace_v2.sql dulu.', 500);
}

json_ok(['order_id' => $orderId, 'status' => $statusBaru, 'sisa_cair' => $sisaCair]);
