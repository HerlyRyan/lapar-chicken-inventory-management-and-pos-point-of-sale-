@extends('layouts.report-layout') {{-- Asumsikan Anda memiliki layout terpisah untuk report --}}

@section('title', $reportTitle ?? 'Laporan Data Cabang')

@section('content')
    <div class="max-w-7xl mx-auto p-6 sm:p-8 bg-white shadow-xl rounded-xl print:shadow-none print:rounded-none">

        {{-- Komponen Report Header --}}
        <x-report.header :title="$reportTitle ?? 'LAPORAN DATA MASTER CABANG'" />

        {{-- Metadata Laporan --}}
        <div class="text-xs text-gray-700 mb-4 flex justify-between print:text-[10px]">
            <p>
                <b>Tanggal:</b> {{ now()->format('d F Y') }}
            </p>
            <p>
                <b>Dicetak Oleh:</b> {{ Auth::user()->name ?? 'Administrator' }}
            </p>
        </div>

        {{-- Tabel Data Cabang (Dibuat lebih ringkas) --}}
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-300 border border-gray-300 print:border-gray-600 print:text-sm">

                {{-- Table Head --}}
                <thead class="bg-gray-100 print:bg-gray-200">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">No.
                        </th>
                        <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                            Nama Cabang</th>
                        <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                            Kode</th>
                        <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                            Tipe</th>
                        <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                            Alamat</th>
                        <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                            Telepon</th>
                        <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>

                {{-- Table Body --}}
                <tbody class="bg-white divide-y divide-gray-200 text-sm print:text-[10px] print:divide-gray-400">
                    @forelse ($branches as $index => $branch)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 whitespace-nowrap text-center">{{ $index + 1 }}</td>
                            <td class="px-3 py-2 font-medium text-gray-900">{{ $branch->name }}</td>
                            <td class="px-3 py-2 whitespace-nowrap text-gray-700">{{ $branch->code }}</td>
                            <td class="px-3 py-2 whitespace-nowrap">
                                @if ($branch->type == 'branch')
                                    <span class="text-blue-600">Cabang Retail</span>
                                @else
                                    <span class="text-purple-600">Pusat Produksi</span>
                                @endif
                            </td>
                            <td class="px-3 py-2 text-gray-600 max-w-sm">{{ $branch->address }}</td>
                            <td class="px-3 py-2 whitespace-nowrap text-gray-700">{{ $branch->phone ?? '-' }}</td>
                            <td class="px-3 py-2 whitespace-nowrap">
                                @if ($branch->is_active)
                                    <span class="text-green-600 font-semibold">Aktif</span>
                                @else
                                    <span class="text-gray-500">Nonaktif</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                Tidak ada data cabang untuk ditampilkan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer Tanda Tangan (Opsional) --}}
        <x-report.footer />

    </div>
@endsection
