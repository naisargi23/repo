<?php

declare(strict_types=1);

require __DIR__ . '/../app/config.php';
require __DIR__ . '/../app/helpers.php';
require __DIR__ . '/../app/models.php';

\App\require_login();

$errors = [];
$messages = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['return_id'])) {
    try {
        \App\returnLoan((int) $_SESSION['user_id'], (int) $_POST['return_id']);
        $messages[] = 'Book returned.';
    } catch (\Throwable $e) {
        $errors[] = $e->getMessage();
    }
}

$loans = \App\getLoansForUser((int) $_SESSION['user_id']);

\App\render('loans', [
    'title' => 'My Loans',
    'errors' => $errors,
    'messages' => $messages,
    'loans' => $loans,
]);
