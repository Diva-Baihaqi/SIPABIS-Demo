<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir Permintaan {{ $permintaan->nomor_permintaan }} | BPS Kabupaten Cirebon</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10pt;
            color: #000000;
            margin: 0;
            padding: 20px;
            background-color: #f1f5f9;
        }
        .page-container {
            max-width: 820px;
            margin: 0 auto;
            background: #ffffff;
            padding: 32px 42px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08);
            border-radius: 6px;
            box-sizing: border-box;
        }

        /* Kop Surat Resmi BPS (Identik dengan form permintaan persediaan.pdf) */
        .kop-bps {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 22px;
        }
        .kop-bps img {
            height: 52px;
            width: auto;
            object-fit: contain;
        }
        .kop-text-1 {
            font-size: 13pt;
            font-weight: 800;
            letter-spacing: 0.6px;
            line-height: 1.15;
            color: #000;
        }
        .kop-text-2 {
            font-size: 13pt;
            font-weight: 800;
            letter-spacing: 0.6px;
            line-height: 1.15;
            color: #000;
        }

        /* Judul & Nomor Dokumen */
        .doc-title-container {
            text-align: center;
            margin: 0 0 16px 0;
        }
        .doc-title {
            font-size: 11.5pt;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 3px;
        }
        .doc-number {
            font-size: 10pt;
            font-family: "Courier New", Courier, monospace;
            font-weight: bold;
        }

        /* Tanggal Pembukuan */
        .tgl-pembukuan-bar {
            text-align: right;
            font-size: 10pt;
            margin-bottom: 6px;
            font-style: italic;
        }

        /* Tabel 4 Kolom Identik dengan Scan Dokumen BPS */
        table.bps-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5pt;
            border: 1.5px solid #000000;
        }
        table.bps-table th, table.bps-table td {
            border: 1px solid #000000;
            padding: 5px 8px;
            vertical-align: middle;
        }
        table.bps-table th {
            text-align: center;
            font-weight: bold;
            font-size: 9.5pt;
            background-color: #ffffff;
            height: 26px;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        .font-mono { font-family: "Courier New", Courier, monospace; }

        /* Shading baris Total */
        .total-row td {
            background-color: #cbd5e1 !important;
            font-weight: bold;
        }

        /* Formasi Tanda Tangan Berjenjang Segitiga BPS */
        .sign-section {
            width: 100%;
            margin-top: 20px;
            page-break-inside: avoid;
        }
        .sign-pemohon-wrapper {
            width: 42%;
            margin-left: auto;
            text-align: center;
            font-size: 9.5pt;
            margin-bottom: 15px;
        }
        .sign-lower-grid {
            display: flex;
            justify-content: space-between;
            width: 100%;
            margin-top: 10px;
        }
        .sign-box-left {
            width: 45%;
            text-align: center;
            font-size: 9.5pt;
            position: relative;
        }
        .sign-box-right {
            width: 45%;
            text-align: center;
            font-size: 9.5pt;
        }
        .sign-space {
            height: 55px;
        }
        .sign-name {
            font-weight: bold;
            text-decoration: underline;
        }
        .sign-nip {
            font-size: 9pt;
            margin-top: 1px;
        }

        /* Non-print toolbar */
        .no-print-bar {
            max-width: 820px;
            margin: 0 auto 16px auto;
            background: #0f172a;
            color: #ffffff;
            padding: 10px 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-radius: 8px;
            font-size: 9.5pt;
        }
        .mode-toggle-btn {
            background: #1e293b;
            color: #94a3b8;
            padding: 5px 12px;
            border-radius: 5px;
            font-size: 8.5pt;
            font-weight: bold;
            text-decoration: none;
            border: 1px solid #334155;
            cursor: pointer;
        }
        .mode-toggle-btn.active {
            background: #0284c7;
            color: #ffffff;
            border-color: #0284c7;
        }
        .btn-print {
            background: #0284c7;
            color: #ffffff;
            padding: 7px 16px;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            font-size: 9pt;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-print:hover { background: #0369a1; }
        .btn-back {
            background: #475569;
            color: #ffffff;
            padding: 7px 14px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            font-size: 9pt;
            margin-left: 8px;
        }

        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }
            .page-container {
                box-shadow: none;
                padding: 10mm 12mm;
                max-width: 100%;
                border-radius: 0;
            }
            .no-print-bar {
                display: none !important;
            }
            @page {
                size: A4 portrait;
                margin: 10mm;
            }
        }
    </style>
</head>
<body>

    @php
        $mode = request('mode', 'standar_bps');
    @endphp

    <!-- Non-Print Toolbar -->
    <div class="no-print-bar">
        <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
            <span style="font-weight: bold;">Format Cetak:</span>
            <a href="?mode=standar_bps" class="mode-toggle-btn {{ $mode === 'standar_bps' ? 'active' : '' }}">
                📄 Format Asli BPS (Scan PSD-133)
            </a>
            <a href="?mode=audit_sistem" class="mode-toggle-btn {{ $mode === 'audit_sistem' ? 'active' : '' }}">
                🔍 Format Detail Sistem (7 Kolom)
            </a>

            <!-- Opsi Nomor Dokumen: Otomatis vs Manual -->
            <div style="display: flex; align-items: center; gap: 6px; margin-left: 10px; padding-left: 10px; border-left: 1px solid #334155;">
                <span style="font-size: 8.5pt; color: #94a3b8; font-weight: bold;">Nomor Surat:</span>
                <input type="text" id="inputNomorSurat" 
                       value="{{ $permintaan->nomor_permintaan }}" 
                       oninput="updateNomorDokumen(this.value)"
                       placeholder="Ketik nomor surat manual..."
                       style="padding: 4px 8px; border-radius: 4px; font-size: 8.5pt; font-family: monospace; font-weight: bold; width: 230px; border: 1px solid #475569; background: #1e293b; color: #38bdf8;"
                       title="Ubah nomor surat secara manual jika diperlukan">
                <button type="button" onclick="setNomorOtomatis()" class="mode-toggle-btn" style="padding: 4px 8px; font-size: 8pt; background: #0369a1; color: #fff;" title="Kembalikan ke nomor otomatis sistem">
                    ↺ Otomatis
                </button>
            </div>
        </div>
        <div style="display: flex; align-items: center; gap: 8px;">
            <button onclick="window.print()" class="btn-print">🖨️ Cetak / Simpan PDF</button>
            <a href="javascript:history.back()" class="btn-back">&larr; Kembali</a>
        </div>
    </div>

    <!-- Printable Official Page -->
    <div class="page-container">
        
        <!-- Kop Resmi BPS (Pojok Kiri Atas Identik Scan BPS) -->
        <div class="kop-bps">
            <img src="{{ asset('images/logo.png') }}" alt="Logo BPS">
            <div>
                <div class="kop-text-1">BADAN PUSAT STATISTIK</div>
                <div class="kop-text-2">KABUPATEN CIREBON</div>
                <span style="display:none;">BADAN PUSAT STATISTIK KABUPATEN CIREBON</span>
            </div>
        </div>

        <!-- Document Title & PSD Reference -->
        <div class="doc-title-container">
            <div class="doc-title">FORMULIR PERMINTAAN BARANG PERSEDIAAN</div>
            <div class="doc-number">
                Nomor : <span id="displayNomorDokumen" contenteditable="true" onblur="syncInputFromContent(this.innerText)" title="Klik langsung untuk mengedit nomor surat ini">{{ $permintaan->nomor_permintaan }}</span>
            </div>
        </div>

        <!-- Tanggal Pembukuan (Sebelah Kanan Atas Sebelum Tabel) -->
        <div class="tgl-pembukuan-bar">
            Tanggal Pembukuan : {{ $permintaan->tanggal_pengajuan ? $permintaan->tanggal_pengajuan->translatedFormat('d F Y') : date('d F Y') }}
        </div>

        @if($mode === 'standar_bps')
            <!-- TABEL 4 KOLOM IDENTIK 100% DENGAN FORM PERMINTAAN PSD-133 BPS -->
            <table class="bps-table">
                <thead>
                    <tr>
                        <th style="width: 6%;">No</th>
                        <th style="width: 54%;">Nama Barang</th>
                        <th style="width: 18%;">Jumlah</th>
                        <th style="width: 22%;">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @php 
                        $totalUnit = 0; 
                        $itemCount = $permintaan->details->count();
                        // Buat grid baris kosong hingga minimal 14 baris agar tampil seperti kertas formulir fisik BPS
                        $targetRows = max(14, $itemCount);
                    @endphp

                    @foreach($permintaan->details as $index => $detail)
                        @php 
                            $qty = ($permintaan->status === 'disetujui' || $permintaan->status === 'selesai') 
                                ? ($detail->jumlah_disetujui ?? $detail->jumlah_diminta) 
                                : $detail->jumlah_diminta;
                            $totalUnit += $qty;
                        @endphp
                        <tr>
                            <td class="text-center font-bold" style="height: 24px;">{{ $index + 1 }}.</td>
                            <td>{{ $detail->barang->nama_barang ?? '-' }}</td>
                            <td class="text-center font-bold">
                                {{ $qty }} {{ ucfirst($detail->barang->satuan ?? 'Unit') }}
                            </td>
                            <td class="text-center">
                                {{ $detail->catatan ?: ($permintaan->ruangan->nama_ruangan ?? '-') }}
                            </td>
                        </tr>
                    @endforeach

                    <!-- Baris Garis Kosong Pengisi (Grid) Sesuai Formulir Asli -->
                    @for($i = $itemCount; $i < $targetRows; $i++)
                        <tr>
                            <td style="height: 22px;">&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                    @endfor
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <td colspan="2" class="text-center font-bold" style="letter-spacing: 1px; height: 26px;">Jumlah</td>
                        <td class="text-center font-bold font-mono" style="font-size: 10.5pt;">{{ $totalUnit }}</td>
                        <td>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>

        @else
            <!-- FORMAT 2: AUDIT SISTEM LENGKAP DENGAN KODE BARANG & STATUS PER ITEM -->
            <table class="bps-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 18%;">Kode Barang</th>
                        <th style="width: 37%;">Nama Barang</th>
                        <th style="width: 10%;">Satuan</th>
                        <th style="width: 10%;">Diminta</th>
                        <th style="width: 10%;">Disetujui</th>
                        <th style="width: 10%;">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totalDiminta = 0; $totalDisetujui = 0; @endphp
                    @foreach($permintaan->details as $index => $detail)
                        @php 
                            $totalDiminta += $detail->jumlah_diminta;
                            $totalDisetujui += ($detail->jumlah_disetujui ?? 0);
                        @endphp
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="font-mono text-center">{{ $detail->barang->kode_barang ?? '-' }}</td>
                            <td>{{ $detail->barang->nama_barang ?? '-' }}</td>
                            <td class="text-center">{{ $detail->barang->satuan ?? '-' }}</td>
                            <td class="text-center font-bold">{{ $detail->jumlah_diminta }}</td>
                            <td class="text-center font-bold">{{ $detail->jumlah_disetujui ?? '-' }}</td>
                            <td class="text-center">{{ $permintaan->status }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <td colspan="4" class="text-center font-bold">Total Jumlah Barang</td>
                        <td class="text-center font-bold">{{ $totalDiminta }}</td>
                        <td class="text-center font-bold">{{ $totalDisetujui }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        @endif

        <!-- Formasi 3 Tanda Tangan Segitiga Berjenjang (Persis Scan form permintaan persediaan.pdf) -->
        <div class="sign-section">
            
            <!-- Tanda Tangan 1: Pemohon di Kanan Atas -->
            <div class="sign-pemohon-wrapper">
                <div>Cirebon, {{ $permintaan->tanggal_pengajuan ? $permintaan->tanggal_pengajuan->translatedFormat('d F Y') : date('d F Y') }}</div>
                <div style="font-weight: 600; margin-top: 2px;">Pemohon,</div>
                <div class="sign-space"></div>
                <div class="sign-name">{{ $permintaan->user->name }}</div>
                <div class="sign-nip">NIP. {{ $permintaan->user->nip }}</div>
            </div>

            <!-- Tanda Tangan 2 & 3: Mengetahui (Kiri Bawah) & Operator Persediaan (Kanan Bawah) -->
            <div class="sign-lower-grid">
                
                <!-- Mengetahui Kasubbag Umum -->
                <div class="sign-box-left">
                    <div style="font-weight: 600;">Mengetahui:</div>
                    <div style="font-weight: 600; margin-bottom: 2px;">Kepala Sub Bagian Umum</div>
                    <div class="sign-space" style="height: 55px;"></div>
                    <div class="sign-name">Rina Kusuma Wardani, S.E., M.M.</div>
                    <div class="sign-nip">NIP. 198003202005012002</div>
                </div>

                <!-- Operator Persediaan -->
                <div class="sign-box-right">
                    <div>Diserahkan pada tgl: {{ $permintaan->tanggal_disetujui ? $permintaan->tanggal_disetujui->translatedFormat('d F Y') : ($permintaan->tanggal_pengajuan ? $permintaan->tanggal_pengajuan->translatedFormat('d F Y') : date('d F Y')) }}</div>
                    <div style="font-weight: 600; margin-bottom: 2px;">Operator Persediaan,</div>
                    <div class="sign-space"></div>
                    <div class="sign-name">Bagus Prasetyo, S.E.</div>
                    <div class="sign-nip">NIP. 198705142010011003</div>
                </div>

            </div>

        </div>

    </div>

    <script>
        const defaultNomor = @json($permintaan->nomor_permintaan);

        function updateNomorDokumen(val) {
            document.getElementById('displayNomorDokumen').innerText = val;
        }

        function syncInputFromContent(val) {
            document.getElementById('inputNomorSurat').value = val.trim();
        }

        function setNomorOtomatis() {
            document.getElementById('inputNomorSurat').value = defaultNomor;
            document.getElementById('displayNomorDokumen').innerText = defaultNomor;
        }
    </script>
</body>
</html>
