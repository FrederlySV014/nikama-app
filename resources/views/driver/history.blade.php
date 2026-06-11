<x-layouts.driver>
    <x-slot:title>Historial de Repartos - Nikama</x-slot:title>

    <div class="space-y-8">
        <!-- Cabecera de Historial -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-200 dark:border-slate-800">
            <div>
                <span class="text-[10px] text-purple-600 dark:text-purple-400 uppercase font-black tracking-wider block">Registros Operacionales</span>
                <h1 class="text-3xl font-extrabold font-['Outfit'] text-slate-850 dark:text-white">Historial de Repartos</h1>
                <p class="text-slate-500 dark:text-slate-400 text-xs mt-1">Consulta tus entregas completadas y los incidentes reportados en ruta.</p>
            </div>
            <a href="{{ route('driver.dashboard') }}" 
               class="px-5 py-3 border border-slate-200 hover:bg-slate-50 dark:border-slate-700 dark:hover:bg-slate-750 text-slate-700 dark:text-slate-350 font-bold text-xs rounded-2xl transition cursor-pointer flex items-center gap-1.5 self-start sm:self-center">
                ➜ Volver a Mi Panel
            </a>
        </div>

        @php
            $successCount = $deliveries->where('status', \App\Models\Delivery::STATUS_DELIVERED)->count();
            $failedCount = $deliveries->where('status', \App\Models\Delivery::STATUS_FAILED)->count();
        @endphp

        <!-- Resumen de Métricas -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            <div class="bg-white dark:bg-slate-800 p-6 rounded-3xl border border-slate-150 dark:border-slate-700/80 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-purple-50 dark:bg-purple-950/20 text-purple-600 dark:text-purple-400 flex items-center justify-center text-xl font-bold">
                    📦
                </div>
                <div>
                    <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider block">Total Servicios</span>
                    <span class="text-2xl font-black font-['Outfit'] text-slate-850 dark:text-white block mt-0.5">{{ $deliveries->total() }}</span>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-800 p-6 rounded-3xl border border-slate-150 dark:border-slate-700/80 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xl font-bold">
                    ✓
                </div>
                <div>
                    <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider block">Entregados</span>
                    <span class="text-2xl font-black font-['Outfit'] text-emerald-600 dark:text-emerald-400 block mt-0.5">{{ auth()->user()->driverProfile->total_deliveries }}</span>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-800 p-6 rounded-3xl border border-slate-150 dark:border-slate-700/80 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-rose-50 dark:bg-rose-950/20 text-rose-600 dark:text-rose-400 flex items-center justify-center text-xl font-bold">
                    ✕
                </div>
                <div>
                    <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider block">Rechazados por Cliente</span>
                    <span class="text-2xl font-black font-['Outfit'] text-rose-600 dark:text-rose-400 block mt-0.5">
                        {{ \App\Models\Delivery::where('driver_profile_id', auth()->user()->driverProfile->id)->where('status', \App\Models\Delivery::STATUS_FAILED)->count() }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Tabla/Lista de Historial -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-150 dark:border-slate-700/80 shadow-sm overflow-hidden">
            @if ($deliveries->isEmpty())
                <div class="p-12 text-center space-y-4">
                    <div class="w-16 h-16 rounded-full bg-slate-50 dark:bg-slate-900 flex items-center justify-center text-slate-350 dark:text-slate-650 border border-slate-100 dark:border-slate-800 mx-auto text-xl">
                        📋
                    </div>
                    <h3 class="font-bold text-slate-800 dark:text-white text-base">No hay historial de repartos</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm mx-auto">Tus servicios completados o reportados aparecerán listados aquí.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-900/60 text-slate-400 dark:text-slate-400 text-[10px] font-black uppercase tracking-wider border-b border-slate-150 dark:border-slate-700/80">
                                <th class="py-4 px-6">N° Pedido</th>
                                <th class="py-4 px-6">Establecimiento</th>
                                <th class="py-4 px-6">Cliente & Destino</th>
                                <th class="py-4 px-6">Fecha & Hora</th>
                                <th class="py-4 px-6">Cobro / Envío</th>
                                <th class="py-4 px-6">Estado Final</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-750 text-xs">
                            @foreach ($deliveries as $del)
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-750/10 transition">
                                    <!-- N° Pedido -->
                                    <td class="py-4 px-6">
                                        <span class="font-extrabold font-mono text-slate-900 dark:text-white block">
                                            {{ $del->order->order_number }}
                                        </span>
                                        <span class="text-[9px] text-slate-400 uppercase font-bold">
                                            Pago: {{ ($del->order->payments->first()?->payment_method ?? 'cash') === 'cash' ? 'Efectivo' : 'Digital' }}
                                        </span>
                                    </td>

                                    <!-- Establecimiento -->
                                    <td class="py-4 px-6">
                                        <div class="font-bold text-slate-800 dark:text-white">
                                            🏪 {{ $del->business->business_name }}
                                        </div>
                                    </td>

                                    <!-- Cliente & Destino -->
                                    <td class="py-4 px-6 max-w-xs">
                                        <div class="font-bold text-slate-800 dark:text-white">
                                            👤 {{ $del->order->user->first_name }} {{ $del->order->user->last_name }}
                                        </div>
                                        <div class="text-[10px] text-slate-500 truncate" title="{{ $del->order->delivery_address }}">
                                            📍 {{ $del->order->delivery_address }}
                                        </div>
                                    </td>

                                    <!-- Fecha & Hora -->
                                    <td class="py-4 px-6 text-slate-500 dark:text-slate-400">
                                        {{ $del->created_at->timezone('America/Lima')->format('d/m/Y h:i A') }}
                                    </td>

                                    <!-- Cobro / Envío -->
                                    <td class="py-4 px-6">
                                        <div class="font-extrabold text-slate-800 dark:text-white">
                                            Cobrar: 
                                            <span class="text-emerald-600 dark:text-emerald-400">
                                                @if(($del->order->payments->first()?->payment_method ?? 'cash') === 'cash')
                                                    S/ {{ number_format($del->order->total, 2) }}
                                                @else
                                                    S/ 0.00
                                                @endif
                                            </span>
                                        </div>
                                        <div class="text-[9px] text-slate-400">Tarifa Envío: S/ {{ number_format($del->delivery_fee ?? 5.00, 2) }}</div>
                                    </td>

                                    <!-- Estado Final -->
                                    <td class="py-4 px-6">
                                        @if ($del->status === 'delivered')
                                            <span class="inline-block px-2.5 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-900/30">
                                                🎁 Entregado
                                            </span>
                                        @elseif ($del->status === 'failed')
                                            <div class="space-y-1">
                                                <span class="inline-block px-2.5 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 border border-rose-100 dark:border-rose-900/30">
                                                    ✕ Cliente Rechazó
                                                </span>
                                                @if ($del->order->cancellation)
                                                    <p class="text-[10px] text-slate-450 dark:text-slate-400 italic font-medium max-w-[150px] truncate" title="{{ $del->order->cancellation->comment }}">
                                                        "{{ $del->order->cancellation->comment }}"
                                                    </p>
                                                @endif
                                            </div>
                                        @else
                                            <span class="inline-block px-2.5 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300">
                                                {{ ucfirst($del->status) }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-6 border-t border-slate-150 dark:border-slate-750">
                    {{ $deliveries->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.driver>
