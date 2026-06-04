@php
    $colors = [
        'bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400',
        'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400',
        'bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400',
        'bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400',
        'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400',
        'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400',
        'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400',
        'bg-cyan-100 dark:bg-cyan-900/30 text-cyan-600 dark:text-cyan-400',
        'bg-pink-100 dark:bg-pink-900/30 text-pink-600 dark:text-pink-400',
        'bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400',
    ];

    // Asegurar que la lista no esté vacía e iterar duplicado para el efecto de scroll infinito
    $categoriesList = $categories ?? collect();
    if ($categoriesList->count() > 0) {
        $duplicatedList = collect();
        // Duplicamos al menos 3 veces o lo suficiente para llenar el carrusel infinito
        $iterations = $categoriesList->count() < 8 ? 4 : 2;
        for ($i = 0; $i < $iterations; $i++) {
            $duplicatedList = $duplicatedList->concat($categoriesList);
        }
        $categoriesList = $duplicatedList;
    }
@endphp

@if ($categoriesList->isNotEmpty())
<section class="py-8 bg-white dark:bg-slate-900 transition-colors duration-300 overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="relative">
            <!-- Sombras laterales para el efecto de desvanecimiento -->
            <div class="absolute left-0 top-0 bottom-0 w-16 bg-gradient-to-r from-white dark:from-slate-900 to-transparent z-10 pointer-events-none"></div>
            <div class="absolute right-0 top-0 bottom-0 w-16 bg-gradient-to-l from-white dark:from-slate-900 to-transparent z-10 pointer-events-none"></div>
            
            <div class="overflow-hidden">
                <div class="flex gap-1 animate-infinite-scroll hover:pause">
                    @foreach ($categoriesList as $categoria)
                        @php
                            $color = $colors[$loop->index % count($colors)];
                            $icon = $categoria->icon;
                            $isSvgPath = $icon && (str_starts_with(trim($icon), 'M') || str_contains($icon, ' '));
                        @endphp
                        <a href="#{{ $categoria->slug }}" class="flex-shrink-0 group snap-start" data-slug="{{ $categoria->slug }}">
                            <div class="w-32 flex flex-col items-center gap-2 py-3 px-2 rounded-2xl hover:bg-gray-50 dark:hover:bg-slate-800 transition-all duration-300 cursor-pointer">
                                
                                @if ($categoria->image_url)
                                    <!-- Renderizar Imagen subida por el Super Admin -->
                                    <div class="w-10 h-10 rounded-2xl overflow-hidden shadow-md group-hover:scale-110 group-hover:shadow-lg transition-all duration-300 border border-slate-100 dark:border-slate-800">
                                        <img src="{{ $categoria->image_url }}" alt="{{ $categoria->name }}" class="w-full h-full object-cover">
                                    </div>
                                @else
                                    <!-- Renderizar Icono / Emoji con fondo de color dinámico -->
                                    <div class="w-10 h-10 {{ $color }} rounded-2xl flex items-center justify-center shadow-md group-hover:scale-110 group-hover:shadow-lg transition-all duration-300">
                                        @if ($isSvgPath)
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $icon }}"></path>
                                            </svg>
                                        @elseif ($icon)
                                            <span class="text-xl select-none">{{ $icon }}</span>
                                        @else
                                            <!-- Fallback Icono por defecto (Folder/Category) -->
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                                            </svg>
                                        @endif
                                    </div>
                                @endif
                                
                                <span class="text-xs font-semibold text-gray-700 dark:text-gray-200 group-hover:text-luffy-red dark:group-hover:text-luffy-straw transition-colors text-center w-full truncate px-1" title="{{ $categoria->name }}">
                                    {{ $categoria->name }}
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
@endif