<?php
// Format jawaban standar: sukses {"ok": true, "data": ...}, gagal {"ok": false, "error": "..."}.

function json_ok($data = null): void
{
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store, no-cache, must-revalidate');
    echo json_encode(['ok' => true, 'data' => $data], JSON_UNESCAPED_UNICODE);
    exit;
}

function json_error(string $error, int $code = 400): void
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store, no-cache, must-revalidate');
    echo json_encode(['ok' => false, 'error' => $error], JSON_UNESCAPED_UNICODE);
    exit;
}

function json_input(): array
{
    $raw = file_get_contents('php://input');
    $data = $raw ? json_decode($raw, true) : null;
    if (is_array($data)) {
        return $data;
    }
    return $_POST;
}
