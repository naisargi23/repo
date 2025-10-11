<?php
/** @var string $pageTitle */
/** @var string $pageContent */
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= h($pageTitle) ?> - College Library</title>
    <link rel="stylesheet" href="/assets/css/styles.css">
</head>
<body>
    <header class="topbar">
        <div class="container nav">
            <div class="brand"><a href="/?route=dashboard">College Library</a></div>
            <nav>
                <?php if (is_logged_in()): ?>
                    <a href="/?route=dashboard">Dashboard</a>
                    <a href="/?route=books">Books</a>
                    <a href="/?route=students">Students</a>
                    <a href="/?route=loans">Loans</a>
                    <span class="user">Hello, <?= h(current_user_name()) ?></span>
                    <a class="btn" href="/?route=logout">Logout</a>
                <?php else: ?>
                    <a class="btn" href="/?route=login">Login</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <main class="container">
        <?= $pageContent ?>
    </main>

    <footer class="footer">
        <div class="container">
            <small>&copy; <?= date('Y') ?> College Library</small>
        </div>
    </footer>
</body>
</html>
