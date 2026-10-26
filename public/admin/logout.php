<?php
declare(strict_types=1);
require __DIR__ . '/../../app/helpers/auth.php';
auth_boot_session();
$_SESSION = [];
session_destroy();
header('Location: login.php');
