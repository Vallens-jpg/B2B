<?php
$secret = getenv('XENDIT_SECRET_KEY');
if (!$secret) {
    exit('XENDIT_SECRET_KEY belum terbaca. Cek .env dan docker-compose.');
}

$orderId = 'TEST-' . time();

$ch = curl_init('https://api.xendit.co/v2/invoices');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_USERPWD        => $secret . ':',
    CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => json_encode([
        'external_id' => $orderId,
        'amount'      => 50000,
        'payer_email' => 'pembeli@gmail.com',
        'description' => "Tes pembayaran $orderId",
    ]),
]);
$response = curl_exec($ch);
$status   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$res = json_decode($response, true);

if ($status === 200 && !empty($res['invoice_url'])) {
    echo "<p>Order: $orderId</p>";
    echo "<a href='{$res['invoice_url']}' target='_blank'>Bayar sekarang</a>";
} else {
    echo "<h3>Gagal (HTTP $status)</h3><pre>" . htmlspecialchars($response) . "</pre>";
}