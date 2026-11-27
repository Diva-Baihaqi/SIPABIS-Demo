<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} | SIPABIS BPS Kabupaten Cirebon</title>

    <!-- Google Fonts Plus Jakarta Sans & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">

    <style>
        [x-cloak] { display: none !important; }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="h-full flex overflow-hidden font-sans text-slate-800 bg-slate-100 selection:bg-bps-orange-500 selection:text-white" x-data="{ sidebarOpen: false }">

    <!-- Mobile Sidebar Backdrop -->
    <div x-show="sidebarOpen" 
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-sm lg:hidden" 
         @click="sidebarOpen = false" 
         style="display: none;"></div>

    <!-- Sidebar Navigation -->
    <aside class="fixed inset-y-0 left-0 z-50 w-72 bg-gradient-to-b from-bps-navy-800 via-bps-navy-700 to-bps-navy-900 text-white flex flex-col transition-transform duration-300 ease-in-out lg:static lg:translate-x-0 shadow-2xl border-r border-bps-navy-700/80"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">
        
        <!-- Brand Header -->
        <div class="h-20 flex items-center px-6 bg-bps-navy-900/80 border-b border-bps-navy-600/50 justify-between">
            <a href="{{ url('/dashboard') }}" class="flex items-center gap-3 group">
                <x-bps-logo size="sm" />
                <div class="flex flex-col">
                    <h1 class="font-extrabold text-lg tracking-wider text-white group-hover:text-bps-orange-400 transition">SIPABIS</h1>
                    <p class="text-[10px] text-bps-navy-200 uppercase font-semibold tracking-wider">BPS Kab. Cirebon</p>
                </div>
            </a>
            <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white p-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 overflow-y-auto px-4 py-6 space-y-1.5 text-sm font-medium">
            <p class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">Utama</p>
            
            <a href="{{ url('/dashboard') }}" 
               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition duration-150 {{ request()->is('*/dashboard') || request()->is('dashboard') ? 'bg-white/15 text-white font-bold border-l-4 border-bps-orange-400 pl-2.5' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-5 h-5 text-bps-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('barang.index') }}" 
               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition duration-150 {{ request()->is('barang*') ? 'bg-white/15 text-white font-bold border-l-4 border-bps-orange-400 pl-2.5' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                <span>Katalog Persediaan</span>
            </a>

            <!-- Menu Pegawai & Pimpinan: Permintaan ATK -->
            @if(auth()->check() && (auth()->user()->isPegawai() || auth()->user()->isPimpinan()))
                <p class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400 mt-6 mb-2">Layanan ATK Pegawai</p>
                <a href="{{ route('pegawai.permintaan.index') }}" 
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition duration-150 {{ (request()->is('pegawai/permintaan*') && !request()->is('pegawai/permintaan/create*')) ? 'bg-white/15 text-white font-bold border-l-4 border-bps-orange-400 pl-2.5' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-5 h-5 text-bps-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    <span>Permintaan ATK Saya</span>
                </a>

                <a href="{{ route('pegawai.permintaan.create') }}" 
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition duration-150 {{ request()->is('pegawai/permintaan/create') ? 'bg-white/15 text-white font-bold border-l-4 border-bps-orange-400 pl-2.5' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Ajukan ATK Baru</span>
                </a>

                <a href="{{ route('usulan-barang.index') }}" 
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition duration-150 {{ request()->is('usulan-barang*') ? 'bg-white/15 text-white font-bold border-l-4 border-bps-orange-400 pl-2.5' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-5 h-5 text-bps-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Usulan Barang Baru</span>
                </a>

                @if(auth()->user()->ruangan_id)
                    <a href="{{ route('kartu-kendali.ruangan', auth()->user()->ruangan_id) }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition duration-150 {{ request()->is('kartu-kendali*') ? 'bg-white/15 text-white font-bold border-l-4 border-bps-orange-400 pl-2.5' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                        <svg class="w-5 h-5 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Buku Kendali Ruangan</span>
                    </a>
                @endif
            @endif

            <!-- Menu Khusus Admin & Pimpinan -->
            @if(auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isPimpinan()))
                <p class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400 mt-6 mb-2">Manajemen Persediaan</p>

                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.permintaan.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition duration-150 {{ request()->is('admin/permintaan*') ? 'bg-white/15 text-white font-bold border-l-4 border-bps-orange-400 pl-2.5' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                        <svg class="w-5 h-5 text-bps-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        <span>Verifikasi Permintaan</span>
                    </a>

                    <a href="{{ route('admin.barang-masuk.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition duration-150 {{ request()->is('admin/barang-masuk*') ? 'bg-white/15 text-white font-bold border-l-4 border-bps-orange-400 pl-2.5' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                        <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        <span>Penerimaan Barang</span>
                    </a>

                    <a href="{{ route('admin.kategori.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition duration-150 {{ request()->is('admin/kategori*') ? 'bg-white/15 text-white font-bold border-l-4 border-bps-orange-400 pl-2.5' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                        <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                        <span>Kategori Barang</span>
                    </a>
                @endif

                <a href="{{ route('usulan-barang.index') }}" 
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition duration-150 {{ request()->is('usulan-barang*') ? 'bg-white/15 text-white font-bold border-l-4 border-bps-orange-400 pl-2.5' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-5 h-5 text-bps-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Usulan Barang Pegawai</span>
                </a>

                <a href="{{ route('kartu-kendali.index') }}" 
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition duration-150 {{ request()->is('kartu-kendali*') ? 'bg-white/15 text-white font-bold border-l-4 border-bps-orange-400 pl-2.5' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-5 h-5 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Buku Kartu Kendali</span>
                </a>

                <a href="{{ route('stok-opname.index') }}" 
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition duration-150 {{ request()->is('stok-opname*') ? 'bg-white/15 text-white font-bold border-l-4 border-bps-orange-400 pl-2.5' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-5 h-5 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    <span>Stock Opname (BASO)</span>
                </a>
            @endif

            <!-- Kategori: Administrasi Sistem (Hanya Admin) -->
            @if(auth()->check() && auth()->user()->isAdmin())
                <p class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400 mt-6 mb-2">Administrasi</p>

                <a href="{{ route('admin.users.index') }}" 
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition duration-150 {{ request()->is('admin/users*') ? 'bg-white/15 text-white font-bold border-l-4 border-bps-orange-400 pl-2.5' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <span>Manajemen Pengguna</span>
                </a>

                <a href="{{ route('admin.ai.index') }}" 
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition duration-150 {{ request()->is('admin/ai*') ? 'bg-white/15 text-white font-bold border-l-4 border-bps-orange-400 pl-2.5' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-5 h-5 text-brand-lime" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/></svg>
                    <span>Manajemen AI</span>
                </a>
            @endif

            <!-- Kategori: Lainnya (Alur Pengguna & Profil) -->
            <p class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400 mt-6 mb-2">Lainnya</p>

            <a href="{{ route('alur-pengguna.index') }}" 
               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition duration-150 {{ request()->is('alur-pengguna*') ? 'bg-white/15 text-white font-bold border-l-4 border-bps-orange-400 pl-2.5' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-5 h-5 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                <span>Alur Pengguna</span>
            </a>

            <a href="{{ route('profil.index') }}" 
               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition duration-150 {{ request()->is('profil*') ? 'bg-white/15 text-white font-bold border-l-4 border-bps-orange-400 pl-2.5' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <span>Profil Saya</span>
            </a>

            <!-- Portal Publik Link -->
            <p class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400 mt-6 mb-2">Akses Cepat</p>
            <a href="{{ url('/') }}" target="_blank"
               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-slate-300 hover:bg-white/10 hover:text-white transition duration-150">
                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                <span>Halaman Depan</span>
            </a>
        </nav>

        <!-- Current User Profile Footer -->
        @auth
        <div class="p-4 bg-bps-navy-900/90 border-t border-bps-navy-600/50 flex items-center justify-between">
            <a href="{{ route('profil.index') }}" class="flex items-center gap-3 min-w-0 group hover:opacity-90 transition">
                <div class="w-9 h-9 rounded-xl overflow-hidden border border-white/20 bg-slate-800 flex items-center justify-center shrink-0 shadow-sm group-hover:scale-105 transition">
                    <img src="{{ auth()->user()->foto_url }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover sidebar-user-avatar">
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-bold text-white truncate group-hover:text-bps-orange-400 transition sidebar-user-name">{{ auth()->user()->name }}</p>
                    <div class="flex items-center gap-1.5 mt-0.5">
                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                        <span class="text-[10px] text-slate-300 uppercase tracking-wider font-semibold font-mono">{{ auth()->user()->role }}</span>
                    </div>
                </div>
            </a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" title="Keluar dari Sistem" class="p-2 text-slate-400 hover:text-rose-400 hover:bg-white/10 rounded-xl transition cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                </button>
            </form>
        </div>
        @endauth
    </aside>

    <!-- Main Content Wrapper -->
    <div class="flex-1 flex flex-col h-full overflow-hidden">
        
        <!-- Top Navbar -->
        <header class="h-20 bg-white border-b border-slate-200/80 px-6 flex items-center justify-between shrink-0 shadow-sm z-30">
            <div class="flex items-center gap-4">
                <button @click="sidebarOpen = true" class="lg:hidden p-2 text-slate-600 hover:bg-slate-100 rounded-xl focus:outline-none" aria-label="Open Sidebar">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <div>
                    <h2 class="text-lg font-extrabold text-slate-900 tracking-tight">{{ $header ?? $title ?? 'Sistem Informasi Persediaan' }}</h2>
                    <p class="text-xs text-slate-500 font-medium">BPS Kabupaten Cirebon • Satker 428646</p>
                </div>
            </div>

            @php
                $nowWib = \Carbon\Carbon::now('Asia/Jakarta')->locale('id');
                $unitKerjaNama = auth()->check() ? (auth()->user()->ruangan?->nama_ruangan ?? 'Subbag Umum') : 'Subbag Umum';
            @endphp

            <div class="flex items-center gap-3 shrink-0">
                <!-- Kartu Waktu & Kalender Realtime -->
                <div x-data="topbarClock({{ $nowWib->timestamp * 1000 }})"
                     class="hidden sm:flex items-center gap-3 px-3.5 py-1.5 rounded-2xl bg-slate-50/90 border border-slate-200/90 shadow-sm hover:border-bps-blue-500/30 transition-colors">
                    <div class="w-9 h-9 rounded-xl bg-bps-blue-50 border border-bps-blue-100 text-bps-blue-600 flex items-center justify-center shrink-0">
                        <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="flex flex-col leading-tight">
                        <span x-text="dateText" class="text-[11px] font-semibold text-slate-500 whitespace-nowrap">{{ $nowWib->translatedFormat('l, d F Y') }}</span>
                        <div class="flex items-center gap-1.5 mt-0.5 whitespace-nowrap">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse shrink-0" aria-hidden="true"></span>
                            <span x-text="timeText" class="text-xs font-extrabold text-bps-navy-800 font-mono tabular-nums tracking-tight">{{ $nowWib->format('H:i:s') }}</span>
                            <span class="text-[10px] font-bold text-bps-blue-600 uppercase tracking-wide">WIB</span>
                        </div>
                    </div>
                </div>

                @auth
                <!-- Kartu Unit Kerja -->
                <div class="hidden md:flex items-center gap-3 px-3.5 py-1.5 rounded-2xl bg-gradient-to-br from-white to-bps-navy-50/60 border border-bps-navy-100/90 shadow-sm hover:border-bps-navy-200 transition-colors"
                     title="Unit Kerja: {{ $unitKerjaNama }}">
                    <div class="w-9 h-9 rounded-xl bg-bps-navy-600 text-white shadow-sm shadow-bps-navy-600/20 flex items-center justify-center shrink-0">
                        <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <div class="flex flex-col leading-tight min-w-0">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Unit Kerja</span>
                        <span class="text-xs font-extrabold text-bps-navy-900 max-w-[200px] truncate mt-0.5">{{ $unitKerjaNama }}</span>
                    </div>
                </div>
                @endauth
            </div>
        </header>

        <!-- Main Scrollable Area -->
        <main class="flex-1 overflow-y-auto p-6 md:p-8 bg-slate-100/70">
            <!-- Flash Notification Messages -->
            @if(session('success'))
                <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 flex items-center justify-between text-emerald-800 shadow-sm" x-data="{ show: true }" x-show="show">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span class="text-sm font-semibold">{{ session('success') }}</span>
                    </div>
                    <button @click="show = false" class="text-emerald-600 hover:text-emerald-800 p-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 flex items-center justify-between text-rose-800 shadow-sm" x-data="{ show: true }" x-show="show">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="text-sm font-semibold">{{ session('error') }}</span>
                    </div>
                    <button @click="show = false" class="text-rose-600 hover:text-rose-800 p-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            @endif

            {{ $slot }}
        </main>
    </div>

    <!-- SIPABIS AI Virtual Assistant Floating Widget -->
    <x-chatbot-widget />

    <!-- SIPABIS Modern Animated Confirmation Modal -->
    <x-confirm-modal />

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('topbarClock', (serverTimestampMs) => {
                const dateFormatter = new Intl.DateTimeFormat('id-ID', {
                    timeZone: 'Asia/Jakarta',
                    weekday: 'long',
                    day: 'numeric',
                    month: 'long',
                    year: 'numeric',
                });
                const timeFormatter = new Intl.DateTimeFormat('en-GB', {
                    timeZone: 'Asia/Jakarta',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                    hourCycle: 'h23',
                });

                return {
                    offsetMs: Number(serverTimestampMs) - Date.now(),
                    dateText: '',
                    timeText: '',
                    timerId: null,

                    init() {
                        this.updateClock();
                        this.timerId = setInterval(() => this.updateClock(), 1000);
                    },

                    destroy() {
                        if (this.timerId) {
                            clearInterval(this.timerId);
                            this.timerId = null;
                        }
                    },

                    updateClock() {
                        const current = new Date(Date.now() + this.offsetMs);
                        this.dateText = dateFormatter.format(current);
                        this.timeText = timeFormatter.format(current);
                    }
                };
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
