<x-layouts.admin>
    <x-slot:title>Detalle de Solicitud de Repartidor - Nikama Admin</x-slot:title>

    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Botón de Volver -->
        <div>
            <a href="{{ route('admin.applications.index', ['tab' => 'drivers', 'status' => 'pending']) }}" 
               class="inline-flex items-center gap-2 text-sm font-bold text-slate-500 hover:text-slate-800 dark:hover:text-white transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                    <p class="font-bold text-sm">Operación exitosa</p>
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
                    <p class="font-bold text-sm">Aviso de rechazo</p>
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

        <!-- Card de Solicitud de Repartidor -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm overflow-hidden" x-data="{ showRejectModal: false }">
            
            <!-- Cabecera Perfil de Repartidor -->
            <div class="relative bg-gradient-to-r from-slate-900 to-slate-800 dark:from-slate-950 dark:to-slate-900 p-8 text-white flex flex-col sm:flex-row items-center gap-6 justify-between border-b border-slate-100 dark:border-slate-700">
                <div class="flex items-center gap-6">
                    <div class="w-20 h-20 rounded-3xl bg-slate-800 dark:bg-slate-900 border-2 border-slate-700 flex items-center justify-center font-bold text-3xl text-luffy-red shadow-inner overflow-hidden">
                        @if ($driverProfile->user && $driverProfile->user->avatar_url)
                            <img src="{{ $driverProfile->user->avatar_url }}" alt="Foto" class="w-full h-full object-cover">
                        @else
                            {{ $driverProfile->user ? substr($driverProfile->user->first_name, 0, 1) . substr($driverProfile->user->last_name, 0, 1) : 'DR' }}
                        @endif
                    </div>
                    <div>
                        @if ($driverProfile->user)
                            <h1 class="text-2xl font-extrabold font-['Outfit'] text-white">{{ $driverProfile->user->first_name }} {{ $driverProfile->user->last_name }}</h1>
                            <p class="text-xs text-slate-400 mt-1">Registrado como postulante el {{ $driverProfile->created_at->format('d/m/Y H:i') }}</p>
                        @else
                            <h1 class="text-2xl font-extrabold font-['Outfit'] text-white">Repartidor sin Usuario</h1>
                        @endif
                    </div>
                </div>

                <!-- Badge de Estado -->
                <div>
                    @if ($driverProfile->status === 'pending')
                        <span class="px-4 py-2 rounded-full text-xs font-black uppercase tracking-wider bg-amber-500 text-white shadow-lg shadow-amber-500/20">Pendiente</span>
                    @elseif ($driverProfile->status === 'active')
                        <span class="px-4 py-2 rounded-full text-xs font-black uppercase tracking-wider bg-emerald-500 text-white shadow-lg shadow-emerald-500/20">Activo</span>
                    @elseif ($driverProfile->status === 'rejected')
                        <span class="px-4 py-2 rounded-full text-xs font-black uppercase tracking-wider bg-rose-500 text-white shadow-lg shadow-rose-500/20">Rechazado</span>
                    @else
                        <span class="px-4 py-2 rounded-full text-xs font-black uppercase tracking-wider bg-slate-600 text-white">{{ $driverProfile->status }}</span>
                    @endif
                </div>
            </div>

            <!-- Información Detallada -->
            <div class="p-8">
                <div class="space-y-6">
                    
                    <!-- Grid de Datos del Conductor y Vehículo -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        <!-- Columna de Datos de Usuario -->
                        <div class="space-y-4">
                            <h3 class="text-xs font-black uppercase tracking-wider text-slate-400">Datos Personales</h3>
                            
                            @if ($driverProfile->user)
                                <div>
                                    <span class="text-xs text-slate-400 block">DNI / Documento</span>
                                    <span class="text-sm font-mono font-bold text-slate-800 dark:text-slate-200">{{ $driverProfile->user->dni ?? 'No especificado' }}</span>
                                </div>

                                <div>
                                    <span class="text-xs text-slate-400 block">Teléfono Móvil</span>
                                    <span class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ $driverProfile->user->phone ?? 'No especificado' }}</span>
                                </div>

                                <div>
                                    <span class="text-xs text-slate-400 block">Correo Electrónico</span>
                                    <a href="mailto:{{ $driverProfile->user->email }}" class="text-sm font-semibold text-luffy-red hover:underline block">{{ $driverProfile->user->email }}</a>
                                </div>
                            @else
                                <div class="text-slate-400 text-sm py-2">No se encontró información personal.</div>
                            @endif
                        </div>

                        <!-- Columna de Datos de Vehículo -->
                        <div class="space-y-4 border-t md:border-t-0 md:border-l border-slate-100 dark:border-slate-700 md:pl-6">
                            <h3 class="text-xs font-black uppercase tracking-wider text-slate-400">Datos del Vehículo</h3>
                            
                            <div>
                                <span class="text-xs text-slate-400 block">Tipo de Vehículo</span>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-bold bg-slate-100 dark:bg-slate-700 text-slate-800 dark:text-slate-200 mt-1 uppercase">
                                    @if ($driverProfile->vehicle_type === 'bicycle')
                                        🚲 Bicicleta
                                    @elseif ($driverProfile->vehicle_type === 'motorcycle')
                                        🏍️ Motocicleta
                                    @elseif ($driverProfile->vehicle_type === 'car')
                                        🚗 Automóvil
                                    @else
                                        {{ $driverProfile->vehicle_type }}
                                    @endif
                                </span>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <span class="text-xs text-slate-400 block">Marca / Modelo</span>
                                    <span class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ $driverProfile->vehicle_brand ?? 'N/A' }} {{ $driverProfile->vehicle_model ?? 'N/A' }}</span>
                                </div>
                                <div>
                                    <span class="text-xs text-slate-400 block">Color</span>
                                    <span class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ $driverProfile->vehicle_color ?? 'N/A' }}</span>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <span class="text-xs text-slate-400 block">Placa</span>
                                    <span class="text-sm font-mono font-black uppercase text-slate-800 dark:text-slate-200">{{ $driverProfile->license_plate ?? 'Sin Placa' }}</span>
                                </div>
                                <div>
                                    <span class="text-xs text-slate-400 block">N° de Licencia</span>
                                    <span class="text-sm font-mono font-bold text-slate-800 dark:text-slate-200">{{ $driverProfile->license_number ?? 'Sin Licencia' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Datos Adicionales & Contacto de Emergencia -->
                    <div class="pt-6 border-t border-slate-100 dark:border-slate-700 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <h3 class="text-xs font-black uppercase tracking-wider text-slate-400">Contacto de Emergencia</h3>

                            <div>
                                <span class="text-xs text-slate-400 block">Nombre de Contacto</span>
                                <span class="text-sm font-bold text-slate-800 dark:text-slate-200 block">{{ $driverProfile->emergency_contact_name ?? 'No especificado' }}</span>
                            </div>

                            <div>
                                <span class="text-xs text-slate-400 block">Teléfono de Contacto</span>
                                <span class="text-sm font-semibold text-slate-800 dark:text-slate-200 block">{{ $driverProfile->emergency_contact_phone ?? 'No especificado' }}</span>
                            </div>
                        </div>

                        <div class="space-y-4 border-t md:border-t-0 md:border-l border-slate-100 dark:border-slate-700 md:pl-6">
                            <h3 class="text-xs font-black uppercase tracking-wider text-slate-400">Preferencia de Pagos</h3>

                            <div>
                                <span class="text-xs text-slate-400 block mb-1">Acepta Pagos en Efectivo</span>
                                @if ($driverProfile->accepts_cash_payments)
                                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-600 dark:text-emerald-400">
                                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        Sí, acepta efectivo
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-rose-500">
                                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        No acepta efectivo (Sólo pagos digitales)
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Historial de Aprobación/Rechazo -->
                    @if ($driverProfile->status !== 'pending')
                        <div class="pt-6 border-t border-slate-100 dark:border-slate-700">
                            <h3 class="text-xs font-black uppercase tracking-wider text-slate-400 mb-3">Historial de Aprobación</h3>
                            
                            @if ($driverProfile->status === 'active')
                                <div class="p-4 bg-emerald-50/50 dark:bg-emerald-950/20 border border-emerald-100 dark:border-emerald-900/50 rounded-2xl">
                                    <p class="text-sm font-semibold text-emerald-800 dark:text-emerald-300">
                                        Solicitud Aprobada (Conductor Activo)
                                    </p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                        Esta solicitud fue verificada y aprobada el {{ $driverProfile->verified_at ? $driverProfile->verified_at->format('d/m/Y H:i') : ($driverProfile->updated_at ? $driverProfile->updated_at->format('d/m/Y H:i') : 'N/A') }}. El conductor ya puede conectarse y recibir pedidos.
                                    </p>
                                </div>
                            @elseif ($driverProfile->status === 'rejected')
                                <div class="p-4 bg-rose-50/50 dark:bg-rose-950/20 border border-rose-100 dark:border-rose-900/50 rounded-2xl space-y-2">
                                    <p class="text-sm font-semibold text-rose-800 dark:text-rose-300">
                                        Solicitud Rechazada
                                    </p>
                                    <div>
                                        <span class="text-xs text-slate-400 block">Motivo del rechazo:</span>
                                        <p class="text-sm text-slate-700 dark:text-slate-300 font-medium bg-white dark:bg-slate-900 p-3 rounded-xl border border-rose-100 dark:border-rose-950 mt-1">
                                            {{ $driverProfile->rejected_reason ?? 'No se especificó un motivo.' }}
                                        </p>
                                    </div>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">
                                        Rechazado el {{ $driverProfile->updated_at->format('d/m/Y H:i') }}.
                                    </p>
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Acciones del Administrador -->
                    @if ($driverProfile->status === 'pending')
                        <div class="pt-8 border-t border-slate-100 dark:border-slate-700 flex flex-col sm:flex-row gap-4 items-center justify-end">
                            <button type="button" @click="showRejectModal = true" class="w-full sm:w-auto px-6 py-3 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-bold text-sm rounded-2xl hover:bg-slate-50 dark:hover:bg-slate-750 transition-colors">
                                Rechazar Solicitud
                            </button>

                            <form action="{{ route('admin.applications.driver.approve', $driverProfile) }}" method="POST" class="w-full sm:w-auto">
                                @csrf
                                <button type="submit" class="w-full sm:w-auto px-6 py-3 bg-luffy-red hover:bg-luffy-red/90 text-white font-bold text-sm rounded-2xl shadow-lg shadow-luffy-red/25 hover:shadow-luffy-red/35 transition-all">
                                    Aprobar Repartidor
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Modal de Rechazo (Alpine.js) -->
            <div x-show="showRejectModal" 
                 class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 style="display: none;">
                
                <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700 w-full max-w-lg p-6 shadow-2xl relative"
                     @click.away="showRejectModal = false">
                    
                    <button type="button" @click="showRejectModal = false" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>

                    <h2 class="text-xl font-extrabold font-['Outfit'] text-slate-800 dark:text-white">Rechazar Solicitud de Repartidor</h2>
                    <p class="text-slate-500 dark:text-slate-400 text-xs mt-1">Explica el motivo del rechazo. Esta nota será visible para el solicitante y debe ser clara y detallada.</p>

                    <form action="{{ route('admin.applications.driver.reject', $driverProfile) }}" method="POST" class="mt-4 space-y-4">
                        @csrf
                        <div>
                            <label for="rejected_reason" class="text-xs font-bold uppercase tracking-wider text-slate-400 block mb-2">Motivo de Rechazo</label>
                            <textarea id="rejected_reason" name="rejected_reason" rows="4" required placeholder="Escribe el motivo del rechazo aquí (mínimo 5 caracteres)..."
                                      class="w-full px-4 py-3 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">{{ old('rejected_reason') }}</textarea>
                            <span class="text-slate-400 text-[10px] block mt-1">Mínimo 5 caracteres y máximo 1000.</span>
                        </div>

                        <div class="flex gap-3 justify-end pt-2">
                            <button type="button" @click="showRejectModal = false" class="px-5 py-2.5 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs rounded-xl hover:bg-slate-50 dark:hover:bg-slate-750 transition-colors">
                                Cancelar
                            </button>
                            <button type="submit" class="px-5 py-2.5 bg-rose-500 hover:bg-rose-600 text-white font-bold text-xs rounded-xl shadow-lg shadow-rose-500/20 transition-all">
                                Confirmar Rechazo
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
        </div>
    </div>
</x-layouts.admin>
