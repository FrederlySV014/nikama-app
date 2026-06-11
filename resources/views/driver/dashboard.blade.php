<x-layouts.driver>
    <x-slot:title>Driver Dashboard - Nikama</x-slot:title>

    <div class="space-y-8">
        <!-- Banner de Bienvenida -->
        <div class="relative bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 p-8 rounded-3xl text-white shadow-xl overflow-hidden border border-indigo-900/40">
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,_var(--tw-gradient-stops))] from-luffy-straw/10 via-transparent to-transparent pointer-events-none"></div>
            <div class="relative z-10 space-y-1">
                <span class="text-[10px] text-luffy-straw uppercase font-black tracking-wider block">Conductor Oficial</span>
                <h2 class="text-3xl font-extrabold font-['Outfit'] text-white">¡Hola, {{ auth()->user()->first_name }}!</h2>
                <p class="text-white/60 mt-1 text-sm max-w-2xl font-medium">Gestiona tus asignaciones operativas en curso y visualiza tus estadísticas de despacho del día de hoy.</p>
            </div>
        </div>

        <!-- Alertas de Estado -->
        @if (session('success'))
            <div class="p-4 bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-250 dark:border-emerald-800/30 text-emerald-800 dark:text-emerald-350 rounded-2xl flex items-center gap-3 text-sm font-semibold">
                <span>✓</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('warning'))
            <div class="p-4 bg-amber-50 dark:bg-amber-955/20 border border-amber-250 dark:border-amber-800/30 text-amber-800 dark:text-amber-350 rounded-2xl flex items-center gap-3 text-sm font-semibold">
                <span>⚠</span>
                <span>{{ session('warning') }}</span>
            </div>
        @endif

        @if ($pendingAssignments->isNotEmpty())
            <!-- VISTA EXCLUSIVA DE SOLICITUD PENDIENTE (ALERTA CRÍTICA) -->
            <div class="space-y-6">
                <div class="bg-gradient-to-r from-rose-900/50 via-slate-900 to-rose-900/50 p-8 rounded-3xl text-white shadow-xl relative overflow-hidden border border-rose-800/20 text-center">
                    <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,_var(--tw-gradient-stops))] from-rose-500/10 via-transparent to-transparent pointer-events-none"></div>
                    <h2 class="text-3xl font-extrabold font-['Outfit'] text-white">🚨 Solicitud de Envío Recibida</h2>
                    <p class="text-white/70 mt-2 font-medium text-xs">Tienes un pedido asignado pendiente de aceptación. Debes confirmar o rechazar esta entrega.</p>
                </div>

                <div class="grid grid-cols-1 gap-6 max-w-2xl mx-auto">
                    @foreach ($pendingAssignments as $assignment)
                        <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-150 dark:border-slate-700/80 p-6 shadow-xl space-y-4">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 dark:border-slate-750 pb-3">
                                <div>
                                    <span class="text-[9px] text-slate-400 font-bold uppercase tracking-wider block">Pedido Asignado</span>
                                    <h4 class="font-extrabold font-mono text-slate-800 dark:text-white text-base">
                                        {{ $assignment->order->order_number }}
                                    </h4>
                                </div>
                                <span class="text-xs font-black text-slate-800 dark:text-white sm:text-right">
                                    Monto a cobrar: 
                                    <span class="text-emerald-600 dark:text-emerald-400 font-extrabold">
                                        @if (($assignment->order->payments->first()?->payment_method ?? 'cash') === 'cash')
                                            S/ {{ number_format($assignment->order->total, 2) }} (Efectivo)
                                        @else
                                            S/ 0.00 (Pagado online)
                                        @endif
                                    </span>
                                </span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                                <div class="space-y-1">
                                    <span class="text-[10px] text-slate-400 uppercase font-bold block">Origen (Comercio)</span>
                                    <p class="font-bold text-slate-700 dark:text-slate-350">
                                        🏪 {{ $assignment->delivery->business->business_name }}
                                    </p>
                                    <p class="text-slate-500 text-[11px]">{{ $assignment->delivery->business->locations()->where('is_main', true)->first()?->address ?? 'Sede Principal' }}</p>
                                </div>
                                <div class="space-y-1">
                                    <span class="text-[10px] text-slate-400 uppercase font-bold block">Destino (Cliente)</span>
                                    <p class="font-bold text-slate-700 dark:text-slate-350">
                                        📍 {{ $assignment->order->delivery_address }}
                                    </p>
                                    @if ($assignment->order->delivery_reference)
                                        <p class="text-slate-500 text-[11px] italic">Ref: {{ $assignment->order->delivery_reference }}</p>
                                    @endif
                                </div>
                            </div>

                            <!-- Listado de Productos -->
                            <div class="space-y-1 text-xs">
                                <span class="text-[10px] text-slate-400 uppercase font-bold block">Productos</span>
                                <div class="bg-slate-50 dark:bg-slate-900/40 rounded-xl p-3 border border-slate-100 dark:border-slate-750/30 space-y-1">
                                    @foreach ($assignment->order->items as $item)
                                        <p class="text-slate-700 dark:text-slate-350 font-semibold"><span class="text-purple-600 dark:text-purple-400 font-bold">{{ $item->quantity }}x</span> {{ $item->product_name }}</p>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Botones Aceptar / Rechazar -->
                            <div class="flex items-center gap-3 pt-3 border-t border-slate-100 dark:border-slate-750">
                                <form action="{{ route('driver.assignments.accept', $assignment) }}" method="POST" class="flex-1">
                                    @csrf
                                    <button type="submit" 
                                            class="w-full py-3 bg-emerald-500 hover:bg-emerald-600 text-white font-extrabold text-xs rounded-2xl shadow-md transition cursor-pointer text-center">
                                        ✓ Aceptar Pedido
                                    </button>
                                </form>

                                <form action="{{ route('driver.assignments.reject', $assignment) }}" method="POST" class="flex-1">
                                    @csrf
                                    <button type="submit" 
                                            class="w-full py-3 bg-rose-50 hover:bg-rose-100 dark:bg-rose-955/20 dark:hover:bg-rose-900/10 text-rose-600 dark:text-rose-450 font-bold text-xs rounded-2xl border border-rose-200 dark:border-rose-800/50 transition cursor-pointer text-center">
                                        ✕ Rechazar
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <!-- VISTA NORMAL (ESTADÍSTICAS Y OPERACIÓN DIARIA) -->
            <!-- Métricas Operativas de Hoy -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Entregas Hoy -->
                <div class="bg-white dark:bg-slate-800 p-6 rounded-3xl border border-slate-150 dark:border-slate-700/80 shadow-sm">
                    <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider block">Entregas Hoy</span>
                    <span class="block text-3xl font-extrabold font-['Outfit'] text-slate-850 dark:text-white mt-3">{{ $todayCompletedCount }}</span>
                    <span class="text-[10px] text-slate-400 font-medium block mt-1">Viajes completados con éxito</span>
                </div>

                <!-- Fallidos / Rechazados Hoy -->
                <div class="bg-white dark:bg-slate-800 p-6 rounded-3xl border border-slate-150 dark:border-slate-700/80 shadow-sm">
                    <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider block">Rechazados Hoy</span>
                    <span class="block text-3xl font-extrabold font-['Outfit'] text-slate-850 dark:text-white mt-3 text-rose-600 dark:text-rose-400">{{ $todayFailedCount }}</span>
                    <span class="text-[10px] text-slate-400 font-medium block mt-1">Incidentes en puerta hoy</span>
                </div>

                <!-- Total Entregas Histórico -->
                <div class="bg-white dark:bg-slate-800 p-6 rounded-3xl border border-slate-150 dark:border-slate-700/80 shadow-sm">
                    <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider block">Histórico Total</span>
                    <span class="block text-3xl font-extrabold font-['Outfit'] text-slate-850 dark:text-white mt-3">{{ auth()->user()->driverProfile->total_deliveries }}</span>
                    <span class="text-[10px] text-slate-400 font-medium block mt-1">Acumulado histórico total</span>
                </div>

                <!-- Reputación Promedio -->
                <div class="bg-white dark:bg-slate-800 p-6 rounded-3xl border border-slate-150 dark:border-slate-700/80 shadow-sm">
                    <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider block">Calificación</span>
                    <span class="block text-3xl font-extrabold font-['Outfit'] text-amber-500 mt-3">⭐ {{ number_format(auth()->user()->driverProfile->rating_average, 2) }}</span>
                    <span class="text-[10px] text-slate-400 font-medium block mt-1">Promedio de estrellas de clientes</span>
                </div>
            </div>

            <!-- ENVIOS ACTIVOS EN CURSO -->
            @if ($activeDelivery)
                <div class="bg-gradient-to-br from-slate-900 via-indigo-950/80 to-slate-950 p-6 rounded-3xl border border-indigo-900/60 text-white shadow-xl space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="text-2xl animate-bounce">🛵</span>
                            <h3 class="text-lg font-black font-['Outfit']">Envío Activo en Progreso</h3>
                        </div>
                        <span class="inline-block px-3 py-1 bg-indigo-500/20 text-indigo-300 text-[10px] font-black uppercase tracking-wider rounded-full border border-indigo-500/30 animate-pulse">
                            @if ($activeDelivery->status === 'on_the_way')
                                En Camino
                            @else
                                Asignado / Recoger Pedido
                            @endif
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs text-slate-300 border-b border-indigo-900/60 pb-4">
                        <div class="space-y-2">
                            <p>Pedido: <span class="font-mono font-bold text-white">{{ $activeDelivery->order->order_number }}</span></p>
                            <p>Establecimiento: <span class="font-semibold text-white">🏪 {{ $activeDelivery->business->business_name }}</span></p>
                            <p>Dirección de entrega: <span class="font-semibold text-white">📍 {{ $activeDelivery->order->delivery_address }}</span></p>
                            @if ($activeDelivery->order->delivery_reference)
                                <p class="text-[11px] text-slate-400 italic">Ref: {{ $activeDelivery->order->delivery_reference }}</p>
                            @endif
                        </div>
                        <div class="space-y-2">
                            <p>Cliente: <span class="font-semibold text-white">👤 {{ $activeDelivery->order->user->first_name }} {{ $activeDelivery->order->user->last_name }}</span></p>
                            <p>Teléfono: <span class="font-semibold text-white">📞 {{ $activeDelivery->order->user->phone }}</span></p>
                            <p>Cobro al Cliente: 
                                <span class="font-black text-emerald-400">
                                    @if (($activeDelivery->order->payments->first()?->payment_method ?? 'cash') === 'cash')
                                        S/ {{ number_format($activeDelivery->order->total, 2) }} (Efectivo)
                                    @else
                                        S/ 0.00 (Pagado online)
                                    @endif
                                </span>
                            </p>
                        </div>
                    </div>

                    <!-- Detalle de Productos -->
                    <div class="space-y-2">
                        <span class="text-[10px] text-slate-400 uppercase font-black tracking-wider block">Productos a entregar:</span>
                        <div class="bg-slate-950/40 rounded-2xl p-4 border border-indigo-900/40 text-xs space-y-1.5">
                            @foreach ($activeDelivery->order->items as $item)
                                <div class="flex justify-between">
                                    <span class="font-semibold text-slate-200"><span class="text-indigo-400 font-bold">{{ $item->quantity }}x</span> {{ $item->product_name }}</span>
                                    <span class="text-slate-400">S/ {{ number_format($item->subtotal, 2) }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="pt-2">
                        <a href="{{ route('driver.deliveries.show', $activeDelivery) }}" 
                           class="inline-block w-full text-center py-3.5 bg-indigo-600 hover:bg-indigo-500 text-white font-extrabold text-xs rounded-2xl shadow-lg transition cursor-pointer">
                            Ver Detalles y Pantalla de Reparto
                        </a>
                    </div>
                </div>
            @else
                <div class="bg-white dark:bg-slate-800 p-8 rounded-3xl border border-slate-150 dark:border-slate-700/80 shadow-sm text-center py-12 space-y-4">
                    <div class="w-16 h-16 rounded-full bg-slate-50 dark:bg-slate-900 flex items-center justify-center text-slate-400 mx-auto text-xl">
                        🛵
                    </div>
                    <h3 class="text-base font-bold text-slate-850 dark:text-white">Sin Envíos Activos</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm mx-auto">No tienes repartos asignados en curso. Mantente alerta por nuevas solicitudes de comercios cercanos.</p>
                </div>
            @endif

            <!-- PERFIL DE VEHÍCULO Y MANTENIMIENTO -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white dark:bg-slate-800 p-8 rounded-3xl border border-slate-150 dark:border-slate-700 shadow-sm space-y-4">
                    <h3 class="text-lg font-bold font-['Outfit'] border-b border-slate-100 dark:border-slate-750 pb-2 text-slate-800 dark:text-white">Perfil de Conducción</h3>
                    <div class="space-y-3 text-xs">
                        <div class="flex justify-between"><span class="text-slate-500 font-medium">Vehículo:</span> <span class="font-bold uppercase text-slate-850 dark:text-white">{{ auth()->user()->driverProfile->vehicle_type }}</span></div>
                        <div class="flex justify-between"><span class="text-slate-500 font-medium">Marca/Modelo:</span> <span class="font-bold text-slate-850 dark:text-white">{{ auth()->user()->driverProfile->vehicle_brand ?? 'N/A' }} {{ auth()->user()->driverProfile->vehicle_model ?? '' }}</span></div>
                        <div class="flex justify-between"><span class="text-slate-500 font-medium">Placa:</span> <span class="font-bold uppercase text-slate-850 dark:text-white">{{ auth()->user()->driverProfile->license_plate ?? 'N/A' }}</span></div>
                        <div class="flex justify-between"><span class="text-slate-500 font-medium">Licencia N°:</span> <span class="font-bold text-slate-850 dark:text-white">{{ auth()->user()->driverProfile->license_number ?? 'N/A' }}</span></div>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-800 p-8 rounded-3xl border border-slate-150 dark:border-slate-700 shadow-sm flex flex-col justify-between gap-6">
                    <div>
                        <h3 class="text-lg font-bold font-['Outfit'] border-b border-slate-100 dark:border-slate-750 pb-2 mb-4 text-slate-800 dark:text-white">Acciones Rápidas</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">Accede al registro histórico completo de todos tus viajes de reparto realizados en la plataforma.</p>
                    </div>
                    <div class="space-y-3">
                        <a href="{{ route('driver.history') }}" class="w-full inline-block text-center py-3 bg-purple-600/10 hover:bg-purple-600/20 text-purple-750 dark:text-purple-300 font-extrabold text-xs rounded-2xl transition cursor-pointer">
                            📋 Ver Historial de Viajes
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-layouts.driver>
