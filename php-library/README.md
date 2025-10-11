# Student Library Management (PHP + SQLite)

A simple student-facing library management system built with plain PHP and SQLite.

## Features
- Student sign up, login, logout (sessions)
- Book catalog with search
- Borrow and return books
- Student dashboard: active and past loans
- CSRF protection, basic flash messages

## Getting started

1. Ensure PHP 8.1+ is installed.
2. Start the built-in server from the project root:
   ```bash
   php -S localhost:8080 -t public
   ```
3. Initialize the database (only first time):
   ```bash
   php src/schema.php
   ```
4. Open `http://localhost:8080` in your browser.

## Project structure
```
php-library/
  public/         # web root
    index.php     # router
    style.css
  src/
    bootstrap.php # db, sessions, helpers
    schema.php    # create tables and seed
  views/
    auth/
      login.php
      signup.php
    catalog/
      index.php
      dashboard.php
    partials/
      layout.php
  data/
    library.sqlite (created on first run)
```

## Notes
- This is intentionally simple and student-focused. No admin UI.
- For production, use a real web server, secure cookies, HTTPS, and stronger validation.
