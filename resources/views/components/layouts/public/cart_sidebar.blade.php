<!-- Slide-over Cart Sidebar -->
<div 
    x-data="cartSidebar()" 
    x-show="isOpen"
    @keydown.escape.window="isOpen = false"
    class="fixed inset-0 z-50 overflow-hidden" 
    style="display: none;"
    role="dialog" 
    aria-modal="true"
>
    <!-- Background overlay -->
    <div 
        x-show="isOpen"
        x-transition:enter="ease-in-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in-out duration-300"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="isOpen = false"
        class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
    ></div>

    <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
        <!-- Panel content -->
        <div 
            x-show="isOpen"
            x-transition:enter="transform transition ease-in-out duration-300 sm:duration-300"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transform transition ease-in-out duration-300 sm:duration-300"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            class="pointer-events-auto w-screen max-w-md"
        >
            <div class="flex h-full flex-col bg-white dark:bg-slate-900 shadow-2xl border-l border-slate-100 dark:border-slate-800">
                <!-- Header -->
                <div class="flex items-center justify-between px-4 py-5 border-b border-slate-100 dark:border-slate-800 sm:px-6">
                    <h2 class="text-lg font-extrabold font-['Outfit'] text-slate-900 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-luffy-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        Tu Carrito
                        <span 
                            x-show="itemsCount > 0"
                            class="bg-rose-100 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 text-xs font-black px-2 py-0.5 rounded-full"
                            x-text="itemsCount"
                        ></span>
                    </h2>
                    <div class="ml-3 flex h-7 items-center">
                        <button 
                            type="button" 
                            @click="isOpen = false"
                            class="relative -m-2 p-2 text-slate-400 hover:text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors cursor-pointer"
                        >
                            <span class="sr-only">Cerrar panel</span>
                            <svg class="h-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Main Scrollable Area -->
                <div class="flex-1 overflow-y-auto py-6 px-4 sm:px-6 relative">
                    <!-- Loading overlay -->
                    <div 
                        x-show="loading" 
                        class="absolute inset-0 bg-white/70 dark:bg-slate-900/70 z-10 flex items-center justify-center backdrop-blur-[1px]"
                    >
                        <div class="animate-spin rounded-full h-8 w-8 border-2 border-luffy-red border-t-transparent"></div>
                    </div>

                    <!-- Items List -->
                    <div class="space-y-6">
                        <!-- Empty state -->
                        <div 
                            x-show="items.length === 0" 
                            class="flex flex-col items-center justify-center text-center py-20 space-y-4"
                        >
                            <div class="w-16 h-16 rounded-full bg-slate-50 dark:bg-slate-800 flex items-center justify-center text-slate-350 dark:text-slate-650 border border-slate-100 dark:border-slate-700/60">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                </svg>
                            </div>
                            <div class="space-y-1">
                                <h3 class="font-bold text-slate-800 dark:text-white text-sm">Tu carrito está vacío</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 max-w-[200px]">Explora nuestros productos y añade algo delicioso.</p>
                            </div>
                            <button 
                                type="button" 
                                @click="isOpen = false"
                                class="bg-luffy-red hover:bg-luffy-red-hover text-white px-4 py-2 rounded-xl text-xs font-bold transition-all hover:scale-105 active:scale-95 cursor-pointer"
                            >
                                Seguir comprando
                            </button>
                        </div>

                        <!-- Active items -->
                        <template x-for="item in items" :key="item.id">
                            <div class="flex items-center gap-4 py-4 border-b border-slate-50 dark:border-slate-800/40 last:border-0">
                                <!-- Thumbnail -->
                                <div class="w-20 h-20 rounded-2xl bg-slate-50 dark:bg-slate-850 overflow-hidden border border-slate-100 dark:border-slate-800 shrink-0">
                                    <template x-if="item.product_image">
                                        <img :src="item.product_image" :alt="item.product_name" class="w-full h-full object-cover">
                                    </template>
                                    <template x-if="!item.product_image">
                                        <div class="w-full h-full flex items-center justify-center text-slate-350 dark:text-slate-600">
                                            <span x-show="item.product_combo_id" class="text-emerald-500">
                                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                                </svg>
                                            </span>
                                            <span x-show="!item.product_combo_id">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                            </span>
                                        </div>
                                    </template>
                                </div>

                                <!-- Info -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex justify-between items-start gap-1">
                                        <div>
                                            <h4 class="font-extrabold text-slate-900 dark:text-white text-xs sm:text-sm truncate" x-text="item.product_name"></h4>
                                            <span x-show="item.product_combo_id" class="inline-flex items-center px-1.5 py-0.5 rounded text-[8px] font-extrabold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-400 mt-1 uppercase tracking-wider">Combo Promocional</span>
                                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wide truncate mt-0.5" x-text="item.business_name"></p>
                                        </div>
                                        <span class="font-black text-slate-900 dark:text-white text-xs shrink-0" x-text="'S/ ' + item.total_price.toFixed(2)"></span>
                                    </div>

                                    <!-- Controls -->
                                    <div class="flex items-center justify-between mt-3">
                                        <!-- +/- Quantities -->
                                        <div class="flex items-center bg-slate-100 dark:bg-slate-800 rounded-lg p-0.5 border border-slate-150/20 dark:border-slate-700/30">
                                            <button 
                                                type="button" 
                                                @click="updateQuantity(item.id, item.quantity - 1)"
                                                class="w-7 h-7 flex items-center justify-center text-slate-500 hover:text-slate-800 dark:hover:text-white hover:bg-white dark:hover:bg-slate-700 rounded-md transition-all cursor-pointer"
                                                aria-label="Disminuir cantidad"
                                            >
                                                -
                                            </button>
                                            <span class="w-8 text-center text-xs font-bold text-slate-800 dark:text-white" x-text="item.quantity"></span>
                                            <button 
                                                type="button" 
                                                @click="updateQuantity(item.id, item.quantity + 1)"
                                                class="w-7 h-7 flex items-center justify-center text-slate-500 hover:text-slate-800 dark:hover:text-white hover:bg-white dark:hover:bg-slate-700 rounded-md transition-all cursor-pointer"
                                                aria-label="Aumentar cantidad"
                                            >
                                                +
                                            </button>
                                        </div>

                                        <!-- Trash Action -->
                                        <button 
                                            type="button" 
                                            @click="removeItem(item.id)"
                                            class="text-rose-500 hover:text-rose-600 dark:text-rose-400 p-1.5 rounded-lg hover:bg-rose-50 dark:hover:bg-rose-950/20 transition-all cursor-pointer"
                                            aria-label="Eliminar artículo"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Footer Summary -->
                <div 
                    x-show="items.length > 0" 
                    class="border-t border-slate-100 dark:border-slate-800 px-4 py-6 sm:px-6 bg-slate-50/50 dark:bg-slate-900/40"
                >
                    <div class="flex justify-between text-base font-extrabold text-slate-900 dark:text-white">
                        <p>Subtotal</p>
                        <p x-text="'S/ ' + subtotal.toFixed(2)"></p>
                    </div>
                    <p class="mt-0.5 text-[11px] text-slate-400">Gastos de envío e impuestos se calculan al finalizar la compra.</p>
                    <div class="mt-6">
                        <a 
                            href="{{ route('checkout.index') }}" 
                            class="flex items-center justify-center rounded-2xl bg-luffy-red hover:bg-luffy-red-hover px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-luffy-red/25 transition-all hover:scale-101 active:scale-99 cursor-pointer"
                        >
                            Finalizar Compra
                        </a>
                    </div>
                    <div class="mt-4 flex justify-center text-center text-xs text-slate-500">
                        <p>
                            o{' '}
                            <button 
                                type="button" 
                                @click="isOpen = false"
                                class="font-bold text-luffy-red hover:underline cursor-pointer"
                            >
                                Seguir Navegando
                            </button>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function cartSidebar() {
        return {
            isOpen: false,
            loading: false,
            items: [],
            subtotal: 0.00,
            itemsCount: 0,
            
            init() {
                this.fetchCart();
                
                // Event listener to open/close cart
                window.addEventListener('toggle-cart', () => {
                    this.isOpen = !this.isOpen;
                });
                
                // Event listener to reload cart list and show
                window.addEventListener('cart-updated', () => {
                    this.fetchCart();
                    this.isOpen = true;
                });
            },
            
            fetchCart() {
                this.loading = true;
                fetch('{{ route('cart.json') }}')
                    .then(res => {
                        if (res.status === 401) {
                            // User is guest, return empty cart
                            return { items: [], subtotal: 0.00, items_count: 0 };
                        }
                        return res.json();
                    })
                    .then(data => {
                        this.items = data.items || [];
                        this.subtotal = parseFloat(data.subtotal) || 0.00;
                        this.itemsCount = parseInt(data.items_count) || 0;
                        
                        // Fire header count updater
                        window.dispatchEvent(new CustomEvent('cart-count-updated', { 
                            detail: { count: this.itemsCount } 
                        }));
                        
                        this.loading = false;
                    })
                    .catch(err => {
                        console.error('Error fetching cart:', err);
                        this.loading = false;
                    });
            },
            
            updateQuantity(itemId, newQuantity) {
                if (newQuantity < 0) return;
                this.loading = true;
                
                fetch(`/cart/items/${itemId}/quantity`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ quantity: newQuantity })
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
                        this.items = data.cart.items;
                        this.subtotal = parseFloat(data.cart.subtotal);
                        this.itemsCount = parseInt(data.cart.items_count);
                        
                        window.dispatchEvent(new CustomEvent('cart-count-updated', { 
                            detail: { count: this.itemsCount } 
                        }));
                    }
                    this.loading = false;
                })
                .catch(err => {
                    console.error('Error updating quantity:', err);
                    this.loading = false;
                });
            },
            
            removeItem(itemId) {
                this.loading = true;
                
                fetch(`/cart/items/${itemId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
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
                        this.items = data.cart.items;
                        this.subtotal = parseFloat(data.cart.subtotal);
                        this.itemsCount = parseInt(data.cart.items_count);
                        
                        window.dispatchEvent(new CustomEvent('cart-count-updated', { 
                            detail: { count: this.itemsCount } 
                        }));
                    }
                    this.loading = false;
                })
                .catch(err => {
                    console.error('Error removing item:', err);
                    this.loading = false;
                });
            }
        }
    }
</script>
