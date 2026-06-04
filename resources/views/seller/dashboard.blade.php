<x-layouts.seller>
    <x-slot:title>Seller Dashboard - Nikama</x-slot:title>

    <div class="space-y-6">
        <div class="bg-gradient-to-r from-luffy-straw/20 to-amber-500/10 p-8 rounded-3xl border border-luffy-straw/30 shadow-sm">
            <h2 class="text-3xl font-extrabold font-['Outfit'] text-slate-800 dark:text-white">¡Bienvenido, {{ auth()->user()->first_name }}!</h2>
            <p class="text-slate-600 dark:text-slate-300 mt-2 font-medium">Este es el panel administrativo de tu negocio. Desde aquí podrás gestionar productos, ver ventas y controlar tus pedidos en tiempo real.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white dark:bg-slate-800 p-6 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm">
                <span class="text-slate-400 text-sm font-bold uppercase tracking-wider">Pedidos Hoy</span>
                <span class="block text-4xl font-extrabold font-['Outfit'] text-slate-800 dark:text-white mt-2">0</span>
            </div>
            
            <div class="bg-white dark:bg-slate-800 p-6 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm">
                <span class="text-slate-400 text-sm font-bold uppercase tracking-wider">Productos Activos</span>
                <span class="block text-4xl font-extrabold font-['Outfit'] text-slate-800 dark:text-white mt-2">0</span>
            </div>
            
            <div class="bg-white dark:bg-slate-800 p-6 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm">
                <span class="text-slate-400 text-sm font-bold uppercase tracking-wider">Ventas de la semana</span>
                <span class="block text-4xl font-extrabold font-['Outfit'] text-slate-800 dark:text-white mt-2">S/ 0.00</span>
            </div>
        </div>
    </div>
</x-layouts.seller>
