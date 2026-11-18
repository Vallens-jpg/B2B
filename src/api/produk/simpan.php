<?php
// Simpan produk (nama, harga, stok, satuan kg, deskripsi). Harga & stok dicek di SERVER, bukan cuma di form.
require_once __DIR__ . '/../../lib/db.php';
require_once __DIR__ . '/../../lib/response.php';
require_once __DIR__ . '/../../lib/auth.php';

$user = auth_user();
if (!$user || ($user['role'] ?? '') !== 'perusahaan') {
    json_error('Hanya perusahaan yang boleh menyimpan produk', 403);
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_error('Metode tidak diizinkan', 405);
}

$input = json_input();
$id = (int) ($input['id'] ?? $input['produk_id'] ?? 0);
$nama = trim($input['nama'] ?? '');
$deskripsi = trim($input['deskripsi'] ?? '');
$harga = $input['harga_per_kg'] ?? $input['harga'] ?? null;
$stok = $input['stok_kg'] ?? $input['stok'] ?? null;
$satuan = trim($input['satuan'] ?? 'kg');
$gambar = trim($input['gambar'] ?? '');

if ($nama === '') {
    json_error('Nama produk wajib diisi', 400);
}
if (!is_numeric($harga) || (float) $harga <= 0) {
    json_error('Harga per kg harus lebih dari 0', 400);
}
if (!is_numeric($stok) || (float) $stok < 0) {
    json_error('Stok tidak boleh negatif', 400);
}
if ($satuan === '') {
    $satuan = 'kg';
}

try {
    $pdo = db();
    if ($id > 0) {
        $stmt = $pdo->prepare('SELECT id, perusahaan_id FROM produk WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $lama = $stmt->fetch();
        if (!$lama) {
            json_error('Produk tidak ditemukan', 404);
        }
        if (!empty($lama['perusahaan_id']) && (int) $lama['perusahaan_id'] !== (int) $user['id']) {
            json_error('Produk ini bukan milik perusahaan Anda', 403);
        }
        $stmt = $pdo->prepare('UPDATE produk SET nama = ?, deskripsi = ?, harga_per_kg = ?, stok_kg = ?, satuan = ?, gambar = ? WHERE id = ?');
        $stmt->execute([$nama, $deskripsi, (float) $harga, (float) $stok, $satuan, $gambar !== '' ? $gambar : null, $id]);
        $produkId = $id;
    } else {
        $stmt = $pdo->prepare('INSERT INTO produk (perusahaan_id, nama, deskripsi, harga_per_kg, stok_kg, satuan, gambar) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([(int) $user['id'], $nama, $deskripsi, (float) $harga, (float) $stok, $satuan, $gambar !== '' ? $gambar : null]);
        $produkId = (int) $pdo->lastInsertId();
    }
} catch (Throwable $e) {
    // Nama produk unik di database: bila nama sudah dipakai produk lain,
    // beri tahu dengan jelas (409), jangan tutupi dengan pesan "database
    // belum siap" — database sudah siap, yang gagal adalah simpanannya.
    $kode = ($e instanceof PDOException) ? (string) $e->getCode() : '';
    if ($kode === '23000') {
        json_error('Nama produk sudah dipakai. Produk dengan nama ini sudah ada — edit produk itu, jangan tambah baru.', 409);
    }
    json_error('Gagal menyimpan produk: ' . $e->getMessage(), 500);
}

json_ok(['produk_id' => $produkId, 'nama' => $nama]);
