<?php
namespace Vitrina\Models;

use Database;
use PDO;
use PDOException;
use Vitrina\Lib\Globales;

class PuestoComercial {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::connect();
    }

    public function getAllVigentes() {
        // Este método se mantiene por si hay otras partes del código que lo usen,
        // pero la paginación usará los nuevos métodos.
        try {
            $stmt = $this->pdo->prepare("CALL USP_PuestoComercial_Listar(?, ?)");
            $filtroEstado = 1; // 1 = activos, 0 = inactivos, -1 = ambos
            $stmt->bindParam(1, Globales::$o_id_empresa, PDO::PARAM_INT);
            $stmt->bindParam(2, $filtroEstado, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en PuestoComercial::getAllVigentes: " . $e->getMessage());
            return [];
        }
    }

    public function getTotalRecords(int $filtroEstado = 1): int {
        try {
            $sql = "SELECT COUNT(a.id_puesto_comercial)
                    FROM CONTRATO_PUESTO_COMERCIAL a
                    INNER JOIN CONTRATO_TIPO_PUESTO_COMERCIAL b ON a.id_tipo_puesto_comercial = b.id_tipo_puesto_comercial AND b.id_empresa = :id_empresa_param_b
                    INNER JOIN sucursal s ON a.sucursalid = s.sucursalid
                    WHERE a.id_empresa = :id_empresa_where AND (:filtro_estado_check = -1 OR IFNULL(a.estado,1) = :filtro_estado_value)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id_empresa_param_b', Globales::$o_id_empresa, PDO::PARAM_INT);
            $stmt->bindParam(':id_empresa_where', Globales::$o_id_empresa, PDO::PARAM_INT);
            $stmt->bindParam(':filtro_estado_check', $filtroEstado, PDO::PARAM_INT);
            $stmt->bindParam(':filtro_estado_value', $filtroEstado, PDO::PARAM_INT);
            $stmt->execute();
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Error en PuestoComercial::getTotalRecords: " . $e->getMessage());
            return 0;
        }
    }

    public function getPaginatedRecords(int $filtroEstado = 1, int $limit, int $offset): array {
        try {
            $sql = "SELECT
                        a.id_puesto_comercial,
                        b.id_empresa,
                        b.id_tipo_puesto_comercial,
                        b.descripcion AS tipoPuesto,
                        a.sucursalid,
                        s.descripcion AS sucursal,
                        a.interior,
                        a.observacion,
                        a.estado
                    FROM CONTRATO_PUESTO_COMERCIAL a
                    INNER JOIN CONTRATO_TIPO_PUESTO_COMERCIAL b
                            ON a.id_tipo_puesto_comercial = b.id_tipo_puesto_comercial
                           AND b.id_empresa              = :id_empresa_param_b
                    INNER JOIN sucursal s
                            ON a.sucursalid = s.sucursalid
                    WHERE a.id_empresa = :id_empresa_where
                      AND (:filtro_estado_check = -1 OR IFNULL(a.estado,1) = :filtro_estado_value)
                    ORDER BY s.descripcion, b.descripcion, a.interior
                    LIMIT :limit_param OFFSET :offset_param;";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id_empresa_param_b', Globales::$o_id_empresa, PDO::PARAM_INT);
            $stmt->bindParam(':id_empresa_where', Globales::$o_id_empresa, PDO::PARAM_INT);
            $stmt->bindParam(':filtro_estado_check', $filtroEstado, PDO::PARAM_INT);
            $stmt->bindParam(':filtro_estado_value', $filtroEstado, PDO::PARAM_INT);
            $stmt->bindParam(':limit_param', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset_param', $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en PuestoComercial::getPaginatedRecords: " . $e->getMessage());
            return [];
        }
    }
}
