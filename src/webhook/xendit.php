<?php
// 1. Tolak request yang bukan dari Xendit
$token    = $_SERVER['HTTP_X_CALLBACK_TOKEN'] ?? '';
$expected = (string) getenv('XENDIT_CALLBACK_TOKEN');

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

    // pembaruan status (belum diaktifkan, menunggu skema database):
    // $stmt = $pdo->prepare(
    //     "UPDATE orders SET status = 'DANA_DITAHAN'
    //      WHERE order_code = ? AND status = 'MENUNGGU_BAYAR'"
    // );
    // $stmt->execute([$orderId]);
}

http_response_code(200);
echo 'ok';