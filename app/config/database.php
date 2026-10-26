<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| DATABASE CONNECTION SETTINGS
|--------------------------------------------------------------------------
| These come from environment variables so no password is ever committed
| to a file. Set them on your hosting control panel (the same place you
| set the SMTP_* variables for the contact form).
|
|   DB_CONNECTION  "mysql" (default, normal cPanel database) or "sqlite"
|                  (a single self-contained file — no database to create
|                  at all, useful if your host has no MySQL or for testing)
|   DB_HOST        e.g. localhost  (cPanel almost always uses "localhost")
|   DB_NAME        e.g. oxyn_studio        (the database you created)
|                  — for sqlite, a file path such as storage/app.sqlite
|   DB_USER        e.g. oxyn_admin         (the database user you created)
|   DB_PASSWORD    the password for that database user
|   DB_PORT        optional, defaults to 3306
|
| See /SETUP.md at the project root for the full step-by-step guide.
|--------------------------------------------------------------------------
*/

return [
    'driver'   => getenv('DB_CONNECTION') ?: 'mysql',
    'host'     => getenv('DB_HOST') ?: 'localhost',
    'port'     => getenv('DB_PORT') ?: '3306',
    'database' => getenv('DB_NAME') ?: '',
    'username' => getenv('DB_USER') ?: '',
    'password' => getenv('DB_PASSWORD') ?: '',
    'charset'  => 'utf8mb4',
];
