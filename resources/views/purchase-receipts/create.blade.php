@extends('layouts.app')
@php use Illuminate\Support\Facades\Storage; @endphp

@section('title', 'Buat Penerimaan Barang')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-orange-50 via-white to-red-50 py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Header --}}
            <x-form.header title="Penerimaan Barang" backRoute="{{ route('purchase-receipts.index') }}" />

            {{-- Card --}}
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden mt-4">
                <x-form.card-header title="Buat Penerimaan Barang" type="add" />

                <div class="p-6 sm:p-8">
                    @if (session('success'))
                        <div class="mb-4 p-4 rounded-lg bg-green-50 border border-green-100 text-green-800">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-4 p-4 rounded-lg bg-red-50 border border-red-100 text-red-800">
                            <h6 class="font-semibold mb-1">Terjadi kesalahan:</h6>
                            <ul class="list-disc pl-5">
                                @foreach ($errors->all() as $error)
                                    <li class="text-sm">{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('purchase-receipts.store') }}" enctype="multipart/form-data"
                        id="purchaseReceiptForm">
                        @csrf

                        {{-- Basic Info --}}
                        <div class="mb-6">
                            <div class="flex items-center mb-4">
                                <div
                                    class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center mr-3">
                                    <i class="bi bi-truck text-white text-sm"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900">Informasi Penerimaan</h3>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                                <div class="lg:col-span-2">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Pesanan Pembelian <span
                                            class="text-red-500">*</span></label>
                                    <select name="purchase_order_id" id="purchase_order_id"
                                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200"
                                        required>
                                        <option value="">Pilih Pesanan</option>
                                        @foreach ($pendingOrders as $order)
                                            <option value="{{ $order->id }}"
                                                {{ old('purchase_order_id', request()->query('purchase_order_id')) == $order->id ? 'selected' : '' }}>
                                                {{ $order->order_number }} - {{ $order->supplier->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Penerimaan <span
                                            class="text-red-500">*</span></label>
                                    <input type="date" name="receipt_date"
                                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200"
                                        value="{{ old('receipt_date', date('Y-m-d')) }}" required>
                                </div>

                                <input type="hidden" name="status" id="status" value="">
                            </div>
                        </div>

                        {{-- Photo & Notes --}}
                        <div class="mb-6">
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Foto Bukti Penerimaan
                                        <span class="text-red-500">*</span></label>
                                    <div class="flex items-center gap-3">
                                        <input type="file" name="receipt_photo" id="receipt_photo"
                                            class="w-full px-4 py-3 border rounded-xl bg-white"
                                            accept="image/jpeg,image/png,image/jpg" required>
                                    </div>
                                    <p class="mt-2 text-sm text-gray-600">Format: JPG, PNG. Maksimal 2MB</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Catatan</label>
                                    <textarea name="notes" id="notes" rows="3"
                                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200"
                                        placeholder="Tambahkan catatan penerimaan...">{{ old('notes') }}</textarea>
                                </div>
                            </div>
                        </div>

                        {{-- Items Section --}}
                        <div class="mb-6 bg-white rounded-xl border border-gray-200 overflow-hidden">
                            <div class="bg-gray-50 p-4 border-b">
                                <h6 class="font-semibold text-gray-800">Detail Item Penerimaan</h6>
                            </div>
                            <div class="p-4">
                                <div id="items-container">
                                    {{-- items loaded by purchase-receipts.js --}}
                                    <div class="text-sm text-gray-500">Pilih pesanan pembelian untuk memuat item...</div>
                                </div>
                            </div>
                        </div>

                        {{-- Additional Costs + Summary --}}
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
                            <div class="lg:col-span-2">
                                @include('purchase-receipts.partials.additional-costs', ['prefix' => 'pr'])
                                <div class="mt-3">
                                    @include('purchase-receipts.partials.discount-tax')
                                </div>
                            </div>

                            <div class="mt-3">
                                <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                                    <div class="bg-gray-50 p-4 border-b flex items-center justify-between">
                                        <h6 class="font-semibold text-gray-800">Ringkasan Pembayaran</h6>
                                    </div>
                                    <div class="p-4">
                                        <div class="space-y-2 text-sm text-gray-700">
                                            <div class="flex justify-between">
                                                <div class="text-muted">Subtotal</div>
                                                <div id="pr-subtotal-amount">Rp 0</div>
                                            </div>
                                            <div class="flex justify-between">
                                                <div class="text-muted">Biaya Tambahan</div>
                                                <div id="pr-additional-amount">Rp 0</div>
                                            </div>
                                            <div class="flex justify-between">
                                                <div class="text-muted">Diskon</div>
                                                <div id="pr-discount-amount">Rp 0</div>
                                            </div>
                                            <div class="flex justify-between">
                                                <div class="text-muted">Pajak</div>
                                                <div id="pr-tax-amount">Rp 0</div>
                                            </div>
                                            <div class="flex justify-between mt-2 pt-2 border-t">
                                                <div class="font-semibold">Grand Total</div>
                                                <div class="font-semibold" id="pr-grand-total-amount">Rp 0</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="border-t border-gray-200 pt-6">
                            <div class="flex flex-col sm:flex-row gap-3 sm:justify-end">
                                <a href="{{ route('purchase-receipts.index') }}"
                                    class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 rounded-xl text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-all duration-200 shadow-sm">
                                    Batal
                                </a>
                                <button type="submit"
                                    class="inline-flex items-center justify-center px-8 py-3 bg-gradient-to-r from-orange-600 to-red-600 border border-transparent rounded-xl text-sm font-medium text-white hover:from-orange-700 hover:to-red-700 focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-all duration-200 shadow-lg">
                                    <i class="bi bi-save mr-2"></i>
                                    Simpan Penerimaan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

<script>
    // small UX helpers to match branch page style
    document.addEventListener('DOMContentLoaded', function() {
        const purchaseSelect = document.getElementById('purchase_order_id');
        const receiptPhoto = document.getElementById('receipt_photo');

        // call JS loader when purchase order selected (existing file handles this)
        if (purchaseSelect) {
            purchaseSelect.addEventListener('change', function() {
                // ensure external script can react
                if (window.loadPurchaseOrderItems) {
                    window.loadPurchaseOrderItems(this.value);
                }
            });
        }

        // optional preview removal / simple validation for image
        if (receiptPhoto) {
            receiptPhoto.addEventListener('change', function() {
                const f = this.files[0];
                if (!f) return;
                const maxMb = 2;
                if (f.size > maxMb * 1024 * 1024) {
                    alert('Ukuran file melebihi 2MB.');
                    this.value = '';
                }
            });
        }

        // auto determine status from items (kept minimal; purchase-receipts.js should update #status)
        if (window.determinePRStatus) {
            window.determinePRStatus();
        }
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/purchase-receipts.js') }}"></script>
