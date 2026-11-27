<x-app-layout>
    <x-slot:title>Usulan Pengadaan Barang</x-slot:title>
    <x-slot:header>Daftar Usulan Barang Persediaan Baru</x-slot:header>

    <div class="space-y-6" x-data="{ previewUrl: null, previewTitle: '' }">
        
        <!-- Header Banner Resmi BPS -->
        <div class="relative overflow-hidden bg-gradient-to-r from-bps-navy-900 via-bps-navy-800 to-bps-navy-700 rounded-3xl p-6 sm:p-8 text-white shadow-xl shadow-bps-navy-950/15 border border-bps-navy-700/80">
            <!-- Decorative Accent Ornaments -->
            <div class="absolute -right-12 -bottom-12 w-64 h-64 bg-bps-orange-500/20 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute right-1/3 -top-12 w-48 h-48 bg-bps-blue-500/20 rounded-full blur-2xl pointer-events-none"></div>

            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="space-y-2">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-bps-orange-300 text-xs font-semibold border border-white/15 backdrop-blur-sm">
                        <svg class="w-3.5 h-3.5 text-bps-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Aspirasi Kebutuhan Logistik Unit Kerja • BPS Kabupaten Cirebon</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white">Usulan Pengadaan Barang Baru</h1>
                    <p class="text-xs sm:text-sm text-slate-200 max-w-2xl leading-relaxed">
                        Sampaikan usulan kebutuhan barang pakai habis atau perlengkapan survei yang belum tercantum di katalog resmi persediaan atau yang stoknya sedang tidak tersedia di gudang logistik.
                    </p>
                </div>

                <div class="shrink-0">
                    <a href="{{ route('usulan-barang.create') }}" 
                       class="px-5 py-3 rounded-2xl font-bold text-xs uppercase tracking-wider bg-bps-orange-500 hover:bg-bps-orange-600 text-white shadow-lg shadow-bps-orange-500/30 hover:scale-[1.02] active:scale-[0.98] transition-all duration-200 flex items-center gap-2.5">
                        <svg class="w-4 h-4 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        <span>Ajukan Usulan Baru</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- KPI Status Filter Cards Row -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3.5">
            
            <!-- Card 1: Semua Usulan -->
            <a href="{{ route('usulan-barang.index', array_filter(['search' => $search])) }}" 
               class="group relative bg-white rounded-2xl p-4 border transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md {{ empty($status) ? 'border-bps-navy-700 shadow-sm ring-2 ring-bps-navy-700/10' : 'border-slate-200/90 hover:border-slate-300' }}">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold {{ empty($status) ? 'text-bps-navy-900' : 'text-slate-500' }}">Semua Usulan</span>
                    <div class="w-7 h-7 rounded-xl flex items-center justify-center {{ empty($status) ? 'bg-bps-navy-900 text-white' : 'bg-slate-100 text-slate-500 group-hover:bg-slate-200' }}">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    </div>
                </div>
                <div class="mt-2.5 flex items-baseline gap-2">
                    <span class="text-2xl font-black text-slate-900 tracking-tight">{{ number_format($totalUsulan) }}</span>
                    <span class="text-[10px] text-slate-400 font-medium">tiket</span>
                </div>
                <div class="mt-1 text-[10px] text-slate-400">Total seluruh usulan</div>
            </a>

            <!-- Card 2: Diajukan -->
            <a href="{{ route('usulan-barang.index', array_filter(['status' => 'diajukan', 'search' => $search])) }}" 
               class="group relative bg-white rounded-2xl p-4 border transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md {{ $status === 'diajukan' ? 'border-amber-500 shadow-sm ring-2 ring-amber-500/10' : 'border-slate-200/90 hover:border-amber-300' }}">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold {{ $status === 'diajukan' ? 'text-amber-800' : 'text-slate-500' }}">Diajukan</span>
                    <div class="w-7 h-7 rounded-xl flex items-center justify-center {{ $status === 'diajukan' ? 'bg-amber-500 text-white' : 'bg-amber-50 text-amber-600 group-hover:bg-amber-100' }}">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <div class="mt-2.5 flex items-baseline gap-2">
                    <span class="text-2xl font-black text-amber-600 tracking-tight">{{ number_format($totalDiajukan) }}</span>
                    <span class="text-[10px] text-amber-500/80 font-medium">antrean</span>
                </div>
                <div class="mt-1 text-[10px] text-slate-400">Menunggu telaah Subbag</div>
            </a>

            <!-- Card 3: Dipertimbangkan -->
            <a href="{{ route('usulan-barang.index', array_filter(['status' => 'dipertimbangkan', 'search' => $search])) }}" 
               class="group relative bg-white rounded-2xl p-4 border transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md {{ $status === 'dipertimbangkan' ? 'border-sky-500 shadow-sm ring-2 ring-sky-500/10' : 'border-slate-200/90 hover:border-sky-300' }}">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold {{ $status === 'dipertimbangkan' ? 'text-sky-800' : 'text-slate-500' }}">Dipertimbangkan</span>
                    <div class="w-7 h-7 rounded-xl flex items-center justify-center {{ $status === 'dipertimbangkan' ? 'bg-sky-500 text-white' : 'bg-sky-50 text-sky-600 group-hover:bg-sky-100' }}">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    </div>
                </div>
                <div class="mt-2.5 flex items-baseline gap-2">
                    <span class="text-2xl font-black text-sky-600 tracking-tight">{{ number_format($totalDipertimbangkan) }}</span>
                    <span class="text-[10px] text-sky-500/80 font-medium">item</span>
                </div>
                <div class="mt-1 text-[10px] text-slate-400">Masuk rencana belanja</div>
            </a>

            <!-- Card 4: Direalisasikan -->
            <a href="{{ route('usulan-barang.index', array_filter(['status' => 'direalisasikan', 'search' => $search])) }}" 
               class="group relative bg-white rounded-2xl p-4 border transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md {{ $status === 'direalisasikan' ? 'border-emerald-500 shadow-sm ring-2 ring-emerald-500/10' : 'border-slate-200/90 hover:border-emerald-300' }}">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold {{ $status === 'direalisasikan' ? 'text-emerald-800' : 'text-slate-500' }}">Direalisasikan</span>
                    <div class="w-7 h-7 rounded-xl flex items-center justify-center {{ $status === 'direalisasikan' ? 'bg-emerald-500 text-white' : 'bg-emerald-50 text-emerald-600 group-hover:bg-emerald-100' }}">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <div class="mt-2.5 flex items-baseline gap-2">
                    <span class="text-2xl font-black text-emerald-600 tracking-tight">{{ number_format($totalDirealisasikan) }}</span>
                    <span class="text-[10px] text-emerald-500/80 font-medium">selesai</span>
                </div>
                <div class="mt-1 text-[10px] text-slate-400">Sudah diadakan/tersedia</div>
            </a>

            <!-- Card 5: Ditolak -->
            <a href="{{ route('usulan-barang.index', array_filter(['status' => 'ditolak', 'search' => $search])) }}" 
               class="group relative bg-white rounded-2xl p-4 border transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md {{ $status === 'ditolak' ? 'border-rose-500 shadow-sm ring-2 ring-rose-500/10' : 'border-slate-200/90 hover:border-rose-300' }}">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold {{ $status === 'ditolak' ? 'text-rose-800' : 'text-slate-500' }}">Ditolak</span>
                    <div class="w-7 h-7 rounded-xl flex items-center justify-center {{ $status === 'ditolak' ? 'bg-rose-500 text-white' : 'bg-rose-50 text-rose-600 group-hover:bg-rose-100' }}">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <div class="mt-2.5 flex items-baseline gap-2">
                    <span class="text-2xl font-black text-rose-600 tracking-tight">{{ number_format($totalDitolak) }}</span>
                    <span class="text-[10px] text-rose-500/80 font-medium">usulan</span>
                </div>
                <div class="mt-1 text-[10px] text-slate-400">Tidak disetujui/anggaran</div>
            </a>

        </div>

        <!-- Filter & Search Toolbar -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-4 sm:p-5 shadow-sm">
            <form method="GET" action="{{ route('usulan-barang.index') }}" class="flex flex-col sm:flex-row items-center gap-3">
                <div class="relative flex-1 w-full">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama barang usulan yang diajukan..."
                           class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-medium focus:ring-2 focus:ring-bps-blue-500 focus:bg-white focus:outline-none transition">
                </div>
                
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <select name="status" class="py-2.5 px-3.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-medium focus:ring-2 focus:ring-bps-blue-500 focus:bg-white focus:outline-none transition">
                        <option value="">Semua Status Usulan</option>
                        <option value="diajukan" {{ $status === 'diajukan' ? 'selected' : '' }}>Diajukan (Menunggu Telaah)</option>
                        <option value="dipertimbangkan" {{ $status === 'dipertimbangkan' ? 'selected' : '' }}>Dipertimbangkan (Rencana Belanja)</option>
                        <option value="direalisasikan" {{ $status === 'direalisasikan' ? 'selected' : '' }}>Direalisasikan (Tersedia)</option>
                        <option value="ditolak" {{ $status === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    </select>

                    <button type="submit" class="px-5 py-2.5 rounded-xl font-bold text-xs bg-bps-navy-800 hover:bg-bps-navy-900 text-white shadow-sm transition shrink-0 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                        <span>Filter</span>
                    </button>

                    @if($search || $status)
                        <a href="{{ route('usulan-barang.index') }}" title="Reset Filter" 
                           class="p-2.5 rounded-xl border border-slate-200 text-slate-400 hover:text-rose-600 hover:bg-rose-50 hover:border-rose-200 transition shrink-0 flex items-center gap-1 text-xs font-semibold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            <span class="hidden sm:inline">Reset</span>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Feedback Alert Messages -->
        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-xl bg-emerald-500 text-white flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    </div>
                    <span class="font-semibold">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        <!-- Table View Container -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs sm:text-sm">
                    <thead class="bg-slate-50/90 border-b border-slate-200/80 text-[11px] uppercase tracking-wider text-slate-500 font-bold">
                        <tr>
                            <th class="py-4 px-4 text-center w-12">No</th>
                            <th class="py-4 px-4">Nama Barang Usulan</th>
                            <th class="py-4 px-4 text-center">Estimasi Kebutuhan</th>
                            <th class="py-4 px-4">Pengusul & Unit Kerja</th>
                            <th class="py-4 px-4">Alasan & Urgensi</th>
                            <th class="py-4 px-4 text-center">Status Usulan</th>
                            <th class="py-4 px-4 text-center">Tindak Lanjut</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($usulanList as $index => $u)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <!-- No -->
                                <td class="py-4 px-4 text-center text-slate-400 font-medium">
                                    {{ $usulanList->firstItem() + $index }}
                                </td>

                                <!-- Nama Barang Usulan & Foto Contoh -->
                                <td class="py-4 px-4">
                                    <div class="flex items-center gap-3">
                                        @if($u->foto_url)
                                            <button type="button"
                                                    @click="previewUrl = '{{ $u->foto_url }}'; previewTitle = '{{ addslashes($u->nama_barang_usulan) }}'"
                                                    title="Klik untuk memperbesar foto contoh barang"
                                                    class="w-12 h-12 rounded-xl border border-slate-200 bg-slate-50 overflow-hidden shrink-0 hover:border-bps-blue-500 transition cursor-pointer group">
                                                <img src="{{ $u->foto_url }}" alt="{{ $u->nama_barang_usulan }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform">
                                            </button>
                                        @else
                                            <div class="w-12 h-12 rounded-xl border border-dashed border-slate-200 bg-slate-50 text-slate-300 flex items-center justify-center shrink-0" title="Tidak ada lampiran foto">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            </div>
                                        @endif
                                        <div>
                                            <p class="font-bold text-slate-900 text-sm">{{ $u->nama_barang_usulan }}</p>
                                            <div class="flex items-center gap-1.5 mt-0.5 text-[11px] text-slate-400">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                <span>Diusulkan: {{ $u->created_at->format('d M Y, H:i') }} WIB</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Estimasi Kebutuhan -->
                                <td class="py-4 px-4 text-center whitespace-nowrap">
                                    <div class="inline-flex flex-col items-center justify-center px-3 py-1.5 rounded-xl bg-slate-50 border border-slate-200">
                                        <span class="font-black text-slate-800 text-sm leading-tight">{{ number_format($u->estimasi_kebutuhan) }}</span>
                                        <span class="text-[10px] text-slate-500 font-medium uppercase tracking-wider">{{ $u->perkiraan_satuan }}</span>
                                    </div>
                                </td>

                                <!-- Pemohon & Ruangan -->
                                <td class="py-4 px-4 whitespace-nowrap">
                                    <p class="font-bold text-slate-800 text-xs sm:text-sm">{{ $u->user->name }}</p>
                                    <div class="inline-flex items-center gap-1 mt-0.5 px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 text-[10px] font-semibold">
                                        <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                        <span>{{ $u->ruangan?->nama_ruangan ?? 'BPS Kabupaten Cirebon' }}</span>
                                    </div>
                                </td>

                                <!-- Alasan Kebutuhan -->
                                <td class="py-4 px-4 max-w-xs">
                                    <p class="text-xs text-slate-600 line-clamp-2 leading-relaxed" title="{{ $u->alasan_kebutuhan }}">
                                        "{{ $u->alasan_kebutuhan }}"
                                    </p>
                                    @if($u->catatan_tindak_lanjut)
                                        <div class="mt-1.5 p-2 rounded-xl bg-amber-50/80 border border-amber-200/80 text-amber-900 text-[11px] leading-snug">
                                            <span class="font-bold text-amber-950 block">Catatan Subbag Umum:</span>
                                            {{ $u->catatan_tindak_lanjut }}
                                        </div>
                                    @endif
                                </td>

                                <!-- Status Badge -->
                                <td class="py-4 px-4 text-center whitespace-nowrap">
                                    @if($u->status === 'diajukan')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                                            <span>Diajukan</span>
                                        </span>
                                    @elseif($u->status === 'dipertimbangkan')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-sky-50 text-sky-700 border border-sky-200">
                                            <span class="w-2 h-2 rounded-full bg-sky-500"></span>
                                            <span>Dipertimbangkan</span>
                                        </span>
                                    @elseif($u->status === 'direalisasikan')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                            <span>Direalisasikan</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                            <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                                            <span>Ditolak</span>
                                        </span>
                                    @endif
                                </td>

                                <!-- Aksi / Tindak Lanjut -->
                                <td class="py-4 px-4 text-center whitespace-nowrap">
                                    @if(auth()->user()->isAdmin())
                                        <!-- Form Aksi Admin Subbag Umum -->
                                        <div x-data="{ openModal: false }">
                                            <button @click="openModal = true" 
                                                    class="px-3 py-1.5 rounded-xl bg-bps-navy-50 hover:bg-bps-navy-100 text-bps-navy-800 font-bold text-xs border border-bps-navy-200/80 transition flex items-center gap-1.5 mx-auto">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                <span>Tindak Lanjut</span>
                                            </button>

                                            <!-- Modal Tindak Lanjut Admin -->
                                            <div x-show="openModal" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" style="display: none;">
                                                <div @click.away="openModal = false" class="bg-white rounded-3xl max-w-md w-full p-6 text-left shadow-2xl border border-slate-200">
                                                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                                                        <div>
                                                            <h3 class="font-extrabold text-slate-900 text-base">Tindak Lanjut Usulan Barang</h3>
                                                            <p class="text-xs text-slate-400 mt-0.5">{{ $u->nama_barang_usulan }}</p>
                                                        </div>
                                                        <button @click="openModal = false" class="text-slate-400 hover:text-slate-600 p-1">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                        </button>
                                                    </div>

                                                    <form action="{{ route('admin.usulan-barang.update-status', $u->id) }}" method="POST" class="mt-4 space-y-4">
                                                        @csrf
                                                        @method('PATCH')

                                                        @if($u->foto_url)
                                                            <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200 flex items-center gap-3">
                                                                <img src="{{ $u->foto_url }}" alt="{{ $u->nama_barang_usulan }}" class="w-16 h-16 rounded-xl object-cover border border-slate-200 shrink-0">
                                                                <div class="text-xs text-slate-600">
                                                                    <span class="font-bold text-slate-800 block">Foto Contoh dari Pemohon</span>
                                                                    <span class="text-[11px] text-slate-500">Lihat visual fisik barang yang diusulkan.</span>
                                                                </div>
                                                            </div>
                                                        @endif

                                                        <div>
                                                            <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Status Keputusan <span class="text-rose-500">*</span></label>
                                                            <select name="status" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:ring-2 focus:ring-bps-blue-500 focus:bg-white focus:outline-none">
                                                                <option value="dipertimbangkan" {{ $u->status === 'dipertimbangkan' ? 'selected' : '' }}>Dipertimbangkan (Masuk Rencana Belanja Persediaan)</option>
                                                                <option value="direalisasikan" {{ $u->status === 'direalisasikan' ? 'selected' : '' }}>Direalisasikan (Sudah Diadakan / Tersedia)</option>
                                                                <option value="ditolak" {{ $u->status === 'ditolak' ? 'selected' : '' }}>Ditolak (Anggaran / Kebutuhan Tidak Sesuai)</option>
                                                            </select>
                                                        </div>

                                                        <div>
                                                            <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Catatan Pertimbangan / Anggaran</label>
                                                            <textarea name="catatan_tindak_lanjut" rows="3" placeholder="Masukkan alasan keputusan, alokasi anggaran, atau rencana belanja triwulan..."
                                                                      class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:ring-2 focus:ring-bps-blue-500 focus:bg-white focus:outline-none">{{ $u->catatan_tindak_lanjut }}</textarea>
                                                        </div>

                                                        <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                                                            <button type="button" @click="openModal = false" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition">Batal</button>
                                                            <button type="submit" class="px-5 py-2 rounded-xl text-xs font-bold bg-bps-navy-800 hover:bg-bps-navy-900 text-white shadow-sm transition">Simpan Keputusan</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        @if($u->status === 'diajukan' && $u->user_id === auth()->id())
                                            <form action="{{ route('usulan-barang.destroy', $u->id) }}" method="POST"
                                                  data-confirm="Apakah Anda yakin ingin membatalkan usulan barang persediaan '{{ $u->nama_barang_usulan }}'? Usulan yang dibatalkan tidak dapat dikembalikan."
                                                  data-confirm-title="Batalkan Usulan Barang"
                                                  data-confirm-type="danger"
                                                  data-confirm-btn="Ya, Batalkan Usulan">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold border border-rose-200/80 transition">
                                                    Batalkan
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-xs text-slate-400 font-medium">Terkunci</span>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-16 px-4">
                                    <div class="max-w-md mx-auto flex flex-col items-center">
                                        <div class="w-16 h-16 rounded-2xl bg-bps-navy-50 border border-bps-navy-100 text-bps-navy-700 flex items-center justify-center mb-4 shadow-sm">
                                            <svg class="w-8 h-8 text-bps-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        </div>
                                        <h3 class="text-base font-extrabold text-slate-800">Belum ada usulan pengadaan barang</h3>
                                        <p class="text-xs text-slate-500 mt-1.5 leading-relaxed text-center">
                                            @if($search || $status)
                                                Tidak ditemukan usulan barang yang sesuai dengan kata kunci atau filter status yang dipilih.
                                            @else
                                                Unit kerja Anda dapat menyampaikan usulan kebutuhan barang pakai habis baru atau stok habis untuk pengadaan logistik periode berikutnya.
                                            @endif
                                        </p>
                                        <div class="mt-5 flex items-center gap-3">
                                            @if($search || $status)
                                                <a href="{{ route('usulan-barang.index') }}" class="px-4 py-2 rounded-xl text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 transition">
                                                    Bersihkan Filter
                                                </a>
                                            @endif
                                            <a href="{{ route('usulan-barang.create') }}" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-bps-orange-500 hover:bg-bps-orange-600 text-white shadow-md shadow-bps-orange-500/20 transition flex items-center gap-2">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                                <span>Ajukan Usulan Pertama</span>
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($usulanList->hasPages())
                <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $usulanList->links() }}
                </div>
            @endif
        </div>

        <!-- Modal Lightbox Foto Contoh Barang Usulan -->
        <div x-show="previewUrl"
             x-transition.opacity
             @keydown.escape.window="previewUrl = null"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/75 backdrop-blur-sm"
             style="display: none;">
            <div @click.away="previewUrl = null" class="bg-white rounded-3xl max-w-md w-full overflow-hidden shadow-2xl border border-slate-200">
                <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between">
                    <h4 class="text-sm font-extrabold text-slate-900 truncate pr-4" x-text="previewTitle"></h4>
                    <button type="button" @click="previewUrl = null" class="text-slate-400 hover:text-slate-700 p-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="p-6 bg-slate-50 flex items-center justify-center">
                    <img :src="previewUrl" :alt="previewTitle" class="max-h-80 w-auto rounded-2xl object-contain shadow-xs">
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
