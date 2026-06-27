<x-layouts.admin>
    <x-slot:title>Historial de Transacciones - Nikama Admin</x-slot:title>

    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-gradient-to-r from-luffy-red/20 to-rose-500/10 p-8 rounded-3xl border border-luffy-red/30 shadow-sm transition-colors duration-300">
            <h2 class="text-3xl font-extrabold font-['Outfit'] text-slate-800 dark:text-white">Auditoría de Billeteras Virtuales</h2>
            <p class="text-slate-650 dark:text-slate-300 mt-2 font-medium">Inspecciona y rastrea todos los ingresos y débitos de las billeteras virtuales de clientes, comercios y repartidores.</p>
        </div>

        <!-- Filtros y Listado -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/80 shadow-sm overflow-hidden transition-colors duration-300">
            <!-- Pestañas de Holder Type -->
            <div class="flex flex-wrap border-b border-slate-100 dark:border-slate-700/60 bg-slate-50 dark:bg-slate-900/20">
                <a href="{{ route('admin.financial.transactions', ['holder_type' => 'all', 'type' => $type, 'search' => $search]) }}" 
                   class="flex items-center gap-2 px-6 py-4 border-b-2 font-bold text-sm transition-all duration-200 {{ $holderType === 'all' ? 'border-luffy-red text-luffy-red dark:text-rose-455 bg-white dark:bg-slate-800' : 'border-transparent text-slate-500 hover:text-slate-800 dark:hover:text-white hover:bg-slate-100/50 dark:hover:bg-slate-700/20' }}">
                    <span>Todos los Roles</span>
                </a>
                <a href="{{ route('admin.financial.transactions', ['holder_type' => 'customer', 'type' => $type, 'search' => $search]) }}" 
                   class="flex items-center gap-2 px-6 py-4 border-b-2 font-bold text-sm transition-all duration-200 {{ $holderType === 'customer' ? 'border-luffy-red text-luffy-red dark:text-rose-455 bg-white dark:bg-slate-800' : 'border-transparent text-slate-500 hover:text-slate-800 dark:hover:text-white hover:bg-slate-100/50 dark:hover:bg-slate-700/20' }}">
                    <span>Clientes</span>
                </a>
                <a href="{{ route('admin.financial.transactions', ['holder_type' => 'seller', 'type' => $type, 'search' => $search]) }}" 
                   class="flex items-center gap-2 px-6 py-4 border-b-2 font-bold text-sm transition-all duration-200 {{ $holderType === 'seller' ? 'border-luffy-red text-luffy-red dark:text-rose-455 bg-white dark:bg-slate-800' : 'border-transparent text-slate-500 hover:text-slate-800 dark:hover:text-white hover:bg-slate-100/50 dark:hover:bg-slate-700/20' }}">
                    <span>Comercios</span>
                </a>
                <a href="{{ route('admin.financial.transactions', ['holder_type' => 'driver', 'type' => $type, 'search' => $search]) }}" 
                   class="flex items-center gap-2 px-6 py-4 border-b-2 font-bold text-sm transition-all duration-200 {{ $holderType === 'driver' ? 'border-luffy-red text-luffy-red dark:text-rose-455 bg-white dark:bg-slate-800' : 'border-transparent text-slate-500 hover:text-slate-800 dark:hover:text-white hover:bg-slate-100/50 dark:hover:bg-slate-700/20' }}">
                    <span>Repartidores</span>
                </a>
            </div>

            <!-- Filtros por tipo de movimiento (Credito/Debito) y Buscador -->
            <div class="p-6 flex flex-col xl:flex-row gap-4 items-center justify-between border-b border-slate-100 dark:border-slate-700/60">
                <div class="flex flex-wrap gap-2 w-full xl:w-auto">
                    <a href="{{ route('admin.financial.transactions', ['holder_type' => $holderType, 'type' => 'all', 'search' => $search]) }}" 
                       class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider transition-all duration-200 {{ $type === 'all' ? 'bg-slate-800 dark:bg-slate-900 text-white' : 'bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-755 text-slate-600 dark:text-slate-300' }}">
                        Todos
                    </a>
                    <a href="{{ route('admin.financial.transactions', ['holder_type' => $holderType, 'type' => 'credit', 'search' => $search]) }}" 
                       class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider transition-all duration-200 {{ $type === 'credit' ? 'bg-emerald-500 text-white shadow-lg shadow-emerald-500/20' : 'bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-755 text-slate-600 dark:text-slate-300' }}">
                        Ingresos (Crédito)
                    </a>
                    <a href="{{ route('admin.financial.transactions', ['holder_type' => $holderType, 'type' => 'debit', 'search' => $search]) }}" 
                       class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider transition-all duration-200 {{ $type === 'debit' ? 'bg-rose-500 text-white shadow-lg shadow-rose-500/20' : 'bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-755 text-slate-600 dark:text-slate-300' }}">
                        Egresos (Débito)
                    </a>
                </div>

                <!-- Buscador -->
                <form action="{{ route('admin.financial.transactions') }}" method="GET" class="w-full xl:w-96 flex gap-2">
                    <input type="hidden" name="holder_type" value="{{ $holderType }}">
                    <input type="hidden" name="type" value="{{ $type }}">
                    <div class="relative w-full">
                        <input type="text" name="search" value="{{ $search }}" placeholder="Buscar por ID de titular, concepto..." 
                               class="w-full pl-4 pr-10 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                        <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </span>
                    </div>
                    <button type="submit" class="px-5 py-2.5 bg-slate-850 hover:bg-slate-900 text-white font-extrabold text-xs uppercase tracking-wider rounded-2xl transition cursor-pointer shrink-0">
                        Buscar
                    </button>
                </form>
            </div>

            <!-- Tabla -->
            <div class="overflow-x-auto">
                @if($transactionsList->isEmpty())
                    <div class="p-16 text-center text-slate-500 dark:text-slate-400">
                        <svg class="w-16 h-16 mx-auto mb-4 text-slate-300 dark:text-slate-600 animate-pulse" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                        <p class="text-base font-black font-['Outfit'] text-slate-700 dark:text-slate-300">No se encontraron movimientos financieros</p>
                    </div>
                @else
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50 dark:bg-slate-900/40 border-b border-slate-100 dark:border-slate-700/60 text-slate-400 dark:text-slate-455 text-[10px] font-black uppercase tracking-wider">
                                <th class="px-6 py-4.5">Fecha y Hora</th>
                                <th class="px-6 py-4.5">Transacción ID</th>
                                <th class="px-6 py-4.5">Titular (Holder)</th>
                                <th class="px-6 py-4.5">Concepto / Categoría</th>
                                <th class="px-6 py-4.5">Monto</th>
                                <th class="px-6 py-4.5">Referencia ID</th>
                                <th class="px-6 py-4.5">Descripción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700 text-sm">
                            @foreach($transactionsList as $transaction)
                                <tr class="hover:bg-slate-50/40 dark:hover:bg-slate-700/20 transition-colors">
                                    <td class="px-6 py-4.5 text-xs text-slate-500">
                                        {{ $transaction->created_at->format('d/m/Y H:i:s') }}
                                    </td>
                                    <td class="px-6 py-4.5 font-mono text-xs text-slate-500">
                                        {{ $transaction->id }}
                                    </td>
                                    <td class="px-6 py-4.5">
                                        @php
                                            $holderLabel = match($transaction->holder_type) {
                                                \App\Models\CustomerProfile::class => 'Cliente',
                                                \App\Models\Business::class => 'Comercio',
                                                \App\Models\DriverProfile::class => 'Repartidor',
                                                default => 'Desconocido',
                                            };
                                        @endphp
                                        <span class="font-bold text-slate-800 dark:text-white block">{{ $holderLabel }}</span>
                                        <span class="font-mono text-xs text-slate-400 block">{{ $transaction->holder_id }}</span>
                                    </td>
                                    <td class="px-6 py-4.5 font-semibold text-slate-600 dark:text-slate-400 capitalize">
                                        {{ $transaction->transaction_type }}
                                    </td>
                                    <td class="px-6 py-4.5 font-black text-base {{ $transaction->type === 'credit' ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-455' }}">
                                        {{ $transaction->type === 'credit' ? '+' : '-' }} S/ {{ number_format($transaction->amount, 2) }}
                                    </td>
                                    <td class="px-6 py-4.5 font-mono text-xs text-slate-500">
                                        {{ $transaction->reference_id ?? 'Sin Referencia' }}
                                    </td>
                                    <td class="px-6 py-4.5 text-slate-600 dark:text-slate-350 text-xs">
                                        {{ $transaction->description }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

            <!-- Paginación -->
            @if($transactionsList->hasPages())
                <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-700 bg-slate-50/60 dark:bg-slate-900/20 flex items-center justify-between">
                    <div class="text-xs text-slate-450 dark:text-slate-400 font-medium">
                        Mostrando {{ $transactionsList->firstItem() }} al {{ $transactionsList->lastItem() }} de {{ $transactionsList->total() }} registros.
                    </div>
                    <div>
                        {{ $transactionsList->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-layouts.admin>
