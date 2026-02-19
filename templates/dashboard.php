<?php $pageTitle = 'Dashboard | La Vitrina'; require_once __DIR__ . '/partials/header.php'; ?>

<?php require_once __DIR__ . '/partials/navbar.php'; ?>

<!-- Contenido Principal -->
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 sm:px-0">
        <!-- Welcome Card -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-2xl shadow-xl shadow-blue-600/20 p-6 mb-6 text-white">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center">
                    <i class="fa-solid fa-hand-sparkles text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold">Bienvenido al Módulo Web</h3>
                    <p class="text-blue-100 text-sm">Sesión iniciada correctamente. Seleccione una opción del menú para comenzar.</p>
                </div>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow-lg shadow-slate-200/60 border border-slate-100 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Sucursal Actual</p>
                        <p class="text-lg font-bold text-slate-800 mt-1"><?php echo htmlspecialchars($_SESSION['sucursal'] ?? 'N/A'); ?></p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center">
                        <i class="fa-solid fa-building text-blue-600"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-lg shadow-slate-200/60 border border-slate-100 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Caja Asignada</p>
                        <p class="text-lg font-bold text-slate-800 mt-1"><?php echo htmlspecialchars($_SESSION['caja'] ?? 'N/A'); ?></p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center">
                        <i class="fa-solid fa-cash-register text-emerald-600"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-lg shadow-slate-200/60 border border-slate-100 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Perfil</p>
                        <p class="text-lg font-bold text-slate-800 mt-1"><?php echo htmlspecialchars($_SESSION['profile_id'] ?? '0'); ?></p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center">
                        <i class="fa-solid fa-user-shield text-purple-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-2xl shadow-xl shadow-slate-200/60 border border-slate-100 p-6">
            <h4 class="text-lg font-bold text-slate-800 mb-4">Accesos Rápidos</h4>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <a href="/contratos/listado" class="flex flex-col items-center gap-2 p-4 rounded-xl bg-slate-50 hover:bg-blue-50 hover:shadow-md transition-all duration-200 group">
                    <div class="w-12 h-12 rounded-xl bg-blue-100 group-hover:bg-blue-200 flex items-center justify-center transition-colors">
                        <i class="fa-solid fa-file-contract text-blue-600 text-xl"></i>
                    </div>
                    <span class="text-sm font-medium text-slate-700 group-hover:text-blue-700">Contratos</span>
                </a>
                <a href="/mantenimiento/arrendatarios" class="flex flex-col items-center gap-2 p-4 rounded-xl bg-slate-50 hover:bg-emerald-50 hover:shadow-md transition-all duration-200 group">
                    <div class="w-12 h-12 rounded-xl bg-emerald-100 group-hover:bg-emerald-200 flex items-center justify-center transition-colors">
                        <i class="fa-solid fa-users text-emerald-600 text-xl"></i>
                    </div>
                    <span class="text-sm font-medium text-slate-700 group-hover:text-emerald-700">Arrendatarios</span>
                </a>
                <a href="/mantenimiento/arrendadores" class="flex flex-col items-center gap-2 p-4 rounded-xl bg-slate-50 hover:bg-purple-50 hover:shadow-md transition-all duration-200 group">
                    <div class="w-12 h-12 rounded-xl bg-purple-100 group-hover:bg-purple-200 flex items-center justify-center transition-colors">
                        <i class="fa-solid fa-building-user text-purple-600 text-xl"></i>
                    </div>
                    <span class="text-sm font-medium text-slate-700 group-hover:text-purple-700">Arrendadores</span>
                </a>
                <a href="/mantenimiento/puesto_comercial" class="flex flex-col items-center gap-2 p-4 rounded-xl bg-slate-50 hover:bg-amber-50 hover:shadow-md transition-all duration-200 group">
                    <div class="w-12 h-12 rounded-xl bg-amber-100 group-hover:bg-amber-200 flex items-center justify-center transition-colors">
                        <i class="fa-solid fa-shop text-amber-600 text-xl"></i>
                    </div>
                    <span class="text-sm font-medium text-slate-700 group-hover:text-amber-700">Puestos</span>
                </a>
            </div>
        </div>
    </div>
</div>

</body>
</html>