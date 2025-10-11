<?php
declare(strict_types=1);

function render(string $title, string $content): void {
    $pageTitle = $title;
    $pageContent = $content;
    require __DIR__ . '/../templates/layout.php';
}

function app_config(): array {
    static $config = null;
    if ($config !== null) {
        return $config;
    }
    $path = __DIR__ . '/../config/config.php';
    if (!file_exists($path)) {
        return [
            'db' => [
                'host' => '127.0.0.1',
                'port' => '3306',
                'name' => 'college_library',
                'user' => 'root',
                'pass' => '',
                'charset' => 'utf8mb4',
            ],
            'app' => [
                'fine_per_day' => 1.0,
                'default_loan_days' => 14,
            ],
        ];
    }
    /** @var array $loaded */
    $loaded = require $path;
    $config = $loaded;
    return $config;
}

function is_logged_in(): bool {
    return isset($_SESSION['user_id']);
}

function current_user_name(): string {
    return $_SESSION['user_name'] ?? 'Guest';
}

function redirect(string $route): void {
    $location = '/?route=' . urlencode($route);
    header('Location: ' . $location);
    exit;
}

function h(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function file_not_found(string $message = 'Page not found'): void {
    http_response_code(404);
    render('Not Found', '<div class="card"><p>' . h($message) . '</p></div>');
}
