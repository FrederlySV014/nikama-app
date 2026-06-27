<x-layouts.admin>
    <x-slot:title>Comisiones de Comercio - Nikama Admin</x-slot:title>

    <div class="space-y-6" x-data="{ showForm: false }">
        <!-- Header -->
        <div class="bg-gradient-to-r from-luffy-red/20 to-rose-500/10 p-8 rounded-3xl border border-luffy-red/30 shadow-sm transition-colors duration-300 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-3xl font-extrabold font-['Outfit'] text-slate-800 dark:text-white">Comisiones de Comercios</h2>
                <p class="text-slate-650 dark:text-slate-300 mt-2 font-medium">Configura los montos fijos o porcentuales que se deducen de cada pedido para financiar los servicios de la plataforma.</p>
            </div>
            <button @click="showForm = !showForm" class="px-5 py-3 bg-luffy-red hover:bg-rose-600 text-white font-extrabold text-xs uppercase tracking-wider rounded-2xl transition shadow-lg shadow-luffy-red/20 cursor-pointer shrink-0">
                Nueva Regla de Comisión
            </button>
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

        @if ($errors->any())
            <div class="p-4 bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-800 text-rose-850 dark:text-rose-350 rounded-3xl flex items-start gap-3 shadow-sm">
                <svg class="w-5 h-5 text-rose-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <div>
                    <p class="font-bold text-sm">Errores de Validación</p>
                    <ul class="text-xs list-disc list-inside mt-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <!-- Formulario de Creación -->
        <div x-show="showForm" x-collapse style="display: none;">
            <div class="bg-white dark:bg-slate-800 p-8 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm transition-colors duration-300">
                <h3 class="text-lg font-black font-['Outfit'] text-slate-800 dark:text-white border-b border-slate-100 dark:border-slate-700 pb-3 mb-6">Configurar Nueva Regla</h3>
                
                <form action="{{ route('admin.financial.commissions.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @csrf
                    <!-- Selector de Negocio -->
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Comercio / Negocio</label>
                        <select name="business_id" required class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                            <option value="">Selecciona un negocio...</option>
                            @foreach($businesses as $business)
                                <option value="{{ $business->id }}">{{ $business->business_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Tipo de Comisión -->
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Tipo de Comisión</label>
                        <select name="commission_type" required class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-955 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                            <option value="percentage">Porcentaje (%)</option>
                            <option value="fixed">Monto Fijo (S/)</option>
                        </select>
                    </div>

                    <!-- Valor -->
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Valor de Comisión</label>
                        <input type="number" step="0.01" min="0" name="commission_value" required placeholder="Ej. 10.00 o 2.50"
                               class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                    </div>

                    <!-- Fecha de Inicio -->
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Fecha de Inicio de Vigencia</label>
                        <input type="datetime-local" name="starts_at" required value="{{ now()->format('Y-m-d\TH:i') }}"
                               class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                    </div>

                    <!-- Fecha de Fin -->
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Fecha de Fin (Opcional)</label>
                        <input type="datetime-local" name="ends_at"
                               class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                    </div>

                    <!-- Botones de Acción -->
                    <div class="flex items-end justify-end gap-3 md:col-span-3 pt-2">
                        <button type="button" @click="showForm = false" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-650 text-slate-700 dark:text-slate-300 font-bold text-xs uppercase tracking-wider rounded-xl transition cursor-pointer">
                            Cancelar
                        </button>
                        <button type="submit" class="px-5 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white font-extrabold text-xs uppercase tracking-wider rounded-xl transition shadow-md shadow-emerald-500/10 cursor-pointer">
                            Guardar y Activar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Filtros y Directorio -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/80 shadow-sm overflow-hidden transition-colors duration-300">
            <div class="p-6 flex flex-col md:flex-row gap-4 items-center justify-between border-b border-slate-100 dark:border-slate-700/60">
                <h3 class="font-extrabold text-slate-800 dark:text-white font-['Outfit']">Reglas de Comisión Activas</h3>

                <!-- Buscador -->
                <form action="{{ route('admin.financial.commissions') }}" method="GET" class="w-full md:w-80 flex gap-2">
                    <div class="relative w-full">
                        <input type="text" name="search" value="{{ $search }}" placeholder="Buscar por comercio..." 
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
                @if($commissionsList->isEmpty())
                    <div class="p-16 text-center text-slate-500 dark:text-slate-400">
                        <svg class="w-16 h-16 mx-auto mb-4 text-slate-300 dark:text-slate-600 animate-pulse" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 14.25l6-6m4.5-3.75L3 21m12 0h6V15"></path></svg>
                        <p class="text-base font-black font-['Outfit'] text-slate-700 dark:text-slate-300">No se encontraron reglas de comisión</p>
                    </div>
                @else
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50 dark:bg-slate-900/40 border-b border-slate-100 dark:border-slate-700/60 text-slate-400 dark:text-slate-455 text-[10px] font-black uppercase tracking-wider">
                                <th class="px-6 py-4.5">Negocio / Comercio</th>
                                <th class="px-6 py-4.5">Tipo de Comisión</th>
                                <th class="px-6 py-4.5">Valor</th>
                                <th class="px-6 py-4.5">Vigencia</th>
                                <th class="px-6 py-4.5 text-center">Estado</th>
                                <th class="px-6 py-4.5 text-right">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700 text-sm">
                            @foreach($commissionsList as $commission)
                                <tr class="hover:bg-slate-50/40 dark:hover:bg-slate-700/20 transition-colors">
                                    <td class="px-6 py-4.5 font-bold text-slate-800 dark:text-white">
                                        {{ $commission->business?->business_name }}
                                        <span class="text-xs block text-slate-400 font-mono">{{ $commission->business_id }}</span>
                                    </td>
                                    <td class="px-6 py-4.5 font-semibold text-slate-700 dark:text-slate-300">
                                        {{ $commission->commission_type === 'percentage' ? 'Porcentual' : 'Monto Fijo' }}
                                    </td>
                                    <td class="px-6 py-4.5 font-extrabold text-slate-800 dark:text-white">
                                        {{ $commission->commission_type === 'percentage' ? $commission->commission_value . '%' : 'S/ ' . number_format($commission->commission_value, 2) }}
                                    </td>
                                    <td class="px-6 py-4.5 text-xs">
                                        <span class="block"><strong>Inicio:</strong> {{ $commission->starts_at->format('d/m/Y H:i') }}</span>
                                        @if($commission->ends_at)
                                            <span class="block"><strong>Fin:</strong> {{ $commission->ends_at->format('d/m/Y H:i') }}</span>
                                        @else
                                            <span class="block text-slate-400">Vigencia indefinida</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4.5 text-center">
                                        @if($commission->is_active)
                                            <span class="px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase bg-emerald-50 dark:bg-emerald-950/30 text-emerald-600 dark:text-emerald-400 border border-emerald-200/40 dark:border-emerald-900/30">Activo</span>
                                        @else
                                            <span class="px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase bg-slate-50 dark:bg-slate-900/30 text-slate-400 dark:text-slate-500 border border-slate-200/40 dark:border-slate-900/20">Inactivo</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4.5 text-right">
                                        <form action="{{ route('admin.financial.commissions.toggle', $commission->id) }}" method="POST">
                                            @csrf
                                            @if($commission->is_active)
                                                <button type="submit" class="px-3.5 py-1.5 bg-rose-500 hover:bg-rose-600 text-white font-extrabold text-[10px] uppercase tracking-wider rounded-xl transition cursor-pointer">
                                                    Desactivar
                                                </button>
                                            @else
                                                <button type="submit" class="px-3.5 py-1.5 bg-emerald-500 hover:bg-emerald-600 text-white font-extrabold text-[10px] uppercase tracking-wider rounded-xl transition cursor-pointer">
                                                    Activar
                                                </button>
                                            @endif
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

            <!-- Paginación -->
            @if($commissionsList->hasPages())
                <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-700 bg-slate-50/60 dark:bg-slate-900/20 flex items-center justify-between">
                    <div class="text-xs text-slate-450 dark:text-slate-400 font-medium">
                        Mostrando {{ $commissionsList->firstItem() }} al {{ $commissionsList->lastItem() }} de {{ $commissionsList->total() }} registros.
                    </div>
                    <div>
                        {{ $commissionsList->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-layouts.admin>
