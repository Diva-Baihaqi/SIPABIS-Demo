<x-app-layout>
    <x-slot:title>Edit Barang Persediaan</x-slot:title>
    <x-slot:header>Pembaruan Data Master Barang</x-slot:header>

    <div class="max-w-4xl mx-auto space-y-6">
        
        <!-- Header Card -->
        <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-7 shadow-sm">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div>
                    <h2 class="text-lg font-black text-slate-900">Perbarui Informasi Barang Persediaan</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Edit detail master untuk barang: <strong class="text-slate-800">{{ $barang->nama_barang }}</strong></p>
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
            <form action="{{ route('barang.update', $barang->id) }}" method="POST" enctype="multipart/form-data" class="mt-6 space-y-5">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <!-- Kode Barang BPS -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Kode Barang <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="kode_barang" value="{{ old('kode_barang', $barang->kode_barang) }}" required
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-mono focus:ring-2 focus:ring-bps-blue-500 focus:bg-white focus:outline-none transition">
                    </div>

                    <!-- Kode SAKTI BMN -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Kode SAKTI (Opsional)
                        </label>
                        <input type="text" name="kode_sakti" value="{{ old('kode_sakti', $barang->kode_sakti) }}"
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-mono focus:ring-2 focus:ring-bps-blue-500 focus:bg-white focus:outline-none transition">
                    </div>

                    <!-- Nama Barang -->
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Nama Barang Persediaan <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="nama_barang" value="{{ old('nama_barang', $barang->nama_barang) }}" required
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
                            @foreach($kategoriList as $kat)
                                <option value="{{ $kat->id }}" {{ old('kategori_id', $barang->kategori_id) == $kat->id ? 'selected' : '' }}>
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
                        <input type="text" name="satuan" value="{{ old('satuan', $barang->satuan) }}" required
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-medium focus:ring-2 focus:ring-bps-blue-500 focus:bg-white focus:outline-none transition">
                    </div>

                    <!-- Stok Minimum -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Batas Stok Minimum <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" name="stok_minimum" value="{{ old('stok_minimum', $barang->stok_minimum) }}" min="0" required
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-bold text-slate-800 focus:ring-2 focus:ring-bps-blue-500 focus:bg-white focus:outline-none transition">
                    </div>

                    <!-- Stok Aktif (Info Readonly) -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">
                            Stok Aktif Fisik (Read-Only)
                        </label>
                        <div class="px-3.5 py-2.5 bg-slate-100 border border-slate-200 rounded-xl text-xs sm:text-sm font-black text-slate-700 flex items-center justify-between">
                            <span>{{ number_format($barang->stok_aktif) }} {{ $barang->satuan }}</span>
                            <span class="text-[10px] text-slate-400 font-normal">Mutasi via Barang Masuk / Opname</span>
                        </div>
                    </div>

                    <!-- Harga Satuan Estimasi -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Harga Satuan Estimasi (Rp)
                        </label>
                        <input type="number" step="any" name="harga_satuan_estimasi" value="{{ old('harga_satuan_estimasi', $barang->harga_satuan_estimasi) }}" min="0"
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-mono focus:ring-2 focus:ring-bps-blue-500 focus:bg-white focus:outline-none transition">
                    </div>

                    <!-- Lokasi Rak / Lemari -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Lokasi Rak / Lemari Gudang
                        </label>
                        <input type="text" name="lokasi_rak" value="{{ old('lokasi_rak', $barang->lokasi_rak) }}"
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-medium focus:ring-2 focus:ring-bps-blue-500 focus:bg-white focus:outline-none transition">
                    </div>

                    <!-- Keterangan Tambahan -->
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Keterangan / Spesifikasi Khusus
                        </label>
                        <textarea name="keterangan" rows="2"
                                  class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-medium focus:ring-2 focus:ring-bps-blue-500 focus:bg-white focus:outline-none transition">{{ old('keterangan', $barang->keterangan) }}</textarea>
                    </div>

                    <!-- Ganti / Hapus Foto Barang -->
                    <x-foto-uploader
                        name="foto"
                        label="Foto Fisik Barang"
                        :currentUrl="$barang->foto_url"
                        :hasExisting="$barang->has_real_foto"
                        :allowRemove="true"
                        hint="Pilih dari galeri, jepret kamera, drag & drop ke kotak ini, atau tekan Ctrl+V untuk mengganti foto lama (foto lama otomatis terhapus permanen)."
                    />
                </div>

                <div class="pt-5 border-t border-slate-100 flex items-center justify-end gap-3">
                    <a href="{{ route('barang.index') }}" class="px-5 py-2.5 rounded-xl font-bold text-xs text-slate-600 hover:bg-slate-100 transition">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2.5 rounded-xl font-bold text-xs bg-bps-blue-600 hover:bg-bps-blue-700 text-white shadow-md shadow-bps-blue-600/20 hover:scale-[1.02] active:scale-[0.98] transition-all">
                        Perbarui Data Barang
                    </button>
                </div>
            </form>
        </div>

    </div>
</x-app-layout>
