@extends('layouts.app')

@section('title', 'Edit Bahan Baku')

@php use Illuminate\Support\Facades\Storage; @endphp

@section('content')
<div class="min-h-screen bg-gradient-to-br from-orange-50 via-white to-red-50 py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <x-form.header edit="true" :name="$rawMaterial->name" title="Bahan Baku" backRoute="{{ route('raw-materials.index') }}"
            detailRoute="{{ route('raw-materials.show', $rawMaterial) }}" />

        {{-- Card --}}
        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
            {{-- Card Header --}}
            <x-form.card-header title="Edit Bahan Baku" type="edit" />

            <div class="p-6 sm:p-8">
                @if(session('success'))
                    <div class="mb-4 rounded-lg bg-green-50 border border-green-200 p-3 text-green-700">
                        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('raw-materials.update', $rawMaterial) }}" method="POST" enctype="multipart/form-data" id="rawMaterialForm">
                    @csrf
                    @method('PUT')

                    <div class="mb-8">
                        <div class="flex items-center mb-6">
                            <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center mr-3">
                                <i class="bi bi-box-seam text-white text-sm"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900">Informasi Bahan Baku</h3>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div class="lg:col-span-2">
                                <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Nama Bahan Baku <span class="text-red-500">*</span></label>
                                <input type="text" name="name" id="name" required
                                    class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 @error('name') border-red-300 ring-2 ring-red-200 @enderror"
                                    value="{{ old('name', $rawMaterial->name) }}" placeholder="Contoh: Ayam Utuh">
                                <p class="mt-2 text-sm text-gray-600"><i class="bi bi-info-circle mr-1"></i>Nama bahan baku</p>
                                @error('name')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label for="code" class="block text-sm font-semibold text-gray-700 mb-2">Kode <span class="text-red-500">*</span></label>
                                <input type="text" name="code" id="code" required
                                    class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 @error('code') border-red-300 ring-2 ring-red-200 @enderror"
                                    value="{{ old('code', $rawMaterial->code) }}" placeholder="Cth: BM-001">
                                <p class="mt-2 text-sm text-gray-600"><i class="bi bi-info-circle mr-1"></i>Kode unik bahan</p>
                                @error('code')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label for="category_id" class="block text-sm font-semibold text-gray-700 mb-2">Kategori <span class="text-red-500">*</span></label>
                                <select name="category_id" id="category_id" required
                                    class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 @error('category_id') border-red-300 ring-2 ring-red-200 @enderror">
                                    <option value="">- Pilih Kategori -</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id', $rawMaterial->category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="mt-2 text-sm text-gray-600">
                                    <a href="{{ route('categories.create') }}" target="_blank" rel="noopener" class="text-orange-600 underline">
                                        <i class="bi bi-box-arrow-up-right me-1"></i>Tambah kategori di tab baru
                                    </a>
                                </p>
                                @error('category_id')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label for="unit_id" class="block text-sm font-semibold text-gray-700 mb-2">Satuan <span class="text-red-500">*</span></label>
                                <select name="unit_id" id="unit_id" required
                                    class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 @error('unit_id') border-red-300 ring-2 ring-red-200 @enderror">
                                    <option value="">- Pilih Satuan -</option>
                                    @foreach($units as $unit)
                                        <option value="{{ $unit->id }}" {{ old('unit_id', $rawMaterial->unit_id) == $unit->id ? 'selected' : '' }}>
                                            {{ $unit->unit_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="mt-2 text-sm text-gray-600">
                                    <a href="{{ route('units.create') }}" target="_blank" rel="noopener" class="text-orange-600 underline">
                                        <i class="bi bi-box-arrow-up-right me-1"></i>Tambah satuan di tab baru
                                    </a>
                                </p>
                                @error('unit_id')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label for="supplier_id" class="block text-sm font-semibold text-gray-700 mb-2">Supplier <span class="text-red-500">*</span></label>
                                <select name="supplier_id" id="supplier_id" required
                                    class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 @error('supplier_id') border-red-300 ring-2 ring-red-200 @enderror">
                                    <option value="">- Pilih Supplier -</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" {{ old('supplier_id', $rawMaterial->supplier_id) == $supplier->id ? 'selected' : '' }}>
                                            {{ $supplier->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="mt-2 text-sm text-gray-600">
                                    <a href="{{ route('suppliers.create') }}" target="_blank" rel="noopener" class="text-orange-600 underline">
                                        <i class="bi bi-box-arrow-up-right me-1"></i>Tambah supplier di tab baru
                                    </a>
                                </p>
                                @error('supplier_id')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label for="minimum_stock" class="block text-sm font-semibold text-gray-700 mb-2">Stok Minimum</label>
                                <input type="number" name="minimum_stock" id="minimum_stock" min="0" step="0.01"
                                    class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 @error('minimum_stock') border-red-300 ring-2 ring-red-200 @enderror"
                                    value="{{ old('minimum_stock', $rawMaterial->minimum_stock) }}" placeholder="Cth: 10">
                                <p class="mt-2 text-sm text-gray-600"><i class="bi bi-info-circle mr-1"></i>Stok minimum untuk peringatan.</p>
                                @error('minimum_stock')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label for="current_stock" class="block text-sm font-semibold text-gray-700 mb-2">Stok Saat Ini</label>
                                <input type="number" name="current_stock" id="current_stock" min="0" step="0.01"
                                    class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 @error('current_stock') border-red-300 ring-2 ring-red-200 @enderror"
                                    value="{{ old('current_stock', $rawMaterial->current_stock) }}" placeholder="Cth: 100">
                                <p class="mt-2 text-sm text-gray-600"><i class="bi bi-info-circle mr-1"></i>Stok saat ini di pusat produksi.</p>
                                @error('current_stock')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label for="unit_price" class="block text-sm font-semibold text-gray-700 mb-2">Harga Satuan</label>
                                <input type="number" name="unit_price" id="unit_price" min="0" step="0.01"
                                    class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 @error('unit_price') border-red-300 ring-2 ring-red-200 @enderror"
                                    value="{{ old('unit_price', $rawMaterial->unit_price) }}" placeholder="Cth: 15000">
                                <p class="mt-2 text-sm text-gray-600"><i class="bi bi-info-circle mr-1"></i>Harga beli per satuan.</p>
                                @error('unit_price')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>

                            <div class="lg:col-span-2">
                                <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi</label>
                                <textarea name="description" id="description" rows="3"
                                    class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 @error('description') border-red-300 ring-2 ring-red-200 @enderror"
                                    placeholder="Masukkan deskripsi singkat bahan baku">{{ old('description', $rawMaterial->description) }}</textarea>
                                @error('description')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label for="image" class="block text-sm font-semibold text-gray-700 mb-2">Foto Bahan</label>
                                <input type="file" name="image" id="image" accept="image/jpeg,image/png,image/jpg,image/gif"
                                    class="w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:bg-orange-50 file:text-orange-700 @error('image') border-red-300 ring-2 ring-red-200 @enderror">
                                <p class="mt-2 text-sm text-gray-600"><i class="bi bi-info-circle mr-1"></i>Format: JPG, PNG, GIF. Maks: 2MB.</p>
                                @error('image')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Preview Foto</label>
                                <div id="previewContainer" class="border rounded-xl p-3 bg-gray-50 text-center" style="height:140px; border-style:dashed;">
                                    @php
                                        $imagePath = null;
                                        if ($rawMaterial->image) {
                                            if (str_starts_with($rawMaterial->image, 'products/')) {
                                                $imagePath = Storage::disk('public')->url($rawMaterial->image);
                                            } else {
                                                $imagePath = asset($rawMaterial->image);
                                            }
                                        }
                                    @endphp
                                    <img id="imagePreview" src="{{ $imagePath }}" alt="Preview" class="mx-auto rounded" style="max-height:120px; {{ $imagePath ? 'display:block' : 'display:none' }};">
                                    <div id="imagePreviewText" class="text-sm text-gray-500 mt-3" style="{{ $imagePath ? 'display:none' : 'display:block' }}">Preview foto</div>
                                </div>
                            </div>

                            <div class="lg:col-span-2">
                                <label class="flex items-center cursor-pointer mt-3">
                                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $rawMaterial->is_active) ? 'checked' : '' }}
                                        class="w-5 h-5 text-orange-600 border-gray-300 rounded focus:ring-orange-500">
                                    <span class="ml-3">
                                        <span class="text-sm font-semibold text-gray-900">Bahan Aktif</span>
                                        <span class="block text-xs text-gray-600 mt-1"><i class="bi bi-info-circle mr-1"></i>Nonaktifkan jika bahan tidak digunakan lagi</span>
                                    </span>
                                </label>
                            </div>

                        </div>
                    </div>

                    <div class="border-t border-gray-200 pt-6">
                        <div class="flex flex-col sm:flex-row gap-3 sm:justify-end">
                            <a href="{{ route('raw-materials.index') }}"
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
document.addEventListener('DOMContentLoaded', function () {
    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('imagePreview');
    const imagePreviewText = document.getElementById('imagePreviewText');
    const previewContainer = document.getElementById('previewContainer');

    if (!imageInput) return;

    function showPreview(src) {
        const img = new Image();
        img.onload = function() {
            imagePreview.src = src;
            imagePreview.style.display = 'block';
            imagePreviewText.style.display = 'none';
            previewContainer.classList.add('ring-2', 'ring-orange-200');
        };
        img.onerror = function() {
            imagePreview.style.display = 'none';
            imagePreviewText.style.display = 'block';
            imagePreviewText.textContent = 'Gagal memuat gambar';
            previewContainer.classList.remove('ring-2', 'ring-orange-200');
        };
        img.src = src;
    }

    imageInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const file = this.files[0];

            if (!file.type.match('image.*')) {
                alert('Silakan pilih file gambar (JPG, PNG, GIF)');
                this.value = '';
                return;
            }

            if (file.size > 2 * 1024 * 1024) {
                alert('Ukuran file terlalu besar. Maksimal 2MB.');
                this.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function (e) {
                showPreview(e.target.result);
            };
            reader.onerror = function () {
                alert('Terjadi kesalahan saat membaca file.');
            };
            reader.readAsDataURL(file);
        }
    });
});
</script>
@endpush
