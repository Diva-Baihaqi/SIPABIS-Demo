<x-app-layout>
    <x-slot:title>Dashboard Pegawai</x-slot:title>
    <x-slot:header>Permintaan ATK & Logistik Pegawai</x-slot:header>

    <div class="space-y-7">
        
        <!-- Welcome Hero Banner -->
        <div class="relative overflow-hidden bg-gradient-to-r from-bps-blue-700 via-bps-blue-600 to-bps-navy-800 rounded-3xl p-6 sm:p-8 text-white shadow-xl shadow-bps-blue-900/10 border border-bps-blue-500/30">
            <div class="absolute -right-10 -bottom-10 w-72 h-72 bg-bps-orange-500/20 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute right-1/3 -top-10 w-60 h-60 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>

            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="space-y-2">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 text-white text-xs font-semibold backdrop-blur-sm border border-white/20">
                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                        <span>Unit Kerja: {{ auth()->user()->ruangan?->nama_ruangan ?? 'Tim Fungsi BPS' }}</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white">Halo, {{ auth()->user()->name }}</h1>
                    <p class="text-xs sm:text-sm text-slate-100 max-w-xl leading-relaxed">
                        Ajukan kebutuhan ATK untuk kegiatan survei, sensus, atau pekerjaan dinas sehari-hari secara mudah dan cepat tanpa perlu formulir kertas.
                    </p>
                </div>

                <!-- Primary Action -->
                <div class="shrink-0">
                    <a href="{{ route('pegawai.permintaan.create') }}" class="inline-flex items-center gap-2.5 px-6 py-3.5 rounded-xl font-bold text-xs uppercase tracking-wider bg-bps-orange-500 hover:bg-bps-orange-600 text-white shadow-lg shadow-bps-orange-500/30 hover:scale-[1.02] active:scale-[0.98] transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Minta ATK Baru</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Metric Summary Cards (4 Kolom) -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Total Permintaan</span>
                <span class="text-3xl font-black text-slate-800 mt-1 block">{{ $totalPermintaan }}</span>
                <span class="text-[11px] text-slate-500 flex items-center gap-1 mt-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                    Permintaan yang saya buat
                </span>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition">
                <span class="text-xs font-bold text-amber-500 uppercase tracking-wider block">Menunggu Persetujuan</span>
                <span class="text-3xl font-black text-amber-600 mt-1 block">{{ $permintaanMenunggu }}</span>
                <span class="text-[11px] text-slate-500 flex items-center gap-1 mt-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                    Sedang dicek oleh Admin
                </span>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition">
                <span class="text-xs font-bold text-bps-blue-500 uppercase tracking-wider block">Siap Diambil</span>
                <span class="text-3xl font-black text-bps-blue-600 mt-1 block">{{ $permintaanDisetujui }}</span>
                <span class="text-[11px] text-slate-500 flex items-center gap-1 mt-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-bps-blue-500"></span>
                    Bisa diambil di Subbag Umum
                </span>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition">
                <span class="text-xs font-bold text-emerald-500 uppercase tracking-wider block">Sudah Diterima</span>
                <span class="text-3xl font-black text-emerald-600 mt-1 block">{{ $permintaanSelesai }}</span>
                <span class="text-[11px] text-slate-500 flex items-center gap-1 mt-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    Barang sudah diserahkan
                </span>
            </div>
        </div>

        <!-- Interactive Charts Grid (2 Columns) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-7">
            
            <!-- Chart 1: Tren Penggunaan ATK Ruangan (7 Kolom) -->
            <div class="lg:col-span-7 bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-7 shadow-sm">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-bps-blue-500"></span>
                            <h3 class="font-extrabold text-slate-900 text-sm sm:text-base">Tren Penyerapan ATK Unit Kerja (6 Bulan Terakhir)</h3>
                        </div>
                        <p class="text-xs text-slate-400 mt-0.5">Jumlah barang persediaan yang diterima oleh {{ auth()->user()->ruangan?->nama_ruangan ?? 'Tim Kerja' }}</p>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-bps-blue-50 text-bps-blue-700">
                        {{ auth()->user()->ruangan?->kode_ruangan ?? 'BPS' }}
                    </span>
                </div>

                <div class="h-64 w-full relative">
                    <canvas id="chartTrenPegawai"></canvas>
                </div>
            </div>

            <!-- Chart 2: Proporsi Status Tiket Pengajuan (5 Kolom) -->
            <div class="lg:col-span-5 bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-7 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-2 pb-4 border-b border-slate-100 mb-4">
                        <span class="w-2.5 h-2.5 rounded-full bg-bps-orange-500"></span>
                        <div>
                            <h3 class="font-extrabold text-slate-900 text-sm sm:text-base">Status Pengajuan Permohonan</h3>
                            <p class="text-xs text-slate-400 mt-0.5">Proporsi siklus proses permohonan ATK Anda</p>
                        </div>
                    </div>

                    <div class="h-56 w-full relative my-2">
                        <canvas id="chartStatusPegawai"></canvas>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2 text-[11px] pt-3 border-t border-slate-100">
                    <div class="flex items-center justify-between p-1.5 rounded-lg bg-amber-50 text-amber-900">
                        <span>Menunggu</span>
                        <span class="font-bold">{{ $permintaanMenunggu }}</span>
                    </div>
                    <div class="flex items-center justify-between p-1.5 rounded-lg bg-blue-50 text-blue-900">
                        <span>Disetujui</span>
                        <span class="font-bold">{{ $permintaanDisetujui }}</span>
                    </div>
                    <div class="flex items-center justify-between p-1.5 rounded-lg bg-emerald-50 text-emerald-900">
                        <span>Selesai</span>
                        <span class="font-bold">{{ $permintaanSelesai }}</span>
                    </div>
                    <div class="flex items-center justify-between p-1.5 rounded-lg bg-rose-50 text-rose-900">
                        <span>Ditolak</span>
                        <span class="font-bold">{{ $permintaanDitolak }}</span>
                    </div>
                </div>
            </div>

        </div>

        <!-- Top 5 Barang Rutin & Riwayat Pengajuan -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-7">
            
            <!-- Top 5 Barang yang Sering Diminta (4 Kolom) -->
            <div class="lg:col-span-4 bg-white rounded-3xl border border-slate-200/80 p-6 shadow-sm">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                    <div>
                        <h3 class="font-extrabold text-slate-900 text-sm">ATK Rutin Ruangan</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Barang yang paling banyak diserap unit kerja Anda</p>
                    </div>
                </div>

                @if($topBarangRuangan->isEmpty())
                    <div class="text-center py-10">
                        <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                        </div>
                        <p class="text-xs font-semibold text-slate-600">Belum ada riwayat konsumsi</p>
                        <p class="text-[11px] text-slate-400 mt-0.5">Data muncul saat permohonan telah selesai.</p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($topBarangRuangan as $top)
                            <div class="p-2.5 rounded-xl bg-slate-50/80 hover:bg-slate-100/80 transition flex items-center justify-between gap-2">
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-slate-800 truncate">{{ $top->nama_barang }}</p>
                                    <span class="text-[10px] text-slate-400">Total diserahkan</span>
                                </div>
                                <span class="px-2 py-1 rounded-lg bg-bps-blue-100 text-bps-blue-800 text-xs font-black shrink-0">
                                    {{ $top->total_qty }} unit
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Riwayat Permintaan Terakhir Saya (8 Kolom) -->
            <div class="lg:col-span-8 bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-7 shadow-sm">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
                    <div>
                        <h3 class="font-extrabold text-slate-900 text-sm">Riwayat Permohonan Terakhir Saya</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Status dan respon verifikasi permohonan ATK</p>
                    </div>
                    <a href="{{ route('pegawai.permintaan.index') }}" class="text-xs font-bold text-bps-blue-600 hover:text-bps-orange-500 transition">Lihat Seluruhnya &rarr;</a>
                </div>

                @if($permintaanSaya->isEmpty())
                    <div class="text-center py-12">
                        <div class="w-14 h-14 rounded-2xl bg-blue-50 text-bps-blue-500 flex items-center justify-center mx-auto mb-3">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        </div>
                        <p class="text-sm font-bold text-slate-700">Belum ada pengajuan ATK</p>
                        <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">Klik tombol "Buat Permintaan ATK Baru" di atas untuk mengajukan perlengkapan persediaan baru.</p>
                    </div>
                @else
                    <div class="divide-y divide-slate-100">
                        @foreach($permintaanSaya as $req)
                            <div class="py-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-slate-50 px-2 rounded-xl transition">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-extrabold text-slate-900 font-mono">{{ $req->nomor_permintaan }}</span>
                                        <span class="px-2.5 py-0.5 text-[10px] font-bold uppercase rounded-md 
                                            @if($req->status === 'diajukan') bg-amber-100 text-amber-800
                                            @elseif($req->status === 'disetujui') bg-blue-100 text-blue-800
                                            @elseif($req->status === 'selesai') bg-emerald-100 text-emerald-800
                                            @elseif($req->status === 'ditolak') bg-rose-100 text-rose-800
                                            @else bg-slate-100 text-slate-600 @endif">
                                            {{ $req->status }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-500">
                                        Tanggal: <span class="font-medium text-slate-700">{{ $req->tanggal_pengajuan->format('d M Y') }}</span> • 
                                        Kebutuhan: <span class="font-medium text-slate-700">{{ $req->details->count() }} item barang</span>
                                    </p>
                                </div>

                                <div class="flex items-center gap-2 self-start sm:self-center">
                                    @if($req->status === 'disetujui')
                                        <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-blue-50 border border-blue-200 text-blue-700 font-semibold text-[11px]">
                                            <svg class="w-3.5 h-3.5 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <span>Siap diambil di Subbag Umum</span>
                                        </div>
                                    @elseif($req->status === 'selesai')
                                        <span class="text-slate-400 text-xs font-medium">Telah diterima {{ $req->tanggal_serah_terima?->format('d/m/Y') }}</span>
                                    @elseif($req->status === 'ditolak')
                                        <span class="text-rose-500 text-xs font-medium">Ditolak: {{ Str::limit($req->catatan_admin, 25) }}</span>
                                    @else
                                        <span class="text-amber-600 text-xs font-medium">Menunggu kuota admin</span>
                                    @endif

                                    <a href="{{ route('pegawai.permintaan.show', $req->id) }}" class="px-2.5 py-1 rounded-lg text-xs font-bold text-bps-blue-600 hover:bg-bps-blue-50 transition">
                                        Detail &rarr;
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>

    </div>

    <!-- Script Chart.js Initialization for Pegawai Dashboard -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof Chart === 'undefined') return;

            // 1. Chart Tren Konsumsi ATK Ruangan 6 Bulan
            const ctxTren = document.getElementById('chartTrenPegawai');
            if (ctxTren) {
                const gradientTren = ctxTren.getContext('2d').createLinearGradient(0, 0, 0, 260);
                gradientTren.addColorStop(0, 'rgba(0, 144, 212, 0.30)');
                gradientTren.addColorStop(1, 'rgba(0, 144, 212, 0.00)');

                new Chart(ctxTren, {
                    type: 'line',
                    data: {
                        labels: @json($bulanLabels),
                        datasets: [{
                            label: 'Volume ATK Diterima',
                            data: @json($ruanganTrenData),
                            borderColor: '#0090D4',
                            backgroundColor: gradientTren,
                            fill: true,
                            tension: 0.35,
                            borderWidth: 2.5,
                            pointBackgroundColor: '#0090D4',
                            pointRadius: 4,
                            pointHoverRadius: 6,
                        }]
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
                        },
                        plugins: {
                            legend: { display: false }
                        }
                    }
                });
            }

            // 2. Chart Distribusi Status Tiket Permintaan
            const ctxStatus = document.getElementById('chartStatusPegawai');
            if (ctxStatus) {
                const statusLabels = @json(array_keys($statusDistribution));
                const statusValues = @json(array_values($statusDistribution));

                new Chart(ctxStatus, {
                    type: 'doughnut',
                    data: {
                        labels: statusLabels,
                        datasets: [{
                            data: statusValues,
                            backgroundColor: [
                                '#F59E0B', // Diajukan (amber)
                                '#0090D4', // Disetujui (blue)
                                '#10B981', // Selesai (emerald)
                                '#F43F5E'  // Ditolak (rose)
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
        });
    </script>
</x-app-layout>
