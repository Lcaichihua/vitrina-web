<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Contratos | La Vitrina</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-gray-100">

<?php require_once __DIR__ . '/../partials/navbar.php'; ?>

<!-- Contenido Principal -->
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-6">
                <div class="flex flex-col md:flex-row justify-between items-center mb-6">
                    <h3 class="text-2xl font-semibold text-gray-900 leading-tight mb-4 md:mb-0">Listado de Contratos</h3>
                    <button class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-150 ease-in-out">
                        + Nuevo Contrato
                    </button>
                </div>

                <!-- Sección de Filtros -->
                <form method="GET" class="mb-6 p-4 border border-gray-200 rounded-lg shadow-sm bg-gray-50">
                    <h4 class="text-lg font-medium text-gray-700 mb-3">Filtros de Búsqueda</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label for="numero_contrato" class="block text-sm font-medium text-gray-700">Nº de Contrato</label>
                            <input type="text" name="numero_contrato" id="numero_contrato" value="<?php echo htmlspecialchars($_GET['numero_contrato'] ?? ''); ?>" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label for="arrendatario" class="block text-sm font-medium text-gray-700">Arrendatario</label>
                            <input type="text" name="arrendatario" id="arrendatario" value="<?php echo htmlspecialchars($_GET['arrendatario'] ?? ''); ?>" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label for="pie_ingreso" class="block text-sm font-medium text-gray-700">Pie de Ingreso</label>
                            <select id="pie_ingreso" name="pie_ingreso" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="TODOS" <?php echo (($_GET['pie_ingreso'] ?? 'TODOS') == 'TODOS') ? 'selected' : ''; ?>>--TODOS--</option>
                                <option value="SI" <?php echo (($_GET['pie_ingreso'] ?? '') == 'SI') ? 'selected' : ''; ?>>SI</option>
                                <option value="NO" <?php echo (($_GET['pie_ingreso'] ?? '') == 'NO') ? 'selected' : ''; ?>>NO</option>
                            </select>
                        </div>
                        <div>
                            <label for="id_tipo_contrato" class="block text-sm font-medium text-gray-700">Tipo Contrato</label>
                            <select id="id_tipo_contrato" name="id_tipo_contrato" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="">--SELECCIONE--</option>
                                <?php foreach ($tiposContrato as $tipo): ?>
                                    <option value="<?php echo htmlspecialchars($tipo['id_tipo_contrato']); ?>" <?php echo ((isset($_GET['id_tipo_contrato']) && $_GET['id_tipo_contrato'] == $tipo['id_tipo_contrato'])) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($tipo['descripcion']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="mt-4 flex justify-end">
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                            </svg>
                            Buscar
                        </button>
                    </div>
                </form>
                <!-- Fin Sección de Filtros -->

                <?php if (isset($error) && $error): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <strong class="font-bold">Error:</strong>
                        <span class="block sm:inline"><?php echo htmlspecialchars($error); ?></span>
                    </div>
                <?php endif; ?>

                <?php if (empty($contratos)): ?>
                    <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <strong class="font-bold">Información:</strong>
                        <span class="block sm:inline">No hay contratos registrados que coincidan con los filtros.</span>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto shadow-sm ring-1 ring-black ring-opacity-5 sm:rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        N° Contrato
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Tipo de Contrato
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Arrendatario
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Pie de Ingreso
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Importe Canastillas
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        % Renta Variable
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Contrap.
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Inicio Contrato
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Fin Contrato
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Estado
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        ¿Activo?
                                    </th>
                                    <th scope="col" class="relative px-6 py-3">
                                        <span class="sr-only">Acciones</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($contratos as $contrato): ?>
                                    <tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            <?php echo htmlspecialchars($contrato['numero_contrato']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            <?php echo htmlspecialchars($contrato['tipo_contrato']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            <?php echo htmlspecialchars($contrato['apellidos_nombres']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            <?php echo htmlspecialchars($contrato['pie_ingreso']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            <?php echo htmlspecialchars($contrato['importe_por_canastilla']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            <?php echo htmlspecialchars($contrato['porcentaje_renta_variable']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            <?php echo htmlspecialchars($contrato['importe_contraprestacion']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            <?php echo htmlspecialchars($contrato['inicio_contrato']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            <?php echo htmlspecialchars($contrato['fin_contrato']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            <?php echo htmlspecialchars($contrato['estado']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <?php if ($contrato['activo']): ?>
                                                <input type="checkbox" checked disabled class="form-checkbox h-4 w-4 text-indigo-600 transition duration-150 ease-in-out">
                                            <?php else: ?>
                                                <input type="checkbox" disabled class="form-checkbox h-4 w-4 text-gray-300 transition duration-150 ease-in-out">
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="#" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-150 ease-in-out mr-2">
                                                Ver
                                            </a>
                                            <?php if (!empty($contrato['ruta_documento_ftp'])): ?>
                                            <a href="#" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-150 ease-in-out">
                                                Descargar
                                            </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>

                <?php if (!empty($contratos) && $total_pages > 1): ?>
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
                                <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $current_page - 1])); ?>" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    Anterior
                                </a>
                            <?php endif; ?>
                            <?php if ($current_page < $total_pages): ?>
                                <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $current_page + 1])); ?>" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
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

</body>
</html>
