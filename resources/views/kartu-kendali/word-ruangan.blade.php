<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://www.w3.org/TR/REC-html40'>
<head>
    <meta charset="utf-8">
    <title>Buku Kendali {{ $ruangan->nama_ruangan }} - BPS Kabupaten Cirebon</title>
    <!--[if gte mso 9]>
    <xml>
        <w:WordDocument>
            <w:View>Print</w:View>
            <w:Zoom>100</w:Zoom>
            <w:DoNotOptimizeForBrowser/>
        </w:WordDocument>
    </xml>
    <![endif]-->
    <style>
        @page Section1 {
            size: 210mm 297mm;
            margin: 20mm 15mm 20mm 15mm;
            mso-header-margin: 10mm;
            mso-footer-margin: 10mm;
        }
        div.Section1 { page: Section1; }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10pt;
            color: #1e293b;
        }
        .header {
            text-align: center;
            border-bottom: 2pt solid #000;
            padding-bottom: 8pt;
            margin-bottom: 12pt;
        }
        .doc-title {
            text-align: center;
            margin: 12pt 0 4pt 0;
            font-size: 12pt;
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
        }
        .doc-subtitle {
            text-align: center;
            margin-bottom: 14pt;
            font-size: 10pt;
            font-weight: bold;
            color: #475569;
        }
        table.meta-table {
            width: 100%;
            margin-bottom: 12pt;
            font-size: 9.5pt;
        }
        table.meta-table td {
            padding: 2pt 0;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15pt;
            font-size: 9pt;
        }
        table.data-table th, table.data-table td {
            border: 1pt solid #000;
            padding: 4pt 6pt;
        }
        table.data-table th {
            background-color: #f1f5f9;
            text-align: center;
            font-weight: bold;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        table.signatures {
            width: 100%;
            margin-top: 25pt;
            page-break-inside: avoid;
        }
        table.signatures td {
            text-align: center;
            vertical-align: top;
            width: 50%;
            font-size: 9.5pt;
        }
        .sign-space {
            height: 50pt;
        }
    </style>
</head>
<body>
    <div class="Section1">

        <!-- HEADER KOP DINAS BPS -->
        <div class="header">
            <h3 style="margin: 0; font-size: 13pt; font-weight: bold; color: #002B49;">BADAN PUSAT STATISTIK KABUPATEN CIREBON</h3>
            <p style="margin: 2pt 0; font-size: 8.5pt; color: #475569;">Jl. Sunan Rahmat No. 7, Sumber, Kec. Sumber, Kabupaten Cirebon, Jawa Barat 45611</p>
            <p style="margin: 2pt 0; font-size: 8.5pt; color: #475569;">Telepon: (0231) 321151 • Email: bps3209@bps.go.id • Satker: 428646 (UAKPB: 054.01.0200.018801)</p>
        </div>

        <div class="doc-title">BUKU KENDALI PENGELUARAN BARANG PAKAI HABIS</div>
        <div class="doc-subtitle">UNIT KERJA: {{ strtoupper($ruangan->nama_ruangan) }} ({{ $ruangan->kode_ruangan }})</div>

        <table class="meta-table">
            <tr>
                <td style="width: 20%;"><strong>Unit Kerja / Ruangan</strong></td>
                <td style="width: 2%;">:</td>
                <td style="width: 48%;"><strong>{{ $ruangan->nama_ruangan }}</strong> ({{ $ruangan->kode_ruangan }})</td>
                <td style="width: 15%;"><strong>Kode UAKPB</strong></td>
                <td style="width: 2%;">:</td>
                <td style="width: 13%;">054.01.0200.018801</td>
            </tr>
            <tr>
                <td><strong>Penanggung Jawab</strong></td>
                <td>:</td>
                <td>{{ $ruangan->penanggung_jawab ?? 'Kasubbag / Ketua Tim' }}</td>
                <td><strong>Periode Anggaran</strong></td>
                <td>:</td>
                <td>
                    @if(!empty($filter['bulan']))
                        Bulan {{ $bulanList[$filter['bulan']] ?? $filter['bulan'] }}
                    @endif
                    Tahun {{ $filter['tahun'] ?? date('Y') }}
                </td>
            </tr>
            <tr>
                <td><strong>Jumlah Unit BMN</strong></td>
                <td>:</td>
                <td>{{ number_format($totalUnitKeluar ?? 0) }} Unit ({{ $totalJenisBarang ?? 0 }} jenis barang)</td>
                <td><strong>Tanggal Unduh</strong></td>
                <td>:</td>
                <td>{{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</td>
            </tr>
        </table>

        <!-- Bagian I -->
        <p style="font-weight: bold; margin-bottom: 4pt; font-size: 9.5pt;">I. REKAPITULASI PENGAMBILAN ATK PER JENIS BARANG</p>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 18%;">Kode Barang</th>
                    <th>Nama Barang Persediaan</th>
                    <th style="width: 15%;">Kategori</th>
                    <th style="width: 10%;">Satuan</th>
                    <th style="width: 12%;">Frekuensi</th>
                    <th style="width: 15%;">Total Dikeluarkan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rekapBarang as $bIdx => $item)
                    <tr>
                        <td class="text-center">{{ $bIdx + 1 }}</td>
                        <td class="text-center">{{ $item->kode_barang }}</td>
                        <td>{{ $item->nama_barang }}</td>
                        <td>{{ $item->kategori }}</td>
                        <td class="text-center">{{ $item->satuan }}</td>
                        <td class="text-center">{{ $item->frekuensi }} kali</td>
                        <td class="text-right font-bold">{{ number_format($item->total_jumlah) }} {{ $item->satuan }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center" style="font-style: italic; color: #64748B;">Tidak ada catatan pengeluaran barang pada periode ini.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr style="background-color: #f8fafc; font-weight: bold;">
                    <td colspan="6" class="text-right">TOTAL KESELURUHAN FISIK BARANG:</td>
                    <td class="text-right">{{ number_format($totalUnitKeluar ?? 0) }} Unit</td>
                </tr>
            </tfoot>
        </table>

        <!-- Bagian II -->
        <p style="font-weight: bold; margin-bottom: 4pt; font-size: 9.5pt; margin-top: 14pt;">II. RINCIAN KRONOLOGIS TRANSAKSI PENGELUARAN FISIK</p>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 12%;">Tanggal</th>
                    <th style="width: 20%;">No. Permintaan</th>
                    <th style="width: 18%;">Pegawai Pengambil</th>
                    <th>Nama Barang Persediaan</th>
                    <th style="width: 12%;">Jumlah</th>
                    <th style="width: 18%;">Peruntukan Kegiatan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transaksiList as $tIdx => $t)
                    <tr>
                        <td class="text-center">{{ $tIdx + 1 }}</td>
                        <td class="text-center">{{ \Carbon\Carbon::parse($t->tanggal_transaksi)->format('d/m/Y') }}</td>
                        <td class="text-center">{{ $t->permintaan?->nomor_permintaan ?? '-' }}</td>
                        <td>{{ $t->user?->name ?? 'Pegawai BPS' }}</td>
                        <td>{{ $t->barang?->nama_barang ?? '-' }}</td>
                        <td class="text-right font-bold">{{ number_format(abs($t->jumlah)) }} {{ $t->barang?->satuan ?? 'Unit' }}</td>
                        <td>{{ $t->permintaan?->keterangan ?? $t->keterangan ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center" style="font-style: italic; color: #64748B;">Tidak ada transaksi pengeluaran fisik tercatat.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Tanda Tangan -->
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
                    Penerima / Penanggung Jawab {{ $ruangan->nama_ruangan }}
                    <div class="sign-space"></div>
                    <strong><u>{{ $ruangan->penanggung_jawab ?? 'Kasubbag / Ketua Tim' }}</u></strong><br>
                    NIP. {{ $ruangan->nip_penanggung_jawab ?? '....................................................' }}
                </td>
            </tr>
        </table>

    </div>
</body>
</html>
