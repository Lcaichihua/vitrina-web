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

    public function getTotalRecords(
        ?int $idTipoContrato,
        ?string $pieIngreso,
        ?string $numeroContrato,
        ?string $arrendatario
    ): int {
        try {
            $p_id_tipo_contrato = $idTipoContrato !== null ? (string)$idTipoContrato : 'NULL';
            $p_pie_ingreso = $pieIngreso !== null ? "'" . addslashes($pieIngreso) . "'" : 'NULL';
            $p_numero_contrato = $numeroContrato !== null ? "'" . addslashes($numeroContrato) . "'" : 'NULL';
            $p_arrendatario = $arrendatario !== null ? "'" . addslashes($arrendatario) . "'" : 'NULL';
            
            $sql = "CALL wptvxhei_ventas.USP_Listar_Contratos_v2_Paginado(
                " . (int)Globales::$o_id_empresa . ",
                $p_id_tipo_contrato,
                $p_pie_ingreso,
                $p_numero_contrato,
                $p_arrendatario,
                0, 0, 1
            )";
            
            $stmt = $this->pdo->query($sql);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result ? (int) $result['total'] : 0;
        } catch (PDOException $e) {
            error_log("Error en Contrato::getTotalRecords: " . $e->getMessage());
            return 0;
        }
    }

    public function getPaginatedRecords(
        ?int $idTipoContrato,
        ?string $pieIngreso,
        ?string $numeroContrato,
        ?string $arrendatario,
        int $limit,
        int $offset
    ): array {
        try {
            $p_id_tipo_contrato = $idTipoContrato !== null ? (string)$idTipoContrato : 'NULL';
            $p_pie_ingreso = $pieIngreso !== null ? "'" . addslashes($pieIngreso) . "'" : 'NULL';
            $p_numero_contrato = $numeroContrato !== null ? "'" . addslashes($numeroContrato) . "'" : 'NULL';
            $p_arrendatario = $arrendatario !== null ? "'" . addslashes($arrendatario) . "'" : 'NULL';
            
            $sql = "CALL wptvxhei_ventas.USP_Listar_Contratos_v2_Paginado(
                " . (int)Globales::$o_id_empresa . ",
                $p_id_tipo_contrato,
                $p_pie_ingreso,
                $p_numero_contrato,
                $p_arrendatario,
                $limit, $offset, 0
            )";
            
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en Contrato::getPaginatedRecords: " . $e->getMessage());
            return [];
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
}
