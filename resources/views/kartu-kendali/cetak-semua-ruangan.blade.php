<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku Kendali Semua Unit Kerja BPS Kabupaten Cirebon - Tahun {{ $filter['tahun'] ?? date('Y') }}</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10.5pt;
            color: #1e293b;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
            margin-bottom: 16px;
        }
        .doc-title {
            text-align: center;
            margin: 14px 0 6px 0;
            font-size: 12pt;
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
        }
        .doc-subtitle {
            text-align: center;
            margin-bottom: 15px;
            font-size: 10pt;
            font-weight: bold;
            color: #475569;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 15px;
            font-size: 9.5pt;
        }
        .meta-table td {
            padding: 2.5px 0;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
            font-size: 9pt;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #000;
            padding: 5px 7px;
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
            margin-top: 30px;
            page-break-inside: avoid;
        }
        .signatures td {
            text-align: center;
            vertical-align: top;
            width: 50%;
            font-size: 9.5pt;
        }
        .sign-space {
            height: 60px;
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
        .room-divider {
            border-top: 2px dashed #94a3b8;
            margin: 30px 0;
            padding-top: 20px;
        }
        @media print {
            .no-print-bar { display: none !important; }
            body { padding: 0; }
            .page-break { page-break-before: always; }
            .room-divider { border-top: none; margin: 0; padding-top: 0; }
        }
    </style>
</head>
<body>

    <!-- Non-Print Toolbar -->
    <div class="no-print-bar">
        <div>
            <strong style="font-size: 11pt;">Pratinjau Cetak Resmi: Buku Kendali Seluruh Unit Kerja (7 Divisi)</strong>
            <span style="font-size: 9pt; display: block; opacity: 0.8;">
                Periode: {{ !empty($filter['bulan']) ? ($bulanList[$filter['bulan']] ?? $filter['bulan']) . ' ' : '' }}Tahun {{ $filter['tahun'] ?? date('Y') }}
            </span>
        </div>
        <div>
            <button onclick="window.print()" class="btn-print">🖨️ Cetak / Simpan PDF</button>
            <button onclick="window.close()" style="margin-left: 8px; padding: 8px 14px; background: #475569; color: #fff; border: none; border-radius: 6px; cursor: pointer;">Tutup</button>
        </div>
    </div>

    <!-- HALAMAN 1: REKAPITULASI PEMAKAIAN KANTOR (SEMUA DIVISI) -->
    <div class="header" style="display: flex; align-items: center; justify-content: center; gap: 15px;">
        <img src="{{ asset('images/logo.png') }}" style="height: 52px; width: auto;" alt="Logo BPS">
        <div>
            <h3 style="margin: 0; font-size: 13pt; font-weight: 800;">BADAN PUSAT STATISTIK KABUPATEN CIREBON</h3>
            <p style="margin: 2px 0; font-size: 8.5pt; color: #475569;">Jl. Sunan Rahmat No. 7, Sumber, Kec. Sumber, Kabupaten Cirebon, Jawa Barat 45611</p>
            <p style="margin: 2px 0; font-size: 8.5pt; color: #475569;">Telepon: (0231) 321151 • Email: bps3209@bps.go.id • Satker: 428646 (UAKPB: 054.01.0200.018801)</p>
        </div>
    </div>

    <div class="doc-title">BUKU KENDALI PENGELUARAN BARANG PAKAI HABIS</div>
    <div class="doc-subtitle">REKAPITULASI DISTRIBUSI ATK SELURUH UNIT KERJA / DIVISI</div>

    <table class="meta-table">
        <tr>
            <td style="width: 20%;"><strong>Satuan Kerja</strong></td>
            <td style="width: 2%;">:</td>
            <td style="width: 48%;"><strong>BPS KABUPATEN CIREBON</strong> (428646)</td>
            <td style="width: 15%;"><strong>Kode UAKPB</strong></td>
            <td style="width: 2%;">:</td>
            <td style="width: 13%;">054.01.0200.018801</td>
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
            <td><strong>Tanggal Cetak</strong></td>
            <td>:</td>
            <td>{{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</td>
        </tr>
    </table>

    <p style="font-weight: bold; margin-bottom: 6px; font-size: 10pt;">RINGKASAN PEMAKAIAN ATK PER UNIT KERJA (7 DIVISI):</p>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 15%;">Kode Ruangan</th>
                <th>Nama Unit Kerja / Divisi</th>
                <th style="width: 25%;">Penanggung Jawab</th>
                <th style="width: 12%;">Pengambilan</th>
                <th style="width: 12%;">Variasi ATK</th>
                <th style="width: 15%;">Total Fisik Keluar</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rekapSemuaRuangan as $idx => $r)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="text-center font-bold">{{ $r->kode_ruangan }}</td>
                    <td><strong>{{ $r->nama_ruangan }}</strong></td>
                    <td>{{ $r->penanggung_jawab ?? 'Kasubbag / Ketua Tim' }}</td>
                    <td class="text-center">{{ number_format($r->total_transaksi) }} kali</td>
                    <td class="text-center">{{ number_format($r->total_jenis_barang) }} jenis</td>
                    <td class="text-right font-bold">{{ number_format($r->total_unit) }} Unit</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #f8fafc; font-weight: bold;">
                <td colspan="4" class="text-right">TOTAL KESELURUHAN DISTRIBUSI KANTOR BPS:</td>
                <td class="text-center">{{ number_format($totalTransaksiSemua) }} kali</td>
                <td class="text-center">-</td>
                <td class="text-right" style="color: #0f172a;">{{ number_format($totalUnitSemua) }} Unit</td>
            </tr>
        </tfoot>
    </table>

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
                Pengelola BMN / Petugas Gudang Persediaan
                <div class="sign-space"></div>
                <strong><u>Pengadministrasi Persediaan BPS</u></strong><br>
                NIP. ....................................................
            </td>
        </tr>
    </table>

    <!-- HALAMAN BERIKUTNYA: DETAIL SETIAP RUANGAN DENGAN PAGE BREAK -->
    @foreach($ruangans as $rIndex => $ruangan)
        @php
            $dataR = $dataPerRuangan[$ruangan->id] ?? [];
            $rekapBarangR = $dataR['rekapBarang'] ?? collect([]);
            $transaksiListR = $dataR['transaksiList'] ?? collect([]);
        @endphp

        <div class="page-break room-divider"></div>

        <!-- Header Kop Tiap Divisi -->
        <div class="header" style="display: flex; align-items: center; justify-content: center; gap: 15px;">
            <img src="{{ asset('images/logo.png') }}" style="height: 48px; width: auto;" alt="Logo BPS">
            <div>
                <h3 style="margin: 0; font-size: 12pt; font-weight: 800;">BADAN PUSAT STATISTIK KABUPATEN CIREBON</h3>
                <p style="margin: 2px 0; font-size: 8pt; color: #475569;">Jl. Sunan Rahmat No. 7, Sumber, Kec. Sumber, Kabupaten Cirebon, Jawa Barat 45611</p>
                <p style="margin: 2px 0; font-size: 8pt; color: #475569;">Telepon: (0231) 321151 • Satker: 428646 (UAKPB: 054.01.0200.018801)</p>
            </div>
        </div>

        <div class="doc-title" style="font-size: 11pt;">BUKU KENDALI PENGELUARAN BARANG PAKAI HABIS</div>
        <div class="doc-subtitle" style="font-size: 9.5pt;">UNIT KERJA: {{ strtoupper($ruangan->nama_ruangan) }} ({{ $ruangan->kode_ruangan }})</div>

        <!-- Meta Ruangan -->
        <table class="meta-table">
            <tr>
                <td style="width: 20%;"><strong>Unit Kerja</strong></td>
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
                <td><strong>Periode</strong></td>
                <td>:</td>
                <td>
                    @if(!empty($filter['bulan']))
                        {{ $bulanList[$filter['bulan']] ?? $filter['bulan'] }}
                    @endif
                    Tahun {{ $filter['tahun'] ?? date('Y') }}
                </td>
            </tr>
        </table>

        <!-- Bagian I: Rekap Barang Ruangan -->
        <p style="font-weight: bold; margin-bottom: 5px; font-size: 9.5pt;">I. REKAPITULASI PENGAMBILAN ATK PER JENIS BARANG</p>
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
                @forelse($rekapBarangR as $bIdx => $item)
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
                    <td class="text-right">{{ number_format($dataR['totalUnitKeluar'] ?? 0) }} Unit</td>
                </tr>
            </tfoot>
        </table>

        <!-- Bagian II: Rincian Transaksi Ruangan -->
        <p style="font-weight: bold; margin-bottom: 5px; font-size: 9.5pt; margin-top: 15px;">II. RINCIAN KRONOLOGIS TRANSAKSI PENGELUARAN FISIK</p>
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
                @forelse($transaksiListR as $tIdx => $t)
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

        <!-- Tanda Tangan Per Divisi -->
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
    @endforeach

</body>
</html>
