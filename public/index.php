<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/../src/helpers.php';

$route = $_GET['route'] ?? 'dashboard';

$publicRoutes = ['login', 'install'];

if (!in_array($route, $publicRoutes, true) && !is_logged_in()) {
    redirect('login');
}

switch ($route) {
    case 'login':
        require __DIR__ . '/../src/pages/login.php';
        break;
    case 'logout':
        require __DIR__ . '/../src/pages/logout.php';
        break;
    case 'dashboard':
        require __DIR__ . '/../src/pages/dashboard.php';
        break;
    case 'books':
        file_not_found('Books module not implemented yet.');
        break;
    case 'students':
        file_not_found('Students module not implemented yet.');
        break;
    case 'loans':
        file_not_found('Loans module not implemented yet.');
        break;
    case 'install':
        require __DIR__ . '/../src/pages/install.php';
        break;
    default:
        file_not_found();
}
