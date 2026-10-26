<?php
declare(strict_types=1);

/**
 * Most shared hosting (including Hostinger) has no screen for setting
 * custom environment variables for a plain PHP site — that feature is
 * usually Node.js-app-only. So instead: copy app/config/env.local.example.php
 * to app/config/env.local.php, fill in your real database/SMTP values, and
 * this file is loaded automatically everywhere. Never share env.local.php —
 * it holds real passwords. If it doesn't exist, everything just falls back
 * to whatever real environment variables ARE set (e.g. on hosts that do
 * support them), same as before.
 */
$oxynEnvFile = dirname(__DIR__) . '/config/env.local.php';
if (is_file($oxynEnvFile)) {
    require_once $oxynEnvFile;
}

function e(string|int|float|null $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function base_path(string $path = ''): string
{
    $base = dirname(__DIR__, 2);
    return $path === '' ? $base : $base . DIRECTORY_SEPARATOR . ltrim($path, '/\\');
}

function asset(string $path): string
{
    return 'assets/' . ltrim($path, '/');
}

function partial(string $name, array $data = []): void
{
    extract($data, EXTR_SKIP);
    require base_path('app/partials/' . $name . '.php');
}
