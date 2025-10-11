<?php

declare(strict_types=1);

require __DIR__ . '/../app/config.php';
require __DIR__ . '/../app/helpers.php';
require __DIR__ . '/../app/models.php';

use function App\render;
use function App\h;

$q = $_GET['q'] ?? '';
$books = App\listBooks($q);

render('catalog', [
    'title' => 'Catalog',
    'q' => $q,
    'books' => $books,
]);
