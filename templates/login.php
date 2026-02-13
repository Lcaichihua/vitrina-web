<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso al Sistema | La Vitrina</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .brand-gradient { background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%); }
    </style>
</head>
<body class="bg-gray-100 h-screen flex overflow-hidden">

<!-- Panel Izquierdo (Branding) -->
<div class="hidden md:flex md:w-1/2 brand-gradient items-center justify-center text-white p-12 relative">
    <div class="z-10 text-center">
        <h1 class="text-5xl font-bold mb-6 tracking-tight">La Vitrina</h1>
        <p class="text-blue-100 text-xl font-light">Gestión Administrativa y de Contratos</p>
    </div>
    <!-- Decoración de fondo -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden opacity-20 pointer-events-none">
        <div class="absolute top-10 left-10 w-40 h-40 bg-white rounded-full mix-blend-overlay filter blur-xl"></div>
        <div class="absolute bottom-10 right-10 w-60 h-60 bg-blue-400 rounded-full mix-blend-overlay filter blur-xl"></div>
    </div>
</div>

<!-- Panel Derecho (Formulario) -->
<div class="w-full md:w-1/2 flex items-center justify-center bg-white p-8 overflow-y-auto">
    <div class="w-full max-w-md space-y-8">

        <div class="text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-blue-100 mb-4">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
            </div>
            <h2 class="text-3xl font-bold text-gray-900">Iniciar Sesión</h2>
            <p class="mt-2 text-sm text-gray-600">Ingrese sus credenciales para acceder</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-md animate-pulse">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <!-- Icono de Error -->
                        <svg class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700 font-medium"><?php echo htmlspecialchars($error); ?></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <form class="mt-8 space-y-6" action="/login" method="POST">
            <div class="rounded-md shadow-sm -space-y-px">
                <!-- Select Empresa -->
                <div class="relative">
                    <label for="empresa" class="sr-only">Empresa</label>
                    <select id="empresa" name="empresa" required class="appearance-none rounded-t-md relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm">
                        <?php if (!empty($empresas)): ?>
                            <?php foreach($empresas as $emp): ?>
                                <option value="<?php echo $emp['id_empresa']; ?>">
                                    <?php echo htmlspecialchars($emp['nombre_empresa']); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="">No hay empresas disponibles</option>
                        <?php endif; ?>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                    </div>
                </div>

                <!-- Usuario -->
                <div>
                    <label for="usuario" class="sr-only">Usuario</label>
                    <input id="usuario" name="usuario" type="text" required class="appearance-none relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm" placeholder="Usuario">
                </div>

                <!-- Contraseña -->
                <div>
                    <label for="password" class="sr-only">Contraseña</label>
                    <input id="password" name="password" type="password" required class="appearance-none rounded-b-md relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm" placeholder="Contraseña">
                </div>
            </div>

            <div>
                <button type="submit" class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <svg class="h-5 w-5 text-blue-500 group-hover:text-blue-400 transition ease-in-out duration-150" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                            </svg>
                        </span>
                    Ingresar al Sistema
                </button>
            </div>
        </form>

        <div class="text-center mt-4">
            <p class="text-xs text-gray-400">© <?php echo date('Y'); ?> Sistema de Gestión</p>
        </div>
    </div>
</div>
</body>
</html>