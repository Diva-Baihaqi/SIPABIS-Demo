<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berita Acara Stock Opname ({{ strtoupper($format) }}) - {{ $opname->nomor_opname }}</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9.5pt;
            color: #0f172a;
            margin: 0;
            padding: 20px;
            background-color: #f8fafc;
        }
        .page-container {
            max-width: {{ $format === 'neraca' ? '1100px' : '900px' }};
            margin: 0 auto;
            background: #ffffff;
            padding: 30px 40px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            border-radius: 8px;
        }
        .header-section {
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
            margin-bottom: 15px;
            text-align: center;
        }
        .header-section h3 {
            font-size: 13pt;
            margin: 2px 0;
            text-transform: uppercase;
            font-weight: 800;
        }
        .header-section p {
            font-size: 8.5pt;
            margin: 2px 0;
            color: #475569;
        }
        .table-label {
            font-size: 11pt;
            font-weight: bold;
            margin-bottom: 2px;
            text-transform: uppercase;
        }
        .doc-title {
            text-align: center;
            font-size: 12pt;
            font-weight: bold;
            margin: 10px 0 3px 0;
            text-decoration: underline;
            text-transform: uppercase;
        }
        .doc-number {
            text-align: center;
            font-size: 10pt;
            font-family: monospace;
            font-weight: bold;
            margin-bottom: 18px;
        }
        .opening-text {
            text-align: justify;
            line-height: 1.6;
            margin-bottom: 16px;
            font-size: 9.5pt;
        }
        table.opname-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
            margin-bottom: 18px;
        }
        table.opname-table th, table.opname-table td {
            border: 1px solid #000;
            padding: 5px 6px;
        }
        table.opname-table th {
            background-color: #f1f5f9;
            text-align: center;
            font-weight: bold;
            vertical-align: middle;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-mono { font-family: monospace; }
        .font-bold { font-weight: bold; }
        .closing-text {
            margin-top: 15px;
            line-height: 1.6;
            font-size: 9.5pt;
        }
        .signatures {
            width: 100%;
            margin-top: 30px;
            page-break-inside: avoid;
            border-collapse: collapse;
        }
        .signatures td {
            text-align: center;
            vertical-align: top;
            font-size: 9.5pt;
            padding: 4px;
        }
        .sign-space {
            height: 60px;
        }
        .stamp-box {
            display: inline-block;
            border: 2px solid #059669;
            color: #059669;
            padding: 4px 10px;
            font-size: 8pt;
            font-weight: bold;
            text-transform: uppercase;
            border-radius: 4px;
            transform: rotate(-3deg);
            margin-top: 5px;
        }
        .no-print-bar {
            max-width: {{ $format === 'neraca' ? '1100px' : '900px' }};
            margin: 0 auto 16px auto;
            background: #0f172a;
            color: #fff;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-radius: 8px;
            font-size: 9.5pt;
        }
        .format-tabs {
            display: flex;
            gap: 6px;
            background: #1e293b;
            padding: 3px;
            border-radius: 6px;
        }
        .format-tab {
            padding: 5px 12px;
            border-radius: 4px;
            color: #cbd5e1;
            text-decoration: none;
            font-size: 8.5pt;
            font-weight: bold;
            transition: all 0.2s;
        }
        .format-tab.active {
            background: #0284c7;
            color: #ffffff;
        }
        .format-tab.active-filter {
            background: #059669;
            color: #ffffff;
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
        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }
            .page-container {
                box-shadow: none;
                padding: 0;
                max-width: 100%;
                border-radius: 0;
            }
            .no-print-bar { display: none !important; }
            @page {
                size: {{ $format === 'neraca' ? 'A4 landscape' : 'A4 portrait' }};
                margin: 12mm;
            }
        }
    </style>
</head>
<body>

    <!-- Non-Print Toolbar -->
    <div class="no-print-bar">
        <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
            <span><strong>Format:</strong></span>
            <div class="format-tabs">
                <a href="{{ route('stok-opname.cetak', [$opname->id, 'format' => 'neraca', 'filter' => $filter]) }}" 
                   class="format-tab {{ $format === 'neraca' ? 'active' : '' }}">
                   Neraca SAKTI
                </a>
                <a href="{{ route('stok-opname.cetak', [$opname->id, 'format' => 'fisik', 'filter' => $filter]) }}" 
                   class="format-tab {{ $format === 'fisik' ? 'active' : '' }}">
                   Komparasi Fisik
                </a>
            </div>

            <span style="margin-left: 6px;"><strong>Cakupan Data:</strong></span>
            <div class="format-tabs">
                <a href="{{ route('stok-opname.cetak', [$opname->id, 'format' => $format, 'filter' => 'ada_stok']) }}" 
                   class="format-tab {{ $filter === 'ada_stok' ? 'active-filter' : '' }}"
                   title="Versi ringkas: hanya barang yang memiliki stok fisik atau nilai nominal > 0">
                   ✨ Hanya Barang Ada Stok / Nominal ({{ $totalItem }})
                </a>
                <a href="{{ route('stok-opname.cetak', [$opname->id, 'format' => $format, 'filter' => 'semua']) }}" 
                   class="format-tab {{ $filter === 'semua' ? 'active-filter' : '' }}"
                   title="Versi lengkap: menyertakan seluruh master barang persediaan">
                   Semua Barang ({{ $totalItemSemua }})
                </a>
            </div>

            @if($format === 'neraca')
                <label style="display: flex; align-items: center; gap: 6px; font-size: 8.5pt; color: #cbd5e1; cursor: pointer; margin-left: 6px;">
                    <input type="checkbox" id="chkPetunjuk" checked onchange="document.getElementById('petunjukCaraPengisian').style.display = this.checked ? 'block' : 'none'">
                    Petunjuk Teknis
                </label>
            @endif
        </div>
        <div style="display: flex; align-items: center; gap: 8px;">
            <button onclick="window.print()" class="btn-print">🖨️ Cetak / Simpan PDF</button>
            <button onclick="window.close()" style="padding: 6px 12px; background: #475569; color: #fff; border: none; border-radius: 6px; cursor: pointer; font-size: 9pt;">Tutup</button>
        </div>
    </div>

    <div class="page-container">

        <!-- Official Header BPS -->
        <div class="header-section" style="display: flex; align-items: center; justify-content: center; gap: 16px;">
            <img src="{{ asset('images/logo.png') }}" style="height: 54px; width: auto;" alt="Logo BPS">
            <div>
                <h3 style="margin: 0; font-size: 13pt; font-weight: 800; letter-spacing: 0.5px;">BADAN PUSAT STATISTIK KABUPATEN CIREBON</h3>
                <p style="margin: 2px 0; font-size: 8.5pt; color: #475569;">Jl. Sunan Rahmat No. 7, Sumber, Kec. Sumber, Kabupaten Cirebon, Jawa Barat 45611</p>
                <p style="margin: 2px 0; font-size: 8.5pt; color: #475569;">Telepon: (0231) 321151 • Email: bps3209@bps.go.id • Satker: 428646 • Kode UAKPB: 054.01.0200.018801</p>
            </div>
        </div>

        @if($format === 'neraca')
            <!-- FORMAT 1: NERACA FINANSIAL BPS (Sesuai Berita Acara Stock Opname.xlsx Tabel 1) -->
            <div style="text-align: center; margin-bottom: 12px;">
                <div class="table-label">TABEL 1</div>
                <div class="doc-title" style="margin-top: 2px;">BERITA ACARA HASIL OPNAME PHISIK (STOCK OPNAME) PERSEDIAAN</div>
                <div class="doc-number">Nomor: {{ $opname->nomor_opname }}</div>
            </div>

            <div class="opening-text">
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Pada hari ini <strong>{{ $terbilangHari }}</strong> Tanggal <strong>{{ $terbilangTgl }}</strong> Bulan <strong>{{ $terbilangBulan }}</strong> Tahun <strong>{{ $terbilangTahun }}</strong>, kami telah melaksanakan opname phisik barang persediaan dengan hasil rincian sebagai berikut :
            </div>

            <!-- Tabel Neraca 10 Kolom Standar BPS -->
            <table class="opname-table">
                <thead>
                    <tr>
                        <th rowspan="2" style="width: 4%;">No.</th>
                        <th rowspan="2">Uraian Barang</th>
                        <th rowspan="2" style="width: 7%;">Satuan</th>
                        <th rowspan="2" style="width: 11%;">Harga Beli Satuan (Rupiah)</th>
                        <th colspan="2" style="width: 22%;">Total Persediaan</th>
                        <th colspan="2" style="width: 18%;">Barang Rusak</th>
                        <th colspan="2" style="width: 18%;">Barang Usang</th>
                    </tr>
                    <tr>
                        <th style="width: 7%;">Jumlah</th>
                        <th style="width: 15%;">Harga Total (Rupiah)</th>
                        <th style="width: 6%;">Jumlah</th>
                        <th style="width: 12%;">Harga Total (Rupiah)</th>
                        <th style="width: 6%;">Jumlah</th>
                        <th style="width: 12%;">Harga Total (Rupiah)</th>
                    </tr>
                    <tr style="background-color: #f8fafc; font-size: 7.5pt; color: #64748b;">
                        <th>(1)</th>
                        <th>(2)</th>
                        <th>(3)</th>
                        <th>(4)</th>
                        <th>(5)</th>
                        <th>(6) = (4) x (5)</th>
                        <th>(7)</th>
                        <th>(8) = (4) x (7)</th>
                        <th>(9)</th>
                        <th>(10) = (4) x (9)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="background-color: #f1f5f9; font-weight: bold;">
                        <td colspan="10">Persediaan Reguler</td>
                    </tr>
                    @foreach($opname->details as $index => $item)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $item->barang->nama_barang }}</td>
                            <td class="text-center">{{ $item->barang->satuan }}</td>
                            <td class="text-right font-mono">{{ number_format($item->harga_beli_satuan, 0, ',', '.') }}</td>
                            
                            <!-- Total Persediaan -->
                            <td class="text-center font-mono">{{ number_format($item->stok_fisik) }}</td>
                            <td class="text-right font-mono">{{ number_format($item->total_persediaan_rp, 0, ',', '.') }}</td>

                            <!-- Barang Rusak -->
                            <td class="text-center font-mono">{{ $item->rusak_qty > 0 ? $item->rusak_qty : '-' }}</td>
                            <td class="text-right font-mono">{{ $item->rusak_rp > 0 ? number_format($item->rusak_rp, 0, ',', '.') : '-' }}</td>

                            <!-- Barang Usang -->
                            <td class="text-center font-mono">{{ $item->usang_qty > 0 ? $item->usang_qty : '-' }}</td>
                            <td class="text-right font-mono">{{ $item->usang_rp > 0 ? number_format($item->usang_rp, 0, ',', '.') : '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="font-weight: bold; background-color: #f1f5f9;">
                        <td colspan="4" class="text-center uppercase">Total</td>
                        <td class="text-center font-mono">{{ number_format($opname->details->sum('stok_fisik')) }}</td>
                        <td class="text-right font-mono">{{ number_format($totalNilaiPersediaan, 0, ',', '.') }}</td>
                        <td class="text-center font-mono">{{ $opname->details->sum('rusak_qty') > 0 ? number_format($opname->details->sum('rusak_qty')) : '-' }}</td>
                        <td class="text-right font-mono">{{ $totalNilaiRusak > 0 ? number_format($totalNilaiRusak, 0, ',', '.') : '-' }}</td>
                        <td class="text-center font-mono">{{ $opname->details->sum('usang_qty') > 0 ? number_format($opname->details->sum('usang_qty')) : '-' }}</td>
                        <td class="text-right font-mono">{{ $totalNilaiUsang > 0 ? number_format($totalNilaiUsang, 0, ',', '.') : '-' }}</td>
                    </tr>
                    <tr style="font-weight: 800; background-color: #e2e8f0; font-size: 9pt;">
                        <td colspan="5" class="text-left">
                            Saldo Persediaan di Neraca = Total Persediaan - Barang Rusak - Barang Usang = (11) - (12) - (13)
                        </td>
                        <td class="text-right font-mono" style="color: #0369a1;">
                            Rp {{ number_format($saldoNeracaTotal, 0, ',', '.') }}
                        </td>
                        <td colspan="4"></td>
                    </tr>
                </tfoot>
            </table>

            <!-- Signatures Berita Acara Excel BPS -->
            <table class="signatures">
                <tr>
                    <td style="width: 50%;"></td>
                    <td style="width: 50%; font-size: 9.5pt;">
                        Cirebon, {{ \Carbon\Carbon::parse($opname->tanggal_opname)->translatedFormat('d F Y') }}
                    </td>
                </tr>
                <tr>
                    <td style="width: 50%;">
                        Mengetahui:<br>
                        <strong>Penanggungjawab KPB</strong><br>
                        <strong>Kepala BPS Kabupaten Cirebon,</strong>
                        @if($opname->status === 'disahkan')
                            <div><span class="stamp-box">✓ Telah Disahkan Digital</span></div>
                        @endif
                        <div class="sign-space" style="{{ $opname->status === 'disahkan' ? 'height: 40px;' : '' }}"></div>
                        <strong><u>{{ $opname->pimpinan->name ?? 'HENDRA WIJAYA' }}</u></strong><br>
                        NIP. {{ $opname->pimpinan->nip ?? '197801152002121001' }}
                    </td>
                    <td style="width: 50%;">
                        <br>
                        <strong>Penyimpan Barang,</strong><br><br>
                        <div class="sign-space"></div>
                        <strong><u>Bagus Prasetyo, S.E.</u></strong><br>
                        NIP. 198705142010011003
                    </td>
                </tr>
            </table>

            <!-- Petunjuk Teknis Cara Pengisian (Sesuai Berita Acara Stock Opname.xlsx Baris 96-116) -->
            <div id="petunjukCaraPengisian" style="margin-top: 25px; font-size: 8pt; color: #475569; page-break-inside: avoid; border-top: 1px dashed #cbd5e1; padding-top: 10px;">
                <div style="font-weight: bold; margin-bottom: 5px; color: #1e293b; font-size: 8.5pt;">Cara Pengisian</div>
                <table style="width: 100%; border-collapse: collapse; font-size: 7.8pt;">
                    <tr><td style="width: 32px; vertical-align: top; padding: 1.5px 0;">(1).</td><td style="padding: 1.5px 0;">Diisi dengan nomor urut</td></tr>
                    <tr><td style="vertical-align: top; padding: 1.5px 0;">(2).</td><td style="padding: 1.5px 0;">Diisi dengan uraian klasifikasi barang</td></tr>
                    <tr><td style="vertical-align: top; padding: 1.5px 0;">(3).</td><td style="padding: 1.5px 0;">Diisi dengan satuan barang biasanya barang dikeluarkan/dimasukkan</td></tr>
                    <tr><td style="vertical-align: top; padding: 1.5px 0;">(4).</td><td style="padding: 1.5px 0;">Diisi dengan harga pembelian terakhir persediaan (dalam rupiah per satuan), berdasarkan faktur pembelian atau dokumen sumber lainnya</td></tr>
                    <tr><td style="vertical-align: top; padding: 1.5px 0;">(5).</td><td style="padding: 1.5px 0;">Diisi dengan kuantitas/jumlah barang keseluruhan yang ditemukan pada opname phisik (dalam satuan) baik barang dalam kondisi baik, rusak atau usang</td></tr>
                    <tr><td style="vertical-align: top; padding: 1.5px 0;">(6).</td><td style="padding: 1.5px 0;">Diisi dengan harga total persediaan (Kolom 4 X Kolom 5)</td></tr>
                    <tr><td style="vertical-align: top; padding: 1.5px 0;">(7).</td><td style="padding: 1.5px 0;">Diisi dengan jumlah barang yang rusak yang ditemukan pada opname phisik (dalam satuan)</td></tr>
                    <tr><td style="vertical-align: top; padding: 1.5px 0;">(8).</td><td style="padding: 1.5px 0;">Diisi dengan harga total persediaan yang rusak (Kolom 4 X Kolom 7)</td></tr>
                    <tr><td style="vertical-align: top; padding: 1.5px 0;">(9).</td><td style="padding: 1.5px 0;">Diisi dengan jumlah barang yang usang yang ditemukan pada opname phisik (dalam satuan)</td></tr>
                    <tr><td style="vertical-align: top; padding: 1.5px 0;">(10).</td><td style="padding: 1.5px 0;">Diisi dengan harga total persediaan yang usang (Kolom 4 X Kolom 9)</td></tr>
                    <tr><td style="vertical-align: top; padding: 1.5px 0;">(11).</td><td style="padding: 1.5px 0;">Diisi dengan jumlah harga total persediaan (Jumlah Kolom 6)</td></tr>
                    <tr><td style="vertical-align: top; padding: 1.5px 0;">(12).</td><td style="padding: 1.5px 0;">Diisi dengan jumlah harga total barang rusak (Jumlah Kolom 8), seharusnya sesuai dengan nilai persediaan satker yang kondisinya rusak per akhir semester pada Aplikasi Persediaan</td></tr>
                    <tr><td style="vertical-align: top; padding: 1.5px 0;">(13).</td><td style="padding: 1.5px 0;">Diisi dengan Jumlah harga total barang usang (Jumlah Kolom 10), seharusnya sesuai dengan nilai persediaan satker yang kondisinya usang per akhir semester pada Aplikasi Persediaan</td></tr>
                    <tr><td style="vertical-align: top; padding: 1.5px 0;">(14).</td><td style="padding: 1.5px 0;">Diisi dengan Jumlah harga total persediaan setelah dikurangi dengan jumlah harga total barang rusak dan usang, seharusnya sesuai dengan nilai persediaan satker per akhir semester pada Aplikasi Persediaan</td></tr>
                    <tr><td style="vertical-align: top; padding: 1.5px 0;">(15).</td><td style="padding: 1.5px 0;">Diisi dengan nama tempat dan tanggal pelaporan</td></tr>
                    <tr><td style="vertical-align: top; padding: 1.5px 0;">(16).</td><td style="padding: 1.5px 0;">Diisi dengan jabatan, nama, NIP dan tanda tangan penanggungjawab UAKPB (Unit Akuntansi Kuasa Pengguna Barang)</td></tr>
                    <tr><td style="vertical-align: top; padding: 1.5px 0;">(17).</td><td style="padding: 1.5px 0;">Diisi dengan nama, NIP dan tanda tangan pengelola persediaan</td></tr>
                </table>
            </div>

        @else
            <!-- FORMAT 2: KOMPARASI FISIK INTERNAL -->
            <div class="doc-title">BERITA ACARA HASIL OPNAME PHISIK (STOCK OPNAME) PERSEDIAAN</div>
            <div class="doc-number">Nomor: {{ $opname->nomor_opname }}</div>

            <div class="opening-text">
                Pada hari ini <strong>{{ $terbilangHari }}</strong>, tanggal <strong>{{ \Carbon\Carbon::parse($opname->tanggal_opname)->translatedFormat('d F Y') }}</strong>, kami yang bertanda tangan di bawah ini Tim Pemeriksa Fisik Persediaan BPS Kabupaten Cirebon telah melaksanakan opname phisik barang persediaan pakai habis periode <strong>{{ $opname->periode }}</strong> dengan rincian hasil sebagai berikut :
            </div>

            <!-- Comparison Table -->
            <table class="opname-table">
                <thead>
                    <tr>
                        <th style="width: 4%;">No.</th>
                        <th style="width: 18%;">Kode Barang</th>
                        <th>Uraian Barang Persediaan</th>
                        <th style="width: 10%;">Satuan</th>
                        <th style="width: 12%;">Stok Sistem</th>
                        <th style="width: 12%;">Opname Fisik</th>
                        <th style="width: 10%;">Selisih</th>
                        <th style="width: 18%;">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($opname->details as $index => $item)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="font-mono text-center">{{ $item->barang->kode_barang }}</td>
                            <td>{{ $item->barang->nama_barang }}</td>
                            <td class="text-center">{{ $item->barang->satuan }}</td>
                            <td class="text-center font-mono">{{ number_format($item->stok_sistem) }}</td>
                            <td class="text-center font-mono font-bold">{{ number_format($item->stok_fisik) }}</td>
                            <td class="text-center font-mono font-bold">
                                @if($item->selisih > 0)
                                    +{{ $item->selisih }}
                                @elseif($item->selisih < 0)
                                    {{ $item->selisih }}
                                @else
                                    0
                                @endif
                            </td>
                            <td>{{ $item->keterangan_selisih ?? ($item->selisih === 0 ? 'Sesuai' : 'Perlu Penyesuaian') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background-color: #f8fafc; font-weight: bold;">
                        <td colspan="4" class="text-right">TOTAL KESELURUHAN ITEM PERSEDIAAN:</td>
                        <td colspan="4">{{ $totalItem }} Item ({{ $totalSelisih }} Item Mengalami Selisih)</td>
                    </tr>
                </tfoot>
            </table>

            @if($opname->catatan_kesimpulan)
                <div style="margin-bottom: 15px; font-size: 9.5pt;">
                    <strong>Catatan Kesimpulan Hasil Opname:</strong><br>
                    <em>"{{ $opname->catatan_kesimpulan }}"</em>
                </div>
            @endif

            <div class="closing-text">
                Demikian Berita Acara Hasil Opname Phisik Persediaan ini dibuat dengan sebenarnya dalam rangkap secukupnya untuk dipergunakan sebagai dasar tertib administrasi mutasi pembukuan persediaan BPS Kabupaten Cirebon.
            </div>

            <!-- Signatures -->
            <table class="signatures">
                <tr>
                    <td style="width: 50%;">
                        Tim Pemeriksa Fisik Persediaan,<br>
                        <strong>Kasubbag Umum BPS Kab. Cirebon</strong>
                        <div class="sign-space"></div>
                        <strong><u>Rina Kusuma Wardani, S.E., M.M.</u></strong><br>
                        NIP. 198003202005012002
                    </td>
                    <td style="width: 50%;">
                        Mengetahui & Mengesahkan,<br>
                        <strong>Kepala BPS Kabupaten Cirebon</strong>
                        @if($opname->status === 'disahkan')
                            <div><span class="stamp-box">✓ Telah Disahkan Digital</span></div>
                        @endif
                        <div class="sign-space" style="{{ $opname->status === 'disahkan' ? 'height: 40px;' : '' }}"></div>
                        <strong><u>{{ $opname->pimpinan->name ?? 'HENDRA WIJAYA' }}</u></strong><br>
                        NIP. {{ $opname->pimpinan->nip ?? '197801152002121001' }}
                    </td>
                </tr>
            </table>
        @endif

    </div>

</body>
</html>
