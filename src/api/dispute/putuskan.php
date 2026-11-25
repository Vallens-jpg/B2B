<?php
// Admin saja: putusan 'uang kembali ke buyer' atau 'lanjut ke perusahaan'.
require_once __DIR__ . '/../../lib/db.php';
require_once __DIR__ . '/../../lib/response.php';
require_once __DIR__ . '/../../lib/auth.php';

$user = auth_user();
if (!$user || ($user['role'] ?? '') !== 'admin') {
    json_error('Hanya admin yang boleh memutuskan dispute', 403);
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_error('Metode tidak diizinkan', 405);
}

$input = json_input();
$id = (int) ($input['dispute_id'] ?? $input['id'] ?? 0);
$putusan = trim($input['putusan'] ?? '');
$catatan = trim((string) ($input['catatan'] ?? ''));
if (mb_strlen($catatan) > 1000) {
    $catatan = mb_substr($catatan, 0, 1000);
}
if ($id <= 0 || !in_array($putusan, ['uang_kembali', 'lanjut_perusahaan', 'revisi'], true)) {
    json_error('Putusan harus uang_kembali, lanjut_perusahaan, atau revisi', 400);
}

try {
    $pdo = db();
    $pdo->beginTransaction();
    $stmt = $pdo->prepare('SELECT id_dispute, id_pesanan, status FROM dispute WHERE id_dispute = ? FOR UPDATE');
    $stmt->execute([$id]);
    $dispute = $stmt->fetch();
    if (!$dispute) {
        $pdo->rollBack();
        json_error('Dispute tidak ditemukan', 404);
    }
    if ($dispute['status'] === 'selesai') {
        $pdo->rollBack();
        json_error('Dispute ini sudah diputuskan', 409);
    }
    // 'revisi': bukan putusan akhir — kembalikan tiket ke 'diproses' dan
    // sampaikan catatan admin sebagai pesan diskusi, supaya buyer &
    // perusahaan bisa merevisi usulan. Escrow/order tidak disentuh.
    if ($putusan === 'revisi') {
        $pesanRevisi = $catatan !== '' ? $catatan : 'Admin meminta revisi rincian usulan dispute.';
        $stmt = $pdo->prepare('INSERT INTO dispute_pesan (id_dispute, id_pengguna, pesan) VALUES (?, ?, ?)');
        $stmt->execute([$id, (int) $user['id'], $pesanRevisi]);
        $stmt = $pdo->prepare("UPDATE dispute SET status = 'diproses', keputusan = NULL, catatan_admin = ?, tanggal_selesai = NULL WHERE id_dispute = ?");
        $stmt->execute([$catatan !== '' ? $catatan : null, $id]);
        $pdo->commit();
        json_ok(['dispute_id' => $id, 'status' => 'diproses', 'putusan' => 'revisi']);
    }
    // Catat admin pemutus (profil admin dari akun session) bila ada.
    $stmt = $pdo->prepare('SELECT id_admin FROM pengguna WHERE id_pengguna = ? LIMIT 1');
    $stmt->execute([(int) $user['id']]);
    $profilAdmin = $stmt->fetch();
    $idAdmin = ($profilAdmin && !empty($profilAdmin['id_admin'])) ? (int) $profilAdmin['id_admin'] : null;
    $stmt = $pdo->prepare("UPDATE dispute SET status = 'selesai', keputusan = ?, catatan_admin = ?, tanggal_selesai = NOW(), id_admin = ? WHERE id_dispute = ?");
    $stmt->execute([$putusan, $catatan !== '' ? $catatan : null, $idAdmin, $id]);

    $orderId = (int) $dispute['id_pesanan'];
    $stmt = $pdo->prepare('SELECT ps.id_pesanan, ps.id_pembeli, ps.id_perusahaan, ps.jumlah, ps.total_harga, ps.fee_nominal, ps.persen_dp, ps.status, pn.id_produk FROM pesanan ps JOIN penawaran pn ON pn.id_penawaran = ps.id_penawaran WHERE ps.id_pesanan = ? FOR UPDATE');
    $stmt->execute([$orderId]);
    $order = $stmt->fetch();

    if ($order) {
        if ($putusan === 'uang_kembali') {
            if ($order['status'] !== 'dibatalkan') {
                $stmt = $pdo->prepare("UPDATE pesanan SET status = 'dibatalkan', alasan_batal = 'Putusan dispute: uang kembali ke buyer' WHERE id_pesanan = ?");
                $stmt->execute([$orderId]);
                // Catat pembatalan di timeline pesanan (pencatat = admin session).
                $stmt = $pdo->prepare('INSERT INTO riwayat_status (id_pesanan, status_lama, status_baru, id_pengguna) VALUES (?, ?, ?, ?)');
                $stmt->execute([$orderId, $order['status'], 'dibatalkan', (int) $user['id']]);
                // Kembalikan stok (baris stok terbaru produk; lewati bila tidak ada baris stok).
                $stmt = $pdo->prepare('SELECT id_stok FROM stok WHERE id_produk = ? ORDER BY tanggal_stok DESC LIMIT 1 FOR UPDATE');
                $stmt->execute([(int) $order['id_produk']]);
                $stok = $stmt->fetch();
                if ($stok) {
                    $stmt = $pdo->prepare('UPDATE stok SET jumlah_stok = jumlah_stok + ? WHERE id_stok = ?');
                    $stmt->execute([(float) $order['jumlah'], (int) $stok['id_stok']]);
                }
                // Pembayaran yang belum dibayar jadi kedaluwarsa.
                $stmt = $pdo->prepare("UPDATE pembayaran SET status = 'expired' WHERE id_pesanan = ? AND status = 'pending'");
                $stmt->execute([$orderId]);
            }
            // Refund (pencairan tahap 3) bila ada pembayaran paid dan belum ada tahap 3.
            $stmt = $pdo->prepare("SELECT id_pembayaran FROM pembayaran WHERE id_pesanan = ? AND status = 'paid' LIMIT 1");
            $stmt->execute([$orderId]);
            $bayar = $stmt->fetch();
            if ($bayar) {
                $stmt = $pdo->prepare('SELECT id_pencairan FROM pencairan WHERE id_pesanan = ? AND tahap = 3 LIMIT 1');
                $stmt->execute([$orderId]);
                if (!$stmt->fetch()) {
                    $stmt = $pdo->prepare('SELECT id_pengguna FROM pengguna WHERE id_pembeli = ? LIMIT 1');
                    $stmt->execute([(int) $order['id_pembeli']]);
                    $akunBuyer = $stmt->fetch();
                    if ($akunBuyer) {
                        // Refund = sisa dana yang masih ditahan: total dikurangi
                        // DP tahap 1 DAN pelunasan tahap 2 yang sudah cair ke
                        // perusahaan (bila ada). Tanpa pengurangan tahap 2,
                        // refund order yang sudah selesai (tahap 2 sudah ada)
                        // melebihi dana yang ditahan.
                        $stmt = $pdo->prepare('SELECT COALESCE(SUM(nominal), 0) AS sudah_cair FROM pencairan WHERE id_pesanan = ? AND tahap IN (1, 2)');
                        $stmt->execute([$orderId]);
                        $cairRow = $stmt->fetch();
                        $total = round((float) $order['total_harga'] - (float) ($cairRow['sudah_cair'] ?? 0), 2);
                        $stmt = $pdo->prepare('SELECT nama_bank, no_rekening, atas_nama FROM pembeli WHERE id_pembeli = ? LIMIT 1');
                        $stmt->execute([(int) $order['id_pembeli']]);
                        $bankBuyer = $stmt->fetch();
                        $stmt = $pdo->prepare("INSERT INTO pencairan (id_pembayaran, id_pesanan, id_pengguna, penerima, external_id, tahap, nominal_kotor, potongan_fee, nominal, nama_bank, no_rekening, atas_nama, status) VALUES (?, ?, ?, 'pembeli', ?, 3, ?, 0, ?, ?, ?, ?, 'pending')");
                        $stmt->execute([(int) $bayar['id_pembayaran'], $orderId, (int) $akunBuyer['id_pengguna'], 'RFND-' . $orderId, $total, $total, $bankBuyer['nama_bank'] ?? null, $bankBuyer['no_rekening'] ?? null, $bankBuyer['atas_nama'] ?? null]);
                    }
                }
            }
        } elseif ($putusan === 'lanjut_perusahaan') {
            // Pelunasan (pencairan tahap 2) bila ada pembayaran paid dan belum ada tahap 2.
            $stmt = $pdo->prepare("SELECT id_pembayaran FROM pembayaran WHERE id_pesanan = ? AND status = 'paid' LIMIT 1");
            $stmt->execute([$orderId]);
            $bayar = $stmt->fetch();
            if ($bayar) {
                $stmt = $pdo->prepare('SELECT id_pencairan FROM pencairan WHERE id_pesanan = ? AND tahap = 2 LIMIT 1');
                $stmt->execute([$orderId]);
                if (!$stmt->fetch()) {
                    $stmt = $pdo->prepare('SELECT nominal FROM pencairan WHERE id_pesanan = ? AND tahap = 1 LIMIT 1');
                    $stmt->execute([$orderId]);
                    $tahap1 = $stmt->fetch();
                    $dp = $tahap1 ? (float) $tahap1['nominal'] : round((float) $order['total_harga'] * (float) $order['persen_dp'] / 100, 2);
                    $sisa = round((float) $order['total_harga'] - $dp, 2);
                    $fee = round((float) $order['fee_nominal'], 2);
                    $stmt = $pdo->prepare('SELECT id_pengguna FROM pengguna WHERE id_perusahaan = ? LIMIT 1');
                    $stmt->execute([(int) $order['id_perusahaan']]);
                    $akunPerusahaan = $stmt->fetch();
                    $stmt = $pdo->prepare('SELECT nama_bank, no_rekening, atas_nama FROM perusahaan WHERE id_perusahaan = ? LIMIT 1');
                    $stmt->execute([(int) $order['id_perusahaan']]);
                    $bank = $stmt->fetch();
                    if ($akunPerusahaan) {
                        $stmt = $pdo->prepare("INSERT INTO pencairan (id_pembayaran, id_pesanan, id_pengguna, penerima, external_id, tahap, nominal_kotor, potongan_fee, nominal, nama_bank, no_rekening, atas_nama, status) VALUES (?, ?, ?, 'perusahaan', ?, 2, ?, ?, ?, ?, ?, ?, 'pending')");
                        $stmt->execute([(int) $bayar['id_pembayaran'], $orderId, (int) $akunPerusahaan['id_pengguna'], 'CAIR-2-' . $orderId, $sisa, $fee, round($sisa - $fee, 2), $bank['nama_bank'] ?? null, $bank['no_rekening'] ?? null, $bank['atas_nama'] ?? null]);
                    }
                }
            }
        }
    }
    $pdo->commit();
} catch (Throwable $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    json_error('Database belum siap. Jalankan online_marketplace_v2.sql dulu.', 500);
}

json_ok(['dispute_id' => $id, 'status' => 'selesai', 'putusan' => $putusan]);
