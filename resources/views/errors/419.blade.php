<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-950">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>419 — Sesi Halaman Kedaluwarsa | SIPABIS BPS Kabupaten Cirebon</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">

    <style>
        @keyframes fadeUpCard {
            from {
                opacity: 0;
                transform: translateY(16px) scale(0.98);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @keyframes floatGlow {
            0%, 100% {
                transform: translate(0, 0) scale(1);
            }
            50% {
                transform: translate(-12px, 14px) scale(1.05);
            }
        }

        .animate-error-card {
            animation: fadeUpCard 0.55s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        .animate-glow-orb {
            animation: floatGlow 8s ease-in-out infinite;
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen font-sans antialiased text-slate-800 flex items-center justify-center p-4 sm:p-6 relative overflow-hidden bg-slate-950 selection:bg-bps-orange-500 selection:text-white">

    <!-- Background Photography & Navy Gradient -->
    <div class="fixed inset-0 z-0 overflow-hidden pointer-events-none">
        @if(file_exists(public_path('images/bps.jpg')))
            <img src="{{ asset('images/bps.jpg') }}"
                 alt="Kantor BPS Kabupaten Cirebon"
                 class="w-full h-full object-cover object-center scale-105 opacity-40">
        @endif
        <div class="absolute inset-0 bg-gradient-to-br from-bps-navy-900/95 via-slate-900/85 to-slate-950/95"></div>
        <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full bg-bps-orange-500/20 blur-3xl animate-glow-orb"></div>
        <div class="absolute -bottom-24 -left-24 w-96 h-96 rounded-full bg-bps-blue-500/20 blur-3xl animate-glow-orb" style="animation-delay: -4s;"></div>
    </div>

    <main class="relative z-10 w-full max-w-lg animate-error-card">
        <div class="bg-white/95 backdrop-blur-2xl rounded-3xl shadow-[0_30px_70px_-15px_rgba(2,6,23,0.55)] border border-white/60 overflow-hidden">

            <div class="h-1.5 w-full bg-gradient-to-r from-bps-orange-500 via-amber-500 to-bps-blue-500"></div>

            <!-- Brand Bar -->
            <div class="px-6 sm:px-8 pt-6 pb-4 border-b border-slate-100 flex items-center justify-between">
                <a href="{{ url('/') }}" class="inline-flex items-center gap-3 group">
                    <x-bps-logo size="sm" />
                    <div>
                        <span class="font-black text-base tracking-tight text-bps-navy-900 block leading-none">SIPABIS</span>
                        <span class="text-[10px] text-slate-500 font-bold uppercase tracking-wider mt-1 block">BPS Kabupaten Cirebon</span>
                    </div>
                </a>
                <span class="px-2.5 py-1 rounded-full bg-amber-50 border border-amber-200 text-[10px] font-bold font-mono text-amber-700">
                    HTTP 419
                </span>
            </div>

            <!-- Main Content -->
            <div class="p-6 sm:p-8 text-center space-y-5">
                <div class="relative mx-auto w-24 h-24 flex items-center justify-center">
                    <span class="absolute inset-1 rounded-full bg-amber-400/20 animate-ping" style="animation-duration: 2.8s;"></span>
                    <div class="relative w-20 h-20 rounded-full bg-gradient-to-br from-amber-500 to-bps-orange-600 shadow-xl shadow-amber-500/30 flex items-center justify-center text-white">
                        <svg class="w-9 h-9" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>

                <div class="space-y-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 border border-amber-200 text-amber-800 text-[11px] font-bold">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                        Token Keamanan Berakhir
                    </span>
                    <h1 class="font-serif text-2xl sm:text-3xl text-slate-900 tracking-tight">Sesi Halaman Kedaluwarsa</h1>
                    <p class="text-xs sm:text-sm text-slate-500 leading-relaxed max-w-md mx-auto">
                        Demi keamanan akun dan data persediaan, token formulir pada halaman ini sudah berakhir karena sesi terlalu lama tidak aktif. Silakan muat ulang atau masuk kembali.
                    </p>
                </div>

                <div class="flex flex-col sm:flex-row items-center justify-center gap-3 pt-2">
                    <button type="button"
                            onclick="window.location.reload()"
                            class="w-full sm:w-auto px-6 py-3 rounded-2xl font-bold text-xs sm:text-sm text-white bg-gradient-to-r from-bps-navy-700 to-bps-navy-800 hover:from-bps-navy-800 hover:to-bps-navy-900 shadow-lg shadow-bps-navy-900/20 hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center justify-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        <span>Muat Ulang Halaman</span>
                    </button>

                    <a href="{{ url('/login') }}"
                       class="w-full sm:w-auto px-6 py-3 rounded-2xl font-bold text-xs sm:text-sm text-slate-700 bg-slate-100 hover:bg-slate-200/80 border border-slate-200 transition-all flex items-center justify-center gap-2">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                        <span>Masuk ke SIPABIS</span>
                    </a>
                </div>
            </div>

            <div class="px-6 sm:px-8 py-4 bg-slate-50/90 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-2 text-[11px] text-slate-400 font-medium">
                <span>Subbagian Umum • BPS Kab. Cirebon</span>
                <span class="font-mono text-slate-500">Satker 428646</span>
            </div>
        </div>
    </main>
</body>
</html>
