@extends('layouts.app')
@php use Illuminate\Support\Facades\Storage; @endphp

@section('title', 'Tambah Paket Penjualan')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-orange-50 via-white to-red-50 py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Header --}}
            <x-form.header title="Paket Penjualan" backRoute="{{ route('sales-packages.index') }}" />

            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden mt-6">
                <x-form.card-header title="Tambah Paket Penjualan" type="add" />

                <div class="p-6 sm:p-8">
                    <form action="{{ route('sales-packages.store') }}" method="POST" enctype="multipart/form-data"
                        id="salesPackageForm" class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 gap-6">
                            <div class="space-y-6">
                                <div class="bg-white rounded-lg p-5">
                                    <div class="flex items-center mb-4">
                                        <div
                                            class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center mr-3">
                                            <i class="bi bi-info-circle text-white text-sm"></i>
                                        </div>
                                        <h3 class="text-lg font-semibold text-gray-900">Informasi Paket</h3>
                                    </div>

                                    {{-- Name --}}
                                    <div class="mb-4">
                                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                                            Nama Paket <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" id="name" name="name" required
                                            class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 @error('name') border-red-300 ring-2 ring-red-200 @enderror"
                                            value="{{ old('name') }}" placeholder="Contoh: Paket Lapar Aja">
                                        <p class="mt-2 text-sm text-gray-600"><i class="bi bi-info-circle mr-1"></i>Kode
                                            paket akan dibuat otomatis</p>
                                        @error('name')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    {{-- Description & Category --}}
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label for="description"
                                                class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi</label>
                                            <textarea id="description" name="description" rows="3"
                                                class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 @error('description') border-red-300 ring-2 ring-red-200 @enderror"
                                                placeholder="Deskripsi paket (opsional)">{{ old('description') }}</textarea>
                                            @error('description')
                                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label for="category_id"
                                                class="block text-sm font-semibold text-gray-700 mb-2">Kategori <span
                                                    class="text-red-500">*</span></label>
                                            <select id="category_id" name="category_id" required
                                                class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 @error('category_id') border-red-300 ring-2 ring-red-200 @enderror">
                                                <option value="" selected disabled>Pilih Kategori</option>
                                                @foreach ($categories as $category)
                                                    <option value="{{ $category->id }}" data-name="{{ $category->name }}"
                                                        {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                        {{ $category->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <input type="hidden" name="category_name" id="category_name"
                                                value="{{ old('category_name') }}">
                                            <p class="mt-2 text-sm text-gray-600"><i
                                                    class="bi bi-info-circle mr-1"></i>Kategori akan digunakan untuk filter
                                                paket penjualan</p>
                                            <p class="mt-1 text-sm"><a href="{{ route('categories.create') }}"
                                                    target="_blank" rel="noopener" class="text-orange-600 underline"><i
                                                        class="bi bi-box-arrow-up-right mr-1"></i>Tambah kategori di tab
                                                    baru</a></p>
                                            @error('category_id')
                                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Pricing --}}
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                                        <div>
                                            <label for="discount_percentage"
                                                class="block text-sm font-semibold text-gray-700 mb-2">Diskon (%)</label>
                                            <div class="flex rounded-xl border overflow-hidden">
                                                <button type="button" class="px-3 bg-gray-50 text-gray-700"
                                                    onclick="decrement('discount_percentage')">-</button>
                                                <input type="number" id="discount_percentage" name="discount_percentage"
                                                    min="0" max="100" step="0.01"
                                                    class="flex-1 px-3 py-2 focus:outline-none" placeholder="0"
                                                    value="{{ old('discount_percentage') }}">
                                                <button type="button" class="px-3 bg-gray-50 text-gray-700"
                                                    onclick="increment('discount_percentage')">+</button>
                                            </div>
                                            @error('discount_percentage')
                                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label for="discount_amount"
                                                class="block text-sm font-semibold text-gray-700 mb-2">Diskon (Rp)</label>
                                            <div class="flex rounded-xl border overflow-hidden">
                                                <button type="button" class="px-3 bg-gray-50 text-gray-700"
                                                    onclick="decrement('discount_amount')">-</button>
                                                <input type="number" id="discount_amount" name="discount_amount"
                                                    min="0" step="1"
                                                    class="flex-1 px-3 py-2 focus:outline-none" placeholder="0"
                                                    value="{{ old('discount_amount') }}">
                                                <button type="button" class="px-3 bg-gray-50 text-gray-700"
                                                    onclick="increment('discount_amount')">+</button>
                                            </div>
                                            @error('discount_amount')
                                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label for="additional_charge"
                                                class="block text-sm font-semibold text-gray-700 mb-2">Biaya Tambahan
                                                (Rp)</label>
                                            <div class="flex rounded-xl border overflow-hidden">
                                                <button type="button" class="px-3 bg-gray-50 text-gray-700"
                                                    onclick="decrement('additional_charge')">-</button>
                                                <input type="number" id="additional_charge" name="additional_charge"
                                                    min="0" step="1"
                                                    class="flex-1 px-3 py-2 focus:outline-none" placeholder="0"
                                                    value="{{ old('additional_charge') }}">
                                                <button type="button" class="px-3 bg-gray-50 text-gray-700"
                                                    onclick="increment('additional_charge')">+</button>
                                            </div>
                                            @error('additional_charge')
                                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="mt-3">
                                        <div class="bg-blue-50 rounded-md p-3 text-sm text-blue-700">
                                            <i class="bi bi-info-circle mr-1"></i>
                                            <strong>Catatan:</strong> Pilih diskon % ATAU nominal. Biaya tambahan untuk
                                            packaging, delivery, dll.
                                        </div>
                                    </div>

                                    {{-- Image --}}
                                    <div class="mt-4">
                                        <label for="image" class="block text-sm font-semibold text-gray-700 mb-2">Foto
                                            Paket</label>
                                        <input type="file" id="image" name="image" accept="image/*"
                                            onchange="previewImage(this)"
                                            class="w-full text-sm file:border-0 file:bg-gray-100 file:px-3 file:py-2 rounded-xl @error('image') border-red-300 ring-2 ring-red-200 @enderror">
                                        <p class="mt-2 text-sm text-gray-600">Format: JPG, PNG, GIF. Maksimal 2MB.</p>
                                        @error('image')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror

                                        <div id="imagePreview" class="mt-3 hidden">
                                            <img id="previewImg" src="" alt="Preview"
                                                class="rounded-lg border max-h-48 object-contain">
                                        </div>
                                    </div>
                                </div>

                                {{-- Package Items --}}
                                <div class="bg-white rounded-lg p-5">
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="flex items-center">
                                            <div
                                                class="w-8 h-8 bg-gradient-to-br from-green-500 to-green-600 rounded-lg flex items-center justify-center mr-3">
                                                <i class="bi bi-box-seam text-white text-sm"></i>
                                            </div>
                                            <h3 class="text-lg font-semibold text-gray-900">Komponen Paket</h3>
                                        </div>
                                        <button type="button"
                                            class="inline-flex items-center px-3 py-2 bg-white border rounded-lg text-sm shadow-sm hover:shadow-md"
                                            onclick="addPackageItem()">
                                            <i class="bi bi-plus-lg mr-2"></i> Tambah Produk
                                        </button>
                                    </div>

                                    <div class="mb-3 text-sm text-gray-600">
                                        <i class="bi bi-info-circle mr-1"></i> Anda bisa <strong>scroll</strong> untuk
                                        melihat seluruh komponen yang ditambahkan.
                                    </div>

                                    <div id="packageItems" class="space-y-4 max-h-96 overflow-auto pr-2">
                                        {{-- Items added dynamically --}}
                                    </div>

                                    @error('items')
                                        <div class="mt-3 text-sm text-red-600">{{ $message }}</div>
                                    @enderror

                                    {{-- Template --}}
                                    <template id="packageItemTemplate">
                                        <div class="package-item border rounded-xl p-4 min-w-0" data-index="">
                                            <div class="flex items-center justify-between mb-3">
                                                <h6 class="text-sm font-semibold">Produk <span class="item-number"></span>
                                                </h6>
                                                <button type="button" class="text-red-600 hover:text-red-800"
                                                    onclick="removePackageItem(this)">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>

                                            <div class="grid grid-cols-1 md:grid-cols-6 gap-3 min-w-0">
                                                <div class="md:col-span-3 min-w-0">
                                                    <label class="block text-sm font-medium text-gray-700 mb-2">Produk Siap
                                                        Jual <span class="text-red-500">*</span></label>
                                                    <select
                                                        class="product-select w-full px-3 py-2 border rounded-xl min-w-0"
                                                        name="items[][finished_product_id]" required
                                                        onchange="updateProductInfo(this)">
                                                        <option value="">Pilih Produk</option>
                                                        @foreach ($finishedProducts as $product)
                                                            <option value="{{ $product->id }}"
                                                                data-price="{{ $product->price }}"
                                                                data-unit="{{ $product->unit->abbreviation ?? 'pcs' }}">
                                                                {{ $product->name }} - Rp
                                                                {{ number_format($product->price, 0, ',', '.') }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="md:col-span-2 min-w-0">
                                                    <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah
                                                        <span class="text-red-500">*</span></label>
                                                    <div class="flex rounded-xl border overflow-hidden items-center">
                                                        <button type="button" class="px-3 bg-gray-50 text-gray-700"
                                                            onclick="decrementItem(this)">-</button>
                                                        <input type="number"
                                                            class="quantity-input flex-1 px-3 py-2 text-center focus:outline-none min-w-0"
                                                            name="items[][quantity]" min="0.01" step="0.01" value="1"
                                                            required onchange="calculateItemTotal(this)">
                                                        <button type="button" class="px-3 bg-gray-50 text-gray-700"
                                                            onclick="incrementItem(this)">+</button>
                                                    </div>
                                                </div>

                                                <div class="md:col-span-1 min-w-0">
                                                    <label
                                                        class="block text-sm font-medium text-gray-700 mb-2">Total</label>
                                                    <div class="flex items-center px-3 py-2 border rounded-xl bg-gray-50 min-w-0">
                                                        <span class="text-sm text-gray-700 mr-2 flex-shrink-0">Rp</span>
                                                        <input type="text"
                                                            class="total-price flex-1 bg-transparent text-right text-sm truncate min-w-0"
                                                            readonly value="">
                                                    </div>
                                                </div>

                                                <div class="md:col-span-6 mt-2">
                                                    <small class="text-gray-500 product-info block">Pilih produk untuk melihat
                                                        informasi</small>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            {{-- Summary & Actions --}}
                            <div class="space-y-6">
                                <div class="bg-white rounded-lg p-5 shadow-sm">
                                    <div class="flex items-center mb-3">
                                        <div
                                            class="w-8 h-8 bg-gradient-to-br from-yellow-400 to-yellow-500 rounded-lg flex items-center justify-center mr-3">
                                            <i class="bi bi-calculator text-white text-sm"></i>
                                        </div>
                                        <h3 class="text-lg font-semibold text-gray-900">Ringkasan Harga</h3>
                                    </div>

                                    <div class="text-sm text-gray-700 space-y-2">
                                        <div class="flex justify-between">
                                            <span>Harga Dasar:</span>
                                            <span class="font-semibold" id="basePriceDisplay">Rp 0</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span>Diskon:</span>
                                            <span class="text-green-600 font-semibold" id="discountDisplay">Rp 0</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span>Biaya Tambahan:</span>
                                            <span class="text-yellow-600 font-semibold" id="additionalChargeDisplay">Rp
                                                0</span>
                                        </div>
                                        <hr>
                                        <div class="flex justify-between items-center">
                                            <span class="text-lg font-semibold">Harga Jual Final:</span>
                                            <span class="text-2xl text-blue-600 font-bold" id="finalPriceDisplay">Rp
                                                0</span>
                                        </div>
                                    </div>

                                    <div class="mt-4">
                                        <button type="button"
                                            class="w-full inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-yellow-400 to-yellow-500 rounded-xl text-sm font-medium text-white shadow"
                                            onclick="finalizeCalculation(event)">
                                            <i class="bi bi-calculator mr-2"></i> Finalisasi Hitungan
                                        </button>
                                    </div>
                                </div>

                                {{-- Hidden fields --}}
                                <input type="hidden" name="base_price" id="base_price" value="0">
                                <input type="hidden" name="final_price" id="final_price" value="0">

                                {{-- Actions --}}
                                <div class="bg-white rounded-lg p-4">
                                    <div class="flex gap-3">
                                        <a href="{{ route('sales-packages.index') }}"
                                            class="flex-1 inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-xl text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 shadow-sm">
                                            Batal
                                        </a>
                                        <button type="submit" id="directSubmitBtn"
                                            class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-orange-600 to-red-600 rounded-xl text-sm font-medium text-white shadow-lg">
                                            <i class="bi bi-check-lg mr-2"></i> Simpan Paket
                                        </button>
                                    </div>

                                    @if ($errors->any())
                                        <div class="mt-4 text-sm text-red-600">
                                            <ul class="list-disc pl-5">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

<script>
    let finishedProducts = @json($finishedProducts->keyBy('id'));

    document.addEventListener('DOMContentLoaded', function() {
        setupCategoryDropdown();
        setupDiscountHandlers();
        setupInitialItems();
        calculateTotalPrice();
    });

    // Category hidden name sync
    function setupCategoryDropdown() {
        const categorySelect = document.getElementById('category_id');
        const categoryNameInput = document.getElementById('category_name');
        if (!categorySelect) return;

        if (categorySelect.value) {
            const selectedOption = categorySelect.options[categorySelect.selectedIndex];
            categoryNameInput.value = selectedOption.dataset.name || '';
        }

        categorySelect.addEventListener('change', function() {
            if (this.value) {
                const selectedOption = this.options[this.selectedIndex];
                categoryNameInput.value = selectedOption.dataset.name || '';
            } else {
                categoryNameInput.value = '';
            }
        });
    }

    // Discount handlers
    function setupDiscountHandlers() {
        const dp = document.getElementById('discount_percentage');
        const da = document.getElementById('discount_amount');
        const ac = document.getElementById('additional_charge');

        if (dp) dp.addEventListener('input', function() {
            if (this.value) da.value = '';
            calculateTotalPrice();
        });
        if (da) da.addEventListener('input', function() {
            if (this.value) dp.value = '';
            calculateTotalPrice();
        });
        if (ac) ac.addEventListener('input', calculateTotalPrice);
    }

    // Package items
    function setupInitialItems() {
        if (document.querySelectorAll('.package-item').length === 0) {
            addPackageItem();
        } else {
            document.querySelectorAll('.package-item').forEach(item => setupItemEventHandlers(item));
        }
    }

    function addPackageItem() {
        const template = document.getElementById('packageItemTemplate').content.cloneNode(true);
        const newItem = template.querySelector('.package-item');
        const newIndex = document.querySelectorAll('.package-item').length;

        newItem.dataset.index = newIndex;
        newItem.querySelector('.item-number').textContent = newIndex + 1;

        // assign unique ids and names
        const quantityInput = newItem.querySelector('.quantity-input');
        if (quantityInput) {
            quantityInput.id = `quantity_new_${newIndex}`;
            quantityInput.value = 1;
        }

        newItem.querySelectorAll('select, input').forEach(input => {
            if (input.name) {
                input.name = input.name.replace('items[][', `items[new_${newIndex}][`);
            }
        });

        document.getElementById('packageItems').appendChild(newItem);
        const last = document.querySelectorAll('.package-item');
        const added = last[last.length - 1];
        setupItemEventHandlers(added);

        // Trigger initial calculation
        const q = added.querySelector('.quantity-input');
        if (q) q.dispatchEvent(new Event('change', {
            bubbles: true
        }));
    }

    function setupItemEventHandlers(item) {
        const productSelect = item.querySelector('.product-select');
        const quantityInput = item.querySelector('.quantity-input');

        if (productSelect) productSelect.addEventListener('change', function() {
            updateProductInfo(this);
        });
        if (quantityInput) {
            quantityInput.addEventListener('input', function() {
                calculateItemTotal(this);
            });
            quantityInput.addEventListener('change', function() {
                calculateItemTotal(this);
            });
        }
    }

    function removePackageItem(button) {
        const item = button.closest('.package-item');
        if (!item) return;
        item.remove();
        updatePackageItemNumbers();
        calculateTotalPrice();
    }

    function updatePackageItemNumbers() {
        document.querySelectorAll('.package-item').forEach((item, idx) => {
            const num = item.querySelector('.item-number');
            if (num) num.textContent = idx + 1;
        });
    }

    function updateProductInfo(select) {
        const item = select.closest('.package-item');
        const productId = select.value;
        const infoDiv = item.querySelector('.product-info');

        if (productId && finishedProducts[productId]) {
            const product = finishedProducts[productId];
            infoDiv.innerHTML =
                `<i class="bi bi-info-circle"></i> Kategori: ${product.category ? product.category.name : '-'} | Satuan: ${product.unit ? product.unit.unit_name : 'pcs'}`;
        } else {
            infoDiv.textContent = 'Pilih produk untuk melihat informasi';
        }

        // recalc
        calculateItemTotal(select);
    }

    function calculateItemTotal(el) {
        const item = el.closest('.package-item');
        const productSelect = item.querySelector('.product-select');
        const quantityInput = item.querySelector('.quantity-input');
        const totalPriceInput = item.querySelector('.total-price');

        const price = parseFloat(productSelect.selectedOptions[0]?.dataset.price || 0);
        const quantity = parseFloat(quantityInput.value || 0);
        const total = price * quantity;

        if (totalPriceInput) totalPriceInput.value = total > 0 ? number_format(total, 0, ',', '.') : '';
        calculateTotalPrice();
    }

    // Increment/Decrement generic
    function increment(elementId) {
        const input = document.getElementById(elementId);
        if (!input) return;
        const step = parseFloat(input.step) || 1;
        const max = parseFloat(input.max);
        let current = parseFloat(input.value) || 0;
        let next = current + step;
        if (!isNaN(max) && next > max) next = max;
        input.value = next;
        input.dispatchEvent(new Event('change', {
            bubbles: true
        }));
    }

    function decrement(elementId) {
        const input = document.getElementById(elementId);
        if (!input) return;
        const step = parseFloat(input.step) || 1;
        const min = parseFloat(input.min) || 0;
        let current = parseFloat(input.value) || 0;
        let next = current - step;
        if (next < min) next = min;
        input.value = next;
        input.dispatchEvent(new Event('change', {
            bubbles: true
        }));
    }

    // Item increment/decrement (buttons sit beside input)
    function incrementItem(button) {
        const input = button.previousElementSibling;
        if (!input) return;
        let current = parseFloat(input.value) || 0;
        input.value = current + 1;
        input.dispatchEvent(new Event('change', {
            bubbles: true
        }));
    }

    function decrementItem(button) {
        const input = button.nextElementSibling;
        if (!input) return;
        const min = parseFloat(input.min) || 0;
        let current = parseFloat(input.value) || 0;
        let next = current - 1;
        if (next < min) next = min;
        input.value = next;
        input.dispatchEvent(new Event('change', {
            bubbles: true
        }));
    }

    function calculateTotalPrice() {
        let basePrice = 0;
        document.querySelectorAll('.package-item').forEach(item => {
            const productSelect = item.querySelector('.product-select');
            const quantityInput = item.querySelector('.quantity-input');
            if (productSelect && productSelect.selectedOptions && productSelect.selectedOptions[0]) {
                const price = parseFloat(productSelect.selectedOptions[0].dataset.price || 0);
                const qty = parseFloat(quantityInput?.value || 0);
                basePrice += price * qty;
            }
        });

        const discountPercentage = parseFloat(document.getElementById('discount_percentage')?.value) || 0;
        const discountAmount = parseFloat(document.getElementById('discount_amount')?.value) || 0;
        const additionalCharge = parseFloat(document.getElementById('additional_charge')?.value) || 0;

        let discount = 0;
        if (discountPercentage > 0) discount = (basePrice * discountPercentage) / 100;
        else discount = discountAmount;

        const finalPrice = basePrice - discount + additionalCharge;

        const basePriceElement = document.getElementById('basePriceDisplay');
        const discountElement = document.getElementById('discountDisplay');
        const additionalChargeElement = document.getElementById('additionalChargeDisplay');
        const finalPriceElement = document.getElementById('finalPriceDisplay');
        const baseInputElement = document.getElementById('base_price');
        const finalInputElement = document.getElementById('final_price');

        if (basePriceElement) basePriceElement.textContent = 'Rp ' + number_format(basePrice, 0, ',', '.');
        if (discountElement) discountElement.textContent = '-Rp ' + number_format(discount, 0, ',', '.');
        if (additionalChargeElement) additionalChargeElement.textContent = 'Rp ' + number_format(additionalCharge, 0,
            ',', '.');
        if (finalPriceElement) finalPriceElement.textContent = 'Rp ' + number_format(finalPrice, 0, ',', '.');

        if (baseInputElement) baseInputElement.value = basePrice;
        if (finalInputElement) finalInputElement.value = finalPrice;
    }

    function finalizeCalculation(e) {
        // allow event to be optional
        const basePriceElement = document.getElementById('basePriceDisplay');
        const finalPriceElement = document.getElementById('finalPriceDisplay');
        const baseInputElement = document.getElementById('base_price');
        const finalInputElement = document.getElementById('final_price');

        const basePrice = parseFloat((basePriceElement?.textContent || '').replace(/[^\d]/g, '')) || 0;
        const finalPrice = parseFloat((finalPriceElement?.textContent || '').replace(/[^\d]/g, '')) || 0;

        if (baseInputElement) baseInputElement.value = basePrice;
        if (finalInputElement) finalInputElement.value = finalPrice;

        calculateTotalPrice();

        // visual feedback on the button
        const button = (e && e.currentTarget) ? e.currentTarget : null;
        if (!button) return;
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="bi bi-check-lg mr-2"></i> Hitungan Difinalisasi!';
        button.classList.remove('from-yellow-400', 'to-yellow-500');
        button.classList.add('bg-green-500', 'text-white');

        setTimeout(() => {
            button.innerHTML = originalText;
            button.classList.remove('bg-green-500', 'text-white');
        }, 2000);
    }

    // Image preview
    function previewImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('previewImg');
                const container = document.getElementById('imagePreview');
                if (preview) preview.src = e.target.result;
                if (container) container.classList.remove('hidden');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Helper: number format
    function number_format(number, decimals, dec_point, thousands_sep) {
        number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
        var n = !isFinite(+number) ? 0 : +number,
            prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
            sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
            dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
            s = '',
            toFixedFix = function(n, prec) {
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
