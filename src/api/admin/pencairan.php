<?php
// Admin saja: catat pencairan dana ke perusahaan.
// Transfer uangnya dilakukan admin secara manual di luar sistem (transfer
// bank); di sini hanya DICATAT: baris pencairan tahap 1 (DP) / tahap 2
// (pelunasan) yang masih 'pending' diubah jadi 'berhasil' + tanggal_cair.
// Ini BUKAN Payout API: sistem tidak mengirim uang sendiri (butuh akun
// bisnis Xendit terverifikasi + integrasi disbursement; pengembangan
// selanjutnya). Referensi transfer manual disimpan di xendit_payout_id
// (kolom dipakai ulang karena belum ada integrasi Xendit Payout).
require_once __DIR__ . '/../../lib/db.php';
require_once __DIR__ . '/../../lib/response.php';
require_once __DIR__ . '/../../lib/auth.php';

$user = auth_user();
if (!$user || ($user['role'] ?? '') !== 'admin') {
    json_error('Hanya admin yang boleh mencatat pencairan', 403);
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_error('Metode tidak diizinkan', 405);
}

$input = json_input();
$ids = $input['ids'] ?? [];
$referensi = trim($input['referensi'] ?? '');
if (!is_array($ids) || !$ids) {
    json_error('Pilih minimal satu baris pencairan', 400);
}
$ids = array_values(array_unique(array_map('intval', $ids)));
if (count($ids) > 100) {
    json_error('Terlalu banyak baris sekaligus', 400);
}

try {
    $pdo = db();
    $pdo->beginTransaction();
    // Kunci barisnya; hanya tahap 1/2 milik perusahaan yang masih pending.
    // Baris tahap 3 (refund ke pembeli) tidak bisa dicatat sebagai pencairan.
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare(
        "SELECT id_pencairan, id_pesanan, tahap, nominal FROM pencairan"
        . " WHERE id_pencairan IN ($placeholders) AND tahap IN (1, 2)"
        . " AND penerima = 'perusahaan' AND status = 'pending' FOR UPDATE"
    );
    $stmt->execute($ids);
    $rows = $stmt->fetchAll();
    if (count($rows) !== count($ids)) {
        $pdo->rollBack();
        json_error('Ada baris yang sudah dicatat atau bukan pencairan perusahaan', 409);
    }
    $stmt = $pdo->prepare(
        "UPDATE pencairan SET status = 'berhasil', tanggal_cair = NOW(), xendit_payout_id = ?"
        . " WHERE id_pencairan IN ($placeholders)"
    );
    $stmt->execute(array_merge([$referensi !== '' ? $referensi : null], $ids));
    $pdo->commit();
    $total = 0.0;
    foreach ($rows as $r) {
        $total += (float) $r['nominal'];
    }
    json_ok(['dicatat' => count($rows), 'total_nominal' => $total, 'rows' => $rows]);
} catch (Throwable $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    json_error('Database belum siap. Jalankan online_marketplace_v2.sql dulu.', 500);
}
