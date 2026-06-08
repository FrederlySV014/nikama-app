<x-layouts.public>
    <x-slot:title>{{ $category->name }} - Nikama</x-slot:title>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
        <!-- Breadcrumbs -->
        <nav class="flex text-xs font-semibold text-slate-500 dark:text-slate-400" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1.5 md:space-x-2">
                <li class="inline-flex items-center">
                    <a href="/" class="hover:text-slate-800 dark:hover:text-white transition-colors">Inicio</a>
                </li>
                @foreach ($breadcrumbs as $index => $crumb)
                    <li class="flex items-center">
                        <svg class="w-3 h-3 text-slate-400 mx-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        @if ($index === count($breadcrumbs) - 1)
                            <span class="text-slate-800 dark:text-slate-200 font-bold truncate max-w-[120px] sm:max-w-none" aria-current="page">{{ $crumb['name'] }}</span>
                        @else
                            <a href="{{ $crumb['url'] }}" class="hover:text-slate-800 dark:hover:text-white transition-colors truncate max-w-[120px] sm:max-w-none">{{ $crumb['name'] }}</a>
                        @endif
                    </li>
                @endforeach
            </ol>
        </nav>

        <!-- Category Banner -->
        <div class="relative rounded-3xl overflow-hidden bg-gradient-to-r from-slate-900 via-slate-850 to-slate-800 text-white shadow-lg border border-slate-800">
            @if ($category->image_url)
                <div class="absolute inset-0 bg-cover bg-center mix-blend-overlay opacity-30" style="background-image: url('{{ $category->image_url }}')"></div>
            @endif
            <div class="relative p-6 sm:p-10 md:p-12 space-y-3 max-w-2xl">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-luffy-straw text-slate-900">Vertical</span>
                <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold font-['Outfit'] tracking-tight">{{ $category->name }}</h1>
                @if ($category->description)
                    <p class="text-sm sm:text-base text-slate-300 leading-relaxed">{{ $category->description }}</p>
                @else
                    <p class="text-sm text-slate-400">Explora las mejores opciones y productos en esta categoría.</p>
                @endif
            </div>
        </div>

        <!-- Subcategories Direct Navigation -->
        @if ($subcategories->isNotEmpty())
            <div class="space-y-3">
                <h2 class="text-sm font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Subcategorías</h2>
                <div class="flex items-center gap-2 overflow-x-auto pb-2 -mx-4 px-4 scrollbar-none">
                    @foreach ($subcategories as $sub)
                        <a href="{{ route('public.category.show', $sub->url_path) }}" class="flex-shrink-0 inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-750 text-slate-700 dark:text-slate-200 hover:text-slate-900 dark:hover:text-white font-bold text-sm transition-all border border-slate-100 dark:border-slate-700/60 shadow-sm hover:scale-102">
                            @if ($sub->icon)
                                <span class="text-lg">{{ $sub->icon }}</span>
                            @else
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            @endif
                            {{ $sub->name }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Filters & Products Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Sidebar: Filters (1 Col) -->
            <div class="space-y-6">
                <!-- Filters Card -->
                <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/60 shadow-sm p-6 space-y-6">
                    <div class="flex items-center justify-between border-b border-slate-50 dark:border-slate-700/30 pb-3">
                        <h3 class="font-extrabold font-['Outfit'] text-slate-800 dark:text-white text-base">Filtros</h3>
                        @if ($search || $selectedBusinessId || $minPrice || $maxPrice)
                            <a href="{{ route('public.category.show', $category->url_path) }}" class="text-xs font-bold text-rose-500 hover:underline">Limpiar todo</a>
                        @endif
                    </div>

                    <form method="GET" action="{{ route('public.category.show', $category->url_path) }}" class="space-y-6">
                        <!-- Search inside category -->
                        <div class="space-y-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-400">Buscar</label>
                            <div class="relative">
                                <input type="text" name="search" value="{{ $search }}" placeholder="Ej. Paracetamol..." class="w-full pl-3 pr-9 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-750 rounded-xl text-xs text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-colors">
                                @if ($search)
                                    <a href="{{ route('public.category.show', ['categoryPath' => $category->url_path, 'business' => $selectedBusinessId, 'min_price' => $minPrice, 'max_price' => $maxPrice, 'sort' => $sort]) }}" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </a>
                                @endif
                            </div>
                        </div>

                        <!-- Price Filter -->
                        <div class="space-y-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-400">Rango de Precio (S/)</label>
                            <div class="grid grid-cols-2 gap-2">
                                <input type="number" step="0.01" name="min_price" value="{{ $minPrice }}" placeholder="Mín" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-750 rounded-xl text-xs text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500">
                                <input type="number" step="0.01" name="max_price" value="{{ $maxPrice }}" placeholder="Máx" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-750 rounded-xl text-xs text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500">
                            </div>
                        </div>

                        <!-- Business Filter -->
                        @if ($filterBusinesses->isNotEmpty())
                            <div class="space-y-2">
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-400">Negocio</label>
                                <select name="business" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-750 rounded-xl text-xs text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-colors">
                                    <option value="all" {{ $selectedBusinessId === 'all' || !$selectedBusinessId ? 'selected' : '' }}>Todos los negocios</option>
                                    @foreach ($filterBusinesses as $fb)
                                        <option value="{{ $fb->id }}" {{ $selectedBusinessId === $fb->id ? 'selected' : '' }}>{{ $fb->business_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <!-- Sort Order -->
                        <div class="space-y-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-400">Ordenar por</label>
                            <select name="sort" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-750 rounded-xl text-xs text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-colors">
                                <option value="latest" {{ $sort === 'latest' ? 'selected' : '' }}>Más recientes</option>
                                <option value="price_asc" {{ $sort === 'price_asc' ? 'selected' : '' }}>Precio: Menor a Mayor</option>
                                <option value="price_desc" {{ $sort === 'price_desc' ? 'selected' : '' }}>Precio: Mayor a Menor</option>
                                <option value="rating_desc" {{ $sort === 'rating_desc' ? 'selected' : '' }}>Mejor valorados</option>
                            </select>
                        </div>

                        <button type="submit" class="w-full py-2.5 bg-slate-900 hover:bg-slate-800 dark:bg-slate-700 dark:hover:bg-slate-600 text-white font-bold rounded-xl text-xs transition-colors shadow-sm cursor-pointer">
                            Aplicar Filtros
                        </button>
                    </form>
                </div>
            </div>

            <!-- Products List (3 Cols) -->
            <div class="lg:col-span-3 space-y-6">
                @if ($products->isEmpty())
                    <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/60 p-12 text-center shadow-sm">
                        <svg class="w-16 h-16 mx-auto text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        <h3 class="text-xl font-bold text-slate-800 dark:text-white mt-4 font-['Outfit']">No se encontraron productos</h3>
                        <p class="text-slate-500 dark:text-slate-400 mt-2 max-w-sm mx-auto text-xs">No hay productos disponibles en esta categoría que cumplan con los filtros seleccionados.</p>
                        @if ($search || $selectedBusinessId || $minPrice || $maxPrice)
                            <a href="{{ route('public.category.show', $category->url_path) }}" class="mt-4 inline-flex items-center px-4 py-2 rounded-xl bg-luffy-straw hover:bg-luffy-straw-hover text-slate-900 font-bold text-xs shadow-sm transition-colors">Limpiar Filtros</a>
                        @endif
                    </div>
                @else
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 sm:gap-6">
                        @foreach ($products as $p)
                            <!-- Product Marketplace Card -->
                            <a href="{{ route('public.product.show', $p->slug) }}" class="group flex flex-col bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700/50 shadow-sm overflow-hidden hover:shadow-md hover:scale-[1.01] transition-all duration-300">
                                <!-- Image Wrapper -->
                                <div class="aspect-square bg-slate-50 dark:bg-slate-900 overflow-hidden relative border-b border-slate-50 dark:border-slate-800">
                                    @if ($p->main_image_url)
                                        <img src="{{ $p->main_image_url }}" alt="{{ $p->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-350">
                                    @else
                                        <div class="w-full h-full flex flex-col items-center justify-center text-slate-300 dark:text-slate-650">
                                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        </div>
                                    @endif

                                    <!-- Discount Tag -->
                                    @if ($p->compare_price && $p->compare_price > $p->price)
                                        @php
                                            $discountPercent = round((($p->compare_price - $p->price) / $p->compare_price) * 100);
                                        @endphp
                                        <span class="absolute top-2.5 left-2.5 bg-rose-500 text-white font-black text-[10px] px-2 py-0.5 rounded-md shadow-sm">
                                            -{{ $discountPercent }}%
                                        </span>
                                    @endif

                                    <!-- Out of stock overlay -->
                                    @if ($p->track_stock && $p->stock_quantity <= 0)
                                        <div class="absolute inset-0 bg-slate-950/40 backdrop-blur-[1px] flex items-center justify-center">
                                            <span class="bg-rose-600 text-white text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider shadow">Agotado</span>
                                        </div>
                                    @endif
                                </div>

                                <!-- Body -->
                                <div class="p-3 sm:p-4 flex-1 flex flex-col justify-between gap-3">
                                    <div class="space-y-1.5">
                                        <!-- Business & Rating -->
                                        <div class="flex items-center justify-between gap-1 text-[11px] text-slate-450 dark:text-slate-400">
                                            <span class="font-bold truncate max-w-[70%] text-slate-500 hover:text-slate-800 dark:hover:text-white transition-colors">{{ $p->business->business_name }}</span>
                                            
                                            <!-- Rating mini -->
                                            <div class="flex items-center gap-0.5 text-amber-400 shrink-0">
                                                <svg class="w-3 h-3 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                                <span class="font-extrabold text-slate-700 dark:text-slate-300">{{ number_format($p->rating_average, 1) }}</span>
                                            </div>
                                        </div>

                                        <!-- Title -->
                                        <h3 class="font-bold text-slate-800 dark:text-slate-200 text-sm leading-snug line-clamp-2 h-10 group-hover:text-luffy-red transition-colors">{{ $p->name }}</h3>
                                    </div>

                                    <!-- Footer: Price & Cart Button -->
                                    <div class="flex items-end justify-between gap-1 pt-1.5 border-t border-slate-50 dark:border-slate-750/30">
                                        <div>
                                            <span class="block font-extrabold text-slate-950 dark:text-white text-base">S/ {{ number_format($p->price, 2) }}</span>
                                            @if ($p->compare_price && $p->compare_price > $p->price)
                                                <span class="block text-xs text-slate-400 line-through">S/ {{ number_format($p->compare_price, 2) }}</span>
                                            @endif
                                        </div>

                                        <!-- Add Button (Icon) -->
                                        <span class="w-8 h-8 rounded-lg bg-luffy-straw text-slate-900 flex items-center justify-center group-hover:bg-luffy-straw-hover transition-colors shadow-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                                        </span>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    @if ($products->hasPages())
                        <div class="pt-4">
                            {{ $products->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</x-layouts.public>
