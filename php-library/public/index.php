<?php

declare(strict_types=1);

require __DIR__ . '/../src/bootstrap.php';

verify_csrf();

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';
$method = $_SERVER['REQUEST_METHOD'];


// Simple routing
function match_route(string $pattern, string $path): ?array {
    $regex = '#^' . preg_replace('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#', '(?P<$1>[^/]+)', $pattern) . '$#';
    if (preg_match($regex, $path, $m)) {
        return array_filter($m, 'is_string', ARRAY_FILTER_USE_KEY);
    }
    return null;
}

function redirect(string $to): never {
    header('Location: ' . $to);
    exit;
}

// Routes
switch (true) {
    // home -> catalog
    case $path === '/':
        $user = auth_user();
        $query = trim((string)($_GET['q'] ?? ''));
        $where = '';
        $params = [];
        if ($query !== '') {
            $where = 'WHERE title LIKE :q OR author LIKE :q OR isbn LIKE :q';
            $params[':q'] = '%' . $query . '%';
        }
        $stmt = db()->prepare('SELECT * FROM books ' . $where . ' ORDER BY title LIMIT 100');
        $stmt->execute($params);
        $books = $stmt->fetchAll();
        render('catalog/index', compact('user', 'books', 'query'));
        break;

    // auth: register
    case $path === '/signup' && $method === 'GET':
        require_guest();
        render('auth/signup');
        break;
    case $path === '/signup' && $method === 'POST':
        require_guest();
        $name = trim((string)($_POST['name'] ?? ''));
        $email = strtolower(trim((string)($_POST['email'] ?? '')));
        $password = (string)($_POST['password'] ?? '');
        if ($name === '' || $email === '' || $password === '') {
            flash('error', 'All fields are required');
            redirect('/signup');
        }
        $hash = password_hash($password, PASSWORD_DEFAULT);
        try {
            $stmt = db()->prepare('INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)');
            $stmt->execute([$name, $email, $hash]);
            flash('success', 'Account created. Please log in.');
            redirect('/login');
        } catch (PDOException $e) {
            flash('error', 'Email already in use');
            redirect('/signup');
        }
        break;

    // auth: login
    case $path === '/login' && $method === 'GET':
        require_guest();
        render('auth/login');
        break;
    case $path === '/login' && $method === 'POST':
        require_guest();
        $email = strtolower(trim((string)($_POST['email'] ?? '')));
        $password = (string)($_POST['password'] ?? '');
        $stmt = db()->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if (!$user || !password_verify($password, $user['password_hash'])) {
            flash('error', 'Invalid credentials');
            redirect('/login');
        }
        $_SESSION['user_id'] = $user['id'];
        redirect('/');
        break;

    case $path === '/logout' && $method === 'POST':
        require_auth();
        unset($_SESSION['user_id']);
        flash('success', 'Logged out');
        redirect('/login');
        break;

    // borrow
    case preg_match('#^/borrow/(?P<id>\d+)$#', $path, $m) && $method === 'POST':
        require_auth();
        $bookId = (int)$m['id'];
        $pdo = db();
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare('SELECT * FROM books WHERE id = ?');
            $stmt->execute([$bookId]);
            $book = $stmt->fetch();
            if (!$book || (int)$book['copies_available'] <= 0) {
                throw new RuntimeException('Book not available');
            }
            $due = (new DateTimeImmutable('+14 days'))->format('Y-m-d H:i:s');
            $stmt = $pdo->prepare('INSERT INTO loans (user_id, book_id, due_at) VALUES (?, ?, ?)');
            $stmt->execute([$_SESSION['user_id'], $bookId, $due]);
            $stmt = $pdo->prepare('UPDATE books SET copies_available = copies_available - 1 WHERE id = ?');
            $stmt->execute([$bookId]);
            $pdo->commit();
            flash('success', 'Book borrowed. Due in 14 days.');
        } catch (Throwable $e) {
            $pdo->rollBack();
            flash('error', 'Unable to borrow: ' . $e->getMessage());
        }
        redirect('/');
        break;

    // return
    case preg_match('#^/return/(?P<id>\d+)$#', $path, $m) && $method === 'POST':
        require_auth();
        $loanId = (int)$m['id'];
        $pdo = db();
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare('SELECT * FROM loans WHERE id = ? AND user_id = ? AND returned_at IS NULL');
            $stmt->execute([$loanId, $_SESSION['user_id']]);
            $loan = $stmt->fetch();
            if (!$loan) {
                throw new RuntimeException('Loan not found');
            }
            $stmt = $pdo->prepare('UPDATE loans SET returned_at = datetime("now") WHERE id = ?');
            $stmt->execute([$loanId]);
            $stmt = $pdo->prepare('UPDATE books SET copies_available = copies_available + 1 WHERE id = ?');
            $stmt->execute([$loan['book_id']]);
            $pdo->commit();
            flash('success', 'Book returned.');
        } catch (Throwable $e) {
            $pdo->rollBack();
            flash('error', 'Unable to return: ' . $e->getMessage());
        }
        redirect('/dashboard');
        break;

    // dashboard
    case $path === '/dashboard' && $method === 'GET':
        require_auth();
        $user = auth_user();
        $active = db()->prepare('SELECT l.*, b.title, b.author FROM loans l JOIN books b ON b.id = l.book_id WHERE l.user_id = ? AND l.returned_at IS NULL ORDER BY l.borrowed_at DESC');
        $active->execute([$user['id']]);
        $activeLoans = $active->fetchAll();
        $past = db()->prepare('SELECT l.*, b.title, b.author FROM loans l JOIN books b ON b.id = l.book_id WHERE l.user_id = ? AND l.returned_at IS NOT NULL ORDER BY l.returned_at DESC LIMIT 50');
        $past->execute([$user['id']]);
        $pastLoans = $past->fetchAll();
        render('catalog/dashboard', compact('user', 'activeLoans', 'pastLoans'));
        break;

    default:
        http_response_code(404);
        echo 'Not Found';
}
