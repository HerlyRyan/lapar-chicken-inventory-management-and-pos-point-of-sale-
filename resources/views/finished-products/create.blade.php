@extends('layouts.app')

@section('title', 'Tambah Produk Siap Jual')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1" style="color: var(--primary-red);">
            <i class="bi bi-plus-circle me-2"></i>Tambah Produk Siap Jual
        </h1>
        <p class="text-muted mb-0">Tambah data produk siap jual baru</p>
    </div>
    <a href="{{ route('finished-products.index', request()->has('branch_id') ? ['branch_id' => request('branch_id')] : []) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Kembali
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-lg">
    <div class="card-header text-white py-3" style="background: linear-gradient(135deg, #22c55e 0%, #16a34a 50%, #15803d 100%);">
        <h5 class="mb-0 fw-bold">
            <i class="bi bi-form me-2"></i>Form Tambah Produk Siap Jual
        </h5>
    </div>
    <div class="card-body">
        @php
            $isProductionCenter = false;
            $branchToCheck = null;
            
            if (request('branch_id')) {
                $branchToCheck = \App\Models\Branch::find(request('branch_id'));
            } elseif (auth()->check() && auth()->user()->branch) {
                $branchToCheck = auth()->user()->branch;
            }
            
            if ($branchToCheck && $branchToCheck->type === 'production') {
                $isProductionCenter = true;
            }
        @endphp
        
        @if($isProductionCenter)
            <div class="alert alert-danger border-0 shadow-sm mb-4">
                <div class="d-flex align-items-center">
                    <i class="bi bi-exclamation-triangle-fill fs-3 me-3 text-danger"></i>
                    <div>
                        <h5 class="alert-heading mb-1">Akses Ditolak - Pusat Produksi</h5>
                        <p class="mb-2">
                            <strong>Produk siap jual tidak dapat dibuat di Pusat Produksi.</strong><br>
                            Pusat Produksi hanya bertugas mengolah bahan mentah menjadi bahan setengah jadi.
                        </p>
                        <p class="mb-0 small">
                            <i class="bi bi-info-circle me-1"></i>
                            Untuk membuat produk siap jual, silakan akses dari cabang toko atau pilih cabang toko di header.
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="text-center py-5">
                <i class="bi bi-building-x display-1 text-muted mb-3"></i>
                <h4 class="text-muted">Form Tidak Tersedia</h4>
                <p class="text-muted mb-4">Silakan pilih cabang toko untuk membuat produk siap jual</p>
                <a href="{{ route('finished-products.index') }}" class="btn btn-outline-primary">
                    <i class="bi bi-arrow-left me-2"></i>Kembali ke Daftar Produk
                </a>
            </div>
        @else
        <form action="{{ route('finished-products.store', request()->has('branch_id') ? ['branch_id' => request('branch_id')] : []) }}" method="POST" enctype="multipart/form-data" onsubmit="console.log('Form submitted!');">
            @csrf
            @if(request()->has('branch_id'))
                <input type="hidden" name="header_branch_id" value="{{ request('branch_id') }}">
                <input type="hidden" name="branch_id" value="{{ request('branch_id') }}">
            @endif
            
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Nama Produk Siap Jual <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" 
                               class="form-control @error('name') is-invalid @enderror" 
                               placeholder="Masukkan nama produk siap jual" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="code" class="form-label fw-semibold">Kode Produk</label>
                        <input type="text" name="code" id="code" value="{{ old('code') }}" 
                               class="form-control @error('code') is-invalid @enderror" 
                               placeholder="Kosongkan untuk generate otomatis">
                        <div class="form-text">
                            <i class="bi bi-info-circle me-1"></i>Kode akan dibuat otomatis jika tidak diisi (Format: FP-XXX-001)
                        </div>
                        @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="category_id" class="form-label fw-semibold">
                            Kategori <span class="text-danger">*</span>
                        </label>
                        <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                            <option value="">- Pilih Kategori -</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text">
                            <i class="bi bi-info-circle me-1"></i>Pilih kategori untuk produk siap jual
                        </div>
                        <div class="form-text mt-1">
                            <a href="{{ route('categories.create') }}" target="_blank" rel="noopener" class="text-decoration-underline">
                                <i class="bi bi-box-arrow-up-right me-1"></i>Tambah kategori di tab baru
                            </a>
                        </div>
                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="unit_id" class="form-label fw-semibold">
                            Satuan <span class="text-danger">*</span>
                        </label>
                        <select name="unit_id" id="unit_id" class="form-select @error('unit_id') is-invalid @enderror" required>
                            <option value="">- Pilih Satuan -</option>
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>
                                    {{ $unit->unit_name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text">
                            <i class="bi bi-info-circle me-1"></i>Pilih satuan produk siap jual
                        </div>
                        <div class="form-text mt-1">
                            <a href="{{ route('units.create') }}" target="_blank" rel="noopener" class="text-decoration-underline">
                                <i class="bi bi-box-arrow-up-right me-1"></i>Tambah satuan di tab baru
                            </a>
                        </div>
                        @error('unit_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="minimum_stock" class="form-label fw-semibold">Stok Minimum</label>
                        <input type="number" name="minimum_stock" id="minimum_stock" 
                               value="{{ old('minimum_stock', 0) }}" 
                               class="form-control @error('minimum_stock') is-invalid @enderror" 
                               step="0.01" min="0" placeholder="0.00">
                        <div class="form-text">
                            <i class="bi bi-info-circle me-1"></i>Stok minimum untuk peringatan
                        </div>
                        @error('minimum_stock')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="price" class="form-label fw-semibold">Harga Jual <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="price" id="price" 
                               class="form-control @error('price') is-invalid @enderror" 
                               value="{{ old('price', 0) }}" placeholder="0.00" required>
                        <div class="form-text">
                            <i class="bi bi-info-circle me-1"></i>Harga jual per satuan (Rupiah)
                        </div>
                        @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="base_cost" class="form-label fw-semibold">Modal Dasar</label>
                        <input type="number" step="0.01" name="base_cost" id="base_cost" 
                               class="form-control @error('base_cost') is-invalid @enderror" 
                               value="{{ old('base_cost', old('production_cost', 0)) }}" placeholder="0.00" max="">
                        <div class="form-text">
                            <i class="bi bi-info-circle me-1"></i>Modal dasar produk untuk perhitungan kerugian pemusnahan (tidak boleh melebihi harga jual)
                        </div>
                        @error('base_cost')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="stock_quantity" class="form-label fw-semibold">Stok Awal</label>
                        <input type="number" name="stock_quantity" id="stock_quantity" 
                               value="{{ old('stock_quantity', 0) }}" 
                               class="form-control @error('stock_quantity') is-invalid @enderror" 
                               step="0.01" min="0" placeholder="0.00">
                        @error('stock_quantity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <!-- Removed duplicate production_cost input to avoid confusion; base_cost is used instead -->
            </div>

            <!-- Stock Initialization Mode Selection -->
            <div class="mb-4">
                <label class="form-label fw-semibold">Mode Inisialisasi Stok <span class="text-danger">*</span></label>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="stock_mode" id="stock_mode_all" value="all" 
                                   {{ old('stock_mode', !$selectedBranch ? 'all' : 'selected') == 'all' ? 'checked' : '' }}>
                            <label class="form-check-label" for="stock_mode_all">
                                <i class="bi bi-buildings text-success me-1"></i>
                                <strong>Semua Cabang Retail</strong>
                                <small class="d-block text-muted">Stok akan diinisialisasi dengan jumlah yang sama di semua cabang retail.</small>
                                <small class="d-block text-warning">Pengecualian: <strong>Pusat Produksi</strong> tidak akan mendapatkan inisialisasi stok karena tidak berjualan.</small>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="stock_mode" id="stock_mode_selected" value="selected" 
                                   {{ old('stock_mode', $selectedBranch ? 'selected' : 'all') == 'selected' ? 'checked' : '' }}>
                            <label class="form-check-label" for="stock_mode_selected">
                                <i class="bi bi-building text-info me-1"></i>
                                <strong>Cabang Tertentu</strong>
                                <small class="d-block text-muted">Stok hanya diinisialisasi untuk cabang yang dipilih.</small>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Branch Selection (only shown when 'selected' mode is chosen) -->
            <div class="mb-4" id="branch-selection" style="display: {{ old('stock_mode', $selectedBranch ? 'selected' : 'all') == 'selected' ? 'block' : 'none' }};">
                <label for="selected_branch_id" class="form-label fw-semibold">Pilih Cabang <span class="text-danger">*</span></label>
                <select name="selected_branch_id" id="selected_branch_id" class="form-select @error('selected_branch_id') is-invalid @enderror">
                    <option value="">-- Pilih Cabang --</option>
                    @foreach($retailBranches as $branch)
                        <option value="{{ $branch->id }}" 
                                {{ old('selected_branch_id', $selectedBranch ? $selectedBranch->id : '') == $branch->id ? 'selected' : '' }}>
                            {{ $branch->name }}
                        </option>
                    @endforeach
                </select>
                @error('selected_branch_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="description" class="form-label fw-semibold">Deskripsi</label>
                <textarea name="description" id="description" rows="3" 
                          class="form-control @error('description') is-invalid @enderror" 
                          placeholder="Deskripsi produk siap jual (opsional)">{{ old('description') }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="photo" class="form-label fw-semibold">Foto Produk</label>
                        <input type="file" name="photo" id="photo" 
                               class="form-control @error('photo') is-invalid @enderror" 
                               accept="image/jpeg,image/png,image/jpg,image/gif" onchange="previewImage(this)">
                        <div class="form-text">
                            <i class="bi bi-info-circle me-1"></i>Upload foto produk (opsional). Format: JPG, PNG, GIF. Max: 2MB
                        </div>
                        @error('photo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Preview Foto</label>
                    <div class="d-flex align-items-center justify-content-center bg-light rounded" 
                         style="height: 120px; border: 2px dashed #ddd;" id="image-preview-container">
                        <div class="text-center text-muted" id="image-preview-text">
                            <i class="bi bi-image fs-1 mb-2 d-block"></i>
                            <small>Preview foto akan tampil di sini</small>
                        </div>
                        <img id="imagePreview" src="" alt="Preview" class="img-fluid rounded d-none" style="max-height: 110px;">
                    </div>
                </div>
            </div>

            <div class="mb-4 mt-2">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold" for="is_active">
                        Status Aktif
                    </label>
                </div>
                <div class="form-text">Centang jika produk siap jual aktif digunakan</div>
            </div>

            <hr class="my-4">

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success shadow" id="submitBtn">
                    <i class="bi bi-check-circle me-2"></i>Simpan
                </button>
                <a href="{{ route('finished-products.index', request()->has('branch_id') ? ['branch_id' => request('branch_id')] : []) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-x-circle me-2"></i>Batal
                </a>
            </div>
        </form>
        @endif
    </div>
</div>
    </div>
</div>

<script>
function previewImage(input) {
    const file = input.files[0];
    const previewContainer = document.getElementById('image-preview-container');
    const previewText = document.getElementById('image-preview-text');
    const previewImg = document.getElementById('imagePreview');
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            previewImg.classList.remove('d-none');
            previewText.classList.add('d-none');
        };
        reader.readAsDataURL(file);
    } else {
        previewImg.classList.add('d-none');
        previewText.classList.remove('d-none');
        previewImg.src = '';
    }
}

// Form submission debugging
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            console.log('Form submitted successfully');
        });
    }
    
    // Listen for branch selection changes from header
    if (typeof window.updateStockModeAlert === 'undefined') {
        window.updateStockModeAlert = function(selectedBranchId, selectedBranchName) {
            const stockModeContent = document.getElementById('stock-mode-content');
            const stockModeInput = document.getElementById('stock_mode');
            const headerBranchIdInput = document.getElementById('header_branch_id');
            
            if (selectedBranchId && selectedBranchName && selectedBranchName !== 'Semua Cabang') {
                // Mode: Selected Branch
                stockModeContent.innerHTML = `
                    <p class="mb-0 mt-1">
                        <span class="badge bg-info me-1"><i class="bi bi-building"></i></span>
                        <strong>Cabang ${selectedBranchName}:</strong> Stok akan diinisialisasi untuk cabang ini
                    </p>
                `;
                stockModeInput.value = 'selected';
                headerBranchIdInput.value = selectedBranchId;
            } else {
                // Mode: All Branches
                stockModeContent.innerHTML = `
                    <p class="mb-0 mt-1">
                        <span class="badge bg-success me-1"><i class="bi bi-buildings"></i></span>
                        <strong>Semua Cabang:</strong> Stok akan diinisialisasi dengan jumlah yang sama di semua cabang
                    </p>
                `;
                stockModeInput.value = 'all';
                headerBranchIdInput.value = '';
            }
        };
    }
    
    // Track current state to prevent unnecessary updates
    let currentBranchId = null;
    let currentBranchName = null;
    let isFromUrl = false;
    
    // Listen for branch changes from sessionStorage
    function checkBranchChange() {
        // Don't override if we already set from URL parameter
        if (isFromUrl) return;
        
        const selectedBranchId = sessionStorage.getItem('selectedBranchId');
        const selectedBranchName = sessionStorage.getItem('selectedBranchName');
        
        // Only update if values have actually changed
        if (selectedBranchId !== currentBranchId || selectedBranchName !== currentBranchName) {
            currentBranchId = selectedBranchId;
            currentBranchName = selectedBranchName;
            
            if (window.updateStockModeAlert) {
                window.updateStockModeAlert(selectedBranchId, selectedBranchName);
            }
        }
    }
    
    // Check URL parameter on page load
    function checkUrlParameter() {
        const urlParams = new URLSearchParams(window.location.search);
        const branchIdFromUrl = urlParams.get('branch_id');
        
        if (branchIdFromUrl) {
            // Find branch name from the branches data
            @if(isset($branches))
                const branches = @json($branches);
                const selectedBranch = branches.find(branch => branch.id == branchIdFromUrl);
                if (selectedBranch) {
                    currentBranchId = branchIdFromUrl;
                    currentBranchName = selectedBranch.name;
                    isFromUrl = true;
                    
                    if (window.updateStockModeAlert) {
                        window.updateStockModeAlert(branchIdFromUrl, selectedBranch.name);
                    }
                    return; // Exit early if we found branch from URL
                }
            @endif
        }
        
        // Fallback to sessionStorage only if no URL parameter
        checkBranchChange();
    }
    
    // Check initially - prioritize URL parameter
    checkUrlParameter();
    
    // Only start monitoring sessionStorage if we're not using URL parameter
    if (!isFromUrl) {
        // Check for changes every 1000ms (reduced frequency)
        setInterval(checkBranchChange, 1000);
    }
    
    // Handle stock mode radio button changes
    const stockModeRadios = document.querySelectorAll('input[name="stock_mode"]');
    const branchSelection = document.getElementById('branch-selection');
    const stockInfoText = document.getElementById('stock-info-text');
    const selectedBranchSelect = document.getElementById('selected_branch_id');
    
    stockModeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'selected') {
                branchSelection.style.display = 'block';
                if (stockInfoText) {
                    stockInfoText.textContent = 'Stok akan diinisialisasi untuk cabang yang dipilih';
                }
            } else {
                branchSelection.style.display = 'none';
                selectedBranchSelect.value = '';
                if (stockInfoText) {
                    stockInfoText.textContent = 'Stok akan diinisialisasi dengan jumlah yang sama di semua cabang retail';
                }
            }
        });
    });
    
    // Handle branch selection changes
    selectedBranchSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const branchName = selectedOption.text;
        
        // Check if selected branch is production center
        @if(isset($branches))
            const branches = @json($branches);
            const selectedBranch = branches.find(branch => branch.id == this.value);
            
            const stockQuantityInput = document.getElementById('stock_quantity');
            const minStockInput = document.getElementById('minimum_stock');
            const productionWarning = document.getElementById('production-center-warning');
            const stockModeInfo = document.getElementById('stock-mode-info');
            
            if (selectedBranch && selectedBranch.type === 'production') {
                // Disable stock inputs for production center
                stockQuantityInput.disabled = true;
                minStockInput.disabled = true;
                stockQuantityInput.value = 0;
                minStockInput.value = 0;
                
                // Show production center warning
                if (productionWarning) {
                    productionWarning.style.display = 'block';
                }
                if (stockModeInfo) {
                    stockModeInfo.style.display = 'none';
                }
            } else {
                // Enable stock inputs for retail branches
                stockQuantityInput.disabled = false;
                minStockInput.disabled = false;
                
                // Hide production center warning
                if (productionWarning) {
                    productionWarning.style.display = 'none';
                }
                if (stockModeInfo) {
                    stockModeInfo.style.display = 'block';
                }
            }
        @endif
    });
    
    // Initialize form state on page load
    const initialStockMode = document.querySelector('input[name="stock_mode"]:checked');
    if (initialStockMode && initialStockMode.value === 'selected') {
        branchSelection.style.display = 'block';
    }
});
</script>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Base cost validation - ensure it doesn't exceed price
    function validateBaseCost() {
        const price = parseFloat($('#price').val()) || 0;
        const baseCost = parseFloat($('#base_cost').val()) || 0;
        const baseCostInput = $('#base_cost');
        const group = baseCostInput.closest('.mb-3');
        // Remove any previous JS-added feedback to prevent duplicates
        group.find('.invalid-feedback.base-cost-js').remove();
        
        if (baseCost > price) {
            baseCostInput.addClass('is-invalid');
            // Append a uniquely-classed feedback so we don't interfere with server-side errors
            group.find('.form-text').after('<div class="invalid-feedback d-block base-cost-js">Modal dasar tidak boleh melebihi harga jual</div>');
            return false;
        } else {
            baseCostInput.removeClass('is-invalid');
            // Ensure any JS-added message is removed on valid state
            group.find('.invalid-feedback.base-cost-js').remove();
            return true;
        }
    }
    
    // Update max attribute and validate on price change
    $('#price').on('input', function() {
        const price = $(this).val();
        $('#base_cost').attr('max', price);
        validateBaseCost();
    });
    
    // Validate on base cost change
    $('#base_cost').on('input', function() {
        validateBaseCost();
    });
    
    // Initialize max attribute
    const initialPrice = $('#price').val();
    if (initialPrice) {
        $('#base_cost').attr('max', initialPrice);
    }
});
</script>
@endpush