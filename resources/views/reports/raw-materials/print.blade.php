@extends('layouts.report-layout')

@section('title', $reportTitle ?? 'Laporan Data Bahan Baku')

@section('content')
    <div class="max-w-7xl mx-auto p-6 sm:p-8 bg-white shadow-xl rounded-xl print:shadow-none print:rounded-none">

        {{-- Komponen Report Header --}}
        <x-report.header :title="$reportTitle ?? 'LAPORAN DATA MASTER BAHAN BAKU'" />

        {{-- Metadata Laporan --}}
        <div class="text-xs text-gray-700 mb-4 flex justify-between print:text-[10px]">
            <p>
                <b>Tanggal:</b> {{ now()->format('d F Y') }}
            </p>
            <p>
                <b>Dicetak Oleh:</b> {{ Auth::user()->name ?? 'Administrator' }}
            </p>
        </div>

        {{-- Tabel Data Bahan Baku --}}
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-300 border border-gray-300 print:border-gray-600 print:text-sm">

                {{-- Table Head --}}
                <thead class="bg-gray-100 print:bg-gray-200">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">No.</th>
                        <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Kode</th>
                        <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Nama Bahan
                            Baku</th>
                        <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Kategori
                        </th>
                        <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Satuan</th>
                        <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Stok Saat
                            Ini</th>
                        <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Stok
                            Minimum</th>
                        <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Harga
                            Satuan</th>
                        <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Supplier
                        </th>
                        <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>

                {{-- Table Body --}}
                <tbody class="bg-white divide-y divide-gray-200 text-sm print:text-[10px] print:divide-gray-400">
                    @forelse ($rawMaterials as $index => $material)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 whitespace-nowrap text-center">{{ $index + 1 }}</td>
                            <td class="px-3 py-2 whitespace-nowrap text-gray-700">{{ $material->code ?? '-' }}</td>
                            <td class="px-3 py-2 font-medium text-gray-900">{{ $material->name }}</td>
                            <td class="px-3 py-2 text-gray-600">{{ $material->category?->name ?? '-' }}</td>
                            <td class="px-3 py-2 whitespace-nowrap text-gray-700">{{ $material->unit?->unit_name ?? '-' }}
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-gray-700">
                                {{ number_format($material->current_stock, 0, ',', '.') }}</td>
                            <td class="px-3 py-2 whitespace-nowrap text-gray-700">
                                {{ number_format($material->minimum_stock, 0, ',', '.') }}</td>
                            <td class="px-3 py-2 whitespace-nowrap text-gray-700">Rp
                                {{ number_format($material->unit_price, 0, ',', '.') }}</td>
                            <td class="px-3 py-2 text-gray-600">{{ $material->supplier?->name ?? '-' }}</td>
                            <td class="px-3 py-2 whitespace-nowrap">
                                @if ($material->is_active)
                                    <span class="text-green-600 font-semibold">Aktif</span>
                                @else
                                    <span class="text-gray-500">Tidak Aktif</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-4 text-center text-sm text-gray-500">
                                Tidak ada data bahan baku untuk ditampilkan.
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
