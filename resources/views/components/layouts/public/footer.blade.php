<!-- Footer -->
<footer class="bg-white dark:bg-slate-900 border-t border-gray-200 dark:border-slate-800 mt-auto transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-12">
            <!-- Columna de Marca -->
            <div class="md:col-span-2 space-y-4">
                <div class="flex items-center space-x-3">
                    <x-logo class="w-10 h-10 text-luffy-red" />
                    <span class="text-xl font-extrabold font-['Outfit'] bg-gradient-to-r from-luffy-red to-luffy-straw-dark dark:to-luffy-straw bg-clip-text text-transparent">Nikama Delivery</span>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400 max-w-sm">
                    El rey de las entregas rápidas y seguras. Conectando tus antojos favoritos con los mejores restaurantes y repartidores de tu zona.
                </p>
                <!-- Redes Sociales Simuladas -->
                <div class="flex space-x-4 pt-2">
                    <a href="#" class="text-gray-400 hover:text-luffy-red transition-colors" aria-label="Facebook">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"/></svg>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-luffy-red transition-colors" aria-label="Instagram">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.051.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                    </a>
                </div>
            </div>

            <!-- Columna Navegación -->
            <div>
                <h4 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-4">Explorar</h4>
                <ul class="space-y-2.5 text-sm text-gray-500 dark:text-gray-400">
                    <li><a href="{{ route('public.welcome') }}" class="hover:text-luffy-red transition-colors">Inicio</a></li>
                    <li><a href="{{ route('public.about-us') }}" class="hover:text-luffy-red transition-colors">Nosotros</a></li>
                </ul>
            </div>

            <!-- Columna Asociados -->
            <div>
                <h4 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-4">Colabora</h4>
                <ul class="space-y-2.5 text-sm text-gray-500 dark:text-gray-400">
                    <li><a href="{{ route('seller.register') }}" class="hover:text-luffy-straw-dark dark:hover:text-luffy-straw transition-colors">Vende en Nikama</a></li>
                    <li><a href="{{ route('driver.register') }}" class="hover:text-slate-700 dark:hover:text-slate-300 transition-colors">Sé un Repartidor</a></li>
                </ul>
            </div>
        </div>

        <!-- Barra Inferior -->
        <div class="pt-8 border-t border-gray-200 dark:border-slate-800 flex flex-col md:flex-row items-center justify-between gap-4 text-xs text-gray-500 dark:text-gray-400">
            <p>&copy; {{ date('Y') }} Nikama Delivery. Todos los derechos reservados.</p>
            
            <div class="flex items-center space-x-6">
                <a href="#" class="hover:text-gray-800 dark:hover:text-white transition-colors">Términos</a>
                <a href="#" class="hover:text-gray-800 dark:hover:text-white transition-colors">Privacidad</a>
                <!-- Enlace discreto para administración -->
                <a href="{{ route('admin.login') }}" class="opacity-30 hover:opacity-100 hover:text-luffy-red transition-colors font-bold uppercase tracking-widest text-[9px] flex items-center gap-1.5" aria-label="Portal de administración">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    Panel
                </a>
            </div>
        </div>
    </div>
</footer>
