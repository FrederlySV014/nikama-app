<x-layouts.admin>
    <x-slot:title>Detalle de Categoría - Nikama Admin</x-slot:title>

    <div class="max-w-3xl mx-auto space-y-6">
        <!-- Volver -->
        <div>
            <a href="{{ route('admin.categories.index') }}" 
               class="inline-flex items-center gap-2 text-sm font-bold text-slate-500 hover:text-slate-800 dark:hover:text-white transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Volver al listado
            </a>
        </div>

        <!-- Alerta de Operación -->
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

        <!-- Card de Detalles -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm overflow-hidden">
            <!-- Portada / Portada por defecto -->
            <div class="relative h-48 bg-slate-900 flex items-center justify-center overflow-hidden">
                @if ($category->image_url)
                    <img src="{{ $category->image_url }}" alt="Imagen de {{ $category->name }}" class="absolute inset-0 w-full h-full object-cover opacity-60">
                @else
                    <div class="absolute inset-0 bg-gradient-to-r from-slate-950 to-slate-850 opacity-95"></div>
                    <div class="absolute text-slate-650 text-sm font-semibold uppercase tracking-wider">Sin imagen de portada</div>
                @endif

                <!-- Badge de Estado -->
                <div class="absolute top-6 right-6">
                    @if ($category->is_active)
                        <span class="px-4 py-2 rounded-full text-xs font-black uppercase tracking-wider bg-emerald-500 text-white shadow-lg shadow-emerald-500/20">Activa</span>
                    @else
                        <span class="px-4 py-2 rounded-full text-xs font-black uppercase tracking-wider bg-rose-500 text-white shadow-lg shadow-rose-500/20">Inactiva</span>
                    @endif
                </div>
            </div>

            <div class="p-8">
                <!-- Info Principal -->
                <div class="space-y-6">
                    <div>
                        <div class="flex items-center gap-3">
                            @if ($category->icon)
                                <span class="text-3xl" title="Icono">{{ $category->icon }}</span>
                            @endif
                            <h1 class="text-3xl font-extrabold font-['Outfit'] text-slate-800 dark:text-white">{{ $category->name }}</h1>
                        </div>
                        <p class="text-sm text-slate-400 mt-1 font-mono">Slug: {{ $category->slug }}</p>
                    </div>

                    <!-- Datos Generales Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-slate-100 dark:border-slate-700">
                        <div class="space-y-4">
                            <h3 class="text-xs font-black uppercase tracking-wider text-slate-400">Jerarquía y Estructura</h3>
                            
                            <div>
                                <span class="text-xs text-slate-400 block">Categoría Padre</span>
                                @if ($category->parent)
                                    <a href="{{ route('admin.categories.show', $category->parent) }}" class="text-sm font-semibold text-luffy-red hover:underline inline-flex items-center gap-1.5 mt-0.5">
                                        {{ $category->parent->icon }} {{ $category->parent->name }}
                                    </a>
                                @else
                                    <span class="text-sm font-semibold text-slate-800 dark:text-slate-200 mt-0.5 block italic text-slate-400">Categoría Raíz (Sector General)</span>
                                @endif
                            </div>

                            <div>
                                <span class="text-xs text-slate-400 block">Clasificación / Orden</span>
                                <span class="text-sm font-semibold text-slate-800 dark:text-slate-200 block mt-0.5">Posición #{{ $category->sort_order }}</span>
                            </div>

                            <div>
                                <span class="text-xs text-slate-400 block">Subcategorías Hijas</span>
                                <span class="text-sm font-semibold text-slate-800 dark:text-slate-200 block mt-0.5">{{ $category->children()->count() }} subcategorías</span>
                            </div>
                        </div>

                        <div class="space-y-4 border-t md:border-t-0 md:border-l border-slate-100 dark:border-slate-700 md:pl-6">
                            <h3 class="text-xs font-black uppercase tracking-wider text-slate-400">Asociaciones Activas</h3>
                            
                            <div>
                                <span class="text-xs text-slate-400 block">Negocios Registrados</span>
                                <span class="text-sm font-bold text-slate-800 dark:text-slate-200 block mt-0.5">{{ $category->businesses_count }} negocios asociados</span>
                            </div>

                            <div>
                                <span class="text-xs text-slate-400 block">Productos Catalogados</span>
                                <span class="text-sm font-bold text-slate-800 dark:text-slate-200 block mt-0.5">{{ $category->products_count }} productos asociados</span>
                            </div>
                        </div>
                    </div>

                    <!-- Descripción -->
                    <div class="pt-4 border-t border-slate-100 dark:border-slate-700">
                        <h3 class="text-xs font-black uppercase tracking-wider text-slate-400 mb-2">Descripción de Alcance</h3>
                        <p class="text-slate-650 dark:text-slate-300 text-sm leading-relaxed bg-slate-50 dark:bg-slate-900/50 p-4 rounded-2xl border border-slate-100 dark:border-slate-800">
                            {{ $category->description ?? 'Esta categoría no tiene una descripción detallada.' }}
                        </p>
                    </div>

                    <!-- Listado de Subcategorías hijas -->
                    @if ($category->children->isNotEmpty())
                        <div class="pt-4 border-t border-slate-100 dark:border-slate-700">
                            <h3 class="text-xs font-black uppercase tracking-wider text-slate-400 mb-3">Subcategorías Hijas Directas</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($category->children as $child)
                                    <a href="{{ route('admin.categories.show', $child) }}" 
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-750 text-slate-700 dark:text-slate-300 transition-colors">
                                        @if ($child->icon)
                                            <span>{{ $child->icon }}</span>
                                        @endif
                                        <span>{{ $child->name }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Acciones Inferiores -->
                    <div class="pt-8 border-t border-slate-100 dark:border-slate-700 flex flex-col sm:flex-row gap-4 items-center justify-end">
                        <form action="{{ route('admin.categories.toggle', $category) }}" method="POST" class="w-full sm:w-auto">
                            @csrf
                            <button type="submit" class="w-full sm:w-auto px-6 py-3 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-bold text-sm rounded-2xl hover:bg-slate-50 dark:hover:bg-slate-750 transition-colors">
                                {{ $category->is_active ? 'Desactivar Categoría' : 'Activar Categoría' }}
                            </button>
                        </form>

                        <a href="{{ route('admin.categories.edit', $category) }}" 
                           class="w-full sm:w-auto px-6 py-3 bg-slate-800 hover:bg-slate-700 text-white text-center font-bold text-sm rounded-2xl transition-colors">
                            Editar Información
                        </a>

                        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="w-full sm:w-auto"
                              onsubmit="return confirm('¿Estás seguro de que deseas eliminar esta categoría?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full sm:w-auto px-6 py-3 bg-rose-500 hover:bg-rose-600 text-white font-bold text-sm rounded-2xl shadow-lg shadow-rose-500/25 transition-all">
                                Eliminar Categoría
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
