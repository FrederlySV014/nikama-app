<x-layouts.seller>
    <x-slot:title>Gestión de Combos - Nikama</x-slot:title>

    <div class="space-y-6">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold font-['Outfit'] text-slate-800 dark:text-white">Combos Promocionales</h1>
                <p class="text-slate-500 dark:text-slate-400 mt-1">Arma paquetes especiales de tus productos con precios de descuento.</p>
            </div>
            <div>
                <a href="{{ route('seller.combos.create') }}" class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl bg-luffy-straw text-slate-900 font-bold hover:bg-luffy-straw-hover shadow-sm transition-all">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                    Nuevo Combo
                </a>
            </div>
        </div>

        <!-- Alert Messages -->
        @if (session('success'))
            <div class="p-4 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 rounded-xl flex items-center gap-3">
                <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                <span class="font-medium text-sm">{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="p-4 bg-rose-50 dark:bg-rose-900/30 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 rounded-xl flex items-center gap-3">
                <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                <span class="font-medium text-sm">{{ session('error') }}</span>
            </div>
        @endif

        <!-- Filters & Search Card -->
        <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-100 dark:border-slate-700/60 shadow-sm">
            <form method="GET" action="{{ route('seller.combos.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search -->
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </span>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Buscar por nombre..." class="w-full pl-10 pr-4 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-750 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-colors">
                </div>

                <!-- Business Filter -->
                <div>
                    <select name="business" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-750 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-colors">
                        <option value="all" {{ $businessFilter === 'all' ? 'selected' : '' }}>Todos los negocios</option>
                        @foreach ($businesses as $b)
                            <option value="{{ $b->id }}" {{ $businessFilter === $b->id ? 'selected' : '' }}>{{ $b->business_name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <select name="status" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-750 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-colors">
                        <option value="all" {{ $status === 'all' ? 'selected' : '' }}>Todos los estados</option>
                        <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Activo</option>
                        <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Inactivo</option>
                    </select>
                </div>

                <!-- Filter Buttons -->
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 py-2 bg-slate-800 hover:bg-slate-900 dark:bg-slate-700 dark:hover:bg-slate-600 text-white font-semibold rounded-xl text-sm transition-colors shadow-sm">
                        Filtrar
                    </button>
                    @if ($search || $status !== 'all' || $businessFilter !== 'all')
                        <a href="{{ route('seller.combos.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-900 dark:hover:bg-slate-850 text-slate-600 dark:text-slate-300 font-semibold rounded-xl text-sm transition-colors flex items-center justify-center border border-slate-200/60 dark:border-slate-750">
                            Limpiar
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Table & Grid Card -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700/60 shadow-sm overflow-hidden">
            @if ($combos->isEmpty())
                <div class="p-12 text-center">
                    <svg class="w-16 h-16 mx-auto text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white mt-4">No se encontraron combos</h3>
                    <p class="text-slate-500 dark:text-slate-400 mt-2 max-w-md mx-auto text-sm">Prueba creando un combo promocional que agrupe varios de tus productos estrella.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/75 dark:bg-slate-900/40 border-b border-slate-100 dark:border-slate-700/50">
                                <th class="p-4 text-xs font-bold uppercase tracking-wider text-slate-400">Combo</th>
                                <th class="p-4 text-xs font-bold uppercase tracking-wider text-slate-400">Negocio</th>
                                <th class="p-4 text-xs font-bold uppercase tracking-wider text-slate-400">Productos Componentes</th>
                                <th class="p-4 text-xs font-bold uppercase tracking-wider text-slate-400">Precio original</th>
                                <th class="p-4 text-xs font-bold uppercase tracking-wider text-slate-400">Precio combo</th>
                                <th class="p-4 text-xs font-bold uppercase tracking-wider text-slate-400">Estado</th>
                                <th class="p-4 text-xs font-bold uppercase tracking-wider text-slate-400 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700/40">
                            @foreach ($combos as $c)
                                @php
                                    $originalPrice = $c->products->sum(function($p) {
                                        return $p->price * $p->pivot->quantity;
                                    });
                                @endphp
                                <tr class="hover:bg-slate-50/40 dark:hover:bg-slate-900/10 transition-colors">
                                    <!-- Combo Info -->
                                    <td class="p-4">
                                        <div>
                                            <span class="block font-bold text-slate-800 dark:text-slate-200 text-sm">{{ $c->name }}</span>
                                            <span class="block text-xs text-slate-450 dark:text-slate-400 mt-1 max-w-xs truncate" title="{{ $c->description }}">{{ $c->description ?? 'Sin descripción' }}</span>
                                        </div>
                                    </td>

                                    <!-- Business -->
                                    <td class="p-4">
                                        <span class="text-sm font-semibold text-slate-700 dark:text-slate-300">{{ $c->business->business_name }}</span>
                                    </td>

                                    <!-- Products Components -->
                                    <td class="p-4">
                                        <div class="space-y-1">
                                            @foreach ($c->products as $p)
                                                <div class="flex items-center gap-1.5 text-xs text-slate-600 dark:text-slate-400">
                                                    <span class="font-extrabold text-luffy-straw bg-luffy-straw/10 px-1.5 py-0.5 rounded">{{ $p->pivot->quantity }}x</span>
                                                    <span>{{ $p->name }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </td>

                                    <!-- Original Price -->
                                    <td class="p-4 text-xs text-slate-400 line-through">
                                        S/ {{ number_format($originalPrice, 2) }}
                                    </td>

                                    <!-- Combo Price -->
                                    <td class="p-4">
                                        <span class="text-sm font-black text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/20 px-2.5 py-1 rounded-lg border border-emerald-100 dark:border-emerald-900/30">
                                            S/ {{ number_format($c->price, 2) }}
                                        </span>
                                    </td>

                                    <!-- Status -->
                                    <td class="p-4">
                                        @php
                                            $currColor = $c->is_active 
                                                ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/20 dark:text-emerald-400 border-emerald-100 dark:border-emerald-900/30'
                                                : 'bg-slate-100 text-slate-700 dark:bg-slate-900/30 dark:text-slate-400 border-slate-200 dark:border-slate-800';
                                        @endphp
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold border {{ $currColor }}">
                                                {{ $c->is_active ? 'Activo' : 'Inactivo' }}
                                            </span>

                                            <form method="POST" action="{{ route('seller.combos.toggle', $c) }}">
                                                @csrf
                                                <button type="submit" title="Cambiar Estado" class="text-slate-400 hover:text-amber-500 transition-colors p-1">
                                                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>

                                    <!-- Actions -->
                                    <td class="p-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('seller.combos.edit', $c) }}" class="p-2 text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700/60 transition-colors" title="Editar">
                                                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                            </a>
                                            <form method="POST" action="{{ route('seller.combos.destroy', $c) }}" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este combo?');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-2 text-rose-500 hover:text-rose-700 dark:text-rose-400 dark:hover:text-rose-300 rounded-lg hover:bg-rose-50/30 dark:hover:bg-rose-950/25 transition-colors" title="Eliminar">
                                                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if ($combos->hasPages())
                    <div class="px-5 py-4 border-t border-slate-100 dark:border-slate-700/50 bg-slate-50/50 dark:bg-slate-900/20">
                        {{ $combos->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</x-layouts.seller>
