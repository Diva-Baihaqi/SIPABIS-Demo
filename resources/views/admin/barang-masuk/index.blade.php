<x-app-layout>
    <x-slot:title>Penerimaan Barang Masuk</x-slot:title>
    <x-slot:header>Barang Masuk & Pengadaan Persediaan</x-slot:header>

    <div class="space-y-6" x-data="{
        detailOpen: false,
        detailData: {},
        openDetail(data) {
            this.detailData = data;
            this.detailOpen = true;
        },
        closeDetail() {
            this.detailOpen = false;
        },

        editOpen: false,
        editData: {
            id: null,
            barang_nama: '',
            barang_kode: '',
            satuan: '',
            stok_aktif: 0,
            jumlah: 1,
            nomor_dokumen: '',
            tanggal_transaksi: '',
            sumber_atau_tujuan: '',
            status_spj: 'selesai',
            keterangan: '',
            actionUrl: '',
        },
        openEdit(item) {
            this.editData = {
                id: item.id,
                barang_nama: item.nama_barang,
                barang_kode: item.kode_bmn,
                satuan: item.satuan,
                stok_aktif: item.stok_aktif,
                jumlah: item.jumlah_raw,
                nomor_dokumen: item.nomor_dokumen === '—' ? '' : item.nomor_dokumen,
                tanggal_transaksi: item.tanggal_raw,
                sumber_atau_tujuan: item.rekanan === '—' ? '' : item.rekanan,
                status_spj: item.status_spj,
                keterangan: item.keterangan_raw || '',
                actionUrl: '{{ url('admin/barang-masuk') }}/' + item.id,
            };
            this.editOpen = true;
        },
        closeEdit() {
            this.editOpen = false;
        }
    }" @keydown.escape.window="closeDetail(); closeEdit()">
        <!-- Top Metrics & Action Row -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
            <div class="bg-white p-5 rounded-3xl border border-slate-200/90 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Penerimaan Bulan Ini</span>
                    <span class="text-2xl font-black text-slate-800 mt-1 block">{{ number_format($totalMasukBulanIni) }}</span>
                    <span class="text-[11px] text-slate-500">Unit Barang Masuk Gudang</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                </div>
            </div>

            <div class="bg-white p-5 rounded-3xl border border-slate-200/90 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Kwitansi Belum Lengkap</span>
                    <span class="text-2xl font-black {{ $totalSpjTertunda > 0 ? 'text-amber-600' : 'text-slate-800' }} mt-1 block">{{ $totalSpjTertunda }}</span>
                    <span class="text-[11px] text-slate-500">Transaksi Menunggu Kwitansi Toko</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>

            <div class="bg-gradient-to-br from-teal-700 to-slate-900 p-5 rounded-3xl text-white shadow-md flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-white">Input Pengadaan Baru</h3>
                    <p class="text-[11px] text-teal-200 mt-0.5">Catat faktur/surat jalan dari rekanan BPS</p>
                    <a href="{{ route('admin.barang-masuk.create') }}" class="mt-3 inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-brand-lime hover:bg-brand-lime-hover text-slate-950 font-bold text-xs shadow-sm transition transform hover:scale-105">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                        <span>Catat Barang Masuk</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Filter, Search & Export Toolbar -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/90 shadow-sm flex flex-col xl:flex-row xl:items-center justify-between gap-4">
            <form action="{{ route('admin.barang-masuk.index') }}" method="GET" class="flex flex-wrap items-center gap-2.5 flex-1">
                <!-- Search Input -->
                <div class="relative w-full sm:w-64">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Cari No. Dokumen, Vendor, Barang..." 
                           class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-2xl text-xs focus:ring-2 focus:ring-teal-500 focus:bg-white transition">
                    <svg class="w-3.5 h-3.5 text-slate-400 absolute left-3 top-2.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>

                <!-- Dropdown Bulan -->
                <div class="w-full sm:w-40">
                    <select name="bulan" 
                            onchange="this.form.submit()" 
                            class="w-full py-2 px-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-medium text-slate-700 focus:ring-2 focus:ring-teal-500 transition">
                        <option value="">Semua Bulan</option>
                        @foreach($daftarBulan as $num => $nama)
                            <option value="{{ $num }}" {{ (string) request('bulan') === (string) $num ? 'selected' : '' }}>
                                {{ $nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Dropdown Tahun -->
                <div class="w-full sm:w-36">
                    <select name="tahun" 
                            onchange="this.form.submit()" 
                            class="w-full py-2 px-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-medium text-slate-700 focus:ring-2 focus:ring-teal-500 transition">
                        <option value="">Semua Tahun</option>
                        @foreach($availableYears as $year)
                            <option value="{{ $year }}" {{ (string) request('tahun') === (string) $year ? 'selected' : '' }}>
                                Tahun {{ $year }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Dropdown Status SPJ -->
                <div class="w-full sm:w-44">
                    <select name="status_spj" 
                            onchange="this.form.submit()" 
                            class="w-full py-2 px-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-medium text-slate-700 focus:ring-2 focus:ring-teal-500 transition">
                        <option value="">Semua Status Kwitansi</option>
                        <option value="selesai" {{ request('status_spj') === 'selesai' ? 'selected' : '' }}>Kwitansi Lengkap</option>
                        <option value="tertunda" {{ request('status_spj') === 'tertunda' ? 'selected' : '' }}>Menunggu Kwitansi</option>
                    </select>
                </div>

                @if(request()->hasAny(['search', 'bulan', 'tahun', 'status_spj']))
                    <a href="{{ route('admin.barang-masuk.index') }}" class="text-xs font-bold text-rose-600 hover:text-rose-700 hover:underline px-2 py-1">
                        Reset Filter
                    </a>
                @endif
            </form>

            <!-- Export Actions -->
            <div class="flex items-center gap-2.5 shrink-0 self-end xl:self-center">
                <a href="{{ route('admin.barang-masuk.export', request()->query()) }}" 
                   title="Unduh Buku Register Penerimaan Barang (.xlsx)"
                   class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-2xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 text-xs font-bold transition shadow-xs">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Ekspor Excel</span>
                </a>

                <a href="{{ route('admin.barang-masuk.cetak', request()->query()) }}" 
                   target="_blank"
                   title="Cetak / Simpan PDF Buku Register Penerimaan Barang"
                   class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-2xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition shadow-sm">
                    <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak PDF</span>
                </a>
            </div>
        </div>

        <!-- Data Table -->
        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                            <th class="py-3.5 px-5">Tanggal</th>
                            <th class="py-3.5 px-5">No. Dokumen / Kwitansi</th>
                            <th class="py-3.5 px-5">Barang Persediaan</th>
                            <th class="py-3.5 px-5 text-center">Jumlah Masuk</th>
                            <th class="py-3.5 px-5 text-center">Mutasi Saldo</th>
                            <th class="py-3.5 px-5">Penyedia / Vendor</th>
                            <th class="py-3.5 px-5 text-center">Status SPJ</th>
                            <th class="py-3.5 px-5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($transaksiList as $t)
                            <tr class="hover:bg-slate-50/80 transition duration-150">
                                <td class="py-4 px-5 whitespace-nowrap text-slate-600 font-mono">
                                    {{ $t->tanggal_transaksi->format('d/m/Y') }}
                                </td>
                                <td class="py-4 px-5 whitespace-nowrap">
                                    <span class="font-bold text-slate-800 font-mono">{{ $t->nomor_dokumen ?? '—' }}</span>
                                </td>
                                <td class="py-4 px-5">
                                    <div class="font-bold text-slate-900">{{ $t->barang->nama_barang }}</div>
                                    <div class="text-[10px] text-slate-400 font-mono mt-0.5">
                                        {{ $t->barang->kode_barang }} • {{ $t->barang->kategori?->nama_kategori ?? 'Umum' }}
                                    </div>
                                </td>
                                <td class="py-4 px-5 text-center whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-black bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        +{{ number_format($t->jumlah) }} {{ $t->barang->satuan }}
                                    </span>
                                </td>
                                <td class="py-4 px-5 text-center whitespace-nowrap text-slate-500 font-mono text-[11px]">
                                    <span>{{ $t->saldo_sebelum }}</span>
                                    <span class="text-slate-300 mx-1">&rarr;</span>
                                    <span class="font-bold text-slate-800">{{ $t->saldo_setelah }}</span>
                                </td>
                                <td class="py-4 px-5">
                                    <span class="text-slate-700 font-medium">{{ $t->sumber_atau_tujuan ?? '—' }}</span>
                                    @if($t->keterangan)
                                        <div class="text-[10px] text-slate-400 italic truncate max-w-xs">{{ $t->keterangan }}</div>
                                    @endif
                                </td>
                                <td class="py-4 px-5 text-center whitespace-nowrap">
                                    @if($t->status_spj === 'selesai')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            <span>SPJ Lunas</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200" title="Barang Langganan: Kwitansi menyusul">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                            <span>SPJ Tertunda</span>
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-5 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1.5 justify-end">
                                        <!-- Tombol Detail -->
                                        <button type="button" 
                                                @click="openDetail({{ json_encode([
                                                    'id' => $t->id,
                                                    'barang_id' => $t->barang_id,
                                                    'tanggal' => $t->tanggal_transaksi->format('d/m/Y'),
                                                    'tanggal_lengkap' => $t->tanggal_transaksi->translatedFormat('d F Y'),
                                                    'nomor_dokumen' => $t->nomor_dokumen ?? '—',
                                                    'nama_barang' => $t->barang->nama_barang,
                                                    'kode_barang' => $t->barang->kode_barang,
                                                    'kode_bmn' => $t->barang->kode_bmn_lengkap ?? $t->barang->kode_barang,
                                                    'kategori' => $t->barang->kategori?->nama_kategori ?? 'Umum',
                                                    'satuan' => $t->barang->satuan,
                                                    'lokasi' => $t->barang->lokasi ?? 'Lemari Persediaan Subbag Umum',
                                                    'jumlah' => number_format($t->jumlah),
                                                    'saldo_sebelum' => number_format($t->saldo_sebelum),
                                                    'saldo_setelah' => number_format($t->saldo_setelah),
                                                    'rekanan' => $t->sumber_atau_tujuan ?? '—',
                                                    'status_spj' => $t->status_spj,
                                                    'pencatat' => $t->user?->name ?? 'Administrator Persediaan',
                                                    'keterangan' => $t->keterangan ?: 'Tidak ada catatan tambahan.',
                                                    'harga_satuan' => $t->barang->harga_satuan_estimasi > 0 ? 'Rp ' . number_format($t->barang->harga_satuan_estimasi, 0, ',', '.') : null,
                                                    'estimasi_total' => $t->barang->harga_satuan_estimasi > 0 ? 'Rp ' . number_format($t->barang->harga_satuan_estimasi * $t->jumlah, 0, ',', '.') : null,
                                                ]) }})"
                                                title="Lihat Detail Transaksi Penerimaan"
                                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-bold transition">
                                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            <span>Detail</span>
                                        </button>

                                        <!-- Tombol Edit / Koreksi -->
                                        <button type="button" 
                                                @click="openEdit({{ json_encode([
                                                    'id' => $t->id,
                                                    'nama_barang' => $t->barang->nama_barang,
                                                    'kode_bmn' => $t->barang->kode_bmn_lengkap ?? $t->barang->kode_barang,
                                                    'satuan' => $t->barang->satuan,
                                                    'stok_aktif' => $t->barang->stok_aktif,
                                                    'jumlah_raw' => $t->jumlah,
                                                    'nomor_dokumen' => $t->nomor_dokumen ?? '',
                                                    'tanggal_raw' => $t->tanggal_transaksi->format('Y-m-d'),
                                                    'rekanan' => $t->sumber_atau_tujuan ?? '',
                                                    'status_spj' => $t->status_spj,
                                                    'keterangan_raw' => $t->keterangan ?? '',
                                                ]) }})"
                                                title="Koreksi Data / Kuantitas Transaksi Ini"
                                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200/80 text-[11px] font-bold transition">
                                            <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            <span>Edit</span>
                                        </button>

                                        <!-- Tombol Hapus / Batalkan -->
                                        <form action="{{ route('admin.barang-masuk.destroy', $t->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    data-confirm="Batalkan pencatatan barang masuk nomor dokumen '{{ $t->nomor_dokumen ?? '-' }}' ({{ $t->barang->nama_barang }})? Saldo fisik gudang akan dikurangi kembali sebanyak {{ $t->jumlah }} {{ $t->barang->satuan }}."
                                                    data-confirm-title="Konfirmasi Pembatalan Penerimaan"
                                                    data-confirm-type="danger"
                                                    data-confirm-btn="Ya, Batalkan Transaksi"
                                                    title="Batalkan / Hapus Transaksi Ini"
                                                    class="inline-flex items-center gap-1 p-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200/80 text-[11px] font-bold transition">
                                                <svg class="w-3.5 h-3.5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>

                                        <!-- Tombol Lunaskan SPJ -->
                                        @if($t->status_spj === 'tertunda')
                                            <form action="{{ route('admin.barang-masuk.update-spj', $t->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" 
                                                        data-confirm="Tandai transaksi pengadaan nomor dokumen '{{ $t->nomor_dokumen ?? '-' }}' sebagai SPJ Selesai (lunas)?"
                                                        data-confirm-title="Konfirmasi Pelunasan SPJ"
                                                        data-confirm-type="success"
                                                        data-confirm-btn="Ya, Lunaskan SPJ"
                                                        title="Pelunasan SPJ (Kwitansi sudah diterima)"
                                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl bg-teal-50 hover:bg-teal-100 text-teal-800 text-[11px] font-bold border border-teal-200 transition">
                                                    <svg class="w-3.5 h-3.5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                    <span>Lunaskan SPJ</span>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-12 text-center text-slate-400">
                                    <svg class="w-10 h-10 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                                    <p class="font-semibold text-sm">Tidak ada riwayat barang masuk untuk filter ini.</p>
                                    <p class="text-xs text-slate-400 mt-0.5">Ubah pilihan periode bulan, tahun, status SPJ, atau kata kunci pencarian Anda.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($transaksiList->hasPages())
                <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $transaksiList->links() }}
                </div>
            @endif
        </div>

        <!-- Detail Modal Dialog -->
        <div x-show="detailOpen" 
             x-cloak 
             class="fixed inset-0 z-50 overflow-y-auto" 
             style="display: none;">
            <!-- Backdrop -->
            <div x-show="detailOpen" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0" 
                 x-transition:enter-end="opacity-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100" 
                 x-transition:leave-end="opacity-0" 
                 class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm transition-opacity" 
                 @click="closeDetail()"></div>

            <!-- Modal Content Panel -->
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div x-show="detailOpen" 
                     x-transition:enter="ease-out duration-300" 
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave="ease-in duration-200" 
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-xl border border-slate-200/90">
                    
                    <!-- Header Banner -->
                    <div class="bg-gradient-to-r from-slate-900 to-teal-900 p-6 text-white relative">
                        <button type="button" 
                                @click="closeDetail()" 
                                class="absolute top-5 right-5 w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                        
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-white/10 flex items-center justify-center shrink-0 text-brand-lime">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-white">Rincian Transaksi Penerimaan</h3>
                                <p class="text-xs text-teal-200 mt-0.5">BPS Kabupaten Cirebon • Satker 428646</p>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Body -->
                    <div class="p-6 space-y-5 text-xs text-slate-600">
                        <!-- Main Item Card -->
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-teal-700 bg-teal-50 px-2 py-0.5 rounded-md border border-teal-200/60 inline-block mb-1.5" x-text="detailData.kategori"></span>
                                    <h4 class="text-base font-black text-slate-900 leading-snug" x-text="detailData.nama_barang"></h4>
                                    <p class="text-[11px] font-mono text-slate-500 mt-0.5">
                                        Kode: <span class="font-bold text-slate-700" x-text="detailData.kode_bmn"></span>
                                    </p>
                                </div>
                                <div class="text-right shrink-0">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black bg-emerald-100 text-emerald-800 border border-emerald-300">
                                        +<span x-text="detailData.jumlah"></span> <span class="ml-1" x-text="detailData.satuan"></span>
                                    </span>
                                </div>
                            </div>

                            <div class="mt-3 pt-3 border-t border-slate-200/60 flex items-center justify-between text-[11px]">
                                <span class="text-slate-500">Mutasi Saldo Fisik:</span>
                                <span class="font-mono font-bold text-slate-800">
                                    <span x-text="detailData.saldo_sebelum"></span> &rarr; <span class="text-emerald-700" x-text="detailData.saldo_setelah"></span> <span x-text="detailData.satuan"></span>
                                </span>
                            </div>

                            <template x-if="detailData.harga_satuan">
                                <div class="mt-1 pt-1 flex items-center justify-between text-[11px]">
                                    <span class="text-slate-500">Estimasi Nilai Pengadaan:</span>
                                    <span class="font-mono font-bold text-slate-900" x-text="detailData.estimasi_total + ' (@ ' + detailData.harga_satuan + ')'"></span>
                                </div>
                            </template>
                        </div>

                        <!-- Fact Sheet 2 Columns -->
                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">No. Dokumen / Faktur</span>
                                <span class="font-mono font-black text-slate-800 text-xs block" x-text="detailData.nomor_dokumen"></span>
                            </div>

                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Tanggal Penerimaan</span>
                                <span class="font-medium text-slate-800 text-xs block" x-text="detailData.tanggal_lengkap || detailData.tanggal"></span>
                            </div>

                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Penyedia / Vendor</span>
                                <span class="font-medium text-slate-800 text-xs block" x-text="detailData.rekanan"></span>
                            </div>

                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Status SPJ Keuangan</span>
                                <div class="pt-0.5">
                                    <template x-if="detailData.status_spj === 'selesai'">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            <span>SPJ Lunas (Kwitansi Ada)</span>
                                        </span>
                                    </template>
                                    <template x-if="detailData.status_spj !== 'selesai'">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                            <span>SPJ Tertunda (Langganan)</span>
                                        </span>
                                    </template>
                                </div>
                            </div>

                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Petugas Pencatat</span>
                                <span class="font-medium text-slate-800 text-xs block" x-text="detailData.pencatat"></span>
                            </div>

                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Lokasi Rak / Lemari</span>
                                <span class="font-medium text-slate-800 text-xs block" x-text="detailData.lokasi"></span>
                            </div>
                        </div>

                        <!-- Catatan / Keterangan -->
                        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Catatan / Keterangan</span>
                            <p class="text-xs text-slate-700 italic" x-text="detailData.keterangan"></p>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="bg-slate-50 px-6 py-4 border-t border-slate-200/80 flex items-center justify-between">
                        <a :href="'/kartu-kendali/barang/' + detailData.barang_id" 
                           class="inline-flex items-center gap-1.5 text-xs font-bold text-teal-700 hover:text-teal-900 transition">
                            <span>Buku Kendali Barang</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>

                        <button type="button" 
                                @click="closeDetail()" 
                                class="px-5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Modal Dialog -->
        <div x-show="editOpen" 
             x-cloak 
             class="fixed inset-0 z-50 overflow-y-auto" 
             style="display: none;">
            <!-- Backdrop -->
            <div x-show="editOpen" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0" 
                 x-transition:enter-end="opacity-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100" 
                 x-transition:leave-end="opacity-0" 
                 class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm transition-opacity" 
                 @click="closeEdit()"></div>

            <!-- Modal Content Panel -->
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div x-show="editOpen" 
                     x-transition:enter="ease-out duration-300" 
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave="ease-in duration-200" 
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-xl border border-slate-200/90">
                    
                    <!-- Header Banner -->
                    <div class="bg-gradient-to-r from-slate-900 via-teal-900 to-slate-900 p-6 text-white relative">
                        <button type="button" 
                                @click="closeEdit()" 
                                class="absolute top-5 right-5 w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                        
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-amber-500/20 text-amber-400 border border-amber-400/30 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-white">Koreksi Transaksi Penerimaan</h3>
                                <p class="text-xs text-teal-200 mt-0.5">Penyesuaian kuantitas & dokumen barang masuk</p>
                            </div>
                        </div>
                    </div>

                    <!-- Form Input -->
                    <form :action="editData.actionUrl" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="p-6 space-y-4 text-xs">
                            <!-- Info Barang Terkait -->
                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                                <div>
                                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Barang Persediaan</span>
                                    <h4 class="text-sm font-black text-slate-900 mt-0.5" x-text="editData.barang_nama"></h4>
                                    <span class="text-[11px] font-mono text-slate-500" x-text="'Kode: ' + editData.barang_kode"></span>
                                </div>
                                <div class="text-right shrink-0">
                                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Stok Aktif Fisik</span>
                                    <span class="text-xs font-bold text-teal-700 bg-teal-50 px-2.5 py-1 rounded-lg border border-teal-200 inline-block mt-0.5" 
                                          x-text="editData.stok_aktif + ' ' + editData.satuan"></span>
                                </div>
                            </div>

                            <!-- Alert Edukatif Sinkronisasi Stok -->
                            <div class="p-3 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 flex items-start gap-2.5 text-[11px] leading-relaxed">
                                <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>Mengubah kuantitas penerimaan di bawah ini akan <strong>secara otomatis mengoreksi saldo stok aktif</strong> pada katalog barang persediaan sebesar selisihnya.</span>
                            </div>

                            <!-- Field Kuantitas & Tanggal -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                <div>
                                    <label class="block font-bold text-slate-700 mb-1">
                                        Kuantitas Masuk Riil <span class="text-rose-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="number" 
                                               name="jumlah" 
                                               x-model.number="editData.jumlah" 
                                               min="1" 
                                               required 
                                               class="w-full py-2.5 pl-3 pr-14 bg-slate-50 border border-slate-200 rounded-xl font-mono font-bold text-slate-800 text-sm focus:ring-2 focus:ring-teal-500 focus:bg-white transition">
                                        <span class="absolute right-3 top-2.5 text-xs font-bold text-slate-400" x-text="editData.satuan"></span>
                                    </div>
                                </div>

                                <div>
                                    <label class="block font-bold text-slate-700 mb-1">
                                        Tanggal Penerimaan <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="date" 
                                           name="tanggal_transaksi" 
                                           x-model="editData.tanggal_transaksi" 
                                           required 
                                           class="w-full py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl font-mono text-slate-800 text-xs focus:ring-2 focus:ring-teal-500 focus:bg-white transition">
                                </div>
                            </div>

                            <!-- Field No Dokumen & Rekanan -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                <div>
                                    <label class="block font-bold text-slate-700 mb-1">
                                        No. Dokumen / Faktur / Kwitansi
                                    </label>
                                    <input type="text" 
                                           name="nomor_dokumen" 
                                           x-model="editData.nomor_dokumen" 
                                           placeholder="Contoh: KW-2026-001" 
                                           class="w-full py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl font-mono text-slate-800 text-xs focus:ring-2 focus:ring-teal-500 focus:bg-white transition">
                                </div>

                                <div>
                                    <label class="block font-bold text-slate-700 mb-1">
                                        Penyedia / Vendor / Rekanan
                                    </label>
                                    <input type="text" 
                                           name="sumber_atau_tujuan" 
                                           x-model="editData.sumber_atau_tujuan" 
                                           placeholder="Contoh: CV Mitra Sejahtera" 
                                           class="w-full py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs focus:ring-2 focus:ring-teal-500 focus:bg-white transition">
                                </div>
                            </div>

                            <!-- Status SPJ -->
                            <div>
                                <label class="block font-bold text-slate-700 mb-1">
                                    Status SPJ Keuangan <span class="text-rose-500">*</span>
                                </label>
                                <select name="status_spj" 
                                        x-model="editData.status_spj" 
                                        required 
                                        class="w-full py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-teal-500 focus:bg-white transition">
                                    <option value="selesai">SPJ Selesai (Kwitansi & Faktur Sudah Ada)</option>
                                    <option value="tertunda">SPJ Tertunda (Barang Langganan, Pembayaran Menyusul)</option>
                                </select>
                            </div>

                            <!-- Keterangan / Alasan Koreksi -->
                            <div>
                                <label class="block font-bold text-slate-700 mb-1">
                                    Catatan / Keterangan Tambahan
                                </label>
                                <textarea name="keterangan" 
                                          x-model="editData.keterangan" 
                                          rows="2" 
                                          placeholder="Catatan koreksi atau spesifikasi pengadaan..." 
                                          class="w-full py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs focus:ring-2 focus:ring-teal-500 focus:bg-white transition"></textarea>
                            </div>
                        </div>

                        <!-- Footer Actions -->
                        <div class="bg-slate-50 px-6 py-4 border-t border-slate-200/80 flex items-center justify-end gap-2.5">
                            <button type="button" 
                                    @click="closeEdit()" 
                                    class="px-4 py-2 rounded-xl bg-white border border-slate-300 hover:bg-slate-100 text-slate-700 font-bold text-xs transition">
                                Batal
                            </button>
                            <button type="submit" 
                                    class="px-5 py-2 rounded-xl bg-teal-800 hover:bg-teal-700 text-white font-bold text-xs shadow-sm transition">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>