@extends('layouts.report-layout')

@section('title', $reportTitle ?? 'Laporan Stok Transfer')

@section('content')
    <div class="max-w-7xl mx-auto p-6 sm:p-8 bg-white shadow-xl rounded-xl print:shadow-none print:rounded-none">

        <x-report.header :title="$reportTitle ?? 'LAPORAN STOK TRANSFER'" address="Jl. Utama Lapar Chicken No. 123, Jakarta" />

        <div class="text-xs text-gray-700 mb-4 flex justify-between print:text-[10px]">
            <p><b>Tanggal Cetak:</b> {{ now()->format('d F Y') }}</p>
            <p><b>Dicetak Oleh:</b> {{ Auth::user()->name ?? 'Administrator' }}</p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-300 border border-gray-300 print:border-gray-600 print:text-sm">
                <thead class="bg-gray-100 print:bg-gray-200">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">No.</th>
                        <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Tipe Item
                        </th>
                        <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Item</th>
                        <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Dari Cabang
                        </th>
                        <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Ke Cabang
                        </th>
                        <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Jumlah</th>
                        <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                        <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Tanggal
                            Pengiriman</th>
                        <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Tanggal
                            Penanganan</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 text-sm print:text-[10px] print:divide-gray-400">
                    @forelse ($transfers as $index => $transfer)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 whitespace-nowrap text-center">{{ $index + 1 }}</td>
                            <td class="px-3 py-2 whitespace-nowrap text-gray-700">{{ $transfer->item_type }}</td>
                            <td class="px-3 py-2 whitespace-nowrap text-gray-700">
                                {{ $transfer->item_type == 'finished' ? $transfer->finishedProduct->name : $transfer->semiFinishedProduct->name }}
                            </td>
                            <td class="px-3 py-2 text-gray-700">{{ $transfer->fromBranch?->name ?? '-' }}</td>
                            <td class="px-3 py-2 text-gray-700">{{ $transfer->toBranch?->name ?? '-' }}</td>
                            <td class="px-3 py-2 whitespace-nowrap text-gray-700">
                                {{ number_format($transfer->quantity) }}</td>
                            <td class="px-3 py-2 whitespace-nowrap">
                                @if ($transfer->status === 'sent')
                                    <span class="text-yellow-600 font-semibold">Dikirim</span>
                                @elseif ($transfer->status === 'accepted')
                                    <span class="text-green-600 font-semibold">Diterima</span>
                                @elseif ($transfer->status === 'rejected')
                                    <span class="text-red-600 font-semibold">Ditolak</span>
                                @endif
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-gray-700">
                                {{ $transfer->created_at?->format('d/m/Y H:i') ?? '-' }}</td>
                            <td class="px-3 py-2 whitespace-nowrap text-gray-700">
                                {{ $transfer->handled_at?->format('d/m/Y H:i') ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-4 text-center text-sm text-gray-500">
                                Tidak ada data stok transfer untuk ditampilkan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-report.footer />

    </div>
@endsection
