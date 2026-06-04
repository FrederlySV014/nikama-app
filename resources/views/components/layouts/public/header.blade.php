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
                <input 
                    type="text" 
                    placeholder="Buscar..." 
                    class="flex-1 w-full pl-3 pr-4 py-2 rounded-l-full bg-gray-100 dark:bg-slate-800 border-0 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-luffy-red/50 transition-all text-xs md:text-sm"
                />
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

            @guest
                <!-- Login/Register -->
                <a href="{{ route('login') }}" class="bg-gray-200 dark:bg-slate-700 hover:bg-gray-300 dark:hover:bg-slate-600 text-gray-700 dark:text-gray-200 px-3 md:px-4 py-1.5 md:py-2 rounded-full font-bold shadow-md dark:shadow-none transition-all hover:scale-105 active:scale-95 text-xs md:text-sm cursor-pointer">Entrar</a>
                <a href="{{ route('register') }}" class="bg-luffy-straw hover:bg-luffy-straw-hover text-amber-900 px-3 md:px-4 py-1.5 md:py-2 rounded-full font-bold shadow-lg shadow-luffy-straw/30 transition-all hover:scale-105 active:scale-95 text-xs md:text-sm cursor-pointer">Registrarse</a>
            @endguest

            @auth
                <!-- Panel del usuario y Cerrar Sesión -->
                <div class="flex items-center gap-2">
                    <span class="text-sm font-semibold text-slate-700 dark:text-slate-300 mr-1 hidden lg:inline">Hola, {{ auth()->user()->first_name }}</span>
                    
                    @if(auth()->user()->hasRole(\App\Models\Role::SUPER_ADMIN))
                        <a href="{{ route('admin.dashboard') }}" class="bg-slate-800 hover:bg-slate-700 text-white px-3 py-1.5 rounded-full font-bold transition-all text-xs cursor-pointer">Admin Panel</a>
                    @elseif(auth()->user()->hasRole(\App\Models\Role::SELLER))
                        <a href="{{ route('seller.dashboard') }}" class="bg-amber-500 hover:bg-amber-600 text-white px-3 py-1.5 rounded-full font-bold transition-all text-xs cursor-pointer">Panel Vendedor</a>
                    @elseif(auth()->user()->hasRole(\App\Models\Role::DRIVER))
                        <a href="{{ route('driver.dashboard') }}" class="bg-slate-800 hover:bg-slate-700 text-white px-3 py-1.5 rounded-full font-bold transition-all text-xs cursor-pointer">Panel Repartidor</a>
                    @endif

                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="bg-rose-500 hover:bg-rose-600 text-white px-3 py-1.5 rounded-full font-bold transition-all hover:scale-105 active:scale-95 text-xs cursor-pointer">
                            Salir
                        </button>
                    </form>
                </div>
            @endauth
        </div>

        <!-- Carrito visible solo en < 768px -->
        <button class="p-2 rounded-xl hover:bg-gray-100 dark:hover:bg-slate-800 transition-colors relative md:hidden cursor-pointer" aria-label="Carrito de compras">
            <svg class="w-5 h-5 text-gray-700 dark:text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
        </button>
    </div>
</header>
