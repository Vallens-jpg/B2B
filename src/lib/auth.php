<?php
// Cek login & peran (pembeli/perusahaan/admin) dari session.

function auth_user(): ?array
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $user = $_SESSION['user'] ?? null;
    if (!$user || empty($user['id'])) {
        return null;
    }
    // Cek ulang status ke database: akun yang dikunci admin (status nonaktif,
    // tabel pengguna skema v2) tidak boleh lagi memakai session lama. Bila database belum
    // siap atau kolom status belum ada, pakai session apa adanya (perilaku lama).
    try {
        require_once __DIR__ . '/db.php';
        $stmt = db()->prepare('SELECT status FROM pengguna WHERE id_pengguna = ? LIMIT 1');
        $stmt->execute([(int) $user['id']]);
        $row = $stmt->fetch();
        if (!$row || ($row['status'] ?? '') === 'nonaktif') {
            unset($_SESSION['user']);
            return null;
        }
    } catch (Throwable $e) {
        // Abaikan: session tetap dipakai.
    }
    return $user;
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
