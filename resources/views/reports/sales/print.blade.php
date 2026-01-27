@extends('layouts.report-layout')

@section('title', $reportTitle ?? 'Laporan Transaksi Penjualan')

@section('content')
    <div class="max-w-7xl mx-auto p-6 sm:p-8 bg-white shadow-xl rounded-xl print:shadow-none print:rounded-none">

        {{-- Komponen Report Header --}}
        <x-report.header :title="$reportTitle ?? 'LAPORAN TRANSAKSI PENJUALAN'" />

        {{-- Metadata Laporan --}}
        <div class="text-xs text-gray-700 mb-4 flex justify-between print:text-[10px]">
            <p><b>Tanggal:</b> {{ now()->format('d F Y') }}</p>
            <p><b>Dicetak Oleh:</b> {{ Auth::user()->name ?? 'Administrator' }}</p>
        </div>

        {{-- Total Revenue Card --}}
        <div
            class="mb-6 p-4 bg-gradient-to-r from-blue-50 to-blue-100 border border-blue-200 rounded-lg print:bg-gray-100 print:border-gray-400">
            <p class="text-sm font-semibold text-gray-700 mb-2">TOTAL PENJUALAN</p>
            <p class="text-2xl font-bold text-blue-900 print:text-xl">Rp
                {{ number_format($sales->where('status', '=', 'completed')->sum('final_amount') ?? 0, 0, ',', '.') }}</p>
            <p class="text-xs text-gray-600 mt-1">Total dari {{ $sales->count() }} transaksi penjualan</p>
        </div>

        {{-- Tabel Data Penjualan --}}
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-300 border border-gray-300 print:border-gray-600 print:text-sm">

                {{-- Table Head --}}
                <thead class="bg-gray-100 print:bg-gray-200">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">No.</th>
                        <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Nomor
                            Penjualan</th>
                        <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Tanggal
                        </th>
                        <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Cabang</th>
                        <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Nama
                            Pelanggan</th>
                        <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Total Harga
                        </th>
                        <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Metode
                            Pembayaran</th>
                        <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>

                {{-- Table Body --}}
                <tbody class="bg-white divide-y divide-gray-200 text-sm print:text-[10px] print:divide-gray-400">
                    @forelse ($sales as $index => $sale)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 whitespace-nowrap text-center">{{ $index + 1 }}</td>
                            <td class="px-3 py-2 whitespace-nowrap text-gray-700">{{ $sale->sale_number }}</td>
                            <td class="px-3 py-2 whitespace-nowrap text-gray-700">
                                {{ \Carbon\Carbon::parse($sale->created_at)->format('d F Y') }}</td>
                            <td class="px-3 py-2 text-gray-700">{{ $sale->branch?->name ?? 'N/A' }}</td>
                            <td class="px-3 py-2 font-medium text-gray-900">{{ $sale->customer_name ?? 'Umum' }}</td>
                            <td class="px-3 py-2 whitespace-nowrap text-gray-700 font-bold">Rp
                                {{ number_format($sale->final_amount ?? 0, 0, ',', '.') }}</td>
                            <td class="px-3 py-2 whitespace-nowrap text-sm">
                                @if ($sale->payment_method === 'cash')
                                    <span
                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Tunai</span>
                                @elseif ($sale->payment_method === 'qris')
                                    <span
                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">QRIS</span>
                                @else
                                    <span
                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">{{ $sale->payment_method }}</span>
                                @endif
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-sm">
                                @if ($sale->status === 'completed')
                                    <span
                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Selesai</span>
                                @elseif ($sale->status === 'cancelled')
                                    <span
                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">Dibatalkan</span>
                                @else
                                    <span
                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">{{ ucfirst($sale->status) }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                                Tidak ada data penjualan untuk ditampilkan.
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
