<aside class="w-64 bg-slate-950 text-white h-screen hidden md:flex flex-col flex-shrink-0 border-r border-slate-900">
    <div class="p-6 border-b border-slate-900 flex items-center justify-between">
        <a href="/" class="text-2xl font-black font-['Outfit'] text-luffy-straw">Nikama Driver</a>
    </div>
    <div class="flex-1 flex flex-col justify-between p-4">
        <nav class="space-y-1">
            <a href="{{ route('driver.dashboard') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-2xl {{ request()->routeIs('driver.dashboard') ? 'bg-luffy-straw text-slate-900 font-extrabold shadow-lg shadow-luffy-straw/10' : 'text-slate-400 hover:bg-slate-900 hover:text-white font-medium' }} transition-all">
                <span class="text-lg">📊</span>
                <span>Panel Principal</span>
            </a>
            <a href="{{ route('driver.history') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-2xl {{ request()->routeIs('driver.history') ? 'bg-luffy-straw text-slate-900 font-extrabold shadow-lg shadow-luffy-straw/10' : 'text-slate-400 hover:bg-slate-900 hover:text-white font-medium' }} transition-all">
                <span class="text-lg">📋</span>
                <span>Historial de Viajes</span>
            </a>
        </nav>
        
        <div>
            <div class="p-3 bg-slate-900/60 rounded-2xl border border-slate-900 mb-4 text-center">
                <span class="text-[10px] text-slate-500 font-bold uppercase tracking-wider block">Calificación</span>
                <span class="text-base font-black text-amber-500 block mt-1">⭐ {{ number_format(auth()->user()->driverProfile->rating_average ?? 5.0, 2) }}</span>
            </div>
            
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-2 py-3 rounded-2xl bg-rose-500/10 hover:bg-rose-500 text-rose-500 hover:text-white font-bold text-xs transition-all duration-300">
                    ✕ Cerrar Sesión
                </button>
            </form>
        </div>
    </div>
</aside>
