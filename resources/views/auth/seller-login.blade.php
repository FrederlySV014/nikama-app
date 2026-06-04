<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar Sesión Socios - Nikama</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700|outfit:400,600,700,800&display=swap" rel="stylesheet" />
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>
<body class="antialiased font-sans flex flex-col min-h-screen bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 transition-colors duration-300">
    <div class="min-h-screen flex flex-col lg:flex-row">
        <!-- Columna Izquierda: Imagen (Oculta en móvil) -->
        <div class="hidden lg:block lg:w-1/2 h-screen sticky top-0 overflow-hidden bg-slate-100 dark:bg-slate-900">
            <img src="{{ asset('nikama-sellers-auth.jpg') }}" alt="Nikama Vendedores" class="w-full h-full object-cover">
        </div>

        <!-- Columna Derecha: Formulario (100% en móvil, 50% en desktop) -->
        <div class="w-full lg:w-1/2 min-h-screen flex items-center justify-center p-6 sm:p-12 md:p-16 lg:p-20 overflow-y-auto relative">
            <!-- Decoraciones de fondo -->
            <div class="absolute top-1/4 left-1/4 w-[250px] h-[250px] bg-luffy-straw/5 blur-[80px] rounded-full pointer-events-none"></div>
            <div class="absolute bottom-1/4 right-1/4 w-[250px] h-[250px] bg-luffy-red/5 blur-[80px] rounded-full pointer-events-none"></div>

            <div class="w-full max-w-md space-y-8 z-10">
                <!-- Encabezado con Logo y Título -->
                <div class="text-center">
                    <a href="{{ route('public.welcome') }}" class="inline-block hover:scale-105 transition-transform mb-4">
                        <x-logo class="w-16 h-16 text-luffy-straw" />
                    </a>
                    <h1 class="text-3xl font-extrabold font-['Outfit'] text-slate-900 dark:text-white tracking-tight">
                        Portal de Socios Negocios
                    </h1>
                    <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">
                        Gestiona tus pedidos y haz crecer tu negocio con Nikama.
                    </p>
                </div>

                <!-- Bloque de Errores Globales (Parte Superior) -->
                @if ($errors->any())
                    <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 text-sm font-semibold">
                        <p class="font-extrabold mb-1">Por favor corrige los siguientes errores:</p>
                        <ul class="list-disc pl-5 space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if(session('success'))
                    <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-sm font-semibold">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Formulario -->
                <form class="space-y-6" action="{{ route('seller.login.post') }}" method="POST">
                    @csrf
                    
                    <div class="space-y-4">
                        <div>
                            <label for="email" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">Correo electrónico del Socio</label>
                            <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}"
                                class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-900 dark:text-white font-medium focus:outline-none focus:ring-2 focus:ring-luffy-straw focus:border-transparent transition-all"
                                placeholder="socio@correo.com">
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">Contraseña</label>
                            <input id="password" name="password" type="password" autocomplete="current-password" required
                                class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-900 dark:text-white font-medium focus:outline-none focus:ring-2 focus:ring-luffy-straw focus:border-transparent transition-all"
                                placeholder="••••••••">
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-sm">
                        <div class="flex items-center">
                            <input id="remember" name="remember" type="checkbox"
                                class="h-4 w-4 rounded border-gray-300 text-luffy-straw focus:ring-luffy-straw cursor-pointer">
                            <label for="remember" class="ml-2 block text-slate-700 dark:text-slate-300 font-medium cursor-pointer">Recordarme</label>
                        </div>

                        <div class="text-sm">
                            <a href="#" class="font-bold text-luffy-straw-dark hover:text-luffy-straw transition">¿Olvidaste tu contraseña?</a>
                        </div>
                    </div>

                    <div>
                        <button type="submit"
                            class="group relative w-full flex justify-center py-3.5 px-4 border border-transparent rounded-xl text-white bg-luffy-straw-dark hover:bg-luffy-straw-hover font-bold text-lg transition-all shadow-lg shadow-luffy-straw-dark/20 hover:scale-[1.02] active:scale-[0.98] cursor-pointer">
                            Ingresar al Portal
                        </button>
                    </div>
                </form>

                <div class="text-center text-sm">
                    <span class="text-slate-600 dark:text-slate-400">¿Quieres vender con nosotros?</span>
                    <a href="{{ route('seller.register') }}" class="font-bold text-luffy-straw-dark hover:text-luffy-straw transition ml-1">Registra tu negocio</a>
                </div>

                <!-- Accesos a otros portales -->
                <div class="pt-6 border-t border-gray-200 dark:border-slate-800 flex justify-between gap-4 text-xs font-bold text-slate-500">
                    <a href="{{ route('login') }}" class="hover:text-slate-800 dark:hover:text-white transition">Acceso Clientes</a>
                    <a href="{{ route('driver.login') }}" class="hover:text-slate-800 dark:hover:text-white transition">Acceso Repartidores</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
