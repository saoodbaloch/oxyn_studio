<?php

declare(strict_types=1);

/**
 * Tiny PDO wrapper. Every admin/blog feature is written to degrade
 * gracefully when the database is not reachable (not configured yet,
 * wrong credentials, host firewall, etc.) — the public site keeps working
 * from its built-in fallback content, it just won't show admin-edited
 * changes until the connection is fixed.
 */

function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $config = require base_path('app/config/database.php');

    if ($config['driver'] === 'sqlite') {
        $path = $config['database'] !== '' ? $config['database'] : 'storage/app.sqlite';
        $fullPath = str_starts_with($path, '/') ? $path : base_path($path);

        $dir = dirname($fullPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        $pdo = new PDO('sqlite:' . $fullPath);
        $pdo->exec('PRAGMA foreign_keys = ON');
    } else {
        if ($config['database'] === '' || $config['username'] === '') {
            throw new RuntimeException('Database is not configured yet.');
        }

        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            $config['host'],
            $config['port'],
            $config['database'],
            $config['charset']
        );

        $pdo = new PDO($dsn, $config['username'], $config['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);
    }

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    return $pdo;
}

/**
 * Returns null instead of throwing when the database can't be reached,
 * so public-facing pages can fall back to their static content instead
 * of showing a fatal error to a visitor.
 */
function db_or_null(): ?PDO
{
    try {
        return db();
    } catch (Throwable $e) {
        return null;
    }
}

/**
 * Runs the schema (see database/schema.sql) against whichever driver is
 * configured. Used by the admin first-run setup screen so a brand-new
 * sqlite file — or an empty MySQL database the admin already created and
 * imported nothing into — gets its tables automatically. For MySQL we
 * still recommend importing database/schema.sql via phpMyAdmin (SETUP.md),
 * but this makes the dashboard self-healing either way.
 */
function db_ensure_schema(PDO $pdo): void
{
    $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
    $auto = $driver === 'sqlite'
        ? 'INTEGER PRIMARY KEY AUTOINCREMENT'
        : 'INT UNSIGNED AUTO_INCREMENT PRIMARY KEY';
    $timestamp = $driver === 'sqlite' ? "TEXT DEFAULT (datetime('now'))" : 'DATETIME NULL DEFAULT NULL';
    $now = $driver === 'sqlite' ? "TEXT DEFAULT (datetime('now'))" : 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP';

    $pdo->exec("CREATE TABLE IF NOT EXISTS admin_users (
        id {$auto},
        email VARCHAR(190) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        reset_code_hash VARCHAR(255) NULL,
        reset_expires_at {$timestamp},
        created_at {$now}
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS blog_posts (
        id {$auto},
        slug VARCHAR(220) NOT NULL UNIQUE,
        title VARCHAR(220) NOT NULL,
        excerpt VARCHAR(400) NOT NULL DEFAULT '',
        content MEDIUMTEXT NOT NULL DEFAULT '',
        cover_image VARCHAR(255) NOT NULL DEFAULT '',
        category VARCHAR(80) NOT NULL DEFAULT 'Video Editing',
        author_name VARCHAR(120) NOT NULL DEFAULT 'OXYN Studios',
        author_avatar VARCHAR(255) NOT NULL DEFAULT '',
        is_published TINYINT(1) NOT NULL DEFAULT 1,
        published_at {$timestamp},
        created_at {$now}
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS contact_messages (
        id {$auto},
        name VARCHAR(120) NOT NULL,
        email VARCHAR(190) NOT NULL,
        service VARCHAR(120) NOT NULL DEFAULT '',
        message TEXT NOT NULL,
        is_read TINYINT(1) NOT NULL DEFAULT 0,
        created_at {$now}
    )");
}
