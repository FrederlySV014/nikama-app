<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Crear Cuenta - Nikama</title>
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
            <img src="{{ asset('nikama-customers-auth.jpg') }}" alt="Nikama Registro" class="w-full h-full object-cover">
        </div>

        <!-- Columna Derecha: Formulario (100% en móvil, 50% en desktop) -->
        <div class="w-full lg:w-1/2 min-h-screen flex items-center justify-center p-6 sm:p-12 md:p-16 lg:p-20 overflow-y-auto relative">
            <!-- Decoraciones de fondo -->
            <div class="absolute top-1/4 left-1/4 w-[250px] h-[250px] bg-luffy-red/5 blur-[80px] rounded-full pointer-events-none"></div>
            <div class="absolute bottom-1/4 right-1/4 w-[250px] h-[250px] bg-luffy-straw/5 blur-[80px] rounded-full pointer-events-none"></div>

            <div class="w-full max-w-md space-y-8 z-10 my-8">
                <!-- Encabezado con Logo y Título -->
                <div class="text-center">
                    <a href="{{ route('public.welcome') }}" class="inline-block hover:scale-105 transition-transform mb-4">
                        <x-logo class="w-16 h-16 text-luffy-red" />
                    </a>
                    <h1 class="text-3xl font-extrabold font-['Outfit'] text-slate-900 dark:text-white tracking-tight">
                        Crea tu Cuenta
                    </h1>
                    <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">
                        Únete a Nikama y pide comida más rápido que nadie.
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
                <form class="space-y-4" action="{{ route('register.post') }}" method="POST">
                    @csrf
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="first_name" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Nombre</label>
                            <input id="first_name" name="first_name" type="text" required value="{{ old('first_name') }}"
                                class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-900 dark:text-white font-medium focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all"
                                placeholder="Tu nombre">
                        </div>

                        <div>
                            <label for="last_name" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Apellido</label>
                            <input id="last_name" name="last_name" type="text" required value="{{ old('last_name') }}"
                                class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-900 dark:text-white font-medium focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all"
                                placeholder="Tu apellido">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="email" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Correo electrónico</label>
                            <input id="email" name="email" type="email" required value="{{ old('email') }}"
                                class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-900 dark:text-white font-medium focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all"
                                placeholder="correo@ejemplo.com">
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Celular / Teléfono</label>
                            <input id="phone" name="phone" type="text" required value="{{ old('phone') }}"
                                class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-900 dark:text-white font-medium focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all"
                                placeholder="999888777">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label for="dni" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">DNI (Opcional)</label>
                            <input id="dni" name="dni" type="text" value="{{ old('dni') }}"
                                class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-900 dark:text-white font-medium focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all"
                                placeholder="8 dígitos">
                        </div>

                        <div>
                            <label for="birth_date" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">F. Nacimiento (Op.)</label>
                            <input id="birth_date" name="birth_date" type="date" value="{{ old('birth_date') }}"
                                class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-900 dark:text-white font-medium focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all">
                        </div>

                        <div>
                            <label for="gender" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Género (Op.)</label>
                            <select id="gender" name="gender"
                                class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-900 dark:text-white font-medium focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all cursor-pointer">
                                <option value="">Selecciona</option>
                                <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Masculino</option>
                                <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Femenino</option>
                                <option value="other" {{ old('gender') === 'other' ? 'selected' : '' }}>Otro</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="password" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Contraseña</label>
                            <input id="password" name="password" type="password" required
                                class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-900 dark:text-white font-medium focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all"
                                placeholder="Mínimo 8 caracteres">
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Confirmar</label>
                            <input id="password_confirmation" name="password_confirmation" type="password" required
                                class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-900 dark:text-white font-medium focus:outline-none focus:ring-2 focus:ring-luffy-red focus:border-transparent transition-all"
                                placeholder="Repite contraseña">
                        </div>
                    </div>

                    <div class="pt-2">
                        <button type="submit"
                            class="group relative w-full flex justify-center py-3.5 px-4 border border-transparent rounded-xl text-white bg-luffy-red hover:bg-luffy-red-hover font-bold text-lg transition-all shadow-lg shadow-luffy-red/20 hover:scale-[1.02] active:scale-[0.98] cursor-pointer">
                            Crear Cuenta
                        </button>
                    </div>
                </form>

                <div class="text-center text-sm">
                    <span class="text-slate-600 dark:text-slate-400">¿Ya tienes una cuenta?</span>
                    <a href="{{ route('login') }}" class="font-bold text-luffy-red hover:text-luffy-red-hover transition ml-1">Inicia sesión aquí</a>
                </div>

                <!-- Accesos a otros registros -->
                <div class="pt-6 border-t border-gray-200 dark:border-slate-800 flex justify-between gap-4 text-xs font-bold text-slate-500">
                    <a href="{{ route('seller.register') }}" class="hover:text-slate-800 dark:hover:text-white transition">Registrar un Negocio</a>
                    <a href="{{ route('driver.register') }}" class="hover:text-slate-800 dark:hover:text-white transition">Postular como Repartidor</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
