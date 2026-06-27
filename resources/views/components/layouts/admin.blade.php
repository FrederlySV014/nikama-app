<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Super Admin - Nikama' }}</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700|outfit:400,600,700,800&display=swap" rel="stylesheet" />
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>
<body class="antialiased font-sans bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-white transition-colors duration-300" 
      x-data="{ 
          sidebarOpen: false, 
          notificationsOpen: false, 
          notifications: { sellers: [], drivers: [], pending_sellers_count: 0, pending_drivers_count: 0 }, 
          theme: document.documentElement.classList.contains('dark') ? 'dark' : 'light', 
          toggleTheme() { 
              this.theme = this.theme === 'dark' ? 'light' : 'dark'; 
              localStorage.theme = this.theme; 
              if (this.theme === 'dark') { 
                  document.documentElement.classList.add('dark'); 
              } else { 
                  document.documentElement.classList.remove('dark'); 
              } 
          } 
      }"
      @update-notifications.window="notifications = $event.detail">
    <div class="flex h-screen overflow-hidden">
        @include('components.layouts.admin.sidebar')

        <div class="flex flex-col flex-1 overflow-hidden">
            @include('components.layouts.admin.topbar')

            <main class="flex-1 overflow-x-hidden overflow-y-auto p-4 sm:p-8">
                {{ $slot }}
            </main>
        </div>
    </div>

    <!-- Admin Notification Toast Container -->
    <div id="admin-notification-toast-container" class="fixed bottom-5 right-5 z-[9999] flex flex-col gap-3 max-w-sm w-full pointer-events-none px-4 sm:px-0"></div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            let seenSellers = new Set();
            let seenDrivers = new Set();
            const checkUrl = "{{ route('admin.dashboard.pending-applications') }}";
            const applicationsSellersUrl = "{{ route('admin.applications.index') }}?tab=sellers";
            const applicationsDriversUrl = "{{ route('admin.applications.index') }}?tab=drivers";

            // --- Audio: Mixkit CDN + AudioContext Fallback ---
            const MIXKIT_ALARM_URL = 'https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3';
            let mixkitAudio = null;

            function loadMixkitAudio() {
                try {
                    mixkitAudio = new Audio(MIXKIT_ALARM_URL);
                    mixkitAudio.volume = 0.7;
                    mixkitAudio.load();
                } catch (e) {
                    mixkitAudio = null;
                }
            }

            function playAlarmSound() {
                const REPEAT = 3;
                const GAP_MS = 2500;

                function playSingleMixkit(attempt) {
                    if (!mixkitAudio) { playSingleFallback(attempt); return; }
                    mixkitAudio.currentTime = 0;
                    mixkitAudio.play().catch(() => playSingleFallback(attempt));
                    if (attempt < REPEAT) {
                        setTimeout(() => playSingleMixkit(attempt + 1), GAP_MS);
                    }
                }

                function playSingleFallback(attempt) {
                    playFallbackAlarm();
                    if (attempt < REPEAT) {
                        setTimeout(() => playSingleFallback(attempt + 1), GAP_MS);
                    }
                }

                if (mixkitAudio) {
                    playSingleMixkit(1);
                } else {
                    playSingleFallback(1);
                }
            }

            function playFallbackAlarm() {
                try {
                    const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                    let time = audioCtx.currentTime;

                    // Urgent 3-tone alarm pattern
                    const tones = [880, 1100, 880, 1100, 880];
                    tones.forEach((freq, i) => {
                        const osc = audioCtx.createOscillator();
                        const gain = audioCtx.createGain();

                        osc.type = 'square';
                        osc.frequency.setValueAtTime(freq, time);

                        gain.gain.setValueAtTime(0, time);
                        gain.gain.linearRampToValueAtTime(0.15, time + 0.02);
                        gain.gain.exponentialRampToValueAtTime(0.001, time + 0.28);

                        osc.connect(gain);
                        gain.connect(audioCtx.destination);
                        osc.start(time);
                        osc.stop(time + 0.3);
                        time += 0.35;
                    });
                } catch (e) {
                    console.warn('Admin alarm AudioContext failed:', e);
                }
            }

            function updateCounts(data) {
                const pendingSellersSpan = document.getElementById('admin-pending-sellers-count');
                const pendingDriversSpan = document.getElementById('admin-pending-drivers-count');
                const activeSellersSpan = document.getElementById('admin-active-sellers-count');
                const activeDriversSpan = document.getElementById('admin-active-drivers-count');

                if (pendingSellersSpan) pendingSellersSpan.textContent = data.pending_sellers_count;
                if (pendingDriversSpan) pendingDriversSpan.textContent = data.pending_drivers_count;
                if (activeSellersSpan) activeSellersSpan.textContent = data.active_sellers_count;
                if (activeDriversSpan) activeDriversSpan.textContent = data.active_drivers_count;
            }

            function showSellerToast(seller) {
                const container = document.getElementById('admin-notification-toast-container');
                if (!container) return;

                const toast = document.createElement('div');
                toast.className = 'pointer-events-auto w-full bg-white/95 dark:bg-slate-800/95 backdrop-blur-md border border-luffy-red/30 rounded-3xl shadow-2xl p-4 transform translate-y-4 opacity-0 transition-all duration-300 flex items-start gap-3';

                toast.innerHTML = `
                    <div class="relative flex-shrink-0 w-11 h-11 bg-luffy-red/10 rounded-2xl flex items-center justify-center text-luffy-red">
                        <span class="absolute -top-1 -right-1 flex h-3 w-3">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-rose-500"></span>
                        </span>
                        <svg class="w-5 h-5 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                       </svg>
                    </div>
                    <div class="flex-1 min-w-0 space-y-1">
                        <h4 class="font-extrabold text-sm text-slate-800 dark:text-white font-['Outfit']">🚨 ¡Nueva Solicitud de Negocio!</h4>
                        <p class="text-[11px] text-slate-600 dark:text-slate-400 font-semibold truncate">
                            <span class="text-luffy-red font-black">${seller.business_name}</span>
                        </p>
                        <p class="text-[11px] text-slate-550 dark:text-slate-400 truncate">Propietario: ${seller.owner_name}</p>
                        <div class="pt-1.5">
                            <a href="${applicationsSellersUrl}"
                               class="inline-flex items-center gap-1 bg-luffy-red hover:bg-luffy-red/90 text-white text-[11px] font-black uppercase tracking-wider py-1.5 px-3 rounded-xl transition cursor-pointer">
                                Ver Solicitud ➜
                            </a>
                        </div>
                    </div>
                    <button class="text-slate-400 hover:text-slate-650 dark:hover:text-white transition flex-shrink-0" onclick="this.parentElement.remove()">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                `;

                container.appendChild(toast);
                setTimeout(() => toast.classList.remove('translate-y-4', 'opacity-0'), 10);
                setTimeout(() => {
                    if (toast && toast.parentElement) {
                        toast.classList.add('opacity-0', 'translate-y-4');
                        setTimeout(() => toast.remove(), 300);
                    }
                }, 15000);
            }

            function showDriverToast(driver) {
                const container = document.getElementById('admin-notification-toast-container');
                if (!container) return;

                const toast = document.createElement('div');
                toast.className = 'pointer-events-auto w-full bg-white/95 dark:bg-slate-800/95 backdrop-blur-md border border-rose-400/30 rounded-3xl shadow-2xl p-4 transform translate-y-4 opacity-0 transition-all duration-300 flex items-start gap-3';

                toast.innerHTML = `
                    <div class="relative flex-shrink-0 w-11 h-11 bg-rose-500/10 rounded-2xl flex items-center justify-center text-rose-500">
                        <span class="absolute -top-1 -right-1 flex h-3 w-3">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-rose-500"></span>
                        </span>
                        <svg class="w-5 h-5 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 009 11v-1.5M12 11c2.907-3.183 6-3.045 6-3V7a1 1 0 00-1-1h-2.28A2 2 0 0010 4.28V4a1 1 0 00-1-1H7a1 1 0 00-1 1v.28A2 2 0 004.28 6H2a1 1 0 00-1 1v.005c0 .052.289 3.015 6 3L7 11.5M12 11c0-3.517 1.009-6.799 2.753-9.571m-3.44 2.04l-.054.09A13.916 13.916 0 0015 11v1.5M20 18l-8-8 8 8z"></path>
                        </svg>
                   </div>
                   <div class="flex-1 min-w-0 space-y-1">
                       <h4 class="font-extrabold text-sm text-slate-800 dark:text-white font-['Outfit']">🚲 ¡Nueva Solicitud de Repartidor!</h4>
                       <p class="text-[11px] text-slate-600 dark:text-slate-400 font-semibold truncate">
                           El repartidor <span class="text-rose-500 font-black">${driver.name}</span> ha enviado una solicitud.
                       </p>
                       <div class="pt-1.5">
                           <a href="${applicationsDriversUrl}"
                              class="inline-flex items-center gap-1 bg-rose-500 hover:bg-rose-600 text-white text-[11px] font-black uppercase tracking-wider py-1.5 px-3 rounded-xl transition cursor-pointer">
                               Ver Solicitud ➜
                           </a>
                       </div>
                   </div>
                   <button class="text-slate-400 hover:text-slate-650 dark:hover:text-white transition flex-shrink-0" onclick="this.parentElement.remove()">
                       <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                   </button>
                `;

                container.appendChild(toast);
                setTimeout(() => toast.classList.remove('translate-y-4', 'opacity-0'), 10);
                setTimeout(() => {
                    if (toast && toast.parentElement) {
                        toast.classList.add('opacity-0', 'translate-y-4');
                        setTimeout(() => toast.remove(), 300);
                    }
                }, 15000);
            }

            function pollPendingApplications() {
                fetch(checkUrl)
                    .then(res => res.json())
                    .then(data => {
                        let shouldPlay = false;

                        // Check new sellers
                        data.sellers.forEach(seller => {
                            if (!seenSellers.has(seller.id)) {
                                seenSellers.add(seller.id);
                                showSellerToast(seller);
                                shouldPlay = true;
                            }
                        });

                        // Check new drivers
                        data.drivers.forEach(driver => {
                            if (!seenDrivers.has(driver.id)) {
                                seenDrivers.add(driver.id);
                                showDriverToast(driver);
                                shouldPlay = true;
                            }
                        });

                        if (shouldPlay) {
                            playAlarmSound();
                        }

                        updateCounts(data);

                        // Dispatch event for Alpine topbar component
                        const event = new CustomEvent('update-notifications', { detail: data });
                        window.dispatchEvent(event);
                    })
                    .catch(err => console.error('Admin pending applications polling error:', err));
            }

            loadMixkitAudio();

            // Initial load to establish baseline
            fetch(checkUrl)
                .then(res => res.json())
                .then(data => {
                    data.sellers.forEach(s => seenSellers.add(s.id));
                    data.drivers.forEach(d => seenDrivers.add(d.id));
                    updateCounts(data);

                    // Dispatch event for Alpine topbar component
                    const event = new CustomEvent('update-notifications', { detail: data });
                    window.dispatchEvent(event);

                    // Start polling every 6 seconds
                    setInterval(pollPendingApplications, 6000);
                })
                .catch(err => console.error('Admin initial load error:', err));
        });
    </script>
</body>
</html>
