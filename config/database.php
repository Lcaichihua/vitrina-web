<?php
// config/database.php

// Configuración de credenciales
define('DB_HOST', '50.31.176.7');
define('DB_NAME', 'wptvxhei_ventas');
define('DB_USER', 'wptvxhei_prueba');
define('DB_PASS', 'Qh&N@PdbEx$8ARD4'); // <--- ¡Asegúrate de poner tu contraseña real aquí!

// ¡ESTA ES LA LÍNEA QUE FALTABA O ESTABA DANDO EL ERROR!
define('DB_CHARSET', 'utf8mb4');

class Database {
    private static $pdo = null;

    public static function connect() {
        // En modo debug, forzamos una nueva conexión para ver si falla
        // if (self::$pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                // Aumentamos el timeout a 15 segundos para conexiones lentas
                PDO::ATTR_TIMEOUT            => 15,
            ];
            self::$pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // MODO DEBUG: Mostrar el error real en pantalla
            echo "<div style='background:red;color:white;padding:20px;font-family:sans-serif;'>";
            echo "<h3>🚨 Error Crítico de Base de Datos:</h3>";
            echo "<p>" . $e->getMessage() . "</p>";
            echo "<hr>";
            echo "<p><strong>Intenta esto:</strong> Verifica que tu IP actual esté autorizada en el 'Remote MySQL' de tu cPanel.</p>";
            echo "</div>";
            die();
        }
        // }
        return self::$pdo;
    }
}