<x-layouts.admin>
    <x-slot:title>Editar Categoría - Nikama Admin</x-slot:title>

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

        <!-- Alerta de Errores de Validación -->
        @if ($errors->any())
            <div class="p-4 bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 rounded-3xl shadow-sm">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-rose-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <p class="font-bold text-sm">Por favor, corrige los siguientes errores:</p>
                        <ul class="list-disc list-inside text-xs opacity-90 mt-1 space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <!-- Card de Formulario (AlpineJS) -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm overflow-hidden"
             x-data="{ 
                name: '{{ old('name', $category->name) }}', 
                slug: '{{ old('slug', $category->slug) }}', 
                manualSlug: true,
                generateSlug() {
                    if (!this.manualSlug) {
                        this.slug = this.name
                            .toLowerCase()
                            .normalize('NFD')
                            .replace(/[\u0300-\u036f]/g, '') // Quitar acentos
                            .trim()
                            .replace(/[^a-z0-9\s-]/g, '')
                            .replace(/[\s_]+/g, '-')
                            .replace(/-+/g, '-')
                            .replace(/^-+|-+$/g, '');
                    }
                }
             }">
            <div class="p-6 border-b border-slate-100 dark:border-slate-700">
                <h3 class="text-xl font-extrabold font-['Outfit'] text-slate-800 dark:text-white">Editar Categoría</h3>
                <p class="text-xs text-slate-400 mt-1">Modifica los detalles y posición en el árbol de la categoría '{{ $category->name }}'.</p>
            </div>

            <form action="{{ route('admin.categories.update', $category) }}" method="POST" class="p-6 space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nombre -->
                    <div>
                        <label for="name" class="text-xs font-bold uppercase tracking-wider text-slate-450 block mb-2">Nombre de Categoría *</label>
                        <input type="text" name="name" id="name" required placeholder="Ej: Farmacias, Bebidas, Pizzas"
                               x-model="name" @input="generateSlug()"
                               class="w-full px-4 py-3 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                        @error('name')
                            <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Slug -->
                    <div>
                        <label for="slug" class="text-xs font-bold uppercase tracking-wider text-slate-450 block mb-2">Slug Único *</label>
                        <input type="text" name="slug" id="slug" required placeholder="Ej: farmacias, bebidas, pizzas-artesanales"
                               x-model="slug" @input="manualSlug = true"
                               class="w-full px-4 py-3 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm font-mono text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                        <span class="text-slate-400 text-[10px] block mt-1">Slug identificador de URL. Debe ser único.</span>
                        @error('slug')
                            <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Categoría Padre -->
                    <div>
                        <label for="parent_id" class="text-xs font-bold uppercase tracking-wider text-slate-450 block mb-2">Categoría Padre (Ubicación en el árbol)</label>
                        <select name="parent_id" id="parent_id"
                                class="w-full px-4 py-3 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                            <option value="">Ninguno (Categoría Raíz / Sector General)</option>
                            @foreach ($parentCategories as $parent)
                                <option value="{{ $parent->id }}" {{ old('parent_id', $category->parent_id) === $parent->id ? 'selected' : '' }}>
                                    {{ $parent->parent_id ? '  ' : '' }}{{ $parent->name }}
                                </option>
                            @endforeach
                        </select>
                        
                        <!-- Mensaje Informativo de Exclusión de Nodos Circulares -->
                        <div class="mt-2 p-3 bg-amber-50/50 dark:bg-amber-950/10 border border-amber-100 dark:border-amber-900/30 rounded-xl flex gap-2 items-start">
                            <svg class="w-4 h-4 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-[10px] text-slate-500 dark:text-slate-400 leading-normal">
                                Para evitar referencias circulares en el árbol jerárquico, no se muestran como opciones la propia categoría ni sus subcategorías descendientes.
                            </span>
                        </div>
                        @error('parent_id')
                            <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Orden de clasificación -->
                    <div>
                        <label for="sort_order" class="text-xs font-bold uppercase tracking-wider text-slate-450 block mb-2">Orden de Clasificación</label>
                        <input type="number" name="sort_order" id="sort_order" min="0" value="{{ old('sort_order', $category->sort_order) }}"
                               class="w-full px-4 py-3 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                        <span class="text-slate-400 text-[10px] block mt-1">Controla el orden visual de aparición de las categorías (0 es el primero).</span>
                        @error('sort_order')
                            <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Icono o Identificador -->
                    <div>
                        <label for="icon" class="text-xs font-bold uppercase tracking-wider text-slate-450 block mb-2">Icono / Emoji</label>
                        <input type="text" name="icon" id="icon" value="{{ old('icon', $category->icon) }}" placeholder="Ej: 🍔, 💊, 🍺"
                               class="w-full px-4 py-3 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                        <span class="text-slate-400 text-[10px] block mt-1">Puedes ingresar un emoji o una clase de icono representativo.</span>
                        @error('icon')
                            <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- URL de Imagen -->
                    <div>
                        <label for="image_url" class="text-xs font-bold uppercase tracking-wider text-slate-450 block mb-2">URL de Imagen</label>
                        <input type="url" name="image_url" id="image_url" value="{{ old('image_url', $category->image_url) }}" placeholder="https://ejemplo.com/imagen.jpg"
                               class="w-full px-4 py-3 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                        <span class="text-slate-400 text-[10px] block mt-1">Enlace opcional para una imagen de portada.</span>
                        @error('image_url')
                            <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Descripción -->
                <div>
                    <label for="description" class="text-xs font-bold uppercase tracking-wider text-slate-450 block mb-2">Descripción</label>
                    <textarea id="description" name="description" rows="4" placeholder="Describe brevemente el alcance de esta categoría..."
                              class="w-full px-4 py-3 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">{{ old('description', $category->description) }}</textarea>
                    @error('description')
                        <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Estado Activo Toggle -->
                <div class="flex items-center gap-3 pt-2">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $category->is_active ? '1' : '0') == '1' ? 'checked' : '' }}
                           class="w-4 h-4 rounded text-luffy-red focus:ring-luffy-red border-slate-300">
                    <label for="is_active" class="text-sm font-bold text-slate-750 dark:text-slate-350 select-none">Habilitar esta categoría inmediatamente</label>
                </div>

                <!-- Botones -->
                <div class="flex gap-4 justify-end pt-4 border-t border-slate-100 dark:border-slate-700">
                    <a href="{{ route('admin.categories.index') }}" 
                       class="px-6 py-3 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-bold text-sm rounded-2xl hover:bg-slate-50 dark:hover:bg-slate-750 transition-colors">
                        Cancelar
                    </a>
                    <button type="submit" class="px-6 py-3 bg-luffy-red hover:bg-luffy-red/90 text-white font-bold text-sm rounded-2xl shadow-lg shadow-luffy-red/25 hover:shadow-luffy-red/35 transition-all">
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>
