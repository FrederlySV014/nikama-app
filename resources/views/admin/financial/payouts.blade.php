<x-layouts.admin>
    <x-slot:title>Liquidaciones y Retiros - Nikama Admin</x-slot:title>

    <div class="space-y-6" x-data="{ showModal: false, payoutId: '', payoutType: '', actionUrl: '', payoutAmount: '' }">
        <!-- Header -->
        <div class="bg-gradient-to-r from-luffy-red/20 to-rose-500/10 p-8 rounded-3xl border border-luffy-red/30 shadow-sm transition-colors duration-300">
            <h2 class="text-3xl font-extrabold font-['Outfit'] text-slate-800 dark:text-white">Liquidaciones e Historial de Retiros</h2>
            <p class="text-slate-650 dark:text-slate-300 mt-2 font-medium">Procesa y valida las solicitudes de transferencia bancaria de los comercios y repartidores de la plataforma.</p>
        </div>

        <!-- Alertas -->
        @if (session('success'))
            <div class="p-4 bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 rounded-3xl flex items-start gap-3 shadow-sm">
                <svg class="w-5 h-5 text-emerald-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <p class="font-bold text-sm">Operación exitosa</p>
                    <p class="text-xs opacity-90 mt-0.5">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if (session('warning'))
            <div class="p-4 bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800 text-amber-800 dark:text-amber-300 rounded-3xl flex items-start gap-3 shadow-sm">
                <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <div>
                    <p class="font-bold text-sm">Alerta financiera</p>
                    <p class="text-xs opacity-90 mt-0.5">{{ session('warning') }}</p>
                </div>
            </div>
        @endif

        <!-- Tarjetas de Métricas -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
            <div class="bg-white dark:bg-slate-800 p-6 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm flex items-center justify-between transition hover:-translate-y-0.5 duration-300">
                <div>
                    <span class="text-slate-400 text-xs font-bold uppercase tracking-wider block">Total Pagado</span>
                    <span class="text-2xl font-extrabold font-['Outfit'] text-slate-800 dark:text-white block mt-1">S/ {{ number_format($totalProcessedAmount, 2) }}</span>
                </div>
                <div class="p-3 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-500 dark:text-emerald-400 rounded-2xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-800 p-6 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm flex items-center justify-between transition hover:-translate-y-0.5 duration-300">
                <div>
                    <span class="text-slate-400 text-xs font-bold uppercase tracking-wider block">Pendientes de Pago</span>
                    <span class="text-2xl font-extrabold font-['Outfit'] text-slate-800 dark:text-white block mt-1">S/ {{ number_format($totalPendingAmount, 2) }}</span>
                </div>
                <div class="p-3 bg-amber-50 dark:bg-amber-950/40 text-amber-500 dark:text-amber-450 rounded-2xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-800 p-6 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm flex items-center justify-between transition hover:-translate-y-0.5 duration-300">
                <div>
                    <span class="text-slate-400 text-xs font-bold uppercase tracking-wider block">Retiros Pend. Negocios</span>
                    <span class="text-2xl font-extrabold font-['Outfit'] text-slate-800 dark:text-white block mt-1">{{ $pendingBusinessesCount }}</span>
                </div>
                <div class="p-3 bg-sky-50 dark:bg-sky-950/40 text-sky-500 dark:text-sky-450 rounded-2xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-800 p-6 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm flex items-center justify-between transition hover:-translate-y-0.5 duration-300">
                <div>
                    <span class="text-slate-400 text-xs font-bold uppercase tracking-wider block">Retiros Pend. Repartidor</span>
                    <span class="text-2xl font-extrabold font-['Outfit'] text-slate-800 dark:text-white block mt-1">{{ $pendingDriversCount }}</span>
                </div>
                <div class="p-3 bg-violet-50 dark:bg-violet-950/40 text-violet-500 dark:text-violet-400 rounded-2xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </div>
            </div>
        </div>

        <!-- Filtros y Tabla -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/80 shadow-sm overflow-hidden transition-colors duration-300">
            <!-- Tabs por Tipo -->
            <div class="flex border-b border-slate-100 dark:border-slate-700/60 bg-slate-50 dark:bg-slate-900/20">
                <a href="{{ route('admin.financial.payouts', ['type' => 'businesses', 'status' => $status]) }}" 
                   class="flex items-center gap-2 px-6 py-4 border-b-2 font-bold text-sm transition-all duration-200 {{ $type === 'businesses' ? 'border-luffy-red text-luffy-red dark:text-rose-455 bg-white dark:bg-slate-800' : 'border-transparent text-slate-500 hover:text-slate-800 dark:hover:text-white hover:bg-slate-100/50 dark:hover:bg-slate-700/20' }}">
                    <span>Comercios / Negocios</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 border border-amber-250 dark:border-amber-900/30">{{ $pendingBusinessesCount }}</span>
                </a>
                <a href="{{ route('admin.financial.payouts', ['type' => 'drivers', 'status' => $status]) }}" 
                   class="flex items-center gap-2 px-6 py-4 border-b-2 font-bold text-sm transition-all duration-200 {{ $type === 'drivers' ? 'border-luffy-red text-luffy-red dark:text-rose-455 bg-white dark:bg-slate-800' : 'border-transparent text-slate-500 hover:text-slate-800 dark:hover:text-white hover:bg-slate-100/50 dark:hover:bg-slate-700/20' }}">
                    <span>Repartidores</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-violet-100 dark:bg-violet-950/40 text-violet-600 dark:text-violet-400 border border-violet-250 dark:border-violet-900/30">{{ $pendingDriversCount }}</span>
                </a>
            </div>

            <!-- Filtros por Estado -->
            <div class="p-6 flex flex-wrap gap-2 border-b border-slate-100 dark:border-slate-700/60">
                <a href="{{ route('admin.financial.payouts', ['type' => $type, 'status' => 'all']) }}" 
                   class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider transition-all duration-200 {{ $status === 'all' ? 'bg-slate-800 dark:bg-slate-900 text-white' : 'bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-750 text-slate-600 dark:text-slate-300' }}">
                    Todos
                </a>
                <a href="{{ route('admin.financial.payouts', ['type' => $type, 'status' => 'pending']) }}" 
                   class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider transition-all duration-200 {{ $status === 'pending' ? 'bg-amber-500 text-white' : 'bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-750 text-slate-600 dark:text-slate-300' }}">
                    Pendientes
                </a>
                <a href="{{ route('admin.financial.payouts', ['type' => $type, 'status' => 'processed']) }}" 
                   class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider transition-all duration-200 {{ $status === 'processed' ? 'bg-emerald-500 text-white' : 'bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-750 text-slate-600 dark:text-slate-300' }}">
                    Procesados
                </a>
                <a href="{{ route('admin.financial.payouts', ['type' => $type, 'status' => 'failed']) }}" 
                   class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider transition-all duration-200 {{ $status === 'failed' ? 'bg-rose-500 text-white' : 'bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-750 text-slate-600 dark:text-slate-300' }}">
                    Fallidos
                </a>
            </div>

            <!-- Tabla -->
            <div class="overflow-x-auto">
                @if($payoutsList->isEmpty())
                    <div class="p-16 text-center text-slate-500 dark:text-slate-400">
                        <svg class="w-16 h-16 mx-auto mb-4 text-slate-300 dark:text-slate-600 animate-pulse" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <p class="text-base font-black font-['Outfit'] text-slate-700 dark:text-slate-300">No hay liquidaciones en esta lista</p>
                    </div>
                @else
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50 dark:bg-slate-900/40 border-b border-slate-100 dark:border-slate-700/60 text-slate-400 dark:text-slate-455 text-[10px] font-black uppercase tracking-wider">
                                <th class="px-6 py-4.5">Beneficiario</th>
                                <th class="px-6 py-4.5">Monto Solicitado</th>
                                @if($type === 'businesses')
                                    <th class="px-6 py-4.5">Comisión Cobrada</th>
                                @endif
                                <th class="px-6 py-4.5">Monto Neto a Transferir</th>
                                <th class="px-6 py-4.5">Referencia</th>
                                <th class="px-6 py-4.5 text-center">Estado</th>
                                <th class="px-6 py-4.5 text-right">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700 text-sm">
                            @foreach($payoutsList as $payout)
                                <tr class="hover:bg-slate-50/40 dark:hover:bg-slate-700/20 transition-colors">
                                    <td class="px-6 py-4.5 font-bold text-slate-800 dark:text-white">
                                        @if($type === 'drivers')
                                            {{ $payout->driver?->user?->first_name }} {{ $payout->driver?->user?->last_name }}
                                            <span class="text-xs block text-slate-400 font-mono">{{ $payout->driver_profile_id }}</span>
                                        @else
                                            {{ $payout->business?->business_name }}
                                            <span class="text-xs block text-slate-400 font-mono">{{ $payout->business_id }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4.5 font-semibold text-slate-700 dark:text-slate-300">
                                        S/ {{ number_format($payout->amount, 2) }}
                                    </td>
                                    @if($type === 'businesses')
                                        <td class="px-6 py-4.5 text-slate-500">
                                            S/ {{ number_format($payout->commission_deducted, 2) }}
                                        </td>
                                    @endif
                                    <td class="px-6 py-4.5 font-black text-emerald-600 dark:text-emerald-400">
                                        S/ {{ number_format($type === 'drivers' ? $payout->amount : $payout->net_amount, 2) }}
                                    </td>
                                    <td class="px-6 py-4.5 font-mono text-xs">
                                        {{ $payout->transaction_reference ?? 'N/A' }}
                                        @if($payout->processed_at)
                                            <span class="text-[10px] text-slate-400 block">{{ $payout->processed_at->format('d/m/Y H:i') }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4.5 text-center">
                                        @php
                                            $badgeClasses = match($payout->status) {
                                                'pending' => 'bg-amber-50 dark:bg-amber-950/30 text-amber-600 dark:text-amber-400 border border-amber-200/40 dark:border-amber-900/30',
                                                'processed' => 'bg-emerald-50 dark:bg-emerald-950/30 text-emerald-600 dark:text-emerald-400 border border-emerald-200/40 dark:border-emerald-900/30',
                                                'failed' => 'bg-rose-50 dark:bg-rose-950/30 text-rose-600 dark:text-rose-455 border border-rose-200/40 dark:border-rose-900/30',
                                                default => 'bg-slate-50 dark:bg-slate-900/30 text-slate-600',
                                            };
                                            $badgeLabel = match($payout->status) {
                                                'pending' => 'Pendiente',
                                                'processed' => 'Procesado',
                                                'failed' => 'Fallido',
                                                default => $payout->status,
                                            };
                                        @endphp
                                        <span class="px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider {{ $badgeClasses }}">
                                            {{ $badgeLabel }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4.5 text-right">
                                        @if($payout->status === 'pending')
                                            <div class="flex items-center justify-end gap-2">
                                                <button @click="payoutId = '{{ $payout->id }}'; payoutType = '{{ $type === 'drivers' ? 'driver' : 'business' }}'; payoutAmount = '{{ number_format($type === 'drivers' ? $payout->amount : $payout->net_amount, 2) }}'; actionUrl = '{{ route('admin.financial.payouts.process', [$type === 'drivers' ? 'driver' : 'business', $payout->id]) }}'; showModal = true"
                                                        class="px-3 py-1.5 bg-emerald-500 hover:bg-emerald-600 text-white font-extrabold text-[10px] uppercase tracking-wider rounded-xl transition shadow-md cursor-pointer shrink-0">
                                                    Pagar
                                                </button>
                                                <form action="{{ route('admin.financial.payouts.process', [$type === 'drivers' ? 'driver' : 'business', $payout->id]) }}" method="POST" class="inline" onsubmit="return confirm('¿Estás seguro de rechazar esta liquidación?')">
                                                    @csrf
                                                    <input type="hidden" name="status" value="failed">
                                                    <button type="submit" class="px-3 py-1.5 bg-rose-500 hover:bg-rose-600 text-white font-extrabold text-[10px] uppercase tracking-wider rounded-xl transition shadow-md cursor-pointer shrink-0">
                                                        Rechazar
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            <span class="text-xs text-slate-400 font-semibold italic">Cerrado</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

            <!-- Paginación -->
            @if($payoutsList->hasPages())
                <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-700 bg-slate-50/60 dark:bg-slate-900/20 flex items-center justify-between">
                    <div class="text-xs text-slate-450 dark:text-slate-400 font-medium">
                        Mostrando {{ $payoutsList->firstItem() }} al {{ $payoutsList->lastItem() }} de {{ $payoutsList->total() }} registros.
                    </div>
                    <div>
                        {{ $payoutsList->links() }}
                    </div>
                </div>
            @endif
        </div>

        <!-- Modal de Confirmación de Pago -->
        <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-transition>
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="showModal = false">
                    <div class="absolute inset-0 bg-slate-950/70 backdrop-blur-sm"></div>
                </div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-3xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-200 dark:border-slate-700">
                    <form :action="actionUrl" method="POST" class="p-6 space-y-4">
                        @csrf
                        <input type="hidden" name="status" value="processed">

                        <div>
                            <h3 class="text-lg font-black font-['Outfit'] text-slate-800 dark:text-white">Registrar Liquidación</h3>
                            <p class="text-xs text-slate-450 dark:text-slate-400 mt-1">Ingresa el código de referencia bancaria para procesar el pago por <span class="font-bold text-slate-700 dark:text-slate-300" x-text="'S/ ' + payoutAmount"></span>.</p>
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Referencia de Transacción Bancaria</label>
                            <input type="text" name="transaction_reference" required placeholder="Ej. OPER-902318, YAPE-8219..."
                                   class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Notas / Comentarios (Opcional)</label>
                            <textarea name="notes" placeholder="Detalles de la transferencia..." rows="3"
                                      class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all"></textarea>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-2">
                            <button type="button" @click="showModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-650 text-slate-700 dark:text-slate-300 font-bold text-xs uppercase tracking-wider rounded-xl transition cursor-pointer">
                                Cancelar
                            </button>
                            <button type="submit" class="px-5 py-2 bg-emerald-500 hover:bg-emerald-600 text-white font-extrabold text-xs uppercase tracking-wider rounded-xl transition shadow-md shadow-emerald-500/10 cursor-pointer">
                                Confirmar Pago
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
