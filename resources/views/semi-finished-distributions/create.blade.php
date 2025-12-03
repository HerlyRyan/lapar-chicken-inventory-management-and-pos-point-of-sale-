@extends('layouts.app')
@php use Illuminate\Support\Str; @endphp

@section('title', 'Distribusi Bahan Setengah Jadi')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-orange-50 via-white to-red-50 py-6">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <x-form.header title="Distribusi Bahan Setengah Jadi" backRoute="{{ route('semi-finished-distributions.index') }}" />

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden relative">
                    <x-form.card-header title="Form Distribusi" icon="bi-truck" type="add" />

                    <div class="p-6 sm:p-8">
                        @if($isOverview)
                            <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700 flex items-start gap-3">
                                <i class="bi bi-lock-fill text-xl"></i>
                                <div>
                                    <strong>Halaman Terkunci.</strong>
                                    Cabang sumber belum dipilih. Pilih cabang di header terlebih dahulu untuk membuat distribusi. Sumber pengirim harus jelas (disarankan pusat produksi).
                                </div>
                            </div>
                        @endif

                        @if($isNotProduction)
                            <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700 flex items-start gap-3">
                                <i class="bi bi-lock-fill text-xl"></i>
                                <div>
                                    <strong>Halaman Terkunci.</strong>
                                    Distribusi bahan setengah jadi hanya bisa dikirim oleh <strong>pusat produksi</strong>. Silakan ganti cabang di header ke cabang bertipe produksi.
                                </div>
                            </div>
                        @endif

                        <form action="{{ route('semi-finished-distributions.store') }}" method="POST" id="distributionForm" class="space-y-6">
                            @csrf

                            @if($isOverview || $isNotProduction)
                                <fieldset disabled>
                            @endif

                            @if($isOverview || $isNotProduction)
                                <div class="form-locked-overlay absolute inset-0 bg-white/60 backdrop-blur-sm z-10 flex items-center justify-center rounded-2xl">
                                    <div class="inline-flex items-center gap-2 bg-red-50 text-red-700 border border-red-200 px-4 py-2 rounded-lg">
                                        <i class="bi bi-lock-fill"></i>
                                        <span class="font-semibold">Halaman terkunci</span>
                                    </div>
                                </div>
                            @endif

                            {{-- Distribution Info --}}
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                <div>
                                    <label for="branch_id" class="block text-sm font-medium text-gray-700">Cabang Tujuan <span class="text-red-500">*</span></label>
                                    <select name="branch_id" id="branch_id" class="mt-1 block w-full rounded-xl border-gray-300 focus:ring-orange-500 focus:border-orange-500" required>
                                        <option value="">Pilih Cabang</option>
                                        @foreach($branches as $branch)
                                            <option value="{{ $branch->id }}" {{ old('branch_id', request('branch_id')) == $branch->id ? 'selected' : '' }}>
                                                {{ $branch->name }} - {{ $branch->address }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('branch_id')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-2 text-sm text-gray-500">Cabang sumber saat ini tidak ditampilkan pada daftar tujuan untuk mencegah pengiriman ke cabang yang sama.</p>
                                </div>

                                <div>
                                    <label for="distribution_date" class="block text-sm font-medium text-gray-700">Tanggal Distribusi <span class="text-red-500">*</span></label>
                                    <input type="date" name="distribution_date" id="distribution_date" class="mt-1 block w-full rounded-xl border-gray-300 focus:ring-orange-500 focus:border-orange-500" value="{{ old('distribution_date', date('Y-m-d')) }}" required>
                                    @error('distribution_date')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            {{-- Distribution Items --}}
                            <div>
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-md font-semibold text-gray-800">
                                        <i class="bi bi-boxes text-info mr-2"></i>
                                        Produk yang Didistribusikan
                                    </h3>
                                    <button type="button" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-white border border-orange-300 text-orange-700 hover:bg-orange-50" onclick="addDistributionItem()" @if($isOverview || $isNotProduction) disabled @endif>
                                        <i class="bi bi-plus"></i> Tambah Produk
                                    </button>
                                </div>

                                <div id="distribution-items-container" class="space-y-4">
                                    {{-- initial row (index 0) --}}
                                    <div class="distribution-item-row" data-index="0">
                                        <div class="bg-white border border-orange-100 rounded-xl p-4 shadow-sm hover:shadow-lg transition">
                                            <div class="flex items-start justify-between">
                                                <div class="text-orange-600 font-semibold">Produk #1</div>
                                                <button type="button" class="inline-flex items-center justify-center p-2 rounded-lg text-red-600 bg-white/50 border border-red-100" onclick="removeDistributionItem(0)" disabled>
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>

                                            <div class="mt-4 grid grid-cols-1 lg:grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700">Produk <span class="text-red-500">*</span></label>
                                                    <select name="items[0][semi_finished_product_id]" class="product-select mt-1 block w-full rounded-xl border-gray-300 focus:ring-orange-500 focus:border-orange-500" required onchange="updateProductInfo(0)">
                                                        <option value="">Pilih Produk</option>
                                                        @foreach($semiFinishedProducts as $product)
                                                            <option value="{{ $product->id }}"
                                                                    data-stock="{{ $product->center_stock ?? 0 }}"
                                                                    data-unit="{{ $product->unit ?? '' }}"
                                                                    data-name="{{ $product->name }}">
                                                                {{ $product->name }} ({{ $product->code }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div>
                                                    <div class="grid grid-cols-3 gap-3">
                                                        <div>
                                                            <label class="block text-sm font-medium text-gray-700">Jumlah <span class="text-red-500">*</span></label>
                                                            <input type="number" name="items[0][quantity]" class="quantity-input mt-1 block w-full rounded-xl border-gray-300 focus:ring-orange-500 focus:border-orange-500" step="1" min="1" inputmode="numeric" required onchange="validateQuantity(0)">
                                                        </div>
                                                        <div>
                                                            <label class="block text-sm font-medium text-gray-700">Satuan</label>
                                                            <input type="text" class="unit-display mt-1 block w-full rounded-xl bg-gray-100 border-gray-200" readonly>
                                                        </div>
                                                        <div>
                                                            <label class="block text-sm font-medium text-gray-700">Stok Tersedia</label>
                                                            <input type="text" class="stock-display mt-1 block w-full rounded-xl bg-gray-50 border-gray-200 font-semibold" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mt-4">
                                                <label class="block text-sm font-medium text-gray-700">Catatan untuk Produk Ini</label>
                                                <textarea name="items[0][notes]" class="mt-1 block w-full rounded-xl border-gray-300 focus:ring-orange-500 focus:border-orange-500" rows="2" placeholder="Catatan khusus untuk produk ini (opsional)"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @error('items')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- General Notes --}}
                            <div>
                                <h4 class="text-md font-semibold text-gray-800">
                                    <i class="bi bi-journal-text text-gray-500 mr-2"></i>
                                    Catatan Distribusi
                                </h4>
                                <label for="notes" class="sr-only">Catatan Umum</label>
                                <textarea name="notes" id="notes" rows="3" class="mt-2 block w-full rounded-xl border-gray-300 focus:ring-orange-500 focus:border-orange-500" placeholder="Catatan umum untuk distribusi ini, instruksi khusus, dll...">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Actions --}}
                            <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
                                <a href="{{ route('semi-finished-distributions.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-gray-300 bg-white text-gray-700 hover:bg-gray-50">
                                    <i class="bi bi-x-lg"></i> Batal
                                </a>
                                <button type="submit" id="submitBtn" class="inline-flex items-center gap-2 px-6 py-2 rounded-xl bg-gradient-to-r from-orange-600 to-red-600 text-white shadow-lg hover:from-orange-700 hover:to-red-700" @if($isOverview || $isNotProduction) disabled @endif>
                                    <i class="bi bi-send"></i> Kirim Distribusi
                                </button>
                            </div>

                            @if($isOverview || $isNotProduction)
                                </fieldset>
                            @endif
                        </form>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div>
                <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden mb-4">
                    <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                        <div class="font-semibold text-green-700">
                            <i class="bi bi-clipboard-check me-2"></i> Ringkasan Distribusi
                        </div>
                    </div>
                    <div class="p-4" id="distribution-summary">
                        <div class="text-center text-muted py-6 text-gray-500">
                            <i class="bi bi-info-circle mb-2" style="font-size: 1.75rem;"></i>
                            <p>Pilih produk untuk melihat ringkasan distribusi</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                    <div class="p-4 border-b border-gray-100 flex items-center">
                        <div class="font-semibold text-sky-700">
                            <i class="bi bi-info-circle me-2"></i> Stok Tersedia
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="divide-y divide-gray-100">
                            @foreach($semiFinishedProducts->take(5) as $product)
                                <div class="py-2 flex items-center justify-between">
                                    <div>
                                        <small class="font-semibold">{{ Str::limit($product->name, 20) }}</small>
                                    </div>
                                    <div>
                                        <span class="inline-flex items-center gap-2 px-2 py-1 rounded-full text-xs font-medium {{ $product->center_stock > $product->minimum_stock ? 'bg-green-50 text-green-700' : 'bg-yellow-50 text-yellow-700' }}">
                                            {{ number_format($product->center_stock, 0, ',', '.') }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach

                            @if($semiFinishedProducts->count() > 5)
                                <div class="py-2 text-center text-gray-500 text-sm">
                                    +{{ $semiFinishedProducts->count() - 5 }} produk lainnya
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('styles')
<style>
    /* small adjustments for item cards and overlay */
    .distribution-item-row .card { /* compatibility fallback - not used but harmless */ }
    .quantity-input.is-invalid { border-color: #dc3545 !important; box-shadow: none !important; }
    .quantity-input.is-valid { border-color: #16a34a !important; box-shadow: none !important; }
    .form-locked-overlay { /* fallback if used elsewhere */ }
</style>
@endpush

<script>
let distributionItemIndex = 1;

document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const productId = urlParams.get('product');
    const branchId = urlParams.get('branch_id');
    const isOverview = {{ isset($isOverview) && $isOverview ? 'true' : 'false' }};
    const isNotProduction = {{ isset($isNotProduction) && $isNotProduction ? 'true' : 'false' }};
    window.__sfDistLocked = (isOverview || isNotProduction);

    if (productId) {
        const firstSelect = document.querySelector('select[name="items[0][semi_finished_product_id]"]');
        if (firstSelect) {
            firstSelect.value = productId;
            updateProductInfo(0);
        }
    }

    if (branchId) {
        const branchSelect = document.getElementById('branch_id');
        if (branchSelect) branchSelect.value = branchId;
    }

    if (isOverview || isNotProduction) {
        const form = document.getElementById('distributionForm');
        if (form) {
            form.querySelectorAll('input:not([type="hidden"]), select, textarea, button').forEach(el => {
                el.disabled = true;
            });
            const submitBtn = document.getElementById('submitBtn');
            if (submitBtn) {
                submitBtn.title = isOverview
                    ? 'Pilih cabang sumber di header terlebih dahulu'
                    : 'Distribusi hanya bisa dikirim oleh pusat produksi';
            }
            form.querySelectorAll('button').forEach(btn => {
                btn.title = isOverview
                    ? 'Pilih cabang sumber di header terlebih dahulu'
                    : 'Distribusi hanya bisa dikirim oleh pusat produksi';
            });
        }
    }

    updateDistributionSummary();
});

function addDistributionItem() {
    if (window.__sfDistLocked) return;
    const container = document.getElementById('distribution-items-container');
    const newIndex = distributionItemIndex;

    const newItem = document.createElement('div');
    newItem.className = 'distribution-item-row';
    newItem.dataset.index = newIndex;
    newItem.innerHTML = `
        <div class="bg-white border border-orange-100 rounded-xl p-4 shadow-sm hover:shadow-lg transition">
            <div class="flex items-start justify-between">
                <div class="text-orange-600 font-semibold">Produk #${newIndex + 1}</div>
                <button type="button" class="inline-flex items-center justify-center p-2 rounded-lg text-red-600 bg-white/50 border border-red-100" onclick="removeDistributionItem(${newIndex})">
                    <i class="bi bi-trash"></i>
                </button>
            </div>

            <div class="mt-4 grid grid-cols-1 lg:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Produk <span class="text-red-500">*</span></label>
                    <select name="items[${newIndex}][semi_finished_product_id]" class="product-select mt-1 block w-full rounded-xl border-gray-300 focus:ring-orange-500 focus:border-orange-500" required onchange="updateProductInfo(${newIndex})">
                        <option value="">Pilih Produk</option>
                        @foreach($semiFinishedProducts as $product)
                            <option value="{{ $product->id }}"
                                    data-stock="{{ $product->center_stock ?? 0 }}"
                                    data-unit="{{ $product->unit ?? '' }}"
                                    data-name="{{ $product->name }}">
                                {{ $product->name }} ({{ $product->code }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Jumlah <span class="text-red-500">*</span></label>
                            <input type="number" name="items[${newIndex}][quantity]" class="quantity-input mt-1 block w-full rounded-xl border-gray-300 focus:ring-orange-500 focus:border-orange-500" step="1" min="1" inputmode="numeric" required onchange="validateQuantity(${newIndex})">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Satuan</label>
                            <input type="text" class="unit-display mt-1 block w-full rounded-xl bg-gray-100 border-gray-200" readonly>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Stok Tersedia</label>
                            <input type="text" class="stock-display mt-1 block w-full rounded-xl bg-gray-50 border-gray-200 font-semibold" readonly>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700">Catatan untuk Produk Ini</label>
                <textarea name="items[${newIndex}][notes]" class="mt-1 block w-full rounded-xl border-gray-300 focus:ring-orange-500 focus:border-orange-500" rows="2" placeholder="Catatan khusus untuk produk ini (opsional)"></textarea>
            </div>
        </div>
    `;
    container.appendChild(newItem);
    distributionItemIndex++;
    updateDistributionSummary();
}

function removeDistributionItem(index) {
    if (window.__sfDistLocked) return;
    const item = document.querySelector(`[data-index="${index}"]`);
    if (item) {
        item.remove();
        updateDistributionSummary();
    }
}

function updateProductInfo(index) {
    const select = document.querySelector(`select[name="items[${index}][semi_finished_product_id]"]`);
    if (!select) return;
    const option = select.selectedOptions[0];
    const row = select.closest('.distribution-item-row');

    if (option && option.value) {
        const stock = parseFloat(option.dataset.stock) || 0;
        const unit = option.dataset.unit || '';

        row.querySelector('.stock-display').value = Math.floor(stock).toLocaleString('id-ID');
        row.querySelector('.unit-display').value = unit;

        const quantityInput = row.querySelector('.quantity-input');
        quantityInput.max = Math.floor(stock);
        validateQuantity(index);
    } else {
        row.querySelector('.stock-display').value = '';
        row.querySelector('.unit-display').value = '';
    }

    updateDistributionSummary();
}

function validateQuantity(index) {
    const row = document.querySelector(`[data-index="${index}"]`);
    if (!row) return;
    const select = row.querySelector('.product-select');
    const quantityInput = row.querySelector('.quantity-input');
    const option = select ? select.selectedOptions[0] : null;

    if (option && option.value) {
        const stock = parseFloat(option.dataset.stock) || 0;
        const quantity = parseFloat(quantityInput.value) || 0;

        quantityInput.classList.remove('is-invalid', 'is-valid');

        if (quantity > stock) {
            quantityInput.classList.add('is-invalid');
            quantityInput.setCustomValidity('Jumlah melebihi stok tersedia');
        } else if (quantity <= 0) {
            quantityInput.classList.add('is-invalid');
            quantityInput.setCustomValidity('Jumlah harus lebih dari 0');
        } else if (!Number.isInteger(quantity)) {
            quantityInput.classList.add('is-invalid');
            quantityInput.setCustomValidity('Jumlah harus bilangan bulat');
        } else {
            quantityInput.classList.add('is-valid');
            quantityInput.setCustomValidity('');
        }
    }

    updateDistributionSummary();
}

function updateDistributionSummary() {
    const summaryDiv = document.getElementById('distribution-summary');
    const items = document.querySelectorAll('.distribution-item-row');

    let totalItems = 0;
    let summaryHtml = '';

    items.forEach((item) => {
        const select = item.querySelector('.product-select');
        const quantityInput = item.querySelector('.quantity-input');

        if (select && select.value && quantityInput && quantityInput.value) {
            const option = select.selectedOptions[0];
            const productName = option.dataset.name;
            const quantity = parseFloat(quantityInput.value);
            const unit = option.dataset.unit || '';

            summaryHtml += `
                <div class="flex items-center justify-between mb-2">
                    <div><small class="font-semibold">${productName}</small></div>
                    <div class="text-right">
                        <span class="inline-flex items-center gap-2 px-2 py-1 rounded-full bg-blue-50 text-blue-700 text-sm">${quantity.toLocaleString('id-ID')} ${unit}</span>
                    </div>
                </div>
            `;
            totalItems++;
        }
    });

    if (totalItems > 0) {
        summaryHtml = `
            <div class="mb-3">
                <h6 class="text-green-700 font-semibold">
                    <i class="bi bi-check-circle me-1"></i>
                    ${totalItems} Produk Dipilih
                </h6>
            </div>
            ${summaryHtml}
        `;
    } else {
        summaryHtml = `
            <div class="text-center text-gray-500 py-6">
                <i class="bi bi-info-circle mb-2" style="font-size: 1.75rem;"></i>
                <p>Pilih produk untuk melihat ringkasan distribusi</p>
            </div>
        `;
    }

    summaryDiv.innerHTML = summaryHtml;
}

document.getElementById('distributionForm').addEventListener('submit', function(e) {
    if (window.__sfDistLocked) {
        e.preventDefault();
        alert('Halaman terkunci. Pilih cabang di header (pusat produksi) untuk mengirim distribusi.');
        return false;
    }
    const items = document.querySelectorAll('.distribution-item-row');
    let hasValidItems = false;
    let hasInvalid = false;

    items.forEach(item => {
        const index = parseInt(item.dataset.index, 10);
        const select = item.querySelector('.product-select');
        const quantityInput = item.querySelector('.quantity-input');

        validateQuantity(index);

        if (select && select.value) {
            if (quantityInput.checkValidity()) {
                hasValidItems = true;
            } else {
                hasInvalid = true;
            }
        }
    });

    if (!hasValidItems) {
        e.preventDefault();
        alert('Harap pilih minimal satu produk dengan jumlah yang valid.');
        return false;
    }

    if (hasInvalid) {
        e.preventDefault();
        const firstInvalid = document.querySelector('.quantity-input.is-invalid');
        if (firstInvalid) firstInvalid.focus();
        return false;
    }
});
</script>
