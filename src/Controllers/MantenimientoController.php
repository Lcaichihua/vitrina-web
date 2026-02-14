<?php
namespace Vitrina\Controllers;

use Vitrina\Models\TipoPuestoComercial;
use Vitrina\Models\PuestoComercial;
use Vitrina\Models\Arrendador;
use Vitrina\Models\Arrendatario; // Añadir esta línea
use Exception;

class MantenimientoController {
    public function tipoPuestoComercial() {
        // Asegurarse de que el usuario esté autenticado
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login'); // Redirigir al login si no está autenticado
            exit;
        }

        $error = null;
        $tiposPuesto = [];

        $records_per_page = 10;
        $current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($current_page - 1) * $records_per_page;

        try {
            $model = new TipoPuestoComercial();
            $total_records = $model->getTotalRecords();
            $total_pages = ceil($total_records / $records_per_page);
            $tiposPuesto = $model->getPaginatedRecords($records_per_page, $offset);
        } catch (Exception $e) {
            error_log("Error al obtener tipos de puesto comercial: " . $e->getMessage());
            $error = "No se pudieron cargar los tipos de puesto comercial: " . $e->getMessage();
        }

        // Cargar la vista, pasando las variables de paginación
        require_once __DIR__ . '/../../templates/mantenimiento/tipos_puesto_comercial.php';
    }

    public function puestoComercial() {
        // Asegurarse de que el usuario esté autenticado
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login'); // Redirigir al login si no está autenticado
            exit;
        }

        $error = null;
        $puestosComerciales = [];

        $records_per_page = 10;
        $current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($current_page - 1) * $records_per_page;
        $filtroEstado = 1; // 1 = activos (vigentes)

        try {
            $model = new PuestoComercial();
            $total_records = $model->getTotalRecords($filtroEstado);
            $total_pages = ceil($total_records / $records_per_page);
            $puestosComerciales = $model->getPaginatedRecords($filtroEstado, $records_per_page, $offset);
        } catch (Exception $e) {
            error_log("Error al obtener puestos comerciales: " . $e->getMessage());
            $error = "No se pudieron cargar los puestos comerciales: " . $e->getMessage();
        }

        // Cargar la vista, pasando las variables de paginación
        require_once __DIR__ . '/../../templates/mantenimiento/puestos_comerciales.php';
    }

    public function arrendador() {
        // Asegurarse de que el usuario esté autenticado
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login'); // Redirigir al login si no está autenticado
            exit;
        }

        $error = null;
        $arrendadores = [];

        $records_per_page = 10;
        $current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($current_page - 1) * $records_per_page;

        try {
            $model = new Arrendador();
            $total_records = $model->getTotalRecords();
            $total_pages = ceil($total_records / $records_per_page);
            $arrendadores = $model->getPaginatedRecords($records_per_page, $offset);
        } catch (Exception $e) {
            error_log("Error al obtener arrendadores: " . $e->getMessage());
            $error = "No se pudieron cargar los arrendadores: " . $e->getMessage();
        }

        // Cargar la vista, pasando las variables de paginación
        require_once __DIR__ . '/../../templates/mantenimiento/arrendadores.php';
    }

    public function arrendatarios() { // Nuevo método para Arrendatarios
        // Asegurarse de que el usuario esté autenticado
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login'); // Redirigir al login si no está autenticado
            exit;
        }

        $error = null;
        $arrendatarios = [];

        $records_per_page = 10;
        $current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($current_page - 1) * $records_per_page;
        $filtroEstado = 1; // 1 = activos (vigentes)

        try {
            $model = new Arrendatario();
            $total_records = $model->getTotalRecords($filtroEstado);
            $total_pages = ceil($total_records / $records_per_page);
            $arrendatarios = $model->getPaginatedRecords($filtroEstado, $records_per_page, $offset);
        } catch (Exception $e) {
            error_log("Error al obtener arrendatarios: " . $e->getMessage());
            $error = "No se pudieron cargar los arrendatarios: " . $e->getMessage();
        }

        // Cargar la vista, pasando las variables de paginación
        require_once __DIR__ . '/../../templates/mantenimiento/arrendatarios.php';
    }
}