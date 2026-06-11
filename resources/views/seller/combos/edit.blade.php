<x-layouts.seller>
    <x-slot:title>Editar Combo - Nikama</x-slot:title>

    <div class="max-w-4xl mx-auto space-y-6" x-data="{ 
        businessId: '{{ old('business_id', $combo->business_id) }}',
        items: {{ json_encode(old('products', $combo->products->map(function($p) {
            return [
                'product_id' => $p->id,
                'quantity' => $p->pivot->quantity
            ];
        })->toArray())) }},
        allProducts: {{ json_encode($products) }},
        
        get filteredProducts() {
            return this.allProducts.filter(p => p.business_id === this.businessId);
        },
        
        addItem() {
            this.items.push({
                product_id: '',
                quantity: 1
            });
        },
        
        removeItem(index) {
            this.items.splice(index, 1);
        },
        
        onBusinessChange() {
            this.items = [{product_id: '', quantity: 1}];
        }
    }">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('seller.combos.index') }}" class="inline-flex items-center text-sm font-semibold text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white transition-colors">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Volver a Combos
                </a>
                <h1 class="text-3xl font-bold font-['Outfit'] text-slate-800 dark:text-white mt-2">Editar Combo</h1>
            </div>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('seller.combos.update', $combo) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Card: General Information -->
            <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl border border-slate-100 dark:border-slate-700/60 shadow-sm space-y-6">
                <h2 class="text-lg font-bold font-['Outfit'] text-slate-800 dark:text-white border-b border-slate-100 dark:border-slate-700/60 pb-3">Información General</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Business -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-300 mb-2">Negocio *</label>
                        <select name="business_id" x-model="businessId" @change="onBusinessChange()" class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-750 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-colors @error('business_id') border-rose-500 @enderror">
                            <option value="">Selecciona el negocio</option>
                            @foreach ($businesses as $b)
                                <option value="{{ $b->id }}">{{ $b->business_name }}</option>
                            @endforeach
                        </select>
                        @error('business_id')
                            <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Name -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-300 mb-2">Nombre del Combo *</label>
                        <input type="text" name="name" value="{{ old('name', $combo->name) }}" placeholder="Ej. Combo Familiar Fin de Semana" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-750 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-colors @error('name') border-rose-500 @enderror">
                        @error('name')
                            <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-300 mb-2">Descripción del Combo</label>
                        <textarea name="description" rows="3" placeholder="Describe qué incluye el combo o los términos de la promoción..." class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-750 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-colors">{{ old('description', $combo->description) }}</textarea>
                    </div>

                    <!-- Price -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-300 mb-2">Precio de Venta del Combo (S/) *</label>
                        <input type="number" step="0.01" name="price" value="{{ old('price', $combo->price) }}" placeholder="0.00" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-750 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-colors @error('price') border-rose-500 @enderror">
                        @error('price')
                            <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status Active -->
                    <div class="flex items-center gap-2 pt-8">
                        <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $combo->is_active) ? 'checked' : '' }} class="w-4 h-4 text-amber-500 border-slate-300 rounded focus:ring-amber-500">
                        <label for="is_active" class="text-sm font-semibold text-slate-700 dark:text-slate-300 select-none">Habilitar Combo (Mostrar al cliente)</label>
                    </div>
                </div>
            </div>

            <!-- Card: Products Selection -->
            <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl border border-slate-100 dark:border-slate-700/60 shadow-sm space-y-6" x-show="businessId">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-700/60 pb-3">
                    <h2 class="text-lg font-bold font-['Outfit'] text-slate-800 dark:text-white">Productos Incluidos *</h2>
                    <button type="button" @click="addItem()" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-800 dark:text-slate-250 font-bold text-xs transition">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                        Añadir Producto
                    </button>
                </div>

                @error('products')
                    <p class="text-xs text-rose-500 font-medium">{{ $message }}</p>
                @enderror

                <div class="space-y-4">
                    <template x-for="(item, index) in items" :key="index">
                        <div class="flex flex-col sm:flex-row items-end sm:items-center gap-4 p-4 bg-slate-50 dark:bg-slate-900 border border-slate-150 dark:border-slate-750/50 rounded-xl relative">
                            <!-- Index badge -->
                            <span class="absolute top-2 left-2 text-[10px] bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-400 font-extrabold w-5 h-5 rounded-full flex items-center justify-center" x-text="index + 1"></span>

                            <!-- Product Selector -->
                            <div class="w-full sm:flex-1 pl-4">
                                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-350 mb-1.5">Producto Componente</label>
                                <select :name="`products[${index}][product_id]`" x-model="item.product_id" required class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-colors">
                                    <option value="">Selecciona un producto</option>
                                    <template x-for="p in filteredProducts" :key="p.id">
                                        <option :value="p.id" x-text="`${p.name} (S/ ${parseFloat(p.price).toFixed(2)})`" :selected="p.id == item.product_id"></option>
                                    </template>
                                </select>
                            </div>

                            <!-- Quantity -->
                            <div class="w-full sm:w-32">
                                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-350 mb-1.5">Cantidad</label>
                                <input type="number" :name="`products[${index}][quantity]`" x-model.number="item.quantity" min="1" required class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-colors">
                            </div>

                            <!-- Delete item Button -->
                            <div class="flex-shrink-0 pt-5">
                                <button type="button" @click="removeItem(index)" :disabled="items.length <= 1" class="p-2 text-rose-500 hover:text-rose-700 dark:text-rose-400 dark:hover:text-rose-350 rounded-lg hover:bg-rose-50/50 dark:hover:bg-rose-950/20 transition disabled:opacity-40">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Hint: Please select business first -->
            <div class="p-6 bg-slate-50 dark:bg-slate-900 border border-slate-150 dark:border-slate-800 text-center rounded-2xl" x-show="!businessId">
                <p class="text-sm font-semibold text-slate-550 dark:text-slate-400">Por favor, selecciona un negocio para cargar sus productos disponibles.</p>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end gap-3 pb-8">
                <a href="{{ route('seller.combos.index') }}" class="px-5 py-3 rounded-xl border border-slate-250 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-850 text-slate-650 dark:text-slate-300 font-bold text-sm transition-colors">
                    Cancelar
                </a>
                <button type="submit" class="px-6 py-3 rounded-xl bg-luffy-straw text-slate-900 font-bold hover:bg-luffy-straw-hover shadow-sm transition-colors text-sm">
                    Actualizar Combo
                </button>
            </div>
        </form>
    </div>
</x-layouts.seller>
