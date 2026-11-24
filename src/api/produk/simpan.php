<?php
// Simpan produk (nama, harga, stok, satuan kg, deskripsi). Harga & stok dicek di SERVER, bukan cuma di form.
// Skema v2: produk tidak punya kolom stok (stok = baris terbaru di tabel stok);
// id_kategori wajib (default 1 = 'Telur' bila tidak dikirim).
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
$kategoriId = (int) ($input['id_kategori'] ?? $input['kategori_id'] ?? 1);

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
    // Resolve id profil perusahaan dari session (id_pengguna != id_perusahaan).
    $stmt = $pdo->prepare('SELECT id_perusahaan FROM pengguna WHERE id_pengguna = ? LIMIT 1');
    $stmt->execute([(int) $user['id']]);
    $profil = $stmt->fetch();
    $idPerusahaan = $profil && $profil['id_perusahaan'] !== null ? (int) $profil['id_perusahaan'] : 0;
    if ($idPerusahaan <= 0) {
        json_error('Akun Anda belum terhubung ke profil perusahaan', 403);
    }
    // Kategori harus ada (id_kategori NOT NULL + FK di skema v2).
    $stmt = $pdo->prepare('SELECT id_kategori FROM kategori_produk WHERE id_kategori = ? LIMIT 1');
    $stmt->execute([$kategoriId]);
    if (!$stmt->fetch()) {
        json_error('Kategori produk tidak ditemukan', 400);
    }
    $pdo->beginTransaction();
    if ($id > 0) {
        $stmt = $pdo->prepare('SELECT id_produk, id_perusahaan FROM produk WHERE id_produk = ? LIMIT 1');
        $stmt->execute([$id]);
        $lama = $stmt->fetch();
        if (!$lama) {
            $pdo->rollBack();
            json_error('Produk tidak ditemukan', 404);
        }
        if ((int) $lama['id_perusahaan'] !== $idPerusahaan) {
            $pdo->rollBack();
            json_error('Produk ini bukan milik perusahaan Anda', 403);
        }
        $stmt = $pdo->prepare('UPDATE produk SET id_kategori = ?, nama_produk = ?, deskripsi = ?, harga_standar = ?, satuan = ?, gambar = ? WHERE id_produk = ?');
        $stmt->execute([$kategoriId, $nama, $deskripsi, (float) $harga, $satuan, $gambar !== '' ? $gambar : null, $id]);
        $produkId = $id;
    } else {
        $stmt = $pdo->prepare('INSERT INTO produk (id_perusahaan, id_kategori, nama_produk, deskripsi, harga_standar, satuan, gambar) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$idPerusahaan, $kategoriId, $nama, $deskripsi, (float) $harga, $satuan, $gambar !== '' ? $gambar : null]);
        $produkId = (int) $pdo->lastInsertId();
    }
    // Stok v2 = baris terbaru di tabel stok; simpan stok hari ini (upsert per hari).
    $stmt = $pdo->prepare('INSERT INTO stok (id_produk, tanggal_stok, jumlah_stok) VALUES (?, CURDATE(), ?) ON DUPLICATE KEY UPDATE jumlah_stok = VALUES(jumlah_stok)');
    $stmt->execute([$produkId, (float) $stok]);
    $pdo->commit();
} catch (Throwable $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    // Skema v2 tidak punya UNIQUE pada nama_produk; 23000 di sini hanya bisa
    // dari pelanggaran FK yang lolos validasi di atas.
    $kode = ($e instanceof PDOException) ? (string) $e->getCode() : '';
    if ($kode === '23000') {
        json_error('Data produk tidak valid.', 409);
    }
    json_error('Gagal menyimpan produk: ' . $e->getMessage(), 500);
}

json_ok(['produk_id' => $produkId, 'nama' => $nama]);
