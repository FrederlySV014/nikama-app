<x-layouts.seller>
    <x-slot:title>Seller Dashboard - Nikama</x-slot:title>

    <div class="space-y-8">
        <!-- Banner de Bienvenida Premium -->
        <div class="relative bg-gradient-to-r from-luffy-straw/20 via-amber-500/10 to-transparent p-8 rounded-3xl border border-luffy-straw/20 shadow-sm overflow-hidden">
            <div class="absolute right-0 top-0 h-full w-1/3 opacity-10 pointer-events-none">
                <span class="text-[120px] font-black font-sans leading-none block select-none">NKM</span>
            </div>
            <div class="relative z-10 space-y-1">
                <span class="text-[10px] text-luffy-straw uppercase font-black tracking-wider block">Establecimiento Oficial</span>
                <h2 class="text-3xl font-extrabold font-['Outfit'] text-slate-850 dark:text-white">¡Bienvenido de vuelta, {{ auth()->user()->first_name }}!</h2>
                <p class="text-slate-500 dark:text-slate-350 text-sm max-w-2xl font-medium">Controla en tiempo real las operaciones, ventas, pedidos en tránsito y el stock de productos de tu negocio desde este panel administrativo central.</p>
            </div>
        </div>

        <!-- Alertas de Sesión -->
        @if (session('success'))
            <div class="p-4 bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-250 dark:border-emerald-800/30 text-emerald-800 dark:text-emerald-350 rounded-2xl flex items-center gap-3 text-sm font-semibold">
                <span>✓</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Panel de Estadísticas Primarias (Métricas) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Ventas Hoy -->
            <div class="bg-white dark:bg-slate-800 p-6 rounded-3xl border border-slate-150 dark:border-slate-700/80 shadow-sm hover:shadow-md transition duration-300">
                <div class="flex items-center justify-between">
                    <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider block">Ventas Hoy</span>
                    <span class="text-xs bg-emerald-50 dark:bg-emerald-950/20 text-emerald-600 dark:text-emerald-400 px-2 py-0.5 rounded font-black">Hoy</span>
                </div>
                <span class="block text-3xl font-extrabold font-['Outfit'] text-slate-850 dark:text-white mt-3">S/ {{ number_format($todaySales, 2) }}</span>
                <span class="text-[10px] text-slate-400 font-medium block mt-1">Suma de subtotales de hoy</span>
            </div>

            <!-- Pedidos Hoy -->
            <div class="bg-white dark:bg-slate-800 p-6 rounded-3xl border border-slate-150 dark:border-slate-700/80 shadow-sm hover:shadow-md transition duration-300">
                <div class="flex items-center justify-between">
                    <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider block">Pedidos Hoy</span>
                    <span class="text-xs bg-blue-50 dark:bg-blue-950/20 text-blue-600 dark:text-blue-400 px-2 py-0.5 rounded font-black">Frecuencia</span>
                </div>
                <span class="block text-3xl font-extrabold font-['Outfit'] text-slate-850 dark:text-white mt-3">{{ $todayOrdersCount }}</span>
                <span class="text-[10px] text-slate-400 font-medium block mt-1">Pedidos generados en las últimas 24h</span>
            </div>

            <!-- Ventas de la Semana -->
            <div class="bg-white dark:bg-slate-800 p-6 rounded-3xl border border-slate-150 dark:border-slate-700/80 shadow-sm hover:shadow-md transition duration-300">
                <div class="flex items-center justify-between">
                    <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider block">Ventas de la semana</span>
                    <span class="text-xs bg-purple-50 dark:bg-purple-950/20 text-purple-600 dark:text-purple-400 px-2 py-0.5 rounded font-black">Semanal</span>
                </div>
                <span class="block text-3xl font-extrabold font-['Outfit'] text-slate-850 dark:text-white mt-3">S/ {{ number_format($weeklySales, 2) }}</span>
                <span class="text-[10px] text-slate-400 font-medium block mt-1">Desde el lunes de la semana actual</span>
            </div>

            <!-- Productos Activos -->
            <div class="bg-white dark:bg-slate-800 p-6 rounded-3xl border border-slate-150 dark:border-slate-700/80 shadow-sm hover:shadow-md transition duration-300">
                <div class="flex items-center justify-between">
                    <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider block">Productos Activos</span>
                    <span class="text-xs bg-amber-50 dark:bg-amber-950/20 text-amber-600 dark:text-amber-400 px-2 py-0.5 rounded font-black">Stock</span>
                </div>
                <span class="block text-3xl font-extrabold font-['Outfit'] text-slate-850 dark:text-white mt-3">{{ $activeProductsCount }}</span>
                <span class="text-[10px] text-slate-400 font-medium block mt-1">Productos publicados activos</span>
            </div>
        </div>

        <!-- Estadísticas Operativas de Atención Rápida -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-gradient-to-br from-rose-500/5 to-rose-500/10 dark:from-rose-950/10 dark:to-rose-950/20 border border-rose-200/40 p-6 rounded-3xl flex items-center justify-between gap-4 shadow-sm">
                <div class="space-y-1">
                    <h3 class="text-sm font-bold text-rose-800 dark:text-rose-350">Pedidos Pendientes de Atención</h3>
                    <p class="text-[11px] text-rose-600/80 dark:text-rose-455">Requieren confirmación, alistar platos/productos y despachar repartidor.</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-3xl font-black font-['Outfit'] text-rose-800 dark:text-rose-300">{{ $pendingOrdersCount }}</span>
                    @if ($pendingOrdersCount > 0)
                        <span class="flex h-2 w-2 relative">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-450 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-rose-500"></span>
                        </span>
                    @endif
                </div>
            </div>

            <div class="bg-gradient-to-br from-indigo-500/5 to-indigo-500/10 dark:from-indigo-950/10 dark:to-indigo-950/20 border border-indigo-200/40 p-6 rounded-3xl flex items-center justify-between gap-4 shadow-sm">
                <div class="space-y-1">
                    <h3 class="text-sm font-bold text-indigo-800 dark:text-indigo-350">Pedidos en Tránsito</h3>
                    <p class="text-[11px] text-indigo-600/80 dark:text-indigo-455">Pedidos cocinándose o en camino a destino mediante motorizados.</p>
                </div>
                <span class="text-3xl font-black font-['Outfit'] text-indigo-800 dark:text-indigo-300">{{ $activeDeliveriesCount }}</span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- COLUMNA IZQUIERDA (8 spans): Últimos Pedidos -->
            <div class="lg:col-span-8 space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-extrabold font-['Outfit'] text-slate-800 dark:text-white flex items-center gap-2">
                        <span>📋</span> Pedidos Recientes
                    </h3>
                    <a href="{{ route('seller.orders.index') }}" class="text-xs text-luffy-straw font-bold hover:underline cursor-pointer">
                        Ver todos los pedidos ➜
                    </a>
                </div>

                <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-150 dark:border-slate-700/80 shadow-sm overflow-hidden">
                    @if ($recentOrders->isEmpty())
                        <div class="p-12 text-center space-y-4">
                            <div class="w-16 h-16 rounded-full bg-slate-50 dark:bg-slate-900 flex items-center justify-center text-slate-350 dark:text-slate-650 mx-auto text-lg">
                                📦
                            </div>
                            <h4 class="font-bold text-slate-800 dark:text-white text-xs">No hay pedidos registrados</h4>
                            <p class="text-[11px] text-slate-500 max-w-xs mx-auto">Cuando los clientes compren en tu establecimiento se mostrarán en esta lista.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-slate-50 dark:bg-slate-900/60 text-slate-400 dark:text-slate-400 text-[9px] font-black uppercase tracking-wider border-b border-slate-150 dark:border-slate-700/80">
                                        <th class="py-3 px-6">Código</th>
                                        <th class="py-3 px-6">Cliente</th>
                                        <th class="py-3 px-6">Monto</th>
                                        <th class="py-3 px-6">Estado</th>
                                        <th class="py-3 px-6 text-right">Acción</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-750 text-xs">
                                    @foreach ($recentOrders as $order)
                                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-750/10 transition">
                                            <td class="py-4 px-6 font-extrabold font-mono text-slate-900 dark:text-white">
                                                {{ $order->order_number }}
                                            </td>
                                            <td class="py-4 px-6 font-semibold text-slate-705 dark:text-slate-300">
                                                {{ $order->user->first_name }} {{ $order->user->last_name }}
                                            </td>
                                            <td class="py-4 px-6 font-black text-slate-800 dark:text-white">
                                                S/ {{ number_format($order->total, 2) }}
                                            </td>
                                            <td class="py-4 px-6 font-bold">
                                                @switch($order->status)
                                                    @case('pending')
                                                        <span class="inline-block px-2 py-0.5 rounded text-[9px] font-bold bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300">
                                                            ⏳ Recibido
                                                        </span>
                                                        @break
                                                    @case('confirmed')
                                                        <span class="inline-block px-2 py-0.5 rounded text-[9px] font-bold bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400">
                                                            ✓ Confirmado
                                                        </span>
                                                        @break
                                                    @case('preparing')
                                                        <span class="inline-block px-2 py-0.5 rounded text-[9px] font-bold bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400">
                                                            🍳 Preparando
                                                        </span>
                                                        @break
                                                    @case('on_the_way')
                                                        <span class="inline-block px-2 py-0.5 rounded text-[9px] font-bold bg-purple-50 dark:bg-purple-950/40 text-purple-600 dark:text-purple-400">
                                                            🛵 En Camino
                                                        </span>
                                                        @break
                                                    @case('delivered')
                                                        <span class="inline-block px-2 py-0.5 rounded text-[9px] font-bold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400">
                                                            🎁 Entregado
                                                        </span>
                                                        @break
                                                    @case('cancelled')
                                                        <span class="inline-block px-2 py-0.5 rounded text-[9px] font-bold bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400">
                                                            ✕ Cancelado
                                                        </span>
                                                        @break
                                                @endswitch
                                            </td>
                                            <td class="py-4 px-6 text-right">
                                                <a href="{{ route('seller.orders.show', $order) }}" class="inline-block bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-650 text-slate-700 dark:text-slate-200 font-bold px-3 py-1.5 rounded-xl transition cursor-pointer text-[11px]">
                                                    Gestionar
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <!-- COLUMNA DERECHA (4 spans): Productos Más Vendidos -->
            <div class="lg:col-span-4 space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-extrabold font-['Outfit'] text-slate-800 dark:text-white flex items-center gap-2">
                        <span>🔥</span> Más Vendidos
                    </h3>
                </div>

                <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-150 dark:border-slate-700/80 p-6 shadow-sm space-y-4">
                    @if ($topProducts->isEmpty())
                        <p class="text-xs text-slate-400 italic text-center py-6">No hay registros de ventas de productos aún.</p>
                    @else
                        <div class="space-y-4 divide-y divide-slate-100 dark:divide-slate-750">
                            @foreach ($topProducts as $tp)
                                <div class="flex items-center justify-between gap-3 text-xs {{ !$loop->first ? 'pt-3.5' : '' }}">
                                    <div class="space-y-0.5 flex-1 min-w-0">
                                        <h4 class="font-extrabold text-slate-800 dark:text-white truncate" title="{{ $tp->product_name }}">
                                            {{ $tp->product_name }}
                                        </h4>
                                        <p class="text-[10px] text-slate-450 font-bold">{{ $tp->total_qty }} unidades vendidas</p>
                                    </div>
                                    <div class="text-right">
                                        <span class="font-black text-slate-900 dark:text-white block">S/ {{ number_format($tp->total_rev, 2) }}</span>
                                        <span class="text-[9px] text-slate-400">Ingresos</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.seller>
