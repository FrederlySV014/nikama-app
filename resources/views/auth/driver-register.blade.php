<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Postula como Repartidor - Nikama Driver</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700|outfit:400,600,700,800&display=swap" rel="stylesheet" />
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>
<body class="antialiased font-sans flex flex-col min-h-screen bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 transition-colors duration-300">
    <div class="min-h-screen flex flex-col lg:flex-row">
        <!-- Columna Izquierda: Imagen (Oculta en móvil) -->
        <div class="hidden lg:block lg:w-1/2 h-screen sticky top-0 overflow-hidden bg-slate-100 dark:bg-slate-900">
            <img src="{{ asset('nikama-drivers-auth.jpg') }}" alt="Nikama Repartidores Registro" class="w-full h-full object-cover">
        </div>

        <!-- Columna Derecha: Formulario (100% en móvil, 50% en desktop) -->
        <div class="w-full lg:w-1/2 min-h-screen flex items-center justify-center p-6 sm:p-12 md:p-16 lg:p-20 overflow-y-auto relative">
            <!-- Decoraciones de fondo -->
            <div class="absolute top-1/4 left-1/4 w-[250px] h-[250px] bg-slate-500/5 blur-[80px] rounded-full pointer-events-none"></div>
            <div class="absolute bottom-1/4 right-1/4 w-[250px] h-[250px] bg-luffy-straw/5 blur-[80px] rounded-full pointer-events-none"></div>

            <div class="w-full max-w-md space-y-8 z-10 my-8">
                <!-- Encabezado con Logo y Título -->
                <div class="text-center">
                    <a href="{{ route('public.welcome') }}" class="inline-block hover:scale-105 transition-transform mb-4">
                        <x-logo class="w-16 h-16 text-slate-600 dark:text-slate-400" />
                    </a>
                    <div class="flex justify-center mb-2">
                        <span class="inline-block px-3 py-1 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold text-xs tracking-wider uppercase">Portal de Repartidores</span>
                    </div>
                    <h1 class="text-3xl font-extrabold font-['Outfit'] text-slate-900 dark:text-white tracking-tight">
                        Postula como Repartidor
                    </h1>
                    <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">
                        Regístrate y comienza a generar ingresos entregando con Nikama.
                    </p>
                </div>

                <!-- Bloque de Errores Globales (Parte Superior) -->
                @if ($errors->any())
                    <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 text-sm font-semibold">
                        <p class="font-extrabold mb-1">Por favor corrige los siguientes errores:</p>
                        <ul class="list-disc pl-5 space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Formulario -->
                <form class="space-y-6" action="{{ route('driver.register.post') }}" method="POST" x-data="{ vehicleType: '{{ old('vehicle_type', 'motorcycle') }}' }">
                    @csrf
                    
                    <!-- Sección 1: Datos Personales -->
                    <div class="space-y-4">
                        <h3 class="text-base font-bold text-slate-900 dark:text-white border-b border-gray-200 dark:border-slate-800 pb-2">1. Datos Personales</h3>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="first_name" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Nombre</label>
                                <input id="first_name" name="first_name" type="text" required value="{{ old('first_name') }}"
                                    class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-900 dark:text-white font-medium focus:outline-none focus:ring-2 focus:ring-slate-500 focus:border-transparent transition-all"
                                    placeholder="Nombre completo">
                            </div>

                            <div>
                                <label for="last_name" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Apellido</label>
                                <input id="last_name" name="last_name" type="text" required value="{{ old('last_name') }}"
                                    class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-900 dark:text-white font-medium focus:outline-none focus:ring-2 focus:ring-slate-500 focus:border-transparent transition-all"
                                    placeholder="Apellido completo">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="email" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Correo electrónico</label>
                                <input id="email" name="email" type="email" required value="{{ old('email') }}"
                                    class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-900 dark:text-white font-medium focus:outline-none focus:ring-2 focus:ring-slate-500 focus:border-transparent transition-all"
                                    placeholder="correo@ejemplo.com">
                            </div>

                            <div>
                                <label for="phone" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Celular / Teléfono</label>
                                <input id="phone" name="phone" type="text" required value="{{ old('phone') }}"
                                    class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-900 dark:text-white font-medium focus:outline-none focus:ring-2 focus:ring-slate-500 focus:border-transparent transition-all"
                                    placeholder="Ej. 999888777">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="password" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Contraseña</label>
                                <input id="password" name="password" type="password" required
                                    class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-900 dark:text-white font-medium focus:outline-none focus:ring-2 focus:ring-slate-500 focus:border-transparent transition-all"
                                    placeholder="Mínimo 8 caracteres">
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Confirmar</label>
                                <input id="password_confirmation" name="password_confirmation" type="password" required
                                    class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-900 dark:text-white font-medium focus:outline-none focus:ring-2 focus:ring-slate-500 focus:border-transparent transition-all"
                                    placeholder="Repita contraseña">
                            </div>
                        </div>
                    </div>

                    <!-- Sección 2: Información del Vehículo -->
                    <div class="space-y-4 pt-4">
                        <h3 class="text-base font-bold text-slate-900 dark:text-white border-b border-gray-200 dark:border-slate-800 pb-2">2. Vehículo</h3>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="vehicle_type" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Tipo de Vehículo</label>
                                <select id="vehicle_type" name="vehicle_type" x-model="vehicleType"
                                    class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-900 dark:text-white font-medium focus:outline-none focus:ring-2 focus:ring-slate-500 focus:border-transparent transition-all cursor-pointer">
                                    <option value="bicycle">Bicicleta</option>
                                    <option value="motorcycle">Motocicleta</option>
                                    <option value="car">Carro / Automóvil</option>
                                </select>
                            </div>

                            <!-- Campos requeridos si NO es bicicleta -->
                            <div x-show="vehicleType !== 'bicycle'">
                                <label for="license_plate" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Placa del Vehículo</label>
                                <input id="license_plate" name="license_plate" type="text" :required="vehicleType !== 'bicycle'" value="{{ old('license_plate') }}"
                                    class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-900 dark:text-white font-medium focus:outline-none focus:ring-2 focus:ring-slate-500 focus:border-transparent transition-all"
                                    placeholder="Nro de Placa">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4" x-show="vehicleType !== 'bicycle'">
                            <div>
                                <label for="license_number" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Licencia (Op.)</label>
                                <input id="license_number" name="license_number" type="text" value="{{ old('license_number') }}"
                                    class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-900 dark:text-white font-medium focus:outline-none focus:ring-2 focus:ring-slate-500 focus:border-transparent transition-all"
                                    placeholder="Nro de Licencia">
                            </div>

                            <div>
                                <label for="vehicle_brand" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Marca (Op.)</label>
                                <input id="vehicle_brand" name="vehicle_brand" type="text" value="{{ old('vehicle_brand') }}"
                                    class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-900 dark:text-white font-medium focus:outline-none focus:ring-2 focus:ring-slate-500 focus:border-transparent transition-all"
                                    placeholder="Ej. Honda">
                            </div>

                            <div>
                                <label for="vehicle_model" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Modelo/Color (Op.)</label>
                                <input id="vehicle_model" name="vehicle_model" type="text" value="{{ old('vehicle_model') }}"
                                    class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-900 dark:text-white font-medium focus:outline-none focus:ring-2 focus:ring-slate-500 focus:border-transparent transition-all"
                                    placeholder="Ej. CB125 Negro">
                            </div>
                        </div>
                    </div>

                    <!-- Sección 3: Contacto de Emergencia -->
                    <div class="space-y-4 pt-4">
                        <h3 class="text-base font-bold text-slate-900 dark:text-white border-b border-gray-200 dark:border-slate-800 pb-2">3. Contacto de Emergencia</h3>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="emergency_contact_name" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Nombre de Contacto</label>
                                <input id="emergency_contact_name" name="emergency_contact_name" type="text" required value="{{ old('emergency_contact_name') }}"
                                    class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-900 dark:text-white font-medium focus:outline-none focus:ring-2 focus:ring-slate-500 focus:border-transparent transition-all"
                                    placeholder="Nombre completo">
                            </div>

                            <div>
                                <label for="emergency_contact_phone" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Celular de Contacto</label>
                                <input id="emergency_contact_phone" name="emergency_contact_phone" type="text" required value="{{ old('emergency_contact_phone') }}"
                                    class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-900 dark:text-white font-medium focus:outline-none focus:ring-2 focus:ring-slate-500 focus:border-transparent transition-all"
                                    placeholder="Ej. 999111222">
                            </div>
                        </div>
                    </div>

                    <div class="pt-4">
                        <button type="submit"
                            class="group relative w-full flex justify-center py-3.5 px-4 border border-transparent rounded-xl text-white bg-slate-800 hover:bg-slate-700 font-bold text-lg transition-all shadow-lg shadow-slate-500/20 hover:scale-[1.02] active:scale-[0.98] cursor-pointer">
                            Enviar Solicitud de Postulación
                        </button>
                    </div>
                </form>

                <div class="text-center text-sm">
                    <span class="text-slate-600 dark:text-slate-400">¿Ya estás registrado como repartidor?</span>
                    <a href="{{ route('driver.login') }}" class="font-bold text-slate-700 hover:text-slate-600 transition ml-1">Inicia sesión en tu portal</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
