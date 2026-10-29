<?php
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');

    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $configuracionLocal = __DIR__ . '/config.local.php';
    if (is_file($configuracionLocal)) {
        require_once $configuracionLocal;
    }

    define('DB_driver', 'mysql');
    define('DB_HOST', 'mariadb');
    define('DB_USER', 'user');
    define('DB_PASS', 'password');
    define('DB_NAME', 'gora');
    define('DB_PORT', '3306');
    defined('SMTP_HOST') || define('SMTP_HOST', 'smtp.gmail.com');
    defined('SMTP_PORT') || define('SMTP_PORT', 465);
    defined('SMTP_USER') || define('SMTP_USER', '');
    defined('SMTP_PASS') || define('SMTP_PASS', '');
    defined('SMTP_FROM_NAME') || define('SMTP_FROM_NAME', 'GORA');


?>