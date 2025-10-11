<?php
declare(strict_types=1);

require_once __DIR__ . '/../../src/helpers.php';

$error = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_error']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Placeholder auth until DB ready: admin@example.com / admin123
    if ($email === 'admin@example.com' && $password === 'admin123') {
        $_SESSION['user_id'] = 1;
        $_SESSION['user_name'] = 'Administrator';
        redirect('dashboard');
    }

    $_SESSION['flash_error'] = 'Invalid credentials';
    redirect('login');
}

ob_start();
?>
<div class="form">
    <h2>Login</h2>
    <?php if ($error): ?>
        <p style="color:#dc2626;"><?= h($error) ?></p>
    <?php endif; ?>
    <form method="post">
        <div class="row">
            <label for="email">Email</label>
            <input id="email" name="email" type="email" required>
        </div>
        <div class="row">
            <label for="password">Password</label>
            <input id="password" name="password" type="password" required>
        </div>
        <button type="submit">Sign in</button>
        <p class="muted">Default: admin@example.com / admin123</p>
    </form>
</div>
<?php
$content = ob_get_clean();
render('Login', $content);
