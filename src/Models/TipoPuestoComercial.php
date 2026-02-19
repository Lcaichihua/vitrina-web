<?php
namespace Vitrina\Models;

use Database;
use PDO;
use PDOException;
use Vitrina\Lib\Globales;

class TipoPuestoComercial {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::connect();
    }

    public function getAll() {
        try {
            $sql = "select id_tipo_puesto_comercial,descripcion,estado," .
                   "id_empresa from CONTRATO_TIPO_PUESTO_COMERCIAL " .
                   "where id_empresa = ".Globales::$o_id_empresa." order by descripcion;";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en TipoPuestoComercial::getAll: " . $e->getMessage());
            return [];
        }
    }

    public function getTotalRecords(?string $search = null): int {
        try {
            $whereClause = "WHERE id_empresa = :id_empresa";
            
            if ($search) {
                $whereClause .= " AND descripcion LIKE :search";
            }

            $sql = "SELECT COUNT(*) FROM CONTRATO_TIPO_PUESTO_COMERCIAL $whereClause";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id_empresa', Globales::$o_id_empresa, PDO::PARAM_INT);
            
            if ($search) {
                $searchParam = "%$search%";
                $stmt->bindParam(':search', $searchParam, PDO::PARAM_STR);
            }
            
            $stmt->execute();
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Error en TipoPuestoComercial::getTotalRecords: " . $e->getMessage());
            return 0;
        }
    }

    public function getPaginatedRecords(int $limit, int $offset, ?string $search = null): array {
        try {
            $whereClause = "WHERE id_empresa = :id_empresa";
            
            if ($search) {
                $whereClause .= " AND descripcion LIKE :search";
            }

            $sql = "SELECT id_tipo_puesto_comercial, descripcion, estado, id_empresa " .
                   "FROM CONTRATO_TIPO_PUESTO_COMERCIAL " .
                   "$whereClause ORDER BY descripcion LIMIT :limit_param OFFSET :offset_param;";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id_empresa', Globales::$o_id_empresa, PDO::PARAM_INT);
            $stmt->bindParam(':limit_param', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset_param', $offset, PDO::PARAM_INT);
            
            if ($search) {
                $searchParam = "%$search%";
                $stmt->bindParam(':search', $searchParam, PDO::PARAM_STR);
            }
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en TipoPuestoComercial::getPaginatedRecords: " . $e->getMessage());
            return [];
        }
    }
}
