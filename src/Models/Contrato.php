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
        $idTipoContrato,
        ?string $pieIngreso,
        ?string $numeroContrato,
        ?string $arrendatario,
        int $limit,
        int $offset
    ): array {
        try {
            // Manejar múltiples tipos de contrato
            $tiposArray = null;
            if (is_array($idTipoContrato) && count($idTipoContrato) > 0) {
                $tiposArray = $idTipoContrato;
                $idTipoContrato = null;
            } elseif ($idTipoContrato !== null) {
                $idTipoContrato = (int)$idTipoContrato;
            }
            
            $p_id_tipo_contrato = $idTipoContrato !== null ? (string)$idTipoContrato : 'NULL';
            $p_pie_ingreso = $pieIngreso !== null ? "'" . addslashes($pieIngreso) . "'" : 'NULL';
            $p_numero_contrato = $numeroContrato !== null ? "'" . addslashes($numeroContrato) . "'" : 'NULL';
            $p_arrendatario = $arrendatario !== null ? "'" . addslashes($arrendatario) . "'" : 'NULL';
            
            // Si hay múltiples tipos, hacer llamada por cada tipo y combinar resultados
            if ($tiposArray !== null && count($tiposArray) > 0) {
                $allData = [];
                $total = 0;
                
                foreach ($tiposArray as $tipoId) {
                    $p_id_tipo = (int)$tipoId;
                    // Obtener todos los registros sin paginación para combinar
                    $sql = "CALL wptvxhei_ventas.USP_Listar_Contratos_v2_Paginado2(
                        " . (int)Globales::$o_id_empresa . ",
                        $p_id_tipo,
                        $p_pie_ingreso,
                        $p_numero_contrato,
                        $p_arrendatario,
                        10000, 0
                    )";
                    
                    $stmt = $this->pdo->query($sql);
                    
                    $totalResult = $stmt->fetch(PDO::FETCH_ASSOC);
                    $total += $totalResult ? (int)$totalResult['total'] : 0;
                    
                    $stmt->nextRowset();
                    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $allData = array_merge($allData, $data);
                }
                
                // Aplicar paginación en memoria
                $paginatedData = array_slice($allData, $offset, $limit);
                
                return [
                    'total' => $total,
                    'data' => $paginatedData
                ];
            }
            
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

    public function getSucursales(): array {
        try {
            $stmt = $this->pdo->query("SELECT sucursalid, descripcion FROM sucursal WHERE id_empresa = " . (int)Globales::$o_id_empresa . " ORDER BY descripcion");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en Contrato::getSucursales: " . $e->getMessage());
            return [];
        }
    }

    public function getTiposPuestoComercial(): array {
        try {
            $stmt = $this->pdo->query("SELECT id_tipo_puesto_comercial, descripcion FROM CONTRATO_TIPO_PUESTO_COMERCIAL WHERE id_empresa = " . (int)Globales::$o_id_empresa . " AND estado = 1 ORDER BY descripcion");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en Contrato::getTiposPuestoComercial: " . $e->getMessage());
            return [];
        }
    }

    public function getPuestosComercialesParaEdicion(?int $sucursalId, ?int $tipoPuestoId, ?int $contratoId = null): array {
        try {
            $p_sucursal = $sucursalId !== null ? (int)$sucursalId : 'NULL';
            $p_tipo_puesto = $tipoPuestoId !== null ? (int)$tipoPuestoId : 'NULL';
            $p_contrato = $contratoId !== null ? (int)$contratoId : 'NULL';

            $sql = "CALL wptvxhei_ventas.USP_PuestoComercial_ListarParaEdicion_Hoy(
                " . (int)Globales::$o_id_empresa . ",
                $p_sucursal,
                $p_tipo_puesto,
                $p_contrato
            )";

            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en Contrato::getPuestosComercialesParaEdicion: " . $e->getMessage());
            return [];
        }
    }

    public function buscarArrendatarios(?string $busqueda): array {
        try {
            $p_buscar = $busqueda !== null && !empty($busqueda) ? "'" . addslashes($busqueda) . "'" : "'%'";

            $sql = "SELECT 
                        a.id_arrendatario,
                        a.docident_id,
                        t.abreviatura AS tipo_documento,
                        a.numero_documento,
                        CONCAT(a.apellidos, ' ', a.nombres) AS nombres,
                        a.nombre_concesionario_razon_social AS concessionsario
                    FROM CONTRATO_ARRENDATARIO a
                    INNER JOIN TIPODOCIDENTIDAD t ON a.docident_id = t.docident_id
                    WHERE a.id_empresa = " . (int)Globales::$o_id_empresa . "
                    AND a.estado = 1
                    AND (a.numero_documento LIKE $p_buscar 
                        OR a.apellidos LIKE $p_buscar 
                        OR a.nombres LIKE $p_buscar 
                        OR a.nombre_concesionario_razon_social LIKE $p_buscar)
                    ORDER BY a.apellidos, a.nombres
                    LIMIT 50";

            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en Contrato::buscarArrendatarios: " . $e->getMessage());
            return [];
        }
    }

    public function getArrendatarioById(int $id): ?array {
        try {
            $stmt = $this->pdo->query("SELECT 
                LPAD(id_arrendatario,7,'0') as id_arrendatario,
                docident_id,
                numero_documento,
                CONCAT(apellidos, ' ', nombres) as nombres,
                nombre_concesionario_razon_social
            FROM CONTRATO_ARRENDATARIO 
            WHERE id_arrendatario = " . (int)$id);
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: null;
        } catch (PDOException $e) {
            error_log("Error en Contrato::getArrendatarioById: " . $e->getMessage());
            return null;
        }
    }

    public function getTiposDocumento(): array {
        try {
            $stmt = $this->pdo->query("SELECT docident_id, descripcion, abreviatura FROM TIPODOCIDENTIDAD ORDER BY descripcion");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en Contrato::getTiposDocumento: " . $e->getMessage());
            return [];
        }
    }

    public function create(array $data): int {
        $this->pdo->beginTransaction();
        try {
            $idEmpresa = (int)Globales::$o_id_empresa;
            $usuario = $_SESSION['username'] ?? 'system';
            $now = date('Y-m-d H:i:s');

            $sql = "INSERT INTO wptvxhei_ventas.CONTRATO (
                id_empresa, numero_contrato, id_arrendador, id_arrendatario, 
                id_sucursal, id_tipo_contrato, inicio_contrato, fin_contrato,
                tipo_moneda, observaciones, estado, usuing, fechaing, activo, nro_meses
            ) VALUES (
                :id_empresa, :numero_contrato, :id_arrendador, :id_arrendatario,
                :id_sucursal, :id_tipo_contrato, :inicio_contrato, :fin_contrato,
                :tipo_moneda, :observaciones, :estado, :usuing, :fechaing, :activo, :nro_meses
            )";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':id_empresa' => $idEmpresa,
                ':numero_contrato' => $data['numero_contrato'],
                ':id_arrendador' => $data['id_arrendador'] ?? null,
                ':id_arrendatario' => $data['id_arrendatario'],
                ':id_sucursal' => $data['id_sucursal'],
                ':id_tipo_contrato' => $data['id_tipo_contrato'],
                ':inicio_contrato' => $data['inicio_contrato'],
                ':fin_contrato' => $data['fin_contrato'],
                ':tipo_moneda' => $data['tipo_moneda'] ?? 'SOLES',
                ':observaciones' => $data['observaciones'] ?? '',
                ':estado' => $data['estado'] ?? 'ACTIVO',
                ':usuing' => $usuario,
                ':fechaing' => $now,
                ':activo' => $data['activo'] ?? 1,
                ':nro_meses' => $data['nro_meses'] ?? 1
            ]);

            $idContrato = (int)$this->pdo->lastInsertId();

            $sqlCondicion = "INSERT INTO wptvxhei_ventas.CONTRATO_CONDICION (
                id_contrato, vigencia_inicio, vigencia_fin, porcentaje_renta_var,
                importe_contraprest, economato_tipo, importe_economato,
                importe_pie_ingreso, importe_canastilla, espacios_economato,
                origen, id_origen
            ) VALUES (
                :id_contrato, :vigencia_inicio, :vigencia_fin, :porcentaje_renta_var,
                :importe_contraprest, :economato_tipo, :importe_economato,
                :importe_pie_ingreso, :importe_canastilla, :espacios_economato,
                :origen, :id_origen
            )";

            $stmtCondicion = $this->pdo->prepare($sqlCondicion);
            $stmtCondicion->execute([
                ':id_contrato' => $idContrato,
                ':vigencia_inicio' => $data['inicio_contrato'],
                ':vigencia_fin' => $data['fin_contrato'],
                ':porcentaje_renta_var' => $data['porcentaje_renta_variable'] ?? 0,
                ':importe_contraprest' => $data['importe_contraprestacion'] ?? 0,
                ':economato_tipo' => $data['economato_tipo'] ?? 'FIJO',
                ':importe_economato' => $data['importe_economato'] ?? 0,
                ':importe_pie_ingreso' => $data['importe_pie_ingreso'] ?? 0,
                ':importe_canastilla' => $data['importe_canastilla'] ?? 0,
                ':espacios_economato' => $data['espacios_economato'] ?? 0,
                ':origen' => 'WEB',
                ':id_origen' => $idContrato
            ]);

            if (!empty($data['puestos'])) {
                $sqlPuesto = "INSERT INTO wptvxhei_ventas.CONTRATO_PUESTO (
                    id_contrato, id_puesto_comercial, alta, origen, id_origen, estado
                ) VALUES (
                    :id_contrato, :id_puesto_comercial, :alta, :origen, :id_origen, 1
                )";
                $stmtPuesto = $this->pdo->prepare($sqlPuesto);

                foreach ($data['puestos'] as $idPuesto) {
                    $stmtPuesto->execute([
                        ':id_contrato' => $idContrato,
                        ':id_puesto_comercial' => $idPuesto,
                        ':alta' => date('Y-m-d'),
                        ':origen' => 'WEB',
                        ':id_origen' => $idContrato
                    ]);
                }
            }

            $this->pdo->commit();
            return $idContrato;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Error en Contrato::create: " . $e->getMessage());
            throw new \Exception("Error al crear el contrato: " . $e->getMessage());
        }
    }

    public function getSiguienteNumeroContrato(): string {
        try {
            $stmt = $this->pdo->query("SELECT MAX(CAST(numero_contrato AS UNSIGNED)) as max_num FROM wptvxhei_ventas.CONTRATO WHERE id_empresa = " . (int)Globales::$o_id_empresa);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $next = ($result && $result['max_num']) ? (int)$result['max_num'] + 1 : 1;
            return str_pad($next, 8, '0', STR_PAD_LEFT);
        } catch (PDOException $e) {
            error_log("Error en Contrato::getSiguienteNumeroContrato: " . $e->getMessage());
            return str_pad(1, 8, '0', STR_PAD_LEFT);
        }
    }
}
