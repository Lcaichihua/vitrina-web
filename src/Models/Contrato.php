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

    // Función auxiliar para construir la parte FROM y JOINs de la consulta
    private function buildBaseQuery(bool $isCount = false): string {
        $selectClause = $isCount ? "COUNT(ct.id_contrato)" : "
            ct.id_contrato AS id_doc_contrato,
            ct.numero_contrato,
            ct.id_sucursal,
            tp.id_tipo_puesto_comercial,
            ct.id_arrendatario,
            CONCAT(CTC.abreviatura,'-', COALESCE(CPC.descripcion,'')) AS tipo_contrato,
            ct.id_tipo_contrato,
            ar.docident_id AS id_tipo_documento,
            ar.numero_documento,
            ar.nombre_concesionario_razon_social,
            IF (TRIM(ar.nombres) = '',
                ar.nombre_concesionario_razon_social,
                CONCAT(ar.apellidos,' ',ar.nombres)
            ) AS apellidos_nombres,
            CASE WHEN IFNULL(cc.importe_pie_ingreso,0) > 0 THEN 'SI' ELSE 'NO' END AS pie_ingreso,
            ar.apellidos, ar.nombres,
            cc.importe_pie_ingreso,
            (cc.importe_canastilla * IFNULL(cn.cant_canastillas,0)) AS importe_por_canastilla,
            cc.porcentaje_renta_var AS porcentaje_renta_variable,
            cc.importe_contraprest AS importe_contraprestacion,
            DATE_FORMAT(ct.inicio_contrato,'%d/%m/%Y') AS inicio_contrato,
            DATE_FORMAT(ct.fin_contrato,'%d/%m/%Y') AS fin_contrato,
            DATE_FORMAT(cc.cobro_inicio,'%d/%m/%Y') AS inicio_cobro,
            DATE_FORMAT(cc.cobro_fin,'%d/%m/%Y') AS fin_cobro,
            ct.observaciones,
            ct.estado,
            ct.activo,
            '' AS verPuesto,
            ct.nro_meses,
            '' AS descargar,
            '' AS subir,
            ct.ruta_documento_ftp,
            DATE_FORMAT(ct.fecha_finalizacion,'%d/%m/%Y') AS fecha_finalizacion,
            cc.economato_tipo AS tipo_economato,
            cc.espacios_economato,
            cc.importe_economato
        ";

        return "
            SELECT {$selectClause}
            FROM CONTRATO ct
            JOIN CONTRATO_CONDICION cc ON cc.id_condicion = (
                   SELECT cc2.id_condicion
                     FROM CONTRATO_CONDICION cc2
                    WHERE cc2.id_contrato = ct.id_contrato
                    ORDER BY
                      (CURDATE() BETWEEN cc2.vigencia_inicio AND cc2.vigencia_fin) DESC,
                      cc2.vigencia_inicio DESC
                    LIMIT 1
                 )
            JOIN CONTRATO_TIPO_CONTRATO_MOD CTC ON CTC.id_tipo_contrato = ct.id_tipo_contrato
            JOIN CONTRATO_ARRENDATARIO ar ON ar.id_arrendatario = ct.id_arrendatario AND ar.id_empresa = :id_empresa_arrendatario

            LEFT JOIN (
                SELECT
                    cp.id_contrato,
                    MIN(pc.id_tipo_puesto_comercial) AS id_tipo_puesto_comercial
                FROM CONTRATO_PUESTO cp
                JOIN CONTRATO_PUESTO_COMERCIAL pc ON pc.id_puesto_comercial = cp.id_puesto_comercial
                WHERE (cp.baja IS NULL OR CURDATE() < cp.baja)
                  AND pc.id_empresa = :id_empresa_pc1
                  AND (pc.estado IS NULL OR pc.estado = 1)
                GROUP BY cp.id_contrato
            ) tp ON tp.id_contrato = ct.id_contrato

            LEFT JOIN CONTRATO_TIPO_PUESTO_COMERCIAL CPC ON CPC.id_tipo_puesto_comercial = tp.id_tipo_puesto_comercial AND CPC.id_empresa = :id_empresa_cpc

            LEFT JOIN (
                SELECT
                  cp.id_contrato,
                  COUNT(*) AS cant_canastillas
                FROM CONTRATO_PUESTO cp
                JOIN CONTRATO_PUESTO_COMERCIAL pc ON pc.id_puesto_comercial = cp.id_puesto_comercial
                JOIN CONTRATO_TIPO_PUESTO_COMERCIAL t ON t.id_tipo_puesto_comercial = pc.id_tipo_puesto_comercial AND t.id_empresa = :id_empresa_pc2
                WHERE (cp.baja IS NULL OR CURDATE() < cp.baja)
                  AND pc.id_empresa = :id_empresa_pc3
                  AND (pc.estado IS NULL OR pc.estado = 1)
                  AND UPPER(t.descripcion) = 'CANASTILLA'
                GROUP BY cp.id_contrato
            ) cn ON cn.id_contrato = ct.id_contrato
            WHERE ct.id_empresa = :id_empresa_ct
        ";
    }

    public function getTotalRecords(
        ?int $idTipoContrato,
        ?string $pieIngreso,
        ?string $numeroContrato,
        ?string $arrendatario
    ): int {
        try {
            $sql = $this->buildBaseQuery(true); // $isCount = true
            $params = [];

            // Bind comunes
            $params[':id_empresa_arrendatario'] = Globales::$o_id_empresa;
            $params[':id_empresa_pc1'] = Globales::$o_id_empresa;
            $params[':id_empresa_cpc'] = Globales::$o_id_empresa;
            $params[':id_empresa_pc2'] = Globales::$o_id_empresa;
            $params[':id_empresa_pc3'] = Globales::$o_id_empresa;
            $params[':id_empresa_ct'] = Globales::$o_id_empresa;

            // Filtros condicionales
            if ($idTipoContrato !== null) {
                $sql .= " AND ct.id_tipo_contrato = :id_tipo_contrato";
                $params[':id_tipo_contrato'] = $idTipoContrato;
            }
            if ($pieIngreso !== null) {
                $sql .= " AND (CASE WHEN IFNULL(cc.importe_pie_ingreso,0) > 0 THEN 'SI' ELSE 'NO' END) = :pie_ingreso";
                $params[':pie_ingreso'] = $pieIngreso;
            }
            if ($numeroContrato !== null) {
                $sql .= " AND ct.numero_contrato LIKE :numero_contrato";
                $params[':numero_contrato'] = "%{$numeroContrato}%";
            }
            if ($arrendatario !== null) {
                $sql .= " AND (IF (TRIM(ar.nombres) = '', ar.nombre_concesionario_razon_social, CONCAT(ar.apellidos,' ',ar.nombres))) LIKE :arrendatario";
                $params[':arrendatario'] = "%{$arrendatario}%";
            }

            $stmt = $this->pdo->prepare($sql);
            foreach ($params as $key => &$val) {
                $stmt->bindParam($key, $val, is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR);
            }
            $stmt->execute();
            return (int) $stmt->fetchColumn();
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
            $sql = $this->buildBaseQuery(); // $isCount = false
            $params = [];

            // Bind comunes
            $params[':id_empresa_arrendatario'] = Globales::$o_id_empresa;
            $params[':id_empresa_pc1'] = Globales::$o_id_empresa;
            $params[':id_empresa_cpc'] = Globales::$o_id_empresa;
            $params[':id_empresa_pc2'] = Globales::$o_id_empresa;
            $params[':id_empresa_pc3'] = Globales::$o_id_empresa;
            $params[':id_empresa_ct'] = Globales::$o_id_empresa;

            // Filtros condicionales
            if ($idTipoContrato !== null) {
                $sql .= " AND ct.id_tipo_contrato = :id_tipo_contrato";
                $params[':id_tipo_contrato'] = $idTipoContrato;
            }
            if ($pieIngreso !== null) {
                $sql .= " AND (CASE WHEN IFNULL(cc.importe_pie_ingreso,0) > 0 THEN 'SI' ELSE 'NO' END) = :pie_ingreso";
                $params[':pie_ingreso'] = $pieIngreso;
            }
            if ($numeroContrato !== null) {
                $sql .= " AND ct.numero_contrato LIKE :numero_contrato";
                $params[':numero_contrato'] = "%{$numeroContrato}%";
            }
            if ($arrendatario !== null) {
                $sql .= " AND (IF (TRIM(ar.nombres) = '', ar.nombre_concesionario_razon_social, CONCAT(ar.apellidos,' ',ar.nombres))) LIKE :arrendatario";
                $params[':arrendatario'] = "%{$arrendatario}%";
            }

            $sql .= " ORDER BY ct.id_contrato DESC LIMIT :limit_param OFFSET :offset_param;";

            $stmt = $this->pdo->prepare($sql);
            foreach ($params as $key => &$val) {
                $stmt->bindParam($key, $val, is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR);
            }
            $stmt->bindParam(':limit_param', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset_param', $offset, PDO::PARAM_INT);
            $stmt->execute();
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
