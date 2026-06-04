<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Acceso Administrativo - Nikama</title>
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
            <img src="{{ asset('nikama-admin-auth.jpg') }}" alt="Nikama Administración" class="w-full h-full object-cover">
        </div>

        <!-- Columna Derecha: Formulario (100% en móvil, 50% en desktop) -->
        <div class="w-full lg:w-1/2 min-h-screen flex items-center justify-center p-6 sm:p-12 md:p-16 lg:p-20 overflow-y-auto relative">
            <!-- Decoraciones de fondo -->
            <div class="absolute top-1/4 left-1/4 w-[250px] h-[250px] bg-slate-800/5 blur-[80px] rounded-full pointer-events-none"></div>
            <div class="absolute bottom-1/4 right-1/4 w-[250px] h-[250px] bg-luffy-red/5 blur-[80px] rounded-full pointer-events-none"></div>

            <div class="w-full max-w-md space-y-8 z-10">
                <!-- Encabezado con Logo y Título -->
                <div class="text-center">
                    <a href="{{ route('public.welcome') }}" class="inline-block hover:scale-105 transition-transform mb-4">
                        <x-logo class="w-16 h-16 text-slate-700 dark:text-slate-300" />
                    </a>
                    <div class="flex justify-center mb-2">
                        <span class="inline-block px-3 py-1 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs tracking-wider uppercase">Acceso Administrativo</span>
                    </div>
                    <h1 class="text-3xl font-extrabold font-['Outfit'] text-slate-900 dark:text-white tracking-tight">
                        Panel de Control
                    </h1>
                    <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">
                        Ingresa tus credenciales autorizadas para gestionar la plataforma Nikama.
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
                <form class="space-y-6" action="{{ route('admin.login.post') }}" method="POST">
                    @csrf
                    
                    <div class="space-y-4">
                        <div>
                            <label for="email" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">Correo electrónico</label>
                            <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}"
                                class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-900 dark:text-white font-medium focus:outline-none focus:ring-2 focus:ring-slate-700 focus:border-transparent transition-all"
                                placeholder="admin@nikama.com">
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">Contraseña</label>
                            <input id="password" name="password" type="password" autocomplete="current-password" required
                                class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-900 dark:text-white font-medium focus:outline-none focus:ring-2 focus:ring-slate-700 focus:border-transparent transition-all"
                                placeholder="••••••••">
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-sm">
                        <div class="flex items-center">
                            <input id="remember" name="remember" type="checkbox"
                                class="h-4 w-4 rounded border-gray-300 text-slate-700 focus:ring-slate-700 cursor-pointer">
                            <label for="remember" class="ml-2 block text-slate-700 dark:text-slate-300 font-medium cursor-pointer">Recordarme</label>
                        </div>

                        <div class="text-sm">
                            <a href="#" class="font-bold text-slate-600 hover:text-slate-800 transition">¿Olvidaste tu contraseña?</a>
                        </div>
                    </div>

                    <div>
                        <button type="submit"
                            class="group relative w-full flex justify-center py-3.5 px-4 border border-transparent rounded-xl text-white bg-slate-900 hover:bg-slate-800 font-bold text-lg transition-all shadow-lg shadow-slate-900/20 hover:scale-[1.02] active:scale-[0.98] cursor-pointer">
                            Ingresar al Sistema
                        </button>
                    </div>
                </form>

                <div class="text-center text-sm pt-4 border-t border-gray-100 dark:border-slate-800">
                    <a href="{{ route('public.welcome') }}" class="font-bold text-slate-600 hover:text-slate-800 transition flex items-center justify-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Volver al Sitio Público
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
