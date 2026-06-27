<x-layouts.admin>
    <x-slot:title>Configuración del Sistema - Nikama Admin</x-slot:title>

    <div class="space-y-6" x-data="{ activeTab: 'financial' }">
        <!-- Header -->
        <div class="bg-gradient-to-r from-luffy-red/20 to-rose-500/10 p-8 rounded-3xl border border-luffy-red/30 shadow-sm transition-colors duration-300">
            <h2 class="text-3xl font-extrabold font-['Outfit'] text-slate-800 dark:text-white">Ajustes Generales del Sistema</h2>
            <p class="text-slate-650 dark:text-slate-300 mt-2 font-medium">Configura los límites operacionales, métodos de contacto de soporte y comisiones por defecto de la plataforma.</p>
        </div>

        <!-- Alertas -->
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
            <div class="p-4 bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-350 rounded-3xl flex items-start gap-3 shadow-sm">
                <svg class="w-5 h-5 text-rose-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <p class="font-bold text-sm">Hubo errores en el formulario</p>
                    <ul class="list-disc list-inside text-xs opacity-90 mt-1 space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Navegación lateral de Ajustes -->
            <div class="lg:col-span-1 space-y-2">
                <button @click="activeTab = 'financial'"
                        :class="activeTab === 'financial' ? 'bg-slate-900 text-white dark:bg-slate-700' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-750'"
                        class="w-full text-left px-5 py-3 rounded-2xl font-bold text-sm transition-all duration-200 border border-transparent shadow-sm flex items-center gap-3 cursor-pointer">
                    <svg class="w-5 h-5 shrink-0 text-luffy-red" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>Límites y Finanzas</span>
                </button>
                <button @click="activeTab = 'support'"
                        :class="activeTab === 'support' ? 'bg-slate-900 text-white dark:bg-slate-700' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-750'"
                        class="w-full text-left px-5 py-3 rounded-2xl font-bold text-sm transition-all duration-200 border border-transparent shadow-sm flex items-center gap-3 cursor-pointer">
                    <svg class="w-5 h-5 shrink-0 text-violet-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    <span>Contacto y Soporte</span>
                </button>
                <button @click="activeTab = 'catalog'"
                        :class="activeTab === 'catalog' ? 'bg-slate-900 text-white dark:bg-slate-700' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-750'"
                        class="w-full text-left px-5 py-3 rounded-2xl font-bold text-sm transition-all duration-200 border border-transparent shadow-sm flex items-center gap-3 cursor-pointer">
                    <svg class="w-5 h-5 shrink-0 text-sky-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                    <span>Catálogo y Delivery</span>
                </button>
            </div>

            <!-- Formulario Principal -->
            <form action="{{ route('admin.system.settings.update') }}" method="POST" class="lg:col-span-3 bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/80 shadow-sm p-6 space-y-6">
                @csrf

                <!-- Tab: Límites y Finanzas -->
                <div x-show="activeTab === 'financial'" class="space-y-6" x-transition>
                    <div class="border-b border-slate-100 dark:border-slate-700 pb-3">
                        <h3 class="text-lg font-black font-['Outfit'] text-slate-800 dark:text-white">Parámetros Financieros y Límites de Retiro</h3>
                        <p class="text-xs text-slate-400 mt-1">Configura los montos mínimos para retiro de fondos y la tasa de comisión global.</p>
                    </div>

                    <!-- Mínimo de Retiro Repartidor -->
                    <div class="space-y-2">
                        <label class="text-xs font-black text-slate-450 dark:text-slate-400 uppercase tracking-wider block">Monto Mínimo de Retiro - Repartidores (S/)</label>
                        <input type="number" step="0.01" name="settings[min_driver_payout]" required value="{{ $settings['min_driver_payout']->value }}"
                               class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all font-semibold">
                        <p class="text-[10px] text-slate-400 font-medium">{{ $settings['min_driver_payout']->description }}</p>
                    </div>

                    <!-- Mínimo de Retiro Negocio -->
                    <div class="space-y-2">
                        <label class="text-xs font-black text-slate-455 dark:text-slate-400 uppercase tracking-wider block">Monto Mínimo de Retiro - Comercios (S/)</label>
                        <input type="number" step="0.01" name="settings[min_business_payout]" required value="{{ $settings['min_business_payout']->value }}"
                               class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all font-semibold">
                        <p class="text-[10px] text-slate-400 font-medium">{{ $settings['min_business_payout']->description }}</p>
                    </div>

                    <!-- Comisión General Plataforma -->
                    <div class="space-y-2">
                        <label class="text-xs font-black text-slate-455 dark:text-slate-400 uppercase tracking-wider block">Comisión General de la Plataforma (%)</label>
                        <input type="number" step="0.01" name="settings[general_commission_percentage]" required min="0" max="100" value="{{ $settings['general_commission_percentage']->value }}"
                               class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all font-semibold">
                        <p class="text-[10px] text-slate-400 font-medium">{{ $settings['general_commission_percentage']->description }}</p>
                    </div>
                </div>

                <!-- Tab: Contacto y Soporte -->
                <div x-show="activeTab === 'support'" class="space-y-6" style="display: none;" x-transition>
                    <div class="border-b border-slate-100 dark:border-slate-700 pb-3">
                        <h3 class="text-lg font-black font-['Outfit'] text-slate-800 dark:text-white">Contacto de Soporte Oficial</h3>
                        <p class="text-xs text-slate-400 mt-1">Configura las vías de soporte y contacto que visualizarán los usuarios y socios.</p>
                    </div>

                    <!-- Correo Soporte -->
                    <div class="space-y-2">
                        <label class="text-xs font-black text-slate-455 dark:text-slate-400 uppercase tracking-wider block">Email de Soporte</label>
                        <input type="email" name="settings[support_email]" required value="{{ $settings['support_email']->value }}"
                               class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all font-semibold font-mono">
                        <p class="text-[10px] text-slate-400 font-medium">{{ $settings['support_email']->description }}</p>
                    </div>

                    <!-- Teléfono Soporte -->
                    <div class="space-y-2">
                        <label class="text-xs font-black text-slate-455 dark:text-slate-400 uppercase tracking-wider block">Teléfono / Celular de Soporte</label>
                        <input type="text" name="settings[support_phone]" required value="{{ $settings['support_phone']->value }}"
                               class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all font-semibold">
                        <p class="text-[10px] text-slate-400 font-medium">{{ $settings['support_phone']->description }}</p>
                    </div>
                </div>

                <!-- Tab: Catálogo y Delivery -->
                <div x-show="activeTab === 'catalog'" class="space-y-6" style="display: none;" x-transition>
                    <div class="border-b border-slate-100 dark:border-slate-700 pb-3">
                        <h3 class="text-lg font-black font-['Outfit'] text-slate-800 dark:text-white">Parámetros del Catálogo y Delivery</h3>
                        <p class="text-xs text-slate-400 mt-1">Configura valores por defecto para pedidos y tarifas del servicio de delivery.</p>
                    </div>

                    <!-- Tarifa Base Delivery -->
                    <div class="space-y-2">
                        <label class="text-xs font-black text-slate-455 dark:text-slate-400 uppercase tracking-wider block">Tarifa Base de Delivery (S/)</label>
                        <input type="number" step="0.01" name="settings[delivery_base_fee]" required value="{{ $settings['delivery_base_fee']->value }}"
                               class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all font-semibold">
                        <p class="text-[10px] text-slate-400 font-medium">{{ $settings['delivery_base_fee']->description }}</p>
                    </div>
                </div>

                <!-- Botón de Guardado -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-50 dark:border-slate-700/60">
                    <button type="submit" class="px-6 py-3 bg-gradient-to-r from-luffy-red to-rose-500 hover:from-luffy-red/90 hover:to-rose-500/90 text-white font-extrabold text-xs uppercase tracking-wider rounded-2xl transition shadow-lg shadow-luffy-red/15 cursor-pointer">
                        Guardar Ajustes
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>
