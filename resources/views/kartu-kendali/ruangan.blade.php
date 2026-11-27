<x-app-layout>
    <x-slot:title>Pemakaian ATK - {{ $ruangan->nama_ruangan }}</x-slot:title>
    <x-slot:header>Riwayat Pemakaian ATK di Ruangan</x-slot:header>

    <div class="space-y-6" x-data="{ viewMode: 'rekap' }">

        <!-- Breadcrumb & Top Bar -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                @if(!auth()->user()->isPegawai())
                    <a href="{{ route('kartu-kendali.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-600 hover:text-slate-900 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        <span>Semua Unit Kerja</span>
                    </a>
                    <span class="text-slate-300">•</span>
                @endif
                <span class="text-xs font-bold text-teal-800 bg-teal-50 px-3 py-1 rounded-full border border-teal-200">
                    {{ $ruangan->kode_ruangan }}
                </span>
                <span class="text-xs text-slate-500 font-medium">BPS Kabupaten Cirebon</span>
            </div>

            <!-- Print & Export Action Buttons -->
            <div class="flex items-center gap-2">
                <a href="{{ route('kartu-kendali.ruangan.export', array_merge(['id' => $ruangan->id], request()->all())) }}" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md transition transform hover:scale-105">
                    <svg class="w-4 h-4 text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Ekspor Excel (.xlsx)</span>
                </a>
                <a href="{{ route('kartu-kendali.ruangan.export-word', array_merge(['id' => $ruangan->id], request()->all())) }}" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-full bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md transition transform hover:scale-105">
                    <svg class="w-4 h-4 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Ekspor Word (.doc)</span>
                </a>
                <a href="{{ route('kartu-kendali.ruangan.cetak', array_merge(['id' => $ruangan->id], request()->all())) }}" 
                   target="_blank"
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-md transition transform hover:scale-105">
                    <svg class="w-4 h-4 text-bps-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Buku Kendali Resmi</span>
                </a>
            </div>
        </div>

        <!-- Ruangan Identity Banner -->
        <div class="bg-gradient-to-r from-bps-navy-800 to-slate-900 text-white rounded-3xl p-6 md:p-8 shadow-xl flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
            <div>
                <span class="text-xs font-mono font-bold text-teal-400 uppercase tracking-wider block">Catatan Pemakaian ATK Unit Kerja</span>
                <h1 class="text-2xl sm:text-3xl font-black text-white mt-1">{{ $ruangan->nama_ruangan }}</h1>
                <div class="flex flex-wrap items-center gap-3 mt-2 text-xs text-slate-300">
                    <span>Penanggung Jawab: <strong>{{ $ruangan->penanggung_jawab ?? 'Ketua Tim / Kasubbag' }}</strong>@if($ruangan->nip_penanggung_jawab) <span class="text-teal-300 font-mono text-[11px] font-normal">(NIP. {{ $ruangan->nip_penanggung_jawab }})</span>@endif</span>
                    <span>•</span>
                    <span>Satker: <strong>428646 BPS Kabupaten Cirebon</strong></span>
                </div>
            </div>

            <!-- Quick Room Switcher for Admin/Pimpinan -->
            @if($semuaRuangan->isNotEmpty())
                <div class="bg-white/10 p-3 rounded-2xl border border-white/10 flex items-center gap-2 shrink-0">
                    <span class="text-xs font-semibold text-slate-300">Pilih Ruangan:</span>
                    <select onchange="window.location.href=this.value" class="bg-slate-900 text-white font-bold text-xs px-3 py-1.5 rounded-xl border border-slate-700">
                        @foreach($semuaRuangan as $r)
                            <option value="{{ route('kartu-kendali.ruangan', $r->id) }}" {{ $r->id == $ruangan->id ? 'selected' : '' }}>
                                {{ $r->nama_ruangan }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>

        <!-- Filter Toolbar -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/90 shadow-sm">
            <form action="{{ route('kartu-kendali.ruangan', $ruangan->id) }}" method="GET" class="flex flex-col md:flex-row items-center gap-3">
                <div class="w-full md:w-36">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Tahun Anggaran</label>
                    <select name="tahun" onchange="this.form.submit()" class="w-full py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-800">
                        @for($y = date('Y'); $y >= date('Y') - 3; $y--)
                            <option value="{{ $y }}" {{ ($filter['tahun'] ?? date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>

                <div class="w-full md:w-44">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Bulan</label>
                    <select name="bulan" onchange="this.form.submit()" class="w-full py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800">
                        <option value="">Semua Bulan</option>
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}" {{ ($filter['bulan'] ?? '') == $m ? 'selected' : '' }}>
                                {{ Carbon\Carbon::create(null, $m, 1)->translatedFormat('F') }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="w-full md:w-64">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Spesifik Barang</label>
                    <select name="barang_id" onchange="this.form.submit()" class="w-full py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800">
                        <option value="">Semua Barang Persediaan</option>
                        @foreach($daftarBarang as $b)
                            <option value="{{ $b->id }}" {{ ($filter['barang_id'] ?? '') == $b->id ? 'selected' : '' }}>
                                {{ $b->nama_barang }}
                            </option>
                        @endforeach
                    </select>
                </div>

                @if(request()->hasAny(['tahun', 'bulan', 'barang_id', 'start_date', 'end_date']))
                    <div class="md:ml-auto pt-2 sm:pt-4">
                        <a href="{{ route('kartu-kendali.ruangan', $ruangan->id) }}" class="text-xs font-semibold text-rose-600 hover:underline">
                            Reset Filter
                        </a>
                    </div>
                @endif
            </form>
        </div>

        <!-- Metric Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
            <div class="bg-white p-5 rounded-3xl border border-slate-200/90 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Total Barang yang Diambil</span>
                    <span class="text-2xl font-black text-slate-800 mt-1 block">{{ number_format($totalUnitKeluar) }}</span>
                    <span class="text-[11px] text-slate-500">Unit barang untuk operasional</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
            </div>

            <div class="bg-white p-5 rounded-3xl border border-slate-200/90 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Macam Barang yang Dipakai</span>
                    <span class="text-2xl font-black text-slate-800 mt-1 block">{{ $totalJenisBarang }}</span>
                    <span class="text-[11px] text-slate-500">Jenis perlengkapan kantor</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-bps-blue-50 text-bps-blue-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                </div>
            </div>

            <div class="bg-white p-5 rounded-3xl border border-slate-200/90 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Berapa Kali Mengambil</span>
                    <span class="text-2xl font-black text-slate-800 mt-1 block">{{ $transaksiList->count() }}</span>
                    <span class="text-[11px] text-slate-500">Kali penyerahan barang</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
            </div>
        </div>

        <!-- View Mode Switcher -->
        <div class="flex items-center gap-2 border-b border-slate-200 pb-2">
            <button @click="viewMode = 'rekap'" 
                    :class="viewMode === 'rekap' ? 'bg-slate-900 text-white' : 'bg-white text-slate-600 hover:bg-slate-100'"
                    class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2">
                <span>1. Rekapitulasi Per Barang</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] bg-white/20">{{ $rekapBarang->count() }}</span>
            </button>

            <button @click="viewMode = 'rincian'" 
                    :class="viewMode === 'rincian' ? 'bg-slate-900 text-white' : 'bg-white text-slate-600 hover:bg-slate-100'"
                    class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2">
                <span>2. Log Rincian Transaksi</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] bg-white/20">{{ $transaksiList->count() }}</span>
            </button>
        </div>

        <!-- VIEW 1: REKAPITULASI PER BARANG -->
        <div x-show="viewMode === 'rekap'" class="bg-white rounded-3xl border border-slate-200/90 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <h4 class="text-sm font-extrabold text-slate-900">Rekapitulasi Pemakaian Barang ATK</h4>
                <span class="text-xs text-slate-400">Diurutkan dari pemakaian terbanyak</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                            <th class="py-3.5 px-5 w-12 text-center">#</th>
                            <th class="py-3.5 px-5">Kode Barang</th>
                            <th class="py-3.5 px-5">Nama Barang Persediaan</th>
                            <th class="py-3.5 px-5">Kategori</th>
                            <th class="py-3.5 px-5 text-center">Satuan</th>
                            <th class="py-3.5 px-5 text-center">Berapa Kali Diambil</th>
                            <th class="py-3.5 px-5 text-right font-black">Jumlah Diambil</th>
                            <th class="py-3.5 px-5 text-right">Kartu Stok</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($rekapBarang as $index => $item)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-4 px-5 text-center font-bold text-slate-400">{{ $index + 1 }}</td>
                                <td class="py-4 px-5 font-mono text-slate-500 font-bold">{{ $item->kode_barang }}</td>
                                <td class="py-4 px-5 font-bold text-slate-900">{{ $item->nama_barang }}</td>
                                <td class="py-4 px-5">
                                    <span class="px-2 py-0.5 rounded-lg text-[10px] bg-slate-100 text-slate-600 font-semibold">
                                        {{ $item->kategori }}
                                    </span>
                                </td>
                                <td class="py-4 px-5 text-center text-slate-600 font-medium">{{ $item->satuan }}</td>
                                <td class="py-4 px-5 text-center font-mono">{{ $item->frekuensi }} kali</td>
                                <td class="py-4 px-5 text-right whitespace-nowrap">
                                    <span class="inline-block px-3 py-1 rounded-xl bg-teal-50 text-teal-800 font-black font-mono text-xs border border-teal-200">
                                        {{ number_format($item->total_jumlah) }} {{ $item->satuan }}
                                    </span>
                                </td>
                                <td class="py-4 px-5 text-right">
                                    <a href="{{ route('kartu-kendali.barang', $item->barang_id) }}" class="inline-flex items-center gap-1 text-[11px] font-bold text-teal-700 hover:underline">
                                        <span>Kartu Stok</span>
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-12 text-center text-slate-400">
                                    Belum ada catatan pengambilan barang untuk ruangan ini pada periode yang dipilih.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- VIEW 2: LOG RINCIAN TRANSAKSI -->
        <div x-show="viewMode === 'rincian'" class="bg-white rounded-3xl border border-slate-200/90 shadow-sm overflow-hidden" style="display: none;">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <h4 class="text-sm font-extrabold text-slate-900">Riwayat Penyerahan Barang ATK</h4>
                <span class="text-xs text-slate-400">Catatan penyerahan fisik barang dari gudang Subbag Umum</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                            <th class="py-3.5 px-5">Tanggal</th>
                            <th class="py-3.5 px-5">No. Dokumen / Permintaan</th>
                            <th class="py-3.5 px-5">Pegawai Pengambil</th>
                            <th class="py-3.5 px-5">Nama Barang</th>
                            <th class="py-3.5 px-5 text-center">Jumlah</th>
                            <th class="py-3.5 px-5">Peruntukan / Kegiatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($transaksiList as $t)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-4 px-5 font-mono text-slate-600 whitespace-nowrap">
                                    {{ $t->tanggal_transaksi->format('d/m/Y') }}
                                </td>
                                <td class="py-4 px-5 font-mono font-bold text-slate-800 whitespace-nowrap">
                                    {{ $t->nomor_dokumen ?? '-' }}
                                </td>
                                <td class="py-4 px-5">
                                    <div class="font-bold text-slate-900">{{ $t->user->name ?? '-' }}</div>
                                    <div class="text-[10px] text-slate-400 font-mono">NIP. {{ $t->user->nip ?? '-' }}</div>
                                </td>
                                <td class="py-4 px-5">
                                    <div class="font-bold text-slate-900">{{ $t->barang->nama_barang }}</div>
                                    <div class="text-[10px] text-slate-400 font-mono">{{ $t->barang->kode_barang }}</div>
                                </td>
                                <td class="py-4 px-5 text-center whitespace-nowrap">
                                    <span class="inline-block px-2.5 py-1 rounded-xl bg-rose-50 text-rose-700 font-bold font-mono border border-rose-200">
                                        {{ abs($t->jumlah) }} {{ $t->barang->satuan }}
                                    </span>
                                </td>
                                <td class="py-4 px-5 text-slate-600">
                                    {{ $t->keterangan ?? $t->permintaan?->catatan_pemohon ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-400">
                                    Belum ada catatan pengambilan barang pada periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
