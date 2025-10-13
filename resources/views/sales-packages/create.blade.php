@extends('layouts.app')

@section('content')
<!-- No hidden form needed for refresh token -->
<div class="container-fluid">
    <x-page-header 
        title="Tambah Paket Penjualan" 
        subtitle="Buat paket produk baru untuk standar penjualan"
        :breadcrumb="[
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => 'Paket Penjualan', 'url' => route('sales-packages.index')],
            ['label' => 'Tambah Paket', 'active' => true]
        ]"
    />

    <form action="{{ route('sales-packages.store') }}" method="POST" enctype="multipart/form-data" id="salesPackageForm" class="compact-form">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <!-- Keep explicit CSRF token above to fix issues with AutoLogin middleware -->
        <div class="row">
            <!-- Form Section -->
            <div class="col-lg-9">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-info-circle me-2"></i>Informasi Paket
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Row 1: Name -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="name" class="form-label">Nama Paket <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name') }}" 
                                       placeholder="Contoh: Paket Lapar Aja" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Kode paket akan dibuat otomatis</div>
                            </div>
                        </div>

                        <!-- Row 2: Description and Category side by side -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="description" class="form-label">Deskripsi</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="3" 
                                          placeholder="Deskripsi paket penjualan (opsional)">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="category_id" class="form-label">Kategori <span class="text-danger">*</span></label>
                                <select class="form-select @error('category_id') is-invalid @enderror" 
                                        id="category_id" name="category_id" required>
                                    <option value="" selected disabled>Pilih Kategori</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" 
                                            data-name="{{ $category->name }}" 
                                            {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="category_name" id="category_name" value="{{ old('category_name') }}">
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Kategori akan digunakan untuk filter paket penjualan</div>
                                <div class="form-text mt-1">
                                    <a href="{{ route('categories.create') }}" target="_blank" rel="noopener" class="text-decoration-underline">
                                        <i class="bi bi-box-arrow-up-right me-1"></i>Tambah kategori di tab baru
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Row 3: Pricing -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="discount_percentage" class="form-label">Diskon (%)</label>
                                <div class="input-group">
                                    <button class="btn btn-outline-secondary" type="button" onclick="decrement('discount_percentage')">-</button>
                                    <input type="number" class="form-control @error('discount_percentage') is-invalid @enderror" 
                                           id="discount_percentage" name="discount_percentage" 
                                           value="{{ old('discount_percentage') }}" 
                                           placeholder="0" min="0" max="100" step="0.01">
                                    <button class="btn btn-outline-secondary" type="button" onclick="increment('discount_percentage')">+</button>
                                </div>
                                @error('discount_percentage')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="discount_amount" class="form-label">Diskon (Rp)</label>
                                <div class="input-group">
                                    <button class="btn btn-outline-secondary" type="button" onclick="decrement('discount_amount')">-</button>
                                    <input type="number" class="form-control @error('discount_amount') is-invalid @enderror" 
                                           id="discount_amount" name="discount_amount" 
                                           value="{{ old('discount_amount') }}" 
                                           placeholder="0" min="0" step="1">
                                    <button class="btn btn-outline-secondary" type="button" onclick="increment('discount_amount')">+</button>
                                </div>
                                @error('discount_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="additional_charge" class="form-label">Biaya Tambahan (Rp)</label>
                                <div class="input-group">
                                    <button class="btn btn-outline-secondary" type="button" onclick="decrement('additional_charge')">-</button>
                                    <input type="number" class="form-control @error('additional_charge') is-invalid @enderror" 
                                           id="additional_charge" name="additional_charge" 
                                           value="{{ old('additional_charge') }}" 
                                           placeholder="0" min="0" step="1">
                                    <button class="btn btn-outline-secondary" type="button" onclick="increment('additional_charge')">+</button>
                                </div>
                                @error('additional_charge')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="alert alert-info py-1 small">
                                    <i class="bi bi-info-circle me-1"></i>
                                    <small><strong>Catatan:</strong> Pilih diskon % ATAU nominal. Biaya tambahan untuk packaging, delivery, dll.</small>
                                </div>
                            </div>
                        </div>

                        <!-- Row 4: Image -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="image" class="form-label">Foto Paket</label>
                                <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                       id="image" name="image" accept="image/*" onchange="previewImage(this)">
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Format: JPG, PNG, GIF. Maksimal 2MB.</div>
                            </div>
                        </div>

                        <!-- Image Preview -->
                        <div class="row mb-3" id="imagePreview" style="display: none;">
                            <div class="col-md-12">
                                <img id="previewImg" src="" alt="Preview" class="img-thumbnail" style="max-height: 200px;">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Package Items Section -->
                <div class="card shadow-sm mt-3">
                    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-box-seam me-2"></i>Komponen Paket
                        </h5>
                        <button type="button" class="btn btn-light btn-sm" onclick="addPackageItem()">
                            <i class="bi bi-plus-lg"></i> Tambah Produk
                        </button>
                    </div>
                    <div class="card-body p-2">
                        <div class="alert alert-info mb-2 py-1 px-2">
                            <small><i class="bi bi-info-circle me-1"></i>Anda bisa <strong>scroll</strong> untuk melihat seluruh komponen yang ditambahkan.</small>
                        </div>
                        <div id="packageItems" class="overflow-auto" style="max-height: 400px; scrollbar-width: thin;">
                            <!-- Items will be added dynamically -->
                        </div>
                        
                        @error('items')
                            <div class="alert alert-danger mt-3">{{ $message }}</div>
                        @enderror
                        
                        <!-- Package Items Template -->
                        <template id="packageItemTemplate">
                            <div class="package-item border rounded p-3 mb-3" data-index="">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="mb-0">Produk <span class="item-number"></span></h6>
                                    <button type="button" class="btn btn-danger btn-sm" onclick="removePackageItem(this)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="form-label">Produk Siap Jual <span class="text-danger">*</span></label>
                                        <select class="form-select product-select" name="items[][finished_product_id]" required onchange="updateProductInfo(this)">
                                            <option value="">Pilih Produk</option>
                                            @foreach($finishedProducts as $product)
                                                <option value="{{ $product->id }}" 
                                                        data-price="{{ $product->price }}" 
                                                        data-unit="{{ $product->unit->abbreviation ?? 'pcs' }}">
                                                    {{ $product->name }} - Rp {{ number_format($product->price, 0, ',', '.') }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Jumlah <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <button class="btn btn-outline-secondary" type="button" onclick="decrementItem(this)">-</button>
                                            <input type="number" class="form-control quantity-input" name="items[][quantity]" min="0.01" step="0.01" placeholder="" required onchange="calculateItemTotal(this)">
                                            <button class="btn btn-outline-secondary" type="button" onclick="incrementItem(this)">+</button>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Total Harga</label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="text" class="form-control total-price" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <small class="text-muted product-info">Pilih produk untuk melihat informasi</small>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Summary Section -->
            <div class="col-lg-3">
                <div class="card shadow-sm sticky-top">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0">
                            <i class="bi bi-calculator me-2"></i>Ringkasan Harga
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Harga Dasar:</span>
                            <span class="fw-bold" id="basePriceDisplay">Rp 0</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Diskon:</span>
                            <span class="text-success fw-bold" id="discountDisplay">Rp 0</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Biaya Tambahan:</span>
                            <span class="text-warning fw-bold" id="additionalChargeDisplay">Rp 0</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="h5">Harga Jual Final:</span>
                            <span class="h4 text-primary fw-bold" id="finalPriceDisplay">Rp 0</span>
                        </div>
                        
                        <!-- Finalize Calculation Button -->
                        <div class="d-grid">
                            <button type="button" class="btn btn-warning" onclick="finalizeCalculation()">
                                <i class="bi bi-calculator me-2"></i>Finalisasi Hitungan
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Hidden Fields for Calculated Values -->
                <input type="hidden" name="base_price" id="base_price" value="0">
                <input type="hidden" name="final_price" id="final_price" value="0">
                
                <!-- Form Actions -->
                <div class="card mt-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('sales-packages.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-lg me-1"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary" id="directSubmitBtn">
                                <i class="bi bi-check-lg me-1"></i> Simpan Paket (Direct Submit)
                            </button>
                        </div>
                        @if($errors->any())
                            <div class="alert alert-danger mt-3">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
let packageItemIndex = 0;
let finishedProducts = @json($finishedProducts->keyBy('id'));

document.addEventListener('DOMContentLoaded', function() {
    // Initial setup
    setupItemHandlers();
    setupDiscountHandlers();
    setupCategoryDropdown();
    calculateTotalPrice();
});

// Add the missing setupItemHandlers function
function setupItemHandlers() {
    // Add event handlers for existing package items
    document.querySelectorAll('.package-item').forEach(item => {
        setupItemEventHandlers(item);
    });
    
    // Add at least one item if none exists
    if (document.querySelectorAll('.package-item').length === 0) {
        addPackageItem();
    }
}

function setupItemEventHandlers(item) {
    const productSelect = item.querySelector('.product-select');
    const quantityInput = item.querySelector('.quantity-input');
    
    if (productSelect) {
        productSelect.addEventListener('change', function() {
            updateProductInfo(this);
        });
    }
    
    if (quantityInput) {
        quantityInput.addEventListener('input', function() {
            calculateItemTotal(this);
        });
        quantityInput.addEventListener('change', function() {
            calculateItemTotal(this);
        });
    }
}

function submitFormWithAjax(event) {
    event.preventDefault();
    
    // First validate form
    if (!validateFormBeforeSubmit()) {
        return false;
    }
    
    // Show loading state
    const submitBtn = document.querySelector('#salesPackageForm button[type="submit"]');
    const originalBtnText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...';
    
    // Get form data including files
    const form = document.getElementById('salesPackageForm');
    const formData = new FormData(form);
    
    // Important: Do NOT set Content-Type header when using FormData
    // The browser will automatically set the correct Content-Type with boundary
    // Only add the CSRF token in the X-CSRF-TOKEN header
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    console.log('Submitting form with token:', token);
    console.log('Form action URL:', form.action);
    
    // Use XMLHttpRequest instead of fetch for better file upload support
    const xhr = new XMLHttpRequest();
    xhr.open('POST', form.action, true);
    xhr.setRequestHeader('X-CSRF-TOKEN', token);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            console.log('XHR Response:', xhr.status, xhr.responseText);
            
            if (xhr.status === 200 || xhr.status === 201) {
                // Success - redirect to the package list
                console.log('Success! Redirecting to index page');
                window.location.href = '{{ route("sales-packages.index") }}?created=true';
            } else if (xhr.status === 419) {
                // CSRF token mismatch
                alert('CSRF token mismatch. Akan dicoba lagi dengan token baru.');
                console.error('CSRF token mismatch, refreshing token');
                
                // Try to refresh the page to get a new token
                window.location.reload();
            } else {
                // Other errors
                let errorMessage = 'Gagal menyimpan data. Kode: ' + xhr.status;
                
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.message) {
                        errorMessage = response.message;
                    } else if (response.errors) {
                        errorMessage = Object.values(response.errors).flat().join('\n');
                    }
                } catch (e) {
                    // Not JSON or other parsing error
                }
                
                alert(errorMessage);
                console.error('Form submission error:', errorMessage);
                
                // Reset button state
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            }
        }
    };
    
    // Add progress monitoring
    xhr.upload.onprogress = function(e) {
        if (e.lengthComputable) {
            const percentComplete = (e.loaded / e.total) * 100;
            console.log('Upload progress: ' + percentComplete + '%');
        }
    };
    
    xhr.send(formData);
    console.log('Form submission initiated');
}

function setupCategoryDropdown() {
    const categorySelect = document.getElementById('category_id');
    const categoryNameInput = document.getElementById('category_name');
    
    // Set initial value if a category is selected
    if (categorySelect.value) {
        const selectedOption = categorySelect.options[categorySelect.selectedIndex];
        categoryNameInput.value = selectedOption.dataset.name;
    }
    
    // Update hidden input when selection changes
    categorySelect.addEventListener('change', function() {
        if (this.value) {
            const selectedOption = this.options[this.selectedIndex];
            categoryNameInput.value = selectedOption.dataset.name;
        } else {
            categoryNameInput.value = '';
        }
    });
}

function validateFormBeforeSubmit() {
    // Ensure category_name is set from the selected category
    const categorySelect = document.getElementById('category_id');
    const categoryNameInput = document.getElementById('category_name');
    
    if (categorySelect.value) {
        const selectedOption = categorySelect.options[categorySelect.selectedIndex];
        categoryNameInput.value = selectedOption.dataset.name;
    }
    
    // Make sure we have at least one package item
    const packageItems = document.querySelectorAll('.package-item');
    if (packageItems.length === 0) {
        alert('Silakan tambahkan minimal satu produk ke dalam paket.');
        return false;
    }
    
    // Make sure each product has a valid selection and quantity
    let isValid = true;
    packageItems.forEach((item, index) => {
        const productSelect = item.querySelector('.product-select');
        const quantityInput = item.querySelector('.quantity-input');
        
        if (!productSelect.value) {
            alert(`Silakan pilih produk untuk item #${index + 1}`);
            isValid = false;
        }
        
        const quantity = parseFloat(quantityInput.value);
        if (isNaN(quantity) || quantity <= 0) {
            alert(`Jumlah produk untuk item #${index + 1} harus lebih dari 0`);
            isValid = false;
        }
    });
    
    // Make sure base price and final price are updated before submission
    finalizeCalculation();
    
    // Ensure our CSRF token is current
    const metaToken = document.querySelector('meta[name="csrf-token"]');
    if (metaToken) {
        const tokenInput = document.querySelector('input[name="_token"]');
        if (tokenInput) {
            // Set the token input value to match the meta tag value
            tokenInput.value = metaToken.getAttribute('content');
            console.log('CSRF token refreshed from meta tag:', tokenInput.value);
        }
    }
    
    console.log('Form validation complete, form ready to submit');
    return isValid;
}

function addPackageItem() {
    const template = document.getElementById('packageItemTemplate').content.cloneNode(true);
    const newItem = template.querySelector('.package-item');
    const newIndex = document.querySelectorAll('.package-item').length;

    newItem.dataset.index = newIndex;
    newItem.querySelector('.item-number').textContent = newIndex + 1;

    // Assign unique ID to the quantity input
    const quantityInput = newItem.querySelector('.quantity-input');
    quantityInput.id = `quantity_new_${newIndex}`;

    // Update names for inputs
    newItem.querySelectorAll('select, input').forEach(input => {
        if (input.name) {
            input.name = input.name.replace('items[][', `items[new_${newIndex}][`);
        }
    });

    document.getElementById('packageItems').appendChild(newItem);
    initializeSelect2ForLastItem();
}

// Initialize Select2 for the last added package item
function initializeSelect2ForLastItem() {
    // Find the newly added package item
    const allItems = document.querySelectorAll('.package-item');
    if (allItems.length === 0) return;
    
    const lastItem = allItems[allItems.length - 1];
    
    // Setup event handlers for the new item
    setupItemEventHandlers(lastItem);
    
    // Find the product select in the last item
    const productSelect = lastItem.querySelector('.product-select');
    if (productSelect) {
        // Check if Select2 is available as a jQuery plugin
        if (typeof $ !== 'undefined' && $.fn && $.fn.select2) {
            $(productSelect).select2({
                placeholder: 'Pilih Produk',
                allowClear: true,
                width: '100%'
            });
        }
        
        // Manually trigger an update to ensure item info is displayed
        updateProductInfo(productSelect);
    }
    
    // Initialize the quantity input with default value 1
    const quantityInput = lastItem.querySelector('.quantity-input');
    if (quantityInput && !quantityInput.value) {
        quantityInput.value = 1;
        // Trigger change event to calculate price
        quantityInput.dispatchEvent(new Event('change', { bubbles: true }));
    }
}

function removePackageItem(button) {
    const item = button.closest('.package-item');
    item.remove();
    updatePackageItemNumbers();
    calculateTotalPrice();
}

function updatePackageItemNumbers() {
    const items = document.querySelectorAll('.package-item');
    items.forEach((item, index) => {
        item.querySelector('.item-number').textContent = index + 1;
    });
}

function updateProductInfo(select) {
    const item = select.closest('.package-item');
    const productId = select.value;
    const infoDiv = item.querySelector('.product-info');
    
    if (productId && finishedProducts[productId]) {
        const product = finishedProducts[productId];
        infoDiv.innerHTML = `
            <i class="bi bi-info-circle"></i> 
            Kategori: ${product.category ? product.category.name : '-'} | 
            Satuan: ${product.unit ? product.unit.unit_name : 'pcs'}
        `;
    } else {
        infoDiv.textContent = 'Pilih produk untuk melihat informasi';
    }
    
    calculateItemTotal(select);
}

function calculateItemTotal(input) {
    const item = input.closest('.package-item');
    const productSelect = item.querySelector('.product-select');
    const quantityInput = item.querySelector('.quantity-input');
    const totalPriceInput = item.querySelector('.total-price');
    
    const price = parseFloat(productSelect.selectedOptions[0]?.dataset.price || 0);
    const quantity = parseFloat(quantityInput.value || 0);
    const total = price * quantity;
    
    totalPriceInput.value = total > 0 ? number_format(total, 0, ',', '.') : '';
    
    calculateTotalPrice();
}

function increment(elementId) {
    const input = document.getElementById(elementId);
    if (!input) return;
    const step = parseFloat(input.step) || 1;
    const max = parseFloat(input.max);
    let currentValue = parseFloat(input.value) || 0;
    let newValue = currentValue + step;

    if (!isNaN(max) && newValue > max) {
        newValue = max;
    }

    input.value = newValue.toFixed(step.toString().includes('.') ? step.toString().split('.')[1].length : 0);
    input.dispatchEvent(new Event('change', { bubbles: true }));
}

function decrement(elementId) {
    const input = document.getElementById(elementId);
    if (!input) return;
    const step = parseFloat(input.step) || 1;
    const min = parseFloat(input.min) || 0;
    let currentValue = parseFloat(input.value) || 0;
    let newValue = currentValue - step;

    if (newValue < min) {
        newValue = min;
    }

    input.value = newValue.toFixed(step.toString().includes('.') ? step.toString().split('.')[1].length : 0);
    input.dispatchEvent(new Event('change', { bubbles: true }));
}

function incrementItem(button) {
    const input = button.previousElementSibling;
    if (input) {
        // Always use 1 as increment step, regardless of input's step attribute
        const max = parseFloat(input.max);
        let currentValue = parseFloat(input.value) || 0;
        let newValue = currentValue + 1; // Fixed increment by 1

        if (!isNaN(max) && newValue > max) {
            newValue = max;
        }

        input.value = newValue;
        input.dispatchEvent(new Event('change', { bubbles: true }));
    }
}

function decrementItem(button) {
    const input = button.nextElementSibling;
    if (input) {
        // Always use 1 as decrement step, regardless of input's step attribute
        const min = parseFloat(input.min) || 0;
        let currentValue = parseFloat(input.value) || 0;
        let newValue = currentValue - 1; // Fixed decrement by 1

        if (newValue < min) {
            newValue = min;
        }

        input.value = newValue;
        input.dispatchEvent(new Event('change', { bubbles: true }));
    }
}

function calculateTotalPrice() {
    let basePrice = 0;
    
    // Calculate base price from all items
    document.querySelectorAll('.package-item').forEach(item => {
        const productSelect = item.querySelector('.product-select');
        const quantityInput = item.querySelector('.quantity-input');
        
        if (productSelect && productSelect.selectedOptions && productSelect.selectedOptions[0]) {
            const price = parseFloat(productSelect.selectedOptions[0].dataset.price || 0);
            const quantity = parseFloat(quantityInput?.value || 0);
            
            basePrice += price * quantity;
        }
    });
    
    // Get discount and additional charge values - ensure we get valid numbers
    const discountPercentage = parseFloat(document.getElementById('discount_percentage')?.value) || 0;
    const discountAmount = parseFloat(document.getElementById('discount_amount')?.value) || 0;
    const additionalCharge = parseFloat(document.getElementById('additional_charge')?.value) || 0;
    
    // Calculate discount
    let discount = 0;
    if (discountPercentage > 0) {
        discount = (basePrice * discountPercentage) / 100;
    } else {
        discount = discountAmount;
    }
    
    // Calculate final price
    const finalPrice = basePrice - discount + additionalCharge;
    
    // Update displays - with safe DOM element checking
    const basePriceElement = document.getElementById('basePriceDisplay');
    const discountElement = document.getElementById('discountDisplay');
    const additionalChargeElement = document.getElementById('additionalChargeDisplay');
    const finalPriceElement = document.getElementById('finalPriceDisplay');
    const baseInputElement = document.getElementById('base_price');
    const finalInputElement = document.getElementById('final_price');
    
    if (basePriceElement) basePriceElement.textContent = 'Rp ' + number_format(basePrice, 0, ',', '.');
    if (discountElement) discountElement.textContent = '-Rp ' + number_format(discount, 0, ',', '.');
    if (additionalChargeElement) additionalChargeElement.textContent = 'Rp ' + number_format(additionalCharge, 0, ',', '.');
    if (finalPriceElement) finalPriceElement.textContent = 'Rp ' + number_format(finalPrice, 0, ',', '.');
    
    // Set hidden fields for form submission
    if (baseInputElement) baseInputElement.value = basePrice;
    if (finalInputElement) finalInputElement.value = finalPrice;
    
    console.log('Price calculation completed - Base Price: ' + basePrice);
}

function finalizeCalculation() {
    // Update hidden form fields with calculated values
    const basePriceElement = document.getElementById('basePriceDisplay');
    const finalPriceElement = document.getElementById('finalPriceDisplay');
    const baseInputElement = document.getElementById('base_price');
    const finalInputElement = document.getElementById('final_price');
    
    // Extract numeric values from display text
    const basePrice = parseFloat(basePriceElement.textContent.replace(/[^\d]/g, '')) || 0;
    const finalPrice = parseFloat(finalPriceElement.textContent.replace(/[^\d]/g, '')) || 0;
    
    // Update hidden fields
    if (baseInputElement) baseInputElement.value = basePrice;
    if (finalInputElement) finalInputElement.value = finalPrice;
    
    // Recalculate from all fields
    calculateTotalPrice();
    
    // Show feedback
    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="bi bi-check-lg me-2"></i>Hitungan Difinalisasi!';
    button.classList.remove('btn-warning');
    button.classList.add('btn-success');
    
    // Reset button after 2 seconds
    setTimeout(() => {
        button.innerHTML = originalText;
        button.classList.remove('btn-success');
        button.classList.add('btn-warning');
    }, 2000);
}

function setupDiscountHandlers() {
    const discountPercentage = document.getElementById('discount_percentage');
    const discountAmount = document.getElementById('discount_amount');
    const additionalCharge = document.getElementById('additional_charge');
    
    discountPercentage.addEventListener('input', function() {
        if (this.value) {
            discountAmount.value = '';
        }
        calculateTotalPrice();
    });
    
    discountAmount.addEventListener('input', function() {
        if (this.value) {
            discountPercentage.value = '';
        }
        calculateTotalPrice();
    });
    
    additionalCharge.addEventListener('input', calculateTotalPrice);
}

function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('previewImg').src = e.target.result;
            document.getElementById('imagePreview').style.display = 'block';
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function number_format(number, decimals, dec_point, thousands_sep) {
    number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
    var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function (n, prec) {
            var k = Math.pow(10, prec);
            return '' + Math.round(n * k) / k;
        };
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
}
</script>
@endpush
@endsection
