@extends('layouts.app')

@section('title', 'Buat Purchase Order')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-orange-50 via-white to-red-50 py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Header --}}
            <x-form.header title="Purchase Order" backRoute="{{ route('purchase-orders.index') }}" />

            {{-- Card --}}
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                <x-form.card-header title="Buat Purchase Order" type="add" />

                <div class="p-6 sm:p-8">
                    @if ($errors->any())
                        <div class="mb-4 p-4 rounded-lg bg-red-50 border border-red-100 text-red-700">
                            <h6 class="font-semibold mb-2"><i class="bi bi-exclamation-triangle me-1"></i> Terjadi kesalahan:
                            </h6>
                            <ul class="list-disc pl-5">
                                @foreach ($errors->all() as $error)
                                    <li class="text-sm">{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="mb-4 p-3 rounded-lg bg-green-50 border border-green-100 text-green-700">
                            <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-4 p-3 rounded-lg bg-red-50 border border-red-100 text-red-700">
                            <i class="bi bi-exclamation-triangle me-1"></i> {{ session('error') }}
                        </div>
                    @endif

                    <form id="purchase-order-form" action="{{ route('purchase-orders.store') }}" method="POST">
                        @csrf

                        {{-- Informasi Dasar --}}
                        <div class="mb-8">
                            <div class="flex items-center mb-4">
                                <div
                                    class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center mr-3">
                                    <i class="bi bi-info-circle text-white text-sm"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900">Informasi Dasar</h3>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                <div>
                                    <label for="po_number" class="block text-sm font-medium text-gray-700 mb-2">Nomor
                                        Purchase Order</label>
                                    <div id="po_number"
                                        class="w-full px-4 py-3 bg-gray-50 rounded-xl border border-gray-200 text-gray-600">
                                        Auto Generate</div>
                                    <p class="text-sm text-gray-500 mt-1">Nomor akan dibuat otomatis saat PO disimpan</p>
                                </div>

                                <div>
                                    <label for="supplier_id" class="block text-sm font-medium text-gray-700 mb-2">Supplier
                                        <span class="text-red-500">*</span></label>
                                    <select name="supplier_id" id="supplier_id" required
                                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 @error('supplier_id') border-red-300 ring-2 ring-red-200 @enderror">
                                        <option value="">-- Pilih Supplier --</option>
                                        @foreach ($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}"
                                                {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}
                                                data-phone="{{ $supplier->phone }}" data-code="{{ $supplier->code }}">
                                                {{ $supplier->name }} ({{ $supplier->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('supplier_id')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="text-sm text-gray-500 mt-1">Hanya supplier aktif yang ditampilkan</p>
                                </div>

                                <input type="hidden" name="order_date" id="order_date" value="{{ date('Y-m-d') }}">

                                <div>
                                    <label for="requested_delivery_date"
                                        class="block text-sm font-medium text-gray-700 mb-2">Tanggal Pengiriman yang
                                        Diminta</label>
                                    <input type="date" name="requested_delivery_date" id="requested_delivery_date"
                                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 @error('requested_delivery_date') border-red-300 ring-2 ring-red-200 @enderror"
                                        value="{{ old('requested_delivery_date', date('Y-m-d', strtotime('+3 days'))) }}">
                                    @error('requested_delivery_date')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="supplier_phone" class="block text-sm font-medium text-gray-700 mb-2">Nomor
                                        WhatsApp Supplier</label>
                                    <div class="flex rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 items-center">
                                        <span
                                            class="inline-flex items-center text-sm text-white bg-green-500 rounded px-2 py-1 mr-3">
                                            <i class="bi bi-whatsapp"></i>
                                        </span>
                                        <input type="text" id="supplier_phone" class="flex-1 bg-transparent outline-none"
                                            readonly>
                                    </div>
                                    <p class="text-sm text-gray-500 mt-1">Terisi otomatis dari data supplier</p>
                                </div>

                                <div class="lg:col-span-2">
                                    <label for="notes"
                                        class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                                    <textarea name="notes" id="notes" rows="3"
                                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 @error('notes') border-red-300 ring-2 ring-red-200 @enderror">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Item Pesanan --}}
                        <div class="mb-6">
                            <div class="flex items-center mb-4">
                                <div
                                    class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center mr-3">
                                    <i class="bi bi-table text-white text-sm"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900">Item Pesanan</h3>
                            </div>

                            <div class="rounded-xl border border-gray-200 overflow-hidden shadow-sm bg-white">
                                <div class="p-4 bg-gradient-to-r from-indigo-200 to-purple-200">
                                    <p class="text-sm text-gray-700 mb-0"><i class="bi bi-info-circle me-1"></i> Pilih
                                        supplier terlebih dahulu untuk melihat bahan mentah yang tersedia. Jika supplier
                                        diganti, semua item akan direset.</p>
                                </div>

                                <div class="p-4 overflow-x-auto">
                                    <table class="w-full table-auto border-collapse min-w-[720px]">
                                        <thead>
                                            <tr class="text-sm text-gray-700 bg-gray-50">
                                                <th class="p-3 text-center">#</th>
                                                <th class="p-3">Bahan Mentah</th>
                                                <th class="p-3 text-center">Kuantitas</th>
                                                <th class="p-3 text-center">Satuan</th>
                                                <th class="p-3 text-center">Harga Satuan</th>
                                                <th class="p-3 text-center">Total</th>
                                                <th class="p-3 text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="item-rows" class="min-h-[120px]">
                                            <tr class="empty-row">
                                                <td colspan="7" class="p-8 text-center text-gray-500">
                                                    <div class="flex flex-col items-center">
                                                        <i class="bi bi-cart-x text-3xl mb-3 opacity-50"></i>
                                                        <h6 class="font-medium">Belum ada item pesanan</h6>
                                                        <p class="text-sm">Klik tombol "Tambah Bahan Mentah" untuk menambah
                                                            item</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="5" class="text-right p-3 font-semibold text-gray-700">
                                                    TOTAL KESELURUHAN:</td>
                                                <td colspan="2" class="p-3 text-center">
                                                    <span id="grand-total" class="text-primary text-lg font-bold">Rp
                                                        0</span>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>

                                <div class="p-4 bg-gray-50 flex flex-col gap-2 items-center justify-between">
                                    <div class="flex gap-2">
                                        <button type="button" id="add-item"
                                            class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-xl shadow-sm hover:bg-green-700">
                                            <i class="bi bi-plus-circle me-2"></i>Tambah Bahan Mentah
                                        </button>
                                        <button type="button" id="validate-prices"
                                            class="inline-flex items-center px-4 py-2 border border-indigo-200 rounded-xl text-indigo-700 bg-white hover:bg-indigo-50">
                                            <i class="bi bi-arrow-clockwise me-2"></i>Validasi Harga Terbaru
                                        </button>
                                    </div>
                                    <div class="text-sm text-gray-500"><i class="bi bi-info-circle me-1"></i>Item akan
                                        otomatis dihitung setelah memilih bahan dan mengisi kuantitas</div>
                                </div>
                            </div>
                        </div>

                        {{-- Item Template --}}
                        <template id="item-template">
                            <tr class="item-row align-top">
                                <td class="p-3 text-center font-semibold row-number">1</td>
                                <td class="p-3">
                                    <select name="items[__index__][raw_material_id]"
                                        class="form-select raw-material-select w-full px-3 py-2 border rounded" required>
                                        <option value="">-- Pilih Bahan Mentah --</option>
                                    </select>
                                    <input type="text" name="items[__index__][notes]"
                                        class="w-full mt-2 px-3 py-2 border rounded text-sm"
                                        placeholder="Catatan item (opsional)" maxlength="500">
                                </td>
                                <td class="p-3 text-center">
                                    <input type="number" name="items[__index__][quantity]"
                                        class="w-full sm:w-20 mx-auto px-2 py-2 border rounded text-center item-quantity"
                                        min="1" step="1" required placeholder="1">
                                </td>
                                <td class="p-3 text-center unit-name">-</td>
                                <td class="p-3">
                                    <div class="flex items-center justify-end gap-2">
                                        <span class="text-sm bg-indigo-600 text-white px-2 py-1 rounded">Rp</span>
                                        <input type="number" name="items[__index__][unit_price]"
                                            class="w-full sm:w-32 px-2 py-2 border rounded text-end item-price"
                                            min="0" required placeholder="0" step="1">
                                    </div>
                                </td>
                                <td class="p-3 text-center item-total font-bold text-green-600 whitespace-nowrap">Rp 0</td>
                                <td class="p-3 text-center">
                                    <button type="button"
                                        class="btn-remove inline-flex items-center justify-center w-8 h-8 rounded bg-red-50 border border-red-100 text-red-600"
                                        title="Hapus item">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </template>

                        {{-- Actions --}}
                        <div class="border-t border-gray-200 pt-6">
                            <div class="flex flex-col sm:flex-row gap-3 sm:justify-end">
                                <a href="{{ route('purchase-orders.index') }}"
                                    class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 rounded-xl text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-shadow shadow-sm">
                                    Kembali
                                </a>

                                <button type="submit" name="submit_action" value="save_draft"
                                    class="inline-flex items-center justify-center px-6 py-3 bg-white border border-indigo-200 rounded-xl text-sm font-medium text-indigo-700 hover:bg-indigo-50 shadow-sm">
                                    <i class="bi bi-save me-2"></i> Simpan sebagai Draft
                                </button>

                                <button type="submit" id="order-button" name="submit_action" value="order_now"
                                    class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-orange-600 to-red-600 text-white rounded-xl text-sm font-medium shadow-lg hover:shadow-xl">
                                    <i class="bi bi-whatsapp me-2"></i> Kirim Pesanan via WhatsApp
                                </button>
                            </div>
                            <div class="mt-3 text-center">
                                <small class="text-gray-500"><i class="bi bi-info-circle me-1"></i><strong>Draft:</strong>
                                    Simpan tanpa kirim • <strong>WhatsApp:</strong> Langsung kirim ke supplier</small>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

<script>
    // provide raw materials to JS file
    window.rawMaterials = @json($rawMaterials ?? []);
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/purchase-orders.js') }}"></script>
