<x-layouts.admin>
    <x-slot:title>Zonas de Cobertura - Nikama Admin</x-slot:title>

    <!-- Leaflet CSS & JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <div class="space-y-6" x-data="adminDistricts()">
        <!-- Header -->
        <div class="bg-gradient-to-r from-slate-900 to-slate-800 dark:from-slate-950 dark:to-slate-900 p-6 rounded-3xl border border-slate-700 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-extrabold font-['Outfit'] text-white">Zonas de Cobertura de Nikama</h2>
                <p class="text-slate-400 text-sm mt-1">Habilita o deshabilita los distritos operacionales de Nikama en diversas regiones de Perú.</p>
            </div>
        </div>

        <!-- Alertas de Sesión -->
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
            <div class="p-4 bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 rounded-3xl">
                <ul class="list-disc list-inside text-sm space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- Left Side: Config Form (Hierarchical list) -->
            <div class="lg:col-span-7 space-y-6">
                <div class="bg-white dark:bg-slate-800 p-6 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm">
                    <form action="{{ route('admin.settings.districts.update') }}" method="POST" class="space-y-6">
                        @csrf

                        <!-- Department Tabs Selector -->
                        <div class="space-y-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-450 dark:text-slate-400">Departamento / Región</label>
                            <div class="flex flex-wrap gap-2">
                                <template x-for="(provinces, dept) in hierarchy" :key="dept">
                                    <button 
                                        type="button" 
                                        @click="setDepartment(dept)"
                                        :class="selectedDepartment === dept ? 'bg-luffy-red text-white shadow-md shadow-luffy-red/20 border-luffy-red' : 'bg-slate-50 dark:bg-slate-900/40 border-slate-200 dark:border-slate-750 text-slate-650 dark:text-slate-350 hover:bg-slate-100 dark:hover:bg-slate-900/60'"
                                        class="px-4 py-2.5 rounded-xl border text-xs font-bold transition-all cursor-pointer"
                                        x-text="dept"
                                    ></button>
                                </template>
                            </div>
                        </div>

                        <!-- Provinces & Districts list inside selected Department -->
                        <div class="space-y-6 pt-4 border-t border-slate-100 dark:border-slate-700">
                            <h3 class="text-base font-bold text-slate-800 dark:text-white flex items-center gap-2">
                                <span>📍</span> Provincias y Distritos de <span class="text-luffy-red font-extrabold" x-text="selectedDepartment"></span>
                            </h3>

                            <div class="space-y-4">
                                <template x-for="(districts, province) in hierarchy[selectedDepartment]" :key="province">
                                    <div class="border border-slate-100 dark:border-slate-750/80 rounded-2xl overflow-hidden bg-slate-50/30 dark:bg-slate-900/10">
                                        <!-- Header of province -->
                                        <div class="bg-slate-50 dark:bg-slate-900/60 px-5 py-3.5 border-b border-slate-100 dark:border-slate-750/80 flex items-center justify-between">
                                            <span class="text-sm font-extrabold text-slate-800 dark:text-white" x-text="'Provincia: ' + province"></span>
                                            <div class="flex items-center gap-2">
                                                <button type="button" @click="checkAllInProvince(province)" class="text-[10px] font-bold text-luffy-red hover:underline cursor-pointer">Activar Todos</button>
                                                <span class="text-slate-300 dark:text-slate-700">|</span>
                                                <button type="button" @click="uncheckAllInProvince(province)" class="text-[10px] font-bold text-slate-400 hover:underline cursor-pointer">Desactivar</button>
                                            </div>
                                        </div>

                                        <!-- Grid of districts -->
                                        <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                            <template x-for="dist in districts" :key="dist">
                                                @php
                                                    $qualifiedVal = "'+selectedDepartment+'|'+province+'|'+dist+'";
                                                @endphp
                                                <label 
                                                    class="flex items-center gap-3 p-3 rounded-xl border transition-all cursor-pointer hover:bg-slate-50/50 dark:hover:bg-slate-900/20"
                                                    :class="isChecked(selectedDepartment, province, dist) ? 'border-luffy-red/30 bg-rose-50/5 dark:bg-slate-850/20' : 'border-slate-100 dark:border-slate-750'"
                                                >
                                                    <input 
                                                        type="checkbox" 
                                                        name="districts[]" 
                                                        :value="selectedDepartment + '|' + province + '|' + dist"
                                                        :checked="isChecked(selectedDepartment, province, dist)"
                                                        @change="toggleDistrict(selectedDepartment, province, dist, $event.target.checked)"
                                                        class="h-4 w-4 rounded text-luffy-red border-slate-350 focus:ring-luffy-red cursor-pointer"
                                                    >
                                                    <div class="flex flex-col">
                                                        <span class="text-xs font-bold text-slate-800 dark:text-white" x-text="dist"></span>
                                                    </div>
                                                </label>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Submit block -->
                        <div class="flex justify-end pt-4 border-t border-slate-100 dark:border-slate-700">
                            <button type="submit" class="px-6 py-3 bg-luffy-red hover:bg-luffy-red/90 text-white font-bold text-sm rounded-2xl shadow-lg shadow-luffy-red/25 transition-all">
                                Guardar Coberturas
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Right Side: Interactive Leaflet Map -->
            <div class="lg:col-span-5 space-y-4">
                <div class="bg-white dark:bg-slate-800 p-6 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm sticky top-24 space-y-4">
                    <div class="border-b border-slate-100 dark:border-slate-700 pb-3">
                        <h3 class="text-base font-extrabold font-['Outfit'] text-slate-800 dark:text-white flex items-center gap-2">
                            <span>🗺️</span> Mapa de Cobertura Activa
                        </h3>
                        <p class="text-xs text-slate-400">Puntos de entrega habilitados y áreas de conversión.</p>
                    </div>

                    <div id="map-admin-districts" class="h-96 w-full rounded-2xl border border-slate-200 dark:border-slate-700 z-0"></div>

                    <!-- Statistics card overlay -->
                    <div class="bg-slate-50 dark:bg-slate-900/40 p-4 rounded-2xl border border-slate-100 dark:border-slate-750/50 flex justify-between items-center text-xs">
                        <div>
                            <span class="block text-slate-400 font-bold uppercase tracking-wider text-[10px]">Total Habilitados</span>
                            <span class="text-lg font-extrabold text-luffy-red mt-0.5 inline-block" x-text="checkedDistricts.length + ' distritos'"></span>
                        </div>
                        <div class="text-right">
                            <span class="block text-slate-400 font-bold uppercase tracking-wider text-[10px]">Departamento Actual</span>
                            <span class="text-sm font-bold text-slate-800 dark:text-slate-200 mt-1 inline-block" x-text="selectedDepartment"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function adminDistricts() {
            return {
                hierarchy: @json($hierarchy),
                checkedDistricts: @json($activeDistricts),
                selectedDepartment: '',
                map: null,
                markers: [],
                coveragePolygons: [],

                // Department capital coordinates center
                centers: {
                    'Lambayeque': [-6.7719, -79.8389],
                    'Lima': [-12.0464, -77.0428],
                    'La Libertad': [-8.1118, -79.0287],
                    'Arequipa': [-16.4090, -71.5375],
                    'Piura': [-5.1945, -80.6278]
                },

                // Approximate center offset coordinates for mapping markers dynamically
                districtCoordinates: {
                    // Lambayeque - Chiclayo
                    'Lambayeque|Chiclayo|Chiclayo': [-6.7719, -79.8389],
                    'Lambayeque|Chiclayo|José Leonardo Ortiz': [-6.7628, -79.8378],
                    'Lambayeque|Chiclayo|La Victoria': [-6.7885, -79.8475],
                    'Lambayeque|Chiclayo|Pimentel': [-6.8377, -79.9366],
                    'Lambayeque|Chiclayo|Reque': [-6.8647, -79.8188],
                    'Lambayeque|Chiclayo|Monsefú': [-6.8778, -79.8687],
                    'Lambayeque|Chiclayo|Zaña': [-6.9234, -79.5855],
                    'Lambayeque|Chiclayo|Chongoyape': [-6.6202, -79.3871],
                    // Lima - Lima
                    'Lima|Lima|Miraflores': [-12.1225, -77.0296],
                    'Lima|Lima|San Isidro': [-12.0974, -77.0353],
                    'Lima|Lima|Santiago de Surco': [-12.1278, -76.9839],
                    'Lima|Lima|Barranco': [-12.1488, -77.0215],
                    'Lima|Lima|San Borja': [-12.0994, -77.0017],
                    'Lima|Lima|La Molina': [-12.0792, -76.9248],
                    'Lima|Lima|Cercado de Lima': [-12.0464, -77.0428],
                    'Lima|Lima|Lince': [-12.0838, -77.0315],
                    'Lima|Lima|Magdalena del Mar': [-12.0898, -77.0673],
                    'Lima|Lima|Pueblo Libre': [-12.0792, -77.0592],
                    'Lima|Lima|San Miguel': [-12.0796, -77.0864],
                    'Lima|Lima|Surquillo': [-12.1150, -77.0161],
                    'Lima|Lima|Chorrillos': [-12.1648, -77.0234],
                    // La Libertad - Trujillo
                    'La Libertad|Trujillo|Trujillo': [-8.1118, -79.0287],
                    'La Libertad|Trujillo|Víctor Larco Herrera': [-8.1347, -79.0538],
                    'La Libertad|Trujillo|Huanchaco': [-8.0772, -79.1172],
                    // Arequipa - Arequipa
                    'Arequipa|Arequipa|Arequipa': [-16.4090, -71.5375],
                    'Arequipa|Arequipa|Cayma': [-16.3768, -71.5545],
                    'Arequipa|Arequipa|Yanahuara': [-16.3895, -71.5436],
                    // Piura - Piura
                    'Piura|Piura|Piura': [-5.1945, -80.6278],
                    'Piura|Piura|Castilla': [-5.1960, -80.6133]
                },

                init() {
                    // Set default selected department to the first key in hierarchy
                    this.selectedDepartment = Object.keys(this.hierarchy)[0];

                    this.$nextTick(() => {
                        this.initMap();
                    });
                },

                initMap() {
                    const center = this.centers[this.selectedDepartment] || [-6.7719, -79.8389];
                    this.map = L.map('map-admin-districts').setView(center, 12);
                    
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '© OpenStreetMap'
                    }).addTo(this.map);

                    this.renderMarkers();
                },

                setDepartment(dept) {
                    this.selectedDepartment = dept;
                    const center = this.centers[dept];
                    if (this.map && center) {
                        this.map.setView(center, 12);
                    }
                    this.renderMarkers();
                },

                isChecked(dept, province, dist) {
                    const val = `${dept}|${province}|${dist}`;
                    return this.checkedDistricts.includes(val);
                },

                toggleDistrict(dept, province, dist, isChecked) {
                    const val = `${dept}|${province}|${dist}`;
                    if (isChecked) {
                        if (!this.checkedDistricts.includes(val)) {
                            this.checkedDistricts.push(val);
                        }
                    } else {
                        this.checkedDistricts = this.checkedDistricts.filter(d => d !== val);
                    }
                    this.renderMarkers();
                },

                checkAllInProvince(province) {
                    const districts = this.hierarchy[this.selectedDepartment][province] || [];
                    districts.forEach(dist => {
                        const val = `${this.selectedDepartment}|${province}|${dist}`;
                        if (!this.checkedDistricts.includes(val)) {
                            this.checkedDistricts.push(val);
                        }
                    });
                    this.renderMarkers();
                },

                uncheckAllInProvince(province) {
                    const districts = this.hierarchy[this.selectedDepartment][province] || [];
                    districts.forEach(dist => {
                        const val = `${this.selectedDepartment}|${province}|${dist}`;
                        this.checkedDistricts = this.checkedDistricts.filter(d => d !== val);
                    });
                    this.renderMarkers();
                },

                drawActiveDistrictsPolygons() {
                    if (!this.map) return;
                    if (this.coveragePolygons) {
                        this.coveragePolygons.forEach(p => this.map.removeLayer(p));
                    }
                    this.coveragePolygons = [];

                    let delay = 0;
                    this.checkedDistricts.forEach(qualified => {
                        const parts = qualified.split('|');
                        if (parts.length !== 3) return;

                        // Only draw polygons for the current selected department to keep it clean
                        if (parts[0] !== this.selectedDepartment) return;

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

                renderMarkers() {
                    if (!this.map) return;

                    // Clear previous markers
                    this.markers.forEach(m => this.map.removeLayer(m));
                    this.markers = [];

                    // Draw markers for checked districts belonging to current selected department
                    this.checkedDistricts.forEach(qualified => {
                        const parts = qualified.split('|');
                        if (parts[0] === this.selectedDepartment) {
                            const coords = this.districtCoordinates[qualified] || this.centers[this.selectedDepartment];
                            if (coords) {
                                // Red marker for operational zones
                                const icon = L.divIcon({
                                    className: 'custom-div-icon',
                                    html: `<div class='flex items-center justify-center w-6 h-6 rounded-full bg-luffy-red text-white border-2 border-white shadow-md font-bold text-[9px]'>✔</div>`,
                                    iconSize: [24, 24],
                                    iconAnchor: [12, 12]
                                });

                                const marker = L.marker(coords, { icon: icon })
                                    .bindPopup(`<b>${parts[2]}</b><br>${parts[1]}, ${parts[0]}<br><span class='text-xs text-emerald-600 font-bold'>● Habilitado</span>`)
                                    .addTo(this.map);
                                
                                this.markers.push(marker);
                            }
                        }
                    });

                    this.drawActiveDistrictsPolygons();
                }
            };
        }
    </script>

    <style>
        .custom-div-icon {
            background: none !important;
            border: none !important;
        }
    </style>
</x-layouts.admin>
