<?php

declare(strict_types=1);

require __DIR__ . '/../app/config.php';
require __DIR__ . '/../app/helpers.php';
require __DIR__ . '/../app/models.php';

use function App\render;
use function App\require_login;
use function App\redirect;

$errors = [];
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$book = $id ? App\getBook($id) : null;
if (!$book) {
    \App\redirect('/catalog.php');
}

$justBorrowed = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    \App\require_login();
    try {
        \App\borrowBook((int) $_SESSION['user_id'], (int) $book['id']);
        $book = App\getBook((int) $book['id']);
        $justBorrowed = true;
    } catch (\Throwable $e) {
        $errors[] = $e->getMessage();
    }
}

render('book', [
    'title' => $book['title'],
    'book' => $book,
    'errors' => $errors,
    'justBorrowed' => $justBorrowed,
]);
