<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contratos | La Vitrina</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .table-container { max-height: 600px; overflow-y: auto; }
        thead th { position: sticky; top: 0; z-index: 10; }
    </style>
</head>
<body class="bg-slate-100" x-data="{ loading: true }" x-init="setTimeout(() => loading = false, 500)">

    <div x-show="loading" class="fixed inset-0 bg-white/80 backdrop-blur-sm z-50 flex items-center justify-center" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="text-center">
            <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-blue-600 border-t-transparent"></div>
            <p class="mt-4 text-slate-600 font-medium">Cargando...</p>
        </div>
    </div>

<?php require_once __DIR__ . '/../partials/navbar.php'; ?>

<div x-show="!loading" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 sm:px-0">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h3 class="text-2xl font-bold text-slate-800">Contratos</h3>
                <p class="text-sm text-slate-500 mt-1">Gestiona los contratos del sistema</p>
            </div>
            <button onclick="openNuevoContratoModal()" class="group relative inline-flex items-center gap-2.5 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold py-2.5 px-5 rounded-xl shadow-lg shadow-blue-600/25 transition-all duration-200 hover:shadow-blue-600/40 hover:-translate-y-0.5 active:translate-y-0 active:shadow-md">
                <span class="absolute inset-0 rounded-xl bg-white/20 opacity-0 group-hover:opacity-100 transition-opacity"></span>
                <span class="w-5 h-5 rounded-full bg-white/20 flex items-center justify-center">
                    <i class="fa-solid fa-plus text-xs"></i>
                </span>
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
                        <label class="block text-xs font-medium text-slate-600 mb-1">Tipo Contrato</label>
                        <div class="relative" x-data="{ open: false }">
                            <button type="button" @click="open = !open" @click.outside="open = false" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-left flex items-center justify-between">
                                <span id="tiposSelectedText">Seleccionar tipos...</span>
                                <i class="fa-solid fa-chevron-down text-xs transition-transform" :class="open ? 'rotate-180' : ''"></i>
                            </button>
                            <div x-show="open" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-1" class="absolute z-50 w-full mt-1 bg-white border border-slate-200 rounded-lg shadow-lg max-h-60 overflow-y-auto" style="display: none;">
                                <?php 
                                $selectedTipos = isset($_GET['tipos_contrato']) && is_array($_GET['tipos_contrato']) 
                                    ? $_GET['tipos_contrato'] 
                                    : [];
                                ?>
                                <?php foreach ($tiposContrato as $tipo): ?>
                                    <label class="flex items-center px-3 py-2 hover:bg-slate-50 cursor-pointer">
                                        <input type="checkbox" 
                                            name="tipos_contrato[]" 
                                            value="<?php echo htmlspecialchars($tipo['id_tipo_contrato']); ?>"
                                            <?php echo in_array((string)$tipo['id_tipo_contrato'], $selectedTipos) ? 'checked' : ''; ?>
                                            @change="updateTiposText()"
                                            class="w-4 h-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500 mr-3">
                                        <span class="text-sm text-slate-700"><?php echo htmlspecialchars($tipo['descripcion']); ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <script>
                            function updateTiposText() {
                                const checkboxes = document.querySelectorAll('input[name="tipos_contrato[]"]:checked');
                                const text = document.getElementById('tiposSelectedText');
                                if (checkboxes.length === 0) {
                                    text.textContent = 'Seleccionar tipos...';
                                } else if (checkboxes.length === 1) {
                                    text.textContent = '1 tipo seleccionado';
                                } else {
                                    text.textContent = checkboxes.length + ' tipos seleccionados';
                                }
                            }
                            document.addEventListener('DOMContentLoaded', updateTiposText);
                        </script>
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
</div>

<!-- Modal Nuevo Contrato -->
<div id="nuevoContratoModal" class="fixed inset-0 z-50 hidden" style="display: none;">
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeNuevoContratoModal()"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-slate-200 bg-gradient-to-r from-blue-600 to-blue-700">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-bold text-white">Contratos</h3>
                        <p class="text-sm text-blue-100">Generación de Contrato</p>
                    </div>
                    <button onclick="closeNuevoContratoModal()" class="p-2 text-white/80 hover:text-white hover:bg-white/20 rounded-lg transition-colors">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            </div>
            
            <!-- Tabs -->
            <div class="border-b border-slate-200 bg-slate-50">
                <div class="flex">
                    <button onclick="switchTab('tab-contrato')" id="tab-btn-contrato" class="tab-btn active px-6 py-3 text-sm font-medium text-blue-600 border-b-2 border-blue-600">
                        Contrato
                    </button>
                    <button onclick="switchTab('tab-arrendatario')" id="tab-btn-arrendatario" class="tab-btn px-6 py-3 text-sm font-medium text-slate-500 hover:text-slate-700">
                        Arrendatario
                    </button>
                    <button onclick="switchTab('tab-otro')" id="tab-btn-otro" class="tab-btn px-6 py-3 text-sm font-medium text-slate-500 hover:text-slate-700">
                        Otro
                    </button>
                </div>
            </div>

            <!-- Tab Content -->
            <div class="p-6 overflow-y-auto max-h-[60vh]">
                <!-- Tab 1: Contrato -->
                <div id="tab-contrato" class="tab-content">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">N° de Contrato</label>
                            <input type="text" value="00000000" disabled class="w-full px-4 py-2.5 bg-slate-100 border border-slate-200 rounded-xl text-sm text-slate-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">N° de Contrato Manual</label>
                            <input type="text" id="numeroContratoManual" value="PU-01-2026" disabled class="w-full px-4 py-2.5 bg-slate-100 border border-slate-200 rounded-xl text-sm text-slate-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Sucursal</label>
                            <select id="sucursalSelect" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                                <option value="">Seleccionar...</option>
                                <?php foreach ($sucursales as $suc): ?>
                                    <option value="<?php echo $suc['sucursalid']; ?>"><?php echo htmlspecialchars($suc['descripcion']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Tipo Puesto Comercial</label>
                            <select id="tipoPuestoSelect" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                                <option value="">Seleccionar...</option>
                                <?php foreach ($tiposPuestoComercial as $tp): ?>
                                    <option value="<?php echo $tp['id_tipo_puesto_comercial']; ?>"><?php echo htmlspecialchars($tp['descripcion']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <button type="button" onclick="buscarPuestos()" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
                            <i class="fa-solid fa-search mr-2"></i>Puesto Comercial
                        </button>
                    </div>

                    <div id="puestosSeleccionados" class="mt-4 hidden">
                        <h4 class="text-sm font-semibold text-slate-700 mb-2">Puestos Seleccionados</h4>
                        <div id="puestosList" class="flex flex-wrap gap-2"></div>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Tipo de Contrato</label>
                        <select id="tipoContratoSelect" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                            <option value="">Seleccionar...</option>
                            <?php foreach ($tiposContrato as $tc): ?>
                                <option value="<?php echo $tc['id_tipo_contrato']; ?>"><?php echo htmlspecialchars($tc['descripcion']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Tab 2: Arrendatario -->
                <div id="tab-arrendatario" class="tab-content hidden">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Código Arrendatario</label>
                            <input type="text" id="codigoArrendatario" value="0000000" disabled class="w-full px-4 py-2.5 bg-slate-100 border border-slate-200 rounded-xl text-sm text-slate-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Tipo de Documento</label>
                            <select id="tipoDocumentoSelect" disabled class="w-full px-4 py-2.5 bg-slate-100 border border-slate-200 rounded-xl text-sm text-slate-500">
                                <option value="">Seleccionar...</option>
                                <?php foreach ($tiposDocumento as $td): ?>
                                    <option value="<?php echo $td['docident_id']; ?>"><?php echo htmlspecialchars($td['descripcion']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">N° de Documento</label>
                            <input type="text" id="numeroDocumento" disabled class="w-full px-4 py-2.5 bg-slate-100 border border-slate-200 rounded-xl text-sm text-slate-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Apellidos y Nombres</label>
                            <input type="text" id="apellidosNombres" disabled class="w-full px-4 py-2.5 bg-slate-100 border border-slate-200 rounded-xl text-sm text-slate-500">
                        </div>
                    </div>
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Concesionario Razón Social</label>
                        <input type="text" id="concesionario" disabled class="w-full px-4 py-2.5 bg-slate-100 border border-slate-200 rounded-xl text-sm text-slate-500">
                    </div>
                    
                    <div class="mt-4">
                        <button type="button" onclick="openBuscarArrendatario()" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
                            <i class="fa-solid fa-user-plus mr-2"></i>Asignar Arrendatario
                        </button>
                    </div>
                </div>

                <!-- Tab 3: Otro (pendiente) -->
                <div id="tab-otro" class="tab-content hidden">
                    <div class="text-center py-8 text-slate-500">
                        <i class="fa-solid fa-clock text-4xl mb-4 text-slate-300"></i>
                        <p>Contenido del tercer tab pendiente...</p>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50 flex justify-end gap-3">
                <button onclick="closeNuevoContratoModal()" class="px-4 py-2 bg-slate-100 text-slate-700 font-medium rounded-xl hover:bg-slate-200 transition-colors">
                    Cancelar
                </button>
                <button class="px-4 py-2 bg-blue-600 text-white font-medium rounded-xl hover:bg-blue-700 transition-colors">
                    Guardar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Seleccionar Puesto Comercial -->
<div id="puestoComercialModal" class="fixed inset-0 z-50 hidden" style="display: none;">
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closePuestoComercialModal()"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[80vh] overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200">
                <h3 class="text-lg font-bold text-slate-800">Selecciona Puesto Comercial</h3>
            </div>
            <div class="p-4 overflow-auto max-h-[50vh]">
                <table class="min-w-full">
                    <thead class="bg-slate-50 sticky top-0">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-slate-600">Selección</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-slate-600">Id</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-slate-600">Tipo Puesto</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-slate-600">Sucursal</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-slate-600">Interior/Número</th>
                        </tr>
                    </thead>
                    <tbody id="puestosTableBody">
                        <!-- Rows will be populated via JS -->
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50 flex justify-end gap-3">
                <button onclick="resetPuestosChecks()" class="px-4 py-2 bg-slate-100 text-slate-700 font-medium rounded-xl hover:bg-slate-200 transition-colors">
                    Resetear
                </button>
                <button onclick="closePuestoComercialModal()" class="px-4 py-2 bg-slate-100 text-slate-700 font-medium rounded-xl hover:bg-slate-200 transition-colors">
                    Cancelar
                </button>
                <button onclick="seleccionarPuestos()" class="px-4 py-2 bg-blue-600 text-white font-medium rounded-xl hover:bg-blue-700 transition-colors">
                    Seleccionar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Buscar Arrendatario -->
<div id="buscarArrendatarioModal" class="fixed inset-0 z-50 hidden" style="display: none;">
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeBuscarArrendatarioModal()"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[80vh] overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200">
                <h3 class="text-lg font-bold text-slate-800">Selecciona Arrendatario</h3>
            </div>
            <div class="p-4 border-b border-slate-200">
                <div class="flex gap-2">
                    <input type="text" id="buscarArrendatarioInput" placeholder="Buscar por nombre, documento o razón social..." 
                        class="flex-1 px-4 py-2.5 border border-slate-200 rounded-xl text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    <button onclick="buscarArrendatarios()" class="px-4 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-medium hover:bg-blue-700 transition-colors">
                        <i class="fa-solid fa-search"></i>
                    </button>
                </div>
            </div>
            <div class="p-4 overflow-auto max-h-[40vh]">
                <table class="min-w-full">
                    <thead class="bg-slate-50 sticky top-0">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-slate-600">Id</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-slate-600">Documento</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-slate-600">Nombres</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-slate-600">Concesionario</th>
                        </tr>
                    </thead>
                    <tbody id="arrendatariosTableBody">
                        <!-- Rows will be populated via JS -->
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50 flex justify-end gap-3">
                <button onclick="closeBuscarArrendatarioModal()" class="px-4 py-2 bg-slate-100 text-slate-700 font-medium rounded-xl hover:bg-slate-200 transition-colors">
                    Cancelar
                </button>
                <button onclick="seleccionarArrendatario()" class="px-4 py-2 bg-blue-600 text-white font-medium rounded-xl hover:bg-blue-700 transition-colors">
                    Seleccionar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Alert Modal -->
<div id="alertModal" class="fixed inset-0 z-50 hidden" style="display: none;">
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeAlertModal()"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm">
            <div class="p-6 text-center">
                <div id="alertIcon" class="w-16 h-16 mx-auto mb-4 rounded-full flex items-center justify-center">
                    <i class="fa-solid fa-exclamation-triangle text-2xl"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-800 mb-2" id="alertTitle">Alerta</h3>
                <p class="text-sm text-slate-600 mb-6" id="alertMessage">Mensaje de alerta</p>
                <button onclick="closeAlertModal()" class="px-6 py-2.5 bg-blue-600 text-white font-medium rounded-xl hover:bg-blue-700 transition-colors">
                    Aceptar
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.tab-btn.active {
    color: #2563eb;
    border-bottom-color: #2563eb;
}
</style>

<script>
let selectedPuestos = [];
let selectedArrendatarioId = null;

function openNuevoContratoModal() {
    document.getElementById('nuevoContratoModal').classList.remove('hidden');
    document.getElementById('nuevoContratoModal').style.display = 'flex';
}

function closeNuevoContratoModal() {
    document.getElementById('nuevoContratoModal').classList.add('hidden');
    document.getElementById('nuevoContratoModal').style.display = 'none';
}

function switchTab(tabId) {
    document.querySelectorAll('.tab-content').forEach(tab => tab.classList.add('hidden'));
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active', 'text-blue-600', 'border-blue-600');
        btn.classList.add('text-slate-500');
    });
    
    document.getElementById(tabId).classList.remove('hidden');
    document.getElementById('tab-btn-' + tabId.replace('tab-', '')).classList.add('active', 'text-blue-600', 'border-blue-600');
    document.getElementById('tab-btn-' + tabId.replace('tab-', '')).classList.remove('text-slate-500');
}

function showAlert(title, message, type = 'warning') {
    const alertModal = document.getElementById('alertModal');
    const alertIcon = document.getElementById('alertIcon');
    const alertTitle = document.getElementById('alertTitle');
    const alertMessage = document.getElementById('alertMessage');
    
    alertTitle.textContent = title;
    alertMessage.textContent = message;
    
    if (type === 'warning') {
        alertIcon.className = 'w-16 h-16 mx-auto mb-4 rounded-full bg-yellow-100 flex items-center justify-center';
        alertIcon.innerHTML = '<i class="fa-solid fa-exclamation-triangle text-2xl text-yellow-600"></i>';
    } else if (type === 'error') {
        alertIcon.className = 'w-16 h-16 mx-auto mb-4 rounded-full bg-red-100 flex items-center justify-center';
        alertIcon.innerHTML = '<i class="fa-solid fa-circle-xmark text-2xl text-red-600"></i>';
    } else {
        alertIcon.className = 'w-16 h-16 mx-auto mb-4 rounded-full bg-blue-100 flex items-center justify-center';
        alertIcon.innerHTML = '<i class="fa-solid fa-circle-info text-2xl text-blue-600"></i>';
    }
    
    alertModal.classList.remove('hidden');
    alertModal.style.display = 'flex';
}

function closeAlertModal() {
    document.getElementById('alertModal').classList.add('hidden');
    document.getElementById('alertModal').style.display = 'none';
}

function buscarPuestos() {
    const sucursalId = document.getElementById('sucursalSelect').value;
    const tipoPuestoId = document.getElementById('tipoPuestoSelect').value;
    
    if (!sucursalId) {
        showAlert('Alerta', 'Seleccione una sucursal.', 'warning');
        return;
    }
    
    if (!tipoPuestoId) {
        showAlert('Alerta', 'Seleccione un tipo de puesto comercial.', 'warning');
        return;
    }
    
    fetch(`/api/contratos/puestos?sucursal=${sucursalId}&tipo_puesto=${tipoPuestoId}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                if (data.data.length === 0) {
                    showAlert('Información', 'No hay puestos comerciales disponibles con los criterios de búsqueda seleccionados.', 'info');
                    return;
                }
                // Show spinner and open modal
                const tbody = document.getElementById('puestosTableBody');
                tbody.innerHTML = `
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center">
                            <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-blue-600 border-t-transparent"></div>
                            <p class="mt-2 text-sm text-slate-500">Cargando...</p>
                        </td>
                    </tr>
                `;
                document.getElementById('puestoComercialModal').classList.remove('hidden');
                document.getElementById('puestoComercialModal').style.display = 'flex';
                renderPuestosTable(data.data);
            } else {
                showAlert('Error', data.error || 'Error al obtener puestos', 'error');
            }
        })
        .catch(err => {
            showAlert('Error', 'Error de conexión', 'error');
        });
}

function renderPuestosTable(puestos) {
    const tbody = document.getElementById('puestosTableBody');
    tbody.innerHTML = '';
    
    puestos.forEach(p => {
        const isChecked = selectedPuestos.includes(String(p.Id));
        const tr = document.createElement('tr');
        tr.className = 'border-b border-slate-100 hover:bg-slate-50 cursor-pointer';
        tr.innerHTML = `
            <td class="px-4 py-2">
                <input type="checkbox" class="puesto-checkbox w-4 h-4 text-blue-600 border-slate-300 rounded" data-id="${p.Id}" ${isChecked ? 'checked' : ''}>
            </td>
            <td class="px-4 py-2 text-sm text-slate-700">${p.Id}</td>
            <td class="px-4 py-2 text-sm text-slate-700">${p.TipoPuesto || ''}</td>
            <td class="px-4 py-2 text-sm text-slate-700">${p.Sucursal || ''}</td>
            <td class="px-4 py-2 text-sm text-slate-700">${p.Interior_Numero || ''}</td>
        `;
        tr.onclick = (e) => {
            if (e.target.type !== 'checkbox') {
                const checkbox = tr.querySelector('.puesto-checkbox');
                checkbox.checked = !checkbox.checked;
            }
        };
        tbody.appendChild(tr);
    });
}

function seleccionarPuestos() {
    const checkboxes = document.querySelectorAll('.puesto-checkbox:checked');
    selectedPuestos = Array.from(checkboxes).map(cb => cb.dataset.id);
    
    renderPuestosSeleccionados();
    closePuestoComercialModal();
}

function resetPuestosChecks() {
    document.querySelectorAll('.puesto-checkbox').forEach(cb => cb.checked = false);
}

function renderPuestosSeleccionados() {
    const container = document.getElementById('puestosList');
    const wrapper = document.getElementById('puestosSeleccionados');
    
    if (selectedPuestos.length === 0) {
        wrapper.classList.add('hidden');
        return;
    }
    
    wrapper.classList.remove('hidden');
    container.innerHTML = selectedPuestos.map(id => `
        <span class="inline-flex items-center gap-1 px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm">
            ${id}
            <button type="button" onclick="removePuesto(${id})" class="hover:text-blue-900"><i class="fa-solid fa-times"></i></button>
        </span>
    `).join('');
}

function removePuesto(id) {
    selectedPuestos = selectedPuestos.filter(p => p != id);
    renderPuestosSeleccionados();
}

function closePuestoComercialModal() {
    document.getElementById('puestoComercialModal').classList.add('hidden');
    document.getElementById('puestoComercialModal').style.display = 'none';
}

function openBuscarArrendatario() {
    document.getElementById('buscarArrendatarioModal').classList.remove('hidden');
    document.getElementById('buscarArrendatarioModal').style.display = 'flex';
    buscarArrendatarios();
}

function closeBuscarArrendatarioModal() {
    document.getElementById('buscarArrendatarioModal').classList.add('hidden');
    document.getElementById('buscarArrendatarioModal').style.display = 'none';
}

function buscarArrendatarios() {
    const query = document.getElementById('buscarArrendatarioInput').value;
    const tbody = document.getElementById('arrendatariosTableBody');
    
    // Show spinner
    tbody.innerHTML = `
        <tr>
            <td colspan="4" class="px-4 py-8 text-center">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-blue-600 border-t-transparent"></div>
                <p class="mt-2 text-sm text-slate-500">Cargando...</p>
            </td>
        </tr>
    `;
    
    fetch(`/api/contratos/arrendatarios?q=${encodeURIComponent(query)}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                renderArrendatariosTable(data.data);
            } else {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-red-500">
                            Error al cargar datos
                        </td>
                    </tr>
                `;
            }
        })
        .catch(err => {
            tbody.innerHTML = `
                <tr>
                    <td colspan="4" class="px-4 py-8 text-center text-red-500">
                        Error de conexión
                    </td>
                </tr>
            `;
        });
}

function renderArrendatariosTable(arrendatarios) {
    const tbody = document.getElementById('arrendatariosTableBody');
    tbody.innerHTML = '';
    
    arrendatarios.forEach(a => {
        const isSelected = selectedArrendatarioId == a.id_arrendatario;
        const tr = document.createElement('tr');
        tr.className = 'border-b border-slate-100 hover:bg-slate-50 cursor-pointer transition-colors' + (isSelected ? ' bg-blue-100 border-l-4 border-l-blue-600' : '');
        tr.dataset.id = a.id_arrendatario;
        tr.onclick = () => selectArrendatarioRow(a.id_arrendatario);
        tr.innerHTML = `
            <td class="px-4 py-2 text-sm font-medium ${isSelected ? 'text-blue-700' : 'text-slate-700'}">${String(a.id_arrendatario).padStart(7, '0')}</td>
            <td class="px-4 py-2 text-sm ${isSelected ? 'text-blue-700' : 'text-slate-700'}">${a.tipo_documento || ''} ${a.numero_documento || ''}</td>
            <td class="px-4 py-2 text-sm ${isSelected ? 'text-blue-700' : 'text-slate-700'}">${a.nombres || ''}</td>
            <td class="px-4 py-2 text-sm ${isSelected ? 'text-blue-700' : 'text-slate-700'}">${a.concessionesario || ''}</td>
        `;
        tbody.appendChild(tr);
    });
}

function selectArrendatarioRow(id) {
    selectedArrendatarioId = id;
    document.querySelectorAll('#arrendatariosTableBody tr').forEach(row => {
        if (row.dataset.id == id) {
            row.classList.add('bg-blue-100', 'border-l-4', 'border-l-blue-600');
            row.querySelectorAll('td').forEach(td => td.classList.add('text-blue-700'));
            row.querySelectorAll('td').forEach(td => td.classList.remove('text-slate-700'));
        } else {
            row.classList.remove('bg-blue-100', 'border-l-4', 'border-l-blue-600');
            row.querySelectorAll('td').forEach(td => td.classList.remove('text-blue-700'));
            row.querySelectorAll('td').forEach(td => td.classList.add('text-slate-700'));
        }
    });
}

function seleccionarArrendatario() {
    if (!selectedArrendatarioId) {
        showAlert('Alerta', 'Seleccione un arrendatario', 'warning');
        return;
    }
    
    fetch(`/api/contratos/arrendatario?id=${selectedArrendatarioId}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const arr = data.data;
                document.getElementById('codigoArrendatario').value = arr.id_arrendatario || '0000000';
                document.getElementById('tipoDocumentoSelect').value = arr.docident_id || '';
                document.getElementById('numeroDocumento').value = arr.numero_documento || '';
                document.getElementById('apellidosNombres').value = arr.nombres || '';
                document.getElementById('concesionario').value = arr.nombre_concesionario_razon_social || '';
                
                closeBuscarArrendatarioModal();
            } else {
                showAlert('Error', data.error || 'Error al obtener arrendatario', 'error');
            }
        })
        .catch(err => {
            showAlert('Error', 'Error de conexión', 'error');
        });
}

// Enter key for search
document.getElementById('buscarArrendatarioInput')?.addEventListener('keypress', (e) => {
    if (e.key === 'Enter') {
        buscarArrendatarios();
    }
});
</script>

</body>
</html>
