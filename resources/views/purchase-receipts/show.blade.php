@extends('layouts.app')

@php
use Illuminate\Support\Facades\Storage;
@endphp

@section('title', 'Detail Penerimaan Barang')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-orange-50 via-red-50 to-orange-100">
    {{-- Header --}}
    <div class="bg-gradient-to-r from-orange-900 via-orange-800 to-red-900 shadow-xl">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('purchase-receipts.index') }}"
                       class="inline-flex items-center justify-center w-10 h-10 bg-white/10 hover:bg-white/20 rounded-xl transition-all duration-200 backdrop-blur-sm border border-white/20 group">
                        <svg class="w-5 h-5 text-white group-hover:text-orange-200 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-2xl lg:text-3xl font-bold text-white">
                            <i class="bi bi-truck me-2"></i>Detail Penerimaan Barang
                        </h1>
                        <p class="text-orange-200 mt-1">{{ $purchaseReceipt->receipt_number }}</p>
                    </div>
                </div>

                <div class="flex items-center space-x-3">
                    <a href="{{ route('purchase-receipts.edit', $purchaseReceipt) }}"
                       class="inline-flex items-center px-4 py-2.5 bg-white/10 hover:bg-white/20 text-white rounded-xl font-medium transition-all duration-200 backdrop-blur-sm border border-white/20 group">
                        <svg class="w-4 h-4 mr-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit
                    </a>
                    <button type="button" onclick="confirmDelete({{ $purchaseReceipt->id }}, '{{ $purchaseReceipt->receipt_number }}')"
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
            {{-- Left: Main Card --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-xl border border-gray-200/50 overflow-hidden">
                    <div class="relative bg-gradient-to-r from-orange-500 via-red-500 to-orange-600 px-8 py-12">
                        <div class="absolute inset-0 bg-black/10"></div>
                        <div class="relative text-center">
                            {{-- Receipt Photo --}}
                            <div class="relative inline-block mb-6">
                                @if($purchaseReceipt->receipt_photo)
                                    <img src="{{ Storage::url($purchaseReceipt->receipt_photo) }}" alt="{{ $purchaseReceipt->receipt_number }}"
                                         class="w-32 h-32 rounded-full border-4 border-white shadow-2xl object-cover">
                                @else
                                    <div class="w-32 h-32 rounded-full border-4 border-white shadow-2xl bg-gradient-to-br from-orange-400 to-red-500 flex items-center justify-center">
                                        <span class="text-4xl font-bold text-white">{{ strtoupper(substr($purchaseReceipt->receipt_number ?? 'PR', 0, 1)) }}</span>
                                    </div>
                                @endif
                                <div class="absolute -bottom-2 -right-2 w-8 h-8 bg-green-500 rounded-full border-4 border-white flex items-center justify-center">
                                    @if($purchaseReceipt->status === 'received' || $purchaseReceipt->status === 'completed')
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                                    @else
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
                                    @endif
                                </div>
                            </div>

                            <h2 class="text-3xl font-bold text-white mb-2">{{ $purchaseReceipt->receipt_number }}</h2>
                            <p class="text-orange-100 text-lg">{{ optional($purchaseReceipt->purchaseOrder->supplier)->name ?? '-' }}</p>

                            <div class="mt-4">
                                @include('purchase-receipts.partials.status-badge', ['status' => $purchaseReceipt->status])
                            </div>
                        </div>
                    </div>

                    <div class="p-8">
                        {{-- Info Grid --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div class="p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border border-blue-100">
                                <h4 class="font-semibold text-gray-800 mb-2">Informasi Penerimaan</h4>
                                <div class="text-sm text-gray-600">
                                    <div class="flex justify-between"><span class="text-gray-500">Tanggal</span><span>{{ $purchaseReceipt->receipt_date->format('d/m/Y') }}</span></div>
                                    <div class="flex justify-between"><span class="text-gray-500">Diterima Oleh</span><span>{{ optional($purchaseReceipt->receiver)->name ?? '-' }}</span></div>
                                    <div class="flex justify-between"><span class="text-gray-500">Nomor Pesanan</span>
                                        <span>
                                            @if($purchaseReceipt->purchaseOrder)
                                                <a href="{{ route('purchase-orders.show', $purchaseReceipt->purchaseOrder) }}" class="text-indigo-600 hover:underline">
                                                    {{ $purchaseReceipt->purchaseOrder->order_number }}
                                                </a>
                                            @else
                                                -
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="p-4 bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl border border-purple-100">
                                <h4 class="font-semibold text-gray-800 mb-2">Metadata</h4>
                                <div class="text-sm text-gray-600">
                                    <div class="flex justify-between"><span class="text-gray-500">Dibuat</span><span>{{ $purchaseReceipt->created_at->format('d/m/Y H:i') }}</span></div>
                                    <div class="flex justify-between"><span class="text-gray-500">Terakhir Diubah</span><span>{{ $purchaseReceipt->updated_at->format('d/m/Y H:i') }}</span></div>
                                    <div class="flex justify-between"><span class="text-gray-500">Status Sistem</span><span class="font-medium text-gray-700">{{ ucfirst($purchaseReceipt->status) }}</span></div>
                                </div>
                            </div>
                        </div>

                        {{-- Notes --}}
                        @if($purchaseReceipt->notes)
                            <div class="mb-6">
                                <h3 class="text-lg font-semibold text-gray-800 mb-2">Catatan</h3>
                                <p class="text-sm text-gray-600">{{ $purchaseReceipt->notes }}</p>
                            </div>
                        @endif

                        {{-- Items Table --}}
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-3">Detail Item Penerimaan</h3>
                            <div class="overflow-hidden rounded-xl border border-gray-100">
                                <div class="w-full overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-100 text-sm">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-4 py-3 text-left text-gray-600">Bahan</th>
                                                <th class="px-4 py-3 text-right text-gray-600">Dipesan</th>
                                                <th class="px-4 py-3 text-right text-gray-600">Diterima</th>
                                                <th class="px-4 py-3 text-right text-gray-600">Ditolak</th>
                                                <th class="px-4 py-3 text-center text-gray-600">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-100">
                                            @foreach($purchaseReceipt->items as $item)
                                                <tr>
                                                    <td class="px-4 py-3">
                                                        <div class="font-semibold text-gray-800">{{ $item->rawMaterial->name }}</div>
                                                        <div class="text-xs text-gray-400">{{ $item->rawMaterial->code }}</div>
                                                    </td>
                                                    <td class="px-4 py-3 text-right text-gray-700">
                                                        {{ number_format($item->ordered_quantity, 0, ',', '.') }} {{ optional($item->rawMaterial->unit)->name }}
                                                    </td>
                                                    <td class="px-4 py-3 text-right text-green-600 font-semibold">
                                                        {{ number_format($item->received_quantity, 0, ',', '.') }} {{ optional($item->rawMaterial->unit)->name }}
                                                    </td>
                                                    <td class="px-4 py-3 text-right">
                                                        @if($item->rejected_quantity > 0)
                                                            <span class="text-red-600 font-semibold">{{ number_format($item->rejected_quantity, 0, ',', '.') }} {{ optional($item->rawMaterial->unit)->name }}</span>
                                                        @else
                                                            <span class="text-gray-400">-</span>
                                                        @endif
                                                    </td>
                                                    <td class="px-4 py-3 text-center">
                                                        @include('purchase-receipts.partials.status-badge', ['status' => $item->item_status])
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            {{-- Right: Sidebar --}}
            <div class="space-y-6">
                {{-- Photo Card --}}
                <div class="bg-white rounded-2xl shadow-xl border border-gray-200/50 overflow-hidden">
                    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-4">
                        <h3 class="text-lg font-bold text-white flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 24 24"><path d="M12,1L3,5V11C3,16.55 6.84,21.74 12,23C17.16,21.74 21,16.55 21,11V5L12,1M7 10l5 5 5-5V6l-5 5-5-5v4z"/></svg>
                            Foto Bukti
                        </h3>
                    </div>
                    <div class="p-6 text-center">
                        @if($purchaseReceipt->receipt_photo)
                            <a href="{{ Storage::url($purchaseReceipt->receipt_photo) }}" target="_blank">
                                <img src="{{ Storage::url($purchaseReceipt->receipt_photo) }}" alt="Foto Penerimaan" class="w-full rounded-lg object-cover" style="max-height:240px;">
                            </a>
                            <div class="mt-3">
                                <a href="{{ Storage::url($purchaseReceipt->receipt_photo) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-white/10 hover:bg-white/20 text-gray-700 rounded-xl border border-gray-200">
                                    Lihat Penuh
                                </a>
                            </div>
                        @else
                            <div class="text-gray-400 py-8">
                                <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="currentColor" viewBox="0 0 24 24"><path d="M21 19V5a2 2 0 0 0-2-2H5C3.9 3 3 3.9 3 5v14a2 2 0 0 0 2 2h14c1.1 0 2-.9 2-2zM8 11l2.5 3L13 11l4 5H7l1-5z"/></svg>
                                <div>Tidak ada foto</div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Payment Summary --}}
                <div class="bg-white rounded-2xl shadow-xl border border-gray-200/50 overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-600 to-gray-700 px-6 py-4">
                        <h3 class="text-lg font-bold text-white flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 17h-2v-2h2v2zm1.07-7.75L11 14V7h2v5.25l1.07-1.0z"/></svg>
                            Ringkasan Pembayaran
                        </h3>
                    </div>
                    <div class="p-6 space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Subtotal Barang</span>
                            <span class="font-medium text-gray-700">Rp {{ number_format($purchaseReceipt->subtotal_items ?? $purchaseReceipt->computeItemsTotal(), 0, ',', '.') }}</span>
                        </div>

                        @if( (float)($purchaseReceipt->additional_cost_total ?? $purchaseReceipt->computeAdditionalCostsTotal()) > 0 )
                            <div class="flex justify-between">
                                <span class="text-gray-500">Total Biaya Tambahan</span>
                                <span class="font-medium text-gray-700">Rp {{ number_format($purchaseReceipt->additional_cost_total ?? $purchaseReceipt->computeAdditionalCostsTotal(), 0, ',', '.') }}</span>
                            </div>
                        @endif

                        @if( (float)($purchaseReceipt->discount_amount ?? 0) > 0 )
                            <div class="flex justify-between">
                                <span class="text-gray-500">Diskon</span>
                                <span class="text-green-600">- Rp {{ number_format($purchaseReceipt->discount_amount, 0, ',', '.') }}</span>
                            </div>
                        @endif

                        @if( (float)($purchaseReceipt->tax_amount ?? 0) > 0 )
                            <div class="flex justify-between">
                                <span class="text-gray-500">Pajak</span>
                                <span class="font-medium text-gray-700">Rp {{ number_format($purchaseReceipt->tax_amount, 0, ',', '.') }}</span>
                            </div>
                        @endif

                        <div><hr class="my-2"></div>

                        <div class="flex justify-between items-center">
                            <span class="text-gray-800 font-bold">Total Bayar</span>
                            <span class="text-primary text-lg font-bold">Rp {{ number_format($purchaseReceipt->computeTotalPayment(), 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Additional Costs (if any) --}}
                @if($purchaseReceipt->additionalCosts && $purchaseReceipt->additionalCosts->count() > 0)
                    <div class="bg-white rounded-2xl shadow-xl border border-gray-200/50 overflow-hidden">
                        <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-4">
                            <h3 class="text-lg font-bold text-white flex items-center">
                                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L3 7v6c0 5 3.84 10.19 9 12 5.16-1.81 9-7 9-12V7l-9-5z"/></svg>
                                Biaya Tambahan
                            </h3>
                        </div>
                        <div class="p-6 space-y-3 text-sm">
                            @foreach($purchaseReceipt->additionalCosts as $cost)
                                <div class="flex justify-between">
                                    <span class="text-gray-700">{{ $cost->name }}</span>
                                    <span class="font-medium text-gray-700">Rp {{ number_format($cost->amount, 0, ',', '.') }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- System Info --}}
                <div class="bg-white rounded-2xl shadow-xl border border-gray-200/50 overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-600 to-gray-700 px-6 py-4">
                        <h3 class="text-lg font-bold text-white flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                            Info Sistem
                        </h3>
                    </div>
                    <div class="p-6 space-y-3 text-sm">
                        <div class="flex justify-between text-sm"><span class="text-gray-500">ID</span><span class="font-mono text-gray-700">{{ $purchaseReceipt->id }}</span></div>
                        <div class="flex justify-between text-sm"><span class="text-gray-500">Dibuat</span><span class="text-gray-700">{{ $purchaseReceipt->created_at->format('d/m/Y H:i') }}</span></div>
                        <div class="flex justify-between text-sm"><span class="text-gray-500">Diperbarui</span><span class="text-gray-700">{{ $purchaseReceipt->updated_at->format('d/m/Y H:i') }}</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Delete Form --}}
<form id="delete-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script src="{{ asset('js/purchase-receipts.js') }}"></script>
@endpush
