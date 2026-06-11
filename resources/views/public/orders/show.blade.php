<x-layouts.public>
    <x-slot:title>Seguimiento de Pedido {{ $order->order_number }} - Nikama</x-slot:title>

    <div class="max-w-7xl mx-auto px-4 py-8 sm:px-6 lg:px-8"
         x-data="orderTracking('{{ $order->id }}', '{{ $order->status }}', {{ $order->getBusinessLocation()?->latitude ?? -6.7719 }}, {{ $order->getBusinessLocation()?->longitude ?? -79.8441 }}, {{ $order->delivery_latitude ?? -6.7725 }}, {{ $order->delivery_longitude ?? -79.8465 }})">
        
        <!-- Breadcrumbs -->
        <nav class="flex text-xs font-semibold text-slate-500 dark:text-slate-400 mb-6" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1.5 md:space-x-2">
                <li class="inline-flex items-center">
                    <a href="/" class="hover:text-slate-800 dark:hover:text-white transition-colors">Inicio</a>
                </li>
                <li class="flex items-center">
                    <svg class="w-3 h-3 text-slate-400 mx-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    <a href="{{ route('orders.index') }}" class="hover:text-slate-800 dark:hover:text-white transition-colors">Mis Pedidos</a>
                </li>
                <li class="flex items-center">
                    <svg class="w-3 h-3 text-slate-400 mx-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    <span class="text-slate-800 dark:text-slate-200 font-bold" aria-current="page">Seguimiento</span>
                </li>
            </ol>
        </nav>

        <!-- Alertas de Sesión -->
        @if (session('success'))
            <div class="p-4 bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-250 dark:border-emerald-800/30 text-emerald-800 dark:text-emerald-350 rounded-2xl flex items-center gap-3 text-sm font-semibold mb-6">
                <span>✓</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="p-4 bg-rose-50 dark:bg-rose-950/20 border border-rose-250 dark:border-rose-800/30 text-rose-800 dark:text-rose-350 rounded-2xl flex items-center gap-3 text-sm font-semibold mb-6">
                <span>✕</span>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- COLUMNA IZQUIERDA: Stepper, Detalles e Info -->
            <div class="lg:col-span-5 space-y-6">
                
                <!-- Encabezado del Pedido -->
                <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/80 p-6 shadow-sm space-y-4">
                    <div class="flex items-start justify-between">
                        <div class="space-y-1">
                            <span class="text-[10px] text-slate-400 uppercase font-black tracking-wider block">Pedido en Curso</span>
                            <h2 class="text-2xl font-black font-mono text-slate-850 dark:text-white">
                                {{ $order->order_number }}
                            </h2>
                            <p class="text-xs text-slate-550 dark:text-slate-400">
                                Sede: <span class="font-semibold">{{ $order->getBusinessLocation()?->business?->business_name ?? 'Nikama Chiclayo' }}</span>
                            </p>
                        </div>
                        <a href="{{ route('orders.index') }}" class="p-2 bg-slate-50 hover:bg-slate-100 dark:bg-slate-750 dark:hover:bg-slate-700 rounded-xl transition-all text-slate-500 dark:text-slate-350 cursor-pointer" aria-label="Volver al historial">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        </a>
                    </div>

                    <!-- Alerta de Cancelado -->
                    <template x-if="status === 'cancelled'">
                        <div class="p-4 bg-rose-50 dark:bg-rose-950/20 border border-rose-150 dark:border-rose-900/30 text-rose-800 dark:text-rose-355 rounded-2xl flex items-start gap-3">
                            <span class="text-xl font-bold">✕</span>
                            <div class="text-xs space-y-0.5">
                                <p class="font-extrabold">Pedido Cancelado</p>
                                <p>Nikama o el restaurante cancelaron tu pedido. Para más información contacta a soporte.</p>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- STEPPER DE PROGRESO (Solo visible si no está cancelado) -->
                <div x-show="status !== 'cancelled'" class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/80 p-6 shadow-sm">
                    <h3 class="text-sm text-slate-450 dark:text-slate-400 uppercase font-black tracking-wider mb-6">Estado de la Entrega</h3>
                    
                    <div class="relative pl-8 space-y-8">
                        <!-- Barra vertical conectora del stepper -->
                        <div class="absolute left-3.5 top-2.5 bottom-2.5 w-[2px] bg-slate-100 dark:bg-slate-700"></div>

                        <!-- Paso 1: Recibido (Pending) -->
                        <div class="relative flex items-start gap-4">
                            <div class="absolute -left-8 flex items-center justify-center w-7 h-7 rounded-full text-xs font-black transition-all z-10"
                                 :class="['pending', 'confirmed', 'preparing', 'on_the_way', 'delivered'].includes(status) 
                                         ? 'bg-emerald-500 text-white' 
                                         : 'bg-slate-100 dark:bg-slate-700 text-slate-400'">
                                ✓
                            </div>
                            <div class="space-y-0.5">
                                <h4 class="text-sm font-extrabold" :class="['pending', 'confirmed', 'preparing', 'on_the_way', 'delivered'].includes(status) ? 'text-slate-850 dark:text-white' : 'text-slate-400'">Recibido</h4>
                                <p class="text-xs text-slate-450 dark:text-slate-450">Hemos recibido tu orden correctamente.</p>
                            </div>
                        </div>

                        <!-- Paso 2: Confirmado (Confirmed) -->
                        <div class="relative flex items-start gap-4">
                            <div class="absolute -left-8 flex items-center justify-center w-7 h-7 rounded-full text-xs font-black transition-all z-10"
                                 :class="['confirmed', 'preparing', 'on_the_way', 'delivered'].includes(status) 
                                         ? 'bg-emerald-500 text-white' 
                                         : (status === 'pending' ? 'bg-amber-500 text-white animate-pulse' : 'bg-slate-100 dark:bg-slate-700 text-slate-400')">
                                <span x-text="['confirmed', 'preparing', 'on_the_way', 'delivered'].includes(status) ? '✓' : '2'"></span>
                            </div>
                            <div class="space-y-0.5">
                                <h4 class="text-sm font-extrabold" :class="['confirmed', 'preparing', 'on_the_way', 'delivered'].includes(status) ? 'text-slate-850 dark:text-white' : 'text-slate-400'">Confirmado</h4>
                                <p class="text-xs text-slate-450 dark:text-slate-450">El comercio ha aceptado tu pedido.</p>
                            </div>
                        </div>

                        <!-- Paso 3: Preparando (Preparing) -->
                        <div class="relative flex items-start gap-4">
                            <div class="absolute -left-8 flex items-center justify-center w-7 h-7 rounded-full text-xs font-black transition-all z-10"
                                 :class="['preparing', 'on_the_way', 'delivered'].includes(status) 
                                         ? 'bg-emerald-500 text-white' 
                                         : (status === 'confirmed' ? 'bg-amber-500 text-white animate-pulse' : 'bg-slate-100 dark:bg-slate-700 text-slate-400')">
                                <span x-text="['preparing', 'on_the_way', 'delivered'].includes(status) ? '✓' : '3'"></span>
                            </div>
                            <div class="space-y-0.5">
                                <h4 class="text-sm font-extrabold" :class="['preparing', 'on_the_way', 'delivered'].includes(status) ? 'text-slate-850 dark:text-white' : 'text-slate-400'">Preparando</h4>
                                <p class="text-xs text-slate-450 dark:text-slate-450">Tu pedido se está preparando en el establecimiento.</p>
                            </div>
                        </div>

                        <!-- Paso 4: En Camino (On The Way) -->
                        <div class="relative flex items-start gap-4">
                            <div class="absolute -left-8 flex items-center justify-center w-7 h-7 rounded-full text-xs font-black transition-all z-10"
                                 :class="status === 'delivered' 
                                         ? 'bg-emerald-500 text-white' 
                                         : (status === 'on_the_way' ? 'bg-emerald-500 text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-400')">
                                <span x-text="status === 'delivered' ? '✓' : '4'"></span>
                            </div>
                            <div class="space-y-0.5 flex-1">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-sm font-extrabold" :class="['on_the_way', 'delivered'].includes(status) ? 'text-slate-850 dark:text-white' : 'text-slate-400'">En Camino</h4>
                                    
                                    <!-- Moto animada para indicar delivery activo -->
                                    <template x-if="status === 'on_the_way'">
                                        <span class="text-xs bg-emerald-50 dark:bg-emerald-950/30 text-emerald-600 dark:text-emerald-400 px-2 py-0.5 rounded-full font-bold animate-bounce flex items-center gap-1">
                                            <span>🛵 Repartidor en ruta</span>
                                        </span>
                                    </template>
                                </div>
                                <p class="text-xs text-slate-450 dark:text-slate-450">El repartidor se dirige a tu ubicación.</p>
                                
                                <!-- Simulación de barra de progreso temporal -->
                                <template x-if="status === 'on_the_way'">
                                    <div class="mt-3 space-y-1">
                                        <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-2 overflow-hidden">
                                            <div class="bg-emerald-500 h-full transition-all duration-1000" :style="`width: ${percentage}%`"></div>
                                        </div>
                                        <div class="flex justify-between text-[10px] font-bold text-slate-450">
                                            <span x-text="`Restan ${Math.max(0, total - elapsed)}s`"></span>
                                            <span x-text="`${percentage}%`"></span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Paso 5: Entregado (Delivered) -->
                        <div class="relative flex items-start gap-4">
                            <div class="absolute -left-8 flex items-center justify-center w-7 h-7 rounded-full text-xs font-black transition-all z-10"
                                 :class="status === 'delivered' ? 'bg-emerald-500 text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-400'">
                                🎁
                            </div>
                            <div class="space-y-0.5">
                                <h4 class="text-sm font-extrabold" :class="status === 'delivered' ? 'text-slate-850 dark:text-white' : 'text-slate-400'">¡Entregado!</h4>
                                <p class="text-xs text-slate-450 dark:text-slate-450">¡Buen provecho! Tu pedido ha llegado a destino.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CALIFICAR REPARTIDOR (Solo visible si el pedido está entregado) -->
                <div x-show="status === 'delivered'" style="display: none;" 
                     class="bg-gradient-to-br from-purple-500/10 to-indigo-500/5 dark:from-purple-950/20 dark:to-indigo-950/10 border border-purple-200 dark:border-purple-800/30 rounded-3xl p-6 shadow-md space-y-4">
                    
                    @php
                        $review = $order->driverReviews->first();
                    @endphp

                    @if ($review)
                        <!-- Ya calificado -->
                        <div class="space-y-3">
                            <span class="text-[10px] text-purple-650 dark:text-purple-400 uppercase font-black tracking-wider block">Calificación Registrada</span>
                            <h3 class="text-sm font-extrabold text-slate-850 dark:text-white font-['Outfit']">Tu reseña sobre el repartidor</h3>
                            
                            <div class="flex items-center gap-1 text-amber-500 text-base">
                                @for ($starIdx = 1; $starIdx <= 5; $starIdx++)
                                    <span>{{ $starIdx <= $review->rating ? '★' : '☆' }}</span>
                                @endfor
                            </div>
                            
                            @if ($review->comment)
                                <p class="text-xs text-slate-600 dark:text-slate-350 italic bg-white/50 dark:bg-slate-900/40 p-3 rounded-2xl border border-slate-100 dark:border-slate-800">
                                    "{{ $review->comment }}"
                                </p>
                            @endif
                            <p class="text-[10px] text-slate-455">¡Gracias por ayudarnos a mejorar el servicio de Nikama!</p>
                        </div>
                    @else
                        <!-- Formulario de Calificación -->
                        <div class="space-y-3" x-data="{ userRating: 5 }">
                            <span class="text-[10px] text-purple-650 dark:text-purple-400 uppercase font-black tracking-wider block">Tu Repartidor llegó</span>
                            <h3 class="text-sm font-extrabold text-slate-850 dark:text-white font-['Outfit']">¿Cómo fue tu servicio de entrega?</h3>
                            <p class="text-xs text-slate-500">Califica la amabilidad, velocidad y profesionalismo de tu repartidor:</p>

                            <form action="{{ route('orders.rate-driver', $order) }}" method="POST" class="space-y-4">
                                @csrf
                                <!-- Estrellas Interactivas con Alpine.js -->
                                <div class="flex items-center gap-2">
                                    <input type="hidden" name="rating" :value="userRating">
                                    
                                    <div class="flex gap-1.5 text-2xl cursor-pointer">
                                        <template x-for="star in [1, 2, 3, 4, 5]">
                                            <button type="button" @click="userRating = star" 
                                                    class="focus:outline-none transition-transform hover:scale-125 duration-100"
                                                    :class="star <= userRating ? 'text-amber-500' : 'text-slate-300 dark:text-slate-650'">
                                                ★
                                            </button>
                                        </template>
                                    </div>
                                    <span class="text-xs font-black text-slate-700 dark:text-slate-300 ml-2" 
                                          x-text="['Pésimo 😡', 'Malo 😟', 'Regular 😐', 'Bueno 🙂', 'Excelente! 😍'][userRating - 1]"></span>
                                </div>

                                <div class="space-y-1">
                                    <label for="review_comment" class="font-bold text-slate-455 uppercase text-[9px] block">Comentario (Opcional)</label>
                                    <textarea id="review_comment" name="comment" rows="2" maxlength="1000"
                                              placeholder="Cuéntanos más detalles del servicio de entrega..."
                                              class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl p-3 text-xs text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500/50"></textarea>
                                </div>

                                <button type="submit" 
                                        class="w-full py-2.5 bg-purple-600 hover:bg-purple-700 text-white font-extrabold text-xs rounded-xl shadow-md transition cursor-pointer text-center uppercase tracking-wider">
                                    Enviar Calificación
                                </button>
                            </form>
                        </div>
                    @endif
                </div>

                <!-- DETALLES DE DIRECCIÓN Y PAGO -->
                <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/80 p-6 shadow-sm space-y-4">
                    <h3 class="text-sm text-slate-450 dark:text-slate-400 uppercase font-black tracking-wider">Datos de Entrega</h3>
                    
                    <div class="text-xs space-y-3">
                        <div class="space-y-1">
                            <span class="text-[10px] text-slate-400 uppercase font-bold block">Destino</span>
                            <p class="font-extrabold text-slate-800 dark:text-white">{{ $order->delivery_address }}</p>
                            @if ($order->delivery_reference)
                                <p class="text-slate-450 italic">Ref: {{ $order->delivery_reference }}</p>
                            @endif
                        </div>

                        <div class="grid grid-cols-2 gap-4 border-t border-slate-50 dark:border-slate-750/30 pt-3">
                            <div class="space-y-0.5">
                                <span class="text-[10px] text-slate-400 uppercase font-bold block">Método de Pago</span>
                                <p class="font-bold text-slate-700 dark:text-slate-300 capitalize">
                                    @switch($order->payments->first()?->payment_method ?? 'cash')
                                        @case('cash')
                                            💵 Efectivo
                                            @break
                                        @case('card')
                                            💳 Tarjeta
                                            @break
                                        @case('yape')
                                            📱 Yape
                                            @break
                                        @case('plin')
                                            📲 Plin
                                            @break
                                        @default
                                            {{ $order->payments->first()?->payment_method }}
                                    @endswitch
                                </p>
                            </div>
                            <div class="space-y-0.5">
                                <span class="text-[10px] text-slate-400 uppercase font-bold block">Estado de Pago</span>
                                <span class="inline-block px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider"
                                      :class="status === 'delivered' || '{{ $order->payment_status }}' === 'paid'
                                              ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 border border-emerald-100'
                                              : 'bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 border border-amber-100'">
                                    <span x-text="status === 'delivered' || '{{ $order->payment_status }}' === 'paid' ? 'Pagado' : 'Pendiente'"></span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CONTENIDO DEL PEDIDO (PRODUCTOS) -->
                <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/80 p-6 shadow-sm space-y-4">
                    <h3 class="text-sm text-slate-450 dark:text-slate-400 uppercase font-black tracking-wider">Tu Pedido</h3>
                    
                    <div class="divide-y divide-slate-100 dark:divide-slate-750">
                        @foreach ($order->items as $item)
                            <div class="py-3 flex justify-between gap-4 text-xs">
                                <div class="space-y-1">
                                    <p class="font-extrabold text-slate-800 dark:text-white">
                                        <span class="text-luffy-red-dark dark:text-luffy-red">{{ $item->quantity }}x</span> {{ $item->product_name }}
                                    </p>
                                    @if ($item->options->isNotEmpty())
                                        <p class="text-[10px] text-slate-450 dark:text-slate-450 leading-relaxed pl-3 border-l-2 border-slate-100 dark:border-slate-700">
                                            @foreach ($item->options as $opt)
                                                + {{ $opt->option_name }} (+S/ {{ number_format($opt->additional_price, 2) }})@if(!$loop->last), @endif
                                            @endforeach
                                        </p>
                                    @endif
                                </div>
                                <span class="font-semibold text-slate-800 dark:text-slate-200">S/ {{ number_format($item->subtotal, 2) }}</span>
                            </div>
                        @endforeach
                    </div>

                    <div class="border-t border-slate-100 dark:border-slate-700 pt-4 space-y-2 text-xs">
                        <div class="flex justify-between text-slate-500 dark:text-slate-400">
                            <span>Subtotal</span>
                            <span class="font-semibold text-slate-700 dark:text-slate-350">S/ {{ number_format($order->subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-slate-500 dark:text-slate-400">
                            <span>Costo de Envío</span>
                            <span class="font-semibold text-slate-700 dark:text-slate-350">S/ {{ number_format($order->delivery_fee, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm font-black text-slate-850 dark:text-white pt-2 border-t border-slate-50 dark:border-slate-750/30">
                            <span>Total</span>
                            <span>S/ {{ number_format($order->total, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- PANEL DE SIMULACIÓN DE DELIVERY (EXCLUSIVO SELLERS/SUPER ADMIN) -->
                @if($canManage)
                    <div class="bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-800/80 dark:to-slate-900/60 rounded-3xl border border-slate-200 dark:border-slate-700 p-6 shadow-lg space-y-4">
                        <div class="flex items-center gap-2">
                            <span class="text-lg">⚙️</span>
                            <h3 class="text-sm font-extrabold font-['Outfit'] text-slate-800 dark:text-white">Simulador de Delivery (Sellers / Admin)</h3>
                        </div>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-normal">
                            Controla el progreso del repartidor en el backend para visualizar el cambio de estados y el movimiento en tiempo real de la moto.
                        </p>

                        <div class="grid grid-cols-2 gap-2 text-[11px]">
                            <button @click="simulateStatusChange('confirmed')"
                                    class="p-2.5 bg-blue-50 hover:bg-blue-100 dark:bg-blue-950/30 dark:hover:bg-blue-900/20 text-blue-700 dark:text-blue-300 font-bold rounded-xl border border-blue-200 dark:border-blue-800/50 transition cursor-pointer text-center"
                                    :class="status === 'confirmed' ? 'ring-2 ring-blue-500' : ''">
                                ✓ Confirmar
                            </button>
                            
                            <button @click="simulateStatusChange('preparing')"
                                    class="p-2.5 bg-amber-50 hover:bg-amber-100 dark:bg-amber-950/30 dark:hover:bg-amber-900/20 text-amber-700 dark:text-amber-300 font-bold rounded-xl border border-amber-255 dark:border-amber-800/50 transition cursor-pointer text-center"
                                    :class="status === 'preparing' ? 'ring-2 ring-amber-500' : ''">
                                🍳 Cocinar
                            </button>
                            
                            <button @click="simulateStatusChange('on_the_way')"
                                    class="p-2.5 bg-purple-50 hover:bg-purple-100 dark:bg-purple-950/30 dark:hover:bg-purple-900/20 text-purple-700 dark:text-purple-300 font-bold rounded-xl border border-purple-200 dark:border-purple-800/50 transition cursor-pointer text-center col-span-2"
                                    :class="status === 'on_the_way' ? 'ring-2 ring-purple-500 animate-pulse' : ''">
                                🛵 Enviar Repartidor
                            </button>
                            
                            <button @click="simulateStatusChange('delivered')"
                                    class="p-2.5 bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/30 dark:hover:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300 font-bold rounded-xl border border-emerald-200 dark:border-emerald-800/50 transition cursor-pointer text-center"
                                    :class="status === 'delivered' ? 'ring-2 ring-emerald-500' : ''">
                                🎁 Entregado
                            </button>

                            <button @click="simulateStatusChange('cancelled')"
                                    class="p-2.5 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/30 dark:hover:bg-rose-900/20 text-rose-750 dark:text-rose-300 font-bold rounded-xl border border-rose-200 dark:border-rose-800/50 transition cursor-pointer text-center"
                                    :class="status === 'cancelled' ? 'ring-2 ring-rose-500' : ''">
                                ✕ Cancelar
                            </button>

                            <button @click="simulateStatusChange('confirmed')"
                                    class="p-2.5 bg-slate-200 hover:bg-slate-350 dark:bg-slate-700 dark:hover:bg-slate-650 text-slate-700 dark:text-slate-200 font-bold rounded-xl transition cursor-pointer text-center col-span-2 mt-2">
                                🔄 Reiniciar Viaje (Repetir Demo)
                            </button>
                        </div>
                    </div>
                @endif

            </div>

            <!-- COLUMNA DERECHA: Mapa interactivo -->
            <div class="lg:col-span-7 space-y-4 lg:sticky lg:top-24">
                
                <!-- Encabezado del Mapa -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 bg-white dark:bg-slate-800 p-4 rounded-t-3xl border-t border-x border-slate-100 dark:border-slate-700/80 shadow-sm">
                    <div class="flex items-center gap-2">
                        <span class="relative flex h-2.5 w-2.5">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75"
                                  :class="status === 'on_the_way' ? 'bg-emerald-400' : 'bg-slate-400'"></span>
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5"
                                  :class="status === 'on_the_way' ? 'bg-emerald-500' : 'bg-slate-500'"></span>
                        </span>
                        <h3 class="text-xs font-extrabold text-slate-850 dark:text-white uppercase tracking-wider">Mapa de Reparto</h3>
                    </div>
                    <p class="text-[10px] text-slate-450 dark:text-slate-400 font-bold">
                        <span x-show="status === 'on_the_way'">Repartidor moviéndose... Actualizado en tiempo real</span>
                        <span x-show="status === 'delivered'">El repartidor ha llegado. Pedido entregado</span>
                        <span x-show="status === 'pending' || status === 'confirmed' || status === 'preparing'">Esperando despacho en cocina</span>
                    </p>
                </div>

                <!-- Contenedor del Mapa Leaflet -->
                <div class="relative bg-white dark:bg-slate-800 rounded-b-3xl border-b border-x border-slate-100 dark:border-slate-700/80 shadow-xl overflow-hidden">
                    <div id="tracking-map" class="w-full h-[350px] sm:h-[500px] z-10"></div>
                </div>

                <!-- Info adicional -->
                <div class="flex justify-between items-center text-[10px] text-slate-450 px-2">
                    <span>Nikama Delivery Service &copy; 2026</span>
                    <span>Lat/Lon de entrega: {{ number_format($order->delivery_latitude, 5) }}, {{ number_format($order->delivery_longitude, 5) }}</span>
                </div>
            </div>

        </div>
    </div>

    <!-- Alpine.js component data logic -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('orderTracking', (orderId, initialStatus, originLat, originLng, destLat, destLng) => ({
                status: initialStatus,
                percentage: 0,
                elapsed: 0,
                total: 60,
                map: null,
                driverMarker: null,
                routeLine: null,
                pollingInterval: null,

                init() {
                    this.initMap();
                    this.startPolling();
                },

                initMap() {
                    const origin = [originLat, originLng];
                    const destination = [destLat, destLng];

                    // Inicializar mapa
                    this.map = L.map('tracking-map', {
                        zoomControl: true,
                        scrollWheelZoom: true
                    }).setView(origin, 14);

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
                        html: `<div class='flex items-center justify-center w-10 h-10 bg-rose-500 border-2 border-white dark:border-slate-800 rounded-full shadow-lg text-lg hover:scale-110 transition-transform'>📍</div>`,
                        className: '',
                        iconSize: [40, 40],
                        iconAnchor: [20, 20]
                    });
                    L.marker(destination, { icon: customerIcon })
                        .addTo(this.map)
                        .bindPopup('<b class="font-sans text-xs">📍 Tu Dirección</b>');

                    // Ajustar zoom para abarcar ambos puntos
                    this.map.fitBounds([origin, destination], { padding: [50, 50] });

                    // Inicializar repartidor en la posición de origen
                    const driverDivHtml = `<div class='flex items-center justify-center w-10 h-10 bg-slate-400 border-2 border-white dark:border-slate-800 rounded-full shadow-lg text-lg'>🛵</div>`;
                    this.driverMarker = L.marker(origin, {
                        icon: L.divIcon({
                            html: driverDivHtml,
                            className: '',
                            iconSize: [40, 40],
                            iconAnchor: [20, 20]
                        })
                    }).addTo(this.map);
                },

                startPolling() {
                    this.fetchLocation();
                    this.pollingInterval = setInterval(() => {
                        this.fetchLocation();
                    }, 1000);
                },

                fetchLocation() {
                    fetch(`/orders/${orderId}/location`)
                        .then(res => res.json())
                        .then(data => {
                            this.status = data.status;
                            this.percentage = Math.round(data.simulation.percentage * 100);
                            this.elapsed = data.simulation.elapsed_seconds;
                            this.total = data.simulation.total_seconds;

                            const driverPos = [data.driver.latitude, data.driver.longitude];
                            this.driverMarker.setLatLng(driverPos);

                            // Cambiar icono del repartidor según el estado
                            if (this.status === 'delivered') {
                                this.driverMarker.setIcon(L.divIcon({
                                    html: `<div class='flex items-center justify-center w-10 h-10 bg-emerald-500 border-2 border-white dark:border-slate-800 rounded-full shadow-lg text-lg animate-bounce'>🎉</div>`,
                                    className: '',
                                    iconSize: [40, 40],
                                    iconAnchor: [20, 20]
                                }));
                            } else if (this.status === 'on_the_way') {
                                this.driverMarker.setIcon(L.divIcon({
                                    html: `<div class='relative flex items-center justify-center w-10 h-10 bg-emerald-500 border-2 border-white dark:border-slate-800 rounded-full shadow-lg text-lg'><span class='absolute -inset-1.5 rounded-full bg-emerald-500/30 animate-ping'></span>🛵</div>`,
                                    className: '',
                                    iconSize: [40, 40],
                                    iconAnchor: [20, 20]
                                }));

                                // Centrar mapa dinámicamente en el repartidor en camino
                                this.map.panTo(driverPos);
                            } else {
                                this.driverMarker.setIcon(L.divIcon({
                                    html: `<div class='flex items-center justify-center w-10 h-10 bg-slate-400 border-2 border-white dark:border-slate-800 rounded-full shadow-lg text-lg'>🛵</div>`,
                                    className: '',
                                    iconSize: [40, 40],
                                    iconAnchor: [20, 20]
                                }));
                            }
                        })
                        .catch(err => console.error('Error fetching location:', err));
                },

                simulateStatusChange(newStatus) {
                    fetch(`/orders/${orderId}/simulate-status`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ status: newStatus })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            this.status = data.status;
                            this.fetchLocation();
                        }
                    })
                    .catch(err => console.error('Error simulating status:', err));
                }
            }));
        });
    </script>
</x-layouts.public>
