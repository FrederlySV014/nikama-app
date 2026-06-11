<x-layouts.public>
    <x-slot:title>Finalizar Compra - Nikama</x-slot:title>

    <div class="max-w-6xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-extrabold font-['Outfit'] text-slate-800 dark:text-white mb-8">Finalizar tu Pedido</h1>

        @if ($errors->any())
            <div class="mb-6 p-4 bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 rounded-2xl">
                <ul class="list-disc list-inside text-sm space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form 
            action="{{ route('checkout.store') }}" 
            method="POST" 
            class="grid grid-cols-1 lg:grid-cols-12 gap-8"
            x-data="checkoutAddresses()"
        >
            @csrf

            <!-- Hidden input for selected address -->
            <input type="hidden" name="customer_address_id" :value="address_selection_type === 'saved' ? selectedAddressId : ''">

            <!-- Columna Izquierda: Datos de Entrega y Pago -->
            <div class="lg:col-span-8 space-y-6">
                
                <!-- Sección 1: Dirección de Entrega -->
                <div class="bg-white dark:bg-slate-800 p-6 sm:p-8 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm space-y-6">
                    <div class="border-b border-slate-100 dark:border-slate-700/60 pb-4">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <span class="text-xl">📍</span> Dirección de Entrega
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Elige dónde quieres recibir tu pedido.</p>
                    </div>

                    <!-- Hidden input for selected option -->
                    <input type="hidden" name="address_selection_type" :value="address_selection_type">

                    <!-- Tab Buttons Selector -->
                    <div class="grid grid-cols-2 gap-4">
                        <button 
                            type="button" 
                            @click="address_selection_type = 'saved'"
                            :disabled="addresses.length === 0"
                            :class="[
                                address_selection_type === 'saved' 
                                    ? 'border-luffy-red bg-rose-50/20 text-luffy-red' 
                                    : 'border-slate-200 dark:border-slate-700 text-slate-650 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-900/10',
                                addresses.length === 0 ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer'
                            ]"
                            class="flex items-center justify-center p-3.5 rounded-2xl border-2 font-bold text-xs gap-2 transition-all animate-all"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                            Mis Direcciones Guardadas
                        </button>
                        <button 
                            type="button" 
                            @click="address_selection_type = 'new'"
                            :class="address_selection_type === 'new' ? 'border-luffy-red bg-rose-50/20 text-luffy-red' : 'border-slate-200 dark:border-slate-700 text-slate-650 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-900/10'"
                            class="flex items-center justify-center p-3.5 rounded-2xl border-2 font-bold text-xs gap-2 transition-all cursor-pointer"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            Enviar a Nueva Ubicación
                        </button>
                    </div>

                    <!-- TAB 1: SAVED ADDRESSES GRID -->
                    <div x-show="address_selection_type === 'saved'" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <template x-for="(addr, index) in addresses" :key="addr.id">
                                <label 
                                    class="relative flex p-4 rounded-2xl border cursor-pointer focus:outline-none transition-all hover:bg-slate-50/50 dark:hover:bg-slate-900/10"
                                    :class="[
                                        selectedAddressId === addr.id ? 'border-luffy-red bg-rose-50/5 dark:bg-slate-800/40' : 'border-slate-200 dark:border-slate-700',
                                        !isActiveDistrict(addr.district, addr.province, addr.department) ? 'opacity-60 cursor-not-allowed bg-slate-50 dark:bg-slate-900/10' : ''
                                    ]"
                                >
                                    <input type="radio" name="address_selector" :value="addr.id" 
                                           class="absolute top-4 right-4 h-4 w-4 text-luffy-red border-slate-350 focus:ring-luffy-red" 
                                           :checked="selectedAddressId === addr.id"
                                           :disabled="!isActiveDistrict(addr.district, addr.province, addr.department)"
                                           @change="selectedAddressId = addr.id">
                                    <div class="pr-6 space-y-1">
                                        <div class="flex flex-wrap items-center gap-1.5">
                                            <span 
                                                class="inline-block px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider bg-slate-100 dark:bg-slate-750 text-slate-650 dark:text-slate-350"
                                                x-text="addr.label"
                                            ></span>
                                            <template x-if="addr.address_type">
                                                <span 
                                                    class="inline-block px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider bg-indigo-50 dark:bg-indigo-950/40 text-indigo-650 dark:text-indigo-400 border border-indigo-100/10"
                                                    x-text="addr.address_type === 'home' ? 'Casa' : (addr.address_type === 'work' ? 'Trabajo' : (addr.address_type === 'study' ? 'Estudios' : addr.address_type))"
                                                ></span>
                                            </template>
                                            <template x-if="!isActiveDistrict(addr.district, addr.province, addr.department)">
                                                <span class="inline-block px-2 py-0.5 rounded-md text-[9px] font-bold uppercase bg-amber-100 dark:bg-amber-950/40 text-amber-800 dark:text-amber-400">Sin Cobertura</span>
                                            </template>
                                        </div>
                                        <p class="text-sm font-bold text-slate-800 dark:text-white mt-1" x-text="addr.address"></p>
                                        <template x-if="addr.reference">
                                            <p class="text-xs text-slate-450 italic" x-text="'Ref: ' + addr.reference"></p>
                                        </template>
                                        <p class="text-xs text-slate-550 dark:text-slate-400 font-semibold" x-text="addr.district + ', ' + addr.province"></p>
                                    </div>
                                </label>
                            </template>
                        </div>
                    </div>

                    <!-- TAB 2: INLINE FORM FOR NEW LOCATION -->
                    <div x-show="address_selection_type === 'new'" class="space-y-6" style="display: none;">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            <!-- Left: Map Preview inline -->
                            <div class="space-y-2">
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-450 dark:text-slate-400">Ubicar en el Mapa (Haz clic o arrastra el marcador)</label>
                                <div id="map-checkout" class="h-72 w-full rounded-2xl border border-slate-200 dark:border-slate-700/60 z-0"></div>
                                <template x-if="showCoverageWarning">
                                    <div class="mt-2 p-3 bg-amber-50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-900 rounded-xl text-[11px] text-amber-800 dark:text-amber-400 font-medium">
                                        ⚠️ Nikama no cuenta con cobertura en <span class="font-bold" x-text="geocodedDistrict"></span>. Selecciona un distrito habilitado en la lista o arrastra el marcador a una zona activa (ej: Chiclayo, Pimentel).
                                    </div>
                                </template>
                                <p class="text-[10px] text-slate-400 mt-1">Coloca el pin rojo exactamente sobre el punto de entrega de tu pedido.</p>
                            </div>

                            <!-- Right: Form Fields -->
                            <div class="space-y-4">
                                <!-- Preset Buttons -->
                                <div class="space-y-1.5">
                                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-450 dark:text-slate-400">Tipo de Dirección</label>
                                    <input type="hidden" name="new_address_type" :value="computedAddressType">
                                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                        <button type="button" @click="selected_preset = 'home'"
                                                :class="selected_preset === 'home' ? 'border-luffy-red bg-rose-50/20 text-luffy-red' : 'border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400'"
                                                class="py-2 rounded-xl border text-xs font-bold transition-all cursor-pointer text-center">
                                            Casa
                                        </button>
                                        <button type="button" @click="selected_preset = 'work'"
                                                :class="selected_preset === 'work' ? 'border-luffy-red bg-rose-50/20 text-luffy-red' : 'border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400'"
                                                class="py-2 rounded-xl border text-xs font-bold transition-all cursor-pointer text-center">
                                            Trabajo
                                        </button>
                                        <button type="button" @click="selected_preset = 'study'"
                                                :class="selected_preset === 'study' ? 'border-luffy-red bg-rose-50/20 text-luffy-red' : 'border-slate-200 dark:border-slate-700 text-slate-650 dark:text-slate-400'"
                                                class="py-2 rounded-xl border text-xs font-bold transition-all cursor-pointer text-center">
                                            Estudios
                                        </button>
                                        <button type="button" @click="selected_preset = 'other'"
                                                :class="selected_preset === 'other' ? 'border-luffy-red bg-rose-50/20 text-luffy-red' : 'border-slate-200 dark:border-slate-700 text-slate-650 dark:text-slate-400'"
                                                class="py-2 rounded-xl border text-xs font-bold transition-all cursor-pointer text-center">
                                            Otro
                                        </button>
                                    </div>
                                    <div x-show="selected_preset === 'other'" class="mt-2" style="display: none;">
                                        <input x-model="custom_type" type="text" placeholder="Ej: Casa de playa, etc."
                                               class="w-full px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-xs text-slate-850 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                                    </div>
                                </div>

                                <!-- Label / Alias & Postal Code -->
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="space-y-1">
                                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-450 dark:text-slate-400">Etiqueta *</label>
                                        <input x-model="new_label" name="new_label" type="text" placeholder="Ej: Mi Casa, Oficina" :required="address_selection_type === 'new'"
                                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                                    </div>
                                    <div class="space-y-1">
                                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-450 dark:text-slate-400">Cód. Postal</label>
                                        <input x-model="new_postal_code" name="new_postal_code" type="text" placeholder="Ej: 15074"
                                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-955 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                                    </div>
                                </div>

                                <!-- Address -->
                                <div class="space-y-1">
                                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-450 dark:text-slate-400">Dirección Completa *</label>
                                    <input x-model="new_address" name="new_address" type="text" placeholder="Calle, Avenida, Pasaje, Número..." :required="address_selection_type === 'new'"
                                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                                </div>
                            </div>

                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                            <!-- Left: Reference & Notes -->
                            <div class="space-y-4">
                                <div class="space-y-1">
                                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-455 dark:text-slate-400">Referencia de Entrega</label>
                                    <input x-model="new_reference" name="new_reference" type="text" placeholder="Ej: Frente al parque, al costado del grifo..."
                                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                                </div>
                                <div class="space-y-1">
                                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-450 dark:text-slate-400">Notas de Entrega para Motorizado</label>
                                    <textarea x-model="new_delivery_notes" name="new_delivery_notes" placeholder="Ej: Timbre malogrado, llamar al celular..." rows="2"
                                              class="w-full px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all resize-none"></textarea>
                                </div>
                            </div>

                            <!-- Right: Contact & Geolocation fields -->
                            <div class="space-y-4">
                                <div class="grid grid-cols-3 gap-2">
                                    <div class="space-y-1">
                                        <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Dpto.</label>
                                        <input x-model="new_department" name="new_department" type="text" placeholder="Lima"
                                               class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-xs text-slate-850 dark:text-white focus:outline-none">
                                    </div>
                                    <div class="space-y-1">
                                        <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Provincia</label>
                                        <input x-model="new_province" name="new_province" type="text" placeholder="Lima"
                                               class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-xs text-slate-850 dark:text-white focus:outline-none">
                                    </div>
                                    <div class="space-y-1">
                                        <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-455 dark:text-slate-400">Distrito *</label>
                                        <select x-model="selectedQualifiedDistrict"
                                                @change="
                                                    if (selectedQualifiedDistrict) {
                                                        const parts = selectedQualifiedDistrict.split('|');
                                                        if (parts.length === 3) {
                                                            new_department = parts[0];
                                                            new_province = parts[1];
                                                            new_district = parts[2];
                                                        } else {
                                                            new_district = selectedQualifiedDistrict;
                                                        }
                                                    } else {
                                                        new_district = '';
                                                    }
                                                "
                                                class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-xs text-slate-850 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent cursor-pointer"
                                        >
                                            <option value="">Selecciona...</option>
                                            <template x-for="dist in activeDistricts" :key="dist">
                                                <option :value="dist" x-text="getDistrictLabel(dist)"></option>
                                            </template>
                                        </select>
                                        <input type="hidden" name="new_district" :value="new_district">
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div class="space-y-1">
                                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-450 dark:text-slate-400">Nombre Contacto</label>
                                        <input x-model="new_contact_name" name="new_contact_name" type="text" placeholder="Quien recibe"
                                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                                    </div>
                                    <div class="space-y-1">
                                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-455 dark:text-slate-400">Teléfono Contacto</label>
                                        <input x-model="new_contact_phone" name="new_contact_phone" type="text" placeholder="Número celular"
                                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-955 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- Hidden Coordinates inputs -->
                        <input type="hidden" name="new_latitude" :value="new_latitude">
                        <input type="hidden" name="new_longitude" :value="new_longitude">

                        <!-- Save Address Switch -->
                        <div class="flex items-center gap-3 bg-slate-50 dark:bg-slate-900/40 p-4 rounded-2xl border border-slate-100 dark:border-slate-750/50">
                            <input type="checkbox" name="save_address" id="save_address_chk" value="1" x-model="save_address"
                                   class="h-4 w-4 rounded text-luffy-red border-slate-350 focus:ring-luffy-red cursor-pointer">
                            <div>
                                <label for="save_address_chk" class="text-xs font-bold text-slate-800 dark:text-slate-200 cursor-pointer block">
                                    Guardar esta dirección en mi libreta de direcciones frecuentes
                                </label>
                                <p class="text-[10px] text-slate-500 dark:text-slate-450 mt-0.5">Si lo desmarcas, se usará únicamente para esta entrega sin guardarse en tu perfil.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección 2: Método de Pago -->
                <div class="bg-white dark:bg-slate-800 p-6 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm space-y-4"
                     x-data="{ selectedMethod: '{{ $activeMethods[0] ?? 'cash' }}' }">
                    <div class="flex items-center justify-between border-b border-slate-50 dark:border-slate-700 pb-3">
                        <h3 class="text-lg font-bold text-slate-800 dark:text-white flex items-center gap-2">
                            <span class="text-xl">💳</span> Método de Pago
                        </h3>
                        <span class="flex items-center gap-1.5 text-[10px] font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-200 dark:border-emerald-900/30 px-2.5 py-1 rounded-full">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            Pago Seguro
                        </span>
                    </div>

                    @if (empty($activeMethods))
                        <div class="p-4 bg-amber-50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-900 text-amber-800 dark:text-amber-400 text-sm rounded-2xl">
                            ⚠️ Actualmente no hay métodos de pago activos. Por favor, contacta con soporte o vuelve a intentarlo más tarde.
                        </div>
                    @else
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach ($activeMethods as $method)
                                @php
                                    $methodData = match($method) {
                                        'cash' => ['icon' => '💵', 'label' => 'Efectivo contra entrega', 'desc' => 'Paga al repartidor al recibir tu pedido', 'badge' => null],
                                        'yape' => ['icon' => '📱', 'label' => 'Yape', 'desc' => 'Pago móvil rápido con QR — confirmación inmediata', 'badge' => 'Instantáneo'],
                                        'plin' => ['icon' => '📲', 'label' => 'Plin', 'desc' => 'Pago interbancario — confirmación inmediata', 'badge' => 'Instantáneo'],
                                        'card' => ['icon' => '💳', 'label' => 'Tarjeta de Crédito / Débito', 'desc' => 'Visa, Mastercard, AMEX — cargo inmediato', 'badge' => 'Seguro'],
                                        'bank_transfer' => ['icon' => '🏦', 'label' => 'Transferencia Bancaria', 'desc' => 'Depósito BCP o Interbank', 'badge' => null],
                                        'pagoefectivo' => ['icon' => '🎫', 'label' => 'PagoEfectivo', 'desc' => 'Código CIP en agentes BCP, Kasnet, etc.', 'badge' => null],
                                        default => ['icon' => '💰', 'label' => ucfirst($method), 'desc' => '', 'badge' => null],
                                    };
                                @endphp
                                <label
                                    @click="selectedMethod = '{{ $method }}'"
                                    class="relative flex items-start gap-3 p-4 rounded-2xl border-2 cursor-pointer transition-all duration-200 select-none"
                                    :class="selectedMethod === '{{ $method }}'
                                        ? 'border-luffy-red bg-rose-50/20 dark:bg-rose-950/10 shadow-md shadow-luffy-red/5'
                                        : 'border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600 hover:bg-slate-50/50 dark:hover:bg-slate-900/20'"
                                >
                                    <input type="radio" name="payment_method" value="{{ $method }}"
                                           class="sr-only"
                                           :checked="selectedMethod === '{{ $method }}'"
                                           {{ $loop->first ? 'checked' : '' }}>

                                    <!-- Selection Dot -->
                                    <span class="mt-0.5 flex-shrink-0 h-5 w-5 rounded-full border-2 transition-all flex items-center justify-center"
                                          :class="selectedMethod === '{{ $method }}' ? 'border-luffy-red bg-luffy-red' : 'border-slate-300 dark:border-slate-600'">
                                        <span class="h-2 w-2 rounded-full bg-white transition-transform"
                                              :class="selectedMethod === '{{ $method }}' ? 'scale-100' : 'scale-0'"></span>
                                    </span>

                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="text-xl leading-none">{{ $methodData['icon'] }}</span>
                                            <span class="font-extrabold text-sm text-slate-800 dark:text-white font-['Outfit']">{{ $methodData['label'] }}</span>
                                            @if ($methodData['badge'])
                                                <span class="text-[9px] font-black uppercase tracking-wider px-1.5 py-0.5 bg-emerald-100 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 rounded-md">{{ $methodData['badge'] }}</span>
                                            @endif
                                        </div>
                                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 leading-snug">{{ $methodData['desc'] }}</p>
                                    </div>
                                </label>
                            @endforeach
                        </div>

                        <!-- Confirmation Note -->
                        <div class="flex items-start gap-2.5 p-3.5 bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-200 dark:border-emerald-900/30 rounded-2xl mt-2">
                            <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0116 0z"></path></svg>
                            <p class="text-[11px] text-emerald-800 dark:text-emerald-300 font-semibold leading-snug">
                                Al confirmar tu pedido, será procesado y confirmado de forma inmediata. Recibirás una notificación cuando el negocio comience a prepararlo.
                            </p>
                        </div>
                    @endif
                </div>

                <!-- Sección 3: Notas adicionales -->
                <div class="bg-white dark:bg-slate-800 p-6 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm space-y-4">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white flex items-center gap-2">
                        <span class="text-xl">📝</span> Notas del Pedido (Opcional)
                    </h3>
                    <textarea name="notes" id="notes" rows="3" placeholder="Ej: No tocar el timbre, bebé durmiendo; llamar al llegar..."
                              class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all"></textarea>
                </div>
            </div>

            <!-- Columna Derecha: Resumen de Pedido -->
            <div class="lg:col-span-4 space-y-6">
                <div class="bg-white dark:bg-slate-800 p-6 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm sticky top-24">
                    <h3 class="text-lg font-extrabold font-['Outfit'] text-slate-800 dark:text-white border-b border-slate-50 dark:border-slate-700 pb-3 mb-4">
                        Resumen de Compra
                    </h3>

                    <!-- Ítems del carrito -->
                    <div class="max-h-60 overflow-y-auto divide-y divide-slate-100 dark:divide-slate-750 pr-1 mb-6">
                        @foreach ($cart->items as $item)
                            <div class="py-3 flex justify-between gap-3 text-sm">
                                <div>
                                    <span class="font-bold text-slate-800 dark:text-white">{{ $item->quantity }}x</span>
                                    <span class="text-slate-650 dark:text-slate-350 ml-1">
                                        {{ $item->product ? $item->product->name : $item->combo->name }}
                                    </span>
                                    @if ($item->options->isNotEmpty())
                                        <div class="text-[11px] text-slate-400 mt-0.5 space-y-0.5">
                                            @foreach ($item->options as $opt)
                                                <p>+ {{ $opt->productOption->name }} (S/ {{ number_format($opt->additional_price, 2) }})</p>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                                <span class="font-bold text-slate-800 dark:text-white shrink-0">
                                    S/ {{ number_format(($item->unit_price + $item->options->sum('additional_price')) * $item->quantity, 2) }}
                                </span>
                            </div>
                        @endforeach
                    </div>

                    <!-- Precios -->
                    <div class="space-y-3 text-sm border-t border-slate-100 dark:border-slate-700 pt-4">
                        <div class="flex justify-between text-slate-600 dark:text-slate-400">
                            <span>Subtotal</span>
                            <span>S/ {{ number_format($subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-slate-600 dark:text-slate-400">
                            <span>Costo de Envío</span>
                            <span>S/ {{ number_format($deliveryFee, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-lg font-extrabold font-['Outfit'] text-slate-800 dark:text-white border-t border-slate-100 dark:border-slate-700 pt-3">
                            <span>Total</span>
                            <span>S/ {{ number_format($total, 2) }}</span>
                        </div>
                    </div>

                    <!-- Botón de Envío -->
                    <div class="mt-6 space-y-3">
                        <button
                            type="submit"
                            ::disabled="(address_selection_type === 'saved' ? !selectedAddressId : (!new_address || !new_district || !new_label)) || {{ empty($activeMethods) ? 'true' : 'false' }}"
                            class="w-full py-4 px-6 font-extrabold text-center rounded-2xl shadow-lg transition-all text-base relative overflow-hidden group"
                            :class="((address_selection_type === 'saved' ? !selectedAddressId : (!new_address || !new_district || !new_label)) || {{ empty($activeMethods) ? 'true' : 'false' }})
                                ? 'bg-slate-200 dark:bg-slate-700 text-slate-450 dark:text-slate-500 cursor-not-allowed shadow-none'
                                : 'bg-luffy-red hover:bg-luffy-red/90 text-white shadow-luffy-red/25 hover:shadow-xl hover:scale-[1.01] active:scale-[0.99]'"
                        >
                            <span class="flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                Confirmar Pedido Ahora
                            </span>
                        </button>
                        <div class="flex items-center justify-center gap-2 text-[10px] text-slate-400">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            <span>Transacción segura · Tu pedido se confirma al instante</span>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        function checkoutAddresses() {
            return {
                address_selection_type: 'saved',
                addresses: @json($addresses),
                activeDistricts: @json($activeDistricts),
                selectedAddressId: '',
                selectedQualifiedDistrict: '',
                map: null,
                marker: null,
                coveragePolygons: [],
                selected_preset: 'home',
                custom_type: '',
                
                // Coverage state
                showCoverageWarning: false,
                geocodedDistrict: '',
                
                // Fields for the new inline address form
                new_label: '',
                new_address_type: 'home',
                new_address: '',
                new_reference: '',
                new_delivery_notes: '',
                new_district: '',
                new_province: 'Chiclayo',
                new_department: 'Lambayeque',
                new_postal_code: '',
                new_country: 'Peru',
                new_contact_name: '{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}',
                new_contact_phone: '{{ auth()->user()->phone }}',
                new_latitude: '-6.7719',
                new_longitude: '-79.8389',
                save_address: true,

                get computedAddressType() {
                    return this.selected_preset === 'other' ? this.custom_type : this.selected_preset;
                },

                isActiveDistrict(district, province, department) {
                    if (!district) return false;
                    const cleanDist = district.toLowerCase().trim();
                    const cleanProv = province ? province.toLowerCase().trim() : '';
                    const cleanDept = department ? department.toLowerCase().trim() : '';
                    
                    return this.activeDistricts.some(d => {
                        const parts = d.split('|');
                        if (parts.length === 3) {
                            const matchDist = parts[2].toLowerCase().trim() === cleanDist;
                            const matchProv = !cleanProv || parts[1].toLowerCase().trim() === cleanProv;
                            const matchDept = !cleanDept || parts[0].toLowerCase().trim() === cleanDept;
                            return matchDist && matchProv && matchDept;
                        }
                        return d.toLowerCase().trim() === cleanDist;
                    });
                },

                getDistrictLabel(qualified) {
                    if (!qualified) return '';
                    const parts = qualified.split('|');
                    if (parts.length === 3) {
                        return `${parts[2]} (${parts[1]}, ${parts[0]})`;
                    }
                    return qualified;
                },

                init() {
                    // Set default selectedAddressId to the first address in an active district
                    const defAddr = this.addresses.find(a => a.is_default && this.isActiveDistrict(a.district, a.province, a.department)) 
                                    || this.addresses.find(a => this.isActiveDistrict(a.district, a.province, a.department));
                    
                    if (this.addresses.length === 0 || !this.addresses.some(a => this.isActiveDistrict(a.district, a.province, a.department))) {
                        this.address_selection_type = 'new';
                    } else {
                        this.address_selection_type = 'saved';
                        if (defAddr) {
                            this.selectedAddressId = defAddr.id;
                        }
                    }

                    // Watch tab switching to initialize the inline map
                    this.$watch('address_selection_type', (val) => {
                        if (val === 'new') {
                            this.initMap(parseFloat(this.new_latitude), parseFloat(this.new_longitude));
                        }
                    });

                    // If 'new' is initially active, init map
                    if (this.address_selection_type === 'new') {
                        this.initMap(parseFloat(this.new_latitude), parseFloat(this.new_longitude));
                    }
                },

                initMap(lat, lon) {
                    this.$nextTick(() => {
                        if (!this.map) {
                            this.map = L.map('map-checkout').setView([lat, lon], 14);
                            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                maxZoom: 19,
                                attribution: '© OpenStreetMap'
                            }).addTo(this.map);

                            this.marker = L.marker([lat, lon], { draggable: true }).addTo(this.map);

                            this.marker.on('dragend', () => {
                                const pos = this.marker.getLatLng();
                                this.updateCoords(pos.lat, pos.lng);
                            });

                            this.map.on('click', (e) => {
                                this.marker.setLatLng(e.latlng);
                                this.updateCoords(e.latlng.lat, e.latlng.lng);
                            });

                            this.drawActiveDistrictsPolygons();
                        } else {
                            this.map.setView([lat, lon], 14);
                            this.marker.setLatLng([lat, lon]);
                        }

                        setTimeout(() => {
                            this.map.invalidateSize();
                        }, 200);
                    });
                },

                updateCoords(lat, lon) {
                    this.new_latitude = lat.toFixed(7);
                    this.new_longitude = lon.toFixed(7);
                    this.reverseGeocode(lat, lon);
                },

                drawActiveDistrictsPolygons() {
                    if (!this.map) return;
                    if (this.coveragePolygons) {
                        this.coveragePolygons.forEach(p => this.map.removeLayer(p));
                    }
                    this.coveragePolygons = [];

                    let delay = 0;
                    this.activeDistricts.forEach(qualified => {
                        const parts = qualified.split('|');
                        if (parts.length !== 3) return;

                        const cacheKey = `nikama_geojson_${qualified.replace(/\|/g, '_')}`;
                        const cached = localStorage.getItem(cacheKey);

                        let needsFetch = true;
                        if (cached) {
                            try {
                                const geojson = JSON.parse(cached);
                                if (geojson && (geojson.type === 'Polygon' || geojson.type === 'MultiPolygon')) {
                                    this.addPolygonToMap(geojson, qualified);
                                    needsFetch = false;
                                } else {
                                    localStorage.removeItem(cacheKey);
                                }
                            } catch (e) {
                                localStorage.removeItem(cacheKey);
                            }
                        }

                        if (needsFetch) {
                            setTimeout(() => {
                                const query = `${parts[2]}, ${parts[1]}, ${parts[0]}, Peru`;
                                fetch(`https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(query)}&format=json&polygon_geojson=1&limit=5`)
                                    .then(res => res.json())
                                    .then(data => {
                                        if (data && data.length > 0) {
                                            const polyResult = data.find(item => item.geojson && (item.geojson.type === 'Polygon' || item.geojson.type === 'MultiPolygon'));
                                            const geojson = polyResult ? polyResult.geojson : data[0].geojson;
                                            if (geojson) {
                                                localStorage.setItem(cacheKey, JSON.stringify(geojson));
                                                this.addPolygonToMap(geojson, qualified);
                                            }
                                        }
                                    })
                                    .catch(err => console.error('Error fetching boundary for ' + qualified, err));
                            }, delay);
                            delay += 1000;
                        }
                    });
                },

                addPolygonToMap(geojson, qualified) {
                    if (!this.map) return;
                    const parts = qualified.split('|');
                    const polygon = L.geoJSON(geojson, {
                        style: {
                            color: '#22c55e',
                            weight: 2,
                            fillColor: '#86efac',
                            fillOpacity: 0.35
                        }
                    });
                    polygon.bindPopup(`<b>Zona de Cobertura Activa</b><br>${parts[2]}, ${parts[1]} (${parts[0]})`);
                    polygon.addTo(this.map);
                    this.coveragePolygons.push(polygon);
                },

                reverseGeocode(lat, lon) {
                    this.showCoverageWarning = false;
                    this.geocodedDistrict = '';
                    fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lon}`)
                        .then(res => res.json())
                        .then(data => {
                            if (data && data.address) {
                                const addr = data.address;
                                const street = addr.road || addr.pedestrian || addr.suburb || '';
                                const num = addr.house_number || '';
                                
                                this.new_address = (street + ' ' + num).trim() || data.display_name;
                                
                                const rawDept = addr.state || '';
                                const rawProv = addr.region || addr.county || addr.city || '';
                                
                                // Prioritized list of candidate district names from OSM address object
                                const candidateNames = [];
                                if (addr.district) candidateNames.push(addr.district);
                                if (addr.city_district) candidateNames.push(addr.city_district);
                                if (addr.suburb) candidateNames.push(addr.suburb);
                                if (addr.town) candidateNames.push(addr.town);
                                if (addr.village) candidateNames.push(addr.village);
                                if (addr.city) candidateNames.push(addr.city);
                                if (addr.municipality) candidateNames.push(addr.municipality);
                                if (addr.county) candidateNames.push(addr.county);
                                if (addr.neighbourhood) candidateNames.push(addr.neighbourhood);

                                let matched = null;
                                for (const cand of candidateNames) {
                                    const cleanCand = cand.toLowerCase().trim();
                                    matched = this.activeDistricts.find(d => {
                                        const parts = d.split('|');
                                        if (parts.length === 3) {
                                            const matchDist = parts[2].toLowerCase().trim() === cleanCand;
                                            const matchProv = !rawProv || parts[1].toLowerCase().trim() === rawProv.toLowerCase().trim();
                                            const matchDept = !rawDept || parts[0].toLowerCase().trim() === rawDept.toLowerCase().trim();
                                            return matchDist && matchProv && matchDept;
                                        }
                                        return d.toLowerCase().trim() === cleanCand;
                                    });
                                    if (matched) break;
                                }
                                
                                if (matched) {
                                    this.selectedQualifiedDistrict = matched;
                                    const parts = matched.split('|');
                                    if (parts.length === 3) {
                                        this.new_department = parts[0];
                                        this.new_province = parts[1];
                                        this.new_district = parts[2];
                                    } else {
                                        this.new_district = matched;
                                        this.new_province = rawProv || 'Chiclayo';
                                        this.new_department = rawDept || 'Lambayeque';
                                    }
                                    this.showCoverageWarning = false;
                                } else {
                                    this.selectedQualifiedDistrict = '';
                                    this.new_district = '';
                                    this.geocodedDistrict = addr.suburb || addr.neighbourhood || addr.city || 'fuera de zona';
                                    this.showCoverageWarning = true;
                                    this.new_province = rawProv || 'Chiclayo';
                                    this.new_department = rawDept || 'Lambayeque';
                                }
                                
                                this.new_postal_code = addr.postcode || '';
                            }
                        })
                        .catch(err => {
                            console.error('Error reverse geocoding:', err);
                        });
                }
            }
        }
    </script>
</x-layouts.public>
