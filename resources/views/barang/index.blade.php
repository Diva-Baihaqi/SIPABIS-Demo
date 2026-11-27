<x-app-layout>
    <x-slot:title>Katalog Persediaan Barang</x-slot:title>
    <x-slot:header>Katalog Persediaan Barang Pakai Habis</x-slot:header>

    <div class="space-y-6" x-data="katalogLiveFilter()" @popstate.window="onPopState($event)">
        
        <!-- Header Banner & Ringkasan Metrik -->
        <div class="relative overflow-hidden bg-gradient-to-r from-bps-navy-800 via-bps-navy-700 to-bps-navy-900 rounded-3xl p-6 sm:p-7 text-white shadow-xl shadow-bps-navy-950/10 border border-bps-navy-700/80">
            <div class="absolute -right-10 -bottom-10 w-60 h-60 bg-bps-orange-500/15 rounded-full blur-2xl pointer-events-none"></div>
            
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="space-y-1.5">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-bps-orange-300 text-xs font-semibold border border-white/10">
                        <span>Kode UAKPB: 054.01.0200.018801 • Satker 428646</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white">Katalog Persediaan ATK & Logistik</h1>
                    <p class="text-xs sm:text-sm text-slate-300 max-w-2xl leading-relaxed">
                        Daftar lengkap master barang persediaan pakai habis BPS Kabupaten Cirebon. Pantau ketersediaan stok fisik gudang dan kartu kendali mutasi per barang.
                    </p>
                </div>

                @if(auth()->user()->isAdmin())
                    <div class="flex items-center gap-2.5 shrink-0">
                        <a href="{{ route('admin.kategori.index') }}" class="px-4 py-3 rounded-xl font-bold text-xs bg-white/10 hover:bg-white/20 text-white border border-white/15 transition-all flex items-center gap-2">
                            <svg class="w-4 h-4 text-bps-orange-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                            <span>Kelola Kategori</span>
                        </a>
                        <a href="{{ route('barang.create') }}" class="px-5 py-3 rounded-xl font-bold text-xs bg-emerald-600 hover:bg-emerald-700 text-white shadow-lg shadow-emerald-600/25 hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            <span>Tambah Barang Baru</span>
                        </a>
                    </div>
                @endif
            </div>

            <!-- Mini KPI Counters -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 mt-6 pt-6 border-t border-white/10">
                <a href="{{ route('barang.index') }}" class="p-3 rounded-2xl bg-white/5 hover:bg-white/10 transition border border-white/5">
                    <span class="text-[11px] text-slate-300 block font-medium">Total Katalog</span>
                    <span class="text-2xl font-black text-white mt-0.5 block">{{ number_format($totalKatalog) }}</span>
                    <span class="text-[10px] text-slate-400">Seluruh item persediaan</span>
                </a>
                <a href="{{ route('barang.index', ['status' => 'aman']) }}" class="p-3 rounded-2xl bg-white/5 hover:bg-white/10 transition border border-white/5">
                    <span class="text-[11px] text-emerald-300 block font-medium">Stok Aman</span>
                    <span class="text-2xl font-black text-emerald-400 mt-0.5 block">{{ number_format($totalAman) }}</span>
                    <span class="text-[10px] text-emerald-300/70">Di atas batas minimum</span>
                </a>
                <a href="{{ route('barang.index', ['status' => 'kritis']) }}" class="p-3 rounded-2xl bg-white/5 hover:bg-white/10 transition border border-white/5">
                    <span class="text-[11px] text-amber-300 block font-medium">Stok Kritis</span>
                    <span class="text-2xl font-black text-amber-400 mt-0.5 block">{{ number_format($totalKritis) }}</span>
                    <span class="text-[10px] text-amber-300/70">&le; Batas minimum</span>
                </a>
                <a href="{{ route('barang.index', ['status' => 'habis']) }}" class="p-3 rounded-2xl bg-white/5 hover:bg-white/10 transition border border-white/5">
                    <span class="text-[11px] text-rose-300 block font-medium">Stok Kosong</span>
                    <span class="text-2xl font-black text-rose-400 mt-0.5 block">{{ number_format($totalHabis) }}</span>
                    <span class="text-[10px] text-rose-300/70">Perlu pengadaan segera</span>
                </a>
            </div>
        </div>

        <!-- Filter & Search Toolbar (Live Dynamic Filter) -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm space-y-4">
            <form x-ref="filterForm" 
                  method="GET" 
                  action="{{ route('barang.index') }}" 
                  @submit.prevent="applyFilter()"
                  class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3.5">
                
                <!-- Search Input (5 Kolom) -->
                <div class="lg:col-span-5 relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" 
                           name="search" 
                           value="{{ $search }}" 
                           placeholder="Cari nama barang, kode BPS, atau kode SAKTI..."
                           class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-medium focus:ring-2 focus:ring-bps-blue-500 focus:bg-white focus:outline-none transition">
                </div>

                <!-- Kategori Dropdown (3 Kolom) -->
                <div class="lg:col-span-3">
                    <select name="kategori_id" 
                            @change="applyFilter()" 
                            class="w-full py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-medium focus:ring-2 focus:ring-bps-blue-500 focus:bg-white focus:outline-none transition">
                        <option value="">Semua Kategori ({{ $kategoriList->count() }})</option>
                        @foreach($kategoriList as $kat)
                            <option value="{{ $kat->id }}" {{ $kategoriId == $kat->id ? 'selected' : '' }}>
                                {{ $kat->nama_kategori }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Status (2 Kolom) -->
                <div class="lg:col-span-2">
                    <select name="status" 
                            @change="applyFilter()" 
                            class="w-full py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-medium focus:ring-2 focus:ring-bps-blue-500 focus:bg-white focus:outline-none transition">
                        <option value="">Status Stok (Semua)</option>
                        <option value="aman" {{ $status === 'aman' ? 'selected' : '' }}>Stok Aman</option>
                        <option value="kritis" {{ $status === 'kritis' ? 'selected' : '' }}>Stok Kritis</option>
                        <option value="habis" {{ $status === 'habis' ? 'selected' : '' }}>Stok Kosong</option>
                    </select>
                </div>

                <!-- Sort & Submit (2 Kolom) -->
                <div class="lg:col-span-2 flex items-center gap-2">
                    <select name="sort" 
                            @change="applyFilter()" 
                            class="w-full py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-medium focus:ring-2 focus:ring-bps-blue-500 focus:bg-white focus:outline-none transition">
                        <option value="nama_asc" {{ $sort === 'nama_asc' ? 'selected' : '' }}>Nama A-Z</option>
                        <option value="nama_desc" {{ $sort === 'nama_desc' ? 'selected' : '' }}>Nama Z-A</option>
                        <option value="stok_asc" {{ $sort === 'stok_asc' ? 'selected' : '' }}>Stok Terkecil</option>
                        <option value="stok_desc" {{ $sort === 'stok_desc' ? 'selected' : '' }}>Stok Terbesar</option>
                    </select>

                    <button type="submit" class="px-4 py-2.5 rounded-xl font-bold text-xs bg-bps-navy-800 hover:bg-bps-navy-900 text-white transition shrink-0">
                        Filter
                    </button>
                    <div id="katalog-reset-container">
                        @if($search || $kategoriId || $status || $sort !== 'nama_asc')
                            <a href="{{ route('barang.index') }}" 
                               @click.prevent="resetFilter('{{ route('barang.index') }}')" 
                               title="Reset Filter" 
                               class="p-2.5 rounded-xl text-slate-400 hover:text-rose-500 hover:bg-slate-100 transition shrink-0 block">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </a>
                        @endif
                    </div>
                </div>

            </form>
        </div>

        <!-- Feedback Alert Messages -->
        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    <span class="font-semibold">{{ session('success') }}</span>
                </div>
            </div>
        @endif
        @if(session('error'))
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs sm:text-sm flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <svg class="w-5 h-5 text-rose-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    <span class="font-semibold">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        <!-- Catalog Data Table (Container for Live Partial Updates) -->
        <div id="katalog-table-container" 
             @click="handleTableClick($event)"
             class="relative bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden transition-opacity duration-200"
             :class="{ 'opacity-60': isLoading }">
            
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
                    <span>Memperbarui daftar...</span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs sm:text-sm">
                    <thead class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] uppercase tracking-wider text-slate-500 font-bold">
                        <tr>
                            <th class="py-3.5 px-4 text-center w-12">No</th>
                            <th class="py-3.5 px-4">Kode & Klasifikasi</th>
                            <th class="py-3.5 px-4">Nama Barang Persediaan</th>
                            <th class="py-3.5 px-4 text-center">Satuan</th>
                            <th class="py-3.5 px-4 text-right">Stok Aktif</th>
                            <th class="py-3.5 px-4 text-center">Status</th>
                            <th class="py-3.5 px-4 text-right">Harga Estimasi</th>
                            <th class="py-3.5 px-4 text-center">Aksi & Buku Stok</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($barangList as $index => $b)
                            <tr class="hover:bg-slate-50/70 transition">
                                <!-- No -->
                                <td class="py-3.5 px-4 text-center text-slate-400 font-medium">
                                    {{ $barangList->firstItem() + $index }}
                                </td>

                                <!-- Kode Barang & Kategori -->
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="font-mono font-bold text-slate-800">{{ $b->kode_barang }}</span>
                                    @if($b->kode_sakti)
                                        <span class="text-[10px] text-slate-400 block font-mono">SAKTI: {{ $b->kode_sakti }}</span>
                                    @endif
                                    <span class="inline-block mt-0.5 px-2 py-0.5 rounded-md text-[10px] font-semibold bg-slate-100 text-slate-600">
                                        {{ $b->kategori?->nama_kategori ?? 'Umum' }}
                                    </span>
                                </td>

                                <!-- Nama Barang + Thumbnail Foto -->
                                <td class="py-3.5 px-4">
                                    <div class="flex items-center gap-3">
                                        <button type="button"
                                                data-foto-preview="{{ $b->foto_url }}"
                                                data-foto-title="{{ $b->nama_barang }}"
                                                title="Klik untuk memperbesar gambar barang"
                                                class="w-11 h-11 rounded-xl border border-slate-200/90 bg-slate-50 overflow-hidden shrink-0 flex items-center justify-center hover:border-bps-blue-400 hover:shadow-xs transition cursor-pointer group">
                                            <img src="{{ $b->foto_url }}"
                                                 alt="{{ $b->nama_barang }}"
                                                 loading="lazy"
                                                 class="w-full h-full {{ $b->has_real_foto ? 'object-cover' : 'object-contain p-1.5 opacity-85' }} group-hover:scale-110 transition-transform duration-200">
                                        </button>
                                        <div class="min-w-0">
                                            <p class="font-bold text-slate-900">{{ $b->nama_barang }}</p>
                                            @if($b->lokasi_rak)
                                                <p class="text-[11px] text-slate-400 flex items-center gap-1 mt-0.5">
                                                    <span>Lokasi: Rak {{ $b->lokasi_rak }}</span>
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <!-- Satuan -->
                                <td class="py-3.5 px-4 text-center font-medium text-slate-600">
                                    {{ $b->satuan }}
                                </td>

                                <!-- Stok Aktif & Min -->
                                <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                    <span class="text-base font-black 
                                        @if($b->stok_aktif <= 0) text-rose-600
                                        @elseif($b->stok_aktif <= $b->stok_minimum) text-amber-600
                                        @else text-emerald-600 @endif">
                                        {{ number_format($b->stok_aktif) }}
                                    </span>
                                    <span class="text-[10px] text-slate-400 block">Min: {{ number_format($b->stok_minimum) }}</span>
                                </td>

                                <!-- Status Badge -->
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    @if($b->stok_aktif <= 0)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span> Habis
                                        </span>
                                    @elseif($b->stok_aktif <= $b->stok_minimum)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span> Kritis
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span> Aman
                                        </span>
                                    @endif
                                </td>

                                <!-- Harga Estimasi -->
                                <td class="py-3.5 px-4 text-right font-mono text-slate-700 whitespace-nowrap">
                                    @if($b->harga_satuan_estimasi > 0)
                                        Rp {{ number_format($b->harga_satuan_estimasi, 0, ',', '.') }}
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>

                                <!-- Aksi & Buku Kartu Stok -->
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1.5">
                                        <!-- Tombol Kartu Stok Perpetual FIFO -->
                                        <a href="{{ route('kartu-kendali.barang', $b->id) }}" title="Lihat Kartu Stok Perpetual FIFO (UC_PER53)"
                                           class="px-2.5 py-1.5 rounded-xl bg-bps-blue-50 hover:bg-bps-blue-100 text-bps-blue-700 font-bold text-xs transition flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            <span>Kartu Stok</span>
                                        </a>

                                        @if(auth()->user()->isAdmin())
                                            <!-- Tombol Edit -->
                                            <a href="{{ route('barang.edit', $b->id) }}" title="Edit Data Barang"
                                               class="p-1.5 rounded-xl text-slate-500 hover:text-bps-orange-600 hover:bg-slate-100 transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </a>

                                            <!-- Tombol Hapus (Hanya jika belum ada riwayat) -->
                                            <form action="{{ route('barang.destroy', $b->id) }}" method="POST" class="inline"
                                                  data-confirm="Apakah Anda yakin ingin menghapus barang persediaan '{{ $b->nama_barang }}'? Tindakan ini tidak dapat dibatalkan."
                                                  data-confirm-title="Hapus Barang Persediaan"
                                                  data-confirm-type="danger"
                                                  data-confirm-btn="Ya, Hapus Barang">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" title="Hapus Barang" class="p-1.5 rounded-xl text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-16 text-slate-500">
                                    <div class="w-14 h-14 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                                    </div>
                                    <p class="font-bold text-slate-700 text-sm">Tidak ada barang persediaan yang cocok</p>
                                    <p class="text-xs text-slate-400 mt-1">Coba ubah kata kunci pencarian atau reset filter kategori.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($barangList->hasPages())
                <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $barangList->links() }}
                </div>
            @endif
        </div>

        <!-- Modal Lightbox Pratinjau Foto Barang -->
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

    @push('scripts')
    <script>
        function katalogLiveFilter() {
            return {
                isLoading: false,
                abortController: null,
                previewUrl: null,
                previewTitle: '',

                async applyFilter(targetUrl = null) {
                    if (this.abortController) {
                        this.abortController.abort();
                    }
                    this.abortController = new AbortController();

                    const form = this.$refs.filterForm;
                    let url = targetUrl;

                    if (!url) {
                        const formData = new FormData(form);
                        const params = new URLSearchParams();
                        for (const [key, value] of formData.entries()) {
                            if (value !== '') {
                                params.append(key, value);
                            }
                        }
                        url = `${form.action}?${params.toString()}`;
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

                        // 1. Update Table Container
                        const newTable = doc.getElementById('katalog-table-container');
                        const currentTable = document.getElementById('katalog-table-container');
                        if (newTable && currentTable) {
                            currentTable.innerHTML = newTable.innerHTML;
                        }

                        // 2. Update Reset Button Container
                        const newReset = doc.getElementById('katalog-reset-container');
                        const currentReset = document.getElementById('katalog-reset-container');
                        if (newReset && currentReset) {
                            currentReset.innerHTML = newReset.innerHTML;
                        }

                        // 3. Sync browser URL without full-page reload
                        if (window.location.href !== url) {
                            window.history.pushState({ path: url }, '', url);
                        }

                        // 4. Also sync form values if targetUrl was used (e.g. pagination or reset or popstate)
                        if (targetUrl) {
                            const parsedUrl = new URL(url, window.location.origin);
                            if (form.elements['search']) form.elements['search'].value = parsedUrl.searchParams.get('search') || '';
                            if (form.elements['kategori_id']) form.elements['kategori_id'].value = parsedUrl.searchParams.get('kategori_id') || '';
                            if (form.elements['status']) form.elements['status'].value = parsedUrl.searchParams.get('status') || '';
                            if (form.elements['sort']) form.elements['sort'].value = parsedUrl.searchParams.get('sort') || 'nama_asc';
                        }
                    } catch (error) {
                        if (error.name !== 'AbortError') {
                            console.error('Filter request failed:', error);
                            // Fallback to normal submission if fetch fails
                            if (!targetUrl) form.submit();
                            else window.location.href = targetUrl;
                        }
                    } finally {
                        this.isLoading = false;
                    }
                },

                resetFilter(resetUrl) {
                    const form = this.$refs.filterForm;
                    if (form.elements['search']) form.elements['search'].value = '';
                    if (form.elements['kategori_id']) form.elements['kategori_id'].value = '';
                    if (form.elements['status']) form.elements['status'].value = '';
                    if (form.elements['sort']) form.elements['sort'].value = 'nama_asc';

                    this.applyFilter(resetUrl);
                },

                handleTableClick(event) {
                    const photoTrigger = event.target.closest('[data-foto-preview]');
                    if (photoTrigger) {
                        event.preventDefault();
                        this.previewUrl = photoTrigger.dataset.fotoPreview;
                        this.previewTitle = photoTrigger.dataset.fotoTitle || '';
                        return;
                    }

                    const link = event.target.closest('a');
                    if (!link) return;

                    // Intercept pagination links
                    const isPagination = link.closest('.pagination') || link.closest('nav');
                    if (isPagination && link.href && link.href.startsWith(window.location.origin)) {
                        event.preventDefault();
                        this.applyFilter(link.href);
                    }
                },

                onPopState(event) {
                    this.applyFilter(window.location.href);
                }
            };
        }
    </script>
    @endpush
</x-app-layout>
