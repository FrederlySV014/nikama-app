<x-layouts.admin title="Admin Dashboard">
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">Centro de Control</h1>
        <p class="text-slate-500 dark:text-slate-400 mt-2">Visión global de toda la plataforma Nikama.</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-100 dark:border-slate-700 relative overflow-hidden group hover:-translate-y-1 transition-transform">
            <div class="text-slate-500 dark:text-slate-400 font-semibold mb-1">Total Vendedores</div>
            <div class="text-4xl font-black text-slate-900 dark:text-white">124</div>
            <div class="mt-3 text-sm text-emerald-500 font-bold flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                +4 esta semana
            </div>
        </div>
        
        <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-100 dark:border-slate-700 relative overflow-hidden group hover:-translate-y-1 transition-transform">
            <div class="text-slate-500 dark:text-slate-400 font-semibold mb-1">Clientes Activos</div>
            <div class="text-4xl font-black text-slate-900 dark:text-white">8,405</div>
            <div class="mt-3 text-sm text-emerald-500 font-bold flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                +142 esta semana
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-100 dark:border-slate-700 relative overflow-hidden group hover:-translate-y-1 transition-transform">
            <div class="text-slate-500 dark:text-slate-400 font-semibold mb-1">Repartidores Activos</div>
            <div class="text-4xl font-black text-slate-900 dark:text-white">312</div>
            <div class="mt-3 text-sm text-slate-400 font-bold flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                Estable
            </div>
        </div>

        <div class="bg-slate-900 dark:bg-slate-950 p-6 rounded-2xl shadow-xl border border-slate-800 text-white relative overflow-hidden hover:-translate-y-1 transition-transform">
            <div class="absolute -right-4 -bottom-4 opacity-10">
                <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z"/></svg>
            </div>
            <div class="font-semibold mb-1 text-slate-300">Ingresos Totales (Mes)</div>
            <div class="text-4xl font-black text-luffy-straw">$45.2K</div>
            <div class="mt-3 text-sm text-emerald-400 font-bold flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                +18% vs mes anterior
            </div>
        </div>
    </div>
</x-layouts.admin>
