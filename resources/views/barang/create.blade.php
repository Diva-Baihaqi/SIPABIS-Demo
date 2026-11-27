<x-app-layout>
    <x-slot:title>Tambah Barang Baru</x-slot:title>
    <x-slot:header>Pendaftaran Master Barang Persediaan</x-slot:header>

    <div class="max-w-4xl mx-auto space-y-6">
        
        <!-- Header Card -->
        <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-7 shadow-sm">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div>
                    <h2 class="text-lg font-black text-slate-900">Formulir Tambah Barang Persediaan</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Daftarkan item barang pakai habis baru ke katalog master BPS Satker 428646</p>
                </div>
                <a href="{{ route('barang.index') }}" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition">
                    &larr; Kembali ke Katalog
                </a>
            </div>

            <!-- Error Notification -->
            @if($errors->any())
                <div class="mt-5 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs space-y-1">
                    <p class="font-bold flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-rose-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        Mohon periksa kembali isian formulir:
                    </p>
                    <ul class="list-disc list-inside pl-1 text-[11px] space-y-0.5">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Form -->
            <form action="{{ route('barang.store') }}" method="POST" enctype="multipart/form-data" class="mt-6 space-y-5">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <!-- Kode Barang BPS -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Kode Barang <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="kode_barang" value="{{ old('kode_barang') }}" placeholder="Contoh: 1.01.03.01.001.000438 atau 000438" required
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-mono focus:ring-2 focus:ring-bps-blue-500 focus:bg-white focus:outline-none transition">
                        <span class="text-[10px] text-slate-400 mt-1 block">Kode identifikasi unik barang persediaan</span>
                    </div>

                    <!-- Kode SAKTI BMN -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Kode SAKTI (Opsional)
                        </label>
                        <input type="text" name="kode_sakti" value="{{ old('kode_sakti') }}" placeholder="Contoh: 1010301001"
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-mono focus:ring-2 focus:ring-bps-blue-500 focus:bg-white focus:outline-none transition">
                        <span class="text-[10px] text-slate-400 mt-1 block">Referensi kode barang di modul persediaan SAKTI Kemenkeu</span>
                    </div>

                    <!-- Nama Barang -->
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Nama Barang Persediaan <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="nama_barang" value="{{ old('nama_barang') }}" placeholder="Contoh: Kertas HVS A4 80 gram PaperOne" required
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-medium focus:ring-2 focus:ring-bps-blue-500 focus:bg-white focus:outline-none transition">
                    </div>

                    <!-- Kategori Barang -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                                Kategori Standar BPS <span class="text-rose-500">*</span>
                            </label>
                            @if(auth()->user()->isAdmin())
                                <a href="{{ route('admin.kategori.index') }}" target="_blank" class="text-[11px] text-bps-blue-600 hover:text-bps-blue-800 font-semibold flex items-center gap-1">
                                    <span>+ Kelola / Tambah Kategori</span>
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                </a>
                            @endif
                        </div>
                        <select name="kategori_id" required
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-medium focus:ring-2 focus:ring-bps-blue-500 focus:bg-white focus:outline-none transition">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($kategoriList as $kat)
                                <option value="{{ $kat->id }}" {{ old('kategori_id') == $kat->id ? 'selected' : '' }}>
                                    {{ $kat->nama_kategori }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Satuan Barang -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Satuan Barang <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="satuan" value="{{ old('satuan') }}" placeholder="Contoh: rim, buah, batang, dus, pak, botol" required
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-medium focus:ring-2 focus:ring-bps-blue-500 focus:bg-white focus:outline-none transition">
                    </div>

                    <!-- Stok Minimum -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Batas Stok Minimum <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" name="stok_minimum" value="{{ old('stok_minimum', 5) }}" min="0" required
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-bold text-slate-800 focus:ring-2 focus:ring-bps-blue-500 focus:bg-white focus:outline-none transition">
                        <span class="text-[10px] text-slate-400 mt-1 block">Peringatan otomatis muncul di dashboard jika stok &le; angka ini</span>
                    </div>

                    <!-- Stok Awal (Opsional) -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Saldo Stok Awal
                        </label>
                        <input type="number" name="stok_awal" value="{{ old('stok_awal', 0) }}" min="0"
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-bold text-slate-800 focus:ring-2 focus:ring-bps-blue-500 focus:bg-white focus:outline-none transition">
                        <span class="text-[10px] text-slate-400 mt-1 block">Otomatis dicatat ke kartu persediaan sebagai saldo awal</span>
                    </div>

                    <!-- Harga Satuan Estimasi -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Harga Satuan Estimasi (Rp)
                        </label>
                        <input type="number" step="any" name="harga_satuan_estimasi" value="{{ old('harga_satuan_estimasi', 0) }}" min="0"
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-mono focus:ring-2 focus:ring-bps-blue-500 focus:bg-white focus:outline-none transition">
                        <span class="text-[10px] text-slate-400 mt-1 block">Harga pembelian terakhir untuk penilaian SAKTI</span>
                    </div>

                    <!-- Lokasi Rak / Lemari -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Lokasi Rak / Lemari Gudang
                        </label>
                        <input type="text" name="lokasi_rak" value="{{ old('lokasi_rak') }}" placeholder="Contoh: Lemari A - Rak 2"
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-medium focus:ring-2 focus:ring-bps-blue-500 focus:bg-white focus:outline-none transition">
                    </div>

                    <!-- Keterangan Tambahan -->
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Keterangan / Spesifikasi Khusus
                        </label>
                        <textarea name="keterangan" rows="2" placeholder="Catatan tambahan mengenai merk, tipe, atau peruntukan survei..."
                                  class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-medium focus:ring-2 focus:ring-bps-blue-500 focus:bg-white focus:outline-none transition">{{ old('keterangan') }}</textarea>
                    </div>

                    <!-- Unggah Foto Barang -->
                    <x-foto-uploader
                        name="foto"
                        label="Foto Fisik Barang (Opsional)"
                        hint="Bisa pilih galeri, jepret kamera, drag & drop, atau tempel dari clipboard (Ctrl+V). Otomatis dikompres ke ukuran ringan (50–100 KB) di folder kategori."
                    />
                </div>

                <div class="pt-5 border-t border-slate-100 flex items-center justify-end gap-3">
                    <a href="{{ route('barang.index') }}" class="px-5 py-2.5 rounded-xl font-bold text-xs text-slate-600 hover:bg-slate-100 transition">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2.5 rounded-xl font-bold text-xs bg-emerald-600 hover:bg-emerald-700 text-white shadow-md shadow-emerald-600/20 hover:scale-[1.02] active:scale-[0.98] transition-all">
                        Simpan Barang ke Katalog
                    </button>
                </div>
            </form>
        </div>

    </div>
</x-app-layout>
