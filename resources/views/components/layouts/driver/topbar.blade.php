<header class="bg-white dark:bg-slate-950 border-b border-slate-200 dark:border-slate-900 h-16 flex items-center justify-between px-6">
    <div class="flex items-center gap-4">
        <button class="md:hidden text-slate-600 dark:text-slate-300" @click="sidebarOpen = !sidebarOpen">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
        </button>
        <h1 class="text-lg font-bold text-slate-800 dark:text-white font-['Outfit']">Repartos Nikama</h1>
    </div>

    <div class="flex items-center gap-6">
        {{-- Driver Live Status Badge --}}
        <div class="flex items-center gap-2 px-3 py-1.5 bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-200 dark:border-emerald-900/30 rounded-full">
            <span class="relative flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
            </span>
            <span class="text-[10px] font-black text-emerald-700 dark:text-emerald-400 uppercase tracking-wider">Disponible / En Línea</span>
        </div>

        {{-- Notification Bell --}}
        <a href="{{ route('driver.dashboard') }}" class="relative text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white transition cursor-pointer p-1.5 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800" title="Asignaciones pendientes">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
            </svg>
            <span id="driver-topbar-notification-badge"
                  style="display: none;"
                  class="absolute top-0.5 right-0.5 flex h-4 w-4 items-center justify-center rounded-full bg-rose-500 text-[10px] font-bold text-white ring-2 ring-white dark:ring-slate-950 animate-pulse">
                0
            </span>
        </a>

        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-full bg-luffy-straw flex items-center justify-center text-slate-900 font-extrabold text-sm shadow-md">
                {{ strtoupper(substr(auth()->user()->first_name, 0, 1)) }}
            </div>
            <span class="text-sm font-semibold text-slate-700 dark:text-slate-300 hidden sm:inline">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</span>
        </div>
    </div>
</header>
