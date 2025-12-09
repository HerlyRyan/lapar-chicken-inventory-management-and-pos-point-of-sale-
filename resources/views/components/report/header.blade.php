@props(['title', 'address'])

<div
    class="flex justify-between items-start border-b-2 border-gray-300 pb-4 mb-6 print:border-b-1 print:border-gray-500">

    {{-- 1. Logo Perusahaan (Pojok Kiri) --}}
    <div class="flex-shrink-0">
        {{-- Menggunakan asset() untuk mengakses gambar di public/img/Logo.png --}}
        <img src="{{ asset('img/Logo.png') }}" alt="Logo Lapar Chicken"
            class="w-16 h-16 sm:w-20 sm:h-20 object-contain print:w-14 print:h-14">
    </div>

    {{-- 2. Detail Perusahaan & Judul (Tengah) --}}
    <div class="text-center mx-auto px-4">
        <h1
            class="text-2xl sm:text-3xl font-extrabold text-gray-800 tracking-wider uppercase print:text-xl print:font-bold">
            Lapar Chicken
        </h1>
        <p class="text-sm text-gray-600 mt-1 print:text-xs print:mt-0 print:text-gray-700">
            {{ $address }}
        </p>
        <h2 class="mt-3 text-xl font-semibold text-red-600 print:text-lg print:mt-1">
            {{ $title }}
        </h2>
    </div>

    {{-- Kolom Kosong untuk Menjaga Keseimbangan Layout --}}
    <div class="w-16 sm:w-20 print:w-14"></div>
</div>
