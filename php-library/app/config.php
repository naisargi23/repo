<?php

declare(strict_types=1);

namespace App;

const DB_PATH = __DIR__ . '/../data/library.sqlite';

function db(): \PDO {
    static $pdo = null;
    if ($pdo instanceof \PDO) {
        return $pdo;
    }
    $dsn = 'sqlite:' . DB_PATH;
    $pdo = new \PDO($dsn);
    $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    $pdo->exec('PRAGMA foreign_keys = ON');
    return $pdo;
}

function start_session(): void {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start([
            'cookie_httponly' => true,
            'cookie_samesite' => 'Lax',
        ]);
    }
}

function is_logged_in(): bool {
    start_session();
    return isset($_SESSION['user_id']);
}

function require_login(): void {
    if (!is_logged_in()) {
        header('Location: /login.php');
        exit;
    }
}
