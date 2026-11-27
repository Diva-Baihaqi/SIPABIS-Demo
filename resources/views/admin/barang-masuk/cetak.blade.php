<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku Register Penerimaan Barang Persediaan - BPS Kabupaten Cirebon</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 9.5pt;
            color: #000;
            background: #f1f5f9;
            margin: 0;
            padding: 20px;
        }
        .page-container {
            background: #fff;
            max-width: 1100px;
            margin: 0 auto;
            padding: 25px 30px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        .header-table {
            width: 100%;
            border-bottom: 2.5px solid #000;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }
        .header-logo {
            width: 70px;
            text-align: center;
            vertical-align: middle;
        }
        .header-text {
            text-align: center;
            vertical-align: middle;
        }
        .header-text h3 {
            margin: 0;
            font-size: 13pt;
            font-weight: bold;
            letter-spacing: 0.5px;
        }
        .header-text h4 {
            margin: 3px 0;
            font-size: 11pt;
            font-weight: bold;
        }
        .header-text p {
            margin: 1px 0;
            font-size: 8.5pt;
            color: #333;
        }
        .doc-title-box {
            text-align: center;
            margin: 15px 0 10px 0;
        }
        .doc-title {
            font-size: 12pt;
            font-weight: bold;
            text-decoration: underline;
        }
        .doc-meta {
            font-size: 9pt;
            color: #475569;
            margin-top: 3px;
            font-style: italic;
        }
        .content-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 9pt;
        }
        .content-table th, .content-table td {
            border: 1px solid #000;
            padding: 6px 8px;
            vertical-align: middle;
        }
        .content-table th {
            background-color: #f1f5f9;
            font-weight: bold;
            text-align: center;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-mono { font-family: monospace, Courier, monospace; }
        .badge-lunas {
            color: #059669;
            font-weight: bold;
        }
        .badge-tertunda {
            color: #d97706;
            font-weight: bold;
        }
        .signature-table {
            width: 100%;
            margin-top: 30px;
            border-collapse: collapse;
            page-break-inside: avoid;
        }
        .signature-table td {
            border: none;
            padding: 4px;
            text-align: center;
            width: 50%;
        }
        .no-print-bar {
            background: #0f172a;
            color: #fff;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-radius: 8px;
            font-size: 9.5pt;
            max-width: 1100px;
            margin: 0 auto 15px auto;
        }
        .btn-print {
            background: #0d9488;
            color: #fff;
            padding: 6px 14px;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            font-size: 9pt;
        }
        .btn-close {
            background: #475569;
            color: #fff;
            padding: 6px 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 9pt;
            margin-left: 8px;
        }
        @media print {
            body { background: #fff; padding: 0; }
            .page-container { box-shadow: none; padding: 0; max-width: 100%; }
            .no-print-bar { display: none !important; }
            @page {
                size: A4 landscape;
                margin: 15mm;
            }
        }
    </style>
</head>
<body>

    <!-- Non-Print Toolbar -->
    <div class="no-print-bar">
        <div>
            <span><strong>Mode Cetak Laporan:</strong> Register Penerimaan Barang Persediaan BPS (Satker 428646) &bull; <span style="color: #38bdf8;">{{ $periodeTitle ?? 'SELURUH PERIODE' }}</span></span>
        </div>
        <div>
            <button onclick="window.print()" class="btn-print">🖨️ Cetak / Simpan PDF</button>
            <button onclick="window.close()" class="btn-close">Tutup</button>
        </div>
    </div>

    <div class="page-container">

        <!-- Kop Resmi BPS -->
        <table class="header-table">
            <tr>
                <td class="header-logo">
                    <img src="{{ asset('images/logo.png') }}" style="height: 55px; width: auto;" alt="Logo BPS">
                </td>
                <td class="header-text">
                    <h3>BADAN PUSAT STATISTIK KABUPATEN CIREBON</h3>
                    <p>Jl. Sunan Rahmat No. 7, Sumber, Kec. Sumber, Kabupaten Cirebon, Jawa Barat 45611</p>
                    <p>Telepon: (0231) 321151 • Email: bps3209@bps.go.id • Satker: 428646 • Kode UAKPB: 054.01.0200.018801</p>
                </td>
            </tr>
        </table>

        <!-- Judul Dokumen -->
        <div class="doc-title-box">
            <div class="doc-title">BUKU REGISTER PENERIMAAN BARANG PERSEDIAAN</div>
            <div style="font-size: 10.5pt; font-weight: bold; margin-top: 3px; color: #1e293b; letter-spacing: 0.5px;">
                {{ $periodeTitle ?? 'SELURUH PERIODE' }}
            </div>
            <div class="doc-meta">
                Filter: {{ $filterDesc }} • Dicetak pada: {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }} WIB
            </div>
        </div>

        <!-- Tabel Register -->
        <table class="content-table">
            <thead>
                <tr>
                    <th style="width: 4%;">No.</th>
                    <th style="width: 10%;">Tanggal</th>
                    <th style="width: 14%;">No. Dokumen / Faktur</th>
                    <th style="width: 12%;">Kode BMN</th>
                    <th>Uraian Barang Persediaan</th>
                    <th style="width: 10%;">Kategori</th>
                    <th style="width: 7%;">Satuan</th>
                    <th style="width: 9%;">Jumlah</th>
                    <th style="width: 15%;">Penyedia / Vendor</th>
                    <th style="width: 10%;">Status SPJ</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transaksiList as $index => $t)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-center font-mono">{{ \Carbon\Carbon::parse($t->tanggal_transaksi)->format('d/m/Y') }}</td>
                        <td class="text-center font-mono">{{ $t->nomor_dokumen ?? '-' }}</td>
                        <td class="text-center font-mono text-xs">{{ $t->barang->kode_barang ?? '-' }}</td>
                        <td><strong>{{ $t->barang->nama_barang ?? '-' }}</strong></td>
                        <td class="text-center text-xs">{{ $t->barang->kategori->nama_kategori ?? '-' }}</td>
                        <td class="text-center">{{ $t->barang->satuan ?? '-' }}</td>
                        <td class="text-right font-mono font-bold">{{ number_format($t->jumlah) }}</td>
                        <td>{{ $t->sumber_atau_tujuan ?? '-' }}</td>
                        <td class="text-center">
                            @if($t->status_spj === 'selesai')
                                <span class="badge-lunas">SPJ Lunas</span>
                            @else
                                <span class="badge-tertunda">SPJ Tertunda</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center" style="padding: 20px; color: #64748b;">
                            Tidak ada data transaksi penerimaan barang yang sesuai dengan filter.
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr style="font-weight: bold; background-color: #f1f5f9;">
                    <td colspan="7" class="text-center uppercase">TOTAL KESELURUHAN UNIT FISIK MASUK</td>
                    <td class="text-right font-mono">{{ number_format($totalUnit) }}</td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        </table>

        <!-- Blok Tanda Tangan -->
        <table class="signature-table">
            <tr>
                <td></td>
                <td>Sumber, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</td>
            </tr>
            <tr>
                <td>Mengetahui,</td>
                <td>Petugas Pengurus Barang Persediaan,</td>
            </tr>
            <tr>
                <td>Kepala Subbagian Umum BPS Kabupaten Cirebon</td>
                <td>Pengelola Persediaan BPS Kabupaten Cirebon</td>
            </tr>
            <tr>
                <td style="height: 60px;"></td>
                <td style="height: 60px;"></td>
            </tr>
            <tr>
                <td><strong><u>Rina Kusuma Wardani, S.E., M.M.</u></strong></td>
                <td><strong><u>Bagus Prasetyo, S.E.</u></strong></td>
            </tr>
            <tr>
                <td>NIP. 198003202005012002</td>
                <td>NIP. 198705142010011003</td>
            </tr>
        </table>

    </div>

</body>
</html>
