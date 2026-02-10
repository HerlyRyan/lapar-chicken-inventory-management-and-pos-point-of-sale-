@extends('layouts.app')
@php use Illuminate\Support\Facades\Storage; @endphp

@section('title', 'Tambah Produk Siap Jual')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-orange-50 via-white to-red-50 py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Header --}}
            <x-form.header title="Produk Siap Jual"
                backRoute="{{ route('finished-products.index', request()->has('branch_id') ? ['branch_id' => request('branch_id')] : []) }}" />

            {{-- Card --}}
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden mt-6">
                <x-form.card-header title="Tambah Produk Siap Jual" type="add" />

                <div class="p-6 sm:p-8">
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

                    @if ($isProductionCenter)
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6 flex gap-4 items-start">
                            <div class="text-red-600 text-2xl">
                                <i class="bi bi-exclamation-triangle-fill"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-red-700 mb-1">Akses Ditolak - Pusat Produksi</h4>
                                <p class="text-sm text-red-600 mb-2">
                                    <strong>Produk siap jual tidak dapat dibuat di Pusat Produksi.</strong>
                                    Pusat Produksi hanya bertugas mengolah bahan mentah menjadi bahan setengah jadi.
                                </p>
                                <p class="text-sm text-gray-600">
                                    <i class="bi bi-info-circle mr-1"></i>
                                    Untuk membuat produk siap jual, silakan akses dari cabang toko atau pilih cabang toko di
                                    header.
                                </p>
                            </div>
                        </div>

                        <div class="text-center py-12">
                            <div class="text-gray-300 text-6xl mb-4"><i class="bi bi-building-x"></i></div>
                            <h3 class="text-gray-700 text-lg font-semibold mb-2">Form Tidak Tersedia</h3>
                            <p class="text-gray-500 mb-4">Silakan pilih cabang toko untuk membuat produk siap jual</p>
                            <a href="{{ route('finished-products.index') }}"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-lg shadow-sm hover:shadow-md">
                                <i class="bi bi-arrow-left"></i> Kembali ke Daftar Produk
                            </a>
                        </div>
                    @else
                        <form
                            action="{{ route('finished-products.store', request()->has('branch_id') ? ['branch_id' => request('branch_id')] : []) }}"
                            method="POST" enctype="multipart/form-data" id="finishedProductForm"
                            onsubmit="console.log('Form submitted!');">
                            @csrf
                            @if (request()->has('branch_id'))
                                <input type="hidden" name="header_branch_id" value="{{ request('branch_id') }}">
                                <input type="hidden" name="branch_id" value="{{ request('branch_id') }}">
                            @endif

                            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                                <div class="lg:col-span-8 space-y-4">
                                    <div>
                                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Nama
                                            Produk Siap Jual <span class="text-red-500">*</span></label>
                                        <input type="text" name="name" id="name" value="{{ old('name') }}"
                                            required
                                            class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-150 @error('name') border-red-300 ring-2 ring-red-200 @enderror"
                                            placeholder="Masukkan nama produk siap jual">
                                        @error('name')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="lg:col-span-4 space-y-4">
                                    <div>
                                        <label for="code" class="block text-sm font-semibold text-gray-700 mb-2">Kode
                                            Produk</label>
                                        <input type="text" name="code" id="code" value="{{ old('code') }}"
                                            class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-150 @error('code') border-red-300 ring-2 ring-red-200 @enderror"
                                            placeholder="Kosongkan untuk generate otomatis">
                                        <p class="mt-2 text-sm text-gray-600"><i class="bi bi-info-circle mr-1"></i>Kode
                                            akan dibuat otomatis jika tidak diisi (Format: FP-XXX-001)</p>
                                        @error('code')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Category --}}
                                <div class="lg:col-span-6">
                                    <label for="category_id" class="block text-sm font-semibold text-gray-700 mb-2">Kategori
                                        <span class="text-red-500">*</span></label>
                                    <select name="category_id" id="category_id" required
                                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-150 @error('category_id') border-red-300 ring-2 ring-red-200 @enderror">
                                        <option value="">- Pilih Kategori -</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}"
                                                {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="mt-2 text-sm text-gray-600">
                                        <a href="{{ route('categories.create') }}" target="_blank" rel="noopener"
                                            class="text-orange-600 underline inline-flex items-center gap-1">
                                            <i class="bi bi-box-arrow-up-right"></i> Tambah kategori di tab baru
                                        </a>
                                    </div>
                                    @error('category_id')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Unit --}}
                                <div class="lg:col-span-6">
                                    <label for="unit_id" class="block text-sm font-semibold text-gray-700 mb-2">Satuan
                                        <span class="text-red-500">*</span></label>
                                    <select name="unit_id" id="unit_id" required
                                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-150 @error('unit_id') border-red-300 ring-2 ring-red-200 @enderror">
                                        <option value="">- Pilih Satuan -</option>
                                        @foreach ($units as $unit)
                                            <option value="{{ $unit->id }}"
                                                {{ old('unit_id') == $unit->id ? 'selected' : '' }}>
                                                {{ $unit->unit_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="mt-2 text-sm text-gray-600">
                                        <a href="{{ route('units.create') }}" target="_blank" rel="noopener"
                                            class="text-orange-600 underline inline-flex items-center gap-1">
                                            <i class="bi bi-box-arrow-up-right"></i> Tambah satuan di tab baru
                                        </a>
                                    </div>
                                    @error('unit_id')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Price & Minimum --}}
                                <div class="lg:col-span-6">
                                    <label for="minimum_stock" class="block text-sm font-semibold text-gray-700 mb-2">Stok
                                        Minimum</label>
                                    <input type="number" name="minimum_stock" id="minimum_stock"
                                        value="{{ old('minimum_stock', 0) }}" step="0.01" min="0"
                                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-150 @error('minimum_stock') border-red-300 ring-2 ring-red-200 @enderror"
                                        placeholder="0.00">
                                    @error('minimum_stock')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="lg:col-span-6">
                                    <label for="price" class="block text-sm font-semibold text-gray-700 mb-2">Harga
                                        Jual <span class="text-red-500">*</span></label>
                                    <input type="number" step="0.01" name="price" id="price"
                                        value="{{ old('price', 0) }}" required
                                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-150 @error('price') border-red-300 ring-2 ring-red-200 @enderror"
                                        placeholder="0.00">
                                    @error('price')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Base cost --}}
                                <div class="lg:col-span-6">
                                    <label for="base_cost" class="block text-sm font-semibold text-gray-700 mb-2">Modal
                                        Dasar</label>
                                    <input type="number" step="0.01" name="base_cost" id="base_cost"
                                        value="{{ old('base_cost', old('production_cost', 0)) }}" placeholder="0.00"
                                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-150 @error('base_cost') border-red-300 ring-2 ring-red-200 @enderror">
                                    <p class="mt-2 text-sm text-gray-600"><i class="bi bi-info-circle mr-1"></i>Modal
                                        dasar produk untuk perhitungan kerugian pemusnahan (tidak boleh melebihi harga jual)
                                    </p>
                                    @error('base_cost')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Stock quantity --}}
                                <div class="lg:col-span-6">
                                    <label for="stock_quantity"
                                        class="block text-sm font-semibold text-gray-700 mb-2">Stok Awal</label>
                                    <input type="number" name="stock_quantity" id="stock_quantity"
                                        value="{{ old('stock_quantity', 0) }}" step="0.01" min="0"
                                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-150 @error('stock_quantity') border-red-300 ring-2 ring-red-200 @enderror"
                                        placeholder="0.00">
                                    @error('stock_quantity')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Stock Initialization Mode --}}
                                <div class="lg:col-span-12 mt-2">
                                    <label class="block text-sm font-semibold text-gray-700 mb-3">Mode Inisialisasi Stok
                                        <span class="text-red-500">*</span></label>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <label
                                            class="flex items-start gap-3 p-4 rounded-xl border cursor-pointer bg-white">
                                            <input class="mt-1" type="radio" name="stock_mode" id="stock_mode_all"
                                                value="all"
                                                {{ old('stock_mode', !$selectedBranch ? 'all' : 'selected') == 'all' ? 'checked' : '' }}>
                                            <div>
                                                <div class="font-semibold text-gray-800 flex items-center gap-2"><i
                                                        class="bi bi-buildings text-green-500"></i> Semua Cabang Retail
                                                </div>
                                                <div class="text-sm text-gray-500 mt-1">Stok akan diinisialisasi dengan
                                                    jumlah yang sama di semua cabang retail. Pengecualian: <strong>Pusat
                                                        Produksi</strong> tidak akan mendapatkan inisialisasi stok.</div>
                                            </div>
                                        </label>

                                        <label
                                            class="flex items-start gap-3 p-4 rounded-xl border cursor-pointer bg-white">
                                            <input class="mt-1" type="radio" name="stock_mode"
                                                id="stock_mode_selected" value="selected"
                                                {{ old('stock_mode', $selectedBranch ? 'selected' : 'all') == 'selected' ? 'checked' : '' }}>
                                            <div>
                                                <div class="font-semibold text-gray-800 flex items-center gap-2"><i
                                                        class="bi bi-building text-indigo-500"></i> Cabang Tertentu</div>
                                                <div class="text-sm text-gray-500 mt-1">Stok hanya diinisialisasi untuk
                                                    cabang yang dipilih.</div>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                {{-- Branch Selection --}}
                                <div class="lg:col-span-12" id="branch-selection"
                                    style="display: {{ old('stock_mode', $selectedBranch ? 'selected' : 'all') == 'selected' ? 'block' : 'none' }};">
                                    <label for="selected_branch_id"
                                        class="block text-sm font-semibold text-gray-700 mb-2">Pilih Cabang <span
                                            class="text-red-500">*</span></label>
                                    <select name="selected_branch_id" id="selected_branch_id"
                                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-150 @error('selected_branch_id') border-red-300 ring-2 ring-red-200 @enderror">
                                        <option value="">-- Pilih Cabang --</option>
                                        @foreach ($retailBranches as $branch)
                                            <option value="{{ $branch->id }}"
                                                {{ old('selected_branch_id', $selectedBranch ? $selectedBranch->id : '') == $branch->id ? 'selected' : '' }}>
                                                {{ $branch->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('selected_branch_id')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Description --}}
                                <div class="lg:col-span-12">
                                    <label for="description"
                                        class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi</label>
                                    <textarea name="description" id="description" rows="3" placeholder="Deskripsi produk siap jual (opsional)"
                                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-150 @error('description') border-red-300 ring-2 ring-red-200 @enderror">{{ old('description') }}</textarea>
                                    @error('description')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Photo & Preview --}}
                                <div class="lg:col-span-6">
                                    <label for="photo" class="block text-sm font-semibold text-gray-700 mb-2">Foto
                                        Produk</label>
                                    <input type="file" name="photo" id="photo"
                                        accept="image/jpeg,image/png,image/jpg,image/gif" onchange="previewImage(this)"
                                        class="w-full text-sm text-gray-700 file:bg-white file:border file:px-3 file:py-2 file:rounded-lg file:border-gray-300 @error('photo') ring-2 ring-red-200 @enderror">
                                    <p class="mt-2 text-sm text-gray-600"><i class="bi bi-info-circle mr-1"></i>Upload
                                        foto produk (opsional). Format: JPG, PNG, GIF. Max: 2MB</p>
                                    @error('photo')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="lg:col-span-6">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Preview Foto</label>
                                    <div id="image-preview-container"
                                        class="h-32 rounded-lg border-2 border-dashed border-gray-200 bg-gray-50 flex items-center justify-center">
                                        <div id="image-preview-text" class="text-center text-gray-400">
                                            <i class="bi bi-image text-2xl mb-2"></i>
                                            <div class="text-sm">Preview foto akan tampil di sini</div>
                                        </div>
                                        <img id="imagePreview" src="" alt="Preview"
                                            class="hidden object-contain h-28 rounded-lg">
                                    </div>
                                </div>

                                {{-- Active --}}
                                <div class="lg:col-span-12">
                                    <label class="flex items-center gap-3 bg-gray-50 p-4 rounded-xl">
                                        <input type="checkbox" name="is_active" id="is_active" value="1"
                                            class="w-5 h-5 text-orange-600 border-gray-300 rounded"
                                            {{ old('is_active', 1) ? 'checked' : '' }}>
                                        <div>
                                            <div class="font-semibold text-gray-900">Status Aktif</div>
                                            <div class="text-xs text-gray-600 mt-1">Centang jika produk siap jual aktif
                                                digunakan</div>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <div class="border-t border-gray-200 pt-6 mt-6 flex gap-3 justify-end">
                                <a href="{{ route('finished-products.index', request()->has('branch_id') ? ['branch_id' => request('branch_id')] : []) }}"
                                    class="inline-flex items-center gap-2 px-4 py-2 border rounded-xl bg-white hover:bg-gray-50">
                                    <i class="bi bi-x-circle"></i> Batal
                                </a>
                                <button type="submit" id="submitBtn"
                                    class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-orange-600 to-red-600 text-white rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                    <i class="bi bi-check-circle"></i> Simpan
                                </button>
                            </div>
                        </form>
                    @endif
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
                previewImg.classList.remove('hidden');
                previewText.classList.add('hidden');
            };
            reader.readAsDataURL(file);
        } else {
            previewImg.classList.add('hidden');
            previewText.classList.remove('hidden');
            previewImg.src = '';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // stock mode toggles
        const stockModeRadios = document.querySelectorAll('input[name="stock_mode"]');
        const branchSelection = document.getElementById('branch-selection');
        const selectedBranchSelect = document.getElementById('selected_branch_id');

        stockModeRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === 'selected') {
                    branchSelection.style.display = 'block';
                } else {
                    branchSelection.style.display = 'none';
                    if (selectedBranchSelect) selectedBranchSelect.value = '';
                }
            });
        });

        // branch specific handling: disable stock inputs if production selected
        @if (isset($branches))
            const branches = @json($branches);
            const selectedBranchEl = document.getElementById('selected_branch_id');
            if (selectedBranchEl) {
                selectedBranchEl.addEventListener('change', function() {
                    const selected = branches.find(b => b.id == this.value);
                    const stockQuantityInput = document.getElementById('stock_quantity');
                    const minStockInput = document.getElementById('minimum_stock');
                    if (selected && selected.type === 'production') {
                        if (stockQuantityInput) {
                            stockQuantityInput.value = 0;
                            stockQuantityInput.disabled = true;
                        }
                        if (minStockInput) {
                            minStockInput.value = 0;
                            minStockInput.disabled = true;
                        }
                    } else {
                        if (stockQuantityInput) {
                            stockQuantityInput.disabled = false;
                        }
                        if (minStockInput) {
                            minStockInput.disabled = false;
                        }
                    }
                });
            }
        @endif

        // Form submission debug
        const form = document.getElementById('finishedProductForm') || document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function() {
                console.log('Form submitted successfully');
            });
        }
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // base cost validation (ensure not exceed price)
        function validateBaseCost() {
            const price = parseFloat(document.getElementById('price').value) || 0;
            const baseCostEl = document.getElementById('base_cost');
            const baseCost = parseFloat(baseCostEl.value) || 0;

            // remove previous message
            const existing = baseCostEl.closest('div')?.querySelector('.base-cost-js');
            if (existing) existing.remove();

            if (baseCost > price) {
                baseCostEl.classList.add('ring-2', 'ring-red-200', 'border-red-300');
                const msg = document.createElement('p');
                msg.className = 'mt-2 text-sm text-red-600 base-cost-js';
                msg.textContent = 'Modal dasar tidak boleh melebihi harga jual';
                baseCostEl.closest('div')?.appendChild(msg);
                return false;
            } else {
                baseCostEl.classList.remove('ring-2', 'ring-red-200', 'border-red-300');
                return true;
            }
        }

        const priceEl = document.getElementById('price');
        const baseCostEl = document.getElementById('base_cost');

        if (priceEl) {
            priceEl.addEventListener('input', function() {
                if (baseCostEl) baseCostEl.max = this.value;
                validateBaseCost();
            });
        }
        if (baseCostEl) baseCostEl.addEventListener('input', validateBaseCost);

        // initialize
        if (priceEl && baseCostEl && priceEl.value) baseCostEl.max = priceEl.value;
    });
</script>

<script>
    // Phone formatting helper (used when the component is embedded similarly)
    function formatPhoneNumberSimple(input) {
        if (!input) return;
        input.value = input.value.replace(/\D/g, '').slice(0, 15);
    }

    // Expose small utilities if needed by other components
    window.formatPhoneNumberSimple = formatPhoneNumberSimple;
</script>
