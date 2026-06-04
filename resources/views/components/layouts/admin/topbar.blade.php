<header class="bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 h-16 flex items-center justify-between px-6">
    <div class="flex items-center gap-4">
        <button class="md:hidden text-slate-600 dark:text-slate-300" @click="sidebarOpen = !sidebarOpen">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
        </button>
        <h1 class="text-lg font-bold text-slate-800 dark:text-white">Panel de Administración</h1>
    </div>
    <div class="flex items-center gap-4">
        <span class="text-sm font-medium text-slate-600 dark:text-slate-300">{{ auth()->user()->first_name }} (Superadmin)</span>
    </div>
</header>
