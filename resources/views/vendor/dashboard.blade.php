<x-layouts.vendor title="Vendor Dashboard">
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white">¡Hola, Restaurante!</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-2">Aquí está el resumen de tus ventas de hoy.</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-700 hover:shadow-md transition-shadow">
            <div class="text-gray-500 dark:text-gray-400 font-medium mb-1">Ventas de hoy</div>
            <div class="text-4xl font-bold text-gray-900 dark:text-white">$1,240</div>
            <div class="mt-2 text-sm text-emerald-500 font-semibold">+12% vs ayer</div>
        </div>
        <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-700 hover:shadow-md transition-shadow">
            <div class="text-gray-500 dark:text-gray-400 font-medium mb-1">Órdenes Pendientes</div>
            <div class="text-4xl font-bold text-luffy-red">3</div>
            <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">Esperando preparación</div>
        </div>
        <div class="bg-luffy-straw dark:bg-luffy-straw-dark p-6 rounded-2xl shadow-sm border border-amber-300 dark:border-amber-700 text-amber-900 dark:text-amber-50">
            <div class="font-medium mb-1 opacity-80">Estado de la Tienda</div>
            <div class="text-4xl font-bold">Abierta</div>
            <div class="mt-2 text-sm font-semibold opacity-90 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                Recibiendo pedidos
            </div>
        </div>
    </div>

    <!-- Recent Orders Table -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 dark:border-slate-700 flex justify-between items-center bg-gray-50/50 dark:bg-slate-800/50">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white">Últimas Órdenes</h2>
            <a href="#" class="text-luffy-red hover:text-luffy-red-dark dark:hover:text-luffy-straw font-semibold text-sm transition-colors">Ver todas</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-white dark:bg-slate-800 text-gray-500 dark:text-gray-400 border-b border-gray-100 dark:border-slate-700">
                    <tr>
                        <th class="px-6 py-4 font-semibold">ID Orden</th>
                        <th class="px-6 py-4 font-semibold">Cliente</th>
                        <th class="px-6 py-4 font-semibold">Monto</th>
                        <th class="px-6 py-4 font-semibold">Estado</th>
                        <th class="px-6 py-4 font-semibold">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-700 text-gray-700 dark:text-gray-300">
                    <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/30 transition-colors">
                        <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">#NK-1023</td>
                        <td class="px-6 py-4">Monkey D. Luffy</td>
                        <td class="px-6 py-4 font-medium text-emerald-600 dark:text-emerald-400">$45.00</td>
                        <td class="px-6 py-4"><span class="px-3 py-1 bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300 rounded-full text-xs font-bold border border-amber-200 dark:border-amber-800/50">Preparando</span></td>
                        <td class="px-6 py-4"><button class="text-luffy-red hover:text-luffy-red-dark dark:text-luffy-straw dark:hover:text-white font-semibold transition-colors">Gestionar</button></td>
                    </tr>
                    <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/30 transition-colors">
                        <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">#NK-1022</td>
                        <td class="px-6 py-4">Roronoa Zoro</td>
                        <td class="px-6 py-4 font-medium text-emerald-600 dark:text-emerald-400">$32.50</td>
                        <td class="px-6 py-4"><span class="px-3 py-1 bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300 rounded-full text-xs font-bold border border-blue-200 dark:border-blue-800/50">En camino</span></td>
                        <td class="px-6 py-4"><button class="text-luffy-red hover:text-luffy-red-dark dark:text-luffy-straw dark:hover:text-white font-semibold transition-colors">Gestionar</button></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.vendor>
