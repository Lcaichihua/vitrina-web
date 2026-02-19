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
}
