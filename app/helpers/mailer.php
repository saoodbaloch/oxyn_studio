<?php

declare(strict_types=1);

/**
 * Minimal raw-socket SMTP client — no Composer/PHPMailer dependency.
 * Same approach the contact form already used; both features now share
 * this one implementation.
 *
 * Configure via environment variables (same place as the contact form):
 *   SMTP_HOST, SMTP_PORT, SMTP_USERNAME, SMTP_PASSWORD, SMTP_ENCRYPTION,
 *   SMTP_FROM_EMAIL, SMTP_FROM_NAME
 */

function mailer_smtp_read($fp): string
{
    $out = '';
    while (($line = fgets($fp, 515)) !== false) {
        $out .= $line;
        if (isset($line[3]) && $line[3] === ' ') {
            break;
        }
    }
    return $out;
}

function mailer_smtp_cmd($fp, string $cmd, array $ok): string
{
    fwrite($fp, $cmd . "\r\n");
    $r = mailer_smtp_read($fp);
    $code = (int) substr($r, 0, 3);
    if (!in_array($code, $ok, true)) {
        throw new RuntimeException('SMTP error ' . $code);
    }
    return $r;
}

/**
 * Sends a plain-text email. Throws on any failure — callers decide how
 * to present that to the user.
 */
function mailer_send(string $to, string $subject, string $body, ?string $replyEmail = null, ?string $replyName = null): void
{
    $host = getenv('SMTP_HOST') ?: '';
    $port = (int) (getenv('SMTP_PORT') ?: 587);
    $user = getenv('SMTP_USERNAME') ?: '';
    $pass = getenv('SMTP_PASSWORD') ?: '';
    $enc = strtolower(getenv('SMTP_ENCRYPTION') ?: 'tls');
    $from = getenv('SMTP_FROM_EMAIL') ?: $user;
    $fromName = getenv('SMTP_FROM_NAME') ?: 'OXYN Studios Website';

    if ($host === '' || $user === '' || $pass === '' || $from === '') {
        throw new RuntimeException('SMTP is not configured.');
    }

    $replyEmail = $replyEmail ?: $from;
    $replyName = $replyName ?: $fromName;

    $remote = ($enc === 'ssl' ? 'ssl://' : '') . $host;
    $fp = stream_socket_client($remote . ':' . $port, $errno, $errstr, 15, STREAM_CLIENT_CONNECT);
    if (!$fp) {
        throw new RuntimeException('SMTP connection failed.');
    }
    stream_set_timeout($fp, 15);

    $greet = mailer_smtp_read($fp);
    if ((int) substr($greet, 0, 3) !== 220) {
        throw new RuntimeException('SMTP greeting failed.');
    }

    mailer_smtp_cmd($fp, 'EHLO oxynstudio.com', [250]);

    if ($enc === 'tls') {
        mailer_smtp_cmd($fp, 'STARTTLS', [220]);
        if (!stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
            throw new RuntimeException('TLS failed.');
        }
        mailer_smtp_cmd($fp, 'EHLO oxynstudio.com', [250]);
    }

    mailer_smtp_cmd($fp, 'AUTH LOGIN', [334]);
    mailer_smtp_cmd($fp, base64_encode($user), [334]);
    mailer_smtp_cmd($fp, base64_encode($pass), [235]);

    mailer_smtp_cmd($fp, 'MAIL FROM:<' . $from . '>', [250]);
    mailer_smtp_cmd($fp, 'RCPT TO:<' . $to . '>', [250, 251]);
    mailer_smtp_cmd($fp, 'DATA', [354]);

    $safeSubject = str_replace(["\r", "\n"], ' ', $subject);
    $safeReply = str_replace(["\r", "\n"], '', $replyEmail);
    $safeName = str_replace(["\r", "\n"], ' ', $replyName);

    $headers = "From: {$fromName} <{$from}>\r\nReply-To: {$safeName} <{$safeReply}>\r\nMIME-Version: 1.0\r\nContent-Type: text/plain; charset=UTF-8\r\nSubject: {$safeSubject}\r\n";
    $data = $headers . "\r\n" . $body;
    $data = preg_replace('/^\./m', '..', $data);

    fwrite($fp, $data . "\r\n.\r\n");
    $r = mailer_smtp_read($fp);
    if ((int) substr($r, 0, 3) !== 250) {
        throw new RuntimeException('SMTP delivery failed.');
    }

    mailer_smtp_cmd($fp, 'QUIT', [221]);
    fclose($fp);
}
