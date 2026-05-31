<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registra tu Negocio - Nikama</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'luffy-red': '#E63946',
                        'luffy-red-hover': '#D62839',
                        'luffy-straw': '#F4D35E',
                        'luffy-straw-hover': '#E9C44E',
                    }
                }
            }
        }
    </script>
</head>
<body class="min-h-screen flex">
    <!-- Mitad izquierda: Imagen -->
    <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-luffy-red to-luffy-red-hover relative overflow-hidden items-center justify-center">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMzAiIGN5PSIzMCIgcj0iMiIgZmlsbD0id2hpdGUiIGZpbGwtb3BhY2l0eT0iMC4xIi8+PC9zdmc+')] opacity-30"></div>
        <div class="relative z-10 text-center p-12">
            <div class="w-24 h-24 mx-auto mb-8 bg-white rounded-full flex items-center justify-center">
                <svg class="w-16 h-16 text-luffy-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            </div>
            <h2 class="text-4xl font-bold text-white mb-4">¡Únete a Nikama!</h2>
            <p class="text-white/90 text-lg max-w-md mx-auto mb-8">Alcanza más clientes y aumenta tus ventas con nuestra plataforma de delivery.</p>
            <div class="flex flex-col gap-4 text-white/80 text-left mx-auto max-w-xs">
                <div class="flex items-center gap-3">
                    <svg class="w-6 h-6 text-luffy-straw flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>Gestiona tus productos fácilmente</span>
                </div>
                <div class="flex items-center gap-3">
                    <svg class="w-6 h-6 text-luffy-straw flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>Recibe pedidos en tiempo real</span>
                </div>
                <div class="flex items-center gap-3">
                    <svg class="w-6 h-6 text-luffy-straw flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>Control total de tu inventario</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Mitad derecha: Formulario -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-white">
        <div class="w-full max-w-md">
            <div class="lg:hidden text-center mb-8">
                <div class="w-16 h-16 mx-auto mb-4 bg-luffy-red rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
            </div>

            <h1 class="text-3xl font-bold text-gray-900 mb-2">Registra tu negocio</h1>
            <p class="text-gray-600 mb-8">Completa los datos de tu empresa para comenzar a vender</p>

            <form method="POST" action="#" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nombre del negocio</label>
                    <input type="text" name="nombre_negocio" required class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-luffy-red focus:border-transparent" placeholder="Ej: Pizzería Don Juan">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">RUC / DNI</label>
                    <input type="text" name="ruc" required class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-luffy-red focus:border-transparent" placeholder="Ej: 20123456789">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Teléfono</label>
                    <input type="tel" name="telefono" required class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-luffy-red focus:border-transparent" placeholder="Ej: 987654321">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Correo electrónico</label>
                    <input type="email" name="email" required class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-luffy-red focus:border-transparent" placeholder="correo@ejemplo.com">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Dirección</label>
                    <input type="text" name="direccion" required class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-luffy-red focus:border-transparent" placeholder="Av. Principal 123">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Contraseña</label>
                    <input type="password" name="password" required class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-luffy-red focus:border-transparent" placeholder="••••••••">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Confirmar contraseña</label>
                    <input type="password" name="password_confirmation" required class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-luffy-red focus:border-transparent" placeholder="••••••••">
                </div>

                <button type="submit" class="w-full bg-luffy-red hover:bg-luffy-red-hover text-white font-bold py-3 px-6 rounded-xl transition-all hover:scale-[1.02] active:scale-[0.98]">
                    Crear cuenta de negocio
                </button>
            </form>

            <p class="mt-6 text-center text-gray-600">
                ¿Ya tienes cuenta? <a href="/login" class="text-luffy-red hover:underline font-medium">Iniciar sesión</a>
            </p>
        </div>
    </div>
</body>
</html>