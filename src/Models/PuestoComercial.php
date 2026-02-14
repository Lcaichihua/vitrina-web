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
        try {
            // El filtroEstado = 1 para mostrar solo los vigentes (activos)
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
}
