@extends('layouts.app')

@php
use Illuminate\Support\Facades\Storage;
@endphp

@section('title', 'Detail Bahan Baku')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-orange-50 via-red-50 to-orange-100">
    {{-- Header Section --}}
    <div class="bg-gradient-to-r from-orange-900 via-orange-800 to-red-900 shadow-xl">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('raw-materials.index') }}"
                       class="inline-flex items-center justify-center w-10 h-10 bg-white/10 hover:bg-white/20 rounded-xl transition-all duration-200 backdrop-blur-sm border border-white/20 group">
                        <svg class="w-5 h-5 text-white group-hover:text-orange-200 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-2xl lg:text-3xl font-bold text-white">Detail Bahan Baku</h1>
                        <p class="text-orange-200 mt-1">Informasi lengkap bahan baku: {{ $rawMaterial->name }}</p>
                    </div>
                </div>

                <div class="flex items-center space-x-3">
                    <a href="{{ route('raw-materials.edit', $rawMaterial) }}"
                       class="inline-flex items-center px-4 py-2.5 bg-white/10 hover:bg-white/20 text-white rounded-xl font-medium transition-all duration-200 backdrop-blur-sm border border-white/20 group">
                        <svg class="w-4 h-4 mr-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit
                    </a>

                    <button onclick="confirmDelete()" 
                            class="inline-flex items-center px-4 py-2.5 bg-red-600/80 hover:bg-red-600 text-white rounded-xl font-medium transition-all duration-200 backdrop-blur-sm border border-red-500/30 group">
                        <svg class="w-4 h-4 mr-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Content --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Main Card --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-xl border border-gray-200/50 overflow-hidden">
                    <div class="relative bg-gradient-to-r from-orange-500 via-red-500 to-orange-600 px-8 py-12">
                        <div class="absolute inset-0 bg-black/10"></div>
                        <div class="relative text-center">
                            {{-- Image --}}
                            <div class="relative inline-block mb-6">
                                @if($rawMaterial->image)
                                    <img src="{{ Storage::url($rawMaterial->image) }}" alt="{{ $rawMaterial->name }}"
                                         class="w-32 h-32 rounded-full border-4 border-white shadow-2xl object-cover">
                                @else
                                    <div class="w-32 h-32 rounded-full border-4 border-white shadow-2xl bg-gradient-to-br from-orange-400 to-red-500 flex items-center justify-center">
                                        <span class="text-4xl font-bold text-white">{{ strtoupper(substr($rawMaterial->name, 0, 1)) }}</span>
                                    </div>
                                @endif

                                <div class="absolute -bottom-2 -right-2 w-8 h-8 bg-green-500 rounded-full border-4 border-white flex items-center justify-center">
                                    @if($rawMaterial->is_active)
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

                            <h2 class="text-3xl font-bold text-white mb-2">{{ $rawMaterial->name }}</h2>
                            <p class="text-orange-100 text-lg">@if($rawMaterial->code) Kode: {{ $rawMaterial->code }} @endif</p>

                            <div class="mt-4">
                                @if($rawMaterial->is_active)
                                    <span class="inline-flex items-center px-4 py-2 bg-green-100 text-green-800 rounded-full text-sm font-semibold">
                                        <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-800 rounded-full text-sm font-semibold">
                                        <div class="w-2 h-2 bg-gray-500 rounded-full mr-2"></div>
                                        Tidak Aktif
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Details --}}
                    <div class="p-8">
                        <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                            <svg class="w-5 h-5 mr-3 text-orange-500" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                            </svg>
                            Informasi Detail
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border border-blue-100">
                                <h4 class="font-semibold text-gray-700 mb-1">Kategori</h4>
                                @if($rawMaterial->category)
                                    <p class="font-medium text-gray-800">{{ $rawMaterial->category->name }}</p>
                                @else
                                    <p class="text-gray-400">-</p>
                                @endif
                            </div>

                            <div class="p-4 bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl border border-purple-100">
                                <h4 class="font-semibold text-gray-700 mb-1">Satuan</h4>
                                <p class="font-medium text-gray-800">{{ $rawMaterial->unit ? (is_object($rawMaterial->unit) ? $rawMaterial->unit->unit_name : $rawMaterial->unit) : '-' }}</p>
                            </div>

                            <div class="p-4 bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl border border-green-100">
                                <h4 class="font-semibold text-gray-700 mb-1">Harga Satuan</h4>
                                @if($rawMaterial->unit_price)
                                    <p class="font-bold text-success text-green-700">Rp {{ number_format($rawMaterial->unit_price, 0, ',', '.') }}</p>
                                @else
                                    <p class="text-gray-400">-</p>
                                @endif
                            </div>

                            <div class="p-4 bg-gradient-to-r from-orange-50 to-amber-50 rounded-xl border border-orange-100">
                                <h4 class="font-semibold text-gray-700 mb-1">Supplier</h4>
                                <p class="font-medium text-gray-800">{{ $rawMaterial->supplier ? $rawMaterial->supplier->name : '-' }}</p>
                            </div>

                            @if($rawMaterial->description)
                                <div class="md:col-span-2 p-4 bg-white rounded-xl border border-gray-100">
                                    <h4 class="font-semibold text-gray-700 mb-2">Deskripsi</h4>
                                    <p class="text-gray-600 italic">"{{ $rawMaterial->description }}"</p>
                                </div>
                            @endif
                        </div>

                        <hr class="my-6">

                        {{-- Stock Info --}}
                        @php
                            $currentStock = $rawMaterial->current_stock ?? 0;
                            $minStock = $rawMaterial->minimum_stock ?? 0;
                            $isLowStock = $currentStock < $minStock;
                        @endphp

                        <h4 class="text-lg font-semibold text-gray-800 mb-4">Informasi Stok</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="p-4 bg-white rounded-xl border border-gray-100 flex items-center justify-between">
                                <div>
                                    <div class="text-sm text-gray-500">Stok Saat Ini</div>
                                    <div class="flex items-center mt-2">
                                        <span class="font-bold text-2xl {{ $isLowStock ? 'text-red-600' : 'text-green-600' }}">
                                            {{ number_format($currentStock, 0) }}
                                        </span>
                                        <span class="text-sm text-gray-500 ml-3">{{ is_object($rawMaterial->unit) ? $rawMaterial->unit->unit_name : '' }}</span>
                                        @if($isLowStock)
                                            <svg class="w-5 h-5 text-yellow-500 ml-3" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M1 21h22L12 2 1 21zM12 16v-4m0 6h.01"/>
                                            </svg>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="p-4 bg-white rounded-xl border border-gray-100">
                                <div class="text-sm text-gray-500">Stok Minimum</div>
                                <div class="mt-2 font-semibold text-gray-800">
                                    {{ number_format($minStock, 0) }}
                                    <span class="text-sm text-gray-500 ml-2">{{ is_object($rawMaterial->unit) ? $rawMaterial->unit->unit_name : '' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                {{-- Actions --}}
                <div class="bg-white rounded-2xl shadow-xl border border-gray-200/50 overflow-hidden">
                    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-4">
                        <h3 class="text-lg font-bold text-white flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12,1L3,5V11C3,16.55 6.84,21.74 12,23C17.16,21.74 21,16.55 21,11V5L12,1M10,17L6,13L7.41,11.59L10,14.17L16.59,7.58L18,9L10,17Z"/>
                            </svg>
                            Aksi
                        </h3>
                    </div>
                    <div class="p-6 space-y-3">
                        <a href="{{ route('raw-materials.edit', $rawMaterial) }}" class="flex items-center justify-between p-3 bg-blue-50 hover:bg-blue-100 rounded-xl transition-colors">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04a1 1 0 0 0 0-1.41l-2.34-2.34a1 1 0 0 0-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="font-medium text-gray-800">Edit Bahan Baku</p>
                                    <p class="text-sm text-gray-500">Ubah informasi bahan baku</p>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 4l-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8z"/>
                            </svg>
                        </a>

                        <button onclick="confirmDelete()" class="w-full inline-flex items-center justify-center p-3 bg-red-50 hover:bg-red-100 rounded-xl transition-colors">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-red-500 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M9 3v1H4v2h1v13a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6h1V4h-5V3H9zM7 6h10v13H7V6z"/>
                                    </svg>
                                </div>
                                <div class="ml-3 text-left">
                                    <p class="font-medium text-gray-800">Hapus Bahan Baku</p>
                                    <p class="text-sm text-gray-500">Tindakan ini tidak bisa dibatalkan</p>
                                </div>
                            </div>
                        </button>

                        <form id="delete-form" action="{{ route('raw-materials.destroy', $rawMaterial) }}" method="POST" class="hidden">
                            @csrf
                            @method('DELETE')
                        </form>
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
                            <span class="text-gray-500">ID</span>
                            <span class="font-mono text-gray-700">{{ $rawMaterial->id }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Dibuat</span>
                            <span class="text-gray-700">{{ $rawMaterial->created_at ? $rawMaterial->created_at->format('d/m/Y H:i') : '-' }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Diperbarui</span>
                            <span class="text-gray-700">{{ $rawMaterial->updated_at ? $rawMaterial->updated_at->format('d/m/Y H:i') : '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection
