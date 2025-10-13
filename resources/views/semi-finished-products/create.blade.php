@extends('layouts.app')

@section('title', 'Tambah Bahan Setengah Jadi')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1" style="color: var(--primary-red);">
            <i class="bi bi-box-seam me-2"></i>Tambah Bahan Setengah Jadi
        </h1>
        <p class="text-muted mb-0">Tambahkan bahan setengah jadi hasil produksi internal</p>
    </div>
    <a href="{{ route('semi-finished-products.index', request()->has('branch_id') ? ['branch_id' => request('branch_id')] : []) }}" class="btn btn-outline-primary">
        <i class="bi bi-arrow-left me-2"></i>Kembali
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-lg">
    <div class="card-header text-white py-3" style="background: linear-gradient(135deg, #0ea5e9 0%, #06b6d4 50%, #10b981 100%);">
        <h5 class="mb-0 fw-bold">
            <i class="bi bi-form me-2"></i>Form Tambah Bahan Setengah Jadi
        </h5>
    </div>
    <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form action="{{ route('semi-finished-products.store', request()->has('branch_id') ? ['branch_id' => request('branch_id')] : []) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @if(request()->has('branch_id'))
                        <input type="hidden" name="branch_id" value="{{ request('branch_id') }}">
                    @endif
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="name" class="form-label fw-semibold">Nama Bahan Setengah Jadi <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" value="{{ old('name') }}" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       placeholder="Masukkan nama bahan setengah jadi" required>
                                <div class="form-text">
                                    <i class="bi bi-info-circle me-1"></i>Nama bahan setengah jadi hasil produksi internal
                                </div>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="code" class="form-label fw-semibold">Kode</label>
                                <input type="text" name="code" id="code" value="{{ old('code') }}" 
                                       class="form-control @error('code') is-invalid @enderror" 
                                       placeholder="Kosongkan untuk generate otomatis">
                                <div class="form-text">
                                    <i class="bi bi-info-circle me-1"></i>Kode akan dibuat otomatis jika tidak diisi (Format: SF-XXX-001)
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
                                    <i class="bi bi-info-circle me-1"></i>Pilih kategori bahan setengah jadi
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
                                    <i class="bi bi-info-circle me-1"></i>Pilih satuan bahan setengah jadi
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
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="minimum_stock" class="form-label fw-semibold">Stok Minimum</label>
                                <input type="number" step="0.01" name="minimum_stock" id="minimum_stock" 
                                       class="form-control @error('minimum_stock') is-invalid @enderror" 
                                       value="{{ old('minimum_stock', 0) }}" placeholder="0.00">
                                <div class="form-text">
                                    <i class="bi bi-info-circle me-1"></i>Batas minimum stok untuk peringatan
                                </div>
                                @error('minimum_stock')
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
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="production_cost" class="form-label fw-semibold">Biaya Produksi</label>
                                <input type="number" step="0.01" name="production_cost" id="production_cost" 
                                       class="form-control @error('production_cost') is-invalid @enderror" 
                                       value="{{ old('production_cost', 0) }}" placeholder="0.00">
                                <div class="form-text">
                                    <i class="bi bi-info-circle me-1"></i>Biaya produksi per satuan (Rupiah)
                                </div>
                                @error('production_cost')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
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
                                        <strong>Semua Cabang</strong>
                                        <small class="d-block text-muted">Stok akan diinisialisasi dengan jumlah yang sama di semua cabang termasuk pusat produksi.</small>
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
                            @foreach($branches as $branch)
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

                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="description" class="form-label fw-semibold">Deskripsi</label>
                                <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" 
                                          rows="3" placeholder="Masukkan deskripsi bahan setengah jadi">{{ old('description') }}</textarea>
                                <div class="form-text">
                                    <i class="bi bi-info-circle me-1"></i>Keterangan tambahan bahan setengah jadi (opsional)
                                </div>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="image" class="form-label fw-semibold">Foto Bahan</label>
                                <input type="file" name="image" id="image" class="form-control @error('image') is-invalid @enderror" 
                                       accept="image/jpeg,image/png,image/jpg,image/gif" onchange="previewImage(this)">
                                <div class="form-text">
                                    <i class="bi bi-info-circle me-1"></i>Upload foto bahan setengah jadi (opsional). Format: JPG, PNG, GIF. Max: 2MB
                                </div>
                                @error('image')
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
                    
                    <div class="mb-4">
                        <div class="form-check">
                            <input type="checkbox" name="is_active" id="is_active" value="1" 
                                   class="form-check-input" {{ old('is_active', 1) ? 'checked' : '' }}>
                            <label for="is_active" class="form-check-label">
                                Status Aktif
                            </label>
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>Centang jika bahan setengah jadi aktif digunakan
                            </div>
                        </div>
                    </div>

                    <hr class="my-4" style="border-color: var(--primary-orange); opacity: 0.3;">

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success shadow" id="submitBtn">
                            <i class="bi bi-check-circle me-2"></i>Simpan
                        </button>
                        <a href="{{ route('semi-finished-products.index', request()->has('branch_id') ? ['branch_id' => request('branch_id')] : []) }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle me-2"></i>Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
    </div>
</div>
@endsection

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
    
    // Stock mode radio buttons functionality
    const stockModeAll = document.getElementById('stock_mode_all');
    const stockModeSelected = document.getElementById('stock_mode_selected');
    const branchSelection = document.getElementById('branch-selection');
    const selectedBranchId = document.getElementById('selected_branch_id');
    
    if (stockModeAll && stockModeSelected && branchSelection) {
        // Initial state
        branchSelection.style.display = stockModeSelected.checked ? 'block' : 'none';
        
        // Add event listeners
        stockModeAll.addEventListener('change', function() {
            if (this.checked) {
                branchSelection.style.display = 'none';
                if (selectedBranchId) {
                    selectedBranchId.required = false;
                }
            }
        });
        
        stockModeSelected.addEventListener('change', function() {
            if (this.checked) {
                branchSelection.style.display = 'block';
                if (selectedBranchId) {
                    selectedBranchId.required = true;
                }
            }
        });
    }
    
    console.log('=== SEMI-FINISHED PRODUCTS CREATE PAGE DEBUG ===');
    console.log('Available branches:', @json($branches ?? []));
    console.log('Selected branch from server:', @json($selectedBranch ?? null));
    console.log('Current branch from server:', @json($currentBranch ?? null));
    
    // Listen for branch selection changes from header
    if (typeof window.updateStockModeAlert === 'undefined') {
        window.updateStockModeAlert = function(selectedBranchId, selectedBranchName) {
            console.log('updateStockModeAlert called with:', selectedBranchId, selectedBranchName);
            
            const stockModeContent = document.getElementById('stock-mode-content');
            const stockModeInput = document.getElementById('stock_mode');
            const headerBranchIdInput = document.getElementById('header_branch_id');
            
            if (!stockModeContent || !stockModeInput || !headerBranchIdInput) {
                console.error('Required elements not found!');
                return;
            }
            
            if (selectedBranchId && selectedBranchName && 
                selectedBranchName !== 'Semua Cabang' && 
                selectedBranchName !== '' && 
                selectedBranchId !== '' && 
                selectedBranchId !== null &&
                selectedBranchId !== 'null') {
                // Mode: Selected Branch
                console.log('Setting mode to SELECTED for branch:', selectedBranchName);
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
                console.log('Setting mode to ALL BRANCHES');
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
        if (isFromUrl) {
            console.log('Skipping sessionStorage check - using URL parameter');
            return;
        }
        
        const selectedBranchId = sessionStorage.getItem('selectedBranchId');
        const selectedBranchName = sessionStorage.getItem('selectedBranchName');
        
        console.log('SessionStorage values:', {
            selectedBranchId: selectedBranchId,
            selectedBranchName: selectedBranchName,
            currentBranchId: currentBranchId,
            currentBranchName: currentBranchName
        });
        
        // Normalize values - treat null/empty as "all branches" mode
        const normalizedBranchId = selectedBranchId === 'null' || selectedBranchId === null || selectedBranchId === '' ? null : selectedBranchId;
        const normalizedBranchName = selectedBranchName === 'null' || selectedBranchName === null || selectedBranchName === '' || selectedBranchName === 'Semua Cabang' ? null : selectedBranchName;
        
        console.log('Normalized values:', {
            normalizedBranchId: normalizedBranchId,
            normalizedBranchName: normalizedBranchName
        });
        
        // Only update if values have actually changed
        if (normalizedBranchId !== currentBranchId || normalizedBranchName !== currentBranchName) {
            console.log('Values changed, updating stock mode');
            currentBranchId = normalizedBranchId;
            currentBranchName = normalizedBranchName;
            
            if (window.updateStockModeAlert) {
                window.updateStockModeAlert(normalizedBranchId, normalizedBranchName);
            }
        } else {
            console.log('No change in values, skipping update');
        }
    }
    
    // Check URL parameter on page load
    function checkUrlParameter() {
        const urlParams = new URLSearchParams(window.location.search);
        const branchIdFromUrl = urlParams.get('branch_id');
        
        console.log('URL branch_id parameter:', branchIdFromUrl);
        
        if (branchIdFromUrl) {
            // Find branch name from the branches data
            @if(isset($branches))
                const branches = @json($branches);
                console.log('Looking for branch with ID:', branchIdFromUrl, 'in branches:', branches);
                const selectedBranch = branches.find(branch => branch.id == branchIdFromUrl);
                if (selectedBranch) {
                    console.log('Found branch from URL:', selectedBranch);
                    currentBranchId = branchIdFromUrl;
                    currentBranchName = selectedBranch.name;
                    isFromUrl = true;
                    
                    if (window.updateStockModeAlert) {
                        window.updateStockModeAlert(branchIdFromUrl, selectedBranch.name);
                    }
                    return; // Exit early if we found branch from URL
                } else {
                    console.log('Branch not found in branches array');
                }
            @else
                console.log('No branches data available from server');
            @endif
        }
        
        // Fallback to sessionStorage only if no URL parameter
        console.log('No URL parameter found, checking sessionStorage');
        checkBranchChange();
    }
    
    // Check initially - prioritize URL parameter
    checkUrlParameter();
    
    // Only start monitoring sessionStorage if we're not using URL parameter
    if (!isFromUrl) {
        console.log('Starting sessionStorage monitoring');
        // Force initial check to handle "Semua Cabang" case
        setTimeout(function() {
            console.log('Running delayed initial check');
            checkBranchChange();
        }, 500);
        
        // Check for changes every 1000ms (reduced frequency)
        setInterval(function() {
            console.log('Running periodic check');
            checkBranchChange();
        }, 1000);
    } else {
        console.log('URL parameter detected, skipping sessionStorage monitoring');
    }
});
</script>
