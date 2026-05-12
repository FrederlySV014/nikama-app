<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nikama Delivery</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-50 text-gray-800">
    <div class="min-h-screen flex flex-col">
        <header class="bg-white shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
                <h1 class="text-2xl font-bold text-orange-600">Nikama</h1>
                <nav class="flex gap-4">
                    <a href="#" class="text-gray-600 hover:text-orange-600 font-medium">Menú</a>
                    <a href="#" class="text-gray-600 hover:text-orange-600 font-medium">Nosotros</a>
                    <a href="#" class="text-gray-600 hover:text-orange-600 font-medium">Contacto</a>
                </nav>
            </div>
        </header>

        <main class="flex-1">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                <div class="text-center">
                    <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
                        Tu comida favorita, entregada a domicilio
                    </h2>
                    <p class="text-xl text-gray-600 mb-8 max-w-2xl mx-auto">
                        Rápido, seguro y directo a tu puerta. Los mejores restaurantes de la ciudad disponibles en tu teléfono.
                    </p>
                    <div class="flex justify-center gap-4">
                        <a href="#" class="bg-orange-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-orange-700 transition">
                            Pedir ahora
                        </a>
                        <a href="#" class="bg-white text-orange-600 border-2 border-orange-600 px-8 py-3 rounded-lg font-semibold hover:bg-orange-50 transition">
                            Ver menú
                        </a>
                    </div>
                </div>

                <div class="mt-20 grid md:grid-cols-3 gap-8">
                    <div class="bg-white p-6 rounded-xl shadow-sm text-center">
                        <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">Rápido</h3>
                        <p class="text-gray-600">Entrega en menos de 30 minutos</p>
                    </div>
                    <div class="bg-white p-6 rounded-xl shadow-sm text-center">
                        <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">Seguro</h3>
                        <p class="text-gray-600">Pagós seguros y protegidos</p>
                    </div>
                    <div class="bg-white p-6 rounded-xl shadow-sm text-center">
                        <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">Soporte 24/7</h3>
                        <p class="text-gray-600">Atención al cliente siempre disponible</p>
                    </div>
                </div>
            </div>
        </main>

        <footer class="bg-gray-800 text-white py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <p class="text-gray-400">© 2026 Nikama Delivery. Todos los derechos reservados.</p>
            </div>
        </footer>
    </div>
</body>
</html>