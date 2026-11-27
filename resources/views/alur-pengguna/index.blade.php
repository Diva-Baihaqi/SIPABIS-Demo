<x-app-layout>
    <x-slot:title>Alur Pengguna - SIPABIS</x-slot:title>
    <x-slot:header>Diagram Alur Penggunaan SIPABIS</x-slot:header>

    <div class="space-y-6">

        <!-- Header Card: Identitas Alur Peran -->
        <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200/90 shadow-sm relative overflow-hidden flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2">
                <!-- Breadcrumb -->
                <nav class="flex items-center gap-2 text-xs text-slate-400 font-medium">
                    <a href="{{ route('dashboard') }}" class="hover:text-teal-700 transition">Dashboard</a>
                    <span>&rsaquo;</span>
                    <span class="text-slate-600">Lainnya</span>
                    <span>&rsaquo;</span>
                    <span class="text-slate-900 font-bold">Alur Pengguna</span>
                </nav>

                <div class="flex items-center gap-4 pt-1">
                    <div class="w-12 h-12 rounded-2xl 
                        @if($user->isAdmin()) bg-amber-50 border border-amber-200 text-amber-700
                        @elseif($user->isPimpinan()) bg-emerald-50 border border-emerald-200 text-emerald-700
                        @else bg-teal-50 border border-teal-200 text-teal-700 @endif 
                        flex items-center justify-center shadow-xs shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                    </div>
                    <div>
                        <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                            @if($user->isAdmin())
                                Diagram Alur Kerja: Administrator Persediaan
                            @elseif($user->isPimpinan())
                                Diagram Alur Kerja: Pimpinan (Kepala BPS Kabupaten Cirebon)
                            @else
                                Diagram Alur Kerja: Pegawai Organik BPS
                            @endif
                        </h1>
                        <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Panduan standar operasional peran Anda di SIPABIS BPS Kabupaten Cirebon</p>
                    </div>
                </div>
            </div>

            <!-- Role Badge Pill -->
            <div class="shrink-0 flex items-center">
                <span class="inline-flex items-center gap-2 px-3.5 py-2 rounded-2xl text-xs font-bold 
                    @if($user->isAdmin()) bg-amber-50 text-amber-900 border border-amber-200/80
                    @elseif($user->isPimpinan()) bg-emerald-50 text-emerald-900 border border-emerald-200/80
                    @else bg-teal-50 text-teal-900 border border-teal-200/80 @endif shadow-xs">
                    <span class="w-2 h-2 rounded-full 
                        @if($user->isAdmin()) bg-amber-500
                        @elseif($user->isPimpinan()) bg-emerald-500
                        @else bg-teal-600 @endif animate-pulse"></span>
                    <span>Peran: {{ strtoupper($user->role) }}</span>
                </span>
            </div>
        </div>

        @if($user->isPegawai())
            <!-- ============================================================== -->
            <!-- 1. KHUSUS ROLE PEGAWAI (ORGANIK BPS)                           -->
            <!-- ============================================================== -->

            <!-- Unified Flowchart Banner -->
            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 text-white relative overflow-hidden shadow-xl">
                <!-- Soft Glow Effect in Background -->
                <div class="absolute top-0 right-0 w-80 h-80 bg-teal-500/10 rounded-full blur-3xl pointer-events-none"></div>

                <div class="relative z-10 space-y-5">
                    <div class="space-y-1.5">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-teal-500/20 text-teal-300 border border-teal-400/30 text-[10px] font-bold uppercase tracking-wider">
                            <span class="w-1.5 h-1.5 rounded-full bg-teal-400 animate-pulse"></span>
                            Diagram Alur Pelayanan ATK
                        </span>
                        <h2 class="text-xl sm:text-2xl font-bold text-white tracking-tight">5 Tahap Alur Kerja Pegawai</h2>
                        <p class="text-xs sm:text-sm text-slate-300 max-w-2xl leading-relaxed">
                            Setiap permintaan yang Anda ajukan otomatis tercatat di bawah unit fungsi/ruangan Anda tanpa membebani Ketua Tim menjadi juru ketik.
                        </p>
                    </div>

                    <!-- Flow Stepper Nodes (Connected with Clean Indicators) -->
                    <div class="pt-2 flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-2.5">
                        
                        <!-- Node 1 -->
                        <div class="flex-1 bg-slate-800/80 border border-slate-700/80 rounded-2xl p-4 text-center space-y-1">
                            <span class="w-7 h-7 rounded-full bg-teal-500 text-slate-950 font-black text-xs inline-flex items-center justify-center shadow-xs">1</span>
                            <div class="text-xs font-bold text-white mt-1">Cek Katalog</div>
                            <div class="text-[11px] text-slate-300">Lihat stok fisik riil</div>
                        </div>

                        <!-- Connector Arrow -->
                        <div class="hidden lg:flex items-center justify-center text-slate-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                        </div>

                        <!-- Node 2 -->
                        <div class="flex-1 bg-slate-800/80 border border-slate-700/80 rounded-2xl p-4 text-center space-y-1">
                            <span class="w-7 h-7 rounded-full bg-teal-500 text-slate-950 font-black text-xs inline-flex items-center justify-center shadow-xs">2</span>
                            <div class="text-xs font-bold text-white mt-1">Ajukan Form PSD</div>
                            <div class="text-[11px] text-slate-300">Pilih barang & kuantitas</div>
                        </div>

                        <!-- Connector Arrow -->
                        <div class="hidden lg:flex items-center justify-center text-slate-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                        </div>

                        <!-- Node 3 -->
                        <div class="flex-1 bg-slate-800/80 border border-slate-700/80 rounded-2xl p-4 text-center space-y-1">
                            <span class="w-7 h-7 rounded-full bg-teal-500 text-slate-950 font-black text-xs inline-flex items-center justify-center shadow-xs">3</span>
                            <div class="text-xs font-bold text-white mt-1">Verifikasi Subbag</div>
                            <div class="text-[11px] text-slate-300">Persetujuan kuota</div>
                        </div>

                        <!-- Connector Arrow -->
                        <div class="hidden lg:flex items-center justify-center text-slate-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                        </div>

                        <!-- Node 4 -->
                        <div class="flex-1 bg-slate-800/80 border border-slate-700/80 rounded-2xl p-4 text-center space-y-1">
                            <span class="w-7 h-7 rounded-full bg-teal-500 text-slate-950 font-black text-xs inline-flex items-center justify-center shadow-xs">4</span>
                            <div class="text-xs font-bold text-white mt-1">Ambil Barang Fisik</div>
                            <div class="text-[11px] text-slate-300">Ke Subbag Umum</div>
                        </div>

                        <!-- Connector Arrow -->
                        <div class="hidden lg:flex items-center justify-center text-slate-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                        </div>

                        <!-- Node 5 -->
                        <div class="flex-1 bg-emerald-950/60 border border-emerald-500/40 rounded-2xl p-4 text-center space-y-1">
                            <span class="w-7 h-7 rounded-full bg-emerald-400 text-slate-950 font-black text-xs inline-flex items-center justify-center shadow-xs">✓</span>
                            <div class="text-xs font-bold text-emerald-300 mt-1">Terbuku di Ruangan</div>
                            <div class="text-[11px] text-slate-300">Masuk Buku Kendali</div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Harmonious Action Cards Grid (4 Columns) -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                
                <!-- Card 1 -->
                <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-sm hover:shadow-md hover:border-teal-400/80 transition flex flex-col justify-between space-y-5">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="w-8 h-8 rounded-2xl bg-teal-50 border border-teal-200 text-teal-800 font-bold text-xs flex items-center justify-center">1</span>
                            <span class="text-[10px] uppercase font-bold tracking-wider text-slate-400">Langkah 1</span>
                        </div>
                        <h3 class="font-bold text-slate-900 text-base leading-snug">Cek Ketersediaan Barang</h3>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Buka menu <strong>Katalog Persediaan</strong> untuk memastikan barang yang Anda perlukan memiliki stok aktif di gudang.
                        </p>
                    </div>
                    <a href="{{ route('barang.index') }}" class="w-full inline-flex items-center justify-center gap-1.5 py-2.5 px-4 rounded-2xl bg-slate-100 hover:bg-teal-50 border border-slate-200 hover:border-teal-300 text-teal-900 text-xs font-bold transition">
                        <span>Buka Katalog &rarr;</span>
                    </a>
                </div>

                <!-- Card 2 -->
                <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-sm hover:shadow-md hover:border-teal-400/80 transition flex flex-col justify-between space-y-5">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="w-8 h-8 rounded-2xl bg-teal-50 border border-teal-200 text-teal-800 font-bold text-xs flex items-center justify-center">2</span>
                            <span class="text-[10px] uppercase font-bold tracking-wider text-slate-400">Langkah 2</span>
                        </div>
                        <h3 class="font-bold text-slate-900 text-base leading-snug">Buat Permintaan ATK (PSD)</h3>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Buka <strong>Ajukan ATK Baru</strong>, pilih barang yang diperlukan, masukkan kuantitas, dan tambahkan catatan dinas.
                        </p>
                    </div>
                    <a href="{{ route('pegawai.permintaan.create') }}" class="w-full inline-flex items-center justify-center gap-1.5 py-2.5 px-4 rounded-2xl bg-teal-700 hover:bg-teal-800 text-white text-xs font-bold shadow-xs transition">
                        <span>Ajukan Permintaan &rarr;</span>
                    </a>
                </div>

                <!-- Card 3 -->
                <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-sm hover:shadow-md hover:border-teal-400/80 transition flex flex-col justify-between space-y-5">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="w-8 h-8 rounded-2xl bg-teal-50 border border-teal-200 text-teal-800 font-bold text-xs flex items-center justify-center">3</span>
                            <span class="text-[10px] uppercase font-bold tracking-wider text-slate-400">Langkah 3</span>
                        </div>
                        <h3 class="font-bold text-slate-900 text-base leading-snug">Pantau Status Permintaan</h3>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Buka <strong>Permintaan ATK Saya</strong> untuk memantau apakah tiket Anda berstatus <em>Diajukan</em>, <em>Disetujui</em>, atau <em>Selesai</em>.
                        </p>
                    </div>
                    <a href="{{ route('pegawai.permintaan.index') }}" class="w-full inline-flex items-center justify-center gap-1.5 py-2.5 px-4 rounded-2xl bg-slate-100 hover:bg-teal-50 border border-slate-200 hover:border-teal-300 text-teal-900 text-xs font-bold transition">
                        <span>Lihat Permintaan Saya &rarr;</span>
                    </a>
                </div>

                <!-- Card 4 -->
                <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-sm hover:shadow-md hover:border-teal-400/80 transition flex flex-col justify-between space-y-5">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="w-8 h-8 rounded-2xl bg-teal-50 border border-teal-200 text-teal-800 font-bold text-xs flex items-center justify-center">4</span>
                            <span class="text-[10px] uppercase font-bold tracking-wider text-slate-400">Langkah 4</span>
                        </div>
                        <h3 class="font-bold text-slate-900 text-base leading-snug">Ambil Fisik Barang</h3>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Saat tiket berstatus <strong>Disetujui</strong>, datang ke Subbag Umum untuk mengambil fisik barang. Pengurus Barang akan menekan serah terima.
                        </p>
                    </div>
                    @if($user->ruangan_id)
                        <a href="{{ route('kartu-kendali.ruangan', $user->ruangan_id) }}" class="w-full inline-flex items-center justify-center gap-1.5 py-2.5 px-4 rounded-2xl bg-slate-100 hover:bg-teal-50 border border-slate-200 hover:border-teal-300 text-teal-900 text-xs font-bold transition">
                            <span>Buku Kendali Ruangan &rarr;</span>
                        </a>
                    @else
                        <span class="w-full inline-flex items-center justify-center gap-1.5 py-2.5 px-4 rounded-2xl bg-emerald-50 text-emerald-800 text-xs font-bold border border-emerald-200">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Siap Diambil</span>
                        </span>
                    @endif
                </div>

            </div>

            <!-- Harmonious Bottom Banner (Usulan Barang Baru) -->
            <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200/90 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-5">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 border border-amber-200 text-amber-700 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div class="space-y-0.5">
                        <h4 class="font-bold text-slate-900 text-sm">Barang yang Anda butuhkan belum ada di Katalog BPS?</h4>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Gunakan modul <strong>Usulan Barang Baru</strong> untuk merekomendasikan perlengkapan kerja kantor ke Subbag Umum sebagai bahan pengadaan berikutnya.
                        </p>
                    </div>
                </div>
                <a href="{{ route('usulan-barang.create') }}" class="shrink-0 px-5 py-2.5 rounded-full bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs tracking-wide shadow-xs hover:shadow transition flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    <span>Buat Usulan Barang</span>
                </a>
            </div>

        @elseif($user->isAdmin())
            <!-- ============================================================== -->
            <!-- 2. KHUSUS ROLE ADMIN (SUBBAG UMUM / PENGURUS BARANG)           -->
            <!-- ============================================================== -->

            <!-- Unified Flowchart Banner -->
            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 text-white relative overflow-hidden shadow-xl">
                <div class="absolute top-0 right-0 w-80 h-80 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>

                <div class="relative z-10 space-y-5">
                    <div class="space-y-1.5">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-500/20 text-amber-300 border border-amber-400/30 text-[10px] font-bold uppercase tracking-wider">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                            Diagram Alur Pengelolaan Persediaan
                        </span>
                        <h2 class="text-xl sm:text-2xl font-bold text-white tracking-tight">4 Pilar Alur Kerja Administrator</h2>
                        <p class="text-xs sm:text-sm text-slate-300 max-w-2xl leading-relaxed">
                            Anda memegang wewenang verifikasi kuota, serah terima pengeluaran barang, pencatatan belanja vendor, dan penutupan buku stock opname fisik.
                        </p>
                    </div>

                    <!-- Flow Stepper Nodes -->
                    <div class="pt-2 flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-2.5">
                        <div class="flex-1 bg-slate-800/80 border border-slate-700/80 rounded-2xl p-4 text-center space-y-1">
                            <span class="w-7 h-7 rounded-full bg-amber-500 text-slate-950 font-black text-xs inline-flex items-center justify-center shadow-xs">1</span>
                            <div class="text-xs font-bold text-white mt-1">Verifikasi Permohonan</div>
                            <div class="text-[11px] text-slate-300">Setujui / sesuaikan kuota</div>
                        </div>

                        <div class="hidden lg:flex items-center justify-center text-slate-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                        </div>

                        <div class="flex-1 bg-slate-800/80 border border-slate-700/80 rounded-2xl p-4 text-center space-y-1">
                            <span class="w-7 h-7 rounded-full bg-amber-500 text-slate-950 font-black text-xs inline-flex items-center justify-center shadow-xs">2</span>
                            <div class="text-xs font-bold text-white mt-1">Serah Terima Barang</div>
                            <div class="text-[11px] text-slate-300">Stok otomatis terpotong</div>
                        </div>

                        <div class="hidden lg:flex items-center justify-center text-slate-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                        </div>

                        <div class="flex-1 bg-slate-800/80 border border-slate-700/80 rounded-2xl p-4 text-center space-y-1">
                            <span class="w-7 h-7 rounded-full bg-amber-500 text-slate-950 font-black text-xs inline-flex items-center justify-center shadow-xs">3</span>
                            <div class="text-xs font-bold text-white mt-1">Penerimaan Vendor</div>
                            <div class="text-[11px] text-slate-300">Catat SPJ & harga beli</div>
                        </div>

                        <div class="hidden lg:flex items-center justify-center text-slate-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                        </div>

                        <div class="flex-1 bg-emerald-950/60 border border-emerald-500/40 rounded-2xl p-4 text-center space-y-1">
                            <span class="w-7 h-7 rounded-full bg-emerald-400 text-slate-950 font-black text-xs inline-flex items-center justify-center shadow-xs">4</span>
                            <div class="text-xs font-bold text-emerald-300 mt-1">Stock Opname (BASO)</div>
                            <div class="text-[11px] text-slate-300">Audit fisik rak gudang</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Harmonious Action Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                
                <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-sm hover:shadow-md hover:border-amber-400/80 transition flex flex-col justify-between space-y-5">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="w-8 h-8 rounded-2xl bg-amber-50 border border-amber-200 text-amber-800 font-bold text-xs flex items-center justify-center">1</span>
                            <span class="text-[10px] uppercase font-bold tracking-wider text-slate-400">Tahap 1</span>
                        </div>
                        <h3 class="font-bold text-slate-900 text-base leading-snug">Verifikasi Kuota Permintaan</h3>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Buka menu <strong>Verifikasi Permintaan</strong>. Tinjau permohonan ATK dan sesuaikan kuota yang disetujui secara adil.
                        </p>
                    </div>
                    <a href="{{ route('admin.permintaan.index') }}" class="w-full inline-flex items-center justify-center gap-1.5 py-2.5 px-4 rounded-2xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold shadow-xs transition">
                        <span>Verifikasi Sekarang &rarr;</span>
                    </a>
                </div>

                <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-sm hover:shadow-md hover:border-amber-400/80 transition flex flex-col justify-between space-y-5">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="w-8 h-8 rounded-2xl bg-amber-50 border border-amber-200 text-amber-800 font-bold text-xs flex items-center justify-center">2</span>
                            <span class="text-[10px] uppercase font-bold tracking-wider text-slate-400">Tahap 2</span>
                        </div>
                        <h3 class="font-bold text-slate-900 text-base leading-snug">Eksekusi Serah Terima</h3>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Saat pegawai mengambil fisik ATK di Subbag Umum, klik tombol <strong>Serah Terima</strong> untuk memotong stok gudang secara otomatis.
                        </p>
                    </div>
                    <span class="w-full inline-flex items-center justify-center gap-1.5 py-2.5 px-4 rounded-2xl bg-emerald-50 text-emerald-800 text-xs font-bold border border-emerald-200">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Potong Stok Atomik</span>
                    </span>
                </div>

                <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-sm hover:shadow-md hover:border-amber-400/80 transition flex flex-col justify-between space-y-5">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="w-8 h-8 rounded-2xl bg-amber-50 border border-amber-200 text-amber-800 font-bold text-xs flex items-center justify-center">3</span>
                            <span class="text-[10px] uppercase font-bold tracking-wider text-slate-400">Tahap 3</span>
                        </div>
                        <h3 class="font-bold text-slate-900 text-base leading-snug">Catat Penerimaan Vendor</h3>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Buka menu <strong>Penerimaan Barang</strong> saat ada pengadaan baru. Masukkan nomor faktur, rekanan, kuantitas, dan harga beli riil.
                        </p>
                    </div>
                    <a href="{{ route('admin.barang-masuk.index') }}" class="w-full inline-flex items-center justify-center gap-1.5 py-2.5 px-4 rounded-2xl bg-slate-100 hover:bg-slate-200 border border-slate-200 text-slate-800 text-xs font-bold transition">
                        <span>Penerimaan Barang &rarr;</span>
                    </a>
                </div>

                <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-sm hover:shadow-md hover:border-amber-400/80 transition flex flex-col justify-between space-y-5">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="w-8 h-8 rounded-2xl bg-amber-50 border border-amber-200 text-amber-800 font-bold text-xs flex items-center justify-center">4</span>
                            <span class="text-[10px] uppercase font-bold tracking-wider text-slate-400">Tahap 4</span>
                        </div>
                        <h3 class="font-bold text-slate-900 text-base leading-snug">Stock Opname & BASO</h3>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Buka sesi di <strong>Stock Opname</strong>, catat fisik rak, finalisasi penyesuaian otomatis, lalu mintakan pengesahan Kepala BPS.
                        </p>
                    </div>
                    <a href="{{ route('stok-opname.index') }}" class="w-full inline-flex items-center justify-center gap-1.5 py-2.5 px-4 rounded-2xl bg-slate-100 hover:bg-slate-200 border border-slate-200 text-slate-800 text-xs font-bold transition">
                        <span>Stock Opname &rarr;</span>
                    </a>
                </div>

            </div>

        @elseif($user->isPimpinan())
            <!-- ============================================================== -->
            <!-- 3. KHUSUS ROLE PIMPINAN (KEPALA BPS KABUPATEN CIREBON)         -->
            <!-- ============================================================== -->

            <!-- Unified Flowchart Banner -->
            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 text-white relative overflow-hidden shadow-xl">
                <div class="absolute top-0 right-0 w-80 h-80 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>

                <div class="relative z-10 space-y-5">
                    <div class="space-y-1.5">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-400/30 text-[10px] font-bold uppercase tracking-wider">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            Diagram Alur Pengawasan Pimpinan
                        </span>
                        <h2 class="text-xl sm:text-2xl font-bold text-white tracking-tight">3 Tahap Pengawasan Eksekutif</h2>
                        <p class="text-xs sm:text-sm text-slate-300 max-w-2xl leading-relaxed">
                            Sebagai Kepala Satker, Anda mengawasi kesehatan persediaan secara makro, mengevaluasi serapan anggaran ATK, dan mengesahkan dokumen Berita Acara Stock Opname resmi.
                        </p>
                    </div>

                    <!-- Flow Stepper Nodes -->
                    <div class="pt-2 flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-2.5">
                        <div class="flex-1 bg-slate-800/80 border border-slate-700/80 rounded-2xl p-4 text-center space-y-1">
                            <span class="w-7 h-7 rounded-full bg-emerald-400 text-slate-950 font-black text-xs inline-flex items-center justify-center shadow-xs">1</span>
                            <div class="text-xs font-bold text-white mt-1">Dashboard Eksekutif</div>
                            <div class="text-[11px] text-slate-300">Nilai aset & sirkulasi stok</div>
                        </div>

                        <div class="hidden lg:flex items-center justify-center text-slate-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                        </div>

                        <div class="flex-1 bg-slate-800/80 border border-slate-700/80 rounded-2xl p-4 text-center space-y-1">
                            <span class="w-7 h-7 rounded-full bg-emerald-400 text-slate-950 font-black text-xs inline-flex items-center justify-center shadow-xs">2</span>
                            <div class="text-xs font-bold text-white mt-1">Audit 7 Ruangan</div>
                            <div class="text-[11px] text-slate-300">Buku Kendali seluruh tim</div>
                        </div>

                        <div class="hidden lg:flex items-center justify-center text-slate-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                        </div>

                        <div class="flex-1 bg-emerald-950/60 border border-emerald-500/40 rounded-2xl p-4 text-center space-y-1">
                            <span class="w-7 h-7 rounded-full bg-emerald-400 text-slate-950 font-black text-xs inline-flex items-center justify-center shadow-xs">3</span>
                            <div class="text-xs font-bold text-emerald-300 mt-1">Pengesahan BASO</div>
                            <div class="text-[11px] text-slate-300">Sahkan audit fisik rak</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Harmonious Action Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-sm hover:shadow-md hover:border-emerald-400/80 transition flex flex-col justify-between space-y-5">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="w-8 h-8 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 font-bold text-xs flex items-center justify-center">1</span>
                            <span class="text-[10px] uppercase font-bold tracking-wider text-slate-400">Tahap 1</span>
                        </div>
                        <h3 class="font-bold text-slate-900 text-base leading-snug">Dashboard Eksekutif Terpadu</h3>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Memantau nilai nominal saldo persediaan kantor secara makro (Rp 49.569.966,-), sirkulasi barang masuk/keluar, dan rasio serapan per fungsi.
                        </p>
                    </div>
                    <a href="{{ route('dashboard') }}" class="w-full inline-flex items-center justify-center gap-1.5 py-2.5 px-4 rounded-2xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold shadow-xs transition">
                        <span>Buka Dashboard &rarr;</span>
                    </a>
                </div>

                <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-sm hover:shadow-md hover:border-emerald-400/80 transition flex flex-col justify-between space-y-5">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="w-8 h-8 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 font-bold text-xs flex items-center justify-center">2</span>
                            <span class="text-[10px] uppercase font-bold tracking-wider text-slate-400">Tahap 2</span>
                        </div>
                        <h3 class="font-bold text-slate-900 text-base leading-snug">Audit Rekap 7 Ruangan</h3>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Mengevaluasi laporan Buku Kendali konsumsi seluruh 7 unit kerja untuk mendeteksi unit yang memerlukan penambahan kuota atau efisiensi anggaran.
                        </p>
                    </div>
                    <a href="{{ route('kartu-kendali.index') }}" class="w-full inline-flex items-center justify-center gap-1.5 py-2.5 px-4 rounded-2xl bg-slate-100 hover:bg-slate-200 border border-slate-200 text-slate-800 text-xs font-bold transition">
                        <span>Buka Buku Kendali &rarr;</span>
                    </a>
                </div>

                <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-sm hover:shadow-md hover:border-emerald-400/80 transition flex flex-col justify-between space-y-5">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="w-8 h-8 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 font-bold text-xs flex items-center justify-center">3</span>
                            <span class="text-[10px] uppercase font-bold tracking-wider text-slate-400">Tahap 3</span>
                        </div>
                        <h3 class="font-bold text-slate-900 text-base leading-snug">Pengesahan Dokumen BASO</h3>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Memeriksa hasil audit fisik rak gudang pada akhir periode dan memberikan <strong>Pengesahan Pimpinan</strong> pada Berita Acara resmi format dinas.
                        </p>
                    </div>
                    <a href="{{ route('stok-opname.index') }}" class="w-full inline-flex items-center justify-center gap-1.5 py-2.5 px-4 rounded-2xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold shadow-xs transition">
                        <span>Buka Sesi Opname &rarr;</span>
                    </a>
                </div>

            </div>

        @endif

    </div>
</x-app-layout>
