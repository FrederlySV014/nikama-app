@if (isset($combos) && $combos->isNotEmpty())
<section class="py-10 transition-colors duration-300" id="combos-section">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold font-['Outfit'] text-gray-900 dark:text-white flex items-center gap-2">
                    🔥 Combos Promocionales
                </h2>
                <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">¡Los mejores paquetes de tus restaurantes locales a precios insuperables!</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($combos as $combo)
                @php
                    $originalPrice = $combo->products->sum(function($p) {
                        return $p->price * $p->pivot->quantity;
                    });
                    $savings = $originalPrice - $combo->price;
                @endphp
                <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 border border-slate-150 dark:border-slate-700/60 shadow-md hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between relative overflow-hidden group">
                    <!-- Top Badge -->
                    @if ($savings > 0)
                        <div class="absolute top-0 right-0 bg-gradient-to-l from-emerald-500 to-teal-500 text-white font-black text-[10px] uppercase tracking-wider py-1.5 px-4 rounded-bl-2xl shadow-sm">
                            Ahorras S/ {{ number_format($savings, 2) }}
                        </div>
                    @endif

                    <div class="space-y-4">
                        <!-- Combo Title -->
                        <div>
                            <span class="text-[10px] font-black text-luffy-straw bg-luffy-straw/10 px-2.5 py-1 rounded-xl uppercase tracking-wider">
                                {{ $combo->business->business_name }}
                            </span>
                            <h3 class="text-lg font-extrabold text-slate-850 dark:text-white font-['Outfit'] mt-2.5 group-hover:text-luffy-straw transition-colors">
                                {{ $combo->name }}
                            </h3>
                            @if ($combo->description)
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5 leading-relaxed line-clamp-2">
                                    {{ $combo->description }}
                                </p>
                            @endif
                        </div>

                        <!-- Included items -->
                        <div class="space-y-2 bg-slate-50 dark:bg-slate-900/40 p-4 rounded-2xl border border-slate-100 dark:border-slate-800/40">
                            <span class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest block mb-1">¿Qué incluye este combo?</span>
                            <div class="space-y-1.5">
                                @foreach ($combo->products as $p)
                                    <div class="flex items-center gap-2 text-xs font-medium text-slate-700 dark:text-slate-350">
                                        <span class="font-black text-[10px] text-emerald-600 dark:text-emerald-400 bg-emerald-500/10 px-1.5 py-0.5 rounded-lg">
                                            {{ $p->pivot->quantity }}x
                                        </span>
                                        <span class="truncate">{{ $p->name }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Pricing & Button -->
                    <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-700/60">
                        <div class="flex items-baseline justify-between mb-3">
                            <div class="flex items-baseline gap-2">
                                <span class="text-2xl font-black text-slate-900 dark:text-white font-['Outfit']">
                                    S/ {{ number_format($combo->price, 2) }}
                                </span>
                                @if ($savings > 0)
                                    <span class="text-xs font-semibold text-slate-400 line-through">
                                        S/ {{ number_format($originalPrice, 2) }}
                                    </span>
                                @endif
                            </div>
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
                                        console.error('Error adding combo to cart:', err);
                                        adding = false;
                                    });
                                @endguest
                            "
                            :disabled="adding"
                            class="w-full py-3 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-bold rounded-2xl text-xs uppercase tracking-wider shadow-lg shadow-emerald-500/10 hover:shadow-emerald-500/20 hover:scale-[1.01] active:scale-[0.99] transition duration-300 flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <span x-show="!adding" class="flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                ¡Pedir Combo!
                            </span>
                            <span x-show="adding" style="display: none;" class="flex items-center gap-1.5">
                                <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Añadiendo...
                            </span>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif
