<x-layouts.admin>
    <x-slot:title>Gestión de Solicitudes - Nikama Admin</x-slot:title>

    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-gradient-to-r from-slate-900 to-slate-800 dark:from-slate-950 dark:to-slate-900 p-6 rounded-3xl border border-slate-700 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-extrabold font-['Outfit'] text-white">Revisión y Aprobación de Solicitudes</h2>
                <p class="text-slate-400 text-sm mt-1">Evalúa y gestiona las solicitudes pendientes de nuevos negocios y repartidores en Nikama.</p>
            </div>
        </div>

        <!-- Tarjetas de Estadísticas -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white dark:bg-slate-800 p-6 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-slate-400 text-xs font-bold uppercase tracking-wider block">Sellers Pendientes</span>
                    <span class="text-3xl font-extrabold font-['Outfit'] text-slate-800 dark:text-white block mt-1">{{ $pendingSellersCount }}</span>
                </div>
                <div class="p-3 bg-amber-50 dark:bg-amber-950/30 text-amber-500 rounded-2xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-800 p-6 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-slate-400 text-xs font-bold uppercase tracking-wider block">Drivers Pendientes</span>
                    <span class="text-3xl font-extrabold font-['Outfit'] text-slate-800 dark:text-white block mt-1">{{ $pendingDriversCount }}</span>
                </div>
                <div class="p-3 bg-indigo-50 dark:bg-indigo-950/30 text-indigo-500 rounded-2xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-800 p-6 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-slate-400 text-xs font-bold uppercase tracking-wider block">Total Aprobados</span>
                    <span class="text-3xl font-extrabold font-['Outfit'] text-slate-800 dark:text-white block mt-1">{{ $totalApproved }}</span>
                </div>
                <div class="p-3 bg-emerald-50 dark:bg-emerald-950/30 text-emerald-500 rounded-2xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-800 p-6 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-slate-400 text-xs font-bold uppercase tracking-wider block">Total Rechazados</span>
                    <span class="text-3xl font-extrabold font-['Outfit'] text-slate-800 dark:text-white block mt-1">{{ $totalRejected }}</span>
                </div>
                <div class="p-3 bg-rose-50 dark:bg-rose-950/30 text-rose-500 rounded-2xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
        </div>

        <!-- Filtros y Pestañas -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm overflow-hidden">
            <!-- Pestañas de Opción B -->
            <div class="flex border-b border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-850">
                <a href="{{ route('admin.applications.index', ['tab' => 'sellers', 'status' => 'pending', 'search' => $search]) }}" 
                   class="flex items-center gap-2 px-6 py-4 border-b-2 font-bold text-sm transition-all {{ $tab === 'sellers' ? 'border-luffy-red text-luffy-red bg-white dark:bg-slate-800' : 'border-transparent text-slate-500 hover:text-slate-800 dark:hover:text-white' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    <span>Solicitudes de Sellers</span>
                    @if($pendingSellersCount > 0)
                        <span class="px-2 py-0.5 rounded-full text-xs bg-amber-500 text-white font-extrabold">{{ $pendingSellersCount }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.applications.index', ['tab' => 'drivers', 'status' => 'pending', 'search' => $search]) }}" 
                   class="flex items-center gap-2 px-6 py-4 border-b-2 font-bold text-sm transition-all {{ $tab === 'drivers' ? 'border-luffy-red text-luffy-red bg-white dark:bg-slate-800' : 'border-transparent text-slate-500 hover:text-slate-800 dark:hover:text-white' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                    <span>Solicitudes de Drivers</span>
                    @if($pendingDriversCount > 0)
                        <span class="px-2 py-0.5 rounded-full text-xs bg-indigo-500 text-white font-extrabold">{{ $pendingDriversCount }}</span>
                    @endif
                </a>
            </div>

            <!-- Filtros por Estado y Buscador -->
            <div class="p-6 flex flex-col md:flex-row gap-4 items-center justify-between border-b border-slate-100 dark:border-slate-700">
                @php
                    $approvedVal = ($tab === 'drivers' ? 'active' : 'approved');
                @endphp
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('admin.applications.index', ['tab' => $tab, 'status' => 'pending', 'search' => $search]) }}" 
                       class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $status === 'pending' ? 'bg-amber-500 text-white shadow-md shadow-amber-500/20' : 'bg-slate-100 hover:bg-slate-200 dark:bg-slate-750 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300' }}">
                        Pendientes
                    </a>
                    <a href="{{ route('admin.applications.index', ['tab' => $tab, 'status' => $approvedVal, 'search' => $search]) }}" 
                       class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $status === $approvedVal ? 'bg-emerald-500 text-white shadow-md shadow-emerald-500/20' : 'bg-slate-100 hover:bg-slate-200 dark:bg-slate-750 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300' }}">
                        Aprobados / Activos
                    </a>
                    <a href="{{ route('admin.applications.index', ['tab' => $tab, 'status' => 'rejected', 'search' => $search]) }}" 
                       class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $status === 'rejected' ? 'bg-rose-500 text-white shadow-md shadow-rose-500/20' : 'bg-slate-100 hover:bg-slate-200 dark:bg-slate-750 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300' }}">
                        Rechazados
                    </a>
                    <a href="{{ route('admin.applications.index', ['tab' => $tab, 'status' => 'all', 'search' => $search]) }}" 
                       class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $status === 'all' ? 'bg-slate-800 dark:bg-slate-900 text-white shadow-md' : 'bg-slate-100 hover:bg-slate-200 dark:bg-slate-750 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300' }}">
                        Todos
                    </a>
                </div>

                <!-- Buscador -->
                <form action="{{ route('admin.applications.index') }}" method="GET" class="w-full md:w-80 flex gap-2">
                    <input type="hidden" name="tab" value="{{ $tab }}">
                    <input type="hidden" name="status" value="{{ $status }}">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Buscar por nombre, email..." 
                           class="w-full px-4 py-2 rounded-xl border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                    <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs rounded-xl transition-all">
                        Buscar
                    </button>
                </form>
            </div>

            <!-- Tabla de Datos -->
            <div class="overflow-x-auto">
                @if($applications->isEmpty())
                    <div class="p-12 text-center text-slate-500 dark:text-slate-400">
                        <svg class="w-12 h-12 mx-auto mb-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <p class="text-base font-semibold">No se encontraron solicitudes.</p>
                        <p class="text-xs mt-1">Prueba cambiando los filtros o la búsqueda.</p>
                    </div>
                @else
                    @if($tab === 'sellers')
                        <!-- Tabla Sellers -->
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50 dark:bg-slate-850 border-b border-slate-100 dark:border-slate-700 text-slate-400 text-xs font-bold uppercase tracking-wider">
                                    <th class="px-6 py-4">Negocio</th>
                                    <th class="px-6 py-4">RUC / Razón Social</th>
                                    <th class="px-6 py-4">Dueño / Representante</th>
                                    <th class="px-6 py-4">F. Registro</th>
                                    <th class="px-6 py-4 text-center">Estado</th>
                                    <th class="px-6 py-4 text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700 text-sm">
                                @foreach($applications as $app)
                                    @php
                                        $owner = $app->users->first();
                                    @endphp
                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                                        <td class="px-6 py-4">
                                            <span class="font-bold text-slate-800 dark:text-white block">{{ $app->business_name }}</span>
                                            <span class="text-xs text-slate-400">{{ $app->contact_email }}</span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="block font-medium text-slate-700 dark:text-slate-300">{{ $app->ruc }}</span>
                                            <span class="text-xs text-slate-400 block max-w-xs truncate">{{ $app->legal_name }}</span>
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($owner)
                                                <span class="font-semibold text-slate-800 dark:text-white block">{{ $owner->first_name }} {{ $owner->last_name }}</span>
                                                <span class="text-xs text-slate-400">{{ $owner->phone }}</span>
                                            @else
                                                <span class="text-slate-400">Sin propietario</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-slate-500">
                                            {{ $app->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @if($app->status === 'pending')
                                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 dark:bg-amber-950/30 text-amber-600">Pendiente</span>
                                            @elseif($app->status === 'approved')
                                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 dark:bg-emerald-950/30 text-emerald-600">Aprobado</span>
                                            @elseif($app->status === 'rejected')
                                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-rose-50 dark:bg-rose-950/30 text-rose-600">Rechazado</span>
                                            @else
                                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-slate-50 dark:bg-slate-800 text-slate-600">{{ $app->status }}</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <a href="{{ route('admin.applications.seller.show', $app->id) }}" class="inline-block px-3.5 py-1.5 bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs rounded-lg transition-colors">
                                                Ver Detalle
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <!-- Tabla Drivers -->
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50 dark:bg-slate-850 border-b border-slate-100 dark:border-slate-700 text-slate-400 text-xs font-bold uppercase tracking-wider">
                                    <th class="px-6 py-4">Repartidor</th>
                                    <th class="px-6 py-4">Vehículo</th>
                                    <th class="px-6 py-4">Placa / Licencia</th>
                                    <th class="px-6 py-4">F. Registro</th>
                                    <th class="px-6 py-4 text-center">Estado</th>
                                    <th class="px-6 py-4 text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700 text-sm">
                                @foreach($applications as $app)
                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                                        <td class="px-6 py-4">
                                            @if($app->user)
                                                <span class="font-bold text-slate-800 dark:text-white block">{{ $app->user->first_name }} {{ $app->user->last_name }}</span>
                                                <span class="text-xs text-slate-400 block">{{ $app->user->email }}</span>
                                                <span class="text-xs text-slate-400">{{ $app->user->phone }}</span>
                                            @else
                                                <span class="text-slate-400">Sin usuario</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="font-semibold text-slate-800 dark:text-white uppercase block text-xs">
                                                @if($app->vehicle_type === 'bicycle') Bicicleta @elseif($app->vehicle_type === 'motorcycle') Motocicleta @else Carro @endif
                                            </span>
                                            <span class="text-xs text-slate-400 block mt-0.5">
                                                {{ $app->vehicle_brand }} {{ $app->vehicle_model }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="block font-medium text-slate-700 dark:text-slate-300 uppercase">{{ $app->license_plate ?? 'Sin Placa' }}</span>
                                            <span class="text-xs text-slate-400">{{ $app->license_number ?? 'Sin Licencia' }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-slate-500">
                                            {{ $app->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @if($app->status === 'pending')
                                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 dark:bg-amber-950/30 text-amber-600">Pendiente</span>
                                            @elseif($app->status === 'active')
                                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 dark:bg-emerald-950/30 text-emerald-600">Activo</span>
                                            @elseif($app->status === 'rejected')
                                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-rose-50 dark:bg-rose-950/30 text-rose-600">Rechazado</span>
                                            @else
                                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-slate-50 dark:bg-slate-800 text-slate-600">{{ $app->status }}</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <a href="{{ route('admin.applications.driver.show', $app->id) }}" class="inline-block px-3.5 py-1.5 bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs rounded-lg transition-colors">
                                                Ver Detalle
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                @endif
            </div>

            <!-- Paginación -->
            @if($applications->hasPages())
                <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-850 flex items-center justify-between">
                    <div class="text-xs text-slate-500">
                        Mostrando {{ $applications->firstItem() }} al {{ $applications->lastItem() }} de {{ $applications->total() }} registros.
                    </div>
                    <div class="pagination-native">
                        {{ $applications->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-layouts.admin>
