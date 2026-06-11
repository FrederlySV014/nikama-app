<x-layouts.public>
    <x-slot:title>Mis Pedidos - Nikama</x-slot:title>

    <div class="max-w-6xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
        <!-- Breadcrumbs -->
        <nav class="flex text-xs font-semibold text-slate-500 dark:text-slate-400 mb-6" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1.5 md:space-x-2">
                <li class="inline-flex items-center">
                    <a href="/" class="hover:text-slate-800 dark:hover:text-white transition-colors">Inicio</a>
                </li>
                <li class="flex items-center">
                    <svg class="w-3 h-3 text-slate-400 mx-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    <span class="text-slate-400 dark:text-slate-500">Mi Perfil</span>
                </li>
                <li class="flex items-center">
                    <svg class="w-3 h-3 text-slate-400 mx-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    <span class="text-slate-800 dark:text-slate-200 font-bold" aria-current="page">Mis Pedidos</span>
                </li>
            </ol>
        </nav>

        <!-- Header Title -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-extrabold font-['Outfit'] text-slate-800 dark:text-white">Historial de Pedidos</h1>
                <p class="text-sm text-slate-550 dark:text-slate-400 mt-1">Sigue el estado de tus pedidos activos y revisa tus compras pasadas.</p>
            </div>
        </div>

        <!-- Orders list -->
        @if ($orders->isEmpty())
            <div class="bg-white dark:bg-slate-800 rounded-3xl border border-dashed border-slate-200 dark:border-slate-700 p-12 text-center">
                <div class="w-16 h-16 rounded-full bg-slate-50 dark:bg-slate-900 flex items-center justify-center text-slate-350 dark:text-slate-650 border border-slate-100 dark:border-slate-800 mx-auto mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                </div>
                <h3 class="font-bold text-slate-800 dark:text-white text-base">Aún no has realizado pedidos</h3>
                <p class="text-sm text-slate-550 dark:text-slate-400 mt-1 max-w-sm mx-auto">Cuando realices compras en nuestras tiendas asociadas, aparecerán aquí para su seguimiento.</p>
                <a 
                    href="{{ route('public.welcome') }}" 
                    class="mt-5 inline-block bg-luffy-red hover:bg-luffy-red-hover text-white px-5 py-3 rounded-2xl text-xs font-bold transition-all cursor-pointer shadow-md shadow-luffy-red/10"
                >
                    Explorar Restaurantes
                </a>
            </div>
        @else
            <div class="space-y-6">
                @foreach ($orders as $order)
                    <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/80 p-6 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-4 border-b border-slate-100 dark:border-slate-750">
                            <!-- Info principal del pedido -->
                            <div class="space-y-1">
                                <div class="flex flex-wrap items-center gap-2.5">
                                    <h3 class="font-extrabold font-mono text-slate-900 dark:text-white text-base">
                                        {{ $order->order_number }}
                                    </h3>
                                    
                                    <!-- Badges de estado del pedido -->
                                    @switch($order->status)
                                        @case('pending')
                                            <span class="inline-block px-2.5 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 border border-slate-200/20">
                                                ⏳ Recibido
                                            </span>
                                            @break
                                        @case('confirmed')
                                            <span class="inline-block px-2.5 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 border border-blue-100 dark:border-blue-900/30">
                                                ✓ Confirmado
                                            </span>
                                            @break
                                        @case('preparing')
                                            <span class="inline-block px-2.5 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-amber-55/40 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 border border-amber-100 dark:border-amber-900/30">
                                                🍳 Preparando
                                            </span>
                                            @break
                                        @case('on_the_way')
                                            <span class="inline-block px-2.5 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-purple-50 dark:bg-purple-950/40 text-purple-600 dark:text-purple-400 border border-purple-100 dark:border-purple-900/30 animate-pulse">
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

                                    <!-- Badge de estado de Pago -->
                                    <span class="inline-block px-2 py-0.5 rounded-md text-[9px] font-bold uppercase tracking-wider {{ $order->payment_status === 'paid' ? 'bg-emerald-100/30 text-emerald-600 dark:text-emerald-400 border border-emerald-500/10' : 'bg-amber-100/30 text-amber-600 dark:text-amber-450 border border-amber-500/10' }}">
                                        {{ $order->payment_status === 'paid' ? 'Pagado' : 'Pago Pendiente' }}
                                    </span>
                                </div>
                                <p class="text-xs text-slate-450 dark:text-slate-400">
                                    Realizado el {{ $order->created_at->timezone('America/Lima')->format('d/m/Y h:i A') }}
                                </p>
                            </div>

                            <!-- Total del pedido y botón -->
                            <div class="flex items-center justify-between lg:justify-end gap-6 w-full lg:w-auto">
                                <div class="text-left lg:text-right">
                                    <span class="text-xs text-slate-400 uppercase font-bold tracking-wider block">Total Pagado</span>
                                    <span class="font-extrabold text-slate-800 dark:text-white text-lg">S/ {{ number_format($order->total, 2) }}</span>
                                </div>
                                <a 
                                    href="{{ route('orders.show', $order) }}" 
                                    class="bg-slate-50 dark:bg-slate-750/50 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-200/80 dark:border-slate-700 px-5 py-3 rounded-2xl font-bold text-xs transition-all flex items-center gap-1.5 cursor-pointer shrink-0"
                                >
                                    <span>Seguir Pedido</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </a>
                            </div>
                        </div>

                        <!-- Detalles rápidos de items -->
                        <div class="pt-4 flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div class="space-y-1.5 max-w-xl">
                                <span class="text-[10px] text-slate-400 uppercase font-black tracking-wider block">Productos</span>
                                <p class="text-xs text-slate-650 dark:text-slate-350 line-clamp-1">
                                    @foreach ($order->items as $item)
                                        <span class="font-bold">{{ $item->quantity }}x</span> {{ $item->product_name }}@if (!$loop->last), @endif
                                    @endforeach
                                </p>
                            </div>

                            <div class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-2">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                <span class="truncate max-w-[250px] md:max-w-[350px]">{{ $order->delivery_address }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="pt-4">
                    {{ $orders->links() }}
                </div>
            </div>
        @endif
    </div>
</x-layouts.public>
