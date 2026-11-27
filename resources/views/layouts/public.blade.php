<!DOCTYPE html>
<html lang="id" class="h-full scroll-smooth bg-white">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Portal Resmi Persediaan' }} | SIPABIS BPS Kabupaten Cirebon</title>

    <!-- Google Fonts: DM Serif Display, Plus Jakarta Sans, Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">

    <style>
        [x-cloak] { display: none !important; }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="min-h-full flex flex-col font-sans text-slate-800 bg-white selection:bg-brand-lime selection:text-slate-950" x-data="{ mobileMenuOpen: false, scrolled: false }" @scroll.window="scrolled = window.scrollY > 40">

    <!-- Floating Glossy Frosted Capsule Navigation Header -->
    <header class="no-print fixed top-4 inset-x-0 z-50 px-4 sm:px-6 lg:px-8 pointer-events-none transition-all duration-300">
        <div class="pointer-events-auto max-w-7xl mx-auto h-16 sm:h-18 px-5 sm:px-7 rounded-2xl backdrop-blur-xl border shadow-[0_8px_30px_0_rgba(0,0,0,0.18),inset_0_1px_1px_0_rgba(255,255,255,0.35)] flex items-center justify-between text-white transition-all duration-300"
             :class="scrolled ? 'bg-slate-950/85 border-white/15 shadow-2xl' : 'bg-slate-900/35 border-white/25'">
            <!-- Brand Logo & Name with Hover Scale -->
            <a href="{{ url('/') }}" class="group flex items-center gap-3.5 transition duration-300 shrink-0">
                <div class="transition-transform duration-300 group-hover:scale-105 group-hover:rotate-1">
                    <x-bps-logo size="md" />
                </div>
                <div class="flex flex-col">
                    <span class="font-black text-xl tracking-tight text-white group-hover:text-brand-lime transition duration-200">SIPABIS</span>
                    <span class="text-[10px] font-bold text-slate-300 tracking-wider uppercase">BPS Kabupaten Cirebon</span>
                </div>
            </a>

            <!-- Desktop Navigation Links (Center, Minimalist with Underline Animation) -->
            <nav class="hidden lg:flex items-center gap-5 xl:gap-6 text-[13px] font-semibold text-white/90 whitespace-nowrap">
                <a href="{{ url('/#beranda') }}" class="nav-link-animated hover:text-white transition-colors duration-200 py-1">Beranda</a>
                <a href="{{ url('/#kategori') }}" class="nav-link-animated hover:text-white transition-colors duration-200 py-1">Katalog Populer</a>
                <a href="{{ url('/#alur') }}" class="nav-link-animated hover:text-white transition-colors duration-200 py-1">Alur Pelayanan</a>
                <a href="{{ url('/#layanan') }}" class="nav-link-animated hover:text-white transition-colors duration-200 py-1">Layanan Unggulan</a>
                <a href="{{ url('/#katalog') }}" class="nav-link-animated hover:text-white transition-colors duration-200 py-1">Semua Barang</a>
                <a href="{{ route('panduan.index') }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full transition-all duration-200 {{ request()->routeIs('panduan.*') ? 'bg-white/20 text-brand-lime border border-brand-lime/40 shadow-sm' : 'bg-white/10 hover:bg-white/20 text-white border border-white/15 hover:border-brand-lime/40' }}">
                    <svg class="w-3.5 h-3.5 text-brand-lime shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    <span>Panduan</span>
                </a>
                <a href="{{ url('/#tentang') }}" class="nav-link-animated hover:text-white transition-colors duration-200 py-1">Tentang</a>
            </nav>

            <!-- User Auth Action & Pill CTA (Right side of navbar with Shimmer & Glow) -->
            <div class="hidden lg:flex items-center gap-4 shrink-0">
                @auth
                    <a href="{{ url('/dashboard') }}" class="rounded-full bg-brand-lime hover:bg-brand-lime-hover text-slate-950 font-bold px-5 py-2 text-xs tracking-wider shadow-sm hover:shadow-glow-lime transition-all duration-300 transform hover:-translate-y-0.5 hover:scale-105 active:scale-95 inline-flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 text-slate-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <span>Dashboard ({{ ucfirst(auth()->user()->role) }})</span>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="group relative overflow-hidden rounded-full bg-brand-lime hover:bg-brand-lime-hover text-slate-950 font-bold px-5 py-2 text-xs tracking-wider shadow-sm hover:shadow-glow-lime transition-all duration-300 transform hover:-translate-y-0.5 hover:scale-105 active:scale-95 inline-flex items-center gap-1.5">
                        <span>Akses Pegawai</span>
                        <svg class="w-3.5 h-3.5 transition-transform duration-200 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </a>
                @endauth
            </div>

            <!-- Mobile Hamburger Toggle -->
            <div class="lg:hidden flex items-center">
                <button @click="mobileMenuOpen = !mobileMenuOpen" type="button" class="p-2 rounded-xl text-white hover:text-brand-lime hover:bg-white/10 focus:outline-none transition duration-150" aria-label="Toggle Navigation">
                    <svg x-show="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    <svg x-show="mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        <!-- Mobile Menu Drawer (Glossy Frosted Glass) -->
        <div x-show="mobileMenuOpen" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="pointer-events-auto max-w-7xl mx-auto mt-2 bg-slate-900/90 backdrop-blur-2xl border border-white/20 rounded-2xl px-5 py-4 space-y-2 text-white shadow-2xl lg:hidden" 
             style="display: none;">
            <a @click="mobileMenuOpen = false" href="{{ url('/#beranda') }}" class="block px-3 py-2 rounded-xl text-sm font-semibold text-slate-200 hover:text-white hover:bg-white/10 transition">Beranda</a>
            <a @click="mobileMenuOpen = false" href="{{ url('/#kategori') }}" class="block px-3 py-2 rounded-xl text-sm font-semibold text-slate-200 hover:text-white hover:bg-white/10 transition">Katalog Populer</a>
            <a @click="mobileMenuOpen = false" href="{{ url('/#alur') }}" class="block px-3 py-2 rounded-xl text-sm font-semibold text-slate-200 hover:text-white hover:bg-white/10 transition">Alur Pelayanan</a>
            <a @click="mobileMenuOpen = false" href="{{ url('/#layanan') }}" class="block px-3 py-2 rounded-xl text-sm font-semibold text-slate-200 hover:text-white hover:bg-white/10 transition">Layanan Unggulan</a>
            <a @click="mobileMenuOpen = false" href="{{ url('/#katalog') }}" class="block px-3 py-2 rounded-xl text-sm font-semibold text-slate-200 hover:text-white hover:bg-white/10 transition">Semua Barang</a>
            <a @click="mobileMenuOpen = false" href="{{ route('panduan.index') }}" class="flex items-center justify-between px-3 py-2 rounded-xl text-sm font-bold text-brand-lime bg-white/10 hover:bg-white/15 transition">
                <span>Buku Panduan Pengguna</span>
                <span class="text-[10px] uppercase tracking-wider px-2 py-0.5 rounded-full bg-brand-lime/20 border border-brand-lime/40">3 Peran</span>
            </a>
            <a @click="mobileMenuOpen = false" href="{{ url('/#tentang') }}" class="block px-3 py-2 rounded-xl text-sm font-semibold text-slate-200 hover:text-white hover:bg-white/10 transition">Tentang</a>
            <div class="pt-2 border-t border-white/10">
                @auth
                    <a href="{{ url('/dashboard') }}" class="block w-full text-center rounded-xl bg-brand-lime text-slate-950 font-bold px-4 py-2 text-xs">
                        Dashboard ({{ ucfirst(auth()->user()->role) }})
                    </a>
                @else
                    <a href="{{ route('login') }}" class="block w-full text-center rounded-xl bg-brand-lime text-slate-950 font-bold px-4 py-2 text-xs">
                        Akses Pegawai &rarr;
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="flex-1">
        {{ $slot }}
    </main>

    <!-- Official BPS Institutional Footer (Refined, Prestigious & Professional with Hover Micro-animations) -->
    <footer id="tentang" class="no-print bg-slate-950 text-slate-400 pt-16 pb-12 border-t border-slate-800 relative overflow-hidden">
        <!-- Subtle Ambient Footer Light -->
        <div class="absolute top-0 right-0 w-96 h-96 bg-teal-500/5 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-10 pb-12 border-b border-slate-800/80">
                
                <!-- Column 1: Institution Info -->
                <div class="space-y-4 md:col-span-1">
                    <x-bps-logo size="md" showText="true" textColor="text-white" />
                    <p class="text-xs text-slate-400 leading-relaxed text-justify">
                        Sistem Informasi Persediaan Barang Pakai Habis (SIPABIS) merupakan portal digital internal Badan Pusat Statistik Kabupaten Cirebon yang mengintegrasikan pengajuan ATK, pencatatan kartu kendali per unit kerja, hingga penerbitan Berita Acara Stock Opname (BASO) secara akuntabel dan transparan.
                    </p>
                    <div class="text-[11px] text-slate-400 space-y-1 pt-1 border-t border-slate-800/60">
                        <p>Kode Satuan Kerja: <span class="font-mono text-white font-semibold">428646 (BPS 3209)</span></p>
                        <p>Wilayah Kerja: <span class="text-slate-200 font-medium">Kabupaten Cirebon</span></p>
                    </div>
                </div>

                <!-- Column 2: Quick Links with Arrow Slide Hover -->
                <div class="space-y-3">
                    <h4 class="text-xs font-bold text-white uppercase tracking-wider">Navigasi Utama</h4>
                    <ul class="space-y-2 text-xs">
                        <li>
                            <a href="{{ url('/#beranda') }}" class="group inline-flex items-center gap-1.5 hover:text-brand-lime transition-all duration-200">
                                <span class="text-slate-600 group-hover:text-brand-lime transition-colors duration-200">&rsaquo;</span>
                                <span class="group-hover:translate-x-1 transition-transform duration-200">Beranda Utama</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ url('/#kategori') }}" class="group inline-flex items-center gap-1.5 hover:text-brand-lime transition-all duration-200">
                                <span class="text-slate-600 group-hover:text-brand-lime transition-colors duration-200">&rsaquo;</span>
                                <span class="group-hover:translate-x-1 transition-transform duration-200">Katalog Kategori Populer</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ url('/#alur') }}" class="group inline-flex items-center gap-1.5 hover:text-brand-lime transition-all duration-200">
                                <span class="text-slate-600 group-hover:text-brand-lime transition-colors duration-200">&rsaquo;</span>
                                <span class="group-hover:translate-x-1 transition-transform duration-200">Alur Pelayanan ATK</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ url('/#layanan') }}" class="group inline-flex items-center gap-1.5 hover:text-brand-lime transition-all duration-200">
                                <span class="text-slate-600 group-hover:text-brand-lime transition-colors duration-200">&rsaquo;</span>
                                <span class="group-hover:translate-x-1 transition-transform duration-200">Keunggulan Layanan</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ url('/#katalog') }}" class="group inline-flex items-center gap-1.5 hover:text-brand-lime transition-all duration-200">
                                <span class="text-slate-600 group-hover:text-brand-lime transition-colors duration-200">&rsaquo;</span>
                                <span class="group-hover:translate-x-1 transition-transform duration-200">Daftar Lengkap Barang Persediaan</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('panduan.index') }}" class="group inline-flex items-center gap-1.5 hover:text-brand-lime transition-all duration-200">
                                <span class="text-slate-600 group-hover:text-brand-lime transition-colors duration-200">&rsaquo;</span>
                                <span class="group-hover:translate-x-1 transition-transform duration-200">Buku Panduan Pengguna</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('login') }}" class="group inline-flex items-center gap-1.5 hover:text-brand-lime transition-all duration-200">
                                <span class="text-slate-600 group-hover:text-brand-lime transition-colors duration-200">&rsaquo;</span>
                                <span class="group-hover:translate-x-1 transition-transform duration-200">Portal Masuk Pegawai (NIP)</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Column 3: Jam Layanan & SOP with Hover Elevation -->
                <div class="space-y-3">
                    <h4 class="text-xs font-bold text-white uppercase tracking-wider">Layanan Subbag Umum</h4>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Pengambilan fisik barang dan verifikasi administrasi dilayani pada jam operasional kantor:
                    </p>
                    <div class="p-4 rounded-2xl bg-slate-900/90 border border-slate-800 text-[11px] space-y-2.5 shadow-sm hover:border-slate-700 transition duration-300">
                        <div class="flex justify-between text-slate-300">
                            <span>Senin – Kamis:</span>
                            <span class="font-semibold text-white font-mono">08.00 – 16.00 WIB</span>
                        </div>
                        <div class="flex justify-between text-slate-300">
                            <span>Jumat:</span>
                            <span class="font-semibold text-white font-mono">08.00 – 16.30 WIB</span>
                        </div>
                        <div class="pt-2 text-[10px] text-amber-300 border-t border-slate-800/80 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Waktu Istirahat: 12.00 – 13.00 WIB</span>
                        </div>
                    </div>
                </div>

                <!-- Column 4: Contact & Office -->
                <div class="space-y-3">
                    <h4 class="text-xs font-bold text-white uppercase tracking-wider">Kantor BPS Kabupaten Cirebon</h4>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Jl. Sunan Rahmat No. 7, Sumber, Kec. Sumber, Kabupaten Cirebon, Jawa Barat 45611
                    </p>
                    <div class="text-xs space-y-2 text-slate-400 pt-1">
                        <p class="flex items-center gap-2 group">
                            <span class="text-slate-500 font-semibold w-16">Telepon:</span>
                            <span class="text-slate-200 font-mono group-hover:text-brand-lime transition">(0231) 321151</span>
                        </p>
                        <p class="flex items-center gap-2 group">
                            <span class="text-slate-500 font-semibold w-16">Email:</span>
                            <a href="mailto:bps3209@bps.go.id" class="text-slate-200 group-hover:text-brand-lime transition">bps3209@bps.go.id</a>
                        </p>
                        <p class="flex items-center gap-2 group">
                            <span class="text-slate-500 font-semibold w-16">Website:</span>
                            <a href="https://cirebonkab.bps.go.id" target="_blank" class="text-teal-400 group-hover:text-brand-lime underline font-medium transition">cirebonkab.bps.go.id</a>
                        </p>
                    </div>
                </div>

            </div>

            <!-- Bottom Sub-Footer (Official, Prestigious, Formal Academic Acknowledgement) -->
            <div class="pt-8 flex flex-col md:flex-row items-center justify-between text-xs text-slate-400 gap-4">
                <div class="flex items-center gap-2 text-center md:text-left">
                    <p>&copy; {{ date('Y') }} Badan Pusat Statistik Kabupaten Cirebon. Hak Cipta Dilindungi.</p>
                </div>
                
                <div class="flex flex-wrap items-center justify-center md:justify-end gap-2 text-[11px] text-slate-400">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-slate-900 border border-slate-800 text-slate-300">
                        <span class="w-1.5 h-1.5 rounded-full bg-brand-lime animate-pulse"></span>
                        Satker 428646
                    </span>
                    <span>•</span>
                    <span class="text-slate-300 font-medium">Inovasi Digitalisasi Pengelolaan Persediaan BMN BPS Kabupaten Cirebon</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- SIPABIS AI Virtual Assistant Floating Widget -->
    <div class="no-print">
        <x-chatbot-widget />
    </div>

    @stack('scripts')
</body>
</html>