@extends('layouts.report-layout')

@section('title', $reportTitle ?? 'Laporan Data Supplier')

@section('content')
    <div class="max-w-7xl mx-auto p-6 sm:p-8 bg-white shadow-xl rounded-xl print:shadow-none print:rounded-none">

        {{-- Komponen Report Header --}}
        <x-report.header :title="$reportTitle ?? 'LAPORAN DATA MASTER SUPPLIER'" address="Jl. Utama Lapar Chicken No. 123, Jakarta" />

        {{-- Metadata Laporan --}}
        <div class="text-xs text-gray-700 mb-4 flex justify-between print:text-[10px]">
            <p>
                <b>Tanggal:</b> {{ now()->format('d F Y') }}
            </p>
            <p>
                <b>Dicetak Oleh:</b> {{ Auth::user()->name ?? 'Administrator' }}
            </p>
        </div>

        {{-- Stats Section - Optimized for Report & Print --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8 print:mb-6">

            {{-- Card Aktif --}}
            <div
                class="bg-white border-2 border-orange-500 rounded-xl p-4 flex items-center justify-between shadow-sm print:shadow-none print:border-gray-300">
                <div>
                    <p class="text-[10px] sm:text-xs text-orange-600 font-bold uppercase tracking-wider print:text-gray-600">
                        Supplier Aktif</p>
                    <p class="text-2xl sm:text-3xl font-extrabold text-gray-900 mt-0.5">
                        {{ $suppliers->where('is_active', true)->count() }}
                    </p>
                </div>
                <div class="bg-orange-50 p-2.5 rounded-lg text-orange-500 print:hidden">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                {{-- Ikon khusus Print (Hitam Putih) --}}
                <div class="hidden print:block text-gray-400">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                        <path fill-rule="evenodd"
                            d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 10a1 1 0 011-1h4a1 1 0 110 2H8a1 1 0 01-1-1z"
                            clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>

            {{-- Card Non-Aktif --}}
            <div
                class="bg-white border-2 border-gray-200 rounded-xl p-4 flex items-center justify-between shadow-sm print:shadow-none print:border-gray-300">
                <div>
                    <p class="text-[10px] sm:text-xs text-gray-500 font-bold uppercase tracking-wider">Non-Aktif</p>
                    <p class="text-2xl sm:text-3xl font-extrabold text-gray-900 mt-0.5">
                        {{ $suppliers->where('is_active', false)->count() }}
                    </p>
                </div>
                <div class="bg-gray-50 p-2.5 rounded-lg text-gray-400 print:hidden">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                {{-- Ikon khusus Print --}}
                <div class="hidden print:block text-gray-400">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M13.477 14.89A6 6 0 015.11 6.524l8.367 8.368zm1.414-1.414L6.524 5.11a6 6 0 018.367 8.367zM18 10a8 8 0 11-16 0 8 8 0 0116 0z"
                            clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>

        </div>

        {{-- Tabel Data Supplier --}}
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-300 border border-gray-300 print:border-gray-600 print:text-sm">

                {{-- Table Head --}}
                <thead class="bg-gray-100 print:bg-gray-200">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">No.</th>
                        <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Kode</th>
                        <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Nama
                            Supplier</th>
                        <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Bahan Baku
                        </th>
                        <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Alamat</th>
                        <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Telepon
                        </th>
                        <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>

                {{-- Table Body --}}
                <tbody class="bg-white divide-y divide-gray-200 text-sm print:text-[10px] print:divide-gray-400">
                    @forelse ($suppliers as $index => $supplier)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 whitespace-nowrap text-center">{{ $index + 1 }}</td>
                            <td class="px-3 py-2 whitespace-nowrap text-gray-700">{{ $supplier->code }}</td>
                            <td class="px-3 py-2 font-medium text-gray-900">{{ $supplier->name }}</td>
                            <td class="px-3 py-2 text-gray-600">
                                @forelse($supplier->rawMaterials as $material)
                                    <div>{{ $material->name }}</div>
                                @empty
                                    <span class="text-gray-400">-</span>
                                @endforelse
                            </td>
                            <td class="px-3 py-2 text-gray-600 max-w-sm">{{ $supplier->address }}</td>
                            <td class="px-3 py-2 whitespace-nowrap text-gray-700">{{ $supplier->phone ?? '-' }}</td>
                            <td class="px-3 py-2 whitespace-nowrap">
                                @if ($supplier->is_active)
                                    <span class="text-green-600 font-semibold">Aktif</span>
                                @else
                                    <span class="text-gray-500">Tidak Aktif</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                Tidak ada data supplier untuk ditampilkan.
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
