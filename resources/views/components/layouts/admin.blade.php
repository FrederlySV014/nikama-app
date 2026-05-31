<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Super Admin - Nikama' }}</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700|outfit:400,600,700,800&display=swap" rel="stylesheet" />
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
<body class="antialiased font-sans bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-white transition-colors duration-300" x-data="{ sidebarOpen: false }">
    <div class="flex h-screen overflow-hidden">
        
        <!-- Sidebar Backdrop -->
        <div x-show="sidebarOpen" @click="sidebarOpen = false" x-transition.opacity class="fixed inset-0 z-40 bg-slate-900/60 lg:hidden backdrop-blur-sm"></div>

        <!-- Sidebar Premium Admin (Siempre oscura o de muy alto contraste) -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-50 w-72 bg-slate-900 border-r border-slate-800 transition-transform duration-300 ease-in-out lg:static lg:translate-x-0 flex flex-col shadow-2xl">
            <div class="flex items-center justify-center h-20 border-b border-slate-800 bg-slate-950/50">
                <a href="/admin" class="flex items-center gap-3">
                    <x-logo class="w-10 h-10" />
                    <span class="text-2xl font-extrabold font-['Outfit'] text-white tracking-widest uppercase">Admin</span>
                </a>
            </div>
            <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
                <div class="text-xs font-semibold text-slate-500 uppercase tracking-widest mb-4 px-3">Gestión</div>
                
                <a href="/admin" class="flex items-center gap-3 px-3 py-3 text-luffy-straw bg-white/10 rounded-xl font-semibold border border-white/5 shadow-inner transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    Resumen General
                </a>
                <a href="#" class="flex items-center gap-3 px-3 py-3 text-slate-400 hover:text-white hover:bg-white/5 rounded-xl font-medium transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    Vendedores (Empresas)
                </a>
                <a href="#" class="flex items-center gap-3 px-3 py-3 text-slate-400 hover:text-white hover:bg-white/5 rounded-xl font-medium transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    Clientes Registrados
                </a>
                <a href="#" class="flex items-center gap-3 px-3 py-3 text-slate-400 hover:text-white hover:bg-white/5 rounded-xl font-medium transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    Repartidores
                </a>
                
                <div class="text-xs font-semibold text-slate-500 uppercase tracking-widest mb-4 mt-8 px-3">Sistema</div>
                <a href="#" class="flex items-center gap-3 px-3 py-3 text-slate-400 hover:text-white hover:bg-white/5 rounded-xl font-medium transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path></svg>
                    Backups
                </a>
                <a href="#" class="flex items-center gap-3 px-3 py-3 text-slate-400 hover:text-white hover:bg-white/5 rounded-xl font-medium transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    Ajustes Globales
                </a>
            </nav>
        </aside>

        <!-- Main Content Wrapper -->
        <div class="flex flex-col flex-1 overflow-hidden">
            <!-- Topbar -->
            <header class="h-20 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between px-4 sm:px-8 z-10 transition-colors duration-300">
                <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white focus:outline-none p-2 rounded-md bg-slate-100 dark:bg-slate-800">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <div class="flex-1"></div>
                <div class="flex items-center gap-5">
                    <!-- Theme Toggle -->
                    <button @click="document.documentElement.classList.toggle('dark'); localStorage.theme = document.documentElement.classList.contains('dark') ? 'dark' : 'light'" class="p-2.5 rounded-full bg-slate-100 text-slate-600 hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700 transition">
                        <svg class="w-5 h-5 hidden dark:block text-luffy-straw" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        <svg class="w-5 h-5 block dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                    </button>
                    <!-- User Profile Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center gap-3 focus:outline-none bg-slate-100 dark:bg-slate-800 p-1 pr-4 rounded-full border border-slate-200 dark:border-slate-700 hover:border-luffy-red dark:hover:border-luffy-red transition-colors">
                            <img src="https://ui-avatars.com/api/?name=Admin&background=ef4444&color=fff" alt="User" class="w-9 h-9 rounded-full">
                            <span class="font-bold text-sm hidden sm:block">SuperAdmin</span>
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-3 w-56 bg-white dark:bg-slate-800 rounded-xl shadow-2xl py-2 border border-slate-100 dark:border-slate-700">
                            <a href="#" class="block px-4 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700">Configuración de Cuenta</a>
                            <div class="border-t border-slate-100 dark:border-slate-700 my-1"></div>
                            <a href="#" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-slate-700 font-bold">Cerrar Sesión Segura</a>
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
