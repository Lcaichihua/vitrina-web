<?php
// Iniciar el buffer de salida (buena práctica)
ob_start();

// Iniciar la sesión
// 'httponly' y 'secure' son vitales para la seguridad
session_start([
    'cookie_httponly' => true,
    'cookie_secure' => isset($_SERVER['HTTPS']), // true si estás en HTTPS
    'cookie_samesite' => 'Lax'
]);

// Definir la ruta raíz del proyecto
define('PROJECT_ROOT', dirname(__DIR__));

// Cargar el autoloader de Composer
require_once PROJECT_ROOT . '/vendor/autoload.php';

// Cargar el archivo de configuración
require_once PROJECT_ROOT . '/config/database.php';

// Iniciar la conexión a la BD y ponerla en una variable global (simple para este proyecto)
// En un proyecto más grande, usaríamos Inyección de Dependencias.
try {
    $GLOBALS['pdo'] = getPdoConnection();
} catch (Exception $e) {
    die($e->getMessage());
}