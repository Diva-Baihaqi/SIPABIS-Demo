<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-950">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ auth()->check() ? 'Konfirmasi Keluar' : 'Berhasil Keluar' }} | SIPABIS BPS Kabupaten Cirebon</title>

    <!-- Google Fonts: DM Serif Display, Plus Jakarta Sans, Inter, JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">

    <style>
        [x-cloak] { display: none !important; }

        @keyframes fadeUpStagger {
            from {
                opacity: 0;
                transform: translateY(18px) scale(0.98);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @keyframes drawCircle {
            from {
                stroke-dashoffset: 226;
            }
            to {
                stroke-dashoffset: 0;
            }
        }

        @keyframes drawCheck {
            from {
                stroke-dashoffset: 48;
            }
            to {
                stroke-dashoffset: 0;
            }
        }

        @keyframes floatOrb {
            0%, 100% {
                transform: translate(0, 0) scale(1);
            }
            50% {
                transform: translate(12px, -16px) scale(1.06);
            }
        }

        .animate-card-enter {
            animation: fadeUpStagger 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        .animate-fade-up-1 {
            opacity: 0;
            animation: fadeUpStagger 0.55s cubic-bezier(0.16, 1, 0.3, 1) 0.12s forwards;
        }

        .animate-fade-up-2 {
            opacity: 0;
            animation: fadeUpStagger 0.55s cubic-bezier(0.16, 1, 0.3, 1) 0.24s forwards;
        }

        .animate-fade-up-3 {
            opacity: 0;
            animation: fadeUpStagger 0.55s cubic-bezier(0.16, 1, 0.3, 1) 0.36s forwards;
        }

        .logout-circle-draw {
            stroke-dasharray: 226;
            stroke-dashoffset: 226;
            animation: drawCircle 0.8s cubic-bezier(0.22, 1, 0.36, 1) 0.1s forwards;
        }

        .logout-check-path {
            stroke-dasharray: 48;
            stroke-dashoffset: 48;
            animation: drawCheck 0.5s cubic-bezier(0.22, 1, 0.36, 1) 0.5s forwards;
        }

        .animate-orb-slow {
            animation: floatOrb 9s ease-in-out infinite;
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen font-sans antialiased text-slate-800 flex items-center justify-center p-4 sm:p-6 relative overflow-hidden bg-slate-950 selection:bg-bps-orange-500 selection:text-white">

    <!-- Background Photography & Layered Gradient Atmosphere -->
    <div class="fixed inset-0 z-0 overflow-hidden pointer-events-none">
        @if(file_exists(public_path('images/bps.jpg')))
            <img src="{{ asset('images/bps.jpg') }}"
                 alt="Kantor BPS Kabupaten Cirebon"
                 class="w-full h-full object-cover object-center scale-105 opacity-45">
        @endif
        <div class="absolute inset-0 bg-gradient-to-br from-bps-navy-900/90 via-slate-900/80 to-bps-navy-900/95"></div>
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,transparent_30%,rgba(2,6,23,0.65)_100%)]"></div>

        <!-- Ambient Floating Orbs -->
        <div class="absolute -top-24 -left-24 w-96 h-96 rounded-full bg-teal-500/20 blur-3xl animate-orb-slow"></div>
        <div class="absolute -bottom-28 -right-20 w-[26rem] h-[26rem] rounded-full bg-bps-orange-500/15 blur-3xl animate-orb-slow" style="animation-delay: -4.5s;"></div>
        <div class="absolute top-1/3 right-1/4 w-64 h-64 rounded-full bg-bps-blue-500/15 blur-3xl animate-orb-slow" style="animation-delay: -2s;"></div>
    </div>

    <!-- Centered Glassmorphism Card -->
    <main class="relative z-10 w-full max-w-lg animate-card-enter">
        <div class="bg-white/95 backdrop-blur-2xl rounded-3xl shadow-[0_30px_70px_-15px_rgba(2,6,23,0.55)] border border-white/60 overflow-hidden">

            <!-- Top BPS Accent Gradient Bar -->
            <div class="h-1.5 w-full bg-gradient-to-r from-bps-blue-500 via-teal-500 to-bps-orange-500"></div>

            <!-- Card Header: Brand & Satker -->
            <div class="px-6 sm:px-8 pt-6 pb-4 border-b border-slate-100 flex items-center justify-between">
                <a href="{{ url('/') }}" class="inline-flex items-center gap-3 group">
                    <x-bps-logo size="sm" />
                    <div>
                        <span class="font-black text-base tracking-tight text-bps-navy-900 group-hover:text-bps-blue-600 transition block leading-none">SIPABIS</span>
                        <span class="text-[10px] text-slate-500 font-bold uppercase tracking-wider mt-1 block">BPS Kabupaten Cirebon</span>
                    </div>
                </a>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-bps-navy-50 border border-bps-navy-100 text-[10px] font-bold font-mono text-bps-navy-700">
                    <span class="w-1.5 h-1.5 rounded-full bg-bps-blue-500"></span>
                    SATKER 428646
                </span>
            </div>

            <div class="p-6 sm:p-8">
                @auth
                    <!-- Mode B: Pengguna Masih Login dan Mengunjungi /logout Langsung -->
                    <div class="text-center space-y-5">
                        <div class="relative mx-auto w-24 h-24 flex items-center justify-center">
                            <span class="absolute inset-1 rounded-full bg-amber-400/20 animate-ping" style="animation-duration: 2.6s;"></span>
                            <div class="relative w-20 h-20 rounded-full bg-gradient-to-br from-amber-50 to-orange-100 border-2 border-amber-200/90 shadow-lg shadow-amber-500/15 flex items-center justify-center text-amber-600">
                                <svg class="w-9 h-9" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                            </div>
                        </div>

                        <div class="animate-fade-up-1 space-y-2">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 border border-amber-200 text-amber-800 text-[11px] font-bold">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                Sesi Aktif Terdeteksi
                            </span>
                            <h1 class="font-serif text-2xl sm:text-3xl text-slate-900 tracking-tight">Konfirmasi Keluar Sistem</h1>
                            <p class="text-xs sm:text-sm text-slate-500 leading-relaxed max-w-md mx-auto">
                                Untuk menjaga keamanan akun, silakan konfirmasi jika Anda ingin mengakhiri sesi kerja saat ini.
                            </p>
                        </div>

                        <!-- Active User Preview Pill -->
                        <div class="animate-fade-up-2 flex items-center gap-3 p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 text-left max-w-sm mx-auto">
                            <div class="w-11 h-11 rounded-xl overflow-hidden border border-slate-200 bg-white shrink-0 shadow-sm">
                                <img src="{{ auth()->user()->foto_url }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover">
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-extrabold text-slate-900 truncate">{{ auth()->user()->name }}</p>
                                <p class="text-[11px] text-slate-500 truncate">{{ auth()->user()->ruangan?->nama_ruangan ?? 'Subbag Umum' }} • <span class="uppercase font-mono font-semibold text-bps-blue-600">{{ auth()->user()->role }}</span></p>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="animate-fade-up-3 flex flex-col sm:flex-row items-center justify-center gap-3 pt-2">
                            <form action="{{ route('logout') }}" method="POST" class="w-full sm:w-auto">
                                @csrf
                                <button type="submit"
                                        class="w-full sm:w-auto px-6 py-3 rounded-2xl font-bold text-xs sm:text-sm text-white bg-gradient-to-r from-rose-600 to-rose-500 hover:from-rose-700 hover:to-rose-600 shadow-lg shadow-rose-500/25 hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center justify-center gap-2 cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                    </svg>
                                    <span>Ya, Keluar Sekarang</span>
                                </button>
                            </form>

                            <a href="{{ route('dashboard') }}"
                               class="w-full sm:w-auto px-6 py-3 rounded-2xl font-bold text-xs sm:text-sm text-slate-700 bg-slate-100 hover:bg-slate-200/80 border border-slate-200 transition-all flex items-center justify-center gap-2">
                                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                </svg>
                                <span>Kembali ke Dashboard</span>
                            </a>
                        </div>
                    </div>
                @else
                    <!-- Mode A: Berhasil Keluar / Sesi Aman (Animasi SVG + Countdown Interaktif) -->
                    <div x-data="logoutCountdown('{{ route('login') }}', 8)" class="text-center space-y-5">

                        <!-- Animated Success Check Shield Emblem -->
                        <div class="relative mx-auto w-24 h-24 flex items-center justify-center">
                            <span class="absolute inset-0 rounded-full bg-emerald-400/20 animate-ping" style="animation-duration: 2.5s;"></span>
                            <div class="absolute inset-1.5 rounded-full bg-emerald-50 border border-emerald-100"></div>
                            <div class="relative w-20 h-20 rounded-full bg-gradient-to-br from-emerald-500 to-teal-600 shadow-xl shadow-emerald-500/30 flex items-center justify-center">
                                <svg class="w-14 h-14 text-white" viewBox="0 0 80 80" fill="none">
                                    <circle cx="40" cy="40" r="36" stroke="rgba(255,255,255,0.35)" stroke-width="3" stroke-linecap="round" class="logout-circle-draw" transform="rotate(-90 40 40)"/>
                                    <path d="M25 41.5L35 51.5L56 29.5" stroke="#FFFFFF" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" class="logout-check-path"/>
                                </svg>
                            </div>
                        </div>

                        <!-- Animated Heading & Status -->
                        <div class="animate-fade-up-1 space-y-2.5">
                            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-700 text-[11px] font-bold">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span>Sesi Terkunci &amp; Aman</span>
                            </div>
                            <h1 class="font-serif text-2xl sm:text-3xl text-slate-900 tracking-tight">Sesi Anda Telah Diamankan</h1>
                            <p class="text-xs sm:text-sm text-slate-500 leading-relaxed max-w-md mx-auto">
                                Terima kasih telah menggunakan <strong class="text-slate-700">SIPABIS</strong>. Seluruh sesi akses dan token keamanan Anda telah dibersihkan dengan aman.
                            </p>
                        </div>

                        <!-- Interactive Auto-Redirect Progress Bar -->
                        <div class="animate-fade-up-2 bg-slate-50 border border-slate-200/80 rounded-2xl p-4 text-left space-y-2.5">
                            <div class="flex items-center justify-between text-[11px]">
                                <span class="font-semibold text-slate-600 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-bps-blue-600" :class="!paused ? 'animate-spin' : ''" style="animation-duration: 3s;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span x-show="!paused" x-cloak>
                                        Dialihkan ke halaman masuk dalam <strong class="font-mono text-bps-navy-800" x-text="seconds">8</strong> detik...
                                    </span>
                                    <span x-show="paused" x-cloak class="text-slate-500">
                                        Pengalihan otomatis dijeda.
                                    </span>
                                </span>
                                <button type="button"
                                        @click="togglePause()"
                                        class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-[10px] font-bold text-slate-600 hover:text-bps-navy-800 hover:border-slate-300 transition cursor-pointer"
                                        x-text="paused ? 'Lanjutkan' : 'Jeda'">
                                    Jeda
                                </button>
                            </div>
                            <div class="w-full h-1.5 rounded-full bg-slate-200/80 overflow-hidden">
                                <div class="h-full rounded-full bg-gradient-to-r from-bps-blue-500 via-teal-500 to-emerald-500 transition-all duration-1000 ease-linear"
                                     :style="`width: ${progressWidth}%`"></div>
                            </div>
                        </div>

                        <!-- CTA Action Buttons -->
                        <div class="animate-fade-up-3 flex flex-col sm:flex-row items-center justify-center gap-3 pt-1">
                            <a href="{{ route('login') }}"
                               class="w-full sm:w-auto px-6 py-3 rounded-2xl font-bold text-xs sm:text-sm text-white bg-gradient-to-r from-bps-navy-700 to-bps-navy-800 hover:from-bps-navy-800 hover:to-bps-navy-900 shadow-lg shadow-bps-navy-900/20 hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center justify-center gap-2">
                                <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                                </svg>
                                <span>Masuk Kembali ke SIPABIS</span>
                            </a>

                            <a href="{{ url('/') }}"
                               class="w-full sm:w-auto px-5 py-3 rounded-2xl font-bold text-xs sm:text-sm text-slate-700 bg-slate-100 hover:bg-slate-200/80 border border-slate-200 transition-all flex items-center justify-center gap-2">
                                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                </svg>
                                <span>Ke Beranda Publik</span>
                            </a>
                        </div>
                    </div>
                @endauth
            </div>

            <!-- Institutional Card Footer -->
            <div class="px-6 sm:px-8 py-4 bg-slate-50/90 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-2 text-[11px] text-slate-400 font-medium">
                <span>Subbagian Umum • BPS Kab. Cirebon</span>
                <span class="font-mono text-slate-500">&copy; {{ date('Y') }} SIPABIS ATK</span>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('logoutCountdown', (redirectUrl, totalSeconds = 8) => ({
                seconds: totalSeconds,
                total: totalSeconds,
                paused: false,
                timerId: null,

                get progressWidth() {
                    return Math.max(0, Math.min(100, Math.round((this.seconds / this.total) * 100)));
                },

                init() {
                    this.startTimer();
                },

                destroy() {
                    this.stopTimer();
                },

                startTimer() {
                    this.stopTimer();
                    this.timerId = setInterval(() => {
                        if (this.paused) return;
                        if (this.seconds <= 1) {
                            this.seconds = 0;
                            this.stopTimer();
                            window.location.assign(redirectUrl);
                            return;
                        }
                        this.seconds -= 1;
                    }, 1000);
                },

                stopTimer() {
                    if (this.timerId) {
                        clearInterval(this.timerId);
                        this.timerId = null;
                    }
                },

                togglePause() {
                    this.paused = !this.paused;
                }
            }));
        });
    </script>
</body>
</html>
