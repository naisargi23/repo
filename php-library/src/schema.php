<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

$pdo = db();

$pdo->beginTransaction();
try {
    $pdo->exec('CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        email TEXT NOT NULL UNIQUE,
        password_hash TEXT NOT NULL,
        created_at TEXT NOT NULL DEFAULT (datetime("now"))
    )');

    $pdo->exec('CREATE TABLE IF NOT EXISTS books (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title TEXT NOT NULL,
        author TEXT NOT NULL,
        isbn TEXT,
        copies_total INTEGER NOT NULL DEFAULT 1,
        copies_available INTEGER NOT NULL DEFAULT 1,
        created_at TEXT NOT NULL DEFAULT (datetime("now"))
    )');

    $pdo->exec('CREATE TABLE IF NOT EXISTS loans (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL,
        book_id INTEGER NOT NULL,
        borrowed_at TEXT NOT NULL DEFAULT (datetime("now")),
        due_at TEXT NOT NULL,
        returned_at TEXT,
        FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY(book_id) REFERENCES books(id) ON DELETE CASCADE
    )');

    // Seed sample books if table empty
    $count = (int) $pdo->query('SELECT COUNT(*) FROM books')->fetchColumn();
    if ($count === 0) {
        $stmt = $pdo->prepare('INSERT INTO books (title, author, isbn, copies_total, copies_available) VALUES (?, ?, ?, ?, ?)');
        $samples = [
            ['Clean Code', 'Robert C. Martin', '9780132350884', 3, 3],
            ['Introduction to Algorithms', 'Cormen et al.', '9780262033848', 2, 2],
            ['Design Patterns', 'Gamma et al.', '9780201633610', 2, 2],
            ['The Pragmatic Programmer', 'Andrew Hunt, David Thomas', '9780201616224', 4, 4],
            ['You Don\'t Know JS Yet', 'Kyle Simpson', null, 5, 5],
        ];
        foreach ($samples as $s) {
            $stmt->execute($s);
        }
    }

    $pdo->commit();
    echo "Database initialized and seeded\n";
} catch (Throwable $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo 'Error: ' . $e->getMessage();
    exit;
}
