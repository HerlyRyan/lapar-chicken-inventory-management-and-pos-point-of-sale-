@extends('layouts.app')

@php
use Illuminate\Support\Facades\Storage;
@endphp

@section('title', 'Detail Produk Siap Jual - ' . $finishedProduct->name)

@section('content')
<div class="min-h-screen bg-gradient-to-br from-orange-50 via-red-50 to-orange-100">
    {{-- Header --}}
    <div class="bg-gradient-to-r from-orange-900 via-orange-800 to-red-900 shadow-xl">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('finished-products.index', request()->has('branch_id') ? ['branch_id' => request('branch_id')] : []) }}"
                       class="inline-flex items-center justify-center w-10 h-10 bg-white/10 hover:bg-white/20 rounded-xl transition-all duration-200 backdrop-blur-sm border border-white/20 group">
                        <svg class="w-5 h-5 text-white group-hover:text-orange-200 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-2xl lg:text-3xl font-bold text-white">Detail Produk Siap Jual</h1>
                        <p class="text-orange-200 mt-1">Informasi lengkap: {{ $finishedProduct->name }}</p>
                    </div>
                </div>

                <div class="flex items-center space-x-3">
                    <a href="{{ route('finished-products.edit', array_merge([$finishedProduct], request()->has('branch_id') ? ['branch_id' => request('branch_id')] : [])) }}"
                       class="inline-flex items-center px-4 py-2.5 bg-white/10 hover:bg-white/20 text-white rounded-xl font-medium transition-all duration-200 backdrop-blur-sm border border-white/20 group">
                        <svg class="w-4 h-4 mr-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit
                    </a>

                    <button onclick="toggleAdjustModal(true)"
                            class="inline-flex items-center px-4 py-2.5 bg-blue-600/80 hover:bg-blue-600 text-white rounded-xl font-medium transition-all duration-200 backdrop-blur-sm border border-blue-500/30 group">
                        <svg class="w-4 h-4 mr-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4M8 15l4 4 4-4"/>
                        </svg>
                        Sesuaikan Stok
                    </button>

                    @if(auth()->id() !== $finishedProduct->id ?? true)
                        <button onclick="confirmDelete()" 
                                class="inline-flex items-center px-4 py-2.5 bg-red-600/80 hover:bg-red-600 text-white rounded-xl font-medium transition-all duration-200 backdrop-blur-sm border border-red-500/30 group">
                            <svg class="w-4 h-4 mr-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Hapus
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Content --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Main Column --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-xl border border-gray-200/50 overflow-hidden">
                    <div class="relative bg-gradient-to-r from-orange-500 via-red-500 to-orange-600 px-8 py-12">
                        <div class="absolute inset-0 bg-black/10"></div>
                        <div class="relative text-center">
                            <div class="relative inline-block mb-6">
                                @if($finishedProduct->photo || $finishedProduct->image)
                                    <img src="{{ $finishedProduct->photo ? Storage::url($finishedProduct->photo) : Storage::url($finishedProduct->image) }}"
                                         alt="{{ $finishedProduct->name }}"
                                         class="w-40 h-40 rounded-full border-4 border-white shadow-2xl object-cover">
                                @else
                                    <div class="w-40 h-40 rounded-full border-4 border-white shadow-2xl bg-gradient-to-br from-orange-400 to-red-500 flex items-center justify-center">
                                        <span class="text-4xl font-bold text-white">{{ strtoupper(substr($finishedProduct->name, 0, 1)) }}</span>
                                    </div>
                                @endif

                                <div class="absolute -bottom-3 -right-3 w-10 h-10 bg-green-500 rounded-full border-4 border-white flex items-center justify-center">
                                    @if($finishedProduct->is_active)
                                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                                        </svg>
                                    @else
                                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                                        </svg>
                                    @endif
                                </div>
                            </div>

                            <h2 class="text-3xl font-bold text-white mb-2">{{ $finishedProduct->name }}</h2>
                            <p class="text-orange-100 text-lg">{{ $finishedProduct->code ? 'Kode: ' . $finishedProduct->code : '' }}</p>

                            <div class="mt-4">
                                @if($finishedProduct->is_active)
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

                    <div class="p-8">
                        <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                            <svg class="w-5 h-5 mr-3 text-orange-500" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                            </svg>
                            Informasi Produk
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div class="p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border border-blue-100">
                                <h4 class="font-semibold text-gray-700 mb-1">Kategori</h4>
                                @if($finishedProduct->category)
                                    <p class="font-medium text-gray-800">{{ $finishedProduct->category->name }}</p>
                                @else
                                    <p class="text-gray-400">-</p>
                                @endif
                            </div>

                            <div class="p-4 bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl border border-purple-100">
                                <h4 class="font-semibold text-gray-700 mb-1">Satuan</h4>
                                <p class="font-medium text-gray-800">{{ $finishedProduct->unit ? (is_object($finishedProduct->unit) ? $finishedProduct->unit->unit_name : $finishedProduct->unit) : '-' }}</p>
                            </div>

                            <div class="p-4 bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl border border-green-100">
                                <h4 class="font-semibold text-gray-700 mb-1">Harga Jual</h4>
                                <p class="font-bold text-success text-lg">
                                    @if($finishedProduct->price)
                                        Rp {{ number_format($finishedProduct->price, 0, ',', '.') }}
                                    @else
                                        -
                                    @endif
                                </p>
                            </div>

                            <div class="p-4 bg-gradient-to-r from-amber-50 to-yellow-50 rounded-xl border border-amber-100">
                                <h4 class="font-semibold text-gray-700 mb-1">Modal Dasar</h4>
                                <p class="font-semibold text-gray-800">
                                    @if(!is_null($finishedProduct->production_cost))
                                        Rp {{ number_format($finishedProduct->production_cost, 0, ',', '.') }}
                                    @else
                                        -
                                    @endif
                                </p>
                            </div>
                        </div>

                        @if($finishedProduct->description)
                            <p class="mb-6 text-gray-600 italic">"{{ $finishedProduct->description }}"</p>
                        @else
                            <p class="mb-6 text-gray-400 italic">Tidak ada deskripsi.</p>
                        @endif

                        <hr class="my-6">

                        <h4 class="text-lg font-bold text-gray-800 mb-3 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-orange-500" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M3 13h2v-2H3v2zm4 0h14v-2H7v2zM3 7h2V5H3v2zm4 0h14V5H7v2zM3 19h2v-2H3v2zm4 0h14v-2H7v2z"/>
                            </svg>
                            Informasi Stok
                        </h4>

                        <div class="mb-4 p-4 bg-blue-50 rounded-lg border border-blue-100 flex items-center gap-3">
                            <svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L2 7v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5z"/></svg>
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

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <div class="p-4 bg-white rounded-xl border border-gray-100">
                                <label class="text-sm text-gray-500">Stok Saat Ini</label>
                                <div class="flex items-baseline gap-3 mt-2">
                                    <span class="text-3xl font-bold {{ $displayStockQuantity > ($finishedProduct->minimum_stock ?? 0) ? 'text-green-600' : 'text-red-600' }}">
                                        {{ number_format($displayStockQuantity, 0, ',', '.') }}
                                    </span>
                                    <small class="text-gray-500">{{ $finishedProduct->unit->unit_name ?? '' }}</small>
                                </div>
                            </div>

                            <div class="p-4 bg-white rounded-xl border border-gray-100">
                                <label class="text-sm text-gray-500">Stok Minimum</label>
                                <div class="mt-2">
                                    <span class="font-semibold">{{ number_format($finishedProduct->minimum_stock ?? 0, 0, ',', '.') }}</span>
                                    <small class="text-gray-500"> {{ $finishedProduct->unit->unit_name ?? '' }}</small>
                                </div>
                            </div>
                        </div>

                        @if(!$branchForStock && !$selectedBranch)
                            <hr class="my-4">
                            <h5 class="text-lg font-bold text-gray-800 mb-3 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-orange-500" fill="currentColor" viewBox="0 0 24 24"><path d="M3 13h2v-2H3v2zm4 0h14v-2H7v2zM3 7h2V5H3v2zm4 0h14V5H7v2zM3 19h2v-2H3v2zm4 0h14v-2H7v2z"/></svg>
                                Rincian Stok Per Cabang
                            </h5>

                            <ul class="space-y-3">
                                @foreach($branches->where('type', 'branch') as $branch)
                                    @php
                                        $branchStock = $finishedProduct->finishedBranchStocks->firstWhere('branch_id', $branch->id);
                                        $quantity = $branchStock ? $branchStock->quantity : 0;
                                    @endphp
                                    <li class="flex justify-between items-center p-4 bg-white rounded-xl border border-gray-100">
                                        <div>
                                            <div class="font-semibold text-gray-800">{{ $branch->name ?? 'Unknown' }}</div>
                                            <small class="text-gray-500">{{ $branch->code ?? '' }}</small>
                                        </div>
                                        <div class="text-right">
                                            <div class="font-semibold {{ $quantity > 0 ? 'text-green-600' : 'text-red-600' }}">{{ number_format($quantity, 0, ',', '.') }}</div>
                                            <small class="text-gray-500">{{ $finishedProduct->unit->unit_name ?? '' }}</small>
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
                {{-- Actions --}}
                <div class="bg-white rounded-2xl shadow-xl border border-gray-200/50 overflow-hidden">
                    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-4">
                        <h3 class="text-lg font-bold text-white flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 24 24"><path d="M12,1L3,5V11C3,16.55 6.84,21.74 12,23C17.16,21.74 21,16.55 21,11V5L12,1M10,17L6,13L7.41,11.59L10,14.17L16.59,7.58L18,9L10,17Z"/></svg>
                            Aksi
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="flex flex-col gap-3">
                            <a href="{{ route('finished-products.edit', array_merge([$finishedProduct], request()->has('branch_id') ? ['branch_id' => request('branch_id')] : [])) }}"
                               class="inline-flex items-center justify-center px-4 py-2 bg-yellow-400 hover:bg-yellow-500 text-white rounded-xl font-medium transition">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                Edit Produk
                            </a>

                            <button onclick="toggleAdjustModal(true)"
                                    class="inline-flex items-center justify-center px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-xl font-medium transition">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4M8 15l4 4 4-4"/></svg>
                                Sesuaikan Stok
                            </button>

                            <button onclick="confirmDelete()"
                                    class="inline-flex items-center justify-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl font-medium transition">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                Hapus Produk
                            </button>
                        </div>
                    </div>
                </div>

                {{-- System Info --}}
                <div class="bg-white rounded-2xl shadow-xl border border-gray-200/50 overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-600 to-gray-700 px-6 py-4">
                        <h3 class="text-lg font-bold text-white flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                            Info Sistem
                        </h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Produk ID</span>
                            <span class="font-mono text-gray-700">{{ $finishedProduct->id }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Dibuat</span>
                            <span class="text-gray-700">{{ $finishedProduct->created_at ? $finishedProduct->created_at->format('d/m/Y H:i') : '-' }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Diperbarui</span>
                            <span class="text-gray-700">{{ $finishedProduct->updated_at ? $finishedProduct->updated_at->format('d/m/Y H:i') : '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Hidden Delete Form --}}
    <form id="delete-form" action="{{ route('finished-products.destroy', $finishedProduct) }}" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
    </form>

    {{-- Stock Adjustment Modal (Tailwind simple) --}}
    <div id="adjustModal" class="fixed inset-0 z-50 hidden flex items-center justify-center px-4 py-6">
        <div class="absolute inset-0 bg-black/50" onclick="toggleAdjustModal(false)"></div>
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-2xl overflow-hidden">
            <div class="px-6 py-4 border-b">
                <h4 class="text-lg font-semibold">Sesuaikan Stok - {{ $finishedProduct->name }}</h4>
            </div>
            <form action="#" method="POST" onsubmit="return confirm('Pastikan data penyesuaian benar. Lanjutkan?')">
                @csrf
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm text-gray-600">Stok Saat Ini</label>
                        <input type="text" readonly class="mt-2 w-full rounded-lg border-gray-200 shadow-sm p-3 bg-gray-50" value="{{ number_format($displayStockQuantity, 0, ',', '.') }} {{ $finishedProduct->unit->unit_name ?? '' }}">
                        <p class="text-xs text-gray-400 mt-1">@if($branchForStock) Stok untuk {{ $branchForStock->name }} @else Total stok semua cabang @endif</p>
                    </div>

                    <div>
                        <label class="block text-sm text-gray-600">Jenis Penyesuaian</label>
                        <select name="adjustment_type" id="adjustment_type" class="mt-2 w-full rounded-lg border-gray-200 p-3">
                            <option value="add">Tambah Stok</option>
                            <option value="subtract">Kurangi Stok</option>
                            <option value="set">Set Stok Baru</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm text-gray-600">Jumlah</label>
                        <input type="number" name="adjustment_quantity" id="adjustment_quantity" step="0.01" min="0" required class="mt-2 w-full rounded-lg border-gray-200 p-3" />
                    </div>

                    <div>
                        <label class="block text-sm text-gray-600">Alasan Penyesuaian</label>
                        <textarea name="adjustment_reason" id="adjustment_reason" rows="3" class="mt-2 w-full rounded-lg border-gray-200 p-3" placeholder="Alasan penyesuaian stok"></textarea>
                    </div>
                </div>
                <div class="flex justify-end gap-3 px-6 py-4 border-t">
                    <button type="button" onclick="toggleAdjustModal(false)" class="px-4 py-2 rounded-xl bg-gray-100">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-blue-600 text-white">Simpan Penyesuaian</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function confirmDelete() {
        if (confirm('Apakah Anda yakin ingin menghapus produk ini?')) {
            document.getElementById('delete-form').submit();
        }
    }

    function toggleAdjustModal(show) {
        document.getElementById('adjustModal').classList.toggle('hidden', !show);
    }
</script>
@endsection
