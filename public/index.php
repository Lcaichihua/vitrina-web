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

// Cargar Archivos de inicialización y configuración
require_once __DIR__ . '/../src/bootstrap.php';

use Vitrina\Controllers\AuthController;
use Vitrina\Controllers\MantenimientoController;
use Vitrina\Lib\Globales;

// Enrutador Básico
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Definir rutas
switch ($uri) {
    case '/':
    case '/login':
        $controller = new AuthController();
        $controller->login();
        break;

    case '/logout':
        $controller = new AuthController();
        $controller->logout();
        break;

    case '/dashboard':
        if (!isset($_SESSION['user_id'])) {
            header('Location: /');
            exit;
        }
        require_once __DIR__ . '/../templates/dashboard.php';
        break;

    case '/mantenimiento/tipo_puesto_comercial':
        if (!isset($_SESSION['user_id'])) {
            header('Location: /');
            exit;
        }
        $controller = new MantenimientoController();
        $controller->tipoPuestoComercial();
        break;

    case '/mantenimiento/puesto_comercial':
        if (!isset($_SESSION['user_id'])) {
            header('Location: /');
            exit;
        }
        $controller = new MantenimientoController();
        $controller->puestoComercial();
        break;

    case '/mantenimiento/arrendadores':
        if (!isset($_SESSION['user_id'])) {
            header('Location: /');
            exit;
        }
        $controller = new MantenimientoController();
        $controller->arrendador();
        break;

    case '/mantenimiento/arrendatarios': // Nueva ruta para Arrendatarios
        if (!isset($_SESSION['user_id'])) {
            header('Location: /');
            exit;
        }
        $controller = new MantenimientoController();
        $controller->arrendatarios();
        break;
        
    case '/contratos/listado': // Nueva ruta para Listado de Contratos
        if (!isset($_SESSION['user_id'])) {
            header('Location: /');
            exit;
        }
        $controller = new MantenimientoController();
        $controller->contratos();
        break;

    case '/contratos/guardar':
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'No autenticado']);
            exit;
        }
        $controller = new MantenimientoController();
        $controller->contratoGuardar();
        break;

    // ============ RUTAS CRUD ============
    case '/mantenimiento/tipo_puesto/guardar':
        if (!isset($_SESSION['user_id'])) {
            header('Location: /');
            exit;
        }
        $controller = new MantenimientoController();
        $controller->tipoPuestoGuardar();
        break;

    case '/mantenimiento/tipo_puesto/eliminar':
        if (!isset($_SESSION['user_id'])) {
            header('Location: /');
            exit;
        }
        $controller = new MantenimientoController();
        $controller->tipoPuestoEliminar();
        break;

    case '/mantenimiento/puesto/guardar':
        if (!isset($_SESSION['user_id'])) {
            header('Location: /');
            exit;
        }
        $controller = new MantenimientoController();
        $controller->puestoGuardar();
        break;

    case '/mantenimiento/puesto/eliminar':
        if (!isset($_SESSION['user_id'])) {
            header('Location: /');
            exit;
        }
        $controller = new MantenimientoController();
        $controller->puestoEliminar();
        break;

    case '/mantenimiento/arrendador/guardar':
        if (!isset($_SESSION['user_id'])) {
            header('Location: /');
            exit;
        }
        $controller = new MantenimientoController();
        $controller->arrendadorGuardar();
        break;

    case '/mantenimiento/arrendador/eliminar':
        if (!isset($_SESSION['user_id'])) {
            header('Location: /');
            exit;
        }
        $controller = new MantenimientoController();
        $controller->arrendadorEliminar();
        break;

    case '/mantenimiento/arrendatario/guardar':
        if (!isset($_SESSION['user_id'])) {
            header('Location: /');
            exit;
        }
        $controller = new MantenimientoController();
        $controller->arrendatarioGuardar();
        break;

    case '/mantenimiento/arrendatario/eliminar':
        if (!isset($_SESSION['user_id'])) {
            header('Location: /');
            exit;
        }
        $controller = new MantenimientoController();
        $controller->arrendatarioEliminar();
        break;

    // ============ API CONTRATOS ============
    case '/api/contratos/puestos':
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }
        $controller = new MantenimientoController();
        $controller->apiGetPuestos();
        break;

    case '/api/contratos/arrendatarios':
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }
        $controller = new MantenimientoController();
        $controller->apiBuscarArrendatarios();
        break;

    case '/api/contratos/arrendatario':
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }
        $controller = new MantenimientoController();
        $controller->apiGetArrendatario();
        break;

    case '/api/arrendatario/ubigeo':
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }
        $controller = new MantenimientoController();
        $controller->apiGetUbigeo();
        break;

    default:
        http_response_code(404);
        echo "<h1>404 - Página no encontrada</h1>";
        echo "<p>La ruta solicitada no existe en el sistema.</p>";
        break;
}