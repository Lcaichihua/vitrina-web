<?php
namespace Vitrina\Controllers;

use Vitrina\Models\TipoPuestoComercial;
use Vitrina\Models\PuestoComercial; // Añadir esta línea
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

        try {
            $model = new TipoPuestoComercial();
            $tiposPuesto = $model->getAll();
        } catch (Exception $e) {
            error_log("Error al obtener tipos de puesto comercial: " . $e->getMessage());
            $error = "No se pudieron cargar los tipos de puesto comercial: " . $e->getMessage();
        }

        // Cargar la vista
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

        try {
            $model = new PuestoComercial();
            $puestosComerciales = $model->getAllVigentes();
        } catch (Exception $e) {
            error_log("Error al obtener puestos comerciales: " . $e->getMessage());
            $error = "No se pudieron cargar los puestos comerciales: " . $e->getMessage();
        }

        // Cargar la vista
        require_once __DIR__ . '/../../templates/mantenimiento/puestos_comerciales.php';
    }
}