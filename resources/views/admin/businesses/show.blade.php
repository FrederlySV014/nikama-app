<x-layouts.admin>
    <x-slot:title>{{ $business->business_name }} - Detalles del Negocio</x-slot:title>

    <div class="space-y-6">
        <!-- Botón de retorno -->
        <div>
            <a href="{{ route('admin.businesses.index') }}" class="inline-flex items-center gap-2 text-slate-500 hover:text-luffy-red dark:text-slate-400 dark:hover:text-rose-400 font-extrabold text-xs uppercase tracking-wider transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path>
                </svg>
                Volver al listado
            </a>
        </div>

        <!-- Hero Card (Ficha) -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm overflow-hidden transition-colors duration-300">
            <!-- Banner -->
            <div class="h-48 w-full bg-gradient-to-r from-luffy-red/30 to-rose-500/20 relative">
                @if($business->banner_url)
                    <img src="{{ $business->banner_url }}" alt="Banner" class="w-full h-full object-cover">
                @endif
                <div class="absolute inset-0 bg-slate-950/20 backdrop-brightness-95"></div>
            </div>
            
            <!-- Perfil / Cabecera Info -->
            <div class="px-8 pb-8 pt-0 relative flex flex-col md:flex-row gap-6 items-start md:items-end">
                <div class="-mt-16 relative z-10">
                    @if($business->logo_url)
                        <img src="{{ $business->logo_url }}" alt="Logo" class="w-32 h-32 rounded-3xl object-cover bg-white dark:bg-slate-900 p-1.5 shadow-md border border-slate-200 dark:border-slate-700">
                    @else
                        <div class="w-32 h-32 rounded-3xl bg-gradient-to-tr from-slate-100 to-slate-200 dark:from-slate-700 dark:to-slate-650 text-slate-700 dark:text-slate-200 flex items-center justify-center font-black text-3xl shadow-md border-4 border-white dark:border-slate-800 font-['Outfit']">
                            {{ substr($business->business_name, 0, 2) }}
                        </div>
                    @endif
                </div>

                <div class="flex-grow space-y-2">
                    <div class="flex flex-wrap gap-2 items-center">
                        <h1 class="text-3xl font-black font-['Outfit'] text-slate-800 dark:text-white leading-tight">{{ $business->business_name }}</h1>
                        
                        <!-- Destacado status -->
                        @if($business->is_featured)
                            <span class="px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase bg-amber-500/10 text-amber-500 border border-amber-500/30 flex items-center gap-1">
                                <svg class="w-3 h-3 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                Destacado
                            </span>
                        @endif
                    </div>
                    
                    <p class="text-slate-500 dark:text-slate-400 font-mono text-sm">{{ $business->slug }}</p>

                    <div class="flex flex-wrap items-center gap-6 text-sm text-slate-600 dark:text-slate-300 font-semibold pt-1">
                        <div class="flex items-center gap-1.5">
                            <svg class="w-5 h-5 text-amber-400 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                            <span class="text-slate-800 dark:text-white font-extrabold">{{ number_format($business->rating_average, 2) }}</span>
                            <span class="text-slate-400 dark:text-slate-500">({{ $business->total_reviews }} reseñas)</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                            <span>{{ $business->total_orders }} pedidos realizados</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alertas Internas -->
        @if (session('success'))
            <div class="p-4 bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 rounded-3xl flex items-start gap-3 shadow-sm">
                <svg class="w-5 h-5 text-emerald-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="text-xs">
                    <p class="font-bold">Actualización exitosa</p>
                    <p class="opacity-90 mt-0.5">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if (session('warning'))
            <div class="p-4 bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800 text-amber-800 dark:text-amber-300 rounded-3xl flex items-start gap-3 shadow-sm">
                <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <div class="text-xs">
                    <p class="font-bold">Aviso del sistema</p>
                    <p class="opacity-90 mt-0.5">{{ session('warning') }}</p>
                </div>
            </div>
        @endif

        <!-- Información Detallada -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Columna Izquierda: Datos del Negocio & Redes -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Información General -->
                <div class="bg-white dark:bg-slate-800 p-8 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm transition-colors duration-300">
                    <h3 class="text-lg font-black font-['Outfit'] text-slate-800 dark:text-white border-b border-slate-100 dark:border-slate-700 pb-3 mb-6">Datos de la Empresa</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <span class="text-slate-400 dark:text-slate-400 text-xs font-bold uppercase tracking-wider block">Razón Social</span>
                            <span class="font-bold text-slate-800 dark:text-white mt-1 block">{{ $business->legal_name ?? 'No registrado' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 dark:text-slate-400 text-xs font-bold uppercase tracking-wider block">RUC</span>
                            <span class="font-mono font-bold text-slate-800 dark:text-white mt-1 block">{{ $business->ruc ?? 'No registrado' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 dark:text-slate-400 text-xs font-bold uppercase tracking-wider block">Email de Contacto</span>
                            <a href="mailto:{{ $business->contact_email }}" class="font-semibold text-luffy-red hover:underline mt-1 block">{{ $business->contact_email ?? 'Sin correo' }}</a>
                        </div>
                        <div>
                            <span class="text-slate-400 dark:text-slate-400 text-xs font-bold uppercase tracking-wider block">Teléfono de Contacto</span>
                            <span class="font-semibold text-slate-800 dark:text-white mt-1 block">{{ $business->contact_phone ?? 'Sin teléfono' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 dark:text-slate-400 text-xs font-bold uppercase tracking-wider block">WhatsApp de Emergencia</span>
                            <span class="font-semibold text-slate-800 dark:text-white mt-1 block">{{ $business->whatsapp_number ?? 'Sin WhatsApp' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 dark:text-slate-400 text-xs font-bold uppercase tracking-wider block">Monto Mínimo de Pedido</span>
                            <span class="font-bold text-slate-800 dark:text-white mt-1 block">S/ {{ number_format($business->minimum_order_amount, 2) }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 dark:text-slate-400 text-xs font-bold uppercase tracking-wider block">Tiempo Estimado de Preparación</span>
                            <span class="font-semibold text-slate-800 dark:text-white mt-1 block">{{ $business->estimated_preparation_time_minutes }} minutos</span>
                        </div>
                        <div>
                            <span class="text-slate-400 dark:text-slate-400 text-xs font-bold uppercase tracking-wider block">Descripción del Negocio</span>
                            <p class="text-slate-600 dark:text-slate-350 text-sm mt-1 leading-relaxed">{{ $business->description ?? 'Sin descripción cargada.' }}</p>
                        </div>
                    </div>

                    <!-- Enlaces Redes / Sitios -->
                    <div class="mt-8 pt-6 border-t border-slate-100 dark:border-slate-700/60 flex flex-wrap gap-6 text-sm">
                        @if($business->facebook_url)
                            <a href="{{ $business->facebook_url }}" target="_blank" class="flex items-center gap-1.5 text-slate-500 hover:text-blue-600 font-bold transition">
                                <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M9 8H7v3h2v9h4v-9h3.625L17 8h-4V6.75c0-.83.67-1 1-1h3V2h-3c-3.13 0-5 1.47-5 4.5V8z"/></svg>
                                Facebook
                            </a>
                        @endif
                        @if($business->instagram_url)
                            <a href="{{ $business->instagram_url }}" target="_blank" class="flex items-center gap-1.5 text-slate-500 hover:text-pink-600 font-bold transition">
                                <svg class="w-5 h-5 fill-none stroke-current" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37zM17.5 6.5h.01"/></svg>
                                Instagram
                            </a>
                        @endif
                        @if($business->website_url)
                            <a href="{{ $business->website_url }}" target="_blank" class="flex items-center gap-1.5 text-slate-500 hover:text-teal-600 font-bold transition">
                                <svg class="w-5 h-5 fill-none stroke-current" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                                Sitio Web
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Ubicaciones / Sedes del Negocio -->
                <div class="bg-white dark:bg-slate-800 p-8 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm transition-colors duration-300">
                    <h3 class="text-lg font-black font-['Outfit'] text-slate-800 dark:text-white border-b border-slate-100 dark:border-slate-700 pb-3 mb-6">Ubicaciones y Sucursales</h3>
                    
                    @if($business->locations->isEmpty())
                        <p class="text-sm text-slate-400 italic">No hay ubicaciones registradas para este comercio.</p>
                    @else
                        <div class="space-y-6">
                            @foreach($business->locations as $location)
                                <div class="p-6 rounded-2xl border {{ $location->is_main ? 'border-luffy-red/30 bg-slate-50/50 dark:bg-slate-900/20' : 'border-slate-100 dark:border-slate-700/60' }} space-y-4">
                                    <div class="flex items-center justify-between flex-wrap gap-2">
                                        <div class="flex items-center gap-2">
                                            <span class="font-extrabold text-slate-800 dark:text-white">{{ $location->name }}</span>
                                            @if($location->is_main)
                                                <span class="px-2 py-0.5 bg-luffy-red/10 text-luffy-red dark:text-rose-455 border border-luffy-red/30 rounded text-[9px] font-black uppercase tracking-wider">Principal</span>
                                            @endif
                                        </div>
                                        <span class="text-xs font-bold {{ $location->is_active ? 'text-emerald-500' : 'text-slate-400' }}">
                                            {{ $location->is_active ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 text-xs font-semibold">
                                        <div class="md:col-span-2">
                                            <span class="text-slate-400 block uppercase font-bold text-[10px] tracking-wide">Dirección</span>
                                            <span class="text-slate-700 dark:text-slate-300">{{ $location->address }}</span>
                                            @if($location->reference)
                                                <span class="text-slate-450 dark:text-slate-450 block italic mt-0.5">Ref: {{ $location->reference }}</span>
                                            @endif
                                        </div>
                                        <div>
                                            <span class="text-slate-400 block uppercase font-bold text-[10px] tracking-wide">Distrito / Provincia</span>
                                            <span class="text-slate-700 dark:text-slate-300">{{ $location->district }}, {{ $location->province }}</span>
                                        </div>
                                        <div>
                                            <span class="text-slate-400 block uppercase font-bold text-[10px] tracking-wide">Costo de Envío</span>
                                            <span class="text-slate-700 dark:text-slate-300 font-extrabold">S/ {{ number_format($location->delivery_fee, 2) }}</span>
                                        </div>
                                        <div>
                                            <span class="text-slate-400 block uppercase font-bold text-[10px] tracking-wide">Radio de Entrega</span>
                                            <span class="text-slate-700 dark:text-slate-300">{{ $location->delivery_radius_km }} km</span>
                                        </div>
                                        <div>
                                            <span class="text-slate-400 block uppercase font-bold text-[10px] tracking-wide">Monto Mínimo Envío</span>
                                            <span class="text-slate-700 dark:text-slate-300">S/ {{ number_format($location->minimum_delivery_amount, 2) }}</span>
                                        </div>
                                        <div>
                                            <span class="text-slate-400 block uppercase font-bold text-[10px] tracking-wide">Tiempo de Entrega</span>
                                            <span class="text-slate-700 dark:text-slate-300">{{ $location->estimated_delivery_time_minutes }} minutos</span>
                                        </div>
                                        @if($location->latitude && $location->longitude)
                                            <div>
                                                <span class="text-slate-400 block uppercase font-bold text-[10px] tracking-wide">Coordenadas</span>
                                                <span class="text-slate-700 dark:text-slate-300 font-mono">{{ $location->latitude }}, {{ $location->longitude }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Columna Derecha: Propietario, Configuración & Categorías -->
            <div class="space-y-6">
                <!-- Propietario / Dueño -->
                <div class="bg-white dark:bg-slate-800 p-6 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm transition-colors duration-300">
                    <h3 class="text-base font-black font-['Outfit'] text-slate-800 dark:text-white border-b border-slate-100 dark:border-slate-700 pb-3 mb-4">Propietario / Administrador</h3>
                    
                    @if($owner)
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-slate-100 to-slate-200 dark:from-slate-700 dark:to-slate-600 text-slate-700 dark:text-slate-200 flex items-center justify-center font-extrabold text-base shadow-inner font-['Outfit'] shrink-0">
                                {{ substr($owner->first_name, 0, 1) }}{{ substr($owner->last_name, 0, 1) }}
                            </div>
                            <div>
                                <span class="font-extrabold text-slate-800 dark:text-white block font-['Outfit']">{{ $owner->first_name }} {{ $owner->last_name }}</span>
                                <span class="text-[11px] text-slate-450 dark:text-slate-400 font-mono block">ID: {{ $owner->id }}</span>
                            </div>
                        </div>
                        <div class="space-y-3 text-xs font-semibold">
                            <div>
                                <span class="text-slate-400 block text-[10px] uppercase font-bold tracking-wide">Email</span>
                                <span class="text-slate-700 dark:text-slate-300 block break-all">{{ $owner->email }}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[10px] uppercase font-bold tracking-wide">Celular</span>
                                <span class="text-slate-700 dark:text-slate-300 block">{{ $owner->phone ?? 'Sin celular' }}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[10px] uppercase font-bold tracking-wide">DNI</span>
                                <span class="text-slate-700 dark:text-slate-300 block font-mono">{{ $owner->dni ?? 'Sin DNI' }}</span>
                            </div>
                        </div>
                    @else
                        <p class="text-xs text-slate-450 italic">No hay propietario asignado a este negocio.</p>
                    @endif
                </div>

                <!-- Configuración Operativa & Estado -->
                <div class="bg-white dark:bg-slate-800 p-6 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm transition-colors duration-300 space-y-6">
                    <h3 class="text-base font-black font-['Outfit'] text-slate-800 dark:text-white border-b border-slate-100 dark:border-slate-700 pb-3">Controles Administrativos</h3>
                    
                    <!-- Estado Actual -->
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wide">Estado de Solicitud:</span>
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
                        <span class="px-3 py-1 rounded-xl text-[10px] font-black uppercase tracking-wider {{ $statusClasses }}">
                            {{ $statusLabel }}
                        </span>
                    </div>

                    <!-- Toggles Form -->
                    <div class="space-y-4 border-t border-slate-100 dark:border-slate-700/60 pt-4">
                        <!-- Toggle Visibilidad / Activo -->
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-xs font-black text-slate-700 dark:text-slate-350 block">Negocio Activo</span>
                                <span class="text-[10px] text-slate-400">Si está inactivo, no aparecerá en el catálogo.</span>
                            </div>
                            <form action="{{ route('admin.businesses.toggle-active', $business->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-12 h-6 rounded-full p-1 transition-colors duration-250 cursor-pointer flex items-center {{ $business->is_active ? 'bg-emerald-500 justify-end' : 'bg-slate-300 dark:bg-slate-600 justify-start' }}">
                                    <span class="w-4 h-4 rounded-full bg-white shadow-sm"></span>
                                </button>
                            </form>
                        </div>

                        <!-- Toggle Featured -->
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-xs font-black text-slate-700 dark:text-slate-350 block">Destacado</span>
                                <span class="text-[10px] text-slate-400">Aparece en posiciones prioritarias.</span>
                            </div>
                            <form action="{{ route('admin.businesses.toggle-featured', $business->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-12 h-6 rounded-full p-1 transition-colors duration-250 cursor-pointer flex items-center {{ $business->is_featured ? 'bg-violet-500 justify-end' : 'bg-slate-300 dark:bg-slate-600 justify-start' }}">
                                    <span class="w-4 h-4 rounded-full bg-white shadow-sm"></span>
                                </button>
                            </form>
                        </div>

                        <!-- Toggle Accepts Orders -->
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-xs font-black text-slate-700 dark:text-slate-350 block">Recepción de Pedidos</span>
                                <span class="text-[10px] text-slate-400">Habilitar/deshabilitar la compra de productos.</span>
                            </div>
                            <form action="{{ route('admin.businesses.toggle-accepts-orders', $business->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-12 h-6 rounded-full p-1 transition-colors duration-250 cursor-pointer flex items-center {{ $business->accepts_orders ? 'bg-emerald-500 justify-end' : 'bg-slate-300 dark:bg-slate-600 justify-start' }}">
                                    <span class="w-4 h-4 rounded-full bg-white shadow-sm"></span>
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Botón de Suspensión -->
                    @if($business->status === \App\Models\Business::STATUS_APPROVED || $business->status === \App\Models\Business::STATUS_SUSPENDED)
                        <div class="border-t border-slate-100 dark:border-slate-700/60 pt-4">
                            @if($business->status === \App\Models\Business::STATUS_APPROVED)
                                <form action="{{ route('admin.businesses.toggle-suspension', $business->id) }}" method="POST" onsubmit="return confirm('¿Deseas suspender este negocio? El negocio dejará de aceptar pedidos de inmediato.')">
                                    @csrf
                                    <button type="submit" class="w-full py-2.5 bg-rose-500 hover:bg-rose-600 text-white font-extrabold text-[11px] uppercase tracking-wider rounded-2xl transition shadow-md shadow-rose-500/10 cursor-pointer">
                                        Suspender Comercio
                                    </button>
                                </form>
                            @else
                                <div class="space-y-3">
                                    <div class="text-[10px] text-rose-500 font-bold bg-rose-50 dark:bg-rose-950/20 border border-rose-200/50 dark:border-rose-900/30 p-3 rounded-xl">
                                        Suspendido el {{ $business->suspended_at ? $business->suspended_at->format('d/m/Y H:i') : 'N/A' }}
                                    </div>
                                    <form action="{{ route('admin.businesses.toggle-suspension', $business->id) }}" method="POST" onsubmit="return confirm('¿Levantar suspensión de este negocio?')">
                                        @csrf
                                        <button type="submit" class="w-full py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white font-extrabold text-[11px] uppercase tracking-wider rounded-2xl transition shadow-md shadow-emerald-500/10 cursor-pointer">
                                            Levantar Suspensión
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Categorías Asociadas -->
                <div class="bg-white dark:bg-slate-800 p-6 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm transition-colors duration-300">
                    <h3 class="text-base font-black font-['Outfit'] text-slate-800 dark:text-white border-b border-slate-100 dark:border-slate-700 pb-3 mb-4">Categorías del Comercio</h3>
                    
                    @if($business->categories->isEmpty())
                        <p class="text-xs text-slate-450 italic">No tiene categorías asociadas.</p>
                    @else
                        <div class="flex flex-wrap gap-2">
                            @foreach($business->categories as $category)
                                <span class="px-3 py-1 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-350 border border-slate-200/50 dark:border-slate-650 rounded-xl text-xs font-bold">
                                    {{ $category->name }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
