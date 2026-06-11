<x-layouts.driver>
    <x-slot:title>Reparto del Pedido #{{ $delivery->order->order_number }} - Nikama</x-slot:title>

    @php
        $order = $delivery->order;
        $businessLocation = $order->getBusinessLocation();
        $originLat = $businessLocation ? (float) $businessLocation->latitude : -6.7719;
        $originLng = $businessLocation ? (float) $businessLocation->longitude : -79.8441;
        $destLat = $order->delivery_latitude ? (float) $order->delivery_latitude : -6.7725;
        $destLng = $order->delivery_longitude ? (float) $order->delivery_longitude : -79.8465;

        $driverLocation = \App\Models\DriverLiveLocation::where('driver_profile_id', $delivery->driver_profile_id)->first();
        $driverLat = $driverLocation ? (float) $driverLocation->latitude : $originLat;
        $driverLng = $driverLocation ? (float) $driverLocation->longitude : $originLng;
    @endphp

    <div class="max-w-6xl mx-auto px-4 py-8 sm:px-6 lg:px-8"
         x-data="driverDeliveryTracker('{{ $delivery->id }}', {{ $originLat }}, {{ $originLng }}, {{ $destLat }}, {{ $destLng }}, {{ $driverLat }}, {{ $driverLng }}, '{{ $order->status }}')">

        <!-- Breadcrumbs -->
        <nav class="flex text-xs font-semibold text-slate-500 dark:text-slate-400 mb-6" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1.5 md:space-x-2">
                <li class="inline-flex items-center">
                    <a href="/" class="hover:text-slate-800 dark:hover:text-white transition-colors">Inicio</a>
                </li>
                <li class="flex items-center">
                    <svg class="w-3 h-3 text-slate-400 mx-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    <a href="{{ route('driver.dashboard') }}" class="hover:text-slate-800 dark:hover:text-white transition-colors">Mi Panel</a>
                </li>
                <li class="flex items-center">
                    <svg class="w-3 h-3 text-slate-400 mx-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    <span class="text-slate-800 dark:text-slate-200 font-bold" aria-current="page">Pantalla de Reparto</span>
                </li>
            </ol>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            <!-- COLUMNA IZQUIERDA: Info de Entrega, Simulador de Ruta y Acciones -->
            <div class="lg:col-span-5 space-y-6">
                
                <!-- Info Básica de Entrega -->
                <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-150 dark:border-slate-700/80 p-6 shadow-sm space-y-4">
                    <div class="flex items-start justify-between">
                        <div>
                            <span class="text-[10px] text-luffy-red dark:text-luffy-red-light uppercase font-black tracking-wider block">Envío Asignado</span>
                            <h2 class="text-2xl font-black font-mono text-slate-850 dark:text-white">
                                {{ $order->order_number }}
                            </h2>
                            <p class="text-xs text-slate-500 mt-1">
                                Establecimiento: <span class="font-bold text-slate-700 dark:text-slate-350">{{ $delivery->business->business_name }}</span>
                            </p>
                        </div>
                        <span class="px-3 py-1 bg-purple-50 dark:bg-purple-950/20 text-purple-700 dark:text-purple-400 text-[10px] font-black uppercase tracking-wider rounded-full border border-purple-200 dark:border-purple-800/30">
                            En Reparto
                        </span>
                    </div>

                    <div class="border-t border-slate-100 dark:border-slate-750 pt-4 space-y-3 text-xs">
                        <div class="space-y-1">
                            <span class="text-[10px] text-slate-400 uppercase font-bold block">Origen (Recoger en)</span>
                            <p class="font-bold text-slate-700 dark:text-slate-300">
                                🏪 {{ $delivery->business->business_name }}
                            </p>
                            <p class="text-slate-500 text-[11px]">{{ $businessLocation?->address ?? 'Sede Principal' }}</p>
                        </div>

                        <div class="space-y-1 border-t border-slate-50 dark:border-slate-750/30 pt-3">
                            <span class="text-[10px] text-slate-400 uppercase font-bold block">Destino (Entregar a)</span>
                            <p class="font-bold text-slate-700 dark:text-slate-300">
                                📍 {{ $order->delivery_address }}
                            </p>
                            @if ($order->delivery_reference)
                                <p class="text-slate-500 text-[11px] italic">Ref: {{ $order->delivery_reference }}</p>
                            @endif
                        </div>

                        <div class="grid grid-cols-2 gap-4 border-t border-slate-50 dark:border-slate-750/30 pt-3">
                            <div>
                                <span class="text-[10px] text-slate-400 uppercase font-bold block">Método de Pago</span>
                                <p class="font-bold text-slate-700 dark:text-slate-300 capitalize">
                                    @switch($order->payments->first()?->payment_method ?? 'cash')
                                        @case('cash') 💵 Efectivo @break
                                        @case('card') 💳 Tarjeta @break
                                        @case('yape') 📱 Yape @break
                                        @case('plin') 📲 Plin @break
                                        @default {{ $order->payments->first()?->payment_method }}
                                    @endswitch
                                </p>
                            </div>
                            <div>
                                <span class="text-[10px] text-slate-400 uppercase font-bold block">Cobro al Cliente</span>
                                <p class="font-extrabold text-slate-850 dark:text-white">
                                    @if(($order->payments->first()?->payment_method ?? 'cash') === 'cash')
                                        S/ {{ number_format($order->total, 2) }}
                                    @else
                                        S/ 0.00 <span class="text-[10px] text-emerald-500 font-medium block">(Ya pagado)</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- GPS Simulator Panel -->
                <div class="bg-gradient-to-br from-slate-900 to-indigo-950 text-white rounded-3xl p-6 shadow-xl border border-indigo-900/60 space-y-4">
                    <div class="flex items-center gap-2">
                        <span class="text-xl">🛵</span>
                        <h3 class="text-md font-extrabold font-['Outfit']">Simulador de Recorrido GPS</h3>
                    </div>
                    
                    <!-- Estado 1: Antes de iniciar ruta (Pedido aún en local) -->
                    <template x-if="!routeStarted">
                        <div class="space-y-4">
                            <div class="p-4 bg-indigo-950/60 border border-indigo-800/40 text-indigo-200 rounded-2xl text-xs space-y-2">
                                <p class="font-extrabold text-white text-sm">🏪 Retirar Pedido en Establecimiento</p>
                                <p>Dirígete al negocio para recoger los productos del cliente. Una vez los tengas contigo en tu vehículo, inicia la ruta de entrega para comenzar el rastreo GPS en tiempo real.</p>
                            </div>
                            <button @click="startRoute()"
                                    class="w-full py-4 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-white font-black text-xs rounded-2xl shadow-lg transition cursor-pointer text-center uppercase tracking-wider">
                                🛵 Iniciar Ruta (Recogí el pedido)
                            </button>
                        </div>
                    </template>

                    <!-- Estado 2: Ruta Iniciada (Rastreo activo) -->
                    <template x-if="routeStarted">
                        <div class="space-y-4">
                            <p class="text-xs text-indigo-200/80 leading-normal">
                                Simula tu desplazamiento desde el restaurante hacia el domicilio del cliente. Esto emitirá coordenadas en tiempo real al cliente para su mapa.
                            </p>

                            <!-- Progreso de Simulación -->
                            <div class="space-y-2 py-2">
                                <div class="flex items-center justify-between text-xs font-bold text-indigo-300">
                                    <span x-text="simulationActive ? 'Transmitiendo GPS...' : (currentStep >= totalSteps ? '¡Llegaste a destino!' : 'Transmisor Inactivo (Pausado)')"></span>
                                    <span x-text="`${percentage}%`"></span>
                                </div>
                                <div class="w-full bg-slate-800 rounded-full h-2.5 overflow-hidden border border-indigo-800/40">
                                    <div class="bg-emerald-400 h-full transition-all duration-1000" :style="`width: ${percentage}%`"></div>
                                </div>
                                <div class="flex justify-between text-[10px] font-mono text-indigo-300">
                                    <span x-text="`Paso: ${currentStep} / ${totalSteps}`"></span>
                                    <span x-text="`Lat: ${currentLat.toFixed(6)}, Lng: ${currentLng.toFixed(6)}`"></span>
                                </div>
                            </div>

                            <div class="flex flex-col sm:flex-row gap-3 pt-2">
                                <!-- Simular Recorrido Button -->
                                <button @click="startSimulation()"
                                        :disabled="simulationActive || currentStep >= totalSteps"
                                        class="flex-1 py-3 bg-indigo-600 hover:bg-indigo-500 disabled:opacity-50 text-white font-extrabold text-xs rounded-2xl transition cursor-pointer text-center">
                                    ⚡ Simular Recorrido (1h / 60 steps)
                                </button>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Completar Envío Acciones (Manual) -->
                <div x-show="routeStarted && percentage >= 100" style="display: none;" class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-150 dark:border-slate-700/80 p-6 shadow-sm space-y-4" x-data="{ showRejectInput: false }">
                    <h3 class="text-sm text-slate-455 dark:text-slate-400 uppercase font-black tracking-wider">Finalizar Entrega</h3>
                    <p class="text-xs text-slate-500 leading-normal">
                        Has llegado al destino. Por favor, selecciona el resultado de la entrega tras interactuar con el cliente.
                    </p>

                    <div class="space-y-3">
                        <!-- Botón Confirmar Entrega -->
                        <form action="{{ route('driver.deliveries.complete', $delivery) }}" method="POST">
                            @csrf
                            <button type="submit"
                                    class="w-full py-3.5 bg-emerald-500 hover:bg-emerald-600 text-white font-extrabold text-xs rounded-2xl shadow-lg transition cursor-pointer text-center uppercase tracking-wider">
                                ✓ Confirmar Entrega
                            </button>
                        </form>

                        <!-- Botón Cliente Rechazó Pedido -->
                        <button type="button" @click="showRejectInput = !showRejectInput"
                                class="w-full py-3 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/20 dark:hover:bg-rose-900/10 text-rose-600 dark:text-rose-400 font-bold text-xs rounded-2xl border border-rose-200 dark:border-rose-800/50 transition cursor-pointer text-center uppercase tracking-wider">
                            ✕ Cliente Rechazó Pedido
                        </button>

                        <!-- Campo para motivo de rechazo del cliente -->
                        <div x-show="showRejectInput" style="display: none;" class="pt-3 border-t border-slate-100 dark:border-slate-750 space-y-3">
                            <form action="{{ route('driver.deliveries.client-reject', $delivery) }}" method="POST" class="space-y-3">
                                @csrf
                                <div class="space-y-1">
                                    <label for="rejection_reason" class="font-bold text-slate-455 uppercase text-[9px] block">Motivo del Rechazo del Cliente</label>
                                    <textarea id="rejection_reason" name="rejection_reason" required minlength="5" maxlength="500" rows="3"
                                              placeholder="Escribe la razón detallada por la cual el cliente rechaza el pedido..."
                                              class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl p-3 text-xs text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-rose-500/50"></textarea>
                                </div>
                                <button type="submit"
                                        class="w-full py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs rounded-xl shadow-md transition cursor-pointer text-center">
                                    Enviar Rechazo de Cliente
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- En Camino / Tránsito -->
                <div x-show="routeStarted && percentage < 100" style="display: none;" class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-150 dark:border-slate-700/80 p-6 shadow-sm space-y-3 text-center">
                    <div class="w-12 h-12 bg-indigo-55/10 dark:bg-indigo-950/20 text-indigo-500 dark:text-indigo-400 rounded-full flex items-center justify-center mx-auto text-lg animate-pulse">
                        🛵
                    </div>
                    <h4 class="font-extrabold text-xs text-slate-850 dark:text-white font-['Outfit']">Pedido en Camino</h4>
                    <p class="text-[11px] text-slate-500 leading-relaxed max-w-xs mx-auto">
                        Estás en camino al destino. Los botones de finalización de entrega se activarán una vez que el recorrido simulado llegue al 100%.
                    </p>
                </div>

            </div>

            <!-- COLUMNA DERECHA: Mapa interactivo -->
            <div class="lg:col-span-7 space-y-4 lg:sticky lg:top-24">
                
                <!-- Encabezado del Mapa -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 bg-white dark:bg-slate-800 p-4 rounded-t-3xl border-t border-x border-slate-150 dark:border-slate-700/80 shadow-sm">
                    <div class="flex items-center gap-2">
                        <span class="relative flex h-2.5 w-2.5">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"
                                  :class="simulationActive ? 'bg-emerald-400' : 'bg-slate-400'"></span>
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"
                                  :class="simulationActive ? 'bg-emerald-500' : 'bg-slate-500'"></span>
                        </span>
                        <h3 class="text-xs font-extrabold text-slate-850 dark:text-white uppercase tracking-wider">Tu Ruta en Vivo</h3>
                    </div>
                    <p class="text-[10px] text-slate-450 dark:text-slate-400 font-bold" x-text="simulationActive ? 'Moviéndote a destino...' : 'Simulador apagado'"></p>
                </div>

                <!-- Contenedor del Mapa Leaflet -->
                <div class="relative bg-white dark:bg-slate-800 rounded-b-3xl border-b border-x border-slate-150 dark:border-slate-700/80 shadow-xl overflow-hidden">
                    <div id="driver-delivery-map" class="w-full h-[350px] sm:h-[500px] z-10"></div>
                </div>

                <!-- Info adicional -->
                <div class="flex justify-between items-center text-[10px] text-slate-450 px-2">
                    <span>Nikama Conductor &copy; 2026</span>
                    <span>Destino Lat/Lon: {{ number_format($destLat, 5) }}, {{ number_format($destLng, 5) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Alpine.js component logic -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('driverDeliveryTracker', (deliveryId, originLat, originLng, destLat, destLng, initialDriverLat, initialDriverLng, orderStatus) => ({
                deliveryId: deliveryId,
                originLat: originLat,
                originLng: originLng,
                destLat: destLat,
                destLng: destLng,
                currentLat: initialDriverLat,
                currentLng: initialDriverLng,
                currentStep: 0,
                totalSteps: 60,
                percentage: 0,
                simulationActive: false,
                routeStarted: orderStatus === 'on_the_way' || orderStatus === 'delivered',
                map: null,
                driverMarker: null,
                interval: null,

                init() {
                    this.initMap();
                    
                    // Si el pedido ya está en camino, calcular el progreso inicial según la base de datos
                    if (orderStatus === 'on_the_way') {
                        const totalDist = Math.sqrt(Math.pow(this.destLat - this.originLat, 2) + Math.pow(this.destLng - this.originLng, 2));
                        if (totalDist > 0) {
                            const coveredDist = Math.sqrt(Math.pow(this.currentLat - this.originLat, 2) + Math.pow(this.currentLng - this.originLng, 2));
                            const progress = Math.min(1.0, coveredDist / totalDist);
                            this.currentStep = Math.round(progress * this.totalSteps);
                            this.percentage = Math.round(progress * 100);
                        }
                    } else {
                        // De lo contrario, forzar inicio en la sede
                        this.currentLat = this.originLat;
                        this.currentLng = this.originLng;
                        this.currentStep = 0;
                        this.percentage = 0;
                    }
                },

                startRoute() {
                    this.routeStarted = true;
                    // Resetear coordenadas al origen al iniciar ruta
                    this.currentLat = this.originLat;
                    this.currentLng = this.originLng;
                    this.currentStep = 0;
                    this.percentage = 0;

                    // Emit initial position immediately to transition status to on_the_way on backend
                    this.tick();
                    // Start simulation and emission immediately upon picking up
                    this.startSimulation();
                },

                initMap() {
                    const origin = [this.originLat, this.originLng];
                    const destination = [this.destLat, this.destLng];
                    const initialDriver = [this.currentLat, this.currentLng];

                    // Inicializar mapa
                    this.map = L.map('driver-delivery-map', {
                        zoomControl: true,
                        scrollWheelZoom: true
                    }).setView(initialDriver, 14);

                    // Seleccionar tema del mapa según el modo oscuro
                    const isDark = document.documentElement.classList.contains('dark');
                    const tileUrl = isDark
                        ? 'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png'
                        : 'https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png';

                    L.tileLayer(tileUrl, {
                        attribution: '&copy; OpenStreetMap contributors &copy; CARTO'
                    }).addTo(this.map);

                    // Marcador del Cliente (Destino)
                    const customerIcon = L.divIcon({
                        html: `<div class='flex items-center justify-center w-10 h-10 bg-rose-500 border-2 border-white dark:border-slate-800 rounded-full shadow-lg text-lg'>📍</div>`,
                        className: '',
                        iconSize: [40, 40],
                        iconAnchor: [20, 20]
                    });
                    L.marker(destination, { icon: customerIcon })
                        .addTo(this.map)
                        .bindPopup('<b class="font-sans text-xs">📍 Dirección de Entrega</b>');

                    // Ajustar zoom para abarcar ruta
                    this.map.fitBounds([origin, destination], { padding: [50, 50] });

                    // Marcador del repartidor (Tú)
                    const driverDivHtml = `<div class='relative flex items-center justify-center w-10 h-10 bg-emerald-500 border-2 border-white dark:border-slate-800 rounded-full shadow-lg text-lg'><span class='absolute -inset-1.5 rounded-full bg-emerald-500/30 animate-ping'></span>🛵</div>`;
                    this.driverMarker = L.marker(initialDriver, {
                        icon: L.divIcon({
                            html: driverDivHtml,
                            className: '',
                            iconSize: [40, 40],
                            iconAnchor: [20, 20]
                        })
                    }).addTo(this.map);
                },

                tick() {
                    this.currentStep++;
                    if (this.currentStep > this.totalSteps) {
                        clearInterval(this.interval);
                        this.simulationActive = false;
                        this.currentStep = this.totalSteps;
                        this.percentage = 100;
                        return;
                    }

                    // Z-shaped street path interpolation
                    const progress = this.currentStep / this.totalSteps;
                    this.percentage = Math.round(progress * 100);

                    if (progress <= 0.3) {
                        const segProgress = progress / 0.3;
                        this.currentLat = this.originLat;
                        this.currentLng = this.originLng + (this.destLng - this.originLng) * 0.6 * segProgress;
                    } else if (progress <= 0.75) {
                        const segProgress = (progress - 0.3) / 0.45;
                        this.currentLat = this.originLat + (this.destLat - this.originLat) * segProgress;
                        this.currentLng = this.originLng + (this.destLng - this.originLng) * 0.6;
                    } else {
                        const segProgress = (progress - 0.75) / 0.25;
                        this.currentLat = this.destLat;
                        this.currentLng = (this.originLng + (this.destLng - this.originLng) * 0.6) + 
                                          (this.destLng - (this.originLng + (this.destLng - this.originLng) * 0.6)) * segProgress;
                    }

                    // Mover el pin en el mapa
                    this.driverMarker.setLatLng([this.currentLat, this.currentLng]);
                    this.map.panTo([this.currentLat, this.currentLng]);

                    // Enviar coordenadas por POST
                    fetch(`/driver/deliveries/${this.deliveryId}/emit-location`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            latitude: this.currentLat,
                            longitude: this.currentLng
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        console.log('Location emitted:', data);
                    })
                    .catch(err => console.error('Error emitting location:', err));
                },

                startSimulation() {
                    if (this.simulationActive) return;
                    this.simulationActive = true;
                    
                    const intervalTime = 1000; // Emitir cada 1 segundo (Simulación de 1 min en total)

                    this.interval = setInterval(() => {
                        this.tick();
                    }, intervalTime);
                }
            }));
        });
    </script>
</x-layouts.driver>
