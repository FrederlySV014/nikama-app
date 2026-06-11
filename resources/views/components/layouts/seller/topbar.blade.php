<header class="bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 h-16 flex items-center justify-between px-6">
    <div class="flex items-center gap-4">
        <button class="md:hidden text-slate-600 dark:text-slate-350" @click="sidebarOpen = !sidebarOpen">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
        </button>
        <h1 class="text-lg font-bold text-slate-800 dark:text-white">Panel del Negocio</h1>
    </div>
    <div class="flex items-center gap-6">
        <!-- Icono de Notificaciones -->
        <a href="{{ route('seller.orders.index') }}" class="relative text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white transition cursor-pointer p-1.5 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-750">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
            </svg>
            <span id="seller-topbar-notification-badge" style="display: none;" class="absolute top-0.5 right-0.5 flex h-4 w-4 items-center justify-center rounded-full bg-rose-500 text-[10px] font-bold text-white ring-2 ring-white dark:ring-slate-800 animate-pulse">
                0
            </span>
        </a>

        <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            <span class="text-sm font-medium text-slate-650 dark:text-slate-350">{{ auth()->user()->first_name }}</span>
        </div>
    </div>
</header>
