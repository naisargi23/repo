<?php

declare(strict_types=1);

namespace App;

function render(string $view, array $params = []): void {
    extract($params, EXTR_SKIP);
    include __DIR__ . '/views/layout_header.php';
    include __DIR__ . "/views/{$view}.php";
    include __DIR__ . '/views/layout_footer.php';
}

function redirect(string $path): void {
    header('Location: ' . $path);
    exit;
}

function h(?string $value): string {
    return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
