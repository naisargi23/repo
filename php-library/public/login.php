<?php

declare(strict_types=1);

require __DIR__ . '/../app/config.php';
require __DIR__ . '/../app/helpers.php';
require __DIR__ . '/../app/models.php';

use function App\render;
use function App\redirect;

$errors = [];
$email = $_POST['email'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $user = App\authenticate($email, $password);
    if ($user) {
        App\start_session();
        $_SESSION['user_id'] = (int) $user['id'];
        $_SESSION['user_name'] = $user['name'];
        redirect('/catalog.php');
    } else {
        $errors[] = 'Invalid credentials';
    }
}

render('login', [
    'title' => 'Login',
    'errors' => $errors,
    'email' => $email,
]);
