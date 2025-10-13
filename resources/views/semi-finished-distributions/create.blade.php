@extends('layouts.app')

@section('title', 'Distribusi Bahan Setengah Jadi')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="bi bi-send text-primary me-2"></i>
                Distribusi Bahan Setengah Jadi
            </h1>
            <p class="text-muted small mb-0">Kirim bahan setengah jadi dari pusat produksi ke cabang</p>
        </div>
        <div>
            <a href="{{ route('semi-finished-distributions.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>
                Kembali
            </a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <!-- Distribution Form -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="bi bi-truck me-2"></i>
                        Form Distribusi
                    </h6>
                </div>
                <div class="card-body">

                    @if($isOverview)
                        <div class="alert alert-danger d-flex align-items-start" role="alert">
                            <i class="bi bi-lock-fill me-2"></i>
                            <div>
                                <strong>Halaman Terkunci.</strong>
                                Cabang sumber belum dipilih. Pilih cabang di header terlebih dahulu untuk membuat distribusi. Sumber pengirim harus jelas (disarankan pusat produksi).
                            </div>
                        </div>
                    @endif

                    @if($isNotProduction)
                        <div class="alert alert-danger d-flex align-items-start" role="alert">
                            <i class="bi bi-lock-fill me-2"></i>
                            <div>
                                <strong>Halaman Terkunci.</strong>
                                Distribusi bahan setengah jadi hanya bisa dikirim oleh <strong>pusat produksi</strong>. Silakan ganti cabang di header ke cabang bertipe produksi.
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('semi-finished-distributions.store') }}" method="POST" id="distributionForm">
                        @csrf
                        @if($isOverview || $isNotProduction)
                            <fieldset disabled>
                        @endif
                        @if($isOverview || $isNotProduction)
                            <div class="form-locked-overlay d-flex align-items-center justify-content-center">
                                <div class="badge bg-danger-subtle text-danger border border-danger fw-semibold py-2 px-3">
                                    <i class="bi bi-lock-fill me-2"></i> Halaman terkunci
                                </div>
                            </div>
                        @endif
                        
                        <!-- Distribution Info Section -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="branch_id" class="form-label">Cabang Tujuan <span class="text-danger">*</span></label>
                                    <select name="branch_id" id="branch_id" class="form-select @error('branch_id') is-invalid @enderror" required>
                                        <option value="">Pilih Cabang</option>
                                        @foreach($branches as $branch)
                                            <option value="{{ $branch->id }}" {{ old('branch_id', request('branch_id')) == $branch->id ? 'selected' : '' }}>
                                                {{ $branch->name }} - {{ $branch->address }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('branch_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        Cabang sumber saat ini tidak ditampilkan pada daftar tujuan untuk mencegah pengiriman ke cabang yang sama.
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="distribution_date" class="form-label">Tanggal Distribusi <span class="text-danger">*</span></label>
                                    <input type="date" name="distribution_date" id="distribution_date" 
                                           class="form-control @error('distribution_date') is-invalid @enderror" 
                                           value="{{ old('distribution_date', date('Y-m-d')) }}" required>
                                    @error('distribution_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Distribution Items Section -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="fw-bold text-secondary mb-0">
                                    <i class="bi bi-boxes me-2"></i>
                                    Produk yang Didistribusikan
                                </h6>
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="addDistributionItem()" @if($isOverview || $isNotProduction) disabled @endif>
                                    <i class="bi bi-plus me-1"></i>
                                    Tambah Produk
                                </button>
                            </div>

                            <div id="distribution-items-container">
                                <!-- Distribution items will be added here -->
                                <div class="distribution-item-row" data-index="0">
                                    <div class="card border-primary mb-3">
                                        <div class="card-header d-flex justify-content-between align-items-center py-2">
                                            <span class="fw-bold text-primary">Produk #1</span>
                                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeDistributionItem(0)" disabled>
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                        <div class="card-body">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label">Produk <span class="text-danger">*</span></label>
                                                    <select name="items[0][semi_finished_product_id]" class="form-select product-select" required onchange="updateProductInfo(0)">
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
                                                <div class="col-md-6">
                                                    <div class="row g-2">
                                                        <div class="col-sm-4">
                                                            <label class="form-label">Jumlah <span class="text-danger">*</span></label>
                                                            <input type="number" name="items[0][quantity]" class="form-control quantity-input" 
                                                                   step="1" min="1" inputmode="numeric" required onchange="validateQuantity(0)">
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <label class="form-label">Satuan</label>
                                                            <input type="text" class="form-control unit-display" readonly>
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <label class="form-label">Stok Tersedia</label>
                                                            <input type="text" class="form-control stock-display" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row g-3 mt-2">
                                                <div class="col-12">
                                                    <label class="form-label">Catatan untuk Produk Ini</label>
                                                    <textarea name="items[0][notes]" class="form-control" rows="2" 
                                                              placeholder="Catatan khusus untuk produk ini (opsional)"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @error('items')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Distribution Notes Section -->
                        <div class="mb-4">
                            <h6 class="fw-bold text-secondary mb-3">
                                <i class="bi bi-journal-text me-2"></i>
                                Catatan Distribusi
                            </h6>
                            <div class="mb-3">
                                <label for="notes" class="form-label">Catatan Umum</label>
                                <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="3" 
                                          placeholder="Catatan umum untuk distribusi ini, instruksi khusus, dll...">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('semi-finished-distributions.index') }}" class="btn btn-outline-secondary me-md-2">
                                <i class="bi bi-x-lg me-1"></i>
                                Batal
                            </a>
                            <button type="submit" class="btn btn-primary" id="submitBtn" @if($isOverview || $isNotProduction) disabled @endif>
                                <i class="bi bi-send me-1"></i>
                                Kirim Distribusi
                            </button>
                        </div>
                        @if($isOverview || $isNotProduction)
                            </fieldset>
                        @endif
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Distribution Summary -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="bi bi-clipboard-check me-2"></i>
                        Ringkasan Distribusi
                    </h6>
                </div>
                <div class="card-body">
                    <div id="distribution-summary">
                        <div class="text-center text-muted py-3">
                            <i class="bi bi-info-circle mb-2" style="font-size: 2rem;"></i>
                            <p>Pilih produk untuk melihat ringkasan distribusi</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Stock Info -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">
                        <i class="bi bi-info-circle me-2"></i>
                        Stok Tersedia
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Produk</th>
                                    <th class="text-end">Stok</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($semiFinishedProducts->take(5) as $product)
                                    <tr>
                                        <td>
                                            <small class="fw-bold">{{ Str::limit($product->name, 15) }}</small>
                                        </td>
                                        <td class="text-end">
                                            <span class="badge bg-{{ $product->center_stock > $product->minimum_stock ? 'success' : 'warning' }}">
                                                {{ number_format($product->center_stock, 0, ',', '.') }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                                @if($semiFinishedProducts->count() > 5)
                                    <tr>
                                        <td colspan="2" class="text-center">
                                            <small class="text-muted">+{{ $semiFinishedProducts->count() - 5 }} produk lainnya</small>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .distribution-item-row .card {
        transition: all 0.3s ease;
    }
    
    .distribution-item-row .card:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
    
    .quantity-input.is-invalid {
        border-color: #dc3545;
    }
    
    .quantity-input.is-valid {
        border-color: #28a745;
    }
    
    .stock-display {
        background-color: #f8f9fa;
        font-weight: bold;
    }
    
    .unit-display {
        background-color: #e9ecef;
    }
    /* Lock overlay covers the form when locked */
    .form-locked-overlay {
        position: absolute;
        inset: 0;
        background: rgba(255,255,255,0.6);
        backdrop-filter: blur(1px);
        z-index: 5;
        pointer-events: all;
        border-radius: 0.5rem;
    }
    /* Ensure overlay anchors to the form */
    #distributionForm { position: relative; }
</style>
@endpush

@push('scripts')
<script>
let distributionItemIndex = 1;

// Pre-select product if passed via query parameter
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const productId = urlParams.get('product');
    const branchId = urlParams.get('branch_id');
    const isOverview = {{ isset($isOverview) && $isOverview ? 'true' : 'false' }};
    const isNotProduction = {{ isset($isNotProduction) && $isNotProduction ? 'true' : 'false' }};
    // Global lock flag so other functions can guard actions
    window.__sfDistLocked = (isOverview || isNotProduction);
    
    if (productId) {
        const firstSelect = document.querySelector('select[name="items[0][semi_finished_product_id]"]');
        if (firstSelect) {
            firstSelect.value = productId;
            updateProductInfo(0);
        }
    }
    // Pre-select branch if provided via query param
    if (branchId) {
        const branchSelect = document.getElementById('branch_id');
        if (branchSelect) {
            branchSelect.value = branchId;
        }
    }

    // Removed page reload on destination branch change to avoid losing selection

    // Disable form controls if in Overview mode or non-production branch
    if (isOverview || isNotProduction) {
        const form = document.getElementById('distributionForm');
        if (form) {
            form.querySelectorAll('input:not([type="hidden"]), select, textarea, button').forEach(el => {
                el.disabled = true;
            });
            // Keep cancel link usable; submit already disabled
            const submitBtn = document.getElementById('submitBtn');
            if (submitBtn) {
                submitBtn.title = isOverview
                    ? 'Pilih cabang sumber di header terlebih dahulu'
                    : 'Distribusi hanya bisa dikirim oleh pusat produksi';
            }
            // Add title to all buttons when locked for better UX
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
    if (window.__sfDistLocked) { return; }
    const container = document.getElementById('distribution-items-container');
    const newIndex = distributionItemIndex;
    
    const newItem = `
        <div class="distribution-item-row" data-index="${newIndex}">
            <div class="card border-primary mb-3">
                <div class="card-header d-flex justify-content-between align-items-center py-2">
                    <span class="fw-bold text-primary">Produk #${newIndex + 1}</span>
                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeDistributionItem(${newIndex})">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Produk <span class="text-danger">*</span></label>
                            <select name="items[${newIndex}][semi_finished_product_id]" class="form-select product-select" required onchange="updateProductInfo(${newIndex})">
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
                        <div class="col-md-6">
                            <div class="row g-2">
                                <div class="col-sm-4">
                                    <label class="form-label">Jumlah <span class="text-danger">*</span></label>
                                    <input type="number" name="items[${newIndex}][quantity]" class="form-control quantity-input" 
                                           step="1" min="1" inputmode="numeric" required onchange="validateQuantity(${newIndex})">
                                </div>
                                <div class="col-sm-4">
                                    <label class="form-label">Satuan</label>
                                    <input type="text" class="form-control unit-display" readonly>
                                </div>
                                <div class="col-sm-4">
                                    <label class="form-label">Stok Tersedia</label>
                                    <input type="text" class="form-control stock-display" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row g-3 mt-2">
                        <div class="col-12">
                            <label class="form-label">Catatan untuk Produk Ini</label>
                            <textarea name="items[${newIndex}][notes]" class="form-control" rows="2" 
                                      placeholder="Catatan khusus untuk produk ini (opsional)"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', newItem);
    distributionItemIndex++;
    updateDistributionSummary();
}

function removeDistributionItem(index) {
    if (window.__sfDistLocked) { return; }
    const item = document.querySelector(`[data-index="${index}"]`);
    if (item) {
        item.remove();
        updateDistributionSummary();
    }
}

function updateProductInfo(index) {
    const select = document.querySelector(`select[name="items[${index}][semi_finished_product_id]"]`);
    const option = select.selectedOptions[0];
    const row = select.closest('.distribution-item-row');
    
    if (option && option.value) {
        const stock = parseFloat(option.dataset.stock) || 0;
        const unit = option.dataset.unit || '';
        
        row.querySelector('.stock-display').value = Math.floor(stock).toLocaleString('id-ID');
        row.querySelector('.unit-display').value = unit;
        
        // Update quantity validation
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
    const select = row.querySelector('.product-select');
    const quantityInput = row.querySelector('.quantity-input');
    const option = select.selectedOptions[0];
    
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
    
    items.forEach((item, index) => {
        const select = item.querySelector('.product-select');
        const quantityInput = item.querySelector('.quantity-input');
        
        if (select.value && quantityInput.value) {
            const option = select.selectedOptions[0];
            const productName = option.dataset.name;
            const quantity = parseFloat(quantityInput.value);
            const unit = option.dataset.unit;
            
            summaryHtml += `
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <small class="fw-bold">${productName}</small>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-primary">${quantity.toLocaleString('id-ID')} ${unit}</span>
                    </div>
                </div>
            `;
            totalItems++;
        }
    });
    
    if (totalItems > 0) {
        summaryHtml = `
            <div class="mb-3">
                <h6 class="text-success">
                    <i class="bi bi-check-circle me-1"></i>
                    ${totalItems} Produk Dipilih
                </h6>
            </div>
            ${summaryHtml}
        `;
    } else {
        summaryHtml = `
            <div class="text-center text-muted py-3">
                <i class="bi bi-info-circle mb-2" style="font-size: 2rem;"></i>
                <p>Pilih produk untuk melihat ringkasan distribusi</p>
            </div>
        `;
    }
    
    summaryDiv.innerHTML = summaryHtml;
}

// Form validation
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

        // Run per-row validation to set validity and classes
        validateQuantity(index);

        if (select.value) {
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
@endpush
@endsection
