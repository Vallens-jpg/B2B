<?php
// Perusahaan: diproses -> dikirim (order_id, resi boleh kosong). DP cair ke perusahaan.
// DP = total_harga * persen_dp / 100 (default skema persen_dp=0, fee dipotong di tahap 2 saja).
require_once __DIR__ . '/../../lib/db.php';
require_once __DIR__ . '/../../lib/response.php';
require_once __DIR__ . '/../../lib/auth.php';

$user = auth_user();
if (!$user || ($user['role'] ?? '') !== 'perusahaan') {
    json_error('Hanya perusahaan yang boleh mengirim order', 403);
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_error('Metode tidak diizinkan', 405);
}

$input = json_input();
$orderId = (int) ($input['order_id'] ?? 0);
$resi = trim($input['resi'] ?? '');
if ($orderId <= 0) {
    json_error('Order tidak ditemukan', 404);
}

// Bukti pengiriman berupa foto (wajib): pengiriman biasanya langsung oleh
// perusahaan tanpa pihak ke-3, jadi tidak ada resi. Foto diunggah sebagai
// multipart/form-data (json_input() sudah mengembalikan $_POST untuk itu).
if (empty($_FILES['foto']) || ($_FILES['foto']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
    json_error('Foto bukti pengiriman wajib diunggah', 400);
}
if (($_FILES['foto']['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
    json_error('Gagal mengunggah foto bukti pengiriman', 400);
}
$infoGambar = @getimagesize($_FILES['foto']['tmp_name']);
$tipeDiizinkan = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
if ($infoGambar === false || !isset($tipeDiizinkan[$infoGambar['mime']])) {
    json_error('Foto harus berformat JPG, PNG, atau WebP', 400);
}
if ((int) $_FILES['foto']['size'] > 5 * 1024 * 1024) {
    json_error('Ukuran foto maksimal 5 MB', 400);
}
$dirBukti = dirname(__DIR__, 2) . '/assets/img/bukti-kirim';
if (!is_dir($dirBukti) && !@mkdir($dirBukti, 0777, true)) {
    json_error('Folder bukti kirim tidak bisa dibuat di server', 500);
}
$namaFoto = 'bukti-' . $orderId . '-' . time() . '.' . $tipeDiizinkan[$infoGambar['mime']];
$pathFoto = $dirBukti . '/' . $namaFoto;
if (!@move_uploaded_file($_FILES['foto']['tmp_name'], $pathFoto)) {
    json_error('Foto tidak bisa disimpan di server (periksa izin tulis folder bukti-kirim)', 500);
}
$fotoRelatif = 'assets/img/bukti-kirim/' . $namaFoto;

try {
    $pdo = db();
    // Session menyimpan pengguna.id_pengguna, bukan id_perusahaan: resolve dulu.
    $stmt = $pdo->prepare('SELECT id_perusahaan FROM pengguna WHERE id_pengguna = ? LIMIT 1');
    $stmt->execute([(int) $user['id']]);
    $profil = $stmt->fetch();
    $idPerusahaan = ($profil && $profil['id_perusahaan'] !== null) ? (int) $profil['id_perusahaan'] : 0;

    $pdo->beginTransaction();
    $stmt = $pdo->prepare(
        'SELECT ps.id_pesanan, ps.id_perusahaan, ps.total_harga, ps.persen_dp, ps.status, ' .
        'pr.nama_bank, pr.no_rekening, pr.atas_nama ' .
        'FROM pesanan ps JOIN perusahaan pr ON pr.id_perusahaan = ps.id_perusahaan ' .
        'WHERE ps.id_pesanan = ? FOR UPDATE'
    );
    $stmt->execute([$orderId]);
    $order = $stmt->fetch();
    if (!$order) {
        $pdo->rollBack();
        json_error('Order tidak ditemukan', 404);
    }
    if ((int) $order['id_perusahaan'] !== $idPerusahaan) {
        $pdo->rollBack();
        json_error('Order ini bukan milik perusahaan Anda', 403);
    }
    if ($order['status'] !== 'diproses') {
        $pdo->rollBack();
        json_error('Order harus berstatus diproses dulu', 409);
    }
    $dp = round((float) $order['total_harga'] * (float) $order['persen_dp'] / 100, 2);
    $stmt = $pdo->prepare('INSERT INTO pengiriman (id_pesanan, tanggal_pengiriman, no_resi, foto_bukti, status) VALUES (?, CURDATE(), ?, ?, \'dikirim\')');
    $stmt->execute([$orderId, $resi !== '' ? $resi : null, $fotoRelatif]);
    $stmt = $pdo->prepare("UPDATE pesanan SET status = 'dikirim' WHERE id_pesanan = ?");
    $stmt->execute([$orderId]);
    // Pencairan tahap 1 (DP) bila ada baris pembayaran yang sudah paid dan tahap 1 belum ada.
    // Fee tidak dipotong di tahap 1 (potongan_fee=0, nominal=nominal_kotor=dp).
    $stmt = $pdo->prepare("SELECT id_pembayaran FROM pembayaran WHERE id_pesanan = ? AND status = 'paid' LIMIT 1");
    $stmt->execute([$orderId]);
    $pembayaran = $stmt->fetch();
    if ($pembayaran) {
        $stmt = $pdo->prepare('SELECT id_pencairan FROM pencairan WHERE id_pesanan = ? AND tahap = 1 LIMIT 1');
        $stmt->execute([$orderId]);
        if (!$stmt->fetch()) {
            $stmt = $pdo->prepare('SELECT id_pengguna FROM pengguna WHERE id_perusahaan = ? LIMIT 1');
            $stmt->execute([(int) $order['id_perusahaan']]);
            $penerima = $stmt->fetch();
            if ($penerima) {
                $stmt = $pdo->prepare(
                    'INSERT INTO pencairan (id_pembayaran, id_pesanan, id_pengguna, penerima, external_id, tahap, nominal_kotor, potongan_fee, nominal, nama_bank, no_rekening, atas_nama, status) ' .
                    "VALUES (?, ?, ?, 'perusahaan', ?, 1, ?, 0, ?, ?, ?, ?, 'pending')"
                );
                $stmt->execute([(int) $pembayaran['id_pembayaran'], $orderId, (int) $penerima['id_pengguna'], 'CAIR-1-' . $orderId, $dp, $dp, $order['nama_bank'], $order['no_rekening'], $order['atas_nama']]);
            }
        }
    }
    $stmt = $pdo->prepare('INSERT INTO riwayat_status (id_pesanan, status_lama, status_baru, id_pengguna) VALUES (?, ?, ?, ?)');
    $stmt->execute([$orderId, 'diproses', 'dikirim', (int) $user['id']]);
    $pdo->commit();
} catch (Throwable $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    // Foto sudah terlanjur tersimpan tapi pengiriman gagal dicatat: hapus
    // supaya tidak ada file yatim di server.
    if (isset($pathFoto) && file_exists($pathFoto)) {
        @unlink($pathFoto);
    }
    json_error('Database belum siap. Jalankan online_marketplace_v2.sql dulu.', 500);
}

json_ok(['order_id' => $orderId, 'status' => 'dikirim', 'dp_cair' => $dp, 'resi' => $resi !== '' ? $resi : null, 'foto' => $fotoRelatif]);
