<?php

declare(strict_types=1);

require __DIR__ . '/../app/config.php';

use function App\db;

$dir = dirname(\App\DB_PATH);
if (!is_dir($dir)) {
    mkdir($dir, 0777, true);
}

$pdo = db();

$pdo->exec(<<<SQL
CREATE TABLE IF NOT EXISTS users (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name TEXT NOT NULL,
  email TEXT NOT NULL UNIQUE,
  password_hash TEXT NOT NULL,
  created_at TEXT NOT NULL
);
SQL);

$pdo->exec(<<<SQL
CREATE TABLE IF NOT EXISTS books (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  title TEXT NOT NULL,
  author TEXT NOT NULL,
  isbn TEXT UNIQUE,
  total_copies INTEGER NOT NULL,
  available_copies INTEGER NOT NULL,
  created_at TEXT NOT NULL
);
CREATE INDEX IF NOT EXISTS idx_books_title ON books(title);
CREATE INDEX IF NOT EXISTS idx_books_author ON books(author);
SQL);

$pdo->exec(<<<SQL
CREATE TABLE IF NOT EXISTS loans (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  user_id INTEGER NOT NULL,
  book_id INTEGER NOT NULL,
  borrowed_at TEXT NOT NULL,
  due_date TEXT NOT NULL,
  returned_at TEXT,
  FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY(book_id) REFERENCES books(id) ON DELETE CASCADE
);
CREATE INDEX IF NOT EXISTS idx_loans_user_active ON loans(user_id, returned_at);
CREATE INDEX IF NOT EXISTS idx_loans_book_active ON loans(book_id, returned_at);
SQL);

echo "Migrations completed\n";
