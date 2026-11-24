<?php
// webhook/xendit.php
require_once __DIR__ . '/../config/database.php';

$tokenSah   = getenv('XENDIT_CALLBACK_TOKEN');
$headers    = getallheaders();
$tokenMasuk = $headers['x-callback-token'] ?? '';

// 1. Tolak jika token tidak cocok
if (!hash_equals($tokenSah, $tokenMasuk)) {
    http_response_code(401);
    exit;
}

// 2. Baca data webhook
$data = json_decode(file_get_contents('php://input'), true);

// 3. Proses hanya bila tagihan berstatus PAID
if (($data['status'] ?? '') === 'PAID') {
    $stmt = $pdo->prepare(
        "UPDATE orders SET status = 'dibayar'
         WHERE external_id = ? AND status = 'menunggu_pembayaran'"
    );
    $stmt->execute([$data['external_id']]);
}

http_response_code(200);