@php
    $barangsData = $barangs->map(fn ($b) => [
        'id' => $b->id,
        'kode' => $b->kode_barang,
        'nama' => $b->nama_barang,
        'kategori_id' => $b->kategori_id,
        'kategori' => $b->kategori?->nama_kategori ?? 'Umum',
        'satuan' => $b->satuan,
        'stok' => $b->stok_aktif,
        'lokasi_rak' => $b->lokasi_rak,
        'keterangan' => $b->keterangan,
        'foto_url' => $b->foto_url,
        'has_real_foto' => $b->has_real_foto,
    ])->values();

    $barangById = $barangsData->keyBy('id');
    $oldItems = old('items');
    $initialCart = [];

    if (is_array($oldItems) && count($oldItems) > 0) {
        foreach ($oldItems as $row) {
            $bId = $row['barang_id'] ?? '';
            $matched = $bId ? $barangById->get((int) $bId) : null;
            $initialCart[] = [
                'barang_id' => $matched ? $matched['id'] : '',
                'nama' => $matched ? $matched['nama'] : '',
                'kode' => $matched ? $matched['kode'] : '',
                'kategori' => $matched ? $matched['kategori'] : '',
                'foto_url' => $matched ? $matched['foto_url'] : '',
                'has_real_foto' => $matched ? $matched['has_real_foto'] : false,
                'lokasi_rak' => $matched ? $matched['lokasi_rak'] : '',
                'satuan' => $matched ? $matched['satuan'] : '-',
                'stok' => $matched ? $matched['stok'] : 0,
                'jumlah_diminta' => max(1, (int) ($row['jumlah_diminta'] ?? 1)),
                'catatan_item' => $row['catatan_item'] ?? '',
            ];
        }
    }

    if (empty($initialCart)) {
        $initialCart[] = [
            'barang_id' => '',
            'nama' => '',
            'kode' => '',
            'kategori' => '',
            'foto_url' => '',
            'has_real_foto' => false,
            'lokasi_rak' => '',
            'satuan' => '-',
            'stok' => 0,
            'jumlah_diminta' => 1,
            'catatan_item' => '',
        ];
    }
@endphp

<x-app-layout>
    <x-slot:title>Ajukan Permintaan ATK</x-slot:title>
    <x-slot:header>Formulir Pengajuan Permintaan ATK Baru</x-slot:header>

    <div class="max-w-6xl mx-auto space-y-6"
         x-data="permintaanCreateForm(@js($barangsData), @js($initialCart))"
         @keydown.escape.window="previewItem = null; activeRowIndex = null">

        <!-- Top Breadcrumb & Unit Info -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <a href="{{ route('pegawai.permintaan.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-600 hover:text-slate-900 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Kembali ke Riwayat Permintaan</span>
            </a>
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-slate-200/70 text-slate-700 text-xs font-semibold">
                <span class="w-2 h-2 rounded-full bg-teal-500"></span>
                <span>Unit Pengusul: <strong>{{ $user->ruangan?->nama_ruangan ?? 'Belum ditentukan' }}</strong></span>
            </div>
        </div>

        @if($errors->any())
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs">
                <div class="font-bold mb-1">Pengajuan gagal, mohon periksa data:</div>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('pegawai.permintaan.store') }}" method="POST" @submit="validateBeforeSubmit($event)" class="space-y-6">
            @csrf

            <!-- Section 1: Informasi Keperluan & Peruntukan -->
            <div class="bg-white p-6 md:p-7 rounded-3xl border border-slate-200/90 shadow-sm space-y-4">
                <div class="border-b border-slate-100 pb-3">
                    <h3 class="text-base font-extrabold text-slate-900">1. Peruntukan / Keperluan Pengajuan ATK</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Tuliskan kegiatan sensus, survei, atau operasional yang membutuhkan persediaan ini.</p>
                </div>

                <div>
                    <label for="catatan_pemohon" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Deskripsi Kegiatan / Catatan Permintaan <span class="text-rose-500">*</span>
                    </label>
                    <textarea id="catatan_pemohon"
                              name="catatan_pemohon"
                              rows="2"
                              required
                              placeholder="Contoh: Kebutuhan ATK untuk pelatihan petugas Survei Angkatan Kerja Nasional (Sakernas) 2026 dan operasional pengolahan data bulanan..."
                              class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs focus:ring-2 focus:ring-teal-500 focus:bg-white transition">{{ old('catatan_pemohon') }}</textarea>
                </div>
            </div>

            <!-- Section 2: Katalog Cepat Barang Persediaan (Visual E-Catalog) -->
            <div class="bg-white p-6 md:p-7 rounded-3xl border border-slate-200/90 shadow-sm space-y-5">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-base font-extrabold text-slate-900">2. Pilih Barang dari Katalog Bergambar</h3>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-teal-50 text-teal-700 border border-teal-200/80">
                                <span x-text="filteredCatalog.length"></span> tersedia
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">Klik foto barang untuk melihat detail lebih jelas, atau klik <strong>+ Pilih</strong> untuk langsung menambahkannya ke daftar pengajuan.</p>
                    </div>

                    <button type="button"
                            @click="showCatalog = !showCatalog"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition shrink-0 cursor-pointer">
                        <svg class="w-4 h-4 transition-transform duration-200" :class="showCatalog ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                        <span x-text="showCatalog ? 'Sembunyikan Katalog' : 'Tampilkan Katalog'"></span>
                    </button>
                </div>

                <div x-show="showCatalog" x-transition class="space-y-4">
                    <!-- Pencarian & Pills Kategori -->
                    <div class="space-y-3">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <input type="text"
                                   x-model="searchCatalog"
                                   placeholder="Cari cepat nama barang, kode BPS, kategori, atau lokasi rak..."
                                   class="w-full pl-10 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs focus:ring-2 focus:ring-teal-500 focus:bg-white focus:outline-none transition">
                            <button type="button"
                                    x-show="searchCatalog.length > 0"
                                    @click="searchCatalog = ''"
                                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 cursor-pointer"
                                    title="Hapus pencarian">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <!-- Category Pills -->
                        <div class="flex items-center gap-2 overflow-x-auto pb-1 text-xs">
                            <button type="button"
                                    @click="selectedKategori = 'all'"
                                    :class="selectedKategori === 'all' ? 'bg-slate-900 text-white font-bold shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200 hover:text-slate-900'"
                                    class="px-3.5 py-1.5 rounded-full whitespace-nowrap transition cursor-pointer">
                                Semua Kategori ({{ $barangs->count() }})
                            </button>
                            @foreach($kategoriList as $kat)
                                <button type="button"
                                        @click="selectedKategori = '{{ $kat->id }}'"
                                        :class="selectedKategori === '{{ $kat->id }}' ? 'bg-slate-900 text-white font-bold shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200 hover:text-slate-900'"
                                        class="px-3.5 py-1.5 rounded-full whitespace-nowrap transition cursor-pointer">
                                    {{ $kat->nama_kategori }} ({{ $kat->aktif_count }})
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- Scrollable Catalog Grid -->
                    <div x-show="filteredCatalog.length > 0"
                         class="max-h-[420px] overflow-y-auto pr-1.5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3.5">
                        <template x-for="b in filteredCatalog" :key="'cat-' + b.id">
                            <div :class="inCartQty(b.id) > 0 ? 'border-emerald-400 bg-emerald-50/20 ring-1 ring-emerald-200' : 'border-slate-200/90 bg-white hover:border-teal-400'"
                                 class="group rounded-2xl border overflow-hidden shadow-sm hover:shadow-md transition-all duration-200 flex flex-col justify-between">
                                <!-- Foto Thumbnail Frame -->
                                <div>
                                    <div @click="openPreview(b)"
                                         title="Klik untuk memperbesar foto barang"
                                         class="relative h-32 w-full bg-slate-50 border-b border-slate-100 overflow-hidden flex items-center justify-center cursor-pointer">
                                        <img :src="b.foto_url"
                                             :alt="b.nama"
                                             loading="lazy"
                                             :class="b.has_real_foto ? 'w-full h-full object-cover group-hover:scale-105' : 'w-16 h-16 object-contain opacity-90 group-hover:scale-110'"
                                             class="transition-transform duration-300">

                                        <!-- Badge Kategori -->
                                        <div class="absolute top-2 left-2 max-w-[58%]">
                                            <span class="inline-block truncate px-2 py-0.5 rounded-full bg-white/90 backdrop-blur-sm text-slate-700 text-[10px] font-bold border border-slate-200/70 shadow-sm"
                                                  x-text="b.kategori"></span>
                                        </div>

                                        <!-- Badge Stok -->
                                        <div class="absolute top-2 right-2">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-50/95 text-emerald-700 border border-emerald-200/80 shadow-sm">
                                                <span x-text="b.stok + ' ' + b.satuan"></span>
                                            </span>
                                        </div>

                                        <!-- Hover Zoom Indicator -->
                                        <div class="absolute inset-0 bg-slate-900/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center pointer-events-none">
                                            <span class="px-2.5 py-1 rounded-full bg-white/95 text-slate-800 text-[10px] font-bold shadow-sm inline-flex items-center gap-1">
                                                <svg class="w-3 h-3 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/></svg>
                                                <span>Lihat Foto</span>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="p-3 pb-2 space-y-1">
                                        <div class="flex items-center justify-between text-[10px] text-slate-400 font-mono">
                                            <span x-text="b.kode"></span>
                                            <span x-show="b.lokasi_rak" x-text="'Rak ' + b.lokasi_rak" class="truncate max-w-[90px]"></span>
                                        </div>
                                        <h4 class="text-xs font-bold text-slate-900 line-clamp-2 leading-snug min-h-[2.2em]" x-text="b.nama"></h4>
                                    </div>
                                </div>

                                <div class="px-3 pb-3 pt-1 flex items-center justify-between gap-2">
                                    <template x-if="inCartQty(b.id) > 0">
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-emerald-100 text-emerald-800 text-[10px] font-bold">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                            <span x-text="'Di Daftar (' + inCartQty(b.id) + ')'"></span>
                                        </span>
                                    </template>
                                    <template x-if="inCartQty(b.id) === 0">
                                        <span class="text-[10px] text-slate-400 font-medium" x-text="'Satuan: ' + b.satuan"></span>
                                    </template>

                                    <button type="button"
                                            @click="addFromCatalog(b)"
                                            :disabled="inCartQty(b.id) >= b.stok"
                                            :class="inCartQty(b.id) >= b.stok
                                                ? 'bg-slate-200 text-slate-400 cursor-not-allowed'
                                                : (inCartQty(b.id) > 0 ? 'bg-emerald-600 hover:bg-emerald-700 text-white' : 'bg-slate-900 hover:bg-teal-700 text-white')"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-[11px] font-bold transition active:scale-95 cursor-pointer shrink-0">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                        <span x-text="inCartQty(b.id) > 0 ? 'Tambah' : 'Pilih'"></span>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Empty Search State -->
                    <div x-show="filteredCatalog.length === 0" class="py-8 text-center rounded-2xl bg-slate-50 border border-dashed border-slate-200 text-slate-500 text-xs">
                        <p class="font-bold text-slate-700">Barang tidak ditemukan pada pencarian katalog.</p>
                        <button type="button" @click="searchCatalog = ''; selectedKategori = 'all'" class="mt-2 text-teal-700 font-bold hover:underline cursor-pointer">
                            Tampilkan semua barang tersedia
                        </button>
                    </div>
                </div>
            </div>

            <!-- Section 3: Daftar Item Permintaan (Cart Table with Thumbnails & Searchable Combobox) -->
            <div class="bg-white p-6 md:p-7 rounded-3xl border border-slate-200/90 shadow-sm space-y-5">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 border-b border-slate-100 pb-4">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900">3. Rincian Barang yang Diajukan</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Periksa foto barang, atur jumlah diminta, atau tambah baris baru secara manual.</p>
                    </div>

                    <button type="button"
                            @click="addItem()"
                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-emerald-50 hover:bg-emerald-100/90 text-emerald-800 text-xs font-bold border border-emerald-300/80 shadow-sm hover:shadow transition-all duration-150 active:scale-95 cursor-pointer">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                        <span>Tambah Baris Barang</span>
                    </button>
                </div>

                <!-- Client-side missing row validation message -->
                <div x-show="validationWarning"
                     x-transition
                     class="p-3.5 rounded-2xl bg-amber-50 border border-amber-200 text-amber-800 text-xs flex items-center gap-2"
                     style="display: none;">
                    <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span x-text="validationWarning"></span>
                </div>

                <div class="overflow-x-auto md:overflow-visible">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-200">
                                <th class="py-3 px-3 w-10 text-center">#</th>
                                <th class="py-3 px-4">Barang Persediaan</th>
                                <th class="py-3 px-3 w-28 text-center">Stok Gudang</th>
                                <th class="py-3 px-3 w-40 text-center">Jumlah Diminta</th>
                                <th class="py-3 px-3 w-48">Catatan Item (Opsional)</th>
                                <th class="py-3 px-3 w-14 text-center">Hapus</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <template x-for="(item, index) in cart" :key="index">
                                <tr class="hover:bg-slate-50/60 transition align-middle">
                                    <td class="py-3.5 px-3 text-center font-bold text-slate-400" x-text="index + 1"></td>

                                    <!-- Thumbnail + Searchable Combobox -->
                                    <td class="py-3.5 px-4">
                                        <input type="hidden" :name="'items[' + index + '][barang_id]'" :value="item.barang_id">

                                        <div class="flex items-center gap-3 relative">
                                            <!-- Thumbnail Preview -->
                                            <button type="button"
                                                    @click="item.barang_id ? openPreviewById(item.barang_id) : openRowPicker(index)"
                                                    :title="item.barang_id ? 'Klik untuk memperbesar foto' : 'Pilih barang terlebih dahulu'"
                                                    class="w-12 h-12 rounded-xl border border-slate-200/90 bg-slate-50 overflow-hidden shrink-0 flex items-center justify-center hover:border-teal-400 hover:shadow-sm transition cursor-pointer group">
                                                <template x-if="item.barang_id && item.foto_url">
                                                    <img :src="item.foto_url"
                                                         :alt="item.nama"
                                                         :class="item.has_real_foto ? 'w-full h-full object-cover' : 'w-8 h-8 object-contain opacity-90'"
                                                         class="group-hover:scale-110 transition-transform duration-200">
                                                </template>
                                                <template x-if="!item.barang_id">
                                                    <svg class="w-5 h-5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                </template>
                                            </button>

                                            <!-- Searchable Combobox Trigger & Dropdown -->
                                            <div class="flex-1 min-w-[200px] relative" @click.outside="if (activeRowIndex === index) activeRowIndex = null">
                                                <button type="button"
                                                        @click="openRowPicker(index)"
                                                        class="w-full text-left py-2 px-3 bg-slate-50 hover:bg-white border border-slate-200 rounded-xl text-xs flex items-center justify-between gap-2 focus:ring-2 focus:ring-teal-500 transition cursor-pointer">
                                                    <div class="min-w-0">
                                                        <template x-if="item.barang_id">
                                                            <div>
                                                                <p class="font-bold text-slate-900 truncate" x-text="item.nama"></p>
                                                                <p class="text-[10px] text-slate-500 font-mono truncate" x-text="item.kode + ' • ' + item.kategori"></p>
                                                            </div>
                                                        </template>
                                                        <template x-if="!item.barang_id">
                                                            <span class="text-slate-400 font-medium">-- Klik untuk cari / pilih barang --</span>
                                                        </template>
                                                    </div>
                                                    <svg class="w-4 h-4 text-slate-400 shrink-0 transition-transform duration-150" :class="activeRowIndex === index ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                                </button>

                                                <!-- Floating Visual Dropdown Panel -->
                                                <div x-show="activeRowIndex === index"
                                                     x-transition
                                                     class="absolute left-0 right-0 mt-1.5 z-40 bg-white rounded-2xl border border-slate-200 shadow-2xl overflow-hidden min-w-[280px]"
                                                     style="display: none;">
                                                    <div class="p-2 border-b border-slate-100 bg-slate-50">
                                                        <input type="text"
                                                               x-model="rowSearch"
                                                               placeholder="Ketik nama / kode barang..."
                                                               class="w-full px-3 py-1.5 bg-white border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-teal-500 focus:outline-none">
                                                    </div>
                                                    <div class="max-h-60 overflow-y-auto divide-y divide-slate-100">
                                                        <template x-for="opt in filteredRowOptions" :key="'opt-' + index + '-' + opt.id">
                                                            <button type="button"
                                                                    @click="selectBarangForRow(index, opt)"
                                                                    :class="item.barang_id == opt.id ? 'bg-teal-50/90' : 'hover:bg-slate-50'"
                                                                    class="w-full text-left px-3 py-2 flex items-center gap-2.5 transition cursor-pointer">
                                                                <div class="w-9 h-9 rounded-lg border border-slate-200 bg-white overflow-hidden shrink-0 flex items-center justify-center">
                                                                    <img :src="opt.foto_url"
                                                                         :alt="opt.nama"
                                                                         :class="opt.has_real_foto ? 'w-full h-full object-cover' : 'w-6 h-6 object-contain opacity-90'">
                                                                </div>
                                                                <div class="flex-1 min-w-0">
                                                                    <p class="font-bold text-slate-900 truncate text-[11px]" x-text="opt.nama"></p>
                                                                    <p class="text-[10px] text-slate-500 truncate" x-text="opt.kode + ' • ' + opt.kategori"></p>
                                                                </div>
                                                                <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 font-mono text-[10px] font-bold shrink-0" x-text="opt.stok + ' ' + opt.satuan"></span>
                                                            </button>
                                                        </template>
                                                        <div x-show="filteredRowOptions.length === 0" class="py-4 text-center text-[11px] text-slate-400">
                                                            Tidak ada barang yang cocok.
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="py-3.5 px-3 text-center whitespace-nowrap">
                                        <span class="inline-block px-2.5 py-1 rounded-xl bg-slate-100 text-slate-700 font-mono font-bold" x-text="item.stok + ' ' + item.satuan"></span>
                                    </td>

                                    <td class="py-3.5 px-3 text-center">
                                        <div class="inline-flex items-center gap-1">
                                            <button type="button"
                                                    @click="decrementQty(index)"
                                                    :disabled="item.jumlah_diminta <= 1"
                                                    class="w-7 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 disabled:opacity-40 text-slate-700 font-bold transition cursor-pointer">-</button>
                                            <input type="number"
                                                   :name="'items[' + index + '][jumlah_diminta]'"
                                                   x-model.number="item.jumlah_diminta"
                                                   min="1"
                                                   :max="item.stok > 0 ? item.stok : 9999"
                                                   required
                                                   class="w-16 text-center py-1.5 px-1 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-900 focus:ring-2 focus:ring-teal-500 focus:bg-white transition">
                                            <button type="button"
                                                    @click="incrementQty(index)"
                                                    :disabled="item.stok > 0 && item.jumlah_diminta >= item.stok"
                                                    class="w-7 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 disabled:opacity-40 text-slate-700 font-bold transition cursor-pointer">+</button>
                                        </div>
                                        <div class="text-[10px] text-slate-500 font-semibold mt-0.5" x-show="item.barang_id" x-text="item.satuan"></div>
                                        <div x-show="item.stok > 0 && item.jumlah_diminta > item.stok" class="text-[10px] text-amber-600 font-medium mt-1">
                                            Melebihi stok gudang (bisa disetujui sebagian)
                                        </div>
                                    </td>

                                    <td class="py-3.5 px-3">
                                        <input type="text"
                                               :name="'items[' + index + '][catatan_item]'"
                                               x-model="item.catatan_item"
                                               placeholder="Contoh: Warna merah / spidol hitam"
                                               class="w-full py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-teal-500 focus:bg-white transition">
                                    </td>

                                    <td class="py-3.5 px-3 text-center">
                                        <button type="button"
                                                @click="removeItem(index)"
                                                :disabled="cart.length <= 1 && !item.barang_id"
                                                :class="(cart.length <= 1 && !item.barang_id) ? 'opacity-30 cursor-not-allowed' : 'text-rose-500 hover:text-rose-700 hover:bg-rose-50 cursor-pointer'"
                                                class="p-2 rounded-xl transition"
                                                title="Hapus baris">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <div class="flex flex-wrap items-center justify-between gap-2 pt-4 border-t border-slate-100">
                    <div class="flex items-center gap-3 text-xs text-slate-500">
                        <span>
                            Total Jenis Barang Diajukan: <span class="font-bold text-slate-800" x-text="totalDipilihCount"></span> Item
                        </span>
                        <span class="text-slate-300" x-show="totalDipilihCount > 0">•</span>
                        <span x-show="totalDipilihCount > 0">
                            Total Unit: <span class="font-bold text-teal-700" x-text="totalUnitCount"></span>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Submit CTA Bar -->
            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('pegawai.permintaan.index') }}" class="px-6 py-2.5 rounded-full border border-slate-300 text-slate-700 font-bold text-xs hover:bg-slate-50 transition">
                    Batal
                </a>
                <button type="submit" class="inline-flex items-center gap-2 px-8 py-2.5 rounded-full bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-md transition transform hover:scale-105 cursor-pointer">
                    <svg class="w-4 h-4 text-brand-lime" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    <span>Kirim Pengajuan ke Subbag Umum</span>
                </button>
            </div>
        </form>

        <!-- Lightbox Modal Preview Foto & Detail Barang -->
        <div x-show="previewItem !== null"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click.self="previewItem = null"
             class="fixed inset-0 z-50 bg-slate-950/75 backdrop-blur-sm flex items-center justify-center p-4"
             style="display: none;">
            <div x-show="previewItem !== null"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 class="bg-white rounded-3xl max-w-lg w-full overflow-hidden shadow-2xl border border-slate-200">
                <div class="relative bg-slate-100 h-64 flex items-center justify-center border-b border-slate-200">
                    <template x-if="previewItem">
                        <img :src="previewItem.foto_url"
                             :alt="previewItem.nama"
                             :class="previewItem.has_real_foto ? 'w-full h-full object-contain' : 'w-28 h-28 object-contain opacity-90'">
                    </template>
                    <button type="button"
                            @click="previewItem = null"
                            class="absolute top-3 right-3 w-8 h-8 rounded-full bg-white/90 hover:bg-white text-slate-700 shadow-sm flex items-center justify-center transition cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <template x-if="previewItem">
                    <div class="p-6 space-y-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <span class="inline-block px-2.5 py-0.5 rounded-full bg-teal-50 text-teal-700 border border-teal-200 text-[10px] font-bold mb-1.5"
                                      x-text="previewItem.kategori"></span>
                                <h4 class="text-base font-extrabold text-slate-900" x-text="previewItem.nama"></h4>
                                <p class="text-xs text-slate-500 font-mono mt-0.5">
                                    <span x-text="previewItem.kode"></span>
                                    <span x-show="previewItem.lokasi_rak" x-text="' • Rak ' + previewItem.lokasi_rak"></span>
                                </p>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="text-[10px] text-slate-400 uppercase font-bold block">Stok Tersedia</span>
                                <span class="text-lg font-black text-emerald-600" x-text="previewItem.stok + ' ' + previewItem.satuan"></span>
                            </div>
                        </div>

                        <p x-show="previewItem.keterangan" class="text-xs text-slate-600 bg-slate-50 p-3 rounded-2xl border border-slate-100" x-text="previewItem.keterangan"></p>

                        <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                            <button type="button"
                                    @click="previewItem = null"
                                    class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition cursor-pointer">
                                Tutup
                            </button>
                            <button type="button"
                                    @click="addFromCatalog(previewItem); previewItem = null"
                                    class="inline-flex items-center gap-1.5 px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-sm transition cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                <span>Tambahkan ke Pengajuan</span>
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function permintaanCreateForm(barangsData, initialCart) {
            return {
                barangs: barangsData,
                cart: initialCart,
                searchCatalog: '',
                selectedKategori: 'all',
                showCatalog: true,
                previewItem: null,
                activeRowIndex: null,
                rowSearch: '',
                validationWarning: '',

                emptyRow() {
                    return {
                        barang_id: '',
                        nama: '',
                        kode: '',
                        kategori: '',
                        foto_url: '',
                        has_real_foto: false,
                        lokasi_rak: '',
                        satuan: '-',
                        stok: 0,
                        jumlah_diminta: 1,
                        catatan_item: '',
                    };
                },

                get filteredCatalog() {
                    const query = this.searchCatalog.trim().toLowerCase();
                    return this.barangs.filter(b => {
                        const matchKat = this.selectedKategori === 'all' || String(b.kategori_id) === String(this.selectedKategori);
                        if (!matchKat) return false;
                        if (!query) return true;
                        return (b.nama || '').toLowerCase().includes(query)
                            || (b.kode || '').toLowerCase().includes(query)
                            || (b.kategori || '').toLowerCase().includes(query)
                            || (b.lokasi_rak || '').toLowerCase().includes(query)
                            || (b.satuan || '').toLowerCase().includes(query);
                    });
                },

                get filteredRowOptions() {
                    const q = this.rowSearch.trim().toLowerCase();
                    if (!q) return this.barangs;
                    return this.barangs.filter(b =>
                        (b.nama || '').toLowerCase().includes(q)
                        || (b.kode || '').toLowerCase().includes(q)
                        || (b.kategori || '').toLowerCase().includes(q)
                    );
                },

                get totalDipilihCount() {
                    return this.cart.filter(c => !!c.barang_id).length;
                },

                get totalUnitCount() {
                    return this.cart.reduce((sum, c) => sum + (c.barang_id ? (parseInt(c.jumlah_diminta, 10) || 0) : 0), 0);
                },

                inCartQty(barangId) {
                    return this.cart.reduce((sum, c) => {
                        return String(c.barang_id) === String(barangId)
                            ? sum + (parseInt(c.jumlah_diminta, 10) || 0)
                            : sum;
                    }, 0);
                },

                addItem() {
                    this.validationWarning = '';
                    this.cart.push(this.emptyRow());
                },

                removeItem(index) {
                    this.validationWarning = '';
                    if (this.cart.length > 1) {
                        this.cart.splice(index, 1);
                        if (this.activeRowIndex === index) {
                            this.activeRowIndex = null;
                        }
                    } else if (this.cart[0].barang_id) {
                        this.cart[0] = this.emptyRow();
                    }
                },

                openRowPicker(index) {
                    this.validationWarning = '';
                    if (this.activeRowIndex === index) {
                        this.activeRowIndex = null;
                    } else {
                        this.activeRowIndex = index;
                        this.rowSearch = '';
                    }
                },

                selectBarangForRow(index, b) {
                    this.validationWarning = '';
                    this.cart[index].barang_id = b.id;
                    this.cart[index].nama = b.nama;
                    this.cart[index].kode = b.kode;
                    this.cart[index].kategori = b.kategori;
                    this.cart[index].foto_url = b.foto_url;
                    this.cart[index].has_real_foto = b.has_real_foto;
                    this.cart[index].lokasi_rak = b.lokasi_rak;
                    this.cart[index].satuan = b.satuan;
                    this.cart[index].stok = b.stok;
                    if (!this.cart[index].jumlah_diminta || this.cart[index].jumlah_diminta < 1) {
                        this.cart[index].jumlah_diminta = 1;
                    }
                    this.activeRowIndex = null;
                    this.rowSearch = '';
                },

                addFromCatalog(b) {
                    this.validationWarning = '';
                    const existing = this.cart.find(c => String(c.barang_id) === String(b.id));
                    if (existing) {
                        if (b.stok <= 0 || existing.jumlah_diminta < b.stok) {
                            existing.jumlah_diminta = (parseInt(existing.jumlah_diminta, 10) || 0) + 1;
                        }
                        return;
                    }

                    const emptySlotIndex = this.cart.findIndex(c => !c.barang_id);
                    const targetIndex = emptySlotIndex !== -1 ? emptySlotIndex : (this.cart.push(this.emptyRow()) - 1);
                    this.selectBarangForRow(targetIndex, b);
                    this.cart[targetIndex].jumlah_diminta = 1;
                },

                incrementQty(index) {
                    const item = this.cart[index];
                    const max = item.stok > 0 ? item.stok : 9999;
                    if ((item.jumlah_diminta || 0) < max) {
                        item.jumlah_diminta = (parseInt(item.jumlah_diminta, 10) || 0) + 1;
                    }
                },

                decrementQty(index) {
                    const item = this.cart[index];
                    if ((item.jumlah_diminta || 1) > 1) {
                        item.jumlah_diminta = parseInt(item.jumlah_diminta, 10) - 1;
                    }
                },

                openPreview(b) {
                    this.previewItem = b;
                },

                openPreviewById(id) {
                    const found = this.barangs.find(b => String(b.id) === String(id));
                    if (found) {
                        this.previewItem = found;
                    }
                },

                validateBeforeSubmit(event) {
                    const filledCount = this.cart.filter(c => !!c.barang_id).length;
                    if (filledCount === 0) {
                        event.preventDefault();
                        this.validationWarning = 'Silakan pilih minimal 1 barang dari Katalog Bergambar atau dropdown pada tabel.';
                        this.activeRowIndex = 0;
                        return;
                    }

                    const emptyIdx = this.cart.findIndex(c => !c.barang_id);
                    if (emptyIdx !== -1) {
                        // Tunggu Alpine menghapus baris kosong dari DOM sebelum form disubmit
                        event.preventDefault();
                        this.cart = this.cart.filter(c => !!c.barang_id);
                        this.$nextTick(() => {
                            event.target.submit();
                        });
                    }
                }
            };
        }
    </script>
    @endpush
</x-app-layout>
