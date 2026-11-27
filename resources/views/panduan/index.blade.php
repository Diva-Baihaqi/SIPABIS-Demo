@php
    $themes = [
        'pegawai' => [
            'tab_active' => 'border-teal-600 text-teal-700 bg-teal-50/40',
            'tab_icon_active' => 'bg-teal-100 text-teal-700',
            'tab_badge_active' => 'bg-teal-600 text-white',
            'tab_badge_idle' => 'bg-slate-100 text-slate-600',
            'roadmap_dot' => 'bg-teal-600 text-white',
            'roadmap_label' => 'text-teal-700 bg-teal-50 border-teal-200/80',
            'toc_active' => 'bg-teal-50/70 text-teal-900 border-teal-600 font-bold',
            'toc_num_active' => 'bg-teal-600 text-white',
            'toc_num_idle' => 'bg-slate-100 text-slate-500 group-hover:bg-teal-100 group-hover:text-teal-700',
            'chapter_eyebrow' => 'text-teal-700',
            'step_circle' => 'bg-teal-600 text-white ring-4 ring-teal-50',
            'accent_border' => 'border-teal-500',
            'nav_hover' => 'hover:border-teal-300 hover:bg-teal-50/30',
        ],
        'pimpinan' => [
            'tab_active' => 'border-emerald-700 text-emerald-800 bg-emerald-50/40',
            'tab_icon_active' => 'bg-emerald-100 text-emerald-700',
            'tab_badge_active' => 'bg-emerald-700 text-white',
            'tab_badge_idle' => 'bg-slate-100 text-slate-600',
            'roadmap_dot' => 'bg-emerald-700 text-white',
            'roadmap_label' => 'text-emerald-800 bg-emerald-50 border-emerald-200/80',
            'toc_active' => 'bg-emerald-50/70 text-emerald-950 border-emerald-700 font-bold',
            'toc_num_active' => 'bg-emerald-700 text-white',
            'toc_num_idle' => 'bg-slate-100 text-slate-500 group-hover:bg-emerald-100 group-hover:text-emerald-700',
            'chapter_eyebrow' => 'text-emerald-700',
            'step_circle' => 'bg-emerald-700 text-white ring-4 ring-emerald-50',
            'accent_border' => 'border-emerald-600',
            'nav_hover' => 'hover:border-emerald-300 hover:bg-emerald-50/30',
        ],
        'admin' => [
            'tab_active' => 'border-bps-navy-700 text-bps-navy-800 bg-bps-navy-50/50',
            'tab_icon_active' => 'bg-bps-navy-100 text-bps-navy-700',
            'tab_badge_active' => 'bg-bps-navy-700 text-white',
            'tab_badge_idle' => 'bg-slate-100 text-slate-600',
            'roadmap_dot' => 'bg-bps-navy-700 text-white',
            'roadmap_label' => 'text-bps-navy-800 bg-bps-navy-50 border-bps-navy-200/80',
            'toc_active' => 'bg-bps-navy-50/80 text-bps-navy-900 border-bps-navy-700 font-bold',
            'toc_num_active' => 'bg-bps-navy-700 text-white',
            'toc_num_idle' => 'bg-slate-100 text-slate-500 group-hover:bg-bps-navy-100 group-hover:text-bps-navy-700',
            'chapter_eyebrow' => 'text-bps-navy-700',
            'step_circle' => 'bg-bps-navy-700 text-white ring-4 ring-bps-navy-50',
            'accent_border' => 'border-bps-navy-600',
            'nav_hover' => 'hover:border-bps-navy-300 hover:bg-bps-navy-50/30',
        ],
    ];

    $tabMeta = [
        'pegawai' => ['num' => '1', 'short' => 'Pengaju ATK'],
        'pimpinan' => ['num' => '2', 'short' => 'Kepala Satker'],
        'admin' => ['num' => '3', 'short' => 'Pengelola Gudang'],
    ];

    $searchIndex = [];
    $initialSections = [];
    foreach ($guides as $tabKey => $guideData) {
        $searchIndex[$tabKey] = array_map(
            fn (array $chapter): string => $chapter['search_text'],
            $guideData['chapters']
        );
        $initialSections[$tabKey] = $guideData['chapters'][0]['id'] ?? 'faq';
    }

    $totalChapters = collect($guides)->sum(fn (array $g): int => count($g['chapters']));
@endphp

<x-public-layout>
    <x-slot:title>Buku Panduan Pengguna</x-slot:title>

    @push('styles')
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            .print-block {
                display: block !important;
            }
            .print-break-inside-avoid {
                break-inside: avoid;
            }
        }
    </style>
    @endpush

    <div x-data="{
        activeTab: '{{ $activeTab }}',
        activeSection: @js($initialSections),
        viewMode: 'single',
        searchQuery: '',
        openFaqKey: null,
        searchIndex: @js($searchIndex),
        switchTab(tab) {
            this.activeTab = tab;
            this.openFaqKey = null;
            const url = new URL(window.location.href);
            url.searchParams.set('tab', tab);
            window.history.replaceState({}, '', url);
        },
        selectSection(tab, sectionId) {
            this.activeSection[tab] = sectionId;
            if (this.searchQuery.trim().length > 0) {
                this.searchQuery = '';
            }
            if (this.viewMode === 'all') {
                this.viewMode = 'single';
            }
            this.$nextTick(() => {
                const workspace = document.getElementById('handbook-workspace');
                if (workspace) {
                    const top = workspace.getBoundingClientRect().top + window.scrollY - 96;
                    window.scrollTo({ top: Math.max(0, top), behavior: 'smooth' });
                }
            });
        },
        toggleViewMode() {
            this.viewMode = this.viewMode === 'single' ? 'all' : 'single';
            if (this.viewMode === 'all') {
                this.searchQuery = '';
            }
        },
        isSearching() {
            return this.searchQuery.trim().length > 0;
        },
        matchesChapter(tabKey, idx) {
            const q = this.searchQuery.trim().toLowerCase();
            if (!q) return true;
            const list = this.searchIndex[tabKey] || [];
            return (list[idx] || '').includes(q);
        },
        shouldShowChapter(tabKey, chapterId, idx) {
            if (this.isSearching()) {
                return this.matchesChapter(tabKey, idx);
            }
            if (this.viewMode === 'all') {
                return true;
            }
            return this.activeSection[tabKey] === chapterId;
        },
        shouldShowFaq(tabKey) {
            if (this.isSearching()) {
                return false;
            }
            return this.viewMode === 'all' || this.activeSection[tabKey] === 'faq';
        },
        countMatches(tabKey) {
            const q = this.searchQuery.trim().toLowerCase();
            const list = this.searchIndex[tabKey] || [];
            if (!q) return list.length;
            return list.filter(item => item.includes(q)).length;
        }
    }" class="bg-slate-50/80 min-h-screen">

        <!-- 1. HEADER TUNGGAL RINGKAS & ELEGAN -->
        <section class="relative overflow-hidden bg-bps-navy-900 text-white pt-28 sm:pt-32 pb-10 px-4 sm:px-6 lg:px-8">
            <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
                @if(file_exists(public_path('images/bps.jpg')))
                    <img src="{{ asset('images/bps.jpg') }}" alt="Kantor BPS Kabupaten Cirebon" class="w-full h-full object-cover object-center opacity-20">
                @endif
                <div class="absolute inset-0 bg-gradient-to-b from-bps-navy-900/95 via-slate-900/90 to-bps-navy-900"></div>
            </div>

            <div class="relative z-10 max-w-7xl mx-auto space-y-6">
                <!-- Clean Breadcrumb -->
                <div class="no-print flex items-center gap-2 text-xs text-slate-300 font-medium">
                    <a href="{{ url('/') }}" class="hover:text-brand-lime transition">Beranda</a>
                    <span class="text-slate-500">&rsaquo;</span>
                    <span class="text-white font-semibold">Buku Panduan Pengguna</span>
                </div>

                <!-- Headline & Search Row -->
                <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6">
                    <div class="max-w-xl space-y-2.5">
                        <h1 class="font-serif text-3xl sm:text-4xl lg:text-[2.65rem] text-white leading-tight tracking-tight">
                            Buku Panduan Penggunaan SIPABIS
                        </h1>
                        <p class="text-sm text-slate-300 leading-relaxed">
                            Panduan praktis langkah demi langkah mengelola dan mengajukan persediaan ATK BPS Kabupaten Cirebon. Pilih peran Anda lalu klik bab yang ingin dibaca.
                        </p>
                    </div>

                    <!-- Search + Download Book + Print Actions -->
                    <div class="no-print w-full lg:w-auto lg:min-w-[520px] xl:min-w-[560px] flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 shrink-0">
                        <div class="relative flex-1 sm:min-w-[220px]">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <input type="text"
                                   x-model="searchQuery"
                                   placeholder="Cari topik... (PSD-133, opname, API key)"
                                   aria-label="Cari topik buku panduan"
                                   class="w-full pl-10 pr-9 py-2.5 rounded-xl bg-white text-sm text-slate-900 placeholder-slate-400 border border-white/20 shadow-md focus:outline-none focus:ring-4 focus:ring-brand-lime/40 transition">
                            <button type="button"
                                    x-show="searchQuery.length > 0"
                                    x-cloak
                                    @click="searchQuery = ''"
                                    title="Hapus pencarian"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-rose-500 cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <div class="flex items-center gap-2 shrink-0">
                            @if($bookMeta['available'])
                                <a href="{{ route('panduan.buku.download') }}"
                                   download
                                   title="Unduh {{ $bookMeta['title'] }} ({{ $bookMeta['pages'] }} Halaman, {{ $bookMeta['format'] }}@if($bookMeta['size_label']), {{ $bookMeta['size_label'] }}@endif)"
                                   class="flex-1 sm:flex-none px-4 py-2.5 rounded-xl bg-brand-lime hover:bg-brand-lime-hover text-bps-navy-900 text-xs font-extrabold inline-flex items-center justify-center gap-2 shadow-glow-lime transition whitespace-nowrap">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <span>Unduh Buku</span>
                                    <span class="hidden xl:inline px-1.5 py-0.5 rounded-md bg-bps-navy-900/10 text-[10px] font-extrabold tracking-wide">PDF</span>
                                </a>
                            @endif

                            <button type="button"
                                    onclick="window.print()"
                                    class="flex-1 sm:flex-none px-3.5 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 text-white text-xs font-bold inline-flex items-center justify-center gap-2 transition whitespace-nowrap cursor-pointer">
                                <svg class="w-4 h-4 text-brand-lime shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                <span>Cetak</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 2. BILAH 3 TAB PERAN BERSIH & RAMPING (NON-STICKY, MENYATU DI BAWAH HEADER) -->
        <section class="no-print bg-white border-b border-slate-200/90 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-stretch overflow-x-auto gap-1 sm:gap-2 -mb-px" role="tablist" aria-label="Pilih Peran Buku Panduan">
                    @foreach(['pegawai', 'pimpinan', 'admin'] as $tabKey)
                        @php
                            $guideMeta = $guides[$tabKey];
                            $t = $themes[$tabKey];
                            $meta = $tabMeta[$tabKey];
                        @endphp
                        <button type="button"
                                role="tab"
                                :aria-selected="activeTab === '{{ $tabKey }}'"
                                @click="switchTab('{{ $tabKey }}')"
                                :class="activeTab === '{{ $tabKey }}' ? '{{ $t['tab_active'] }}' : 'border-transparent text-slate-600 hover:text-slate-900 hover:bg-slate-50/80'"
                                class="flex-1 sm:flex-none px-4 sm:px-6 py-4 border-b-[3px] transition-all duration-150 flex items-center gap-3 whitespace-nowrap cursor-pointer">
                            <span class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 transition-colors"
                                  :class="activeTab === '{{ $tabKey }}' ? '{{ $t['tab_icon_active'] }}' : 'bg-slate-100 text-slate-500'">
                                @if($tabKey === 'pegawai')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                @elseif($tabKey === 'pimpinan')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                @else
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                @endif
                            </span>
                            <div class="text-left">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block leading-none">Tab {{ $meta['num'] }} &bull; {{ $meta['short'] }}</span>
                                <span class="text-sm font-extrabold block mt-1 leading-none">{{ $guideMeta['tab_label'] }}</span>
                            </div>
                            <span class="ml-1 px-2 py-0.5 rounded-full text-[11px] font-bold transition-colors"
                                  :class="activeTab === '{{ $tabKey }}' ? '{{ $t['tab_badge_active'] }}' : '{{ $t['tab_badge_idle'] }}'">
                                <span x-text="countMatches('{{ $tabKey }}')">{{ count($guideMeta['chapters']) }}</span>
                            </span>
                        </button>
                    @endforeach

                    <!-- Right Info Counter (Desktop Only) -->
                    <div class="hidden lg:flex items-center ml-auto text-xs text-slate-400 font-medium pl-4">
                        <span>Total {{ $totalChapters }} Bab &bull; Bahasa Sehari-hari</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- 3. WORKSPACE BUKU DIGITAL PER PERAN -->
        <section id="handbook-workspace" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10 pb-20">
            @foreach(['pegawai', 'pimpinan', 'admin'] as $tabKey)
                @php
                    $guide = $guides[$tabKey];
                    $t = $themes[$tabKey];
                    $chapters = $guide['chapters'];
                    $chapterCount = count($chapters);
                @endphp

                <div x-show="activeTab === '{{ $tabKey }}'" x-cloak class="space-y-6">

                    <!-- A. Strip Pengantar & Peta Alur Ringkas (Terang, Ringan & Profesional) -->
                    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5 sm:p-6 space-y-5">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
                            <div class="space-y-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="px-2.5 py-0.5 rounded-md border {{ $t['roadmap_label'] }} text-[11px] font-bold">
                                        {{ $guide['role_badge'] }}
                                    </span>
                                    <span class="text-xs text-slate-400 font-medium">{{ $guide['read_time'] }}</span>
                                </div>
                                <h2 class="text-lg sm:text-xl font-extrabold text-slate-900 tracking-tight">{{ $guide['hero_title'] }}</h2>
                                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed max-w-3xl">{{ $guide['hero_desc'] }}</p>
                            </div>

                            <div class="no-print shrink-0 flex items-center gap-2">
                                <button type="button"
                                        @click="toggleViewMode()"
                                        class="px-3.5 py-2 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700 text-xs font-bold inline-flex items-center gap-1.5 transition cursor-pointer">
                                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                                    <span x-text="viewMode === 'single' ? 'Tampilkan Semua Bab' : 'Mode Fokus 1 Bab'">Tampilkan Semua Bab</span>
                                </button>
                            </div>
                        </div>

                        <!-- Minimal Horizontal Roadmap -->
                        <div class="pt-4 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                            @foreach($guide['roadmap'] as $idx => $node)
                                <div class="flex items-start gap-2.5">
                                    <span class="w-6 h-6 rounded-full {{ $t['roadmap_dot'] }} text-[11px] font-black flex items-center justify-center shrink-0 mt-0.5">
                                        {{ $node['step'] }}
                                    </span>
                                    <div class="min-w-0">
                                        <p class="text-xs font-bold text-slate-800 leading-snug">{{ $node['title'] }}</p>
                                        <p class="text-[11px] text-slate-500 leading-snug mt-0.5">{{ $node['desc'] }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- B. Pemilih Bab Mobile (Praktis untuk HP & Tablet) -->
                    <div class="no-print lg:hidden bg-white rounded-2xl border border-slate-200/80 shadow-sm p-4 space-y-2">
                        <label for="mobile-chapter-{{ $tabKey }}" class="text-[11px] font-bold uppercase tracking-wider text-slate-500 block">
                            Pilih Bab Panduan
                        </label>
                        <select id="mobile-chapter-{{ $tabKey }}"
                                :value="activeSection['{{ $tabKey }}']"
                                @change="selectSection('{{ $tabKey }}', $event.target.value)"
                                class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm font-semibold text-slate-800 py-2.5 px-3 focus:border-bps-blue-500 focus:ring-bps-blue-500/20">
                            @foreach($chapters as $chapter)
                                <option value="{{ $chapter['id'] }}">Bab {{ $chapter['number'] }} — {{ $chapter['title'] }}</option>
                            @endforeach
                            <option value="faq">Tanya Jawab Populer (FAQ)</option>
                        </select>
                    </div>

                    @if($bookMeta['available'])
                        <!-- B2. Kartu E-Book Ringkas Mobile & Tablet -->
                        <div class="no-print lg:hidden bg-gradient-to-br from-white via-white to-bps-navy-50/40 rounded-2xl border border-slate-200/90 shadow-sm p-4 flex items-center gap-3.5">
                            @if($bookMeta['cover_exists'])
                                <a href="{{ route('panduan.buku.preview') }}"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   class="shrink-0 block">
                                    <img src="{{ $bookMeta['cover_url'] }}"
                                         alt="Sampul {{ $bookMeta['title'] }}"
                                         width="52"
                                         height="74"
                                         loading="lazy"
                                         class="w-[52px] h-[74px] rounded-lg object-cover shadow-md ring-1 ring-slate-900/10">
                                </a>
                            @endif
                            <div class="min-w-0 flex-1 space-y-2">
                                <div>
                                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-bps-blue-600 block">E-Book Resmi</span>
                                    <p class="text-xs sm:text-sm font-extrabold text-slate-900 leading-snug">{{ $bookMeta['title'] }}</p>
                                    <p class="text-[11px] text-slate-500">
                                        {{ $bookMeta['pages'] }} Halaman &bull; {{ $bookMeta['format'] }}@if($bookMeta['size_label']) &bull; {{ $bookMeta['size_label'] }}@endif
                                    </p>
                                </div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <a href="{{ route('panduan.buku.download') }}"
                                       download
                                       class="px-3 py-1.5 rounded-lg bg-bps-navy-700 hover:bg-bps-navy-800 text-white text-[11px] font-bold inline-flex items-center gap-1.5 transition">
                                        <svg class="w-3.5 h-3.5 text-brand-lime shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                        <span>Unduh PDF</span>
                                    </a>
                                    <a href="{{ route('panduan.buku.preview') }}"
                                       target="_blank"
                                       rel="noopener noreferrer"
                                       class="px-3 py-1.5 rounded-lg border border-slate-200 hover:bg-slate-50 text-slate-700 text-[11px] font-semibold inline-flex items-center gap-1.5 transition">
                                        <span>Pratinjau</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- C. Tata Letak 2 Kolom Buku Digital -->
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-7 items-start">

                        <!-- Kolom Kiri: Daftar Bab + Kartu E-Book Resmi (Sticky Ramping di Desktop) -->
                        <aside class="no-print hidden lg:block lg:col-span-4 xl:col-span-3 sticky top-24 space-y-4">
                            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-4 space-y-3">
                                <div class="px-2 pb-2.5 border-b border-slate-100 flex items-center justify-between">
                                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Daftar Bab</span>
                                    <span class="text-[11px] font-bold text-slate-500">{{ $chapterCount }} Bab + FAQ</span>
                                </div>

                                <nav class="space-y-1 max-h-[40vh] overflow-y-auto pr-1" aria-label="Daftar bab {{ $guide['tab_label'] }}">
                                    @foreach($chapters as $chapterIdx => $chapter)
                                        <button type="button"
                                                x-show="matchesChapter('{{ $tabKey }}', {{ $chapterIdx }})"
                                                @click="selectSection('{{ $tabKey }}', '{{ $chapter['id'] }}')"
                                                :class="(viewMode === 'single' && !isSearching() && activeSection['{{ $tabKey }}'] === '{{ $chapter['id'] }}') ? '{{ $t['toc_active'] }}' : 'border-transparent text-slate-600 hover:bg-slate-50 hover:text-slate-900'"
                                                class="group w-full flex items-start gap-2.5 px-2.5 py-2 rounded-xl border-l-2 transition-all text-left cursor-pointer">
                                            <span class="w-6 h-6 rounded-md text-[11px] font-bold flex items-center justify-center shrink-0 transition-colors mt-0.5"
                                                  :class="(viewMode === 'single' && !isSearching() && activeSection['{{ $tabKey }}'] === '{{ $chapter['id'] }}') ? '{{ $t['toc_num_active'] }}' : '{{ $t['toc_num_idle'] }}'">
                                                {{ $chapter['number'] }}
                                            </span>
                                            <span class="text-xs leading-snug">{{ $chapter['title'] }}</span>
                                        </button>
                                    @endforeach

                                    <div class="pt-2 mt-2 border-t border-slate-100">
                                        <button type="button"
                                                @click="selectSection('{{ $tabKey }}', 'faq')"
                                                :class="(viewMode === 'single' && !isSearching() && activeSection['{{ $tabKey }}'] === 'faq') ? '{{ $t['toc_active'] }}' : 'border-transparent text-slate-600 hover:bg-slate-50 hover:text-slate-900'"
                                                class="group w-full flex items-center gap-2.5 px-2.5 py-2 rounded-xl border-l-2 transition-all text-left cursor-pointer">
                                            <span class="w-6 h-6 rounded-md text-[11px] font-bold flex items-center justify-center shrink-0 transition-colors"
                                                  :class="(viewMode === 'single' && !isSearching() && activeSection['{{ $tabKey }}'] === 'faq') ? '{{ $t['toc_num_active'] }}' : '{{ $t['toc_num_idle'] }}'">?</span>
                                            <span class="text-xs font-semibold">Tanya Jawab (FAQ)</span>
                                        </button>
                                    </div>
                                </nav>

                                <div class="pt-3 border-t border-slate-100">
                                    @auth
                                        <a href="{{ route('dashboard') }}" class="w-full py-2.5 px-3.5 rounded-xl bg-bps-navy-700 hover:bg-bps-navy-800 text-white text-xs font-bold flex items-center justify-center gap-1.5 transition">
                                            <span>Buka Dashboard Saya</span>
                                            <span>&rarr;</span>
                                        </a>
                                    @else
                                        <a href="{{ route('login') }}" class="w-full py-2.5 px-3.5 rounded-xl bg-bps-navy-700 hover:bg-bps-navy-800 text-white text-xs font-bold flex items-center justify-center gap-1.5 transition">
                                            <span>Masuk ke SIPABIS</span>
                                            <span>&rarr;</span>
                                        </a>
                                    @endauth
                                </div>
                            </div>

                            @if($bookMeta['available'])
                                <!-- Kartu Mini E-Book Resmi SIPABIS -->
                                <div class="bg-gradient-to-br from-white via-white to-bps-navy-50/40 rounded-2xl border border-slate-200/90 shadow-sm p-4 space-y-3.5">
                                    <div class="flex items-start gap-3">
                                        @if($bookMeta['cover_exists'])
                                            <a href="{{ route('panduan.buku.preview') }}"
                                               target="_blank"
                                               rel="noopener noreferrer"
                                               title="Pratinjau {{ $bookMeta['title'] }}"
                                               class="block shrink-0 group">
                                                <img src="{{ $bookMeta['cover_url'] }}"
                                                     alt="Sampul {{ $bookMeta['title'] }} BPS Kabupaten Cirebon"
                                                     width="56"
                                                     height="80"
                                                     loading="lazy"
                                                     class="w-14 h-20 rounded-lg object-cover shadow-md ring-1 ring-slate-900/10 group-hover:scale-[1.03] transition-transform duration-200">
                                            </a>
                                        @else
                                            <div class="w-14 h-20 rounded-lg bg-bps-navy-800 text-brand-lime flex items-center justify-center shrink-0 shadow-md">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                            </div>
                                        @endif

                                        <div class="min-w-0 space-y-1">
                                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-bps-blue-600 block">E-Book Resmi</span>
                                            <h3 class="text-xs font-extrabold text-slate-900 leading-snug">{{ $bookMeta['title'] }}</h3>
                                            <p class="text-[11px] text-slate-500 leading-relaxed">
                                                {{ $bookMeta['pages'] }} Halaman &bull; {{ $bookMeta['format'] }}@if($bookMeta['size_label'])<br>{{ $bookMeta['size_label'] }} &bull; {{ $bookMeta['edition'] }}@endif
                                            </p>
                                        </div>
                                    </div>

                                    <div class="space-y-2 pt-0.5">
                                        <a href="{{ route('panduan.buku.download') }}"
                                           download
                                           class="w-full py-2.5 px-3.5 rounded-xl bg-bps-navy-700 hover:bg-bps-navy-800 text-white text-xs font-bold flex items-center justify-center gap-2 transition">
                                            <svg class="w-3.5 h-3.5 text-brand-lime shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                            <span>Unduh PDF Lengkap</span>
                                        </a>
                                        <a href="{{ route('panduan.buku.preview') }}"
                                           target="_blank"
                                           rel="noopener noreferrer"
                                           class="w-full py-2 px-3.5 rounded-xl border border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-semibold flex items-center justify-center gap-1.5 transition">
                                            <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                            <span>Pratinjau di Browser</span>
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </aside>

                        <!-- Kolom Kanan: Kanvas Baca Lega & Tenang -->
                        <div class="lg:col-span-8 xl:col-span-9 min-w-0 space-y-6">

                            <!-- Info Banner Saat Pencarian Aktif -->
                            <div x-show="isSearching() && countMatches('{{ $tabKey }}') > 0"
                                 x-cloak
                                 class="no-print flex items-center justify-between gap-3 px-4 py-3 rounded-2xl bg-white border border-slate-200 text-xs text-slate-600 shadow-sm">
                                <span>
                                    Ditemukan <strong class="text-slate-900" x-text="countMatches('{{ $tabKey }}')"></strong> bab cocok untuk "<strong class="text-slate-900" x-text="searchQuery"></strong>" di {{ $guide['tab_label'] }}.
                                </span>
                                <button type="button" @click="searchQuery = ''" class="text-bps-blue-600 hover:underline font-bold cursor-pointer shrink-0">
                                    Reset Pencarian
                                </button>
                            </div>

                            <!-- Pesan Jika Pencarian Kosong di Tab Aktif -->
                            <div x-show="isSearching() && countMatches('{{ $tabKey }}') === 0"
                                 x-cloak
                                 class="bg-white rounded-3xl border border-slate-200/80 p-10 text-center space-y-3 shadow-sm">
                                <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                </div>
                                <h3 class="text-base font-bold text-slate-900">Topik "<span x-text="searchQuery"></span>" tidak ada di {{ $guide['tab_label'] }}</h3>
                                <p class="text-xs sm:text-sm text-slate-500 max-w-md mx-auto leading-relaxed">
                                    Periksa angka kecocokan di bilah tab peran lainnya di atas, atau hapus kata pencarian untuk kembali membaca bab panduan.
                                </p>
                                <button type="button" @click="searchQuery = ''" class="px-4 py-2 rounded-xl bg-bps-navy-700 text-white text-xs font-bold hover:bg-bps-navy-800 transition cursor-pointer">
                                    Kembali ke Panduan
                                </button>
                            </div>

                            <!-- Daftar Bab (Fokus 1 Bab atau Semua Bab / Print) -->
                            @foreach($chapters as $chapterIdx => $chapter)
                                @php
                                    $prevChapter = $chapterIdx > 0 ? $chapters[$chapterIdx - 1] : null;
                                    $nextChapter = $chapterIdx < ($chapterCount - 1) ? $chapters[$chapterIdx + 1] : null;
                                @endphp
                                <article id="{{ $chapter['id'] }}"
                                         x-show="shouldShowChapter('{{ $tabKey }}', '{{ $chapter['id'] }}', {{ $chapterIdx }})"
                                         x-cloak
                                         class="print-block print-break-inside-avoid bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 sm:p-9 lg:p-10 space-y-8">

                                    <!-- Header Bab Editorial -->
                                    <header class="space-y-4 pb-6 border-b border-slate-100">
                                        <div class="flex flex-wrap items-center justify-between gap-2 text-xs">
                                            <div class="flex items-center gap-2 font-bold">
                                                <span class="{{ $t['chapter_eyebrow'] }} uppercase tracking-wider">Bab {{ $chapter['number'] }}</span>
                                                <span class="text-slate-300">&bull;</span>
                                                <span class="text-slate-500 font-semibold">{{ $chapter['category'] }}</span>
                                            </div>
                                            <span class="text-[11px] font-medium text-slate-400">
                                                Halaman {{ $chapterIdx + 1 }} dari {{ $chapterCount }}
                                            </span>
                                        </div>

                                        <h3 class="text-xl sm:text-2xl lg:text-[1.65rem] font-extrabold text-slate-900 tracking-tight leading-snug">
                                            {{ $chapter['title'] }}
                                        </h3>

                                        <!-- Lokasi Menu Ringan -->
                                        <p class="text-xs text-slate-500 flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                            <span>Lokasi menu:</span>
                                            <strong class="text-slate-700 font-semibold">{{ $chapter['menu_path'] }}</strong>
                                        </p>

                                        <p class="text-sm sm:text-[15px] text-slate-600 leading-relaxed max-w-3xl">
                                            {{ $chapter['summary'] }}
                                        </p>

                                        <!-- Aksen Kapan Dipakai (Tanpa Kotak Berat) -->
                                        <div class="border-l-2 {{ $t['accent_border'] }} pl-4 py-0.5 text-xs sm:text-sm text-slate-600 leading-relaxed">
                                            <strong class="text-slate-800">Kapan dipakai?</strong> {{ $chapter['when_to_use'] }}
                                        </div>
                                    </header>

                                    <!-- Langkah-Langkah dengan Vertical Timeline Bersih -->
                                    <div class="space-y-5">
                                        <h4 class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Langkah-Langkah Praktis</h4>

                                        <ol class="relative space-y-6">
                                            @foreach($chapter['steps'] as $stepIdx => $step)
                                                <li class="relative flex items-start gap-4">
                                                    @if(!$loop->last)
                                                        <span class="absolute left-3.5 top-8 -bottom-6 w-px bg-slate-200" aria-hidden="true"></span>
                                                    @endif
                                                    <span class="relative z-10 w-7 h-7 rounded-full {{ $t['step_circle'] }} text-xs font-extrabold flex items-center justify-center shrink-0 mt-0.5">
                                                        {{ $stepIdx + 1 }}
                                                    </span>
                                                    <div class="space-y-1.5 pb-1 max-w-2xl">
                                                        <p class="text-sm sm:text-[15px] font-bold text-slate-900 leading-snug">{{ $step['title'] }}</p>
                                                        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">{{ $step['detail'] }}</p>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ol>
                                    </div>

                                    <!-- Kamus Warna Status (Daftar Rapi Berpemisah Halus) -->
                                    @if(!empty($chapter['statuses']))
                                        <div class="space-y-3 pt-2">
                                            <h4 class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Arti Warna Status di Aplikasi</h4>
                                            <div class="rounded-2xl border border-slate-200/80 divide-y divide-slate-100 overflow-hidden bg-slate-50/40">
                                                @foreach($chapter['statuses'] as $st)
                                                    <div class="px-4 py-3 flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4">
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full border text-[11px] font-bold w-fit shrink-0 {{ $st['class'] }}">
                                                            {{ $st['label'] }}
                                                        </span>
                                                        <p class="text-xs sm:text-[13px] text-slate-600 leading-relaxed">{{ $st['meaning'] }}</p>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Tips & Catatan Penting (Minimalist Left-Accent Callout) -->
                                    @if(!empty($chapter['tips']))
                                        <div class="space-y-3 pt-1">
                                            @foreach($chapter['tips'] as $tip)
                                                @if($tip['type'] === 'warning')
                                                    <div class="border-l-4 border-amber-500 bg-amber-50/60 rounded-r-2xl px-4 py-3.5 text-xs sm:text-[13px] text-amber-950 leading-relaxed">
                                                        <strong class="font-bold text-amber-900">Penting Diperhatikan:</strong> {{ $tip['text'] }}
                                                    </div>
                                                @else
                                                    <div class="border-l-4 border-emerald-500 bg-emerald-50/50 rounded-r-2xl px-4 py-3.5 text-xs sm:text-[13px] text-emerald-950 leading-relaxed">
                                                        <strong class="font-bold text-emerald-900">Tips Lapangan:</strong> {{ $tip['text'] }}
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    @endif

                                    <!-- Footer Navigasi Buku: Bab Sebelumnya / Bab Selanjutnya -->
                                    <footer x-show="viewMode === 'single' && !isSearching()"
                                            class="no-print pt-6 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        @if($prevChapter)
                                            <button type="button"
                                                    @click="selectSection('{{ $tabKey }}', '{{ $prevChapter['id'] }}')"
                                                    class="p-4 rounded-2xl border border-slate-200/90 {{ $t['nav_hover'] }} transition text-left space-y-1 cursor-pointer">
                                                <span class="text-[11px] font-bold text-slate-400 block">&larr; Bab Sebelumnya</span>
                                                <span class="text-xs sm:text-sm font-bold text-slate-800 block leading-snug">{{ $prevChapter['number'] }}. {{ $prevChapter['title'] }}</span>
                                            </button>
                                        @else
                                            <div class="hidden sm:block"></div>
                                        @endif

                                        @if($nextChapter)
                                            <button type="button"
                                                    @click="selectSection('{{ $tabKey }}', '{{ $nextChapter['id'] }}')"
                                                    class="p-4 rounded-2xl border border-slate-200/90 {{ $t['nav_hover'] }} transition text-left sm:text-right space-y-1 cursor-pointer">
                                                <span class="text-[11px] font-bold text-slate-400 block">Bab Selanjutnya &rarr;</span>
                                                <span class="text-xs sm:text-sm font-bold text-slate-800 block leading-snug">{{ $nextChapter['number'] }}. {{ $nextChapter['title'] }}</span>
                                            </button>
                                        @else
                                            <button type="button"
                                                    @click="selectSection('{{ $tabKey }}', 'faq')"
                                                    class="p-4 rounded-2xl border border-slate-200/90 {{ $t['nav_hover'] }} transition text-left sm:text-right space-y-1 cursor-pointer">
                                                <span class="text-[11px] font-bold text-slate-400 block">Bagian Berikutnya &rarr;</span>
                                                <span class="text-xs sm:text-sm font-bold text-slate-800 block leading-snug">Tanya Jawab Populer (FAQ)</span>
                                            </button>
                                        @endif
                                    </footer>
                                </article>
                            @endforeach

                            <!-- Bagian Tanya Jawab (FAQ) -->
                            <section id="faq-{{ $tabKey }}"
                                     x-show="shouldShowFaq('{{ $tabKey }}')"
                                     x-cloak
                                     class="print-block bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 sm:p-9 lg:p-10 space-y-6">
                                <div class="pb-5 border-b border-slate-100 space-y-1.5">
                                    <span class="text-xs font-extrabold uppercase tracking-wider {{ $t['chapter_eyebrow'] }} block">Kendala Sehari-hari</span>
                                    <h3 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">Tanya Jawab Populer ({{ $guide['tab_label'] }})</h3>
                                    <p class="text-xs sm:text-sm text-slate-500">Klik pertanyaan di bawah ini untuk melihat solusi praktisnya.</p>
                                </div>

                                <div class="divide-y divide-slate-100">
                                    @foreach($guide['faqs'] as $faqIdx => $faq)
                                        @php
                                            $faqKey = "{$tabKey}-{$faqIdx}";
                                        @endphp
                                        <div class="py-1">
                                            <button type="button"
                                                    @click="openFaqKey = (openFaqKey === '{{ $faqKey }}' ? null : '{{ $faqKey }}')"
                                                    class="w-full py-4 text-left flex items-center justify-between gap-4 group cursor-pointer">
                                                <span class="text-sm font-bold text-slate-800 group-hover:text-slate-950 leading-snug">{{ $faq['q'] }}</span>
                                                <svg class="w-4 h-4 text-slate-400 shrink-0 transition-transform duration-200"
                                                     :class="openFaqKey === '{{ $faqKey }}' ? 'rotate-180 text-bps-blue-600' : ''"
                                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                </svg>
                                            </button>
                                            <div x-show="openFaqKey === '{{ $faqKey }}'"
                                                 x-cloak
                                                 class="print-block pb-4 pr-6 text-xs sm:text-sm text-slate-600 leading-relaxed">
                                                {{ $faq['a'] }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                @if($chapterCount > 0)
                                    <div x-show="viewMode === 'single' && !isSearching()" class="no-print pt-5 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3">
                                        <button type="button"
                                                @click="selectSection('{{ $tabKey }}', '{{ $chapters[$chapterCount - 1]['id'] }}')"
                                                class="px-4 py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-xs font-bold text-slate-700 transition cursor-pointer">
                                            &larr; Kembali ke Bab {{ $chapters[$chapterCount - 1]['number'] }}
                                        </button>
                                        <div class="flex items-center gap-2 text-xs">
                                            <span class="text-slate-400">Baca peran lain:</span>
                                            @if($tabKey !== 'pegawai')
                                                <button type="button" @click="switchTab('pegawai')" class="font-bold text-teal-700 hover:underline cursor-pointer">Pegawai</button>
                                            @endif
                                            @if($tabKey !== 'pimpinan')
                                                <button type="button" @click="switchTab('pimpinan')" class="font-bold text-emerald-700 hover:underline cursor-pointer">Pimpinan</button>
                                            @endif
                                            @if($tabKey !== 'admin')
                                                <button type="button" @click="switchTab('admin')" class="font-bold text-bps-navy-700 hover:underline cursor-pointer">Admin</button>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </section>

                        </div>
                    </div>
                </div>
            @endforeach
        </section>

    </div>
</x-public-layout>
