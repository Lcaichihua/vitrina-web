<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Arrendadores | La Vitrina</title>
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
                    <a href="/dashboard" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                        Inicio
                    </a>
                    <a href="#" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                        Contratos
                    </a>
                    <!-- Nuevo elemento de menú con sub-menú -->
                    <div class="relative group inline-flex">
                        <a href="#" id="maintenance-dropdown-toggle" class="border-blue-500 text-blue-600 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium focus:outline-none">
                            Mantenimiento
                            <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </a>
                        <div id="maintenance-dropdown-menu" class="absolute z-10 left-0 top-full w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none hidden">
                            <div class="py-1" role="menu" aria-orientation="vertical" aria-labelledby="options-menu">
                                <a href="/mantenimiento/tipo_puesto_comercial" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900" role="menuitem">Tipo puesto comercial</a>
                                <a href="/mantenimiento/puesto_comercial" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900" role="menuitem">Puesto comercial</a>
                                <a href="/mantenimiento/arrendadores" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 bg-gray-100 text-gray-900" role="menuitem">Arrendadores</a>
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
                <div class="flex flex-col md:flex-row justify-between items-center mb-6">
                    <h3 class="text-2xl font-semibold text-gray-900 leading-tight mb-4 md:mb-0">Listado de Arrendadores</h3>
                    <button class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-150 ease-in-out">
                        + Nuevo Arrendador
                    </button>
                </div>

                <?php if (isset($error) && $error): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <strong class="font-bold">Error:</strong>
                        <span class="block sm:inline"><?php echo htmlspecialchars($error); ?></span>
                    </div>
                <?php endif; ?>

                <?php if (empty($arrendadores)): ?>
                    <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <strong class="font-bold">Información:</strong>
                        <span class="block sm:inline">No hay arrendadores registrados.</span>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto shadow-sm ring-1 ring-black ring-opacity-5 sm:rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Tipo Doc.
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Nº Documento
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Apellidos
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Nombres
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Dirección
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Desde
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Hasta
                                    </th>
                                    <th scope="col" class="relative px-6 py-3">
                                        <span class="sr-only">Acciones</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($arrendadores as $arrendador): ?>
                                    <tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            <?php echo htmlspecialchars($arrendador['abrev_doc']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            <?php echo htmlspecialchars($arrendador['numero_documento']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            <?php echo htmlspecialchars($arrendador['apellidos']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            <?php echo htmlspecialchars($arrendador['nombres']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            <?php echo htmlspecialchars($arrendador['direccion']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            <?php echo htmlspecialchars($arrendador['desde']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            <?php echo htmlspecialchars($arrendador['hasta']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="#" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-150 ease-in-out mr-2">
                                                Editar
                                            </a>
                                            <a href="#" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-150 ease-in-out">
                                                Eliminar
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>

                <?php if (!empty($arrendadores) && $total_pages > 1): ?>
                    <nav class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6 mt-4" aria-label="Pagination">
                        <div class="hidden sm:block">
                            <p class="text-sm text-gray-700">
                                Mostrando
                                <span class="font-medium"><?php echo ($current_page - 1) * $records_per_page + 1; ?></span>
                                a
                                <span class="font-medium"><?php echo min($current_page * $records_per_page, $total_records); ?></span>
                                de
                                <span class="font-medium"><?php echo $total_records; ?></span>
                                resultados
                            </p>
                        </div>
                        <div class="flex-1 flex justify-between sm:justify-end">
                            <?php if ($current_page > 1): ?>
                                <a href="?page=<?php echo $current_page - 1; ?>" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    Anterior
                                </a>
                            <?php endif; ?>
                            <?php if ($current_page < $total_pages): ?>
                                <a href="?page=<?php echo $current_page + 1; ?>" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    Siguiente
                                </a>
                            <?php endif; ?>
                        </div>
                    </nav>
                <?php endif; ?>
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