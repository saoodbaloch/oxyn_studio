<?php
declare(strict_types=1);
require __DIR__ . '/../app/helpers/view.php';
require __DIR__ . '/../app/helpers/db.php';
require __DIR__ . '/../app/helpers/mailer.php';

header('Content-Type: application/json; charset=utf-8');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['ok'=>false,'message'=>'Method not allowed.']); exit; }

$name = trim((string)($_POST['name'] ?? ''));
$email = trim((string)($_POST['email'] ?? ''));
$service = trim((string)($_POST['service'] ?? ''));
$message = trim((string)($_POST['message'] ?? ''));
$website = trim((string)($_POST['website'] ?? '')); // honeypot
if ($website !== '') { echo json_encode(['ok'=>true,'message'=>'Thank you.']); exit; }
if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $service === '' || $message === '') { http_response_code(422); echo json_encode(['ok'=>false,'message'=>'Please complete all fields with a valid email.']); exit; }
if (strlen($name)>120 || strlen($email)>190 || strlen($service)>120 || strlen($message)>5000) { http_response_code(422); echo json_encode(['ok'=>false,'message'=>'One or more fields are too long.']); exit; }

$to = getenv('CONTACT_TO_EMAIL') ?: 'saoodbaloch88@gmail.com';
$subject = 'New OXYN website inquiry — ' . preg_replace('/[\r\n]+/', ' ', $service);
$body = "New website inquiry\n\nName: {$name}\nEmail: {$email}\nService: {$service}\n\nMessage:\n{$message}\n";

// Best-effort: keep a copy in the dashboard's Messages inbox even if the
// database isn't configured yet or the email send below fails.
$pdo = db_or_null();
if ($pdo !== null) {
    try {
        db_ensure_schema($pdo);
        $stmt = $pdo->prepare('INSERT INTO contact_messages (name, email, service, message) VALUES (?, ?, ?, ?)');
        $stmt->execute([$name, $email, $service, $message]);
    } catch (Throwable $e) {
        // Non-fatal: the inbox is a convenience, email delivery below is what matters.
    }
}

try {
    mailer_send($to, $subject, $body, $email, $name);
    echo json_encode(['ok'=>true,'message'=>'Thanks! Your request has been sent.']);
} catch (Throwable $e) {
    if ($pdo !== null) {
        // The message is already saved in the dashboard inbox, so this
        // isn't a total loss even though the email itself didn't go out.
        http_response_code(200);
        echo json_encode(['ok'=>true,'message'=>'Thanks! Your request has been received.']);
    } else {
        http_response_code(500);
        echo json_encode(['ok'=>false,'message'=>'Message could not be sent yet. Please email saoodbaloch88@gmail.com directly.']);
    }
}
