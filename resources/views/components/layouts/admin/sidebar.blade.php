<aside class="w-64 bg-slate-950 text-white h-screen hidden md:block flex-shrink-0">
    <div class="p-6 border-b border-slate-800">
        <a href="/" class="text-2xl font-extrabold font-['Outfit'] text-luffy-red">Nikama Admin</a>
    </div>
    <nav class="mt-6 px-4 space-y-1">
        <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2.5 rounded-xl {{ request()->routeIs('admin.dashboard') ? 'bg-slate-900 text-white font-bold' : 'text-slate-400 hover:bg-slate-900 hover:text-white font-medium' }} transition">Dashboard</a>
        <a href="{{ route('admin.applications.index') }}" class="block px-4 py-2.5 rounded-xl {{ request()->routeIs('admin.applications.*') ? 'bg-slate-900 text-white font-bold' : 'text-slate-400 hover:bg-slate-900 hover:text-white font-medium' }} transition">Aprobaciones</a>
        <a href="{{ route('admin.categories.index') }}" class="block px-4 py-2.5 rounded-xl {{ request()->routeIs('admin.categories.*') ? 'bg-slate-900 text-white font-bold' : 'text-slate-400 hover:bg-slate-900 hover:text-white font-medium' }} transition">Categorías</a>
        <a href="{{ route('admin.products.index') }}" class="block px-4 py-2.5 rounded-xl {{ request()->routeIs('admin.products.*') ? 'bg-slate-900 text-white font-bold' : 'text-slate-400 hover:bg-slate-900 hover:text-white font-medium' }} transition">Productos Oficiales</a>
        <a href="{{ route('admin.settings.payments.edit') }}" class="block px-4 py-2.5 rounded-xl {{ request()->routeIs('admin.settings.payments.*') ? 'bg-slate-900 text-white font-bold' : 'text-slate-400 hover:bg-slate-900 hover:text-white font-medium' }} transition">Métodos de Pago</a>
        <a href="{{ route('admin.settings.districts.edit') }}" class="block px-4 py-2.5 rounded-xl {{ request()->routeIs('admin.settings.districts.*') ? 'bg-slate-900 text-white font-bold' : 'text-slate-400 hover:bg-slate-900 hover:text-white font-medium' }} transition">Distritos Cobertura</a>
        <a href="#" class="block px-4 py-2.5 rounded-xl text-slate-400 hover:bg-slate-900 hover:text-white font-medium transition">Usuarios</a>
        <form method="POST" action="{{ route('logout') }}" class="block mt-10">
            @csrf
            <button type="submit" class="w-full text-left px-4 py-2.5 rounded-xl text-rose-400 hover:bg-rose-500/10 font-bold transition">Cerrar Sesión</button>
        </form>
    </nav>
</aside>
