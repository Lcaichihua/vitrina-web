<?php
namespace Vitrina\Models;

use Database;
use PDO;
use PDOException;
use Exception;
use Vitrina\Lib\Globales;

class PuestoComercial {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::connect();
    }

    public function getAllVigentes() {
        try {
            $stmt = $this->pdo->prepare("CALL USP_PuestoComercial_Listar(?, ?)");
            $filtroEstado = 1;
            $stmt->bindParam(1, Globales::$o_id_empresa, PDO::PARAM_INT);
            $stmt->bindParam(2, $filtroEstado, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en PuestoComercial::getAllVidentes: " . $e->getMessage());
            return [];
        }
    }

    public function getTotalRecords(int $filtroEstado = 1, ?string $search = null): int {
        try {
            $whereClause = "WHERE a.id_empresa = :id_empresa_where AND (:filtro_estado_check = -1 OR IFNULL(a.estado,1) = :filtro_estado_value)";
            
            if ($search) {
                $whereClause .= " AND (
                    a.interior LIKE :search1 
                    OR b.descripcion LIKE :search2 
                    OR s.descripcion LIKE :search3
                    OR a.observacion LIKE :search4
                )";
            }

            $sql = "SELECT COUNT(a.id_puesto_comercial)
                    FROM CONTRATO_PUESTO_COMERCIAL a
                    INNER JOIN CONTRATO_TIPO_PUESTO_COMERCIAL b ON a.id_tipo_puesto_comercial = b.id_tipo_puesto_comercial AND b.id_empresa = :id_empresa_param_b
                    INNER JOIN sucursal s ON a.sucursalid = s.sucursalid
                    $whereClause";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id_empresa_param_b', Globales::$o_id_empresa, PDO::PARAM_INT);
            $stmt->bindParam(':id_empresa_where', Globales::$o_id_empresa, PDO::PARAM_INT);
            $stmt->bindParam(':filtro_estado_check', $filtroEstado, PDO::PARAM_INT);
            $stmt->bindParam(':filtro_estado_value', $filtroEstado, PDO::PARAM_INT);
            
            if ($search) {
                $searchParam = "%$search%";
                $stmt->bindParam(':search1', $searchParam, PDO::PARAM_STR);
                $stmt->bindParam(':search2', $searchParam, PDO::PARAM_STR);
                $stmt->bindParam(':search3', $searchParam, PDO::PARAM_STR);
                $stmt->bindParam(':search4', $searchParam, PDO::PARAM_STR);
            }
            
            $stmt->execute();
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Error en PuestoComercial::getTotalRecords: " . $e->getMessage());
            return 0;
        }
    }

    public function getAllRecords(int $filtroEstado = -1): array {
        try {
            $stmt = $this->pdo->prepare("CALL USP_PuestoComercial_Listar(?, ?)");
            $stmt->bindParam(1, Globales::$o_id_empresa, PDO::PARAM_INT);
            $stmt->bindParam(2, $filtroEstado, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en PuestoComercial::getAllRecords: " . $e->getMessage());
            return [];
        }
    }

    public function getPaginatedRecords(int $filtroEstado = 1, int $limit, int $offset, ?string $search = null): array {
        try {
            $allRecords = $this->getAllRecords($filtroEstado);
            
            if ($search) {
                $searchLower = strtolower($search);
                $allRecords = array_filter($allRecords, function($record) use ($searchLower) {
                    return str_contains(strtolower($record['interior']), $searchLower)
                        || str_contains(strtolower($record['tipoPuesto']), $searchLower)
                        || str_contains(strtolower($record['sucursal']), $searchLower)
                        || str_contains(strtolower($record['observacion']), $searchLower);
                });
            }
            
            $totalRecords = count($allRecords);
            return array_slice($allRecords, $offset, $limit);
        } catch (PDOException $e) {
            error_log("Error en PuestoComercial::getPaginatedRecords: " . $e->getMessage());
            return [];
        }
    }

    public function create(int $tipoPuestoId, int $sucursalId, string $interior, string $observacion = '', int $estado = 1): int {
        try {
            $sqlId = "SELECT IFNULL(MAX(id_puesto_comercial), 0) + 1 FROM CONTRATO_PUESTO_COMERCIAL WHERE id_empresa = :id_empresa";
            $stmtId = $this->pdo->prepare($sqlId);
            $stmtId->bindParam(':id_empresa', Globales::$o_id_empresa, PDO::PARAM_INT);
            $stmtId->execute();
            $newId = $stmtId->fetchColumn();

            $sql = "INSERT INTO CONTRATO_PUESTO_COMERCIAL (id_puesto_comercial, id_tipo_puesto_comercial, sucursalid, interior, observacion, estado, id_empresa) 
                    VALUES (:id, :tipo_id, :sucursal_id, :interior, :observacion, :estado, :id_empresa)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $newId, PDO::PARAM_INT);
            $stmt->bindParam(':tipo_id', $tipoPuestoId, PDO::PARAM_INT);
            $stmt->bindParam(':sucursal_id', $sucursalId, PDO::PARAM_INT);
            $stmt->bindParam(':interior', $interior, PDO::PARAM_STR);
            $stmt->bindParam(':observacion', $observacion, PDO::PARAM_STR);
            $stmt->bindParam(':estado', $estado, PDO::PARAM_INT);
            $stmt->bindParam(':id_empresa', Globales::$o_id_empresa, PDO::PARAM_INT);
            $stmt->execute();
            return (int)$newId;
        } catch (PDOException $e) {
            error_log("Error en PuestoComercial::create: " . $e->getMessage());
            throw new Exception("Error al crear puesto comercial");
        }
    }

    public function update(int $id, int $tipoPuestoId, int $sucursalId, string $interior, string $observacion = '', int $estado = 1): bool {
        try {
            $sql = "UPDATE CONTRATO_PUESTO_COMERCIAL SET id_tipo_puesto_comercial = :tipo_id, sucursalid = :sucursal_id, interior = :interior, observacion = :observacion, estado = :estado 
                    WHERE id_puesto_comercial = :id AND id_empresa = :id_empresa";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':tipo_id', $tipoPuestoId, PDO::PARAM_INT);
            $stmt->bindParam(':sucursal_id', $sucursalId, PDO::PARAM_INT);
            $stmt->bindParam(':interior', $interior, PDO::PARAM_STR);
            $stmt->bindParam(':observacion', $observacion, PDO::PARAM_STR);
            $stmt->bindParam(':estado', $estado, PDO::PARAM_INT);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':id_empresa', Globales::$o_id_empresa, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en PuestoComercial::update: " . $e->getMessage());
            throw new Exception("Error al actualizar puesto comercial");
        }
    }

    public function delete(int $id): bool {
        try {
            $sql = "DELETE FROM CONTRATO_PUESTO_COMERCIAL WHERE id_puesto_comercial = :id AND id_empresa = :id_empresa";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':id_empresa', Globales::$o_id_empresa, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en PuestoComercial::delete: " . $e->getMessage());
            throw new Exception("Error al eliminar puesto comercial");
        }
    }

    public function getTiposPuestoComercial(): array {
        try {
            $stmt = $this->pdo->query("SELECT id_tipo_puesto_comercial, descripcion FROM CONTRATO_TIPO_PUESTO_COMERCIAL WHERE id_empresa = " . (int)Globales::$o_id_empresa . " AND estado = 1 ORDER BY descripcion");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en PuestoComercial::getTiposPuestoComercial: " . $e->getMessage());
            return [];
        }
    }

    public function getSucursales(): array {
        try {
            $stmt = $this->pdo->query("SELECT sucursalid, descripcion FROM sucursal WHERE id_empresa = " . (int)Globales::$o_id_empresa . " ORDER BY descripcion");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en PuestoComercial::getSucursales: " . $e->getMessage());
            return [];
        }
    }
}
