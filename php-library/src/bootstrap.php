<?php

declare(strict_types=1);

session_start();

// Configuration
$BASE_PATH = dirname(__DIR__);
$DB_PATH = $BASE_PATH . '/data/library.sqlite';

// Ensure data directory exists
if (!is_dir($BASE_PATH . '/data')) {
    mkdir($BASE_PATH . '/data', 0775, true);
}

// Database connection (SQLite)
function db(): PDO {
    static $pdo = null;
    global $DB_PATH;
    if ($pdo === null) {
        $pdo = new PDO('sqlite:' . $DB_PATH, null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
        $pdo->exec('PRAGMA foreign_keys = ON');
    }
    return $pdo;
}

// Simple view renderer
function render(string $view, array $params = []): void {
    extract($params);
    $baseTitle = 'Student Library';
    $viewPath = __DIR__ . '/../views/' . $view . '.php';
    if (!file_exists($viewPath)) {
        http_response_code(500);
        echo 'View not found';
        exit;
    }
    include __DIR__ . '/../views/partials/layout.php';
}

// CSRF helpers
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string {
    $token = htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8');
    return '<input type="hidden" name="_token" value="' . $token . '">';
}

function verify_csrf(): void {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $provided = $_POST['_token'] ?? '';
        if (!$provided || !hash_equals($_SESSION['csrf_token'] ?? '', $provided)) {
            http_response_code(419);
            echo 'CSRF token mismatch';
            exit;
        }
    }
}

// Flash messages
function flash(string $key, ?string $message = null): ?string {
    if ($message === null) {
        $val = $_SESSION['flash'][$key] ?? null;
        unset($_SESSION['flash'][$key]);
        return $val;
    }
    $_SESSION['flash'][$key] = $message;
    return null;
}

// Auth helpers
function auth_user(): ?array {
    if (!empty($_SESSION['user_id'])) {
        $stmt = db()->prepare('SELECT id, name, email FROM users WHERE id = ?');
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();
        return $user ?: null;
    }
    return null;
}

function require_guest(): void {
    if (auth_user()) {
        header('Location: /');
        exit;
    }
}

function require_auth(): void {
    if (!auth_user()) {
        header('Location: /login');
        exit;
    }
}

// Utilities
function e(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
