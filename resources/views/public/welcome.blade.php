<x-layouts.public title="Nikama - El Rey del Delivery">
    <style>
        @keyframes infiniteScroll {
            0% { transform: translateX(0); }
            100% { transform: translateX(-33.33%); }
        }
        .animate-infinite-scroll {
            animation: infiniteScroll 30s linear infinite;
            width: fit-content;
        }
        .hover\:pause:hover { animation-play-state: paused; }
        [x-cloak] { display: none !important; }
    </style>

    @include('public.welcome.categorias')
    @include('public.welcome.negocios-locales')
    @include('public.welcome.combos')
    @include('public.welcome.productos')
    <!-- @include('public.welcome.marcas-populares') -->
</x-layouts.public>