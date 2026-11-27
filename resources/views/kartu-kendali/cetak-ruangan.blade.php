<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku Kendali - {{ $ruangan->nama_ruangan }} | BPS Kabupaten Cirebon</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11pt;
            color: #1e293b;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
            position: relative;
        }
        .header h3, .header h4, .header p {
            margin: 2px 0;
        }
        .header h3 {
            font-size: 14pt;
            text-transform: uppercase;
        }
        .header h4 {
            font-size: 12pt;
            text-transform: uppercase;
        }
        .header p {
            font-size: 9pt;
            color: #475569;
        }
        .doc-title {
            text-align: center;
            margin: 15px 0;
            font-size: 12pt;
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 15px;
            font-size: 10pt;
        }
        .meta-table td {
            padding: 3px 0;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 9.5pt;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #000;
            padding: 6px 8px;
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
            margin-top: 35px;
            page-break-inside: avoid;
        }
        .signatures td {
            text-align: center;
            vertical-align: top;
            width: 50%;
            font-size: 10pt;
        }
        .sign-space {
            height: 70px;
        }
        .no-print-bar {
            background: #0f172a;
            color: #fff;
            padding: 10px 20px;
            margin-bottom: 20px;
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
        <span>Pratinjau Cetak Resmi: Buku Kendali Distribusi ATK</span>
        <div>
            <button onclick="window.print()" class="btn-print">🖨️ Cetak / Simpan PDF</button>
            <button onclick="window.close()" style="margin-left: 8px; padding: 8px 14px; background: #475569; color: #fff; border: none; border-radius: 6px; cursor: pointer;">Tutup</button>
        </div>
    </div>

    <!-- Official Header -->
    <div class="header" style="display: flex; align-items: center; justify-content: center; gap: 15px;">
        <img src="{{ asset('images/logo.png') }}" style="height: 52px; width: auto;" alt="Logo BPS">
        <div>
            <h3 style="margin: 0; font-size: 13pt; font-weight: 800;">BADAN PUSAT STATISTIK KABUPATEN CIREBON</h3>
            <p style="margin: 2px 0; font-size: 8.5pt; color: #475569;">Jl. Sunan Rahmat No. 7, Sumber, Kec. Sumber, Kabupaten Cirebon, Jawa Barat 45611</p>
            <p style="margin: 2px 0; font-size: 8.5pt; color: #475569;">Telepon: (0231) 321151 • Email: bps3209@bps.go.id • Satker: 428646 (UAKPB: 054.01.0200.018801)</p>
        </div>
    </div>

    <div class="doc-title">BUKU KENDALI PENGELUARAN BARANG PAKAI HABIS</div>

    <!-- Meta Info -->
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
            <td>Tahun {{ $filter['tahun'] ?? date('Y') }}</td>
        </tr>
    </table>

    <!-- Section 1: Rekapitulasi per barang -->
    <p style="font-weight: bold; margin-bottom: 6px; font-size: 10pt;">I. REKAPITULASI PENGAMBILAN ATK PER JENIS BARANG</p>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 20%;">Kode Barang</th>
                <th>Nama Barang Persediaan</th>
                <th style="width: 15%;">Kategori</th>
                <th style="width: 10%;">Satuan</th>
                <th style="width: 12%;">Frekuensi</th>
                <th style="width: 15%;">Total Dikeluarkan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rekapBarang as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ $item->kode_barang }}</td>
                    <td>{{ $item->nama_barang }}</td>
                    <td>{{ $item->kategori }}</td>
                    <td class="text-center">{{ $item->satuan }}</td>
                    <td class="text-center">{{ $item->frekuensi }} kali</td>
                    <td class="text-right font-bold">{{ number_format($item->total_jumlah) }} {{ $item->satuan }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">Tidak ada catatan pengeluaran barang.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background-color: #f8fafc; font-weight: bold;">
                <td colspan="6" class="text-right">TOTAL KESELURUHAN FISIK BARANG:</td>
                <td class="text-right">{{ number_format($totalUnitKeluar) }} Unit</td>
            </tr>
        </tfoot>
    </table>

    <!-- Section 2: Log Transaksi -->
    <p style="font-weight: bold; margin-top: 25px; margin-bottom: 6px; font-size: 10pt;">II. RINCIAN KRONOLOGIS TRANSAKSI PENGELUARAN FISIK</p>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 12%;">Tanggal</th>
                <th style="width: 22%;">No. Permintaan / Dokumen</th>
                <th style="width: 18%;">Pegawai Pengambil</th>
                <th>Nama Barang</th>
                <th style="width: 10%;">Jumlah</th>
                <th style="width: 18%;">Peruntukan Kegiatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transaksiList as $idx => $t)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="text-center">{{ $t->tanggal_transaksi->format('d/m/Y') }}</td>
                    <td>{{ $t->nomor_dokumen ?? '-' }}</td>
                    <td>{{ $t->user->name ?? '-' }}</td>
                    <td>{{ $t->barang->nama_barang }}</td>
                    <td class="text-center font-bold">{{ abs($t->jumlah) }} {{ $t->barang->satuan }}</td>
                    <td>{{ $t->keterangan ?? $t->permintaan?->catatan_pemohon ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">Tidak ada transaksi tercatat.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Signatures -->
    <table class="signatures">
        <tr>
            <td>
                Mengetahui,<br>
                <strong>Kepala Subbagian Umum</strong><br>
                BPS Kabupaten Cirebon
                <div class="sign-space"></div>
                <strong><u>Rina Kusuma Wardani, S.E., M.M.</u></strong><br>
                NIP. 198003202005012002
            </td>
            <td>
                Sumber, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                Penanggung Jawab / Penerima,<br>
                <strong>{{ $ruangan->nama_ruangan }}</strong>
                <div class="sign-space"></div>
                <strong><u>{{ $ruangan->penanggung_jawab ?? 'Ketua Tim / Kasubbag' }}</u></strong><br>
                NIP. {{ $ruangan->nip_penanggung_jawab ?? '....................................................' }}
            </td>
        </tr>
    </table>

</body>
</html>
