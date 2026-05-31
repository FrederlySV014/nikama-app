<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Vendor Dashboard - Nikama' }}</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700|outfit:400,600,700&display=swap" rel="stylesheet" />
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>
<body class="antialiased font-sans bg-gray-50 dark:bg-slate-900 text-gray-900 dark:text-white transition-colors duration-300" x-data="{ sidebarOpen: false }">
    <div class="flex h-screen overflow-hidden">
        
        <!-- Sidebar Backdrop para móviles -->
        <div x-show="sidebarOpen" @click="sidebarOpen = false" x-transition.opacity class="fixed inset-0 z-40 bg-gray-900/50 lg:hidden backdrop-blur-sm"></div>

        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-50 w-72 bg-white dark:bg-slate-800 border-r border-gray-200 dark:border-slate-700 transition-transform duration-300 ease-in-out lg:static lg:translate-x-0 flex flex-col shadow-xl lg:shadow-none">
            <div class="flex items-center justify-center h-20 border-b border-gray-200 dark:border-slate-700">
                <a href="/vendor" class="flex items-center gap-3 group">
                    <x-logo class="w-10 h-10 group-hover:scale-110 transition-transform" />
                    <span class="text-2xl font-bold font-['Outfit'] text-luffy-red dark:text-luffy-straw">Nikama <span class="text-gray-800 dark:text-white">Vendor</span></span>
                </a>
            </div>
            <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
                <div class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-4 px-3">Menú Principal</div>
                
                <a href="/vendor" class="flex items-center gap-3 px-3 py-3 text-luffy-red dark:text-luffy-straw bg-red-50 dark:bg-luffy-straw/10 rounded-xl font-semibold transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    Dashboard
                </a>
                <a href="#" class="flex items-center gap-3 px-3 py-3 text-gray-600 dark:text-gray-300 hover:text-luffy-red dark:hover:text-luffy-straw hover:bg-gray-100 dark:hover:bg-slate-700/50 rounded-xl font-medium transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    Mis Productos
                </a>
                <a href="#" class="flex items-center gap-3 px-3 py-3 text-gray-600 dark:text-gray-300 hover:text-luffy-red dark:hover:text-luffy-straw hover:bg-gray-100 dark:hover:bg-slate-700/50 rounded-xl font-medium transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    Órdenes <span class="ml-auto bg-luffy-red text-white text-xs font-bold px-2 py-0.5 rounded-full">3</span>
                </a>
                <a href="#" class="flex items-center gap-3 px-3 py-3 text-gray-600 dark:text-gray-300 hover:text-luffy-red dark:hover:text-luffy-straw hover:bg-gray-100 dark:hover:bg-slate-700/50 rounded-xl font-medium transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    Ajustes de Tienda
                </a>
            </nav>
        </aside>

        <!-- Main Content Wrapper -->
        <div class="flex flex-col flex-1 overflow-hidden">
            <!-- Topbar -->
            <header class="h-20 bg-white/80 dark:bg-slate-800/80 backdrop-blur-md border-b border-gray-200 dark:border-slate-700 flex items-center justify-between px-4 sm:px-8 z-10">
                <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white focus:outline-none p-2 rounded-md bg-gray-100 dark:bg-slate-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <div class="flex-1"></div>
                <div class="flex items-center gap-5">
                    <!-- Theme Toggle -->
                    <button @click="document.documentElement.classList.toggle('dark'); localStorage.theme = document.documentElement.classList.contains('dark') ? 'dark' : 'light'" class="p-2.5 rounded-full bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-slate-700 dark:text-gray-300 dark:hover:bg-slate-600 transition">
                        <svg class="w-5 h-5 hidden dark:block text-luffy-straw" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        <svg class="w-5 h-5 block dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                    </button>
                    <!-- User Profile Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center gap-3 focus:outline-none p-1 pr-3 rounded-full border border-gray-200 dark:border-slate-600 hover:bg-gray-50 dark:hover:bg-slate-700 transition">
                            <img src="https://ui-avatars.com/api/?name=Empresa&background=fcd34d&color=b45309" alt="User" class="w-10 h-10 rounded-full">
                            <span class="font-medium text-sm hidden sm:block">Mi Restaurante</span>
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-3 w-56 bg-white dark:bg-slate-800 rounded-xl shadow-xl py-2 border border-gray-100 dark:border-slate-700">
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-700">Mi Perfil</a>
                            <div class="border-t border-gray-100 dark:border-slate-700 my-1"></div>
                            <a href="#" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-slate-700 font-medium">Cerrar Sesión</a>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content Area -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto p-4 sm:p-8">
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
