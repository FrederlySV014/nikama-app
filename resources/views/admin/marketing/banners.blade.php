<x-layouts.admin>
    <x-slot:title>Banners Promocionales - Nikama Admin</x-slot:title>

    <div class="space-y-6" x-data="{ 
        showCreateModal: false, 
        actionType: 'external_link',
        showEditModal: false,
        editBannerId: '',
        editTitle: '',
        editImageUrl: '',
        editActionType: '',
        editActionId: '',
        editActionUrl: '',
        editSortOrder: '',
        editStartsAt: '',
        editExpiresAt: '',
        updateUrl: '',
        deleteUrl: ''
    }">
        <!-- Header -->
        <div class="bg-gradient-to-r from-luffy-red/20 to-rose-500/10 p-8 rounded-3xl border border-luffy-red/30 shadow-sm transition-colors duration-300">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-3xl font-extrabold font-['Outfit'] text-slate-800 dark:text-white">Banners Promocionales</h2>
                    <p class="text-slate-650 dark:text-slate-300 mt-2 font-medium">Gestiona los banners de publicidad y enlaces destacados que se muestran en el catálogo principal de los clientes.</p>
                </div>
                <button @click="showCreateModal = true" class="px-5 py-3 bg-gradient-to-r from-luffy-red to-rose-500 hover:from-luffy-red/90 hover:to-rose-500/90 text-white font-extrabold text-sm uppercase tracking-wider rounded-2xl transition shadow-lg shadow-luffy-red/20 cursor-pointer self-start md:self-auto flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                    Nuevo Banner
                </button>
            </div>
        </div>

        <!-- Alertas -->
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

        @if ($errors->any())
            <div class="p-4 bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-350 rounded-3xl flex items-start gap-3 shadow-sm">
                <svg class="w-5 h-5 text-rose-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <p class="font-bold text-sm">Hubo errores en el formulario</p>
                    <ul class="list-disc list-inside text-xs opacity-90 mt-1 space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <!-- Listado en Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($bannersList as $banner)
                <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/80 shadow-sm overflow-hidden flex flex-col justify-between group hover:-translate-y-1 transition duration-300">
                    <div>
                        <!-- Preview Imagen -->
                        <div class="relative h-44 bg-slate-900 overflow-hidden">
                            <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500 opacity-90">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 to-transparent"></div>
                            
                            <!-- Badge de Orden y Estado -->
                            <div class="absolute top-4 left-4 flex gap-2">
                                <span class="px-2.5 py-1 bg-slate-950/60 backdrop-blur-md text-[10px] font-bold text-white rounded-lg border border-white/10">
                                    Prioridad: {{ $banner->sort_order }}
                                </span>
                                @php
                                    $isActive = $banner->is_active && $banner->starts_at->isPast() && $banner->expires_at->isFuture();
                                    $badgeClass = $isActive 
                                        ? 'bg-emerald-500/80 text-white border border-emerald-400/20' 
                                        : 'bg-rose-500/80 text-white border border-rose-400/20';
                                    $badgeLabel = $isActive ? 'Activo' : 'Inactivo';
                                @endphp
                                <span class="px-2.5 py-1 backdrop-blur-md text-[10px] font-bold rounded-lg {{ $badgeClass }}">
                                    {{ $badgeLabel }}
                                </span>
                            </div>
                        </div>

                        <!-- Info -->
                        <div class="p-6 space-y-3">
                            <h3 class="text-lg font-black font-['Outfit'] text-slate-855 dark:text-white leading-tight">
                                {{ $banner->title }}
                            </h3>
                            
                            <div class="space-y-1.5 text-xs text-slate-650 dark:text-slate-400 font-semibold">
                                <div class="flex items-center gap-1.5">
                                    <span class="text-slate-400">Acción:</span>
                                    <span class="px-2 py-0.5 bg-slate-100 dark:bg-slate-900 rounded-lg text-slate-700 dark:text-slate-300 font-bold uppercase text-[9px] tracking-wider">
                                        {{ str_replace('_', ' ', $banner->action_type) }}
                                    </span>
                                </div>
                                @if($banner->action_type === 'external_link')
                                    <div class="flex items-center gap-1.5 truncate">
                                        <span class="text-slate-400">Enlace:</span>
                                        <a href="{{ $banner->action_url }}" target="_blank" class="text-luffy-red hover:underline font-bold truncate">
                                            {{ $banner->action_url }}
                                        </a>
                                    </div>
                                @else
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-slate-400">ID Destino:</span>
                                        <span class="font-mono text-slate-800 dark:text-slate-200">{{ $banner->action_id }}</span>
                                    </div>
                                @endif
                                <div class="text-[11px] text-slate-400 mt-2 font-medium flex flex-col gap-0.5">
                                    <span>Inicio: {{ $banner->starts_at->format('d/m/Y H:i') }}</span>
                                    <span>Fin: {{ $banner->expires_at->format('d/m/Y H:i') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer / Acciones -->
                    <div class="p-6 pt-0 border-t border-slate-50 dark:border-slate-700/40 mt-4 flex items-center justify-between gap-2">
                        <form action="{{ route('admin.marketing.banners.toggle', $banner) }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit" class="w-full py-2.5 rounded-xl border font-bold text-xs uppercase tracking-wider transition duration-200 cursor-pointer text-center
                                {{ $banner->is_active 
                                    ? 'border-amber-250 dark:border-amber-900/40 text-amber-600 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-950/20' 
                                    : 'border-emerald-250 dark:border-emerald-900/40 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950/20' }}">
                                {{ $banner->is_active ? 'Desactivar' : 'Activar' }}
                            </button>
                        </form>
                        <button @click="
                            editBannerId = '{{ $banner->id }}';
                            editTitle = '{{ addslashes($banner->title) }}';
                            editImageUrl = '{{ $banner->image_url }}';
                            editActionType = '{{ $banner->action_type }}';
                            editActionId = '{{ $banner->action_id }}';
                            editActionUrl = '{{ $banner->action_url }}';
                            editSortOrder = '{{ $banner->sort_order }}';
                            editStartsAt = '{{ $banner->starts_at->format('Y-m-d\TH:i') }}';
                            editExpiresAt = '{{ $banner->expires_at->format('Y-m-d\TH:i') }}';
                            updateUrl = '{{ route('admin.marketing.banners.update', $banner) }}';
                            deleteUrl = '{{ route('admin.marketing.banners.destroy', $banner) }}';
                            showEditModal = true;
                        " class="px-3 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-650 text-slate-700 dark:text-slate-350 font-bold text-xs uppercase tracking-wider rounded-xl transition cursor-pointer">
                            Editar
                        </button>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white dark:bg-slate-800 p-16 text-center text-slate-500 dark:text-slate-400 border border-slate-100 dark:border-slate-700 rounded-3xl">
                    <svg class="w-16 h-16 mx-auto mb-4 text-slate-300 dark:text-slate-600 animate-pulse" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <p class="text-base font-black font-['Outfit'] text-slate-700 dark:text-slate-300">No hay banners promocionales registrados</p>
                    <p class="text-xs text-slate-400 mt-1">Crea tu primer banner promocional usando el botón de arriba.</p>
                </div>
            @endforelse
        </div>

        <!-- Paginación -->
        @if($bannersList->hasPages())
            <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 border border-slate-100 dark:border-slate-700 flex items-center justify-between shadow-sm">
                <div class="text-xs text-slate-450 dark:text-slate-400 font-medium">
                    Mostrando {{ $bannersList->firstItem() }} al {{ $bannersList->lastItem() }} de {{ $bannersList->total() }} registros.
                </div>
                <div>
                    {{ $bannersList->links() }}
                </div>
            </div>
        @endif

        <!-- Modal de Creación de Banner -->
        <div x-show="showCreateModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-transition>
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="showCreateModal = false">
                    <div class="absolute inset-0 bg-slate-950/70 backdrop-blur-sm"></div>
                </div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-3xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full border border-slate-200 dark:border-slate-700">
                    <form action="{{ route('admin.marketing.banners.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                        @csrf

                        <div>
                            <h3 class="text-xl font-black font-['Outfit'] text-slate-855 dark:text-white">Registrar Banner Promocional</h3>
                            <p class="text-xs text-slate-450 dark:text-slate-400 mt-1">Crea un banner visual destacado en el catálogo principal.</p>
                        </div>

                        <!-- Título -->
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Título del Banner</label>
                            <input type="text" name="title" required placeholder="Ej. 50% de Descuento en tu primer pedido"
                                   class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-955 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                        </div>

                        <!-- Selector de Imagen -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Subir Archivo de Imagen</label>
                                <input type="file" name="image" accept="image/*"
                                       class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-rose-50 file:text-rose-700 hover:file:bg-rose-100 dark:file:bg-slate-900 dark:file:text-slate-300">
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">O URL de Imagen Pública</label>
                                <input type="url" name="image_url" placeholder="https://ejemplo.com/banner.jpg"
                                       class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-955 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                            </div>
                        </div>

                        <!-- Tipo de Acción -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Tipo de Acción al Clic</label>
                                <select name="action_type" x-model="actionType" required
                                        class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-955 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                                    <option value="external_link">Abrir Enlace Externo</option>
                                    <option value="open_business">Abrir Comercio / Negocio</option>
                                    <option value="open_category">Abrir Categoría</option>
                                    <option value="open_product">Abrir Producto Oficial</option>
                                </select>
                            </div>

                            <div class="space-y-1">
                                <!-- Enlace Externo -->
                                <div x-show="actionType === 'external_link'">
                                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">URL Externa</label>
                                    <input type="url" name="action_url" placeholder="https://ejemplo.com/promocion"
                                           class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-955 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                                </div>

                                <!-- Comercio -->
                                <div x-show="actionType === 'open_business'" style="display: none;">
                                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Seleccionar Comercio</label>
                                    <select name="action_id" class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-955 text-sm text-slate-850 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                                        <option value="">-- Elige un Comercio --</option>
                                        @foreach($businesses as $business)
                                            <option value="{{ $business->id }}">{{ $business->business_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Categoría -->
                                <div x-show="actionType === 'open_category'" style="display: none;">
                                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Seleccionar Categoría</label>
                                    <select name="action_id" class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-955 text-sm text-slate-855 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                                        <option value="">-- Elige una Categoría --</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Producto -->
                                <div x-show="actionType === 'open_product'" style="display: none;">
                                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Seleccionar Producto</label>
                                    <select name="action_id" class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-955 text-sm text-slate-855 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                                        <option value="">-- Elige un Producto --</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Prioridad y Rango de Fechas -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Prioridad de Orden</label>
                                <input type="number" name="sort_order" required min="0" value="0"
                                       class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-955 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all font-semibold">
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Fecha Inicio</label>
                                <input type="datetime-local" name="starts_at" required
                                       class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-955 text-sm text-slate-850 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Fecha Expiración</label>
                                <input type="datetime-local" name="expires_at" required
                                       class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-955 text-sm text-slate-855 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                            </div>
                        </div>

                        <!-- Botones Acción -->
                        <div class="flex items-center justify-end gap-3 pt-2">
                            <button type="button" @click="showCreateModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-650 text-slate-700 dark:text-slate-300 font-bold text-xs uppercase tracking-wider rounded-xl transition cursor-pointer">
                                Cancelar
                            </button>
                            <button type="submit" class="px-5 py-2 bg-gradient-to-r from-luffy-red to-rose-500 hover:from-luffy-red/90 hover:to-rose-500/90 text-white font-extrabold text-xs uppercase tracking-wider rounded-xl transition shadow-md shadow-luffy-red/10 cursor-pointer">
                                Registrar Banner
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal de Edición de Banner -->
        <div x-show="showEditModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-transition>
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="showEditModal = false">
                    <div class="absolute inset-0 bg-slate-950/70 backdrop-blur-sm"></div>
                </div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-3xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full border border-slate-200 dark:border-slate-700">
                    <div class="p-6">
                        <div class="flex items-center justify-between pb-3">
                            <h3 class="text-xl font-black font-['Outfit'] text-slate-855 dark:text-white">Modificar Banner</h3>
                            <!-- Botón Eliminar en el encabezado para comodidad -->
                            <form :action="deleteUrl" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este banner por completo? Esta acción es irreversible.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1.5 bg-rose-500 hover:bg-rose-600 text-white text-[10px] font-bold uppercase tracking-wider rounded-xl transition cursor-pointer">
                                    Eliminar
                                </button>
                            </form>
                        </div>
                        <p class="text-xs text-slate-450 dark:text-slate-400 mt-1 mb-4">Actualiza la configuración o elimina el banner permanentemente.</p>

                        <form :action="updateUrl" method="POST" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            @method('PUT')

                            <!-- Título -->
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Título del Banner</label>
                                <input type="text" name="title" required x-model="editTitle"
                                       class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-955 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                            </div>

                            <!-- Selector de Imagen -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-1">
                                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Cambiar Archivo de Imagen</label>
                                    <input type="file" name="image" accept="image/*"
                                           class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-rose-50 file:text-rose-700 hover:file:bg-rose-100 dark:file:bg-slate-900 dark:file:text-slate-300">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">O URL de Imagen</label>
                                    <input type="url" name="image_url" x-model="editImageUrl" placeholder="https://ejemplo.com/banner.jpg"
                                           class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-955 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                                </div>
                            </div>

                            <!-- Tipo de Acción -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-1">
                                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Tipo de Acción al Clic</label>
                                    <select name="action_type" x-model="editActionType" required
                                            class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-955 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                                        <option value="external_link">Abrir Enlace Externo</option>
                                        <option value="open_business">Abrir Comercio / Negocio</option>
                                        <option value="open_category">Abrir Categoría</option>
                                        <option value="open_product">Abrir Producto Oficial</option>
                                    </select>
                                </div>

                                <div class="space-y-1">
                                    <!-- Enlace Externo -->
                                    <div x-show="editActionType === 'external_link'">
                                        <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">URL Externa</label>
                                        <input type="url" name="action_url" x-model="editActionUrl" placeholder="https://ejemplo.com/promocion"
                                               class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-955 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                                    </div>

                                    <!-- Comercio -->
                                    <div x-show="editActionType === 'open_business'" style="display: none;">
                                        <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Seleccionar Comercio</label>
                                        <select name="action_id" x-model="editActionId" class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-955 text-sm text-slate-850 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                                            <option value="">-- Elige un Comercio --</option>
                                            @foreach($businesses as $business)
                                                <option value="{{ $business->id }}">{{ $business->business_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Categoría -->
                                    <div x-show="editActionType === 'open_category'" style="display: none;">
                                        <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Seleccionar Categoría</label>
                                        <select name="action_id" x-model="editActionId" class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-955 text-sm text-slate-855 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                                            <option value="">-- Elige una Categoría --</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Producto -->
                                    <div x-show="editActionType === 'open_product'" style="display: none;">
                                        <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Seleccionar Producto</label>
                                        <select name="action_id" x-model="editActionId" class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-955 text-sm text-slate-855 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                                            <option value="">-- Elige un Producto --</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Prioridad y Rango de Fechas -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="space-y-1">
                                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Prioridad de Orden</label>
                                    <input type="number" name="sort_order" required min="0" x-model="editSortOrder"
                                           class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-955 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all font-semibold">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Fecha Inicio</label>
                                    <input type="datetime-local" name="starts_at" required x-model="editStartsAt"
                                           class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-955 text-sm text-slate-850 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Fecha Expiración</label>
                                    <input type="datetime-local" name="expires_at" required x-model="editExpiresAt"
                                           class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-955 text-sm text-slate-855 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                                </div>
                            </div>

                            <!-- Botones Acción -->
                            <div class="flex items-center justify-end gap-3 pt-2">
                                <button type="button" @click="showEditModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-650 text-slate-700 dark:text-slate-300 font-bold text-xs uppercase tracking-wider rounded-xl transition cursor-pointer">
                                    Cancelar
                                </button>
                                <button type="submit" class="px-5 py-2 bg-emerald-500 hover:bg-emerald-600 text-white font-extrabold text-xs uppercase tracking-wider rounded-xl transition shadow-md cursor-pointer">
                                    Guardar Cambios
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
