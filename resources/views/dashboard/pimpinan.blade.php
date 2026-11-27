<x-app-layout>
    <x-slot:title>Dashboard Pimpinan</x-slot:title>
    <x-slot:header>Ringkasan Eksekutif Persediaan Aset BPS</x-slot:header>

    <div class="space-y-7">
        
        <!-- Welcome Hero Banner -->
        <div class="relative overflow-hidden bg-gradient-to-r from-emerald-900 via-teal-800 to-bps-navy-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl shadow-emerald-950/20 border border-emerald-700/40">
            <div class="absolute -right-10 -bottom-10 w-72 h-72 bg-emerald-400/15 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute right-1/3 -top-10 w-60 h-60 bg-bps-blue-500/15 rounded-full blur-2xl pointer-events-none"></div>

            <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <div class="space-y-2">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-emerald-200 text-xs font-semibold backdrop-blur-sm border border-white/10">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>Hak Akses Eksekutif: Kepala BPS Kabupaten Cirebon</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white">Selamat Datang, {{ auth()->user()->name }}</h1>
                    <p class="text-xs sm:text-sm text-slate-200 max-w-2xl leading-relaxed">
                        Pantau transparansi distribusi logistik persediaan pada 7 fungsi teknis, evaluasi akurasi audit fisik gudang, dan lakukan pengesahan Berita Acara Stock Opname (BASO) secara digital.
                    </p>
                </div>

                <!-- Executive Quick Action -->
                <div class="flex items-center gap-3 shrink-0">
                    @if($opnameMenungguPengesahan > 0)
                        <a href="{{ route('stok-opname.index') }}" class="px-5 py-3 rounded-xl font-bold text-xs bg-amber-500 hover:bg-amber-600 text-slate-900 shadow-lg shadow-amber-500/25 hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-slate-900 animate-ping"></span>
                            <span>{{ $opnameMenungguPengesahan }} BASO Perlu Disahkan</span>
                        </a>
                    @else
                        <div class="p-4 rounded-2xl bg-white/10 border border-white/20 text-center">
                            <span class="text-[10px] text-emerald-200 uppercase tracking-widest font-bold block">Status Dokumen</span>
                            <span class="text-sm font-black text-white mt-0.5 block flex items-center justify-center gap-1.5">
                                <svg class="w-4 h-4 text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                Seluruh BASO Telah Disahkan
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Metric Cards (4 Kolom) -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
            <!-- Katalog Aktif -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Katalog Aktif</span>
                <span class="text-3xl font-black text-slate-800 mt-1 block">{{ number_format($totalBarang) }}</span>
                <span class="text-[11px] text-slate-500 flex items-center gap-1.5 mt-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-bps-blue-500"></span>
                    8 Kategori persediaan
                </span>
            </div>

            <!-- Permintaan Selesai -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition">
                <span class="text-xs font-bold text-emerald-600 uppercase tracking-wider block">Permintaan Tuntas</span>
                <span class="text-3xl font-black text-emerald-600 mt-1 block">{{ number_format($permintaanSelesai) }}</span>
                <span class="text-[11px] text-slate-500 flex items-center gap-1.5 mt-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    Dari {{ number_format($totalPermintaan) }} pengajuan
                </span>
            </div>

            <!-- Tingkat Akurasi Opname -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition">
                <span class="text-xs font-bold text-teal-600 uppercase tracking-wider block">Akurasi Fisik Gudang</span>
                <div class="flex items-baseline gap-1 mt-1">
                    <span class="text-3xl font-black text-teal-600">{{ $akurasiOpname }}</span>
                    <span class="text-sm font-bold text-teal-500">%</span>
                </div>
                <span class="text-[11px] text-slate-500 flex items-center gap-1.5 mt-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-teal-500"></span>
                    Hasil opname terakhir
                </span>
            </div>

            <!-- Dokumen BASO -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition">
                <span class="text-xs font-bold text-bps-orange-600 uppercase tracking-wider block">Antrean Pengesahan</span>
                <span class="text-3xl font-black text-bps-orange-600 mt-1 block">{{ number_format($opnameMenungguPengesahan) }}</span>
                <span class="text-[11px] text-slate-500 flex items-center gap-1.5 mt-1">
                    <span class="w-1.5 h-1.5 rounded-full {{ $opnameMenungguPengesahan > 0 ? 'bg-amber-500' : 'bg-emerald-500' }}"></span>
                    {{ $opnameMenungguPengesahan > 0 ? 'Menunggu tanda tangan' : 'Tertib administrasi' }}
                </span>
            </div>
        </div>

        <!-- Interactive Charts Grid (2 Columns) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-7">
            
            <!-- Chart 1: Komparasi Penyerapan Persediaan Antar 7 Fungsi BPS (7 Kolom) -->
            <div class="lg:col-span-7 bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-7 shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100 mb-5">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
                            <h3 class="font-extrabold text-slate-900 text-sm sm:text-base">Distribusi Serapan Logistik Antar 7 Fungsi</h3>
                        </div>
                        <p class="text-xs text-slate-400 mt-0.5">Transparansi volume barang persediaan yang diserap oleh masing-masing unit kerja</p>
                    </div>
                    <a href="{{ route('kartu-kendali.index') }}" class="text-xs font-bold text-teal-700 hover:text-bps-orange-500 transition">
                        Buku Kendali &rarr;
                    </a>
                </div>

                <div class="h-64 w-full relative">
                    <canvas id="chartSerapanPimpinan"></canvas>
                </div>
            </div>

            <!-- Chart 2: Akurasi Stock Opname Fisik vs Sistem (5 Kolom) -->
            <div class="lg:col-span-5 bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-7 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-2 pb-4 border-b border-slate-100 mb-4">
                        <span class="w-2.5 h-2.5 rounded-full bg-teal-500"></span>
                        <div>
                            <h3 class="font-extrabold text-slate-900 text-sm sm:text-base">Indeks Akurasi Stock Opname</h3>
                            <p class="text-xs text-slate-400 mt-0.5">Kesesuaian fisik riil gudang terhadap saldo sistem</p>
                        </div>
                    </div>

                    <div class="h-56 w-full relative my-2">
                        <canvas id="chartAkurasiPimpinan"></canvas>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                        <span class="text-slate-600 font-medium">Cocok: <strong>{{ $totalCocokOpname }}</strong> item</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                        <span class="text-slate-600 font-medium">Deviasi: <strong>{{ $totalSelisihOpname }}</strong> item</span>
                    </div>
                    <span class="px-2 py-0.5 rounded-md bg-teal-50 text-teal-800 font-bold text-[11px]">
                        {{ $akurasiOpname }}% Akurat
                    </span>
                </div>
            </div>

        </div>

        <!-- Chart 3: Tren 6 Bulan Sirkulasi Makro Kantor (Full Width) -->
        <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-7 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100 mb-5">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-bps-blue-500"></span>
                        <h3 class="font-extrabold text-slate-900 text-sm sm:text-base">Tren Sirkulasi Persediaan Kantor (6 Bulan Terakhir)</h3>
                    </div>
                    <p class="text-xs text-slate-400 mt-0.5">Volume logistik pengadaan masuk vs pengeluaran serah terima ke tim kerja</p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-600">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Pengadaan Masuk
                    </span>
                    <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-bps-orange-500">
                        <span class="w-2.5 h-2.5 rounded-full bg-bps-orange-500"></span> Serah Terima Keluar
                    </span>
                </div>
            </div>

            <div class="h-64 w-full relative">
                <canvas id="chartTrenMakroPimpinan"></canvas>
            </div>
        </div>

        <!-- BASO History & Approval Section -->
        <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-7 shadow-sm">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
                <div>
                    <h3 class="font-extrabold text-slate-900 text-sm">Histori Berita Acara Stock Opname (BASO)</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Dokumen pertanggungjawaban fisik persediaan bulanan BPS Kabupaten Cirebon</p>
                </div>
                <a href="{{ route('stok-opname.index') }}" class="text-xs font-bold text-teal-700 hover:text-teal-800 hover:underline">
                    Lihat Seluruh Arsip &rarr;
                </a>
            </div>

            @if($opnameTerbaru->isEmpty())
                <div class="text-center py-12">
                    <div class="w-14 h-14 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <p class="text-sm font-bold text-slate-700">Belum ada sesi Berita Acara Opname</p>
                    <p class="text-xs text-slate-400 mt-1">Dokumen opname yang difinalisasi Subbag Umum akan muncul di sini untuk disahkan.</p>
                </div>
            @else
                <div class="divide-y divide-slate-100">
                    @foreach($opnameTerbaru as $so)
                        <div class="py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-slate-50 px-3 rounded-2xl transition">
                            <div class="space-y-1">
                                <div class="flex items-center gap-2.5">
                                    <span class="text-xs font-mono font-bold text-slate-900">{{ $so->nomor_opname }}</span>
                                    <span class="px-2.5 py-0.5 text-[10px] font-bold uppercase rounded-md 
                                        @if($so->status === 'disahkan') bg-emerald-100 text-emerald-800
                                        @elseif($so->status === 'final') bg-amber-100 text-amber-800
                                        @else bg-slate-100 text-slate-600 @endif">
                                        {{ $so->status }}
                                    </span>
                                </div>
                                <p class="text-xs text-slate-500">
                                    Periode: <strong class="text-slate-800">{{ $so->periode }}</strong> • Dibuat oleh: {{ $so->user->name }}
                                </p>
                            </div>

                            <div class="flex items-center gap-3">
                                @if($so->status === 'final')
                                    <a href="{{ route('stok-opname.show', $so->id) }}" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-900 font-bold text-xs shadow-sm transition flex items-center gap-1.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                        <span>Periksa & Sahkan BASO</span>
                                    </a>
                                @elseif($so->status === 'disahkan')
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs text-emerald-600 font-semibold flex items-center gap-1">
                                            <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                            Telah Disahkan
                                        </span>
                                        <a href="{{ route('stok-opname.cetak', $so->id) }}" target="_blank" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 transition">
                                            Cetak BASO
                                        </a>
                                    </div>
                                @else
                                    <a href="{{ route('stok-opname.show', $so->id) }}" class="text-xs text-slate-500 hover:underline">
                                        Lihat Draft
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>

    <!-- Script Chart.js Initialization for Pimpinan Dashboard -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof Chart === 'undefined') return;

            // 1. Chart Komparasi Serapan Antar 7 Fungsi BPS
            const ctxSerapan = document.getElementById('chartSerapanPimpinan');
            if (ctxSerapan) {
                const serapanLabels = @json($ruanganSerapan->pluck('nama'));
                const serapanTotals = @json($ruanganSerapan->pluck('total'));

                new Chart(ctxSerapan, {
                    type: 'bar',
                    data: {
                        labels: serapanLabels,
                        datasets: [{
                            label: 'Volume Serapan ATK',
                            data: serapanTotals,
                            backgroundColor: [
                                '#002B49', '#0090D4', '#F7941D', '#10B981',
                                '#6366F1', '#EC4899', '#8B5CF6'
                            ],
                            borderRadius: 8,
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

            // 2. Chart Akurasi Stock Opname
            const ctxAkurasi = document.getElementById('chartAkurasiPimpinan');
            if (ctxAkurasi) {
                const totalCocok = {{ $totalCocokOpname }};
                const totalSelisih = {{ $totalSelisihOpname }};
                // Fallback jika belum ada data opname
                const dataValues = (totalCocok === 0 && totalSelisih === 0) ? [100, 0] : [totalCocok, totalSelisih];

                new Chart(ctxAkurasi, {
                    type: 'doughnut',
                    data: {
                        labels: ['Sesuai Fisik', 'Selisih'],
                        datasets: [{
                            data: dataValues,
                            backgroundColor: ['#10B981', '#F43F5E'],
                            borderWidth: 2,
                            borderColor: '#ffffff',
                            hoverOffset: 6
                        }]
                    },
                    options: {
                        circumference: 180,
                        rotation: -90,
                        cutout: '72%',
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: { boxWidth: 10, font: { size: 10 } }
                            }
                        }
                    }
                });
            }

            // 3. Chart Tren Sirkulasi Makro 6 Bulan
            const ctxMakro = document.getElementById('chartTrenMakroPimpinan');
            if (ctxMakro) {
                const gradientMasuk = ctxMakro.getContext('2d').createLinearGradient(0, 0, 0, 260);
                gradientMasuk.addColorStop(0, 'rgba(16, 185, 129, 0.28)');
                gradientMasuk.addColorStop(1, 'rgba(16, 185, 129, 0.00)');

                const gradientKeluar = ctxMakro.getContext('2d').createLinearGradient(0, 0, 0, 260);
                gradientKeluar.addColorStop(0, 'rgba(247, 148, 29, 0.28)');
                gradientKeluar.addColorStop(1, 'rgba(247, 148, 29, 0.00)');

                new Chart(ctxMakro, {
                    type: 'line',
                    data: {
                        labels: @json($bulanLabels),
                        datasets: [
                            {
                                label: 'Pengadaan Masuk',
                                data: @json($masukData),
                                borderColor: '#10B981',
                                backgroundColor: gradientMasuk,
                                fill: true,
                                tension: 0.35,
                                borderWidth: 2.5,
                                pointBackgroundColor: '#10B981',
                                pointRadius: 4,
                            },
                            {
                                label: 'Serah Terima Keluar',
                                data: @json($keluarData),
                                borderColor: '#F7941D',
                                backgroundColor: gradientKeluar,
                                fill: true,
                                tension: 0.35,
                                borderWidth: 2.5,
                                pointBackgroundColor: '#F7941D',
                                pointRadius: 4,
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
        });
    </script>
</x-app-layout>
