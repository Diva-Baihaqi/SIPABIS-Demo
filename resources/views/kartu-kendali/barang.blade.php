<x-app-layout>
    <x-slot:title>Kartu Persediaan - {{ $barang->nama_barang }}</x-slot:title>
    <x-slot:header>Buku Catatan Keluar-Masuk Barang (Kartu Stok)</x-slot:header>

    <div class="space-y-6">

        <!-- Top Breadcrumb & Print Action -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <a href="{{ route('kartu-kendali.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-600 hover:text-slate-900 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>Kembali ke Katalog</span>
                </a>
                <span class="text-slate-300">•</span>
                <span class="text-xs font-mono font-bold text-teal-800 bg-teal-50 px-3 py-1 rounded-full border border-teal-200">
                    {{ $barang->kode_bmn_lengkap }}
                </span>
                <span class="text-xs text-slate-500 font-medium">BPS Kabupaten Cirebon</span>
            </div>

            <!-- Print & Export Action Buttons -->
            <div class="flex items-center gap-2">
                <a href="{{ route('kartu-kendali.barang.export', array_merge(['id' => $barang->id], request()->all())) }}" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md transition transform hover:scale-105">
                    <svg class="w-4 h-4 text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Ekspor Excel (.xlsx)</span>
                </a>
                <a href="{{ route('kartu-kendali.barang.cetak', array_merge(['id' => $barang->id], request()->all())) }}" 
                   target="_blank"
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-md transition transform hover:scale-105">
                    <svg class="w-4 h-4 text-brand-lime" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Format SAKTI (UC_PER53)</span>
                </a>
            </div>
        </div>

        <!-- Barang Identity Banner -->
        <div class="bg-gradient-to-r from-bps-navy-800 to-slate-900 text-white rounded-3xl p-6 md:p-8 shadow-xl flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
            <div>
                <span class="text-xs font-mono font-bold text-teal-400 uppercase tracking-wider block">Buku Catatan Riwayat Stok Barang</span>
                <h1 class="text-2xl sm:text-3xl font-black text-white mt-1">{{ $barang->nama_barang }}</h1>
                <div class="flex flex-wrap items-center gap-3 mt-2 text-xs text-slate-300">
                    <span>Kode BMN: <strong class="font-mono text-teal-300">{{ $barang->kode_bmn_lengkap }}</strong></span>
                    <span>•</span>
                    <span>Kategori: <strong>{{ $barang->kategori?->nama_kategori ?? 'Umum' }}</strong></span>
                    <span>•</span>
                    <span>Satuan: <strong>{{ $barang->satuan }}</strong></span>
                    @if($hargaSatuan > 0)
                        <span>•</span>
                        <span>Estimasi Harga: <strong class="text-amber-300">Rp {{ number_format($hargaSatuan, 0, ',', '.') }}</strong></span>
                    @endif
                </div>
            </div>

            <!-- Quick Item Switcher -->
            @if(isset($semuaBarang) && $semuaBarang->isNotEmpty())
                <div class="bg-white/10 p-3 rounded-2xl border border-white/10 flex items-center gap-2 shrink-0">
                    <span class="text-xs font-semibold text-slate-300">Ganti Item:</span>
                    <select onchange="window.location.href=this.value" class="bg-slate-900 text-white font-bold text-xs px-3 py-1.5 rounded-xl border border-slate-700 max-w-xs">
                        @foreach($semuaBarang as $sb)
                            <option value="{{ route('kartu-kendali.barang', $sb->id) }}" {{ $sb->id == $barang->id ? 'selected' : '' }}>
                                {{ $sb->nama_barang }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>

        <!-- Filter Toolbar -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/90 shadow-sm">
            <form action="{{ route('kartu-kendali.barang', $barang->id) }}" method="GET" class="flex flex-col sm:flex-row items-center gap-3">
                <div class="w-full sm:w-36">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Tahun Anggaran</label>
                    <select name="tahun" onchange="this.form.submit()" class="w-full py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-800">
                        @for($y = date('Y'); $y >= date('Y') - 3; $y--)
                            <option value="{{ $y }}" {{ ($filter['tahun'] ?? date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>

                <div class="w-full sm:w-44">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Dari Tanggal</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" onchange="this.form.submit()" class="w-full py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800">
                </div>

                <div class="w-full sm:w-44">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Sampai Tanggal</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" onchange="this.form.submit()" class="w-full py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800">
                </div>

                @if(request()->hasAny(['tahun', 'start_date', 'end_date']))
                    <div class="sm:ml-auto pt-2 sm:pt-4">
                        <a href="{{ route('kartu-kendali.barang', $barang->id) }}" class="text-xs font-semibold text-rose-600 hover:underline">
                            Reset Filter
                        </a>
                    </div>
                @endif
            </form>
        </div>

        <!-- Metric Balance Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 sm:gap-5">
            <div class="bg-white p-5 rounded-3xl border border-slate-200/90 shadow-sm">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Stok Awal Lemari</span>
                <span class="text-2xl font-black text-slate-800 mt-1 block font-mono">{{ number_format($saldoAwal) }}</span>
                <span class="text-[11px] text-slate-500">Stok bawaan awal periode @if($saldoAwalRp > 0)(Rp {{ number_format($saldoAwalRp, 0, ',', '.') }})@endif</span>
            </div>

            <div class="bg-white p-5 rounded-3xl border border-slate-200/90 shadow-sm">
                <span class="text-xs font-bold text-emerald-600 uppercase tracking-wider block">Barang Baru Datang (+)</span>
                <span class="text-2xl font-black text-emerald-600 mt-1 block font-mono">+{{ number_format($totalMasuk) }}</span>
                <span class="text-[11px] text-slate-500">{{ $totalMasuk > 0 ? 'Belanja / Pengadaan' : 'Belum ada belanja baru' }} @if($totalMasukRp > 0)(+Rp {{ number_format($totalMasukRp, 0, ',', '.') }})@endif</span>
            </div>

            <div class="bg-white p-5 rounded-3xl border border-slate-200/90 shadow-sm">
                <span class="text-xs font-bold text-rose-600 uppercase tracking-wider block">Barang Diambil Pegawai (-)</span>
                <span class="text-2xl font-black text-rose-600 mt-1 block font-mono">-{{ number_format($totalKeluar) }}</span>
                <span class="text-[11px] text-slate-500">Dipakai untuk dinas @if($totalKeluarRp > 0)(-Rp {{ number_format($totalKeluarRp, 0, ',', '.') }})@endif</span>
            </div>

            <div class="bg-white p-5 rounded-3xl border border-slate-200/90 shadow-sm bg-gradient-to-br from-teal-50/50 to-white">
                <span class="text-xs font-bold text-teal-700 uppercase tracking-wider block">Sisa Fisik di Gudang</span>
                <span class="text-2xl font-black text-teal-800 mt-1 block font-mono">{{ number_format($saldoAkhir) }}</span>
                <span class="text-[11px] text-teal-700 font-bold">Stok siap pakai saat ini @if($saldoAkhirRp > 0)(Rp {{ number_format($saldoAkhirRp, 0, ',', '.') }})@endif</span>
            </div>
        </div>

        <!-- Perpetual Ledger Table -->
        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h4 class="text-sm font-extrabold text-slate-900">Riwayat Keluar-Masuk Barang</h4>
                    <span class="text-xs text-slate-400">Catatan kronologis penerimaan belanja dan pengeluaran barang dinas</span>
                </div>
                <span class="text-xs font-mono font-bold text-slate-600">Satuan: {{ $barang->satuan }}</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                            <th class="py-3 px-4 w-12 text-center">No</th>
                            <th class="py-3 px-4 w-28">Tanggal</th>
                            <th class="py-3 px-4 w-44">No. Dokumen</th>
                            <th class="py-3 px-4">Uraian / Keterangan Transaksi</th>
                            <th class="py-3 px-4 w-28 text-center text-emerald-700 bg-emerald-50/40">Masuk</th>
                            <th class="py-3 px-4 w-28 text-center text-rose-700 bg-rose-50/40">Keluar</th>
                            <th class="py-3 px-4 w-32 text-center text-teal-800 bg-teal-50/50 font-black">Saldo</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <!-- Saldo Awal Row -->
                        <tr class="bg-slate-50/40 font-bold text-slate-700">
                            <td class="py-3 px-4 text-center text-slate-400 font-mono">—</td>
                            <td class="py-3 px-4 font-mono text-slate-500">{{ $startDate ?? 'Awal Periode' }}</td>
                            <td class="py-3 px-4 font-mono text-slate-400">—</td>
                            <td class="py-3 px-4 italic text-slate-600">Stok Awal Lemari</td>
                            <td class="py-3 px-4 text-center font-mono text-slate-400">—</td>
                            <td class="py-3 px-4 text-center font-mono text-slate-400">—</td>
                            <td class="py-3 px-4 text-center font-mono font-black text-slate-800 bg-teal-50/30">
                                {{ number_format($saldoAwal) }}
                            </td>
                        </tr>

                        <!-- Mutasi Rows -->
                        @forelse($mutasiList as $idx => $m)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-3.5 px-4 text-center font-mono text-slate-400 font-bold">{{ $idx + 1 }}</td>
                                <td class="py-3.5 px-4 font-mono text-slate-600 whitespace-nowrap">
                                    {{ $m->tanggal->format('d/m/Y') }}
                                </td>
                                <td class="py-3.5 px-4 font-mono font-bold text-slate-800 whitespace-nowrap">
                                    {{ $m->nomor_dokumen }}
                                </td>
                                <td class="py-3.5 px-4 text-slate-800">
                                    {{ $m->uraian }}
                                </td>
                                <td class="py-3.5 px-4 text-center font-mono whitespace-nowrap bg-emerald-50/20">
                                    @if($m->masuk > 0)
                                        <span class="font-bold text-emerald-700">+{{ number_format($m->masuk) }}</span>
                                        @if($m->masuk_rp > 0)
                                            <span class="block text-[10px] text-emerald-600 font-sans">Rp {{ number_format($m->masuk_rp, 0, ',', '.') }}</span>
                                        @endif
                                    @else
                                        <span class="text-slate-300">0</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center font-mono whitespace-nowrap bg-rose-50/20">
                                    @if($m->keluar > 0)
                                        <span class="font-bold text-rose-700">-{{ number_format($m->keluar) }}</span>
                                        @if($m->keluar_rp > 0)
                                            <span class="block text-[10px] text-rose-600 font-sans">Rp {{ number_format($m->keluar_rp, 0, ',', '.') }}</span>
                                        @endif
                                    @else
                                        <span class="text-slate-300">0</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center font-mono font-black text-teal-900 bg-teal-50/40">
                                    {{ number_format($m->saldo) }}
                                    @if($m->saldo_rp > 0)
                                        <span class="block text-[10px] text-teal-700 font-normal font-sans">Rp {{ number_format($m->saldo_rp, 0, ',', '.') }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-10 text-center text-slate-400">
                                    Tidak ada mutasi transaksi pada periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-slate-100/80 font-bold text-slate-800 border-t-2 border-slate-300">
                        <tr>
                            <td colspan="4" class="py-3.5 px-4 text-right uppercase tracking-wider text-[11px]">Total Mutasi & Saldo Akhir:</td>
                            <td class="py-3.5 px-4 text-center font-mono text-emerald-800 bg-emerald-100/60">
                                +{{ number_format($totalMasuk) }}
                                @if($totalMasukRp > 0)
                                    <span class="block text-[10px] font-normal font-sans text-emerald-700">Rp {{ number_format($totalMasukRp, 0, ',', '.') }}</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center font-mono text-rose-800 bg-rose-100/60">
                                -{{ number_format($totalKeluar) }}
                                @if($totalKeluarRp > 0)
                                    <span class="block text-[10px] font-normal font-sans text-rose-700">Rp {{ number_format($totalKeluarRp, 0, ',', '.') }}</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center font-mono font-black text-teal-950 bg-teal-100/80">
                                {{ number_format($saldoAkhir) }}
                                @if($saldoAkhirRp > 0)
                                    <span class="block text-[10px] font-bold font-sans text-teal-900">Rp {{ number_format($saldoAkhirRp, 0, ',', '.') }}</span>
                                @endif
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
