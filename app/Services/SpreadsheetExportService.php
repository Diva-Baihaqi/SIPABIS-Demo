<?php

namespace App\Services;

use App\Models\BarangPersediaan;
use App\Models\Ruangan;
use App\Models\StokOpname;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SpreadsheetExportService
{
    /**
     * Menyematkan Logo Resmi BPS pada sheet Excel jika file gambar tersedia.
     */
    private function addLogoToSheet($sheet, string $cellCoordinate = 'A1', int $height = 42, int $offsetX = 0, int $offsetY = 0): void
    {
        $logoPath = public_path('images/logo.png');
        if (file_exists($logoPath)) {
            $drawing = new Drawing();
            $drawing->setName('Logo BPS');
            $drawing->setDescription('Logo Badan Pusat Statistik');
            $drawing->setPath($logoPath);
            $drawing->setHeight($height);
            $drawing->setCoordinates($cellCoordinate);
            if ($offsetX > 0) $drawing->setOffsetX($offsetX);
            if ($offsetY > 0) $drawing->setOffsetY($offsetY);
            $drawing->setWorksheet($sheet);
        }
    }

    /**
     * Konversi bilangan integer ke teks terbilang Bahasa Indonesia.
     */
    private function terbilang(int $angka): string
    {
        $bilangan = ['', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas'];
        if ($angka < 12) {
            return $bilangan[$angka];
        } elseif ($angka < 20) {
            return $this->terbilang($angka - 10) . ' belas';
        } elseif ($angka < 100) {
            return $this->terbilang((int)($angka / 10)) . ' puluh' . ($angka % 10 ? ' ' . $this->terbilang($angka % 10) : '');
        } elseif ($angka < 200) {
            return 'seratus' . ($angka - 100 ? ' ' . $this->terbilang($angka - 100) : '');
        } elseif ($angka < 1000) {
            return $this->terbilang((int)($angka / 100)) . ' ratus' . ($angka % 100 ? ' ' . $this->terbilang($angka % 100) : '');
        } elseif ($angka < 2000) {
            return 'seribu' . ($angka - 1000 ? ' ' . $this->terbilang($angka - 1000) : '');
        } elseif ($angka < 1000000) {
            return $this->terbilang((int)($angka / 1000)) . ' ribu' . ($angka % 1000 ? ' ' . $this->terbilang($angka % 1000) : '');
        } elseif ($angka < 1000000000) {
            return $this->terbilang((int)($angka / 1000000)) . ' juta' . ($angka % 1000000 ? ' ' . $this->terbilang($angka % 1000000) : '');
        }
        return (string)$angka;
    }

    /**
     * Ekspor Buku Kendali Pengeluaran Persediaan per Ruangan ke format Excel (.xlsx).
     * Susunan tata letak dirancang rapi dan profesional menyerupai format cetak PDF resmi BPS.
     */
    public function exportBukuKendaliRuangan(Ruangan $ruangan, array $data): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Buku Kendali Ruangan');
        $sheet->setShowGridLines(true);

        // Logo Resmi BPS di A1
        $this->addLogoToSheet($sheet, 'A1', 46, 6, 4);

        // 1. Header Kop Resmi Instansi
        $sheet->setCellValue('B1', 'BADAN PUSAT STATISTIK KABUPATEN CIREBON');
        $sheet->setCellValue('B2', 'Jl. Sunan Rahmat No. 7, Sumber, Kec. Sumber, Kabupaten Cirebon, Jawa Barat 45611');
        $sheet->setCellValue('B3', 'Telepon: (0231) 321151 • Email: bps3209@bps.go.id • Satker: 428646 (UAKPB: 054.01.0200.018801)');

        $sheet->mergeCells('B1:H1');
        $sheet->mergeCells('B2:H2');
        $sheet->mergeCells('B3:H3');

        $sheet->getStyle('B1')->getFont()->setBold(true)->setSize(12)->getColor()->setRGB('002B49');
        $sheet->getStyle('B2:B3')->getFont()->setSize(9)->getColor()->setRGB('475569');
        $sheet->getStyle('B1:B3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        // Garis Pembatas Kop Surat Resmi (Garis Ganda di Baris 4)
        $sheet->getStyle('A4:H4')->getBorders()->getBottom()->setBorderStyle(Border::BORDER_DOUBLE)->getColor()->setRGB('000000');
        $sheet->getRowDimension(4)->setRowHeight(6);

        // 2. Judul Dokumen Resmi (Baris 6)
        $sheet->setCellValue('A6', 'BUKU KENDALI PENGELUARAN BARANG PAKAI HABIS');
        $sheet->mergeCells('A6:H6');
        $sheet->getStyle('A6')->getFont()->setBold(true)->setSize(12)->setUnderline(true)->getColor()->setRGB('0F172A');
        $sheet->getStyle('A6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(6)->setRowHeight(24);

        // 3. Kotak Informasi Ruangan (Meta Table) (Baris 8-9)
        $sheet->setCellValue('A8', 'Unit Kerja / Ruangan');
        $sheet->setCellValue('B8', ': ' . $ruangan->nama_ruangan . ' (' . $ruangan->kode_ruangan . ')');
        $sheet->mergeCells('B8:D8');

        $sheet->setCellValue('E8', 'Kode UAKPB');
        $sheet->setCellValue('F8', ': 054.01.0200.018801');
        $sheet->mergeCells('F8:H8');

        $sheet->setCellValue('A9', 'Penanggung Jawab');
        $sheet->setCellValue('B9', ': ' . ($ruangan->penanggung_jawab ?? $ruangan->pj_ruangan ?? 'Ketua Tim'));
        $sheet->mergeCells('B9:D9');

        $periodeTahun = $data['filter']['tahun'] ?? date('Y');
        $sheet->setCellValue('E9', 'Periode Anggaran');
        $sheet->setCellValue('F9', ': Tahun ' . $periodeTahun . ' | Dicetak: ' . Carbon::now()->format('d/m/Y H:i'));
        $sheet->mergeCells('F9:H9');

        $metaStyle = [
            'font' => ['size' => 9.5],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ];
        $sheet->getStyle('A8:H9')->applyFromArray($metaStyle);
        $sheet->getStyle('A8:A9')->getFont()->setBold(true);
        $sheet->getStyle('E8:E9')->getFont()->setBold(true);
        $sheet->getStyle('B8')->getFont()->setBold(true);

        // 4. BAGIAN I: REKAPITULASI PENGAMBILAN ATK PER JENIS BARANG
        $sheet->setCellValue('A11', 'I. REKAPITULASI PENGAMBILAN ATK PER JENIS BARANG');
        $sheet->mergeCells('A11:H11');
        $sheet->getStyle('A11')->getFont()->setBold(true)->setSize(10)->getColor()->setRGB('0F172A');
        $sheet->getRowDimension(11)->setRowHeight(20);

        // Header Tabel I (Baris 12)
        $sheet->setCellValue('A12', 'No');
        $sheet->setCellValue('B12', 'Kode Barang');
        $sheet->setCellValue('C12', 'Nama Barang Persediaan');
        $sheet->mergeCells('C12:D12');
        $sheet->setCellValue('E12', 'Kategori');
        $sheet->setCellValue('F12', 'Satuan');
        $sheet->setCellValue('G12', 'Frekuensi');
        $sheet->setCellValue('H12', 'Total Dikeluarkan');

        $headerTableStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => '0F172A'], 'size' => 9.5],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F1F5F9']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CBD5E1']]],
        ];
        $sheet->getStyle('A12:H12')->applyFromArray($headerTableStyle);
        $sheet->getRowDimension(12)->setRowHeight(24);

        $row = 13;
        $rekapBarang = $data['rekapBarang'] ?? collect([]);
        $totalUnitRekap = 0;

        if (count($rekapBarang) === 0) {
            $sheet->setCellValue("A{$row}", 'Tidak ada catatan pengeluaran barang.');
            $sheet->mergeCells("A{$row}:H{$row}");
            $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("A{$row}")->getFont()->setItalic(true)->setSize(9)->getColor()->setRGB('64748B');
            $sheet->getStyle("A{$row}:H{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('CBD5E1');
            $sheet->getRowDimension($row)->setRowHeight(22);
            $row++;
        } else {
            foreach ($rekapBarang as $idx => $item) {
                $totalUnitRekap += (int) $item->total_jumlah;

                $sheet->setCellValue('A' . $row, $idx + 1);
                $sheet->setCellValue('B' . $row, $item->kode_barang);
                $sheet->setCellValue('C' . $row, $item->nama_barang);
                $sheet->mergeCells("C{$row}:D{$row}");
                $sheet->setCellValue('E' . $row, $item->kategori);
                $sheet->setCellValue('F' . $row, $item->satuan);
                $sheet->setCellValue('G' . $row, $item->frekuensi . ' kali');
                $sheet->setCellValue('H' . $row, number_format($item->total_jumlah) . ' ' . $item->satuan);

                $sheet->getStyle("A{$row}:H{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('E2E8F0');
                $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("C{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle("E{$row}:G{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("H{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle("H{$row}")->getFont()->setBold(true);
                $sheet->getStyle("A{$row}:H{$row}")->getFont()->setSize(9);
                $sheet->getRowDimension($row)->setRowHeight(20);

                $row++;
            }
        }

        // Summary Row Tabel I
        $totalFisikUnit = $data['totalUnitKeluar'] ?? $totalUnitRekap;
        $sheet->setCellValue("A{$row}", 'TOTAL KESELURUHAN FISIK BARANG:');
        $sheet->mergeCells("A{$row}:G{$row}");
        $sheet->setCellValue("H{$row}", number_format($totalFisikUnit) . ' Unit');

        $sheet->getStyle("A{$row}:H{$row}")->getFont()->setBold(true)->setSize(9.5);
        $sheet->getStyle("A{$row}:H{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F8FAFC');
        $sheet->getStyle("A{$row}:H{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('CBD5E1');
        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("H{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getRowDimension($row)->setRowHeight(22);

        // 5. BAGIAN II: RINCIAN KRONOLOGIS TRANSAKSI PENGELUARAN FISIK
        $row += 2;
        $sheet->setCellValue("A{$row}", 'II. RINCIAN KRONOLOGIS TRANSAKSI PENGELUARAN FISIK');
        $sheet->mergeCells("A{$row}:H{$row}");
        $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setSize(10)->getColor()->setRGB('0F172A');
        $sheet->getRowDimension($row)->setRowHeight(20);

        $row++;
        // Header Tabel II
        $sheet->setCellValue("A{$row}", 'No');
        $sheet->setCellValue("B{$row}", 'Tanggal');
        $sheet->setCellValue("C{$row}", 'No. Permintaan / Dokumen');
        $sheet->setCellValue("D{$row}", 'Pegawai Pengambil');
        $sheet->setCellValue("E{$row}", 'Nama Barang Persediaan');
        $sheet->setCellValue("F{$row}", 'Jumlah Keluar');
        $sheet->setCellValue("G{$row}", 'Satuan');
        $sheet->setCellValue("H{$row}", 'Peruntukan Kegiatan');

        $sheet->getStyle("A{$row}:H{$row}")->applyFromArray($headerTableStyle);
        $sheet->getRowDimension($row)->setRowHeight(24);

        $row++;
        $transaksiList = $data['transaksiList'] ?? $data['transaksi'] ?? [];
        $totalKeluarTransaksi = 0;

        if (count($transaksiList) === 0) {
            $sheet->setCellValue("A{$row}", 'Tidak ada transaksi pengeluaran fisik tercatat.');
            $sheet->mergeCells("A{$row}:H{$row}");
            $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("A{$row}")->getFont()->setItalic(true)->setSize(9)->getColor()->setRGB('64748B');
            $sheet->getStyle("A{$row}:H{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('CBD5E1');
            $sheet->getRowDimension($row)->setRowHeight(22);
            $row++;
        } else {
            foreach ($transaksiList as $idx => $t) {
                $qty = abs($t->jumlah);
                $totalKeluarTransaksi += $qty;

                $sheet->setCellValue('A' . $row, $idx + 1);
                $sheet->setCellValue('B' . $row, Carbon::parse($t->tanggal_transaksi)->format('d/m/Y'));
                $sheet->setCellValue('C' . $row, $t->nomor_dokumen ?: '-');
                $sheet->setCellValue('D' . $row, $t->user->name ?? $t->penerima ?? '-');
                $sheet->setCellValue('E' . $row, $t->barang->nama_barang);
                $sheet->setCellValue('F' . $row, $qty);
                $sheet->setCellValue('G' . $row, $t->barang->satuan);
                $sheet->setCellValue('H' . $row, $t->keterangan ?? $t->permintaan?->catatan_pemohon ?? '-');

                $sheet->getStyle("A{$row}:H{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('E2E8F0');
                $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("C{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("D{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle("E{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle("F{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle("F{$row}")->getNumberFormat()->setFormatCode('#,##0');
                $sheet->getStyle("G{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("H{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle("A{$row}:H{$row}")->getFont()->setSize(9);
                $sheet->getRowDimension($row)->setRowHeight(20);

                $row++;
            }
        }

        // Summary Row Tabel II
        $sheet->setCellValue("A{$row}", 'TOTAL UNIT TRANSAKSI KELUAR:');
        $sheet->mergeCells("A{$row}:E{$row}");
        $sheet->setCellValue("F{$row}", $totalKeluarTransaksi);
        $sheet->setCellValue("G{$row}", 'Unit');
        $sheet->setCellValue("H{$row}", '-');

        $sheet->getStyle("A{$row}:H{$row}")->getFont()->setBold(true)->setSize(9.5);
        $sheet->getStyle("A{$row}:H{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F8FAFC');
        $sheet->getStyle("A{$row}:H{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('CBD5E1');
        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("F{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("F{$row}")->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle("G{$row}:H{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getRowDimension($row)->setRowHeight(22);

        // 6. BLOK TANDA TANGAN RESMI SIMETRIS
        $row += 2;
        $sheet->setCellValue("F{$row}", 'Sumber, ' . Carbon::now()->translatedFormat('d F Y'));
        $sheet->mergeCells("F{$row}:H{$row}");
        $sheet->getStyle("F{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("F{$row}")->getFont()->setSize(9.5);

        $row++;
        $sheet->setCellValue("A{$row}", 'Mengetahui,');
        $sheet->mergeCells("A{$row}:C{$row}");
        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue("F{$row}", 'Penerima / Penanggung Jawab,');
        $sheet->mergeCells("F{$row}:H{$row}");
        $sheet->getStyle("F{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("A{$row}:F{$row}")->getFont()->setSize(9.5);

        $row++;
        $sheet->setCellValue("A{$row}", 'Kepala Subbagian Umum BPS Kabupaten Cirebon');
        $sheet->mergeCells("A{$row}:C{$row}");
        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue("F{$row}", $ruangan->nama_ruangan);
        $sheet->mergeCells("F{$row}:H{$row}");
        $sheet->getStyle("F{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("F{$row}")->getFont()->setBold(true);
        $sheet->getStyle("A{$row}:F{$row}")->getFont()->setSize(9.5);

        // Ruang Tanda Tangan
        $row += 4;
        $sheet->setCellValue("A{$row}", 'Rina Kusuma Wardani, S.E., M.M.');
        $sheet->mergeCells("A{$row}:C{$row}");
        $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setUnderline(true)->setSize(9.5);
        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $pjName = $ruangan->penanggung_jawab ?? $ruangan->pj_ruangan ?? 'Ketua Tim';
        $sheet->setCellValue("F{$row}", $pjName);
        $sheet->mergeCells("F{$row}:H{$row}");
        $sheet->getStyle("F{$row}")->getFont()->setBold(true)->setUnderline(true)->setSize(9.5);
        $sheet->getStyle("F{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $row++;
        $sheet->setCellValue("A{$row}", 'NIP. 198003202005012002');
        $sheet->mergeCells("A{$row}:C{$row}");
        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("A{$row}")->getFont()->setSize(9);

        $nipPj = $ruangan->nip_penanggung_jawab ?? ($ruangan->userPj->nip ?? '....................................................');
        $sheet->setCellValue("F{$row}", 'NIP. ' . $nipPj);
        $sheet->mergeCells("F{$row}:H{$row}");
        $sheet->getStyle("F{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("F{$row}")->getFont()->setSize(9);

        // 7. Pengaturan Lebar Kolom Tetap yang Proporsional (Fixed Widths)
        $sheet->getColumnDimension('A')->setWidth(7);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(24);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(34);
        $sheet->getColumnDimension('F')->setWidth(15);
        $sheet->getColumnDimension('G')->setWidth(12);
        $sheet->getColumnDimension('H')->setWidth(28);

        // Text wrap pada nama barang dan peruntukan
        $sheet->getStyle('E:H')->getAlignment()->setWrapText(true);

        $filename = 'Buku_Kendali_' . str_replace(' ', '_', $ruangan->nama_ruangan) . '_' . date('Ymd_His') . '.xlsx';

        return new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Ekspor Multi-Sheet Workbook Buku Kendali Seluruh Ruangan / Divisi BPS ke Excel (.xlsx).
     * Sheet 1: Rekapitulasi Kantor (Perbandingan 7 Unit Kerja)
     * Sheet 2..8: Lembar Detail masing-masing Unit Kerja
     */
    public function exportBukuKendaliSemuaRuangan(Collection $ruangans, array $dataPerRuangan, Collection $rekapSemuaRuangan, array $filter = []): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();

        // --- Sheet 1: Rekapitulasi Seluruh Unit Kerja ---
        $sheetRekap = $spreadsheet->getActiveSheet();
        $sheetRekap->setTitle('Rekap Semua Divisi');
        $sheetRekap->setShowGridLines(true);

        $this->addLogoToSheet($sheetRekap, 'A1', 46, 6, 4);

        $sheetRekap->setCellValue('B1', 'BADAN PUSAT STATISTIK KABUPATEN CIREBON');
        $sheetRekap->setCellValue('B2', 'Jl. Sunan Rahmat No. 7, Sumber, Kec. Sumber, Kabupaten Cirebon, Jawa Barat 45611');
        $sheetRekap->setCellValue('B3', 'Telepon: (0231) 321151 • Email: bps3209@bps.go.id • Satker: 428646 (UAKPB: 054.01.0200.018801)');

        $sheetRekap->mergeCells('B1:H1');
        $sheetRekap->mergeCells('B2:H2');
        $sheetRekap->mergeCells('B3:H3');

        $sheetRekap->getStyle('B1')->getFont()->setBold(true)->setSize(12)->getColor()->setRGB('002B49');
        $sheetRekap->getStyle('B2:B3')->getFont()->setSize(9)->getColor()->setRGB('475569');

        $sheetRekap->getStyle('A4:H4')->getBorders()->getBottom()->setBorderStyle(Border::BORDER_DOUBLE)->getColor()->setRGB('000000');
        $sheetRekap->getRowDimension(4)->setRowHeight(6);

        $sheetRekap->setCellValue('A6', 'REKAPITULASI BUKU KENDALI PENGELUARAN ATK - SELURUH UNIT KERJA');
        $sheetRekap->mergeCells('A6:H6');
        $sheetRekap->getStyle('A6')->getFont()->setBold(true)->setSize(12)->setUnderline(true)->getColor()->setRGB('0F172A');
        $sheetRekap->getStyle('A6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheetRekap->getRowDimension(6)->setRowHeight(24);

        $periodeTahun = $filter['tahun'] ?? date('Y');
        $periodeBulan = !empty($filter['bulan']) ? 'Bulan: ' . $filter['bulan'] . ' | ' : '';
        $sheetRekap->setCellValue('A8', 'Satuan Kerja');
        $sheetRekap->setCellValue('B8', ': 428646 - BPS KABUPATEN CIREBON');
        $sheetRekap->mergeCells('B8:D8');

        $sheetRekap->setCellValue('E8', 'Periode Anggaran');
        $sheetRekap->setCellValue('F8', ': ' . $periodeBulan . 'Tahun ' . $periodeTahun);
        $sheetRekap->mergeCells('F8:H8');

        $sheetRekap->setCellValue('A9', 'Jumlah Unit Kerja');
        $sheetRekap->setCellValue('B9', ': ' . $ruangans->count() . ' Unit Kerja (Divisi)');
        $sheetRekap->mergeCells('B9:D9');

        $sheetRekap->setCellValue('E9', 'Tanggal Cetak');
        $sheetRekap->setCellValue('F9', ': ' . Carbon::now()->translatedFormat('d F Y H:i') . ' WIB');
        $sheetRekap->mergeCells('F9:H9');

        $sheetRekap->getStyle('A8:H9')->getFont()->setSize(9.5);
        $sheetRekap->getStyle('A8:A9')->getFont()->setBold(true);
        $sheetRekap->getStyle('E8:E9')->getFont()->setBold(true);
        $sheetRekap->getStyle('B8')->getFont()->setBold(true);

        // Header Tabel Rekap
        $sheetRekap->setCellValue('A11', 'No');
        $sheetRekap->setCellValue('B11', 'Kode Ruangan');
        $sheetRekap->setCellValue('C11', 'Nama Unit Kerja / Divisi');
        $sheetRekap->mergeCells('C11:D11');
        $sheetRekap->setCellValue('E11', 'Penanggung Jawab');
        $sheetRekap->setCellValue('F11', 'Pengambilan');
        $sheetRekap->setCellValue('G11', 'Variasi ATK');
        $sheetRekap->setCellValue('H11', 'Total Barang Keluar');

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 9.5],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '002B49']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CBD5E1']]],
        ];
        $sheetRekap->getStyle('A11:H11')->applyFromArray($headerStyle);
        $sheetRekap->getRowDimension(11)->setRowHeight(26);

        $rowR = 12;
        $totalPengambilanAll = 0;
        $totalUnitAll = 0;
        foreach ($rekapSemuaRuangan as $idx => $r) {
            $totalPengambilanAll += (int) $r->total_transaksi;
            $totalUnitAll += (int) $r->total_unit;

            $sheetRekap->setCellValue('A' . $rowR, $idx + 1);
            $sheetRekap->setCellValue('B' . $rowR, $r->kode_ruangan);
            $sheetRekap->setCellValue('C' . $rowR, $r->nama_ruangan);
            $sheetRekap->mergeCells("C{$rowR}:D{$rowR}");
            $sheetRekap->setCellValue('E' . $rowR, $r->penanggung_jawab ?? 'Kasubbag / Ketua Tim');
            $sheetRekap->setCellValue('F' . $rowR, number_format($r->total_transaksi) . ' kali');
            $sheetRekap->setCellValue('G' . $rowR, number_format($r->total_jenis_barang) . ' jenis');
            $sheetRekap->setCellValue('H' . $rowR, number_format($r->total_unit) . ' Unit');

            $sheetRekap->getStyle("A{$rowR}:H{$rowR}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('E2E8F0');
            $sheetRekap->getStyle("A{$rowR}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheetRekap->getStyle("B{$rowR}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheetRekap->getStyle("C{$rowR}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheetRekap->getStyle("E{$rowR}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheetRekap->getStyle("F{$rowR}:G{$rowR}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheetRekap->getStyle("H{$rowR}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheetRekap->getStyle("H{$rowR}")->getFont()->setBold(true);
            $sheetRekap->getStyle("A{$rowR}:H{$rowR}")->getFont()->setSize(9.5);
            $sheetRekap->getRowDimension($rowR)->setRowHeight(22);

            $rowR++;
        }

        // Summary Row Rekap
        $sheetRekap->setCellValue("A{$rowR}", 'TOTAL KESELURUHAN PENGELUARAN KANTOR BPS:');
        $sheetRekap->mergeCells("A{$rowR}:E{$rowR}");
        $sheetRekap->setCellValue("F{$rowR}", number_format($totalPengambilanAll) . ' kali');
        $sheetRekap->setCellValue("G{$rowR}", '-');
        $sheetRekap->setCellValue("H{$rowR}", number_format($totalUnitAll) . ' Unit');

        $sheetRekap->getStyle("A{$rowR}:H{$rowR}")->getFont()->setBold(true)->setSize(10);
        $sheetRekap->getStyle("A{$rowR}:H{$rowR}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F1F5F9');
        $sheetRekap->getStyle("A{$rowR}:H{$rowR}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('CBD5E1');
        $sheetRekap->getStyle("A{$rowR}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheetRekap->getStyle("F{$rowR}:G{$rowR}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheetRekap->getStyle("H{$rowR}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheetRekap->getRowDimension($rowR)->setRowHeight(24);

        // Tanda Tangan Rekap
        $rowR += 2;
        $sheetRekap->setCellValue("F{$rowR}", 'Sumber, ' . Carbon::now()->translatedFormat('d F Y'));
        $sheetRekap->mergeCells("F{$rowR}:H{$rowR}");
        $sheetRekap->getStyle("F{$rowR}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheetRekap->getStyle("F{$rowR}")->getFont()->setSize(9.5);

        $rowR++;
        $sheetRekap->setCellValue("A{$rowR}", 'Mengetahui,');
        $sheetRekap->mergeCells("A{$rowR}:C{$rowR}");
        $sheetRekap->setCellValue("F{$rowR}", 'Pengelola BMN / Petugas Gudang,');
        $sheetRekap->mergeCells("F{$rowR}:H{$rowR}");
        $sheetRekap->getStyle("A{$rowR}:H{$rowR}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheetRekap->getStyle("A{$rowR}:H{$rowR}")->getFont()->setSize(9.5);

        $rowR++;
        $sheetRekap->setCellValue("A{$rowR}", 'Kepala Subbagian Umum BPS Kabupaten Cirebon');
        $sheetRekap->mergeCells("A{$rowR}:C{$rowR}");
        $sheetRekap->setCellValue("F{$rowR}", 'BPS Kabupaten Cirebon');
        $sheetRekap->mergeCells("F{$rowR}:H{$rowR}");
        $sheetRekap->getStyle("A{$rowR}:H{$rowR}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheetRekap->getStyle("A{$rowR}:H{$rowR}")->getFont()->setSize(9.5);

        $rowR += 4;
        $sheetRekap->setCellValue("A{$rowR}", 'Rina Kusuma Wardani, S.E., M.M.');
        $sheetRekap->mergeCells("A{$rowR}:C{$rowR}");
        $sheetRekap->setCellValue("F{$rowR}", 'Pengadministrasi Persediaan BPS');
        $sheetRekap->mergeCells("F{$rowR}:H{$rowR}");
        $sheetRekap->getStyle("A{$rowR}:H{$rowR}")->getFont()->setBold(true)->setUnderline(true)->setSize(9.5);
        $sheetRekap->getStyle("A{$rowR}:H{$rowR}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $rowR++;
        $sheetRekap->setCellValue("A{$rowR}", 'NIP. 198003202005012002');
        $sheetRekap->mergeCells("A{$rowR}:C{$rowR}");
        $sheetRekap->setCellValue("F{$rowR}", 'NIP. ....................................................');
        $sheetRekap->mergeCells("F{$rowR}:H{$rowR}");
        $sheetRekap->getStyle("A{$rowR}:H{$rowR}")->getFont()->setSize(9);
        $sheetRekap->getStyle("A{$rowR}:H{$rowR}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheetRekap->getColumnDimension('A')->setWidth(7);
        $sheetRekap->getColumnDimension('B')->setWidth(16);
        $sheetRekap->getColumnDimension('C')->setWidth(26);
        $sheetRekap->getColumnDimension('D')->setWidth(18);
        $sheetRekap->getColumnDimension('E')->setWidth(30);
        $sheetRekap->getColumnDimension('F')->setWidth(18);
        $sheetRekap->getColumnDimension('G')->setWidth(16);
        $sheetRekap->getColumnDimension('H')->setWidth(24);

        // --- Sheet 2..8: Detail masing-masing Ruangan ---
        foreach ($ruangans as $r) {
            $sheetRoom = $spreadsheet->createSheet();
            $dataR = $dataPerRuangan[$r->id] ?? [];
            $cleanTitle = substr(trim(preg_replace('/[\\\\\/\?\*\[\]\:]/', '', $r->nama_ruangan)), 0, 30);
            $this->populateRuanganSheetContent($sheetRoom, $r, $dataR, $cleanTitle);
        }

        // Set active sheet back to Sheet 0
        $spreadsheet->setActiveSheetIndex(0);

        $filename = 'Buku_Kendali_BPS_Semua_Divisi_' . ($filter['tahun'] ?? date('Y')) . '_' . date('Ymd_His') . '.xlsx';

        return new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Ekspor Rekapitulasi Posisi Nilai & Fisik Barang Persediaan ke Excel (.xlsx).
     * Format Landscape dengan kolom neraca BMN lengkap.
     */
    public function exportRekapPersediaanBarang(array $data): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rekap Posisi Persediaan');
        $sheet->setShowGridLines(true);
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

        $this->addLogoToSheet($sheet, 'A1', 46, 6, 4);

        $sheet->setCellValue('B1', 'BADAN PUSAT STATISTIK KABUPATEN CIREBON');
        $sheet->setCellValue('B2', 'Jl. Sunan Rahmat No. 7, Sumber, Kec. Sumber, Kabupaten Cirebon, Jawa Barat 45611');
        $sheet->setCellValue('B3', 'Telepon: (0231) 321151 • Email: bps3209@bps.go.id • Satker: 428646 (UAKPB: 054.01.0200.018801)');

        $sheet->mergeCells('B1:N1');
        $sheet->mergeCells('B2:N2');
        $sheet->mergeCells('B3:N3');

        $sheet->getStyle('B1')->getFont()->setBold(true)->setSize(12)->getColor()->setRGB('002B49');
        $sheet->getStyle('B2:B3')->getFont()->setSize(9)->getColor()->setRGB('475569');

        $sheet->getStyle('A4:N4')->getBorders()->getBottom()->setBorderStyle(Border::BORDER_DOUBLE)->getColor()->setRGB('000000');
        $sheet->getRowDimension(4)->setRowHeight(6);

        $sheet->setCellValue('A6', 'LAPORAN REKAPITULASI POSISI NILAI DAN FISIK BARANG PERSEDIAAN (NERACA ATK)');
        $sheet->mergeCells('A6:N6');
        $sheet->getStyle('A6')->getFont()->setBold(true)->setSize(12)->setUnderline(true)->getColor()->setRGB('0F172A');
        $sheet->getStyle('A6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(6)->setRowHeight(24);

        $filter = $data['filter'] ?? [];
        $periodeTahun = $filter['tahun'] ?? date('Y');
        $periodeBulan = !empty($filter['bulan']) ? 'Bulan: ' . $filter['bulan'] . ' | ' : '';
        $sheet->setCellValue('A8', 'Satuan Kerja');
        $sheet->setCellValue('B8', ': 428646 - BPS KABUPATEN CIREBON');
        $sheet->mergeCells('B8:E8');

        $sheet->setCellValue('F8', 'Periode Anggaran');
        $sheet->setCellValue('G8', ': ' . $periodeBulan . 'Tahun ' . $periodeTahun);
        $sheet->mergeCells('G8:I8');

        $cakupanLabel = ($filter['cakupan'] ?? 'aktif') === 'aktif' ? 'Hanya Barang Aktif (Stok / Mutasi > 0)' : 'Seluruh Katalog BPS';
        $sheet->setCellValue('A9', 'Cakupan Laporan');
        $sheet->setCellValue('B9', ': ' . $cakupanLabel . ' (' . ($data['totalJenisBarang'] ?? 0) . ' Item Barang)');
        $sheet->mergeCells('B9:E9');

        $sheet->setCellValue('F9', 'Tanggal Cetak');
        $sheet->setCellValue('G9', ': ' . Carbon::now()->translatedFormat('d F Y H:i') . ' WIB');
        $sheet->mergeCells('G9:I9');

        $sheet->getStyle('A8:N9')->getFont()->setSize(9.5);
        $sheet->getStyle('A8:A9')->getFont()->setBold(true);
        $sheet->getStyle('F8:F9')->getFont()->setBold(true);
        $sheet->getStyle('B8:B9')->getFont()->setBold(true);

        // Header Tabel Rekap Persediaan (Baris 11)
        $sheet->setCellValue('A11', 'No');
        $sheet->setCellValue('B11', 'Kode BMN');
        $sheet->setCellValue('C11', 'Nama Barang Persediaan');
        $sheet->setCellValue('D11', 'Kategori');
        $sheet->setCellValue('E11', 'Satuan');
        $sheet->setCellValue('F11', 'Harga Satuan');
        $sheet->setCellValue('G11', 'Saldo Awal');
        $sheet->setCellValue('H11', 'Nilai Saldo Awal');
        $sheet->setCellValue('I11', 'Barang Masuk');
        $sheet->setCellValue('J11', 'Nilai Masuk');
        $sheet->setCellValue('K11', 'Barang Keluar');
        $sheet->setCellValue('L11', 'Nilai Keluar');
        $sheet->setCellValue('M11', 'Sisa Gudang');
        $sheet->setCellValue('N11', 'Nilai Persediaan');

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 9],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '002B49']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CBD5E1']]],
        ];
        $sheet->getStyle('A11:N11')->applyFromArray($headerStyle);
        $sheet->getRowDimension(11)->setRowHeight(28);

        $row = 12;
        $barangItems = $data['barangItems'] ?? collect([]);

        if ($barangItems->isEmpty()) {
            $sheet->setCellValue("A{$row}", 'Tidak ada data barang persediaan yang cocok dengan kriteria.');
            $sheet->mergeCells("A{$row}:N{$row}");
            $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("A{$row}")->getFont()->setItalic(true)->setSize(9)->getColor()->setRGB('64748B');
            $sheet->getStyle("A{$row}:N{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('CBD5E1');
            $sheet->getRowDimension($row)->setRowHeight(22);
            $row++;
        } else {
            foreach ($barangItems as $idx => $b) {
                $sheet->setCellValue('A' . $row, $idx + 1);
                $sheet->setCellValue('B' . $row, $b->kode_barang);
                $sheet->setCellValue('C' . $row, $b->nama_barang);
                $sheet->setCellValue('D' . $row, $b->kategori);
                $sheet->setCellValue('E' . $row, $b->satuan);
                $sheet->setCellValue('F' . $row, $b->harga_satuan);
                $sheet->setCellValue('G' . $row, $b->saldo_awal);
                $sheet->setCellValue('H' . $row, $b->saldo_awal_rp);
                $sheet->setCellValue('I' . $row, $b->total_masuk);
                $sheet->setCellValue('J' . $row, $b->total_masuk_rp);
                $sheet->setCellValue('K' . $row, $b->total_keluar);
                $sheet->setCellValue('L' . $row, $b->total_keluar_rp);
                $sheet->setCellValue('M' . $row, $b->saldo_akhir);
                $sheet->setCellValue('N' . $row, $b->saldo_akhir_rp);

                $sheet->getStyle("A{$row}:N{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('E2E8F0');
                $sheet->getStyle("A{$row}:B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("C{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle("D{$row}:E{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("F{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle("G{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle("H{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle("I{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle("J{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle("K{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle("L{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle("M{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle("N{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle("M{$row}:N{$row}")->getFont()->setBold(true);

                $sheet->getStyle("F{$row}")->getNumberFormat()->setFormatCode('#,##0');
                $sheet->getStyle("G{$row}")->getNumberFormat()->setFormatCode('#,##0');
                $sheet->getStyle("H{$row}")->getNumberFormat()->setFormatCode('#,##0');
                $sheet->getStyle("I{$row}")->getNumberFormat()->setFormatCode('#,##0');
                $sheet->getStyle("J{$row}")->getNumberFormat()->setFormatCode('#,##0');
                $sheet->getStyle("K{$row}")->getNumberFormat()->setFormatCode('#,##0');
                $sheet->getStyle("L{$row}")->getNumberFormat()->setFormatCode('#,##0');
                $sheet->getStyle("M{$row}")->getNumberFormat()->setFormatCode('#,##0');
                $sheet->getStyle("N{$row}")->getNumberFormat()->setFormatCode('#,##0');

                $sheet->getStyle("A{$row}:N{$row}")->getFont()->setSize(9);
                $sheet->getRowDimension($row)->setRowHeight(20);

                $row++;
            }
        }

        // Summary Row Grand Total
        $sheet->setCellValue("A{$row}", 'GRAND TOTAL KESELURUHAN BPS:');
        $sheet->mergeCells("A{$row}:F{$row}");
        $sheet->setCellValue("G{$row}", $data['grandTotalSaldoAwalFisik'] ?? 0);
        $sheet->setCellValue("H{$row}", $data['grandTotalSaldoAwalRp'] ?? 0);
        $sheet->setCellValue("I{$row}", $data['grandTotalMasukFisik'] ?? 0);
        $sheet->setCellValue("J{$row}", $data['grandTotalMasukRp'] ?? 0);
        $sheet->setCellValue("K{$row}", $data['grandTotalKeluarFisik'] ?? 0);
        $sheet->setCellValue("L{$row}", $data['grandTotalKeluarRp'] ?? 0);
        $sheet->setCellValue("M{$row}", $data['grandTotalSaldoAkhirFisik'] ?? 0);
        $sheet->setCellValue("N{$row}", $data['grandTotalSaldoAkhirRp'] ?? 0);

        $sheet->getStyle("A{$row}:N{$row}")->getFont()->setBold(true)->setSize(9.5);
        $sheet->getStyle("A{$row}:N{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F1F5F9');
        $sheet->getStyle("A{$row}:N{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('CBD5E1');
        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("G{$row}:N{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        $sheet->getStyle("G{$row}:N{$row}")->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getRowDimension($row)->setRowHeight(24);

        // Blok Tanda Tangan
        $row += 2;
        $sheet->setCellValue("K{$row}", 'Sumber, ' . Carbon::now()->translatedFormat('d F Y'));
        $sheet->mergeCells("K{$row}:N{$row}");
        $sheet->getStyle("K{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("K{$row}")->getFont()->setSize(9.5);

        $row++;
        $sheet->setCellValue("B{$row}", 'Mengetahui,');
        $sheet->mergeCells("B{$row}:E{$row}");
        $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("B{$row}")->getFont()->setSize(9.5);

        $sheet->setCellValue("K{$row}", 'Pengelola BMN / Petugas Persediaan,');
        $sheet->mergeCells("K{$row}:N{$row}");
        $sheet->getStyle("K{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("K{$row}")->getFont()->setSize(9.5);

        $row++;
        $sheet->setCellValue("B{$row}", 'Kepala Subbagian Umum BPS Kabupaten Cirebon');
        $sheet->mergeCells("B{$row}:E{$row}");
        $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("B{$row}")->getFont()->setSize(9.5);

        $sheet->setCellValue("K{$row}", 'BPS Kabupaten Cirebon');
        $sheet->mergeCells("K{$row}:N{$row}");
        $sheet->getStyle("K{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("K{$row}")->getFont()->setSize(9.5);

        $row += 4;
        $sheet->setCellValue("B{$row}", 'Rina Kusuma Wardani, S.E., M.M.');
        $sheet->mergeCells("B{$row}:E{$row}");
        $sheet->getStyle("B{$row}")->getFont()->setBold(true)->setUnderline(true)->setSize(9.5);
        $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue("K{$row}", 'Pengadministrasi Persediaan BPS');
        $sheet->mergeCells("K{$row}:N{$row}");
        $sheet->getStyle("K{$row}")->getFont()->setBold(true)->setUnderline(true)->setSize(9.5);
        $sheet->getStyle("K{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $row++;
        $sheet->setCellValue("B{$row}", 'NIP. 198003202005012002');
        $sheet->mergeCells("B{$row}:E{$row}");
        $sheet->getStyle("B{$row}")->getFont()->setSize(9);
        $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue("K{$row}", 'NIP. ....................................................');
        $sheet->mergeCells("K{$row}:N{$row}");
        $sheet->getStyle("K{$row}")->getFont()->setSize(9);
        $sheet->getStyle("K{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Column widths
        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(18);
        $sheet->getColumnDimension('E')->setWidth(10);
        $sheet->getColumnDimension('F')->setWidth(14);
        $sheet->getColumnDimension('G')->setWidth(12);
        $sheet->getColumnDimension('H')->setWidth(16);
        $sheet->getColumnDimension('I')->setWidth(12);
        $sheet->getColumnDimension('J')->setWidth(16);
        $sheet->getColumnDimension('K')->setWidth(12);
        $sheet->getColumnDimension('L')->setWidth(16);
        $sheet->getColumnDimension('M')->setWidth(12);
        $sheet->getColumnDimension('N')->setWidth(18);

        $sheet->getStyle('C')->getAlignment()->setWrapText(true);

        $filename = 'Rekap_Posisi_Persediaan_ATK_BPS_' . ($filter['tahun'] ?? date('Y')) . '_' . date('Ymd_His') . '.xlsx';

        return new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Helper privat untuk mengisi struktur sheet Buku Kendali suatu ruangan.
     */
    private function populateRuanganSheetContent($sheet, Ruangan $ruangan, array $data, string $sheetTitle): void
    {
        $sheet->setTitle($sheetTitle);
        $sheet->setShowGridLines(true);

        $this->addLogoToSheet($sheet, 'A1', 46, 6, 4);

        $sheet->setCellValue('B1', 'BADAN PUSAT STATISTIK KABUPATEN CIREBON');
        $sheet->setCellValue('B2', 'Jl. Sunan Rahmat No. 7, Sumber, Kec. Sumber, Kabupaten Cirebon, Jawa Barat 45611');
        $sheet->setCellValue('B3', 'Telepon: (0231) 321151 • Email: bps3209@bps.go.id • Satker: 428646 (UAKPB: 054.01.0200.018801)');

        $sheet->mergeCells('B1:H1');
        $sheet->mergeCells('B2:H2');
        $sheet->mergeCells('B3:H3');

        $sheet->getStyle('B1')->getFont()->setBold(true)->setSize(12)->getColor()->setRGB('002B49');
        $sheet->getStyle('B2:B3')->getFont()->setSize(9)->getColor()->setRGB('475569');
        $sheet->getStyle('B1:B3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $sheet->getStyle('A4:H4')->getBorders()->getBottom()->setBorderStyle(Border::BORDER_DOUBLE)->getColor()->setRGB('000000');
        $sheet->getRowDimension(4)->setRowHeight(6);

        $sheet->setCellValue('A6', 'BUKU KENDALI PENGELUARAN BARANG PAKAI HABIS');
        $sheet->mergeCells('A6:H6');
        $sheet->getStyle('A6')->getFont()->setBold(true)->setSize(12)->setUnderline(true)->getColor()->setRGB('0F172A');
        $sheet->getStyle('A6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(6)->setRowHeight(24);

        $sheet->setCellValue('A8', 'Unit Kerja / Ruangan');
        $sheet->setCellValue('B8', ': ' . $ruangan->nama_ruangan . ' (' . $ruangan->kode_ruangan . ')');
        $sheet->mergeCells('B8:D8');

        $sheet->setCellValue('E8', 'Kode UAKPB');
        $sheet->setCellValue('F8', ': 054.01.0200.018801');
        $sheet->mergeCells('F8:H8');

        $sheet->setCellValue('A9', 'Penanggung Jawab');
        $sheet->setCellValue('B9', ': ' . ($ruangan->penanggung_jawab ?? $ruangan->pj_ruangan ?? 'Ketua Tim'));
        $sheet->mergeCells('B9:D9');

        $periodeTahun = $data['filter']['tahun'] ?? date('Y');
        $sheet->setCellValue('E9', 'Periode Anggaran');
        $sheet->setCellValue('F9', ': Tahun ' . $periodeTahun . ' | Dicetak: ' . Carbon::now()->format('d/m/Y H:i'));
        $sheet->mergeCells('F9:H9');

        $metaStyle = [
            'font' => ['size' => 9.5],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ];
        $sheet->getStyle('A8:H9')->applyFromArray($metaStyle);
        $sheet->getStyle('A8:A9')->getFont()->setBold(true);
        $sheet->getStyle('E8:E9')->getFont()->setBold(true);
        $sheet->getStyle('B8')->getFont()->setBold(true);

        // BAGIAN I
        $sheet->setCellValue('A11', 'I. REKAPITULASI PENGAMBILAN ATK PER JENIS BARANG');
        $sheet->mergeCells('A11:H11');
        $sheet->getStyle('A11')->getFont()->setBold(true)->setSize(10)->getColor()->setRGB('0F172A');
        $sheet->getRowDimension(11)->setRowHeight(20);

        $sheet->setCellValue('A12', 'No');
        $sheet->setCellValue('B12', 'Kode Barang');
        $sheet->setCellValue('C12', 'Nama Barang Persediaan');
        $sheet->mergeCells('C12:D12');
        $sheet->setCellValue('E12', 'Kategori');
        $sheet->setCellValue('F12', 'Satuan');
        $sheet->setCellValue('G12', 'Frekuensi');
        $sheet->setCellValue('H12', 'Total Dikeluarkan');

        $headerTableStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => '0F172A'], 'size' => 9.5],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F1F5F9']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CBD5E1']]],
        ];
        $sheet->getStyle('A12:H12')->applyFromArray($headerTableStyle);
        $sheet->getRowDimension(12)->setRowHeight(24);

        $row = 13;
        $rekapBarang = $data['rekapBarang'] ?? collect([]);
        $totalUnitRekap = 0;

        if (count($rekapBarang) === 0) {
            $sheet->setCellValue("A{$row}", 'Tidak ada catatan pengeluaran barang.');
            $sheet->mergeCells("A{$row}:H{$row}");
            $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("A{$row}")->getFont()->setItalic(true)->setSize(9)->getColor()->setRGB('64748B');
            $sheet->getStyle("A{$row}:H{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('CBD5E1');
            $sheet->getRowDimension($row)->setRowHeight(22);
            $row++;
        } else {
            foreach ($rekapBarang as $idx => $item) {
                $totalUnitRekap += (int) $item->total_jumlah;

                $sheet->setCellValue('A' . $row, $idx + 1);
                $sheet->setCellValue('B' . $row, $item->kode_barang);
                $sheet->setCellValue('C' . $row, $item->nama_barang);
                $sheet->mergeCells("C{$row}:D{$row}");
                $sheet->setCellValue('E' . $row, $item->kategori);
                $sheet->setCellValue('F' . $row, $item->satuan);
                $sheet->setCellValue('G' . $row, $item->frekuensi . ' kali');
                $sheet->setCellValue('H' . $row, number_format($item->total_jumlah) . ' ' . $item->satuan);

                $sheet->getStyle("A{$row}:H{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('E2E8F0');
                $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("C{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle("E{$row}:G{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("H{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle("H{$row}")->getFont()->setBold(true);
                $sheet->getStyle("A{$row}:H{$row}")->getFont()->setSize(9);
                $sheet->getRowDimension($row)->setRowHeight(20);

                $row++;
            }
        }

        $totalFisikUnit = $data['totalUnitKeluar'] ?? $totalUnitRekap;
        $sheet->setCellValue("A{$row}", 'TOTAL KESELURUHAN FISIK BARANG:');
        $sheet->mergeCells("A{$row}:G{$row}");
        $sheet->setCellValue("H{$row}", number_format($totalFisikUnit) . ' Unit');

        $sheet->getStyle("A{$row}:H{$row}")->getFont()->setBold(true)->setSize(9.5);
        $sheet->getStyle("A{$row}:H{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F8FAFC');
        $sheet->getStyle("A{$row}:H{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('CBD5E1');
        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("H{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getRowDimension($row)->setRowHeight(22);

        // BAGIAN II
        $row += 2;
        $sheet->setCellValue("A{$row}", 'II. RINCIAN KRONOLOGIS TRANSAKSI PENGELUARAN FISIK');
        $sheet->mergeCells("A{$row}:H{$row}");
        $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setSize(10)->getColor()->setRGB('0F172A');
        $sheet->getRowDimension($row)->setRowHeight(20);

        $row++;
        $sheet->setCellValue("A{$row}", 'No');
        $sheet->setCellValue("B{$row}", 'Tanggal');
        $sheet->setCellValue("C{$row}", 'No. Permintaan / Dokumen');
        $sheet->setCellValue("D{$row}", 'Pegawai Pengambil');
        $sheet->setCellValue("E{$row}", 'Nama Barang Persediaan');
        $sheet->setCellValue("F{$row}", 'Jumlah Keluar');
        $sheet->setCellValue("G{$row}", 'Satuan');
        $sheet->setCellValue("H{$row}", 'Peruntukan Kegiatan');

        $sheet->getStyle("A{$row}:H{$row}")->applyFromArray($headerTableStyle);
        $sheet->getRowDimension($row)->setRowHeight(24);

        $row++;
        $transaksiList = $data['transaksiList'] ?? $data['transaksi'] ?? [];
        $totalKeluarTransaksi = 0;

        if (count($transaksiList) === 0) {
            $sheet->setCellValue("A{$row}", 'Tidak ada transaksi pengeluaran fisik tercatat.');
            $sheet->mergeCells("A{$row}:H{$row}");
            $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("A{$row}")->getFont()->setItalic(true)->setSize(9)->getColor()->setRGB('64748B');
            $sheet->getStyle("A{$row}:H{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('CBD5E1');
            $sheet->getRowDimension($row)->setRowHeight(22);
            $row++;
        } else {
            foreach ($transaksiList as $idx => $t) {
                $jumlah = abs($t->jumlah);
                $totalKeluarTransaksi += $jumlah;

                $sheet->setCellValue('A' . $row, $idx + 1);
                $sheet->setCellValue('B' . $row, Carbon::parse($t->tanggal_transaksi)->format('d/m/Y'));
                $sheet->setCellValue('C' . $row, $t->permintaan?->nomor_permintaan ?? '-');
                $sheet->setCellValue('D' . $row, $t->user?->name ?? 'Pegawai BPS');
                $sheet->setCellValue('E' . $row, $t->barang?->nama_barang ?? '-');
                $sheet->setCellValue('F' . $row, $jumlah);
                $sheet->setCellValue('G' . $row, $t->barang?->satuan ?? 'Unit');
                $sheet->setCellValue('H' . $row, $t->permintaan?->keterangan ?? $t->keterangan ?? '-');

                $sheet->getStyle("A{$row}:H{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('E2E8F0');
                $sheet->getStyle("A{$row}:B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("C{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("D{$row}:E{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle("F{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle("F{$row}")->getNumberFormat()->setFormatCode('#,##0');
                $sheet->getStyle("F{$row}")->getFont()->setBold(true);
                $sheet->getStyle("G{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("H{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle("A{$row}:H{$row}")->getFont()->setSize(9);
                $sheet->getRowDimension($row)->setRowHeight(20);

                $row++;
            }
        }

        $sheet->setCellValue("A{$row}", 'TOTAL UNIT TRANSAKSI KELUAR:');
        $sheet->mergeCells("A{$row}:E{$row}");
        $sheet->setCellValue("F{$row}", $totalKeluarTransaksi);
        $sheet->setCellValue("G{$row}", 'Unit');
        $sheet->setCellValue("H{$row}", '-');

        $sheet->getStyle("A{$row}:H{$row}")->getFont()->setBold(true)->setSize(9.5);
        $sheet->getStyle("A{$row}:H{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F8FAFC');
        $sheet->getStyle("A{$row}:H{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('CBD5E1');
        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("F{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("F{$row}")->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle("G{$row}:H{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getRowDimension($row)->setRowHeight(22);

        // TANDA TANGAN
        $row += 2;
        $sheet->setCellValue("F{$row}", 'Sumber, ' . Carbon::now()->translatedFormat('d F Y'));
        $sheet->mergeCells("F{$row}:H{$row}");
        $sheet->getStyle("F{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("F{$row}")->getFont()->setSize(9.5);

        $row++;
        $sheet->setCellValue("A{$row}", 'Mengetahui,');
        $sheet->mergeCells("A{$row}:C{$row}");
        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue("F{$row}", 'Penerima / Penanggung Jawab,');
        $sheet->mergeCells("F{$row}:H{$row}");
        $sheet->getStyle("F{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("A{$row}:F{$row}")->getFont()->setSize(9.5);

        $row++;
        $sheet->setCellValue("A{$row}", 'Kepala Subbagian Umum BPS Kabupaten Cirebon');
        $sheet->mergeCells("A{$row}:C{$row}");
        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue("F{$row}", $ruangan->nama_ruangan);
        $sheet->mergeCells("F{$row}:H{$row}");
        $sheet->getStyle("F{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("F{$row}")->getFont()->setBold(true);
        $sheet->getStyle("A{$row}:F{$row}")->getFont()->setSize(9.5);

        $row += 4;
        $sheet->setCellValue("A{$row}", 'Rina Kusuma Wardani, S.E., M.M.');
        $sheet->mergeCells("A{$row}:C{$row}");
        $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setUnderline(true)->setSize(9.5);
        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $pjName = $ruangan->penanggung_jawab ?? $ruangan->pj_ruangan ?? 'Ketua Tim';
        $sheet->setCellValue("F{$row}", $pjName);
        $sheet->mergeCells("F{$row}:H{$row}");
        $sheet->getStyle("F{$row}")->getFont()->setBold(true)->setUnderline(true)->setSize(9.5);
        $sheet->getStyle("F{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $row++;
        $sheet->setCellValue("A{$row}", 'NIP. 198003202005012002');
        $sheet->mergeCells("A{$row}:C{$row}");
        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("A{$row}")->getFont()->setSize(9);

        $nipPj = $ruangan->nip_penanggung_jawab ?? ($ruangan->userPj->nip ?? '....................................................');
        $sheet->setCellValue("F{$row}", 'NIP. ' . $nipPj);
        $sheet->mergeCells("F{$row}:H{$row}");
        $sheet->getStyle("F{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("F{$row}")->getFont()->setSize(9);

        $sheet->getColumnDimension('A')->setWidth(7);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(24);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(34);
        $sheet->getColumnDimension('F')->setWidth(15);
        $sheet->getColumnDimension('G')->setWidth(12);
        $sheet->getColumnDimension('H')->setWidth(28);

        $sheet->getStyle('E:H')->getAlignment()->setWrapText(true);
    }

    /**
     * Ekspor Kartu Stok Perpetual FIFO UC_PER53 per Barang ke Excel (.xlsx).
     */
    public function exportKartuStokBarang(BarangPersediaan $barang, array $data): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Kartu Stok ' . substr($barang->kode_barang, -6));

        // Logo Resmi BPS
        $this->addLogoToSheet($sheet, 'A1', 45);

        // Header
        $sheet->setCellValue('B1', 'BADAN PUSAT STATISTIK KABUPATEN CIREBON');
        $sheet->setCellValue('B2', 'RINCIAN BUKU PERSEDIAAN / KARTU STOK PERPETUAL FIFO (UC_PER53)');
        $sheet->setCellValue('B3', 'Kode Barang: ' . $barang->kode_bmn_lengkap . ' | Satuan: ' . strtoupper($barang->satuan) . ' | Metode: PERPETUAL FIFO');
        $sheet->setCellValue('B4', 'Nama Barang: ' . $barang->nama_barang . ' | Satker: 428646 | Kode UAKPB: 054.01.0200.018801');

        $sheet->mergeCells('B1:M1');
        $sheet->mergeCells('B2:M2');
        $sheet->mergeCells('B3:M3');
        $sheet->mergeCells('B4:M4');

        $sheet->getStyle('B1:B2')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('B1:B4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        // Table Header (Multi-level 13 Kolom SAKTI UC_PER53)
        // Row 6: Main headers
        $sheet->setCellValue('A6', 'No');
        $sheet->setCellValue('B6', 'Tanggal');
        $sheet->setCellValue('C6', 'Uraian Transaksi / Keterangan');
        $sheet->setCellValue('D6', 'Nomor Dokumen');
        $sheet->setCellValue('E6', 'Masuk');
        $sheet->setCellValue('H6', 'Keluar');
        $sheet->setCellValue('K6', 'Saldo Persediaan');

        $sheet->mergeCells('A6:A7');
        $sheet->mergeCells('B6:B7');
        $sheet->mergeCells('C6:C7');
        $sheet->mergeCells('D6:D7');
        $sheet->mergeCells('E6:G6');
        $sheet->mergeCells('H6:J6');
        $sheet->mergeCells('K6:M6');

        // Row 7: Sub-headers
        $sheet->setCellValue('E7', 'Unit');
        $sheet->setCellValue('F7', 'Harga (Rp)');
        $sheet->setCellValue('G7', 'Jumlah (Rp)');

        $sheet->setCellValue('H7', 'Unit');
        $sheet->setCellValue('I7', 'Harga (Rp)');
        $sheet->setCellValue('J7', 'Jumlah (Rp)');

        $sheet->setCellValue('K7', 'Unit');
        $sheet->setCellValue('L7', 'Harga (Rp)');
        $sheet->setCellValue('M7', 'Jumlah (Rp)');

        $headerStyle = [
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E2E8F0']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ];
        $sheet->getStyle('A6:M7')->applyFromArray($headerStyle);

        $hargaSatuan = (float) ($data['hargaSatuan'] ?? $barang->harga_satuan_estimasi ?? 0);

        // Saldo Awal Row (Row 8)
        $row = 8;
        $sheet->setCellValue('A8', '1');
        $sheet->setCellValue('B8', $data['startDate'] ? Carbon::parse($data['startDate'])->format('d/m/Y') : '01/01/' . date('Y'));
        $sheet->setCellValue('C8', 'Saldo Awal Persediaan');
        $sheet->setCellValue('D8', '-');
        $sheet->setCellValue('E8', 0);
        $sheet->setCellValue('F8', 0);
        $sheet->setCellValue('G8', 0);
        $sheet->setCellValue('H8', 0);
        $sheet->setCellValue('I8', 0);
        $sheet->setCellValue('J8', 0);
        $sheet->setCellValue('K8', $data['saldoAwal'] ?? 0);
        $sheet->setCellValue('L8', $hargaSatuan);
        $sheet->setCellValue('M8', $data['saldoAwalRp'] ?? (($data['saldoAwal'] ?? 0) * $hargaSatuan));

        $sheet->getStyle('A8:M8')->getFont()->setBold(true);
        $sheet->getStyle('A8:M8')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle('A8:B8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('D8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('E8:M8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('F8:G8')->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('I8:J8')->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('L8:M8')->getNumberFormat()->setFormatCode('#,##0');

        // Mutasi Rows
        $row = 9;
        $mutasiList = $data['mutasiList'] ?? $data['transaksi'] ?? [];
        foreach ($mutasiList as $idx => $t) {
            $t = (object) $t;
            $masuk = isset($t->masuk) ? (int) $t->masuk : 0;
            $keluar = isset($t->keluar) ? (int) $t->keluar : 0;
            $itemHarga = isset($t->harga_satuan) ? (float) $t->harga_satuan : $hargaSatuan;
            $masukRp = isset($t->masuk_rp) ? (float) $t->masuk_rp : ($masuk * $itemHarga);
            $keluarRp = isset($t->keluar_rp) ? (float) $t->keluar_rp : ($keluar * $itemHarga);
            $tanggal = $t->tanggal ?? $t->tanggal_transaksi ?? null;
            $nomorDok = $t->nomor_dokumen ?? '-';
            $uraian = $t->uraian ?? $t->keterangan ?? $t->sumber_atau_tujuan ?? '-';
            $saldo = isset($t->saldo) ? (int) $t->saldo : ($t->saldo_setelah ?? 0);
            $saldoRp = isset($t->saldo_rp) ? (float) $t->saldo_rp : ($saldo * $itemHarga);

            $sheet->setCellValue('A' . $row, $idx + 2);
            $sheet->setCellValue('B' . $row, $tanggal ? Carbon::parse($tanggal)->format('d/m/Y') : '-');
            $sheet->setCellValue('C' . $row, $uraian ?: '-');
            $sheet->setCellValue('D' . $row, $nomorDok ?: '-');

            $sheet->setCellValue('E' . $row, $masuk > 0 ? $masuk : 0);
            $sheet->setCellValue('F' . $row, $masuk > 0 ? $itemHarga : 0);
            $sheet->setCellValue('G' . $row, $masuk > 0 ? $masukRp : 0);

            $sheet->setCellValue('H' . $row, $keluar > 0 ? $keluar : 0);
            $sheet->setCellValue('I' . $row, $keluar > 0 ? $itemHarga : 0);
            $sheet->setCellValue('J' . $row, $keluar > 0 ? $keluarRp : 0);

            $sheet->setCellValue('K' . $row, $saldo);
            $sheet->setCellValue('L' . $row, $itemHarga);
            $sheet->setCellValue('M' . $row, $saldoRp);

            $sheet->getStyle("A{$row}:M{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getStyle("A{$row}:B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("D{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("E{$row}:M{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("F{$row}:G{$row}")->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle("I{$row}:J{$row}")->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle("L{$row}:M{$row}")->getNumberFormat()->setFormatCode('#,##0');

            $row++;
        }

        // Summary Row ("Jumlah")
        $sheet->setCellValue('A' . $row, 'TOTAL MUTASI & SALDO AKHIR PERSEDIAAN (SAKTI)');
        $sheet->mergeCells("A{$row}:D{$row}");
        $sheet->setCellValue('E' . $row, $data['totalMasuk'] ?? 0);
        $sheet->setCellValue('F' . $row, '-');
        $sheet->setCellValue('G' . $row, $data['totalMasukRp'] ?? (($data['totalMasuk'] ?? 0) * $hargaSatuan));
        $sheet->setCellValue('H' . $row, $data['totalKeluar'] ?? 0);
        $sheet->setCellValue('I' . $row, '-');
        $sheet->setCellValue('J' . $row, $data['totalKeluarRp'] ?? (($data['totalKeluar'] ?? 0) * $hargaSatuan));
        $sheet->setCellValue('K' . $row, $data['saldoAkhir'] ?? 0);
        $sheet->setCellValue('L' . $row, '-');
        $sheet->setCellValue('M' . $row, $data['saldoAkhirRp'] ?? (($data['saldoAkhir'] ?? 0) * $hargaSatuan));

        $sheet->getStyle("A{$row}:M{$row}")->getFont()->setBold(true);
        $sheet->getStyle("A{$row}:M{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F1F5F9');
        $sheet->getStyle("A{$row}:M{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("E{$row}:M{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("G{$row}")->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle("J{$row}")->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle("M{$row}")->getNumberFormat()->setFormatCode('#,##0');

        // Signatures
        $row += 2;
        $sheet->setCellValue('K' . $row, 'Sumber, ' . Carbon::now()->format('d F Y'));
        $row++;
        $sheet->setCellValue('C' . $row, 'Mengetahui:');
        $sheet->setCellValue('K' . $row, 'Penyimpan / Pengurus Barang,');
        $row++;
        $sheet->setCellValue('C' . $row, 'Kepala Subbagian Umum BPS Kabupaten Cirebon');
        $sheet->setCellValue('K' . $row, 'BPS Kabupaten Cirebon');

        $row += 4;
        $sheet->setCellValue('C' . $row, 'Rina Kusuma Wardani, S.E., M.M.');
        $sheet->setCellValue('K' . $row, 'Bagus Prasetyo, S.E.');
        $sheet->getStyle("C{$row}")->getFont()->setBold(true)->setUnderline(true);
        $sheet->getStyle("K{$row}")->getFont()->setBold(true)->setUnderline(true);

        $row++;
        $sheet->setCellValue('C' . $row, 'NIP. 198003202005012002');
        $sheet->setCellValue('K' . $row, 'NIP. 198705142010011003');

        $cols = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M'];
        foreach ($cols as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'Kartu_Stok_' . $barang->kode_barang . '_' . date('Ymd_His') . '.xlsx';

        return new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Ekspor Berita Acara Stock Opname (BASO) Format Neraca SAKTI ke Excel (.xlsx).
     * Format kolom dan baris identik dengan Berita Acara Stock Opname.xlsx Tabel 1 BPS.
     *
     * @param StokOpname $opname
     * @param bool $onlyWithStock Jika true, hanya ekspor barang yang memiliki stok fisik/nominal/selisih > 0.
     */
    public function exportStockOpnameBaso(StokOpname $opname, bool $onlyWithStock = false): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('BASO');

        $date = Carbon::parse($opname->tanggal_opname);
        $terbilangHari = $date->translatedFormat('l');
        $terbilangTgl = $this->terbilang((int)$date->format('d'));
        $terbilangBulan = $date->translatedFormat('F');
        $terbilangTahun = $this->terbilang((int)$date->format('Y'));

        // Logo Resmi BPS
        $this->addLogoToSheet($sheet, 'A1', 45);

        // Header Title (Persis Berita Acara Stock Opname.xlsx Tabel 1)
        $sheet->setCellValue('B2', 'BADAN PUSAT STATISTIK KABUPATEN CIREBON');
        $sheet->setCellValue('B3', 'TABEL 1 - BERITA ACARA HASIL OPNAME PHISIK (STOCK OPNAME) PERSEDIAAN');
        $sheet->setCellValue('B4', 'Nomor: ' . $opname->nomor_opname . ' | Satker: 428646 (UAKPB: 054.01.0200.018801)');
        $sheet->getStyle('B2:B3')->getFont()->setBold(true)->setSize(11);

        // Teks Pembuka Resmi Berbahasa Indonesia (Baris 6)
        $sheet->setCellValue('A6', "                Pada hari ini " . $terbilangHari . " Tanggal " . $terbilangTgl . " Bulan " . $terbilangBulan . " Tahun " . $terbilangTahun . ", kami telah melaksanakan opname phisik barang persediaan dengan hasil rincian sebagai berikut :");
        $sheet->mergeCells('A6:J6');
        $sheet->getStyle('A6')->getFont()->setItalic(true);

        // Header 2 Baris Sesuai Excel BPS Asli (Baris 8-9)
        $sheet->setCellValue('A8', 'No.');
        $sheet->setCellValue('B8', 'Uraian Barang');
        $sheet->setCellValue('C8', 'Satuan');
        $sheet->setCellValue('D8', 'Harga Beli Satuan (Rupiah)');
        $sheet->setCellValue('E8', 'Total Persediaan');
        $sheet->setCellValue('G8', 'Barang Rusak');
        $sheet->setCellValue('I8', 'Barang Usang');

        $sheet->mergeCells('A8:A9');
        $sheet->mergeCells('B8:B9');
        $sheet->mergeCells('C8:C9');
        $sheet->mergeCells('D8:D9');
        $sheet->mergeCells('E8:F8');
        $sheet->mergeCells('G8:H8');
        $sheet->mergeCells('I8:J8');

        $sheet->setCellValue('E9', 'Jumlah');
        $sheet->setCellValue('F9', 'Harga Total (Rupiah)');
        $sheet->setCellValue('G9', 'Jumlah');
        $sheet->setCellValue('H9', 'Harga Total (Rupiah)');
        $sheet->setCellValue('I9', 'Jumlah');
        $sheet->setCellValue('J9', 'Harga Total (Rupiah)');

        // Baris Nomor Kolom (Baris 10)
        $sheet->setCellValue('A10', '(1)');
        $sheet->setCellValue('B10', '(2)');
        $sheet->setCellValue('C10', '(3)');
        $sheet->setCellValue('D10', '(4)');
        $sheet->setCellValue('E10', '(5)');
        $sheet->setCellValue('F10', '(6) = (4) x (5)');
        $sheet->setCellValue('G10', '(7)');
        $sheet->setCellValue('H10', '(8) = (4) x (7)');
        $sheet->setCellValue('I10', '(9)');
        $sheet->setCellValue('J10', '(10) = (4) x (9)');

        $headerStyle = [
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E2E8F0']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ];
        $sheet->getStyle('A8:J10')->applyFromArray($headerStyle);

        // Category Subheader (Baris 11)
        $sheet->setCellValue('A11', 'Persediaan Reguler');
        $sheet->mergeCells('A11:J11');
        $sheet->getStyle('A11:J11')->getFont()->setBold(true);
        $sheet->getStyle('A11:J11')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F1F5F9');
        $sheet->getStyle('A11:J11')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Filter data jika $onlyWithStock aktif
        $itemsToExport = $opname->details;
        if ($onlyWithStock) {
            $itemsToExport = $itemsToExport->filter(function ($item) {
                $hargaBeli = (float) ($item->barang->harga_satuan_estimasi ?? 0);
                $qtyFisik = (int) $item->stok_fisik;
                $totalHarga = $hargaBeli * $qtyFisik;
                $rusakQty = str_contains(strtolower($item->keterangan_selisih ?? ''), 'rusak') ? abs($item->selisih) : 0;
                $usangQty = str_contains(strtolower($item->keterangan_selisih ?? ''), 'usang') ? abs($item->selisih) : 0;

                return $qtyFisik > 0 || $totalHarga > 0 || $rusakQty > 0 || $usangQty > 0 || $item->selisih != 0;
            })->values();
        }

        // Data Rows (Mulai Baris 12)
        $row = 12;
        $totalPersediaanRp = 0;
        $totalRusakRp = 0;
        $totalUsangRp = 0;

        foreach ($itemsToExport as $index => $item) {
            $hargaBeli = (float) ($item->barang->harga_satuan_estimasi ?? 0);
            $qtyFisik = (int) $item->stok_fisik;
            $totalHarga = $hargaBeli * $qtyFisik;

            $rusakQty = str_contains(strtolower($item->keterangan_selisih ?? ''), 'rusak') ? abs($item->selisih) : 0;
            $rusakRp = $rusakQty * $hargaBeli;

            $usangQty = str_contains(strtolower($item->keterangan_selisih ?? ''), 'usang') ? abs($item->selisih) : 0;
            $usangRp = $usangQty * $hargaBeli;

            $totalPersediaanRp += $totalHarga;
            $totalRusakRp += $rusakRp;
            $totalUsangRp += $usangRp;

            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $item->barang->nama_barang);
            $sheet->setCellValue('C' . $row, $item->barang->satuan);
            $sheet->setCellValue('D' . $row, $hargaBeli);
            $sheet->setCellValue('E' . $row, $qtyFisik);
            $sheet->setCellValue('F' . $row, $totalHarga);
            $sheet->setCellValue('G' . $row, $rusakQty > 0 ? $rusakQty : '-');
            $sheet->setCellValue('H' . $row, $rusakRp > 0 ? $rusakRp : '-');
            $sheet->setCellValue('I' . $row, $usangQty > 0 ? $usangQty : '-');
            $sheet->setCellValue('J' . $row, $usangRp > 0 ? $usangRp : '-');

            $sheet->getStyle("A{$row}:J{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("C{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("D{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("E{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("F{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("G{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("H{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("I{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("J{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            $sheet->getStyle("D{$row}")->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle("F{$row}")->getNumberFormat()->setFormatCode('#,##0');
            if ($rusakRp > 0) $sheet->getStyle("H{$row}")->getNumberFormat()->setFormatCode('#,##0');
            if ($usangRp > 0) $sheet->getStyle("J{$row}")->getNumberFormat()->setFormatCode('#,##0');

            $row++;
        }

        // Summary Total Row
        $sheet->setCellValue('A' . $row, 'Total');
        $sheet->mergeCells("A{$row}:E{$row}");
        $sheet->setCellValue('F' . $row, $totalPersediaanRp);
        $sheet->setCellValue('H' . $row, $totalRusakRp > 0 ? $totalRusakRp : '-');
        $sheet->setCellValue('J' . $row, $totalUsangRp > 0 ? $totalUsangRp : '-');
        $sheet->getStyle("A{$row}:J{$row}")->getFont()->setBold(true);
        $sheet->getStyle("A{$row}:J{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F1F5F9');
        $sheet->getStyle("A{$row}:J{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("F{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("H{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("J{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("F{$row}")->getNumberFormat()->setFormatCode('#,##0');
        if ($totalRusakRp > 0) $sheet->getStyle("H{$row}")->getNumberFormat()->setFormatCode('#,##0');
        if ($totalUsangRp > 0) $sheet->getStyle("J{$row}")->getNumberFormat()->setFormatCode('#,##0');

        $row++;
        // Saldo Neraca Row
        $saldoNeraca = $totalPersediaanRp - $totalRusakRp - $totalUsangRp;
        $sheet->setCellValue('A' . $row, 'Saldo Persediaan di Neraca = Total Persediaan - Barang Rusak - Barang Usang = (11) - (12) - (13)');
        $sheet->mergeCells("A{$row}:E{$row}");
        $sheet->setCellValue('F' . $row, $saldoNeraca);
        $sheet->getStyle("A{$row}:J{$row}")->getFont()->setBold(true);
        $sheet->getStyle("A{$row}:J{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E2E8F0');
        $sheet->getStyle("A{$row}:J{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle("F{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("F{$row}")->getNumberFormat()->setFormatCode('#,##0');

        // Signatures (Identik Penanggungjawab KPB & Penyimpan Barang BPS)
        $row += 2;
        $sheet->setCellValue('H' . $row, 'Cirebon, ' . Carbon::parse($opname->tanggal_opname)->format('d F Y'));
        $row++;
        $sheet->setCellValue('B' . $row, 'Mengetahui:');
        $row++;
        $sheet->setCellValue('B' . $row, 'Penanggungjawab KPB');
        $sheet->setCellValue('H' . $row, 'Penyimpan Barang,');
        $row++;
        $sheet->setCellValue('B' . $row, 'Kepala BPS Kabupaten Cirebon,');

        $row += 4;
        $sheet->setCellValue('B' . $row, $opname->pimpinan->name ?? 'HENDRA WIJAYA');
        $sheet->setCellValue('H' . $row, 'BAGUS PRASETYO, S.E.');
        $sheet->getStyle("B{$row}")->getFont()->setBold(true)->setUnderline(true);
        $sheet->getStyle("H{$row}")->getFont()->setBold(true)->setUnderline(true);

        $row++;
        $sheet->setCellValue('B' . $row, 'NIP. ' . ($opname->pimpinan->nip ?? '197801152002121001'));
        $sheet->setCellValue('H' . $row, 'NIP. 198705142010011003');

        // Petunjuk Teknis Cara Pengisian (Sesuai Berita Acara Stock Opname.xlsx Baris 96-116)
        $row += 2;
        $sheet->setCellValue('A' . $row, 'Cara Pengisian');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);

        $petunjuk = [
            '(1). Diisi dengan nomor urut',
            '(2). Diisi dengan uraian klasifikasi barang',
            '(3). Diisi dengan satuan barang biasanya barang dikeluarkan/dimasukkan',
            '(4). Diisi dengan harga pembelian terakhir persediaan (dalam rupiah per satuan), berdasarkan faktur pembelian atau dokumen sumber lainnya',
            '(5). Diisi dengan kuantitas/jumlah barang keseluruhan yang ditemukan pada opname phisik (dalam satuan) baik barang dalam kondisi baik, rusak atau usang',
            '(6). Diisi dengan harga total persediaan (Kolom 4 X Kolom 5)',
            '(7). Diisi dengan jumlah barang yang rusak yang ditemukan pada opname phisik (dalam satuan)',
            '(8). Diisi dengan harga total persediaan yang rusak (Kolom 4 X Kolom 7)',
            '(9). Diisi dengan jumlah barang yang usang yang ditemukan pada opname phisik (dalam satuan)',
            '(10). Diisi dengan harga total persediaan yang usang (Kolom 4 X Kolom 9)',
            '(11). Diisi dengan jumlah harga total persediaan (Jumlah Kolom 6)',
            '(12). Diisi dengan jumlah harga total barang rusak (Jumlah Kolom 8), seharusnya sesuai dengan nilai persediaan satker yang kondisinya rusak per akhir semester pada Aplikasi Persediaan',
            '(13). Diisi dengan Jumlah harga total barang usang (Jumlah Kolom 10), seharusnya sesuai dengan nilai persediaan satker yang kondisinya usang per akhir semester pada Aplikasi Persediaan',
            '(14). Diisi dengan Jumlah harga total persediaan setelah dikurangi dengan jumlah harga total barang rusak dan usang, seharusnya sesuai dengan nilai persediaan satker per akhir semester pada Aplikasi Persediaan',
            '(15). Diisi dengan nama tempat dan tanggal pelaporan',
            '(16). Diisi dengan jabatan, nama, NIP dan tanda tangan penanggungjawab UAKPB (Unit Akuntansi Kuasa Pengguna Barang)',
            '(17). Diisi dengan nama, NIP dan tanda tangan pengelola persediaan',
        ];

        foreach ($petunjuk as $ptx) {
            $row++;
            $sheet->setCellValue('A' . $row, $ptx);
            $sheet->getStyle('A' . $row)->getFont()->setSize(8.5)->getColor()->setRGB('475569');
        }

        // Auto widths
        foreach (['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'] as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $suffix = $onlyWithStock ? '_Ringkas' : '_Lengkap';
        $filename = 'Berita_Acara_Stock_Opname_' . str_replace('/', '_', $opname->nomor_opname) . $suffix . '.xlsx';

        return new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Ekspor Buku Register Penerimaan Barang Persediaan ke Excel (.xlsx).
     * Format resmi BPS Kabupaten Cirebon Satker 428646.
     *
     * @param \Illuminate\Support\Collection $transaksiList
     * @param array $filterInfo
     * @return StreamedResponse
     */
    public function exportRegisterBarangMasuk($transaksiList, array $filterInfo = []): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Penerimaan Persediaan');

        // Logo Resmi BPS
        $this->addLogoToSheet($sheet, 'A1', 45);

        // Header Title BPS
        $sheet->setCellValue('B2', 'BADAN PUSAT STATISTIK KABUPATEN CIREBON');
        $sheet->setCellValue('B3', 'BUKU REGISTER PENERIMAAN BARANG PERSEDIAAN (BARANG MASUK & PENGADAAN)');
        $tahunAnggaran = $filterInfo['tahun'] ?? date('Y');
        $sheet->setCellValue('B4', 'Satker: 428646 (UAKPB: 054.01.0200.018801) | Tahun Anggaran: ' . $tahunAnggaran);
        $sheet->getStyle('B2:B3')->getFont()->setBold(true)->setSize(11);

        // Subheader filter / tanggal cetak
        $filterDesc = $filterInfo['deskripsi'] ?? 'Seluruh Riwayat Penerimaan';
        $sheet->setCellValue('A6', 'Filter Data: ' . $filterDesc . ' | Dicetak pada: ' . Carbon::now()->translatedFormat('d F Y H:i') . ' WIB');
        $sheet->mergeCells('A6:K6');
        $sheet->getStyle('A6')->getFont()->setItalic(true)->setSize(9)->getColor()->setRGB('475569');

        // Table Headers (Baris 8)
        $headers = [
            'A8' => 'No.',
            'B8' => 'Tanggal Terima',
            'C8' => 'No. Dokumen / Faktur',
            'D8' => 'Kode BMN',
            'E8' => 'Uraian Barang Persediaan',
            'F8' => 'Kategori',
            'G8' => 'Satuan',
            'H8' => 'Jumlah Masuk',
            'I8' => 'Penyedia / Rekanan (Vendor)',
            'J8' => 'Status SPJ',
            'K8' => 'Keterangan',
        ];

        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
        }

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '002B49']], // BPS Navy
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CBD5E1']]],
        ];
        $sheet->getStyle('A8:K8')->applyFromArray($headerStyle);
        $sheet->getRowDimension(8)->setRowHeight(28);

        // Baris Nomor Kolom (Baris 9)
        $kolomAngka = ['(1)', '(2)', '(3)', '(4)', '(5)', '(6)', '(7)', '(8)', '(9)', '(10)', '(11)'];
        $cols = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K'];
        foreach ($cols as $idx => $col) {
            $sheet->setCellValue($col . '9', $kolomAngka[$idx]);
            $sheet->getStyle($col . '9')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }
        $sheet->getStyle('A9:K9')->getFont()->setSize(8)->setBold(true)->getColor()->setRGB('64748B');
        $sheet->getStyle('A9:K9')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F8FAFC');
        $sheet->getStyle('A9:K9')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('CBD5E1');

        $row = 10;
        $totalKuantitas = 0;

        foreach ($transaksiList as $index => $t) {
            $totalKuantitas += (int) $t->jumlah;
            $statusSpj = $t->status_spj === 'selesai' ? 'SPJ Lunas' : 'SPJ Tertunda';

            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, Carbon::parse($t->tanggal_transaksi)->translatedFormat('d/m/Y'));
            $sheet->setCellValue('C' . $row, $t->nomor_dokumen ?? '-');
            $sheet->setCellValue('D' . $row, $t->barang->kode_barang ?? '-');
            $sheet->setCellValue('E' . $row, $t->barang->nama_barang ?? '-');
            $sheet->setCellValue('F' . $row, $t->barang->kategori->nama_kategori ?? '-');
            $sheet->setCellValue('G' . $row, $t->barang->satuan ?? '-');
            $sheet->setCellValue('H' . $row, (int) $t->jumlah);
            $sheet->setCellValue('I' . $row, $t->sumber_atau_tujuan ?? '-');
            $sheet->setCellValue('J' . $row, $statusSpj);
            $sheet->setCellValue('K' . $row, $t->keterangan ?? '-');

            $sheet->getStyle("A{$row}:K{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('E2E8F0');
            $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("C{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("D{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("G{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("H{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("J{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $sheet->getStyle("H{$row}")->getNumberFormat()->setFormatCode('#,##0');

            if ($t->status_spj !== 'selesai') {
                $sheet->getStyle("J{$row}")->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('D97706'))->setBold(true);
            } else {
                $sheet->getStyle("J{$row}")->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('059669'))->setBold(true);
            }

            $row++;
        }

        // Summary Total Row
        $sheet->setCellValue('A' . $row, 'TOTAL UNIT BARANG MASUK');
        $sheet->mergeCells("A{$row}:G{$row}");
        $sheet->setCellValue('H' . $row, $totalKuantitas);
        $sheet->setCellValue('I' . $row, '');
        $sheet->mergeCells("I{$row}:K{$row}");

        $sheet->getStyle("A{$row}:K{$row}")->getFont()->setBold(true);
        $sheet->getStyle("A{$row}:K{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F1F5F9');
        $sheet->getStyle("A{$row}:K{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('CBD5E1');
        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("H{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("H{$row}")->getNumberFormat()->setFormatCode('#,##0');

        // Blok Tanda Tangan Simetris
        $row += 2;
        $sheet->setCellValue('I' . $row, 'Sumber, ' . Carbon::now()->translatedFormat('d F Y'));
        $sheet->mergeCells("I{$row}:K{$row}");
        $sheet->getStyle("I{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $row++;
        $sheet->setCellValue('B' . $row, 'Mengetahui,');
        $sheet->mergeCells("B{$row}:D{$row}");
        $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('I' . $row, 'Petugas Pengurus Barang Persediaan,');
        $sheet->mergeCells("I{$row}:K{$row}");
        $sheet->getStyle("I{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $row++;
        $sheet->setCellValue('B' . $row, 'Kepala Subbagian Umum BPS Kabupaten Cirebon');
        $sheet->mergeCells("B{$row}:D{$row}");
        $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $row += 4;
        $sheet->setCellValue('B' . $row, 'Rina Kusuma Wardani, S.E., M.M.');
        $sheet->mergeCells("B{$row}:D{$row}");
        $sheet->getStyle("B{$row}")->getFont()->setBold(true)->setUnderline(true);
        $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('I' . $row, 'Bagus Prasetyo, S.E.');
        $sheet->mergeCells("I{$row}:K{$row}");
        $sheet->getStyle("I{$row}")->getFont()->setBold(true)->setUnderline(true);
        $sheet->getStyle("I{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $row++;
        $sheet->setCellValue('B' . $row, 'NIP. 198003202005012002');
        $sheet->mergeCells("B{$row}:D{$row}");
        $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('I' . $row, 'NIP. 198705142010011003');
        $sheet->mergeCells("I{$row}:K{$row}");
        $sheet->getStyle("I{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Auto column widths
        foreach ($cols as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'Register_Penerimaan_Persediaan_BPS_428646_' . date('Ymd_His') . '.xlsx';

        return new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0',
        ]);
    }
}
