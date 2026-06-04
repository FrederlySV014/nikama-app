@php
    $colors = [
        'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400',
        'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400',
        'bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400',
        'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400',
        'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400',
        'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400',
        'bg-cyan-100 dark:bg-cyan-900/30 text-cyan-600 dark:text-cyan-400',
        'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400',
        'bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400',
        'bg-pink-100 dark:bg-pink-900/30 text-pink-600 dark:text-pink-400',
        'bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400',
        'bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400',
        'bg-lime-100 dark:bg-lime-900/30 text-lime-600 dark:text-lime-400',
        'bg-sky-100 dark:bg-sky-900/30 text-sky-600 dark:text-sky-400',
        'bg-violet-100 dark:bg-violet-900/30 text-violet-600 dark:text-violet-400',
        'bg-teal-100 dark:bg-teal-900/30 text-teal-600 dark:text-teal-400',
        'bg-fuchsia-100 dark:bg-fuchsia-900/30 text-fuchsia-600 dark:text-fuchsia-400',
    ];

    $initialCount = 6;
    $businessesList = $businesses ?? collect();
@endphp

@if ($businessesList->isNotEmpty())
<!-- 2. Marcas y Negocios Locales -->
<section class="py-10 transition-colors duration-300" id="negocios-section">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold font-['Outfit'] text-gray-900 dark:text-white">Negocios Locales</h2>
            @if ($businessesList->count() > $initialCount)
                <button id="verTodosBtn" class="text-sm font-semibold text-luffy-red hover:text-luffy-red-hover transition">Ver todos</button>
            @endif
        </div>

        <div class="negocios-grid">
            @foreach($businessesList as $index => $negocio)
                @php
                    $color = $colors[$loop->index % count($colors)];
                    $logoUrl = $negocio->logo_url;
                    
                    // Iniciales si no tiene logo
                    $iniciales = '';
                    if (!$logoUrl) {
                        $words = explode(' ', trim($negocio->business_name));
                        if (count($words) >= 2) {
                            $iniciales = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
                        } else {
                            $iniciales = strtoupper(substr($negocio->business_name, 0, 2));
                        }
                    }
                @endphp
                <a href="#" class="group negocios-item" data-initial="{{ $index < $initialCount ? 'true' : 'false' }}" style="{{ $index < $initialCount ? '' : 'display: none;' }}" data-slug="{{ $negocio->slug }}" title="{{ $negocio->business_name }}">
                    <div class="card-negocio">
                        @if ($logoUrl)
                            <div class="icon-wrapper bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-850 overflow-hidden shadow-inner">
                                <img src="{{ $logoUrl }}" alt="{{ $negocio->business_name }}" class="w-full h-full object-contain p-1.5">
                            </div>
                        @else
                            <div class="icon-wrapper {{ $color }} shadow-sm">
                                <span class="font-black select-none tracking-wider font-mono">{{ $iniciales }}</span>
                            </div>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

<style>
    /* Grid con CSS puro - Responsive */
    .negocios-grid {
        display: grid;
        gap: 0.375rem;
        /* Pantallas muy pequeñas (< 425px): 4 columnas */
        grid-template-columns: repeat(4, 1fr);
    }

    /* Card del negocio */
    .card-negocio {
        width: 100%;
        aspect-ratio: 1 / 1;
        height: auto;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 0.1rem;
        border-radius: 0.25rem;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .card-negocio:hover {
        background-color: #f9fafb;
    }

    .dark .card-negocio:hover {
        background-color: #1e293b;
    }

    /* Icono */
    .icon-wrapper {
        width: 2rem;
        height: 2rem;
        border-radius: 0.125rem;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    .icon-wrapper span {
        font-size: 0.7rem;
    }

    .group:hover .icon-wrapper {
        transform: scale(1.1);
    }

    .icon-svg {
        width: 1rem;
        height: 1rem;
    }

    /* Nombre del negocio */
    .nombre-negocio {
        font-weight: 600;
        font-size: 0.625rem;
        text-align: center;
        color: #374151;
        transition: color 0.3s ease;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        width: 100%;
        padding: 0 0.125rem;
    }

    .dark .nombre-negocio {
        color: #d1d5db;
    }

    .group:hover .nombre-negocio {
        color: #ef4444;
    }

    .dark .group:hover .nombre-negocio {
        color: #fcd34d;
    }

    /* Categoría */
    .categoria-negocio {
        font-size: 0.5rem;
        text-align: center;
        color: #6b7280;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        width: 100%;
        padding: 0 0.125rem;
    }

    .dark .categoria-negocio {
        color: #9ca3af;
    }


    /* Responsive - Aumentamos columnas en pantallas más grandes */
    @media (min-width: 425px) {
        .negocios-grid {
            grid-template-columns: repeat(5, 1fr);
            gap: 0.5rem;
        }

        .icon-wrapper {
            width: 2.25rem;
            height: 2.25rem;
        }

        .icon-wrapper span {
            font-size: 0.775rem;
        }

        .icon-svg {
            width: 1.6rem;
            height: 1.6rem;
        }
    }

    @media (min-width: 640px) {
        .negocios-grid {
            grid-template-columns: repeat(6, 1fr);
            gap: 0.5rem;
        }

        .icon-wrapper {
            width: 2.5rem;
            height: 2.5rem;
        }

        .icon-wrapper span {
            font-size: 0.85rem;
        }

        .icon-svg {
            width: 1.85rem;
            height: 1.85rem;
        }
    }

    @media (min-width: 768px) {
        .negocios-grid {
            grid-template-columns: repeat(8, 1fr);
        }
    }

    @media (min-width: 1024px) {
        .negocios-grid {
            grid-template-columns: repeat(10, 1fr);
        }
    }

    @media (min-width: 1280px) {
        .negocios-grid {
            grid-template-columns: repeat(12, 1fr);
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const verTodosBtn = document.getElementById('verTodosBtn');
        const items = document.querySelectorAll('.negocios-item');
        let showAll = false;

        if (verTodosBtn) {
            verTodosBtn.addEventListener('click', function() {
                showAll = !showAll;

                items.forEach(item => {
                    const isInitial = item.getAttribute('data-initial') === 'true';
                    if (showAll) {
                        item.style.display = '';
                    } else {
                        item.style.display = isInitial ? '' : 'none';
                    }
                });

                verTodosBtn.textContent = showAll ? 'Ver menos' : 'Ver todos';
            });
        }
    });
</script>