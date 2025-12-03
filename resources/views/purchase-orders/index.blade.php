@extends('layouts.app')

@section('title', 'Purchase Orders')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-orange-50/30 to-red-50/30">
        {{-- Page Header --}}
        <x-index.header title="Purchase Orders" subtitle="Kelola pesanan pembelian bahan mentah"
            addRoute="{{ route('purchase-orders.create') }}" addText="Buat Purchase Order" />

        {{-- Main Content --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-8">
            <div x-data="sortableTable(@js($purchaseOrders))" @sort-column.window="sortBy($event.detail)"
                class="bg-white rounded-lg sm:rounded-2xl shadow-lg sm:shadow-xl border border-gray-200 overflow-hidden">
                {{-- Card Header --}}
                <x-index.card-header title="Daftar Purchase Orders" />

                {{-- Filter Section --}}
                <x-filter-bar searchPlaceholder="Cari nama, kode, atau po..." :selects="$selects" :date="true" />

                {{-- Desktop Table --}}
                <div class="hidden lg:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <x-index.table-head :columns="$columns" />
                        <tbody class="bg-white divide-y divide-gray-200">
                            <template x-for="(order, index) in sortedRows" :key="order.id">
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="index + 1"></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900" x-text="order.order_number"></div>
                                        <div class="text-sm text-gray-500" x-text="order.order_code"></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900" x-text="order.supplier?.name"></div>
                                        <template x-if="order.supplier?.phone">
                                            <div class="text-sm text-gray-500 flex items-center">
                                                <x-whatsapp-logo class="w-3 h-3 mr-1" />
                                                <span x-text="order.supplier.phone"></span>
                                            </div>
                                        </template>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900"
                                            x-text="new Date(order.order_date).toLocaleDateString('id-ID')"></div>
                                        <div class="text-sm text-gray-500"
                                            x-text="new Date(order.created_at).toLocaleDateString('id-ID')"></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900"
                                            x-text="new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(order.total_amount)">
                                        </div>
                                        <div class="text-sm text-gray-500" x-text="order.items.length + ' item'"></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <template x-if="order.status === 'draft'">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                                Draft
                                            </span>
                                        </template>
                                        <template x-if="order.status === 'ordered'">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 1.414L10.586 9H7a1 1 0 100 2h3.586l-1.293 1.293a1 1 0 101.414 1.414l3-3a1 1 0 000-1.414z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                                Ordered
                                            </span>
                                        </template>
                                        <template x-if="order.status === 'received'">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                                Received
                                            </span>
                                        </template>
                                        <template x-if="order.status === 'partially_received'">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                                Partially Received
                                            </span>
                                        </template>
                                        <template x-if="order.status === 'rejected'">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                                Rejected
                                            </span>
                                        </template>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900" x-text="order.creator?.name"></div>
                                        <div class="text-sm text-gray-500"
                                            x-text="order.creator?.roles?.first().name || 'N/A'"></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex flex-col items-center space-y-2">
                                            {{-- Standard Actions --}}
                                            <div x-data="{
                                                viewUrl: '/purchase-orders/' + order.id,
                                                editUrl: '/purchase-orders/' + order.id + '/edit',
                                                deleteUrl: '/purchase-orders/' + order.id,
                                                toggleUrl: '/purchase-orders/' + order.id + '/toggle-status',
                                                itemName: 'order ' + order.name,
                                            }">
                                                <x-index.action-buttons :view="true" :edit="true"
                                                    :delete="true" />
                                            </div>

                                            {{-- Special Actions --}}
                                            {{-- <div class="flex space-x-1">
                                                <template x-if="order.can_be_edited">
                                                    <button @click="confirmOrder(order.id, order.order_number)"
                                                        class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-green-700 bg-green-100 hover:bg-green-200 transition-colors"
                                                        title="Pesan Sekarang">
                                                        <x-whatsapp-logo class="w-3 h-3 mr-1" />
                                                        Pesan
                                                    </button>
                                                </template>
                                                <template x-if="order.can_be_edited">
                                                    <button @click="confirmDelete(order.id, order.order_number)"
                                                        class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-red-700 bg-red-100 hover:bg-red-200 transition-colors"
                                                        title="Hapus">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </template>
                                                <template x-if="order.status === 'ordered'">
                                                    <a :href="'/purchase-orders/' + order.id + '/print'" target="_blank"
                                                        class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-gray-700 bg-gray-100 hover:bg-gray-200 transition-colors"
                                                        title="Print">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                                        </svg>
                                                        Print
                                                    </a>
                                                </template>
                                            </div> --}}
                                        </div>
                                    </td>
                                </tr>
                            </template>
                            <template x-if="sortedRows.length === 0">
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                                    d="M16 11V7a4 4 0 00-8 0v4M8 11v6h8v-6M8 11H6a2 2 0 00-2 2v6a2 2 0 002 2h12a2 2 0 002-2v-6a2 2 0 00-2-2h-2" />
                                            </svg>
                                            <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada Purchase Order
                                            </h3>
                                            <p class="text-gray-500 mb-4">Mulai buat purchase order pertama Anda</p>
                                            <a href="{{ route('purchase-orders.create') }}"
                                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                </svg>
                                                Buat Purchase Order
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                {{-- Mobile Cards --}}
                <div class="lg:hidden divide-y divide-gray-200">
                    <template x-for="order in sortedRows" :key="order.id">
                        <div class="p-4 hover:bg-gray-50 transition-colors duration-150">
                            {{-- Order Header --}}
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-sm font-medium text-gray-900" x-text="order.order_number"></h3>
                                    <p class="text-sm text-gray-500" x-text="order.order_code"></p>
                                </div>
                                <div class="ml-4">
                                    <template x-if="order.status === 'draft'">
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Draft
                                        </span>
                                    </template>
                                    <template x-if="order.status === 'ordered'">
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Ordered
                                        </span>
                                    </template>
                                    <template x-if="order.status === 'received'">
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            Received
                                        </span>
                                    </template>
                                    <template x-if="order.status === 'partially_received'">
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                            Partially Received
                                        </span>
                                    </template>
                                    <template x-if="order.status === 'rejected'">
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Rejected
                                        </span>
                                    </template>
                                </div>
                            </div>

                            {{-- Order Details --}}
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500">Supplier:</span>
                                    <div class="text-sm text-gray-900 font-medium" x-text="order.supplier?.name"></div>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500">Tanggal:</span>
                                    <div class="text-sm text-gray-900"
                                        x-text="new Date(order.order_date).toLocaleDateString('id-ID')"></div>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500">Total:</span>
                                    <div class="text-sm text-gray-900 font-medium"
                                        x-text="new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(order.total_amount)">
                                    </div>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500">Items:</span>
                                    <div class="text-sm text-gray-900" x-text="order.items.length + ' item'"></div>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500">Dibuat:</span>
                                    <div class="text-sm text-gray-900" x-text="order.creator?.name"></div>
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="mt-4 pt-3 border-t border-gray-200">
                                <div class="flex flex-wrap gap-2">
                                    {{-- Standard Actions --}}
                                    <div x-data="{
                                        viewUrl: '/purchase-orders/' + order.id,
                                        editUrl: '/purchase-orders/' + order.id + '/edit',
                                        deleteUrl: '/purchase-orders/' + order.id,
                                        toggleUrl: '/purchase-orders/' + order.id + '/toggle-status',
                                        itemName: 'order ' + order.name,
                                    }">
                                        <x-index.action-buttons :view="true" :edit="true" :delete="true" />
                                    </div>
                                    <template x-if="order.can_be_edited">
                                        <button @click="confirmOrder(order.id, order.order_number)"
                                            class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-green-700 bg-green-100 hover:bg-green-200 transition-colors">
                                            <x-whatsapp-logo class="w-3 h-3 mr-1" />
                                            Pesan
                                        </button>
                                    </template>
                                    <template x-if="order.can_be_edited">
                                        <button @click="confirmDelete(order.id, order.order_number)"
                                            class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-red-700 bg-red-100 hover:bg-red-200 transition-colors">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Hapus
                                        </button>
                                    </template>
                                    <template x-if="order.status === 'ordered'">
                                        <a :href="'/purchase-orders/' + order.id + '/print'" target="_blank"
                                            class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-gray-700 bg-gray-100 hover:bg-gray-200 transition-colors">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                            </svg>
                                            Print
                                        </a>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Pagination --}}
                <template x-if="sortedRows.length !== 0">
                    <div class="pagination-wrapper">
                        {{ $pagination->links('vendor.pagination.tailwind') }}
                    </div>
                </template>
            </div>
        </div>
    </div>

    {{-- JavaScript Functions --}}
    <script>
        function confirmOrder(orderId, orderNumber) {
            if (confirm(`Pesan Purchase Order ${orderNumber} sekarang? PO tidak dapat diedit setelah dipesan.`)) {
                fetch(`/purchase-orders/${orderId}/mark-as-ordered`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(data.message);
                            window.location.reload();
                        } else {
                            alert(data.message || 'Terjadi kesalahan');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan saat memproses permintaan');
                    });
            }
        }

        function confirmDelete(orderId, orderNumber) {
            if (confirm(`Hapus Purchase Order ${orderNumber}? Tindakan ini tidak dapat dibatalkan.`)) {
                fetch(`/purchase-orders/${orderId}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(data.message);
                            window.location.reload();
                        } else {
                            alert(data.message || 'Terjadi kesalahan');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan saat memproses permintaan');
                    });
            }
        }
    </script>
@endsection
