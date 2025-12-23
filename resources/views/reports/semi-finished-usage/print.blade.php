@extends('layouts.report-layout')

@section('title', 'Laporan Penggunaan Bahan Setengah Jadi')

@section('content')
    <div class="max-w-7xl mx-auto p-6 sm:p-8 bg-white shadow-xl rounded-xl print:shadow-none print:rounded-none">

        {{-- Komponen Report Header --}}
        <x-report.header :title="'LAPORAN PENGGUNAAN BAHAN SETENGAH JADI'" address="Jl. Utama Lapar Chicken No. 123, Jakarta" />

        {{-- Metadata Laporan --}}
        <div class="text-xs text-gray-700 mb-4 flex justify-between print:text-[10px]">
            <p>
                <b>Tanggal Cetak:</b> {{ now()->format('d F Y') }}
            </p>
            <p>
                <b>Dicetak Oleh:</b> {{ Auth::user()->name ?? 'Administrator' }}
            </p>
        </div>

        {{-- Tabel Data Penggunaan --}}
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-300 border border-gray-300 print:border-gray-600 print:text-sm">

                {{-- Table Head --}}
                <thead class="bg-gray-100 print:bg-gray-200">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">No.</th>
                        <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Tanggal
                            Penggunaan</th>
                        <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Cabang</th>
                        <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Produk</th>
                        <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Total Qty
                        </th>
                    </tr>
                </thead>

                {{-- Table Body --}}
                <tbody class="bg-white divide-y divide-gray-200 text-sm print:text-[10px] print:divide-gray-400">
                    @forelse ($usages as $index => $usage)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 whitespace-nowrap text-center">{{ $index + 1 }}</td>
                            <td class="px-3 py-2 text-gray-700">
                                {{ $usage->usage_date ? \Carbon\Carbon::parse($usage->usage_date)->format('d F Y') : '-' }}
                            </td>
                            <td class="px-3 py-2 text-gray-700">{{ $usage->branch_name ?? '-' }}</td>
                            <td class="px-3 py-2 text-gray-700">{{ $usage->product_name ?? '-' }}</td>
                            <td class="px-3 py-2 text-gray-700">{{ $usage->total_quantity ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                Tidak ada data penggunaan bahan setengah jadi untuk ditampilkan.
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
