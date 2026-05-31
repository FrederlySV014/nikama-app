<!-- 2. Marcas y Negocios Locales -->
<section class="py-10 transition-colors duration-300" id="negocios-section">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold font-['Outfit'] text-gray-900 dark:text-white">Negocios Locales</h2>
            <button id="verTodosBtn" class="text-sm font-semibold text-luffy-red hover:text-luffy-red-hover transition">Ver todos</button>
        </div>

        <div class="negocios-grid">
            @php
                $negociosLocales = [
                        ['name' => 'Metro', 'icon' => 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z', 'color' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400', 'category' => 'Supermercado'],
                        ['name' => 'Tottus', 'icon' => 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z', 'color' => 'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400', 'category' => 'Supermercado'],
                        ['name' => 'Marakos', 'icon' => 'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z M12 8v8m-4-4h8', 'color' => 'bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400', 'category' => 'Frutas'],
                        ['name' => 'Mi Felicita', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400', 'category' => 'Panadería'],
                        ['name' => 'Perunet', 'icon' => 'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'color' => 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400', 'category' => 'Tecnología'],
                        ['name' => 'Botica', 'icon' => 'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.414 1.414.586 3.414-.586 3.414H12.5a1 1 0 00-1 .5v2a1 1 0 001 1h2a1 1 0 001-1v-1.5a1 1 0 011-1h1a1 1 0 011 1v1.5a1 1 0 001 1h2a1 1 0 001-1V11.828l-5 1.414a1 1 0 00-.586.828z', 'color' => 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400', 'category' => 'Farmacia'],
                        ['name' => 'Open Plaza', 'icon' => 'M3 3h18v18H3V3z', 'color' => 'bg-cyan-100 dark:bg-cyan-900/30 text-cyan-600 dark:text-cyan-400', 'category' => 'Centro Comercial'],
                        ['name' => 'Real Plaza', 'icon' => 'M3 3h18v18H3V3z', 'color' => 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400', 'category' => 'Centro Comercial'],
                        ['name' => 'Makro', 'icon' => 'M3 3h2l.4 2M7 13h10l4-8H5.4', 'color' => 'bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400', 'category' => 'Mayorista'],
                        ['name' => 'Inkafarma', 'icon' => 'M19.428 15.428a2 2 0 00-1.022-.547', 'color' => 'bg-pink-100 dark:bg-pink-900/30 text-pink-600 dark:text-pink-400', 'category' => 'Farmacia'],
                        ['name' => 'Mifarma', 'icon' => 'M19.428 15.428a2 2 0 00-1.022-.547', 'color' => 'bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400', 'category' => 'Farmacia'],
                        ['name' => 'Pardos Chicken', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2', 'color' => 'bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400', 'category' => 'Pollería'],
                        ['name' => 'KFC', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2', 'color' => 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400', 'category' => 'Fast Food'],
                        ['name' => 'Bembos', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2', 'color' => 'bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400', 'category' => 'Hamburguesas'],
                        ['name' => 'Chinawok', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2', 'color' => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400', 'category' => 'Comida China'],
                        ['name' => 'Rústica', 'icon' => 'M9.75 17L9 20l-1 1h8', 'color' => 'bg-lime-100 dark:bg-lime-900/30 text-lime-600 dark:text-lime-400', 'category' => 'Restaurante'],
                        ['name' => 'Cix Phone', 'icon' => 'M9.75 17L9 20l-1 1h8', 'color' => 'bg-sky-100 dark:bg-sky-900/30 text-sky-600 dark:text-sky-400', 'category' => 'Tecnología'],
                        ['name' => 'CompuPlaza', 'icon' => 'M9.75 17L9 20l-1 1h8', 'color' => 'bg-violet-100 dark:bg-violet-900/30 text-violet-600 dark:text-violet-400', 'category' => 'Tecnología'],
                        ['name' => 'Starbucks', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2', 'color' => 'bg-teal-100 dark:bg-teal-900/30 text-teal-600 dark:text-teal-400', 'category' => 'Café'],
                        ['name' => 'Juan Valdez', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2', 'color' => 'bg-amber-900 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400', 'category' => 'Café'],
                        ['name' => 'Donofrio', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2', 'color' => 'bg-fuchsia-100 dark:bg-fuchsia-900/30 text-fuchsia-600 dark:text-fuchsia-400', 'category' => 'Helados'],
                    ];
                    
                    $initialCount = 6;
                @endphp

                @foreach($negociosLocales as $index => $negocio)
                    <a href="#" class="group negocios-item" data-initial="{{ $index < $initialCount ? 'true' : 'false' }}" style="{{ $index < $initialCount ? '' : 'display: none;' }}">
                        <div class="card-negocio">
                            <div class="icon-wrapper {{ $negocio['color'] }}">
                                <svg class="icon-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $negocio['icon'] }}"></path>
                                </svg>
                            </div>
                            <h3 class="nombre-negocio">{{ $negocio['name'] }}</h3>
                            <p class="categoria-negocio">{{ $negocio['category'] }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
    
    <style>
        /* Grid con CSS puro - Responsive */
        .negocios-grid {
            display: grid;
            gap: 0.5rem;
            /* Pantallas muy pequeñas (< 425px): 4 columnas */
            grid-template-columns: repeat(3, 1fr);
        }
        
        /* Card del negocio */
        .card-negocio {
            width: 100%;
            aspect-ratio: 1 / 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 0.25rem;
            padding: 0.25rem;
            border-radius: 0.75rem;
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
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
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
            
            .icon-svg {
                width: 1.125rem;
                height: 1.125rem;
            }
        }
        
        @media (min-width: 640px) {
            .negocios-grid {
                grid-template-columns: repeat(6, 1fr);
                gap: 0.75rem;
            }
            
            .icon-wrapper {
                width: 2.5rem;
                height: 2.5rem;
            }
            
            .icon-svg {
                width: 1.25rem;
                height: 1.25rem;
            }
            
            .nombre-negocio {
                font-size: 0.6875rem;
            }
            
            .categoria-negocio {
                font-size: 0.5625rem;
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