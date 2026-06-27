<x-layouts.admin>
    <x-slot:title>Bitácora de Auditoría - Nikama Admin</x-slot:title>

    <div class="space-y-6" x-data="{ showDiffModal: false, diffData: { old: null, new: null, action: '' } }">
        <!-- Header -->
        <div class="bg-gradient-to-r from-luffy-red/20 to-rose-500/10 p-8 rounded-3xl border border-luffy-red/30 shadow-sm transition-colors duration-300">
            <h2 class="text-3xl font-extrabold font-['Outfit'] text-slate-800 dark:text-white">Bitácora de Auditoría del Sistema</h2>
            <p class="text-slate-650 dark:text-slate-300 mt-2 font-medium">Inspecciona y rastrea de forma cronológica todas las acciones realizadas por los administradores y usuarios del sistema.</p>
        </div>

        <!-- Filtros -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/80 shadow-sm p-6 transition-colors duration-300">
            <form action="{{ route('admin.system.audit-logs') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Buscar -->
                <div class="space-y-1">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-wider block">Buscar por Acción / Usuario</label>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Buscar acción o email..."
                           class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-xs text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                </div>

                <!-- Acción -->
                <div class="space-y-1">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-wider block">Acción</label>
                    <select name="action" class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-xs text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                        <option value="">Todas las Acciones</option>
                        @foreach($actions as $act)
                            <option value="{{ $act }}" {{ $action === $act ? 'selected' : '' }}>{{ $act }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Dirección IP -->
                <div class="space-y-1">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-wider block">Dirección IP</label>
                    <input type="text" name="ip_address" value="{{ $ipAddress }}" placeholder="Filtrar por IP..."
                           class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-xs text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                </div>

                <!-- Botones -->
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 py-2.5 bg-slate-900 dark:bg-slate-700 text-white font-bold text-xs uppercase tracking-wider rounded-2xl hover:bg-slate-800 transition shadow-sm cursor-pointer text-center">
                        Filtrar
                    </button>
                    <a href="{{ route('admin.system.audit-logs') }}" class="py-2.5 px-4 bg-slate-100 hover:bg-slate-200 dark:bg-slate-750 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs uppercase tracking-wider rounded-2xl transition cursor-pointer text-center">
                        Limpiar
                    </a>
                </div>
            </form>
        </div>

        <!-- Tabla de Logs -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/80 shadow-sm overflow-hidden transition-colors duration-300">
            <div class="overflow-x-auto">
                @if($logsList->isEmpty())
                    <div class="p-16 text-center text-slate-500 dark:text-slate-400">
                        <svg class="w-16 h-16 mx-auto mb-4 text-slate-300 dark:text-slate-600 animate-pulse" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        <p class="text-base font-black font-['Outfit'] text-slate-700 dark:text-slate-300">No se encontraron registros de auditoría</p>
                        <p class="text-xs text-slate-400 mt-1 font-medium">Intenta ajustando o limpiando los filtros de búsqueda.</p>
                    </div>
                @else
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50 dark:bg-slate-900/40 border-b border-slate-100 dark:border-slate-700/60 text-slate-400 dark:text-slate-455 text-[10px] font-black uppercase tracking-wider">
                                <th class="px-6 py-4.5">Usuario</th>
                                <th class="px-6 py-4.5">Acción</th>
                                <th class="px-6 py-4.5">Entidad</th>
                                <th class="px-6 py-4.5">IP / Dispositivo</th>
                                <th class="px-6 py-4.5">Fecha</th>
                                <th class="px-6 py-4.5 text-right">Detalle</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700 text-xs">
                            @foreach($logsList as $log)
                                <tr class="hover:bg-slate-50/40 dark:hover:bg-slate-700/20 transition-colors">
                                    <td class="px-6 py-4.5 font-bold text-slate-800 dark:text-white">
                                        @if($log->user)
                                            <div class="flex items-center gap-2">
                                                <div class="w-7 h-7 rounded-full bg-slate-100 dark:bg-slate-900 flex items-center justify-center text-slate-700 dark:text-slate-300 font-bold border border-slate-200 dark:border-slate-700 font-['Outfit'] text-xs">
                                                    {{ strtoupper(substr($log->user->first_name, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <span class="font-bold text-slate-800 dark:text-white text-xs block leading-tight">
                                                        {{ $log->user->first_name }} {{ $log->user->last_name }}
                                                    </span>
                                                    <span class="text-[10px] text-slate-400 font-medium block leading-none mt-0.5">{{ $log->user->email }}</span>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-slate-400 italic">Sistema / Tarea Programada</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4.5">
                                        <span class="px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider bg-slate-100 dark:bg-slate-900 text-slate-700 dark:text-slate-300 border border-slate-200/40 dark:border-slate-700/60">
                                            {{ $log->action }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4.5">
                                        @if($log->entity_type)
                                            <span class="font-bold text-slate-700 dark:text-slate-300 block">
                                                {{ class_basename($log->entity_type) }}
                                            </span>
                                            <span class="text-[10px] font-mono text-slate-400 mt-0.5 block">{{ $log->entity_id }}</span>
                                        @else
                                            <span class="text-slate-400 font-medium">N/A</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4.5 font-medium text-slate-650 dark:text-slate-400">
                                        <span class="font-mono block text-xs">{{ $log->ip_address }}</span>
                                        <span class="text-[10px] text-slate-400 block max-w-xs truncate leading-normal mt-0.5" title="{{ $log->user_agent }}">
                                            {{ $log->user_agent }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4.5 font-medium text-slate-550 dark:text-slate-400">
                                        {{ $log->created_at->format('d/m/Y H:i:s') }}
                                        <span class="text-[10px] text-slate-400 block mt-0.5">{{ $log->created_at->diffForHumans() }}</span>
                                    </td>
                                    <td class="px-6 py-4.5 text-right">
                                        @if($log->old_values || $log->new_values)
                                            <button @click="diffData = { old: {{ json_encode($log->old_values) }}, new: {{ json_encode($log->new_values) }}, action: '{{ $log->action }}' }; showDiffModal = true"
                                                    class="px-2.5 py-1.5 bg-slate-50 hover:bg-slate-100 dark:bg-slate-900 dark:hover:bg-slate-950 border border-slate-200 dark:border-slate-850 text-[10px] font-extrabold uppercase tracking-wider text-slate-650 dark:text-slate-350 rounded-xl transition cursor-pointer">
                                                Ver Cambios
                                            </button>
                                        @else
                                            <span class="text-[10px] text-slate-400 font-medium italic">Sin cambios</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

            <!-- Paginación -->
            @if($logsList->hasPages())
                <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-700 bg-slate-50/60 dark:bg-slate-900/20 flex items-center justify-between">
                    <div class="text-xs text-slate-450 dark:text-slate-400 font-medium">
                        Mostrando {{ $logsList->firstItem() }} al {{ $logsList->lastItem() }} de {{ $logsList->total() }} registros.
                    </div>
                    <div>
                        {{ $logsList->links() }}
                    </div>
                </div>
            @endif
        </div>

        <!-- Modal de Detalle de Cambios (JSON Diff Viewer) -->
        <div x-show="showDiffModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-transition>
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="showDiffModal = false">
                    <div class="absolute inset-0 bg-slate-950/70 backdrop-blur-sm"></div>
                </div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-middle bg-white dark:bg-slate-800 rounded-3xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full border border-slate-200 dark:border-slate-700">
                    <div class="p-6 space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-700 pb-3">
                            <div>
                                <h3 class="text-lg font-black font-['Outfit'] text-slate-850 dark:text-white">Historial de Valores Modificados</h3>
                                <p class="text-xs text-slate-450 dark:text-slate-400 mt-1">Comparación de valores anteriores frente a los nuevos registrados en la acción: <span class="font-bold text-slate-750 dark:text-slate-350" x-text="diffData.action"></span>.</p>
                            </div>
                            <button @click="showDiffModal = false" class="text-slate-450 dark:text-slate-400 hover:text-slate-750 dark:hover:text-white transition">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Valores Anteriores -->
                            <div class="space-y-1">
                                <label class="text-xs font-black text-rose-500 uppercase tracking-wider block">Valores Anteriores</label>
                                <div class="bg-rose-50/40 dark:bg-rose-955/10 border border-rose-100 dark:border-rose-950/40 p-4 rounded-2xl overflow-auto max-h-96 font-mono text-[11px] text-rose-700 dark:text-rose-400">
                                    <template x-if="diffData.old">
                                        <pre x-text="JSON.stringify(diffData.old, null, 2)"></pre>
                                    </template>
                                    <template x-if="!diffData.old">
                                        <span class="italic font-sans">Ninguno (Registro Nuevo / Creación)</span>
                                    </template>
                                </div>
                            </div>

                            <!-- Valores Nuevos -->
                            <div class="space-y-1">
                                <label class="text-xs font-black text-emerald-500 uppercase tracking-wider block">Valores Nuevos</label>
                                <div class="bg-emerald-50/40 dark:bg-emerald-955/10 border border-emerald-100 dark:border-emerald-950/40 p-4 rounded-2xl overflow-auto max-h-96 font-mono text-[11px] text-emerald-700 dark:text-emerald-400">
                                    <template x-if="diffData.new">
                                        <pre x-text="JSON.stringify(diffData.new, null, 2)"></pre>
                                    </template>
                                    <template x-if="!diffData.new">
                                        <span class="italic font-sans">Ninguno (Eliminación)</span>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end pt-2">
                            <button type="button" @click="showDiffModal = false" class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs uppercase tracking-wider rounded-xl transition cursor-pointer">
                                Cerrar Ventana
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
