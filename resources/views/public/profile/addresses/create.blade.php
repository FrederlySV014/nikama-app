<x-layouts.public>
    <x-slot:title>Nueva Dirección - Nikama</x-slot:title>

    <div class="max-w-7xl mx-auto px-4 py-8 sm:px-6 lg:px-8" x-data="addressForm()">
        <!-- Breadcrumbs -->
        <nav class="flex text-xs font-semibold text-slate-500 dark:text-slate-400 mb-6" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1.5 md:space-x-2">
                <li class="inline-flex items-center">
                    <a href="/" class="hover:text-slate-800 dark:hover:text-white transition-colors">Inicio</a>
                </li>
                <li class="flex items-center">
                    <svg class="w-3 h-3 text-slate-400 mx-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    <a href="{{ route('profile.addresses.index') }}" class="hover:text-slate-800 dark:hover:text-white transition-colors">Mi Perfil</a>
                </li>
                <li class="flex items-center">
                    <svg class="w-3 h-3 text-slate-400 mx-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    <a href="{{ route('profile.addresses.index') }}" class="hover:text-slate-800 dark:hover:text-white transition-colors">Mis Direcciones</a>
                </li>
                <li class="flex items-center">
                    <svg class="w-3 h-3 text-slate-400 mx-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    <span class="text-slate-800 dark:text-slate-200 font-bold" aria-current="page">Nueva Dirección</span>
                </li>
            </ol>
        </nav>

        <!-- Main Container Split -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Left Side: Form Details -->
            <div class="lg:col-span-7 bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/80 p-6 sm:p-8 shadow-sm">
                <div class="mb-6 pb-6 border-b border-slate-100 dark:border-slate-700/50">
                    <h1 class="text-2xl font-extrabold font-['Outfit'] text-slate-900 dark:text-white">Agregar Nueva Dirección</h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Completa los campos detallados para registrar tu dirección de entrega.</p>
                </div>

                <form action="{{ route('profile.addresses.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- Address Type Selection Buttons -->
                    <div class="space-y-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-450 dark:text-slate-400">Tipo de Dirección</label>
                        <input type="hidden" name="address_type" :value="computedAddressType">
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            <button type="button" @click="selected_preset = 'home'"
                                :class="selected_preset === 'home' ? 'border-luffy-red bg-rose-50/30 dark:bg-rose-950/20 text-luffy-red' : 'border-slate-200 dark:border-slate-700 text-slate-650 dark:text-slate-350 hover:bg-slate-50 dark:hover:bg-slate-750/30'"
                                class="flex flex-col items-center justify-center p-3 rounded-2xl border-2 font-bold text-xs gap-1.5 transition-all cursor-pointer">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                                Casa
                            </button>
                            <button type="button" @click="selected_preset = 'work'"
                                :class="selected_preset === 'work' ? 'border-luffy-red bg-rose-50/30 dark:bg-rose-950/20 text-luffy-red' : 'border-slate-200 dark:border-slate-700 text-slate-650 dark:text-slate-350 hover:bg-slate-50 dark:hover:bg-slate-750/30'"
                                class="flex flex-col items-center justify-center p-3 rounded-2xl border-2 font-bold text-xs gap-1.5 transition-all cursor-pointer">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                Trabajo
                            </button>
                            <button type="button" @click="selected_preset = 'study'"
                                :class="selected_preset === 'study' ? 'border-luffy-red bg-rose-50/30 dark:bg-rose-950/20 text-luffy-red' : 'border-slate-200 dark:border-slate-700 text-slate-650 dark:text-slate-350 hover:bg-slate-50 dark:hover:bg-slate-750/30'"
                                class="flex flex-col items-center justify-center p-3 rounded-2xl border-2 font-bold text-xs gap-1.5 transition-all cursor-pointer">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                Estudios
                            </button>
                            <button type="button" @click="selected_preset = 'other'"
                                :class="selected_preset === 'other' ? 'border-luffy-red bg-rose-50/30 dark:bg-rose-950/20 text-luffy-red' : 'border-slate-200 dark:border-slate-700 text-slate-650 dark:text-slate-350 hover:bg-slate-50 dark:hover:bg-slate-750/30'"
                                class="flex flex-col items-center justify-center p-3 rounded-2xl border-2 font-bold text-xs gap-1.5 transition-all cursor-pointer">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Otro
                            </button>
                        </div>

                        <!-- Custom Address Type Text Input -->
                        <div x-show="selected_preset === 'other'" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 transform -translate-y-2"
                             x-transition:enter-end="opacity-100 transform translate-y-0"
                             class="pt-2">
                            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1">Nombre del Tipo Personalizado *</label>
                            <input x-model="custom_type" type="text" placeholder="Ej: Casa de playa, Gimnasio, etc." 
                                   :required="selected_preset === 'other'"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-955 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                        </div>
                    </div>

                    <!-- Label (Alias) & Postal Code -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-450 dark:text-slate-400">Etiqueta / Alias *</label>
                            <input x-model="form.label" name="label" type="text" placeholder="Ej: Mi Casa, Oficina" required
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                        </div>
                        <div class="space-y-1">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-450 dark:text-slate-400">Código Postal</label>
                            <input x-model="form.postal_code" name="postal_code" type="text" placeholder="Ej: 15074"
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                        </div>
                    </div>

                    <!-- Address & Reference -->
                    <div class="space-y-4">
                        <div class="space-y-1">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-450 dark:text-slate-400">Dirección Completa *</label>
                            <input x-model="form.address" name="address" type="text" placeholder="Calle, Avenida, Pasaje, Número..." required
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                        </div>

                        <div class="space-y-1">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-450 dark:text-slate-400">Referencia</label>
                            <input x-model="form.reference" name="reference" type="text" placeholder="Ej: Frente al parque, al costado del grifo..."
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                        </div>
                    </div>

                    <!-- Geolocation Fields (Department, Province, District) -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="space-y-1">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-450 dark:text-slate-400">Departamento</label>
                            <input x-model="form.department" name="department" type="text" placeholder="Lima"
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                        </div>
                        <div class="space-y-1">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-450 dark:text-slate-400">Provincia</label>
                            <input x-model="form.province" name="province" type="text" placeholder="Lima"
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                        </div>
                        <div class="space-y-1">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-455 dark:text-slate-400">Distrito *</label>
                            <select x-model="selectedQualifiedDistrict"
                                    @change="
                                        if (selectedQualifiedDistrict) {
                                            const parts = selectedQualifiedDistrict.split('|');
                                            if (parts.length === 3) {
                                                form.department = parts[0];
                                                form.province = parts[1];
                                                form.district = parts[2];
                                            } else {
                                                form.district = selectedQualifiedDistrict;
                                            }
                                            showCoverageWarning = false;
                                        } else {
                                            form.district = '';
                                            showCoverageWarning = false;
                                        }
                                    "
                                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-955 text-sm text-slate-850 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent cursor-pointer"
                            >
                                <option value="">Selecciona...</option>
                                <template x-for="dist in activeDistricts" :key="dist">
                                    <option :value="dist" x-text="getDistrictLabel(dist)"></option>
                                </template>
                            </select>
                            <input type="hidden" name="district" :value="form.district">
                        </div>
                    </div>

                    <!-- Delivery Notes & Country -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="md:col-span-2 space-y-1">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-450 dark:text-slate-400">Instrucciones / Notas de Entrega</label>
                            <textarea x-model="form.delivery_notes" name="delivery_notes" placeholder="Ej: Dejar en portería, portón negro, timbre no funciona..." rows="2"
                                class="w-full px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all resize-none"></textarea>
                        </div>
                        <div class="space-y-1">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-450 dark:text-slate-400">País</label>
                            <input x-model="form.country" name="country" type="text" readonly
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 text-sm text-slate-550 cursor-not-allowed focus:outline-none">
                        </div>
                    </div>

                    <!-- Lat/Lon Hidden or Read-only visual coordinates -->
                    <div class="grid grid-cols-2 gap-4 bg-slate-50 dark:bg-slate-900/50 p-4 rounded-2xl border border-slate-100 dark:border-slate-750">
                        <div class="space-y-1">
                            <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500">Latitud</label>
                            <input x-model="form.latitude" name="latitude" type="text" readonly
                                class="w-full bg-transparent border-0 p-0 text-xs text-slate-500 dark:text-slate-400 focus:ring-0 cursor-default">
                        </div>
                        <div class="space-y-1">
                            <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500">Longitud</label>
                            <input x-model="form.longitude" name="longitude" type="text" readonly
                                class="w-full bg-transparent border-0 p-0 text-xs text-slate-500 dark:text-slate-400 focus:ring-0 cursor-default">
                        </div>
                    </div>

                    <!-- Contact Name & Phone -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-450 dark:text-slate-400">Nombre de Contacto (Quien Recibe)</label>
                            <input x-model="form.contact_name" name="contact_name" type="text" placeholder="Ej: Juan Perez"
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                        </div>
                        <div class="space-y-1">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-450 dark:text-slate-400">Teléfono de Contacto</label>
                            <input x-model="form.contact_phone" name="contact_phone" type="text" placeholder="Ej: 999888777"
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                        </div>
                    </div>

                    <!-- Default Switch -->
                    <div class="flex items-center gap-3 pt-2">
                        <input type="checkbox" name="is_default" id="is_default_chk" value="1" x-model="form.is_default"
                               class="h-4 w-4 rounded text-luffy-red border-slate-350 focus:ring-luffy-red cursor-pointer">
                        <label for="is_default_chk" class="text-xs font-bold text-slate-700 dark:text-slate-300 cursor-pointer selection:bg-transparent">
                            Establecer como dirección predeterminada
                        </label>
                    </div>

                    <!-- Form Buttons -->
                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100 dark:border-slate-700/50">
                        <a href="{{ route('profile.addresses.index') }}" 
                           class="px-5 py-2.5 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-750 transition-all cursor-pointer">
                            Cancelar
                        </a>
                        <button type="submit" 
                            :disabled="!form.district"
                            :class="!form.district ? 'opacity-50 cursor-not-allowed bg-slate-300 dark:bg-slate-700 text-slate-500 dark:text-slate-500 shadow-none border-0' : 'bg-luffy-red hover:bg-luffy-red-hover text-white shadow-md shadow-luffy-red/10'"
                            class="px-6 py-2.5 rounded-xl text-xs font-bold transition-all cursor-pointer">
                            Guardar Dirección
                        </button>
                    </div>
                </form>
            </div>

            <!-- Right Side: Interactive Map Preview -->
            <div class="lg:col-span-5 bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/80 p-6 shadow-sm sticky top-6">
                <div class="mb-4">
                    <h3 class="text-lg font-bold font-['Outfit'] text-slate-900 dark:text-white">Ubicar en el Mapa</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Haz clic en el mapa o arrastra el marcador rojo al punto exacto de entrega.</p>
                </div>

                <div id="map-page" class="h-96 w-full rounded-2xl border border-slate-200 dark:border-slate-700/60 z-0"></div>
                
                <template x-if="showCoverageWarning">
                    <div class="mt-2 p-3 bg-amber-50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-900 rounded-xl text-[11px] text-amber-800 dark:text-amber-400 font-medium">
                        ⚠️ Nikama no cuenta con cobertura en <span class="font-bold" x-text="geocodedDistrict"></span>. Selecciona un distrito habilitado en la lista o arrastra el marcador a una zona activa (ej: Chiclayo, Miraflores).
                    </div>
                </template>

                <div class="mt-4 flex items-start gap-2.5 p-3.5 bg-amber-50/50 dark:bg-amber-950/10 border border-amber-100 dark:border-amber-900/30 rounded-2xl">
                    <svg class="w-5 h-5 text-amber-600 dark:text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <p class="text-xs text-amber-800 dark:text-amber-300 leading-relaxed font-medium">
                        El pin del mapa determina las coordenadas precisas que usará el motorizado para llegar a tu puerta. Asegúrate de posicionarlo correctamente.
                    </p>
                </div>
            </div>

        </div>
    </div>

    <script>
        function addressForm() {
            return {
                map: null,
                marker: null,
                selected_preset: 'home',
                custom_type: '',
                activeDistricts: @json($activeDistricts),
                selectedQualifiedDistrict: '',
                showCoverageWarning: false,
                geocodedDistrict: '',
                coveragePolygons: [],
                get computedAddressType() {
                    return this.selected_preset === 'other' ? this.custom_type : this.selected_preset;
                },
                form: {
                    label: '',
                    address: '',
                    reference: '',
                    delivery_notes: '',
                    district: '',
                    province: '',
                    department: '',
                    postal_code: '',
                    country: 'Peru',
                    contact_name: '{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}',
                    contact_phone: '{{ auth()->user()->phone }}',
                    latitude: '-6.7719', // Center of Lambayeque coverage
                    longitude: '-79.8389',
                    is_default: false
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
                    // Preselect first active district if available
                    if (this.activeDistricts.length > 0) {
                        this.selectedQualifiedDistrict = this.activeDistricts[0];
                        const parts = this.selectedQualifiedDistrict.split('|');
                        if (parts.length === 3) {
                            this.form.department = parts[0];
                            this.form.province = parts[1];
                            this.form.district = parts[2];
                        }
                    }
                    this.initMap(parseFloat(this.form.latitude), parseFloat(this.form.longitude));
                },

                initMap(lat, lon) {
                    this.$nextTick(() => {
                        this.map = L.map('map-page').setView([lat, lon], 14);
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

                        setTimeout(() => {
                            this.map.invalidateSize();
                        }, 200);
                    });
                },

                updateCoords(lat, lon) {
                    this.form.latitude = lat.toFixed(7);
                    this.form.longitude = lon.toFixed(7);
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
                                
                                this.form.address = (street + ' ' + num).trim() || data.display_name;
                                
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
                                        this.form.department = parts[0];
                                        this.form.province = parts[1];
                                        this.form.district = parts[2];
                                    } else {
                                        this.form.district = matched;
                                        this.form.province = rawProv || 'Chiclayo';
                                        this.form.department = rawDept || 'Lambayeque';
                                    }
                                    this.showCoverageWarning = false;
                                } else {
                                    this.selectedQualifiedDistrict = '';
                                    this.form.district = '';
                                    this.geocodedDistrict = addr.suburb || addr.neighbourhood || addr.city || 'fuera de zona';
                                    this.showCoverageWarning = true;
                                    this.form.province = rawProv || 'Chiclayo';
                                    this.form.department = rawDept || 'Lambayeque';
                                }
                                
                                this.form.postal_code = addr.postcode || '';
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
