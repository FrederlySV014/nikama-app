<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Nikama - Delivery' }}</title>
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
<body class="antialiased font-sans flex flex-col min-h-screen" x-data="{ menuOpen: false }">
    <!-- Glassmorphism Header -->
    <header class="fixed w-full top-0 z-30 backdrop-blur-xl bg-white dark:bg-slate-900 border-b border-gray-200 dark:border-slate-700/50 transition-colors duration-300 shadow-sm">
        <div class="max-w-7xl mx-auto px-2 sm:px-4 lg:px-8 h-16 sm:h-20 flex items-center justify-between gap-2 md:gap-4">
            <!-- Grupo izquierdo: menú + logo -->
            <div class="flex items-center gap-2 shrink-0">
                <!-- 1. Menú lateral desplegable (hamburguesa) -->
                <button 
                    @click="menuOpen = !menuOpen" 
                    class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-800 transition-colors shrink-0 cursor-pointer"
                    aria-label="Abrir menú"
                >
                    <svg class="w-5 h-5 md:w-6 md:h-6 text-gray-700 dark:text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>

                <!-- 2. Logo principal (siempre visible) -->
                <a href="/" class="flex items-center gap-2 group shrink-0">
                    <div class="transition-transform duration-300 group-hover:-rotate-12 group-hover:scale-110">
                        <x-logo class="w-8 h-8 md:w-9 md:h-9" />
                    </div>
                    <span class="text-xl md:text-2xl font-extrabold font-['Outfit'] text-gray-900 dark:text-white tracking-tight hidden md:inline">Nikama</span>
                </a>
            </div>

            <!-- 3. Barra de búsqueda (100% en < 768px, 50% en >= 768px) -->
            <div class="flex-1 w-full md:w-1/2 md:max-w-[50%] mx-1 md:mx-4">
                <div class="flex items-center">
                    <!-- Input de búsqueda -->
                    <input 
                        type="text" 
                        placeholder="Buscar..." 
                        class="flex-1 w-full pl-3 pr-4 py-2 rounded-l-full bg-gray-100 dark:bg-slate-800 border-0 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-luffy-red/50 transition-all text-xs md:text-sm"
                    />
                    <!-- Botón de búsqueda al lado DERECHO -->
                    <button class="p-2 rounded-r-full bg-luffy-red hover:bg-luffy-red-hover text-white transition-all shrink-0 cursor-pointer" aria-label="Buscar">
                        <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- 4. Sección de acciones del usuario (visible en >= 768px) -->
            <div class="flex items-center gap-1 md:gap-2 shrink-0 hidden md:flex">
                <!-- Carrito -->
                <button class="p-2 rounded-xl hover:bg-gray-100 dark:hover:bg-slate-800 transition-colors relative cursor-pointer" aria-label="Carrito de compras">
                    <svg class="w-5 h-5 text-gray-700 dark:text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </button>

                <!-- Login/Register -->
                <a href="/login" class="bg-gray-200 dark:bg-slate-700 hover:bg-gray-300 dark:hover:bg-slate-600 text-gray-700 dark:text-gray-200 px-3 md:px-4 py-1.5 md:py-2 rounded-full font-bold shadow-md dark:shadow-none transition-all hover:scale-105 active:scale-95 text-xs md:text-sm cursor-pointer">Entrar</a>
                <a href="/register" class="bg-luffy-straw hover:bg-luffy-straw-hover text-amber-900 px-3 md:px-4 py-1.5 md:py-2 rounded-full font-bold shadow-lg shadow-luffy-straw/30 transition-all hover:scale-105 active:scale-95 text-xs md:text-sm cursor-pointer">Registrarse</a>
            </div>

            <!-- Carrito visible solo en < 768px -->
            <button class="p-2 rounded-xl hover:bg-gray-100 dark:hover:bg-slate-800 transition-colors relative md:hidden cursor-pointer" aria-label="Carrito de compras">
                <svg class="w-5 h-5 text-gray-700 dark:text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </button>
        </div>
    </header>

    <!-- Overlay (aparece uniformemente en toda la pantalla) -->
    <div 
        x-show="menuOpen"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-40 bg-black/50 backdrop-blur-sm"
        @click="menuOpen = false"
    ></div>
    
    <!-- Panel del menú - responsive: 80% (<768px), 40% (768-1023px), 20% (>=1024px) -->
    <aside 
        x-show="menuOpen"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="-translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-full"
        class="fixed top-0 left-0 z-50 w-[80%] md:w-[40%] lg:w-[20%] min-w-70 max-w-sm bg-white dark:bg-slate-900 h-full shadow-2xl flex flex-col border-r border-gray-200 dark:border-slate-800"
    >
        
        <!-- Botón X flotante dentro del menú (arriba derecha) -->
        <button 
            @click="menuOpen = false" 
            class="absolute top-4 right-4 p-2 rounded-full bg-gray-100 dark:bg-slate-800 hover:bg-gray-200 dark:hover:bg-slate-700 shadow-md transition cursor-pointer"
            aria-label="Cerrar menú"
        >
            <svg class="w-5 h-5 text-gray-700 dark:text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>

            <!-- Header del menú -->
            <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-900">
                <span class="text-xl font-bold font-['Outfit'] text-gray-900 dark:text-white">Menú</span>
            </div>
            
            <!-- Contenido del menú -->
            <nav class="flex-1 overflow-y-auto p-4 space-y-2 bg-white dark:bg-slate-900">
                <!-- Theme Toggle dentro del menú -->
                <button 
                    x-data 
                    @click="document.documentElement.classList.toggle('dark'); localStorage.theme = document.documentElement.classList.contains('dark') ? 'dark' : 'light'" 
                    class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-slate-800 font-medium transition cursor-pointer"
                >
                    <span class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        Cambiar tema
                    </span>
                    <span class="relative inline-flex h-6 w-11 items-center rounded-full bg-gray-200 dark:bg-slate-700 transition-colors">
                        <span class="inline-block h-4 w-4 transform rounded-full bg-white shadow-sm transition-transform dark:translate-x-5"></span>
                    </span>
                </button>

                <a href="{{ route('vendor.register') }}" class="block px-4 py-3 rounded-xl text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-slate-800 font-medium transition">
                    Registra tu negocio
                </a>
                <a href="#" class="block px-4 py-3 rounded-xl text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-slate-800 font-medium transition">
                    Restaurantes
                </a>
                <a href="#" class="block px-4 py-3 rounded-xl text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-slate-800 font-medium transition">
                    Categorías
                </a>
                <a href="#" class="block px-4 py-3 rounded-xl text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-slate-800 font-medium transition">
                    Ofertas
                </a>
                <a href="#" class="block px-4 py-3 rounded-xl text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-slate-800 font-medium transition">
                    Mis Pedidos
                </a>
                <a href="#" class="block px-4 py-3 rounded-xl text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-slate-800 font-medium transition">
                    Favoritos
                </a>
            </nav>
            
            <!-- Footer del menú -->
            <div class="p-4 border-t border-gray-200 dark:border-slate-800">
                <div class="space-y-2">
                    <a href="/login" class="block w-full text-center px-4 py-3 rounded-xl border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-200 font-semibold hover:bg-gray-50 dark:hover:bg-slate-800 transition cursor-pointer">
                        Iniciar Sesión
                    </a>
                    <a href="/register" class="block w-full text-center px-4 py-3 rounded-xl bg-luffy-red text-white font-bold hover:bg-luffy-red-hover transition cursor-pointer">
                        Registrarse
                    </a>
                </div>
        </aside>

    <!-- Main Content -->
    <main class="grow pt-20">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="bg-white dark:bg-slate-900 border-t border-gray-200 dark:border-slate-800 py-12 mt-auto transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <x-logo class="w-16 h-16 mx-auto grayscale opacity-40 hover:grayscale-0 hover:opacity-100 transition duration-500 mb-6" />
            <p class="text-gray-500 dark:text-gray-400 font-medium">&copy; {{ date('Y') }} Nikama Delivery. El rey de las entregas.</p>
        </div>
    </footer>
</body>
</html>
