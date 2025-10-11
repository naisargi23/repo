<?php
declare(strict_types=1);

require_once __DIR__ . '/../../src/helpers.php';
require_once __DIR__ . '/../../src/db.php';

$error = null;
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = db();
        $pdo->beginTransaction();

        $pdo->exec("CREATE TABLE IF NOT EXISTS users (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(190) NOT NULL UNIQUE,
            password_hash VARCHAR(255) NOT NULL,
            role ENUM('admin','staff') NOT NULL DEFAULT 'admin',
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        $pdo->exec("CREATE TABLE IF NOT EXISTS students (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            roll_no VARCHAR(50) NOT NULL UNIQUE,
            name VARCHAR(150) NOT NULL,
            email VARCHAR(190) NULL UNIQUE,
            department VARCHAR(100) NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        $pdo->exec("CREATE TABLE IF NOT EXISTS books (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            isbn VARCHAR(50) NULL UNIQUE,
            title VARCHAR(255) NOT NULL,
            author VARCHAR(255) NOT NULL,
            category VARCHAR(100) NULL,
            total_copies INT NOT NULL DEFAULT 1,
            available_copies INT NOT NULL DEFAULT 1,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        $pdo->exec("CREATE TABLE IF NOT EXISTS loans (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            student_id INT UNSIGNED NOT NULL,
            book_id INT UNSIGNED NOT NULL,
            issue_date DATE NOT NULL,
            due_date DATE NOT NULL,
            return_date DATE NULL,
            fine_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
            status ENUM('issued','returned','lost') NOT NULL DEFAULT 'issued',
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_loans_student (student_id),
            INDEX idx_loans_book (book_id),
            INDEX idx_loans_status (status),
            CONSTRAINT fk_loans_student FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE RESTRICT,
            CONSTRAINT fk_loans_book FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE RESTRICT
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        // Seed default admin if no users
        $count = (int) $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
        if ($count === 0) {
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, 'admin')");
            $stmt->execute([
                'Administrator',
                'admin@example.com',
                password_hash('admin123', PASSWORD_DEFAULT),
            ]);
        }

        $pdo->commit();
        $success = true;
    } catch (Throwable $e) {
        if (isset($pdo) && $pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $error = $e->getMessage();
    }
}

ob_start();
?>
<div class="card">
    <h2>Install Database</h2>
    <?php if ($success): ?>
        <p>Installation completed successfully.</p>
        <p>Default admin: <strong>admin@example.com / admin123</strong></p>
        <p><a class="btn" href="/?route=login">Go to Login</a></p>
    <?php else: ?>
        <?php if ($error): ?>
            <p style="color:#dc2626;">Error: <?= h($error) ?></p>
        <?php endif; ?>
        <form method="post">
            <p>This will create necessary tables and a default admin.</p>
            <button type="submit">Run Installation</button>
        </form>
    <?php endif; ?>
</div>
<?php
$content = ob_get_clean();
render('Install', $content);
