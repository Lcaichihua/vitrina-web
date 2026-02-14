<?php
namespace Vitrina\Models;

use Database;
use PDO;
use PDOException;
use Vitrina\Lib\Globales;

class Arrendatario {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::connect();
    }

    public function getTotalRecords(int $filtroEstado = 1): int {
        try {
            $sql = "SELECT COUNT(a.id_arrendatario)
                    FROM CONTRATO_ARRENDATARIO a
                    INNER JOIN TIPODOCIDENTIDAD b ON a.docident_id = b.docident_id
                    WHERE a.id_empresa = :id_empresa
                      AND (:filtro_estado_check = -1 OR IFNULL(a.estado,1) = :filtro_estado_value)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id_empresa', Globales::$o_id_empresa, PDO::PARAM_INT);
            $stmt->bindParam(':filtro_estado_check', $filtroEstado, PDO::PARAM_INT);
            $stmt->bindParam(':filtro_estado_value', $filtroEstado, PDO::PARAM_INT);
            $stmt->execute();
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Error en Arrendatario::getTotalRecords: " . $e->getMessage());
            return 0;
        }
    }

    public function getPaginatedRecords(int $filtroEstado = 1, int $limit, int $offset): array {
        try {
            $sql = "SELECT
                        a.id_arrendatario,
                        b.abreviatura AS tipo_documento,
                        a.numero_documento,
                        a.nombres,
                        a.apellidos,
                        a.direccion,
                        DATE_FORMAT(a.fechaing, '%d/%m/%Y') AS desde,
                        DATE_FORMAT(a.fechamod, '%d/%m/%Y') AS hasta,
                        a.estado
                    FROM CONTRATO_ARRENDATARIO a
                    INNER JOIN TIPODOCIDENTIDAD b ON a.docident_id = b.docident_id
                    WHERE a.id_empresa = :id_empresa
                      AND (:filtro_estado_check = -1 OR IFNULL(a.estado,1) = :filtro_estado_value)
                    ORDER BY a.apellidos, a.nombres
                    LIMIT :limit_param OFFSET :offset_param;";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id_empresa', Globales::$o_id_empresa, PDO::PARAM_INT);
            $stmt->bindParam(':filtro_estado_check', $filtroEstado, PDO::PARAM_INT);
            $stmt->bindParam(':filtro_estado_value', $filtroEstado, PDO::PARAM_INT);
            $stmt->bindParam(':limit_param', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset_param', $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en Arrendatario::getPaginatedRecords: " . $e->getMessage());
            return [];
        }
    }
}
