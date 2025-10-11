<?php

declare(strict_types=1);

require __DIR__ . '/../app/config.php';
require __DIR__ . '/../app/helpers.php';
require __DIR__ . '/../app/models.php';

use function App\render;
use function App\h;
use function App\redirect;

$errors = [];
$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';
    if (trim($name) === '') { $errors[] = 'Name is required.'; }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors[] = 'Valid email is required.'; }
    if (strlen($password) < 6) { $errors[] = 'Password must be at least 6 characters.'; }
    if ($password !== $confirm) { $errors[] = 'Passwords do not match.'; }
    if (!$errors) {
        try {
            $userId = App\createUser($name, $email, $password);
            App\start_session();
            $_SESSION['user_id'] = $userId;
            $_SESSION['user_name'] = $name;
            redirect('/catalog.php');
        } catch (\Throwable $e) {
            $errors[] = $e->getMessage();
        }
    }
}

render('register', [
    'title' => 'Register',
    'errors' => $errors,
    'name' => $name,
    'email' => $email,
]);
