<x-app-layout>
    <x-slot:title>Telaah Permintaan {{ $permintaan->nomor_permintaan }}</x-slot:title>
    <x-slot:header>Telaah & Verifikasi Permohonan ATK</x-slot:header>

    <div class="max-w-5xl mx-auto space-y-6"
         x-data="{
             showRejectModal: false,
             setFullApproval() {
                 @foreach($permintaan->details as $d)
                     if (document.getElementById('item_{{ $d->id }}')) {
                         document.getElementById('item_{{ $d->id }}').value = {{ min($d->jumlah_diminta, $d->barang->stok_aktif) }};
                     }
                 @endforeach
             }
         }">

        <!-- Back Button & Breadcrumb -->
        <div class="flex items-center justify-between">
            <a href="{{ route('admin.permintaan.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-600 hover:text-slate-900 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Kembali ke Antrean Verifikasi</span>
            </a>
            <div class="flex items-center gap-3">
                <a href="{{ route('permintaan.cetak', $permintaan->id) }}" target="_blank"
                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-2xl bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold border border-slate-300 shadow-sm transition">
                    <svg class="w-4 h-4 text-bps-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Formulir PSD</span>
                </a>
                <span class="text-[11px] font-mono text-slate-400 hidden sm:inline">Pengelolaan Persediaan Subbag Umum BPS 3209</span>
            </div>
        </div>

        <!-- Main Document Ribbon Card -->
        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm overflow-hidden">
            <!-- Ribbon -->
            <div class="bg-gradient-to-r from-bps-navy-800 to-slate-900 text-white p-6 md:p-8 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div>
                    <span class="text-xs font-bold text-teal-400 font-mono uppercase tracking-wider block">Tiket Permohonan ATK Resmi</span>
                    <h2 class="text-xl md:text-2xl font-black font-mono tracking-tight text-white mt-1">{{ $permintaan->nomor_permintaan }}</h2>
                    <div class="flex flex-wrap items-center gap-3 mt-2 text-xs text-slate-300">
                        <span>Pengusul: <strong>{{ $permintaan->user->name }}</strong> (NIP. {{ $permintaan->user->nip }})</span>
                        <span>•</span>
                        <span>Unit: <strong>{{ $permintaan->ruangan->nama_ruangan ?? '-' }}</strong></span>
                        <span>•</span>
                        <span>Tanggal: <strong>{{ $permintaan->tanggal_pengajuan->format('d/m/Y') }}</strong></span>
                    </div>
                </div>

                <div class="shrink-0">
                    @if($permintaan->status === 'diajukan')
                        <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-2xl text-xs font-bold bg-amber-400/20 text-amber-300 border border-amber-400/30">
                            <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                            <span>Menunggu Telaah Admin</span>
                        </span>
                    @elseif($permintaan->status === 'disetujui')
                        <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-2xl text-xs font-bold bg-teal-400/20 text-teal-300 border border-teal-400/30">
                            <span class="w-2 h-2 rounded-full bg-teal-400"></span>
                            <span>Disetujui (Siap Serah Terima)</span>
                        </span>
                    @elseif($permintaan->status === 'selesai')
                        <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-2xl text-xs font-bold bg-emerald-400/20 text-emerald-300 border border-emerald-400/30">
                            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Selesai Diserahterimakan</span>
                        </span>
                    @elseif($permintaan->status === 'ditolak')
                        <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-2xl text-xs font-bold bg-rose-400/20 text-rose-300 border border-rose-400/30">
                            <span>Ditolak</span>
                        </span>
                    @elseif($permintaan->status === 'dibatalkan')
                        <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-2xl text-xs font-bold bg-slate-400/20 text-slate-300 border border-slate-400/30">
                            <span>Dibatalkan Pemohon</span>
                        </span>
                    @endif
                </div>
            </div>

            <!-- Notes Section -->
            <div class="p-6 md:p-8 space-y-4 border-b border-slate-100 bg-slate-50/50">
                <div>
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Catatan Keperluan dari Pemohon:</span>
                    <p class="text-xs text-slate-800 mt-1 font-medium bg-white p-3.5 rounded-2xl border border-slate-200/80 shadow-xs">
                        {{ $permintaan->catatan_pemohon ?? 'Tidak ada catatan khusus.' }}
                    </p>
                </div>

                @if($permintaan->catatan_admin)
                    <div>
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Catatan / Alasan dari Subbag Umum:</span>
                        <p class="text-xs text-slate-800 mt-1 font-medium bg-amber-50 p-3.5 rounded-2xl border border-amber-200/80 text-amber-900">
                            {{ $permintaan->catatan_admin }}
                        </p>
                    </div>
                @endif
            </div>

            <!-- Case A: Status DIAJUKAN -> Triage / Approval Form -->
            @if($permintaan->status === 'diajukan')
                <form action="{{ route('admin.permintaan.verifikasi', $permintaan->id) }}" method="POST" class="p-6 md:p-8 space-y-6">
                    @csrf
                    
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 border-b border-slate-100 pb-4">
                        <div>
                            <h3 class="text-base font-extrabold text-slate-900">Verifikasi Kuota Barang</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Tentukan jumlah kuota yang disetujui berdasarkan ketersediaan buffer gudang BPS.</p>
                        </div>

                        <button type="button" 
                                @click="setFullApproval()" 
                                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-2xl bg-teal-50 hover:bg-teal-100 text-teal-800 text-xs font-bold border border-teal-200 transition">
                            <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Setujui Semua Kuota (Maksimal)</span>
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                                    <th class="py-3 px-4 w-12 text-center">#</th>
                                    <th class="py-3 px-4">Nama Barang Persediaan</th>
                                    <th class="py-3 px-4 w-36 text-center">Stok Gudang</th>
                                    <th class="py-3 px-4 w-32 text-center">Jumlah Diminta</th>
                                    <th class="py-3 px-4 w-40 text-center">Jumlah Disetujui</th>
                                    <th class="py-3 px-4 w-28 text-center">Satuan</th>
                                    <th class="py-3 px-4">Catatan Item</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($permintaan->details as $index => $item)
                                    <tr class="hover:bg-slate-50/60 transition">
                                        <td class="py-3.5 px-4 text-center font-bold text-slate-400">{{ $index + 1 }}</td>
                                        <td class="py-3.5 px-4">
                                            <div class="font-bold text-slate-900">{{ $item->barang->nama_barang }}</div>
                                            <div class="text-[10px] text-slate-400 font-mono mt-0.5">
                                                {{ $item->barang->kode_barang }} • {{ $item->barang->kategori?->nama_kategori }}
                                            </div>
                                        </td>
                                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                            <span class="inline-block px-3 py-1 rounded-xl {{ $item->barang->stok_aktif < $item->jumlah_diminta ? 'bg-amber-100 text-amber-800 font-black' : 'bg-slate-100 text-slate-700 font-bold' }} font-mono">
                                                {{ $item->barang->stok_aktif }} {{ $item->barang->satuan }}
                                            </span>
                                            @if($item->barang->stok_aktif < $item->jumlah_diminta)
                                                <div class="text-[9px] text-rose-600 font-semibold mt-0.5">Stok kurang</div>
                                            @endif
                                        </td>
                                        <td class="py-3.5 px-4 text-center whitespace-nowrap font-bold text-slate-800 font-mono">
                                            {{ $item->jumlah_diminta }}
                                        </td>
                                        <td class="py-3.5 px-4 text-center">
                                            <input type="number" 
                                                   id="item_{{ $item->id }}"
                                                   name="items[{{ $item->id }}]" 
                                                   value="{{ old('items.'.$item->id, min($item->jumlah_diminta, $item->barang->stok_aktif)) }}" 
                                                   min="0" 
                                                   max="{{ $item->jumlah_diminta }}"
                                                   required
                                                   class="w-24 text-center py-2 px-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-teal-900 focus:ring-2 focus:ring-teal-500 focus:bg-white transition">
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

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="catatan_admin" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                Catatan Verifikasi Subbag Umum (Opsional)
                            </label>
                            <input type="text" 
                                   id="catatan_admin" 
                                   name="catatan_admin" 
                                   value="{{ old('catatan_admin') }}" 
                                   placeholder="Contoh: Disetujui sebagian menyesuaikan stok logistik sensus..."
                                   class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs focus:ring-2 focus:ring-teal-500 focus:bg-white transition">
                        </div>
                        <div>
                            <label for="nomor_permintaan_custom" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                Nomor Dokumen PSD (Opsi Manual / Ubah Sesuai Bon)
                            </label>
                            <input type="text" 
                                   id="nomor_permintaan_custom" 
                                   name="nomor_permintaan_custom" 
                                   value="{{ old('nomor_permintaan_custom', $permintaan->nomor_permintaan) }}" 
                                   placeholder="Contoh: PSD-133/32091/PL.711/2026"
                                   class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-mono font-bold text-slate-800 focus:ring-2 focus:ring-teal-500 focus:bg-white transition">
                            <span class="text-[10px] text-slate-400 mt-1 block">Bisa diubah manual sesuai buku register, atau biarkan nomor otomatis sistem.</span>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                        <button type="button" 
                                @click="showRejectModal = true" 
                                class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-full bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs border border-rose-200 transition">
                            <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            <span>Tolak Permintaan Ini</span>
                        </button>

                        <button type="submit" class="inline-flex items-center gap-2 px-8 py-2.5 rounded-full bg-teal-700 hover:bg-teal-800 text-white font-bold text-xs shadow-md transition transform hover:scale-105 cursor-pointer">
                            <svg class="w-4 h-4 text-brand-lime" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            <span>Simpan Persetujuan Kuota</span>
                        </button>
                    </div>
                </form>

            <!-- Case B: Status DISETUJUI -> Ready for Serah Terima Fisik -->
            @elseif($permintaan->status === 'disetujui')
                <div class="p-6 md:p-8 space-y-6">
                    <div class="p-5 rounded-2xl bg-teal-50 border border-teal-200 text-teal-900 flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl bg-teal-600 text-white flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-extrabold text-teal-950">Permohonan Telah Disetujui</h4>
                            <p class="text-xs text-teal-800 mt-1">
                                Kuota telah disahkan oleh <strong>{{ $permintaan->verifikator->name ?? 'Admin' }}</strong>. Saat barang fisik diserahkan kepada pemohon, klik tombol konfirmasi di bawah untuk memotong stok fisik secara resmi di gudang dan mencatatnya ke kartu kendali.
                            </p>
                        </div>
                    </div>

                    <!-- Items Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                                    <th class="py-3 px-4 w-12 text-center">#</th>
                                    <th class="py-3 px-4">Barang</th>
                                    <th class="py-3 px-4 w-32 text-center">Diminta</th>
                                    <th class="py-3 px-4 w-36 text-center">Disetujui</th>
                                    <th class="py-3 px-4 w-28 text-center">Satuan</th>
                                    <th class="py-3 px-4">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($permintaan->details as $index => $item)
                                    <tr>
                                        <td class="py-3.5 px-4 text-center font-bold text-slate-400">{{ $index + 1 }}</td>
                                        <td class="py-3.5 px-4">
                                            <div class="font-bold text-slate-900">{{ $item->barang->nama_barang }}</div>
                                            <div class="text-[10px] text-slate-400 font-mono">{{ $item->barang->kode_barang }}</div>
                                        </td>
                                        <td class="py-3.5 px-4 text-center font-mono">{{ $item->jumlah_diminta }}</td>
                                        <td class="py-3.5 px-4 text-center">
                                            <span class="inline-block px-3 py-1 rounded-xl bg-teal-100 text-teal-800 font-black font-mono">
                                                {{ $item->jumlah_disetujui }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-4 text-center text-slate-600 font-medium">{{ $item->barang->satuan }}</td>
                                        <td class="py-3.5 px-4 text-slate-500">{{ $item->catatan_item ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Confirmation Form -->
                    <form action="{{ route('admin.permintaan.serah-terima', $permintaan->id) }}" method="POST" class="pt-4 border-t border-slate-100 flex items-center justify-end">
                        @csrf
                        <button type="submit" 
                                data-confirm="Konfirmasi serah terima fisik barang untuk permohonan '{{ $permintaan->nomor_permintaan }}'? Stok aktif gudang persediaan akan otomatis dipotong secara resmi."
                                data-confirm-title="Konfirmasi Serah Terima Fisik Barang"
                                data-confirm-type="success"
                                data-confirm-btn="Ya, Konfirmasi & Potong Stok"
                                class="inline-flex items-center gap-2 px-8 py-3 rounded-full bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-md transition transform hover:scale-105 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            <span>Konfirmasi Serah Terima Fisik Barang</span>
                        </button>
                    </form>
                </div>

            <!-- Case C: Status SELESAI -> Read Only Summary -->
            @else
                <div class="p-6 md:p-8 space-y-6">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                                    <th class="py-3 px-4 w-12 text-center">#</th>
                                    <th class="py-3 px-4">Nama Barang</th>
                                    <th class="py-3 px-4 w-32 text-center">Jumlah Diminta</th>
                                    <th class="py-3 px-4 w-32 text-center">Jumlah Diserahkan</th>
                                    <th class="py-3 px-4 w-28 text-center">Satuan</th>
                                    <th class="py-3 px-4">Catatan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($permintaan->details as $index => $item)
                                    <tr>
                                        <td class="py-3.5 px-4 text-center font-bold text-slate-400">{{ $index + 1 }}</td>
                                        <td class="py-3.5 px-4 font-bold text-slate-900">{{ $item->barang->nama_barang }}</td>
                                        <td class="py-3.5 px-4 text-center font-mono">{{ $item->jumlah_diminta }}</td>
                                        <td class="py-3.5 px-4 text-center">
                                            <span class="inline-block px-3 py-1 rounded-xl bg-slate-100 text-slate-800 font-bold font-mono">
                                                {{ $item->jumlah_disetujui }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-4 text-center text-slate-600 font-medium">{{ $item->barang->satuan }}</td>
                                        <td class="py-3.5 px-4 text-slate-500">{{ $item->catatan_item ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>

        <!-- Reject Modal -->
        <div x-show="showRejectModal" 
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
             style="display: none;">
            <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200 space-y-4" @click.outside="showRejectModal = false">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="font-extrabold text-sm text-slate-900">Tolak Permohonan ATK</h3>
                    <button type="button" @click="showRejectModal = false" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form action="{{ route('admin.permintaan.tolak', $permintaan->id) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="alasan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            Alasan Penolakan <span class="text-rose-500">*</span>
                        </label>
                        <textarea id="alasan" 
                                  name="alasan" 
                                  rows="4" 
                                  required
                                  placeholder="Contoh: Stok sedang diprioritaskan untuk persiapan Sensus Pertanian..."
                                  class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs focus:ring-2 focus:ring-rose-500 focus:bg-white transition"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button type="button" @click="showRejectModal = false" class="px-5 py-2 rounded-full border border-slate-300 text-slate-700 text-xs font-bold hover:bg-slate-50 transition">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-full bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-sm transition">
                            Konfirmasi Tolak
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
