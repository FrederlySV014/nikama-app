<aside class="w-64 bg-slate-950 text-white h-screen hidden md:flex flex-col flex-shrink-0 border-r border-slate-900 shadow-xl transition-all duration-300">
    <div class="p-6 border-b border-slate-900/80 flex items-center justify-between">
        <a href="/" class="text-2xl font-extrabold font-['Outfit'] bg-gradient-to-r from-luffy-red to-rose-455 bg-clip-text text-transparent tracking-tight">
            Nikama Admin
        </a>
    </div>
    <nav class="flex-1 mt-4 px-4 space-y-5 overflow-y-auto pb-8 scrollbar-thin scrollbar-thumb-slate-800 scrollbar-track-transparent">
        <!-- Grupo: Gestión Principal -->
        <div>
            <span class="px-4 text-[9px] font-black text-slate-500 uppercase tracking-widest block mb-2">Gestión Principal</span>
            <div class="space-y-1">
                <a href="{{ route('admin.dashboard') }}" 
                   class="block px-4 py-2 rounded-xl transition duration-200 text-xs {{ request()->routeIs('admin.dashboard') ? 'bg-gradient-to-r from-luffy-red/20 to-slate-900/40 text-white font-bold border-l-4 border-luffy-red' : 'text-slate-400 hover:bg-slate-900 hover:text-white font-semibold' }}">
                    Dashboard
                </a>
                <a href="{{ route('admin.applications.index') }}" 
                   class="block px-4 py-2 rounded-xl transition duration-200 text-xs {{ request()->routeIs('admin.applications.*') ? 'bg-gradient-to-r from-luffy-red/20 to-slate-900/40 text-white font-bold border-l-4 border-luffy-red' : 'text-slate-400 hover:bg-slate-900 hover:text-white font-semibold' }}">
                    Aprobaciones
                </a>
                <a href="{{ route('admin.businesses.index') }}" 
                   class="block px-4 py-2 rounded-xl transition duration-200 text-xs {{ request()->routeIs('admin.businesses.*') ? 'bg-gradient-to-r from-luffy-red/20 to-slate-900/40 text-white font-bold border-l-4 border-luffy-red' : 'text-slate-400 hover:bg-slate-900 hover:text-white font-semibold' }}">
                    Negocios
                </a>
                <a href="{{ route('admin.categories.index') }}" 
                   class="block px-4 py-2 rounded-xl transition duration-200 text-xs {{ request()->routeIs('admin.categories.*') ? 'bg-gradient-to-r from-luffy-red/20 to-slate-900/40 text-white font-bold border-l-4 border-luffy-red' : 'text-slate-400 hover:bg-slate-900 hover:text-white font-semibold' }}">
                    Categorías
                </a>
                <a href="{{ route('admin.products.index') }}" 
                   class="block px-4 py-2 rounded-xl transition duration-200 text-xs {{ request()->routeIs('admin.products.*') ? 'bg-gradient-to-r from-luffy-red/20 to-slate-900/40 text-white font-bold border-l-4 border-luffy-red' : 'text-slate-400 hover:bg-slate-900 hover:text-white font-semibold' }}">
                    Productos Oficiales
                </a>
                <a href="{{ route('admin.users.index') }}" 
                   class="block px-4 py-2 rounded-xl transition duration-200 text-xs {{ request()->routeIs('admin.users.*') ? 'bg-gradient-to-r from-luffy-red/20 to-slate-900/40 text-white font-bold border-l-4 border-luffy-red' : 'text-slate-400 hover:bg-slate-900 hover:text-white font-semibold' }}">
                    Usuarios
                </a>
            </div>
        </div>

        <!-- Grupo: Módulo Financiero -->
        <div>
            <span class="px-4 text-[9px] font-black text-slate-500 uppercase tracking-widest block mb-2">Módulo Financiero</span>
            <div class="space-y-1">
                <a href="{{ route('admin.financial.payouts') }}" 
                   class="block px-4 py-2 rounded-xl transition duration-200 text-xs {{ request()->routeIs('admin.financial.payouts') ? 'bg-gradient-to-r from-luffy-red/20 to-slate-900/40 text-white font-bold border-l-4 border-luffy-red' : 'text-slate-400 hover:bg-slate-900 hover:text-white font-semibold' }}">
                    Liquidaciones
                </a>
                <a href="{{ route('admin.financial.commissions') }}" 
                   class="block px-4 py-2 rounded-xl transition duration-200 text-xs {{ request()->routeIs('admin.financial.commissions') ? 'bg-gradient-to-r from-luffy-red/20 to-slate-900/40 text-white font-bold border-l-4 border-luffy-red' : 'text-slate-400 hover:bg-slate-900 hover:text-white font-semibold' }}">
                    Comisiones
                </a>
                <a href="{{ route('admin.financial.transactions') }}" 
                   class="block px-4 py-2 rounded-xl transition duration-200 text-xs {{ request()->routeIs('admin.financial.transactions') ? 'bg-gradient-to-r from-luffy-red/20 to-slate-900/40 text-white font-bold border-l-4 border-luffy-red' : 'text-slate-400 hover:bg-slate-900 hover:text-white font-semibold' }}">
                    Transacciones
                </a>
            </div>
        </div>

        <!-- Grupo: Marketing y Promociones -->
        <div>
            <span class="px-4 text-[9px] font-black text-slate-500 uppercase tracking-widest block mb-2">Marketing</span>
            <div class="space-y-1">
                <a href="{{ route('admin.marketing.banners') }}" 
                   class="block px-4 py-2 rounded-xl transition duration-200 text-xs {{ request()->routeIs('admin.marketing.banners') ? 'bg-gradient-to-r from-luffy-red/20 to-slate-900/40 text-white font-bold border-l-4 border-luffy-red' : 'text-slate-400 hover:bg-slate-900 hover:text-white font-semibold' }}">
                    Banners
                </a>
                <a href="{{ route('admin.marketing.discounts') }}" 
                   class="block px-4 py-2 rounded-xl transition duration-200 text-xs {{ request()->routeIs('admin.marketing.discounts') ? 'bg-gradient-to-r from-luffy-red/20 to-slate-900/40 text-white font-bold border-l-4 border-luffy-red' : 'text-slate-400 hover:bg-slate-900 hover:text-white font-semibold' }}">
                    Cupones y Descuentos
                </a>
            </div>
        </div>

        <!-- Grupo: Configuración y Sistema -->
        <div>
            <span class="px-4 text-[9px] font-black text-slate-500 uppercase tracking-widest block mb-2">Configuración y Sistema</span>
            <div class="space-y-1">
                <a href="{{ route('admin.settings.payments.edit') }}" 
                   class="block px-4 py-2 rounded-xl transition duration-200 text-xs {{ request()->routeIs('admin.settings.payments.*') ? 'bg-gradient-to-r from-luffy-red/20 to-slate-900/40 text-white font-bold border-l-4 border-luffy-red' : 'text-slate-400 hover:bg-slate-900 hover:text-white font-semibold' }}">
                    Métodos de Pago
                </a>
                <a href="{{ route('admin.settings.districts.edit') }}" 
                   class="block px-4 py-2 rounded-xl transition duration-200 text-xs {{ request()->routeIs('admin.settings.districts.*') ? 'bg-gradient-to-r from-luffy-red/20 to-slate-900/40 text-white font-bold border-l-4 border-luffy-red' : 'text-slate-400 hover:bg-slate-900 hover:text-white font-semibold' }}">
                    Zonas de Cobertura
                </a>
                <a href="{{ route('admin.system.settings.edit') }}" 
                   class="block px-4 py-2 rounded-xl transition duration-200 text-xs {{ request()->routeIs('admin.system.settings.edit') ? 'bg-gradient-to-r from-luffy-red/20 to-slate-900/40 text-white font-bold border-l-4 border-luffy-red' : 'text-slate-400 hover:bg-slate-900 hover:text-white font-semibold' }}">
                    Ajustes Generales
                </a>
                <a href="{{ route('admin.system.audit-logs') }}" 
                   class="block px-4 py-2 rounded-xl transition duration-200 text-xs {{ request()->routeIs('admin.system.audit-logs') ? 'bg-gradient-to-r from-luffy-red/20 to-slate-900/40 text-white font-bold border-l-4 border-luffy-red' : 'text-slate-400 hover:bg-slate-900 hover:text-white font-semibold' }}">
                    Bitácora de Auditoría
                </a>
            </div>
        </div>

        <form method="POST" action="{{ route('logout') }}" class="block pt-4 border-t border-slate-900/60">
            @csrf
            <button type="submit" 
                    class="w-full text-left px-4 py-2 rounded-xl text-rose-455 hover:bg-rose-500/10 hover:text-rose-400 font-bold transition duration-200 cursor-pointer text-xs">
                Cerrar Sesión
            </button>
        </form>
    </nav>
</aside>
