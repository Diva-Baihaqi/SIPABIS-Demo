<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Dokumen Resmi' }} | BPS Kabupaten Cirebon</title>
    
    @vite(['resources/css/app.css'])

    <style>
        @media print {
            body {
                font-family: 'Times New Roman', Times, serif;
                font-size: 11pt;
                color: #000;
                background: #fff;
            }
            .no-print {
                display: none !important;
            }
            @page {
                size: A4 portrait;
                margin: 2cm 1.5cm 2cm 1.5cm;
            }
        }
        body {
            font-family: 'Times New Roman', Times, serif;
        }
    </style>
</head>
<body class="bg-slate-100 p-4 sm:p-8 print:p-0 print:bg-white text-slate-900">

    <!-- Print Control Bar (Hidden on Print) -->
    <div class="max-w-4xl mx-auto mb-6 bg-white p-4 rounded-xl shadow-sm border border-slate-200 flex items-center justify-between no-print">
        <div class="flex items-center gap-3">
            <a href="javascript:history.back()" class="px-4 py-2 text-sm font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg transition">
                &larr; Kembali
            </a>
            <span class="text-sm text-slate-500">Pratinjau Dokumen Cetak Resmi BPS</span>
        </div>
        <button onclick="window.print()" class="px-5 py-2 text-sm font-bold text-white bg-bps-navy-600 hover:bg-bps-navy-700 rounded-lg shadow-sm transition flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            <span>Cetak Dokumen</span>
        </button>
    </div>

    <!-- Printable Paper Sheet -->
    <div class="max-w-4xl mx-auto bg-white p-8 sm:p-12 shadow-md print:shadow-none print:p-0 border border-slate-200 print:border-none min-h-[29.7cm]">
        
        <!-- Official BPS Kop Surat -->
        <div class="border-b-2 border-black pb-3 mb-6 text-center relative">
            <h2 class="text-base font-bold uppercase tracking-wider">BADAN PUSAT STATISTIK KABUPATEN CIREBON</h2>
            <p class="text-xs">Jl. Sunan Rahmat No. 7, Sumber, Kec. Sumber, Kabupaten Cirebon, Jawa Barat 45611</p>
            <p class="text-xs">Telepon: (0231) 321171 | Pos-el: bps3209@bps.go.id | Laman: cirebonkab.bps.go.id</p>
        </div>

        <!-- Document Content Slot -->
        {{ $slot }}
    </div>

</body>
</html>
