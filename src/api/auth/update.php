<?php
// Ubah profil user yang sedang login (nama, email). POST saja.
// Dipakai halaman profil.php. Email harus tetap unik antar akun.
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
$nama = trim((string) ($input['nama'] ?? ''));
$email = trim((string) ($input['email'] ?? ''));
if ($nama === '' || $email === '') {
    json_error('Nama dan email wajib diisi', 422);
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    json_error('Format email tidak valid', 422);
}

try {
    $pdo = db();
    $stmt = $pdo->prepare('UPDATE pengguna SET nama_pengguna = ?, email = ? WHERE id_pengguna = ?');
    $stmt->execute([$nama, $email, (int) $user['id']]);
    // Sinkron nama (dan data profil lain bila dikirim) ke tabel profil
    // sesuai role, supaya nama di katalog/invoice ikut berubah.
    // Nilai kosong berarti "tidak diubah", bukan "dikosongkan".
    $stmt = $pdo->prepare('SELECT id_perusahaan, id_pembeli, id_admin, role FROM pengguna WHERE id_pengguna = ?');
    $stmt->execute([(int) $user['id']]);
    $profil = $stmt->fetch();
    $role = $profil['role'] ?? ($user['role'] ?? '');
    $namaBank = trim((string) ($input['nama_bank'] ?? ''));
    $noRek = trim((string) ($input['no_rekening'] ?? ''));
    $atasNama = trim((string) ($input['atas_nama'] ?? ''));
    $telepon = trim((string) ($input['telepon'] ?? ''));
    $kota = trim((string) ($input['kota'] ?? ''));
    $alamat = trim((string) ($input['alamat'] ?? ''));
    if ($role === 'pembeli' && !empty($profil['id_pembeli'])) {
        $stmt = $pdo->prepare(
            'UPDATE pembeli SET nama_pembeli = ?,'
            . ' nama_bank = COALESCE(NULLIF(?, \'\'), nama_bank),'
            . ' no_rekening = COALESCE(NULLIF(?, \'\'), no_rekening),'
            . ' atas_nama = COALESCE(NULLIF(?, \'\'), atas_nama),'
            . ' telepon = COALESCE(NULLIF(?, \'\'), telepon),'
            . ' kota = COALESCE(NULLIF(?, \'\'), kota),'
            . ' alamat = COALESCE(NULLIF(?, \'\'), alamat)'
            . ' WHERE id_pembeli = ?'
        );
        $stmt->execute([$nama, $namaBank, $noRek, $atasNama, $telepon, $kota, $alamat, (int) $profil['id_pembeli']]);
    } elseif ($role === 'perusahaan' && !empty($profil['id_perusahaan'])) {
        $stmt = $pdo->prepare(
            'UPDATE perusahaan SET nama_perusahaan = ?,'
            . ' nama_bank = COALESCE(NULLIF(?, \'\'), nama_bank),'
            . ' no_rekening = COALESCE(NULLIF(?, \'\'), no_rekening),'
            . ' atas_nama = COALESCE(NULLIF(?, \'\'), atas_nama),'
            . ' telepon = COALESCE(NULLIF(?, \'\'), telepon),'
            . ' kota = COALESCE(NULLIF(?, \'\'), kota),'
            . ' alamat = COALESCE(NULLIF(?, \'\'), alamat)'
            . ' WHERE id_perusahaan = ?'
        );
        $stmt->execute([$nama, $namaBank, $noRek, $atasNama, $telepon, $kota, $alamat, (int) $profil['id_perusahaan']]);
    } elseif ($role === 'admin' && !empty($profil['id_admin'])) {
        $stmt = $pdo->prepare('UPDATE admin SET nama = ? WHERE id_admin = ?');
        $stmt->execute([$nama, (int) $profil['id_admin']]);
    }
    // Perbarui session supaya nama baru langsung tampil di semua halaman.
    $_SESSION['user']['nama'] = $nama;
    $_SESSION['user']['email'] = $email;
} catch (Throwable $e) {
    if ((string) ($e->getCode() ?? '') === '23000') {
        json_error('Email sudah dipakai akun lain', 409);
    }
    json_error('Database belum siap. Jalankan online_marketplace_v2.sql dulu.', 500);
}

json_ok([
    'id' => (int) $user['id'],
    'nama' => $nama,
    'email' => $email,
    'role' => $user['role'] ?? '',
    'nama_bank' => $namaBank ?? '',
    'no_rekening' => $noRek ?? '',
    'atas_nama' => $atasNama ?? '',
    'telepon' => $telepon ?? '',
    'kota' => $kota ?? '',
    'alamat' => $alamat ?? '',
]);
