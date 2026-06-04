<x-layouts.admin>
    <x-slot:title>Gestión de Categorías - Nikama Admin</x-slot:title>

    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-gradient-to-r from-slate-900 to-slate-800 dark:from-slate-950 dark:to-slate-900 p-6 rounded-3xl border border-slate-700 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-extrabold font-['Outfit'] text-white">Categorías Generales</h2>
                <p class="text-slate-400 text-sm mt-1">Administra los sectores comerciales y la jerarquía de categorías de la plataforma.</p>
            </div>
            <div>
                <a href="{{ route('admin.categories.create') }}" 
                   class="inline-flex items-center gap-2 px-5 py-3 bg-luffy-red hover:bg-luffy-red/90 text-white font-bold text-sm rounded-2xl shadow-lg shadow-luffy-red/25 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Nueva Categoría
                </a>
            </div>
        </div>

        <!-- Alertas de Sesión -->
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

        @if (session('error'))
            <div class="p-4 bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 rounded-3xl flex items-start gap-3 shadow-sm">
                <svg class="w-5 h-5 text-rose-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <p class="font-bold text-sm">Error de restricción</p>
                    <p class="text-xs opacity-90 mt-0.5">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        <!-- Dashboard Superior de Métricas -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            <div class="bg-white dark:bg-slate-800 p-5 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm">
                <span class="text-slate-450 text-xs font-bold uppercase tracking-wider block">Total Categorías</span>
                <span class="text-2xl font-extrabold font-['Outfit'] text-slate-800 dark:text-white block mt-1">{{ $totalCategoriesCount }}</span>
            </div>

            <div class="bg-white dark:bg-slate-800 p-5 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm">
                <span class="text-slate-450 text-xs font-bold uppercase tracking-wider block">Activas</span>
                <span class="text-2xl font-extrabold font-['Outfit'] text-emerald-600 dark:text-emerald-400 block mt-1">{{ $activeCategoriesCount }}</span>
            </div>

            <div class="bg-white dark:bg-slate-800 p-5 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm">
                <span class="text-slate-450 text-xs font-bold uppercase tracking-wider block">Inactivas</span>
                <span class="text-2xl font-extrabold font-['Outfit'] text-rose-500 block mt-1">{{ $inactiveCategoriesCount }}</span>
            </div>

            <div class="bg-white dark:bg-slate-800 p-5 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm">
                <span class="text-slate-450 text-xs font-bold uppercase tracking-wider block">Nivel Raíz (Sectores)</span>
                <span class="text-2xl font-extrabold font-['Outfit'] text-slate-800 dark:text-white block mt-1">{{ $rootCategoriesCount }}</span>
            </div>

            <div class="bg-white dark:bg-slate-800 p-5 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm col-span-2 md:col-span-1">
                <span class="text-slate-450 text-xs font-bold uppercase tracking-wider block">Subcategorías</span>
                <span class="text-2xl font-extrabold font-['Outfit'] text-indigo-500 block mt-1">{{ $childCategoriesCount }}</span>
            </div>
        </div>

        <!-- Filtros y Buscador -->
        <div class="bg-white dark:bg-slate-800 p-6 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm">
            <form action="{{ route('admin.categories.index') }}" method="GET" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Búsqueda -->
                    <div>
                        <label for="search" class="text-xs font-bold text-slate-450 uppercase tracking-wider block mb-1.5">Búsqueda</label>
                        <input type="text" name="search" id="search" value="{{ $search }}" placeholder="Nombre o slug..."
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                    </div>

                    <!-- Filtro Estado -->
                    <div>
                        <label for="status" class="text-xs font-bold text-slate-450 uppercase tracking-wider block mb-1.5">Estado</label>
                        <select name="status" id="status" 
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                            <option value="all" {{ $status === 'all' ? 'selected' : '' }}>Todos</option>
                            <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Activas</option>
                            <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Inactivas</option>
                        </select>
                    </div>

                    <!-- Filtro Nivel -->
                    <div>
                        <label for="level" class="text-xs font-bold text-slate-450 uppercase tracking-wider block mb-1.5">Nivel Jerárquico</label>
                        <select name="level" id="level" 
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                            <option value="all" {{ $level === 'all' ? 'selected' : '' }}>Todos los niveles</option>
                            <option value="root" {{ $level === 'root' ? 'selected' : '' }}>Sólo raíces (Sectores)</option>
                            <option value="child" {{ $level === 'child' ? 'selected' : '' }}>Sólo subcategorías</option>
                        </select>
                    </div>

                    <!-- Filtro Padre -->
                    <div>
                        <label for="parent" class="text-xs font-bold text-slate-450 uppercase tracking-wider block mb-1.5">Categoría Padre</label>
                        <select name="parent" id="parent" 
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                            <option value="all" {{ $parentFilter === 'all' ? 'selected' : '' }}>Todas las raíces</option>
                            @foreach ($parentCategories as $parentCat)
                                <option value="{{ $parentCat->id }}" {{ $parentFilter === $parentCat->id ? 'selected' : '' }}>{{ $parentCat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex gap-2 justify-end pt-2">
                    @if ($search || $status !== 'all' || $level !== 'all' || $parentFilter !== 'all')
                        <a href="{{ route('admin.categories.index') }}" 
                           class="px-5 py-2.5 border border-slate-200 dark:border-slate-750 text-slate-700 dark:text-slate-300 font-bold text-xs rounded-xl hover:bg-slate-50 dark:hover:bg-slate-750 transition-colors">
                            Limpiar Filtros
                        </a>
                    @endif
                    <button type="submit" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs rounded-xl transition-all">
                        Aplicar Filtros
                    </button>
                </div>
            </form>
        </div>

        <!-- Tabla Jerárquica Híbrida (AlpineJS) -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm overflow-hidden" 
             x-data="{ 
                expandedParents: [], 
                searchActive: {{ ($search || $status !== 'all' || $level !== 'all' || $parentFilter !== 'all') ? 'true' : 'false' }},
                toggleParent(id) {
                    if (this.expandedParents.includes(id)) {
                        this.expandedParents = this.expandedParents.filter(pId => pId !== id);
                    } else {
                        this.expandedParents.push(id);
                    }
                }
             }">
            <div class="overflow-x-auto">
                @if ($categories->isEmpty())
                    <div class="p-12 text-center text-slate-500 dark:text-slate-400">
                        <svg class="w-12 h-12 mx-auto mb-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-base font-semibold">No se encontraron categorías.</p>
                        <p class="text-xs mt-1">Prueba cambiando los filtros o agregando una nueva categoría.</p>
                    </div>
                @else
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-850 border-b border-slate-100 dark:border-slate-700 text-slate-400 text-xs font-bold uppercase tracking-wider">
                                <th class="px-6 py-4 w-12 text-center"></th>
                                <th class="px-6 py-4">Nombre / Slug</th>
                                <th class="px-6 py-4">Categoría Padre</th>
                                <th class="px-6 py-4 text-center">Orden</th>
                                <th class="px-6 py-4 text-center">Negocios</th>
                                <th class="px-6 py-4 text-center">Productos</th>
                                <th class="px-6 py-4 text-center">Estado</th>
                                <th class="px-6 py-4 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700 text-sm">
                            @foreach ($categories as $cat)
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors"
                                    x-show="searchActive || !'{{ $cat->parent_id }}' || expandedParents.includes('{{ $cat->parent_id }}')"
                                    x-transition:enter="transition ease-out duration-150"
                                    x-transition:enter-start="opacity-0 transform -translate-y-1"
                                    x-transition:enter-end="opacity-100 transform translate-y-0"
                                    style="{{ (!$search && $level === 'all' && $parentFilter === 'all' && $cat->parent_id) ? 'display: none;' : '' }}">
                                    
                                    <!-- Botón Expandir si tiene hijos -->
                                    <td class="px-6 py-4 text-center">
                                        @if ($cat->children_count > 0)
                                            <button type="button" @click="toggleParent('{{ $cat->id }}')" 
                                                    class="text-slate-450 hover:text-slate-800 dark:hover:text-white transition-colors focus:outline-none">
                                                <!-- Icono expander + / - -->
                                                <svg class="w-4 h-4 transform transition-transform" 
                                                     :class="expandedParents.includes('{{ $cat->id }}') ? 'rotate-90' : ''"
                                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path>
                                                </svg>
                                            </button>
                                        @endif
                                    </td>

                                    <!-- Nombre / Slug (Indentado si es hija) -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center {{ $cat->parent_id ? 'pl-6' : '' }}">
                                            @if ($cat->parent_id)
                                                <span class="text-slate-350 dark:text-slate-650 mr-2 font-mono">├──</span>
                                            @endif
                                            <div>
                                                <div class="flex items-center gap-2">
                                                    @if ($cat->icon)
                                                        <span class="text-sm" title="Icono: {{ $cat->icon }}">{{ $cat->icon }}</span>
                                                    @endif
                                                    <span class="font-bold text-slate-800 dark:text-white">{{ $cat->name }}</span>
                                                    @if ($cat->children_count > 0)
                                                        <span class="px-1.5 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400">
                                                            {{ $cat->children_count }} {{ $cat->children_count === 1 ? 'hija' : 'hijas' }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <span class="text-xs text-slate-400 block font-mono">{{ $cat->slug }}</span>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Categoría Padre -->
                                    <td class="px-6 py-4 text-slate-500">
                                        @if ($cat->parent)
                                            <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-semibold bg-indigo-50 dark:bg-indigo-950/20 text-indigo-600 dark:text-indigo-400">
                                                {{ $cat->parent->name }}
                                            </span>
                                        @else
                                            <span class="text-xs text-slate-400 italic">Sector Raíz</span>
                                        @endif
                                    </td>

                                    <!-- Orden -->
                                    <td class="px-6 py-4 text-center font-semibold text-slate-650 dark:text-slate-300">
                                        {{ $cat->sort_order }}
                                    </td>

                                    <!-- Negocios asociados -->
                                    <td class="px-6 py-4 text-center text-slate-600 dark:text-slate-400 font-medium">
                                        {{ $cat->businesses_count }}
                                    </td>

                                    <!-- Productos asociados -->
                                    <td class="px-6 py-4 text-center text-slate-600 dark:text-slate-400 font-medium">
                                        {{ $cat->products_count }}
                                    </td>

                                    <!-- Estado Toggle -->
                                    <td class="px-6 py-4 text-center">
                                        <form action="{{ route('admin.categories.toggle', $cat) }}" method="POST" class="inline-block">
                                            @csrf
                                            <button type="submit" 
                                                    class="px-2.5 py-1 rounded-full text-xs font-bold transition-all hover:scale-105 {{ $cat->is_active ? 'bg-emerald-50 dark:bg-emerald-950/30 text-emerald-600' : 'bg-rose-50 dark:bg-rose-950/30 text-rose-600' }}"
                                                    title="Haz clic para alternar estado">
                                                {{ $cat->is_active ? 'Activa' : 'Inactiva' }}
                                            </button>
                                        </form>
                                    </td>

                                    <!-- Acciones -->
                                    <td class="px-6 py-4 text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('admin.categories.show', $cat) }}" 
                                               class="p-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-750 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-lg transition-colors"
                                               title="Ver Detalle">
                                                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </a>
                                            
                                            <a href="{{ route('admin.categories.edit', $cat) }}" 
                                               class="p-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-750 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-lg transition-colors"
                                               title="Editar">
                                                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                                </svg>
                                            </a>

                                            <form action="{{ route('admin.categories.destroy', $cat) }}" method="POST" class="inline-block"
                                                  onsubmit="return confirm('¿Estás seguro de que deseas eliminar esta categoría?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="p-1.5 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/20 dark:hover:bg-rose-900/30 text-rose-600 rounded-lg transition-colors"
                                                        title="Eliminar">
                                                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

            <!-- Paginación -->
            @if ($categories->hasPages())
                <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-850 flex items-center justify-between">
                    <div class="text-xs text-slate-500">
                        Mostrando {{ $categories->firstItem() }} al {{ $categories->lastItem() }} de {{ $categories->total() }} registros.
                    </div>
                    <div class="pagination-native">
                        {{ $categories->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-layouts.admin>
