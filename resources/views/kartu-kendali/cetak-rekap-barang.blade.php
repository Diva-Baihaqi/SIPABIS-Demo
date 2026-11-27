<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Posisi Persediaan BPS Kabupaten Cirebon - Tahun {{ $filter['tahun'] ?? date('Y') }}</title>
    <style>
        @page {
            size: landscape;
            margin: 12mm 10mm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9.5pt;
            color: #1e293b;
            margin: 0;
            padding: 15px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
            margin-bottom: 14px;
        }
        .doc-title {
            text-align: center;
            margin: 10px 0 4px 0;
            font-size: 12pt;
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
        }
        .doc-subtitle {
            text-align: center;
            margin-bottom: 14px;
            font-size: 9.5pt;
            font-weight: bold;
            color: #475569;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 12px;
            font-size: 9pt;
        }
        .meta-table td {
            padding: 2px 0;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 8.5pt;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #000;
            padding: 4px 6px;
        }
        table.data-table th {
            background-color: #f1f5f9;
            text-align: center;
            font-weight: bold;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .signatures {
            width: 100%;
            margin-top: 25px;
            page-break-inside: avoid;
        }
        .signatures td {
            text-align: center;
            vertical-align: top;
            width: 50%;
            font-size: 9pt;
        }
        .sign-space {
            height: 55px;
        }
        .no-print-bar {
            background: #0f172a;
            color: #fff;
            padding: 10px 20px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-radius: 8px;
        }
        .btn-print {
            background: #0d9488;
            color: #fff;
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
        }
        @media print {
            .no-print-bar { display: none !important; }
            body { padding: 0; }
        }
    </style>
</head>
<body>

    <!-- Non-Print Toolbar -->
    <div class="no-print-bar">
        <div>
            <strong style="font-size: 11pt;">Pratinjau Cetak: Laporan Posisi Nilai & Fisik Persediaan ATK</strong>
            <span style="font-size: 9pt; display: block; opacity: 0.8;">
                Periode: {{ !empty($filter['bulan']) ? ($bulanList[$filter['bulan']] ?? $filter['bulan']) . ' ' : '' }}Tahun {{ $filter['tahun'] ?? date('Y') }} 
                • Cakupan: {{ ($filter['cakupan'] ?? 'aktif') === 'aktif' ? 'Hanya Barang Aktif (Stok/Mutasi > 0)' : 'Seluruh Katalog' }}
            </span>
        </div>
        <div>
            <button onclick="window.print()" class="btn-print">🖨️ Cetak / Simpan PDF (Landscape)</button>
            <button onclick="window.close()" style="margin-left: 8px; padding: 8px 14px; background: #475569; color: #fff; border: none; border-radius: 6px; cursor: pointer;">Tutup</button>
        </div>
    </div>

    <!-- Official Header -->
    <div class="header" style="display: flex; align-items: center; justify-content: center; gap: 15px;">
        <img src="{{ asset('images/logo.png') }}" style="height: 48px; width: auto;" alt="Logo BPS">
        <div>
            <h3 style="margin: 0; font-size: 13pt; font-weight: 800;">BADAN PUSAT STATISTIK KABUPATEN CIREBON</h3>
            <p style="margin: 2px 0; font-size: 8pt; color: #475569;">Jl. Sunan Rahmat No. 7, Sumber, Kec. Sumber, Kabupaten Cirebon, Jawa Barat 45611</p>
            <p style="margin: 2px 0; font-size: 8pt; color: #475569;">Telepon: (0231) 321151 • Email: bps3209@bps.go.id • Satker: 428646 (UAKPB: 054.01.0200.018801)</p>
        </div>
    </div>

    <div class="doc-title">LAPORAN REKAPITULASI POSISI NILAI DAN FISIK BARANG PERSEDIAAN</div>
    <div class="doc-subtitle">BUKU PERSEDIAAN KANTOR BPS KABUPATEN CIREBON (NERACA ATK)</div>

    <!-- Meta Info -->
    <table class="meta-table">
        <tr>
            <td style="width: 15%;"><strong>Satuan Kerja</strong></td>
            <td style="width: 2%;">:</td>
            <td style="width: 48%;"><strong>BPS KABUPATEN CIREBON</strong> (428646)</td>
            <td style="width: 15%;"><strong>Kode UAKPB</strong></td>
            <td style="width: 2%;">:</td>
            <td style="width: 18%;">054.01.0200.018801</td>
        </tr>
        <tr>
            <td><strong>Periode Anggaran</strong></td>
            <td>:</td>
            <td>
                @if(!empty($filter['bulan']))
                    Bulan {{ $bulanList[$filter['bulan']] ?? $filter['bulan'] }}
                @endif
                Tahun {{ $filter['tahun'] ?? date('Y') }}
            </td>
            <td><strong>Cakupan Laporan</strong></td>
            <td>:</td>
            <td>
                {{ ($filter['cakupan'] ?? 'aktif') === 'aktif' ? 'Hanya Barang Aktif (Stok/Mutasi > 0)' : 'Seluruh Katalog' }}
                ({{ $totalJenisBarang }} Item)
            </td>
        </tr>
        <tr>
            <td><strong>Tanggal Cetak</strong></td>
            <td>:</td>
            <td>{{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }} WIB</td>
            <td><strong>Format Laporan</strong></td>
            <td>:</td>
            <td>Standar Akuntansi BMN (FIFO)</td>
        </tr>
    </table>

    <!-- Data Table -->
    <table class="data-table">
        <thead>
            <tr>
                <th rowspan="2" style="width: 3%;">No</th>
                <th rowspan="2" style="width: 9%;">Kode BMN</th>
                <th rowspan="2">Nama Barang Persediaan</th>
                <th rowspan="2" style="width: 9%;">Kategori</th>
                <th rowspan="2" style="width: 5%;">Satuan</th>
                <th rowspan="2" style="width: 7%;">Harga Satuan</th>
                <th colspan="2" style="width: 13%;">Saldo Awal</th>
                <th colspan="2" style="width: 13%;">Mutasi Masuk (+)</th>
                <th colspan="2" style="width: 13%;">Mutasi Keluar (-)</th>
                <th colspan="2" style="width: 15%;">Sisa Fisik & Nilai</th>
            </tr>
            <tr>
                <th style="width: 5%;">Fisik</th>
                <th style="width: 8%;">Nilai (Rp)</th>
                <th style="width: 5%;">Fisik</th>
                <th style="width: 8%;">Nilai (Rp)</th>
                <th style="width: 5%;">Fisik</th>
                <th style="width: 8%;">Nilai (Rp)</th>
                <th style="width: 6%;">Fisik</th>
                <th style="width: 9%;">Nilai Akhir (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($barangItems as $idx => $b)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="text-center font-mono">{{ $b->kode_barang }}</td>
                    <td><strong>{{ $b->nama_barang }}</strong></td>
                    <td class="text-center">{{ $b->kategori }}</td>
                    <td class="text-center">{{ $b->satuan }}</td>
                    <td class="text-right">{{ number_format($b->harga_satuan) }}</td>
                    <td class="text-right">{{ number_format($b->saldo_awal) }}</td>
                    <td class="text-right">{{ number_format($b->saldo_awal_rp) }}</td>
                    <td class="text-right">{{ number_format($b->total_masuk) }}</td>
                    <td class="text-right">{{ number_format($b->total_masuk_rp) }}</td>
                    <td class="text-right">{{ number_format($b->total_keluar) }}</td>
                    <td class="text-right">{{ number_format($b->total_keluar_rp) }}</td>
                    <td class="text-right font-bold">{{ number_format($b->saldo_akhir) }}</td>
                    <td class="text-right font-bold">{{ number_format($b->saldo_akhir_rp) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="14" class="text-center" style="font-style: italic; color: #64748B; padding: 15px;">
                        Tidak ada barang persediaan yang cocok dengan kriteria filter.
                    </td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background-color: #f1f5f9; font-weight: bold;">
                <td colspan="6" class="text-right">GRAND TOTAL KESELURUHAN BPS:</td>
                <td class="text-right">{{ number_format($grandTotalSaldoAwalFisik) }}</td>
                <td class="text-right">Rp {{ number_format($grandTotalSaldoAwalRp) }}</td>
                <td class="text-right">{{ number_format($grandTotalMasukFisik) }}</td>
                <td class="text-right">Rp {{ number_format($grandTotalMasukRp) }}</td>
                <td class="text-right">{{ number_format($grandTotalKeluarFisik) }}</td>
                <td class="text-right">Rp {{ number_format($grandTotalKeluarRp) }}</td>
                <td class="text-right" style="color: #0f172a;">{{ number_format($grandTotalSaldoAkhirFisik) }}</td>
                <td class="text-right" style="color: #0f172a;">Rp {{ number_format($grandTotalSaldoAkhirRp) }}</td>
            </tr>
        </tfoot>
    </table>

    <!-- Signatures -->
    <table class="signatures">
        <tr>
            <td>
                Mengetahui,<br>
                Kepala Subbagian Umum BPS Kabupaten Cirebon
                <div class="sign-space"></div>
                <strong><u>Rina Kusuma Wardani, S.E., M.M.</u></strong><br>
                NIP. 198003202005012002
            </td>
            <td>
                Sumber, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                Pengelola BMN / Petugas Persediaan BPS
                <div class="sign-space"></div>
                <strong><u>Pengadministrasi Persediaan BPS</u></strong><br>
                NIP. ....................................................
            </td>
        </tr>
    </table>

</body>
</html>
