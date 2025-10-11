<?php

declare(strict_types=1);

require __DIR__ . '/../app/config.php';
require __DIR__ . '/../app/helpers.php';

use function App\render;

render('home', [
    'title' => 'Library',
]);
