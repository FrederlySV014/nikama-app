<x-layouts.public>
    <x-slot:title>{{ $product->name }} - Nikama</x-slot:title>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-8">
        <!-- Breadcrumbs -->
        <nav class="flex text-xs font-semibold text-slate-500 dark:text-slate-400" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1.5 md:space-x-2">
                <li class="inline-flex items-center">
                    <a href="/" class="hover:text-slate-800 dark:hover:text-white transition-colors">Inicio</a>
                </li>
                @if ($product->categories->isNotEmpty())
                    @php $cat = $product->categories->first(); @endphp
                    <li class="flex items-center">
                        <svg class="w-3 h-3 text-slate-400 mx-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        <a href="{{ route('public.category.show', $cat->url_path) }}" class="hover:text-slate-800 dark:hover:text-white transition-colors truncate max-w-[150px]">{{ $cat->name }}</a>
                    </li>
                @endif
                <li class="flex items-center">
                    <svg class="w-3 h-3 text-slate-400 mx-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    <span class="text-slate-800 dark:text-slate-200 font-bold truncate max-w-[150px] sm:max-w-none" aria-current="page">{{ $product->name }}</span>
                </li>
            </ol>
        </nav>

        <!-- Status Alerts -->
        @if (session('success'))
            <div class="p-4 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 rounded-2xl flex items-center gap-3">
                <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                <span class="font-medium text-sm">{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="p-4 bg-rose-50 dark:bg-rose-900/30 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 rounded-2xl flex items-center gap-3">
                <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                <span class="font-medium text-sm">{{ session('error') }}</span>
            </div>
        @endif

        <!-- Product Details Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- Column: Gallery (5 Cols) -->
            <div class="lg:col-span-5 space-y-4" x-data="{ activeImage: '{{ $product->main_image_url ?? '' }}' }">
                <!-- Large Display -->
                <div class="w-full aspect-square rounded-3xl overflow-hidden bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800 relative shadow-sm">
                    <template x-if="activeImage">
                        <img :src="activeImage" alt="{{ $product->name }}" class="w-full h-full object-cover">
                    </template>
                    <template x-if="!activeImage">
                        <div class="w-full h-full flex flex-col items-center justify-center text-slate-350 dark:text-slate-600">
                            <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                    </template>

                    <!-- Discount Label -->
                    @if ($product->compare_price && $product->compare_price > $product->price)
                        @php
                            $discountPercent = round((($product->compare_price - $product->price) / $product->compare_price) * 100);
                        @endphp
                        <span class="absolute top-4 left-4 bg-rose-500 text-white font-black text-xs px-3 py-1 rounded-lg shadow-sm">
                            -{{ $discountPercent }}%
                        </span>
                    @endif
                </div>

                <!-- Thumbnails Gallery -->
                @if ($product->images->isNotEmpty())
                    <div class="grid grid-cols-4 gap-3">
                        @if ($product->main_image_url)
                            <button @click="activeImage = '{{ $product->main_image_url }}'" class="w-full aspect-square rounded-xl overflow-hidden bg-slate-50 dark:bg-slate-900 border-2 transition-all hover:scale-102 cursor-pointer" :class="activeImage === '{{ $product->main_image_url }}' ? 'border-amber-500' : 'border-slate-200 dark:border-slate-700'">
                                <img src="{{ $product->main_image_url }}" class="w-full h-full object-cover">
                            </button>
                        @endif
                        @foreach ($product->images as $img)
                            <button @click="activeImage = '{{ $img->image_url }}'" class="w-full aspect-square rounded-xl overflow-hidden bg-slate-50 dark:bg-slate-900 border-2 transition-all hover:scale-102 cursor-pointer" :class="activeImage === '{{ $img->image_url }}' ? 'border-amber-500' : 'border-slate-200 dark:border-slate-700'">
                                <img src="{{ $img->image_url }}" class="w-full h-full object-cover">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Column: Details & Seller Info (7 Cols) -->
            <div class="lg:col-span-7 flex flex-col md:flex-row gap-8">
                <!-- Main Info Column (Left) -->
                <div class="flex-1 space-y-6">
                    <!-- Title & Headers -->
                    <div class="space-y-3">
                        @if ($product->categories->isNotEmpty())
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 dark:bg-amber-900/10 text-amber-600 dark:text-amber-400 border border-amber-100 dark:border-amber-900/20">
                                {{ $product->categories->first()->name }}
                            </span>
                        @endif
                        <h2 class="text-3xl font-extrabold font-['Outfit'] text-slate-800 dark:text-white leading-tight">{{ $product->name }}</h2>
                        
                        <!-- Ratings summary -->
                        <div class="flex items-center gap-1.5 text-xs text-slate-500 dark:text-slate-400">
                            <div class="flex items-center text-amber-400">
                                @for ($i = 1; $i <= 5; $i++)
                                    <svg class="w-4 h-4 {{ $i <= round($product->rating_average) ? 'fill-current' : 'text-slate-250 dark:text-slate-700' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                @endfor
                            </div>
                            <span class="font-extrabold text-slate-700 dark:text-slate-300">{{ number_format($product->rating_average, 1) }}</span>
                            <span>•</span>
                            <a href="#opiniones" class="hover:underline">{{ $product->total_reviews }} opiniones</a>
                        </div>
                    </div>

                    <!-- Price Card -->
                    <div class="bg-slate-50/50 dark:bg-slate-900/30 p-5 rounded-2xl border border-slate-150/40 dark:border-slate-800 space-y-2">
                        <div class="flex items-baseline gap-2">
                            <span class="text-3xl font-black text-slate-950 dark:text-white">S/ {{ number_format($product->price, 2) }}</span>
                            @if ($product->compare_price && $product->compare_price > $product->price)
                                <span class="text-sm text-slate-400 line-through">S/ {{ number_format($product->compare_price, 2) }}</span>
                            @endif
                        </div>
                        @if ($product->compare_price && $product->compare_price > $product->price)
                            @php $savings = $product->compare_price - $product->price; @endphp
                            <p class="text-xs font-bold text-emerald-600 dark:text-emerald-400">¡Ahorras S/ {{ number_format($savings, 2) }}!</p>
                        @endif
                    </div>

                    <!-- Description -->
                    @if ($product->description)
                        <div class="space-y-2">
                            <h3 class="text-sm font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Descripción</h3>
                            <p class="text-sm text-slate-650 dark:text-slate-350 leading-relaxed whitespace-pre-line">{{ $product->description }}</p>
                        </div>
                    @endif

                    <!-- Product Specifications / Details -->
                    <div class="space-y-4 pt-4 border-t border-slate-100 dark:border-slate-750">
                        <h3 class="text-sm font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Especificaciones</h3>
                        <div class="grid grid-cols-2 gap-4 text-xs">
                            <!-- SKU -->
                            <div class="bg-white dark:bg-slate-800 p-3 rounded-xl border border-slate-100 dark:border-slate-700/60 flex items-center justify-between">
                                <span class="text-slate-400">SKU:</span>
                                <span class="font-mono font-bold text-slate-800 dark:text-white">{{ $product->sku ?? 'No registrado' }}</span>
                            </div>

                            <!-- Weight -->
                            @if ($product->weight_grams)
                                <div class="bg-white dark:bg-slate-800 p-3 rounded-xl border border-slate-100 dark:border-slate-700/60 flex items-center justify-between">
                                    <span class="text-slate-400">Peso:</span>
                                    <span class="font-bold text-slate-800 dark:text-white">{{ number_format($product->weight_grams) }} gr</span>
                                </div>
                            @endif

                            <!-- Prep Time -->
                            @if ($product->requires_preparation && $product->preparation_time_minutes)
                                <div class="bg-white dark:bg-slate-800 p-3 rounded-xl border border-slate-100 dark:border-slate-700/60 flex items-center justify-between col-span-2">
                                    <span class="text-slate-400 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        Tiempo de preparación:
                                    </span>
                                    <span class="font-bold text-slate-800 dark:text-white">{{ $product->preparation_time_minutes }} minutos</span>
                                </div>
                            @endif

                            <!-- Availability / Stock -->
                            <div class="bg-white dark:bg-slate-800 p-3 rounded-xl border border-slate-100 dark:border-slate-700/60 flex items-center justify-between col-span-2">
                                <span class="text-slate-400">Inventario:</span>
                                @if ($product->track_stock)
                                    @if ($product->stock_quantity > 0)
                                        <span class="font-bold text-emerald-600">Disponibles ({{ $product->stock_quantity }} unidades)</span>
                                    @else
                                        <span class="font-bold text-rose-500">Agotado</span>
                                    @endif
                                @else
                                    <span class="font-bold text-emerald-600">Stock Ilimitado</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Seller Info Sidebar (Right) -->
                <div class="w-full md:w-64 space-y-6 flex-shrink-0">
                    <!-- Seller Card -->
                    <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/60 shadow-sm p-5 space-y-4">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 dark:border-slate-75 pb-2">Vendido por</h3>
                        
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-750 flex-shrink-0 overflow-hidden flex items-center justify-center">
                                @if ($product->business->logo_url)
                                    <img src="{{ $product->business->logo_url }}" alt="{{ $product->business->business_name }}" class="w-full h-full object-cover">
                                @else
                                    <span class="font-bold text-sm text-slate-450">{{ substr($product->business->business_name, 0, 2) }}</span>
                                @endif
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-800 dark:text-white text-sm line-clamp-1">{{ $product->business->business_name }}</h4>
                                <div class="flex items-center gap-1 mt-0.5 text-[11px] text-amber-400">
                                    <svg class="w-3 h-3 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                    <span class="font-extrabold text-slate-700 dark:text-slate-300">{{ number_format($product->business->rating_average, 1) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Location & Contact Info -->
                        <div class="text-xs space-y-2.5 pt-3 border-t border-slate-50 dark:border-slate-750/30 text-slate-650 dark:text-slate-350">
                            @php $mainLoc = $product->business->locations->where('is_main', true)->first(); @endphp
                            @if ($mainLoc)
                                <div class="space-y-1">
                                    <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wide">Dirección</span>
                                    <span class="block leading-relaxed">{{ $mainLoc->address }}, {{ $mainLoc->district }}</span>
                                </div>
                            @endif

                            @if ($product->business->contact_phone)
                                <div class="space-y-0.5">
                                    <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wide">Contacto</span>
                                    <span class="block font-semibold">{{ $product->business->contact_phone }}</span>
                                </div>
                            @endif

                            <!-- Business Hours Summary -->
                            @if ($mainLoc && $mainLoc->hours->isNotEmpty())
                                <div class="space-y-1 pt-1" x-data="{ openHours: false }">
                                    <button @click="openHours = !openHours" type="button" class="w-full flex items-center justify-between text-[10px] font-bold text-slate-400 uppercase tracking-wide hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer">
                                        Horarios de Atención
                                        <svg class="w-3 h-3 transform transition-transform" :class="openHours ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </button>
                                    <div x-show="openHours" class="space-y-1 pt-1.5 pl-1.5 border-l border-slate-100 dark:border-slate-800 text-[11px] leading-relaxed" style="display: none;">
                                        @foreach ($mainLoc->hours as $hour)
                                            <div class="flex justify-between">
                                                <span class="capitalize">{{ __($hour->day_of_week) }}:</span>
                                                @if ($hour->is_closed)
                                                    <span class="text-rose-500 font-semibold">Cerrado</span>
                                                @elseif ($hour->is_24_hours)
                                                    <span class="text-teal-600 font-semibold">24 hrs</span>
                                                @else
                                                    <span class="font-semibold">{{ substr($hour->opening_time, 0, 5) }} - {{ substr($hour->closing_time, 0, 5) }}</span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Marketplace Button Actions -->
                    <div class="space-y-3" x-data="{ adding: false }">
                        <button 
                            @click="
                                @guest
                                    window.location.href = '{{ route('login') }}';
                                @else
                                    adding = true;
                                    fetch('{{ route('cart.add') }}', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                            'Accept': 'application/json'
                                        },
                                        body: JSON.stringify({ product_id: '{{ $product->id }}', quantity: 1 })
                                    })
                                    .then(res => {
                                        if (res.status === 401) {
                                            window.location.href = '{{ route('login') }}';
                                            return null;
                                        }
                                        return res.json();
                                    })
                                    .then(data => {
                                        if (data && data.success) {
                                            window.dispatchEvent(new CustomEvent('cart-updated'));
                                        }
                                        adding = false;
                                    })
                                    .catch(err => {
                                        console.error('Error adding to cart:', err);
                                        adding = false;
                                    });
                                @endguest
                            "
                            class="w-full py-3 bg-luffy-red hover:bg-luffy-red-hover text-white font-bold rounded-2xl text-sm shadow-md shadow-luffy-red/10 transition-all flex items-center justify-center gap-2 cursor-pointer hover:scale-[1.01] active:scale-[0.99] disabled:opacity-50 disabled:cursor-not-allowed"
                            :disabled="adding || {{ ($product->track_stock && $product->stock_quantity <= 0 && !$product->allow_backorder) ? 'true' : 'false' }}"
                        >
                            <!-- Spinner / Icon -->
                            <svg x-show="!adding" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            <div x-show="adding" class="animate-spin rounded-full h-5 w-5 border-2 border-white border-t-transparent" style="display: none;"></div>
                            
                            <span x-text="adding ? 'Añadiendo...' : 'Añadir al Carrito'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section: Combo Promotions -->
        @if ($combos->isNotEmpty())
            <div class="space-y-4 pt-8 border-t border-slate-100 dark:border-slate-750">
                <div class="bg-gradient-to-r from-luffy-straw/10 via-amber-500/5 to-emerald-500/5 dark:from-luffy-straw/5 dark:via-amber-500/5 dark:to-emerald-500/5 border border-luffy-straw/25 dark:border-luffy-straw/15 p-6 sm:p-8 rounded-3xl space-y-6">
                    <div class="flex items-center gap-3">
                        <span class="text-2xl">⚡</span>
                        <div>
                            <h3 class="text-lg font-black font-['Outfit'] text-slate-800 dark:text-white">¡Ahorra en Combo!</h3>
                            <p class="text-xs text-slate-550 dark:text-slate-400 mt-0.5">Llévate este producto como parte de estas increíbles ofertas agrupadas.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach ($combos as $combo)
                            @php
                                $originalPrice = $combo->products->sum(function($p) {
                                    return $p->price * $p->pivot->quantity;
                                });
                                $savings = $originalPrice - $combo->price;
                            @endphp
                            <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-150 dark:border-slate-700/60 shadow-sm flex flex-col justify-between relative overflow-hidden group">
                                @if ($savings > 0)
                                    <div class="absolute top-0 right-0 bg-emerald-500 text-white font-black text-[9px] uppercase tracking-wider py-1 px-3 rounded-bl-xl">
                                        Ahorra S/ {{ number_format($savings, 2) }}
                                    </div>
                                @endif
                                <div>
                                    <h4 class="font-extrabold text-slate-800 dark:text-white text-sm font-['Outfit'] group-hover:text-amber-500 transition-colors">
                                        {{ $combo->name }}
                                    </h4>
                                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block mt-1">
                                        De: {{ $combo->business->business_name }}
                                    </span>
                                    
                                    <div class="mt-3 space-y-1.5 text-xs text-slate-650 dark:text-slate-350 bg-slate-50 dark:bg-slate-900/30 p-3 rounded-xl border border-slate-100/50 dark:border-slate-800/40">
                                        <span class="text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest block mb-0.5">Incluye:</span>
                                        @foreach ($combo->products as $cp)
                                            <div class="flex items-center gap-1.5 {{ $cp->id === $product->id ? 'text-amber-600 dark:text-amber-400 font-extrabold' : '' }}">
                                                <span class="font-extrabold text-[9px] bg-emerald-500/10 px-1.5 py-0.5 rounded text-emerald-600 dark:text-emerald-400">{{ $cp->pivot->quantity }}x</span>
                                                <span class="truncate">{{ $cp->name }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="mt-4 pt-3 border-t border-slate-50 dark:border-slate-750 flex items-center justify-between gap-4">
                                    <div>
                                        <span class="text-base font-black text-slate-900 dark:text-white">
                                            S/ {{ number_format($combo->price, 2) }}
                                        </span>
                                        @if ($savings > 0)
                                            <span class="text-[10px] font-semibold text-slate-400 line-through ml-1.5">
                                                S/ {{ number_format($originalPrice, 2) }}
                                            </span>
                                        @endif
                                    </div>

                                    <button 
                                        x-data="{ adding: false }"
                                        @click="
                                            @guest
                                                window.location.href = '{{ route('login') }}';
                                            @else
                                                adding = true;
                                                fetch('{{ route('cart.add') }}', {
                                                    method: 'POST',
                                                    headers: {
                                                        'Content-Type': 'application/json',
                                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                        'Accept': 'application/json'
                                                    },
                                                    body: JSON.stringify({ product_combo_id: '{{ $combo->id }}', quantity: 1 })
                                                })
                                                .then(res => {
                                                    if (res.status === 401) {
                                                        window.location.href = '{{ route('login') }}';
                                                        return null;
                                                    }
                                                    return res.json();
                                                })
                                                .then(data => {
                                                    if (data && data.success) {
                                                        window.dispatchEvent(new CustomEvent('cart-updated'));
                                                    }
                                                    adding = false;
                                                })
                                                .catch(err => {
                                                    console.error('Error adding combo:', err);
                                                    adding = false;
                                                });
                                            @endguest
                                        "
                                        :disabled="adding"
                                        class="px-4 py-2 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-bold rounded-xl text-2xs uppercase tracking-wider shadow-sm hover:scale-[1.01] active:scale-[0.99] transition duration-300 flex items-center justify-center gap-1.5 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
                                    >
                                        <span x-show="!adding">Comprar Combo</span>
                                        <span x-show="adding" style="display: none;" class="flex items-center gap-1">
                                            <svg class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                        </span>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- Section: Related Products -->
        @if ($relatedProducts->isNotEmpty())
            <div class="space-y-4 pt-8 border-t border-slate-100 dark:border-slate-750">
                <h3 class="text-xl font-bold font-['Outfit'] text-slate-800 dark:text-white">Productos Relacionados</h3>
                
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 sm:gap-6">
                    @foreach ($relatedProducts as $p)
                        <!-- Product Card -->
                        <a href="{{ route('public.product.show', $p->slug) }}" class="group flex flex-col bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700/50 shadow-sm overflow-hidden hover:shadow-md hover:scale-[1.01] transition-all duration-300">
                            <div class="aspect-square bg-slate-50 dark:bg-slate-900 overflow-hidden relative border-b border-slate-50 dark:border-slate-800">
                                @if ($p->main_image_url)
                                    <img src="{{ $p->main_image_url }}" alt="{{ $p->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-350">
                                @else
                                    <div class="w-full h-full flex flex-col items-center justify-center text-slate-350 dark:text-slate-650">
                                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    </div>
                                @endif
                            </div>
                            <div class="p-3 sm:p-4 flex-1 flex flex-col justify-between gap-3">
                                <div class="space-y-1">
                                    <div class="flex items-center justify-between text-[10px] text-slate-450">
                                        <span class="font-bold truncate max-w-[70%] text-slate-550">{{ $p->business->business_name }}</span>
                                    </div>
                                    <h4 class="font-bold text-slate-800 dark:text-slate-200 text-xs line-clamp-2 leading-snug h-8 group-hover:text-luffy-red transition-colors">{{ $p->name }}</h4>
                                </div>
                                <div class="flex items-baseline justify-between pt-1 border-t border-slate-50 dark:border-slate-750/30">
                                    <span class="font-extrabold text-slate-900 dark:text-white text-sm">S/ {{ number_format($p->price, 2) }}</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Section: Reviews (Opiniones) -->
        <div id="opiniones" class="pt-8 border-t border-slate-100 dark:border-slate-750 grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- Review Summary (4 Cols) -->
            <div class="lg:col-span-4 space-y-6">
                <h3 class="text-xl font-bold font-['Outfit'] text-slate-800 dark:text-white">Opiniones y Calificaciones</h3>
                
                <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/60 shadow-sm p-6 flex items-center justify-center flex-col text-center space-y-3">
                    <span class="text-5xl font-black text-slate-900 dark:text-white font-['Outfit']">{{ number_format($product->rating_average, 1) }}</span>
                    
                    <div class="flex items-center text-amber-400">
                        @for ($i = 1; $i <= 5; $i++)
                            <svg class="w-5 h-5 {{ $i <= round($product->rating_average) ? 'fill-current' : 'text-slate-200 dark:text-slate-700' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                        @endfor
                    </div>

                    <span class="text-xs text-slate-450 dark:text-slate-400 font-semibold">Basado en {{ $product->total_reviews }} opiniones</span>
                </div>

                <!-- Review Creation Box -->
                <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/60 shadow-sm p-6">
                    @guest
                        <!-- Lock for Guests -->
                        <div class="flex flex-col items-center text-center space-y-4 py-4">
                            <div class="w-12 h-12 rounded-full bg-slate-50 dark:bg-slate-900 flex items-center justify-center text-slate-400 dark:text-slate-600 border border-slate-100 dark:border-slate-800">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            </div>
                            <p class="text-xs text-slate-600 dark:text-slate-300 font-semibold leading-relaxed">Inicia sesión para calificar este producto y compartir tu experiencia.</p>
                            <a href="{{ route('login') }}" class="inline-flex items-center justify-center w-full py-2.5 rounded-xl bg-luffy-straw hover:bg-luffy-straw-hover text-slate-900 font-bold text-xs shadow-sm transition-colors cursor-pointer">
                                Entrar a mi cuenta
                            </a>
                        </div>
                    @endguest

                    @auth
                        @if ($product->reviews()->where('user_id', auth()->id())->exists())
                            <!-- Already Reviewed -->
                            <div class="text-center py-6 space-y-2">
                                <div class="text-emerald-500 mx-auto flex items-center justify-center w-10 h-10 rounded-full bg-emerald-50 dark:bg-emerald-950/20">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                                <h4 class="text-sm font-bold text-slate-800 dark:text-white">¡Gracias por calificar!</h4>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Ya has dejado tu reseña y comentario para este producto.</p>
                            </div>
                        @else
                            <!-- Review Submission Form -->
                            <form method="POST" action="{{ route('public.product.review.store', $product) }}" class="space-y-4" x-data="{ rating: 0, hoverRating: 0 }">
                                @csrf
                                <h4 class="text-sm font-bold text-slate-800 dark:text-white font-['Outfit']">Calificar Producto</h4>
                                
                                <!-- Star Selector -->
                                <div class="space-y-1">
                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Tu calificación *</label>
                                    <div class="flex items-center gap-1">
                                        <input type="hidden" name="rating" :value="rating">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <button 
                                                type="button" 
                                                @click="rating = {{ $i }}"
                                                @mouseenter="hoverRating = {{ $i }}"
                                                @mouseleave="hoverRating = 0"
                                                class="text-2xl transition-colors cursor-pointer"
                                                :class="(hoverRating || rating) >= {{ $i }} ? 'text-amber-400' : 'text-slate-200 dark:text-slate-700'"
                                            >
                                                ★
                                            </button>
                                        @endfor
                                    </div>
                                    @error('rating')
                                        <p class="text-xs text-rose-500 font-medium">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Comment -->
                                <div class="space-y-1">
                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Tu opinión (Opcional)</label>
                                    <textarea name="comment" rows="3" placeholder="Comparte los puntos fuertes, sabor, calidad..." class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-750 rounded-xl text-xs text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500"></textarea>
                                    @error('comment')
                                        <p class="text-xs text-rose-500 font-medium">{{ $message }}</p>
                                    @enderror
                                </div>

                                <button type="submit" class="w-full py-2.5 bg-slate-900 hover:bg-slate-800 dark:bg-slate-700 dark:hover:bg-slate-600 text-white font-bold rounded-xl text-xs transition-colors shadow-sm cursor-pointer">
                                    Enviar Opinión
                                </button>
                            </form>
                        @endif
                    @endauth
                </div>
            </div>

            <!-- Reviews List (8 Cols) -->
            <div class="lg:col-span-8 space-y-4">
                @if ($product->reviews->isEmpty())
                    <div class="bg-slate-50/50 dark:bg-slate-900/40 rounded-3xl p-10 text-center border border-dashed border-slate-200 dark:border-slate-800">
                        <svg class="w-12 h-12 mx-auto text-slate-350 dark:text-slate-650" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                        <h4 class="text-sm font-bold text-slate-700 dark:text-slate-300 mt-3 font-['Outfit']">Aún no hay opiniones</h4>
                        <p class="text-xs text-slate-450 dark:text-slate-400 mt-1 max-w-xs mx-auto">Nadie ha calificado este producto todavía. ¿Has comprado este artículo? Sé el primero en dejar tu reseña.</p>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach ($product->reviews as $rev)
                            <!-- Review Card -->
                            <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/60 p-5 shadow-sm space-y-3">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="flex items-center gap-2.5">
                                        <!-- Avatar -->
                                        <div class="w-9 h-9 rounded-full bg-slate-100 dark:bg-slate-900 font-bold text-xs text-slate-500 dark:text-slate-400 flex items-center justify-center border border-slate-200/50 dark:border-slate-750">
                                            {{ strtoupper(substr($rev->user->first_name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <span class="block font-bold text-slate-800 dark:text-white text-xs">{{ $rev->user->first_name }} {{ $rev->user->last_name }}</span>
                                            
                                            <!-- Date and Verified Badge -->
                                            <div class="flex items-center gap-1.5 mt-0.5 text-[10px] text-slate-400">
                                                <span>{{ $rev->created_at->format('d/m/Y') }}</span>
                                                @if ($rev->is_verified_purchase)
                                                    <span>•</span>
                                                    <span class="text-teal-600 font-bold flex items-center gap-0.5">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                        Compra Verificada
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Rating -->
                                    <div class="flex items-center text-amber-400">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <svg class="w-3.5 h-3.5 {{ $i <= $rev->rating ? 'fill-current' : 'text-slate-200 dark:text-slate-750' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                        @endfor
                                    </div>
                                </div>

                                <!-- Comment -->
                                @if ($rev->comment)
                                    <p class="text-xs text-slate-650 dark:text-slate-350 leading-relaxed pl-1.5 border-l border-slate-100 dark:border-slate-750">{{ $rev->comment }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.public>
