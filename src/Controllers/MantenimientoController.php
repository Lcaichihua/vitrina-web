<?php
namespace Vitrina\Controllers;

use Vitrina\Models\TipoPuestoComercial;
use Vitrina\Models\PuestoComercial;
use Vitrina\Models\Arrendador;
use Vitrina\Models\Arrendatario;
use Vitrina\Models\Contrato; // Añadir esta línea
use Vitrina\Lib\Globales;
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
        $search = isset($_GET['search']) ? trim($_GET['search']) : null;

        try {
            $model = new TipoPuestoComercial();
            $total_records = $model->getTotalRecords($search);
            $total_pages = ceil($total_records / $records_per_page);
            $tiposPuesto = $model->getPaginatedRecords($records_per_page, $offset, $search);
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
        $filtroEstado = isset($_GET['estado']) && is_numeric($_GET['estado']) ? (int)$_GET['estado'] : 1;
        $search = isset($_GET['search']) ? trim($_GET['search']) : null;

        try {
            $model = new PuestoComercial();
            $total_records = $model->getTotalRecords($filtroEstado, $search);
            $total_pages = ceil($total_records / $records_per_page);
            $puestosComerciales = $model->getPaginatedRecords($filtroEstado, $records_per_page, $offset, $search);
            $tiposPuesto = $model->getTiposPuestoComercial();
            $sucursales = $model->getSucursales();
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
        $search = isset($_GET['search']) ? trim($_GET['search']) : null;

        try {
            $model = new Arrendador();
            $total_records = $model->getTotalRecords($search);
            $total_pages = ceil($total_records / $records_per_page);
            $arrendadores = $model->getPaginatedRecords($records_per_page, $offset, $search);
        } catch (Exception $e) {
            error_log("Error al obtener arrendadores: " . $e->getMessage());
            $error = "No se pudieron cargar los arrendadores: " . $e->getMessage();
        }

        // Cargar la vista, pasando las variables de paginación
        require_once __DIR__ . '/../../templates/mantenimiento/arrendadores.php';
    }

    public function arrendatarios() {
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
        $filtroEstado = isset($_GET['estado']) && is_numeric($_GET['estado']) ? (int)$_GET['estado'] : 1;
        $search = isset($_GET['search']) ? trim($_GET['search']) : null;

        try {
            $model = new Arrendatario();
            $total_records = $model->getTotalRecords($filtroEstado, $search);
            $total_pages = ceil($total_records / $records_per_page);
            $arrendatarios = $model->getPaginatedRecords($filtroEstado, $records_per_page, $offset, $search);
            $tiposDocumento = $model->getTiposDocumento();
            $departamentos = $model->getDepartamentos();
        } catch (Exception $e) {
            error_log("Error al obtener arrendatarios: " . $e->getMessage());
            $error = "No se pudieron cargar los arrendatarios: " . $e->getMessage();
        }

        // Cargar la vista, pasando las variables de paginación
        require_once __DIR__ . '/../../templates/mantenimiento/arrendatarios.php';
    }

    public function contratos() { // Nuevo método para Contratos
        // Asegurarse de que el usuario esté autenticado
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login'); // Redirigir al login si no está autenticado
            exit;
        }

        $error = null;
        $contratos = [];
        $tiposContrato = [];

        // Parámetros de paginación
        $records_per_page = 10;
        $current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($current_page - 1) * $records_per_page;

        // Parámetros de filtro
        $numeroContrato = $_GET['numero_contrato'] ?? null;
        $arrendatario = $_GET['arrendatario'] ?? null;
        $pieIngreso = $_GET['pie_ingreso'] ?? null; // 'SI', 'NO', 'TODOS'
        
        $tiposSeleccionados = [];
        if (isset($_GET['tipos_contrato']) && is_array($_GET['tipos_contrato'])) {
            $tiposSeleccionados = array_map('intval', array_filter($_GET['tipos_contrato'], 'is_numeric'));
        }
        
        $idTipoContrato = !empty($tiposSeleccionados) ? $tiposSeleccionados : null;

        if ($pieIngreso === 'TODOS') {
            $pieIngreso = null;
        }

        try {
            $model = new Contrato();
            $tiposContrato = $model->getTiposContrato();
            $sucursales = $model->getSucursales();
            $tiposPuestoComercial = $model->getTiposPuestoComercial();
            $tiposDocumento = $model->getTiposDocumento();

            // Una sola llamada al SP
            $result = $model->getContratos($idTipoContrato, $pieIngreso, $numeroContrato, $arrendatario, $records_per_page, $offset);
            $total_records = $result['total'];
            $contratos = $result['data'];
            $total_pages = ceil($total_records / $records_per_page);
        } catch (Exception $e) {
            error_log("Error al obtener contratos: " . $e->getMessage());
            $error = "No se pudieron cargar los contratos: " . $e->getMessage();
        }

        // Pasar las variables a la vista
        require_once __DIR__ . '/../../templates/contratos/listado.php';
    }

    public function contratoGuardar() {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'No autenticado']);
            exit;
        }

        header('Content-Type: application/json');

        try {
            $model = new Contrato();

            $data = [
                'numero_contrato' => $model->getSiguienteNumeroContrato(),
                'id_arrendatario' => (int)$_POST['id_arrendatario'],
                'id_arrendador' => !empty($_POST['id_arrendador']) ? (int)$_POST['id_arrendador'] : null,
                'id_sucursal' => (int)$_POST['id_sucursal'],
                'id_tipo_contrato' => (int)$_POST['id_tipo_contrato'],
                'inicio_contrato' => $_POST['inicio_contrato'],
                'fin_contrato' => $_POST['fin_contrato'],
                'nro_meses' => (int)$_POST['nro_meses'],
                'tipo_moneda' => $_POST['tipo_moneda'] ?? 'SOLES',
                'observaciones' => $_POST['observaciones'] ?? '',
                'activo' => 1,
                'estado' => 'ACTIVO',
                'porcentaje_renta_variable' => isset($_POST['chk_renta_variable']) ? (float)$_POST['porcentaje_renta_variable'] : 0,
                'importe_contraprestacion' => (float)$_POST['importe_contraprestacion'],
                'economato_tipo' => $_POST['economato_tipo'] ?? 'FIJO',
                'importe_economato' => (float)$_POST['importe_economato'],
                'espacios_economato' => (int)$_POST['espacios_economato'],
                'importe_pie_ingreso' => isset($_POST['chk_pie_ingreso']) ? (float)$_POST['importe_pie_ingreso'] : 0,
                'importe_canastilla' => (float)$_POST['importe_canastilla'],
                'puestos' => isset($_POST['puestos']) ? array_map('intval', $_POST['puestos']) : []
            ];

            $idContrato = $model->create($data);

            echo json_encode(['success' => true, 'id_contrato' => $idContrato]);
        } catch (Exception $e) {
            error_log("Error al guardar contrato: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }

    // ============ TIPO PUESTO COMERCIAL ============
    public function tipoPuestoGuardar() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $id = isset($_POST['id']) ? (int)$_POST['id'] : null;
        $descripcion = trim($_POST['descripcion'] ?? '');
        $estado = isset($_POST['estado']) ? (int)$_POST['estado'] : 1;

        if (empty($descripcion)) {
            $_SESSION['error'] = 'La descripción es requerida';
            header('Location: /mantenimiento/tipo_puesto_comercial');
            exit;
        }

        try {
            $model = new TipoPuestoComercial();
            if ($id) {
                $model->update($id, $descripcion, $estado);
                $_SESSION['success'] = 'Tipo de puesto actualizado correctamente';
            } else {
                $model->create($descripcion, $estado);
                $_SESSION['success'] = 'Tipo de puesto creado correctamente';
            }
        } catch (Exception $e) {
            error_log("Error al guardar tipo puesto: " . $e->getMessage());
            $_SESSION['error'] = 'Error al guardar: ' . $e->getMessage();
        }

        header('Location: /mantenimiento/tipo_puesto_comercial');
        exit;
    }

    public function tipoPuestoEliminar() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($id <= 0) {
            $_SESSION['error'] = 'ID inválido';
            header('Location: /mantenimiento/tipo_puesto_comercial');
            exit;
        }

        try {
            $model = new TipoPuestoComercial();
            $model->delete($id);
            $_SESSION['success'] = 'Tipo de puesto eliminado correctamente';
        } catch (Exception $e) {
            error_log("Error al eliminar tipo puesto: " . $e->getMessage());
            $_SESSION['error'] = 'Error al eliminar: ' . $e->getMessage();
        }

        header('Location: /mantenimiento/tipo_puesto_comercial');
        exit;
    }

    // ============ PUESTO COMERCIAL ============
    public function puestoGuardar() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $tipoPuesto = isset($_POST['tipoPuesto']) ? (int)$_POST['tipoPuesto'] : 0;
        $sucursal = isset($_POST['sucursal']) ? (int)$_POST['sucursal'] : 0;
        $interior = trim($_POST['interior'] ?? '');
        $observacion = trim($_POST['observacion'] ?? '');
        $estado = isset($_POST['estado']) ? (int)$_POST['estado'] : 1;

        if ($tipoPuesto <= 0 || $sucursal <= 0 || empty($interior)) {
            $_SESSION['error'] = 'Los campos tipo puesto, sucursal e interior son requeridos';
            header('Location: /mantenimiento/puesto_comercial');
            exit;
        }

        try {
            $model = new PuestoComercial();
            if ($id) {
                $model->update($id, $tipoPuesto, $sucursal, $interior, $observacion, $estado);
                $_SESSION['success'] = 'Puesto actualizado correctamente';
            } else {
                $model->create($tipoPuesto, $sucursal, $interior, $observacion, $estado);
                $_SESSION['success'] = 'Puesto creado correctamente';
            }
        } catch (Exception $e) {
            error_log("Error al guardar puesto: " . $e->getMessage());
            $_SESSION['error'] = 'Error al guardar: ' . $e->getMessage();
        }

        header('Location: /mantenimiento/puesto_comercial');
        exit;
    }

    public function puestoEliminar() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($id <= 0) {
            $_SESSION['error'] = 'ID inválido';
            header('Location: /mantenimiento/puesto_comercial');
            exit;
        }

        try {
            $model = new PuestoComercial();
            $model->delete($id);
            $_SESSION['success'] = 'Puesto eliminado correctamente';
        } catch (Exception $e) {
            error_log("Error al eliminar puesto: " . $e->getMessage());
            $_SESSION['error'] = 'Error al eliminar: ' . $e->getMessage();
        }

        header('Location: /mantenimiento/puesto_comercial');
        exit;
    }

    // ============ ARRENDADOR ============
    public function arrendadorGuardar() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $id = isset($_POST['id']) ? (int)$_POST['id'] : null;
        $tipoDoc = trim($_POST['tipoDoc'] ?? 'DNI');
        $numeroDoc = trim($_POST['numeroDoc'] ?? '');
        $apellidos = trim($_POST['apellidos'] ?? '');
        $nombres = trim($_POST['nombres'] ?? '');
        $direccion = trim($_POST['direccion'] ?? '');

        if (empty($numeroDoc) || empty($apellidos) || empty($nombres)) {
            $_SESSION['error'] = 'Los campos documento, apellidos y nombres son requeridos';
            header('Location: /mantenimiento/arrendadores');
            exit;
        }

        try {
            $model = new Arrendador();
            if ($id) {
                $model->update($id, $tipoDoc, $numeroDoc, $apellidos, $nombres, $direccion);
                $_SESSION['success'] = 'Arrendador actualizado correctamente';
            } else {
                $model->create($tipoDoc, $numeroDoc, $apellidos, $nombres, $direccion);
                $_SESSION['success'] = 'Arrendador creado correctamente';
            }
        } catch (Exception $e) {
            error_log("Error al guardar arrendador: " . $e->getMessage());
            $_SESSION['error'] = 'Error al guardar: ' . $e->getMessage();
        }

        header('Location: /mantenimiento/arrendadores');
        exit;
    }

    public function arrendadorEliminar() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($id <= 0) {
            $_SESSION['error'] = 'ID inválido';
            header('Location: /mantenimiento/arrendadores');
            exit;
        }

        try {
            $model = new Arrendador();
            $model->delete($id);
            $_SESSION['success'] = 'Arrendador eliminado correctamente';
        } catch (Exception $e) {
            error_log("Error al eliminar arrendador: " . $e->getMessage());
            $_SESSION['error'] = 'Error al eliminar: ' . $e->getMessage();
        }

        header('Location: /mantenimiento/arrendadores');
        exit;
    }

    // ============ ARRENDATARIO ============
    public function arrendatarioGuardar() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $tipoDocId = isset($_POST['tipoDoc']) ? (int)$_POST['tipoDoc'] : 0;
        $numeroDoc = trim($_POST['numeroDoc'] ?? '');
        $apellidos = trim($_POST['apellidos'] ?? '');
        $nombres = trim($_POST['nombres'] ?? '');
        $razonSocial = trim($_POST['razonSocial'] ?? '');
        $depaid = isset($_POST['departamento']) ? (int)$_POST['departamento'] : 0;
        $provid = isset($_POST['provincia']) ? (int)$_POST['provincia'] : 0;
        $distid = isset($_POST['distrito']) ? (int)$_POST['distrito'] : 0;
        $direccion = trim($_POST['direccion'] ?? '');
        $estado = isset($_POST['estado']) ? (int)$_POST['estado'] : 1;

        if ($tipoDocId <= 0 || empty($numeroDoc)) {
            $_SESSION['error'] = 'Seleccione tipo de documento e ingrese el número';
            header('Location: /mantenimiento/arrendatarios');
            exit;
        }

        try {
            $model = new Arrendatario();
            if ($id > 0) {
                $model->update($id, $tipoDocId, $numeroDoc, $apellidos, $nombres, $razonSocial, $depaid, $provid, $distid, $direccion, $estado);
                $_SESSION['success'] = 'Arrendatario actualizado correctamente';
            } else {
                $model->create($tipoDocId, $numeroDoc, $apellidos, $nombres, $razonSocial, $depaid, $provid, $distid, $direccion, $estado);
                $_SESSION['success'] = 'Arrendatario creado correctamente';
            }
        } catch (Exception $e) {
            error_log("Error al guardar arrendatario: " . $e->getMessage());
            $_SESSION['error'] = 'Error al guardar: ' . $e->getMessage();
        }

        header('Location: /mantenimiento/arrendatarios');
        exit;
    }

    public function arrendatarioEliminar() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($id <= 0) {
            $_SESSION['error'] = 'ID inválido';
            header('Location: /mantenimiento/arrendatarios');
            exit;
        }

        try {
            $model = new Arrendatario();
            $model->delete($id);
            $_SESSION['success'] = 'Arrendatario eliminado correctamente';
        } catch (Exception $e) {
            error_log("Error al eliminar arrendatario: " . $e->getMessage());
            $_SESSION['error'] = 'Error al eliminar: ' . $e->getMessage();
        }

        header('Location: /mantenimiento/arrendatarios');
        exit;
    }

    // ============ API CONTRATOS ============
    public function apiGetPuestos() {
        header('Content-Type: application/json');
        
        try {
            $sucursalId = isset($_GET['sucursal']) && is_numeric($_GET['sucursal']) ? (int)$_GET['sucursal'] : null;
            $tipoPuestoId = isset($_GET['tipo_puesto']) && is_numeric($_GET['tipo_puesto']) ? (int)$_GET['tipo_puesto'] : null;
            $contratoId = isset($_GET['contrato']) && is_numeric($_GET['contrato']) ? (int)$_GET['contrato'] : null;

            $model = new Contrato();
            $puestos = $model->getPuestosComercialesParaEdicion($sucursalId, $tipoPuestoId, $contratoId);

            echo json_encode(['success' => true, 'data' => $puestos]);
        } catch (Exception $e) {
            error_log("Error en apiGetPuestos: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }

    public function apiBuscarArrendatarios() {
        header('Content-Type: application/json');
        
        try {
            $busqueda = isset($_GET['q']) ? trim($_GET['q']) : null;

            $model = new Contrato();
            $arrendatarios = $model->buscarArrendatarios($busqueda);

            echo json_encode(['success' => true, 'data' => $arrendatarios]);
        } catch (Exception $e) {
            error_log("Error en apiBuscarArrendatarios: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }

    public function apiGetArrendatario() {
        header('Content-Type: application/json');
        
        try {
            $id = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : null;

            if (!$id) {
                echo json_encode(['success' => false, 'error' => 'ID requerido']);
                exit;
            }

            $model = new Contrato();
            $arrendatario = $model->getArrendatarioById($id);

            if ($arrendatario) {
                echo json_encode(['success' => true, 'data' => $arrendatario]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Arrendatario no encontrado']);
            }
        } catch (Exception $e) {
            error_log("Error en apiGetArrendatario: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }

    public function apiGetUbigeo() {
        header('Content-Type: application/json');
        
        try {
            $action = $_POST['action'] ?? '';
            $model = new Arrendatario();
            
            if ($action === 'getProvincias') {
                $depaid = isset($_POST['depaid']) ? (int)$_POST['depaid'] : 0;
                if ($depaid <= 0) {
                    echo json_encode([]);
                    exit;
                }
                $provincias = $model->getProvincias($depaid);
                echo json_encode($provincias);
            } elseif ($action === 'getDistritos') {
                $provid = isset($_POST['provid']) ? (int)$_POST['provid'] : 0;
                if ($provid <= 0) {
                    echo json_encode([]);
                    exit;
                }
                $distritos = $model->getDistritos($provid);
                echo json_encode($distritos);
            } else {
                echo json_encode([]);
            }
        } catch (Exception $e) {
            error_log("Error en apiGetUbigeo: " . $e->getMessage());
            echo json_encode(['error' => $e->getMessage()]);
        }
        exit;
    }
}