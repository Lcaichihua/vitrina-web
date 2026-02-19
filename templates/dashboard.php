<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | La Vitrina</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-gray-100">

<?php require_once __DIR__ . '/partials/navbar.php'; ?>

<!-- Contenido Principal -->
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Bienvenido al Módulo Web</h3>
                        <p class="mt-1 text-sm text-gray-500">Sesión iniciada correctamente. Seleccione una opción del menú para comenzar.</p>
                    </div>
                </div>

                <div class="mt-6 border-t border-gray-100 pt-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-gray-50 p-4 rounded-md">
                            <span class="text-xs text-gray-500 uppercase tracking-wide font-bold">Sucursal Actual</span>
                            <p class="text-md font-semibold text-gray-800"><?php echo htmlspecialchars($_SESSION['sucursal'] ?? 'N/A'); ?></p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-md">
                            <span class="text-xs text-gray-500 uppercase tracking-wide font-bold">Caja Asignada</span>
                            <p class="text-md font-semibold text-gray-800"><?php echo htmlspecialchars($_SESSION['caja'] ?? 'N/A'); ?></p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-md">
                            <span class="text-xs text-gray-500 uppercase tracking-wide font-bold">Perfil ID</span>
                            <p class="text-md font-semibold text-gray-800"><?php echo htmlspecialchars($_SESSION['profile_id'] ?? '0'); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>