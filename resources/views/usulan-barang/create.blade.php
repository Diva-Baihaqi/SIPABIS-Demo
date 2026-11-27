<x-app-layout>
    <x-slot:title>Ajukan Usulan Pengadaan Barang</x-slot:title>
    <x-slot:header>Usulan Pengadaan Barang Baru</x-slot:header>

    <div class="max-w-3xl mx-auto space-y-6">
        
        <!-- Breadcrumb / Back button -->
        <div class="flex items-center justify-between">
            <a href="{{ route('usulan-barang.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-600 hover:text-slate-900 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Kembali ke Daftar Usulan</span>
            </a>
            <span class="text-[11px] font-mono text-slate-400">Pengadaan Persediaan BPS Satker 428646</span>
        </div>

        <!-- Main Form Card -->
        <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-8 shadow-sm">
            
            <div class="pb-5 border-b border-slate-100 flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Formulir Usulan Pengadaan Barang</h2>
                    <p class="text-xs text-slate-400 mt-1">
                        Sampaikan kebutuhan barang pakai habis atau perlengkapan operasional tim kerja yang belum tersedia di katalog.
                    </p>
                </div>
                <div class="w-10 h-10 rounded-2xl bg-bps-navy-50 text-bps-navy-700 border border-bps-navy-100 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-bps-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>

            <!-- Petunjuk & Ketentuan Pengusulan -->
            <div class="mt-5 p-4 rounded-2xl bg-gradient-to-r from-bps-blue-50/80 to-sky-50/80 border border-bps-blue-100 flex items-start gap-3.5">
                <div class="w-8 h-8 rounded-xl bg-bps-blue-600 text-white flex items-center justify-center shrink-0 shadow-sm mt-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="text-xs text-slate-700 leading-relaxed">
                    <span class="font-extrabold text-bps-navy-900 block mb-0.5">Ketentuan Telaah Subbagian Umum:</span>
                    Setiap usulan barang akan ditinjau oleh Operator Persediaan dan disetujui oleh Kepala Subbagian Umum BPS Kabupaten Cirebon untuk dialokasikan pada rencana anggaran belanja berikutnya.
                </div>
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
            <form action="{{ route('usulan-barang.store') }}" method="POST" enctype="multipart/form-data" class="mt-6 space-y-6">
                @csrf

                <!-- Nama Barang Usulan -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Nama Barang yang Diusulkan <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="nama_barang_usulan" value="{{ old('nama_barang_usulan') }}" 
                           placeholder="Contoh: Flashdisk Kingston 64GB USB 3.0, Map Snelhecter Biru, dll." required
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-medium focus:ring-2 focus:ring-bps-blue-500 focus:bg-white focus:outline-none transition">
                    <span class="text-[11px] text-slate-400 mt-1 block">Tuliskan nama barang beserta spesifikasi merk atau tipe jika diperlukan</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <!-- Perkiraan Satuan -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Satuan Barang <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="perkiraan_satuan" value="{{ old('perkiraan_satuan') }}" 
                               placeholder="Contoh: buah, rim, pak, dus, roll, botol" required
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-medium focus:ring-2 focus:ring-bps-blue-500 focus:bg-white focus:outline-none transition">
                    </div>

                    <!-- Estimasi Kebutuhan -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Estimasi Kebutuhan Unit <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" name="estimasi_kebutuhan" value="{{ old('estimasi_kebutuhan', 1) }}" min="1" required
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-bold text-slate-800 focus:ring-2 focus:ring-bps-blue-500 focus:bg-white focus:outline-none transition">
                        <span class="text-[11px] text-slate-400 mt-1 block">Jumlah unit yang dibutuhkan untuk kegiatan dinas</span>
                    </div>
                </div>

                <!-- Alasan Kebutuhan -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Alasan / Urgensi Kebutuhan <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="alasan_kebutuhan" rows="4" required
                              placeholder="Jelaskan untuk kegiatan atau survei apa barang ini dibutuhkan, urgensi pemakaian, atau dampak kendala jika barang tidak tersedia..."
                              class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-medium focus:ring-2 focus:ring-bps-blue-500 focus:bg-white focus:outline-none transition leading-relaxed">{{ old('alasan_kebutuhan') }}</textarea>
                    <span class="text-[11px] text-slate-400 mt-1 block">Deskripsi yang jelas akan mempermudah Kasubbag Umum mempertimbangkan alokasi anggaran</span>
                </div>

                <!-- Lampiran Foto Contoh Barang -->
                <x-foto-uploader
                    name="foto"
                    label="Foto Contoh Barang (Sangat Disarankan)"
                    hint="Lampirkan foto contoh barang lewat galeri, kamera, drag & drop, atau tempel gambar (Ctrl+V) agar Admin tahu persis barang yang dimaksud. Otomatis dikompres ke 50–100 KB."
                />

                <!-- Unit Kerja / Pengusul Info Card -->
                <div class="p-4 rounded-2xl bg-slate-50/90 border border-slate-200/90 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full overflow-hidden border border-slate-200/90 shadow-2xs shrink-0 bg-slate-100 flex items-center justify-center">
                            <img src="{{ Auth::user()->foto_url }}" alt="{{ Auth::user()->name }}" class="w-full h-full object-cover">
                        </div>
                        <div>
                            <span class="text-slate-400 text-[10px] uppercase font-bold block">Pegawai Pengusul</span>
                            <span class="font-extrabold text-slate-800 text-sm">{{ Auth::user()->name }}</span>
                            <span class="text-slate-400 font-mono text-[11px] block">NIP. {{ Auth::user()->nip }}</span>
                        </div>
                    </div>
                    <div class="sm:text-right border-t sm:border-t-0 pt-2 sm:pt-0 border-slate-200">
                        <span class="text-slate-400 text-[10px] uppercase font-bold block">Unit Kerja BPS</span>
                        <span class="font-bold text-bps-navy-900 text-xs">{{ Auth::user()->ruangan->nama_ruangan ?? 'BPS Kabupaten Cirebon' }}</span>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('usulan-barang.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-xs font-bold text-slate-700 hover:bg-slate-50 transition">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-bps-orange-500 hover:bg-bps-orange-600 text-white text-xs font-bold shadow-md shadow-bps-orange-500/25 hover:scale-[1.01] active:scale-[0.99] transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                        <span>Kirim Usulan Pengadaan</span>
                    </button>
                </div>
            </form>
        </div>

    </div>
</x-app-layout>
