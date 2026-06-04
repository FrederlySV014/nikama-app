<aside class="w-64 bg-slate-900 text-white h-screen hidden md:block flex-shrink-0">
    <div class="p-6">
        <a href="/" class="text-2xl font-bold font-['Outfit'] text-luffy-straw">Nikama Seller</a>
    </div>
    <nav class="mt-6 px-4 space-y-1">
        <a href="{{ route('seller.dashboard') }}" class="block px-4 py-2.5 rounded-xl bg-slate-800 text-white font-medium">Dashboard</a>
        <a href="#" class="block px-4 py-2.5 rounded-xl text-slate-300 hover:bg-slate-800 hover:text-white font-medium transition">Productos</a>
        <a href="#" class="block px-4 py-2.5 rounded-xl text-slate-300 hover:bg-slate-800 hover:text-white font-medium transition">Pedidos</a>
        <form method="POST" action="{{ route('logout') }}" class="block mt-10">
            @csrf
            <button type="submit" class="w-full text-left px-4 py-2.5 rounded-xl text-rose-400 hover:bg-rose-500/10 font-bold transition">Cerrar Sesión</button>
        </form>
    </nav>
</aside>
