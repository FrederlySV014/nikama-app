<section class="py-10 bg-white dark:bg-slate-900 transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold font-['Outfit'] text-gray-900 dark:text-white">Marcas Populares</h2>
            <a href="#" class="text-sm font-semibold text-luffy-red hover:text-luffy-red-hover transition">Ver todos</a>
        </div>

        <div class="flex gap-4 overflow-x-auto pb-4 scrollbar-hide snap-x">
            @php
                $franquicias = [
                    ['name' => "McDonald's", 'bg' => 'bg-yellow-400', 'text' => 'text-yellow-800', 'border' => 'border-yellow-500'],
                    ['name' => 'Bembos', 'bg' => 'bg-red-600', 'text' => 'text-white', 'border' => 'border-red-700'],
                    ['name' => 'Popeyes', 'bg' => 'bg-orange-500', 'text' => 'text-white', 'border' => 'border-orange-600'],
                    ['name' => "Papa John's", 'bg' => 'bg-red-700', 'text' => 'text-white', 'border' => 'border-red-800'],
                    ['name' => 'Chinawok', 'bg' => 'bg-red-500', 'text' => 'text-white', 'border' => 'border-red-600'],
                    ['name' => 'KFC', 'bg' => 'bg-white', 'text' => 'text-red-700', 'border' => 'border-red-700'],
                    ['name' => 'Little Caesars', 'bg' => 'bg-blue-600', 'text' => 'text-white', 'border' => 'border-blue-700'],
                    ['name' => "Fridays", 'bg' => 'bg-red-600', 'text' => 'text-white', 'border' => 'border-red-700'],
                    ['name' => 'Dunkin Donuts', 'bg' => 'bg-pink-500', 'text' => 'text-white', 'border' => 'border-pink-600'],
                    ['name' => 'Subway', 'bg' => 'bg-green-600', 'text' => 'text-white', 'border' => 'border-green-700'],
                ];
            @endphp

            @foreach($franquicias as $franquicia)
                <a href="#" class="flex-shrink-0 group snap-start">
                    <div class="w-24 h-24 {{ $franquicia['bg'] }} {{ $franquicia['text'] }} rounded-full flex items-center justify-center shadow-md border-2 {{ $franquicia['border'] }} group-hover:scale-110 group-hover:shadow-xl transition-all duration-300">
                        <span class="font-bold text-xs text-center leading-tight px-1">{{ $franquicia['name'] }}</span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>