<?php

declare(strict_types=1);

require __DIR__ . '/../app/config.php';
require __DIR__ . '/../app/helpers.php';

\App\start_session();
$_SESSION = [];
session_destroy();
\App\redirect('/index.php');
