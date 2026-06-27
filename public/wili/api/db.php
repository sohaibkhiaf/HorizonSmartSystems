<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

function pdo_server(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) return $pdo;
    $dsn = 'mysql:host=' . WILI_DB_HOST . ';port=' . WILI_DB_PORT . ';charset=utf8mb4';
    $pdo = new PDO($dsn, WILI_DB_USER, WILI_DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    return $pdo;
}

function pdo_db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) return $pdo;
    $dsn = 'mysql:host=' . WILI_DB_HOST . ';port=' . WILI_DB_PORT . ';dbname=' . WILI_DB_NAME . ';charset=utf8mb4';
    $pdo = new PDO($dsn, WILI_DB_USER, WILI_DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    return $pdo;
}

function db_all(string $sql, array $params = []): array
{
    $stmt = pdo_db()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function db_one(string $sql, array $params = []): ?array
{
    $stmt = pdo_db()->prepare($sql);
    $stmt->execute($params);
    $row = $stmt->fetch();
    return is_array($row) ? $row : null;
}

function db_exec(string $sql, array $params = []): int
{
    $stmt = pdo_db()->prepare($sql);
    $stmt->execute($params);
    return $stmt->rowCount();
}

function db_insert(string $sql, array $params = []): int
{
    $stmt = pdo_db()->prepare($sql);
    $stmt->execute($params);
    return (int) pdo_db()->lastInsertId();
}

