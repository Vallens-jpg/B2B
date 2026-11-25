<?php
// 1. Tolak request yang bukan dari Xendit
$token    = $_SERVER['HTTP_X_CALLBACK_TOKEN'] ?? '';
$expected = (string) getenv('XENDIT_WEBHOOK_TOKEN');

if ($expected === '' || !hash_equals($expected, $token)) {
    http_response_code(403);
    exit('forbidden');
}

$raw  = file_get_contents('php://input');
$data = json_decode($raw, true);
error_log('XENDIT WEBHOOK: ' . $raw);

if (!is_array($data)) {
    http_response_code(400);
    exit('bad request');
}

if (($data['status'] ?? '') === 'PAID') {
    $orderId = $data['external_id'] ?? '';
    error_log("Order $orderId PAID");

    // Pembaruan status dilakukan oleh src/api/webhook/xendit.php (webhook utama).
    // Contoh untuk skema v2 bila file ini dipakai sendiri:
    // $stmt = $pdo->prepare(
    //     "UPDATE pesanan p JOIN pembayaran b ON b.id_pesanan = p.id_pesanan
    //      SET p.status = 'dibayar', b.status = 'paid', b.paid_at = NOW()
    //      WHERE b.xendit_id = ? AND p.status = 'menunggu_bayar'"
    // );
    // $stmt->execute([$orderId]);
}

http_response_code(200);
echo 'ok';