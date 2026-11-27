@props([
    'size' => 'md', // sm, md, lg, xl
    'showText' => false,
    'textColor' => 'text-white'
])

@php
    $sizeClasses = match($size) {
        'xs' => 'w-6 h-6',
        'sm' => 'w-8 h-8',
        'md' => 'w-10 h-10',
        'lg' => 'w-14 h-14',
        'xl' => 'w-20 h-20',
        default => 'w-10 h-10',
    };
@endphp

<div class="inline-flex items-center gap-3">
    <!-- Official BPS Emblem Image -->
    <div {{ $attributes->merge(['class' => "relative shrink-0 $sizeClasses drop-shadow-sm"]) }}>
        @if(file_exists(public_path('images/logo.png')))
            <img src="{{ asset('images/logo.png') }}" alt="Badan Pusat Statistik" class="w-full h-full object-contain">
        @else
            <!-- Vector Fallback -->
            <svg viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
                <defs>
                    <linearGradient id="bpsBlueGrad" x1="60" y1="10" x2="60" y2="60" gradientUnits="userSpaceOnUse">
                        <stop offset="0%" stop-color="#0073B7"/>
                        <stop offset="100%" stop-color="#0057A8"/>
                    </linearGradient>
                    <linearGradient id="bpsOrangeGrad" x1="15" y1="60" x2="60" y2="105" gradientUnits="userSpaceOnUse">
                        <stop offset="0%" stop-color="#F7941D"/>
                        <stop offset="100%" stop-color="#EA580C"/>
                    </linearGradient>
                    <linearGradient id="bpsGreenGrad" x1="60" y1="60" x2="105" y2="105" gradientUnits="userSpaceOnUse">
                        <stop offset="0%" stop-color="#54B847"/>
                        <stop offset="100%" stop-color="#009245"/>
                    </linearGradient>
                </defs>
                <path d="M60 10 L104 54 L60 54 L16 54 Z" fill="url(#bpsBlueGrad)"/>
                <path d="M16 60 L60 60 L60 104 L16 60 Z" fill="url(#bpsOrangeGrad)"/>
                <path d="M60 60 L104 60 L60 104 Z" fill="url(#bpsGreenGrad)"/>
                <path d="M28 54 C40 50, 48 30, 60 22 C72 30, 80 50, 92 54" stroke="#FFFFFF" stroke-width="4.5" stroke-linecap="round" stroke-linejoin="round" fill="none" opacity="0.95"/>
                <circle cx="60" cy="22" r="3" fill="#FFFFFF"/>
            </svg>
        @endif
    </div>

    @if($showText)
        <div class="flex flex-col text-left">
            <span class="font-black text-lg tracking-wider leading-none {{ $textColor }}">SIPABIS</span>
            <span class="text-[10px] uppercase tracking-widest font-semibold opacity-90 {{ $textColor }}">BPS Kab. Cirebon</span>
        </div>
    @endif
</div>
