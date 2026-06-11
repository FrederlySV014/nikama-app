<x-layouts.admin>
    <x-slot:title>Configuración de Pagos - Nikama Admin</x-slot:title>

    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-gradient-to-r from-slate-900 to-slate-800 dark:from-slate-950 dark:to-slate-900 p-6 rounded-3xl border border-slate-700 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-extrabold font-['Outfit'] text-white">Métodos de Pago del Sistema</h2>
                <p class="text-slate-450 text-sm mt-1">Activa o desactiva las opciones de pago que se le mostrarán al cliente durante el checkout.</p>
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

        <!-- Formulario -->
        <div class="bg-white dark:bg-slate-800 p-8 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm">
            <form action="{{ route('admin.settings.payments.update') }}" method="POST" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($settings as $method => $isActive)
                        <div class="p-5 rounded-2xl border border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/20 flex items-center justify-between transition-all hover:shadow-sm">
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    @switch($method)
                                        @case('cash')
                                            <span class="text-xl">💵</span>
                                            <span class="font-bold text-slate-800 dark:text-white capitalize">Efectivo contra entrega</span>
                                            @break
                                        @case('card')
                                            <span class="text-xl">💳</span>
                                            <span class="font-bold text-slate-800 dark:text-white capitalize">Tarjeta de Crédito / Débito</span>
                                            @break
                                        @case('yape')
                                            <span class="text-xl">📱</span>
                                            <span class="font-bold text-slate-800 dark:text-white capitalize">Yape</span>
                                            @break
                                        @case('plin')
                                            <span class="text-xl">📲</span>
                                            <span class="font-bold text-slate-800 dark:text-white capitalize">Plin</span>
                                            @break
                                        @case('bank_transfer')
                                            <span class="text-xl">🏦</span>
                                            <span class="font-bold text-slate-800 dark:text-white capitalize">Transferencia Bancaria</span>
                                            @break
                                        @case('pagoefectivo')
                                            <span class="text-xl">🎫</span>
                                            <span class="font-bold text-slate-800 dark:text-white capitalize">PagoEfectivo</span>
                                            @break
                                        @default
                                            <span class="font-bold text-slate-800 dark:text-white capitalize">{{ $method }}</span>
                                    @endswitch
                                </div>
                                <p class="text-xs text-slate-400">
                                    @switch($method)
                                        @case('cash')
                                            Permite a los clientes pagar en efectivo directamente al motorizado al recibir el pedido.
                                            @break
                                        @case('card')
                                            Habilita el cobro online con tarjetas Visa, Mastercard, AMEX y Diners Club (simulado).
                                            @break
                                        @case('yape')
                                            Habilita la billetera digital Yape a través de código QR y confirmación de pago (simulado).
                                            @break
                                        @case('plin')
                                            Habilita la billetera digital Plin a través de código QR y confirmación de pago (simulado).
                                            @break
                                        @case('bank_transfer')
                                            Permite subir un comprobante/voucher de transferencia a cuentas BCP o Interbank (simulado).
                                            @break
                                        @case('pagoefectivo')
                                            Genera un código CIP para pagar en agentes o banca por internet (simulado).
                                            @break
                                    @endswitch
                                </p>
                            </div>
                            
                            <!-- Toggle switch -->
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="hidden" name="methods[{{ $method }}]" value="0">
                                <input type="checkbox" name="methods[{{ $method }}]" value="1" class="sr-only peer" {{ $isActive ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-slate-200 dark:bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-350 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-luffy-red"></div>
                            </label>
                        </div>
                    @endforeach
                </div>

                <div class="flex justify-end pt-4 border-t border-slate-100 dark:border-slate-700">
                    <button type="submit" class="px-6 py-3 bg-luffy-red hover:bg-luffy-red/90 text-white font-bold text-sm rounded-2xl shadow-lg shadow-luffy-red/25 transition-all">
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>
