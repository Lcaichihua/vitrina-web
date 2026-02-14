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

// Cargar la clase Globales
require_once PROJECT_ROOT . '/src/Lib/Globales.php';

// Cargar el id_empresa de la sesión en la variable global estática
if (isset($_SESSION['empresa_id'])) {
    \Vitrina\Lib\Globales::$o_id_empresa = $_SESSION['empresa_id'];
}

// Iniciar la conexión a la BD y ponerla en una variable global (simple para este proyecto)
// En un proyecto más grande, usaríamos Inyección de Dependencias.
try {
    $GLOBALS['pdo'] = Database::connect();
} catch (Exception $e) {
    die($e->getMessage());
}