@extends('layouts.app')

@php
use Illuminate\Support\Facades\Storage;
@endphp

@section('title', 'Detail Bahan Setengah Jadi - ' . $semiFinishedProduct->name)

@section('content')
<div class="min-h-screen bg-gradient-to-br from-orange-50 via-red-50 to-orange-100">
    {{-- Header --}}
    <div class="bg-gradient-to-r from-orange-900 via-orange-800 to-red-900 shadow-xl">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('semi-finished-products.index', request()->has('branch_id') ? ['branch_id' => request('branch_id')] : []) }}"
                       class="inline-flex items-center justify-center w-10 h-10 bg-white/10 hover:bg-white/20 rounded-xl transition-all duration-200 backdrop-blur-sm border border-white/20 group">
                        <svg class="w-5 h-5 text-white group-hover:text-orange-200 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-2xl lg:text-3xl font-bold text-white">
                            <i class="bi bi-box-seam me-2"></i>Detail Bahan Setengah Jadi
                        </h1>
                        <p class="text-orange-200 mt-1">Informasi lengkap: {{ $semiFinishedProduct->name }}</p>
                    </div>
                </div>

                <div class="flex items-center space-x-3">
                    <a href="{{ route('semi-finished-products.edit', array_merge([$semiFinishedProduct], request()->has('branch_id') ? ['branch_id' => request('branch_id')] : [])) }}"
                       class="inline-flex items-center px-4 py-2.5 bg-white/10 hover:bg-white/20 text-white rounded-xl font-medium transition-all duration-200 backdrop-blur-sm border border-white/20 group">
                        <svg class="w-4 h-4 mr-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit
                    </a>

                    <button onclick="confirmDelete()" 
                            class="inline-flex items-center px-4 py-2.5 bg-red-600/80 hover:bg-red-600 text-white rounded-xl font-medium transition-all duration-200 backdrop-blur-sm border border-red-500/30 group">
                        <svg class="w-4 h-4 mr-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
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
                    {{-- Header / Avatar --}}
                    <div class="relative bg-gradient-to-r from-orange-500 via-red-500 to-orange-600 px-8 py-12">
                        <div class="absolute inset-0 bg-black/10"></div>
                        <div class="relative text-center">
                            <div class="relative inline-block mb-6">
                                @if($semiFinishedProduct->image)
                                    <img src="{{ Storage::url($semiFinishedProduct->image) }}" alt="{{ $semiFinishedProduct->name }}"
                                         class="w-32 h-32 rounded-full border-4 border-white shadow-2xl object-cover">
                                @else
                                    <div class="w-32 h-32 rounded-full border-4 border-white shadow-2xl bg-gradient-to-br from-orange-400 to-red-500 flex items-center justify-center">
                                        <span class="text-4xl font-bold text-white">{{ strtoupper(substr($semiFinishedProduct->name, 0, 1)) }}</span>
                                    </div>
                                @endif

                                <div class="absolute -bottom-2 -right-2 w-8 h-8 bg-green-500 rounded-full border-4 border-white flex items-center justify-center">
                                    @if($semiFinishedProduct->is_active)
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

                            <h2 class="text-3xl font-bold text-white mb-2">{{ $semiFinishedProduct->name }}</h2>
                            <p class="text-orange-100 text-lg">
                                @if($semiFinishedProduct->code)
                                    <span class="inline-flex items-center px-3 py-1 bg-white/10 rounded-full text-sm">{{ $semiFinishedProduct->code }}</span>
                                @else
                                    -
                                @endif
                            </p>

                            <div class="mt-4">
                                @if($semiFinishedProduct->is_active)
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
                                <h4 class="font-semibold text-gray-800 mb-1">Kategori</h4>
                                @if($semiFinishedProduct->category)
                                    <p class="font-medium text-gray-700">{{ $semiFinishedProduct->category->name }}</p>
                                @else
                                    <p class="text-gray-400">-</p>
                                @endif
                            </div>

                            <div class="p-4 bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl border border-purple-100">
                                <h4 class="font-semibold text-gray-800 mb-1">Satuan</h4>
                                <p class="text-gray-700">{{ $semiFinishedProduct->unit ? (is_object($semiFinishedProduct->unit) ? $semiFinishedProduct->unit->unit_name : $semiFinishedProduct->unit) : '-' }}</p>
                            </div>

                            <div class="p-4 bg-gradient-to-r from-emerald-50 to-green-50 rounded-xl border border-green-100">
                                <h4 class="font-semibold text-gray-800 mb-1">Biaya Produksi</h4>
                                <p class="font-bold text-success">
                                    @if($semiFinishedProduct->production_cost)
                                        Rp {{ number_format($semiFinishedProduct->production_cost, 0, ',', '.') }}
                                    @else
                                        -
                                    @endif
                                </p>
                            </div>

                            <div class="p-4 bg-gradient-to-r from-orange-50 to-amber-50 rounded-xl border border-orange-100">
                                <h4 class="font-semibold text-gray-800 mb-1">Stok Minimum</h4>
                                <p class="font-semibold text-gray-700">{{ number_format($displayMinimumStock, 0, ',', '.') }} <small class="text-muted">{{ $semiFinishedProduct->unit->unit_name ?? '' }}</small></p>
                            </div>
                        </div>

                        <div class="mt-6">
                            <h4 class="font-semibold text-gray-800 mb-2">Deskripsi</h4>
                            @if($semiFinishedProduct->description)
                                <p class="text-gray-600 italic">"{{ $semiFinishedProduct->description }}"</p>
                            @else
                                <p class="text-gray-400 italic">Tidak ada deskripsi.</p>
                            @endif
                        </div>

                        <hr class="my-6">

                        {{-- Stock Summary --}}
                        <h6 class="fw-bold mb-3 flex items-center text-gray-800"><i class="bi bi-archive me-2"></i>Informasi Stok</h6>
                        <div class="alert alert-info d-flex align-items-center p-4 rounded-xl bg-blue-50 border border-blue-100">
                            <i class="bi bi-info-circle-fill me-2 text-blue-600"></i>
                            <div class="text-sm text-gray-700">
                                Stok ditampilkan untuk:
                                <span class="font-semibold">
                                @if($branchForStock)
                                    {{ $branchForStock->name }}
                                @elseif($selectedBranch)
                                    {{ $selectedBranch->name }}
                                @else
                                    Semua Cabang
                                @endif
                                </span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                            <div class="p-4 bg-white rounded-xl border border-gray-100">
                                <label class="form-label text-muted fw-semibold">Stok Saat Ini</label>
                                <div class="d-flex align-items-center mt-2">
                                    <span class="fw-bold fs-4 {{ $displayStockQuantity > $displayMinimumStock ? 'text-success' : 'text-danger' }}">
                                        {{ number_format($displayStockQuantity, 0, ',', '.') }}
                                    </span>
                                    <small class="text-muted ms-2">{{ $semiFinishedProduct->unit->unit_name ?? '' }}</small>
                                </div>
                            </div>

                            <div class="p-4 bg-white rounded-xl border border-gray-100">
                                <label class="form-label text-muted fw-semibold">Stok Minimum</label>
                                <p class="fw-semibold mb-0 mt-2">{{ number_format($displayMinimumStock, 0, ',', '.') }} <small class="text-muted">{{ $semiFinishedProduct->unit->unit_name ?? '' }}</small></p>
                            </div>
                        </div>

                        {{-- Branch Stocks --}}
                        @if(!$branchForStock && !$selectedBranch && $semiFinishedProduct->semiFinishedBranchStocks->isNotEmpty())
                            <hr class="my-6">
                            <h6 class="fw-bold mb-3 flex items-center text-gray-800"><i class="bi bi-building me-2"></i>Rincian Stok Per Cabang</h6>
                            <ul class="space-y-3">
                                @foreach($semiFinishedProduct->semiFinishedBranchStocks as $branchStock)
                                    <li class="flex items-center justify-between p-4 bg-white rounded-xl border border-gray-100">
                                        <div>
                                            <div class="font-semibold text-gray-800">{{ $branchStock->branch->name ?? 'Unknown' }}</div>
                                            <small class="text-gray-500">{{ $branchStock->branch->code ?? '' }}</small>
                                        </div>
                                        <div class="text-right">
                                            <div class="font-semibold {{ $branchStock->quantity > 0 ? 'text-green-600' : 'text-red-600' }}">
                                                {{ number_format($branchStock->quantity, 0, ',', '.') }}
                                            </div>
                                            <small class="text-gray-500">{{ $semiFinishedProduct->unit->unit_name ?? '' }}</small>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                {{-- Actions Card --}}
                <div class="bg-white rounded-2xl shadow-xl border border-gray-200/50 overflow-hidden">
                    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-4">
                        <h3 class="text-lg font-bold text-white flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12,1L3,5V11C3,16.55 6.84,21.74 12,23C17.16,21.74 21,16.55 21,11V5L12,1M10,17L6,13L7.41,11.59L10,14.17L16.59,7.58L18,9L10,17Z"/>
                            </svg>
                            Aksi
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="d-grid gap-2">
                            <a href="{{ route('semi-finished-products.edit', array_merge([$semiFinishedProduct], request()->has('branch_id') ? ['branch_id' => request('branch_id')] : [])) }}" class="inline-flex items-center justify-center px-4 py-2 bg-yellow-400 hover:bg-yellow-300 text-white rounded-xl font-medium">
                                <i class="bi bi-pencil me-2"></i>Edit Bahan
                            </a>

                            <button type="button" onclick="confirmDelete()" class="inline-flex items-center justify-center px-4 py-2 bg-red-600 hover:bg-red-500 text-white rounded-xl font-medium">
                                <i class="bi bi-trash me-2"></i>Hapus Bahan
                            </button>
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
                            <span class="text-gray-500">Produk ID</span>
                            <span class="font-mono text-gray-700">{{ $semiFinishedProduct->id }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Dibuat</span>
                            <span class="text-gray-700">{{ $semiFinishedProduct->created_at ? $semiFinishedProduct->created_at->format('d/m/Y H:i') : '-' }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Diperbarui</span>
                            <span class="text-gray-700">{{ $semiFinishedProduct->updated_at ? $semiFinishedProduct->updated_at->format('d/m/Y H:i') : '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Small script to toggle modal using the 'show' class for progressive enhancement --}}
<script>
    (function(){
        // reflect 'show' class by toggling hidden / flex
        const observer = new MutationObserver(() => {
            const modal = document.getElementById('deleteModal');
            if(!modal) return;
            if(modal.classList.contains('show')){
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }else{
                modal.classList.remove('flex');
                modal.classList.add('hidden');
            }
        });
        observer.observe(document.getElementById('deleteModal'), { attributes: true, attributeFilter: ['class'] });
    })();
</script>
@endsection
