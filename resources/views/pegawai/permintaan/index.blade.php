<x-app-layout>
    <x-slot:title>Permintaan ATK Saya</x-slot:title>
    <x-slot:header>Pelayanan Permintaan ATK Pegawai</x-slot:header>

    <div class="space-y-6" x-data="permintaanLiveFilter()" @popstate.window="onPopState($event)">
        <!-- Top Status Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-5">
            <div class="bg-white p-5 rounded-3xl border border-slate-200/90 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Menunggu Verifikasi</span>
                    <span class="text-2xl font-black text-amber-600 mt-1 block">{{ $countDiajukan }}</span>
                    <span class="text-[11px] text-slate-500">Antrean Subbag Umum</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>

            <div class="bg-white p-5 rounded-3xl border border-slate-200/90 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Disetujui</span>
                    <span class="text-2xl font-black text-teal-600 mt-1 block">{{ $countDisetujui }}</span>
                    <span class="text-[11px] text-slate-500">Siap Diambil di Gudang</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>

            <div class="bg-white p-5 rounded-3xl border border-slate-200/90 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Sudah Diserahterimakan</span>
                    <span class="text-2xl font-black text-emerald-600 mt-1 block">{{ $countSelesai }}</span>
                    <span class="text-[11px] text-slate-500">Selesai & Tertib Administrasi</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
            </div>

            <div class="bg-gradient-to-br from-bps-navy-800 to-slate-900 p-5 rounded-3xl text-white shadow-md flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-white">Butuh ATK Baru?</h3>
                    <p class="text-[11px] text-slate-300 mt-0.5">Ajukan kebutuhan tim kerja Anda</p>
                    <a href="{{ route('pegawai.permintaan.create') }}" class="mt-3 inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-brand-lime hover:bg-brand-lime-hover text-slate-950 font-bold text-xs shadow-sm transition transform hover:scale-105">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                        <span>Buat Pengajuan</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Filter & Search Toolbar (Live Dynamic Filter) -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/90 shadow-sm">
            <form x-ref="filterForm" 
                  action="{{ route('pegawai.permintaan.index') }}" 
                  method="GET" 
                  @submit.prevent="applyFilter()"
                  class="flex flex-col md:flex-row items-center gap-3">
                
                <!-- Search Box -->
                <div class="relative w-full md:w-72">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Cari No. Permintaan, Rincian, Catatan..." 
                           class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs focus:ring-2 focus:ring-teal-500 focus:bg-white transition">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>

                <!-- Dropdown Kategori Barang -->
                <select name="kategori_id" 
                        @change="applyFilter()" 
                        class="w-full sm:w-56 py-2.5 px-4 bg-slate-50 border border-slate-200 rounded-2xl text-xs focus:ring-2 focus:ring-teal-500 transition">
                    <option value="">Semua Kategori ({{ $kategoriList->count() }})</option>
                    @foreach($kategoriList as $kat)
                        <option value="{{ $kat->id }}" {{ request('kategori_id') == $kat->id ? 'selected' : '' }}>
                            {{ $kat->nama_kategori }}
                        </option>
                    @endforeach
                </select>

                <!-- Dropdown Status -->
                <select name="status" 
                        @change="applyFilter()" 
                        class="w-full sm:w-56 py-2.5 px-4 bg-slate-50 border border-slate-200 rounded-2xl text-xs focus:ring-2 focus:ring-teal-500 transition">
                    <option value="">Semua Status Pengajuan</option>
                    <option value="diajukan" {{ request('status') === 'diajukan' ? 'selected' : '' }}>Menunggu Verifikasi (Diajukan)</option>
                    <option value="disetujui" {{ request('status') === 'disetujui' ? 'selected' : '' }}>Disetujui (Siap Diambil)</option>
                    <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai Diserahterimakan</option>
                    <option value="ditolak" {{ request('status') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    <option value="dibatalkan" {{ request('status') === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                </select>

                <div id="permintaan-reset-container" class="shrink-0">
                    @if(request()->hasAny(['search', 'kategori_id', 'status']))
                        <button type="button" 
                                @click="resetFilter('{{ route('pegawai.permintaan.index') }}')" 
                                class="text-xs font-semibold text-rose-600 hover:underline cursor-pointer">
                            Reset Filter
                        </button>
                    @endif
                </div>
            </form>
        </div>

        <!-- Table of Requests -->
        <div id="permintaan-table-container" 
             class="relative bg-white rounded-3xl border border-slate-200/90 shadow-sm overflow-hidden"
             @click="handleTableClick($event)">

            <!-- Loading Spinner Overlay -->
            <div x-show="isLoading" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="absolute inset-0 z-20 bg-white/70 backdrop-blur-[2px] flex items-center justify-center"
                 style="display: none;">
                <div class="inline-flex items-center gap-3 px-4 py-2.5 rounded-2xl bg-white shadow-xl border border-slate-200 text-slate-800 text-xs font-bold">
                    <svg class="animate-spin w-4 h-4 text-teal-600" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <span>Memperbarui data permohonan...</span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                            <th class="py-3.5 px-5">Nomor Permintaan</th>
                            <th class="py-3.5 px-5">Tanggal Pengajuan</th>
                            <th class="py-3.5 px-5">Rincian Barang</th>
                            <th class="py-3.5 px-5">Peruntukan / Kegiatan</th>
                            <th class="py-3.5 px-5 text-center">Status</th>
                            <th class="py-3.5 px-5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($permintaanList as $p)
                            <tr class="hover:bg-slate-50/80 transition duration-150">
                                <td class="py-4 px-5 whitespace-nowrap">
                                    <a href="{{ route('pegawai.permintaan.show', $p->id) }}" class="font-extrabold text-teal-700 hover:underline font-mono">
                                        {{ $p->nomor_permintaan }}
                                    </a>
                                </td>
                                <td class="py-4 px-5 whitespace-nowrap text-slate-600 font-mono">
                                    {{ $p->tanggal_pengajuan->format('d/m/Y') }}
                                </td>
                                <td class="py-4 px-5">
                                    <div class="flex flex-wrap gap-1.5 max-w-sm">
                                        @foreach($p->details as $d)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[10px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                                {{ $d->barang->nama_barang }} ({{ $d->jumlah_diminta }} {{ $d->barang->satuan }})
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="py-4 px-5">
                                    <span class="text-slate-700 font-medium line-clamp-2">{{ $p->catatan_pemohon ?? '-' }}</span>
                                </td>
                                <td class="py-4 px-5 text-center whitespace-nowrap">
                                    @if($p->status === 'diajukan')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                            <span>Menunggu Verifikasi</span>
                                        </span>
                                    @elseif($p->status === 'disetujui')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-teal-50 text-teal-700 border border-teal-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-teal-500"></span>
                                            <span>Disetujui (Ambil Fisik)</span>
                                        </span>
                                    @elseif($p->status === 'selesai')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            <span>Selesai Diserahterimakan</span>
                                        </span>
                                    @elseif($p->status === 'ditolak')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                            <span>Ditolak</span>
                                        </span>
                                    @elseif($p->status === 'dibatalkan')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-500 border border-slate-200">
                                            <span>Dibatalkan</span>
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-5 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('pegawai.permintaan.show', $p->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-bold transition">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            <span>Detail</span>
                                        </a>

                                        @if($p->status === 'diajukan')
                                            <form action="{{ route('pegawai.permintaan.destroy', $p->id) }}" method="POST" class="inline" 
                                                  data-confirm="Apakah Anda yakin ingin membatalkan pengajuan nomor '{{ $p->nomor_permintaan }}'? Pengajuan yang dibatalkan tidak dapat dikembalikan."
                                                  data-confirm-title="Batalkan Pengajuan Permintaan"
                                                  data-confirm-type="danger"
                                                  data-confirm-btn="Ya, Batalkan Pengajuan">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 rounded-xl text-rose-500 hover:text-rose-700 hover:bg-rose-50 transition" title="Batalkan Pengajuan">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-400">
                                    <svg class="w-10 h-10 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                    <p class="font-semibold text-sm">Belum ada permohonan ATK yang diajukan.</p>
                                    <p class="text-xs text-slate-400 mt-0.5">Klik tombol "Buat Pengajuan" untuk meminta persediaan ATK.</p>
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

    @push('scripts')
    <script>
        function permintaanLiveFilter() {
            return {
                isLoading: false,
                currentAbortController: null,

                async applyFilter(targetUrl = null) {
                    const form = this.$refs.filterForm;
                    let url;

                    if (targetUrl) {
                        url = targetUrl;
                    } else {
                        const formData = new FormData(form);
                        const params = new URLSearchParams();
                        for (const [key, value] of formData.entries()) {
                            if (value !== '' && value !== null) {
                                params.append(key, value);
                            }
                        }
                        const queryString = params.toString();
                        url = form.action + (queryString ? '?' + queryString : '');
                    }

                    if (this.currentAbortController) {
                        this.currentAbortController.abort();
                    }
                    this.currentAbortController = new AbortController();

                    this.isLoading = true;

                    try {
                        const response = await fetch(url, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            signal: this.currentAbortController.signal
                        });

                        if (!response.ok) throw new Error('Network error: ' + response.status);

                        const htmlText = await response.text();
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(htmlText, 'text/html');

                        // 1. Update Table Container
                        const newTable = doc.getElementById('permintaan-table-container');
                        const currentTable = document.getElementById('permintaan-table-container');
                        if (newTable && currentTable) {
                            currentTable.innerHTML = newTable.innerHTML;
                        }

                        // 2. Update Reset Button Container
                        const newReset = doc.getElementById('permintaan-reset-container');
                        const currentReset = document.getElementById('permintaan-reset-container');
                        if (newReset && currentReset) {
                            currentReset.innerHTML = newReset.innerHTML;
                        }

                        // 3. Sync browser URL without full-page reload
                        if (window.location.href !== url) {
                            window.history.pushState({ path: url }, '', url);
                        }

                        // 4. Sync form input values if targetUrl was used (e.g. pagination or reset or popstate)
                        if (targetUrl) {
                            const parsedUrl = new URL(url, window.location.origin);
                            if (form.elements['search']) form.elements['search'].value = parsedUrl.searchParams.get('search') || '';
                            if (form.elements['kategori_id']) form.elements['kategori_id'].value = parsedUrl.searchParams.get('kategori_id') || '';
                            if (form.elements['status']) form.elements['status'].value = parsedUrl.searchParams.get('status') || '';
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

                    this.applyFilter(resetUrl);
                },

                handleTableClick(event) {
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
