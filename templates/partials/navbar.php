<?php
$currentUri = $_SERVER['REQUEST_URI'];
$currentUri = strtok($currentUri, '?'); // Sin query strings
$currentUri = str_replace('#', '', $currentUri); // Sin hash
?>

<!-- Navbar -->
<nav class="bg-white shadow">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="flex-shrink-0 flex items-center">
                    <span class="font-bold text-xl text-blue-800">La Vitrina</span>
                </div>
                <!-- Menú de Navegación -->
                <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                    <a href="/dashboard" class="<?php echo ($currentUri === '/dashboard' || $currentUri === '/') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'; ?> inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                        Inicio
                    </a>
                    <a href="/contratos/listado" class="<?php echo (strpos($currentUri, '/contratos/') !== false) ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'; ?> inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                        Contratos
                    </a>
                    <!-- Menú Mantenimiento -->
                    <div class="relative group inline-flex">
                        <a href="#" id="maintenance-dropdown-toggle" class="<?php echo (strpos($currentUri, '/mantenimiento/') !== false) ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'; ?> inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium focus:outline-none">
                            Mantenimiento
                            <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </a>
                        <div id="maintenance-dropdown-menu" class="absolute z-10 left-0 top-full w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none hidden">
                            <div class="py-1" role="menu" aria-orientation="vertical" aria-labelledby="options-menu">
                                <a href="/mantenimiento/tipo_puesto_comercial" class="block px-4 py-2 text-sm <?php echo ($currentUri === '/mantenimiento/tipo_puesto_comercial') ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900'; ?>" role="menuitem">Tipo puesto comercial</a>
                                <a href="/mantenimiento/puesto_comercial" class="block px-4 py-2 text-sm <?php echo ($currentUri === '/mantenimiento/puesto_comercial') ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900'; ?>" role="menuitem">Puesto comercial</a>
                                <a href="/mantenimiento/arrendadores" class="block px-4 py-2 text-sm <?php echo ($currentUri === '/mantenimiento/arrendadores') ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900'; ?>" role="menuitem">Arrendadores</a>
                                <a href="/mantenimiento/arrendatarios" class="block px-4 py-2 text-sm <?php echo ($currentUri === '/mantenimiento/arrendatarios') ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900'; ?>" role="menuitem">Arrendatarios</a>
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleButton = document.getElementById('maintenance-dropdown-toggle');
        const dropdownMenu = document.getElementById('maintenance-dropdown-menu');

        if (toggleButton && dropdownMenu) {
            toggleButton.addEventListener('click', function(event) {
                event.preventDefault();
                dropdownMenu.classList.toggle('hidden');
            });

            document.addEventListener('click', function(event) {
                if (!dropdownMenu.contains(event.target) && !toggleButton.contains(event.target)) {
                    dropdownMenu.classList.add('hidden');
                }
            });
        }
    });
</script>
