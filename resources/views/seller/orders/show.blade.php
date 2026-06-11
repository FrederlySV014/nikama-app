<x-layouts.seller>
    <x-slot:title>Detalle de Pedido {{ $order->order_number }} - Nikama Seller</x-slot:title>

    <div class="space-y-6">
        <!-- Breadcrumbs -->
        <nav class="flex text-xs font-semibold text-slate-500 dark:text-slate-400 mb-6" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1.5 md:space-x-2">
                <li>
                    <a href="{{ route('seller.dashboard') }}" class="hover:text-slate-800 dark:hover:text-white transition-colors">Dashboard</a>
                </li>
                <li class="flex items-center">
                    <svg class="w-3 h-3 text-slate-400 mx-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    <a href="{{ route('seller.orders.index') }}" class="hover:text-slate-800 dark:hover:text-white transition-colors">Pedidos</a>
                </li>
                <li class="flex items-center">
                    <svg class="w-3 h-3 text-slate-400 mx-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    <span class="text-slate-800 dark:text-slate-200 font-bold" aria-current="page">Detalle</span>
                </li>
            </ol>
        </nav>

        <!-- Cabecera de Detalle -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-200 dark:border-slate-800">
            <div class="space-y-1">
                <span class="text-[10px] text-slate-455 uppercase font-black tracking-wider block">Gestionar Pedido</span>
                <div class="flex flex-wrap items-center gap-3">
                    <h1 class="text-3xl font-extrabold font-mono text-slate-800 dark:text-white">{{ $order->order_number }}</h1>
                    
                    <!-- Badge del Estado de Pedido -->
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
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400">
                    Pedido ingresado el {{ $order->created_at->timezone('America/Lima')->format('d/m/Y h:i A') }}
                </p>
            </div>
            <a href="{{ route('seller.orders.index') }}" class="px-5 py-3 border border-slate-200 hover:bg-slate-50 dark:border-slate-700 dark:hover:bg-slate-750 text-slate-700 dark:text-slate-350 font-bold text-xs rounded-2xl transition cursor-pointer flex items-center gap-1.5 self-start sm:self-center">
                <span>Volver al listado</span>
            </a>
        </div>

        <!-- Alertas de Sesión -->
        @if (session('success'))
            <div class="p-4 bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-250 dark:border-emerald-800/30 text-emerald-800 dark:text-emerald-350 rounded-2xl flex items-center gap-3 text-sm font-semibold">
                <span>✓</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- COLUMNA IZQUIERDA: Detalle del Cliente y Productos -->
            <div class="lg:col-span-7 space-y-6">
                
                <!-- Datos del Cliente -->
                <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-150 dark:border-slate-700 p-6 shadow-sm space-y-4">
                    <h3 class="text-sm text-slate-450 dark:text-slate-400 uppercase font-black tracking-wider">Datos de Entrega</h3>
                    
                    <div class="text-xs space-y-3">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-0.5">
                                <span class="text-[10px] text-slate-400 uppercase font-bold block">Cliente</span>
                                <p class="font-extrabold text-slate-800 dark:text-white">{{ $order->user->first_name }} {{ $order->user->last_name }}</p>
                                <p class="text-slate-500">{{ $order->user->phone }} | {{ $order->user->email }}</p>
                            </div>
                            <div class="space-y-0.5">
                                <span class="text-[10px] text-slate-400 uppercase font-bold block">Dirección de Envío</span>
                                <p class="font-extrabold text-slate-800 dark:text-white">{{ $order->delivery_address }}</p>
                                @if($order->delivery_reference)
                                    <p class="text-slate-500 italic">Ref: {{ $order->delivery_reference }}</p>
                                @endif
                            </div>
                        </div>

                        @if($order->notes)
                            <div class="bg-slate-50 dark:bg-slate-900/30 p-3.5 rounded-2xl border border-slate-100 dark:border-slate-750 pt-3">
                                <span class="text-[10px] text-slate-400 uppercase font-black tracking-wider block mb-1">Notas especiales</span>
                                <p class="text-slate-750 dark:text-slate-300 leading-relaxed font-semibold italic">{{ $order->notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Detalle de Productos -->
                <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-150 dark:border-slate-700 p-6 shadow-sm space-y-4">
                    <h3 class="text-sm text-slate-450 dark:text-slate-400 uppercase font-black tracking-wider">Productos del Pedido</h3>
                    
                    <div class="divide-y divide-slate-100 dark:divide-slate-750 border-b border-slate-100 dark:border-slate-750 pb-4">
                        @foreach ($order->items as $item)
                            <div class="py-3 flex justify-between gap-4 text-xs">
                                <div class="space-y-1">
                                    <p class="font-extrabold text-slate-800 dark:text-white">
                                        <span class="text-luffy-red-dark dark:text-luffy-red font-black">{{ $item->quantity }}x</span> {{ $item->product_name }}
                                    </p>
                                    @if ($item->options->isNotEmpty())
                                        <p class="text-[10px] text-slate-450 dark:text-slate-450 leading-relaxed pl-3 border-l-2 border-slate-100 dark:border-slate-700">
                                            @foreach ($item->options as $opt)
                                                + {{ $opt->option_name }} (+S/ {{ number_format($opt->additional_price, 2) }})@if(!$loop->last), @endif
                                            @endforeach
                                        </p>
                                    @endif
                                </div>
                                <span class="font-bold text-slate-800 dark:text-slate-200">S/ {{ number_format($item->subtotal, 2) }}</span>
                            </div>
                        @endforeach
                    </div>

                    <div class="space-y-2 text-xs">
                        <div class="flex justify-between text-slate-500">
                            <span>Subtotal</span>
                            <span class="font-semibold text-slate-750 dark:text-slate-350">S/ {{ number_format($order->subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-slate-500">
                            <span>Envío</span>
                            <span class="font-semibold text-slate-750 dark:text-slate-350">S/ {{ number_format($order->delivery_fee, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-base font-black text-slate-850 dark:text-white pt-2 border-t border-slate-100 dark:border-slate-750">
                            <span>Total del Pedido</span>
                            <span>S/ {{ number_format($order->total, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Historial de Estados (Auditoría) -->
                <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-150 dark:border-slate-700 p-6 shadow-sm space-y-4">
                    <h3 class="text-sm text-slate-450 dark:text-slate-400 uppercase font-black tracking-wider">Historial de Transiciones</h3>
                    
                    @if ($order->statusHistory->isEmpty())
                        <p class="text-xs text-slate-400 italic">No hay registros de transiciones para este pedido.</p>
                    @else
                        <div class="relative pl-6 space-y-4">
                            <div class="absolute left-2 top-2 bottom-2 w-[1px] bg-slate-100 dark:bg-slate-750"></div>
                            @foreach ($order->statusHistory as $history)
                                <div class="relative text-xs">
                                    <div class="absolute -left-6 top-1 w-2.5 h-2.5 rounded-full bg-slate-300 dark:bg-slate-600"></div>
                                    <div class="flex flex-wrap items-center justify-between gap-2">
                                        <p class="font-bold text-slate-700 dark:text-slate-300">
                                            Estado cambiado a: <span class="capitalize text-slate-900 dark:text-white">{{ $history->status }}</span>
                                        </p>
                                        <span class="text-[10px] text-slate-400 font-medium">
                                            {{ $history->created_at->timezone('America/Lima')->format('d/m/Y h:i A') }}
                                        </span>
                                    </div>
                                    <p class="text-[11px] text-slate-500 dark:text-slate-450 mt-0.5">{{ $history->description }}</p>
                                    @if($history->changedByUser)
                                        <p class="text-[9px] text-slate-400 font-bold mt-0.5">Por: {{ $history->changedByUser->first_name }} {{ $history->changedByUser->last_name }} ({{ $history->changedByUser->roles->first()?->name ?? 'Usuario' }})</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

            </div>

            <!-- COLUMNA DERECHA: Control de Estados y Asignación de Repartidores -->
            <div class="lg:col-span-5 space-y-6">
                
                <!-- Acciones de Estado -->
                <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-150 dark:border-slate-700 p-6 shadow-sm space-y-4" x-data="{ showCancelModal: false }">
                    <h3 class="text-sm text-slate-455 dark:text-slate-400 uppercase font-black tracking-wider">Panel de Estados</h3>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">Actualiza las etapas operacionales internas de este pedido.</p>

                    <div class="flex flex-col gap-2.5 text-xs">
                        @if ($order->status === 'pending')
                            <form action="{{ route('seller.orders.updateStatus', $order) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="confirmed">
                                <button type="submit" class="w-full py-3 bg-blue-500 hover:bg-blue-600 text-white font-extrabold rounded-2xl shadow-md transition cursor-pointer text-center">
                                    ✓ Confirmar Pedido
                                </button>
                            </form>
                        @endif

                        @if ($order->status === 'confirmed')
                            <form action="{{ route('seller.orders.updateStatus', $order) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="preparing">
                                <button type="submit" class="w-full py-3 bg-amber-500 hover:bg-amber-600 text-white font-extrabold rounded-2xl shadow-md transition cursor-pointer text-center">
                                    🍳 Iniciar Preparación (Listo en Local)
                                </button>
                            </form>
                        @endif

                        @if ($order->status === 'on_the_way')
                            <form action="{{ route('seller.orders.updateStatus', $order) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="delivered">
                                <button type="submit" class="w-full py-3 bg-emerald-500 hover:bg-emerald-600 text-white font-extrabold rounded-2xl shadow-md transition cursor-pointer text-center">
                                    🎁 Marcar como Entregado (Llegó al Destino)
                                </button>
                            </form>
                        @endif

                        @if (!in_array($order->status, ['delivered', 'cancelled']))
                            <button type="button" @click="showCancelModal = true" class="w-full py-2.5 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/20 dark:hover:bg-rose-900/10 text-rose-600 dark:text-rose-400 font-bold rounded-2xl border border-rose-200 dark:border-rose-800/50 transition cursor-pointer text-center">
                                ✕ Cancelar Pedido
                            </button>

                            <!-- Modal de Cancelación -->
                            <div x-show="showCancelModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
                                <div @click.away="showCancelModal = false" class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-2xl p-6 max-w-md w-full space-y-4 text-left">
                                    <div class="flex items-center gap-2 text-rose-500">
                                        <span class="text-xl">⚠️</span>
                                        <h3 class="text-lg font-extrabold font-['Outfit']">Cancelar Pedido</h3>
                                    </div>
                                    <p class="text-xs text-slate-550 dark:text-slate-400 leading-relaxed">
                                        ¿Estás seguro de que deseas cancelar este pedido? Si el pago fue realizado de manera digital, se realizará un reembolso automático al cliente. Por favor, ingresa el motivo del rechazo:
                                    </p>
                                    <form action="{{ route('seller.orders.updateStatus', $order) }}" method="POST" class="space-y-4">
                                        @csrf
                                        <input type="hidden" name="status" value="cancelled">
                                        <div class="space-y-1">
                                            <label for="cancellation_reason" class="font-bold text-slate-455 uppercase text-[9px] block">Motivo de Cancelación</label>
                                            <textarea id="cancellation_reason" name="cancellation_reason" required minlength="5" maxlength="500" rows="3"
                                                      placeholder="Ej. Sin stock del producto principal, establecimiento cerrado, etc."
                                                      class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl p-3 text-xs text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-rose-500/50"></textarea>
                                        </div>
                                        <div class="flex gap-3 pt-2">
                                            <button type="button" @click="showCancelModal = false" class="flex-1 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-650 text-slate-750 dark:text-slate-200 font-bold rounded-xl text-xs transition">
                                                Cerrar
                                            </button>
                                            <button type="submit" class="flex-1 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-extrabold rounded-xl text-xs shadow-md transition">
                                                Confirmar Cancelación
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endif

                        @if (in_array($order->status, ['delivered', 'cancelled']))
                            <div class="p-4 bg-slate-50 dark:bg-slate-900/40 rounded-2xl border border-slate-100 dark:border-slate-750 text-center">
                                <p class="font-extrabold text-slate-800 dark:text-white capitalize">Pedido {{ $order->status === 'delivered' ? 'Entregado 🎉' : 'Cancelado ✕' }}</p>
                                <p class="text-[10px] text-slate-455 mt-1">Este pedido ya finalizó su ciclo operacional y no admite cambios de estado.</p>
                            </div>
                        @endif
                    </div>
                </div>

                @php
                    $latestAssignment = $order->driverAssignments->sortByDesc('created_at')->first();
                @endphp

                <!-- Panel de Despacho / Asignación de Repartidores -->
                @if (in_array($order->status, ['confirmed', 'preparing']))
                    @if ($latestAssignment && $latestAssignment->status === \App\Models\DriverAssignment::STATUS_ASSIGNED)
                        <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-150 dark:border-slate-700 p-6 shadow-sm space-y-4">
                            <div class="flex items-center gap-2">
                                <span class="text-base animate-bounce">🛵</span>
                                <h3 class="text-sm font-extrabold font-['Outfit'] text-slate-800 dark:text-white">Despacho Solicitado</h3>
                            </div>
                            <div class="p-4 bg-amber-50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-900/30 text-amber-800 dark:text-amber-300 rounded-2xl space-y-2 text-xs">
                                <p class="font-bold">⏳ Solicitud enviada a: {{ $latestAssignment->driver->user->first_name }} {{ $latestAssignment->driver->user->last_name }}</p>
                                <p>Esperando que el repartidor acepte o rechace el pedido.</p>
                                <div class="flex items-center gap-2 text-[10px] text-amber-600/80 dark:text-amber-400/80 font-mono mt-2">
                                    <span class="animate-spin inline-block w-3 h-3 border-2 border-amber-500 border-t-transparent rounded-full"></span>
                                    <span>Sincronizando estado en tiempo real...</span>
                                </div>
                            </div>
                            <!-- Script de recarga removido en favor del polling centralizado al final de la página -->
                        </div>
                    @elseif ($latestAssignment && $latestAssignment->status === \App\Models\DriverAssignment::STATUS_ACCEPTED)
                        <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-150 dark:border-slate-700 p-6 shadow-sm space-y-4">
                            <div class="flex items-center gap-2">
                                <span class="text-base text-emerald-500">🛵</span>
                                <h3 class="text-sm font-extrabold font-['Outfit'] text-slate-800 dark:text-white">Repartidor Listo</h3>
                            </div>
                            <div class="p-4 bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-250 dark:border-emerald-800/30 text-emerald-800 dark:text-emerald-350 rounded-2xl space-y-2 text-xs">
                                <p class="font-bold">✅ {{ $latestAssignment->driver->user->first_name }} {{ $latestAssignment->driver->user->last_name }} aceptó el pedido</p>
                                <p>El repartidor está preparando el despacho. Esperando inicio de trayecto...</p>
                                <div class="flex items-center gap-2 text-[10px] text-emerald-650 dark:text-emerald-400 font-mono mt-2">
                                    <span class="animate-spin inline-block w-3 h-3 border-2 border-emerald-500 border-t-transparent rounded-full"></span>
                                    <span>Sincronizando estado en tiempo real...</span>
                                </div>
                            </div>
                            <!-- Script de recarga removido en favor del polling centralizado al final de la página -->
                        </div>
                    @else
                        <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-150 dark:border-slate-700 p-6 shadow-sm space-y-4">
                            <div class="flex items-center gap-2">
                                <span class="text-base">🛵</span>
                                <h3 class="text-sm font-extrabold font-['Outfit'] text-slate-800 dark:text-white">Despachar Pedido</h3>
                            </div>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400">Asigna un repartidor activo del sistema para que comience el viaje de reparto.</p>

                            @if ($latestAssignment && $latestAssignment->status === \App\Models\DriverAssignment::STATUS_REJECTED)
                                <div class="p-3.5 bg-rose-50 dark:bg-rose-950/20 border border-rose-200 dark:border-rose-800/50 text-rose-700 dark:text-rose-400 rounded-2xl text-xs font-semibold flex items-start gap-2">
                                    <span>✕</span>
                                    <div>
                                        <p class="font-extrabold">Solicitud Rechazada</p>
                                        <p class="text-[10px] opacity-90 mt-0.5">El repartidor {{ $latestAssignment->driver->user->first_name }} {{ $latestAssignment->driver->user->last_name }} rechazó la entrega. Por favor, selecciona otro repartidor.</p>
                                    </div>
                                </div>
                            @endif

                            @if ($drivers->isEmpty())
                                <div class="p-3 bg-amber-50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-900/30 text-amber-800 dark:text-amber-300 rounded-xl text-center text-[10px] font-bold">
                                    ⚠ No hay repartidores activos y disponibles en este momento.
                                </div>
                            @else
                                <form action="{{ route('seller.orders.assignDriver', $order) }}" method="POST" class="space-y-4">
                                    @csrf
                                    
                                    <div class="space-y-1 text-xs">
                                        <label for="driver_profile_id" class="font-bold text-slate-455 uppercase text-[9px] block">Seleccionar Repartidor</label>
                                        <select id="driver_profile_id" name="driver_profile_id" required
                                                class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl p-3 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500/50">
                                            <option value="" disabled selected>-- Elige un repartidor disponible --</option>
                                            @foreach ($drivers as $dr)
                                                <option value="{{ $dr->id }}">
                                                    👤 {{ $dr->user->first_name }} {{ $dr->user->last_name }} ({{ ucfirst($dr->vehicle_type ?? 'Repartidor') }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('driver_profile_id')
                                            <p class="text-[10px] text-rose-500 font-bold mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <button type="submit" class="w-full py-3 bg-purple-600 hover:bg-purple-700 text-white font-extrabold rounded-2xl shadow-md transition cursor-pointer text-center">
                                        🛵 Despachar y Enviar Pedido
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endif
                @endif

                <!-- Datos de Despacho Activos (si está en camino o entregado) -->
                @if (in_array($order->status, ['on_the_way', 'delivered']) && $order->deliveries->isNotEmpty())
                    <div class="bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-800/80 dark:to-slate-900/60 rounded-3xl border border-slate-200 dark:border-slate-700 p-6 shadow-md space-y-4">
                        <div class="flex items-center gap-2">
                            <span class="text-base">🛵</span>
                            <h3 class="text-sm font-extrabold font-['Outfit'] text-slate-800 dark:text-white">Datos de Reparto</h3>
                        </div>

                        <div class="text-xs space-y-2">
                            @foreach ($order->deliveries as $del)
                                <div class="p-3 bg-white dark:bg-slate-800 rounded-xl border border-slate-150 dark:border-slate-700/80 space-y-2">
                                    <div class="flex items-center justify-between text-[10px] font-black uppercase text-slate-400">
                                        <span>Repartidor asignado</span>
                                        <span class="text-emerald-500">
                                            @switch($del->status)
                                                @case('pending')
                                                    Pendiente
                                                    @break
                                                @case('assigned')
                                                    Asignado
                                                    @break
                                                @case('picked_up')
                                                    Retirado
                                                    @break
                                                @case('on_the_way')
                                                    En Camino
                                                    @break
                                                @case('delivered')
                                                    Entregado
                                                    @break
                                                @default
                                                    {{ $del->status }}
                                            @endswitch
                                        </span>
                                    </div>
                                    <div class="space-y-0.5">
                                        <p class="font-extrabold text-slate-800 dark:text-white">
                                            👤 {{ $del->driver?->user->first_name }} {{ $del->driver?->user->last_name }}
                                        </p>
                                        <p class="text-slate-500">Licencia: {{ $del->driver?->license_number ?? '-' }}</p>
                                        @if ($del->driver?->license_plate)
                                            <p class="text-slate-500">Vehículo: {{ $del->driver?->vehicle_brand }} {{ $del->driver?->vehicle_model }} (Placa: {{ $del->driver?->license_plate }})</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>

        </div>
    </div>

    <!-- Polling de Estado y Asignación en Tiempo Real -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const initialOrderStatus = '{{ $order->status }}';
            const initialAssignmentStatus = '{{ $latestAssignment ? $latestAssignment->status : "" }}';
            const statusCheckUrl = "{{ route('orders.location', $order) }}";

            function checkOrderStatus() {
                fetch(statusCheckUrl)
                    .then(res => res.json())
                    .then(data => {
                        const currentStatus = data.status;
                        const currentAssignmentStatus = data.assignment_status || "";

                        // Si el estado general del pedido o el estado de la asignación cambió, recargar la página
                        if (currentStatus !== initialOrderStatus || currentAssignmentStatus !== initialAssignmentStatus) {
                            window.location.reload();
                        }
                    })
                    .catch(err => console.error('Error polling order status:', err));
            }

            // Consultar cada 4 segundos si hay actualizaciones en el backend
            setInterval(checkOrderStatus, 4000);
        });
    </script>
</x-layouts.seller>
