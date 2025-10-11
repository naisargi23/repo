<?php
declare(strict_types=1);

return [
    'db' => [
        'host' => getenv('DB_HOST') ?: '127.0.0.1',
        'port' => getenv('DB_PORT') ?: '3306',
        'name' => getenv('DB_NAME') ?: 'college_library',
        'user' => getenv('DB_USER') ?: 'root',
        'pass' => getenv('DB_PASS') ?: '',
        'charset' => getenv('DB_CHARSET') ?: 'utf8mb4',
    ],
    'app' => [
        'fine_per_day' => (float) (getenv('FINE_PER_DAY') ?: 1.0),
        'default_loan_days' => (int) (getenv('DEFAULT_LOAN_DAYS') ?: 14),
    ],
];
