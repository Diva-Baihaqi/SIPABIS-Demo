<x-app-layout>
    <x-slot:title>Inisiasi Sesi Stock Opname - SIPABIS BPS</x-slot:title>
    <x-slot:header>Inisiasi Sesi Stock Opname & Audit Fisik Persediaan</x-slot:header>

    <div class="max-w-4xl mx-auto space-y-6 pb-12" x-data="{
        periodeValue: '{{ old('periode', $defaultPeriode) }}',
        setPeriode(val) {
            this.periodeValue = val;
        }
    }">

        <!-- Top Breadcrumb & Satker Badge -->
        <div class="flex items-center justify-between">
            <a href="{{ route('stok-opname.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-600 hover:text-slate-900 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Kembali ke Riwayat Sesi Opname</span>
            </a>
            <span class="text-[11px] font-mono text-slate-500 font-bold bg-slate-100 px-3 py-1 rounded-full border border-slate-200">
                Satker 428646 • BPS Kabupaten Cirebon
            </span>
        </div>

        <!-- Banner Edukasi & Pengantar Konteks BPS -->
        <div class="bg-gradient-to-r from-bps-navy-800 via-bps-navy-700 to-slate-900 text-white rounded-3xl p-6 md:p-8 shadow-xl relative overflow-hidden">
            <div class="absolute -right-12 -bottom-12 w-64 h-64 bg-teal-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 space-y-3">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-teal-400/20 text-teal-300 border border-teal-400/30">
                        Modul Inventarisasi BMN & ATK
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-brand-lime/20 text-brand-lime border border-brand-lime/30">
                        Standar SAKTI Kemenkeu
                    </span>
                </div>

                <h1 class="text-xl sm:text-2xl font-black text-white tracking-tight">
                    Inisiasi Sesi Stock Opname & Audit Fisik Persediaan
                </h1>

                <p class="text-xs sm:text-sm text-slate-300 leading-relaxed max-w-3xl">
                    <strong>Stock Opname Persediaan</strong> adalah kegiatan pemeriksaan fisik berkala untuk mencocokkan saldo catatan buku persediaan (SIPABIS / SAKTI) dengan ketersediaan fisik riil di gudang kantor BPS Kabupaten Cirebon. Hasil audit ini akan dituangkan ke dalam <strong>Berita Acara Stock Opname (BASO)</strong> resmi sebagai dasar pelaporan neraca keuangan Satker.
                </p>
            </div>
        </div>

        <!-- Visual Stepper: 5 Tahapan Stock Opname BPS -->
        <div class="bg-white p-5 md:p-6 rounded-3xl border border-slate-200/90 shadow-sm space-y-4">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Alur Tahapan Audit Stock Opname</span>
                <span class="text-xs font-bold text-teal-700 bg-teal-50 px-2.5 py-0.5 rounded-full border border-teal-200">Tahap 1 dari 5</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-5 gap-3">
                <!-- Tahap 1 (Active) -->
                <div class="p-3.5 rounded-2xl bg-teal-50 border-2 border-teal-500 text-teal-900 relative">
                    <div class="flex items-center gap-2 mb-1.5">
                        <span class="w-5 h-5 rounded-full bg-teal-600 text-white font-extrabold text-[11px] flex items-center justify-center shrink-0">1</span>
                        <span class="text-xs font-black text-teal-950">Inisiasi Sesi</span>
                    </div>
                    <p class="text-[10px] text-teal-800 leading-tight">
                        <strong>Saat Ini:</strong> Membekukan snapshot saldo buku ke lembar kerja audit.
                    </p>
                </div>

                <!-- Tahap 2 -->
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 text-slate-600">
                    <div class="flex items-center gap-2 mb-1.5">
                        <span class="w-5 h-5 rounded-full bg-slate-200 text-slate-700 font-extrabold text-[11px] flex items-center justify-center shrink-0">2</span>
                        <span class="text-xs font-bold text-slate-800">Cek Fisik Rak</span>
                    </div>
                    <p class="text-[10px] text-slate-500 leading-tight">
                        Cetak lembar hitung & hitung fisik riil di rak gudang BPS.
                    </p>
                </div>

                <!-- Tahap 3 -->
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 text-slate-600">
                    <div class="flex items-center gap-2 mb-1.5">
                        <span class="w-5 h-5 rounded-full bg-slate-200 text-slate-700 font-extrabold text-[11px] flex items-center justify-center shrink-0">3</span>
                        <span class="text-xs font-bold text-slate-800">Input Selisih</span>
                    </div>
                    <p class="text-[10px] text-slate-500 leading-tight">
                        Input hasil hitung & catat kondisi rusak/usang/hilang.
                    </p>
                </div>

                <!-- Tahap 4 -->
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 text-slate-600">
                    <div class="flex items-center gap-2 mb-1.5">
                        <span class="w-5 h-5 rounded-full bg-slate-200 text-slate-700 font-extrabold text-[11px] flex items-center justify-center shrink-0">4</span>
                        <span class="text-xs font-bold text-slate-800">Finalisasi</span>
                    </div>
                    <p class="text-[10px] text-slate-500 leading-tight">
                        Penyesuaian stok otomatis pada master persediaan.
                    </p>
                </div>

                <!-- Tahap 5 -->
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 text-slate-600">
                    <div class="flex items-center gap-2 mb-1.5">
                        <span class="w-5 h-5 rounded-full bg-slate-200 text-slate-700 font-extrabold text-[11px] flex items-center justify-center shrink-0">5</span>
                        <span class="text-xs font-bold text-slate-800">Cetak BASO</span>
                    </div>
                    <p class="text-[10px] text-slate-500 leading-tight">
                        Cetak dokumen resmi & pengesahan Kepala BPS.
                    </p>
                </div>
            </div>
        </div>

        <!-- Audit Readiness: Ringkasan Kondisi Gudang Sebelum Opname -->
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    <span>Status Kesiapan Gudang Persediaan Saat Ini</span>
                </h3>
                <span class="text-[11px] text-slate-400">Data live master barang</span>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
                <div class="bg-white p-4 rounded-3xl border border-slate-200/90 shadow-sm">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Katalog Barang Aktif</span>
                    <span class="text-xl sm:text-2xl font-black text-slate-900 mt-0.5 block font-mono">{{ number_format($totalBarang) }}</span>
                    <span class="text-[11px] text-slate-500">Item siap diaudit</span>
                </div>

                <div class="bg-white p-4 rounded-3xl border border-slate-200/90 shadow-sm">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Total Fisik di Buku</span>
                    <span class="text-xl sm:text-2xl font-black text-teal-700 mt-0.5 block font-mono">{{ number_format($totalStokUnit) }}</span>
                    <span class="text-[11px] text-slate-500">Unit barang di rak</span>
                </div>

                <div class="bg-white p-4 rounded-3xl border border-slate-200/90 shadow-sm">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Estimasi Nilai Buku</span>
                    <span class="text-base sm:text-lg font-black text-slate-900 mt-1 block font-mono">Rp {{ number_format($totalNilaiBuku, 0, ',', '.') }}</span>
                    <span class="text-[11px] text-slate-500">Saldo neraca sebelum opname</span>
                </div>

                <div class="bg-white p-4 rounded-3xl border border-slate-200/90 shadow-sm">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Permintaan Berjalan</span>
                    @if($pendingPermintaanCount > 0)
                        <span class="text-xl sm:text-2xl font-black text-amber-600 mt-0.5 block font-mono">{{ $pendingPermintaanCount }}</span>
                        <span class="text-[11px] text-amber-700 font-semibold">Perlu serah terima</span>
                    @else
                        <span class="text-xl sm:text-2xl font-black text-emerald-600 mt-0.5 block font-mono">0</span>
                        <span class="text-[11px] text-emerald-700 font-semibold">Gudang sinkron</span>
                    @endif
                </div>
            </div>

            @if($pendingPermintaanCount > 0)
                <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs flex items-start gap-3">
                    <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <div>
                        <strong class="font-bold">Tips Akurasi Audit Fisik Gudang:</strong>
                        <p class="text-[11px] text-amber-800 leading-relaxed mt-0.5">
                            Terdapat <strong>{{ $pendingPermintaanCount }} pengajuan formulir permintaan ATK</strong> yang belum diselesaikan serah terimanya. Pastikan barang yang sudah diambil oleh ruangan/seksi telah dicatat <em>Serah Terima</em> di SIPABIS, agar barang fisik di rak gudang tidak menimbulkan selisih semu saat dihitung.
                        </p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Alert Jika Ada Sesi Draft Yang Sedang Aktif -->
        @if($activeDraft)
            <div class="p-5 rounded-3xl bg-rose-50 border border-rose-200 text-rose-950 flex items-start justify-between gap-4">
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-2xl bg-rose-500 text-white flex items-center justify-center shrink-0 font-bold">
                        !
                    </div>
                    <div>
                        <h4 class="text-sm font-extrabold text-rose-950">Terdapat Sesi Opname Draft yang Masih Aktif</h4>
                        <p class="text-xs text-rose-800 mt-1">
                            Sesi <strong>{{ $activeDraft->nomor_opname }}</strong> (Periode {{ $activeDraft->periode }}) saat ini masih berstatus <em>Draft</em>. Sistem mengunci pembukaan sesi baru untuk mencegah kerancuan ganda. Anda dapat melanjutkan sesi tersebut atau menghapusnya jika terjadi kesalahan.
                        </p>
                    </div>
                </div>
                <div class="shrink-0 flex items-center gap-2">
                    <a href="{{ route('stok-opname.show', $activeDraft->id) }}" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-sm transition">
                        Buka Sesi Berjalan &rarr;
                    </a>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs">
                <div class="font-bold mb-1">Periksa kembali isian form:</div>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form Card Utama -->
        <form action="{{ route('stok-opname.store') }}" method="POST" class="bg-white p-6 md:p-8 rounded-3xl border border-slate-200/90 shadow-sm space-y-6">
            @csrf

            <div class="border-b border-slate-100 pb-4">
                <h3 class="text-base font-extrabold text-slate-900">Parameter Inisiasi Sesi Opname</h3>
                <p class="text-xs text-slate-500 mt-0.5">Pilih siklus audit resmi atau tentukan periode pembukuan Berita Acara (BASO).</p>
            </div>

            <!-- Bagian 1: Pilihan Cepat Periode Standar BPS (Quick Presets) -->
            <div class="space-y-2.5">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                    Pilihan Cepat Siklus Audit BPS <span class="text-slate-400 font-normal normal-case">(Klik salah satu untuk mengisi otomatis)</span>
                </label>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                    @foreach($presets as $p)
                        <button type="button" 
                                @click="setPeriode('{{ $p['val'] }}')"
                                :class="periodeValue === '{{ $p['val'] }}' ? 'bg-teal-50 border-teal-500 text-teal-950 ring-2 ring-teal-500/20' : 'bg-slate-50 border-slate-200 hover:bg-slate-100/80 text-slate-700'"
                                class="p-3 rounded-2xl border text-left transition flex items-start justify-between gap-3 cursor-pointer">
                            <div>
                                <div class="font-extrabold text-xs" :class="periodeValue === '{{ $p['val'] }}' ? 'text-teal-900' : 'text-slate-800'">
                                    {{ $p['label'] }}
                                </div>
                                <div class="text-[10px] text-slate-500 mt-0.5">{{ $p['sub'] }}</div>
                            </div>
                            <span x-show="periodeValue === '{{ $p['val'] }}'" class="w-4 h-4 rounded-full bg-teal-600 text-white flex items-center justify-center text-[10px] shrink-0 font-bold">
                                ✓
                            </span>
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Bagian 2: Input Field Periode & Tanggal Pelaksanaan -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                <div>
                    <label for="periode" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Nama Periode Opname <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           id="periode" 
                           name="periode" 
                           x-model="periodeValue"
                           required
                           placeholder="Contoh: Semester II 2026 atau November 2026"
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-bold text-slate-800 focus:ring-2 focus:ring-teal-500 focus:bg-white transition">
                    <span class="text-[11px] text-slate-400 mt-1 block">Teks ini akan tertera pada judul resmi Berita Acara Stock Opname (BASO).</span>
                </div>

                <div>
                    <label for="tanggal_opname" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Tanggal Pelaksanaan Perhitungan Fisik <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" 
                           id="tanggal_opname" 
                           name="tanggal_opname" 
                           value="{{ old('tanggal_opname', date('Y-m-d')) }}" 
                           required
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-bold text-slate-800 focus:ring-2 focus:ring-teal-500 focus:bg-white transition">
                    <span class="text-[11px] text-slate-400 mt-1 block">Tanggal fisik barang dihitung bersama tim pemeriksa.</span>
                </div>
            </div>

            <!-- Penjelasan Transparan Mekanisme Keamanan Saldo -->
            <div class="p-4 rounded-2xl bg-teal-50/70 border border-teal-200/80 text-teal-950 text-xs flex items-start gap-3">
                <div class="w-7 h-7 rounded-xl bg-teal-600 text-white flex items-center justify-center shrink-0 mt-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
                <div class="space-y-1">
                    <strong class="font-extrabold text-teal-900 block">Jaminan Keamanan Saldo Gudang:</strong>
                    <p class="text-[11px] text-teal-800 leading-relaxed">
                        Membuka sesi ini <strong>TIDAK AKAN memotong atau mengubah saldo stok aktif</strong> saat ini. Sistem hanya menduplikasi catatan buku ke lembar kerja audit fisik. Saldo riil hanya akan disesuaikan setelah Anda selesai memasukkan hasil hitung fisik dan menekan tombol <em>Finalisasi</em>. Jika salah buka, sesi dapat dibatalkan atau dihapus kapan saja.
                    </p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('stok-opname.index') }}" class="px-5 py-2.5 rounded-full border border-slate-300 text-slate-700 text-xs font-bold hover:bg-slate-50 transition">
                    Batal
                </a>
                <button type="submit" class="inline-flex items-center gap-2 px-7 py-2.5 rounded-full bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-md transition transform hover:scale-105 cursor-pointer">
                    <svg class="w-4 h-4 text-brand-lime" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    <span>Mulai Sesi Opname & Buat Lembar Kerja Fisik</span>
                </button>
            </div>
        </form>

        <!-- Pertanyaan yang Sering Diajukan (FAQ & Bantuan Petugas BPS) -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200/90 shadow-sm space-y-4">
            <h4 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Panduan & Tanya Jawab Pengelolaan Stock Opname</span>
            </h4>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs text-slate-600">
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 space-y-1.5">
                    <strong class="font-bold text-slate-900 block">Kapan Stock Opname Dilakukan?</strong>
                    <p class="text-[11px] text-slate-500 leading-relaxed">
                        Sesuai pedoman akuntansi BMN, audit wajib dilakukan minimal <strong>tiap semester</strong> (Semester I per 30 Juni dan Semester II per 31 Desember) untuk tutup buku neraca keuangan.
                    </p>
                </div>

                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 space-y-1.5">
                    <strong class="font-bold text-slate-900 block">Bagaimana Jika Ada Salah Buka?</strong>
                    <p class="text-[11px] text-slate-500 leading-relaxed">
                        Sesi yang berstatus <em>Draft</em> dapat langsung dihapus melalui tombol ikon tempat sampah di daftar riwayat, atau tombol <em>Hapus Sesi Opname</em> di lembar kerja.
                    </p>
                </div>

                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 space-y-1.5">
                    <strong class="font-bold text-slate-900 block">Siapa yang Mengesahkan BASO?</strong>
                    <p class="text-[11px] text-slate-500 leading-relaxed">
                        Berita Acara ditandatangani oleh <strong>Kepala BPS Kabupaten Cirebon</strong> dan <strong>Pengurus / Penyimpan Barang</strong> setelah perhitungan fisik selesai difinalisasi.
                    </p>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
