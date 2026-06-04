<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Estado de la Cuenta - Nikama</title>
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
    <div class="grow flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
        <!-- Decoraciones de fondo -->
        <div class="absolute top-1/4 left-1/4 w-[300px] h-[300px] bg-amber-500/5 blur-[100px] rounded-full pointer-events-none"></div>
        <div class="absolute bottom-1/4 right-1/4 w-[300px] h-[300px] bg-rose-500/5 blur-[100px] rounded-full pointer-events-none"></div>

        <div class="max-w-md w-full space-y-8 bg-white/75 dark:bg-slate-900/75 backdrop-blur-xl p-8 sm:p-10 rounded-3xl border border-gray-100 dark:border-slate-800 shadow-xl shadow-gray-200/50 dark:shadow-none z-10 text-center">
            
            @if($status === 'pending')
                <!-- Icono Reloj (Pendiente) -->
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-amber-100 dark:bg-amber-950/30 text-amber-600 dark:text-luffy-straw">
                    <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                
                <h2 class="mt-6 text-3xl font-extrabold font-['Outfit'] text-slate-900 dark:text-white">
                    Solicitud en Revisión
                </h2>
                
                <p class="mt-4 text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                    Hola <strong>{{ $name }}</strong>. Tu solicitud de registro para el rol de 
                    <strong>{{ $role === 'seller' ? 'Vendedor/Negocio' : 'Repartidor' }}</strong> está siendo revisada por nuestro equipo de administración.
                </p>
                
                <p class="mt-2 text-xs text-slate-500">
                    Te enviaremos un correo electrónico una vez que tu cuenta haya sido aprobada. ¡Gracias por tu paciencia!
                </p>

            @elseif($status === 'rejected')
                <!-- Icono X (Rechazado) -->
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-rose-100 dark:bg-rose-950/30 text-rose-600 dark:text-luffy-red">
                    <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                
                <h2 class="mt-6 text-3xl font-extrabold font-['Outfit'] text-slate-900 dark:text-white">
                    Solicitud Rechazada
                </h2>
                
                <p class="mt-4 text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                    Lamentamos informarte que tu solicitud de registro como 
                    <strong>{{ $role === 'seller' ? 'Vendedor' : 'Repartidor' }}</strong> ha sido rechazada por el equipo administrativo.
                </p>

                @if($reason)
                    <div class="mt-4 p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/30 border border-rose-100 dark:border-rose-900 text-left">
                        <span class="block text-xs font-bold uppercase text-rose-500 tracking-wider mb-1">Motivo del rechazo</span>
                        <p class="text-sm text-rose-800 dark:text-rose-300 font-medium">{{ $reason }}</p>
                    </div>
                @endif
                
                <p class="mt-4 text-xs text-slate-500">
                    Si consideras que es un error o deseas postular nuevamente con otros datos, por favor contáctanos al soporte.
                </p>

            @elseif($status === 'suspended')
                <!-- Icono Candado (Suspendido) -->
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 dark:bg-red-950/30 text-red-600">
                    <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                </div>
                
                <h2 class="mt-6 text-3xl font-extrabold font-['Outfit'] text-slate-900 dark:text-white">
                    Cuenta Suspendida
                </h2>
                
                <p class="mt-4 text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                    Tu acceso como <strong>{{ $role === 'seller' ? 'Vendedor' : 'Repartidor' }}</strong> ha sido temporalmente suspendido por incumplimiento de nuestros términos y condiciones de servicio.
                </p>

                @if($reason)
                    <div class="mt-4 p-4 rounded-2xl bg-red-50 dark:bg-red-950/30 border border-red-100 dark:border-red-900 text-left">
                        <span class="block text-xs font-bold uppercase text-red-500 tracking-wider mb-1">Detalle de suspensión</span>
                        <p class="text-sm text-red-800 dark:text-red-300 font-medium">{{ $reason }}</p>
                    </div>
                @endif

            @else
                <!-- Estado Indefinido / Ninguno -->
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-gray-100 dark:bg-slate-800 text-gray-600 dark:text-slate-300">
                    <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h2 class="mt-6 text-3xl font-extrabold font-['Outfit'] text-slate-900 dark:text-white">Sin Perfil Activo</h2>
                <p class="mt-4 text-sm text-slate-600 dark:text-slate-400">
                    No pudimos detectar una solicitud de registro pendiente para esta cuenta.
                </p>
            @endif

            <div class="mt-8 pt-6 border-t border-gray-100 dark:border-slate-800">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full flex justify-center py-3.5 px-4 border border-gray-200 dark:border-slate-800 rounded-xl text-slate-700 dark:text-slate-300 font-bold hover:bg-gray-50 dark:hover:bg-slate-800 transition cursor-pointer">
                        Cerrar Sesión
                    </button>
                </form>
            </div>

        </div>
    </div>
</body>
</html>
