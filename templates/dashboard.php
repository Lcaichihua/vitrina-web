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

<!-- Navbar -->
<nav class="bg-white shadow">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="flex-shrink-0 flex items-center">
                    <!-- Logo / Nombre del Sistema -->
                    <span class="font-bold text-xl text-blue-800">La Vitrina</span>
                </div>
                <!-- Menú de Navegación -->
                <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                    <a href="/dashboard" class="border-blue-500 text-gray-900 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                        Inicio
                    </a>
                    <a href="#" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                        Contratos
                    </a>
                    <!-- Nuevo elemento de menú con sub-menú -->
                    <div class="relative group inline-flex">
                        <a href="#" id="maintenance-dropdown-toggle" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium focus:outline-none">
                            Mantenimiento
                            <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </a>
                        <div id="maintenance-dropdown-menu" class="absolute z-10 left-0 top-full w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none hidden">
                            <div class="py-1" role="menu" aria-orientation="vertical" aria-labelledby="options-menu">
                                <a href="/mantenimiento/tipo_puesto_comercial" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900" role="menuitem">Tipo puesto comercial</a>
                                <a href="/mantenimiento/puesto_comercial" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900" role="menuitem">Puesto comercial</a>
                                <a href="/mantenimiento/arrendadores" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900" role="menuitem">Arrendadores</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Información del Usuario -->
            <div class="flex items-center">
                <div class="ml-3 relative flex items-center gap-4">
                    <div class="text-right">
                        <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Usuario'); ?></p>
                        <p class="text-xs text-gray-500"><?php echo htmlspecialchars($_SESSION['empresa_nombre'] ?? 'Empresa'); ?></p>
                    </div>
                    <a href="/logout" class="text-sm text-red-600 hover:text-red-900 font-medium ml-4 border border-red-200 bg-red-50 hover:bg-red-100 px-3 py-1 rounded transition">Cerrar Sesión</a>
                </div>
            </div>
        </div>
    </div>
</nav>

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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleButton = document.getElementById('maintenance-dropdown-toggle');
            const dropdownMenu = document.getElementById('maintenance-dropdown-menu');

            if (toggleButton && dropdownMenu) {
                toggleButton.addEventListener('click', function(event) {
                    event.preventDefault(); // Evitar el comportamiento predeterminado del enlace
                    dropdownMenu.classList.toggle('hidden');
                });

                // Cerrar el dropdown si se hace clic fuera de él
                document.addEventListener('click', function(event) {
                    if (!dropdownMenu.contains(event.target) && !toggleButton.contains(event.target)) {
                        dropdownMenu.classList.add('hidden');
                    }
                });
            }
        });
    </script>
</body>
</html>