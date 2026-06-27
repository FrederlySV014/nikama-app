<x-layouts.admin>
    <x-slot:title>Detalle de Solicitud de Negocio - Nikama Admin</x-slot:title>

    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Botón de Volver -->
        <div>
            <a href="{{ route('admin.applications.index', ['tab' => 'sellers', 'status' => 'pending']) }}" 
               class="inline-flex items-center gap-2 text-sm font-black uppercase tracking-wider text-slate-500 hover:text-slate-800 dark:hover:text-white transition-all duration-200 group">
                <svg class="w-4 h-4 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Volver al listado
            </a>
        </div>

        <!-- Mensajes de Estado / Alertas -->
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
                    <p class="font-extrabold text-sm">Aviso de rechazo</p>
                    <p class="text-xs opacity-90 mt-0.5">{{ session('warning') }}</p>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="p-4 bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 rounded-3xl shadow-sm">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-rose-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <p class="font-extrabold text-sm">Por favor, corrige los siguientes errores:</p>
                        <ul class="list-disc list-inside text-xs opacity-90 mt-1 space-y-0.5 font-medium">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <!-- Card de Solicitud de Negocio -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm overflow-hidden" x-data="{ showRejectModal: false }">
            
            <!-- Banner y Logo Header -->
            <div class="relative h-48 bg-slate-900 flex items-center justify-center overflow-hidden">
                @if ($business->banner_url)
                    <img src="{{ $business->banner_url }}" alt="Banner de {{ $business->business_name }}" class="absolute inset-0 w-full h-full object-cover opacity-60">
                @else
                    <div class="absolute inset-0 bg-gradient-to-r from-slate-950 to-slate-900 opacity-90"></div>
                    <div class="absolute text-slate-500 text-xs font-black uppercase tracking-wider">Sin imagen de portada</div>
                @endif

                <!-- Badge de Estado -->
                <div class="absolute top-6 right-6">
                    @if ($business->status === 'pending')
                        <span class="px-4 py-2 rounded-full text-xs font-black uppercase tracking-wider bg-amber-500 text-white shadow-lg shadow-amber-500/20 animate-pulse">Pendiente de Revisión</span>
                    @elseif ($business->status === 'approved')
                        <span class="px-4 py-2 rounded-full text-xs font-black uppercase tracking-wider bg-emerald-500 text-white shadow-lg shadow-emerald-500/20">Aprobado</span>
                    @elseif ($business->status === 'rejected')
                        <span class="px-4 py-2 rounded-full text-xs font-black uppercase tracking-wider bg-rose-500 text-white shadow-lg shadow-rose-500/20">Rechazado</span>
                    @else
                        <span class="px-4 py-2 rounded-full text-xs font-black uppercase tracking-wider bg-slate-600 text-white">{{ $business->status }}</span>
                    @endif
                </div>
            </div>

            <!-- Información Principal del Negocio -->
            <div class="p-8 relative">
                <!-- Logo flotante -->
                <div class="absolute -top-16 left-8 w-24 h-24 rounded-3xl bg-white dark:bg-slate-800 border-4 border-white dark:border-slate-800 shadow-md overflow-hidden flex items-center justify-center">
                    @if ($business->logo_url)
                        <img src="{{ $business->logo_url }}" alt="Logo de {{ $business->business_name }}" class="w-full h-full object-cover">
                    @else
                        <span class="text-3xl font-extrabold text-slate-400 dark:text-slate-600 font-['Outfit']">{{ substr($business->business_name, 0, 2) }}</span>
                    @endif
                </div>

                <div class="pt-8 space-y-6">
                    <!-- Título -->
                    <div>
                        <h1 class="text-3xl font-extrabold font-['Outfit'] text-slate-800 dark:text-white">{{ $business->business_name }}</h1>
                        <p class="text-sm text-slate-450 dark:text-slate-400 mt-1 font-medium">Registrado el {{ $business->created_at->format('d \d\e F, Y \a \l\a\s H:i') }}</p>
                    </div>

                    <!-- Grid de Datos del Negocio -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-6 border-t border-slate-100 dark:border-slate-700/60">
                        <div class="space-y-4">
                            <h3 class="text-xs font-black uppercase tracking-wider text-slate-400 dark:text-slate-400">Datos Comerciales</h3>
                            
                            <div>
                                <span class="text-xs text-slate-400 dark:text-slate-400 block font-bold">Razón Social</span>
                                <span class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ $business->legal_name ?? 'No especificado' }}</span>
                            </div>

                            <div>
                                <span class="text-xs text-slate-400 dark:text-slate-400 block font-bold">Número RUC</span>
                                <span class="text-sm font-mono font-bold text-slate-800 dark:text-slate-200">{{ $business->ruc ?? 'No especificado' }}</span>
                            </div>

                            <div>
                                <span class="text-xs text-slate-400 dark:text-slate-400 block font-bold mb-1.5">Categorías</span>
                                <div class="flex flex-wrap gap-1.5">
                                    @forelse ($business->categories as $category)
                                        <span class="px-2.5 py-1 text-xs font-bold rounded-xl bg-slate-100 dark:bg-slate-700/60 text-slate-700 dark:text-slate-200 border border-slate-150/20 dark:border-slate-600/30">{{ $category->name }}</span>
                                    @empty
                                        <span class="text-xs text-slate-450 dark:text-slate-500 font-medium italic">Ninguna categoría asignada</span>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4 md:border-l border-slate-150/40 dark:border-slate-700/50 md:pl-6">
                            <h3 class="text-xs font-black uppercase tracking-wider text-slate-400 dark:text-slate-400">Datos de Contacto</h3>

                            <div>
                                <span class="text-xs text-slate-400 dark:text-slate-400 block font-bold">Correo Electrónico</span>
                                <a href="mailto:{{ $business->contact_email }}" class="text-sm font-bold text-luffy-red dark:text-rose-450 hover:underline block">{{ $business->contact_email ?? 'No especificado' }}</a>
                            </div>

                            <div>
                                <span class="text-xs text-slate-400 dark:text-slate-400 block font-bold">Teléfono de Contacto</span>
                                <span class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ $business->contact_phone ?? 'No especificado' }}</span>
                            </div>

                            <div>
                                <span class="text-xs text-slate-400 dark:text-slate-400 block font-bold">WhatsApp de Pedidos</span>
                                @if ($business->whatsapp_number)
                                    <a href="https://wa.me/{{ preg_replace('/\D/', '', $business->whatsapp_number) }}" target="_blank" class="text-sm font-bold text-emerald-600 dark:text-emerald-400 hover:underline flex items-center gap-1">
                                        <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.185-.573c.948.519 1.938.852 3.141.852 3.182 0 5.768-2.586 5.768-5.766 0-3.18-2.585-5.766-5.763-5.766zm3.361 8.24c-.14.39-1.002.77-1.393.82-.36.05-.83.08-2.11-.44-1.63-.67-2.68-2.35-2.76-2.46-.08-.11-.67-.9-.67-1.71 0-.81.42-1.21.57-1.38.15-.17.33-.21.44-.21h.31c.11 0 .26-.04.41.31.15.36.52 1.27.57 1.37.05.1.08.21.01.35-.07.14-.14.24-.25.37-.11.12-.22.25-.31.35-.1.1-.2.21-.08.41.11.19.49.82 1.06 1.33.73.65 1.34.85 1.53.94.19.09.3.08.41-.05.11-.13.48-.56.61-.75.13-.19.26-.16.44-.09.18.07 1.17.55 1.37.65.2.1.33.15.38.24.06.09.06.54-.15.96z"/></svg>
                                        {{ $business->whatsapp_number }}
                                    </a>
                                @else
                                    <span class="text-sm text-slate-450 dark:text-slate-500 font-medium italic">No especificado</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Descripción -->
                    <div class="pt-6 border-t border-slate-100 dark:border-slate-700/60">
                        <h3 class="text-xs font-black uppercase tracking-wider text-slate-400 dark:text-slate-400 mb-2">Descripción del Negocio</h3>
                        <p class="text-slate-650 dark:text-slate-300 text-sm leading-relaxed bg-slate-50 dark:bg-slate-900/40 p-4 rounded-2xl border border-slate-100 dark:border-slate-800 font-medium">
                            {{ $business->description ?? 'El negocio no proporcionó una descripción.' }}
                        </p>
                    </div>

                    <!-- Datos del Propietario -->
                    <div class="pt-6 border-t border-slate-100 dark:border-slate-700/60">
                        <h3 class="text-xs font-black uppercase tracking-wider text-slate-400 dark:text-slate-400 mb-4">Información del Propietario</h3>
                        <div class="bg-slate-50 dark:bg-slate-900/30 rounded-3xl p-6 border border-slate-100 dark:border-slate-800 flex flex-col md:flex-row gap-6 justify-between items-start md:items-center">
                            @if ($owner)
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-luffy-red to-rose-500 text-white flex items-center justify-center font-extrabold text-base shadow-md shadow-luffy-red/10 font-['Outfit']">
                                        {{ substr($owner->first_name, 0, 1) }}{{ substr($owner->last_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <span class="font-extrabold text-slate-850 dark:text-white block font-['Outfit']">{{ $owner->first_name }} {{ $owner->last_name }}</span>
                                        <span class="text-xs text-slate-450 dark:text-slate-400 block font-mono font-bold">DNI: {{ $owner->dni ?? 'No especificado' }}</span>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm w-full md:w-auto">
                                    <div>
                                        <span class="text-xs text-slate-400 dark:text-slate-400 block font-bold">Correo Electrónico</span>
                                        <a href="mailto:{{ $owner->email }}" class="font-bold text-slate-700 dark:text-slate-300 hover:underline">{{ $owner->email }}</a>
                                    </div>
                                    <div>
                                        <span class="text-xs text-slate-400 dark:text-slate-400 block font-bold">Teléfono Personal</span>
                                        <span class="font-bold text-slate-700 dark:text-slate-300">{{ $owner->phone ?? 'No especificado' }}</span>
                                    </div>
                                </div>
                            @else
                                <div class="text-slate-450 dark:text-slate-500 text-sm py-2 italic font-medium">No se encontró información del usuario propietario.</div>
                            @endif
                        </div>
                    </div>

                    <!-- Historial de Revisión (si ya fue procesado) -->
                    @if ($business->status !== 'pending')
                        <div class="pt-6 border-t border-slate-100 dark:border-slate-700/60">
                            <h3 class="text-xs font-black uppercase tracking-wider text-slate-400 dark:text-slate-400 mb-3">Historial de Aprobación</h3>
                            
                            @if ($business->status === 'approved')
                                <div class="p-4 bg-emerald-50/50 dark:bg-emerald-950/15 border border-emerald-100 dark:border-emerald-900/40 rounded-2xl">
                                    <p class="text-sm font-extrabold text-emerald-800 dark:text-emerald-300">
                                        Solicitud Aprobada
                                    </p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 font-medium">
                                        Esta solicitud fue aprobada el {{ $business->approved_at ? $business->approved_at->format('d/m/Y H:i') : ($business->updated_at ? $business->updated_at->format('d/m/Y H:i') : 'N/A') }}. El negocio se encuentra verificado y activo.
                                    </p>
                                </div>
                            @elseif ($business->status === 'rejected')
                                <div class="p-4 bg-rose-50/50 dark:bg-rose-950/15 border border-rose-100 dark:border-rose-900/40 rounded-2xl space-y-2">
                                    <p class="text-sm font-extrabold text-rose-800 dark:text-rose-350">
                                        Solicitud Rechazada
                                    </p>
                                    <div>
                                        <span class="text-xs text-slate-400 dark:text-slate-400 block font-bold">Motivo del rechazo:</span>
                                        <p class="text-sm text-slate-750 dark:text-slate-300 font-bold bg-white dark:bg-slate-900 p-3 rounded-xl border border-rose-100 dark:border-rose-950/60 mt-1">
                                            {{ $business->rejected_reason ?? 'No se especificó un motivo.' }}
                                        </p>
                                    </div>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">
                                        Rechazado el {{ $business->updated_at->format('d/m/Y H:i') }}.
                                    </p>
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Acciones del Administrador -->
                    @if ($business->status === 'pending')
                        <div class="pt-8 border-t border-slate-100 dark:border-slate-700/60 flex flex-col sm:flex-row gap-4 items-center justify-end">
                            <button type="button" @click="showRejectModal = true" class="w-full sm:w-auto px-6 py-3 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-extrabold text-sm rounded-2xl hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors cursor-pointer">
                                Rechazar Solicitud
                            </button>

                            <form action="{{ route('admin.applications.seller.approve', $business) }}" method="POST" class="w-full sm:w-auto">
                                @csrf
                                <button type="submit" class="w-full sm:w-auto px-6 py-3 bg-luffy-red hover:bg-luffy-red-hover text-white font-extrabold text-sm rounded-2xl shadow-lg shadow-luffy-red/20 hover:shadow-luffy-red/30 transition-all cursor-pointer">
                                    Aprobar Negocio
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Modal de Rechazo (Alpine.js) -->
            <div x-show="showRejectModal" 
                 class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/65 backdrop-blur-sm"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 style="display: none;">
                
                <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700 w-full max-w-lg p-6 shadow-2xl relative"
                     @click.away="showRejectModal = false">
                    
                    <button type="button" @click="showRejectModal = false" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>

                    <h2 class="text-xl font-extrabold font-['Outfit'] text-slate-850 dark:text-white">Rechazar Solicitud de Negocio</h2>
                    <p class="text-slate-500 dark:text-slate-400 text-xs mt-1 font-medium">Explica el motivo del rechazo. Esta nota será visible para el solicitante y debe ser clara y detallada.</p>

                    <form action="{{ route('admin.applications.seller.reject', $business) }}" method="POST" class="mt-4 space-y-4">
                        @csrf
                        <div>
                            <label for="rejected_reason" class="text-xs font-black uppercase tracking-wider text-slate-400 dark:text-slate-400 block mb-2">Motivo de Rechazo</label>
                            <textarea id="rejected_reason" name="rejected_reason" rows="4" required placeholder="Escribe el motivo del rechazo aquí (mínimo 5 caracteres)..."
                                      class="w-full px-4 py-3 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm text-slate-850 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">{{ old('rejected_reason') }}</textarea>
                            <span class="text-slate-400 text-[10px] block mt-1 font-medium">Mínimo 5 caracteres y máximo 1000.</span>
                        </div>

                        <div class="flex gap-3 justify-end pt-2">
                            <button type="button" @click="showRejectModal = false" class="px-5 py-2.5 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-extrabold text-xs rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors cursor-pointer">
                                Cancelar
                            </button>
                            <button type="submit" class="px-5 py-2.5 bg-rose-500 hover:bg-rose-600 text-white font-extrabold text-xs rounded-xl shadow-lg shadow-rose-500/20 transition-all cursor-pointer">
                                Confirmar Rechazo
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
        </div>
    </div>
</x-layouts.admin>
