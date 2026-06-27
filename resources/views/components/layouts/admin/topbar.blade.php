<header class="bg-white dark:bg-slate-800 border-b border-slate-200/60 dark:border-slate-700/60 h-16 flex items-center justify-between px-6 transition-colors duration-300">
    <div class="flex items-center gap-4">
        <button class="md:hidden text-slate-600 dark:text-slate-350 hover:text-slate-900 dark:hover:text-white transition" @click="sidebarOpen = !sidebarOpen">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
        </button>
        <h1 class="text-lg font-extrabold font-['Outfit'] bg-gradient-to-r from-slate-800 to-slate-500 dark:from-white dark:to-slate-350 bg-clip-text text-transparent">
            Panel de Administración
        </h1>
    </div>
    
    <div class="flex items-center gap-4">
        <!-- Theme Toggle Button -->
        <button @click="toggleTheme()" 
                class="w-10 h-10 flex items-center justify-center rounded-2xl bg-slate-50 hover:bg-slate-100 dark:bg-slate-750 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 transition-all duration-300 border border-slate-150/40 dark:border-slate-650/30 cursor-pointer"
                title="Cambiar tema">
            <!-- Moon Icon (for Light Mode, switches to Dark) -->
            <svg x-show="theme === 'light'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
            </svg>
            <!-- Sun Icon (for Dark Mode, switches to Light) -->
            <svg x-show="theme === 'dark'" style="display: none;" class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m0-12.728l.707.707m12.728 12.728l.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z"></path>
            </svg>
        </button>

        <!-- Notification Bell Dropdown Container -->
        <div class="relative" x-data="{ localOpen: false }" @click.away="localOpen = false">
            <button @click="localOpen = !localOpen" 
                    class="w-10 h-10 flex items-center justify-center rounded-2xl bg-slate-50 hover:bg-slate-100 dark:bg-slate-750 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 transition-all duration-300 border border-slate-150/40 dark:border-slate-650/30 relative cursor-pointer"
                    title="Notificaciones">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
                
                <!-- Pulsing Badge -->
                <span x-show="notifications.pending_sellers_count + notifications.pending_drivers_count > 0"
                      style="display: none;"
                      class="absolute -top-1 -right-1 flex h-4 w-4">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-4 w-4 bg-rose-500 items-center justify-center text-[9px] font-black text-white" 
                          x-text="notifications.pending_sellers_count + notifications.pending_drivers_count"></span>
                </span>
            </button>

            <!-- Dropdown Panel -->
            <div x-show="localOpen"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                 class="absolute right-0 mt-3 w-80 bg-white dark:bg-slate-800/98 border border-slate-200/60 dark:border-slate-700/60 rounded-3xl shadow-2xl z-[9999] overflow-hidden"
                 style="display: none;">
                 
                 <!-- Header -->
                 <div class="p-4 border-b border-slate-100 dark:border-slate-750 flex justify-between items-center bg-slate-50/50 dark:bg-slate-900/30">
                     <h3 class="font-extrabold text-sm text-slate-800 dark:text-white font-['Outfit']">Solicitudes Pendientes</h3>
                     <span class="text-[10px] bg-rose-500/10 text-rose-500 dark:bg-rose-500/20 dark:text-rose-400 px-2 py-0.5 rounded-full font-black uppercase tracking-wider"
                           x-text="`${notifications.pending_sellers_count + notifications.pending_drivers_count} nuevas`"
                           x-show="notifications.pending_sellers_count + notifications.pending_drivers_count > 0"></span>
                 </div>

                 <!-- Scrollable items area -->
                 <div class="max-h-64 overflow-y-auto divide-y divide-slate-100 dark:divide-slate-750">
                     
                     <!-- Empty state -->
                     <div class="p-8 text-center text-slate-400 dark:text-slate-500"
                          x-show="notifications.pending_sellers_count + notifications.pending_drivers_count === 0">
                          <svg class="w-10 h-10 mx-auto text-slate-300 dark:text-slate-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                          </svg>
                          <p class="text-xs font-bold font-['Outfit'] text-slate-600 dark:text-slate-400">¡Todo al día!</p>
                          <p class="text-[10px] mt-0.5 font-medium">No hay solicitudes por revisar.</p>
                     </div>

                     <!-- Sellers list -->
                     <template x-for="seller in notifications.sellers" :key="seller.id">
                         <a :href="`{{ route('admin.applications.index') }}?tab=sellers`" 
                            class="flex items-start gap-3 p-3.5 hover:bg-slate-50 dark:hover:bg-slate-700/30 transition">
                             <div class="flex-shrink-0 w-9 h-9 bg-luffy-red/10 text-luffy-red rounded-xl flex items-center justify-center">
                                 <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                 </svg>
                             </div>
                             <div class="flex-1 min-w-0">
                                 <p class="text-xs font-extrabold text-slate-800 dark:text-slate-100 truncate" x-text="seller.business_name"></p>
                                 <p class="text-[10px] text-slate-500 dark:text-slate-400 truncate" x-text="`Propietario: ${seller.owner_name}`"></p>
                                 <span class="text-[9px] font-black uppercase text-luffy-red tracking-wider block mt-1">Negocio ➜</span>
                             </div>
                         </a>
                     </template>

                     <!-- Drivers list -->
                     <template x-for="driver in notifications.drivers" :key="driver.id">
                         <a :href="`{{ route('admin.applications.index') }}?tab=drivers`" 
                            class="flex items-start gap-3 p-3.5 hover:bg-slate-50 dark:hover:bg-slate-700/30 transition">
                             <div class="flex-shrink-0 w-9 h-9 bg-rose-500/10 text-rose-500 rounded-xl flex items-center justify-center">
                                 <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 009 11v-1.5M12 11c2.907-3.183 6-3.045 6-3V7a1 1 0 00-1-1h-2.28A2 2 0 0010 4.28V4a1 1 0 00-1-1H7a1 1 0 00-1 1v.28A2 2 0 004.28 6H2a1 1 0 00-1 1v.005c0 .052.289 3.015 6 3L7 11.5M12 11c0-3.517 1.009-6.799 2.753-9.571m-3.44 2.04l-.054.09A13.916 13.916 0 0015 11v1.5M20 18l-8-8 8 8z"></path>
                                 </svg>
                             </div>
                             <div class="flex-1 min-w-0">
                                 <p class="text-xs font-extrabold text-slate-800 dark:text-slate-100 truncate" x-text="driver.name"></p>
                                 <p class="text-[10px] text-slate-500 dark:text-slate-400 truncate">Nueva solicitud de repartidor</p>
                                 <span class="text-[9px] font-black uppercase text-rose-500 tracking-wider block mt-1">Repartidor ➜</span>
                             </div>
                         </a>
                     </template>

                 </div>

                 <!-- Footer link -->
                 <div class="p-3 bg-slate-50/50 dark:bg-slate-900/30 border-t border-slate-100 dark:border-slate-750 text-center">
                     <a href="{{ route('admin.applications.index') }}" 
                        class="text-[10px] font-extrabold uppercase text-slate-600 hover:text-slate-800 dark:text-slate-300 dark:hover:text-white transition tracking-wider">
                         Ver Todo en Aprobaciones ➜
                     </a>
                 </div>
            </div>
        </div>

        <div class="h-6 w-px bg-slate-200 dark:bg-slate-700"></div>

        <!-- Profile Details -->
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 bg-gradient-to-tr from-luffy-red to-rose-500 rounded-xl flex items-center justify-center text-white font-extrabold text-sm shadow-md font-['Outfit']">
                {{ substr(auth()->user()->first_name, 0, 1) }}
            </div>
            <span class="text-sm font-bold text-slate-700 dark:text-slate-200 hidden sm:inline">
                {{ auth()->user()->first_name }} 
                <span class="text-[10px] font-black uppercase bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400 px-1.5 py-0.5 rounded ml-1 tracking-wider">Superadmin</span>
            </span>
        </div>
    </div>
</header>
