<x-layouts.seller>
    <x-slot:title>Gestión de Pedidos - Nikama Seller</x-slot:title>

    <div class="space-y-6">
        <!-- Encabezado -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-slate-200 dark:border-slate-800">
            <div>
                <h1 class="text-3xl font-extrabold font-['Outfit'] text-slate-800 dark:text-white">Gestión de Pedidos</h1>
                <p class="text-slate-500 dark:text-slate-400 mt-1 text-sm">Administra los pedidos de tus establecimientos, procesa estados y despacha a repartidores.</p>
            </div>
        </div>

        <!-- Alertas de Sesión -->
        @if (session('success'))
            <div class="p-4 bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-250 dark:border-emerald-800/30 text-emerald-800 dark:text-emerald-350 rounded-2xl flex items-center gap-3 text-sm font-semibold">
                <span>✓</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Tabla de Pedidos -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-150 dark:border-slate-700/80 shadow-sm overflow-hidden">
            @if ($orders->isEmpty())
                <div class="p-12 text-center space-y-4">
                    <div class="w-16 h-16 rounded-full bg-slate-50 dark:bg-slate-900 flex items-center justify-center text-slate-350 dark:text-slate-650 border border-slate-100 dark:border-slate-800 mx-auto">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    </div>
                    <h3 class="font-bold text-slate-800 dark:text-white text-base">No hay pedidos registrados</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm mx-auto">Cuando los clientes realicen compras en tu establecimiento, se listarán en esta pantalla.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-900/60 text-slate-400 dark:text-slate-400 text-[10px] font-black uppercase tracking-wider border-b border-slate-150 dark:border-slate-700/80">
                                <th class="py-4 px-6">N° Pedido</th>
                                <th class="py-4 px-6">Fecha</th>
                                <th class="py-4 px-6">Cliente</th>
                                <th class="py-4 px-6">Productos</th>
                                <th class="py-4 px-6">Total</th>
                                <th class="py-4 px-6">Pago</th>
                                <th class="py-4 px-6">Estado</th>
                                <th class="py-4 px-6 text-right">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-750 text-xs">
                            @foreach ($orders as $order)
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-750/10 transition">
                                    <!-- N° Pedido -->
                                    <td class="py-4 px-6 font-extrabold font-mono text-slate-900 dark:text-white">
                                        {{ $order->order_number }}
                                    </td>
                                    
                                    <!-- Fecha -->
                                    <td class="py-4 px-6 text-slate-500 dark:text-slate-400">
                                        {{ $order->created_at->timezone('America/Lima')->format('d/m/Y h:i A') }}
                                    </td>

                                    <!-- Cliente -->
                                    <td class="py-4 px-6">
                                        <div class="font-bold text-slate-850 dark:text-slate-200">
                                            {{ $order->user->first_name }} {{ $order->user->last_name }}
                                        </div>
                                        <div class="text-[10px] text-slate-450">{{ $order->user->phone }}</div>
                                    </td>

                                    <!-- Productos -->
                                    <td class="py-4 px-6 max-w-[200px] truncate text-slate-650 dark:text-slate-350">
                                        @foreach ($order->items as $item)
                                            <span>{{ $item->quantity }}x {{ $item->product_name }}</span>@if(!$loop->last), @endif
                                        @endforeach
                                    </td>

                                    <!-- Total -->
                                    <td class="py-4 px-6 font-black text-slate-800 dark:text-white">
                                        S/ {{ number_format($order->total, 2) }}
                                    </td>

                                    <!-- Pago -->
                                    <td class="py-4 px-6 capitalize text-slate-600 dark:text-slate-300">
                                        <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold {{ $order->payment_status === 'paid' ? 'bg-emerald-50 dark:bg-emerald-950/20 text-emerald-600 dark:text-emerald-400' : 'bg-amber-50 dark:bg-amber-950/20 text-amber-600 dark:text-amber-400' }}">
                                            {{ $order->payment_status === 'paid' ? 'Pagado' : 'Pendiente' }}
                                        </span>
                                    </td>

                                    <!-- Estado -->
                                    <td class="py-4 px-6 font-bold">
                                        @switch($order->status)
                                            @case('pending')
                                                <span class="inline-block px-2.5 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300">
                                                    ⏳ Recibido
                                                </span>
                                                @break
                                            @case('confirmed')
                                                <span class="inline-block px-2.5 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 border border-blue-100 dark:border-blue-900/30">
                                                    ✓ Confirmado
                                                </span>
                                                @break
                                            @case('preparing')
                                                <span class="inline-block px-2.5 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 border border-amber-100 dark:border-amber-900/30">
                                                    🍳 Preparando
                                                </span>
                                                @break
                                            @case('on_the_way')
                                                <span class="inline-block px-2.5 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-purple-50 dark:bg-purple-950/40 text-purple-600 dark:text-purple-400 border border-purple-100 dark:border-purple-900/30">
                                                    🛵 En Camino
                                                </span>
                                                @break
                                            @case('delivered')
                                                <span class="inline-block px-2.5 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-900/30">
                                                    🎁 Entregado
                                                </span>
                                                @break
                                            @case('cancelled')
                                                <span class="inline-block px-2.5 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 border border-rose-100 dark:border-rose-900/30">
                                                    ✕ Cancelado
                                                </span>
                                                @break
                                        @endswitch
                                    </td>

                                    <!-- Acciones -->
                                    <td class="py-4 px-6 text-right">
                                        <a href="{{ route('seller.orders.show', $order) }}" class="inline-block bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-650 text-slate-700 dark:text-slate-200 font-bold px-3.5 py-2 rounded-xl transition cursor-pointer">
                                            Gestionar
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-6 border-t border-slate-150 dark:border-slate-750">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.seller>
