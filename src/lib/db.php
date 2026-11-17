<?php
// Koneksi ke database MySQL (dipakai semua endpoint).
// Host di Docker namanya "db" (lihat docker-compose.yml).
// Di luar Docker, .env di root proyek dibaca sebagai fallback.

function db(): PDO
{
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }

    $envFile = dirname(__DIR__, 2) . '/.env';
    if (is_file($envFile)) {
        foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }
            [$k, $v] = array_pad(explode('=', $line, 2), 2, '');
            $k = trim($k);
            $v = trim($v);
            if ($k !== '' && getenv($k) === false) {
                putenv("$k=$v");
            }
        }
    }

    $host = getenv('DB_HOST') ?: 'db';
    $port = getenv('DB_PORT') ?: '3306';
    $name = getenv('DB_NAME') ?: 'app_db';
    $user = getenv('DB_USER') ?: 'app_user';
    $pass = getenv('DB_PASSWORD') ?: 'app_pass';

    $dsn = "mysql:host=$host;port=$port;dbname=$name;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    return $pdo;
}
