<?php
// Configuración base
define('BASE_URL', 'http://localhost/transporte_mvc');
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');
define('PUBLIC_PATH', BASE_PATH . '/public');
define('ASSETS_PATH', BASE_URL . '/public/assets');

// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'transportedb');  // Nombre correcto de la BD
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');
?>