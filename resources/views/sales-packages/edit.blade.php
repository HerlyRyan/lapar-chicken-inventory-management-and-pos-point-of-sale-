@extends('layouts.app')

@php
use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
<div class="container-fluid">
    <x-page-header 
        title="Edit Paket Penjualan" 
        subtitle="Edit paket {{ $salesPackage->name }}"
        :breadcrumb="[
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => 'Paket Penjualan', 'url' => route('sales-packages.index')],
            ['label' => $salesPackage->name, 'url' => route('sales-packages.show', $salesPackage)],
            ['label' => 'Edit', 'active' => true]
        ]"
    />

    <form action="{{ route('sales-packages.update', $salesPackage) }}" method="POST" enctype="multipart/form-data" id="salesPackageForm" class="compact-form">
        @csrf
        @method('PUT')
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
                                       id="name" name="name" value="{{ old('name', $salesPackage->name) }}" 
                                       placeholder="Contoh: Paket Lapar Aja" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Kode paket: {{ $salesPackage->code }}</div>
                            </div>
                        </div>

                        <!-- Row 2: Description & Category -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="description" class="form-label">Deskripsi</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $salesPackage->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="category_id" class="form-label">Kategori <span class="text-danger">*</span></label>
                                <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                                    <option value="" disabled>Pilih Kategori...</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" data-name="{{ $category->name }}" {{ old('category_id', $salesPackage->category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text mt-1">
                                    <a href="{{ route('categories.create') }}" target="_blank" rel="noopener" class="text-decoration-underline">
                                        <i class="bi bi-box-arrow-up-right me-1"></i>Tambah kategori di tab baru
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="form-text">Kategori akan digunakan untuk filter paket penjualan</div>

                        <!-- Row 3: Pricing -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="discount_percentage" class="form-label">Diskon (%)</label>
                                <div class="input-group">
                                    <button class="btn btn-outline-secondary" type="button" onclick="decrement('discount_percentage')">-</button>
                                    <input type="number" class="form-control @error('discount_percentage') is-invalid @enderror" 
                                           id="discount_percentage" name="discount_percentage" 
                                           value="{{ old('discount_percentage', $salesPackage->discount_percentage) }}" 
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
                                           value="{{ old('discount_amount', $salesPackage->discount_amount) }}" 
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
                                           value="{{ old('additional_charge', $salesPackage->additional_charge) }}" 
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

                        <!-- Row 4: Image Management -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <label class="form-label">Foto Paket</label>
                                <div class="row">
                                    <!-- Current Image -->
                                    <div class="col-md-6">
                                        <div class="card h-100">
                                            <div class="card-header bg-light py-2">
                                                <small class="text-muted fw-bold">Foto Saat Ini</small>
                                            </div>
                                            <div class="card-body d-flex align-items-center justify-content-center" style="min-height: 200px;">
                                                @if($salesPackage->image && Storage::disk('public')->exists($salesPackage->image))
                                                    <img src="{{ asset('storage/' . $salesPackage->image) }}" 
                                                         alt="{{ $salesPackage->name }}" 
                                                         class="img-fluid rounded shadow-sm" 
                                                         style="max-height: 180px; max-width: 100%; object-fit: cover;">
                                                @else
                                                    <div class="text-center text-muted">
                                                        <i class="bi bi-image" style="font-size: 3rem;"></i>
                                                        <div class="mt-2">Belum ada foto</div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Upload New Image -->
                                    <div class="col-md-6">
                                        <div class="card h-100">
                                            <div class="card-header bg-light py-2">
                                                <small class="text-muted fw-bold">Upload Foto Baru (Opsional)</small>
                                            </div>
                                            <div class="card-body">
                                                <input type="file" class="form-control mb-3 @error('image') is-invalid @enderror" 
                                                       id="image" name="image" accept="image/*" onchange="previewImage(this)">
                                                @error('image')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <div class="form-text mb-3">
                                                    <i class="bi bi-info-circle me-1"></i>
                                                    Format: JPG, PNG, GIF. Maksimal 2MB.
                                                </div>
                                                
                                                <!-- Preview Container -->
                                                <div id="imagePreview" style="display: none;">
                                                    <div class="border rounded p-2 bg-light">
                                                        <small class="text-muted fw-bold d-block mb-2">Preview:</small>
                                                        <img id="previewImg" src="" alt="Preview" 
                                                             class="img-fluid rounded shadow-sm" 
                                                             style="max-height: 120px; width: 100%; object-fit: cover;">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
                            <!-- Existing items will be loaded here -->
                            @foreach($salesPackage->packageItems as $index => $item)
                                <div class="package-item border rounded p-3 mb-3" data-index="{{ $index }}">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="mb-0">Produk {{ $index + 1 }}</h6>
                                        <button type="button" class="btn btn-danger btn-sm" onclick="removePackageItem(this)">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class="form-label">Produk Siap Jual <span class="text-danger">*</span></label>
                                            <select class="form-select product-select" name="items[{{ $index }}][finished_product_id]" required onchange="updateProductInfo(this)">
                                                <option value="">Pilih Produk</option>
                                                @foreach($finishedProducts as $product)
                                                    <option value="{{ $product->id }}" 
                                                            data-price="{{ $product->price }}" 
                                                            data-unit="{{ $product->unit->abbreviation ?? 'pcs' }}"
                                                            {{ $item->finished_product_id == $product->id ? 'selected' : '' }}>
                                                        {{ $product->name }} - Rp {{ number_format($product->price, 0, ',', '.') }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="quantity" class="form-label">Jumlah <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <button class="btn btn-outline-secondary" type="button" onclick="decrementItem(this)">-</button>
                                                <input type="number" class="form-control quantity-input" id="quantity_{{ $index }}" name="items[{{ $index }}][quantity]" min="0.01" step="0.01" placeholder="" required onchange="calculateItemTotal(this)" value="{{ $item->quantity }}">
                                                <button class="btn btn-outline-secondary" type="button" onclick="incrementItem(this)">+</button>
                                            </div>
                                            @error('items.*.quantity')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
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
                            @endforeach
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
                                        <label for="quantity" class="form-label">Jumlah <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <button class="btn btn-outline-secondary" type="button" onclick="decrementItem(this)">-</button>
                                            <input type="number" class="form-control quantity-input" name="items[][quantity]" min="0.01" step="0.01" placeholder="" required onchange="calculateItemTotal(this)">
                                            <button class="btn btn-outline-secondary" type="button" onclick="incrementItem(this)">+</button>
                                        </div>
                                        @error('items.*.quantity')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
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
                <input type="hidden" name="base_price" id="base_price" value="{{ $salesPackage->base_price }}">
                <input type="hidden" name="final_price" id="final_price" value="{{ $salesPackage->final_price }}">
                <input type="hidden" name="category_name" id="category_name" value="{{ $salesPackage->category }}">
                
                <!-- Form Actions -->
                <x-form-buttons
                    :cancelUrl="route('sales-packages.show', $salesPackage)"
                    submitText="Update Paket"
                    submitIcon="bi-check-lg"
                />
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
let existingPackageItems = @json($salesPackage->packageItems);
let finishedProducts = @json($finishedProducts->keyBy('id'));

let packageItemIndex = 0;
let isItemsLoaded = false; // Flag to prevent duplicate loading

document.addEventListener('DOMContentLoaded', function() {
    // Prevent multiple initialization
    if (isItemsLoaded) {
        console.log('Items already loaded, skipping...');
        return;
    }
    
    console.log('DOMContentLoaded - initializing form...');
    
    // Load existing items
    loadExistingItems();
    
    // Setup handlers
    setupDiscountHandlers();
    setupCategoryDropdown();
    calculateTotalPrice();
    
    // Setup form submission debugging
    setupFormSubmissionDebugging();
    
    // Mark as loaded
    isItemsLoaded = true;
    console.log('Form initialization completed');
});

function setupCategoryDropdown() {
    // Category dropdown setup without category_name hidden field
    const categorySelect = document.getElementById('category_id');
    
    // Any other category dropdown functionality can go here
    // No need to update hidden field anymore
}

function setupFormSubmissionDebugging() {
    console.log('Setting up form submission debugging...');
    
    const form = document.getElementById('salesPackageForm');
    if (!form) {
        console.error('Form salesPackageForm not found!');
        return;
    }
    
    console.log('Form found:', form);
    
    // Track form submit event
    form.addEventListener('submit', function(e) {
        console.log('=== FORM SUBMISSION TRIGGERED ===');
        console.log('Event:', e);
        console.log('Form action:', form.action);
        console.log('Form method:', form.method);
        
        // Collect all form data
        const formData = new FormData(form);
        console.log('Form data entries:');
        for (let [key, value] of formData.entries()) {
            console.log(`${key}: ${value}`);
        }
        
        // Check package items specifically
        const packageItems = document.querySelectorAll('.package-item');
        console.log('Package items count:', packageItems.length);
        
        packageItems.forEach((item, index) => {
            const productSelect = item.querySelector('.product-select');
            const quantityInput = item.querySelector('.quantity-input');
            console.log(`Item ${index}:`, {
                product: productSelect ? productSelect.value : 'not found',
                quantity: quantityInput ? quantityInput.value : 'not found',
                productName: productSelect ? productSelect.name : 'no name',
                quantityName: quantityInput ? quantityInput.name : 'no name'
            });
        });
        
        console.log('Form submission will proceed...');
        // Don't prevent default - let form submit naturally
    });
    
    // Track submit button clicks
    const submitButtons = form.querySelectorAll('button[type="submit"]');
    submitButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            console.log('Submit button clicked:', button);
            console.log('Button text:', button.textContent.trim());
        });
    });
    
    console.log('Form submission debugging setup completed');
}

function loadExistingItems() {
    console.log('Loading existing items:', existingPackageItems);
    
    // Clear existing items in container first to prevent duplicates
    const container = document.getElementById('packageItems');
    if (container) {
        // Remove all existing package items except template
        const existingItems = container.querySelectorAll('.package-item');
        existingItems.forEach(item => item.remove());
        console.log('Cleared existing package items from container');
    }
    
    if (existingPackageItems && existingPackageItems.length > 0) {
        existingPackageItems.forEach((item, index) => {
            console.log('Loading item:', item);
            addPackageItem(item);
        });
    } else {
        console.log('No existing items found, adding empty item');
        addPackageItem();
    }
    
    // Update calculations after loading
    calculateTotalPrice();
}

function addPackageItem(existingItem = null) {
    console.log('addPackageItem called with:', existingItem);
    
    const template = document.getElementById('packageItemTemplate');
    console.log('Template found:', template);
    
    if (!template) {
        console.error('Template not found!');
        return;
    }
    
    const templateContent = template.content.cloneNode(true);
    const newItem = templateContent.querySelector('.package-item');
    console.log('New item from template:', newItem);
    
    const container = document.getElementById('packageItems');
    console.log('Container found:', container);
    
    if (!container) {
        console.error('Container not found!');
        return;
    }
    
    const newIndex = document.querySelectorAll('.package-item').length;
    console.log('New index:', newIndex);

    newItem.dataset.index = newIndex;
    newItem.querySelector('.item-number').textContent = newIndex + 1;

    // Assign unique ID to the quantity input
    const quantityInput = newItem.querySelector('.quantity-input');
    if (quantityInput) {
        quantityInput.id = `quantity_new_${newIndex}`;
    }

    // Update names for inputs
    newItem.querySelectorAll('select, input').forEach(input => {
        if (input.name) {
            input.name = input.name.replace('items[][', `items[new_${newIndex}][`);
        }
    });
    
    // Append the new item to container
    console.log('Appending item to container...');
    container.appendChild(newItem);
    console.log('Item appended. Container children count:', container.children.length);
    
    // Populate with existing data if provided
    if (existingItem) {
        console.log('Populating with existing data:', existingItem);
        const addedItem = container.lastElementChild;
        const productSelect = addedItem.querySelector('.product-select');
        const quantityInput = addedItem.querySelector('.quantity-input');
        
        console.log('Product select found:', productSelect);
        console.log('Quantity input found:', quantityInput);
        
        if (productSelect && quantityInput) {
            productSelect.value = existingItem.finished_product_id;
            quantityInput.value = existingItem.quantity;
            
            console.log('Values set - product:', existingItem.finished_product_id, 'quantity:', existingItem.quantity);
            updateProductInfo(productSelect);
        }
    }
    
    updatePackageItemNumbers();
    calculateTotalPrice();
    
    console.log('addPackageItem completed');
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
        
        const price = parseFloat(productSelect.selectedOptions[0]?.dataset.price || 0);
        const quantity = parseFloat(quantityInput.value || 0);
        
        basePrice += price * quantity;
    });
    
    // Get discount and additional charge values - ensure we get valid numbers
    const discountPercentage = parseFloat(document.getElementById('discount_percentage').value) || 0;
    const discountAmount = parseFloat(document.getElementById('discount_amount').value) || 0;
    const additionalCharge = parseFloat(document.getElementById('additional_charge').value) || 0;
    
    // Calculate discount
    let discount = 0;
    if (discountPercentage > 0) {
        discount = (basePrice * discountPercentage) / 100;
    } else {
        discount = discountAmount;
    }
    
    // Calculate final price
    const finalPrice = basePrice - discount + additionalCharge;
    
    // Update displays
    const basePriceElement = document.getElementById('basePriceDisplay');
    if (basePriceElement) basePriceElement.textContent = 'Rp ' + number_format(basePrice, 0, ',', '.');
    document.getElementById('discountDisplay').textContent = '-Rp ' + number_format(discount, 0, ',', '.');
    document.getElementById('additionalChargeDisplay').textContent = 'Rp ' + number_format(additionalCharge, 0, ',', '.');
    document.getElementById('finalPriceDisplay').textContent = 'Rp ' + number_format(finalPrice, 0, ',', '.');
    
    // Set hidden fields for form submission
    document.getElementById('base_price').value = basePrice;
    document.getElementById('final_price').value = finalPrice;
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
