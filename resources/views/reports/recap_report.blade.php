<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Rekapitulasi MCU</title>
    {{-- You can include CSS directly or link to external CSS (less reliable for DomPDF) --}}
    <style>
        body { font-family: sans-serif; margin: 40px; font-size: 12px; }
        h1, h2 { text-align: center; color: #333; }
        h1 { margin-bottom: 20px; font-size: 24px; }
        h2 { margin-top: 30px; margin-bottom: 15px; font-size: 18px; border-bottom: 1px solid #eee; padding-bottom: 5px;}
        .date-range { text-align: center; margin-bottom: 30px; font-size: 14px; color: #555;}
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; page-break-inside: avoid; /* Keep tables on one page if possible */ }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        td { vertical-align: top; }
        .no-data { text-align: center; color: #777; }
         .description { margin-bottom: 15px; color: #444;}
         .chart-container { text-align: center; margin-top: 15px; margin-bottom: 20px; }
         .chart-container img { max-width: 100%; height: auto; } /* Ensure images fit */
         .footer { text-align: center; margin-top: 50px; font-size: 10px; color: #888; }
         .page-break { page-break-after: always; } /* Force a page break after each section */
    </style>
</head>
<body>
    <h1>Laporan Rekapitulasi Hasil MCU</h1>
    <div class="date-range">
        Periode: {{ $startDate }} s/d {{ $endDate }}
    </div>

    @if (empty($recaps))
        <p class="no-data">Tidak ada data rekapitulasi yang tersedia untuk periode ini.</p>
    @else
        @foreach ($recaps as $title => $data)
            <h2>Rekapitulasi {{ $title }}</h2>

            {{-- Add a description based on the data --}}
            {{-- You might need a helper function or logic in your controller/service
                 to generate more detailed descriptions based on specific results. --}}
            <p class="description">
                @if (empty($data))
                    Tidak ada data relevan untuk kategori ini.
                @else
                    Hasil rekapitulasi untuk {{ strtolower($title) }} menunjukkan distribusi sebagai berikut:
                @endif
            </p>


            {{-- Display data in a table --}}
            @if (!empty($data))
                <table>
                    <thead>
                        <tr>
                            {{-- Conditionally add Gender column --}}
                            @if ($title === 'Kelompok Umur' || $title === 'Hemoglobin')
                                <th>Jenis Kelamin</th>
                            @endif
                            <th>Kategori</th>
                            <th>Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $item)
                            <tr>
                                {{-- Conditionally display Gender --}}
                                @if ($title === 'Kelompok Umur' || $title === 'Hemoglobin')
                                    <td>{{ $item['Jenis Kelamin'] ?? '-' }}</td>
                                @endif
                                {{-- Use null coalescing operator for keys that might differ --}}
                                <td>{{ $item['Kategori'] ?? $item['Gangguan Status Gizi'] ?? $item['Kategori Penyakit'] ?? '-' }}</td>
                                <td>{{ $item['Jumlah'] ?? 0 }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="no-data">Tidak ada data kategori yang ditemukan.</p>
            @endif

            {{-- Placeholder for Chart Image (if implemented in Controller) --}}
            @if (isset($chartImages[$title]))
                <div class="chart-container">
                    <img src="{{ $chartImages[$title] }}" alt="Chart {{ $title }}">
                </div>
            @else
                <p class="no-data">Grafik tidak tersedia.</p>
            @endif

            @if (!$loop->last) {{-- Add page break after each section except the last one --}}
                <div class="page-break"></div>
            @endif

        @endforeach
    @endif

    <div class="footer">
        <p>Laporan dibuat pada: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</p>
        <p>© 2025 OccuHelp. All Rights Reserved.</p>
    </div>

</body>
</html>