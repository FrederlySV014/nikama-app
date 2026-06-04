<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registra tu Negocio - Nikama Socios</title>
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
            <img src="{{ asset('nikama-sellers-auth.jpg') }}" alt="Nikama Vendedores Registro" class="w-full h-full object-cover">
        </div>

        <!-- Columna Derecha: Formulario (100% en móvil, 50% en desktop) -->
        <div class="w-full lg:w-1/2 min-h-screen flex items-center justify-center p-6 sm:p-12 md:p-16 lg:p-20 overflow-y-auto relative">
            <!-- Decoraciones de fondo -->
            <div class="absolute top-1/4 left-1/4 w-[250px] h-[250px] bg-luffy-straw/5 blur-[80px] rounded-full pointer-events-none"></div>
            <div class="absolute bottom-1/4 right-1/4 w-[250px] h-[250px] bg-luffy-red/5 blur-[80px] rounded-full pointer-events-none"></div>

            <div class="w-full max-w-md space-y-8 z-10 my-8">
                <!-- Encabezado con Logo y Título -->
                <div class="text-center">
                    <a href="{{ route('public.welcome') }}" class="inline-block hover:scale-105 transition-transform mb-4">
                        <x-logo class="w-16 h-16 text-luffy-straw" />
                    </a>
                    <div class="flex justify-center mb-2">
                        <span class="inline-block px-3 py-1 rounded-full bg-luffy-straw/10 text-luffy-straw-dark dark:text-luffy-straw font-bold text-xs tracking-wider uppercase">Portal de Negocios</span>
                    </div>
                    <h1 class="text-3xl font-extrabold font-['Outfit'] text-slate-900 dark:text-white tracking-tight">
                        Postula tu Negocio
                    </h1>
                    <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">
                        Únete a la red de restaurantes y tiendas aliadas de Nikama.
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
                <form class="space-y-6" action="{{ route('seller.register.post') }}" method="POST">
                    @csrf
                    
                    <!-- Sección 1: Datos del Administrador -->
                    <div class="space-y-4">
                        <h3 class="text-base font-bold text-slate-900 dark:text-white border-b border-gray-200 dark:border-slate-800 pb-2">1. Datos del Representante / Administrador</h3>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="first_name" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Nombre</label>
                                <input id="first_name" name="first_name" type="text" required value="{{ old('first_name') }}"
                                    class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-900 dark:text-white font-medium focus:outline-none focus:ring-2 focus:ring-luffy-straw focus:border-transparent transition-all"
                                    placeholder="Nombre completo">
                            </div>

                            <div>
                                <label for="last_name" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Apellido</label>
                                <input id="last_name" name="last_name" type="text" required value="{{ old('last_name') }}"
                                    class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-900 dark:text-white font-medium focus:outline-none focus:ring-2 focus:ring-luffy-straw focus:border-transparent transition-all"
                                    placeholder="Apellido completo">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="email" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Correo electrónico personal</label>
                                <input id="email" name="email" type="email" required value="{{ old('email') }}"
                                    class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-900 dark:text-white font-medium focus:outline-none focus:ring-2 focus:ring-luffy-straw focus:border-transparent transition-all"
                                    placeholder="tucorreo@ejemplo.com">
                            </div>

                            <div>
                                <label for="phone" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Celular personal</label>
                                <input id="phone" name="phone" type="text" required value="{{ old('phone') }}"
                                    class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-900 dark:text-white font-medium focus:outline-none focus:ring-2 focus:ring-luffy-straw focus:border-transparent transition-all"
                                    placeholder="999888777">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="password" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Contraseña</label>
                                <input id="password" name="password" type="password" required
                                    class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-900 dark:text-white font-medium focus:outline-none focus:ring-2 focus:ring-luffy-straw focus:border-transparent transition-all"
                                    placeholder="Mínimo 8 caracteres">
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Confirmar Contraseña</label>
                                <input id="password_confirmation" name="password_confirmation" type="password" required
                                    class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-900 dark:text-white font-medium focus:outline-none focus:ring-2 focus:ring-luffy-straw focus:border-transparent transition-all"
                                    placeholder="Repita la contraseña">
                            </div>
                        </div>
                    </div>

                    <!-- Sección 2: Datos del Negocio -->
                    <div class="space-y-4 pt-4">
                        <h3 class="text-base font-bold text-slate-900 dark:text-white border-b border-gray-200 dark:border-slate-800 pb-2">2. Datos de la Empresa / Negocio</h3>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="business_name" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Nombre Comercial</label>
                                <input id="business_name" name="business_name" type="text" required value="{{ old('business_name') }}"
                                    class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-900 dark:text-white font-medium focus:outline-none focus:ring-2 focus:ring-luffy-straw focus:border-transparent transition-all"
                                    placeholder="Ej. Pizzería Luffy">
                            </div>

                            <div>
                                <label for="legal_name" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Razón Social</label>
                                <input id="legal_name" name="legal_name" type="text" required value="{{ old('legal_name') }}"
                                    class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-900 dark:text-white font-medium focus:outline-none focus:ring-2 focus:ring-luffy-straw focus:border-transparent transition-all"
                                    placeholder="Ej. Luffy Alimentos S.A.C.">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="ruc" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">RUC</label>
                                <input id="ruc" name="ruc" type="text" required value="{{ old('ruc') }}"
                                    class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-900 dark:text-white font-medium focus:outline-none focus:ring-2 focus:ring-luffy-straw focus:border-transparent transition-all"
                                    placeholder="11 dígitos">
                            </div>

                            <div>
                                <label for="whatsapp_number" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">WhatsApp del negocio (Op.)</label>
                                <input id="whatsapp_number" name="whatsapp_number" type="text" value="{{ old('whatsapp_number') }}"
                                    class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-900 dark:text-white font-medium focus:outline-none focus:ring-2 focus:ring-luffy-straw focus:border-transparent transition-all"
                                    placeholder="Ej. 999888777">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="contact_email" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Correo de Contacto del negocio</label>
                                <input id="contact_email" name="contact_email" type="email" required value="{{ old('contact_email') }}"
                                    class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-900 dark:text-white font-medium focus:outline-none focus:ring-2 focus:ring-luffy-straw focus:border-transparent transition-all"
                                    placeholder="contacto@minegocio.com">
                            </div>

                            <div>
                                <label for="contact_phone" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Teléfono de Contacto del negocio</label>
                                <input id="contact_phone" name="contact_phone" type="text" required value="{{ old('contact_phone') }}"
                                    class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-900 dark:text-white font-medium focus:outline-none focus:ring-2 focus:ring-luffy-straw focus:border-transparent transition-all"
                                    placeholder="Fijo o celular de contacto">
                            </div>
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Breve Descripción del negocio</label>
                            <textarea id="description" name="description" rows="3"
                                class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-900 dark:text-white font-medium focus:outline-none focus:ring-2 focus:ring-luffy-straw focus:border-transparent transition-all resize-none"
                                placeholder="Describe qué vende tu negocio (Ej. Pizzas artesanales a la leña)...">{{ old('description') }}</textarea>
                        </div>
                    </div>

                    <div class="pt-4">
                        <button type="submit"
                            class="group relative w-full flex justify-center py-3.5 px-4 border border-transparent rounded-xl text-white bg-luffy-straw-dark hover:bg-luffy-straw-hover font-bold text-lg transition-all shadow-lg shadow-luffy-straw-dark/20 hover:scale-[1.02] active:scale-[0.98] cursor-pointer">
                            Enviar Solicitud de Registro
                        </button>
                    </div>
                </form>

                <div class="text-center text-sm">
                    <span class="text-slate-600 dark:text-slate-400">¿Ya tienes un negocio registrado?</span>
                    <a href="{{ route('seller.login') }}" class="font-bold text-luffy-straw-dark hover:text-luffy-straw transition ml-1">Inicia sesión en tu portal</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
