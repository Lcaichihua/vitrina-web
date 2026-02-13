<?php
namespace Vitrina\Controllers;

use Vitrina\Models\User;
use Exception; // Importante para capturar errores generales

class AuthController {

    public function login() {
        if (isset($_SESSION['user_id'])) {
            header('Location: /dashboard');
            exit;
        }

        $error = null;
        $userModel = null;

        // Intentamos instanciar el modelo. Si la BD falla aquí, capturamos el error.
        try {
            $userModel = new User();
        } catch (Exception $e) {
            // Si hay error de conexión (timeout, credenciales, etc.), lo guardamos
            // pero permitimos que la página cargue para mostrar el mensaje.
            $error = "Error de conexión al sistema (BD): " . $e->getMessage();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$error) {
            $username = trim($_POST['usuario'] ?? '');
            $password = $_POST['password'] ?? '';
            $empresaId = (int)($_POST['empresa'] ?? 0);
            $ip = $_SERVER['REMOTE_ADDR'];

            if (empty($username) || empty($password) || empty($empresaId)) {
                $error = "Todos los campos son obligatorios.";
            } else {
                // Envolvemos la búsqueda en try-catch por seguridad extra
                try {
                    $user = $userModel->findByUsername($username, $ip, $empresaId);

                    if ($user) {
                        if (isset($user['estado']) && $user['estado'] == 0) {
                            $error = "El usuario está inactivo o deshabilitado.";
                        }
                        else {
                            // Verificación de hash web
                            $hashWeb = $user['contrasenia_web'] ?? null;

                            if (!empty($hashWeb) && password_verify($password, $hashWeb)) {

                                // --- LOGIN EXITOSO ---
                                session_regenerate_id(true);

                                // 1. ID de Usuario
                                $userIdDb = $user['usuarioid'] ?? $user['UsuarioID'] ?? $username;
                                $_SESSION['user_id'] = $userIdDb;

                                // 2. Nombre de Usuario
                                $_SESSION['nombre_usuario'] = $username;

                                // 3. Nombre Completo
                                $_SESSION['user_name'] = $user['nombrecompleto'] ?? $user['NombreCompleto'] ?? $user['NOMBRECOMPLETO'] ?? $username;

                                $_SESSION['profile_id'] = $user['perfilid'] ?? 0;
                                $_SESSION['empresa_id'] = $empresaId;
                                $_SESSION['empresa_nombre'] = $user['empresa_nombre'] ?? 'Empresa';

                                $_SESSION['sucursal'] = $user['sucursal_nombre'] ?? 'N/A';
                                $_SESSION['caja'] = $user['caja_nombre'] ?? 'N/A';
                                $_SESSION['igv'] = $user['igv'] ?? 0.18;

                                header('Location: /dashboard');
                                exit;
                            } else {
                                $error = "Contraseña incorrecta o acceso web no configurado.";
                            }
                        }
                    } else {
                        $error = "Usuario no encontrado en esta empresa.";
                    }
                } catch (Exception $e) {
                    $error = "Error al consultar usuario: " . $e->getMessage();
                }
            }
        }

        // Si el modelo falló al inicio, no podemos llamar a getEmpresas()
        $empresas = [];
        if ($userModel) {
            try {
                $empresas = $userModel->getEmpresas();
            } catch (Exception $e) {
                // Si falla obtener empresas, simplemente dejamos la lista vacía o mostramos error
                if (!$error) $error = "No se pudo cargar la lista de empresas.";
            }
        }

        require_once __DIR__ . '/../../templates/login.php';
    }

    public function logout() {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
        header('Location: /');
        exit;
    }
}