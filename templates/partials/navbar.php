<?php
$currentUri = $_SERVER['REQUEST_URI'];
$currentUri = strtok($currentUri, '?');
$currentUri = str_replace('#', '', $currentUri);
?>
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<!-- Navbar -->
<nav class="bg-white shadow-lg sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="flex-shrink-0 flex items-center">
                    <span class="font-bold text-xl text-blue-800">La Vitrina</span>
                </div>
                <!-- Menú de Navegación - Desktop -->
                <div class="hidden md:ml-6 md:flex md:items-center md:space-x-1">
                    <a href="/dashboard" class="<?php echo ($currentUri === '/dashboard' || $currentUri === '/') ? 'border-blue-500 text-gray-900 bg-blue-50' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 hover:bg-gray-50'; ?> inline-flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150">
                        <i class="fa-solid fa-house mr-2"></i>Inicio
                    </a>
                    <a href="/contratos/listado" class="<?php echo (strpos($currentUri, '/contratos/') !== false) ? 'border-blue-500 text-gray-900 bg-blue-50' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 hover:bg-gray-50'; ?> inline-flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150">
                        <i class="fa-solid fa-file-contract mr-2"></i>Contratos
                    </a>
                    <!-- Menú Mantenimiento -->
                    <div class="relative" x-data="{ open: false }">
                        <button id="maintenance-dropdown-toggle" @click="open = !open"
                            class="<?php echo (strpos($currentUri, '/mantenimiento/') !== false) ? 'border-blue-500 text-gray-900 bg-blue-50' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 hover:bg-gray-50'; ?> inline-flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150 focus:outline-none">
                            <i class="fa-solid fa-gears mr-2"></i>Mantenimiento
                            <svg class="ml-1.5 h-4 w-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div id="maintenance-dropdown-menu" x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95"
                            class="absolute left-0 top-full mt-1 w-56 rounded-xl shadow-xl bg-white ring-1 ring-black ring-opacity-5" style="display: none;">
                            <div class="py-2" role="menu">
                                <a href="/mantenimiento/tipo_puesto_comercial" class="flex items-center px-4 py-2.5 text-sm <?php echo ($currentUri === '/mantenimiento/tipo_puesto_comercial') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900'; ?>" role="menuitem">
                                    <i class="fa-solid fa-layer-group w-6 text-gray-400"></i>
                                    Tipo puesto comercial
                                </a>
                                <a href="/mantenimiento/puesto_comercial" class="flex items-center px-4 py-2.5 text-sm <?php echo ($currentUri === '/mantenimiento/puesto_comercial') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900'; ?>" role="menuitem">
                                    <i class="fa-solid fa-shop w-6 text-gray-400"></i>
                                    Puesto comercial
                                </a>
                                <a href="/mantenimiento/arrendadores" class="flex items-center px-4 py-2.5 text-sm <?php echo ($currentUri === '/mantenimiento/arrendadores') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900'; ?>" role="menuitem">
                                    <i class="fa-solid fa-building-user w-6 text-gray-400"></i>
                                    Arrendadores
                                </a>
                                <a href="/mantenimiento/arrendatarios" class="flex items-center px-4 py-2.5 text-sm <?php echo ($currentUri === '/mantenimiento/arrendatarios') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900'; ?>" role="menuitem">
                                    <i class="fa-solid fa-users w-6 text-gray-400"></i>
                                    Arrendatarios
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Información del Usuario -->
            <div class="hidden md:flex items-center">
                <div class="flex items-center gap-4">
                    <div class="text-right">
                        <p class="text-sm font-semibold text-gray-900"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Usuario'); ?></p>
                        <p class="text-xs text-gray-500"><?php echo htmlspecialchars($_SESSION['empresa_nombre'] ?? 'Empresa'); ?></p>
                    </div>
                    <a href="/logout" class="text-sm text-red-600 hover:text-red-800 font-medium px-3 py-2 rounded-lg hover:bg-red-50 transition-colors">
                        <i class="fa-solid fa-right-from-bracket mr-1"></i>Salir
                    </a>
                </div>
            </div>
            <!-- Botón Mobile Menu -->
            <div class="flex items-center md:hidden">
                <button id="mobile-menu-btn" class="inline-flex items-center justify-center p-2 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500 transition-colors">
                    <span class="sr-only">Abrir menú</span>
                    <i class="fa-solid fa-bars text-xl" id="menu-icon"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div id="mobile-menu" class="hidden md:hidden bg-white border-t border-gray-100">
        <div class="px-4 py-4 space-y-3">
            <!-- User Info Mobile -->
            <div class="flex items-center gap-3 pb-3 border-b border-gray-100">
                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                    <i class="fa-solid fa-user text-blue-600"></i>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-900"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Usuario'); ?></p>
                    <p class="text-xs text-gray-500"><?php echo htmlspecialchars($_SESSION['empresa_nombre'] ?? 'Empresa'); ?></p>
                </div>
            </div>
            <!-- Nav Links -->
            <a href="/dashboard" class="flex items-center px-3 py-2.5 rounded-lg text-base font-medium <?php echo ($currentUri === '/dashboard' || $currentUri === '/') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50'; ?>">
                <i class="fa-solid fa-house w-6"></i>Inicio
            </a>
            <a href="/contratos/listado" class="flex items-center px-3 py-2.5 rounded-lg text-base font-medium <?php echo (strpos($currentUri, '/contratos/') !== false) ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50'; ?>">
                <i class="fa-solid fa-file-contract w-6"></i>Contratos
            </a>
            <!-- Mantenimiento Accordion -->
            <div class="border-t border-gray-100 pt-2">
                <button id="mobile-maintenance-btn" class="flex items-center justify-between w-full px-3 py-2.5 rounded-lg text-base font-medium text-gray-700 hover:bg-gray-50">
                    <span class="flex items-center">
                        <i class="fa-solid fa-gears w-6"></i>Mantenimiento
                    </span>
                    <i class="fa-solid fa-chevron-down transition-transform duration-200" id="maintenance-chevron"></i>
                </button>
                <div id="mobile-maintenance-menu" class="hidden pl-4 mt-1 space-y-1">
                    <a href="/mantenimiento/tipo_puesto_comercial" class="flex items-center px-3 py-2 rounded-lg text-sm font-medium <?php echo ($currentUri === '/mantenimiento/tipo_puesto_comercial') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50'; ?>">
                        <i class="fa-solid fa-layer-group w-5 mr-2"></i>Tipo puesto comercial
                    </a>
                    <a href="/mantenimiento/puesto_comercial" class="flex items-center px-3 py-2 rounded-lg text-sm font-medium <?php echo ($currentUri === '/mantenimiento/puesto_comercial') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50'; ?>">
                        <i class="fa-solid fa-shop w-5 mr-2"></i>Puesto comercial
                    </a>
                    <a href="/mantenimiento/arrendadores" class="flex items-center px-3 py-2 rounded-lg text-sm font-medium <?php echo ($currentUri === '/mantenimiento/arrendadores') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50'; ?>">
                        <i class="fa-solid fa-building-user w-5 mr-2"></i>Arrendadores
                    </a>
                    <a href="/mantenimiento/arrendatarios" class="flex items-center px-3 py-2 rounded-lg text-sm font-medium <?php echo ($currentUri === '/mantenimiento/arrendatarios') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50'; ?>">
                        <i class="fa-solid fa-users w-5 mr-2"></i>Arrendatarios
                    </a>
                </div>
            </div>
            <!-- Logout -->
            <div class="border-t border-gray-100 pt-2">
                <a href="/logout" class="flex items-center px-3 py-2.5 rounded-lg text-base font-medium text-red-600 hover:bg-red-50">
                    <i class="fa-solid fa-right-from-bracket w-6"></i>Cerrar Sesión
                </a>
            </div>
        </div>
    </div>
</nav>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        const menuIcon = document.getElementById('menu-icon');
        const mobileMaintenanceBtn = document.getElementById('mobile-maintenance-btn');
        const mobileMaintenanceMenu = document.getElementById('mobile-maintenance-menu');
        const maintenanceChevron = document.getElementById('maintenance-chevron');

        // Toggle Mobile Menu
        if (mobileMenuBtn && mobileMenu) {
            mobileMenuBtn.addEventListener('click', function() {
                mobileMenu.classList.toggle('hidden');
                if (mobileMenu.classList.contains('hidden')) {
                    menuIcon.classList.remove('fa-xmark');
                    menuIcon.classList.add('fa-bars');
                } else {
                    menuIcon.classList.remove('fa-bars');
                    menuIcon.classList.add('fa-xmark');
                }
            });
        }

        // Toggle Maintenance Menu Mobile
        if (mobileMaintenanceBtn && mobileMaintenanceMenu) {
            mobileMaintenanceBtn.addEventListener('click', function() {
                mobileMaintenanceMenu.classList.toggle('hidden');
                maintenanceChevron.classList.toggle('rotate-180');
            });
        }
    });
</script>
