@extends('layouts.app')

@php
use Illuminate\Support\Facades\Storage;
@endphp

@section('title', 'Detail Paket Penjualan')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-orange-50 via-red-50 to-orange-100">
    {{-- Header --}}
    <div class="bg-gradient-to-r from-orange-900 via-orange-800 to-red-900 shadow-xl">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('sales-packages.index') }}"
                       class="inline-flex items-center justify-center w-10 h-10 bg-white/10 hover:bg-white/20 rounded-xl transition-all duration-200 backdrop-blur-sm border border-white/20 group">
                        <svg class="w-5 h-5 text-white group-hover:text-orange-200 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-2xl lg:text-3xl font-bold text-white">Detail Paket Penjualan</h1>
                        <p class="text-orange-200 mt-1">Informasi lengkap paket {{ $salesPackage->name }}</p>
                    </div>
                </div>

                <div class="flex items-center space-x-3">
                    <a href="{{ route('sales-packages.edit', $salesPackage) }}"
                       class="inline-flex items-center px-4 py-2.5 bg-white/10 hover:bg-white/20 text-white rounded-xl font-medium transition-all duration-200 backdrop-blur-sm border border-white/20 group">
                        <svg class="w-4 h-4 mr-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit
                    </a>

                    <form action="{{ route('sales-packages.toggle-status', $salesPackage) }}" method="POST" class="inline-block">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2.5 {{ $salesPackage->is_active ? 'bg-red-600/80 hover:bg-red-600 border border-red-500/30' : 'bg-green-600/80 hover:bg-green-600 border border-green-500/30' }} text-white rounded-xl font-medium transition-all duration-200 backdrop-blur-sm group">
                            <svg class="w-4 h-4 mr-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                @if($salesPackage->is_active)
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                @else
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14M5 12h14"/>
                                @endif
                            </svg>
                            {{ $salesPackage->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Content --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Main --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-xl border border-gray-200/50 overflow-hidden">
                    {{-- Package Header --}}
                    <div class="relative bg-gradient-to-r from-orange-500 via-red-500 to-orange-600 px-8 py-12">
                        <div class="absolute inset-0 bg-black/10"></div>
                        <div class="relative text-center">
                            <div class="relative inline-block mb-6">
                                @if($salesPackage->image && Storage::disk('public')->exists($salesPackage->image))
                                    <img src="{{ Storage::url($salesPackage->image) }}" alt="{{ $salesPackage->name }}"
                                         class="w-32 h-32 rounded-full border-4 border-white shadow-2xl object-cover">
                                @else
                                    <div class="w-32 h-32 rounded-full border-4 border-white shadow-2xl bg-gradient-to-br from-orange-400 to-red-500 flex items-center justify-center">
                                        <span class="text-4xl font-bold text-white">{{ strtoupper(substr($salesPackage->name, 0, 1)) }}</span>
                                    </div>
                                @endif

                                <div class="absolute -bottom-2 -right-2 w-8 h-8 {{ $salesPackage->is_active ? 'bg-green-500' : 'bg-gray-400' }} rounded-full border-4 border-white flex items-center justify-center">
                                    @if($salesPackage->is_active)
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                                        </svg>
                                    @endif
                                </div>
                            </div>

                            <h2 class="text-3xl font-bold text-white mb-2">{{ $salesPackage->name }}</h2>
                            <p class="text-orange-100 text-lg">Kode: <span class="badge bg-white/20 px-2 py-1 rounded">{{ $salesPackage->code }}</span></p>

                            <div class="mt-4">
                                @if($salesPackage->category)
                                    <span class="inline-flex items-center px-4 py-2 bg-info-100 text-white/90 rounded-full text-sm font-semibold">
                                        <div class="w-2 h-2 bg-white mr-2 rounded-full"></div>
                                        {{ $salesPackage->category->name ?? $salesPackage->category }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-800 rounded-full text-sm font-semibold">
                                        Tanpa Kategori
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Body --}}
                    <div class="p-8">
                        <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                            <svg class="w-5 h-5 mr-3 text-orange-500" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                            </svg>
                            Informasi Paket
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="p-4 bg-gradient-to-r from-yellow-50 to-amber-50 rounded-xl border border-yellow-100">
                                <h4 class="font-semibold text-gray-800 mb-2">Harga Dasar</h4>
                                <div class="text-2xl font-bold">Rp {{ number_format($salesPackage->base_price, 0, ',', '.') }}</div>
                                <p class="text-sm text-gray-500 mt-2">Harga akumulasi komponen paket</p>
                            </div>

                            <div class="p-4 bg-gradient-to-r from-indigo-50 to-purple-50 rounded-xl border border-indigo-100">
                                <h4 class="font-semibold text-gray-800 mb-2">Status</h4>
                                @if($salesPackage->is_active)
                                    <span class="inline-flex items-center px-3 py-1.5 bg-green-100 text-green-800 rounded-full text-sm font-semibold">
                                        <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1.5 bg-gray-100 text-gray-800 rounded-full text-sm font-semibold">
                                        <div class="w-2 h-2 bg-gray-500 rounded-full mr-2"></div>
                                        Tidak Aktif
                                    </span>
                                @endif

                                <div class="mt-3 text-sm text-gray-500">
                                    Dibuat oleh: {{ $salesPackage->creator->name ?? 'System' }} <br>
                                    Dibuat: {{ $salesPackage->created_at->format('d/m/Y H:i') }}
                                </div>
                            </div>

                            @if($salesPackage->description)
                                <div class="md:col-span-2 p-4 bg-white rounded-xl border border-gray-100">
                                    <h4 class="font-semibold text-gray-800 mb-2">Deskripsi</h4>
                                    <p class="text-gray-600">{{ $salesPackage->description }}</p>
                                </div>
                            @endif
                        </div>

                        {{-- Paket Komponen --}}
                        <div class="mt-6">
                            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                                <div class="px-6 py-4 bg-gradient-to-r from-indigo-500 to-purple-600">
                                    <h4 class="text-white font-bold flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24"><path d="M3 7h18v2H3zM3 11h18v2H3zM3 15h18v2H3z"/></svg>
                                        Komponen Paket
                                    </h4>
                                </div>
                                <div class="p-4">
                                    <div class="table-responsive">
                                        <table class="w-full text-sm">
                                            <thead class="text-gray-600 text-left border-b">
                                                <tr>
                                                    <th class="pb-2">#</th>
                                                    <th class="pb-2">Produk</th>
                                                    <th class="pb-2 text-center">Jumlah</th>
                                                    <th class="pb-2 text-right">Harga Satuan</th>
                                                    <th class="pb-2 text-right">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($salesPackage->packageItems as $item)
                                                    <tr class="border-b last:border-b-0">
                                                        <td class="py-3 align-top">{{ $loop->iteration }}</td>
                                                        <td class="py-3 align-top">
                                                            <div class="flex items-center">
                                                                @if($item->finishedProduct->photo)
                                                                    <img src="{{ Storage::url($item->finishedProduct->photo) }}" alt="{{ $item->finishedProduct->name }}"
                                                                         class="w-10 h-10 rounded mr-3 object-cover">
                                                                @else
                                                                    <div class="w-10 h-10 rounded bg-gray-100 mr-3 flex items-center justify-center">
                                                                        <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                                                            <path d="M3 3h18v18H3z"/>
                                                                        </svg>
                                                                    </div>
                                                                @endif
                                                                <div>
                                                                    <div class="font-semibold text-gray-800">{{ $item->finishedProduct->name }}</div>
                                                                    <div class="text-xs text-gray-500">{{ $item->finishedProduct->category->name ?? 'Tanpa Kategori' }}</div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="py-3 text-center align-top">
                                                            <span class="inline-flex items-center px-2 py-1 bg-info-50 text-info-700 rounded">
                                                                {{ number_format($item->quantity, ($item->quantity == floor($item->quantity)) ? 0 : 2) }}
                                                                {{ $item->finishedProduct->unit->abbreviation ?? 'pcs' }}
                                                            </span>
                                                        </td>
                                                        <td class="py-3 text-right align-top">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                                        <td class="py-3 text-right align-top font-bold">Rp {{ number_format($item->total_price, 0, ',', '.') }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="4" class="text-right py-3 font-medium">Total Harga Dasar:</td>
                                                    <td class="text-right py-3 font-bold">Rp {{ number_format($salesPackage->base_price, 0, ',', '.') }}</td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Branch Availability --}}
                        <div class="mt-6">
                            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                                <div class="px-6 py-4 bg-gradient-to-r from-green-500 to-teal-600">
                                    <h4 class="text-white font-bold flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24"><path d="M3 7h18v2H3zM3 11h18v2H3zM3 15h18v2H3z"/></svg>
                                        Ketersediaan per Cabang
                                    </h4>
                                </div>
                                <div class="p-4">
                                    <div class="space-y-3">
                                        @foreach($branchAvailability as $branchId => $availability)
                                            <div class="flex items-center justify-between p-3 rounded-lg border {{ $availability['is_available'] ? 'border-green-100 bg-green-50' : 'border-red-100 bg-red-50' }}">
                                                <div>
                                                    <div class="font-semibold text-gray-800">{{ $availability['name'] }}</div>
                                                    <div class="text-sm text-gray-500">
                                                        @if($availability['is_available'])
                                                            Semua komponen tersedia
                                                        @else
                                                            Stok komponen tidak mencukupi
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="text-right">
                                                    <div>
                                                        @if($availability['is_available'])
                                                            <span class="inline-flex items-center px-3 py-1 rounded-full bg-green-100 text-green-700 font-semibold">Tersedia</span>
                                                        @else
                                                            <span class="inline-flex items-center px-3 py-1 rounded-full bg-red-100 text-red-700 font-semibold">Tidak Tersedia</span>
                                                        @endif
                                                    </div>
                                                    <div class="text-sm text-gray-600 mt-1">
                                                        {{ $availability['available_quantity'] }} paket
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            {{-- Sidebar --}}
            <div class="space-y-6">
                {{-- Pricing Summary --}}
                <div class="bg-white rounded-2xl shadow-xl border border-gray-200/50 overflow-hidden sticky top-6">
                    <div class="bg-gradient-to-r from-yellow-400 to-yellow-300 px-6 py-4">
                        <h3 class="text-lg font-bold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M11 17h2v-2h-2v2zm1-15C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"/>
                            </svg>
                            Ringkasan Harga
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-3">
                            <span>Harga Dasar:</span>
                            <span class="font-bold">Rp {{ number_format($salesPackage->base_price, 0, ',', '.') }}</span>
                        </div>

                        @if($salesPackage->discount_percentage > 0 || $salesPackage->discount_amount > 0)
                            <div class="flex justify-between items-center mb-3">
                                <span>Diskon:</span>
                                <span class="text-green-600 font-bold">
                                    @if($salesPackage->discount_percentage > 0)
                                        -{{ $salesPackage->discount_percentage }}% (Rp {{ number_format(($salesPackage->base_price * $salesPackage->discount_percentage) / 100, 0, ',', '.') }})
                                    @else
                                        -Rp {{ number_format($salesPackage->discount_amount, 0, ',', '.') }}
                                    @endif
                                </span>
                            </div>
                        @endif

                        @if($salesPackage->additional_charge > 0)
                            <div class="flex justify-between items-center mb-3">
                                <span>Biaya Tambahan:</span>
                                <span class="text-yellow-700 font-bold">+Rp {{ number_format($salesPackage->additional_charge, 0, ',', '.') }}</span>
                            </div>
                        @endif

                        <hr class="my-3">

                        <div class="flex justify-between items-center">
                            <span class="text-lg font-medium">Harga Jual Final:</span>
                            <span class="text-2xl text-primary font-bold">Rp {{ number_format($salesPackage->final_price, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="bg-white rounded-2xl shadow-xl border border-gray-200/50 overflow-hidden">
                    <div class="p-6">
                        <div class="flex flex-col gap-3">
                            <a href="{{ route('sales-packages.edit', $salesPackage) }}" class="inline-flex items-center justify-center w-full px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-xl font-medium">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04a1 1 0 000-1.41L18.37 3.29a1 1 0 00-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
                                Edit Paket
                            </a>

                            <form action="{{ route('sales-packages.toggle-status', $salesPackage) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 {{ $salesPackage->is_active ? 'bg-red-600 text-white' : 'bg-green-600 text-white' }} rounded-xl font-medium">
                                    @if($salesPackage->is_active)
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        Nonaktifkan Paket
                                    @else
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14M5 12h14"/></svg>
                                        Aktifkan Paket
                                    @endif
                                </button>
                            </form>

                            <a href="{{ route('sales-packages.index') }}" class="inline-flex items-center justify-center w-full px-4 py-2 border border-gray-200 rounded-xl text-gray-700">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24"><path d="M10 19l-7-7 7-7v14zM12 5h2v14h-2z"/></svg>
                                Kembali ke Daftar
                            </a>
                        </div>
                    </div>
                </div>

                {{-- System Info --}}
                <div class="bg-white rounded-2xl shadow-xl border border-gray-200/50 overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-600 to-gray-700 px-6 py-4">
                        <h3 class="text-lg font-bold text-white flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                            </svg>
                            Info Sistem
                        </h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Package ID</span>
                            <span class="font-mono text-gray-700">{{ $salesPackage->id }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Dibuat</span>
                            <span class="text-gray-700">{{ $salesPackage->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Diperbarui</span>
                            <span class="text-gray-700">{{ $salesPackage->updated_at->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
