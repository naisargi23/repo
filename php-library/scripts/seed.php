<?php

declare(strict_types=1);

require __DIR__ . '/../app/config.php';

use function App\db;

$pdo = db();

$books = [
    ['The Pragmatic Programmer', 'Andrew Hunt, David Thomas', '9780201616224', 5],
    ['Clean Code', 'Robert C. Martin', '9780132350884', 5],
    ['Introduction to Algorithms', 'Cormen, Leiserson, Rivest, Stein', '9780262033848', 3],
    ['Design Patterns', 'Erich Gamma et al.', '9780201633610', 4],
    ['You Don\'t Know JS Yet', 'Kyle Simpson', '9781098124045', 6],
    ['Operating System Concepts', 'Silberschatz, Galvin, Gagne', '9781119456339', 2],
    ['Database System Concepts', 'Silberschatz, Korth, Sudarshan', '9780073523323', 3],
    ['Artificial Intelligence: A Modern Approach', 'Russell, Norvig', '9780134610993', 4],
    ['Structure and Interpretation of Computer Programs', 'Abelson, Sussman', '9780262510875', 2],
    ['Refactoring', 'Martin Fowler', '9780201485677', 3],
];

$stmt = $pdo->prepare('INSERT OR IGNORE INTO books (title, author, isbn, total_copies, available_copies, created_at) VALUES (:title, :author, :isbn, :total, :avail, :created)');
$now = (new DateTimeImmutable('now'))->format(DATE_ATOM);
foreach ($books as [$title, $author, $isbn, $total]) {
    $stmt->execute([
        ':title' => $title,
        ':author' => $author,
        ':isbn' => $isbn,
        ':total' => $total,
        ':avail' => $total,
        ':created' => $now,
    ]);
}

echo "Seed completed\n";
