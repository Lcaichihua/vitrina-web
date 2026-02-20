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

    public function getTotalRecords(int $filtroEstado = 1, ?string $search = null): int {
        try {
            $whereClause = "WHERE a.id_empresa = :id_empresa AND (:filtro_estado_check = -1 OR IFNULL(a.estado,1) = :filtro_estado_value)";
            
            if ($search) {
                $whereClause .= " AND (
                    a.numero_documento LIKE :search1 
                    OR a.nombres LIKE :search2 
                    OR a.apellidos LIKE :search3 
                    OR a.direccion LIKE :search4
                )";
            }

            $sql = "SELECT COUNT(a.id_arrendatario)
                    FROM CONTRATO_ARRENDATARIO a
                    INNER JOIN TIPODOCIDENTIDAD b ON a.docident_id = b.docident_id
                    $whereClause";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id_empresa', Globales::$o_id_empresa, PDO::PARAM_INT);
            $stmt->bindParam(':filtro_estado_check', $filtroEstado, PDO::PARAM_INT);
            $stmt->bindParam(':filtro_estado_value', $filtroEstado, PDO::PARAM_INT);
            
            if ($search) {
                $searchParam = "%$search%";
                $stmt->bindParam(':search1', $searchParam, PDO::PARAM_STR);
                $stmt->bindParam(':search2', $searchParam, PDO::PARAM_STR);
                $stmt->bindParam(':search3', $searchParam, PDO::PARAM_STR);
                $stmt->bindParam(':search4', $searchParam, PDO::PARAM_STR);
            }
            
            $stmt->execute();
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Error en Arrendatario::getTotalRecords: " . $e->getMessage());
            return 0;
        }
    }

    public function getPaginatedRecords(int $filtroEstado = 1, int $limit, int $offset, ?string $search = null): array {
        try {
            $whereClause = "WHERE a.id_empresa = :id_empresa AND (:filtro_estado_check = -1 OR IFNULL(a.estado,1) = :filtro_estado_value)";
            
            if ($search) {
                $whereClause .= " AND (
                    a.numero_documento LIKE :search1 
                    OR a.nombres LIKE :search2 
                    OR a.apellidos LIKE :search3 
                    OR a.direccion LIKE :search4
                )";
            }

            $sql = "SELECT
                        a.id_arrendatario,
                        b.abreviatura AS tipo_documento,
                        a.numero_documento,
                        a.nombres,
                        a.apellidos,
                        a.direccion,
                        DATE_FORMAT(a.fechaing, '%d/%m/%Y') AS desde,
                        DATE_FORMAT(a.fechamod, '%d/%m/%Y') AS hasta,
                        a.estado
                    FROM CONTRATO_ARRENDATARIO a
                    INNER JOIN TIPODOCIDENTIDAD b ON a.docident_id = b.docident_id
                    $whereClause
                    ORDER BY a.apellidos, a.nombres
                    LIMIT :limit_param OFFSET :offset_param;";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id_empresa', Globales::$o_id_empresa, PDO::PARAM_INT);
            $stmt->bindParam(':filtro_estado_check', $filtroEstado, PDO::PARAM_INT);
            $stmt->bindParam(':filtro_estado_value', $filtroEstado, PDO::PARAM_INT);
            $stmt->bindParam(':limit_param', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset_param', $offset, PDO::PARAM_INT);
            
            if ($search) {
                $searchParam = "%$search%";
                $stmt->bindParam(':search1', $searchParam, PDO::PARAM_STR);
                $stmt->bindParam(':search2', $searchParam, PDO::PARAM_STR);
                $stmt->bindParam(':search3', $searchParam, PDO::PARAM_STR);
                $stmt->bindParam(':search4', $searchParam, PDO::PARAM_STR);
            }
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en Arrendatario::getPaginatedRecords: " . $e->getMessage());
            return [];
        }
    }

    public function create(string $tipoDoc, string $numeroDoc, string $apellidos, string $nombres, string $direccion = '', int $estado = 1): int {
        try {
            $sqlDocId = "SELECT docident_id FROM TIPODOCIDENTIDAD WHERE abreviatura = :abrev LIMIT 1";
            $stmtDocId = $this->pdo->prepare($sqlDocId);
            $stmtDocId->bindParam(':abrev', $tipoDoc, PDO::PARAM_STR);
            $stmtDocId->execute();
            $docId = $stmtDocId->fetchColumn();

            $sql = "INSERT INTO CONTRATO_ARRENDATARIO (docident_id, numero_documento, nombres, apellidos, direccion, estado, id_empresa, fechaing) 
                    VALUES (:doc_id, :numero, :nombres, :apellidos, :direccion, :estado, :id_empresa, NOW())";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':doc_id', $docId, PDO::PARAM_INT);
            $stmt->bindParam(':numero', $numeroDoc, PDO::PARAM_STR);
            $stmt->bindParam(':nombres', $nombres, PDO::PARAM_STR);
            $stmt->bindParam(':apellidos', $apellidos, PDO::PARAM_STR);
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

    public function update(int $id, string $tipoDoc, string $numeroDoc, string $apellidos, string $nombres, string $direccion = '', int $estado = 1): bool {
        try {
            $sqlDocId = "SELECT docident_id FROM TIPODOCIDENTIDAD WHERE abreviatura = :abrev LIMIT 1";
            $stmtDocId = $this->pdo->prepare($sqlDocId);
            $stmtDocId->bindParam(':abrev', $tipoDoc, PDO::PARAM_STR);
            $stmtDocId->execute();
            $docId = $stmtDocId->fetchColumn();

            $sql = "UPDATE CONTRATO_ARRENDATARIO SET docident_id = :doc_id, numero_documento = :numero, 
                    nombres = :nombres, apellidos = :apellidos, direccion = :direccion, estado = :estado, fechamod = NOW()
                    WHERE id_arrendatario = :id AND id_empresa = :id_empresa";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':doc_id', $docId, PDO::PARAM_INT);
            $stmt->bindParam(':numero', $numeroDoc, PDO::PARAM_STR);
            $stmt->bindParam(':nombres', $nombres, PDO::PARAM_STR);
            $stmt->bindParam(':apellidos', $apellidos, PDO::PARAM_STR);
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
