@extends('layouts.report-layout')

@section('title', $reportTitle ?? 'Laporan Produk Terlaris')

@section('content')
    <div class="max-w-7xl mx-auto p-6 sm:p-8 bg-white shadow-xl rounded-xl print:shadow-none print:rounded-none">

        <x-report.header :title="$reportTitle ?? 'LAPORAN PRODUK TERLARIS'" address="Jl. Utama Lapar Chicken No. 123, Jakarta" />

        <div class="text-xs text-gray-700 mb-4 flex justify-between print:text-[10px]">
            <p><b>Tanggal:</b> {{ now()->format('d F Y') }}</p>
            <p><b>Dicetak Oleh:</b> {{ Auth::user()->name ?? 'Administrator' }}</p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-300 border border-gray-300 print:border-gray-600 print:text-sm">
                <thead class="bg-gray-100 print:bg-gray-200">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">No.</th>
                        <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Cabang</th>
                        <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Nama Produk
                        </th>
                        <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Kategori
                        </th>
                        <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Total
                            Terjual</th>
                        <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Total Nilai
                            Penjualan</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 text-sm print:text-[10px] print:divide-gray-400">
                    @forelse ($bestSelling as $index => $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 whitespace-nowrap text-center">{{ $index + 1 }}</td>
                            <td class="px-3 py-2 text-gray-700">{{ $item->branch_name }}</td>
                            <td class="px-3 py-2 text-gray-700">{{ $item->item_name }}</td>
                            <td class="px-3 py-2 text-gray-700">{{ $item->category_name }}</td>
                            <td class="px-3 py-2 whitespace-nowrap text-gray-700">{{ number_format($item->quantity) }}</td>
                            <td class="px-3 py-2 whitespace-nowrap text-gray-700">Rp
                                {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                Tidak ada data produk terlaris untuk ditampilkan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-report.footer />

    </div>
@endsection
