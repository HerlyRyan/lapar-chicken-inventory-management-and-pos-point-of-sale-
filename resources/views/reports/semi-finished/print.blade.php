@extends('layouts.report-layout')

@section('title', 'Laporan Data Bahan Setengah Jadi')

@section('content')
    <div class="max-w-7xl mx-auto p-6 sm:p-8 bg-white shadow-xl rounded-xl print:shadow-none print:rounded-none">

        {{-- Komponen Report Header --}}
        <x-report.header title="LAPORAN DATA BAHAN SETENGAH JADI" address="Jl. Utama Lapar Chicken No. 123, Jakarta" />

        {{-- Metadata Laporan --}}
        <div class="text-xs text-gray-700 mb-4 flex justify-between print:text-[10px]">
            <p>
                <b>Tanggal:</b> {{ now()->format('d F Y') }}
            </p>
            <p>
                <b>Dicetak Oleh:</b> {{ Auth::user()->name ?? 'Administrator' }}
            </p>
        </div>

        {{-- Tabel Data Bahan Setengah Jadi --}}
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-300 border border-gray-300 print:border-gray-600 print:text-sm">

                {{-- Table Head --}}
                <thead class="bg-gray-100 print:bg-gray-200">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">No.</th>
                        <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Kode</th>
                        <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Nama Bahan
                        </th>
                        <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Deskripsi
                        </th>
                        <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Stok</th>
                        <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>

                {{-- Table Body --}}
                <tbody class="bg-white divide-y divide-gray-200 text-sm print:text-[10px] print:divide-gray-400">
                    @forelse ($semiFinishedProducts as $index => $product)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 whitespace-nowrap text-center">{{ $index + 1 }}</td>
                            <td class="px-3 py-2 whitespace-nowrap text-gray-700">{{ $product->code }}</td>
                            <td class="px-3 py-2 font-medium text-gray-900">{{ $product->name }}</td>
                            <td class="px-3 py-2 text-gray-600">{{ $product->description }}</td>
                            <td class="px-3 py-2 whitespace-nowrap text-gray-700">
                                {{ $product->display_stock_quantity ?? '-' }}</td>
                            <td class="px-3 py-2 whitespace-nowrap">
                                @if ($product->is_active)
                                    <span class="text-green-600 font-semibold">Aktif</span>
                                @else
                                    <span class="text-gray-500">Tidak Aktif</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                Tidak ada data bahan setengah jadi untuk ditampilkan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer Tanda Tangan --}}
        <x-report.footer />

    </div>
@endsection
