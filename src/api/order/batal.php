<?php
// Batalkan pesanan (order_id, alasan). Kalau sudah dibayar, dana kembali ke buyer.
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

$input = json_input();
$orderId = (int) ($input['order_id'] ?? 0);
$alasan = trim($input['alasan'] ?? '');
if ($orderId <= 0) {
    json_error('Order tidak ditemukan', 404);
}

try {
    $pdo = db();
    $pdo->beginTransaction();
    // $user['id'] adalah pengguna.id_pengguna, bukan id profil: resolve dulu
    // ke id_pembeli sebelum membandingkan dengan pesanan.id_pembeli.
    $stmt = $pdo->prepare('SELECT id_pembeli FROM pengguna WHERE id_pengguna = ? LIMIT 1');
    $stmt->execute([(int) $user['id']]);
    $profil = $stmt->fetch();
    $idPembeli = $profil['id_pembeli'] !== null ? (int) $profil['id_pembeli'] : null;

    $stmt = $pdo->prepare('SELECT ps.*, pn.id_produk FROM pesanan ps JOIN penawaran pn ON pn.id_penawaran = ps.id_penawaran WHERE ps.id_pesanan = ? FOR UPDATE');
    $stmt->execute([$orderId]);
    $order = $stmt->fetch();
    if (!$order) {
        $pdo->rollBack();
        json_error('Order tidak ditemukan', 404);
    }
    $isBuyer = $idPembeli !== null && (int) $order['id_pembeli'] === $idPembeli;
    $isAdmin = ($user['role'] ?? '') === 'admin';
    if (!$isBuyer && !$isAdmin) {
        $pdo->rollBack();
        json_error('Order ini bukan milik Anda', 403);
    }
    if (!in_array($order['status'], ['menunggu_bayar', 'dibayar'], true)) {
        $pdo->rollBack();
        json_error('Order ini sudah tidak bisa dibatalkan lagi', 409);
    }
    $sudahDibayar = $order['status'] === 'dibayar';
    $stmt = $pdo->prepare("UPDATE pesanan SET status = 'dibatalkan', alasan_batal = ? WHERE id_pesanan = ?");
    $stmt->execute([$alasan !== '' ? $alasan : null, $orderId]);
    // Kembalikan stok: tambah ke baris stok terbaru produk. Tidak ada lagi
    // kolom stok di produk; bila tidak ada baris stok, lewati saja.
    $stmt = $pdo->prepare('SELECT id_stok FROM stok WHERE id_produk = ? ORDER BY tanggal_stok DESC LIMIT 1 FOR UPDATE');
    $stmt->execute([(int) $order['id_produk']]);
    $stok = $stmt->fetch();
    if ($stok) {
        $stmt = $pdo->prepare('UPDATE stok SET jumlah_stok = jumlah_stok + ? WHERE id_stok = ?');
        $stmt->execute([(float) $order['jumlah'], (int) $stok['id_stok']]);
    }
    if (!$sudahDibayar) {
        // Belum dibayar: invoice jadi kedaluwarsa (tidak ada nilai 'dibatalkan'
        // di pembayaran.status v2).
        $stmt = $pdo->prepare("UPDATE pembayaran SET status = 'expired' WHERE id_pesanan = ? AND status = 'pending'");
        $stmt->execute([$orderId]);
    } else {
        // Sudah dibayar: refund dicatat sebagai pencairan tahap 3, bila ada
        // baris pembayaran paid dan belum ada pencairan tahap 3.
        $stmt = $pdo->prepare("SELECT id_pembayaran FROM pembayaran WHERE id_pesanan = ? AND status = 'paid' LIMIT 1");
        $stmt->execute([$orderId]);
        $pembayaran = $stmt->fetch();
        $stmt = $pdo->prepare('SELECT id_pencairan FROM pencairan WHERE id_pesanan = ? AND tahap = 3 LIMIT 1');
        $stmt->execute([$orderId]);
        $refundAda = $stmt->fetch();
        if ($pembayaran && !$refundAda) {
            // id_pengguna penerima = akun pengguna milik buyer pesanan ini
            // (bisa dibatalkan admin, jadi jangan pakai session).
            $stmt = $pdo->prepare('SELECT id_pengguna FROM pengguna WHERE id_pembeli = ? LIMIT 1');
            $stmt->execute([(int) $order['id_pembeli']]);
            $penggunaBuyer = $stmt->fetch();
            if ($penggunaBuyer) {
                $total = round((float) $order['total_harga'], 2);
                $stmt = $pdo->prepare("INSERT INTO pencairan (id_pembayaran, id_pesanan, id_pengguna, penerima, external_id, tahap, nominal_kotor, potongan_fee, nominal, status) VALUES (?, ?, ?, 'pembeli', ?, 3, ?, 0, ?, 'pending')");
                $stmt->execute([(int) $pembayaran['id_pembayaran'], $orderId, (int) $penggunaBuyer['id_pengguna'], 'RFND-' . $orderId, $total, $total]);
            }
        }
    }
    $stmt = $pdo->prepare('INSERT INTO riwayat_status (id_pesanan, status_lama, status_baru, id_pengguna) VALUES (?, ?, ?, ?)');
    $stmt->execute([$orderId, $order['status'], 'dibatalkan', (int) $user['id']]);
    $pdo->commit();
} catch (Throwable $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    json_error('Database belum siap. Jalankan online_marketplace_v2.sql dulu.', 500);
}

json_ok(['order_id' => $orderId, 'status' => 'dibatalkan', 'dana_kembali' => $sudahDibayar]);
