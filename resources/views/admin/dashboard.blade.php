<x-layouts.admin>
    <x-slot:title>Admin Dashboard - Nikama</x-slot:title>

    <div class="space-y-6">
        <div class="bg-gradient-to-r from-luffy-red/20 to-rose-500/10 p-8 rounded-3xl border border-luffy-red/30 shadow-sm">
            <h2 class="text-3xl font-extrabold font-['Outfit'] text-slate-800 dark:text-white">Panel de Control General</h2>
            <p class="text-slate-600 dark:text-slate-300 mt-2 font-medium">Bienvenido, {{ auth()->user()->first_name }}. Como Super Admin, tienes control total sobre la aprobación de negocios, repartidores, comisiones y configuración global del sistema.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white dark:bg-slate-800 p-6 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm">
                <span class="text-slate-400 text-sm font-bold uppercase tracking-wider block">Socios Pendientes</span>
                <span class="text-4xl font-extrabold font-['Outfit'] text-slate-800 dark:text-white block mt-2">0</span>
            </div>
            
            <div class="bg-white dark:bg-slate-800 p-6 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm">
                <span class="text-slate-400 text-sm font-bold uppercase tracking-wider block">Repartidores Pendientes</span>
                <span class="text-4xl font-extrabold font-['Outfit'] text-slate-800 dark:text-white block mt-2">0</span>
            </div>

            <div class="bg-white dark:bg-slate-800 p-6 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm">
                <span class="text-slate-400 text-sm font-bold uppercase tracking-wider block">Negocios Activos</span>
                <span class="text-4xl font-extrabold font-['Outfit'] text-slate-800 dark:text-white block mt-2">0</span>
            </div>

            <div class="bg-white dark:bg-slate-800 p-6 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm">
                <span class="text-slate-400 text-sm font-bold uppercase tracking-wider block">Conductores Activos</span>
                <span class="text-4xl font-extrabold font-['Outfit'] text-slate-800 dark:text-white block mt-2">0</span>
            </div>
        </div>
    </div>
</x-layouts.admin>
