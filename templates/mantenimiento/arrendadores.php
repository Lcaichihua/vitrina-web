<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Arrendadores | La Vitrina</title>
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
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h3 class="text-2xl font-bold text-slate-800">Arrendadores</h3>
                <p class="text-sm text-slate-500 mt-1">Gestiona los arrendadores del sistema</p>
            </div>
            <button class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 px-5 rounded-xl shadow-lg shadow-blue-600/20 transition-all duration-200 hover:shadow-blue-600/30 hover:-translate-y-0.5">
                <i class="fa-solid fa-plus"></i>
                Nuevo Arrendador
            </button>
        </div>

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
                                            <button class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors" title="Editar">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </button>
                                            <button class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Eliminar">
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

</body>
</html>