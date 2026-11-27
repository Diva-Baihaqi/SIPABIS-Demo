<x-app-layout>
    <x-slot:title>Kelola Kategori Persediaan</x-slot:title>
    <x-slot:header>Manajemen Kategori Barang Persediaan</x-slot:header>

    <div class="space-y-6" x-data="kategoriManager()">
        
        <!-- Header Banner & Ringkasan Metrik -->
        <div class="relative overflow-hidden bg-gradient-to-r from-bps-navy-800 via-bps-navy-700 to-bps-navy-900 rounded-3xl p-6 sm:p-7 text-white shadow-xl shadow-bps-navy-950/10 border border-bps-navy-700/80">
            <div class="absolute -right-10 -bottom-10 w-60 h-60 bg-bps-orange-500/15 rounded-full blur-2xl pointer-events-none"></div>
            
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="space-y-1.5">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-bps-orange-300 text-xs font-semibold border border-white/10">
                        <span>Klasifikasi Master Barang • Satker 428646 BPS Kab. Cirebon</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white">Kelola Kategori Persediaan</h1>
                    <p class="text-xs sm:text-sm text-slate-300 max-w-2xl leading-relaxed">
                        Atur klasifikasi kelompok barang persediaan pakai habis. Kategori yang dibuat di sini akan otomatis tersedia saat mendaftarkan barang baru, mengubah barang, maupun menyaring katalog.
                    </p>
                </div>

                <div class="flex items-center gap-3 shrink-0">
                    <a href="{{ route('barang.index') }}" class="px-4 py-2.5 rounded-xl font-bold text-xs bg-white/10 hover:bg-white/20 text-white transition flex items-center gap-2 border border-white/15">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        <span>Lihat Katalog</span>
                    </a>
                    <button type="button" 
                            @click="openCreateModal()"
                            class="px-5 py-3 rounded-xl font-bold text-xs bg-bps-orange-500 hover:bg-bps-orange-600 text-white shadow-lg shadow-bps-orange-500/25 hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Tambah Kategori Baru</span>
                    </button>
                </div>
            </div>

            <!-- Mini KPI Counters -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4 mt-6 pt-6 border-t border-white/10">
                <div class="p-3.5 rounded-2xl bg-white/5 border border-white/5">
                    <span class="text-[11px] text-slate-300 block font-medium">Total Kategori</span>
                    <span class="text-2xl font-black text-white mt-0.5 block">{{ number_format($totalKategori) }}</span>
                    <span class="text-[10px] text-slate-400">Seluruh kategori aktif di sistem</span>
                </div>
                <div class="p-3.5 rounded-2xl bg-white/5 border border-white/5">
                    <span class="text-[11px] text-emerald-300 block font-medium">Kategori Terisi</span>
                    <span class="text-2xl font-black text-emerald-400 mt-0.5 block">{{ number_format($kategoriTerisi) }}</span>
                    <span class="text-[10px] text-emerald-300/70">Memiliki item barang persediaan</span>
                </div>
                <div class="p-3.5 rounded-2xl bg-white/5 border border-white/5">
                    <span class="text-[11px] text-amber-300 block font-medium">Kategori Kosong</span>
                    <span class="text-2xl font-black text-amber-400 mt-0.5 block">{{ number_format($kategoriKosong) }}</span>
                    <span class="text-[10px] text-amber-300/70">Belum ada barang (dapat dihapus)</span>
                </div>
            </div>
        </div>

        <!-- Filter & Search Toolbar (Posisi Rapi & Berdampingan) -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-4 sm:p-5 shadow-sm">
            <form method="GET" action="{{ route('admin.kategori.index') }}" class="flex flex-wrap items-center gap-2.5 max-w-xl">
                <!-- Search Input Container -->
                <div class="relative flex-1 min-w-[260px]">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" 
                           name="search" 
                           value="{{ $search }}" 
                           placeholder="Cari nama atau kode kategori..."
                           class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-medium focus:ring-2 focus:ring-bps-blue-500 focus:bg-white focus:outline-none transition">
                </div>

                <!-- Tombol Cari & Reset (Menempel Berdampingan Rapi) -->
                <div class="flex items-center gap-2 shrink-0">
                    <button type="submit" class="px-5 py-2.5 rounded-xl font-bold text-xs bg-bps-navy-800 hover:bg-bps-navy-900 text-white shadow-sm hover:shadow transition flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <span>Cari</span>
                    </button>
                    @if($search)
                        <a href="{{ route('admin.kategori.index') }}" 
                           title="Reset Pencarian" 
                           class="p-2.5 rounded-xl text-slate-400 hover:text-rose-500 hover:bg-slate-100 transition shrink-0"
                           aria-label="Reset Filter">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Feedback Alert Messages -->
        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    <span class="font-semibold">{{ session('success') }}</span>
                </div>
            </div>
        @endif
        @if(session('error'))
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs sm:text-sm flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <svg class="w-5 h-5 text-rose-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    <span class="font-semibold">{{ session('error') }}</span>
                </div>
            </div>
        @endif
        @if($errors->any())
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs space-y-1">
                <p class="font-bold">Mohon periksa kesalahan pengisian formulir:</p>
                <ul class="list-disc list-inside text-[11px]">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Tabel Daftar Kategori -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs sm:text-sm">
                    <thead class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] uppercase tracking-wider text-slate-500 font-bold">
                        <tr>
                            <th class="py-3.5 px-4 text-center w-12">No</th>
                            <th class="py-3.5 px-4">Kode Kategori</th>
                            <th class="py-3.5 px-4">Nama Kategori</th>
                            <th class="py-3.5 px-4">Deskripsi / Cakupan Barang</th>
                            <th class="py-3.5 px-4 text-center">Jumlah Barang</th>
                            <th class="py-3.5 px-4 text-center w-28">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($kategoriList as $index => $kat)
                            <tr class="hover:bg-slate-50/70 transition">
                                <!-- No -->
                                <td class="py-3.5 px-4 text-center text-slate-400 font-medium">
                                    {{ $index + 1 }}
                                </td>

                                <!-- Kode Kategori -->
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-mono font-bold bg-slate-100 text-slate-800 border border-slate-200">
                                        {{ $kat->kode_kategori }}
                                    </span>
                                </td>

                                <!-- Nama Kategori -->
                                <td class="py-3.5 px-4">
                                    <p class="font-bold text-slate-900 text-sm">{{ $kat->nama_kategori }}</p>
                                </td>

                                <!-- Deskripsi -->
                                <td class="py-3.5 px-4 text-slate-500 text-xs max-w-md">
                                    {{ $kat->deskripsi ?: '-' }}
                                </td>

                                <!-- Jumlah Barang & Link Filter Katalog -->
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    @if($kat->barang_count > 0)
                                        <a href="{{ route('barang.index', ['kategori_id' => $kat->id]) }}" 
                                           title="Klik untuk melihat {{ $kat->barang_count }} barang pada katalog"
                                           class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-bps-blue-50 text-bps-blue-700 hover:bg-bps-blue-100 transition border border-bps-blue-200/50">
                                            <span>{{ number_format($kat->barang_count) }} Barang</span>
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                        </a>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-500">
                                            0 Barang (Kosong)
                                        </span>
                                    @endif
                                </td>

                                <!-- Aksi -->
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1.5">
                                        <!-- Tombol Edit -->
                                        <button type="button" 
                                                @click="openEditModal({
                                                    id: {{ $kat->id }},
                                                    kode_kategori: '{{ addslashes($kat->kode_kategori) }}',
                                                    nama_kategori: '{{ addslashes($kat->nama_kategori) }}',
                                                    deskripsi: '{{ addslashes($kat->deskripsi ?? '') }}'
                                                })"
                                                title="Edit Kategori"
                                                class="p-1.5 rounded-xl text-slate-500 hover:text-bps-orange-600 hover:bg-slate-100 transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>

                                        <!-- Tombol Hapus (Diproteksi jika ada barang) -->
                                        @if($kat->barang_count > 0)
                                            <button type="button" 
                                                    disabled
                                                    title="Kategori ini tidak dapat dihapus karena masih memuat {{ $kat->barang_count }} barang persediaan."
                                                    class="p-1.5 rounded-xl text-slate-300 cursor-not-allowed">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        @else
                                            <form action="{{ route('admin.kategori.destroy', $kat->id) }}" method="POST" class="inline"
                                                  data-confirm="Apakah Anda yakin ingin menghapus kategori '{{ $kat->nama_kategori }}'? Tindakan ini tidak dapat dibatalkan."
                                                  data-confirm-title="Hapus Kategori Persediaan"
                                                  data-confirm-type="danger"
                                                  data-confirm-btn="Ya, Hapus Kategori">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" title="Hapus Kategori Kosong" class="p-1.5 rounded-xl text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-16 text-slate-500">
                                    <div class="w-14 h-14 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                                    </div>
                                    <p class="font-bold text-slate-700 text-sm">Tidak ada kategori yang sesuai</p>
                                    <p class="text-xs text-slate-400 mt-1">Coba gunakan kata kunci pencarian yang lain.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- MODAL TAMBAH KATEGORI BARU -->
        <div x-show="showCreateModal" 
             x-cloak 
             class="fixed inset-0 z-50 overflow-y-auto"
             style="display: none;">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="showCreateModal = false"></div>

            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative w-full max-w-lg bg-white rounded-3xl shadow-2xl border border-slate-200/80 p-6 sm:p-7 overflow-hidden z-10"
                     @click.away="showCreateModal = false">
                    
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-xl bg-bps-orange-50 text-bps-orange-600 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            </div>
                            <div>
                                <h3 class="text-base font-black text-slate-900">Tambah Kategori Baru</h3>
                                <p class="text-xs text-slate-400">Buat klasifikasi baru untuk master barang persediaan BPS</p>
                            </div>
                        </div>
                        <button type="button" @click="showCreateModal = false" class="text-slate-400 hover:text-slate-600 p-1">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <form action="{{ route('admin.kategori.store') }}" method="POST" class="mt-5 space-y-4">
                        @csrf

                        <!-- Nama Kategori -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                Nama Kategori <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" 
                                   name="nama_kategori" 
                                   required 
                                   placeholder="Contoh: Peralatan Rumah Tangga Kantor"
                                   class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-medium focus:ring-2 focus:ring-bps-blue-500 focus:bg-white focus:outline-none transition">
                        </div>

                        <!-- Kode Kategori (Opsional / Otomatis) -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5 flex items-center justify-between">
                                <span>Kode Kategori (Singkatan)</span>
                                <span class="text-[10px] text-slate-400 font-normal lowercase">opsional (otomatis jika kosong)</span>
                            </label>
                            <input type="text" 
                                   name="kode_kategori" 
                                   placeholder="Contoh: RT_KANTOR (Huruf besar/kapital)"
                                   class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-mono uppercase focus:ring-2 focus:ring-bps-blue-500 focus:bg-white focus:outline-none transition">
                        </div>

                        <!-- Deskripsi -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                Deskripsi / Rincian Cakupan Barang
                            </label>
                            <textarea name="deskripsi" 
                                      rows="3" 
                                      placeholder="Rincian jenis barang yang termasuk ke dalam kategori ini..."
                                      class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-bps-blue-500 focus:bg-white focus:outline-none transition"></textarea>
                        </div>

                        <div class="pt-3 flex items-center justify-end gap-2.5 border-t border-slate-100">
                            <button type="button" @click="showCreateModal = false" class="px-4 py-2.5 rounded-xl font-bold text-xs text-slate-600 hover:bg-slate-100 transition">
                                Batal
                            </button>
                            <button type="submit" class="px-5 py-2.5 rounded-xl font-bold text-xs bg-bps-navy-800 hover:bg-bps-navy-900 text-white transition shadow-md">
                                Simpan Kategori
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>

        <!-- MODAL EDIT KATEGORI -->
        <div x-show="showEditModal" 
             x-cloak 
             class="fixed inset-0 z-50 overflow-y-auto"
             style="display: none;">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="showEditModal = false"></div>

            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative w-full max-w-lg bg-white rounded-3xl shadow-2xl border border-slate-200/80 p-6 sm:p-7 overflow-hidden z-10"
                     @click.away="showEditModal = false">
                    
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-xl bg-bps-blue-50 text-bps-blue-600 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-base font-black text-slate-900">Perbarui Kategori</h3>
                                <p class="text-xs text-slate-400">Ubah nama atau deskripsi kategori persediaan</p>
                            </div>
                        </div>
                        <button type="button" @click="showEditModal = false" class="text-slate-400 hover:text-slate-600 p-1">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <form :action="editActionUrl" method="POST" class="mt-5 space-y-4">
                        @csrf
                        @method('PUT')

                        <!-- Nama Kategori -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                Nama Kategori <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" 
                                   name="nama_kategori" 
                                   x-model="editForm.nama_kategori"
                                   required 
                                   class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-medium focus:ring-2 focus:ring-bps-blue-500 focus:bg-white focus:outline-none transition">
                        </div>

                        <!-- Kode Kategori -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                Kode Kategori <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" 
                                   name="kode_kategori" 
                                   x-model="editForm.kode_kategori"
                                   required 
                                   class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-mono uppercase focus:ring-2 focus:ring-bps-blue-500 focus:bg-white focus:outline-none transition">
                        </div>

                        <!-- Deskripsi -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                Deskripsi / Rincian Cakupan Barang
                            </label>
                            <textarea name="deskripsi" 
                                      x-model="editForm.deskripsi"
                                      rows="3" 
                                      class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-bps-blue-500 focus:bg-white focus:outline-none transition"></textarea>
                        </div>

                        <div class="pt-3 flex items-center justify-end gap-2.5 border-t border-slate-100">
                            <button type="button" @click="showEditModal = false" class="px-4 py-2.5 rounded-xl font-bold text-xs text-slate-600 hover:bg-slate-100 transition">
                                Batal
                            </button>
                            <button type="submit" class="px-5 py-2.5 rounded-xl font-bold text-xs bg-bps-blue-600 hover:bg-bps-blue-700 text-white transition shadow-md">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>

    </div>

    @push('scripts')
    <script>
        function kategoriManager() {
            return {
                showCreateModal: false,
                showEditModal: false,
                editActionUrl: '',
                editForm: {
                    id: null,
                    kode_kategori: '',
                    nama_kategori: '',
                    deskripsi: ''
                },

                openCreateModal() {
                    this.showCreateModal = true;
                },

                openEditModal(data) {
                    this.editForm = { ...data };
                    this.editActionUrl = "{{ url('admin/kategori') }}/" + data.id;
                    this.showEditModal = true;
                }
            };
        }
    </script>
    @endpush
</x-app-layout>
