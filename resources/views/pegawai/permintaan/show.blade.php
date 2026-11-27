<x-app-layout>
    <x-slot:title>Detail Permintaan {{ $permintaan->nomor_permintaan }}</x-slot:title>
    <x-slot:header>Detail Tiket Permohonan ATK</x-slot:header>

    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Back Button & Status Header -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <a href="{{ route('pegawai.permintaan.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-600 hover:text-slate-900 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Kembali ke Riwayat Permintaan</span>
            </a>

            <div class="flex items-center gap-2">
                <a href="{{ route('permintaan.cetak', $permintaan->id) }}" target="_blank"
                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-2xl bg-bps-blue-50 hover:bg-bps-blue-100 text-bps-blue-700 text-xs font-bold border border-bps-blue-200 transition shadow-sm">
                    <svg class="w-4 h-4 text-bps-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Formulir PSD</span>
                </a>

                @if($permintaan->status === 'diajukan')
                    <form action="{{ route('pegawai.permintaan.destroy', $permintaan->id) }}" method="POST"
                          data-confirm="Apakah Anda yakin ingin membatalkan permohonan nomor '{{ $permintaan->nomor_permintaan }}'? Tindakan ini tidak dapat dibatalkan."
                          data-confirm-title="Batalkan Permohonan ATK"
                          data-confirm-type="danger"
                          data-confirm-btn="Ya, Batalkan Permohonan">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-2xl bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold border border-rose-200 transition">
                            <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            <span>Batalkan Permohonan</span>
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Main Ticket Card -->
        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm overflow-hidden">
            <!-- Ticket Header Ribbon -->
            <div class="bg-gradient-to-r from-bps-navy-800 to-slate-900 text-white p-6 md:p-8 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div>
                    <span class="text-xs font-bold text-teal-400 font-mono uppercase tracking-wider block">Nomor Dokumen Resmi BPS</span>
                    <h2 class="text-xl md:text-2xl font-black font-mono tracking-tight text-white mt-1">{{ $permintaan->nomor_permintaan }}</h2>
                    <div class="flex items-center gap-2 mt-2 text-xs text-slate-300">
                        <span>Diajukan pada: <strong>{{ $permintaan->tanggal_pengajuan->format('d F Y') }}</strong></span>
                        <span>•</span>
                        <span>Unit: <strong>{{ $permintaan->ruangan->nama_ruangan ?? '-' }}</strong></span>
                    </div>
                </div>

                <div class="shrink-0">
                    @if($permintaan->status === 'diajukan')
                        <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-2xl text-xs font-bold bg-amber-400/20 text-amber-300 border border-amber-400/30">
                            <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                            <span>Menunggu Verifikasi</span>
                        </span>
                    @elseif($permintaan->status === 'disetujui')
                        <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-2xl text-xs font-bold bg-teal-400/20 text-teal-300 border border-teal-400/30">
                            <span class="w-2 h-2 rounded-full bg-teal-400"></span>
                            <span>Disetujui (Siap Diambil)</span>
                        </span>
                    @elseif($permintaan->status === 'selesai')
                        <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-2xl text-xs font-bold bg-emerald-400/20 text-emerald-300 border border-emerald-400/30">
                            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Selesai Diserahterimakan</span>
                        </span>
                    @elseif($permintaan->status === 'ditolak')
                        <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-2xl text-xs font-bold bg-rose-400/20 text-rose-300 border border-rose-400/30">
                            <span>Ditolak Subbag Umum</span>
                        </span>
                    @elseif($permintaan->status === 'dibatalkan')
                        <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-2xl text-xs font-bold bg-slate-400/20 text-slate-300 border border-slate-400/30">
                            <span>Dibatalkan</span>
                        </span>
                    @endif
                </div>
            </div>

            <!-- Workflow Progress Timeline -->
            <div class="p-6 md:p-8 border-b border-slate-100 bg-slate-50/50">
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-6">Alur Status Tiket</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 relative">
                    <!-- Step 1 -->
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full bg-teal-600 text-white flex items-center justify-center font-bold text-xs shrink-0 shadow-sm">
                            ✓
                        </div>
                        <div>
                            <h5 class="text-xs font-extrabold text-slate-900">1. Pengajuan Dibuat</h5>
                            <p class="text-[11px] text-slate-500 mt-0.5">{{ $permintaan->user->name }}</p>
                            <span class="text-[10px] text-slate-400 font-mono">{{ $permintaan->tanggal_pengajuan->format('d/m/Y') }}</span>
                        </div>
                    </div>

                    <!-- Step 2 -->
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full {{ in_array($permintaan->status, ['disetujui', 'selesai', 'ditolak']) ? 'bg-teal-600 text-white' : 'bg-slate-200 text-slate-500' }} flex items-center justify-center font-bold text-xs shrink-0 shadow-sm">
                            {{ in_array($permintaan->status, ['disetujui', 'selesai', 'ditolak']) ? '✓' : '2' }}
                        </div>
                        <div>
                            <h5 class="text-xs font-extrabold {{ in_array($permintaan->status, ['disetujui', 'selesai', 'ditolak']) ? 'text-slate-900' : 'text-slate-400' }}">2. Verifikasi Subbag Umum</h5>
                            @if($permintaan->tanggal_verifikasi)
                                <p class="text-[11px] text-slate-600 mt-0.5">{{ $permintaan->verifikator->name ?? 'Admin Subbag Umum' }}</p>
                                <span class="text-[10px] text-slate-400 font-mono">{{ $permintaan->tanggal_verifikasi->format('d/m/Y H:i') }}</span>
                            @else
                                <p class="text-[11px] text-amber-600 mt-0.5 font-medium">Menunggu pemeriksaan admin</p>
                            @endif
                        </div>
                    </div>

                    <!-- Step 3 -->
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full {{ $permintaan->status === 'selesai' ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-500' }} flex items-center justify-center font-bold text-xs shrink-0 shadow-sm">
                            {{ $permintaan->status === 'selesai' ? '✓' : '3' }}
                        </div>
                        <div>
                            <h5 class="text-xs font-extrabold {{ $permintaan->status === 'selesai' ? 'text-slate-900' : 'text-slate-400' }}">3. Serah Terima Fisik Barang</h5>
                            @if($permintaan->tanggal_serah_terima)
                                <p class="text-[11px] text-emerald-700 mt-0.5 font-semibold">Barang telah diserahterimakan</p>
                                <span class="text-[10px] text-slate-400 font-mono">{{ $permintaan->tanggal_serah_terima->format('d/m/Y H:i') }}</span>
                            @else
                                <p class="text-[11px] text-slate-400 mt-0.5">Pengambilan fisik di gudang BPS</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Catatan Pemohon & Admin -->
            <div class="p-6 md:p-8 space-y-4 border-b border-slate-100">
                <div>
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Catatan Keperluan Pemohon:</span>
                    <p class="text-xs text-slate-800 mt-1 font-medium bg-slate-50 p-3.5 rounded-2xl border border-slate-200/80">
                        {{ $permintaan->catatan_pemohon ?? 'Tidak ada catatan khusus.' }}
                    </p>
                </div>

                @if($permintaan->catatan_admin)
                    <div>
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Catatan / Keterangan Subbag Umum:</span>
                        <p class="text-xs text-slate-800 mt-1 font-medium bg-amber-50/60 p-3.5 rounded-2xl border border-amber-200/80 text-amber-900">
                            {{ $permintaan->catatan_admin }}
                        </p>
                    </div>
                @endif
            </div>

            <!-- Items Table -->
            <div class="p-6 md:p-8 space-y-4">
                <h4 class="text-sm font-extrabold text-slate-900">Daftar Barang yang Diajukan</h4>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                                <th class="py-3 px-4 w-12 text-center">#</th>
                                <th class="py-3 px-4">Nama & Kode Barang</th>
                                <th class="py-3 px-4 w-32 text-center">Jumlah Diminta</th>
                                <th class="py-3 px-4 w-32 text-center">Jumlah Disetujui</th>
                                <th class="py-3 px-4 w-28 text-center">Satuan</th>
                                <th class="py-3 px-4">Catatan Item</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($permintaan->details as $index => $item)
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="py-3.5 px-4 text-center font-bold text-slate-400">{{ $index + 1 }}</td>
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-slate-900">{{ $item->barang->nama_barang }}</div>
                                        <div class="text-[10px] text-slate-400 font-mono mt-0.5">
                                            {{ $item->barang->kode_barang }} • {{ $item->barang->kategori?->nama_kategori }}
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        <span class="inline-block px-3 py-1 rounded-xl bg-slate-100 text-slate-800 font-bold font-mono">
                                            {{ $item->jumlah_diminta }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        @if(in_array($permintaan->status, ['disetujui', 'selesai']))
                                            @if($item->jumlah_disetujui < $item->jumlah_diminta)
                                                <span class="inline-block px-3 py-1 rounded-xl bg-amber-50 text-amber-700 border border-amber-200 font-bold font-mono" title="Disetujui sebagian">
                                                    {{ $item->jumlah_disetujui }} (Sebagian)
                                                </span>
                                            @else
                                                <span class="inline-block px-3 py-1 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold font-mono">
                                                    {{ $item->jumlah_disetujui }} (Penuh)
                                                </span>
                                            @endif
                                        @else
                                            <span class="text-slate-400 font-mono">—</span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-center text-slate-600 font-medium">
                                        {{ $item->barang->satuan }}
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-500">
                                        {{ $item->catatan_item ?? '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
