<x-layouts.public>
    <x-slot:title>Pasarela de Pagos (Simulado) - Nikama</x-slot:title>

    <div class="max-w-3xl mx-auto px-4 py-12 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-slate-800 p-8 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-xl space-y-6">
            
            <!-- Simulador Banner -->
            <div class="bg-gradient-to-r from-amber-500/10 to-orange-500/15 p-5 rounded-2xl border border-amber-500/20 text-center">
                <span class="text-xs uppercase font-extrabold tracking-wider bg-amber-500/20 text-amber-700 dark:text-amber-400 px-2.5 py-1 rounded-full">Ambiente de Pruebas</span>
                <h2 class="text-xl font-bold text-slate-800 dark:text-white mt-2">Pasarela de Pagos Simulada</h2>
                <p class="text-xs text-slate-500 dark:text-slate-450 mt-1">Este es un entorno ficticio. No se debitará dinero real ni se procesarán datos sensibles.</p>
            </div>

            @if (session('error'))
                <div class="p-4 bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 rounded-2xl">
                    <p class="text-sm font-bold flex items-center gap-2">
                        <span>❌</span> {{ session('error') }}
                    </p>
                </div>
            @endif

            <!-- Info Pedido -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center p-5 rounded-2xl bg-slate-50 dark:bg-slate-900/40 border border-slate-100 dark:border-slate-750 gap-3">
                <div>
                    <span class="text-xs text-slate-400 uppercase font-bold tracking-wider">Detalles de la compra</span>
                    <h3 class="text-sm font-bold text-slate-700 dark:text-slate-300 mt-0.5">Pedido: {{ $payment->order->order_number }}</h3>
                </div>
                <div class="text-right sm:text-right">
                    <span class="text-xs text-slate-400 uppercase font-bold tracking-wider">Monto a Pagar</span>
                    <p class="text-2xl font-extrabold font-['Outfit'] text-slate-800 dark:text-white mt-0.5">S/ {{ number_format($payment->amount, 2) }}</p>
                </div>
            </div>

            <!-- Contenedor del Simulador según método -->
            <div class="border-t border-slate-100 dark:border-slate-700 pt-6">
                @switch($payment->payment_method)
                    
                    @case('card')
                        <!-- Simulación Tarjeta -->
                        <div class="space-y-6" x-data="{ cardType: 'visa', cardNumber: '', cardName: '', cardExpiry: '', cardCvv: '' }">
                            <h4 class="text-base font-bold text-slate-800 dark:text-white mb-4">Pago con Tarjeta de Crédito / Débito</h4>
                            
                            <!-- Tarjeta Gráfica Premium -->
                            <div class="relative w-full max-w-[340px] h-[190px] mx-auto rounded-2xl bg-gradient-to-br from-slate-900 via-slate-800 to-slate-950 p-5 text-white shadow-lg flex flex-col justify-between overflow-hidden">
                                <div class="flex justify-between items-center z-10">
                                    <div class="h-8 w-11 bg-amber-400/25 rounded-md border border-amber-300/40 relative">
                                        <!-- Chip ficticio -->
                                        <div class="absolute top-1.5 left-2.5 w-4 h-3.5 bg-amber-300/60 rounded"></div>
                                    </div>
                                    <span class="text-sm font-extrabold uppercase italic tracking-widest" x-text="cardType.toUpperCase()">Visa</span>
                                </div>
                                <div class="space-y-1 z-10">
                                    <p class="text-xs text-slate-400 tracking-wider">Número de tarjeta</p>
                                    <p class="text-lg font-bold font-mono tracking-widest" x-text="cardNumber || '•••• •••• •••• ••••'">•••• •••• •••• ••••</p>
                                </div>
                                <div class="flex justify-between items-center z-10">
                                    <div>
                                        <p class="text-[9px] text-slate-450 uppercase tracking-wider">Titular</p>
                                        <p class="text-xs font-bold truncate max-w-[180px]" x-text="cardName.toUpperCase() || 'NOMBRE APELLIDO'">NOMBRE APELLIDO</p>
                                    </div>
                                    <div>
                                        <p class="text-[9px] text-slate-450 uppercase tracking-wider">Expira</p>
                                        <p class="text-xs font-bold font-mono" x-text="cardExpiry || 'MM/AA'">MM/AA</p>
                                    </div>
                                </div>
                                <!-- Background effects -->
                                <div class="absolute -right-20 -bottom-20 w-48 h-48 rounded-full bg-luffy-red/10 blur-3xl"></div>
                            </div>

                            <!-- Campos de entrada simulados -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-bold text-slate-450 uppercase mb-1.5">Número de Tarjeta</label>
                                    <input type="text" x-model="cardNumber" placeholder="4557 1234 5678 9012" maxlength="19"
                                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-bold text-slate-450 uppercase mb-1.5">Nombre en la Tarjeta</label>
                                    <input type="text" x-model="cardName" placeholder="Juan Perez"
                                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-450 uppercase mb-1.5">Fecha de Vencimiento</label>
                                    <input type="text" x-model="cardExpiry" placeholder="MM/AA" maxlength="5"
                                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-450 uppercase mb-1.5">Código CVV</label>
                                    <input type="password" x-model="cardCvv" placeholder="123" maxlength="4"
                                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                                </div>
                            </div>
                        </div>
                        @break

                    @case('yape')
                        <!-- Simulación Yape -->
                        <div class="space-y-6 text-center">
                            <div class="flex justify-center mb-4">
                                <span class="bg-[#78007C] text-white px-5 py-2.5 rounded-2xl font-extrabold text-lg flex items-center gap-2 font-['Outfit']">
                                    📱 Pago con Yape
                                </span>
                            </div>

                            <p class="text-sm text-slate-600 dark:text-slate-350">Escanea el código QR desde tu celular o introduce tu número de celular registrado.</p>

                            <!-- QR Code Placeholder -->
                            <div class="w-44 h-44 bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 mx-auto rounded-2xl flex flex-col items-center justify-center p-3 relative shadow-inner">
                                <div class="grid grid-cols-4 grid-rows-4 gap-1.5 w-full h-full opacity-60">
                                    @for($i=0; $i<16; $i++)
                                        <div class="bg-slate-800 dark:bg-white rounded-sm {{ ($i % 3 === 0) ? 'invisible' : '' }}"></div>
                                    @endfor
                                </div>
                                <span class="absolute text-center bg-white dark:bg-slate-800 text-[10px] font-bold px-2 py-1 border rounded shadow-md text-[#78007C]">Escanea el QR</span>
                            </div>

                            <div class="max-w-xs mx-auto space-y-4 pt-4">
                                <div>
                                    <label class="block text-left text-xs font-bold text-slate-450 uppercase mb-1.5">Número de Celular Yape</label>
                                    <input type="text" placeholder="987 654 321"
                                           class="w-full px-4 py-2.5 text-center rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                                </div>
                                <div>
                                    <label class="block text-left text-xs font-bold text-slate-450 uppercase mb-1.5">Código de Aprobación Yape (6 dígitos)</label>
                                    <input type="text" placeholder="123456" maxlength="6"
                                           class="w-full px-4 py-2.5 text-center rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                                </div>
                            </div>
                        </div>
                        @break

                    @case('plin')
                        <!-- Simulación Plin -->
                        <div class="space-y-6 text-center">
                            <div class="flex justify-center mb-4">
                                <span class="bg-[#009FD0] text-white px-5 py-2.5 rounded-2xl font-extrabold text-lg flex items-center gap-2 font-['Outfit']">
                                    📲 Pago con Plin
                                </span>
                            </div>

                            <p class="text-sm text-slate-600 dark:text-slate-350">Escanea el código QR desde tu app bancaria asociada (Interbank, BBVA, Scotiabank).</p>

                            <!-- QR Code Placeholder -->
                            <div class="w-44 h-44 bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 mx-auto rounded-2xl flex flex-col items-center justify-center p-3 relative shadow-inner">
                                <div class="grid grid-cols-4 grid-rows-4 gap-1.5 w-full h-full opacity-60">
                                    @for($i=0; $i<16; $i++)
                                        <div class="bg-slate-800 dark:bg-white rounded-sm {{ ($i % 4 == 0) ? 'invisible' : '' }}"></div>
                                    @endfor
                                </div>
                                <span class="absolute text-center bg-white dark:bg-slate-800 text-[10px] font-bold px-2 py-1 border rounded shadow-md text-[#009FD0]">Código Plin QR</span>
                            </div>

                            <div class="max-w-xs mx-auto pt-4">
                                <label class="block text-left text-xs font-bold text-slate-450 uppercase mb-1.5">Número de Celular Plin</label>
                                <input type="text" placeholder="987 654 321"
                                       class="w-full px-4 py-2.5 text-center rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                            </div>
                        </div>
                        @break

                    @case('bank_transfer')
                        <!-- Simulación Transferencia -->
                        <div class="space-y-6">
                            <h4 class="text-base font-bold text-slate-800 dark:text-white mb-2 flex items-center gap-2">
                                <span>🏦</span> Cuentas Bancarias de Recaudo
                            </h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="p-4 rounded-xl border border-slate-100 dark:border-slate-700 bg-slate-55/30 dark:bg-slate-900/10 space-y-2">
                                    <p class="text-xs font-bold text-indigo-500 uppercase">Banco de Crédito (BCP)</p>
                                    <p class="text-sm font-semibold text-slate-750 dark:text-slate-350">Cuenta: <span class="font-mono">193-98765432-0-12</span></p>
                                    <p class="text-xs text-slate-450">CCI: 00219300987654320123</p>
                                </div>
                                <div class="p-4 rounded-xl border border-slate-100 dark:border-slate-700 bg-slate-55/30 dark:bg-slate-900/10 space-y-2">
                                    <p class="text-xs font-bold text-emerald-500 uppercase">Interbank</p>
                                    <p class="text-sm font-semibold text-slate-750 dark:text-slate-350">Cuenta: <span class="font-mono">200-300123456-7</span></p>
                                    <p class="text-xs text-slate-450">CCI: 00320000300123456789</p>
                                </div>
                            </div>

                            <div class="space-y-4 pt-4 border-t border-slate-100 dark:border-slate-700">
                                <p class="text-xs text-slate-400">Una vez realizada la transferencia bancaria, adjunta una captura/imagen del comprobante o voucher para verificar la transacción.</p>
                                
                                <div>
                                    <label class="block text-xs font-bold text-slate-450 uppercase mb-1.5">Adjuntar Comprobante de Transferencia (Falso)</label>
                                    <div class="border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-xl p-6 text-center cursor-pointer hover:border-luffy-red transition-all">
                                        <span class="text-2xl block mb-1">📁</span>
                                        <span class="text-xs font-bold text-slate-600 dark:text-slate-400 block">Subir Voucher de Pago</span>
                                        <span class="text-[10px] text-slate-400 block mt-0.5">Formatos permitidos: JPG, PNG, PDF (Máx. 5MB)</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @break

                    @case('pagoefectivo')
                        <!-- Simulación PagoEfectivo -->
                        <div class="space-y-6 text-center">
                            <div class="flex justify-center mb-2">
                                <span class="bg-[#FFCE00] text-slate-800 px-5 py-2.5 rounded-2xl font-extrabold text-lg flex items-center gap-2 font-['Outfit']">
                                    🎫 Código de Pago PagoEfectivo (CIP)
                                </span>
                            </div>

                            <p class="text-sm text-slate-600 dark:text-slate-350">Acércate a un agente participante (BCP, BBVA, Interbank, Tambo, Kasnet) y brinda este código de pago.</p>

                            <!-- Código CIP -->
                            <div class="max-w-xs mx-auto p-6 bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-750 rounded-2xl text-center space-y-2">
                                <p class="text-xs text-slate-450 uppercase font-bold tracking-widest">Código CIP generado</p>
                                <p class="text-3xl font-black font-mono tracking-wider text-slate-800 dark:text-white">9876543</p>
                                <p class="text-[10px] text-rose-500 font-bold uppercase tracking-wider">Expira: Mañana a las 23:59</p>
                            </div>

                            <p class="text-xs text-slate-400 pt-2">El pedido quedará registrado en espera de que realices el depósito. En esta simulación puedes procesar el éxito del pago inmediatamente.</p>
                        </div>
                        @break

                @endswitch
            </div>

            <!-- Botones de Acción de la Simulación -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center pt-6 border-t border-slate-100 dark:border-slate-700">
                <!-- Formulario de Simulación Exito -->
                <form action="{{ route('checkout.payment.simulate', $payment) }}" method="POST" class="w-full sm:w-auto">
                    @csrf
                    <input type="hidden" name="simulate_success" value="1">
                    <button type="submit" 
                            class="w-full px-6 py-4 bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-sm rounded-2xl shadow-lg shadow-emerald-600/20 hover:shadow-xl transition-all">
                        🟢 Simular Pago Exitoso
                    </button>
                </form>

                <!-- Formulario de Simulación Fallo -->
                <form action="{{ route('checkout.payment.simulate', $payment) }}" method="POST" class="w-full sm:w-auto">
                    @csrf
                    <input type="hidden" name="simulate_success" value="0">
                    <button type="submit" 
                            class="w-full px-6 py-4 bg-rose-50 border border-rose-200 hover:bg-rose-100 dark:bg-rose-950/20 dark:border-rose-900 dark:hover:bg-rose-900/35 text-rose-600 font-extrabold text-sm rounded-2xl transition-all">
                        🔴 Simular Pago Fallido
                    </button>
                </form>
            </div>

            <div class="text-center">
                <a href="{{ route('checkout.index') }}" 
                   class="text-xs font-bold text-slate-450 hover:text-slate-850 dark:hover:text-white underline transition-colors">
                    Volver y Cambiar Método de Pago
                </a>
            </div>
        </div>
    </div>
</x-layouts.public>
