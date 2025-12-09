<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Laporan') - Lapar Chicken</title>

    {{-- Tailwind CSS (Pastikan dimuat) --}}
    @vite('resources/css/app.css')

    {{-- CSS Khusus Cetak --}}
    <style>
        /* Sembunyikan semua elemen yang tidak perlu saat dicetak */
        @media print {
            body {
                margin: 0;
                padding: 0;
                color: #000;
                /* Pastikan teks berwarna hitam */
            }

            .print\:shadow-none {
                box-shadow: none !important;
            }

            .print\:rounded-none {
                border-radius: 0 !important;
            }

            .print\:border-b-1 {
                border-bottom-width: 1px !important;
            }

            .print\:text-xl {
                font-size: 1.25rem !important;
            }

            .print\:text-lg {
                font-size: 1.125rem !important;
            }

            .print\:text-sm {
                font-size: 0.875rem !important;
            }

            .print\:text-xs {
                font-size: 0.75rem !important;
            }

            .print\:text-\[10px\] {
                font-size: 10px !important;
            }

            .print\:mt-1 {
                margin-top: 0.25rem !important;
            }

            /* Atur ukuran kertas A4 */
            @page {
                size: A4;
                margin: 1cm;
            }
        }
    </style>
</head>

<body class="bg-gray-100 font-sans antialiased">
    <div id="app">
        @yield('content')
    </div>
</body>

</html>
