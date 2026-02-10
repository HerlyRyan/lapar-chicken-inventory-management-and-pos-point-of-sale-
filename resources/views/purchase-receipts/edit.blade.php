@extends('layouts.app')
@php use Illuminate\Support\Facades\Storage; @endphp

@section('title', 'Edit Penerimaan Barang')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-orange-50 via-white to-red-50 py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Header --}}
            <x-form.header title="Penerimaan Barang" edit="true" :name="$purchaseReceipt->receipt_number" detailRoute="{{ route('purchase-receipts.show', $purchaseReceipt) }}" backRoute="{{ route('purchase-receipts.index') }}"/>

            {{-- Card --}}
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden mt-4">
                <x-form.card-header title="Edit Penerimaan Barang" type="edit" />

                <div class="p-6 sm:p-8">
                    @include('purchase-receipts.partials.alerts')

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

                    <form method="POST" action="{{ route('purchase-receipts.update', $purchaseReceipt) }}" enctype="multipart/form-data" id="purchaseReceiptForm">
                        @csrf
                        @method('PUT')

                        {{-- Basic Info --}}
                        <div class="mb-6">
                            <div class="flex items-center mb-4">
                                <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center mr-3">
                                    <i class="bi bi-pencil-square text-white text-sm"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900">Informasi Penerimaan</h3>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                                <div class="lg:col-span-2">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Pesanan Pembelian</label>
                                    <input type="text" class="w-full px-4 py-3 border rounded-xl bg-gray-50" value="{{ $purchaseReceipt->purchaseOrder->order_number }} - {{ $purchaseReceipt->purchaseOrder->supplier->name }}" readonly>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Penerimaan <span class="text-red-500">*</span></label>
                                    <input type="date" name="receipt_date" class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200" value="{{ old('receipt_date', $purchaseReceipt->receipt_date->format('Y-m-d')) }}" required>
                                </div>

                                <input type="hidden" name="status" id="status" value="">
                            </div>
                        </div>

                        {{-- Photo & Notes --}}
                        <div class="mb-6">
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Foto Bukti Penerimaan
                                        @if(!$purchaseReceipt->receipt_photo) <span class="text-red-500">*</span> @endif
                                    </label>
                                    <div class="flex items-center gap-3">
                                        <input type="file" name="receipt_photo" id="receipt_photo" class="w-full px-4 py-3 border rounded-xl bg-white" accept="image/jpeg,image/png,image/jpg" @if(!$purchaseReceipt->receipt_photo) required @endif>
                                    </div>
                                    <p class="mt-2 text-sm text-gray-600">
                                        @if($purchaseReceipt->receipt_photo)
                                            Kosongkan jika tidak ingin mengubah foto. Format: JPG, PNG. Maksimal 2MB
                                        @else
                                            Wajib diunggah. Format: JPG, PNG. Maksimal 2MB
                                        @endif
                                    </p>
                                    @if($purchaseReceipt->receipt_photo)
                                        <div class="mt-2">
                                            <small class="text-info text-sm">
                                                <i class="bi bi-info-circle me-1"></i>Foto saat ini:
                                                <a href="{{ Storage::url($purchaseReceipt->receipt_photo) }}" target="_blank" class="text-blue-600 underline">Lihat foto</a>
                                            </small>
                                        </div>
                                    @endif
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Catatan</label>
                                    <textarea name="notes" id="notes" rows="3" class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200" placeholder="Tambahkan catatan penerimaan...">{{ old('notes', $purchaseReceipt->notes) }}</textarea>
                                </div>
                            </div>
                        </div>

                        {{-- Items Section --}}
                        <div class="mb-6 bg-white rounded-xl border border-gray-200 overflow-hidden">
                            <div class="bg-gray-50 p-4 border-b">
                                <h6 class="font-semibold text-gray-800">Detail Item Penerimaan</h6>
                            </div>
                            <div class="p-4">
                                <div id="items-container" class="space-y-3">
                                    @foreach($purchaseReceipt->items as $index => $item)
                                        <div class="mb-3 p-3 border rounded-xl pr-row overflow-hidden" data-price="{{ $item->unit_price }}">
                                            <input type="hidden" name="items[{{ $item->id }}][purchase_order_item_id]" value="{{ $item->purchase_order_item_id }}">

                                            <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                                                <div class="md:col-span-4">
                                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Bahan</label>
                                                    <input type="text" class="w-full px-3 py-2 border rounded-lg bg-gray-50 truncate" value="{{ $item->rawMaterial->name }}" readonly>
                                                    <small class="text-gray-500 text-sm break-words">{{ $item->rawMaterial->code }}</small>
                                                </div>

                                                <div class="md:col-span-2">
                                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Dipesan</label>
                                                    <input type="text" class="w-full px-3 py-2 border rounded-lg bg-gray-50" value="{{ number_format($item->ordered_quantity, 0, ',', '.') }} {{ $item->rawMaterial->unit->name }}" readonly>
                                                </div>

                                                <div class="md:col-span-3">
                                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Diterima <span class="text-red-500">*</span></label>
                                                    <div class="flex items-center space-x-2">
                                                        <button class="inline-flex items-center justify-center w-10 h-10 border rounded-lg bg-white btn-decrement flex-shrink-0" type="button" aria-label="Kurangi">
                                                            <i class="bi bi-dash"></i>
                                                        </button>
                                                        <input type="number" name="items[{{ $item->id }}][received_quantity]" class="received-input flex-1 px-3 py-2 border rounded-lg w-full min-w-0" step="1" min="0" max="{{ $item->ordered_quantity }}" value="{{ old('items.'.$item->id.'.received_quantity', $item->received_quantity) }}" required>
                                                        <button class="inline-flex items-center justify-center w-10 h-10 border rounded-lg bg-white btn-increment flex-shrink-0" type="button" aria-label="Tambah">
                                                            <i class="bi bi-plus"></i>
                                                        </button>
                                                    </div>
                                                    <div class="text-xs text-red-600 mt-1">Nilai diterima harus 0 - {{ number_format($item->ordered_quantity, 0, ',', '.') }} {{ $item->rawMaterial->unit->name }}</div>
                                                </div>

                                                <div class="md:col-span-2">
                                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Ditolak</label>
                                                    <div class="text-sm text-gray-500 js-rejected break-words">Otomatis: {{ number_format($item->rejected_quantity, 0, ',', '.') }} {{ $item->rawMaterial->unit->name }}</div>
                                                    <input type="hidden" name="items[{{ $item->id }}][rejected_quantity]" value="">
                                                </div>

                                                <div class="md:col-span-1">
                                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Status</label>
                                                    <div class="text-sm js-item-status truncate">Otomatis: {{ ucfirst(trans('purchase-receipts.item_status.' . $item->item_status)) }}</div>
                                                    <input type="hidden" name="items[{{ $item->id }}][item_status]" value="">
                                                </div>
                                            </div>

                                            <div class="mt-4">
                                                <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                                    <div class="js-progress {{ $item->item_status === 'accepted' ? 'bg-green-500' : ($item->item_status === 'rejected' ? 'bg-red-500' : 'bg-yellow-400') }}" role="progressbar" style="width: {{ $item->ordered_quantity > 0 ? min(100, ($item->received_quantity / $item->ordered_quantity) * 100) : 0 }}% ; height: 6px;"></div>
                                                </div>
                                                <div class="text-sm text-gray-500 mt-1 js-progress-text truncate">{{ number_format($item->received_quantity, 0, ',', '.') }} / {{ number_format($item->ordered_quantity, 0, ',', '.') }} {{ $item->rawMaterial->unit->name }} ({{ number_format($item->ordered_quantity > 0 ? min(100, ($item->received_quantity / $item->ordered_quantity) * 100) : 0, 0, ',', '.') }}%)</div>
                                            </div>

                                            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Foto Kondisi Item @if(!$item->condition_photo) <span class="text-red-500">*</span> @endif</label>
                                                    <input type="file" name="items[{{ $item->id }}][condition_photo]" class="w-full px-3 py-2 border rounded-lg bg-white" accept="image/jpeg,image/png,image/jpg" @if(!$item->condition_photo) required @endif>
                                                    <p class="mt-2 text-sm text-gray-600">
                                                        @if($item->condition_photo)
                                                            Kosongkan jika tidak ingin mengubah foto. Format: JPG, PNG. Maksimal 2MB
                                                        @else
                                                            Wajib diunggah karena item belum memiliki foto. Format: JPG, PNG. Maksimal 2MB
                                                        @endif
                                                    </p>
                                                    @if($item->condition_photo)
                                                        <div class="mt-2">
                                                            <small class="text-info text-sm break-words">
                                                                <i class="bi bi-info-circle me-1"></i>Foto saat ini:
                                                                <a href="{{ Storage::url($item->condition_photo) }}" target="_blank" class="text-blue-600 underline">Lihat foto</a>
                                                            </small>
                                                        </div>
                                                    @endif
                                                </div>

                                                <div>
                                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Catatan Item</label>
                                                    <textarea name="items[{{ $item->id }}][notes]" class="w-full px-3 py-2 border rounded-lg" rows="2" placeholder="Catatan khusus untuk item ini...">{{ old('items.'.$item->id.'.notes', $item->notes) }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        {{-- Additional Costs + Summary --}}
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
                            <div class="lg:col-span-2">
                                @include('purchase-receipts.partials.additional-costs', ['prefix' => 'pr', 'existingCosts' => $purchaseReceipt->additionalCosts])
                                <div class="mt-3">
                                    @include('purchase-receipts.partials.discount-tax', ['model' => $purchaseReceipt])
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
                                <a href="{{ route('purchase-receipts.show', $purchaseReceipt) }}" class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 rounded-xl text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-all duration-200 shadow-sm">
                                    Batal
                                </a>
                                <button type="submit" class="inline-flex items-center justify-center px-8 py-3 bg-gradient-to-r from-orange-600 to-red-600 border border-transparent rounded-xl text-sm font-medium text-white hover:from-orange-700 hover:to-red-700 focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-all duration-200 shadow-lg">
                                    <i class="bi bi-save mr-2"></i>
                                    Update Penerimaan
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
    document.addEventListener('DOMContentLoaded', function() {
        const receiptPhoto = document.getElementById('receipt_photo');

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

        // ensure external script can initialize status from existing items
        if (window.determinePRStatus) {
            window.determinePRStatus();
        }
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/purchase-receipts.js') }}"></script>

