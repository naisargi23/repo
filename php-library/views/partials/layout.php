<?php
/** @var array|null $user */
/** @var string $baseTitle */
/** @var string $viewPath */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($baseTitle) ?></title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>
<header class="nav">
    <div class="container nav-inner">
        <a class="brand" href="/">Student Library</a>
        <form class="search" action="/" method="get">
            <input type="text" name="q" placeholder="Search by title, author, ISBN" value="<?= e($query ?? '') ?>" />
            <button type="submit">Search</button>
        </form>
        <nav>
            <?php if ($user = auth_user()): ?>
                <span>Hello, <?= e($user['name']) ?></span>
                <a href="/dashboard">Dashboard</a>
                <form action="/logout" method="post" style="display:inline">
                    <?= csrf_field() ?>
                    <button type="submit" class="linklike">Logout</button>
                </form>
            <?php else: ?>
                <a href="/login">Login</a>
                <a href="/signup">Sign up</a>
            <?php endif; ?>
        </nav>
    </div>
    <?php if ($msg = flash('success')): ?>
        <div class="flash success container"><?= e($msg) ?></div>
    <?php endif; ?>
    <?php if ($msg = flash('error')): ?>
        <div class="flash error container"><?= e($msg) ?></div>
    <?php endif; ?>
</header>
<main class="container">
    <?php include $viewPath; ?>
</main>
<footer class="container foot">&copy; <?= date('Y') ?> Student Library</footer>
</body>
</html>
