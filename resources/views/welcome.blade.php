<x-public-layout>
    <x-slot:title>Portal Resmi SIPABIS</x-slot:title>

    <!-- 1. HERO SECTION WITH OFFICIAL BPS BUILDING BACKGROUND & GLOSSY TRANSPARENT DESIGN -->
    <section id="beranda" class="relative overflow-hidden bg-slate-950 text-white min-h-screen flex flex-col justify-between pt-28 pb-8 sm:pb-12 px-4 sm:px-6 lg:px-8">
        <!-- High-Resolution Photography of BPS Cirebon Office as Full-Width Background (Sharp & Clear) -->
        <div class="absolute inset-0 z-0 overflow-hidden">
            @if(file_exists(public_path('images/bps.jpg')))
                <img src="{{ asset('images/bps.jpg') }}" alt="Gedung Kantor BPS Kabupaten Cirebon" class="w-full h-full object-cover object-[center_35%]">
            @elseif(file_exists(public_path('images/home.jpg')))
                <img src="{{ asset('images/home.jpg') }}" alt="Gedung Kantor BPS Kabupaten Cirebon" class="w-full h-full object-cover object-[center_35%]">
            @endif

            <!-- Clean Soft Directional Scrim: Subtle soft gradient on left for crystal clarity & bright sunlit scene -->
            <div class="absolute inset-0 bg-gradient-to-r from-slate-950/50 via-slate-950/20 to-transparent"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/15 via-transparent to-transparent"></div>
        </div>

        <!-- Middle Content: Editorial Headline, Subtitle, & Quick Action Pills -->
        <div class="relative z-10 max-w-7xl mx-auto w-full my-auto py-8">
            <div class="max-w-2xl space-y-6 text-left">
                <!-- Cyan & Lime Uppercase Category Tag with Glowing Dot -->
                <div class="inline-flex items-center gap-2.5 px-4 py-1.5 rounded-full bg-teal-950/80 border border-teal-500/30 backdrop-blur-md text-teal-300 shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-brand-lime animate-pulse"></span>
                    <span class="text-xs font-bold tracking-widest uppercase font-sans">
                        PORTAL RESMI • BPS KABUPATEN CIREBON
                    </span>
                </div>

                <!-- Main Editorial Serif Headline (4 Distinct Lines matching Reference) -->
                <h1 class="font-serif text-4xl sm:text-6xl lg:text-[4.75rem] text-white leading-[1.08] tracking-tight drop-shadow-md">
                    Tata kelola<br>
                    persediaan.<br>
                    Kami siap penuhi<br>
                    kebutuhan Anda
                </h1>

                <!-- Concise 2-Line Subtitle -->
                <p class="text-sm sm:text-base text-slate-100 leading-relaxed font-sans max-w-xl font-normal drop-shadow-sm">
                    Sistem informasi persediaan barang pakai habis terpadu<br class="hidden sm:inline">
                    Badan Pusat Statistik Kabupaten Cirebon.
                </p>

                <!-- Quick Action Translucent Pill Buttons -->
                <div class="pt-2 flex flex-wrap items-center gap-3 text-xs text-white">
                    <a href="#katalog" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-white/10 hover:bg-white/20 border border-white/20 backdrop-blur-md transition-all duration-200 hover:scale-105 active:scale-95 shadow-sm text-white font-medium">
                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <span>Cari {{ $totalBarang }} Barang</span>
                    </a>
                    <a href="#alur" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-white/10 hover:bg-white/20 border border-white/20 backdrop-blur-md transition-all duration-200 hover:scale-105 active:scale-95 shadow-sm text-white font-medium">
                        <span>&rarr; Alur Pelayanan</span>
                    </a>
                    <a href="{{ route('panduan.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-teal-950/60 hover:bg-teal-900/80 border border-teal-400/40 backdrop-blur-md transition-all duration-200 hover:scale-105 active:scale-95 shadow-sm text-brand-lime font-semibold">
                        <svg class="w-3.5 h-3.5 text-brand-lime shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        <span>Buku Panduan</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Bottom Horizontal Glossy Frosted Glass Dock: Ringkasan Persediaan -->
        <div class="relative z-10 max-w-7xl mx-auto w-full pt-4">
            <div class="relative overflow-hidden rounded-2xl sm:rounded-3xl bg-slate-900/20 backdrop-blur-2xl border border-white/30 shadow-[0_8px_30px_0_rgba(0,0,0,0.18),inset_0_1px_1px_0_rgba(255,255,255,0.45)] p-5 sm:p-6 text-white transition-all duration-300 hover:border-white/40">
                <!-- Top Specular Gloss Highlight Reflection -->
                <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-white/60 to-transparent pointer-events-none"></div>
                <!-- Ambient Glare Reflection in corner -->
                <div class="absolute -top-12 -left-12 w-48 h-32 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>

                <div class="relative z-10 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-6 items-center">
                    <!-- Title with Live Pulse Indicator -->
                    <div class="col-span-2 sm:col-span-3 lg:col-span-1 flex items-center gap-2.5">
                        <span class="w-2 h-2 rounded-full bg-brand-lime shadow-glow-lime animate-pulse"></span>
                        <h3 class="text-base sm:text-lg font-bold text-white tracking-wide">Ringkasan Persediaan</h3>
                    </div>

                    <!-- Metric 1: Total Katalog -->
                    <div class="border-t sm:border-t-0 sm:border-l border-white/20 pt-3 sm:pt-0 sm:pl-6 text-left">
                        <span class="text-3xl sm:text-4xl font-black text-brand-lime block tracking-tight drop-shadow-sm">{{ number_format($totalBarang) }}</span>
                        <span class="text-xs text-slate-200 font-medium block mt-0.5">Total Katalog</span>
                    </div>

                    <!-- Metric 2: Klasifikasi -->
                    <div class="border-t sm:border-t-0 sm:border-l border-white/20 pt-3 sm:pt-0 sm:pl-6 text-left">
                        <span class="text-3xl sm:text-4xl font-black text-teal-300 block tracking-tight drop-shadow-sm">{{ $totalKategori }}</span>
                        <span class="text-xs text-slate-200 font-medium block mt-0.5">Klasifikasi</span>
                    </div>

                    <!-- Metric 3: Unit Kerja -->
                    <div class="border-t sm:border-t-0 lg:border-l border-white/20 pt-3 sm:pt-0 lg:pl-6 text-left">
                        <span class="text-3xl sm:text-4xl font-black text-sky-300 block tracking-tight drop-shadow-sm">{{ $totalRuangan }}</span>
                        <span class="text-xs text-slate-200 font-medium block mt-0.5">Unit Kerja</span>
                    </div>

                    <!-- Metric 4: Akurasi Opname -->
                    <div class="border-t sm:border-t-0 sm:border-l border-white/20 pt-3 sm:pt-0 sm:pl-6 text-left">
                        <span class="text-3xl sm:text-4xl font-black text-amber-300 block tracking-tight drop-shadow-sm">100%</span>
                        <span class="text-xs text-slate-200 font-medium block mt-0.5">Akurasi Opname</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 2. TOP SELLING / POPULAR CATEGORIES ("Explore Top Destinations" with 3D Hover & Shimmer) -->
    <section id="kategori" class="bg-gradient-to-b from-white via-slate-50/50 to-white py-16 sm:py-24 border-t border-slate-100 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Section Header -->
            <div class="text-center max-w-2xl mx-auto mb-14">
                <span class="text-xs font-bold tracking-widest uppercase text-teal-600 block mb-2 transition-transform duration-300 hover:scale-105 cursor-default">KATALOG POPULER</span>
                <h2 class="font-serif text-3xl sm:text-4xl text-slate-900 tracking-tight">Kategori Barang Terpopuler</h2>
                <p class="text-sm text-slate-500 mt-2">Daftar kelompok barang pakai habis yang paling sering diajukan untuk mendukung kelancaran kegiatan sensus dan survei statistik.</p>
            </div>

            <!-- 4 Cards Grid with Smooth Hover Lift, Depth, and Rotation -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                
                <!-- Card 1: Barang Persediaan Umum -->
                <div @click="window.dispatchEvent(new CustomEvent('filter-kategori', { detail: '8' }))" class="group bg-white rounded-3xl border border-slate-200/90 overflow-hidden shadow-sm hover:shadow-2xl hover:border-teal-400 hover:-translate-y-2.5 transition-all duration-400 flex flex-col justify-between cursor-pointer">
                    <div class="relative overflow-hidden bg-slate-50/60 h-48 sm:h-52 w-full flex items-center justify-center p-3 border-b border-slate-100/80">
                        <!-- Subtle Ambient Glow -->
                        <div class="absolute inset-0 bg-teal-500/5 rounded-full blur-2xl pointer-events-none"></div>

                        <!-- 3D Isometric Illustration -->
                        <img src="{{ asset('images/kategori/barang.png') }}"
                             alt="Barang Persediaan Umum"
                             class="w-full h-full object-contain transition-transform duration-500 ease-out group-hover:scale-108 group-hover:-translate-y-1 relative z-0">

                        <!-- Top-Right Dark Badge (Matching Reference Pill) with Hover Shimmer -->
                        <div class="absolute top-3.5 right-3.5 z-10">
                            <span class="bg-slate-900/90 group-hover:bg-teal-900 backdrop-blur text-white text-[11px] font-bold px-3 py-1 rounded-full shadow-sm transition-all duration-300 group-hover:scale-105">
                                {{ $kategoriList->firstWhere('id', 8)?->barang_count ?? 16 }} Item
                            </span>
                        </div>
                    </div>

                    <div class="p-5 flex-1 flex flex-col justify-between space-y-3 bg-white">
                        <div>
                            <h3 class="font-bold text-slate-900 text-base group-hover:text-teal-700 transition-colors duration-200 leading-snug">
                                Barang Persediaan Umum
                            </h3>
                            <p class="text-xs text-slate-500 mt-1 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0 group-hover:text-teal-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                <span>Gudang Utama • Subbag Umum</span>
                            </p>
                        </div>

                        <div class="pt-2 border-t border-slate-100 flex items-center justify-between">
                            <div class="flex items-center gap-1 text-amber-500 text-xs">
                                <span>★★★★★</span>
                                <span class="text-slate-600 font-semibold text-[11px] ml-1">(5.0)</span>
                            </div>
                            <span class="text-[11px] font-bold text-emerald-600 flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span>Stok Sedia</span>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Perlengkapan Sensus & Survei -->
                <div @click="window.dispatchEvent(new CustomEvent('filter-kategori', { detail: '5' }))" class="group bg-white rounded-3xl border border-slate-200/90 overflow-hidden shadow-sm hover:shadow-2xl hover:border-amber-400 hover:-translate-y-2.5 transition-all duration-400 flex flex-col justify-between cursor-pointer">
                    <div class="relative overflow-hidden bg-slate-50/60 h-48 sm:h-52 w-full flex items-center justify-center p-3 border-b border-slate-100/80">
                        <!-- Subtle Ambient Glow -->
                        <div class="absolute inset-0 bg-amber-500/5 rounded-full blur-2xl pointer-events-none"></div>

                        <!-- 3D Isometric Illustration -->
                        <img src="{{ asset('images/kategori/sensus.png') }}"
                             alt="Perlengkapan Sensus & Survei"
                             class="w-full h-full object-contain transition-transform duration-500 ease-out group-hover:scale-108 group-hover:-translate-y-1 relative z-0">

                        <div class="absolute top-3.5 right-3.5 z-10">
                            <span class="bg-slate-900/90 group-hover:bg-amber-900 backdrop-blur text-white text-[11px] font-bold px-3 py-1 rounded-full shadow-sm transition-all duration-300 group-hover:scale-105">
                                {{ $kategoriList->firstWhere('id', 5)?->barang_count ?? 400 }} Item
                            </span>
                        </div>
                    </div>

                    <div class="p-5 flex-1 flex flex-col justify-between space-y-3 bg-white">
                        <div>
                            <h3 class="font-bold text-slate-900 text-base group-hover:text-amber-600 transition-colors duration-200 leading-snug">
                                Perlengkapan Sensus & Survei
                            </h3>
                            <p class="text-xs text-slate-500 mt-1 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0 group-hover:text-amber-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                <span>Logistik Lapangan • BPS 3209</span>
                            </p>
                        </div>

                        <div class="pt-2 border-t border-slate-100 flex items-center justify-between">
                            <div class="flex items-center gap-1 text-amber-500 text-xs">
                                <span>★★★★★</span>
                                <span class="text-slate-600 font-semibold text-[11px] ml-1">(5.0)</span>
                            </div>
                            <span class="text-[11px] font-bold text-emerald-600 flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span>Stok Sedia</span>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Buku, Map & Pengarsipan -->
                <div @click="window.dispatchEvent(new CustomEvent('filter-kategori', { detail: '4' }))" class="group bg-white rounded-3xl border border-slate-200/90 overflow-hidden shadow-sm hover:shadow-2xl hover:border-sky-400 hover:-translate-y-2.5 transition-all duration-400 flex flex-col justify-between cursor-pointer">
                    <div class="relative overflow-hidden bg-slate-50/60 h-48 sm:h-52 w-full flex items-center justify-center p-3 border-b border-slate-100/80">
                        <!-- Subtle Ambient Glow -->
                        <div class="absolute inset-0 bg-sky-500/5 rounded-full blur-2xl pointer-events-none"></div>

                        <!-- 3D Isometric Illustration -->
                        <img src="{{ asset('images/kategori/buku.png') }}"
                             alt="Buku, Map & Pengarsipan"
                             class="w-full h-full object-contain transition-transform duration-500 ease-out group-hover:scale-108 group-hover:-translate-y-1 relative z-0">

                        <div class="absolute top-3.5 right-3.5 z-10">
                            <span class="bg-slate-900/90 group-hover:bg-sky-900 backdrop-blur text-white text-[11px] font-bold px-3 py-1 rounded-full shadow-sm transition-all duration-300 group-hover:scale-105">
                                {{ $kategoriList->firstWhere('id', 4)?->barang_count ?? 39 }} Item
                            </span>
                        </div>
                    </div>

                    <div class="p-5 flex-1 flex flex-col justify-between space-y-3 bg-white">
                        <div>
                            <h3 class="font-bold text-slate-900 text-base group-hover:text-sky-600 transition-colors duration-200 leading-snug">
                                Buku, Map & Pengarsipan
                            </h3>
                            <p class="text-xs text-slate-500 mt-1 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0 group-hover:text-sky-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                <span>Arsip Persediaan • Rak B</span>
                            </p>
                        </div>

                        <div class="pt-2 border-t border-slate-100 flex items-center justify-between">
                            <div class="flex items-center gap-1 text-amber-500 text-xs">
                                <span>★★★★★</span>
                                <span class="text-slate-600 font-semibold text-[11px] ml-1">(5.0)</span>
                            </div>
                            <span class="text-[11px] font-bold text-emerald-600 flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span>Stok Sedia</span>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Card 4: Alat Tulis Kantor (ATK) -->
                <div @click="window.dispatchEvent(new CustomEvent('filter-kategori', { detail: '1' }))" class="group bg-white rounded-3xl border border-slate-200/90 overflow-hidden shadow-sm hover:shadow-2xl hover:border-emerald-400 hover:-translate-y-2.5 transition-all duration-400 flex flex-col justify-between cursor-pointer">
                    <div class="relative overflow-hidden bg-slate-50/60 h-48 sm:h-52 w-full flex items-center justify-center p-3 border-b border-slate-100/80">
                        <!-- Subtle Ambient Glow -->
                        <div class="absolute inset-0 bg-emerald-500/5 rounded-full blur-2xl pointer-events-none"></div>

                        <!-- 3D Isometric Illustration -->
                        <img src="{{ asset('images/kategori/atk.png') }}"
                             alt="Alat Tulis Kantor (ATK)"
                             class="w-full h-full object-contain transition-transform duration-500 ease-out group-hover:scale-108 group-hover:-translate-y-1 relative z-0">

                        <div class="absolute top-3.5 right-3.5 z-10">
                            <span class="bg-slate-900/90 group-hover:bg-emerald-900 backdrop-blur text-white text-[11px] font-bold px-3 py-1 rounded-full shadow-sm transition-all duration-300 group-hover:scale-105">
                                {{ $kategoriList->firstWhere('id', 1)?->barang_count ?? 116 }} Item
                            </span>
                        </div>
                    </div>

                    <div class="p-5 flex-1 flex flex-col justify-between space-y-3 bg-white">
                        <div>
                            <h3 class="font-bold text-slate-900 text-base group-hover:text-emerald-600 transition-colors duration-200 leading-snug">
                                Alat Tulis Kantor (ATK)
                            </h3>
                            <p class="text-xs text-slate-500 mt-1 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0 group-hover:text-emerald-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                <span>Etalase Pelayanan • Subbag Umum</span>
                            </p>
                        </div>

                        <div class="pt-2 border-t border-slate-100 flex items-center justify-between">
                            <div class="flex items-center gap-1 text-amber-500 text-xs">
                                <span>★★★★★</span>
                                <span class="text-slate-600 font-semibold text-[11px] ml-1">(5.0)</span>
                            </div>
                            <span class="text-[11px] font-bold text-emerald-600 flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span>Stok Sedia</span>
                            </span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- 3. EASY STEPS ("Easy steps for planning your next trip" with Interactive Slide Hover) -->
    <section id="alur" class="bg-gradient-to-b from-[#f0fbf9] via-[#eaf8f6] to-[#f0fbf9] py-20 sm:py-28 border-y border-teal-100/60 relative overflow-hidden">
        <!-- Decorative Ambient Light -->
        <div class="absolute -top-40 right-1/4 w-96 h-96 bg-teal-200/20 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-center">
                
                <!-- Left Column: Heading + Visual Composition -->
                <div class="lg:col-span-5 space-y-6">
                    <span class="text-xs font-bold tracking-widest uppercase text-teal-600 block">TAHAPAN MUDAH</span>
                    
                    <h2 class="font-serif text-3xl sm:text-5xl text-slate-900 leading-[1.18] tracking-tight">
                        Langkah mudah<br>
                        pengajuan persediaan<br>
                        kantor Anda
                    </h2>

                    <p class="text-sm text-slate-600 leading-relaxed">
                        Tertib administrasi persediaan sesuai SOP Badan Pusat Statistik. Seluruh proses terpantau secara transparan mulai dari permohonan digital hingga Berita Acara serah terima.
                    </p>

                    <!-- Framed Photo Accent with Soft Hover Tilt -->
                    @if(file_exists(public_path('images/home.jpg')))
                        <div class="relative rounded-3xl overflow-hidden shadow-lg border-2 border-white max-w-sm mt-6 group transition-all duration-500 hover:scale-[1.03] hover:shadow-2xl">
                            <img src="{{ asset('images/home.jpg') }}" alt="Subbag Umum BPS" class="w-full h-48 object-cover transition-transform duration-700 group-hover:scale-110">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-slate-900/30 to-transparent flex items-end p-4">
                                <div class="text-white text-xs font-medium">
                                    <span class="font-bold block text-sm">Pelayanan Subbag Umum BPS</span>
                                    <span class="text-slate-300 text-[11px]">Senin – Jumat di Ruang Persediaan</span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Right Column: 3 Numbered Steps with Smooth Hover Slide & Ring Expansion -->
                <div class="lg:col-span-7 space-y-6">
                    
                    <!-- Step 1 -->
                    <div class="group flex items-start gap-5 bg-white p-6 sm:p-7 rounded-3xl shadow-sm border border-slate-200/90 hover:border-teal-300 hover:shadow-xl hover:translate-x-2.5 transition-all duration-300 cursor-default">
                        <div class="w-12 h-12 rounded-full bg-brand-lime text-slate-950 font-black text-base flex items-center justify-center shrink-0 shadow-sm transition-all duration-300 group-hover:scale-110 group-hover:ring-4 group-hover:ring-brand-lime/40">
                            1
                        </div>
                        <div class="space-y-1.5">
                            <h3 class="font-bold text-slate-900 text-lg group-hover:text-teal-700 transition-colors duration-200">Pilih Barang dari Katalog Digital</h3>
                            <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
                                Temukan barang habis pakai melalui katalog {{ $totalBarang }} item riil BPS Kabupaten Cirebon. Cek ketersediaan fisik secara real-time sebelum mengajukan pesanan.
                            </p>
                        </div>
                    </div>

                    <!-- Step 2 -->
                    <div class="group flex items-start gap-5 bg-white p-6 sm:p-7 rounded-3xl shadow-sm border border-slate-200/90 hover:border-teal-300 hover:shadow-xl hover:translate-x-2.5 transition-all duration-300 cursor-default">
                        <div class="w-12 h-12 rounded-full bg-brand-lime text-slate-950 font-black text-base flex items-center justify-center shrink-0 shadow-sm transition-all duration-300 group-hover:scale-110 group-hover:ring-4 group-hover:ring-brand-lime/40">
                            2
                        </div>
                        <div class="space-y-1.5">
                            <h3 class="font-bold text-slate-900 text-lg group-hover:text-teal-700 transition-colors duration-200">Tentukan Jumlah & Ruangan Kerja</h3>
                            <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
                                Masukkan kuota yang dibutuhkan serta peruntukan kegiatan (sensus, survei, atau administrasi rutin). Setiap transaksi otomatis tercatat di kartu kendali ruangan.
                            </p>
                        </div>
                    </div>

                    <!-- Step 3 -->
                    <div class="group flex items-start gap-5 bg-white p-6 sm:p-7 rounded-3xl shadow-sm border border-slate-200/90 hover:border-teal-300 hover:shadow-xl hover:translate-x-2.5 transition-all duration-300 cursor-default">
                        <div class="w-12 h-12 rounded-full bg-brand-lime text-slate-950 font-black text-base flex items-center justify-center shrink-0 shadow-sm transition-all duration-300 group-hover:scale-110 group-hover:ring-4 group-hover:ring-brand-lime/40">
                            3
                        </div>
                        <div class="space-y-1.5">
                            <h3 class="font-bold text-slate-900 text-lg group-hover:text-teal-700 transition-colors duration-200">Verifikasi Subbag Umum & Ambil Fisik</h3>
                            <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
                                Admin Subbag Umum menyetujui kuota, memotong stok secara atomik tanpa risiko minus, dan menyerahkan barang disertai bukti penerimaan resmi.
                            </p>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </section>

    <!-- 4. SERVICES / KEUNGGULAN SISTEM ("We offer the best services" with Multi-Color Duotone & Hover Lift) -->
    <section id="layanan" class="bg-white py-20 sm:py-28 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Centered Header -->
            <div class="text-center max-w-2xl mx-auto mb-16">
                <span class="text-xs font-bold tracking-widest uppercase text-teal-600 block mb-2 transition-transform duration-300 hover:scale-105 cursor-default">SERVICES</span>
                <h2 class="font-serif text-3xl sm:text-4xl text-slate-900 tracking-tight">Pelayanan Terbaik untuk Insan Statistik</h2>
                <p class="text-sm text-slate-500 mt-2">Dukungan fitur cerdas yang dirancang untuk mengatasi kelemahan sistem sebelumnya dan meningkatkan akuntabilitas BMN.</p>
            </div>

            <!-- 4 Duotone Outline Icon Cards Grid with Multi-Color Highlights -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                
                <!-- Service 1: Smart Search (Teal Theme) -->
                <div class="group p-7 rounded-3xl bg-slate-50/80 border border-slate-200/80 hover:bg-white hover:border-teal-300 hover:shadow-2xl hover:shadow-teal-100/50 hover:-translate-y-2 transition-all duration-300 space-y-4 cursor-default">
                    <div class="w-14 h-14 rounded-2xl bg-teal-50 group-hover:bg-teal-600 shadow-sm border border-teal-100 flex items-center justify-center text-teal-600 group-hover:text-white transition-all duration-300 group-hover:scale-110 group-hover:rotate-3">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <h3 class="font-bold text-slate-900 text-base group-hover:text-teal-700 transition-colors">Pencarian Cerdas</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Filter seketika {{ $totalBarang }} item persediaan berdasarkan kode barang BPS, nama spesifik, atau kategori tanpa reload halaman.
                    </p>
                </div>

                <!-- Service 2: Anti-Minus Concurrency (Emerald Theme) -->
                <div class="group p-7 rounded-3xl bg-slate-50/80 border border-slate-200/80 hover:bg-white hover:border-emerald-300 hover:shadow-2xl hover:shadow-emerald-100/50 hover:-translate-y-2 transition-all duration-300 space-y-4 cursor-default">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-50 group-hover:bg-emerald-600 shadow-sm border border-emerald-100 flex items-center justify-center text-emerald-600 group-hover:text-white transition-all duration-300 group-hover:scale-110 group-hover:rotate-3">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <h3 class="font-bold text-slate-900 text-base group-hover:text-emerald-700 transition-colors">Pencegahan Stok Minus</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Arsitektur transaksi database dengan <code class="text-emerald-700 font-mono text-[11px] bg-emerald-50 px-1 py-0.5 rounded">lockForUpdate()</code> menjamin integritas stok saat banyak permintaan bersamaan.
                    </p>
                </div>

                <!-- Service 3: Buku Kendali Real-Time (Sky Theme) -->
                <div class="group p-7 rounded-3xl bg-slate-50/80 border border-slate-200/80 hover:bg-white hover:border-sky-300 hover:shadow-2xl hover:shadow-sky-100/50 hover:-translate-y-2 transition-all duration-300 space-y-4 cursor-default">
                    <div class="w-14 h-14 rounded-2xl bg-sky-50 group-hover:bg-sky-600 shadow-sm border border-sky-100 flex items-center justify-center text-sky-600 group-hover:text-white transition-all duration-300 group-hover:scale-110 group-hover:rotate-3">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <h3 class="font-bold text-slate-900 text-base group-hover:text-sky-700 transition-colors">Buku Kendali Otomatis</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Riwayat mutasi saldo awal, pengadaan masuk, dan pengeluaran per tim kerja tercatat rapi secara digital.
                    </p>
                </div>

                <!-- Service 4: Stock Opname & BASO (Amber Theme) -->
                <div class="group p-7 rounded-3xl bg-slate-50/80 border border-slate-200/80 hover:bg-white hover:border-amber-300 hover:shadow-2xl hover:shadow-amber-100/50 hover:-translate-y-2 transition-all duration-300 space-y-4 cursor-default">
                    <div class="w-14 h-14 rounded-2xl bg-amber-50 group-hover:bg-amber-600 shadow-sm border border-amber-100 flex items-center justify-center text-amber-600 group-hover:text-white transition-all duration-300 group-hover:scale-110 group-hover:rotate-3">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <h3 class="font-bold text-slate-900 text-base group-hover:text-amber-700 transition-colors">Stock Opname & BASO</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Fasilitas stock opname berkala dan penerbitan format resmi Berita Acara Stock Opname (BASO) siap disahkan Kepala BPS.
                    </p>
                </div>

            </div>
        </div>
    </section>

    <!-- 5. TESTIMONIALS SECTION ("What people say about us" with Floating Elevation) -->
    <section class="bg-gradient-to-b from-[#f0fbf9] via-[#e8f7f5] to-[#f0fbf9] py-20 sm:py-28 border-t border-teal-100/60 relative overflow-hidden">
        <!-- Ambient Soft Glow -->
        <div class="hidden sm:block absolute top-1/2 left-1/4 -translate-y-1/2 w-96 h-96 bg-teal-200/30 rounded-full blur-3xl -z-0 pointer-events-none"></div>
        <div class="hidden sm:block absolute top-1/2 right-1/4 -translate-y-1/2 w-96 h-96 bg-lime-200/25 rounded-full blur-3xl -z-0 pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <!-- Header Section -->
            <div class="text-center max-w-2xl mx-auto mb-14 sm:mb-16 space-y-4">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold tracking-widest uppercase text-teal-800 bg-teal-100/70 border border-teal-200/60 shadow-xs">
                    <span class="w-2 h-2 rounded-full bg-teal-500 animate-pulse"></span>
                    Testimonials
                </span>
                
                <h2 class="font-serif text-3xl sm:text-5xl text-slate-900 leading-[1.18] tracking-tight">
                    Apresiasi dari tim kerja BPS
                </h2>

                <p class="text-sm text-slate-600 leading-relaxed">
                    Dukungan nyata dari pimpinan hingga tim teknis lapangan dalam mewujudkan tata kelola logistik persediaan yang modern, cepat, dan transparan.
                </p>
            </div>

            <!-- Two Testimonials Cards Side by Side -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-7 lg:gap-8 max-w-5xl mx-auto">
                <!-- Card 1: Kepala BPS - Drs. Hendra Wijaya, M.Si. -->
                <div class="group bg-white p-7 sm:p-8 rounded-3xl shadow-xl hover:shadow-2xl border border-slate-200/80 flex flex-col justify-between transition-all duration-400 hover:-translate-y-1.5 cursor-default relative overflow-hidden">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="w-14 h-14 rounded-full ring-4 ring-brand-lime/80 p-0.5 bg-slate-100 overflow-hidden shrink-0 transition-transform duration-300 group-hover:scale-105 shadow-sm">
                                    <div class="w-full h-full bg-gradient-to-br from-teal-800 to-teal-950 text-white font-black flex items-center justify-center text-sm rounded-full tracking-wider">
                                        HW
                                    </div>
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-900 text-base group-hover:text-teal-700 transition-colors">Drs. Hendra Wijaya, M.Si.</h4>
                                    <p class="text-xs text-slate-500 font-medium">Kepala BPS Kabupaten Cirebon</p>
                                </div>
                            </div>
                            <div class="hidden sm:flex text-amber-400 text-xs gap-0.5" title="Akurasi Penatausahaan">
                                ★★★★★
                            </div>
                        </div>

                        <blockquote class="text-slate-600 text-xs sm:text-sm leading-relaxed italic pt-2">
                            "SIPABIS mentransformasi tata kelola persediaan BPS Cirebon menjadi serba cepat, akuntabel, dan transparan. Pengawasan stok dan pengesahan opname bulanan kini tuntas dalam hitungan detik."
                        </blockquote>
                    </div>

                    <div class="pt-5 mt-5 flex items-center justify-between text-[11px] text-slate-400 border-t border-slate-100">
                        <span class="font-medium text-slate-500">Badan Pusat Statistik Kab. Cirebon</span>
                        <span class="text-teal-700 font-semibold font-mono bg-teal-50 border border-teal-100 px-2.5 py-0.5 rounded-full">Satker 428646</span>
                    </div>
                </div>

                <!-- Card 2: Pegawai Divisi Sosial - Dimas Pratama, S.M. -->
                <div class="group bg-white p-7 sm:p-8 rounded-3xl shadow-xl hover:shadow-2xl border border-slate-200/80 flex flex-col justify-between transition-all duration-400 hover:-translate-y-1.5 cursor-default relative overflow-hidden">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="w-14 h-14 rounded-full ring-4 ring-teal-400/80 p-0.5 bg-slate-100 overflow-hidden shrink-0 transition-transform duration-300 group-hover:scale-105 shadow-sm">
                                    <div class="w-full h-full bg-gradient-to-br from-emerald-700 to-teal-900 text-white font-black flex items-center justify-center text-sm rounded-full tracking-wider">
                                        DP
                                    </div>
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-900 text-base group-hover:text-teal-700 transition-colors">Dimas Pratama, S.M.</h4>
                                    <p class="text-xs text-slate-500 font-medium">Tim Kerja Statistik Sosial • Pegawai BPS</p>
                                </div>
                            </div>
                            <div class="hidden sm:flex text-amber-400 text-xs gap-0.5" title="Kepuasan Pelayanan">
                                ★★★★★
                            </div>
                        </div>

                        <blockquote class="text-slate-600 text-xs sm:text-sm leading-relaxed italic pt-2">
                            "Pengajuan ATK untuk tim survei lapangan kini sangat praktis tanpa birokrasi kertas. Kebutuhan logistik langsung terpenuhi, koordinasi lancar, dan kerja jadi jauh lebih produktif!"
                        </blockquote>
                    </div>

                    <div class="pt-5 mt-5 flex items-center justify-between text-[11px] text-slate-400 border-t border-slate-100">
                        <span class="font-medium text-slate-500">Fungsi Statistik Sosial</span>
                        <span class="text-emerald-700 font-semibold font-mono bg-emerald-50 border border-emerald-100 px-2.5 py-0.5 rounded-full">Unit Lapangan</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 6. INTERACTIVE REAL-TIME CATALOG (Alpine.js Filter & Search with Marketplace Product Cards) -->
    <section id="katalog" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 sm:py-28" x-data="{
        searchQuery: '',
        selectedCategory: 'all',
        displayLimit: 24,
        selectedProduct: null,
        items: {{ Js::from($barangContoh) }},
        get allFiltered() {
            const query = this.searchQuery.trim().toLowerCase();
            return this.items.filter(item => {
                const matchesCat = this.selectedCategory === 'all' || item.kategori_id == this.selectedCategory;
                if (!matchesCat) return false;
                if (!query) return true;
                const nama = (item.nama_barang || '').toLowerCase();
                const kode = (item.kode_barang || '').toLowerCase();
                const satuan = (item.satuan || '').toLowerCase();
                const rak = (item.lokasi_rak || '').toLowerCase();
                const katNama = (item.kategori?.nama_kategori || '').toLowerCase();
                return nama.includes(query) || kode.includes(query) || satuan.includes(query) || rak.includes(query) || katNama.includes(query);
            });
        },
        get filteredItems() {
            return this.allFiltered.slice(0, this.displayLimit);
        },
        selectCategory(catId) {
            this.selectedCategory = catId;
            this.displayLimit = 24;
        },
        resetFilters() {
            this.searchQuery = '';
            this.selectedCategory = 'all';
            this.displayLimit = 24;
        },
        loadMore() {
            this.displayLimit += 24;
        },
        showAll() {
            this.displayLimit = this.allFiltered.length;
        }
    }"
    @filter-kategori.window="selectCategory($event.detail); $el.scrollIntoView({ behavior: 'smooth' })">
        <div class="text-center max-w-2xl mx-auto mb-12">
            <span class="text-xs font-bold tracking-widest uppercase text-teal-600 block mb-2 transition-transform duration-300 hover:scale-105 cursor-default">KATALOG LENGKAP</span>
            <h2 class="font-serif text-3xl sm:text-4xl text-slate-900 tracking-tight">Eksplorasi Barang Persediaan</h2>
            <p class="text-sm text-slate-500 mt-2">Cari dan ajukan barang persediaan secara mandiri dengan data ketersediaan real-time.</p>
        </div>

        <!-- Filter & Search Toolbar (Crisp White Card with Focus Glow) -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/90 shadow-sm hover:shadow-md transition-shadow duration-300 mb-8 space-y-4">
            <!-- Search Input with Interactive Glow Focus & Clear Button -->
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="text"
                       x-model="searchQuery"
                       @input="displayLimit = 24"
                       placeholder="Ketik nama atau kode barang (contoh: Kertas, Spidol, Toner, Map, Tinta, Dus)..."
                       class="w-full pl-11 pr-10 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm focus:outline-none focus:ring-4 focus:ring-teal-500/20 focus:border-teal-500 focus:bg-white transition duration-200">
                <button type="button" 
                        x-show="searchQuery.length > 0" 
                        @click="searchQuery = ''; displayLimit = 24" 
                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 transition-colors"
                        title="Hapus pencarian">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Category Filter Pills with Smooth Hover Scale -->
            <div class="flex items-center gap-2 overflow-x-auto pb-1 text-xs">
                <button type="button" 
                        @click="selectCategory('all')" 
                        :class="selectedCategory === 'all' ? 'bg-slate-900 text-white font-bold shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200 hover:text-slate-900'"
                        class="px-4 py-2 rounded-full whitespace-nowrap transition-all duration-200 transform hover:scale-105 active:scale-95 cursor-pointer">
                    Semua Kategori ({{ $totalBarang }})
                </button>
                @foreach($kategoriList as $kat)
                    <button type="button" 
                            @click="selectCategory('{{ $kat->id }}')" 
                            :class="selectedCategory === '{{ $kat->id }}' ? 'bg-slate-900 text-white font-bold shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200 hover:text-slate-900'"
                            class="px-4 py-2 rounded-full whitespace-nowrap transition-all duration-200 transform hover:scale-105 active:scale-95 cursor-pointer">
                        {{ $kat->nama_kategori }} ({{ $kat->barang_count }})
                    </button>
                @endforeach
            </div>
        </div>

        <!-- Real-time Filter Feedback Bar -->
        <div class="flex flex-wrap items-center justify-between gap-3 mb-6 px-1 text-xs text-slate-500">
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 font-medium text-slate-700 bg-slate-100 px-3 py-1 rounded-full">
                    <span class="w-2 h-2 rounded-full bg-teal-500 animate-pulse"></span>
                    <span>Menampilkan <strong class="text-slate-900" x-text="filteredItems.length"></strong> dari <strong class="text-slate-900" x-text="allFiltered.length"></strong> barang yang cocok</span>
                </span>
                <span x-show="selectedCategory !== 'all' || searchQuery !== ''" class="text-slate-400">|</span>
                <button x-show="selectedCategory !== 'all' || searchQuery !== ''" 
                        type="button" 
                        @click="resetFilters()" 
                        class="text-teal-700 hover:text-teal-900 font-semibold hover:underline inline-flex items-center gap-1 cursor-pointer">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    <span>Reset Filter</span>
                </button>
            </div>
            <div class="text-[11px] text-slate-400 hidden sm:block">
                <span>Total Basis Data: <strong class="text-slate-600">{{ $totalBarang }}</strong> barang persediaan BPS</span>
            </div>
        </div>

        <!-- Catalog Items Grid ala Marketplace (Product Thumbnail + Stock Badge + Quick Detail) -->
        <div x-show="allFiltered.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
            <template x-for="item in filteredItems" :key="item.id">
                <div @click="selectedProduct = item" class="group bg-white rounded-3xl border border-slate-200/90 overflow-hidden shadow-sm hover:shadow-xl hover:border-teal-400 hover:-translate-y-1.5 transition-all duration-300 flex flex-col justify-between cursor-pointer">
                    <!-- Marketplace Product Thumbnail Frame -->
                    <div>
                        <div class="relative h-44 sm:h-48 w-full bg-slate-50/90 border-b border-slate-100 overflow-hidden flex items-center justify-center">
                            <img :src="item.foto_url"
                                 :alt="item.nama_barang"
                                 loading="lazy"
                                 :class="item.has_real_foto ? 'w-full h-full object-cover group-hover:scale-105' : 'w-24 h-24 object-contain opacity-90 group-hover:scale-110'"
                                 class="transition-transform duration-500 ease-out">

                            <!-- Badge Kategori Pojok Kiri Atas -->
                            <div class="absolute top-3 left-3 max-w-[60%]">
                                <span class="inline-block truncate bg-white/90 backdrop-blur-sm text-slate-700 text-[10px] font-bold px-2.5 py-1 rounded-full shadow-2xs border border-slate-200/70"
                                      x-text="item.kategori ? item.kategori.nama_kategori : 'ATK'"></span>
                            </div>

                            <!-- Badge Status Stok Pojok Kanan Atas -->
                            <div class="absolute top-3 right-3">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-extrabold backdrop-blur-sm shadow-2xs border"
                                      :class="item.stok_aktif > (item.stok_minimum || 0)
                                          ? 'bg-emerald-50/95 text-emerald-700 border-emerald-200/80'
                                          : (item.stok_aktif > 0
                                              ? 'bg-amber-50/95 text-amber-700 border-amber-200/80'
                                              : 'bg-rose-50/95 text-rose-700 border-rose-200/80')">
                                    <span class="w-1.5 h-1.5 rounded-full"
                                          :class="item.stok_aktif > (item.stok_minimum || 0) ? 'bg-emerald-500' : (item.stok_aktif > 0 ? 'bg-amber-500' : 'bg-rose-500')"></span>
                                    <span x-text="item.stok_aktif > 0 ? 'Tersedia' : 'Kosong'"></span>
                                </span>
                            </div>
                        </div>

                        <!-- Card Content Info -->
                        <div class="p-4 sm:p-5 pb-3">
                            <div class="flex items-center justify-between text-[11px] mb-1.5">
                                <span class="font-mono font-semibold text-teal-800 bg-teal-50 group-hover:bg-teal-100 px-2 py-0.5 rounded-md transition-colors" x-text="item.kode_barang"></span>
                                <span class="text-slate-400 font-medium" x-text="item.satuan"></span>
                            </div>
                            <h4 class="font-bold text-sm text-slate-900 group-hover:text-teal-700 transition-colors line-clamp-2 leading-snug min-h-[2.5rem]" x-text="item.nama_barang"></h4>
                            <template x-if="item.lokasi_rak">
                                <p class="text-[11px] text-slate-400 font-mono mt-1.5 flex items-center gap-1">
                                    <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                    <span class="truncate" x-text="'Rak ' + item.lokasi_rak"></span>
                                </p>
                            </template>
                        </div>
                    </div>

                    <!-- Card Footer Action -->
                    <div class="px-4 sm:px-5 pb-4 sm:pb-5 pt-3 border-t border-slate-100 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] text-slate-400 uppercase font-semibold block">Sisa Stok</span>
                            <span class="text-xs font-extrabold flex items-center gap-1"
                                  :class="item.stok_aktif > (item.stok_minimum || 0) ? 'text-emerald-600' : (item.stok_aktif > 0 ? 'text-amber-600' : 'text-rose-600')"
                                  x-text="item.stok_aktif + ' ' + item.satuan"></span>
                        </div>
                        <a href="{{ auth()->check() ? route('barang.index') : route('login') }}"
                           @click.stop
                           class="group/btn rounded-full bg-brand-lime hover:bg-brand-lime-hover text-slate-950 font-bold px-3.5 py-1.5 text-[11px] transition-all duration-200 transform hover:scale-105 hover:shadow-glow-lime inline-flex items-center gap-1 shadow-2xs">
                            <span>Ajukan</span>
                            <svg class="w-3 h-3 transition-transform duration-200 group-hover/btn:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>
            </template>
        </div>

        <!-- Quick Product Detail Modal Ala Marketplace -->
        <div x-show="selectedProduct"
             x-transition.opacity
             @keydown.escape.window="selectedProduct = null"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm"
             style="display: none;">
            <div @click.away="selectedProduct = null" class="bg-white rounded-3xl max-w-lg w-full overflow-hidden shadow-2xl border border-slate-200">
                <template x-if="selectedProduct">
                    <div>
                        <div class="relative h-64 bg-slate-50 border-b border-slate-100 flex items-center justify-center p-4">
                            <img :src="selectedProduct.foto_url"
                                 :alt="selectedProduct.nama_barang"
                                 :class="selectedProduct.has_real_foto ? 'max-h-full w-auto rounded-2xl object-contain shadow-xs' : 'w-32 h-32 object-contain'">
                            <button type="button" @click="selectedProduct = null"
                                    class="absolute top-4 right-4 w-8 h-8 rounded-full bg-white/90 hover:bg-white text-slate-500 hover:text-slate-900 shadow-sm flex items-center justify-center border border-slate-200 cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="flex items-center justify-between gap-2">
                                <span class="font-mono text-xs font-bold text-teal-800 bg-teal-50 px-2.5 py-1 rounded-lg" x-text="selectedProduct.kode_barang"></span>
                                <span class="text-xs font-semibold text-slate-500 bg-slate-100 px-2.5 py-1 rounded-lg" x-text="selectedProduct.kategori ? selectedProduct.kategori.nama_kategori : 'Umum'"></span>
                            </div>
                            <div>
                                <h3 class="text-lg font-extrabold text-slate-900 leading-snug" x-text="selectedProduct.nama_barang"></h3>
                                <p x-show="selectedProduct.keterangan" class="text-xs text-slate-500 mt-1.5 leading-relaxed" x-text="selectedProduct.keterangan"></p>
                            </div>
                            <div class="grid grid-cols-2 gap-3 p-3.5 rounded-2xl bg-slate-50 border border-slate-100 text-xs">
                                <div>
                                    <span class="text-[10px] uppercase font-bold text-slate-400 block">Stok di Gudang</span>
                                    <span class="text-sm font-black mt-0.5 block"
                                          :class="selectedProduct.stok_aktif > 0 ? 'text-emerald-600' : 'text-rose-600'"
                                          x-text="selectedProduct.stok_aktif + ' ' + selectedProduct.satuan"></span>
                                </div>
                                <div>
                                    <span class="text-[10px] uppercase font-bold text-slate-400 block">Lokasi Rak</span>
                                    <span class="text-sm font-bold text-slate-700 mt-0.5 block" x-text="selectedProduct.lokasi_rak || 'Gudang Persediaan'"></span>
                                </div>
                            </div>
                            <div class="flex items-center justify-end gap-2.5 pt-2">
                                <button type="button" @click="selectedProduct = null" class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition cursor-pointer">
                                    Tutup
                                </button>
                                <a href="{{ auth()->check() ? route('permintaan') : route('login') }}" class="px-5 py-2.5 rounded-xl text-xs font-extrabold bg-brand-lime hover:bg-brand-lime-hover text-slate-950 shadow-sm transition inline-flex items-center gap-1.5">
                                    <span>Minta Barang Ini</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Empty State Card when 0 items match -->
        <div x-show="allFiltered.length === 0" class="bg-white rounded-3xl border border-dashed border-slate-300 p-12 text-center max-w-lg mx-auto">
            <div class="w-16 h-16 mx-auto mb-4 bg-slate-100 rounded-2xl flex items-center justify-center text-slate-400">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h3 class="font-bold text-slate-800 text-lg mb-1">Barang Tidak Ditemukan</h3>
            <p class="text-xs text-slate-500 mb-6">Tidak ada barang persediaan yang cocok dengan kata kunci atau filter kategori yang dipilih.</p>
            <button type="button" 
                    @click="resetFilters()" 
                    class="rounded-full bg-slate-900 hover:bg-slate-800 text-white font-semibold px-5 py-2.5 text-xs transition-all shadow-sm cursor-pointer">
                Tampilkan Semua Barang ({{ $totalBarang }})
            </button>
        </div>

        <!-- Load More / Show All Controls (when filtered count > displayLimit) -->
        <div x-show="allFiltered.length > displayLimit" class="text-center mt-10 space-y-3">
            <div class="flex items-center justify-center gap-3">
                <button type="button" 
                        @click="loadMore()" 
                        class="rounded-full bg-white hover:bg-slate-50 border border-slate-300 hover:border-teal-500 text-slate-800 hover:text-teal-700 font-bold px-6 py-2.5 text-xs transition-all duration-200 shadow-xs cursor-pointer inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    <span>Muat Lebih Banyak (+24 Barang)</span>
                </button>
                <button type="button" 
                        @click="showAll()" 
                        class="rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold px-5 py-2.5 text-xs transition-colors cursor-pointer">
                    <span>Tampilkan Semua (<span x-text="allFiltered.length"></span>)</span>
                </button>
            </div>
            <p class="text-[11px] text-slate-400">
                Menampilkan <span x-text="filteredItems.length"></span> dari <span x-text="allFiltered.length"></span> item barang yang cocok.
            </p>
        </div>

        <!-- Full Catalog Access Link -->
        <div class="text-center mt-10">
            <p class="text-xs text-slate-500">
                Menampilkan cuplikan barang persediaan. Untuk memesan kuota dan melihat seluruh {{ $totalBarang }} item persediaan, silakan 
                <a href="{{ route('login') }}" class="font-bold text-teal-700 hover:text-slate-950 underline transition-colors">Masuk ke Sistem SIPABIS</a>.
            </p>
        </div>
    </section>

</x-public-layout>