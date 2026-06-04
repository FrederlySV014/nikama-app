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

        <a href="{{ route('public.about-us') }}" class="block px-4 py-3 rounded-xl text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-slate-800 font-medium transition">
            Nosotros
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
        @guest
            <div class="space-y-2">
                <a href="{{ route('login') }}" class="block w-full text-center px-4 py-3 rounded-xl border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-200 font-semibold hover:bg-gray-50 dark:hover:bg-slate-800 transition cursor-pointer">
                    Iniciar Sesión
                </a>
                <a href="{{ route('register') }}" class="block w-full text-center px-4 py-3 rounded-xl bg-luffy-red text-white font-bold hover:bg-luffy-red-hover transition cursor-pointer">
                    Registrarse
                </a>
            </div>
        @endguest

        @auth
            <div class="space-y-2">
                <div class="text-xs text-slate-500 dark:text-slate-400 mb-2 px-1">Sesión iniciada como: <span class="font-bold text-slate-800 dark:text-white">{{ auth()->user()->first_name }}</span></div>
                
                @if(auth()->user()->hasRole(\App\Models\Role::SUPER_ADMIN))
                    <a href="{{ route('admin.dashboard') }}" class="block w-full text-center px-4 py-3 rounded-xl bg-slate-800 text-white font-bold hover:bg-slate-700 transition cursor-pointer">
                        Admin Panel
                    </a>
                @elseif(auth()->user()->hasRole(\App\Models\Role::SELLER))
                    <a href="{{ route('seller.dashboard') }}" class="block w-full text-center px-4 py-3 rounded-xl bg-amber-500 text-white font-bold hover:bg-amber-600 transition cursor-pointer">
                        Panel Vendedor
                    </a>
                @elseif(auth()->user()->hasRole(\App\Models\Role::DRIVER))
                    <a href="{{ route('driver.dashboard') }}" class="block w-full text-center px-4 py-3 rounded-xl bg-slate-800 text-white font-bold hover:bg-slate-700 transition cursor-pointer">
                        Panel Repartidor
                    </a>
                @endif

                <form method="POST" action="{{ route('logout') }}" class="block">
                    @csrf
                    <button type="submit" class="block w-full text-center px-4 py-3 rounded-xl bg-rose-500 hover:bg-rose-600 text-white font-bold transition cursor-pointer">
                        Cerrar Sesión
                    </button>
                </form>
            </div>
        @endauth
    </div>
</aside>
