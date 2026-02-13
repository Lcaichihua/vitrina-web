<?php
// Utilidad simple para generar hashes de prueba
// IMPORTANTE: BORRAR ESTE ARCHIVO DESPUÉS DE USARLO EN PRODUCCIÓN

// 1. Cambia esto por la contraseña que quieras probar (ej: "123", "admin", etc.)
$password = "123";

// 2. Generamos el hash seguro usando el algoritmo por defecto de PHP (actualmente Bcrypt o Argon2)
$hash = password_hash($password, PASSWORD_DEFAULT);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generador de Hash</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style> body { font-family: sans-serif; } </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
<div class="bg-white p-8 rounded-lg shadow-md max-w-2xl w-full">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Generador de Contraseñas Seguras</h1>

    <div class="mb-6">
        <p class="text-sm text-gray-600 mb-1">Contraseña original:</p>
        <div class="p-3 bg-gray-50 border rounded font-mono text-lg font-bold text-blue-600">
            <?php echo htmlspecialchars($password); ?>
        </div>
    </div>

    <div class="mb-6">
        <p class="text-sm text-gray-600 mb-1">Hash generado (Para la columna 'contrasenia_web'):</p>
        <div class="p-3 bg-gray-50 border rounded font-mono text-sm break-all text-green-700 select-all">
            <?php echo $hash; ?>
        </div>
    </div>

    <hr class="my-6 border-gray-200">

    <div>
        <p class="text-sm font-semibold text-gray-700 mb-2">Instrucciones SQL:</p>
        <p class="text-sm text-gray-600 mb-2">Ejecuta el siguiente comando en tu gestor de base de datos para habilitar el acceso web a este usuario sin afectar el escritorio:</p>

        <div class="p-4 bg-gray-800 rounded text-gray-200 font-mono text-sm overflow-x-auto">
            UPDATE usuario <br>
            SET contrasenia_web = '<span class="text-green-400"><?php echo $hash; ?></span>' <br>
            WHERE nombreusuario = '<span class="text-yellow-400">TU_USUARIO</span>';
        </div>
    </div>
</div>
</body>
</html>