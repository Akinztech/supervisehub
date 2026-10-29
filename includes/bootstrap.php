<?php
declare(strict_types=1);
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
$config = require dirname(__DIR__) . '/config/app.php';
date_default_timezone_set($config['timezone']);
require dirname(__DIR__) . '/config/database.php';
require __DIR__ . '/functions.php';
try { $pdo = database_connection(); }
catch (Throwable $e) {
    http_response_code(500);
    echo '<h2>Database connection failed</h2><p>Configure your .env/environment values or run demo_init.php with DB_DRIVER=sqlite.</p><pre>' . e($e->getMessage()) . '</pre>';
    exit;
}
