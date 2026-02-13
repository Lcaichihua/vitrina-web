<?php
// public/index.php

// --- FIX PARA SERVIDOR INTERNO DE PHP (PhpStorm/Terminal) ---
// Si estás usando "php -S" o el servidor de PhpStorm, esto permite
// que los archivos reales (como generate_hash.php, .css, .js) se carguen directamente.
if (php_sapi_name() === 'cli-server') {
    $file = __DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if (is_file($file)) {
        return false; // Deja que el servidor sirva el archivo directamente
    }
}

// Configuración de Sesión Segura
session_start([
    'cookie_lifetime' => 86400,
    'cookie_secure' => false, // Cambiar a true si usas HTTPS
    'cookie_httponly' => true,
    'cookie_samesite' => 'Lax',
]);

// Cargar Archivos
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/Models/User.php';
require_once __DIR__ . '/../src/Controllers/AuthController.php';

// Enrutador Básico
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Definir rutas
switch ($uri) {
    case '/':
    case '/login':
        $controller = new \Vitrina\Controllers\AuthController();
        $controller->login();
        break;

    case '/logout':
        $controller = new \Vitrina\Controllers\AuthController();
        $controller->logout();
        break;

    case '/dashboard':
        if (!isset($_SESSION['user_id'])) {
            header('Location: /');
            exit;
        }
        require_once __DIR__ . '/../templates/dashboard.php';
        break;

    default:
        http_response_code(404);
        echo "<h1>404 - Página no encontrada</h1>";
        echo "<p>La ruta solicitada no existe en el sistema.</p>";
        break;
}