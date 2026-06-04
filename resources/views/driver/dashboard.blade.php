<x-layouts.public>
    <x-slot:title>Driver Dashboard - Nikama</x-slot:title>

    <div class="max-w-4xl mx-auto px-4 py-12 space-y-6">
        <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 p-8 rounded-3xl text-white shadow-xl relative overflow-hidden">
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,_var(--tw-gradient-stops))] from-luffy-straw/10 via-transparent to-transparent pointer-events-none"></div>
            <h2 class="text-3xl font-extrabold font-['Outfit'] text-white">¡Hola, {{ auth()->user()->first_name }}!</h2>
            <p class="text-white/60 mt-2 font-medium">Bienvenido a tu panel de conductor de reparto. La mayor parte de tus entregas las gestionarás desde la app móvil, pero desde aquí puedes ver un resumen básico de tu perfil.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white dark:bg-slate-900 p-8 rounded-3xl border border-gray-100 dark:border-slate-800 shadow-xl shadow-gray-200/50 dark:shadow-none space-y-4">
                <h3 class="text-xl font-bold font-['Outfit'] border-b border-gray-100 dark:border-slate-800 pb-2">Información del Repartidor</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between"><span class="text-slate-500 font-medium">Vehículo:</span> <span class="font-bold uppercase">{{ auth()->user()->driverProfile->vehicle_type }}</span></div>
                    <div class="flex justify-between"><span class="text-slate-500 font-medium">Placa:</span> <span class="font-bold uppercase">{{ auth()->user()->driverProfile->license_plate ?? 'N/A' }}</span></div>
                    <div class="flex justify-between"><span class="text-slate-500 font-medium">Licencia:</span> <span class="font-bold">{{ auth()->user()->driverProfile->license_number ?? 'N/A' }}</span></div>
                    <div class="flex justify-between"><span class="text-slate-500 font-medium">Calificación:</span> <span class="font-bold text-amber-500">⭐ {{ number_format(auth()->user()->driverProfile->rating_average, 2) }}</span></div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 p-8 rounded-3xl border border-gray-100 dark:border-slate-800 shadow-xl shadow-gray-200/50 dark:shadow-none flex flex-col justify-between">
                <div>
                    <h3 class="text-xl font-bold font-['Outfit'] border-b border-gray-100 dark:border-slate-800 pb-2 mb-4">Estadísticas de Entrega</h3>
                    <div class="text-center py-6">
                        <span class="text-slate-400 text-sm font-bold uppercase tracking-wider block">Entregas Completadas</span>
                        <span class="text-5xl font-extrabold font-['Outfit'] text-slate-800 dark:text-white block mt-2">{{ auth()->user()->driverProfile->total_deliveries }}</span>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full py-3 rounded-xl border border-gray-200 dark:border-slate-800 text-rose-500 font-bold hover:bg-rose-50 dark:hover:bg-rose-950/20 transition cursor-pointer">
                        Cerrar Sesión
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-layouts.public>
