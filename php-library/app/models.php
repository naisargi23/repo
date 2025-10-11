<?php

declare(strict_types=1);

namespace App;

use DateInterval;
use DateTimeImmutable;
use PDO;
use PDOException;

function findUserByEmail(string $email): ?array {
    $stmt = db()->prepare('SELECT * FROM users WHERE email = :email');
    $stmt->execute([':email' => $email]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ?: null;
}

function createUser(string $name, string $email, string $password): int {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $now = (new DateTimeImmutable('now'))->format(DATE_ATOM);
    try {
        $stmt = db()->prepare('INSERT INTO users (name, email, password_hash, created_at) VALUES (:name, :email, :hash, :created)');
        $stmt->execute([':name' => $name, ':email' => $email, ':hash' => $hash, ':created' => $now]);
        return (int) db()->lastInsertId();
    } catch (PDOException $e) {
        if (str_contains($e->getMessage(), 'UNIQUE')) {
            throw new \RuntimeException('Email already registered');
        }
        throw $e;
    }
}

function authenticate(string $email, string $password): ?array {
    $user = findUserByEmail($email);
    if (!$user) {
        return null;
    }
    if (!password_verify($password, $user['password_hash'])) {
        return null;
    }
    return $user;
}

function listBooks(?string $query = null): array {
    $pdo = db();
    if ($query === null || trim($query) === '') {
        $stmt = $pdo->query('SELECT * FROM books ORDER BY title');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    $like = '%' . str_replace(['%', '_'], ['\\%', '\\_'], trim($query)) . '%';
    $stmt = $pdo->prepare('SELECT * FROM books WHERE title LIKE :q ESCAPE "\\" OR author LIKE :q ESCAPE "\\" OR isbn LIKE :q ESCAPE "\\" ORDER BY title');
    $stmt->execute([':q' => $like]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getBook(int $bookId): ?array {
    $stmt = db()->prepare('SELECT * FROM books WHERE id = :id');
    $stmt->execute([':id' => $bookId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ?: null;
}

function countActiveLoans(int $userId): int {
    $stmt = db()->prepare('SELECT COUNT(*) FROM loans WHERE user_id = :uid AND returned_at IS NULL');
    $stmt->execute([':uid' => $userId]);
    return (int) $stmt->fetchColumn();
}

function hasActiveLoanForBook(int $userId, int $bookId): bool {
    $stmt = db()->prepare('SELECT 1 FROM loans WHERE user_id = :uid AND book_id = :bid AND returned_at IS NULL LIMIT 1');
    $stmt->execute([':uid' => $userId, ':bid' => $bookId]);
    return (bool) $stmt->fetchColumn();
}

function borrowBook(int $userId, int $bookId, int $maxActiveLoans = 3, int $loanDays = 14): int {
    $pdo = db();
    $pdo->beginTransaction();
    try {
        $active = countActiveLoans($userId);
        if ($active >= $maxActiveLoans) {
            throw new \RuntimeException('You reached the maximum number of active loans.');
        }
        $book = getBook($bookId);
        if (!$book) {
            throw new \RuntimeException('Book not found.');
        }
        if ((int) $book['available_copies'] <= 0) {
            throw new \RuntimeException('No copies available to borrow.');
        }
        if (hasActiveLoanForBook($userId, $bookId)) {
            throw new \RuntimeException('You already borrowed this book.');
        }
        $now = new DateTimeImmutable('now');
        $due = $now->add(new DateInterval('P' . $loanDays . 'D'));
        $stmt = $pdo->prepare('INSERT INTO loans (user_id, book_id, borrowed_at, due_date) VALUES (:uid, :bid, :borrowed, :due)');
        $stmt->execute([
            ':uid' => $userId,
            ':bid' => $bookId,
            ':borrowed' => $now->format(DATE_ATOM),
            ':due' => $due->format(DATE_ATOM),
        ]);
        $loanId = (int) $pdo->lastInsertId();
        $stmt2 = $pdo->prepare('UPDATE books SET available_copies = available_copies - 1 WHERE id = :bid');
        $stmt2->execute([':bid' => $bookId]);
        $pdo->commit();
        return $loanId;
    } catch (\Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $e;
    }
}

function returnLoan(int $userId, int $loanId): void {
    $pdo = db();
    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare('SELECT * FROM loans WHERE id = :lid AND user_id = :uid AND returned_at IS NULL');
        $stmt->execute([':lid' => $loanId, ':uid' => $userId]);
        $loan = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$loan) {
            throw new \RuntimeException('Active loan not found.');
        }
        $now = (new DateTimeImmutable('now'))->format(DATE_ATOM);
        $upd = $pdo->prepare('UPDATE loans SET returned_at = :now WHERE id = :lid');
        $upd->execute([':now' => $now, ':lid' => $loanId]);
        $upd2 = $pdo->prepare('UPDATE books SET available_copies = available_copies + 1 WHERE id = :bid');
        $upd2->execute([':bid' => (int) $loan['book_id']]);
        $pdo->commit();
    } catch (\Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $e;
    }
}

function getLoansForUser(int $userId): array {
    $stmt = db()->prepare('SELECT l.*, b.title, b.author FROM loans l JOIN books b ON b.id = l.book_id WHERE l.user_id = :uid ORDER BY COALESCE(l.returned_at, l.due_date) DESC');
    $stmt->execute([':uid' => $userId]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $now = new DateTimeImmutable('now');
    foreach ($rows as &$row) {
        $row['is_overdue'] = $row['returned_at'] === null && new DateTimeImmutable($row['due_date']) < $now;
    }
    return $rows;
}
