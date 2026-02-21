<?php
namespace Vitrina\Models;

use Database;
use PDO;
use PDOException;
use Vitrina\Lib\Globales;

class Contrato {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::connect();
    }

    public function getContratos(
        $idTipoContrato,
        ?string $pieIngreso,
        ?string $numeroContrato,
        ?string $arrendatario,
        int $limit,
        int $offset
    ): array {
        try {
            // Manejar múltiples tipos de contrato
            $tiposArray = null;
            if (is_array($idTipoContrato) && count($idTipoContrato) > 0) {
                $tiposArray = $idTipoContrato;
                $idTipoContrato = null;
            } elseif ($idTipoContrato !== null) {
                $idTipoContrato = (int)$idTipoContrato;
            }
            
            $p_id_tipo_contrato = $idTipoContrato !== null ? (string)$idTipoContrato : 'NULL';
            $p_pie_ingreso = $pieIngreso !== null ? "'" . addslashes($pieIngreso) . "'" : 'NULL';
            $p_numero_contrato = $numeroContrato !== null ? "'" . addslashes($numeroContrato) . "'" : 'NULL';
            $p_arrendatario = $arrendatario !== null ? "'" . addslashes($arrendatario) . "'" : 'NULL';
            
            // Si hay múltiples tipos, hacer llamada por cada tipo y combinar resultados
            if ($tiposArray !== null && count($tiposArray) > 0) {
                $allData = [];
                $total = 0;
                
                foreach ($tiposArray as $tipoId) {
                    $p_id_tipo = (int)$tipoId;
                    // Obtener todos los registros sin paginación para combinar
                    $sql = "CALL wptvxhei_ventas.USP_Listar_Contratos_v2_Paginado2(
                        " . (int)Globales::$o_id_empresa . ",
                        $p_id_tipo,
                        $p_pie_ingreso,
                        $p_numero_contrato,
                        $p_arrendatario,
                        10000, 0
                    )";
                    
                    $stmt = $this->pdo->query($sql);
                    
                    $totalResult = $stmt->fetch(PDO::FETCH_ASSOC);
                    $total += $totalResult ? (int)$totalResult['total'] : 0;
                    
                    $stmt->nextRowset();
                    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $allData = array_merge($allData, $data);
                }
                
                // Aplicar paginación en memoria
                $paginatedData = array_slice($allData, $offset, $limit);
                
                return [
                    'total' => $total,
                    'data' => $paginatedData
                ];
            }
            
            $sql = "CALL wptvxhei_ventas.USP_Listar_Contratos_v2_Paginado2(
                " . (int)Globales::$o_id_empresa . ",
                $p_id_tipo_contrato,
                $p_pie_ingreso,
                $p_numero_contrato,
                $p_arrendatario,
                $limit, $offset
            )";
            
            $stmt = $this->pdo->query($sql);
            
            // Primer resultset: Total
            $totalResult = $stmt->fetch(PDO::FETCH_ASSOC);
            $total = $totalResult ? (int)$totalResult['total'] : 0;
            
            // Segundo resultset: Datos
            $stmt->nextRowset();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Retornar ambos valores
            return [
                'total' => $total,
                'data' => $data
            ];
        } catch (PDOException $e) {
            error_log("Error en Contrato::getContratos: " . $e->getMessage());
            return ['total' => 0, 'data' => []];
        }
    }

    public function getTiposContrato(): array {
        try {
            $stmt = $this->pdo->query("SELECT id_tipo_contrato, descripcion FROM CONTRATO_TIPO_CONTRATO_MOD WHERE activo = 1 ORDER BY descripcion");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en Contrato::getTiposContrato: " . $e->getMessage());
            return [];
        }
    }

    public function getSucursales(): array {
        try {
            $stmt = $this->pdo->query("SELECT sucursalid, descripcion FROM sucursal WHERE id_empresa = " . (int)Globales::$o_id_empresa . " ORDER BY descripcion");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en Contrato::getSucursales: " . $e->getMessage());
            return [];
        }
    }

    public function getTiposPuestoComercial(): array {
        try {
            $stmt = $this->pdo->query("SELECT id_tipo_puesto_comercial, descripcion FROM CONTRATO_TIPO_PUESTO_COMERCIAL WHERE id_empresa = " . (int)Globales::$o_id_empresa . " AND estado = 1 ORDER BY descripcion");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en Contrato::getTiposPuestoComercial: " . $e->getMessage());
            return [];
        }
    }

    public function getPuestosComercialesParaEdicion(?int $sucursalId, ?int $tipoPuestoId, ?int $contratoId = null): array {
        try {
            $p_sucursal = $sucursalId !== null ? (int)$sucursalId : 'NULL';
            $p_tipo_puesto = $tipoPuestoId !== null ? (int)$tipoPuestoId : 'NULL';
            $p_contrato = $contratoId !== null ? (int)$contratoId : 'NULL';

            $sql = "CALL wptvxhei_ventas.USP_PuestoComercial_ListarParaEdicion_Hoy(
                " . (int)Globales::$o_id_empresa . ",
                $p_sucursal,
                $p_tipo_puesto,
                $p_contrato
            )";

            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en Contrato::getPuestosComercialesParaEdicion: " . $e->getMessage());
            return [];
        }
    }

    public function buscarArrendatarios(?string $busqueda): array {
        try {
            $p_buscar = $busqueda !== null && !empty($busqueda) ? "'" . addslashes($busqueda) . "'" : "'%'";

            $sql = "SELECT 
                        a.id_arrendatario,
                        a.docident_id,
                        t.abreviatura AS tipo_documento,
                        a.numero_documento,
                        CONCAT(a.apellidos, ' ', a.nombres) AS nombres,
                        a.nombre_concesionario_razon_social AS concessionsario
                    FROM CONTRATO_ARRENDATARIO a
                    INNER JOIN TIPODOCIDENTIDAD t ON a.docident_id = t.docident_id
                    WHERE a.id_empresa = " . (int)Globales::$o_id_empresa . "
                    AND a.estado = 1
                    AND (a.numero_documento LIKE $p_buscar 
                        OR a.apellidos LIKE $p_buscar 
                        OR a.nombres LIKE $p_buscar 
                        OR a.nombre_concesionario_razon_social LIKE $p_buscar)
                    ORDER BY a.apellidos, a.nombres
                    LIMIT 50";

            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en Contrato::buscarArrendatarios: " . $e->getMessage());
            return [];
        }
    }

    public function getArrendatarioById(int $id): ?array {
        try {
            $stmt = $this->pdo->query("SELECT 
                LPAD(id_arrendatario,7,'0') as id_arrendatario,
                docident_id,
                numero_documento,
                CONCAT(apellidos, ' ', nombres) as nombres,
                nombre_concesionario_razon_social
            FROM CONTRATO_ARRENDATARIO 
            WHERE id_arrendatario = " . (int)$id);
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: null;
        } catch (PDOException $e) {
            error_log("Error en Contrato::getArrendatarioById: " . $e->getMessage());
            return null;
        }
    }

    public function getTiposDocumento(): array {
        try {
            $stmt = $this->pdo->query("SELECT docident_id, descripcion, abreviatura FROM TIPODOCIDENTIDAD ORDER BY descripcion");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en Contrato::getTiposDocumento: " . $e->getMessage());
            return [];
        }
    }
}
