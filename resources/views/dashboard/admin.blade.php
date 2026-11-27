<x-app-layout>
    <x-slot:title>Dashboard Administrator</x-slot:title>
    <x-slot:header>Pengelolaan Persediaan & Operasional Logistik</x-slot:header>

    <div class="space-y-7">
        
        <!-- Welcome Hero Banner -->
        <div class="relative overflow-hidden bg-gradient-to-r from-bps-navy-800 via-bps-navy-700 to-bps-navy-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl shadow-bps-navy-950/10 border border-bps-navy-700/80">
            <div class="absolute -right-10 -bottom-10 w-72 h-72 bg-bps-orange-500/15 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute right-1/3 -top-10 w-60 h-60 bg-bps-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>
            
            <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <div class="space-y-2">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-bps-orange-300 text-xs font-semibold backdrop-blur-sm border border-white/10">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>Subbagian Umum • Satker 428646 BPS Kab. Cirebon</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white">Selamat Datang, {{ auth()->user()->name }}</h1>
                    <p class="text-xs sm:text-sm text-slate-300 max-w-2xl leading-relaxed">
                        Kelola stok ATK kantor, periksa permintaan barang dari rekan kerja, serta pastikan perlengkapan kerja pegawai selalu siap digunakan.
                    </p>
                </div>

                <!-- Quick Action Buttons -->
                <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                    <a href="{{ route('admin.barang-masuk.create') }}" class="px-4 py-2.5 rounded-xl font-bold text-xs bg-emerald-600 hover:bg-emerald-700 text-white shadow-md shadow-emerald-600/20 hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Catat Barang Masuk</span>
                    </a>
                    <a href="{{ route('admin.permintaan.index') }}" class="px-4 py-2.5 rounded-xl font-bold text-xs bg-bps-orange-500 hover:bg-bps-orange-600 text-white shadow-md shadow-bps-orange-500/20 hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        <span>Periksa Permintaan</span>
                    </a>
                    <a href="{{ route('stok-opname.index') }}" class="px-4 py-2.5 rounded-xl font-bold text-xs bg-white/15 hover:bg-white/25 text-white border border-white/20 hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Cek Fisik Stok (Opname)</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Metric Cards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <!-- Total Katalog -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition duration-200">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Katalog BPS</span>
                    <div class="w-10 h-10 rounded-xl bg-bps-blue-50 text-bps-blue-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-black text-slate-800">{{ number_format($totalBarang) }}</span>
                    <span class="text-xs text-slate-500 font-medium">Jenis ATK</span>
                </div>
                <p class="text-[11px] text-slate-400 mt-2 flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    <span>Kamus standar persediaan BPS</span>
                </p>
            </div>

            <!-- Stok Menipis Warning -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition duration-200">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Stok Hampir Habis</span>
                    <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-black {{ $stokMenipis > 0 ? 'text-rose-600' : 'text-slate-800' }}">{{ number_format($stokMenipis) }}</span>
                    <span class="text-xs text-rose-500 font-semibold">Perlu Dibeli Lagi</span>
                </div>
                <p class="text-[11px] text-slate-400 mt-2 flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full {{ $stokMenipis > 0 ? 'bg-rose-500' : 'bg-emerald-500' }}"></span>
                    <span>{{ $stokMenipis > 0 ? 'Stok di gudang sudah menipis' : 'Stok semua barang aman' }}</span>
                </p>
            </div>

            <!-- Antrean Permintaan -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition duration-200">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Permintaan Baru</span>
                    <div class="w-10 h-10 rounded-xl bg-bps-orange-50 text-bps-orange-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-black text-bps-orange-600">{{ number_format($permintaanMenunggu) }}</span>
                    <span class="text-xs text-slate-500 font-medium">Pengajuan</span>
                </div>
                <p class="text-[11px] text-slate-400 mt-2 flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full {{ $permintaanMenunggu > 0 ? 'bg-amber-500' : 'bg-slate-400' }}"></span>
                    <span>Menunggu persetujuan Admin</span>
                </p>
            </div>

            <!-- Mutasi Bulan Ini -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition duration-200">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Keluar-Masuk Bulan Ini</span>
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div>
                        <span class="text-xs text-slate-400 block font-medium">Masuk</span>
                        <span class="text-xl font-black text-emerald-600">+{{ number_format($masukBulanIni) }}</span>
                    </div>
                    <div class="w-px h-8 bg-slate-200"></div>
                    <div>
                        <span class="text-xs text-slate-400 block font-medium">Keluar</span>
                        <span class="text-xl font-black text-bps-orange-600">-{{ number_format($keluarBulanIni) }}</span>
                    </div>
                </div>
                <p class="text-[11px] text-slate-400 mt-2 flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-bps-blue-500"></span>
                    <span>Periode {{ now()->translatedFormat('F Y') }}</span>
                </p>
            </div>
        </div>

        <!-- Interactive Charts Grid (2 Columns) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-7">
            
            <!-- Chart 1: Tren Sirkulasi Barang (6 Bulan Terakhir) (8 Kolom) -->
            <div class="lg:col-span-7 bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-7 shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100 mb-5">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-bps-blue-500"></span>
                            <h3 class="font-extrabold text-slate-900 text-sm sm:text-base">Tren Sirkulasi Persediaan (6 Bulan Terakhir)</h3>
                        </div>
                        <p class="text-xs text-slate-400 mt-0.5">Komparasi kuantitas barang masuk (pengadaan) vs barang keluar (serah terima ATK)</p>
                    </div>
                    <div class="flex items-center gap-3 self-start sm:self-center">
                        <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-600">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Masuk
                        </span>
                        <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-bps-orange-500">
                            <span class="w-2.5 h-2.5 rounded-full bg-bps-orange-500"></span> Keluar
                        </span>
                    </div>
                </div>

                <div class="h-72 w-full relative">
                    <canvas id="chartSirkulasiAdmin"></canvas>
                </div>
            </div>

            <!-- Chart 2: Komposisi Persediaan per Kategori (5 Kolom) -->
            <div class="lg:col-span-5 bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-7 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-2 pb-4 border-b border-slate-100 mb-4">
                        <span class="w-2.5 h-2.5 rounded-full bg-bps-orange-500"></span>
                        <div>
                            <h3 class="font-extrabold text-slate-900 text-sm sm:text-base">Distribusi Kategori Barang</h3>
                            <p class="text-xs text-slate-400 mt-0.5">Proporsi jumlah item persediaan berdasarkan kategori</p>
                        </div>
                    </div>

                    <div class="h-60 w-full relative my-2">
                        <canvas id="chartKategoriAdmin"></canvas>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100">
                    <div class="grid grid-cols-2 gap-2 text-[11px]">
                        @foreach($kategoriDistribution->take(4) as $kat)
                            <div class="flex items-center justify-between p-1.5 rounded-lg bg-slate-50">
                                <span class="text-slate-600 font-medium truncate max-w-[110px]" title="{{ $kat['nama'] }}">{{ $kat['nama'] }}</span>
                                <span class="font-bold text-slate-800">{{ $kat['total_barang'] }} item</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>

        <!-- Chart 3: Penyerapan Persediaan per Ruangan/Fungsi BPS (Full Width) -->
        <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-7 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100 mb-5">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                        <h3 class="font-extrabold text-slate-900 text-sm sm:text-base">Volume Penyerapan Logistik per Unit Kerja BPS</h3>
                    </div>
                    <p class="text-xs text-slate-400 mt-0.5">Total unit barang yang telah diserahkan ke 7 Tim Fungsi Kerja BPS Kabupaten Cirebon</p>
                </div>
                <a href="{{ route('kartu-kendali.index') }}" class="text-xs font-bold text-bps-blue-600 hover:text-bps-orange-500 transition flex items-center gap-1">
                    <span>Lihat Buku Kendali Ruangan</span>
                    <span>&rarr;</span>
                </a>
            </div>

            <div class="h-64 w-full relative">
                <canvas id="chartRuanganAdmin"></canvas>
            </div>
        </div>

        <!-- Operational Lists (2 Columns: Antrean & Stok Menipis) -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-7">
            <!-- Permintaan Terbaru -->
            <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-sm">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                    <div>
                        <h3 class="font-extrabold text-slate-900 text-sm">Permintaan ATK Terkini</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Antrean pengajuan kebutuhan dari unit kerja</p>
                    </div>
                    <a href="{{ route('admin.permintaan.index') }}" class="text-xs font-bold text-bps-blue-600 hover:text-bps-orange-500 transition">Lihat Semua &rarr;</a>
                </div>

                @if($permintaanTerbaru->isEmpty())
                    <div class="text-center py-12">
                        <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                        </div>
                        <p class="text-xs font-semibold text-slate-600">Belum ada antrean permintaan baru</p>
                        <p class="text-[11px] text-slate-400 mt-0.5">Seluruh permohonan telah selesai diproses.</p>
                    </div>
                @else
                    <div class="divide-y divide-slate-100">
                        @foreach($permintaanTerbaru as $req)
                            <div class="py-3 flex items-center justify-between gap-3 hover:bg-slate-50 px-2 rounded-xl transition">
                                <div class="space-y-0.5 min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-bold text-slate-800 font-mono">{{ $req->nomor_permintaan }}</span>
                                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded-md 
                                            @if($req->status === 'diajukan') bg-amber-100 text-amber-800
                                            @elseif($req->status === 'disetujui') bg-blue-100 text-blue-800
                                            @elseif($req->status === 'selesai') bg-emerald-100 text-emerald-800
                                            @else bg-slate-100 text-slate-600 @endif">
                                            {{ $req->status }}
                                        </span>
                                    </div>
                                    <p class="text-[11px] text-slate-500 truncate">{{ $req->user->name }} • {{ $req->ruangan?->nama_ruangan }}</p>
                                </div>
                                <div class="text-right shrink-0">
                                    <span class="text-xs font-mono text-slate-400 block">{{ $req->tanggal_pengajuan->format('d/m/Y') }}</span>
                                    <a href="{{ route('admin.permintaan.show', $req->id) }}" class="text-[11px] font-semibold text-bps-blue-600 hover:underline">Periksa &rarr;</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Barang Stok Minimum -->
            <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-sm">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                    <div>
                        <h3 class="font-extrabold text-slate-900 text-sm">Barang yang Hampir Habis</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Daftar ATK yang stok fisiknya tinggal sedikit dan perlu segera dipesan</p>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">{{ $stokMenipis }} Barang Menipis</span>
                </div>

                @if($barangMenipisList->isEmpty())
                    <div class="text-center py-12">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-500 flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <p class="text-xs font-semibold text-slate-600">Seluruh persediaan dalam kondisi aman</p>
                        <p class="text-[11px] text-slate-400 mt-0.5">Tidak ada item di bawah batas stok minimum.</p>
                    </div>
                @else
                    <div class="divide-y divide-slate-100">
                        @foreach($barangMenipisList as $b)
                            @php
                                $percentage = $b->stok_minimum > 0 ? min(100, round(($b->stok_aktif / $b->stok_minimum) * 100)) : 0;
                            @endphp
                            <div class="py-3 px-2 rounded-xl hover:bg-slate-50 transition space-y-1.5">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="text-xs font-bold text-slate-800 truncate">{{ $b->nama_barang }}</p>
                                        <p class="text-[11px] text-slate-500">{{ $b->kategori?->nama_kategori }} • {{ $b->satuan }}</p>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <span class="text-xs font-black text-rose-600">{{ $b->stok_aktif }}</span>
                                        <span class="text-[10px] text-slate-400 block font-medium">Min: {{ $b->stok_minimum }}</span>
                                    </div>
                                </div>
                                <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                                    <div class="bg-rose-500 h-full rounded-full" style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

    </div>

    <!-- Script Chart.js Initialization for Admin Dashboard -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof Chart === 'undefined') return;

            // 1. Chart Tren Sirkulasi 6 Bulan Terakhir
            const ctxSirkulasi = document.getElementById('chartSirkulasiAdmin');
            if (ctxSirkulasi) {
                const gradientMasuk = ctxSirkulasi.getContext('2d').createLinearGradient(0, 0, 0, 280);
                gradientMasuk.addColorStop(0, 'rgba(16, 185, 129, 0.28)');
                gradientMasuk.addColorStop(1, 'rgba(16, 185, 129, 0.00)');

                const gradientKeluar = ctxSirkulasi.getContext('2d').createLinearGradient(0, 0, 0, 280);
                gradientKeluar.addColorStop(0, 'rgba(247, 148, 29, 0.28)');
                gradientKeluar.addColorStop(1, 'rgba(247, 148, 29, 0.00)');

                new Chart(ctxSirkulasi, {
                    type: 'line',
                    data: {
                        labels: @json($bulanLabels),
                        datasets: [
                            {
                                label: 'Barang Masuk',
                                data: @json($masukData),
                                borderColor: '#10B981',
                                backgroundColor: gradientMasuk,
                                fill: true,
                                tension: 0.35,
                                borderWidth: 2.5,
                                pointBackgroundColor: '#10B981',
                                pointRadius: 4,
                                pointHoverRadius: 6,
                            },
                            {
                                label: 'Barang Keluar',
                                data: @json($keluarData),
                                borderColor: '#F7941D',
                                backgroundColor: gradientKeluar,
                                fill: true,
                                tension: 0.35,
                                borderWidth: 2.5,
                                pointBackgroundColor: '#F7941D',
                                pointRadius: 4,
                                pointHoverRadius: 6,
                            }
                        ]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: { color: 'rgba(226, 232, 240, 0.6)' },
                                ticks: { precision: 0 }
                            },
                            x: {
                                grid: { display: false }
                            }
                        }
                    }
                });
            }

            // 2. Chart Distribusi Kategori
            const ctxKategori = document.getElementById('chartKategoriAdmin');
            if (ctxKategori) {
                const kategoriLabels = @json($kategoriDistribution->pluck('nama'));
                const kategoriTotals = @json($kategoriDistribution->pluck('total_barang'));

                new Chart(ctxKategori, {
                    type: 'doughnut',
                    data: {
                        labels: kategoriLabels,
                        datasets: [{
                            data: kategoriTotals,
                            backgroundColor: [
                                '#002B49', '#0090D4', '#F7941D', '#10B981',
                                '#6366F1', '#EC4899', '#8B5CF6', '#F59E0B'
                            ],
                            borderWidth: 2,
                            borderColor: '#ffffff',
                            hoverOffset: 6
                        }]
                    },
                    options: {
                        cutout: '68%',
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: { boxWidth: 10, font: { size: 10 } }
                            }
                        }
                    }
                });
            }

            // 3. Chart Penyerapan per Ruangan BPS
            const ctxRuangan = document.getElementById('chartRuanganAdmin');
            if (ctxRuangan) {
                const ruanganLabels = @json($ruanganDistribution->pluck('nama'));
                const ruanganTotals = @json($ruanganDistribution->pluck('total_diserahkan'));

                new Chart(ctxRuangan, {
                    type: 'bar',
                    data: {
                        labels: ruanganLabels,
                        datasets: [{
                            label: 'Volume Barang Diserahkan',
                            data: ruanganTotals,
                            backgroundColor: '#0090D4',
                            borderRadius: 8,
                            hoverBackgroundColor: '#002B49',
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        scales: {
                            x: {
                                beginAtZero: true,
                                grid: { color: 'rgba(226, 232, 240, 0.6)' },
                                ticks: { precision: 0 }
                            },
                            y: {
                                grid: { display: false }
                            }
                        },
                        plugins: {
                            legend: { display: false }
                        }
                    }
                });
            }
        });
    </script>
</x-app-layout>
