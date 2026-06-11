@if (isset($featuredProducts) && $featuredProducts->isNotEmpty())
<section class="py-10 transition-colors duration-300" id="productos-destacados-section">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold font-['Outfit'] text-gray-900 dark:text-white flex items-center gap-2">
                    ✨ Productos Destacados
                </h2>
                <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Lo más vendido y recomendado de nuestros comercios locales.</p>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6">
            @foreach ($featuredProducts as $p)
                <!-- Product Card -->
                <a href="{{ route('public.product.show', $p->slug) }}" class="group flex flex-col bg-white dark:bg-slate-800 rounded-3xl border border-slate-150 dark:border-slate-700/50 shadow-sm overflow-hidden hover:shadow-md hover:scale-[1.01] transition-all duration-300">
                    <!-- Image Wrapper -->
                    <div class="aspect-square bg-slate-50 dark:bg-slate-900 overflow-hidden relative border-b border-slate-100 dark:border-slate-800">
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
                            <span class="absolute top-3 left-3 bg-rose-500 text-white font-black text-[10px] px-2.5 py-0.8 rounded-lg shadow-sm">
                                -{{ $discountPercent }}%
                            </span>
                        @endif

                        <!-- Out of stock overlay -->
                        @if ($p->track_stock && $p->stock_quantity <= 0)
                            <div class="absolute inset-0 bg-slate-950/40 backdrop-blur-[1px] flex items-center justify-center">
                                <span class="bg-rose-600 text-white text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-wider shadow">Agotado</span>
                            </div>
                        @endif
                    </div>

                    <!-- Body -->
                    <div class="p-4 flex-1 flex flex-col justify-between gap-3">
                        <div class="space-y-1.5">
                            <!-- Business & Rating -->
                            <div class="flex items-center justify-between gap-1 text-[10px] text-slate-450 dark:text-slate-400">
                                <span class="font-extrabold truncate max-w-[70%] text-slate-500">{{ $p->business->business_name }}</span>
                                
                                <!-- Rating -->
                                <div class="flex items-center gap-0.5 text-amber-400 shrink-0">
                                    <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                    <span class="font-extrabold text-slate-700 dark:text-slate-350">{{ number_format($p->rating_average, 1) }}</span>
                                </div>
                            </div>

                            <!-- Title -->
                            <h3 class="font-bold text-slate-800 dark:text-slate-200 text-sm leading-snug line-clamp-2 h-10 group-hover:text-luffy-red transition-colors font-['Outfit']">{{ $p->name }}</h3>
                        </div>

                        <!-- Footer: Price & Add button -->
                        <div class="flex items-end justify-between gap-1 pt-2 border-t border-slate-100 dark:border-slate-750/30">
                            <div>
                                <span class="block font-black text-slate-900 dark:text-white text-base">S/ {{ number_format($p->price, 2) }}</span>
                                @if ($p->compare_price && $p->compare_price > $p->price)
                                    <span class="block text-2xs text-slate-400 line-through">S/ {{ number_format($p->compare_price, 2) }}</span>
                                @endif
                            </div>

                            <!-- Add button -->
                            <button 
                                x-data="{ adding: false }"
                                @click.prevent="
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
                                            body: JSON.stringify({ product_id: '{{ $p->id }}', quantity: 1 })
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
                                            console.error('Error adding product to cart:', err);
                                            adding = false;
                                        });
                                    @endguest
                                "
                                :disabled="adding || {{ ($p->track_stock && $p->stock_quantity <= 0 && !$p->allow_backorder) ? 'true' : 'false' }}"
                                class="w-8 h-8 rounded-xl bg-luffy-straw hover:bg-luffy-straw-hover text-slate-900 flex items-center justify-center transition shadow-sm disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer shrink-0"
                                title="Añadir al Carrito"
                            >
                                <svg x-show="!adding" class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                                <div x-show="adding" style="display: none;" class="animate-spin rounded-full h-4.5 w-4.5 border-2 border-slate-900 border-t-transparent"></div>
                            </button>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif
