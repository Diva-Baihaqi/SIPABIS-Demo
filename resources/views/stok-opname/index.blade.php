<x-app-layout>
    <x-slot:title>Stock Opname & BASO</x-slot:title>
    <x-slot:header>Stock Opname & Berita Acara Fisik Persediaan (BASO)</x-slot:header>

    <div class="space-y-6">

        <!-- Top Overview & Metric Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-5">
            <div class="bg-white p-5 rounded-3xl border border-slate-200/90 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Sesi Sedang Berjalan</span>
                    <span class="text-2xl font-black {{ $activeDraft ? 'text-amber-600' : 'text-slate-800' }} mt-1 block">
                        {{ $activeDraft ? '1 Aktif' : 'Tidak Ada' }}
                    </span>
                    <span class="text-[11px] text-slate-500">
                        {{ $activeDraft ? $activeDraft->periode : 'Semua sesi final/selesai' }}
                    </span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>

            <div class="bg-white p-5 rounded-3xl border border-slate-200/90 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Menunggu Pengesahan</span>
                    <span class="text-2xl font-black text-teal-600 mt-1 block">{{ $totalFinal }}</span>
                    <span class="text-[11px] text-slate-500">Perlu Pengesahan Kepala BPS</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>

            <div class="bg-white p-5 rounded-3xl border border-slate-200/90 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">BASO Disahkan</span>
                    <span class="text-2xl font-black text-emerald-600 mt-1 block">{{ $totalDisahkan }}</span>
                    <span class="text-[11px] text-slate-500">Dokumen Hukum Sah BPS</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
            </div>

            <div class="bg-gradient-to-br from-bps-navy-800 to-slate-900 p-5 rounded-3xl text-white shadow-md flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-white">Opname Periodik</h3>
                    <p class="text-[11px] text-slate-300 mt-0.5">Ukur kepatuhan saldo fisik riil</p>
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('stok-opname.create') }}" class="mt-3 inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-brand-lime hover:bg-brand-lime-hover text-slate-950 font-bold text-xs shadow-sm transition transform hover:scale-105">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                            <span>Buka Sesi Opname</span>
                        </a>
                    @else
                        <span class="mt-3 inline-block text-[11px] text-slate-400">Khusus Subbag Umum</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Filter & Table Card -->
        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                <form action="{{ route('stok-opname.index') }}" method="GET" class="w-full sm:w-auto flex items-center gap-3">
                    <select name="status" onchange="this.form.submit()" class="py-2.5 px-4 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-semibold focus:ring-2 focus:ring-teal-500 transition">
                        <option value="">Semua Status Sesi</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Sesi Draft (Perhitungan Fisik)</option>
                        <option value="final" {{ request('status') === 'final' ? 'selected' : '' }}>Selesai Finalisasi (Tunggu Sahkan)</option>
                        <option value="disahkan" {{ request('status') === 'disahkan' ? 'selected' : '' }}>Resmi Disahkan Kepala BPS</option>
                    </select>

                    @if(request()->filled('status'))
                        <a href="{{ route('stok-opname.index') }}" class="text-xs font-semibold text-rose-600 hover:underline">
                            Reset
                        </a>
                    @endif
                </form>

                @if(auth()->user()->isAdmin())
                    <a href="{{ route('stok-opname.create') }}" class="hidden sm:inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-sm transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Buka Sesi Opname Baru</span>
                    </a>
                @endif
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                            <th class="py-3.5 px-5">Nomor Berita Acara</th>
                            <th class="py-3.5 px-5">Tanggal Pelaksanaan</th>
                            <th class="py-3.5 px-5">Periode Audit</th>
                            <th class="py-3.5 px-5">Petugas Pemeriksa</th>
                            <th class="py-3.5 px-5 text-center">Status Sesi</th>
                            <th class="py-3.5 px-5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($opnameList as $op)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-4 px-5 whitespace-nowrap">
                                    <a href="{{ route('stok-opname.show', $op->id) }}" class="font-extrabold text-teal-700 hover:underline font-mono">
                                        {{ $op->nomor_opname }}
                                    </a>
                                </td>
                                <td class="py-4 px-5 whitespace-nowrap font-mono text-slate-600">
                                    {{ $op->tanggal_opname->format('d/m/Y') }}
                                </td>
                                <td class="py-4 px-5 font-bold text-slate-800">
                                    {{ $op->periode }}
                                </td>
                                <td class="py-4 px-5">
                                    <div class="font-bold text-slate-900">{{ $op->user->name ?? 'Admin Subbag Umum' }}</div>
                                    <div class="text-[10px] text-slate-400 font-mono">Subbag Umum BPS</div>
                                </td>
                                <td class="py-4 px-5 text-center whitespace-nowrap">
                                    @if($op->status === 'draft')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                            <span>Draft Perhitungan Fisik</span>
                                        </span>
                                    @elseif($op->status === 'final')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-teal-50 text-teal-700 border border-teal-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-teal-500"></span>
                                            <span>Final (Menunggu Sahkan)</span>
                                        </span>
                                    @elseif($op->status === 'disahkan')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                            <span>Disahkan Kepala BPS</span>
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-5 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('stok-opname.show', $op->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition">
                                            <span>{{ $op->status === 'draft' ? 'Hitung Fisik' : 'Rincian' }}</span>
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                        </a>

                                        @if($op->status !== 'draft')
                                            <a href="{{ route('stok-opname.cetak', $op->id) }}" target="_blank" class="p-1.5 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition" title="Cetak Berita Acara">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                            </a>
                                        @endif

                                        @if(Auth::user()->isAdmin())
                                            <form action="{{ route('stok-opname.destroy', $op->id) }}" method="POST" class="inline"
                                                  data-confirm="Apakah Anda yakin ingin menghapus sesi Stock Opname {{ $op->nomor_opname }}?{{ $op->status !== 'draft' ? ' Peringatan: Seluruh penyesuaian saldo stok akan dibatalkan/direverse otomatis.' : '' }}"
                                                  data-confirm-title="Hapus Sesi Stock Opname"
                                                  data-confirm-type="danger"
                                                  data-confirm-btn="Ya, Hapus Sesi">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 rounded-xl text-rose-500 hover:text-rose-700 hover:bg-rose-50 transition" title="Hapus Sesi Opname">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-400">
                                    <svg class="w-10 h-10 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                                    <p class="font-semibold text-sm">Belum ada sesi Stock Opname yang tercatat.</p>
                                    <p class="text-xs text-slate-400 mt-0.5">Buka sesi opname baru untuk memulai perhitungan fisik berkala.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($opnameList->hasPages())
                <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $opnameList->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
