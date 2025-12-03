@extends('layouts.app')

@section('title', 'Edit Produk Siap Jual')

@section('content')
    @php use Illuminate\Support\Facades\Storage; @endphp

    <div class="min-h-screen bg-gradient-to-br from-orange-50 via-white to-red-50 py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Header --}}
            <x-form.header edit="true" :name="$finishedProduct->name" title="Produk Siap Jual"
                backRoute="{{ route('finished-products.index', request()->has('branch_id') ? ['branch_id' => request('branch_id')] : []) }}"
                detailRoute="{{ route('finished-products.show', $finishedProduct) }}" />

            {{-- Card --}}
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                {{-- Card Header --}}
                <x-form.card-header title="Form Edit Produk Siap Jual" type="edit" />

                <div class="p-6 sm:p-8">
                    <form action="{{ route('finished-products.update', array_merge([$finishedProduct], request()->has('branch_id') ? ['branch_id' => request('branch_id')] : [])) }}"
                        method="POST" enctype="multipart/form-data" id="finishedProductForm">
                        @csrf
                        @method('PUT')
                        @if(request('branch_id'))
                            <input type="hidden" name="branch_id" value="{{ request('branch_id') }}">
                        @endif

                        <div class="mb-6">
                            <div class="flex items-center mb-4">
                                <div
                                    class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center mr-3">
                                    <i class="bi bi-pencil text-white text-sm"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900">Detail Produk</h3>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <div class="lg:col-span-2">
                                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Nama Produk
                                        <span class="text-red-500">*</span></label>
                                    <input type="text" name="name" id="name"
                                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 @error('name') border-red-300 ring-2 ring-red-200 @enderror"
                                        value="{{ old('name', $finishedProduct->name) }}" required placeholder="Cth: Paket Ayam Geprek">
                                    @error('name')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="code" class="block text-sm font-semibold text-gray-700 mb-2">Kode</label>
                                    <input type="text" name="code" id="code"
                                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 @error('code') border-red-300 ring-2 ring-red-200 @enderror"
                                        value="{{ old('code', $finishedProduct->code) }}" placeholder="Cth: FP-001">
                                    @error('code')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="category_id" class="block text-sm font-semibold text-gray-700 mb-2">Kategori
                                        <span class="text-red-500">*</span></label>
                                    <select name="category_id" id="category_id"
                                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 @error('category_id') border-red-300 ring-2 ring-red-200 @enderror"
                                        required>
                                        <option value="">- Pilih Kategori -</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id', $finishedProduct->category_id) == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <p class="mt-2 text-sm text-gray-600"><i class="bi bi-info-circle mr-1"></i>
                                        <a href="{{ route('categories.create') }}" target="_blank" rel="noopener" class="underline">Tambah kategori di tab baru</a>
                                    </p>
                                    @error('category_id')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="unit_id" class="block text-sm font-semibold text-gray-700 mb-2">Satuan
                                        <span class="text-red-500">*</span></label>
                                    <select name="unit_id" id="unit_id"
                                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 @error('unit_id') border-red-300 ring-2 ring-red-200 @enderror"
                                        required>
                                        <option value="">- Pilih Satuan -</option>
                                        @foreach($units as $unit)
                                            <option value="{{ $unit->id }}" {{ old('unit_id', $finishedProduct->unit_id) == $unit->id ? 'selected' : '' }}>
                                                {{ $unit->unit_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <p class="mt-2 text-sm text-gray-600"><i class="bi bi-info-circle mr-1"></i>
                                        <a href="{{ route('units.create') }}" target="_blank" rel="noopener" class="underline">Tambah satuan di tab baru</a>
                                    </p>
                                    @error('unit_id')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="minimum_stock" class="block text-sm font-semibold text-gray-700 mb-2">Stok Minimum</label>
                                    <input type="number" step="0.01" name="minimum_stock" id="minimum_stock"
                                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 @error('minimum_stock') border-red-300 ring-2 ring-red-200 @enderror"
                                        value="{{ old('minimum_stock', $finishedProduct->minimum_stock) }}" placeholder="0">
                                    @error('minimum_stock')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="stock_quantity" class="block text-sm font-semibold text-gray-700 mb-2">Stok Saat Ini
                                        @if($branchForStock)
                                            <span class="text-info">({{ $branchForStock->name }})</span>
                                        @endif
                                    </label>
                                    <input type="number" step="0.01" name="stock_quantity" id="stock_quantity"
                                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 @error('stock_quantity') border-red-300 ring-2 ring-red-200 @enderror"
                                        value="{{ old('stock_quantity', $displayStockQuantity) }}" placeholder="0">
                                    @if(!$branchForStock)
                                        <p class="mt-2 text-sm text-yellow-600"><i class="bi bi-info-circle mr-1"></i>Pilih cabang untuk edit stok.</p>
                                    @endif
                                    @error('stock_quantity')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="price" class="block text-sm font-semibold text-gray-700 mb-2">Harga Jual
                                        <span class="text-red-500">*</span></label>
                                    <input type="number" step="0.01" name="price" id="price"
                                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 @error('price') border-red-300 ring-2 ring-red-200 @enderror"
                                        value="{{ old('price', $finishedProduct->price) }}" required placeholder="0">
                                    @error('price')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="base_cost" class="block text-sm font-semibold text-gray-700 mb-2">Modal Dasar</label>
                                    <input type="number" step="0.01" name="base_cost" id="base_cost"
                                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 @error('base_cost') border-red-300 ring-2 ring-red-200 @enderror"
                                        value="{{ old('base_cost', $finishedProduct->production_cost) }}" placeholder="0" max="{{ old('price', $finishedProduct->price) }}">
                                    <p class="mt-2 text-sm text-gray-600"><i class="bi bi-info-circle mr-1"></i>Modal dasar tidak boleh melebihi harga jual</p>
                                    @error('base_cost')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="lg:col-span-2">
                                    <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi</label>
                                    <textarea name="description" id="description" rows="3"
                                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 @error('description') border-red-300 ring-2 ring-red-200 @enderror"
                                        placeholder="Masukkan deskripsi (opsional)">{{ old('description', $finishedProduct->description) }}</textarea>
                                    @error('description')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="photo" class="block text-sm font-semibold text-gray-700 mb-2">Foto Produk</label>
                                    <input type="file" name="photo" id="photo"
                                        class="w-full px-4 py-3 border rounded-xl transition-all duration-200 @error('photo') border-red-300 ring-2 ring-red-200 @enderror"
                                        accept="image/jpeg,image/png,image/jpg,image/gif" onchange="previewImage(this)">
                                    <p class="mt-2 text-sm text-gray-600"><i class="bi bi-info-circle mr-1"></i>Format: JPG, PNG, GIF. Maks: 2MB.</p>
                                    @error('photo')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Preview Foto</label>
                                    <div id="previewContainer" class="border rounded-xl p-3 bg-gray-50 text-center" style="height: 140px; position: relative; overflow: hidden;">
                                        <img id="imagePreview" src="{{ $finishedProduct->photo ? asset('storage/' . $finishedProduct->photo) : '' }}"
                                            alt="Preview" class="mx-auto rounded" style="max-height: 110px; max-width: 100%; display: {{ $finishedProduct->photo ? 'block' : 'none' }};">
                                        <span id="imagePreviewText" class="text-sm text-gray-500" style="display: {{ $finishedProduct->photo ? 'none' : 'block' }};">Preview foto</span>
                                    </div>
                                </div>

                                <div class="lg:col-span-2">
                                    <label class="flex items-center cursor-pointer">
                                        <input type="checkbox" name="is_active" id="is_active"
                                            class="w-5 h-5 text-orange-600 border-gray-300 rounded focus:ring-orange-500"
                                            value="1" {{ old('is_active', $finishedProduct->is_active) ? 'checked' : '' }}>
                                        <span class="ml-3">
                                            <span class="text-sm font-semibold text-gray-900">Produk Aktif</span>
                                            <span class="block text-xs text-gray-600 mt-1"><i class="bi bi-info-circle mr-1"></i>Nonaktifkan jika produk tidak dijual lagi</span>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="border-t border-gray-200 pt-6">
                            <div class="flex flex-col sm:flex-row gap-3 sm:justify-end">
                                <a href="{{ route('finished-products.index', request()->has('branch_id') ? ['branch_id' => request('branch_id')] : []) }}"
                                    class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 rounded-xl text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-all duration-200 shadow-sm">
                                    Batal
                                </a>
                                <button type="submit"
                                    class="inline-flex items-center justify-center px-8 py-3 bg-gradient-to-r from-orange-600 to-red-600 border border-transparent rounded-xl text-sm font-medium text-white hover:from-orange-700 hover:to-red-700 focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-all duration-200 shadow-lg">
                                    <i class="bi bi-check-circle mr-2"></i> Simpan Perubahan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Image preview (kept behavior from previous implementation)
        function previewImage(input) {
            const imagePreview = document.getElementById('imagePreview');
            const imagePreviewText = document.getElementById('imagePreviewText');

            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    imagePreview.style.display = 'block';
                    imagePreviewText.style.display = 'none';
                }

                reader.readAsDataURL(input.files[0]);
            } else {
                @if($finishedProduct->photo)
                    imagePreview.src = "{{ asset('storage/' . $finishedProduct->photo) }}";
                    imagePreview.style.display = 'block';
                    imagePreviewText.style.display = 'none';
                @else
                    imagePreview.src = "";
                    imagePreview.style.display = 'none';
                    imagePreviewText.style.display = 'block';
                @endif
            }
        }

        // Validasi Modal Dasar <= Harga Jual
        document.addEventListener('DOMContentLoaded', function() {
            const priceInput = document.getElementById('price');
            const baseCostInput = document.getElementById('base_cost');

            function validateBaseCost() {
                const price = parseFloat(priceInput.value) || 0;
                const baseCost = parseFloat(baseCostInput.value) || 0;

                if (baseCost > price) {
                    baseCostInput.classList.add('border-red-300', 'ring-2', 'ring-red-200');
                    // ensure feedback exists only once
                    let feedback = baseCostInput.parentElement.querySelector('.invalid-feedback.base-cost');
                    if (!feedback) {
                        feedback = document.createElement('div');
                        feedback.className = 'invalid-feedback d-block base-cost text-sm text-red-600';
                        feedback.textContent = 'Modal dasar tidak boleh melebihi harga jual';
                        baseCostInput.parentElement.appendChild(feedback);
                    }
                    return false;
                } else {
                    baseCostInput.classList.remove('border-red-300', 'ring-2', 'ring-red-200');
                    const feedback = baseCostInput.parentElement.querySelector('.invalid-feedback.base-cost');
                    if (feedback) feedback.remove();
                    return true;
                }
            }

            if (priceInput && baseCostInput) {
                baseCostInput.setAttribute('max', priceInput.value || '');
                priceInput.addEventListener('input', function() {
                    baseCostInput.setAttribute('max', this.value);
                    validateBaseCost();
                });
                baseCostInput.addEventListener('input', validateBaseCost);
            }
        });
    </script>
@endpush