<x-layouts.seller>
    <x-slot:title>Crear Producto - Nikama</x-slot:title>
 
    <div class="max-w-4xl mx-auto space-y-6" x-data="{ 
        trackStock: {{ old('track_stock', 'true') === 'true' || old('track_stock') === '1' ? 'true' : 'false' }}, 
        requiresPrep: {{ old('requires_preparation', 'true') === 'true' || old('requires_preparation') === '1' ? 'true' : 'false' }},
        mainImageName: '',
        additionalImagesNames: []
    }">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('seller.products.index') }}" class="inline-flex items-center text-sm font-semibold text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white transition-colors">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Volver a Productos
                </a>
                <h1 class="text-3xl font-bold font-['Outfit'] text-slate-800 dark:text-white mt-2">Nuevo Producto</h1>
            </div>
        </div>
 
        <!-- Form -->
        <form method="POST" action="{{ route('seller.products.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
 
            <!-- Card: General Information -->
            <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl border border-slate-100 dark:border-slate-700/60 shadow-sm space-y-6">
                <h2 class="text-lg font-bold font-['Outfit'] text-slate-800 dark:text-white border-b border-slate-100 dark:border-slate-700/60 pb-3">Información General</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Name -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-300 mb-2">Nombre del Producto *</label>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="Ej. Hamburguesa Doble con Queso" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-750 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-colors @error('name') border-rose-500 @enderror">
                        @error('name')
                            <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
 
                    <!-- Business -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-300 mb-2">Negocio *</label>
                        <select name="business_id" class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-750 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-colors @error('business_id') border-rose-500 @enderror">
                            <option value="">Selecciona el negocio</option>
                            @foreach ($businesses as $b)
                                <option value="{{ $b->id }}" {{ old('business_id') === $b->id ? 'selected' : '' }}>{{ $b->business_name }}</option>
                            @endforeach
                        </select>
                        @error('business_id')
                            <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
 
                    <!-- Category -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-300 mb-2">Categoría (Último Nivel) *</label>
                        <select name="category_id" class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-750 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-colors @error('category_id') border-rose-500 @enderror">
                            <option value="">Selecciona la categoría</option>
                            @foreach ($categories as $c)
                                <option value="{{ $c->id }}" {{ old('category_id') === $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
 
                    <!-- Description -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-300 mb-2">Descripción Completa</label>
                        <textarea name="description" rows="4" placeholder="Describe los ingredientes, dimensiones o detalles del producto..." class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-750 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-colors">{{ old('description') }}</textarea>
                    </div>
                </div>
            </div>
 
            <!-- Card: Commercial Info & Stock -->
            <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl border border-slate-100 dark:border-slate-700/60 shadow-sm space-y-6">
                <h2 class="text-lg font-bold font-['Outfit'] text-slate-800 dark:text-white border-b border-slate-100 dark:border-slate-700/60 pb-3">Información Comercial</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Price -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-300 mb-2">Precio de Venta (S/) *</label>
                        <input type="number" step="0.01" name="price" value="{{ old('price') }}" placeholder="0.00" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-750 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-colors @error('price') border-rose-500 @enderror">
                        @error('price')
                            <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
 
                    <!-- Compare Price -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-300 mb-2">Precio de Oferta / Antes (S/)</label>
                        <input type="number" step="0.01" name="compare_price" value="{{ old('compare_price') }}" placeholder="0.00" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-750 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-colors">
                        @error('compare_price')
                            <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
 
                    <!-- SKU -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-300 mb-2">SKU (Código Único)</label>
                        <input type="text" name="sku" value="{{ old('sku') }}" placeholder="Ej. HAM-001" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-750 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-colors @error('sku') border-rose-500 @enderror">
                        @error('sku')
                            <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
 
                    <!-- Track Stock Checkbox -->
                    <div class="md:col-span-3 flex items-center gap-2 py-2 border-y border-slate-50 dark:border-slate-900/60">
                        <input type="checkbox" name="track_stock" id="track_stock" value="1" x-model="trackStock" class="w-4 h-4 text-amber-500 border-slate-300 rounded focus:ring-amber-500">
                        <label for="track_stock" class="text-sm font-semibold text-slate-700 dark:text-slate-300 select-none">Habilitar control de inventario (Stock)</label>
                    </div>
 
                    <!-- Stock Quantity -->
                    <div x-show="trackStock" class="md:col-span-2 space-y-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-300 mb-2">Cantidad Disponible</label>
                        <input type="number" name="stock_quantity" value="{{ old('stock_quantity', 0) }}" min="0" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-750 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-colors @error('stock_quantity') border-rose-500 @enderror">
                        @error('stock_quantity')
                            <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
 
                    <!-- Allow Backorder Checkbox -->
                    <div x-show="trackStock" class="flex items-center gap-2 md:col-span-1 pt-8">
                        <input type="checkbox" name="allow_backorder" id="allow_backorder" value="1" {{ old('allow_backorder') ? 'checked' : '' }} class="w-4 h-4 text-amber-500 border-slate-300 rounded focus:ring-amber-500">
                        <label for="allow_backorder" class="text-sm font-semibold text-slate-700 dark:text-slate-300 select-none">Permitir pedidos sin stock</label>
                    </div>
                </div>
            </div>
 
            <!-- Card: Images -->
            <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl border border-slate-100 dark:border-slate-700/60 shadow-sm space-y-6">
                <h2 class="text-lg font-bold font-['Outfit'] text-slate-800 dark:text-white border-b border-slate-100 dark:border-slate-700/60 pb-3">Imágenes del Producto</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Main Image -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-300 mb-2">Imagen Principal *</label>
                        <div class="flex items-center justify-center w-full">
                            <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-slate-200 dark:border-slate-700 border-dashed rounded-xl cursor-pointer bg-slate-50 dark:bg-slate-900 hover:bg-slate-100/60 dark:hover:bg-slate-850/60 transition-colors">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                     <svg class="w-8 h-8 mb-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 font-semibold">Cargar imagen principal</p>
                                </div>
                                <input type="file" name="main_image" class="hidden" accept="image/*" @change="mainImageName = $event.target.files[0] ? $event.target.files[0].name : ''">
                            </label>
                        </div>
                        <template x-if="mainImageName">
                            <div class="mt-2 text-xs font-semibold text-emerald-600 dark:text-emerald-400 flex items-center gap-1.5 bg-emerald-50 dark:bg-emerald-950/30 px-3 py-1.5 rounded-lg border border-emerald-100 dark:border-emerald-900/40">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                <span class="truncate">Seleccionado: <span x-text="mainImageName"></span></span>
                            </div>
                        </template>
                        @error('main_image')
                            <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
 
                    <!-- Additional Images -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-300 mb-2">Imágenes de Galería (Opcional)</label>
                        <div class="flex items-center justify-center w-full">
                            <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-slate-200 dark:border-slate-700 border-dashed rounded-xl cursor-pointer bg-slate-50 dark:bg-slate-900 hover:bg-slate-100/60 dark:hover:bg-slate-850/60 transition-colors">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <svg class="w-8 h-8 mb-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 font-semibold">Cargar múltiples imágenes</p>
                                </div>
                                <input type="file" name="additional_images[]" class="hidden" multiple accept="image/*" @change="additionalImagesNames = Array.from($event.target.files).map(f => f.name)">
                            </label>
                        </div>
                        <template x-if="additionalImagesNames.length > 0">
                            <div class="mt-2 text-xs font-semibold text-emerald-600 dark:text-emerald-400 flex flex-col gap-1 bg-emerald-50 dark:bg-emerald-950/30 px-3 py-1.5 rounded-lg border border-emerald-100 dark:border-emerald-900/40">
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    <span>Seleccionadas (<span x-text="additionalImagesNames.length"></span> imágenes):</span>
                                </div>
                                <ul class="list-disc pl-4 mt-1 font-normal text-slate-500 dark:text-slate-400 max-h-24 overflow-y-auto space-y-0.5">
                                    <template x-for="name in additionalImagesNames">
                                        <li class="truncate" x-text="name"></li>
                                    </template>
                                </ul>
                            </div>
                        </template>
                        @error('additional_images')
                            <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
 
            <!-- Card: Preparation & Status -->
            <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl border border-slate-100 dark:border-slate-700/60 shadow-sm space-y-6">
                <h2 class="text-lg font-bold font-['Outfit'] text-slate-800 dark:text-white border-b border-slate-100 dark:border-slate-700/60 pb-3">Preparación y Estado</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Status -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-300 mb-2">Estado del Producto *</label>
                        <select name="status" class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-750 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-colors @error('status') border-rose-500 @enderror">
                            <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Borrador</option>
                            <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Activo</option>
                            <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactivo</option>
                            <option value="out_of_stock" {{ old('status') === 'out_of_stock' ? 'selected' : '' }}>Agotado</option>
                        </select>
                        @error('status')
                            <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
 
                    <!-- Weight -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-300 mb-2">Peso en Gramos (Opcional)</label>
                        <input type="number" step="0.1" name="weight_grams" value="{{ old('weight_grams') }}" placeholder="0.0" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-750 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-colors">
                        @error('weight_grams')
                            <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
 
                    <!-- Requires Preparation -->
                    <div class="md:col-span-2 flex items-center gap-2 py-2 border-y border-slate-50 dark:border-slate-900/60">
                        <input type="checkbox" name="requires_preparation" id="requires_preparation" value="1" x-model="requiresPrep" class="w-4 h-4 text-amber-500 border-slate-300 rounded focus:ring-amber-500">
                        <label for="requires_preparation" class="text-sm font-semibold text-slate-700 dark:text-slate-300 select-none">Requiere tiempo de preparación previa</label>
                    </div>
 
                    <!-- Prep Time -->
                    <div x-show="requiresPrep" class="md:col-span-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-300 mb-2">Tiempo estimado de preparación (Minutos)</label>
                        <input type="number" name="preparation_time_minutes" value="{{ old('preparation_time_minutes') }}" min="0" placeholder="Ej. 15" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-750 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-colors @error('preparation_time_minutes') border-rose-500 @enderror">
                        @error('preparation_time_minutes')
                            <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
 
                    <!-- Is Featured -->
                    <div class="md:col-span-2 flex items-center gap-2">
                        <input type="checkbox" name="is_featured" id="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }} class="w-4 h-4 text-amber-500 border-slate-300 rounded focus:ring-amber-500">
                        <label for="is_featured" class="text-sm font-semibold text-slate-700 dark:text-slate-300 select-none">Destacar producto en el catálogo</label>
                    </div>
                </div>
            </div>
 
            <!-- Form Actions -->
            <div class="flex justify-end gap-3 pb-8">
                <a href="{{ route('seller.products.index') }}" class="px-5 py-3 rounded-xl border border-slate-250 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-850 text-slate-650 dark:text-slate-300 font-bold text-sm transition-colors">
                    Cancelar
                </a>
                <button type="submit" class="px-6 py-3 rounded-xl bg-luffy-straw text-slate-900 font-bold hover:bg-luffy-straw-hover shadow-sm transition-colors text-sm">
                    Guardar Producto
                </button>
            </div>
        </form>
    </div>
</x-layouts.seller>
