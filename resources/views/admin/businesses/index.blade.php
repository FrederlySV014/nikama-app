<x-layouts.admin>
    <x-slot:title>Gestión de Negocios - Nikama Admin</x-slot:title>

    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-gradient-to-r from-luffy-red/20 to-rose-500/10 p-8 rounded-3xl border border-luffy-red/30 shadow-sm transition-colors duration-300">
            <h2 class="text-3xl font-extrabold font-['Outfit'] text-slate-800 dark:text-white">Gestión de Negocios</h2>
            <p class="text-slate-650 dark:text-slate-300 mt-2 font-medium">Administra y configura los comercios, su visibilidad, su estado de operaciones y sus características destacadas.</p>
        </div>

        <!-- Alertas -->
        @if (session('success'))
            <div class="p-4 bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 rounded-3xl flex items-start gap-3 shadow-sm">
                <svg class="w-5 h-5 text-emerald-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <p class="font-extrabold text-sm">Operación exitosa</p>
                    <p class="text-xs opacity-90 mt-0.5">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if (session('warning'))
            <div class="p-4 bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800 text-amber-800 dark:text-amber-300 rounded-3xl flex items-start gap-3 shadow-sm">
                <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <div>
                    <p class="font-extrabold text-sm">Alerta del sistema</p>
                    <p class="text-xs opacity-90 mt-0.5">{{ session('warning') }}</p>
                </div>
            </div>
        @endif

        <!-- Tarjetas de Estadísticas -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <div class="bg-white dark:bg-slate-800 p-6 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm flex items-center justify-between transition-all duration-300 hover:shadow-md hover:-translate-y-0.5">
                <div>
                    <span class="text-slate-400 dark:text-slate-400 text-xs font-bold uppercase tracking-wider block">Aprobados</span>
                    <span class="text-3xl font-extrabold font-['Outfit'] text-slate-800 dark:text-white block mt-1">{{ $approvedCount }}</span>
                </div>
                <div class="p-3 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-500 dark:text-emerald-400 rounded-2xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-800 p-6 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm flex items-center justify-between transition-all duration-300 hover:shadow-md hover:-translate-y-0.5">
                <div>
                    <span class="text-slate-400 dark:text-slate-400 text-xs font-bold uppercase tracking-wider block">Pendientes</span>
                    <span class="text-3xl font-extrabold font-['Outfit'] text-slate-800 dark:text-white block mt-1">{{ $pendingCount }}</span>
                </div>
                <div class="p-3 bg-amber-50 dark:bg-amber-950/40 text-amber-500 dark:text-amber-400 rounded-2xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-800 p-6 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm flex items-center justify-between transition-all duration-300 hover:shadow-md hover:-translate-y-0.5">
                <div>
                    <span class="text-slate-400 dark:text-slate-400 text-xs font-bold uppercase tracking-wider block">Suspendidos</span>
                    <span class="text-3xl font-extrabold font-['Outfit'] text-slate-800 dark:text-white block mt-1">{{ $suspendedCount }}</span>
                </div>
                <div class="p-3 bg-red-50 dark:bg-red-950/40 text-red-500 dark:text-red-400 rounded-2xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                    </svg>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-800 p-6 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm flex items-center justify-between transition-all duration-300 hover:shadow-md hover:-translate-y-0.5">
                <div>
                    <span class="text-slate-400 dark:text-slate-400 text-xs font-bold uppercase tracking-wider block">Rechazados</span>
                    <span class="text-3xl font-extrabold font-['Outfit'] text-slate-800 dark:text-white block mt-1">{{ $rejectedCount }}</span>
                </div>
                <div class="p-3 bg-rose-50 dark:bg-rose-950/40 text-rose-500 dark:text-rose-455 rounded-2xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Filtros y Listado -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/80 shadow-sm overflow-hidden transition-colors duration-300">
            <!-- Pestañas de Estado (Tabs) -->
            <div class="flex flex-wrap border-b border-slate-100 dark:border-slate-700/60 bg-slate-50 dark:bg-slate-900/20">
                <a href="{{ route('admin.businesses.index', ['tab' => 'all', 'active' => $active, 'featured' => $featured, 'search' => $search]) }}" 
                   class="flex items-center gap-2 px-6 py-4 border-b-2 font-bold text-sm transition-all duration-200 {{ $tab === 'all' ? 'border-luffy-red text-luffy-red dark:text-rose-455 bg-white dark:bg-slate-800' : 'border-transparent text-slate-500 hover:text-slate-800 dark:hover:text-white hover:bg-slate-100/50 dark:hover:bg-slate-700/20' }}">
                    <span>Todos</span>
                </a>
                <a href="{{ route('admin.businesses.index', ['tab' => 'approved', 'active' => $active, 'featured' => $featured, 'search' => $search]) }}" 
                   class="flex items-center gap-2 px-6 py-4 border-b-2 font-bold text-sm transition-all duration-200 {{ $tab === 'approved' ? 'border-luffy-red text-luffy-red dark:text-rose-455 bg-white dark:bg-slate-800' : 'border-transparent text-slate-500 hover:text-slate-800 dark:hover:text-white hover:bg-slate-100/50 dark:hover:bg-slate-700/20' }}">
                    <span>Aprobados</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 border border-emerald-250 dark:border-emerald-900/30">{{ $approvedCount }}</span>
                </a>
                <a href="{{ route('admin.businesses.index', ['tab' => 'pending', 'active' => $active, 'featured' => $featured, 'search' => $search]) }}" 
                   class="flex items-center gap-2 px-6 py-4 border-b-2 font-bold text-sm transition-all duration-200 {{ $tab === 'pending' ? 'border-luffy-red text-luffy-red dark:text-rose-455 bg-white dark:bg-slate-800' : 'border-transparent text-slate-500 hover:text-slate-800 dark:hover:text-white hover:bg-slate-100/50 dark:hover:bg-slate-700/20' }}">
                    <span>Pendientes</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 border border-amber-250 dark:border-amber-900/30">{{ $pendingCount }}</span>
                </a>
                <a href="{{ route('admin.businesses.index', ['tab' => 'suspended', 'active' => $active, 'featured' => $featured, 'search' => $search]) }}" 
                   class="flex items-center gap-2 px-6 py-4 border-b-2 font-bold text-sm transition-all duration-200 {{ $tab === 'suspended' ? 'border-luffy-red text-luffy-red dark:text-rose-455 bg-white dark:bg-slate-800' : 'border-transparent text-slate-500 hover:text-slate-800 dark:hover:text-white hover:bg-slate-100/50 dark:hover:bg-slate-700/20' }}">
                    <span>Suspendidos</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 dark:bg-red-950/40 text-red-600 dark:text-red-400 border border-red-250 dark:border-red-900/30">{{ $suspendedCount }}</span>
                </a>
                <a href="{{ route('admin.businesses.index', ['tab' => 'rejected', 'active' => $active, 'featured' => $featured, 'search' => $search]) }}" 
                   class="flex items-center gap-2 px-6 py-4 border-b-2 font-bold text-sm transition-all duration-200 {{ $tab === 'rejected' ? 'border-luffy-red text-luffy-red dark:text-rose-455 bg-white dark:bg-slate-800' : 'border-transparent text-slate-500 hover:text-slate-800 dark:hover:text-white hover:bg-slate-100/50 dark:hover:bg-slate-700/20' }}">
                    <span>Rechazados</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 border border-rose-250 dark:border-rose-900/30">{{ $rejectedCount }}</span>
                </a>
            </div>

            <!-- Filtros Secundarios y Buscador -->
            <div class="p-6 flex flex-col xl:flex-row gap-4 items-center justify-between border-b border-slate-100 dark:border-slate-700/60">
                <div class="flex flex-wrap gap-4 items-center w-full xl:w-auto">
                    <!-- Filtro por Activo -->
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Visibilidad:</span>
                        <div class="flex gap-1">
                            <a href="{{ route('admin.businesses.index', ['tab' => $tab, 'active' => 'all', 'featured' => $featured, 'search' => $search]) }}" 
                               class="px-3 py-1.5 rounded-lg text-[11px] font-black uppercase tracking-wider transition {{ $active === 'all' ? 'bg-slate-800 dark:bg-slate-900 text-white' : 'bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-750 text-slate-600 dark:text-slate-300' }}">
                                Todos
                            </a>
                            <a href="{{ route('admin.businesses.index', ['tab' => $tab, 'active' => 'active', 'featured' => $featured, 'search' => $search]) }}" 
                               class="px-3 py-1.5 rounded-lg text-[11px] font-black uppercase tracking-wider transition {{ $active === 'active' ? 'bg-emerald-500 text-white' : 'bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-750 text-slate-600 dark:text-slate-300' }}">
                                Activos
                            </a>
                            <a href="{{ route('admin.businesses.index', ['tab' => $tab, 'active' => 'inactive', 'featured' => $featured, 'search' => $search]) }}" 
                               class="px-3 py-1.5 rounded-lg text-[11px] font-black uppercase tracking-wider transition {{ $active === 'inactive' ? 'bg-amber-500 text-white' : 'bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-750 text-slate-600 dark:text-slate-300' }}">
                                Inactivos
                            </a>
                        </div>
                    </div>

                    <!-- Filtro por Destacados -->
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Destacados:</span>
                        <div class="flex gap-1">
                            <a href="{{ route('admin.businesses.index', ['tab' => $tab, 'active' => $active, 'featured' => 'all', 'search' => $search]) }}" 
                               class="px-3 py-1.5 rounded-lg text-[11px] font-black uppercase tracking-wider transition {{ $featured === 'all' ? 'bg-slate-800 dark:bg-slate-900 text-white' : 'bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-750 text-slate-600 dark:text-slate-300' }}">
                                Todos
                            </a>
                            <a href="{{ route('admin.businesses.index', ['tab' => $tab, 'active' => $active, 'featured' => 'featured', 'search' => $search]) }}" 
                               class="px-3 py-1.5 rounded-lg text-[11px] font-black uppercase tracking-wider transition {{ $featured === 'featured' ? 'bg-violet-500 text-white' : 'bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-750 text-slate-600 dark:text-slate-300' }}">
                                Destacados
                            </a>
                            <a href="{{ route('admin.businesses.index', ['tab' => $tab, 'active' => $active, 'featured' => 'standard', 'search' => $search]) }}" 
                               class="px-3 py-1.5 rounded-lg text-[11px] font-black uppercase tracking-wider transition {{ $featured === 'standard' ? 'bg-slate-800 dark:bg-slate-900 text-white' : 'bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-750 text-slate-600 dark:text-slate-300' }}">
                                Estándar
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Buscador -->
                <form action="{{ route('admin.businesses.index') }}" method="GET" class="w-full xl:w-96 flex gap-2">
                    <input type="hidden" name="tab" value="{{ $tab }}">
                    <input type="hidden" name="active" value="{{ $active }}">
                    <input type="hidden" name="featured" value="{{ $featured }}">
                    <div class="relative w-full">
                        <input type="text" name="search" value="{{ $search }}" placeholder="Buscar por negocio, legal, RUC, dueño..." 
                               class="w-full pl-4 pr-10 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-955 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                        <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </span>
                    </div>
                    <button type="submit" class="px-5 py-2.5 bg-slate-850 hover:bg-slate-900 text-white font-extrabold text-xs uppercase tracking-wider rounded-2xl transition shadow-md cursor-pointer shrink-0">
                        Buscar
                    </button>
                </form>
            </div>

            <!-- Tabla de Negocios -->
            <div class="overflow-x-auto">
                @if($businesses->isEmpty())
                    <div class="p-16 text-center text-slate-500 dark:text-slate-400">
                        <svg class="w-16 h-16 mx-auto mb-4 text-slate-300 dark:text-slate-600 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        <p class="text-base font-black font-['Outfit'] text-slate-700 dark:text-slate-300">No se encontraron negocios</p>
                        <p class="text-xs mt-1 font-medium font-sans">Prueba cambiando los filtros o la búsqueda de texto.</p>
                    </div>
                @else
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50 dark:bg-slate-900/40 border-b border-slate-100 dark:border-slate-700/60 text-slate-400 dark:text-slate-455 text-[10px] font-black uppercase tracking-wider">
                                <th class="px-6 py-4.5">Negocio</th>
                                <th class="px-6 py-4.5">RUC / Legal</th>
                                <th class="px-6 py-4.5">Dueño</th>
                                <th class="px-6 py-4.5">Calificación y Pedidos</th>
                                <th class="px-6 py-4.5 text-center">Featured</th>
                                <th class="px-6 py-4.5 text-center">Estado</th>
                                <th class="px-6 py-4.5 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700 text-sm">
                            @foreach($businesses as $business)
                                <tr class="hover:bg-slate-50/40 dark:hover:bg-slate-700/20 transition-colors">
                                    <!-- Negocio Details -->
                                    <td class="px-6 py-4.5">
                                        <div class="flex items-center gap-3">
                                            @if($business->logo_url)
                                                <img src="{{ $business->logo_url }}" alt="Logo" class="w-10 h-10 rounded-2xl object-cover shadow-inner border border-slate-100 dark:border-slate-700">
                                            @else
                                                <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-slate-100 to-slate-200 dark:from-slate-700 dark:to-slate-600 text-slate-750 dark:text-slate-200 flex items-center justify-center font-extrabold text-sm shadow-inner font-['Outfit']">
                                                    {{ substr($business->business_name, 0, 2) }}
                                                </div>
                                            @endif
                                            <div>
                                                <span class="font-extrabold text-slate-800 dark:text-white block font-['Outfit']">{{ $business->business_name }}</span>
                                                <span class="text-xs text-slate-450 dark:text-slate-400 font-mono block">{{ $business->slug }}</span>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- RUC & Legal -->
                                    <td class="px-6 py-4.5">
                                        <span class="block font-semibold text-slate-700 dark:text-slate-350">{{ $business->legal_name ?? 'N/A' }}</span>
                                        <span class="text-xs text-slate-450 dark:text-slate-400 font-mono font-bold">{{ $business->ruc ?? 'Sin RUC' }}</span>
                                    </td>

                                    <!-- Dueño -->
                                    <td class="px-6 py-4.5">
                                        @if($business->users->isNotEmpty())
                                            @php $owner = $business->users->first(); @endphp
                                            <span class="block font-semibold text-slate-700 dark:text-slate-300">{{ $owner->first_name }} {{ $owner->last_name }}</span>
                                            <span class="text-xs text-slate-450 dark:text-slate-400 font-medium">{{ $owner->email }}</span>
                                        @else
                                            <span class="text-xs text-slate-400 italic font-semibold">Sin dueño asignado</span>
                                        @endif
                                    </td>

                                    <!-- Stats -->
                                    <td class="px-6 py-4.5">
                                        <div class="flex items-center gap-1.5">
                                            <svg class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                            <span class="font-bold text-slate-700 dark:text-slate-300">{{ number_format($business->rating_average, 1) }}</span>
                                            <span class="text-xs text-slate-400">({{ $business->total_reviews }})</span>
                                        </div>
                                        <span class="text-xs text-slate-450 dark:text-slate-400 font-medium block mt-0.5">{{ $business->total_orders }} pedidos totales</span>
                                    </td>

                                    <!-- Featured Toggle -->
                                    <td class="px-6 py-4.5 text-center">
                                        <form action="{{ route('admin.businesses.toggle-featured', $business->id) }}" method="POST" class="inline-block">
                                            @csrf
                                            <button type="submit" class="p-1.5 rounded-xl transition hover:bg-slate-100 dark:hover:bg-slate-700 cursor-pointer">
                                                @if($business->is_featured)
                                                    <svg class="w-6 h-6 text-amber-500 fill-current" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                    </svg>
                                                @else
                                                    <svg class="w-6 h-6 text-slate-300 dark:text-slate-600 hover:text-amber-500 fill-none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.837-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                                    </svg>
                                                @endif
                                            </button>
                                        </form>
                                    </td>

                                    <!-- Status Badge / Operaciones -->
                                    <td class="px-6 py-4.5 text-center">
                                        <div class="flex flex-col items-center gap-1">
                                            @php
                                                $statusClasses = match($business->status) {
                                                    \App\Models\Business::STATUS_APPROVED => 'bg-emerald-50 dark:bg-emerald-950/30 text-emerald-600 dark:text-emerald-400 border border-emerald-200/40 dark:border-emerald-900/30',
                                                    \App\Models\Business::STATUS_PENDING => 'bg-amber-50 dark:bg-amber-950/30 text-amber-600 dark:text-amber-400 border border-amber-200/40 dark:border-amber-900/30',
                                                    \App\Models\Business::STATUS_SUSPENDED => 'bg-red-50 dark:bg-red-950/30 text-red-600 dark:text-red-400 border border-red-200/40 dark:border-red-900/30',
                                                    \App\Models\Business::STATUS_REJECTED => 'bg-rose-50 dark:bg-rose-950/30 text-rose-600 dark:text-rose-455 border border-rose-200/40 dark:border-rose-900/30',
                                                    default => 'bg-slate-50 dark:bg-slate-900/30 text-slate-600 dark:text-slate-400 border border-slate-200/40 dark:border-slate-900/30',
                                                };
                                                $statusLabel = match($business->status) {
                                                    \App\Models\Business::STATUS_APPROVED => 'Aprobado',
                                                    \App\Models\Business::STATUS_PENDING => 'Pendiente',
                                                    \App\Models\Business::STATUS_SUSPENDED => 'Suspendido',
                                                    \App\Models\Business::STATUS_REJECTED => 'Rechazado',
                                                    default => $business->status,
                                                };
                                            @endphp
                                            <span class="px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider {{ $statusClasses }}">
                                                {{ $statusLabel }}
                                            </span>

                                            <!-- Visible / Inactivo switch status -->
                                            <form action="{{ route('admin.businesses.toggle-active', $business->id) }}" method="POST" class="inline-block mt-1">
                                                @csrf
                                                <button type="submit" class="text-[10px] font-bold uppercase tracking-wider flex items-center gap-1 px-1.5 py-0.5 rounded transition cursor-pointer {{ $business->is_active ? 'text-emerald-500 hover:text-emerald-600' : 'text-slate-400 hover:text-slate-500' }}">
                                                    <span class="w-1.5 h-1.5 rounded-full {{ $business->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                                    {{ $business->is_active ? 'Activo' : 'Inactivo' }}
                                                </button>
                                            </form>
                                        </div>
                                    </td>

                                    <!-- Acciones -->
                                    <td class="px-6 py-4.5 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('admin.businesses.show', $business->id) }}" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-650 text-slate-800 dark:text-white font-extrabold text-[10px] uppercase tracking-wider rounded-xl transition cursor-pointer">
                                                Ver Ficha
                                            </a>

                                            @if($business->status === \App\Models\Business::STATUS_APPROVED)
                                                <form action="{{ route('admin.businesses.toggle-suspension', $business->id) }}" method="POST" class="inline-block" onsubmit="return confirm('¿Estás seguro de que deseas suspender este negocio?')">
                                                    @csrf
                                                    <button type="submit" class="px-3 py-1.5 bg-rose-500 hover:bg-rose-600 text-white font-extrabold text-[10px] uppercase tracking-wider rounded-xl transition shadow-md shadow-rose-500/10 cursor-pointer">
                                                        Suspender
                                                    </button>
                                                </form>
                                            @elseif($business->status === \App\Models\Business::STATUS_SUSPENDED)
                                                <form action="{{ route('admin.businesses.toggle-suspension', $business->id) }}" method="POST" class="inline-block" onsubmit="return confirm('¿Deseas levantar la suspensión de este negocio?')">
                                                    @csrf
                                                    <button type="submit" class="px-3 py-1.5 bg-emerald-500 hover:bg-emerald-600 text-white font-extrabold text-[10px] uppercase tracking-wider rounded-xl transition shadow-md shadow-emerald-500/10 cursor-pointer">
                                                        Reactivar
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

            <!-- Paginación -->
            @if($businesses->hasPages())
                <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-700 bg-slate-50/60 dark:bg-slate-900/20 flex items-center justify-between">
                    <div class="text-xs text-slate-450 dark:text-slate-400 font-medium">
                        Mostrando {{ $businesses->firstItem() }} al {{ $businesses->lastItem() }} de {{ $businesses->total() }} registros.
                    </div>
                    <div class="pagination-native">
                        {{ $businesses->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-layouts.admin>
