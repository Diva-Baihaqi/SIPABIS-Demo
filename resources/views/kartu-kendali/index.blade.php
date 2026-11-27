<x-app-layout>
    <x-slot:title>Buku Kendali & Kartu Stok Persediaan</x-slot:title>
    <x-slot:header>Buku Kendali Distribusi & Kartu Stok Persediaan BPS</x-slot:header>

    <div class="space-y-8" x-data="kartuKendaliLiveFilter()" @popstate.window="onPopState($event)">
        
        <!-- Header Banner & Stats -->
        <div class="bg-gradient-to-r from-bps-navy-800 via-bps-navy-700 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl border border-bps-navy-700/80 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
            <div>
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-bps-orange-300 text-xs font-semibold">
                    <span>Satker 428646 • Kode UAKPB 054.01.0200.018801</span>
                </span>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white mt-2">Buku Kendali & Kartu Stok BPS</h1>
                <p class="text-xs sm:text-sm text-slate-300 max-w-2xl mt-1">
                    Catatan pemakaian ATK per unit kerja dan buku riwayat keluar-masuk barang persediaan kantor secara transparan.
                </p>
            </div>

            <!-- Global Year & Month Period Filter (Live Update) -->
            <form x-ref="periodForm" 
                  action="{{ route('kartu-kendali.index') }}" 
                  method="GET" 
                  @submit.prevent="applyFilter()"
                  class="bg-white/10 p-2.5 sm:p-3 rounded-2xl border border-white/10 flex flex-wrap items-center gap-2.5 shrink-0">
                <input type="hidden" name="tab" :value="activeTab">
                @if(request('kategori_id'))
                    <input type="hidden" name="kategori_id" value="{{ request('kategori_id') }}">
                @endif
                @if(request('search_barang'))
                    <input type="hidden" name="search_barang" value="{{ request('search_barang') }}">
                @endif

                <!-- Pilihan Bulan -->
                <div class="flex items-center gap-1.5">
                    <span class="text-xs font-semibold text-slate-300">Bulan:</span>
                    <select name="bulan" 
                            @change="applyFilter()" 
                            class="bg-slate-900 text-white font-bold text-xs px-2.5 py-1.5 rounded-xl border border-slate-700 focus:ring-2 focus:ring-bps-orange-400">
                        <option value="">Semua Bulan (Jan - Des)</option>
                        @foreach($bulanList as $num => $nama)
                            <option value="{{ $num }}" {{ ($filter['bulan'] ?? '') == $num ? 'selected' : '' }}>
                                {{ $nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Pilihan Tahun Anggaran -->
                <div class="flex items-center gap-1.5">
                    <span class="text-xs font-semibold text-slate-300">Tahun:</span>
                    <select name="tahun" 
                            @change="applyFilter()" 
                            class="bg-slate-900 text-white font-bold text-xs px-2.5 py-1.5 rounded-xl border border-slate-700 focus:ring-2 focus:ring-bps-orange-400">
                        @for($y = date('Y'); $y >= date('Y') - 3; $y--)
                            <option value="{{ $y }}" {{ ($filter['tahun'] ?? date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>

                @if(!empty($filter['bulan']) || ($filter['tahun'] ?? date('Y')) != date('Y'))
                    <a href="{{ route('kartu-kendali.index', ['tab' => request('tab', 'ruangan')]) }}" 
                       @click.prevent="resetPeriod('{{ route('kartu-kendali.index', ['tab' => request('tab', 'ruangan')]) }}')"
                       title="Reset Filter Periode" 
                       class="p-1.5 rounded-lg text-slate-300 hover:text-rose-400 hover:bg-white/10 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </a>
                @endif
            </form>
        </div>

        <!-- Tab Selector Navigation -->
        <div class="flex items-center gap-3 border-b border-slate-200 pb-3">
            <button type="button"
                    @click="activeTab = 'ruangan'; syncUrlTab('ruangan')" 
                    :class="activeTab === 'ruangan' ? 'bg-slate-900 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'"
                    class="px-5 py-2.5 rounded-2xl text-xs font-extrabold transition flex items-center gap-2 cursor-pointer">
                <svg class="w-4 h-4 text-bps-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                <span>Pemakaian Per Ruangan (7 Unit Kerja)</span>
            </button>

            <button type="button"
                    @click="activeTab = 'barang'; syncUrlTab('barang')" 
                    :class="activeTab === 'barang' ? 'bg-slate-900 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'"
                    class="px-5 py-2.5 rounded-2xl text-xs font-extrabold transition flex items-center gap-2 cursor-pointer">
                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Kartu Stok Barang (Riwayat Per Item)</span>
            </button>
        </div>

        <!-- TAB 1: BUKU KENDALI PER RUANGAN (CONTAINER DENGAN LIVE AJAX UPDATE) -->
        <div x-show="activeTab === 'ruangan'" class="space-y-6">
            <div id="kartu-kendali-ruangan-container" 
                 class="space-y-6 relative transition-opacity duration-200" 
                 :class="{ 'opacity-60 pointer-events-none': isLoading && activeTab === 'ruangan' }">
                
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900">Daftar Pemakaian ATK Per Unit Kerja</h3>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Pilih salah satu fungsi/unit kerja untuk melihat barang apa saja yang sudah digunakan 
                            @if(!empty($filter['bulan']))
                                pada bulan <strong class="text-slate-800">{{ $bulanList[$filter['bulan']] ?? $filter['bulan'] }}</strong>
                            @endif
                            tahun <strong class="text-slate-800">{{ $filter['tahun'] ?? date('Y') }}</strong>.
                        </p>
                    </div>
                    <div class="text-left sm:text-right bg-slate-50 sm:bg-transparent p-3 sm:p-0 rounded-2xl sm:rounded-none border sm:border-0 border-slate-200 w-full sm:w-auto">
                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Total Barang yang Diambil</span>
                        <span class="text-xl font-black text-slate-900">{{ number_format($totalUnitSemuaRuangan) }} <span class="text-xs font-normal text-slate-500">Unit</span></span>
                    </div>
                </div>

                <!-- Bilah Aksi Ekspor Kolektif Seluruh Unit Kerja (PDF, Excel, Word) -->
                @if(!auth()->user()->isPegawai())
                    <div class="bg-gradient-to-r from-slate-900 via-bps-navy-800 to-slate-900 rounded-2xl p-4 text-white shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4 border border-slate-800">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center text-bps-orange-400 shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            </div>
                            <div>
                                <h4 class="text-xs sm:text-sm font-extrabold text-white flex items-center gap-2">
                                    <span>Cetak & Ekspor Buku Kendali Kolektif</span>
                                    <span class="px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 text-[10px] font-bold border border-emerald-500/30">Semua 7 Divisi</span>
                                </h4>
                                <p class="text-[11px] text-slate-300 mt-0.5">
                                    Sekali klik untuk cetak PDF atau unduh rekapan seluruh divisi sekaligus tanpa memilih satu per satu.
                                </p>
                            </div>
                        </div>

                        <!-- 3 Tombol Ekspor Kolektif: PDF, Excel, Word -->
                        <div class="flex flex-wrap items-center gap-2 w-full md:w-auto shrink-0">
                            <!-- 1. Cetak / Simpan PDF -->
                            <a href="{{ route('kartu-kendali.cetak-semua', array_filter(['tahun' => $filter['tahun'] ?? null, 'bulan' => $filter['bulan'] ?? null])) }}" 
                               target="_blank"
                               class="flex-1 md:flex-initial inline-flex items-center justify-center gap-1.5 px-3.5 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs transition shadow-xs"
                               title="Cetak atau Simpan PDF seluruh 7 unit kerja sekaligus">
                                <svg class="w-4 h-4 text-rose-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                <span>Cetak PDF (7 Divisi)</span>
                            </a>

                            <!-- 2. Ekspor Excel Multi-Sheet -->
                            <a href="{{ route('kartu-kendali.export-semua.excel', array_filter(['tahun' => $filter['tahun'] ?? null, 'bulan' => $filter['bulan'] ?? null])) }}" 
                               class="flex-1 md:flex-initial inline-flex items-center justify-center gap-1.5 px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs transition shadow-xs"
                               title="Unduh Buku Kerja Excel (.xlsx) dengan Sheet Rekap + Sheet masing-masing divisi">
                                <svg class="w-4 h-4 text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <span>Ekspor Excel (.xlsx)</span>
                            </a>

                            <!-- 3. Ekspor Word Dokumen -->
                            <a href="{{ route('kartu-kendali.export-semua.word', array_filter(['tahun' => $filter['tahun'] ?? null, 'bulan' => $filter['bulan'] ?? null])) }}" 
                               class="flex-1 md:flex-initial inline-flex items-center justify-center gap-1.5 px-3.5 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs transition shadow-xs"
                               title="Unduh dokumen Word (.doc) lengkap siap edit untuk lampiran laporan dinas">
                                <svg class="w-4 h-4 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <span>Ekspor Word (.doc)</span>
                            </a>
                        </div>
                    </div>
                @endif

                <!-- 7 Ruangan Grid Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach($rekapRuangan as $r)
                        <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-sm hover:shadow-md hover:border-teal-500 transition duration-200 flex flex-col justify-between group">
                            <div>
                                <div class="flex items-center justify-between mb-3">
                                    <span class="px-2.5 py-1 rounded-xl bg-slate-100 text-slate-600 font-mono text-[10px] font-bold">
                                        {{ $r->kode_ruangan }}
                                    </span>
                                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-slate-500">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                        <span>{{ $r->total_transaksi }}x Pengambilan</span>
                                    </span>
                                </div>

                                <h4 class="text-base font-extrabold text-slate-900 group-hover:text-teal-700 transition">
                                    {{ $r->nama_ruangan }}
                                </h4>
                                <p class="text-xs text-slate-500 mt-1">
                                    P.J.: <span class="font-semibold text-slate-700">{{ $r->penanggung_jawab ?? 'Kasubbag / Ketua Tim' }}</span>
                                </p>

                                <!-- Metric Stats in Card -->
                                <div class="grid grid-cols-2 gap-3 mt-5 p-3 rounded-2xl bg-slate-50 border border-slate-100">
                                    <div>
                                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Total Fisik</span>
                                        <span class="text-lg font-black text-slate-800">{{ number_format($r->total_unit) }}</span>
                                        <span class="text-[10px] text-slate-500">Unit</span>
                                    </div>
                                    <div>
                                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Variasi Item</span>
                                        <span class="text-lg font-black text-teal-700">{{ $r->total_jenis_barang }}</span>
                                        <span class="text-[10px] text-slate-500">Jenis ATK</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Card Action CTA -->
                            <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between">
                                <span class="text-[10px] text-slate-400 font-mono">
                                    @if($r->terakhir_transaksi)
                                        Aktif: {{ \Carbon\Carbon::parse($r->terakhir_transaksi)->format('d/m/Y') }}
                                    @else
                                        Belum ada mutasi
                                    @endif
                                </span>

                                <a href="{{ route('kartu-kendali.ruangan', array_merge(['id' => $r->id], array_filter(['tahun' => $filter['tahun'] ?? null, 'bulan' => $filter['bulan'] ?? null]))) }}" 
                                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-slate-900 hover:bg-teal-700 text-white font-bold text-xs transition">
                                    <span>Buka Buku Kendali</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- TAB 2: KARTU STOK PER BARANG -->
        <div x-show="activeTab === 'barang'" class="space-y-6" style="display: none;">
            <!-- BANNER EKSPOR KOLEKTIF REKAP POSISI PERSEDIAAN (BARANG AKTIF) (ADMIN/PIMPINAN ONLY) -->
            @if(auth()->user()->isAdmin() || auth()->user()->isPimpinan())
                <div class="bg-gradient-to-r from-slate-900 via-bps-navy-900 to-emerald-950 rounded-3xl p-5 text-white shadow-md flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 font-bold text-[10px] tracking-wider uppercase border border-emerald-500/30">
                                Laporan Kantor
                            </span>
                            <span class="text-xs text-slate-300 font-medium">Khusus barang yang ada stok atau pernah dipakai</span>
                        </div>
                        <h3 class="text-base font-extrabold text-white">Cetak & Unduh Rekap Stok Barang</h3>
                        <p class="text-xs text-slate-300 max-w-xl">
                            Cetak atau download rangkuman sisa barang di lemari kantor beserta total uangnya sekaligus tanpa baris kosong.
                        </p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2.5 w-full md:w-auto">
                        <!-- 1. Cetak Rekap Barang (PDF Landscape) -->
                        <a href="{{ route('kartu-kendali.barang.cetak-rekap', array_filter(['tahun' => $filter['tahun'] ?? null, 'bulan' => $filter['bulan'] ?? null, 'kategori_id' => $kategoriId ?? null, 'cakupan' => 'aktif'])) }}" 
                           target="_blank"
                           class="flex-1 md:flex-initial inline-flex items-center justify-center gap-1.5 px-3.5 py-2 rounded-xl bg-teal-600 hover:bg-teal-500 text-white font-bold text-xs transition shadow-xs"
                           title="Pratinjau cetak dan simpan PDF resmi rekapitulasi posisi barang aktif (Landscape)">
                            <svg class="w-4 h-4 text-teal-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            <span>Cetak Rekap (PDF)</span>
                        </a>
                        <!-- 2. Ekspor Excel (.xlsx) -->
                        <a href="{{ route('kartu-kendali.barang.export-rekap.excel', array_filter(['tahun' => $filter['tahun'] ?? null, 'bulan' => $filter['bulan'] ?? null, 'kategori_id' => $kategoriId ?? null, 'cakupan' => 'aktif'])) }}" 
                           class="flex-1 md:flex-initial inline-flex items-center justify-center gap-1.5 px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs transition shadow-xs"
                           title="Unduh berkas Excel .xlsx resmi rekapitulasi posisi barang aktif dengan formula otomatis">
                            <svg class="w-4 h-4 text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span>Ekspor Excel (.xlsx)</span>
                        </a>
                        <!-- 3. Ekspor Word (.doc) -->
                        <a href="{{ route('kartu-kendali.barang.export-rekap.word', array_filter(['tahun' => $filter['tahun'] ?? null, 'bulan' => $filter['bulan'] ?? null, 'kategori_id' => $kategoriId ?? null, 'cakupan' => 'aktif'])) }}" 
                           class="flex-1 md:flex-initial inline-flex items-center justify-center gap-1.5 px-3.5 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs transition shadow-xs"
                           title="Unduh dokumen Word (.doc) rekapitulasi posisi barang aktif siap edit">
                            <svg class="w-4 h-4 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span>Ekspor Word (.doc)</span>
                        </a>
                    </div>
                </div>
            @endif

            <!-- Filter Toolbar (Search + Kategori Dropdown + Akses Ekspor) -->
            <div class="bg-white p-5 rounded-3xl border border-slate-200/90 shadow-sm space-y-4">
                <div class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-4">
                    <form x-ref="barangFilterForm" 
                          action="{{ route('kartu-kendali.index') }}" 
                          method="GET" 
                          @submit.prevent="applyFilter()"
                          class="flex-1 flex flex-wrap items-center gap-2.5">
                        <input type="hidden" name="tab" value="barang">
                        <input type="hidden" name="tahun" value="{{ $filter['tahun'] ?? date('Y') }}">
                        @if(!empty($filter['bulan']))
                            <input type="hidden" name="bulan" value="{{ $filter['bulan'] }}">
                        @endif

                        <!-- Input Pencarian -->
                        <div class="relative flex-1 min-w-[240px]">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <input type="text" 
                                   name="search_barang" 
                                   value="{{ request('search_barang') }}" 
                                   placeholder="Cari kode atau nama barang persediaan..." 
                                   class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-medium focus:ring-2 focus:ring-teal-500 focus:bg-white focus:outline-none transition">
                        </div>

                        <!-- Dropdown Filter Kategori (Live AJAX onchange) -->
                        <div class="w-full sm:w-64">
                            <select name="kategori_id" 
                                    @change="applyFilter()" 
                                    class="w-full py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-medium focus:ring-2 focus:ring-teal-500 focus:bg-white focus:outline-none transition">
                                <option value="">Semua Kategori ({{ $kategoriList->count() }})</option>
                                @foreach($kategoriList as $kat)
                                    <option value="{{ $kat->id }}" {{ ($kategoriId == $kat->id) ? 'selected' : '' }}>
                                        {{ $kat->nama_kategori }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Tombol Cari & Reset -->
                        <div class="flex items-center gap-2 shrink-0">
                            <button type="submit" class="px-5 py-2.5 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-800 transition flex items-center gap-1.5 shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                <span>Cari</span>
                            </button>
                            <div id="kartu-reset-container">
                                @if(request()->filled('search_barang') || request()->filled('kategori_id'))
                                    <a href="{{ route('kartu-kendali.index', ['tab' => 'barang', 'tahun' => $filter['tahun'] ?? date('Y'), 'bulan' => $filter['bulan'] ?? '']) }}" 
                                       @click.prevent="resetBarangFilter('{{ route('kartu-kendali.index', ['tab' => 'barang', 'tahun' => $filter['tahun'] ?? date('Y'), 'bulan' => $filter['bulan'] ?? '']) }}')"
                                       class="p-2.5 rounded-xl text-slate-400 hover:text-rose-500 hover:bg-slate-100 transition shrink-0 block" 
                                       title="Reset Pencarian & Kategori">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>

                    <!-- Info Jumlah Item -->
                    <div class="flex items-center justify-between lg:justify-end gap-3 text-xs text-slate-500 pt-2 lg:pt-0 border-t lg:border-t-0 border-slate-100 shrink-0">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-50 border border-slate-200/80 font-medium">
                            <svg class="w-4 h-4 text-bps-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span>Menampilkan <strong>{{ $barangs->total() }}</strong> Item</span>
                        </span>
                    </div>
                </div>
            </div>

            <!-- KONTINER KARTU BARANG DENGAN LIVE AJAX UPDATE & LOADING SPINNER -->
            <div id="kartu-stok-barang-container" 
                 @click="handlePaginationClick($event)"
                 class="space-y-6 relative transition-opacity duration-200" 
                 :class="{ 'opacity-60 pointer-events-none': isLoading && activeTab === 'barang' }">
                
                <!-- Subtle Loading Indicator Overlay -->
                <div x-show="isLoading" 
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="absolute inset-0 bg-white/50 backdrop-blur-[1px] z-20 flex items-center justify-center pointer-events-none"
                     style="display: none;">
                    <div class="inline-flex items-center gap-2.5 px-4 py-2 rounded-2xl bg-bps-navy-900/90 text-white shadow-xl text-xs font-semibold">
                        <svg class="animate-spin w-4 h-4 text-bps-orange-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Memperbarui kartu stok...</span>
                    </div>
                </div>

                <!-- Barang Grid Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @forelse($barangs as $b)
                        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs hover:border-teal-400 hover:shadow-sm transition flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between">
                                    <span class="text-[10px] font-mono text-slate-400 font-bold">{{ $b->kode_barang }}</span>
                                    <span class="px-2 py-0.5 rounded-lg text-[10px] font-semibold bg-slate-100 text-slate-600">
                                        {{ $b->kategori?->nama_kategori ?? 'Umum' }}
                                    </span>
                                </div>
                                <h4 class="text-sm font-bold text-slate-900 mt-2 line-clamp-1">{{ $b->nama_barang }}</h4>
                                <div class="mt-3 flex items-baseline gap-2">
                                    <span class="text-xl font-black text-slate-800">{{ number_format($b->stok_aktif) }}</span>
                                    <span class="text-xs font-semibold text-slate-500">{{ $b->satuan }}</span>
                                    <span class="text-[11px] text-slate-400 ml-auto">Stok Gudang</span>
                                </div>
                            </div>

                            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                                <span class="text-[11px] text-slate-400 font-mono">
                                    Min: {{ number_format($b->stok_minimum) }} {{ $b->satuan }}
                                </span>
                                <a href="{{ route('kartu-kendali.barang', array_merge(['id' => $b->id], array_filter(['tahun' => $filter['tahun'] ?? null]))) }}" 
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-teal-50 hover:bg-teal-100 text-teal-800 text-xs font-bold border border-teal-200 transition">
                                    <span>Lihat Kartu Stok</span>
                                    <svg class="w-3.5 h-3.5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-1 md:col-span-2 lg:col-span-3 text-center py-16 bg-white rounded-3xl border border-slate-200 p-8">
                            <div class="w-14 h-14 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                            </div>
                            <p class="font-bold text-slate-700 text-sm">Tidak ada kartu persediaan yang cocok</p>
                            <p class="text-xs text-slate-400 mt-1">Coba sesuaikan kata kunci pencarian atau reset pilihan kategori.</p>
                        </div>
                    @endforelse
                </div>

                @if($barangs->hasPages())
                    <div class="p-4 bg-white rounded-2xl border border-slate-200">
                        {{ $barangs->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function kartuKendaliLiveFilter() {
            return {
                activeTab: '{{ request('tab', request()->hasAny(['search_barang', 'kategori_id', 'page']) ? 'barang' : 'ruangan') }}',
                isLoading: false,
                abortController: null,

                syncUrlTab(tab) {
                    const url = new URL(window.location.href);
                    url.searchParams.set('tab', tab);
                    window.history.replaceState({ path: url.href, tab: tab }, '', url.href);
                },

                async applyFilter(targetUrl = null) {
                    if (this.abortController) {
                        this.abortController.abort();
                    }
                    this.abortController = new AbortController();

                    let url = targetUrl;

                    if (!url) {
                        const periodForm = this.$refs.periodForm;
                        const barangForm = this.$refs.barangFilterForm;
                        const params = new URLSearchParams();

                        // 1. Ambil nilai periode dari banner
                        if (periodForm) {
                            const periodData = new FormData(periodForm);
                            for (const [key, val] of periodData.entries()) {
                                if (val !== '' && key !== 'tab') {
                                    params.append(key, val);
                                }
                            }
                        }

                        // 2. Ambil nilai pencarian & kategori jika sedang di tab barang
                        if (barangForm) {
                            const barangData = new FormData(barangForm);
                            for (const [key, val] of barangData.entries()) {
                                if (val !== '' && key !== 'tab' && !params.has(key)) {
                                    params.append(key, val);
                                }
                            }
                        }

                        params.set('tab', this.activeTab);
                        url = `${window.location.pathname}?${params.toString()}`;
                    }

                    this.isLoading = true;

                    try {
                        const response = await fetch(url, {
                            signal: this.abortController.signal,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'text/html,application/xhtml+xml'
                            }
                        });

                        if (!response.ok) throw new Error('Network response was not ok');

                        const htmlText = await response.text();
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(htmlText, 'text/html');

                        // 1. Update Tab 1 (Ruangan Container)
                        const newRuangan = doc.getElementById('kartu-kendali-ruangan-container');
                        const currentRuangan = document.getElementById('kartu-kendali-ruangan-container');
                        if (newRuangan && currentRuangan) {
                            currentRuangan.innerHTML = newRuangan.innerHTML;
                        }

                        // 2. Update Tab 2 (Barang Cards & Pagination Container)
                        const newBarang = doc.getElementById('kartu-stok-barang-container');
                        const currentBarang = document.getElementById('kartu-stok-barang-container');
                        if (newBarang && currentBarang) {
                            currentBarang.innerHTML = newBarang.innerHTML;
                        }

                        // 3. Update Reset Button Container
                        const newReset = doc.getElementById('kartu-reset-container');
                        const currentReset = document.getElementById('kartu-reset-container');
                        if (newReset && currentReset) {
                            currentReset.innerHTML = newReset.innerHTML;
                        }

                        // 4. Sinkronisasi URL Address Bar
                        if (window.location.href !== url) {
                            window.history.pushState({ path: url, tab: this.activeTab }, '', url);
                        }

                        // 5. Sinkronisasi input jika targetUrl digunakan (misal pagination atau reset)
                        if (targetUrl) {
                            const parsedUrl = new URL(url, window.location.origin);
                            const barangForm = this.$refs.barangFilterForm;
                            if (barangForm) {
                                if (barangForm.elements['search_barang']) {
                                    barangForm.elements['search_barang'].value = parsedUrl.searchParams.get('search_barang') || '';
                                }
                                if (barangForm.elements['kategori_id']) {
                                    barangForm.elements['kategori_id'].value = parsedUrl.searchParams.get('kategori_id') || '';
                                }
                            }
                        }
                    } catch (error) {
                        if (error.name !== 'AbortError') {
                            console.error('Filter request failed:', error);
                            // Fallback jika fetch terganggu
                            if (!targetUrl) {
                                if (this.activeTab === 'barang' && this.$refs.barangFilterForm) {
                                    this.$refs.barangFilterForm.submit();
                                } else if (this.$refs.periodForm) {
                                    this.$refs.periodForm.submit();
                                }
                            } else {
                                window.location.href = targetUrl;
                            }
                        }
                    } finally {
                        this.isLoading = false;
                    }
                },

                resetBarangFilter(resetUrl) {
                    const barangForm = this.$refs.barangFilterForm;
                    if (barangForm) {
                        if (barangForm.elements['search_barang']) barangForm.elements['search_barang'].value = '';
                        if (barangForm.elements['kategori_id']) barangForm.elements['kategori_id'].value = '';
                    }
                    this.applyFilter(resetUrl);
                },

                resetPeriod(resetUrl) {
                    const periodForm = this.$refs.periodForm;
                    if (periodForm) {
                        if (periodForm.elements['bulan']) periodForm.elements['bulan'].value = '';
                    }
                    this.applyFilter(resetUrl);
                },

                handlePaginationClick(event) {
                    const link = event.target.closest('a');
                    if (!link) return;

                    const isPagination = link.closest('.pagination') || link.closest('nav');
                    if (isPagination && link.href && link.href.startsWith(window.location.origin)) {
                        event.preventDefault();
                        this.applyFilter(link.href);
                    }
                },

                onPopState(event) {
                    if (event.state && event.state.tab) {
                        this.activeTab = event.state.tab;
                    }
                    this.applyFilter(window.location.href);
                }
            };
        }
    </script>
    @endpush
</x-app-layout>
