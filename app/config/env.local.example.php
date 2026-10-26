<?php
declare(strict_types=1);

/**
 * HOW TO USE THIS FILE (see SETUP.md for the full walkthrough):
 *
 *   1. Rename this file from "env.local.example.php" to "env.local.php"
 *      (same folder). It is loaded automatically once renamed.
 *   2. Replace every 'CHANGE_ME' value below with your real details.
 *   3. Save. That's it — no hosting control panel settings needed.
 *
 * This file holds real passwords, so:
 *   - Never share it or paste its contents anywhere public.
 *   - It only needs to exist on your live server, not anywhere else.
 */

// ---- Database (from Hostinger hPanel → Databases) ----
putenv('DB_CONNECTION=mysql');
putenv('DB_HOST=localhost');
putenv('DB_NAME=CHANGE_ME');       // e.g. u123456789_oxyn
putenv('DB_USER=CHANGE_ME');       // e.g. u123456789_admin
putenv('DB_PASSWORD=CHANGE_ME');   // the password you set when creating the database user

// ---- Email sending (for the contact form + admin "forgot password") ----
// If Hostinger issued you a mailbox (e.g. hello@yourdomain.com), its SMTP
// details are in hPanel → Emails → your mailbox → Configure/Connect devices.
putenv('SMTP_HOST=CHANGE_ME');           // e.g. smtp.hostinger.com
putenv('SMTP_PORT=587');
putenv('SMTP_ENCRYPTION=tls');
putenv('SMTP_USERNAME=CHANGE_ME');       // your full mailbox address
putenv('SMTP_PASSWORD=CHANGE_ME');       // that mailbox's password
putenv('SMTP_FROM_EMAIL=CHANGE_ME');     // usually the same as SMTP_USERNAME
putenv('SMTP_FROM_NAME=OXYN Studios');

// ---- Where contact form messages get emailed ----
putenv('CONTACT_TO_EMAIL=saoodbaloch88@gmail.com');
