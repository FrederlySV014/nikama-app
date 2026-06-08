<x-layouts.seller>
    <x-slot:title>{{ $product->name }} - Detalles - Nikama</x-slot:title>

    <div class="max-w-5xl mx-auto space-y-6">
        <!-- Breadcrumbs & Navigation -->
        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('seller.products.index') }}" class="inline-flex items-center text-sm font-semibold text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white transition-colors">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Volver a Productos
                </a>
                <h1 class="text-3xl font-bold font-['Outfit'] text-slate-800 dark:text-white mt-2">{{ $product->name }}</h1>
                <p class="text-xs font-mono text-slate-400 dark:text-slate-500 mt-1">ID: {{ $product->id }}</p>
            </div>
            
            <!-- Quick Actions -->
            <div class="flex items-center gap-2">
                <a href="{{ route('seller.products.edit', $product) }}" class="inline-flex items-center justify-center px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-750 dark:text-white font-bold rounded-xl text-sm transition-colors shadow-sm border border-slate-200/50 dark:border-slate-650">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                    Editar
                </a>
                
                @if (in_array($product->status, ['active', 'inactive']))
                    <form method="POST" action="{{ route('seller.products.toggle', $product) }}" class="inline">
                        @csrf
                        <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-amber-500/10 hover:bg-amber-500/20 text-amber-600 dark:text-amber-400 font-bold rounded-xl text-sm transition-colors border border-amber-500/20">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                            Alternar Estado
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Main Product Card & Details Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Side: Product Image & Attributes (2 Cols) -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Overview Header -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700/60 shadow-sm p-6 overflow-hidden">
                    <div class="flex flex-col md:flex-row gap-6">
                        <!-- Image Container -->
                        <div class="w-full md:w-48 aspect-square md:h-48 rounded-2xl bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800 overflow-hidden flex-shrink-0 relative">
                            @if ($product->main_image_url)
                                <img src="{{ $product->main_image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex flex-col items-center justify-center text-slate-300 dark:text-slate-600">
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    <span class="text-xs font-semibold mt-2 text-slate-400">Sin Imagen</span>
                                </div>
                            @endif
                        </div>

                        <!-- General Details -->
                        <div class="flex-1 space-y-4">
                            <div class="flex flex-wrap items-center gap-2">
                                <!-- Status Badge -->
                                @php
                                    $statusColors = [
                                        'draft' => 'bg-slate-150 text-slate-700 dark:bg-slate-900/40 dark:text-slate-400 border-slate-200 dark:border-slate-850',
                                        'active' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/20 dark:text-emerald-400 border-emerald-100 dark:border-emerald-900/30',
                                        'inactive' => 'bg-amber-50 text-amber-700 dark:bg-amber-950/20 dark:text-amber-400 border-amber-100 dark:border-amber-900/30',
                                        'out_of_stock' => 'bg-rose-50 text-rose-700 dark:bg-rose-950/20 dark:text-rose-400 border-rose-100 dark:border-rose-900/30',
                                        'suspended' => 'bg-red-50 text-red-700 dark:bg-red-950/20 dark:text-red-400 border-red-100 dark:border-red-900/30',
                                    ];
                                    $currColor = $statusColors[$product->status] ?? $statusColors['draft'];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $currColor }}">
                                    {{ ucfirst(str_replace('_', ' ', $product->status)) }}
                                </span>

                                <!-- Category Badge -->
                                @if ($product->categories->isNotEmpty())
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 dark:bg-amber-900/10 text-amber-600 dark:text-amber-400 border border-amber-100 dark:border-amber-900/20">
                                        Categoría: {{ $product->categories->first()->name }}
                                    </span>
                                @endif

                                <!-- Available Badge -->
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $product->is_available ? 'bg-teal-50 dark:bg-teal-950/20 text-teal-700 dark:text-teal-400 border border-teal-100 dark:border-teal-900/20' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200/50 dark:border-slate-700' }}">
                                    {{ $product->is_available ? 'Disponible en Venta' : 'No Disponible' }}
                                </span>
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Negocio Asociado</label>
                                <span class="text-lg font-bold text-slate-800 dark:text-white">{{ $product->business->business_name }}</span>
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Slug de URL</label>
                                <span class="text-sm font-mono text-slate-600 dark:text-slate-350 bg-slate-50 dark:bg-slate-900/50 px-2 py-1 rounded-md border border-slate-150/40 dark:border-slate-800/60">{{ $product->slug }}</span>
                            </div>
                        </div>
                    </div>

                    @if ($product->description)
                        <div class="mt-6 pt-6 border-t border-slate-100 dark:border-slate-750">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Descripción Completa</label>
                            <div class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed whitespace-pre-line">
                                {{ $product->description }}
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Commercial & Inventory Details -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700/60 shadow-sm p-6 space-y-6">
                    <h3 class="text-lg font-bold font-['Outfit'] text-slate-800 dark:text-white border-b border-slate-100 dark:border-slate-750 pb-3">Información Comercial y Stock</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Pricing Details -->
                        <div class="space-y-4">
                            <div class="flex justify-between items-center py-2 border-b border-slate-50 dark:border-slate-750/30">
                                <span class="text-sm text-slate-500">Precio de Venta:</span>
                                <span class="text-lg font-extrabold text-slate-800 dark:text-white">S/ {{ number_format($product->price, 2) }}</span>
                            </div>
                            
                            <div class="flex justify-between items-center py-2 border-b border-slate-50 dark:border-slate-750/30">
                                <span class="text-sm text-slate-500">Precio Comparativo (Antes):</span>
                                <span class="text-sm font-semibold text-slate-600 dark:text-slate-300 {{ $product->compare_price ? 'line-through' : '' }}">
                                    {{ $product->compare_price ? 'S/ ' . number_format($product->compare_price, 2) : 'Sin oferta' }}
                                </span>
                            </div>

                            @if ($product->compare_price && $product->compare_price > $product->price)
                                <div class="flex justify-between items-center py-2 border-b border-slate-50 dark:border-slate-750/30">
                                    <span class="text-sm text-slate-500">Descuento Estimado:</span>
                                    @php
                                        $discount = (($product->compare_price - $product->price) / $product->compare_price) * 100;
                                    @endphp
                                    <span class="text-sm font-bold text-emerald-600 dark:text-emerald-400">
                                        -{{ round($discount) }}% OFF
                                    </span>
                                </div>
                            @endif

                            <div class="flex justify-between items-center py-2">
                                <span class="text-sm text-slate-500">Código SKU:</span>
                                <span class="text-sm font-mono font-bold text-slate-700 dark:text-slate-200">{{ $product->sku ?? 'No registrado' }}</span>
                            </div>
                        </div>

                        <!-- Stock Details -->
                        <div class="space-y-4">
                            <div class="flex justify-between items-center py-2 border-b border-slate-50 dark:border-slate-750/30">
                                <span class="text-sm text-slate-500">Control de Inventario:</span>
                                <span class="text-sm font-bold {{ $product->track_stock ? 'text-emerald-600' : 'text-slate-400' }}">
                                    {{ $product->track_stock ? 'Habilitado' : 'Desactivado (Ilimitado)' }}
                                </span>
                            </div>

                            @if ($product->track_stock)
                                <div class="flex justify-between items-center py-2 border-b border-slate-50 dark:border-slate-750/30">
                                    <span class="text-sm text-slate-500">Cantidad Disponible:</span>
                                    <span class="text-sm font-bold {{ $product->stock_quantity > 0 ? 'text-slate-800 dark:text-white' : 'text-rose-500' }}">
                                        {{ $product->stock_quantity }} unidades
                                    </span>
                                </div>

                                <div class="flex justify-between items-center py-2">
                                    <span class="text-sm text-slate-500">Permitir pedidos sin stock:</span>
                                    <span class="text-sm font-bold {{ $product->allow_backorder ? 'text-emerald-600' : 'text-slate-400' }}">
                                        {{ $product->allow_backorder ? 'Sí' : 'No' }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Logistics & Preparation Details -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700/60 shadow-sm p-6 space-y-6">
                    <h3 class="text-lg font-bold font-['Outfit'] text-slate-800 dark:text-white border-b border-slate-100 dark:border-slate-750 pb-3">Logística y Preparación</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Preparation -->
                        <div class="space-y-4">
                            <div class="flex justify-between items-center py-2 border-b border-slate-50 dark:border-slate-750/30">
                                <span class="text-sm text-slate-500">Requiere Preparación:</span>
                                <span class="text-sm font-bold {{ $product->requires_preparation ? 'text-amber-600' : 'text-slate-400' }}">
                                    {{ $product->requires_preparation ? 'Sí' : 'Entrega Inmediata (No)' }}
                                </span>
                            </div>

                            @if ($product->requires_preparation)
                                <div class="flex justify-between items-center py-2">
                                    <span class="text-sm text-slate-500">Tiempo de Preparación:</span>
                                    <span class="text-sm font-bold text-slate-800 dark:text-white">
                                        {{ $product->preparation_time_minutes ?? 0 }} minutos
                                    </span>
                                </div>
                            @endif
                        </div>

                        <!-- Logistics -->
                        <div class="space-y-4">
                            <div class="flex justify-between items-center py-2">
                                <span class="text-sm text-slate-500">Peso del Producto:</span>
                                <span class="text-sm font-bold text-slate-800 dark:text-white">
                                    {{ $product->weight_grams ? number_format($product->weight_grams) . ' gramos' : 'No registrado' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side: Stats, Gallery & Metadata (1 Col) -->
            <div class="space-y-6">
                <!-- Metrics Card -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700/60 shadow-sm p-5 space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 dark:border-slate-750 pb-2">Estadísticas de Rendimiento</h3>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <!-- Sales -->
                        <div class="bg-slate-50 dark:bg-slate-900/50 p-4 rounded-xl border border-slate-100 dark:border-slate-800">
                            <span class="block text-xs font-semibold text-slate-400">Ventas Totales</span>
                            <span class="block text-xl font-extrabold text-slate-800 dark:text-white mt-1">{{ $product->total_sales }}</span>
                        </div>
                        
                        <!-- Views -->
                        <div class="bg-slate-50 dark:bg-slate-900/50 p-4 rounded-xl border border-slate-100 dark:border-slate-800">
                            <span class="block text-xs font-semibold text-slate-400">Visitas</span>
                            <span class="block text-xl font-extrabold text-slate-800 dark:text-white mt-1">{{ $product->views_count }}</span>
                        </div>
                        
                        <!-- Rating -->
                        <div class="bg-slate-50 dark:bg-slate-900/50 p-4 rounded-xl border border-slate-100 dark:border-slate-800">
                            <span class="block text-xs font-semibold text-slate-400">Calificación Prom.</span>
                            <div class="flex items-center gap-1 mt-1 text-slate-800 dark:text-white">
                                <span class="text-xl font-extrabold">{{ number_format($product->rating_average, 1) }}</span>
                                <svg class="w-4.5 h-4.5 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                            </div>
                        </div>
                        
                        <!-- Reviews -->
                        <div class="bg-slate-50 dark:bg-slate-900/50 p-4 rounded-xl border border-slate-100 dark:border-slate-800">
                            <span class="block text-xs font-semibold text-slate-400">Reseñas</span>
                            <span class="block text-xl font-extrabold text-slate-800 dark:text-white mt-1">{{ $product->total_reviews }}</span>
                        </div>
                    </div>
                </div>

                <!-- Gallery Images Card -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700/60 shadow-sm p-5 space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 dark:border-slate-75 pb-2">Galería de Imágenes</h3>
                    
                    @if ($product->images->isEmpty())
                        <div class="py-8 text-center bg-slate-50/50 dark:bg-slate-900/40 rounded-xl border border-dashed border-slate-200 dark:border-slate-700">
                            <svg class="w-8 h-8 mx-auto text-slate-350 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <span class="block text-xs text-slate-400 font-semibold mt-2">No hay imágenes adicionales</span>
                        </div>
                    @else
                        <div class="grid grid-cols-3 gap-3">
                            @foreach ($product->images as $img)
                                <a href="{{ $img->image_url }}" target="_blank" class="block aspect-square rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-150 dark:border-slate-750 overflow-hidden relative group hover:opacity-85 transition-opacity" title="Ampliar imagen">
                                    <img src="{{ $img->image_url }}" alt="Gallery Image" class="w-full h-full object-cover">
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Date Metadata -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700/60 shadow-sm p-5 space-y-3">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 dark:border-slate-75 pb-2">Fechas de Registro</h3>
                    
                    <div class="text-xs space-y-2 text-slate-650 dark:text-slate-350">
                        <div class="flex justify-between">
                            <span>Registrado el:</span>
                            <span class="font-bold">{{ $product->created_at->format('d/m/Y h:i A') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Última modificación:</span>
                            <span class="font-bold">{{ $product->updated_at->format('d/m/Y h:i A') }}</span>
                        </div>
                        @if ($product->published_at)
                            <div class="flex justify-between text-teal-600 dark:text-teal-400">
                                <span>Publicado el:</span>
                                <span class="font-bold">{{ $product->published_at->format('d/m/Y h:i A') }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Danger Zone Card -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-rose-100 dark:border-rose-900/30 shadow-sm p-5 space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-rose-500 border-b border-rose-50 dark:border-rose-900/20 pb-2">Zona de Peligro</h3>
                    
                    <form method="POST" action="{{ route('seller.products.destroy', $product) }}" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este producto? Esta acción no se puede deshacer.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full py-2.5 rounded-xl border border-rose-200 dark:border-rose-900/50 hover:bg-rose-50 dark:hover:bg-rose-950/20 text-rose-600 dark:text-rose-400 font-bold text-sm transition-colors text-center shadow-sm">
                            Eliminar Producto
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.seller>
