<?php

declare(strict_types=1);

function auth_boot_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'httponly' => true,
        'samesite' => 'Lax',
        'secure' => $secure,
    ]);
    session_name('oxyn_admin_session');
    session_start();
}

function csrf_token(): string
{
    auth_boot_session();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}

function csrf_verify(): bool
{
    auth_boot_session();
    $sent = (string) ($_POST['_csrf'] ?? '');
    $known = (string) ($_SESSION['csrf_token'] ?? '');
    return $known !== '' && hash_equals($known, $sent);
}

function current_admin_id(): ?int
{
    auth_boot_session();
    return isset($_SESSION['admin_id']) ? (int) $_SESSION['admin_id'] : null;
}

function is_logged_in(): bool
{
    return current_admin_id() !== null;
}

/** Redirects to the login page unless already authenticated. Call at the top of every protected admin page. */
function require_admin(): void
{
    auth_boot_session();
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

function admin_login_throttle_key(): string
{
    return 'login_attempts_' . substr(hash('sha256', $_SERVER['REMOTE_ADDR'] ?? 'unknown'), 0, 16);
}

/** Simple in-session lockout: 8 bad attempts pauses further tries for 60 seconds. */
function admin_login_is_locked(): bool
{
    auth_boot_session();
    $key = admin_login_throttle_key();
    $data = $_SESSION[$key] ?? ['count' => 0, 'until' => 0];
    return $data['count'] >= 8 && time() < $data['until'];
}

function admin_login_register_failure(): void
{
    auth_boot_session();
    $key = admin_login_throttle_key();
    $data = $_SESSION[$key] ?? ['count' => 0, 'until' => 0];
    $data['count']++;
    if ($data['count'] >= 8) {
        $data['until'] = time() + 60;
    }
    $_SESSION[$key] = $data;
}

function admin_login_clear_failures(): void
{
    auth_boot_session();
    unset($_SESSION[admin_login_throttle_key()]);
}

function admin_find_by_email(PDO $pdo, string $email): ?array
{
    $stmt = $pdo->prepare('SELECT * FROM admin_users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function admin_count(PDO $pdo): int
{
    return (int) $pdo->query('SELECT COUNT(*) AS c FROM admin_users')->fetch()['c'];
}

function admin_create(PDO $pdo, string $email, string $password): int
{
    $stmt = $pdo->prepare('INSERT INTO admin_users (email, password_hash) VALUES (?, ?)');
    $stmt->execute([$email, password_hash($password, PASSWORD_DEFAULT)]);
    return (int) $pdo->lastInsertId();
}

function admin_set_password(PDO $pdo, int $id, string $password): void
{
    $stmt = $pdo->prepare('UPDATE admin_users SET password_hash = ?, reset_code_hash = NULL, reset_expires_at = NULL WHERE id = ?');
    $stmt->execute([password_hash($password, PASSWORD_DEFAULT), $id]);
}

/** Generates a 6-digit code, stores its hash + a 15 minute expiry, and returns the plain code to email to the user. */
function admin_issue_reset_code(PDO $pdo, int $id): string
{
    $code = (string) random_int(100000, 999999);
    $expires = (new DateTimeImmutable('+15 minutes'))->format('Y-m-d H:i:s');
    $stmt = $pdo->prepare('UPDATE admin_users SET reset_code_hash = ?, reset_expires_at = ? WHERE id = ?');
    $stmt->execute([password_hash($code, PASSWORD_DEFAULT), $expires, $id]);
    return $code;
}

function admin_verify_reset_code(array $admin, string $code): bool
{
    if (empty($admin['reset_code_hash']) || empty($admin['reset_expires_at'])) {
        return false;
    }
    if (strtotime((string) $admin['reset_expires_at']) < time()) {
        return false;
    }
    return password_verify($code, (string) $admin['reset_code_hash']);
}
