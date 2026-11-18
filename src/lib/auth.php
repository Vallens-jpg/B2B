<?php
// Cek login & peran (pembeli/perusahaan/admin) dari session.

function auth_user(): ?array
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return $_SESSION['user'] ?? null;
}

function require_login(): array
{
    $user = auth_user();
    if (!$user) {
        header('Location: /pages/login.php');
        exit;
    }
    return $user;
}

function require_role(string ...$roles): array
{
    $user = require_login();
    if (!in_array($user['role'] ?? '', $roles, true)) {
        http_response_code(403);
        exit('Akses ditolak untuk peran ini.');
    }
    return $user;
}
