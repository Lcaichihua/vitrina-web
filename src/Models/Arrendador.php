<?php
namespace Vitrina\Models;

use Database;
use PDO;
use PDOException;
use Vitrina\Lib\Globales;

class Arrendador {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::connect();
    }

    public function getTotalRecords(): int {
        try {
            $sql = "SELECT COUNT(a.id_arrendador)
                    FROM CONTRATO_ARRENDADOR a
                    INNER JOIN TIPODOCIDENTIDAD b ON a.docident_id = b.docident_id
                    WHERE a.id_empresa = :id_empresa";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id_empresa', Globales::$o_id_empresa, PDO::PARAM_INT);
            $stmt->execute();
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Error en Arrendador::getTotalRecords: " . $e->getMessage());
            return 0;
        }
    }

    public function getPaginatedRecords(int $limit, int $offset): array {
        try {
            $sql = "SELECT a.id_arrendador, b.docident_id, b.abreviatura as abrev_doc, " .
                   "a.numero_documento, a.abreviatura, a.apellidos, a.nombres, a.direccion, ".
                   "DATE_FORMAT(a.fecha_desde, '%d/%m/%Y') as desde, DATE_FORMAT(a.fecha_hasta, '%d/%m/%Y') as hasta,".
                   "a.depaid, a.provid, a.distid " .
                   "FROM CONTRATO_ARRENDADOR a " .
                   "INNER JOIN TIPODOCIDENTIDAD b ON a.docident_id = b.docident_id " .
                   "WHERE a.id_empresa = :id_empresa " .
                   "ORDER BY a.apellidos, a.nombres LIMIT :limit_param OFFSET :offset_param;";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id_empresa', Globales::$o_id_empresa, PDO::PARAM_INT);
            $stmt->bindParam(':limit_param', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset_param', $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en Arrendador::getPaginatedRecords: " . $e->getMessage());
            return [];
        }
    }
}
