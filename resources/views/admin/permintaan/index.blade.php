<x-app-layout>
    <x-slot:title>Persetujuan Permintaan ATK</x-slot:title>
    <x-slot:header>Pemeriksaan & Serah Terima Permintaan ATK Pegawai</x-slot:header>

    <div class="space-y-6">
        <!-- Metric Triage Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-5">
            <div class="bg-white p-5 rounded-3xl border border-slate-200/90 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Perlu Dicek</span>
                    <span class="text-2xl font-black text-amber-600 mt-1 block">{{ $countDiajukan }}</span>
                    <span class="text-[11px] text-slate-500">Menunggu Persetujuan Admin</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>

            <div class="bg-white p-5 rounded-3xl border border-slate-200/90 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Siap Diambil</span>
                    <span class="text-2xl font-black text-teal-600 mt-1 block">{{ $countDisetujui }}</span>
                    <span class="text-[11px] text-slate-500">Menunggu Diambil Pegawai</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
            </div>

            <div class="bg-white p-5 rounded-3xl border border-slate-200/90 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Sudah Diserahkan</span>
                    <span class="text-2xl font-black text-emerald-600 mt-1 block">{{ $countSelesai }}</span>
                    <span class="text-[11px] text-slate-500">Barang Sudah Diterima</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
            </div>

            <div class="bg-white p-5 rounded-3xl border border-slate-200/90 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Ditolak / Batal</span>
                    <span class="text-2xl font-black text-slate-600 mt-1 block">{{ $countDitolak }}</span>
                    <span class="text-[11px] text-slate-500">Pengajuan Tidak Disetujui</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-500 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </div>
            </div>
        </div>

        <!-- Filter & Search Toolbar -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/90 shadow-sm">
            <form action="{{ route('admin.permintaan.index') }}" method="GET" class="flex flex-col md:flex-row items-center gap-3">
                <div class="relative w-full md:w-80">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Cari No. Dokumen, Pemohon, NIP..." 
                           class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs focus:ring-2 focus:ring-teal-500 focus:bg-white transition">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>

                <select name="status" 
                        onchange="this.form.submit()" 
                        class="w-full sm:w-48 py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs focus:ring-2 focus:ring-teal-500 transition">
                    <option value="">Semua Status Antrean</option>
                    <option value="diajukan" {{ request('status') === 'diajukan' ? 'selected' : '' }}>Perlu Ditelaah (Diajukan)</option>
                    <option value="disetujui" {{ request('status') === 'disetujui' ? 'selected' : '' }}>Disetujui (Siap Diambil)</option>
                    <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai Diserahterimakan</option>
                    <option value="ditolak" {{ request('status') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>

                <select name="ruangan_id" 
                        onchange="this.form.submit()" 
                        class="w-full sm:w-56 py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs focus:ring-2 focus:ring-teal-500 transition">
                    <option value="">Semua Unit Kerja BPS</option>
                    @foreach($ruangans as $r)
                        <option value="{{ $r->id }}" {{ request('ruangan_id') == $r->id ? 'selected' : '' }}>{{ $r->nama_ruangan }}</option>
                    @endforeach
                </select>

                @if(request()->hasAny(['search', 'status', 'ruangan_id']))
                    <a href="{{ route('admin.permintaan.index') }}" class="text-xs font-semibold text-rose-600 hover:underline shrink-0">
                        Reset Filter
                    </a>
                @endif
            </form>
        </div>

        <!-- Table of Requests -->
        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                            <th class="py-3.5 px-5">Nomor Permintaan</th>
                            <th class="py-3.5 px-5">Tanggal</th>
                            <th class="py-3.5 px-5">Pegawai Pemohon</th>
                            <th class="py-3.5 px-5">Unit / Fungsi BPS</th>
                            <th class="py-3.5 px-5">Barang Diminta</th>
                            <th class="py-3.5 px-5 text-center">Status</th>
                            <th class="py-3.5 px-5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($permintaanList as $p)
                            <tr class="hover:bg-slate-50/80 transition duration-150 {{ $p->status === 'diajukan' ? 'bg-amber-50/20' : '' }}">
                                <td class="py-4 px-5 whitespace-nowrap">
                                    <a href="{{ route('admin.permintaan.show', $p->id) }}" class="font-extrabold text-teal-700 hover:underline font-mono">
                                        {{ $p->nomor_permintaan }}
                                    </a>
                                </td>
                                <td class="py-4 px-5 whitespace-nowrap text-slate-600 font-mono">
                                    {{ $p->tanggal_pengajuan->format('d/m/Y') }}
                                </td>
                                <td class="py-4 px-5">
                                    <div class="font-bold text-slate-900">{{ $p->user->name }}</div>
                                    <div class="text-[10px] text-slate-400 font-mono">NIP. {{ $p->user->nip }}</div>
                                </td>
                                <td class="py-4 px-5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[10px] font-semibold bg-slate-100 text-slate-700">
                                        {{ $p->ruangan->nama_ruangan ?? '-' }}
                                    </span>
                                </td>
                                <td class="py-4 px-5">
                                    <div class="flex flex-wrap gap-1 max-w-xs">
                                        @foreach($p->details as $d)
                                            <span class="text-[10px] font-medium text-slate-700 bg-slate-50 px-2 py-0.5 rounded border border-slate-200">
                                                {{ $d->barang->nama_barang }} ({{ $d->jumlah_diminta }})
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="py-4 px-5 text-center whitespace-nowrap">
                                    @if($p->status === 'diajukan')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                            <span>Perlu Ditelaah</span>
                                        </span>
                                    @elseif($p->status === 'disetujui')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-teal-50 text-teal-700 border border-teal-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-teal-500"></span>
                                            <span>Siap Diambil</span>
                                        </span>
                                    @elseif($p->status === 'selesai')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            <span>Selesai</span>
                                        </span>
                                    @elseif($p->status === 'ditolak')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                            <span>Ditolak</span>
                                        </span>
                                    @elseif($p->status === 'dibatalkan')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-500 border border-slate-200">
                                            <span>Dibatalkan</span>
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-5 text-right whitespace-nowrap">
                                    <a href="{{ route('admin.permintaan.show', $p->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl {{ $p->status === 'diajukan' ? 'bg-amber-500 text-white hover:bg-amber-600 shadow-sm' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }} text-[11px] font-bold transition">
                                        @if($p->status === 'diajukan')
                                            <span>Telaah Kuota</span>
                                        @elseif($p->status === 'disetujui')
                                            <span>Serah Terima</span>
                                        @else
                                            <span>Detail</span>
                                        @endif
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-slate-400">
                                    <svg class="w-10 h-10 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                    <p class="font-semibold text-sm">Tidak ada antrean permohonan ATK.</p>
                                    <p class="text-xs text-slate-400 mt-0.5">Semua permohonan dari pegawai telah diproses.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($permintaanList->hasPages())
                <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $permintaanList->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
