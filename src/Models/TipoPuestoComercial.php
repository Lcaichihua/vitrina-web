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
}
