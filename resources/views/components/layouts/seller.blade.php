<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Seller Dashboard - Nikama' }}</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700|outfit:400,600,700&display=swap" rel="stylesheet" />
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
<body class="antialiased font-sans bg-gray-50 dark:bg-slate-900 text-gray-900 dark:text-white transition-colors duration-300" x-data="{ sidebarOpen: false }">
    <div class="flex h-screen overflow-hidden">
        @include('components.layouts.seller.sidebar')

        <div class="flex flex-col flex-1 overflow-hidden">
            @include('components.layouts.seller.topbar')

            <main class="flex-1 overflow-x-hidden overflow-y-auto p-4 sm:p-8">
                {{ $slot }}
            </main>
        </div>
    </div>

    <!-- Notification Toast Container -->
    <div id="seller-notification-toast-container" class="fixed bottom-5 right-5 z-[9999] flex flex-col gap-3 max-w-sm w-full pointer-events-none px-4 sm:px-0"></div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            let lastPendingCount = null;
            const checkUrl = "{{ route('seller.orders.pending-count') }}";
            const ordersUrl = "{{ route('seller.orders.index') }}";

            // --- Audio: Mixkit CDN (free) + AudioContext fallback ---
            const MIXKIT_URL = 'https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3';
            let mixkitAudio = null;

            function loadMixkitAudio() {
                try {
                    mixkitAudio = new Audio(MIXKIT_URL);
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

                    // Alternating warning beep alarm
                    for (let i = 0; i < 4; i++) {
                        const osc = audioCtx.createOscillator();
                        const gain = audioCtx.createGain();

                        osc.type = 'sawtooth';
                        osc.frequency.setValueAtTime(880, time);
                        osc.frequency.setValueAtTime(660, time + 0.15);

                        gain.gain.setValueAtTime(0.12, time);
                        gain.gain.exponentialRampToValueAtTime(0.001, time + 0.3);

                        osc.connect(gain);
                        gain.connect(audioCtx.destination);

                        osc.start(time);
                        osc.stop(time + 0.35);

                        time += 0.4;
                    }
                } catch (e) {
                    console.warn('AudioContext alarm failed:', e);
                }
            }

            function updateTopbarBadge(count) {
                const badge = document.getElementById('seller-topbar-notification-badge');
                if (badge) {
                    if (count > 0) {
                        badge.textContent = count;
                        badge.style.display = 'flex';
                    } else {
                        badge.style.display = 'none';
                    }
                }
            }

            function showToast(count) {
                const container = document.getElementById('seller-notification-toast-container');
                if (!container) return;

                const toast = document.createElement('div');
                toast.className = "pointer-events-auto w-full bg-white/95 dark:bg-slate-800/95 backdrop-blur-md border border-luffy-straw/30 rounded-3xl shadow-xl p-4 transform translate-y-2 opacity-0 transition-all duration-300 flex items-start gap-3";
                
                toast.innerHTML = `
                    <div class="relative flex-shrink-0 w-10 h-10 bg-luffy-straw/10 rounded-2xl flex items-center justify-center text-luffy-straw">
                        <span class="absolute top-1 right-1 flex h-2.5 w-2.5">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-rose-500"></span>
                        </span>
                        <svg class="w-5 h-5 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                    </div>
                    <div class="flex-1 space-y-1">
                        <h4 class="font-extrabold text-sm text-slate-800 dark:text-white font-['Outfit']">¡Nuevo Pedido Recibido!</h4>
                        <p class="text-xs text-slate-550 dark:text-slate-400 font-medium">Tienes un total de <span class="text-luffy-straw font-black">${count}</span> pedidos pendientes de atención.</p>
                        <div class="pt-2">
                            <a href="${ordersUrl}" class="inline-flex items-center gap-1 bg-luffy-straw hover:bg-luffy-straw/90 text-white text-[11px] font-black uppercase tracking-wider py-1.5 px-3 rounded-xl transition cursor-pointer">
                                Ver Pedidos ➜
                            </a>
                        </div>
                    </div>
                    <button class="text-slate-400 hover:text-slate-650 dark:hover:text-white transition" onclick="this.parentElement.remove()">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                `;

                container.appendChild(toast);
                
                // Animate entry
                setTimeout(() => {
                    toast.classList.remove('translate-y-2', 'opacity-0');
                }, 10);

                // Auto remove after 10 seconds
                setTimeout(() => {
                    if (toast && toast.parentElement) {
                        toast.classList.add('opacity-0', 'translate-y-2');
                        setTimeout(() => toast.remove(), 300);
                    }
                }, 10000);
            }

            function pollPendingCount() {
                fetch(checkUrl)
                    .then(response => response.json())
                    .then(data => {
                        const count = data.count;
                        updateTopbarBadge(count);
                        if (lastPendingCount !== null && count > lastPendingCount) {
                            playAlarmSound();
                            showToast(count);
                        }
                        lastPendingCount = count;
                    })
                    .catch(err => console.error('Error polling pending counts:', err));
            }

            // Initial call to set lastPendingCount without firing notifications
            loadMixkitAudio();
            fetch(checkUrl)
                .then(response => response.json())
                .then(data => {
                    lastPendingCount = data.count;
                    updateTopbarBadge(data.count);
                    // Start periodic check
                    setInterval(pollPendingCount, 6000);
                })
                .catch(err => console.error('Error in initial pending counts fetch:', err));
        });
    </script>
</body>
</html>
