-- USP_Listar_Contratos_v2_Paginado (basado en el original v2 con paginación)
DROP PROCEDURE IF EXISTS USP_Listar_Contratos_v2_Paginado;

CREATE DEFINER=`root`@`localhost` PROCEDURE `USP_Listar_Contratos_v2_Paginado`(
    IN p_id_empresa INT,
    IN p_id_tipo_contrato INT,
    IN p_pie_ingreso VARCHAR(20),
    IN p_numero_contrato VARCHAR(50),
    IN p_arrendatario VARCHAR(200),
    IN p_limit INT,
    IN p_offset INT,
    IN p_contar_solo INT
)
BEGIN
    DECLARE v_prev_coll VARCHAR(64);
    SELECT @@collation_connection INTO v_prev_coll;
    SET SESSION collation_connection = 'utf8mb4_general_ci';

    IF p_contar_solo = 1 THEN
        SELECT COUNT(ct.id_contrato) AS total
        FROM CONTRATO ct
        JOIN CONTRATO_CONDICION cc
          ON cc.id_condicion = (
               SELECT cc2.id_condicion
                 FROM CONTRATO_CONDICION cc2
                WHERE cc2.id_contrato = ct.id_contrato
                ORDER BY
                  (CURDATE() BETWEEN cc2.vigencia_inicio AND cc2.vigencia_fin) DESC,
                  cc2.vigencia_inicio DESC
               LIMIT 1
            )
        JOIN CONTRATO_TIPO_CONTRATO_MOD CTC ON CTC.id_tipo_contrato = ct.id_tipo_contrato
        JOIN CONTRATO_ARRENDATARIO ar ON ar.id_arrendatario = ct.id_arrendatario AND ar.id_empresa = p_id_empresa
        WHERE ct.id_empresa = p_id_empresa
        AND (p_id_tipo_contrato IS NULL OR ct.id_tipo_contrato = p_id_tipo_contrato)
        AND (p_pie_ingreso IS NULL OR
             (CASE WHEN IFNULL(cc.importe_pie_ingreso,0) > 0 THEN 'SI' ELSE 'NO' END)
               COLLATE utf8mb4_general_ci = p_pie_ingreso COLLATE utf8mb4_general_ci)
        AND (p_numero_contrato IS NULL OR
             ct.numero_contrato COLLATE utf8mb4_general_ci LIKE CONCAT('%', p_numero_contrato COLLATE utf8mb4_general_ci, '%'))
        AND (p_arrendatario IS NULL OR 
             (IF(TRIM(ar.nombres) = '', ar.nombre_concesionario_razon_social, CONCAT(ar.apellidos,' ',ar.nombres))
             COLLATE utf8mb4_general_ci) LIKE CONCAT('%', p_arrendatario COLLATE utf8mb4_general_ci, '%'));
    ELSE
        SELECT
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
            IF(TRIM(ar.nombres) = '', ar.nombre_concesionario_razon_social, CONCAT(ar.apellidos,' ',ar.nombres)) AS apellidos_nombres,
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
        FROM CONTRATO ct
        JOIN CONTRATO_CONDICION cc
          ON cc.id_condicion = (
               SELECT cc2.id_condicion
                 FROM CONTRATO_CONDICION cc2
                WHERE cc2.id_contrato = ct.id_contrato
                ORDER BY
                  (CURDATE() BETWEEN cc2.vigencia_inicio AND cc2.vigencia_fin) DESC,
                  cc2.vigencia_inicio DESC
               LIMIT 1
            )
        JOIN CONTRATO_TIPO_CONTRATO_MOD CTC ON CTC.id_tipo_contrato = ct.id_tipo_contrato
        JOIN CONTRATO_ARRENDATARIO ar ON ar.id_arrendatario = ct.id_arrendatario AND ar.id_empresa = p_id_empresa

        LEFT JOIN (
            SELECT cp.id_contrato, MIN(pc.id_tipo_puesto_comercial) AS id_tipo_puesto_comercial
            FROM CONTRATO_PUESTO cp
            JOIN CONTRATO_PUESTO_COMERCIAL pc ON pc.id_puesto_comercial = cp.id_puesto_comercial
            WHERE (cp.baja IS NULL OR CURDATE() < cp.baja)
              AND pc.id_empresa = p_id_empresa
              AND (pc.estado IS NULL OR pc.estado = 1)
            GROUP BY cp.id_contrato
        ) tp ON tp.id_contrato = ct.id_contrato

        LEFT JOIN CONTRATO_TIPO_PUESTO_COMERCIAL CPC ON CPC.id_tipo_puesto_comercial = tp.id_tipo_puesto_comercial AND CPC.id_empresa = p_id_empresa

        LEFT JOIN (
            SELECT cp.id_contrato, COUNT(*) AS cant_canastillas
            FROM CONTRATO_PUESTO cp
            JOIN CONTRATO_PUESTO_COMERCIAL pc ON pc.id_puesto_comercial = cp.id_puesto_comercial
            JOIN CONTRATO_TIPO_PUESTO_COMERCIAL t ON t.id_tipo_puesto_comercial = pc.id_tipo_puesto_comercial AND t.id_empresa = p_id_empresa
            WHERE (cp.baja IS NULL OR CURDATE() < cp.baja)
              AND pc.id_empresa = p_id_empresa
              AND (pc.estado IS NULL OR pc.estado = 1)
              AND UPPER(t.descripcion) = 'CANASTILLA'
            GROUP BY cp.id_contrato
        ) cn ON cn.id_contrato = ct.id_contrato

        WHERE ct.id_empresa = p_id_empresa
        AND (p_id_tipo_contrato IS NULL OR ct.id_tipo_contrato = p_id_tipo_contrato)
        AND (p_pie_ingreso IS NULL OR
             (CASE WHEN IFNULL(cc.importe_pie_ingreso,0) > 0 THEN 'SI' ELSE 'NO' END)
               COLLATE utf8mb4_general_ci = p_pie_ingreso COLLATE utf8mb4_general_ci)
        AND (p_numero_contrato IS NULL OR
             ct.numero_contrato COLLATE utf8mb4_general_ci LIKE CONCAT('%', p_numero_contrato COLLATE utf8mb4_general_ci, '%'))
        AND (p_arrendatario IS NULL OR 
             (IF(TRIM(ar.nombres) = '', ar.nombre_concesionario_razon_social, CONCAT(ar.apellidos,' ',ar.nombres))
             COLLATE utf8mb4_general_ci) LIKE CONCAT('%', p_arrendatario COLLATE utf8mb4_general_ci, '%'))

        ORDER BY ct.id_contrato DESC
        LIMIT p_limit OFFSET p_offset;
    END IF;

    SET SESSION collation_connection = v_prev_coll;
END;
