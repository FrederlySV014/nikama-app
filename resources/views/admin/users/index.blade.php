<x-layouts.admin>
    <x-slot:title>Gestión de Usuarios - Nikama Admin</x-slot:title>

    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-gradient-to-r from-luffy-red/20 to-rose-500/10 p-8 rounded-3xl border border-luffy-red/30 shadow-sm transition-colors duration-300">
            <h2 class="text-3xl font-extrabold font-['Outfit'] text-slate-800 dark:text-white">Gestión de Usuarios</h2>
            <p class="text-slate-650 dark:text-slate-300 mt-2 font-medium">Administra las cuentas de clientes, vendedores, repartidores y administradores del sistema.</p>
        </div>

        <!-- Alertas -->
        @if (session('success'))
            <div class="p-4 bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 rounded-3xl flex items-start gap-3 shadow-sm">
                <svg class="w-5 h-5 text-emerald-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <p class="font-extrabold text-sm">Operación exitosa</p>
                    <p class="text-xs opacity-90 mt-0.5">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if (session('warning'))
            <div class="p-4 bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800 text-amber-800 dark:text-amber-300 rounded-3xl flex items-start gap-3 shadow-sm">
                <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <div>
                    <p class="font-extrabold text-sm">Alerta del sistema</p>
                    <p class="text-xs opacity-90 mt-0.5">{{ session('warning') }}</p>
                </div>
            </div>
        @endif

        <!-- Tarjetas de Estadísticas -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
            <div class="bg-white dark:bg-slate-800 p-6 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm flex items-center justify-between transition-all duration-300 hover:shadow-md hover:-translate-y-0.5">
                <div>
                    <span class="text-slate-400 dark:text-slate-400 text-xs font-bold uppercase tracking-wider block">Clientes</span>
                    <span class="text-3xl font-extrabold font-['Outfit'] text-slate-800 dark:text-white block mt-1">{{ $customersCount }}</span>
                </div>
                <div class="p-3 bg-sky-50 dark:bg-sky-950/40 text-sky-500 dark:text-sky-400 rounded-2xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-800 p-6 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm flex items-center justify-between transition-all duration-300 hover:shadow-md hover:-translate-y-0.5">
                <div>
                    <span class="text-slate-400 dark:text-slate-400 text-xs font-bold uppercase tracking-wider block">Vendedores</span>
                    <span class="text-3xl font-extrabold font-['Outfit'] text-slate-800 dark:text-white block mt-1">{{ $sellersCount }}</span>
                </div>
                <div class="p-3 bg-amber-50 dark:bg-amber-950/40 text-amber-500 dark:text-amber-400 rounded-2xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-800 p-6 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm flex items-center justify-between transition-all duration-300 hover:shadow-md hover:-translate-y-0.5">
                <div>
                    <span class="text-slate-400 dark:text-slate-400 text-xs font-bold uppercase tracking-wider block">Repartidores</span>
                    <span class="text-3xl font-extrabold font-['Outfit'] text-slate-800 dark:text-white block mt-1">{{ $driversCount }}</span>
                </div>
                <div class="p-3 bg-rose-50 dark:bg-rose-950/40 text-rose-500 dark:text-rose-450 rounded-2xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-800 p-6 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm flex items-center justify-between transition-all duration-300 hover:shadow-md hover:-translate-y-0.5">
                <div>
                    <span class="text-slate-400 dark:text-slate-400 text-xs font-bold uppercase tracking-wider block">Administradores</span>
                    <span class="text-3xl font-extrabold font-['Outfit'] text-slate-800 dark:text-white block mt-1">{{ $adminsCount }}</span>
                </div>
                <div class="p-3 bg-violet-50 dark:bg-violet-950/40 text-violet-500 dark:text-violet-400 rounded-2xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Filtros y Listado -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/80 shadow-sm overflow-hidden transition-colors duration-300">
            <!-- Pestañas de Roles -->
            <div class="flex border-b border-slate-100 dark:border-slate-700/60 bg-slate-50 dark:bg-slate-900/20">
                <a href="{{ route('admin.users.index', ['tab' => 'all', 'status' => $status, 'search' => $search]) }}" 
                   class="flex items-center gap-2 px-6 py-4 border-b-2 font-bold text-sm transition-all duration-200 {{ $tab === 'all' ? 'border-luffy-red text-luffy-red dark:text-rose-450 bg-white dark:bg-slate-800' : 'border-transparent text-slate-500 hover:text-slate-800 dark:hover:text-white hover:bg-slate-100/50 dark:hover:bg-slate-700/20' }}">
                    <span>Todos</span>
                </a>
                <a href="{{ route('admin.users.index', ['tab' => 'customer', 'status' => $status, 'search' => $search]) }}" 
                   class="flex items-center gap-2 px-6 py-4 border-b-2 font-bold text-sm transition-all duration-200 {{ $tab === 'customer' ? 'border-luffy-red text-luffy-red dark:text-rose-450 bg-white dark:bg-slate-800' : 'border-transparent text-slate-500 hover:text-slate-800 dark:hover:text-white hover:bg-slate-100/50 dark:hover:bg-slate-700/20' }}">
                    <span>Clientes</span>
                </a>
                <a href="{{ route('admin.users.index', ['tab' => 'seller', 'status' => $status, 'search' => $search]) }}" 
                   class="flex items-center gap-2 px-6 py-4 border-b-2 font-bold text-sm transition-all duration-200 {{ $tab === 'seller' ? 'border-luffy-red text-luffy-red dark:text-rose-450 bg-white dark:bg-slate-800' : 'border-transparent text-slate-500 hover:text-slate-800 dark:hover:text-white hover:bg-slate-100/50 dark:hover:bg-slate-700/20' }}">
                    <span>Vendedores</span>
                </a>
                <a href="{{ route('admin.users.index', ['tab' => 'driver', 'status' => $status, 'search' => $search]) }}" 
                   class="flex items-center gap-2 px-6 py-4 border-b-2 font-bold text-sm transition-all duration-200 {{ $tab === 'driver' ? 'border-luffy-red text-luffy-red dark:text-rose-450 bg-white dark:bg-slate-800' : 'border-transparent text-slate-500 hover:text-slate-800 dark:hover:text-white hover:bg-slate-100/50 dark:hover:bg-slate-700/20' }}">
                    <span>Repartidores</span>
                </a>
                <a href="{{ route('admin.users.index', ['tab' => 'super_admin', 'status' => $status, 'search' => $search]) }}" 
                   class="flex items-center gap-2 px-6 py-4 border-b-2 font-bold text-sm transition-all duration-200 {{ $tab === 'super_admin' ? 'border-luffy-red text-luffy-red dark:text-rose-450 bg-white dark:bg-slate-800' : 'border-transparent text-slate-500 hover:text-slate-800 dark:hover:text-white hover:bg-slate-100/50 dark:hover:bg-slate-700/20' }}">
                    <span>Admins</span>
                </a>
            </div>

            <!-- Filtros por Estado y Buscador -->
            <div class="p-6 flex flex-col md:flex-row gap-4 items-center justify-between border-b border-slate-100 dark:border-slate-700/60">
                <div class="flex flex-wrap gap-2 w-full md:w-auto">
                    <a href="{{ route('admin.users.index', ['tab' => $tab, 'status' => 'all', 'search' => $search]) }}" 
                       class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider transition-all duration-200 {{ $status === 'all' ? 'bg-slate-800 dark:bg-slate-900 text-white shadow-lg shadow-slate-900/20' : 'bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-700/70 text-slate-600 dark:text-slate-300' }}">
                        Todos
                    </a>
                    <a href="{{ route('admin.users.index', ['tab' => $tab, 'status' => 'active', 'search' => $search]) }}" 
                       class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider transition-all duration-200 {{ $status === 'active' ? 'bg-emerald-500 text-white shadow-lg shadow-emerald-500/25' : 'bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-700/70 text-slate-600 dark:text-slate-300' }}">
                        Activos
                    </a>
                    <a href="{{ route('admin.users.index', ['tab' => $tab, 'status' => 'blocked', 'search' => $search]) }}" 
                       class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider transition-all duration-200 {{ $status === 'blocked' ? 'bg-rose-500 text-white shadow-lg shadow-rose-500/25' : 'bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-700/70 text-slate-600 dark:text-slate-300' }}">
                        Bloqueados
                    </a>
                </div>

                <!-- Buscador -->
                <form action="{{ route('admin.users.index') }}" method="GET" class="w-full md:w-80 flex gap-2">
                    <input type="hidden" name="tab" value="{{ $tab }}">
                    <input type="hidden" name="status" value="{{ $status }}">
                    <div class="relative w-full">
                        <input type="text" name="search" value="{{ $search }}" placeholder="Buscar por nombre, email, DNI..." 
                               class="w-full pl-4 pr-10 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                        <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </span>
                    </div>
                    <button type="submit" class="px-5 py-2.5 bg-slate-850 hover:bg-slate-900 text-white font-extrabold text-xs uppercase tracking-wider rounded-2xl transition-all shadow-md shadow-slate-800/10 cursor-pointer">
                        Buscar
                    </button>
                </form>
            </div>

            <!-- Tabla de Usuarios -->
            <div class="overflow-x-auto">
                @if($users->isEmpty())
                    <div class="p-16 text-center text-slate-500 dark:text-slate-400">
                        <svg class="w-16 h-16 mx-auto mb-4 text-slate-300 dark:text-slate-600 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <p class="text-base font-black font-['Outfit'] text-slate-700 dark:text-slate-300">No se encontraron usuarios</p>
                        <p class="text-xs mt-1 font-medium font-sans">Prueba cambiando los filtros o la búsqueda de texto.</p>
                    </div>
                @else
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50 dark:bg-slate-900/40 border-b border-slate-100 dark:border-slate-700/60 text-slate-400 dark:text-slate-455 text-[10px] font-black uppercase tracking-wider">
                                <th class="px-6 py-4.5">Usuario</th>
                                <th class="px-6 py-4.5">DNI</th>
                                <th class="px-6 py-4.5">Contacto</th>
                                <th class="px-6 py-4.5">Roles</th>
                                <th class="px-6 py-4.5 text-center">Estado</th>
                                <th class="px-6 py-4.5 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700 text-sm">
                            @foreach($users as $user)
                                <tr class="hover:bg-slate-50/40 dark:hover:bg-slate-700/20 transition-colors">
                                    <td class="px-6 py-4.5">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-slate-100 to-slate-200 dark:from-slate-700 dark:to-slate-600 text-slate-700 dark:text-slate-200 flex items-center justify-center font-extrabold text-sm shadow-inner font-['Outfit']">
                                                {{ substr($user->first_name, 0, 1) }}{{ substr($user->last_name, 0, 1) }}
                                            </div>
                                            <div>
                                                <span class="font-extrabold text-slate-800 dark:text-white block font-['Outfit']">{{ $user->first_name }} {{ $user->last_name }}</span>
                                                <span class="text-xs text-slate-450 dark:text-slate-400 font-medium font-mono block">{{ $user->id }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4.5">
                                        <span class="font-mono font-bold text-slate-700 dark:text-slate-300">{{ $user->dni ?? 'N/A' }}</span>
                                    </td>
                                    <td class="px-6 py-4.5">
                                        <span class="block font-semibold text-slate-700 dark:text-slate-300">{{ $user->email }}</span>
                                        <span class="text-xs text-slate-450 dark:text-slate-400 font-medium">{{ $user->phone ?? 'Sin teléfono' }}</span>
                                    </td>
                                    <td class="px-6 py-4.5">
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($user->roles as $role)
                                                @php
                                                    $roleClasses = match($role->slug) {
                                                        \App\Models\Role::SUPER_ADMIN => 'bg-violet-50 dark:bg-violet-950/30 text-violet-600 dark:text-violet-400 border border-violet-200/40 dark:border-violet-900/30',
                                                        \App\Models\Role::SELLER => 'bg-amber-50 dark:bg-amber-950/30 text-amber-600 dark:text-amber-400 border border-amber-200/40 dark:border-amber-900/30',
                                                        \App\Models\Role::DRIVER => 'bg-rose-50 dark:bg-rose-950/30 text-rose-600 dark:text-rose-455 border border-rose-200/40 dark:border-rose-900/30',
                                                        default => 'bg-sky-50 dark:bg-sky-950/30 text-sky-600 dark:text-sky-400 border border-sky-200/40 dark:border-sky-900/30',
                                                    };
                                                @endphp
                                                <span class="px-2 py-0.5 rounded-lg text-[9px] font-black uppercase tracking-wider {{ $roleClasses }}">
                                                    {{ $role->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="px-6 py-4.5 text-center">
                                        @if($user->is_active)
                                            <span class="px-3 py-1 rounded-xl text-[10px] font-black uppercase tracking-wider bg-emerald-50 dark:bg-emerald-950/30 text-emerald-600 dark:text-emerald-400 border border-emerald-200/40 dark:border-emerald-900/30">Activo</span>
                                        @else
                                            <span class="px-3 py-1 rounded-xl text-[10px] font-black uppercase tracking-wider bg-rose-50 dark:bg-rose-950/30 text-rose-600 dark:text-rose-455 border border-rose-200/40 dark:border-rose-900/30">Bloqueado</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4.5 text-right">
                                        @if($user->id !== auth()->id())
                                            <form action="{{ route('admin.users.toggle-status', $user->id) }}" method="POST" class="inline-block" onsubmit="return confirm('¿Estás seguro de que deseas cambiar el estado de este usuario?')">
                                                @csrf
                                                @if($user->is_active)
                                                    <button type="submit" class="px-3.5 py-1.5 bg-rose-500 hover:bg-rose-600 text-white font-extrabold text-[10px] uppercase tracking-wider rounded-xl transition-all shadow-md shadow-rose-500/15 cursor-pointer">
                                                        Bloquear
                                                    </button>
                                                @else
                                                    <button type="submit" class="px-3.5 py-1.5 bg-emerald-500 hover:bg-emerald-600 text-white font-extrabold text-[10px] uppercase tracking-wider rounded-xl transition-all shadow-md shadow-emerald-500/15 cursor-pointer">
                                                        Activar
                                                    </button>
                                                @endif
                                            </form>
                                        @else
                                            <span class="text-xs text-slate-400 font-bold uppercase tracking-wider italic">Tú (Sesión)</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

            <!-- Paginación -->
            @if($users->hasPages())
                <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-700 bg-slate-50/60 dark:bg-slate-900/20 flex items-center justify-between">
                    <div class="text-xs text-slate-450 dark:text-slate-400 font-medium">
                        Mostrando {{ $users->firstItem() }} al {{ $users->lastItem() }} de {{ $users->total() }} registros.
                    </div>
                    <div class="pagination-native">
                        {{ $users->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-layouts.admin>
