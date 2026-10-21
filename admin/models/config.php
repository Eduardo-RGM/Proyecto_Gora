<?php
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $configuracionLocal = __DIR__ . '/config.local.php';
    if (is_file($configuracionLocal)) {
        require_once $configuracionLocal;
    }

    //Variables de configuración de la base de datos
    defined('DB_driver') || define('DB_driver', 'mysql');
    defined('DB_HOST') || define('DB_HOST', 'mariadb');
    defined('DB_USER') || define('DB_USER', 'user');
    defined('DB_PASS') || define('DB_PASS', 'password');
    defined('DB_NAME') || define('DB_NAME', 'gora');
    defined('DB_PORT') || define('DB_PORT', '3306');
    defined('SMTP_HOST') || define('SMTP_HOST', getenv('SMTP_HOST') ?: 'smtp.gmail.com');
    defined('SMTP_PORT') || define('SMTP_PORT', (int) (getenv('SMTP_PORT') ?: 465));
    defined('SMTP_USER') || define('SMTP_USER', getenv('SMTP_USER') ?: '');
    defined('SMTP_PASS') || define('SMTP_PASS', getenv('SMTP_PASS') ?: '');
    defined('SMTP_FROM_NAME') || define('SMTP_FROM_NAME', getenv('SMTP_FROM_NAME') ?: 'GORA');


?>