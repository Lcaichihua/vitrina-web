<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Arrendadores | La Vitrina</title>
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
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h3 class="text-2xl font-bold text-slate-800">Arrendadores</h3>
                <p class="text-sm text-slate-500 mt-1">Gestiona los arrendadores del sistema</p>
            </div>
            <button onclick="openNewModal()" class="group relative inline-flex items-center gap-2.5 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold py-2.5 px-5 rounded-xl shadow-lg shadow-blue-600/25 transition-all duration-200 hover:shadow-blue-600/40 hover:-translate-y-0.5 active:translate-y-0 active:shadow-md">
                <span class="absolute inset-0 rounded-xl bg-white/20 opacity-0 group-hover:opacity-100 transition-opacity"></span>
                <span class="w-5 h-5 rounded-full bg-white/20 flex items-center justify-center">
                    <i class="fa-solid fa-plus text-xs"></i>
                </span>
                Nuevo Arrendador
            </button>
        </div>

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

        <?php if (isset($error) && $error): ?>
            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg mb-6 animate-pulse">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-circle-exclamation text-red-500"></i>
                    <p class="text-red-700 font-medium"><?php echo htmlspecialchars($error); ?></p>
                </div>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-2xl shadow-xl shadow-slate-200/60 border border-slate-100 overflow-hidden">
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
                        <button type="submit" class="px-4 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-medium hover:bg-blue-700 transition-colors">
                            <i class="fa-solid fa-search"></i>
                        </button>
                        <?php if (!empty($_GET['search'])): ?>
                            <a href="?" class="px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm text-slate-600 hover:bg-slate-50 hover:border-slate-300 transition-colors">
                                <i class="fa-solid fa-rotate-left"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </form>

            <?php if (empty($arrendadores)): ?>
                <div class="p-12 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-100 mb-4">
                        <i class="fa-solid fa-building-slash text-2xl text-slate-400"></i>
                    </div>
                    <h4 class="text-lg font-semibold text-slate-700 mb-2">No hay arrendadores registrados</h4>
                    <p class="text-slate-500">Comienza agregando un nuevo arrendador</p>
                </div>
            <?php else: ?>
                <div class="table-container">
                    <table class="min-w-full">
                        <thead class="bg-slate-800 text-white">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Tipo Doc.</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Nº Documento</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Apellidos</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Nombres</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Dirección</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Desde</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Hasta</th>
                                <th class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php foreach ($arrendadores as $index => $arrendador): ?>
                                <tr class="<?php echo $index % 2 === 0 ? 'bg-white' : 'bg-slate-50/50'; ?> hover:bg-blue-50/50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700">
                                            <?php echo htmlspecialchars($arrendador['abrev_doc']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-slate-700">
                                        <?php echo htmlspecialchars($arrendador['numero_documento']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                        <?php echo htmlspecialchars($arrendador['apellidos']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                        <?php echo htmlspecialchars($arrendador['nombres']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                        <?php echo htmlspecialchars($arrendador['direccion']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                        <?php echo htmlspecialchars($arrendador['desde']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                        <?php echo htmlspecialchars($arrendador['hasta']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center justify-center gap-2">
                                            <button class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors" title="Editar" onclick="openEditModal(<?php echo $arrendador['id_arrendador']; ?>, '<?php echo htmlspecialchars($arrendador['abrev_doc']); ?>', '<?php echo htmlspecialchars($arrendador['numero_documento']); ?>', '<?php echo htmlspecialchars($arrendador['apellidos']); ?>', '<?php echo htmlspecialchars($arrendador['nombres']); ?>', '<?php echo htmlspecialchars($arrendador['direccion']); ?>')">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </button>
                                            <button class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Eliminar" onclick="confirmDelete(<?php echo $arrendador['id_arrendador']; ?>, '<?php echo htmlspecialchars($arrendador['apellidos'] . ' ' . $arrendador['nombres']); ?>')">
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

            <?php if (!empty($arrendadores) && $total_pages > 1): ?>
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <p class="text-sm text-slate-500">
                        Mostrando <span class="font-semibold text-slate-700"><?php echo ($current_page - 1) * $records_per_page + 1; ?></span>
                        a <span class="font-semibold text-slate-700"><?php echo min($current_page * $records_per_page, $total_records); ?></span>
                        de <span class="font-semibold text-slate-700"><?php echo $total_records; ?></span> resultados
                    </p>
                    <div class="flex gap-2">
                        <?php 
                        $queryParams = array_filter([
                            'search' => $_GET['search'] ?? null
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

<!-- Modal Nuevo/Editar Arrendador -->
<div id="arrendadorModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeModal()"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-slate-800" id="modalTitle">Nuevo Arrendador</h3>
                    <button onclick="closeModal()" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <form id="arrendadorForm" method="POST" action="/mantenimiento/arrendador/guardar">
                    <input type="hidden" name="id" id="arrendadorId">
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="tipoDoc" class="block text-sm font-medium text-slate-700 mb-1">Tipo Documento</label>
                                <select name="tipoDoc" id="tipoDoc" required class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                                    <option value="DNI">DNI</option>
                                    <option value="RUC">RUC</option>
                                    <option value="CE">Carnet Extranjería</option>
                                </select>
                            </div>
                            <div>
                                <label for="numeroDoc" class="block text-sm font-medium text-slate-700 mb-1">Nº Documento</label>
                                <input type="text" name="numeroDoc" id="numeroDoc" required class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500" placeholder="Nº documento">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="apellidos" class="block text-sm font-medium text-slate-700 mb-1">Apellidos</label>
                                <input type="text" name="apellidos" id="apellidos" required class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500" placeholder="Apellidos">
                            </div>
                            <div>
                                <label for="nombres" class="block text-sm font-medium text-slate-700 mb-1">Nombres</label>
                                <input type="text" name="nombres" id="nombres" required class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500" placeholder="Nombres">
                            </div>
                        </div>
                        <div>
                            <label for="direccion" class="block text-sm font-medium text-slate-700 mb-1">Dirección</label>
                            <input type="text" name="direccion" id="direccion" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500" placeholder="Dirección">
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
    function openNewModal() {
        document.getElementById('modalTitle').textContent = 'Nuevo Arrendador';
        document.getElementById('arrendadorId').value = '';
        document.getElementById('tipoDoc').value = 'DNI';
        document.getElementById('numeroDoc').value = '';
        document.getElementById('apellidos').value = '';
        document.getElementById('nombres').value = '';
        document.getElementById('direccion').value = '';
        document.getElementById('arrendadorModal').classList.remove('hidden');
        document.getElementById('arrendadorModal').style.display = 'flex';
    }

    function openEditModal(id, tipoDoc, numeroDoc, apellidos, nombres, direccion) {
        document.getElementById('modalTitle').textContent = 'Editar Arrendador';
        document.getElementById('arrendadorId').value = id;
        document.getElementById('tipoDoc').value = tipoDoc;
        document.getElementById('numeroDoc').value = numeroDoc;
        document.getElementById('apellidos').value = apellidos;
        document.getElementById('nombres').value = nombres;
        document.getElementById('direccion').value = direccion;
        document.getElementById('arrendadorModal').classList.remove('hidden');
        document.getElementById('arrendadorModal').style.display = 'flex';
    }

    function closeModal() {
        document.getElementById('arrendadorModal').classList.add('hidden');
        document.getElementById('arrendadorModal').style.display = 'none';
    }

    function confirmDelete(id, nombre) {
        document.getElementById('deleteMessage').textContent = '¿Está seguro de eliminar a "' + nombre + '"? Esta acción no se puede deshacer.';
        document.getElementById('deleteBtn').href = '/mantenimiento/arrendador/eliminar?id=' + id;
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