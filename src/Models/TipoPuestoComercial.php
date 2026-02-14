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

    // El método getAll() se mantiene por si hay otras partes del código que lo usen,
    // pero la paginación usará los nuevos métodos.
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

    public function getTotalRecords(): int {
        try {
            $stmt = $this->pdo->prepare(
                "SELECT COUNT(*) FROM CONTRATO_TIPO_PUESTO_COMERCIAL WHERE id_empresa = ?"
            );
            $stmt->bindParam(1, Globales::$o_id_empresa, PDO::PARAM_INT);
            $stmt->execute();
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Error en TipoPuestoComercial::getTotalRecords: " . $e->getMessage());
            return 0;
        }
    }

    public function getPaginatedRecords(int $limit, int $offset): array {
        try {
            $sql = "SELECT id_tipo_puesto_comercial, descripcion, estado, id_empresa " .
                   "FROM CONTRATO_TIPO_PUESTO_COMERCIAL " .
                   "WHERE id_empresa = ? ORDER BY descripcion LIMIT ? OFFSET ?;";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(1, Globales::$o_id_empresa, PDO::PARAM_INT);
            $stmt->bindParam(2, $limit, PDO::PARAM_INT);
            $stmt->bindParam(3, $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en TipoPuestoComercial::getPaginatedRecords: " . $e->getMessage());
            return [];
        }
    }
}
