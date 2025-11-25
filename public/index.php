<?php
session_start();

// Cargar configuración
require_once '../config/config.php';

// Autoload simple
function autoload($class) {
    $file = APP_PATH . '/core/' . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
}
spl_autoload_register('autoload');

// Iniciar aplicación
$app = new App();
?> 