<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UC_PER53 Rincian Buku Persediaan - {{ $barang->nama_barang }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 12mm 15mm 12mm 15mm;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 8.5pt;
            color: #0f172a;
            margin: 0;
            padding: 15px 20px;
            background-color: #fff;
        }
        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 8px;
        }
        .instansi-title {
            font-size: 8.5pt;
            font-weight: bold;
            line-height: 1.25;
            color: #000;
        }
        .doc-header {
            text-align: center;
            margin: 0 0 10px 0;
        }
        .doc-title {
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0 0 3px 0;
        }
        .doc-subtitle {
            font-size: 9pt;
            font-weight: bold;
            color: #1e293b;
        }
        .meta-container {
            width: 100%;
            margin-bottom: 8px;
            font-size: 8pt;
        }
        .meta-table {
            width: 100%;
            border-collapse: collapse;
        }
        .meta-table td {
            padding: 1.5px 0;
            vertical-align: top;
        }
        .font-mono {
            font-family: "Courier New", Courier, monospace;
        }
        .font-bold {
            font-weight: bold;
        }

        /* 13 Kolom SAKTI Table */
        table.sakti-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt;
            margin-bottom: 12px;
        }
        table.sakti-table th, table.sakti-table td {
            border: 1px solid #000;
            padding: 3.5px 4px;
        }
        table.sakti-table th {
            background-color: #f1f5f9;
            text-align: center;
            font-weight: bold;
            vertical-align: middle;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }

        /* SAKTI Sub-Saldo Row */
        tr.sakti-saldo-subrow td {
            padding: 2px 4px;
            border-top: none;
            border-bottom: 1px solid #000;
            font-size: 7.8pt;
        }
        tr.sakti-tx-row td {
            border-bottom: 1px dashed #cbd5e1;
        }

        /* Signatures Section */
        .signatures-area {
            width: 100%;
            margin-top: 20px;
            page-break-inside: avoid;
        }
        .signatures-table {
            width: 100%;
            border-collapse: collapse;
        }
        .signatures-table td {
            text-align: center;
            vertical-align: top;
            width: 50%;
            font-size: 9pt;
        }
        .sign-space {
            height: 60px;
        }

        /* Page Footer */
        .page-footer {
            display: flex;
            justify-content: space-between;
            font-size: 7.5pt;
            color: #64748b;
            margin-top: 15px;
            border-top: 1px solid #e2e8f0;
            padding-top: 4px;
        }

        /* Interactive Non-Print Toolbar */
        .no-print-toolbar {
            background: #0f172a;
            color: #fff;
            padding: 10px 16px;
            margin-bottom: 15px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        }
        .toolbar-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .toolbar-btn {
            background: #334155;
            color: #fff;
            border: 1px solid #475569;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 8.5pt;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s ease;
        }
        .toolbar-btn:hover {
            background: #475569;
        }
        .toolbar-btn.active {
            background: #0d9488;
            border-color: #0f766e;
            color: #fff;
        }
        .btn-print {
            background: #059669;
            border-color: #047857;
            color: #fff;
            font-weight: bold;
        }
        .btn-print:hover {
            background: #047857;
        }

        @media print {
            .no-print-toolbar { display: none !important; }
            body { padding: 0; }
        }
    </style>
</head>
<body>

    <!-- Non-Print Interactive Toolbar -->
    <div class="no-print-toolbar">
        <div class="toolbar-group">
            <span style="font-weight: bold; font-size: 9pt; color: #38bdf8;">⚙️ Opsi Tampilan UC_PER53:</span>
            <button type="button" id="btnModeSakti" class="toolbar-btn active" onclick="switchMode('sakti')">
                📊 Standar SAKTI (13 Kolom Finansial)
            </button>
            <button type="button" id="btnModeFisik" class="toolbar-btn" onclick="switchMode('fisik')">
                📦 Mode Fisik Gudang (Kuantitas Saja)
            </button>
            <label style="display: flex; align-items: center; gap: 5px; font-size: 8.5pt; margin-left: 8px; cursor: pointer;">
                <input type="checkbox" id="chkSignatures" onchange="toggleSignatures(this.checked)">
                Tampilkan Kolom Tanda Tangan SPJ
            </label>
        </div>
        <div class="toolbar-group">
            <button onclick="window.print()" class="toolbar-btn btn-print">🖨️ Cetak / Simpan PDF</button>
            <button onclick="window.close()" class="toolbar-btn">✕ Tutup</button>
        </div>
    </div>

    <!-- Official Header Instansi BPS (SAKTI Style) -->
    <div class="header-top">
        <div style="display: flex; align-items: center; gap: 10px;">
            <img src="{{ asset('images/logo.png') }}" alt="Logo BPS" style="height: 44px; width: auto;">
            <div class="instansi-title">
                BADAN PUSAT STATISTIK<br>
                BADAN PUSAT STATISTIK PROP. JAWA BARAT<br>
                BADAN PUSAT STATISTIK KAB. CIREBON
            </div>
        </div>
        <div style="text-align: right; font-size: 8pt; color: #334155;">
            <div>Satker: <strong>428646</strong></div>
            <div>Kode UAKPB: <strong>054.01.0200.018801</strong></div>
        </div>
    </div>

    <!-- Title Section -->
    <div class="doc-header">
        <div class="doc-title">RINCIAN BUKU PERSEDIAAN</div>
        <span style="display:none;">RINCIAN BUKU PERSEDIAAN (UC_PER53)</span>
        <div class="doc-subtitle">PERIODE {{ $startDate ? \Carbon\Carbon::parse($startDate)->format('d-m-Y') : '01-01-'.($filter['tahun'] ?? date('Y')) }} S/D {{ $endDate ? \Carbon\Carbon::parse($endDate)->format('d-m-Y') : '31-12-'.($filter['tahun'] ?? date('Y')) }}</div>
    </div>

    <!-- Metadata Section 2 Columns -->
    <div class="meta-container">
        <table class="meta-table">
            <tr>
                <td style="width: 16%;"><strong>NAMA UAKPB</strong></td>
                <td style="width: 2%;">:</td>
                <td style="width: 42%;">BADAN PUSAT STATISTIK KAB. CIREBON</td>
                <td style="width: 14%;"><strong>KODE BARANG</strong></td>
                <td style="width: 2%;">:</td>
                <td style="width: 24%;" class="font-mono font-bold">{{ $barang->kode_bmn_lengkap }}</td>
            </tr>
            <tr>
                <td><strong>KODE UAKPB</strong></td>
                <td>:</td>
                <td>054.01.0200.018801</td>
                <td><strong>NAMA BARANG</strong></td>
                <td>:</td>
                <td class="font-bold">{{ $barang->nama_barang }}</td>
            </tr>
            <tr>
                <td><strong>METODE PENCATATAN</strong></td>
                <td>:</td>
                <td>PERPETUAL</td>
                <td><strong>SATUAN</strong></td>
                <td>:</td>
                <td>{{ strtoupper($barang->satuan) }}</td>
            </tr>
            <tr>
                <td><strong>METODE PENILAIAN</strong></td>
                <td>:</td>
                <td>FIFO</td>
                <td><strong>KATEGORI</strong></td>
                <td>:</td>
                <td>{{ $barang->kategori?->nama_kategori ?? 'Umum' }}</td>
            </tr>
        </table>
    </div>

    <!-- 13-Column Ledger Table (SAKTI Format) -->
    <table class="sakti-table" id="tableSakti">
        <thead>
            <tr>
                <th rowspan="2" style="width: 3%;">No</th>
                <th rowspan="2" style="width: 7.5%;">Tanggal</th>
                <th rowspan="2" style="width: 20%;">Keterangan</th>
                <th rowspan="2" style="width: 15.5%;">No Dok</th>
                <th colspan="3" class="col-finansial" style="width: 18%;">Masuk</th>
                <th colspan="3" class="col-finansial" style="width: 18%;">Keluar</th>
                <th colspan="3" class="col-finansial" style="width: 18%;">Saldo Persediaan</th>
                <!-- Alternatif mode fisik header (hidden by default) -->
                <th rowspan="2" class="col-fisik" style="width: 9%; display: none;">Masuk</th>
                <th rowspan="2" class="col-fisik" style="width: 9%; display: none;">Keluar</th>
                <th rowspan="2" class="col-fisik" style="width: 10%; display: none;">Saldo</th>
            </tr>
            <tr class="col-finansial">
                <th style="width: 4%;">Unit</th>
                <th style="width: 6%;">Harga</th>
                <th style="width: 8%;">Jumlah</th>
                <th style="width: 4%;">Unit</th>
                <th style="width: 6%;">Harga</th>
                <th style="width: 8%;">Jumlah</th>
                <th style="width: 4%;">Unit</th>
                <th style="width: 6%;">Harga</th>
                <th style="width: 8%;">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <!-- Saldo Awal -->
            <tr class="sakti-tx-row" style="background-color: #fafafa;">
                <td class="text-center font-mono">1</td>
                <td class="text-center font-mono">{{ $startDate ? \Carbon\Carbon::parse($startDate)->format('d-M-y') : '01-JAN-'.substr($filter['tahun'] ?? date('Y'), -2) }}</td>
                <td class="font-bold">Saldo Awal</td>
                <td class="text-center font-mono">—</td>
                <!-- Masuk -->
                <td class="text-right font-mono col-finansial">0</td>
                <td class="text-right font-mono col-finansial">0</td>
                <td class="text-right font-mono col-finansial">0</td>
                <!-- Keluar -->
                <td class="text-right font-mono col-finansial">0</td>
                <td class="text-right font-mono col-finansial">0</td>
                <td class="text-right font-mono col-finansial">0</td>
                <!-- Saldo -->
                <td class="text-right font-mono font-bold col-finansial">{{ number_format($saldoAwal) }}</td>
                <td class="text-right font-mono col-finansial">{{ number_format($hargaSatuan) }}</td>
                <td class="text-right font-mono font-bold col-finansial">{{ number_format($saldoAwalRp) }}</td>
                <!-- Kolom mode fisik -->
                <td class="text-right font-mono col-fisik" style="display: none;">0</td>
                <td class="text-right font-mono col-fisik" style="display: none;">0</td>
                <td class="text-right font-mono font-bold col-fisik" style="display: none;">{{ number_format($saldoAwal) }}</td>
            </tr>
            <!-- SAKTI Sub-Saldo Row for Saldo Awal -->
            <tr class="sakti-saldo-subrow col-finansial" style="background-color: #fafafa;">
                <td colspan="4" class="text-right font-bold" style="padding-right: 12px; color: #475569;">Saldo</td>
                <td colspan="6"></td>
                <td class="text-right font-mono font-bold">{{ number_format($saldoAwal) }}</td>
                <td></td>
                <td class="text-right font-mono font-bold">{{ number_format($saldoAwalRp) }}</td>
            </tr>

            <!-- Transactions List -->
            @forelse($mutasiList as $index => $m)
                <tr class="sakti-tx-row">
                    <td class="text-center font-mono">{{ $index + 2 }}</td>
                    <td class="text-center font-mono">{{ $m->tanggal->format('d-m-Y') }}</td>
                    <td>{{ $m->uraian }}</td>
                    <td class="font-mono font-bold">{{ $m->nomor_dokumen }}</td>
                    
                    <!-- Masuk -->
                    <td class="text-right font-mono col-finansial">{{ $m->masuk > 0 ? number_format($m->masuk) : '0' }}</td>
                    <td class="text-right font-mono col-finansial">{{ $m->masuk > 0 ? number_format($m->harga_satuan) : '0' }}</td>
                    <td class="text-right font-mono col-finansial">{{ $m->masuk > 0 ? number_format($m->masuk_rp) : '0' }}</td>

                    <!-- Keluar -->
                    <td class="text-right font-mono col-finansial">{{ $m->keluar > 0 ? number_format($m->keluar) : '0' }}</td>
                    <td class="text-right font-mono col-finansial">{{ $m->keluar > 0 ? number_format($m->harga_satuan) : '0' }}</td>
                    <td class="text-right font-mono col-finansial">{{ $m->keluar > 0 ? number_format($m->keluar_rp) : '0' }}</td>

                    <!-- Saldo Persediaan -->
                    <td class="text-right font-mono font-bold col-finansial">{{ number_format($m->saldo) }}</td>
                    <td class="text-right font-mono col-finansial">{{ number_format($m->harga_satuan) }}</td>
                    <td class="text-right font-mono font-bold col-finansial">{{ number_format($m->saldo_rp) }}</td>

                    <!-- Mode Fisik Columns -->
                    <td class="text-right font-mono col-fisik" style="display: none;">{{ $m->masuk > 0 ? number_format($m->masuk) : '0' }}</td>
                    <td class="text-right font-mono col-fisik" style="display: none;">{{ $m->keluar > 0 ? number_format($m->keluar) : '0' }}</td>
                    <td class="text-right font-mono font-bold col-fisik" style="display: none;">{{ number_format($m->saldo) }}</td>
                </tr>

                <!-- SAKTI Sub-Saldo Row -->
                <tr class="sakti-saldo-subrow col-finansial">
                    <td colspan="4" class="text-right font-bold" style="padding-right: 12px; color: #475569;">Saldo</td>
                    <td colspan="6"></td>
                    <td class="text-right font-mono font-bold">{{ number_format($m->saldo) }}</td>
                    <td></td>
                    <td class="text-right font-mono font-bold">{{ number_format($m->saldo_rp) }}</td>
                </tr>
            @empty
                @if($saldoAwal == 0)
                    <tr>
                        <td colspan="13" class="text-center col-finansial" style="padding: 10px;">Tidak ada transaksi mutasi persediaan pada periode ini.</td>
                        <td colspan="7" class="text-center col-fisik" style="display: none; padding: 10px;">Tidak ada transaksi mutasi persediaan pada periode ini.</td>
                    </tr>
                @endif
            @endforelse
        </tbody>
        <tfoot>
            <!-- SAKTI Summary Line ("Jumlah") -->
            <tr style="background-color: #f1f5f9; font-weight: bold;">
                <td colspan="4" class="text-center">Jumlah</td>
                <!-- Masuk Total -->
                <td class="text-right font-mono col-finansial">{{ number_format($totalMasuk) }}</td>
                <td class="text-right font-mono col-finansial">—</td>
                <td class="text-right font-mono col-finansial">{{ number_format($totalMasukRp) }}</td>

                <!-- Keluar Total -->
                <td class="text-right font-mono col-finansial">{{ number_format($totalKeluar) }}</td>
                <td class="text-right font-mono col-finansial">—</td>
                <td class="text-right font-mono col-finansial">{{ number_format($totalKeluarRp) }}</td>

                <!-- Saldo Akhir -->
                <td class="text-right font-mono font-bold col-finansial">{{ number_format($saldoAkhir) }}</td>
                <td class="text-right font-mono col-finansial">—</td>
                <td class="text-right font-mono font-bold col-finansial">{{ number_format($saldoAkhirRp) }}</td>

                <!-- Mode Fisik Total -->
                <td class="text-right font-mono col-fisik" style="display: none;">{{ number_format($totalMasuk) }}</td>
                <td class="text-right font-mono col-fisik" style="display: none;">{{ number_format($totalKeluar) }}</td>
                <td class="text-right font-mono font-bold col-fisik" style="display: none;">{{ number_format($saldoAkhir) }}</td>
            </tr>
        </tfoot>
    </table>

    <!-- Optional Signatures Section (Toggleable) -->
    <div class="signatures-area" id="signaturesArea" style="display: none;">
        <table class="signatures-table">
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
                    Pengurus Barang Pengguna / Penyimpan,<br>
                    <strong>BPS Kabupaten Cirebon</strong>
                    <div class="sign-space"></div>
                    <strong><u>Bagus Prasetyo, S.E.</u></strong><br>
                    NIP. 198705142010011003
                </td>
            </tr>
        </table>
    </div>

    <!-- Page Footer SAKTI Style -->
    <div class="page-footer">
        <div>{{ \Carbon\Carbon::now()->format('d-m-Y') }}</div>
        <div>1 dari 1</div>
    </div>

    <script>
        function switchMode(mode) {
            const finansialEls = document.querySelectorAll('.col-finansial');
            const fisikEls = document.querySelectorAll('.col-fisik');
            const btnSakti = document.getElementById('btnModeSakti');
            const btnFisik = document.getElementById('btnModeFisik');

            if (mode === 'sakti') {
                finansialEls.forEach(el => el.style.display = '');
                fisikEls.forEach(el => el.style.display = 'none');
                btnSakti.classList.add('active');
                btnFisik.classList.remove('active');
            } else {
                finansialEls.forEach(el => el.style.display = 'none');
                fisikEls.forEach(el => el.style.display = '');
                btnSakti.classList.remove('active');
                btnFisik.classList.add('active');
            }
        }

        function toggleSignatures(show) {
            const area = document.getElementById('signaturesArea');
            area.style.display = show ? 'block' : 'none';
        }
    </script>
</body>
</html>
