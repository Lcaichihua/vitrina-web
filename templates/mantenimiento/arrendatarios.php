<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Arrendatarios | La Vitrina</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .table-container {
            max-height: 600px;
            overflow-y: auto;
        }
        thead th {
            position: sticky;
            top: 0;
            z-index: 10;
        }
    </style>
</head>
<body class="bg-slate-100" x-data="{ loading: true }" x-init="setTimeout(() => loading = false, 500)">

    <!-- Spinner de carga -->
    <div x-show="loading" class="fixed inset-0 bg-white/80 backdrop-blur-sm z-50 flex items-center justify-center" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="text-center">
            <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-blue-600 border-t-transparent"></div>
            <p class="mt-4 text-slate-600 font-medium">Cargando...</p>
        </div>
    </div>

<?php require_once __DIR__ . '/../partials/navbar.php'; ?>

<!-- Contenido Principal -->
<div x-show="!loading" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 sm:px-0">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h3 class="text-2xl font-bold text-slate-800">Arrendatarios</h3>
                <p class="text-sm text-slate-500 mt-1">Gestiona los arrendatarios del sistema</p>
            </div>
            <button onclick="openNewModal()" class="group relative inline-flex items-center gap-2.5 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold py-2.5 px-5 rounded-xl shadow-lg shadow-blue-600/25 transition-all duration-200 hover:shadow-blue-600/40 hover:-translate-y-0.5 active:translate-y-0 active:shadow-md">
                <span class="absolute inset-0 rounded-xl bg-white/20 opacity-0 group-hover:opacity-100 transition-opacity"></span>
                <span class="w-5 h-5 rounded-full bg-white/20 flex items-center justify-center">
                    <i class="fa-solid fa-plus text-xs"></i>
                </span>
                Nuevo Arrendatario
            </button>
        </div>

        <!-- Flash Messages -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-lg mb-6 animate-pulse">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-circle-check text-emerald-500"></i>
                    <p class="text-emerald-700 font-medium"><?php echo htmlspecialchars($_SESSION['success']); ?></p>
                </div>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg mb-6 animate-pulse">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-circle-exclamation text-red-500"></i>
                    <p class="text-red-700 font-medium"><?php echo htmlspecialchars($_SESSION['error']); ?></p>
                </div>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <!-- Alerts -->
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
            <!-- Search & Filters Bar -->
            <form method="GET" class="p-5 border-b border-slate-100 bg-slate-50/50">
                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fa-solid fa-magnifying-glass text-slate-400"></i>
                        </div>
                        <input type="text" name="search" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" placeholder="Buscar por documento, nombre o dirección..." 
                            class="w-full pl-11 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                    </div>
                    <div class="flex gap-2">
                        <select name="estado" class="px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                            <option value="">Todos los estados</option>
                            <option value="1" <?php echo (($_GET['estado'] ?? '') == '1') ? 'selected' : ''; ?>>Activo</option>
                            <option value="0" <?php echo (($_GET['estado'] ?? '') == '0') ? 'selected' : ''; ?>>Inactivo</option>
                        </select>
                        <button type="submit" class="px-4 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-medium hover:bg-blue-700 transition-colors">
                            <i class="fa-solid fa-search"></i>
                        </button>
                        <?php if (!empty($_GET['search']) || !empty($_GET['estado'])): ?>
                            <a href="?" class="px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm text-slate-600 hover:bg-slate-50 hover:border-slate-300 transition-colors">
                                <i class="fa-solid fa-rotate-left"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </form>

            <?php if (empty($arrendatarios)): ?>
                <div class="p-12 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-100 mb-4">
                        <i class="fa-solid fa-user-slash text-2xl text-slate-400"></i>
                    </div>
                    <h4 class="text-lg font-semibold text-slate-700 mb-2">No hay arrendatarios registrados</h4>
                    <p class="text-slate-500">Comienza agregando un nuevo arrendatario</p>
                </div>
            <?php else: ?>
                <div class="table-container">
                    <table class="min-w-full">
                        <thead class="bg-slate-800 text-white">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">
                                    Tipo Doc.
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">
                                    N° Documento
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">
                                    R.Social
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">
                                    Apellidos
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">
                                    Nombres
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">
                                    Dirección
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">
                                    Estado
                                </th>
                                <th class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider">
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php foreach ($arrendatarios as $index => $arrendatario): ?>
                                <tr class="<?php echo $index % 2 === 0 ? 'bg-white' : 'bg-slate-50/50'; ?> hover:bg-blue-50/50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700">
                                            <?php echo htmlspecialchars($arrendatario['abrev_doc']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-slate-700">
                                        <?php echo htmlspecialchars($arrendatario['numero_documento']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                        <?php echo htmlspecialchars($arrendatario['nombre_concesionario_razon_social'] ?? ''); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                        <?php echo htmlspecialchars($arrendatario['apellidos'] ?? ''); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                        <?php echo htmlspecialchars($arrendatario['nombres'] ?? ''); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                        <?php echo htmlspecialchars($arrendatario['direccion'] ?? ''); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php if ($arrendatario['estado'] == 1): ?>
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                Activo
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                                Inactivo
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center justify-center gap-2">
                                            <button class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors" title="Editar" onclick="openEditModal(<?php echo $arrendatario['id_arrendatario']; ?>, <?php echo $arrendatario['docident_id']; ?>, '<?php echo htmlspecialchars($arrendatario['numero_documento']); ?>', '<?php echo htmlspecialchars($arrendatario['abrev_doc']); ?>', '<?php echo htmlspecialchars($arrendatario['apellidos'] ?? ''); ?>', '<?php echo htmlspecialchars($arrendatario['nombres'] ?? ''); ?>', '<?php echo htmlspecialchars($arrendatario['nombre_concesionario_razon_social'] ?? ''); ?>', '<?php echo htmlspecialchars($arrendatario['direccion'] ?? ''); ?>', <?php echo $arrendatario['depaid']; ?>, <?php echo $arrendatario['provid']; ?>, <?php echo $arrendatario['distid']; ?>, <?php echo $arrendatario['estado']; ?>)">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </button>
                                            <button class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Eliminar" onclick="confirmDelete(<?php echo $arrendatario['id_arrendatario']; ?>, '<?php echo htmlspecialchars(($arrendatario['nombre_concesionario_razon_social'] ?? '') ?: ($arrendatario['apellidos'] ?? '') . ' ' . ($arrendatario['nombres'] ?? '')); ?>')">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <!-- Pagination -->
            <?php if (!empty($arrendatarios) && $total_pages > 1): ?>
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <p class="text-sm text-slate-500">
                        Mostrando <span class="font-semibold text-slate-700"><?php echo ($current_page - 1) * $records_per_page + 1; ?></span>
                        a <span class="font-semibold text-slate-700"><?php echo min($current_page * $records_per_page, $total_records); ?></span>
                        de <span class="font-semibold text-slate-700"><?php echo $total_records; ?></span> resultados
                    </p>
                    <div class="flex gap-2">
                        <?php 
                        $queryParams = array_filter([
                            'search' => $_GET['search'] ?? null,
                            'estado' => $_GET['estado'] ?? null
                        ]);
                        ?>
                        <?php if ($current_page > 1): ?>
                            <a href="?<?php echo http_build_query(array_merge($queryParams, ['page' => $current_page - 1])); ?>" class="px-4 py-2 bg-white border border-slate-200 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-50 hover:border-slate-300 transition-colors">
                                <i class="fa-solid fa-chevron-left mr-1"></i> Anterior
                            </a>
                        <?php endif; ?>
                        <?php if ($current_page < $total_pages): ?>
                            <a href="?<?php echo http_build_query(array_merge($queryParams, ['page' => $current_page + 1])); ?>" class="px-4 py-2 bg-white border border-slate-200 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-50 hover:border-slate-300 transition-colors">
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

<!-- Modal Nuevo/Editar Arrendatario -->
<div id="arrendatarioModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeModal()"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-slate-800" id="modalTitle">Registro de Arrendatario</h3>
                    <button onclick="closeModal()" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <form id="arrendatarioForm" method="POST" action="/mantenimiento/arrendatario/guardar">
                    <input type="hidden" name="id" id="arrendatarioId">
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="tipoDoc" class="block text-sm font-medium text-slate-700 mb-1">Tipo Documento</label>
                                <select name="tipoDoc" id="tipoDoc" required class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                                    <option value="">Seleccione</option>
                                    <?php foreach ($tiposDocumento as $tipo): ?>
                                        <option value="<?php echo (string)$tipo['docident_id']; ?>"><?php echo htmlspecialchars($tipo['abreviatura']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <label for="numeroDoc" class="block text-sm font-medium text-slate-700 mb-1">N° Documento</label>
                                <input type="text" name="numeroDoc" id="numeroDoc" required class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500" placeholder="N° documento">
                            </div>
                        </div>
                        <div>
                            <label for="razonSocial" class="block text-sm font-medium text-slate-700 mb-1">Concesionario o Razón Social</label>
                            <input type="text" name="razonSocial" id="razonSocial" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500" placeholder="Razón social o nombre de concesión">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="apellidos" class="block text-sm font-medium text-slate-700 mb-1">Apellidos</label>
                                <input type="text" name="apellidos" id="apellidos" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500" placeholder="Apellidos">
                            </div>
                            <div>
                                <label for="nombres" class="block text-sm font-medium text-slate-700 mb-1">Nombres</label>
                                <input type="text" name="nombres" id="nombres" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500" placeholder="Nombres">
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <label for="departamento" class="block text-sm font-medium text-slate-700 mb-1">Departamento</label>
                                <select name="departamento" id="departamento" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                                    <option value="">Seleccione</option>
                                    <?php foreach ($departamentos as $depa): ?>
                                        <option value="<?php echo (string)$depa['depaid']; ?>"><?php echo htmlspecialchars($depa['departamento']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <label for="provincia" class="block text-sm font-medium text-slate-700 mb-1">Provincia</label>
                                <select name="provincia" id="provincia" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                                    <option value="">Seleccione</option>
                                </select>
                            </div>
                            <div>
                                <label for="distrito" class="block text-sm font-medium text-slate-700 mb-1">Distrito</label>
                                <select name="distrito" id="distrito" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                                    <option value="">Seleccione</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label for="direccion" class="block text-sm font-medium text-slate-700 mb-1">Dirección</label>
                            <input type="text" name="direccion" id="direccion" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500" placeholder="Dirección">
                        </div>
                        <div>
                            <label for="estado" class="block text-sm font-medium text-slate-700 mb-1">Estado</label>
                            <select name="estado" id="estado" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex gap-3 mt-6">
                        <button type="button" onclick="closeModal()" class="flex-1 px-4 py-2.5 bg-slate-100 text-slate-700 font-medium rounded-xl hover:bg-slate-200 transition-colors">
                            Cancelar
                        </button>
                        <button type="submit" class="flex-1 px-4 py-2.5 bg-blue-600 text-white font-medium rounded-xl hover:bg-blue-700 transition-colors">
                            Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Confirmar Eliminación -->
<div id="deleteModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeDeleteModal()"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm">
            <div class="p-6 text-center">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-red-100 flex items-center justify-center">
                    <i class="fa-solid fa-triangle-exclamation text-2xl text-red-600"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-800 mb-2">Confirmar Eliminación</h3>
                <p class="text-sm text-slate-600 mb-6" id="deleteMessage">¿Está seguro de que desea eliminar este registro?</p>
                <div class="flex gap-3">
                    <button onclick="closeDeleteModal()" class="flex-1 px-4 py-2.5 bg-slate-100 text-slate-700 font-medium rounded-xl hover:bg-slate-200 transition-colors">
                        Cancelar
                    </button>
                    <a id="deleteBtn" href="#" class="flex-1 px-4 py-2.5 bg-red-600 text-white font-medium rounded-xl hover:bg-red-700 transition-colors">
                        Eliminar
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Store initial departamentos for cascading
    const departamentosData = <?php echo json_encode($departamentos); ?>;

    function openNewModal() {
        document.getElementById('modalTitle').textContent = 'Registro de Arrendatario';
        document.getElementById('arrendatarioId').value = '';
        document.getElementById('tipoDoc').value = '';
        document.getElementById('numeroDoc').value = '';
        document.getElementById('razonSocial').value = '';
        document.getElementById('apellidos').value = '';
        document.getElementById('nombres').value = '';
        document.getElementById('departamento').value = '';
        document.getElementById('provincia').innerHTML = '<option value="">Seleccione</option>';
        document.getElementById('provincia').disabled = true;
        document.getElementById('distrito').innerHTML = '<option value="">Seleccione</option>';
        document.getElementById('distrito').disabled = true;
        document.getElementById('direccion').value = '';
        document.getElementById('estado').value = '1';
        document.getElementById('arrendatarioModal').classList.remove('hidden');
        document.getElementById('arrendatarioModal').style.display = 'flex';
    }

    function openEditModal(id, tipoDocId, numeroDoc, abreviatura, apellidos, nombres, razonSocial, direccion, depaId, provId, distId, estado) {
        document.getElementById('modalTitle').textContent = 'Editar Arrendatario';
        document.getElementById('arrendatarioId').value = id;
        document.getElementById('tipoDoc').value = String(tipoDocId);
        document.getElementById('numeroDoc').value = numeroDoc;
        document.getElementById('razonSocial').value = razonSocial || '';
        document.getElementById('apellidos').value = apellidos || '';
        document.getElementById('nombres').value = nombres || '';
        document.getElementById('direccion').value = direccion || '';
        document.getElementById('estado').value = estado;
        
        // Set department and load provinces
        if (depaId > 0) {
            document.getElementById('departamento').value = String(depaId);
            loadProvincias(depaId, provId);
        } else {
            document.getElementById('departamento').value = '';
            document.getElementById('provincia').innerHTML = '<option value="">Seleccione</option>';
            document.getElementById('provincia').disabled = true;
            document.getElementById('distrito').innerHTML = '<option value="">Seleccione</option>';
            document.getElementById('distrito').disabled = true;
        }
        
        document.getElementById('arrendatarioModal').classList.remove('hidden');
        document.getElementById('arrendatarioModal').style.display = 'flex';
    }

    function loadProvincias(depaId, selectedProvId = null) {
        const provinciaSelect = document.getElementById('provincia');
        const distritoSelect = document.getElementById('distrito');
        
        if (!depaId || depaId === '') {
            provinciaSelect.innerHTML = '<option value="">Seleccione</option>';
            provinciaSelect.disabled = true;
            distritoSelect.innerHTML = '<option value="">Seleccione</option>';
            distritoSelect.disabled = true;
            return;
        }

        // Build query string for API call
        const formData = new FormData();
        formData.append('action', 'getProvincias');
        formData.append('depaid', depaId);

        fetch('/api/arrendatario/ubigeo', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            let options = '<option value="">Seleccione</option>';
            data.forEach(function(provincia) {
                const selected = selectedProvId && String(provincia.provid) === String(selectedProvId) ? 'selected' : '';
                options += `<option value="${provincia.provid}" ${selected}>${provincia.provincia}</option>`;
            });
            provinciaSelect.innerHTML = options;
            provinciaSelect.disabled = false;
            
            // If we have a selected province, load distritos
            if (selectedProvId) {
                loadDistritos(selectedProvId);
            } else {
                distritoSelect.innerHTML = '<option value="">Seleccione</option>';
                distritoSelect.disabled = true;
            }
        })
        .catch(error => {
            console.error('Error loading provinces:', error);
            provinciaSelect.innerHTML = '<option value="">Error</option>';
        });
    }

    function loadDistritos(provId, selectedDistId = null) {
        const distritoSelect = document.getElementById('distrito');
        
        if (!provId || provId === '') {
            distritoSelect.innerHTML = '<option value="">Seleccione</option>';
            distritoSelect.disabled = true;
            return;
        }

        const formData = new FormData();
        formData.append('action', 'getDistritos');
        formData.append('provid', provId);

        fetch('/api/arrendatario/ubigeo', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            let options = '<option value="">Seleccione</option>';
            data.forEach(function(distrito) {
                const selected = selectedDistId && String(distrito.distid) === String(selectedDistId) ? 'selected' : '';
                options += `<option value="${distrito.distid}" ${selected}>${distrito.distrito}</option>`;
            });
            distritoSelect.innerHTML = options;
            distritoSelect.disabled = false;
        })
        .catch(error => {
            console.error('Error loading districts:', error);
            distritoSelect.innerHTML = '<option value="">Error</option>';
        });
    }

    // Event listeners for cascading dropdowns
    document.getElementById('departamento').addEventListener('change', function() {
        loadProvincias(this.value);
    });

    document.getElementById('provincia').addEventListener('change', function() {
        loadDistritos(this.value);
    });

    function closeModal() {
        document.getElementById('arrendatarioModal').classList.add('hidden');
        document.getElementById('arrendatarioModal').style.display = 'none';
    }

    function confirmDelete(id, nombre) {
        document.getElementById('deleteMessage').textContent = '¿Está seguro de eliminar a "' + nombre + '"? Esta acción no se puede deshacer.';
        document.getElementById('deleteBtn').href = '/mantenimiento/arrendatario/eliminar?id=' + id;
        document.getElementById('deleteModal').classList.remove('hidden');
        document.getElementById('deleteModal').style.display = 'flex';
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
        document.getElementById('deleteModal').style.display = 'none';
    }
</script>

</body>
</html>