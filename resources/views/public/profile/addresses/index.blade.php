<x-layouts.public>
    <x-slot:title>Mis Direcciones - Nikama</x-slot:title>

    <div class="max-w-6xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
        <!-- Breadcrumbs -->
        <nav class="flex text-xs font-semibold text-slate-500 dark:text-slate-400 mb-6" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1.5 md:space-x-2">
                <li class="inline-flex items-center">
                    <a href="/" class="hover:text-slate-800 dark:hover:text-white transition-colors">Inicio</a>
                </li>
                <li class="flex items-center">
                    <svg class="w-3 h-3 text-slate-400 mx-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    <span class="text-slate-400 dark:text-slate-500">Mi Perfil</span>
                </li>
                <li class="flex items-center">
                    <svg class="w-3 h-3 text-slate-400 mx-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    <span class="text-slate-800 dark:text-slate-200 font-bold" aria-current="page">Mis Direcciones</span>
                </li>
            </ol>
        </nav>

        <!-- Status Alerts -->
        @if (session('success'))
            <div class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 rounded-2xl flex items-center gap-3">
                <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                <span class="font-medium text-sm">{{ session('success') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 p-4 bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 rounded-2xl">
                <ul class="list-disc list-inside text-sm space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Header Title and Add Button -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-extrabold font-['Outfit'] text-slate-800 dark:text-white">Direcciones de Entrega</h1>
                <p class="text-sm text-slate-550 dark:text-slate-400 mt-1">Registra y administra tus lugares de entrega frecuentes para agilizar tu compra.</p>
            </div>
            <a 
                href="{{ route('profile.addresses.create') }}" 
                class="bg-luffy-red hover:bg-luffy-red-hover text-white px-5 py-3 rounded-2xl font-bold text-sm shadow-md shadow-luffy-red/10 transition-all hover:scale-102 active:scale-98 flex items-center justify-center gap-2 cursor-pointer"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                Agregar Dirección
            </a>
        </div>

        <!-- Addresses List -->
        @if ($addresses->isEmpty())
            <div class="bg-white dark:bg-slate-800 rounded-3xl border border-dashed border-slate-200 dark:border-slate-700 p-12 text-center">
                <div class="w-16 h-16 rounded-full bg-slate-50 dark:bg-slate-900 flex items-center justify-center text-slate-350 dark:text-slate-650 border border-slate-100 dark:border-slate-800 mx-auto mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                </div>
                <h3 class="font-bold text-slate-800 dark:text-white text-base">No tienes direcciones registradas</h3>
                <p class="text-sm text-slate-550 dark:text-slate-400 mt-1 max-w-sm mx-auto">Agrega tus direcciones habituales (Casa, Oficina, etc.) para que aparezcan en tu checkout.</p>
                <a 
                    href="{{ route('profile.addresses.create') }}" 
                    class="mt-5 inline-block bg-slate-100 dark:bg-slate-700 hover:bg-slate-250 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 px-4 py-2 rounded-xl text-xs font-bold transition-all cursor-pointer"
                >
                    + Agregar Dirección
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($addresses as $addr)
                    <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700 p-6 flex flex-col justify-between gap-4 shadow-sm hover:shadow-md transition-shadow relative">
                        <!-- Default Badge -->
                        @if ($addr->is_default)
                            <span class="absolute top-6 right-6 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 text-[10px] font-black uppercase tracking-wider px-2 py-0.5 rounded-md border border-emerald-100 dark:border-emerald-900/30">
                                Predeterminada
                            </span>
                        @endif

                        <div class="space-y-3">
                            <div class="flex flex-wrap items-center gap-1.5">
                                <span class="inline-block px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300">
                                    {{ $addr->label }}
                                </span>
                                @if ($addr->address_type)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider bg-indigo-50 dark:bg-indigo-950/40 text-indigo-650 dark:text-indigo-400 border border-indigo-100/30">
                                        @if ($addr->address_type === 'home')
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                                            Casa
                                        @elseif ($addr->address_type === 'work')
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                            Trabajo
                                        @elseif ($addr->address_type === 'study')
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                            Estudios
                                        @else
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                            {{ $addr->address_type }}
                                        @endif
                                    </span>
                                @endif
                            </div>
                            
                            <div>
                                <h3 class="font-bold text-slate-900 dark:text-white text-base mt-2 pr-20">{{ $addr->address }}</h3>
                            </div>

                            @if ($addr->reference)
                                <p class="text-xs text-slate-450 dark:text-slate-400 italic">Ref: {{ $addr->reference }}</p>
                            @endif

                            @if ($addr->delivery_notes)
                                <div class="bg-slate-50 dark:bg-slate-900/30 p-2.5 rounded-xl border border-slate-100 dark:border-slate-700/50">
                                    <p class="text-[10px] text-slate-450 dark:text-slate-450 uppercase font-black tracking-wide mb-1">Notas de Entrega</p>
                                    <p class="text-xs text-slate-650 dark:text-slate-350 leading-relaxed">{{ $addr->delivery_notes }}</p>
                                </div>
                            @endif

                            <div class="text-xs space-y-1 text-slate-550 dark:text-slate-350">
                                <p class="font-medium">
                                    {{ $addr->district }}, {{ $addr->province }}, {{ $addr->department }}
                                    @if ($addr->postal_code)
                                        <span class="text-[11px] text-slate-450">({{ $addr->postal_code }})</span>
                                    @endif
                                </p>
                                <div class="pt-2 border-t border-slate-50 dark:border-slate-750/30 space-y-0.5">
                                    <p class="text-[10px] text-slate-400 uppercase font-bold tracking-wide">Contacto</p>
                                    <p class="font-semibold text-slate-800 dark:text-slate-200">{{ $addr->contact_name ?? '-' }}</p>
                                    <p>{{ $addr->contact_phone ?? '-' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Card Actions -->
                        <div class="flex items-center gap-2 pt-3 border-t border-slate-50 dark:border-slate-750/30 text-xs">
                            @if (! $addr->is_default)
                                <form action="{{ route('profile.addresses.default', $addr) }}" method="POST" class="inline flex-1">
                                    @csrf
                                    <button type="submit" class="w-full py-2 bg-slate-50 dark:bg-slate-750/40 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-xl font-bold transition-all cursor-pointer text-center">
                                        Fijar
                                    </button>
                                </form>
                            @endif
                            
                            <a 
                                href="{{ route('profile.addresses.edit', $addr) }}" 
                                class="flex-1 py-2 border border-slate-100 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-750 text-slate-700 dark:text-slate-300 rounded-xl font-bold transition-all cursor-pointer text-center"
                            >
                                Editar
                            </a>

                            <form action="{{ route('profile.addresses.destroy', $addr) }}" method="POST" class="inline" onsubmit="return confirm('¿Estás seguro de que deseas eliminar esta dirección?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-rose-500 hover:text-rose-600 dark:hover:bg-rose-950/20 rounded-xl transition-all cursor-pointer" aria-label="Eliminar dirección">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-layouts.public>
