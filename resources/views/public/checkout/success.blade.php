<x-layouts.public>
    <x-slot:title>¡Pedido Confirmado! - Nikama</x-slot:title>

    <div class="max-w-3xl mx-auto px-4 py-16 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-slate-800 p-8 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-xl space-y-8 text-center">
            
            <!-- Icono Exito Animado -->
            <div class="flex justify-center">
                <div class="h-20 w-20 bg-emerald-50 dark:bg-emerald-950/30 rounded-full flex items-center justify-center border-2 border-emerald-500 shadow-inner">
                    <svg class="h-10 w-10 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
            </div>

            <!-- Titulo y Mensaje -->
            <div class="space-y-2">
                <h1 class="text-3xl font-extrabold font-['Outfit'] text-slate-800 dark:text-white">¡Gracias por tu compra!</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">Tu pedido ha sido recibido y se encuentra en estado de confirmación.</p>
                <div class="inline-block px-4 py-1.5 bg-slate-150/70 dark:bg-slate-700 text-slate-800 dark:text-white rounded-full text-sm font-bold font-mono">
                    N° de Pedido: {{ $order->order_number }}
                </div>
            </div>

            <!-- Detalles del Pedido -->
            <div class="border-t border-slate-100 dark:border-slate-700 pt-6 text-left space-y-4">
                <h3 class="text-base font-extrabold font-['Outfit'] text-slate-800 dark:text-white">Detalles del Envío</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-900/40 border border-slate-100 dark:border-slate-750 space-y-1">
                        <span class="text-xs text-slate-400 font-bold uppercase tracking-wider block">Entregar en:</span>
                        <p class="font-bold text-slate-850 dark:text-white">{{ $order->delivery_address }}</p>
                        @if ($order->delivery_reference)
                            <p class="text-xs text-slate-450 italic">Ref: {{ $order->delivery_reference }}</p>
                        @endif
                    </div>

                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-900/40 border border-slate-100 dark:border-slate-750 space-y-1">
                        <span class="text-xs text-slate-400 font-bold uppercase tracking-wider block">Método de pago:</span>
                        <p class="font-bold text-slate-850 dark:text-white capitalize">
                            @switch($order->payments->first()?->payment_method ?? 'cash')
                                @case('cash')
                                    💵 Efectivo contra entrega
                                    @break
                                @case('card')
                                    💳 Tarjeta de Crédito/Débito
                                    @break
                                @case('yape')
                                    📱 Yape
                                    @break
                                @case('plin')
                                    📲 Plin
                                    @break
                                @case('bank_transfer')
                                    🏦 Transferencia Bancaria
                                    @break
                                @case('pagoefectivo')
                                    🎫 PagoEfectivo
                                    @break
                                @default
                                    {{ $order->payments->first()?->payment_method }}
                            @endswitch
                        </p>
                        <p class="text-xs text-slate-450 font-semibold">
                            Estado del Pago: 
                            <span class="font-bold uppercase {{ $order->payment_status === 'paid' ? 'text-emerald-600' : 'text-amber-500' }}">
                                {{ $order->payment_status === 'paid' ? 'Pagado' : 'Pendiente' }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Resumen de Costos -->
            <div class="border-t border-slate-100 dark:border-slate-700 pt-6 text-left">
                <div class="bg-slate-50 dark:bg-slate-900/40 border border-slate-100 dark:border-slate-750 rounded-2xl p-5 space-y-2.5 text-sm">
                    <div class="flex justify-between text-slate-600 dark:text-slate-400">
                        <span>Subtotal</span>
                        <span class="font-semibold text-slate-850 dark:text-white">S/ {{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-slate-600 dark:text-slate-400">
                        <span>Envío</span>
                        <span class="font-semibold text-slate-850 dark:text-white">S/ {{ number_format($order->delivery_fee, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-base font-black text-slate-800 dark:text-white border-t border-slate-100 dark:border-slate-700 pt-2.5">
                        <span>Total pagado:</span>
                        <span>S/ {{ number_format($order->total, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center pt-4">
                <a href="{{ route('public.welcome') }}" 
                   class="px-6 py-4 bg-luffy-red hover:bg-luffy-red/90 text-white font-extrabold text-sm rounded-2xl shadow-lg shadow-luffy-red/20 hover:shadow-xl transition-all">
                    Seguir Comprando
                </a>
                
                <!-- If they have a customer orders dashboard, link there. Otherwise just welcome page. -->
                <a href="{{ route('orders.index') }}"
                   class="px-6 py-4 bg-slate-50 border border-slate-200 hover:bg-slate-100 dark:bg-slate-750 dark:border-slate-700 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-sm rounded-2xl transition-all">
                    Ver mis Pedidos
                </a>
            </div>
        </div>
    </div>
</x-layouts.public>
