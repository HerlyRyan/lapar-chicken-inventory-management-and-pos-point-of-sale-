@extends('layouts.app')

@section('title', 'Penerimaan Barang')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-orange-50/30 to-red-50/30">
        {{-- Page Header --}}
        <x-index.header title="Penerimaan Barang" subtitle="Kelola penerimaan barang dari supplier"
            addRoute="{{ route('purchase-receipts.create') }}" addText="Buat Penerimaan Baru" />

        {{-- Main Content --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-8 space-y-6">

            {{-- Filter Section --}}
            <x-filter-bar searchPlaceholder="Cari nama, kode, atau alamat supplier..." :selects="$selects" date="true"
                export_csv="true" />

            {{-- Summary Card --}}
            <div class="bg-white rounded-lg sm:rounded-2xl shadow-lg border border-gray-200 p-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Periode</h3>
                        <div class="flex items-center gap-2">
                            <span class="text-lg font-semibold text-gray-900">
                                @php($start = request('start_date'))
                                @php($end = request('end_date'))
                                @if ($start || $end)
                                    {{ $start ? \Carbon\Carbon::parse($start)->format('d/m/Y') : 'Awal' }}
                                    —
                                    {{ $end ? \Carbon\Carbon::parse($end)->format('d/m/Y') : 'Sekarang' }}
                                @else
                                    Semua tanggal
                                @endif
                            </span>
                            <span
                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $filteredReceiptsCount ?? 0 }} penerimaan
                            </span>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 text-right">
                        <div class="lg:col-span-1">
                            <div class="text-xs text-gray-500">Subtotal</div>
                            <div class="font-semibold text-gray-900">Rp
                                {{ number_format($totalItemsAmount ?? 0, 0, ',', '.') }}</div>
                        </div>
                        <div class="lg:col-span-1">
                            <div class="text-xs text-gray-500">Biaya Tambahan</div>
                            <div class="font-semibold text-gray-900">Rp
                                {{ number_format($totalAdditionalCosts ?? 0, 0, ',', '.') }}</div>
                        </div>
                        <div class="lg:col-span-1">
                            <div class="text-xs text-gray-500">Diskon</div>
                            <div class="font-semibold text-gray-900">Rp
                                {{ number_format($totalDiscount ?? 0, 0, ',', '.') }}</div>
                        </div>
                        <div class="lg:col-span-1">
                            <div class="text-xs text-gray-500">Pajak</div>
                            <div class="font-semibold text-gray-900">Rp {{ number_format($totalTax ?? 0, 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="lg:col-span-1">
                            <div class="text-xs text-gray-500">Total</div>
                            <div class="text-lg font-bold text-orange-600">Rp
                                {{ number_format($totalBelanja ?? 0, 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Pending Orders --}}
            @if (isset($pendingOrders) && $pendingOrders->count() > 0)
                <div x-data="{ open: false }"
                    class="bg-white rounded-lg sm:rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
                    <button @click="open = !open"
                        class="w-full px-6 py-4 bg-gray-50 flex items-center justify-between hover:bg-gray-100 transition-colors duration-200">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            <span class="font-medium text-gray-900">Total menunggu konfirmasi</span>
                            <span
                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $pendingOrders->count() }}
                            </span>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 transform transition-transform duration-200"
                            :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </button>
                    <div x-show="open" x-transition class="border-t border-gray-200">
                        <div class="overflow-x-auto max-h-96 overflow-y-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            No. PO</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Supplier</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Tanggal</th>
                                        <th
                                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($pendingOrders as $po)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ $po->order_number }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $po->supplier->name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ optional($po->order_date)->format('d/m/Y') }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <div class="flex items-center justify-end gap-2">
                                                    <a href="{{ route('purchase-receipts.create', ['purchase_order_id' => $po->id]) }}"
                                                        class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                            </path>
                                                        </svg>
                                                        Buat Penerimaan
                                                    </a>
                                                    {{-- <button type="button"
                                                        class="inline-flex items-center px-3 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors duration-200"
                                                        data-po-id="{{ $po->id }}"
                                                        data-po-number="{{ $po->order_number }}"
                                                        data-supplier="{{ $po->supplier->name }}"
                                                        onclick="openQuickReceive(this)">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4">
                                                            </path>
                                                        </svg>
                                                        Terima
                                                    </button> --}}
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Purchase Receipts Table --}}
            <div x-data="sortableTable(@js($purchaseReceipts->items()))" @sort-column.window="sortBy($event.detail)"
                class="bg-white rounded-lg sm:rounded-2xl shadow-lg border border-gray-200 overflow-hidden">

                {{-- Card Header --}}
                <x-index.card-header title="Penerimaan Barang" />

                @include('purchase-receipts.partials.alerts')

                {{-- Desktop Table --}}
                <div class="hidden lg:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <x-index.table-head :columns="$columns" />
                        <tbody class="bg-white divide-y divide-gray-200">
                            <template x-for="(receipt, index) in sortedRows" :key="receipt.id">
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="index + 1"></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm font-medium text-orange-600"
                                            x-text="receipt.receipt_number"></span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                                        x-text="new Date(receipt.receipt_date).toLocaleDateString('id-ID')"></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a :href="'/purchase-orders/' + receipt.purchase_order_id"
                                            class="text-sm text-blue-600 hover:text-blue-800"
                                            x-text="receipt.purchase_order?.order_number"></a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                                        x-text="receipt.purchase_order?.supplier?.name"></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <template x-if="receipt.status === 'accepted'">
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Diterima
                                            </span>
                                        </template>
                                        <template x-if="receipt.status === 'rejected'">
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                Ditolak
                                            </span>
                                        </template>
                                        <template x-if="receipt.status === 'partial'">
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                Sebagian
                                            </span>
                                        </template>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                                        x-text="receipt.receiver?.name || '-'"></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <span class="text-sm font-semibold text-gray-900"
                                            x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(receipt.total_amount || 0)"></span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div x-data="{
                                            viewUrl: '/purchase-receipts/' + receipt.id,
                                            editUrl: '/purchase-receipts/' + receipt.id + '/edit',
                                            deleteUrl: '/purchase-receipts/' + receipt.id,
                                            itemName: 'penerimaan ' + receipt.receipt_number,
                                            canEditDelete: receipt.status !== 'accepted'
                                        }">
                                            <x-index.action-buttons :view="true" :edit="true" :delete="true" />
                                        </div>
                                    </td>
                                </tr>
                            </template>
                            <template x-if="sortedRows.length === 0">
                                <x-index.none-data colspan="8" message="Belum ada data penerimaan barang" />
                            </template>
                        </tbody>
                    </table>
                </div>

                {{-- Mobile Cards --}}
                <div class="lg:hidden divide-y divide-gray-200">
                    <template x-for="(receipt, index) in sortedRows" :key="receipt.id">
                        <div class="p-4 hover:bg-gray-50 transition-colors duration-150">
                            {{-- Receipt Header --}}
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-sm font-medium text-orange-600 truncate"
                                            x-text="receipt.receipt_number"></h3>
                                        <span class="text-xs text-gray-500 ml-2"
                                            x-text="new Date(receipt.receipt_date).toLocaleDateString('id-ID')"></span>
                                    </div>
                                    <p class="text-sm text-gray-500 mt-1" x-text="receipt.purchase_order?.supplier?.name">
                                    </p>
                                </div>
                            </div>

                            {{-- Receipt Details --}}
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500">Pesanan:</span>
                                    <a :href="'/purchase-orders/' + receipt.purchase_order_id"
                                        class="text-sm text-blue-600 hover:text-blue-800"
                                        x-text="receipt.purchase_order?.order_number"></a>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500">Status:</span>
                                    <template x-if="receipt.status === 'accepted'">
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Diterima
                                        </span>
                                    </template>
                                    <template x-if="receipt.status === 'rejected'">
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Ditolak
                                        </span>
                                    </template>
                                    <template x-if="receipt.status === 'partial'">
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Sebagian
                                        </span>
                                    </template>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500">Penerima:</span>
                                    <span class="text-sm text-gray-900" x-text="receipt.receiver?.name || '-'"></span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500">Total:</span>
                                    <span class="text-sm font-semibold text-gray-900"
                                        x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(receipt.total_payment || 0)"></span>
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="mt-4 pt-3 border-t border-gray-200">
                                <div x-data="{
                                    viewUrl: '/purchase-receipts/' + receipt.id,
                                    editUrl: '/purchase-receipts/' + receipt.id + '/edit',
                                    deleteUrl: '/purchase-receipts/' + receipt.id,
                                    itemName: 'penerimaan ' + receipt.receipt_number,
                                    canEditDelete: receipt.status !== 'accepted'
                                }">
                                    <x-index.action-buttons :view="true" :edit="true" :delete="true" />
                                </div>
                            </div>
                        </div>
                </div>
                </template>
            </div>

            {{-- Pagination --}}
            <template x-if="sortedRows.length !== 0">
                <div class="pagination-wrapper">
                    {{ $purchaseReceipts->appends(request()->query())->links('vendor.pagination.tailwind') }}
                </div>
            </template>
        </div>
    </div>
    </div>

    {{-- Quick Receive Modal --}}
    <div x-data="{ open: false }" x-show="open" @keydown.escape.window="open = false"
        class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="open" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-6xl sm:w-full">
                <form id="quickReceiveForm" method="POST" action="{{ route('purchase-receipts.store') }}"
                    enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="purchase_order_id" id="qr-purchase-order-id">

                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-lg leading-6 font-medium text-gray-900">
                                    Terima Pesanan
                                    <span class="ml-2 font-semibold" id="qr-po-number"></span>
                                </h3>
                                <p class="text-sm text-gray-500 mt-1" id="qr-supplier-name"></p>
                            </div>
                            <button @click="open = false" type="button"
                                class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <div class="max-h-96 overflow-y-auto space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Penerimaan <span
                                            class="text-red-500">*</span></label>
                                    <input type="date" name="receipt_date" id="qr-receipt-date"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                        required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Status Penerimaan</label>
                                    <div class="text-sm text-gray-500">Otomatis: Ditentukan dari status item</div>
                                    <input type="hidden" name="status" id="qr-status" value="">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Foto Bukti <span
                                            class="text-red-500">*</span></label>
                                    <input type="file" name="receipt_photo"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                        accept="image/jpeg,image/png,image/jpg" required>
                                    <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG. Maks 2MB</p>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                                <textarea name="notes"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                    rows="2" placeholder="Tambahkan catatan penerimaan..."></textarea>
                            </div>

                            <div class="bg-gray-50 rounded-lg p-4">
                                <h4 class="font-medium text-gray-900 mb-3">Detail Item Penerimaan</h4>
                                <div id="qr-items-container">
                                    <p class="text-gray-500 text-sm">Pilih PO untuk memuat item...</p>
                                </div>
                            </div>

                            @include('purchase-receipts.partials.additional-costs', ['prefix' => 'qr'])
                            @include('purchase-receipts.partials.discount-tax')

                            <div class="bg-gray-50 rounded-lg p-4">
                                <h4 class="font-medium text-gray-900 mb-3">Ringkasan Pembayaran</h4>
                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div class="flex justify-between">
                                        <span>Subtotal:</span>
                                        <span id="qr-subtotal-amount">Rp 0</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Biaya Tambahan:</span>
                                        <span id="qr-additional-amount">Rp 0</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Diskon:</span>
                                        <span id="qr-discount-amount">Rp 0</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Pajak:</span>
                                        <span id="qr-tax-amount">Rp 0</span>
                                    </div>
                                    <div class="col-span-2 pt-2 border-t border-gray-200">
                                        <div class="flex justify-between font-semibold">
                                            <span>Grand Total:</span>
                                            <span id="qr-grand-total-amount">Rp 0</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-orange-600 text-base font-medium text-white hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 sm:ml-3 sm:w-auto sm:text-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            Simpan Penerimaan
                        </button>
                        <button @click="open = false" type="button"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Tutup
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
