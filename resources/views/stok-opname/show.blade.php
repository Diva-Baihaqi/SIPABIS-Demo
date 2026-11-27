<x-app-layout>
    <x-slot:title>Sesi Opname {{ $opname->nomor_opname }}</x-slot:title>
    <x-slot:header>Lembar Kerja Stock Opname Persediaan (BASO)</x-slot:header>

    <div class="space-y-6" x-data="{
        filterMode: '{{ request('filter_selisih', 'all') }}',
        searchQuery: '',
    }">

        <!-- Top Breadcrumb & Print Button -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <a href="{{ route('stok-opname.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-600 hover:text-slate-900 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Kembali ke Riwayat Sesi Opname</span>
            </a>

            <div class="flex items-center gap-2">
                @if($opname->status !== 'draft')
                    <!-- Dropdown Ekspor Excel -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" @click.away="open = false" type="button" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md transition transform hover:scale-105">
                            <svg class="w-4 h-4 text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span>Ekspor Excel (.xlsx)</span>
                            <svg class="w-3 h-3 text-emerald-200 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-cloak class="absolute right-0 mt-2 w-64 bg-white rounded-2xl shadow-xl border border-slate-100 py-2 z-50">
                            <div class="px-4 py-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-400">Pilih Cakupan Excel:</div>
                            <a href="{{ route('stok-opname.export', [$opname->id, 'filter' => 'ada_stok']) }}" class="flex items-start gap-2.5 px-4 py-2 text-xs text-slate-700 hover:bg-emerald-50 hover:text-emerald-800 transition">
                                <span class="text-emerald-600 font-bold">✨</span>
                                <div>
                                    <strong class="block text-slate-900">Format Ringkas (Ada Stok)</strong>
                                    <span class="text-[11px] text-slate-500">Hanya barang yang memiliki saldo fisik/nominal</span>
                                </div>
                            </a>
                            <a href="{{ route('stok-opname.export', [$opname->id, 'filter' => 'semua']) }}" class="flex items-start gap-2.5 px-4 py-2 text-xs text-slate-700 hover:bg-slate-50 transition border-t border-slate-50">
                                <span class="text-slate-400">📋</span>
                                <div>
                                    <strong class="block text-slate-900">Format Lengkap (Semua Barang)</strong>
                                    <span class="text-[11px] text-slate-500">Mencakup seluruh daftar master barang persediaan</span>
                                </div>
                            </a>
                        </div>
                    </div>

                    <!-- Dropdown Cetak BASO Resmi -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" @click.away="open = false" type="button" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-md transition transform hover:scale-105">
                            <svg class="w-4 h-4 text-brand-lime" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            <span>Cetak BASO Resmi</span>
                            <svg class="w-3 h-3 text-slate-300 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-cloak class="absolute right-0 mt-2 w-64 bg-white rounded-2xl shadow-xl border border-slate-100 py-2 z-50">
                            <div class="px-4 py-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-400">Pilih Cakupan Cetak:</div>
                            <a href="{{ route('stok-opname.cetak', [$opname->id, 'filter' => 'ada_stok']) }}" target="_blank" class="flex items-start gap-2.5 px-4 py-2 text-xs text-slate-700 hover:bg-emerald-50 hover:text-emerald-800 transition">
                                <span class="text-emerald-600 font-bold">✨</span>
                                <div>
                                    <strong class="block text-slate-900">Versi Ringkas (Ada Stok)</strong>
                                    <span class="text-[11px] text-slate-500">Hemat kertas, hanya barang yang ada nominalnya</span>
                                </div>
                            </a>
                            <a href="{{ route('stok-opname.cetak', [$opname->id, 'filter' => 'semua']) }}" target="_blank" class="flex items-start gap-2.5 px-4 py-2 text-xs text-slate-700 hover:bg-slate-50 transition border-t border-slate-50">
                                <span class="text-slate-400">📋</span>
                                <div>
                                    <strong class="block text-slate-900">Versi Lengkap (Semua Barang)</strong>
                                    <span class="text-[11px] text-slate-500">Mencetak seluruh 727 barang persediaan</span>
                                </div>
                            </a>
                        </div>
                    </div>
                @endif

                @if(Auth::user()->isAdmin())
                    <form action="{{ route('stok-opname.destroy', $opname->id) }}" method="POST" class="inline"
                          data-confirm="Apakah Anda yakin ingin menghapus sesi Stock Opname {{ $opname->nomor_opname }}?{{ $opname->status !== 'draft' ? ' Peringatan: Seluruh penyesuaian saldo stok akan dibatalkan/direverse otomatis.' : '' }}"
                          data-confirm-title="Hapus Sesi Stock Opname"
                          data-confirm-type="danger"
                          data-confirm-btn="Ya, Hapus Sesi">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-full bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-bold text-xs shadow-sm transition">
                            <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            <span>Hapus Sesi Opname</span>
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Document Ribbon Card -->
        <div class="bg-gradient-to-r from-bps-navy-800 via-bps-navy-700 to-slate-900 text-white rounded-3xl p-6 md:p-8 shadow-xl flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
            <div>
                <span class="text-xs font-mono font-bold text-teal-400 uppercase tracking-wider block">Berita Acara Stock Opname BPS</span>
                <h1 class="text-xl sm:text-2xl font-black font-mono tracking-tight text-white mt-1">{{ $opname->nomor_opname }}</h1>
                <div class="flex flex-wrap items-center gap-3 mt-2 text-xs text-slate-300">
                    <span>Periode: <strong>{{ $opname->periode }}</strong></span>
                    <span>•</span>
                    <span>Tanggal: <strong>{{ $opname->tanggal_opname->format('d F Y') }}</strong></span>
                    <span>•</span>
                    <span>Pemeriksa: <strong>{{ $opname->user->name ?? 'Subbag Umum' }}</strong></span>
                </div>
            </div>

            <div class="shrink-0">
                @if($opname->status === 'draft')
                    <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-2xl text-xs font-bold bg-amber-400/20 text-amber-300 border border-amber-400/30">
                        <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                        <span>Fase Perhitungan Fisik (Draft)</span>
                    </span>
                @elseif($opname->status === 'final')
                    <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-2xl text-xs font-bold bg-teal-400/20 text-teal-300 border border-teal-400/30">
                        <span class="w-2 h-2 rounded-full bg-teal-400"></span>
                        <span>Finalisasi Selesai (Menunggu Pengesahan)</span>
                    </span>
                @elseif($opname->status === 'disahkan')
                    <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-2xl text-xs font-bold bg-emerald-400/20 text-emerald-300 border border-emerald-400/30">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span>Telah Disahkan Kepala BPS</span>
                    </span>
                @endif
            </div>
        </div>

        <!-- Metric Overview Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 sm:gap-5">
            <div class="bg-white p-5 rounded-3xl border border-slate-200/90 shadow-sm">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Total Katalog Opname</span>
                <span class="text-2xl font-black text-slate-800 mt-1 block font-mono">{{ number_format($totalItem) }}</span>
                <span class="text-[11px] text-slate-500">Item persediaan</span>
            </div>

            <div class="bg-white p-5 rounded-3xl border border-slate-200/90 shadow-sm">
                <span class="text-xs font-bold text-emerald-600 uppercase tracking-wider block">Fisik Cocok / Sesuai</span>
                <span class="text-2xl font-black text-emerald-600 mt-1 block font-mono">{{ number_format($totalCocok) }}</span>
                <span class="text-[11px] text-slate-500">Selisih nol (0)</span>
            </div>

            <div class="bg-white p-5 rounded-3xl border border-slate-200/90 shadow-sm">
                <span class="text-xs font-bold {{ $totalSelisih > 0 ? 'text-amber-600' : 'text-slate-400' }} uppercase tracking-wider block">Item Mengalami Selisih</span>
                <span class="text-2xl font-black {{ $totalSelisih > 0 ? 'text-amber-600' : 'text-slate-800' }} mt-1 block font-mono">{{ number_format($totalSelisih) }}</span>
                <span class="text-[11px] text-slate-500">Perlu penyesuaian</span>
            </div>

            <div class="bg-white p-5 rounded-3xl border border-slate-200/90 shadow-sm">
                <span class="text-xs font-bold text-teal-700 uppercase tracking-wider block">Net Deviasi Fisik</span>
                <span class="text-2xl font-black {{ $totalSelisihUnit < 0 ? 'text-rose-600' : 'text-teal-800' }} mt-1 block font-mono">
                    {{ $totalSelisihUnit > 0 ? '+' : '' }}{{ number_format($totalSelisihUnit) }}
                </span>
                <span class="text-[11px] text-slate-500">Unit total selisih</span>
            </div>
        </div>

        <!-- SPECIAL PIMPINAN ENDORSEMENT BANNER (IF FINAL & USER IS PIMPINAN) -->
        @if($opname->status === 'final' && auth()->user()->isPimpinan())
            <div class="p-6 rounded-3xl bg-gradient-to-r from-teal-800 to-slate-900 text-white shadow-xl flex flex-col md:flex-row items-start md:items-center justify-between gap-5 border border-teal-600/40">
                <div class="space-y-1">
                    <span class="text-xs font-bold text-brand-lime uppercase tracking-wider">Tindakan Pengesahan Pimpinan</span>
                    <h3 class="text-lg font-extrabold text-white">Sahkan Berita Acara Stock Opname Ini</h3>
                    <p class="text-xs text-slate-300 max-w-xl">
                        Hasil perhitungan fisik dan jurnal penyesuaian telah difinalisasi oleh Subbag Umum. Klik tombol di samping untuk membubuhkan tanda tangan digital pengesahan Kepala BPS Kabupaten Cirebon.
                    </p>
                </div>
                <form action="{{ route('stok-opname.sahkan', $opname->id) }}" method="POST"
                      data-confirm="Sahkan Berita Acara Stock Opname nomor '{{ $opname->nomor_opname }}'? Tanda tangan digital Anda akan tertera pada lembar dokumen resmi BASO BPS."
                      data-confirm-title="Pengesahan Resmi Dokumen BASO"
                      data-confirm-type="success"
                      data-confirm-btn="Ya, Sahkan BASO">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 px-7 py-3 rounded-full bg-brand-lime hover:bg-brand-lime-hover text-slate-950 font-extrabold text-xs shadow-md transition transform hover:scale-105 cursor-pointer">
                        <svg class="w-4 h-4 text-slate-950" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span>Sahkan Berita Acara (Digital)</span>
                    </button>
                </form>
            </div>
        @elseif($opname->status === 'disahkan')
            <div class="p-5 rounded-3xl bg-emerald-50 border border-emerald-200 text-emerald-900 flex items-center justify-between">
                <div class="flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-600 text-white flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-extrabold text-emerald-950">Berita Acara Telah Sah Secara Hukum</h4>
                        <p class="text-xs text-emerald-800">
                            Disahkan oleh: <strong>{{ $opname->pimpinan->name ?? 'Kepala BPS Kabupaten Cirebon' }}</strong>
                        </p>
                    </div>
                </div>
                <a href="{{ route('stok-opname.cetak', $opname->id) }}" target="_blank" class="hidden sm:inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold shadow-sm transition">
                    <span>Unduh / Cetak Dokumen BASO</span>
                </a>
            </div>
        @endif

        <!-- Filter & Search Toolbar -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/90 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
            <form action="{{ route('stok-opname.show', $opname->id) }}" method="GET" class="w-full sm:w-auto flex-1 flex flex-col sm:flex-row items-center gap-3">
                <div class="relative w-full sm:w-80">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Cari kode atau nama barang persediaan..." 
                           class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs focus:ring-2 focus:ring-teal-500 focus:bg-white transition">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>

                <select name="filter_selisih" onchange="this.form.submit()" class="w-full sm:w-48 py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-semibold focus:ring-2 focus:ring-teal-500 transition">
                    <option value="">Semua Barang ({{ $totalItem }})</option>
                    <option value="selisih" {{ request('filter_selisih') === 'selisih' ? 'selected' : '' }}>Hanya Item Selisih ({{ $totalSelisih }})</option>
                    <option value="cocok" {{ request('filter_selisih') === 'cocok' ? 'selected' : '' }}>Hanya Item Cocok ({{ $totalCocok }})</option>
                </select>

                @if(request()->hasAny(['search', 'filter_selisih']))
                    <a href="{{ route('stok-opname.show', $opname->id) }}" class="text-xs font-semibold text-rose-600 hover:underline">
                        Reset
                    </a>
                @endif
            </form>

            <span class="text-xs text-slate-500 font-medium">
                Menampilkan <strong>{{ $details->count() }}</strong> barang dalam lembar kerja
            </span>
        </div>

        <!-- Master Sheet Table Card -->
        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm overflow-hidden">
            
            @if($opname->status === 'draft' && auth()->user()->isAdmin())
                <!-- Form Mode DRAFT -->
                <form action="{{ route('stok-opname.update-fisik', $opname->id) }}" method="POST">
                    @csrf
                    
                    <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 bg-slate-50/50">
                        <div>
                            <h4 class="text-sm font-extrabold text-slate-900">Lembar Input Hasil Perhitungan Fisik</h4>
                            <p class="text-xs text-slate-500 mt-0.5">Ubah angka pada kolom "Stok Fisik". Selisih akan terhitung otomatis.</p>
                        </div>

                        <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-md transition transform hover:scale-105">
                            <svg class="w-4 h-4 text-brand-lime" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            <span>Simpan Perhitungan Fisik</span>
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                                    <th class="py-3 px-4 w-12 text-center">#</th>
                                    <th class="py-3 px-4 w-32">Kode Barang</th>
                                    <th class="py-3 px-4">Nama Barang Persediaan</th>
                                    <th class="py-3 px-4 w-28 text-center">Satuan</th>
                                    <th class="py-3 px-4 w-32 text-center bg-slate-100/60 font-black">Stok Sistem</th>
                                    <th class="py-3 px-4 w-36 text-center bg-teal-50/40">Hasil Fisik</th>
                                    <th class="py-3 px-4 w-28 text-center">Selisih</th>
                                    <th class="py-3 px-4 w-56">Keterangan Kondisi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($details as $idx => $d)
                                    <tr class="hover:bg-slate-50/70 transition" 
                                        x-data="{ 
                                            sistem: {{ $d->stok_sistem }}, 
                                            fisik: {{ $d->stok_fisik }},
                                            get selisih() { return this.fisik - this.sistem; }
                                        }">
                                        <td class="py-3 px-4 text-center text-slate-400 font-bold font-mono">{{ $idx + 1 }}</td>
                                        <td class="py-3 px-4 font-mono font-bold text-slate-500 whitespace-nowrap">{{ $d->barang->kode_barang }}</td>
                                        <td class="py-3 px-4">
                                            <div class="font-bold text-slate-900">{{ $d->barang->nama_barang }}</div>
                                            <div class="text-[10px] text-slate-400">{{ $d->barang->kategori?->nama_kategori }}</div>
                                        </td>
                                        <td class="py-3 px-4 text-center text-slate-600 font-medium">{{ $d->barang->satuan }}</td>
                                        <td class="py-3 px-4 text-center font-mono font-bold text-slate-800 bg-slate-100/30">
                                            {{ $d->stok_sistem }}
                                        </td>
                                        <td class="py-3 px-4 text-center bg-teal-50/20">
                                            <input type="hidden" name="items[{{ $idx }}][barang_id]" value="{{ $d->barang_id }}">
                                            <input type="number" 
                                                   name="items[{{ $idx }}][stok_fisik]" 
                                                   x-model.number="fisik" 
                                                   min="0" 
                                                   required
                                                   class="w-24 text-center py-1.5 px-2 bg-white border border-slate-200 rounded-xl text-xs font-black text-slate-900 focus:ring-2 focus:ring-teal-500 transition">
                                        </td>
                                        <td class="py-3 px-4 text-center whitespace-nowrap">
                                            <span :class="{
                                                'bg-emerald-50 text-emerald-700 border-emerald-200': selisih === 0,
                                                'bg-rose-50 text-rose-700 border-rose-200 font-black': selisih < 0,
                                                'bg-amber-50 text-amber-700 border-amber-200 font-black': selisih > 0
                                            }" class="inline-block px-2.5 py-1 rounded-xl text-[11px] font-mono border">
                                                <span x-text="selisih > 0 ? '+' + selisih : selisih"></span>
                                            </span>
                                        </td>
                                        <td class="py-3 px-4">
                                            <input type="text" 
                                                   name="items[{{ $idx }}][keterangan_selisih]" 
                                                   value="{{ $d->keterangan_selisih }}" 
                                                   placeholder="Contoh: Barang rusak / selisih hitung"
                                                   class="w-full py-1.5 px-3 bg-white border border-slate-200 rounded-xl text-xs text-slate-700 focus:ring-2 focus:ring-teal-500 transition">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="p-5 border-t border-slate-100 bg-slate-50/50 flex items-center justify-end">
                        <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-md transition transform hover:scale-105">
                            <svg class="w-4 h-4 text-brand-lime" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            <span>Simpan Perhitungan Fisik</span>
                        </button>
                    </div>
                </form>

                <!-- Finalize Box -->
                <div class="p-6 md:p-8 border-t-2 border-slate-200 bg-gradient-to-br from-slate-50 to-teal-50/30">
                    <div class="border-b border-slate-200/80 pb-3 mb-4">
                        <h4 class="text-base font-extrabold text-slate-900">Finalisasi Stock Opname & Penyesuaian Saldo</h4>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Menekan tombol di bawah akan menyelesaikan sesi ini dan <strong>secara otomatis memutasi saldo buku persediaan gudang</strong> via koreksi jurnal penyesuaian hanya untuk barang yang mengalami deviasi.
                        </p>
                    </div>

                    <form action="{{ route('stok-opname.finalisasi', $opname->id) }}" method="POST"
                          data-confirm="Apakah Anda yakin ingin memfinalisasi Stock Opname ini? Saldo stok aktif gudang akan resmi disesuaikan mengikuti hasil fisik riil."
                          data-confirm-title="Finalisasi Stock Opname"
                          data-confirm-type="warning"
                          data-confirm-btn="Ya, Finalisasi Sesi"
                          class="space-y-4">
                        @csrf
                        <div>
                            <label for="catatan_kesimpulan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                Catatan Kesimpulan Pelaksanaan Opname Fisik
                            </label>
                            <textarea id="catatan_kesimpulan" 
                                      name="catatan_kesimpulan" 
                                      rows="2" 
                                      placeholder="Contoh: Telah dilakukan opname fisik menyeluruh oleh tim Subbag Umum. Ditemukan beberapa selisih minor akibat sisa potongan kertas dan spidol habis pakai..."
                                      class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-2xl text-xs focus:ring-2 focus:ring-teal-500 transition"></textarea>
                        </div>

                        <div class="flex items-center justify-end">
                            <button type="submit" class="inline-flex items-center gap-2 px-8 py-3 rounded-full bg-teal-700 hover:bg-teal-800 text-white font-black text-xs shadow-md transition transform hover:scale-105 cursor-pointer">
                                <svg class="w-4 h-4 text-brand-lime" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>Finalisasi & Sesuaikan Saldo Sistem</span>
                            </button>
                        </div>
                    </form>
                </div>

            @else
                <!-- Read Only Mode (FINAL or DISAHKAN) -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                                <th class="py-3.5 px-5 w-12 text-center">#</th>
                                <th class="py-3.5 px-5">Kode Barang</th>
                                <th class="py-3.5 px-5">Nama Barang Persediaan</th>
                                <th class="py-3.5 px-5 text-center">Satuan</th>
                                <th class="py-3.5 px-5 text-center bg-slate-100/60 font-black">Stok Sistem</th>
                                <th class="py-3.5 px-5 text-center bg-teal-50/40">Hasil Fisik</th>
                                <th class="py-3.5 px-5 text-center">Selisih</th>
                                <th class="py-3.5 px-5">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($details as $idx => $d)
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="py-3.5 px-5 text-center font-mono text-slate-400 font-bold">{{ $idx + 1 }}</td>
                                    <td class="py-3.5 px-5 font-mono font-bold text-slate-500 whitespace-nowrap">{{ $d->barang->kode_barang }}</td>
                                    <td class="py-3.5 px-5">
                                        <div class="font-bold text-slate-900">{{ $d->barang->nama_barang }}</div>
                                        <div class="text-[10px] text-slate-400">{{ $d->barang->kategori?->nama_kategori }}</div>
                                    </td>
                                    <td class="py-3.5 px-5 text-center text-slate-600 font-medium">{{ $d->barang->satuan }}</td>
                                    <td class="py-3.5 px-5 text-center font-mono font-bold text-slate-800 bg-slate-100/30">{{ $d->stok_sistem }}</td>
                                    <td class="py-3.5 px-5 text-center font-mono font-bold text-teal-900 bg-teal-50/20">{{ $d->stok_fisik }}</td>
                                    <td class="py-3.5 px-5 text-center whitespace-nowrap">
                                        @if($d->selisih === 0)
                                            <span class="inline-block px-2.5 py-1 rounded-xl text-[11px] font-mono bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                0 (Cocok)
                                            </span>
                                        @elseif($d->selisih > 0)
                                            <span class="inline-block px-2.5 py-1 rounded-xl text-[11px] font-mono bg-amber-50 text-amber-700 border border-amber-200 font-black">
                                                +{{ $d->selisih }} (Lebih)
                                            </span>
                                        @else
                                            <span class="inline-block px-2.5 py-1 rounded-xl text-[11px] font-mono bg-rose-50 text-rose-700 border border-rose-200 font-black">
                                                {{ $d->selisih }} (Kurang)
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-5 text-slate-600">{{ $d->keterangan_selisih ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($opname->catatan_kesimpulan)
                    <div class="p-6 border-t border-slate-100 bg-slate-50/60">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Catatan Kesimpulan Berita Acara:</span>
                        <p class="text-xs text-slate-800 mt-1 italic font-medium">
                            "{{ $opname->catatan_kesimpulan }}"
                        </p>
                    </div>
                @endif
            @endif
        </div>
    </div>
</x-app-layout>
