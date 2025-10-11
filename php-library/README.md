# Student Library (PHP + SQLite)

A simple student-facing library management system with registration/login, catalog search, borrow/return, and loans view.

## Requirements
- PHP 8+ with `pdo_sqlite`

## Setup
1. Run migrations and seeds:
   ```bash
   php scripts/migrate.php
   php scripts/seed.php
   ```
2. Start the PHP dev server:
   ```bash
   php -S localhost:8080 -t public
   ```
3. Visit `http://localhost:8080`

## Features
- Register/Login/Logout
- Catalog with search by title/author/ISBN
- Borrow book (max 3 active loans)
- Return book
- My Loans with overdue indicator
