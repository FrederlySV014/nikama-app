<svg {{ $attributes->merge(['class' => 'w-10 h-10']) }} viewBox="0 0 120 100" fill="none" xmlns="http://www.w3.org/2000/svg" class="animate-[pulse-slow_3s_ease-in-out_infinite]">
  <!-- Rastro de velocidad / destello -->
  <path d="M 5 50 L 25 50" stroke="#fcd34d" stroke-width="3" stroke-linecap="round" class="opacity-60"/>
  <path d="M 2 35 L 18 35" stroke="#fcd34d" stroke-width="2" stroke-linecap="round" class="opacity-40"/>
  <path d="M 2 65 L 18 65" stroke="#fcd34d" stroke-width="2" stroke-linecap="round" class="opacity-40"/>
  
  <!-- Brillo efecto -->
  <ellipse cx="30" cy="50" rx="15" ry="25" fill="url(#brillo)" class="opacity-30"/>
  
  <!-- Sombra base -->
  <ellipse cx="60" cy="82" rx="42" ry="8" fill="currentColor" class="opacity-20 dark:opacity-40"/>
  
  <!-- Ala del sombrero -->
  <ellipse cx="60" cy="78" rx="45" ry="12" fill="#fcd34d" stroke="#b45309" stroke-width="2"/>
  
  <!-- Corona del sombrero -->
  <path d="M 35 72 C 35 35, 45 28, 60 28 C 75 28, 85 35, 85 72 Z" fill="#fcd34d" stroke="#b45309" stroke-width="2"/>
  
  <!-- Brillo en la corona -->
  <path d="M 40 45 Q 50 35 60 38" stroke="white" stroke-width="2" stroke-linecap="round" class="opacity-50"/>
  
  <!-- Cinta roja -->
  <path d="M 36 66 Q 60 74 84 66 L 85 72 Q 60 80 35 72 Z" fill="#ef4444"/>
  
  <!-- Brillo en la cinta -->
  <path d="M 40 68 Q 50 72 60 70" stroke="white" stroke-width="1.5" stroke-linecap="round" class="opacity-40"/>
  
  <!-- Gradiente de brillo -->
  <defs>
    <linearGradient id="brillo" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#fcd34d" stop-opacity="0.8"/>
      <stop offset="100%" stop-color="#fcd34d" stop-opacity="0"/>
    </linearGradient>
  </defs>
</svg>
