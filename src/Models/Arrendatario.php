<?php
namespace Vitrina\Models;

use Database;
use PDO;
use PDOException;
use Exception;
use Vitrina\Lib\Globales;

class Arrendatario {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::connect();
    }

    public function getAllRecords(int $filtroEstado = -1): array {
        try {
            $stmt = $this->pdo->prepare("CALL USP_Listar_Contrato_Arrendatarios(?, ?)");
            $stmt->bindParam(1, Globales::$o_id_empresa, PDO::PARAM_INT);
            $stmt->bindParam(2, $filtroEstado, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en Arrendatario::getAllRecords: " . $e->getMessage());
            return [];
        }
    }

    public function getTotalRecords(int $filtroEstado = 1, ?string $search = null): int {
        try {
            $allRecords = $this->getAllRecords($filtroEstado);
            
            if ($search) {
                $searchLower = strtolower($search);
                $allRecords = array_filter($allRecords, function($record) use ($searchLower) {
                    return str_contains(strtolower($record['numero_documento'] ?? ''), $searchLower)
                        || str_contains(strtolower($record['nombre_concesionario_razon_social'] ?? ''), $searchLower)
                        || str_contains(strtolower($record['apellidos'] ?? ''), $searchLower)
                        || str_contains(strtolower($record['nombres'] ?? ''), $searchLower)
                        || str_contains(strtolower($record['direccion'] ?? ''), $searchLower);
                });
            }
            
            return count($allRecords);
        } catch (PDOException $e) {
            error_log("Error en Arrendatario::getTotalRecords: " . $e->getMessage());
            return 0;
        }
    }

    public function getPaginatedRecords(int $filtroEstado = 1, int $limit, int $offset, ?string $search = null): array {
        try {
            $allRecords = $this->getAllRecords($filtroEstado);
            
            if ($search) {
                $searchLower = strtolower($search);
                $allRecords = array_filter($allRecords, function($record) use ($searchLower) {
                    return str_contains(strtolower($record['numero_documento'] ?? ''), $searchLower)
                        || str_contains(strtolower($record['nombre_concesionario_razon_social'] ?? ''), $searchLower)
                        || str_contains(strtolower($record['apellidos'] ?? ''), $searchLower)
                        || str_contains(strtolower($record['nombres'] ?? ''), $searchLower)
                        || str_contains(strtolower($record['direccion'] ?? ''), $searchLower);
                });
            }
            
            return array_slice($allRecords, $offset, $limit);
        } catch (PDOException $e) {
            error_log("Error en Arrendatario::getPaginatedRecords: " . $e->getMessage());
            return [];
        }
    }

    public function getTiposDocumento(): array {
        try {
            $stmt = $this->pdo->query("SELECT docident_id, abreviatura FROM TIPODOCIDENTIDAD ORDER BY docident_id ASC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en Arrendatario::getTiposDocumento: " . $e->getMessage());
            return [];
        }
    }

    public function getDepartamentos(): array {
        try {
            $stmt = $this->pdo->query("SELECT depaid, departamento FROM ubdepartamento ORDER BY depaid ASC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en Arrendatario::getDepartamentos: " . $e->getMessage());
            return [];
        }
    }

    public function getProvincias(int $depaid): array {
        try {
            $stmt = $this->pdo->prepare("SELECT provid, provincia FROM ubprovincia WHERE depaid = :depaid ORDER BY provid ASC");
            $stmt->bindParam(':depaid', $depaid, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en Arrendatario::getProvincias: " . $e->getMessage());
            return [];
        }
    }

    public function getDistritos(int $provid): array {
        try {
            $stmt = $this->pdo->prepare("SELECT distid, distrito FROM ubdistrito WHERE provid = :provid ORDER BY distid ASC");
            $stmt->bindParam(':provid', $provid, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en Arrendatario::getDistritos: " . $e->getMessage());
            return [];
        }
    }

    public function create(int $tipoDocId, string $numeroDoc, string $apellidos, string $nombres, string $razonSocial, int $depaid, int $provid, int $distid, string $direccion = '', int $estado = 1): int {
        try {
            $sql = "INSERT INTO CONTRATO_ARRENDATARIO (docident_id, numero_documento, nombres, apellidos, nombre_concesionario_razon_social, depaid, provid, distid, direccion, estado, id_empresa, fechaing) 
                    VALUES (:doc_id, :numero, :nombres, :apellidos, :razon_social, :depaid, :provid, :distid, :direccion, :estado, :id_empresa, NOW())";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':doc_id', $tipoDocId, PDO::PARAM_INT);
            $stmt->bindParam(':numero', $numeroDoc, PDO::PARAM_STR);
            $stmt->bindParam(':nombres', $nombres, PDO::PARAM_STR);
            $stmt->bindParam(':apellidos', $apellidos, PDO::PARAM_STR);
            $stmt->bindParam(':razon_social', $razonSocial, PDO::PARAM_STR);
            $stmt->bindParam(':depaid', $depaid, PDO::PARAM_INT);
            $stmt->bindParam(':provid', $provid, PDO::PARAM_INT);
            $stmt->bindParam(':distid', $distid, PDO::PARAM_INT);
            $stmt->bindParam(':direccion', $direccion, PDO::PARAM_STR);
            $stmt->bindParam(':estado', $estado, PDO::PARAM_INT);
            $stmt->bindParam(':id_empresa', Globales::$o_id_empresa, PDO::PARAM_INT);
            $stmt->execute();
            return (int)$this->pdo->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error en Arrendatario::create: " . $e->getMessage());
            throw new Exception("Error al crear arrendatario");
        }
    }

    public function update(int $id, int $tipoDocId, string $numeroDoc, string $apellidos, string $nombres, string $razonSocial, int $depaid, int $provid, int $distid, string $direccion = '', int $estado = 1): bool {
        try {
            $sql = "UPDATE CONTRATO_ARRENDATARIO SET docident_id = :doc_id, numero_documento = :numero, 
                    nombres = :nombres, apellidos = :apellidos, nombre_concesionario_razon_social = :razon_social,
                    depaid = :depaid, provid = :provid, distid = :distid,
                    direccion = :direccion, estado = :estado, fechamod = NOW()
                    WHERE id_arrendatario = :id AND id_empresa = :id_empresa";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':doc_id', $tipoDocId, PDO::PARAM_INT);
            $stmt->bindParam(':numero', $numeroDoc, PDO::PARAM_STR);
            $stmt->bindParam(':nombres', $nombres, PDO::PARAM_STR);
            $stmt->bindParam(':apellidos', $apellidos, PDO::PARAM_STR);
            $stmt->bindParam(':razon_social', $razonSocial, PDO::PARAM_STR);
            $stmt->bindParam(':depaid', $depaid, PDO::PARAM_INT);
            $stmt->bindParam(':provid', $provid, PDO::PARAM_INT);
            $stmt->bindParam(':distid', $distid, PDO::PARAM_INT);
            $stmt->bindParam(':direccion', $direccion, PDO::PARAM_STR);
            $stmt->bindParam(':estado', $estado, PDO::PARAM_INT);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':id_empresa', Globales::$o_id_empresa, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en Arrendatario::update: " . $e->getMessage());
            throw new Exception("Error al actualizar arrendatario");
        }
    }

    public function delete(int $id): bool {
        try {
            $sql = "DELETE FROM CONTRATO_ARRENDATARIO WHERE id_arrendatario = :id AND id_empresa = :id_empresa";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':id_empresa', Globales::$o_id_empresa, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en Arrendatario::delete: " . $e->getMessage());
            throw new Exception("Error al eliminar arrendatario");
        }
    }
}
