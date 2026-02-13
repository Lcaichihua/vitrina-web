<?php
namespace Vitrina\Models;

use Database;
use PDO;
use PDOException;

class User {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::connect();
    }

    public function getEmpresas() {
        $stmt = $this->pdo->query("SELECT id_empresa, nombre_empresa FROM EMPRESA WHERE estado=1 ORDER BY cod_empresa ASC");
        return $stmt->fetchAll();
    }

    public function findByUsername($username, $ip, $empresaId) {
        try {
            // 1. Llamamos a tu SP existente para traer los datos generales
            $stmt = $this->pdo->prepare("CALL USP_CONSULTAR_USUARIO(?, ?, ?)");
            $stmt->execute([$username, $ip, $empresaId]);

            // --- Procesar Result Sets ---
            $userData = $stmt->fetch();

            // Si el SP no devuelve nada, retornamos null
            if (!$userData) {
                return null;
            }

            // Consumir el resto de los result sets para liberar la conexión
            $stmt->nextRowset(); $sucursalData = $stmt->fetch();
            $stmt->nextRowset(); $paramsData = $stmt->fetch();
            $stmt->nextRowset(); $cajaData = $stmt->fetch();
            
            // Se cierra el cursor y se anula el objeto statement para limpiar la conexión
            // antes de la siguiente consulta. Esto previene el error "Packets out of order".
            while ($stmt->nextRowset()) {}; // Consumir cualquier result set adicional
            $stmt->closeCursor();
            $stmt = null; // Anular el objeto statement

            // 2. [CORRECCIÓN] Consultar contrasenia_web usando el NOMBRE DE USUARIO
            // Esto es más robusto porque no depende de cómo se llame la columna ID en el SP
            $stmtWeb = $this->pdo->prepare("SELECT contrasenia_web, usuarioid FROM usuario WHERE nombreusuario = ?");
            $stmtWeb->execute([$username]);
            $webData = $stmtWeb->fetch();

            // Asignamos la contraseña web encontrada
            $userData['contrasenia_web'] = $webData['contrasenia_web'] ?? null;

            // Aseguramos tener el ID correcto para la sesión
            if (isset($webData['usuarioid'])) {
                $userData['usuarioid'] = $webData['usuarioid'];
            }

            // --- Datos Extra para Sesión ---
            $userData['sucursal_nombre'] = $sucursalData['descripcion'] ?? 'GENERICO';
            $userData['caja_nombre'] = $cajaData['nombrecaja'] ?? 'NINGUNA';
            $userData['igv'] = $paramsData['valor'] ?? 0.18;

            $stmtEmp = $this->pdo->prepare("SELECT nombre_empresa FROM EMPRESA WHERE id_empresa = ?");
            $stmtEmp->execute([$empresaId]);
            $emp = $stmtEmp->fetch();
            $userData['empresa_nombre'] = $emp['nombre_empresa'] ?? 'Desconocida';

            return $userData;

        } catch (PDOException $e) {
            error_log("Error en User::findByUsername: " . $e->getMessage());
            return null;
        }
    }
}