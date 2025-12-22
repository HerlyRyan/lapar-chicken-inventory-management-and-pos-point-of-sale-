@extends('layouts.report-layout')

@section('title', $reportTitle ?? 'Laporan Transaksi Penjualan')

@section('content')
    <div class="max-w-7xl mx-auto p-6 sm:p-8 bg-white shadow-xl rounded-xl print:shadow-none print:rounded-none">

        {{-- Komponen Report Header --}}
        <x-report.header :title="$reportTitle ?? 'LAPORAN TRANSAKSI PENJUALAN'" address="Jl. Utama Lapar Chicken No. 123, Jakarta" />

        {{-- Metadata Laporan --}}
        <div class="text-xs text-gray-700 mb-4 flex justify-between print:text-[10px]">
            <p>
                <b>Tanggal:</b> {{ now()->format('d F Y') }}
            </p>
            <p>
                <b>Dicetak Oleh:</b> {{ Auth::user()->name ?? 'Administrator' }}
            </p>
        </div>

        {{-- Tabel Data Paket Penjualan --}}
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-300 border border-gray-300 print:border-gray-600 print:text-sm">

                {{-- Table Head --}}
                <thead class="bg-gray-100 print:bg-gray-200">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">No.</th>
                        <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Nomor
                            Penjualan</th>
                        <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Tanggal</th>
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
                            <td class="px-3 py-2 whitespace-nowrap text-gray-700">{{ $sale->created_at->format('d F Y') }}
                            </td>
                            <td class="px-3 py-2 text-gray-700">{{ $sale->branch?->name ?? 'N/A' }}</td>
                            <td class="px-3 py-2 font-medium text-gray-900">{{ $sale->customer_name ?? 'Umum' }}</td>
                            <td class="px-3 py-2 whitespace-nowrap text-gray-700 font-bold">Rp
                                {{ number_format($sale->final_amount ?? 0, 0, ',', '.') }}</td>
                            <td class="px-3 py-2 whitespace-nowrap">
                                @if ($sale->payment_method === 'cash')
                                    <span class="text-green-600 font-semibold">Tunai</span>
                                @elseif ($sale->payment_method === 'qris')
                                    <span class="text-blue-600 font-semibold">QRIS</span>
                                @else
                                    <span class="text-gray-600">{{ $sale->payment_method }}</span>
                                @endif
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap">
                                @if ($sale->status === 'completed')
                                    <span class="text-green-600 font-semibold">Selesai</span>
                                @elseif ($sale->status === 'cancelled')
                                    <span class="text-red-600 font-semibold">Dibatalkan</span>
                                @else
                                    <span class="text-yellow-600 font-semibold">{{ ucfirst($sale->status) }}</span>
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
