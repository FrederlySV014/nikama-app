<x-layouts.admin>
    <x-slot:title>Cupones y Descuentos - Nikama Admin</x-slot:title>

    <div class="space-y-6" x-data="{ 
        showCreateModal: false, 
        discountType: 'percentage',
        showEditModal: false,
        editDiscountId: '',
        editCode: '',
        editName: '',
        editDescription: '',
        editDiscountType: '',
        editDiscountValue: '',
        editMinimumOrderAmount: '',
        editMaximumDiscountAmount: '',
        editUsageLimit: '',
        editUsageLimitPerUser: '',
        editStartsAt: '',
        editExpiresAt: '',
        updateUrl: '',
        deleteUrl: ''
    }">
        <!-- Header -->
        <div class="bg-gradient-to-r from-luffy-red/20 to-rose-500/10 p-8 rounded-3xl border border-luffy-red/30 shadow-sm transition-colors duration-300">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-3xl font-extrabold font-['Outfit'] text-slate-800 dark:text-white">Cupones y Descuentos Globales</h2>
                    <p class="text-slate-650 dark:text-slate-300 mt-2 font-medium">Crea y administra cupones de descuento y promociones globales para incentivar compras en la plataforma.</p>
                </div>
                <button @click="showCreateModal = true" class="px-5 py-3 bg-gradient-to-r from-luffy-red to-rose-500 hover:from-luffy-red/90 hover:to-rose-500/90 text-white font-extrabold text-sm uppercase tracking-wider rounded-2xl transition shadow-lg shadow-luffy-red/20 cursor-pointer self-start md:self-auto flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                    Nuevo Cupón
                </button>
            </div>
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

        <!-- Tabla de Cupones -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/80 shadow-sm overflow-hidden transition-colors duration-300">
            <div class="overflow-x-auto">
                @if($discountsList->isEmpty())
                    <div class="p-16 text-center text-slate-500 dark:text-slate-400">
                        <svg class="w-16 h-16 mx-auto mb-4 text-slate-300 dark:text-slate-600 animate-pulse" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <p class="text-base font-black font-['Outfit'] text-slate-700 dark:text-slate-300">No hay cupones registrados</p>
                        <p class="text-xs text-slate-400 mt-1 font-medium">Comienza creando tu primer cupón global con el botón superior.</p>
                    </div>
                @else
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50 dark:bg-slate-900/40 border-b border-slate-100 dark:border-slate-700/60 text-slate-400 dark:text-slate-455 text-[10px] font-black uppercase tracking-wider">
                                <th class="px-6 py-4.5">Código / Nombre</th>
                                <th class="px-6 py-4.5">Descuento</th>
                                <th class="px-6 py-4.5">Mínimo de Compra</th>
                                <th class="px-6 py-4.5">Usos / Límite</th>
                                <th class="px-6 py-4.5">Vigencia</th>
                                <th class="px-6 py-4.5 text-center">Estado</th>
                                <th class="px-6 py-4.5 text-right">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700 text-sm">
                            @foreach($discountsList as $discount)
                                <tr class="hover:bg-slate-50/40 dark:hover:bg-slate-700/20 transition-colors">
                                    <td class="px-6 py-4.5 font-bold text-slate-800 dark:text-white">
                                        <span class="px-2.5 py-1 bg-rose-50 dark:bg-rose-955/40 text-luffy-red dark:text-rose-455 rounded-xl border border-rose-100 dark:border-rose-900/40 font-mono font-black tracking-wider text-xs block w-fit mb-1">
                                            {{ $discount->code }}
                                        </span>
                                        <span class="font-bold text-slate-800 dark:text-white text-sm block">{{ $discount->name }}</span>
                                        @if($discount->description)
                                            <span class="text-xs text-slate-400 font-medium block mt-0.5 line-clamp-1">{{ $discount->description }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4.5 font-black text-emerald-600 dark:text-emerald-450">
                                        @if($discount->discount_type === 'percentage')
                                            {{ number_format($discount->discount_value, 0) }}%
                                        @elseif($discount->discount_type === 'fixed')
                                            S/ {{ number_format($discount->discount_value, 2) }}
                                        @else
                                            Envío Gratis
                                        @endif
                                    </td>
                                    <td class="px-6 py-4.5 font-semibold text-slate-700 dark:text-slate-300">
                                        S/ {{ number_format($discount->minimum_order_amount, 2) }}
                                    </td>
                                    <td class="px-6 py-4.5 text-slate-650 dark:text-slate-300 font-semibold">
                                        <div class="flex items-center gap-2">
                                            <span>{{ $discount->used_count }} / {{ $discount->usage_limit }}</span>
                                            <span class="text-[10px] text-slate-400 font-medium">(Límite/User: {{ $discount->usage_limit_per_user }})</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4.5 font-medium text-[11px] text-slate-500 dark:text-slate-400">
                                        <div class="flex flex-col gap-0.5">
                                            <span>Inicio: {{ $discount->starts_at->format('d/m/Y H:i') }}</span>
                                            <span>Fin: {{ $discount->expires_at->format('d/m/Y H:i') }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4.5 text-center">
                                        @php
                                            $isValid = $discount->is_active && $discount->starts_at->isPast() && $discount->expires_at->isFuture() && ($discount->used_count < $discount->usage_limit);
                                            $badgeClasses = $isValid
                                                ? 'bg-emerald-50 dark:bg-emerald-950/30 text-emerald-600 dark:text-emerald-400 border border-emerald-200/40 dark:border-emerald-900/30'
                                                : 'bg-rose-50 dark:bg-rose-955/30 text-rose-600 dark:text-rose-455 border border-rose-200/40 dark:border-rose-900/30';
                                            $badgeLabel = $isValid ? 'Vigente' : 'Inactivo';
                                        @endphp
                                        <span class="px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider {{ $badgeClasses }}">
                                            {{ $badgeLabel }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4.5 text-right flex items-center justify-end gap-2">
                                        <form action="{{ route('admin.marketing.discounts.toggle', $discount) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 border border-slate-200 dark:border-slate-700 hover:bg-slate-55/40 dark:hover:bg-slate-700/60 text-slate-700 dark:text-slate-300 font-bold text-[10px] uppercase tracking-wider rounded-xl transition cursor-pointer">
                                                {{ $discount->is_active ? 'Desactivar' : 'Activar' }}
                                            </button>
                                        </form>
                                        <button @click="
                                            editDiscountId = '{{ $discount->id }}';
                                            editCode = '{{ $discount->code }}';
                                            editName = '{{ addslashes($discount->name) }}';
                                            editDescription = '{{ addslashes($discount->description ?? '') }}';
                                            editDiscountType = '{{ $discount->discount_type }}';
                                            editDiscountValue = '{{ $discount->discount_value }}';
                                            editMinimumOrderAmount = '{{ $discount->minimum_order_amount }}';
                                            editMaximumDiscountAmount = '{{ $discount->maximum_discount_amount ?? '' }}';
                                            editUsageLimit = '{{ $discount->usage_limit }}';
                                            editUsageLimitPerUser = '{{ $discount->usage_limit_per_user }}';
                                            editStartsAt = '{{ $discount->starts_at->format('Y-m-d\TH:i') }}';
                                            editExpiresAt = '{{ $discount->expires_at->format('Y-m-d\TH:i') }}';
                                            updateUrl = '{{ route('admin.marketing.discounts.update', $discount) }}';
                                            deleteUrl = '{{ route('admin.marketing.discounts.destroy', $discount) }}';
                                            showEditModal = true;
                                        " class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-650 text-slate-750 dark:text-slate-300 font-bold text-[10px] uppercase tracking-wider rounded-xl transition cursor-pointer">
                                            Editar
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

            <!-- Paginación -->
            @if($discountsList->hasPages())
                <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-700 bg-slate-50/60 dark:bg-slate-900/20 flex items-center justify-between">
                    <div class="text-xs text-slate-450 dark:text-slate-400 font-medium">
                        Mostrando {{ $discountsList->firstItem() }} al {{ $discountsList->lastItem() }} de {{ $discountsList->total() }} registros.
                    </div>
                    <div>
                        {{ $discountsList->links() }}
                    </div>
                </div>
            @endif
        </div>

        <!-- Modal de Creación de Cupón -->
        <div x-show="showCreateModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-transition>
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="showCreateModal = false">
                    <div class="absolute inset-0 bg-slate-950/70 backdrop-blur-sm"></div>
                </div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-3xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full border border-slate-200 dark:border-slate-700">
                    <form action="{{ route('admin.marketing.discounts.store') }}" method="POST" class="p-6 space-y-4">
                        @csrf

                        <div>
                            <h3 class="text-xl font-black font-['Outfit'] text-slate-855 dark:text-white">Crear Cupón de Descuento</h3>
                            <p class="text-xs text-slate-455 dark:text-slate-400 mt-1">Ingresa las especificaciones del cupón y sus reglas operativas.</p>
                        </div>

                        <!-- Código y Nombre -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Código del Cupón</label>
                                <input type="text" name="code" required placeholder="Ej. BIENVENIDA50"
                                       class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-955 text-sm font-mono font-bold text-slate-800 dark:text-white uppercase focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Nombre Descriptivo</label>
                                <input type="text" name="name" required placeholder="Ej. Descuento de Bienvenida"
                                       class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-955 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                            </div>
                        </div>

                        <!-- Descripción -->
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Descripción (Opcional)</label>
                            <input type="text" name="description" placeholder="Ej. S/ 10.00 de descuento aplicable en tu primer pedido con valor mínimo de S/ 50.00."
                                   class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-955 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                        </div>

                        <!-- Tipo de Descuento y Valor -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Tipo de Descuento</label>
                                <select name="discount_type" x-model="discountType" required
                                        class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-955 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                                    <option value="percentage">Porcentual (%)</option>
                                    <option value="fixed">Monto Fijo (S/)</option>
                                    <option value="free_delivery">Envío Gratis</option>
                                </select>
                            </div>

                            <div class="space-y-1">
                                <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Valor de Descuento</label>
                                <input type="number" step="0.01" name="discount_value" required min="0" value="0" :disabled="discountType === 'free_delivery'"
                                       class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-955 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all disabled:opacity-50">
                            </div>
                        </div>

                        <!-- Pedido Mínimo y Tope Descuento -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Monto de Pedido Mínimo</label>
                                <input type="number" step="0.01" name="minimum_order_amount" required min="0" value="0"
                                       class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-955 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Límite Máx Descuento (Solo para %)</label>
                                <input type="number" step="0.01" name="maximum_discount_amount" placeholder="Dejar en blanco para ilimitado" :disabled="discountType !== 'percentage'"
                                       class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-955 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all disabled:opacity-50">
                            </div>
                        </div>

                        <!-- Límites de Uso -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Límite de Usos Globales</label>
                                <input type="number" name="usage_limit" required min="1" value="100"
                                       class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-955 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Límite de Usos por Cliente</label>
                                <input type="number" name="usage_limit_per_user" required min="1" value="1"
                                       class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-955 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                            </div>
                        </div>

                        <!-- Vigencia -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Fecha Inicio</label>
                                <input type="datetime-local" name="starts_at" required
                                       class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-955 text-sm text-slate-850 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Fecha Expiración</label>
                                <input type="datetime-local" name="expires_at" required
                                       class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-955 text-sm text-slate-855 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                            </div>
                        </div>

                        <!-- Botones Acción -->
                        <div class="flex items-center justify-end gap-3 pt-2">
                            <button type="button" @click="showCreateModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-650 text-slate-700 dark:text-slate-300 font-bold text-xs uppercase tracking-wider rounded-xl transition cursor-pointer">
                                Cancelar
                            </button>
                            <button type="submit" class="px-5 py-2 bg-gradient-to-r from-luffy-red to-rose-500 hover:from-luffy-red/90 hover:to-rose-500/90 text-white font-extrabold text-xs uppercase tracking-wider rounded-xl transition shadow-md shadow-luffy-red/10 cursor-pointer">
                                Registrar Cupón
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal de Edición de Cupón -->
        <div x-show="showEditModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-transition>
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="showEditModal = false">
                    <div class="absolute inset-0 bg-slate-955/70 backdrop-blur-sm"></div>
                </div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-3xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full border border-slate-200 dark:border-slate-700">
                    <div class="p-6">
                        <div class="flex items-center justify-between pb-3">
                            <h3 class="text-xl font-black font-['Outfit'] text-slate-855 dark:text-white">Modificar Cupón</h3>
                            <form :action="deleteUrl" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este cupón de descuento por completo? Esta acción es irreversible.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1.5 bg-rose-500 hover:bg-rose-600 text-white text-[10px] font-bold uppercase tracking-wider rounded-xl transition cursor-pointer">
                                    Eliminar
                                </button>
                            </form>
                        </div>
                        <p class="text-xs text-slate-455 dark:text-slate-400 mt-1 mb-4">Actualiza las condiciones del cupón o elimínalo del sistema.</p>

                        <form :action="updateUrl" method="POST" class="space-y-4">
                            @csrf
                            @method('PUT')

                            <!-- Código y Nombre -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-1">
                                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Código del Cupón</label>
                                    <input type="text" name="code" required x-model="editCode"
                                           class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-955 text-sm font-mono font-bold text-slate-800 dark:text-white uppercase focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Nombre Descriptivo</label>
                                    <input type="text" name="name" required x-model="editName"
                                           class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-955 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                                </div>
                            </div>

                            <!-- Descripción -->
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Descripción (Opcional)</label>
                                <input type="text" name="description" x-model="editDescription" placeholder="Ej. S/ 10.00 de descuento aplicable en tu primer pedido..."
                                       class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-955 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                            </div>

                            <!-- Tipo de Descuento y Valor -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-1">
                                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Tipo de Descuento</label>
                                    <select name="discount_type" x-model="editDiscountType" required
                                            class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-955 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                                        <option value="percentage">Porcentual (%)</option>
                                        <option value="fixed">Monto Fijo (S/)</option>
                                        <option value="free_delivery">Envío Gratis</option>
                                    </select>
                                </div>

                                <div class="space-y-1">
                                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Valor de Descuento</label>
                                    <input type="number" step="0.01" name="discount_value" required min="0" x-model="editDiscountValue" :disabled="editDiscountType === 'free_delivery'"
                                           class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-955 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all disabled:opacity-50">
                                </div>
                            </div>

                            <!-- Pedido Mínimo y Tope Descuento -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-1">
                                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Monto de Pedido Mínimo</label>
                                    <input type="number" step="0.01" name="minimum_order_amount" required min="0" x-model="editMinimumOrderAmount"
                                           class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-955 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Límite Máx Descuento (Solo para %)</label>
                                    <input type="number" step="0.01" name="maximum_discount_amount" x-model="editMaximumDiscountAmount" placeholder="Ilimitado" :disabled="editDiscountType !== 'percentage'"
                                           class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-955 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all disabled:opacity-50">
                                </div>
                            </div>

                            <!-- Límites de Uso -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-1">
                                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Límite de Usos Globales</label>
                                    <input type="number" name="usage_limit" required min="1" x-model="editUsageLimit"
                                           class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-955 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Límite de Usos por Cliente</label>
                                    <input type="number" name="usage_limit_per_user" required min="1" x-model="editUsageLimitPerUser"
                                           class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-955 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                                </div>
                            </div>

                            <!-- Vigencia -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-1">
                                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Fecha Inicio</label>
                                    <input type="datetime-local" name="starts_at" required x-model="editStartsAt"
                                           class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-955 text-sm text-slate-850 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Fecha Expiración</label>
                                    <input type="datetime-local" name="expires_at" required x-model="editExpiresAt"
                                           class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-955 text-sm text-slate-855 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                                </div>
                            </div>

                            <!-- Botones Acción -->
                            <div class="flex items-center justify-end gap-3 pt-2">
                                <button type="button" @click="showEditModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-650 text-slate-700 dark:text-slate-300 font-bold text-xs uppercase tracking-wider rounded-xl transition cursor-pointer">
                                    Cancelar
                                </button>
                                <button type="submit" class="px-5 py-2 bg-emerald-500 hover:bg-emerald-600 text-white font-extrabold text-xs uppercase tracking-wider rounded-xl transition shadow-md cursor-pointer">
                                    Guardar Cambios
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
