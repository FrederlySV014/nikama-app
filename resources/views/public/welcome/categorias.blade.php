@php
    $categorias = [
        ['name' => 'Restaurantes', 'icon' => 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z', 'color' => 'bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400'],
        ['name' => 'Farmacias', 'icon' => 'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.414 1.414.586 3.414-.586 3.414H12.5a1 1 0 00-1 .5v2a1 1 0 001 1h2a1 1 0 001-1v-1.5a1 1 0 011-1h1a1 1 0 011 1v1.5a1 1 0 001 1h2a1 1 0 001-1V11.828l-5 1.414a1 1 0 00-.586.828z', 'color' => 'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400'],
        ['name' => 'Tiendas', 'icon' => 'M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z M9 22V12h6v10', 'color' => 'bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400'],
        ['name' => 'Mascotas', 'icon' => 'M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z', 'color' => 'bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400'],
        ['name' => 'Tecnología', 'icon' => 'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'color' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400'],
        ['name' => 'NikamaYa', 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z', 'color' => 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400'],
        ['name' => 'Supermercados', 'icon' => 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z M3 21h18M5 21v-8a2 2 0 012-2h10a2 2 0 012 2v8', 'color' => 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400'],
        ['name' => 'Bebidas', 'icon' => 'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z M12 8v8m-4-4h8', 'color' => 'bg-cyan-100 dark:bg-cyan-900/30 text-cyan-600 dark:text-cyan-400'],
        ['name' => 'Postres', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'bg-pink-100 dark:bg-pink-900/30 text-pink-600 dark:text-pink-400'],
        ['name' => 'Frutas y Verduras', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'bg-pink-100 dark:bg-pink-900/30 text-pink-600 dark:text-pink-400'],
        ['name' => 'Abarrotes', 'icon' => 'M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4', 'color' => 'bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400'],
    ];
@endphp

<section class="py-8 bg-white dark:bg-slate-900 transition-colors duration-300 overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="relative">
            <div class="absolute left-0 top-0 bottom-0 w-16 bg-gradient-to-r from-white dark:from-slate-900 to-transparent z-10 pointer-events-none"></div>
            <div class="absolute right-0 top-0 bottom-0 w-16 bg-gradient-to-l from-white dark:from-slate-900 to-transparent z-10 pointer-events-none"></div>
            
            <div class="overflow-hidden">
                <div class="flex gap-1 animate-infinite-scroll hover:pause">
                    @foreach(array_merge($categorias, $categorias, $categorias) as $categoria)
                        <a href="#" class="flex-shrink-0 group snap-start">
                            <div class="w-32 flex flex-col items-center gap-2 py-3 px-2 rounded-2xl hover:bg-gray-50 dark:hover:bg-slate-800 transition-all duration-300 cursor-pointer">
                                <div class="w-10 h-10 {{ $categoria['color'] }} rounded-2xl flex items-center justify-center shadow-md group-hover:scale-110 group-hover:shadow-lg transition-all duration-300">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $categoria['icon'] }}"></path>
                                    </svg>
                                </div>
                                <span class="text-xs font-semibold text-gray-700 dark:text-gray-200 group-hover:text-luffy-red dark:group-hover:text-luffy-straw transition-colors text-center w-full truncate px-1" title="{{ $categoria['name'] }}">
                                    {{ $categoria['name'] }}
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>