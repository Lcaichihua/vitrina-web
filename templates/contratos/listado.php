<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contratos | La Vitrina</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .table-container { max-height: 600px; overflow-y: auto; }
        thead th { position: sticky; top: 0; z-index: 10; }
    </style>
</head>
<body class="bg-slate-100">

<?php require_once __DIR__ . '/../partials/navbar.php'; ?>

<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 sm:px-0">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h3 class="text-2xl font-bold text-slate-800">Contratos</h3>
                <p class="text-sm text-slate-500 mt-1">Gestiona los contratos del sistema</p>
            </div>
            <button class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 px-5 rounded-xl shadow-lg shadow-blue-600/20 transition-all duration-200 hover:shadow-blue-600/30 hover:-translate-y-0.5">
                <i class="fa-solid fa-plus"></i>
                Nuevo Contrato
            </button>
        </div>

        <!-- Error Alert -->
        <?php if (isset($error) && $error): ?>
            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg mb-6 animate-pulse">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-circle-exclamation text-red-500"></i>
                    <p class="text-red-700 font-medium"><?php echo htmlspecialchars($error); ?></p>
                </div>
            </div>
        <?php endif; ?>

        <!-- Card -->
        <div class="bg-white rounded-2xl shadow-xl shadow-slate-200/60 border border-slate-100 overflow-hidden">
            <!-- Filters Section -->
            <form method="GET" class="p-5 border-b border-slate-100 bg-slate-50/50">
                <div class="flex items-center gap-2 mb-4">
                    <i class="fa-solid fa-filter text-slate-400"></i>
                    <h4 class="text-sm font-semibold text-slate-700">Filtros de Búsqueda</h4>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label for="numero_contrato" class="block text-xs font-medium text-slate-600 mb-1">Nº de Contrato</label>
                        <input type="text" name="numero_contrato" id="numero_contrato" value="<?php echo htmlspecialchars($_GET['numero_contrato'] ?? ''); ?>" 
                            class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500" 
                            placeholder="Buscar...">
                    </div>
                    <div>
                        <label for="arrendatario" class="block text-xs font-medium text-slate-600 mb-1">Arrendatario</label>
                        <input type="text" name="arrendatario" id="arrendatario" value="<?php echo htmlspecialchars($_GET['arrendatario'] ?? ''); ?>" 
                            class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500" 
                            placeholder="Buscar...">
                    </div>
                    <div>
                        <label for="pie_ingreso" class="block text-xs font-medium text-slate-600 mb-1">Pie de Ingreso</label>
                        <select id="pie_ingreso" name="pie_ingreso" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                            <option value="TODOS" <?php echo (($_GET['pie_ingreso'] ?? 'TODOS') == 'TODOS') ? 'selected' : ''; ?>>Todos</option>
                            <option value="SI" <?php echo (($_GET['pie_ingreso'] ?? '') == 'SI') ? 'selected' : ''; ?>>SI</option>
                            <option value="NO" <?php echo (($_GET['pie_ingreso'] ?? '') == 'NO') ? 'selected' : ''; ?>>NO</option>
                        </select>
                    </div>
                    <div>
                        <label for="id_tipo_contrato" class="block text-xs font-medium text-slate-600 mb-1">Tipo Contrato</label>
                        <select id="id_tipo_contrato" name="id_tipo_contrato" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                            <option value="">Todos los tipos</option>
                            <?php foreach ($tiposContrato as $tipo): ?>
                                <option value="<?php echo htmlspecialchars($tipo['id_tipo_contrato']); ?>" <?php echo ((isset($_GET['id_tipo_contrato']) && $_GET['id_tipo_contrato'] == $tipo['id_tipo_contrato'])) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($tipo['descripcion']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="mt-4 flex justify-end gap-2">
                    <a href="/contratos/listado" class="px-4 py-2 bg-white border border-slate-200 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-50 hover:border-slate-300 transition-colors">
                        <i class="fa-solid fa-rotate-left mr-1"></i>Limpiar
                    </a>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors shadow-md">
                        <i class="fa-solid fa-magnifying-glass mr-1"></i>Buscar
                    </button>
                </div>
            </form>

            <?php if (empty($contratos)): ?>
                <div class="p-12 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-100 mb-4">
                        <i class="fa-solid fa-file-contract text-2xl text-slate-400"></i>
                    </div>
                    <h4 class="text-lg font-semibold text-slate-700 mb-2">No hay contratos registrados</h4>
                    <p class="text-slate-500">No hay contratos que coincidan con los filtros seleccionados.</p>
                </div>
            <?php else: ?>
                <div class="table-container">
                    <table class="min-w-full">
                        <thead class="bg-slate-800 text-white">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">N° Contrato</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Tipo</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Arrendatario</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Pie</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Importe Can.</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">% Renta</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Contrap.</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Inicio</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Fin</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Estado</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Activo</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php foreach ($contratos as $index => $contrato): ?>
                                <tr class="<?php echo $index % 2 === 0 ? 'bg-white' : 'bg-slate-50/50'; ?> hover:bg-blue-50/50 transition-colors duration-150">
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-bold text-slate-700">
                                        <?php echo htmlspecialchars($contrato['numero_contrato']); ?>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-slate-600">
                                        <?php echo htmlspecialchars($contrato['tipo_contrato']); ?>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-slate-600 max-w-[150px] truncate">
                                        <?php echo htmlspecialchars($contrato['apellidos_nombres']); ?>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-slate-600">
                                        <?php if ($contrato['pie_ingreso'] === 'SI'): ?>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-100 text-emerald-700">
                                                <?php echo htmlspecialchars($contrato['pie_ingreso']); ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-600">
                                                <?php echo htmlspecialchars($contrato['pie_ingreso']); ?>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-slate-600">
                                        <?php echo htmlspecialchars($contrato['importe_por_canastilla']); ?>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-slate-600">
                                        <?php echo htmlspecialchars($contrato['porcentaje_renta_variable']); ?>%
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-slate-600">
                                        <?php echo htmlspecialchars($contrato['importe_contraprestacion']); ?>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-slate-500">
                                        <?php echo htmlspecialchars($contrato['inicio_contrato']); ?>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-slate-500">
                                        <?php echo htmlspecialchars($contrato['fin_contrato']); ?>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-700">
                                            <?php echo htmlspecialchars($contrato['estado']); ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-center">
                                        <?php if ($contrato['activo']): ?>
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                Sí
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                                No
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="flex items-center justify-center gap-1">
                                            <button class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Ver">
                                                <i class="fa-solid fa-eye"></i>
                                            </button>
                                            <?php if (!empty($contrato['ruta_documento_ftp'])): ?>
                                                <button class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors" title="Descargar">
                                                    <i class="fa-solid fa-download"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <!-- Pagination -->
            <?php if (!empty($contratos) && $total_pages > 1): ?>
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <p class="text-sm text-slate-500">
                        Mostrando <span class="font-semibold text-slate-700"><?php echo ($current_page - 1) * $records_per_page + 1; ?></span>
                        a <span class="font-semibold text-slate-700"><?php echo min($current_page * $records_per_page, $total_records); ?></span>
                        de <span class="font-semibold text-slate-700"><?php echo $total_records; ?></span> resultados
                    </p>
                    <div class="flex gap-2">
                        <?php if ($current_page > 1): ?>
                            <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $current_page - 1])); ?>" class="px-4 py-2 bg-white border border-slate-200 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-50 hover:border-slate-300 transition-colors">
                                <i class="fa-solid fa-chevron-left mr-1"></i> Anterior
                            </a>
                        <?php endif; ?>
                        <?php if ($current_page < $total_pages): ?>
                            <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $current_page + 1])); ?>" class="px-4 py-2 bg-white border border-slate-200 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-50 hover:border-slate-300 transition-colors">
                                Siguiente <i class="fa-solid fa-chevron-right ml-1"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>
