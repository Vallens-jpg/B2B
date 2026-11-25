<?php
// Profil user yang sedang login (dari session). GET saja.
// Jawab 401 bila belum login. Dipakai halaman profil.php.
// Pola sama seperti endpoint lain: auth_user() dari lib/auth.php + json_ok/json_error.
// Selain nama/email, ikut mengembalikan data profil (telepon, kota, alamat,
// bank, nomor rekening, atas nama) bila role-nya pembeli/perusahaan.
require_once __DIR__ . '/../../lib/db.php';
require_once __DIR__ . '/../../lib/response.php';
require_once __DIR__ . '/../../lib/auth.php';

$user = auth_user();
if (!$user) {
    json_error('Silakan login dulu', 401);
}

// Data profil tambahan (opsional; bila gagal, tetap jawab nama/email saja).
$profil = [];
try {
    $pdo = db();
    $stmt = $pdo->prepare('SELECT id_perusahaan, id_pembeli, id_admin, role FROM pengguna WHERE id_pengguna = ?');
    $stmt->execute([(int) ($user['id'] ?? 0)]);
    $baris = $stmt->fetch();
    if ($baris && ($baris['role'] ?? '') === 'perusahaan' && !empty($baris['id_perusahaan'])) {
        $stmt = $pdo->prepare('SELECT nama_bank, no_rekening, atas_nama, telepon, kota, alamat, nib, npwp FROM perusahaan WHERE id_perusahaan = ? LIMIT 1');
        $stmt->execute([(int) $baris['id_perusahaan']]);
        $profil = $stmt->fetch() ?: [];
    } elseif ($baris && ($baris['role'] ?? '') === 'pembeli' && !empty($baris['id_pembeli'])) {
        $stmt = $pdo->prepare('SELECT nama_bank, no_rekening, atas_nama, telepon, kota, alamat FROM pembeli WHERE id_pembeli = ? LIMIT 1');
        $stmt->execute([(int) $baris['id_pembeli']]);
        $profil = $stmt->fetch() ?: [];
    }
} catch (Throwable $e) {
    $profil = [];
}

json_ok([
    'id' => (int) ($user['id'] ?? 0),
    'nama' => $user['nama'] ?? '',
    'email' => $user['email'] ?? '',
    'role' => $user['role'] ?? '',
    'nama_bank' => $profil['nama_bank'] ?? '',
    'no_rekening' => $profil['no_rekening'] ?? '',
    'atas_nama' => $profil['atas_nama'] ?? '',
    'telepon' => $profil['telepon'] ?? '',
    'kota' => $profil['kota'] ?? '',
    'alamat' => $profil['alamat'] ?? '',
    'nib' => $profil['nib'] ?? '',
    'npwp' => $profil['npwp'] ?? '',
]);
