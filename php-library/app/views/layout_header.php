<?php
\App\start_session();
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= isset($title) ? \App\h($title) . ' · ' : '' ?>Student Library</title>
    <link rel="stylesheet" href="/style.css">
  </head>
  <body>
    <header class="site-header">
      <nav class="nav">
        <a href="/index.php" class="brand">Student Library</a>
        <a href="/catalog.php">Catalog</a>
        <?php if (\App\is_logged_in()): ?>
          <a href="/loans.php">My Loans</a>
          <a href="/logout.php">Logout</a>
        <?php else: ?>
          <a href="/login.php">Login</a>
          <a href="/register.php">Register</a>
        <?php endif; ?>
      </nav>
    </header>
    <main class="container">
