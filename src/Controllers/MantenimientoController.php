<?php
namespace Vitrina\Controllers;

use Vitrina\Models\TipoPuestoComercial;
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
}