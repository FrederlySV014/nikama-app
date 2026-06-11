<x-layouts.admin>
    <x-slot:title>Gestión de Productos - Nikama Admin</x-slot:title>

    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-gradient-to-r from-slate-900 to-slate-800 dark:from-slate-950 dark:to-slate-900 p-6 rounded-3xl border border-slate-700 shadow-sm">
            <h2 class="text-2xl font-extrabold font-['Outfit'] text-white">Productos del Sistema</h2>
            <p class="text-slate-400 text-sm mt-1">Administra y destaca los productos de todos los negocios de la plataforma.</p>
        </div>

        <!-- Session Alerts -->
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

        <!-- Filters & Search -->
        <div class="bg-white dark:bg-slate-800 p-6 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm">
            <form action="{{ route('admin.products.index') }}" method="GET" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Search -->
                    <div>
                        <label for="search" class="text-xs font-bold text-slate-450 uppercase tracking-wider block mb-1.5">Búsqueda</label>
                        <input type="text" name="search" id="search" value="{{ $search }}" placeholder="Nombre o SKU..."
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                    </div>

                    <!-- Business Filter -->
                    <div>
                        <label for="business" class="text-xs font-bold text-slate-450 uppercase tracking-wider block mb-1.5">Negocio</label>
                        <select name="business" id="business" 
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                            <option value="all" {{ $businessFilter === 'all' ? 'selected' : '' }}>Todos los negocios</option>
                            @foreach ($businesses as $b)
                                <option value="{{ $b->id }}" {{ $businessFilter === $b->id ? 'selected' : '' }}>{{ $b->business_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <label for="status" class="text-xs font-bold text-slate-450 uppercase tracking-wider block mb-1.5">Estado</label>
                        <select name="status" id="status" 
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                            <option value="all" {{ $status === 'all' ? 'selected' : '' }}>Todos</option>
                            <option value="draft" {{ $status === 'draft' ? 'selected' : '' }}>Borrador</option>
                            <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Activo</option>
                            <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Inactivo</option>
                            <option value="out_of_stock" {{ $status === 'out_of_stock' ? 'selected' : '' }}>Agotado</option>
                        </select>
                    </div>

                    <!-- Featured Filter -->
                    <div>
                        <label for="featured" class="text-xs font-bold text-slate-450 uppercase tracking-wider block mb-1.5">Destacado Inicio</label>
                        <select name="featured" id="featured" 
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                            <option value="all" {{ $featuredFilter === 'all' ? 'selected' : '' }}>Todos</option>
                            <option value="yes" {{ $featuredFilter === 'yes' ? 'selected' : '' }}>Destacados</option>
                            <option value="no" {{ $featuredFilter === 'no' ? 'selected' : '' }}>No Destacados</option>
                        </select>
                    </div>
                </div>

                <div class="flex gap-2 justify-end pt-2">
                    @if ($search || $status !== 'all' || $businessFilter !== 'all' || $featuredFilter !== 'all')
                        <a href="{{ route('admin.products.index') }}" 
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

        <!-- Products Table -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                @if ($products->isEmpty())
                    <div class="p-12 text-center text-slate-500 dark:text-slate-400">
                        <svg class="w-12 h-12 mx-auto mb-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        <p class="text-base font-semibold">No se encontraron productos.</p>
                        <p class="text-xs mt-1">Los negocios aún no han creado productos o la búsqueda no arrojó resultados.</p>
                    </div>
                @else
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-850 border-b border-slate-100 dark:border-slate-700 text-slate-400 text-xs font-bold uppercase tracking-wider">
                                <th class="px-6 py-4">Producto</th>
                                <th class="px-6 py-4">Negocio / SKU</th>
                                <th class="px-6 py-4">Categoría</th>
                                <th class="px-6 py-4">Precio</th>
                                <th class="px-6 py-4 text-center">Stock</th>
                                <th class="px-6 py-4 text-center">Destacado</th>
                                <th class="px-6 py-4 text-center">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700 text-sm">
                            @foreach ($products as $p)
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                                    <!-- Image / Name -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-12 h-12 rounded-xl bg-slate-100 dark:bg-slate-900 border border-slate-150 dark:border-slate-750 overflow-hidden flex-shrink-0">
                                                @if ($p->main_image_url)
                                                    <img src="{{ $p->main_image_url }}" alt="{{ $p->name }}" class="w-full h-full object-cover">
                                                @else
                                                    <div class="w-full h-full flex items-center justify-center text-slate-350 dark:text-slate-650">
                                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <span class="font-bold text-slate-850 dark:text-white block">{{ $p->name }}</span>
                                                <span class="text-xs text-slate-400 block font-mono">{{ $p->slug }}</span>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Business / SKU -->
                                    <td class="px-6 py-4">
                                        <span class="font-semibold text-slate-800 dark:text-slate-200 block">{{ $p->business->business_name }}</span>
                                        <span class="text-xs text-slate-450 block font-mono">{{ $p->sku ?? 'Sin SKU' }}</span>
                                    </td>

                                    <!-- Category -->
                                    <td class="px-6 py-4">
                                        @if ($p->categories->isNotEmpty())
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-semibold bg-indigo-50 dark:bg-indigo-950/20 text-indigo-600 dark:text-indigo-400">
                                                {{ $p->categories->first()->name }}
                                            </span>
                                        @else
                                            <span class="text-xs text-slate-400 italic">Sin categoría</span>
                                        @endif
                                    </td>

                                    <!-- Price -->
                                    <td class="px-6 py-4 font-bold text-slate-900 dark:text-white">
                                        S/ {{ number_format($p->price, 2) }}
                                        @if ($p->compare_price)
                                            <span class="block text-xs text-slate-400 line-through font-normal">S/ {{ number_format($p->compare_price, 2) }}</span>
                                        @endif
                                    </td>

                                    <!-- Stock -->
                                    <td class="px-6 py-4 text-center">
                                        @if ($p->track_stock)
                                            <span class="font-semibold {{ $p->stock_quantity > 0 ? 'text-slate-800 dark:text-slate-200' : 'text-rose-500' }}">
                                                {{ $p->stock_quantity }}
                                            </span>
                                        @else
                                            <span class="text-xs text-slate-400 italic">Ilimitado</span>
                                        @endif
                                    </td>

                                    <!-- Featured Toggle -->
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex items-center justify-center gap-1.5">
                                            @if ($p->is_featured)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-semibold bg-amber-50 text-amber-600 border border-amber-100 dark:bg-amber-950/20 dark:text-amber-400 dark:border-amber-900/30">
                                                    ★ Destacado
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-semibold bg-slate-50 text-slate-450 border border-slate-200 dark:bg-slate-900/30 dark:text-slate-500 dark:border-slate-800">
                                                    Normal
                                                </span>
                                            @endif
                                            <form action="{{ route('admin.products.toggle-featured', $p) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" title="Alternar Destacado en Inicio" class="text-slate-400 hover:text-amber-500 transition-colors p-1">
                                                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>

                                    <!-- Status -->
                                    <td class="px-6 py-4 text-center">
                                        @php
                                            $colors = [
                                                'draft' => 'bg-slate-100 text-slate-600 dark:bg-slate-900/30 dark:text-slate-450 border-slate-200 dark:border-slate-800',
                                                'active' => 'bg-emerald-50 text-emerald-600 dark:bg-emerald-950/20 dark:text-emerald-450 border-emerald-100 dark:border-emerald-900/30',
                                                'inactive' => 'bg-amber-50 text-amber-600 dark:bg-amber-950/20 dark:text-amber-450 border-amber-100 dark:border-amber-900/30',
                                                'out_of_stock' => 'bg-rose-50 text-rose-600 dark:bg-rose-950/20 dark:text-rose-450 border-rose-100 dark:border-rose-900/30',
                                            ];
                                            $currColor = $colors[$p->status] ?? $colors['draft'];
                                        @endphp
                                        <div class="flex items-center justify-center gap-1.5">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold border {{ $currColor }}">
                                                {{ ucfirst($p->status) }}
                                            </span>
                                            <form action="{{ route('admin.products.toggle', $p) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" title="Alternar Estado Activo/Inactivo" class="text-slate-400 hover:text-amber-500 transition-colors p-1">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
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

            <!-- Pagination -->
            @if ($products->hasPages())
                <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-850 flex items-center justify-between">
                    <div class="text-xs text-slate-500">
                        Mostrando {{ $products->firstItem() }} al {{ $products->lastItem() }} de {{ $products->total() }} registros.
                    </div>
                    <div class="pagination-native">
                        {{ $products->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-layouts.admin>
