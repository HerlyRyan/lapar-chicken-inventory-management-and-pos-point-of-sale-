@extends('layouts.app')

@php
use Illuminate\Support\Facades\Storage;
@endphp

@section('title', 'Detail Purchase Order')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-orange-50 via-red-50 to-orange-100">
    {{-- Header --}}
    <div class="bg-gradient-to-r from-orange-900 via-orange-800 to-red-900 shadow-xl">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('purchase-orders.index') }}"
                       class="inline-flex items-center justify-center w-10 h-10 bg-white/10 hover:bg-white/20 rounded-xl transition-all duration-200 backdrop-blur-sm border border-white/20 group">
                        <svg class="w-5 h-5 text-white group-hover:text-orange-200 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </a>

                    <div>
                        <h1 class="text-2xl lg:text-3xl font-bold text-white">Detail Purchase Order</h1>
                        <p class="text-orange-200 mt-1">{{ $purchaseOrder->order_number }}</p>
                    </div>
                </div>

                <div class="flex items-center space-x-3">
                    @if($purchaseOrder->canBeEdited())
                        <a href="{{ route('purchase-orders.edit', $purchaseOrder) }}"
                           class="inline-flex items-center px-4 py-2.5 bg-white/10 hover:bg-white/20 text-white rounded-xl font-medium transition-all duration-200 backdrop-blur-sm border border-white/20 group">
                            <svg class="w-4 h-4 mr-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edit
                        </a>
                    @endif

                    @if($purchaseOrder->status === 'ordered')
                        <a href="{{ route('purchase-orders.print', $purchaseOrder) }}" target="_blank"
                           class="inline-flex items-center px-4 py-2.5 bg-white/10 hover:bg-white/20 text-white rounded-xl font-medium transition-all duration-200 backdrop-blur-sm border border-white/20 group">
                            <svg class="w-4 h-4 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9V2h12v7"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18h12v4H6z"/>
                            </svg>
                            Print
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Content --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Left: Details --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-xl border border-gray-200/50 overflow-hidden">
                    <div class="relative bg-gradient-to-r from-orange-500 via-red-500 to-orange-600 px-8 py-8">
                        <div class="absolute inset-0 bg-black/6"></div>
                        <div class="relative grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
                            <div class="md:col-span-1 flex items-center justify-center">
                                <div class="w-28 h-28 rounded-full border-4 border-white shadow-2xl bg-white/10 flex items-center justify-center">
                                    <svg class="w-12 h-12 text-white" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M3 3h18v7H3zM6 13h12v8H6z"/>
                                    </svg>
                                </div>
                            </div>

                            <div class="md:col-span-2 text-white">
                                <h2 class="text-2xl font-bold mb-1">{{ $purchaseOrder->order_number }}</h2>
                                <p class="text-orange-100 mb-2">{{ $purchaseOrder->order_code ?? '-' }}</p>

                                <div class="flex items-center gap-3">
                                    @if($purchaseOrder->status === 'draft')
                                        <span class="inline-flex items-center px-3 py-1.5 bg-yellow-100 text-yellow-800 rounded-full text-sm font-semibold">
                                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L2 12h3v8h4v-6h6v6h4v-8h3z"/></svg>
                                            Draft
                                        </span>
                                    @elseif($purchaseOrder->status === 'ordered')
                                        <span class="inline-flex items-center px-3 py-1.5 bg-green-100 text-green-800 rounded-full text-sm font-semibold">
                                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24"><path d="M20 6L9 17l-5-5"/></svg>
                                            Ordered
                                        </span>
                                    @elseif($purchaseOrder->status === 'received')
                                        <span class="inline-flex items-center px-3 py-1.5 bg-blue-100 text-blue-800 rounded-full text-sm font-semibold">
                                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12 3.41 13.41 9 19 21 7 19.59 5.59z"/></svg>
                                            Received
                                        </span>
                                    @elseif($purchaseOrder->status === 'partially_received')
                                        <span class="inline-flex items-center px-3 py-1.5 bg-emerald-100 text-emerald-800 rounded-full text-sm font-semibold">
                                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10"/></svg>
                                            Partially Received
                                        </span>
                                    @elseif($purchaseOrder->status === 'rejected')
                                        <span class="inline-flex items-center px-3 py-1.5 bg-red-100 text-red-800 rounded-full text-sm font-semibold">
                                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24"><path d="M18.36 6.64L12 13 5.64 6.64 4.22 8.06 10.58 14.42 4.22 20.78 5.64 22.2 12 15.84 18.36 22.2 19.78 20.78 13.42 14.42 19.78 8.06z"/></svg>
                                            Rejected
                                        </span>
                                    @endif

                                    <span class="ml-4 text-orange-100 text-sm">Dibuat: {{ $purchaseOrder->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Left info --}}
                            <div class="space-y-3">
                                <div>
                                    <h4 class="text-sm text-gray-500">Supplier</h4>
                                    <p class="font-semibold text-gray-800">{{ $purchaseOrder->supplier->name }}</p>
                                </div>

                                <div>
                                    <h4 class="text-sm text-gray-500">Telepon</h4>
                                    @if($purchaseOrder->supplier->phone)
                                        <p class="text-gray-700 inline-flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967..."/>
                                            </svg>
                                            {{ $purchaseOrder->supplier->phone }}
                                        </p>
                                    @else
                                        <p class="text-gray-400">-</p>
                                    @endif
                                </div>

                                <div>
                                    <h4 class="text-sm text-gray-500">Alamat</h4>
                                    <p class="text-gray-700">{{ $purchaseOrder->supplier->address ?? '-' }}</p>
                                </div>
                            </div>

                            {{-- Right info --}}
                            <div class="space-y-3">
                                <div>
                                    <h4 class="text-sm text-gray-500">Pengiriman Diharapkan</h4>
                                    <p class="text-gray-700">
                                        @if($purchaseOrder->requested_delivery_date)
                                            {{ $purchaseOrder->requested_delivery_date->format('d/m/Y') }}
                                        @else
                                            <span class="text-gray-400">Tidak ditentukan</span>
                                        @endif
                                    </p>
                                </div>

                                <div>
                                    <h4 class="text-sm text-gray-500">Kode Order</h4>
                                    <p class="text-gray-700">{{ $purchaseOrder->order_code }}</p>
                                </div>

                                @if($purchaseOrder->notes)
                                    <div>
                                        <h4 class="text-sm text-gray-500">Catatan</h4>
                                        <div class="mt-2 p-3 bg-gray-50 rounded-md text-gray-700">
                                            {{ $purchaseOrder->notes }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Items list --}}
                <div class="mt-6 bg-white rounded-2xl shadow-xl border border-gray-200/50 overflow-hidden">
                    <div class="bg-gradient-to-r from-orange-500 via-red-500 to-orange-600 px-6 py-4">
                        <h3 class="text-lg font-bold text-white flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M3 3h18v7H3zM6 13h12v8H6z"/>
                            </svg>
                            Daftar Item ({{ $purchaseOrder->items->count() }} item)
                        </h3>
                    </div>

                    <div class="p-6 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">#</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Bahan Mentah</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Kode</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500">Kuantitas</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500">Satuan</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500">Harga Satuan</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500">Total Harga</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @foreach($purchaseOrder->items as $index => $item)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-4 text-sm text-gray-700">{{ $index + 1 }}</td>
                                        <td class="px-4 py-4">
                                            <div class="font-semibold text-gray-800">{{ $item->rawMaterial->name }}</div>
                                            @if($item->rawMaterial->category)
                                                <div class="text-sm text-gray-400">{{ $item->rawMaterial->category->name }}</div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4">
                                            <span class="inline-flex items-center px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">
                                                {{ $item->rawMaterial->code ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <span class="font-semibold">{{ number_format($item->quantity, 0, ',', '.') }}</span>
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <span class="inline-flex items-center px-2 py-1 bg-amber-100 text-amber-800 rounded-full text-xs">
                                                {{ $item->unit_name ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 text-right">
                                            <span class="font-semibold">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</span>
                                        </td>
                                        <td class="px-4 py-4 text-right">
                                            <span class="font-bold text-green-600">Rp {{ number_format($item->total_price, 0, ',', '.') }}</span>
                                        </td>
                                    </tr>
                                    @if($item->notes)
                                        <tr>
                                            <td colspan="7" class="px-8 py-2 text-sm text-gray-500">
                                                <svg class="w-4 h-4 inline mr-2 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M3 3h18v7H3z"/>
                                                </svg>
                                                {{ $item->notes }}
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <td colspan="6" class="px-4 py-4 text-right font-bold text-gray-800">TOTAL KESELURUHAN:</td>
                                    <td class="px-4 py-4 text-right font-bold text-green-600">
                                        Rp {{ number_format($purchaseOrder->total_amount, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Right: Summary & Actions --}}
            <div class="space-y-6">
                <div class="bg-white rounded-2xl shadow-xl border border-gray-200/50 overflow-hidden">
                    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-4">
                        <h3 class="text-lg font-bold text-white flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M3 3h18v7H3zM6 13h12v8H6z"/>
                            </svg>
                            Ringkasan
                        </h3>
                    </div>

                    <div class="p-6 text-center">
                        <h3 class="text-2xl font-bold text-green-600 mb-1">Rp {{ number_format($purchaseOrder->total_amount, 0, ',', '.') }}</h3>
                        <p class="text-gray-500 mb-4">Total Pesanan</p>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <div class="text-sm text-gray-500">Item</div>
                                <div class="text-lg font-semibold text-gray-800">{{ $purchaseOrder->items->count() }}</div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-500">Total Qty</div>
                                <div class="text-lg font-semibold text-gray-800">{{ $purchaseOrder->items->sum('quantity') }}</div>
                            </div>
                        </div>

                        <div class="text-sm text-gray-500 space-y-2">
                            <div class="flex justify-between">
                                <span>Dibuat oleh:</span>
                                <span class="font-medium text-gray-800">{{ $purchaseOrder->creator->name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Role:</span>
                                <span class="text-gray-700">{{ $purchaseOrder->creator->role->name ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Terakhir update:</span>
                                <span class="text-gray-700">{{ $purchaseOrder->updated_at->format('d/m/Y H:i') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                @if($purchaseOrder->canBeEdited())
                    <div class="bg-white rounded-2xl shadow-xl border border-gray-200/50 overflow-hidden">
                        <div class="bg-white/5 px-6 py-4 border-b border-gray-100">
                            <h4 class="text-sm font-semibold text-gray-700 flex items-center">
                                <i class="fa fa-home w-4 h-4 mr-2 text-orange-500"></i>
                                Aksi Cepat
                            </h4>
                        </div>

                        <div class="p-6 space-y-3">
                            @if($purchaseOrder->canBeOrdered())
                                <button type="button"
                                        onclick="confirmOrder({{ $purchaseOrder->id }}, '{{ $purchaseOrder->order_number }}')"
                                        class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-green-500 hover:bg-green-600 text-white rounded-xl font-medium transition">
                                    <i class="fa fa-paper-plane mr-2"></i>
                                    Pesan Sekarang
                                </button>
                            @endif

                            <a href="{{ route('purchase-orders.edit', $purchaseOrder) }}"
                               class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-yellow-400 hover:bg-yellow-500 text-gray-900 rounded-xl font-medium transition">
                                <i class="fa fa-edit mr-2"></i>
                                Edit Purchase Order
                            </a>

                            <button type="button"
                                    onclick="confirmDelete({{ $purchaseOrder->id }}, '{{ $purchaseOrder->order_number }}')"
                                    class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl font-medium transition">
                                <i class="fa fa-trash mr-2"></i>
                                Hapus
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css">

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>
<script>
/**
 * Confirm and mark purchase order as ordered
 */
function confirmOrder(orderId, orderNumber) {
    Swal.fire({
        title: 'Pesan Sekarang?',
        html: `
            <p>Purchase Order <strong>${orderNumber}</strong> akan dikirim ke supplier melalui WhatsApp.</p>
            <p class="text-warning mb-0">
                <i class="bi bi-exclamation-triangle"></i>
                Setelah dipesan, PO tidak dapat diedit lagi.
            </p>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10B981',
        cancelButtonColor: '#6B7280',
        confirmButtonText: '<i class="bi bi-whatsapp"></i> Ya, Pesan Sekarang',
        cancelButtonText: 'Batal',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            return fetch(`/purchase-orders/${orderId}/mark-as-ordered`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrf
                },
                body: JSON.stringify({})
            }).then(async (response) => {
                if (response.ok) return response.json();
                if (response.status === 405 || response.status === 404) {
                    return fetch(`/purchase-orders/${orderId}/mark-as-ordered`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-HTTP-Method-Override': 'PATCH',
                            'X-CSRF-TOKEN': csrf
                        },
                        body: JSON.stringify({})
                    }).then(fb => {
                        if (!fb.ok) throw new Error(`Fallback failed: ${fb.status}`);
                        return fb.json();
                    });
                }
                throw new Error(`HTTP ${response.status}`);
            }).catch(error => {
                Swal.showValidationMessage(`Request failed: ${error}`);
            });
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed) {
            if (result.value && result.value.success) {
                Swal.fire({
                    title: 'Berhasil!',
                    text: result.value.message,
                    icon: 'success',
                    confirmButtonColor: '#2563EB'
                }).then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: result.value ? result.value.message : 'Terjadi kesalahan',
                    icon: 'error',
                    confirmButtonColor: '#DC2626'
                });
            }
        }
    });
}

/**
 * Confirm and delete purchase order
 */
function confirmDelete(orderId, orderNumber) {
    Swal.fire({
        title: 'Hapus Purchase Order?',
        html: `
            <p>Purchase Order <strong>${orderNumber}</strong> akan dihapus permanen.</p>
            <p class="text-danger mb-0">
                <i class="bi bi-exclamation-triangle"></i>
                Tindakan ini tidak dapat dibatalkan!
            </p>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#DC2626',
        cancelButtonColor: '#6B7280',
        confirmButtonText: '<i class="bi bi-trash"></i> Ya, Hapus',
        cancelButtonText: 'Batal',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            return fetch(`/purchase-orders/${orderId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrf
                }
            })
            .then(response => {
                if (!response.ok) throw new Error(`HTTP ${response.status}`);
                return response.json();
            })
            .catch(error => {
                Swal.showValidationMessage(`Request failed: ${error}`);
            });
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed) {
            if (result.value && result.value.success) {
                Swal.fire({
                    title: 'Berhasil!',
                    text: result.value.message,
                    icon: 'success',
                    confirmButtonColor: '#2563EB'
                }).then(() => {
                    window.location.href = '{{ route("purchase-orders.index") }}';
                });
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: result.value ? result.value.message : 'Terjadi kesalahan',
                    icon: 'error',
                    confirmButtonColor: '#DC2626'
                });
            }
        }
    });
}
</script>
