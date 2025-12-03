@extends('layouts.app')

@section('title', 'Edit Bahan Setengah Jadi')

@section('content')
    @php use Illuminate\Support\Facades\Storage; @endphp

    <div class="min-h-screen bg-gradient-to-br from-orange-50 via-white to-red-50 py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Header --}}
            <x-form.header edit="true" :name="$semiFinishedProduct->name" title="Bahan Setengah Jadi"
                backRoute="{{ route('semi-finished-products.index', request()->has('branch_id') ? ['branch_id' => request('branch_id')] : []) }}"
                detailRoute="{{ route('semi-finished-products.show', $semiFinishedProduct) }}" />

            {{-- Card --}}
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden mt-6">
                {{-- Card Header --}}
                <x-form.card-header title="Edit Bahan Setengah Jadi" type="edit" />

                <div class="p-6 sm:p-8">
                    @if(session('success'))
                        <div class="mb-4 rounded-lg bg-green-50 border border-green-100 p-4 text-sm text-green-700">
                            <i class="bi bi-check-circle mr-2"></i>{{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('semi-finished-products.update', $semiFinishedProduct->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        @if(request('branch_id'))
                            <input type="hidden" name="branch_id" value="{{ request('branch_id') }}">
                        @endif

                        <div class="mb-8">
                            <div class="flex items-center mb-6">
                                <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center mr-3">
                                    <i class="bi bi-box-seam text-white text-sm"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900">Informasi Bahan</h3>
                                <div class="ml-auto text-sm">
                                    @if(isset($branchForStock) && $branchForStock)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-info text-white">{{ $branchForStock->name }}</span>
                                    @elseif(isset($selectedBranch) && $selectedBranch)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-info text-white">{{ $selectedBranch->name }}</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-gray-200 text-gray-700">Semua Cabang</span>
                                    @endif
                                </div>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <div class="lg:col-span-2">
                                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Nama Bahan Setengah Jadi <span class="text-red-500">*</span></label>
                                    <input type="text" name="name" id="name"
                                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 @error('name') border-red-300 ring-2 ring-red-200 @enderror"
                                        value="{{ old('name', $semiFinishedProduct->name) }}" required placeholder="Cth: Adonan Tepung">
                                    <p class="mt-2 text-sm text-gray-600"><i class="bi bi-info-circle mr-1"></i>Nama bahan setengah jadi</p>
                                    @error('name') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="code" class="block text-sm font-semibold text-gray-700 mb-2">Kode</label>
                                    <input type="text" name="code" id="code"
                                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 @error('code') border-red-300 ring-2 ring-red-200 @enderror"
                                        value="{{ old('code', $semiFinishedProduct->code) }}" placeholder="Cth: SFP-001">
                                    @error('code') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="category_id" class="block text-sm font-semibold text-gray-700 mb-2">Kategori <span class="text-red-500">*</span></label>
                                    <select name="category_id" id="category_id"
                                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 @error('category_id') border-red-300 ring-2 ring-red-200 @enderror"
                                        required>
                                        <option value="">- Pilih Kategori -</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id', $semiFinishedProduct->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    <p class="mt-2 text-sm text-gray-600"><a href="{{ route('categories.create') }}" target="_blank" rel="noopener" class="text-orange-600 underline"><i class="bi bi-box-arrow-up-right mr-1"></i>Tambah kategori di tab baru</a></p>
                                    @error('category_id') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="unit_id" class="block text-sm font-semibold text-gray-700 mb-2">Satuan <span class="text-red-500">*</span></label>
                                    <select name="unit_id" id="unit_id"
                                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 @error('unit_id') border-red-300 ring-2 ring-red-200 @enderror"
                                        required>
                                        <option value="">- Pilih Satuan -</option>
                                        @foreach($units as $unit)
                                            <option value="{{ $unit->id }}" {{ old('unit_id', $semiFinishedProduct->unit_id) == $unit->id ? 'selected' : '' }}>{{ $unit->unit_name }}</option>
                                        @endforeach
                                    </select>
                                    <p class="mt-2 text-sm text-gray-600"><a href="{{ route('units.create') }}" target="_blank" rel="noopener" class="text-orange-600 underline"><i class="bi bi-box-arrow-up-right mr-1"></i>Tambah satuan di tab baru</a></p>
                                    @error('unit_id') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                @php
                                    $canEditStock = (isset($branchForStock) && $branchForStock) || (isset($selectedBranch) && $selectedBranch);
                                    $currentStock = $displayStockQuantity ?? 0;
                                @endphp

                                <div>
                                    <label for="minimum_stock" class="block text-sm font-semibold text-gray-700 mb-2">Stok Minimum</label>
                                    <input type="number" step="0.01" name="minimum_stock" id="minimum_stock"
                                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 @error('minimum_stock') border-red-300 ring-2 ring-red-200 @enderror"
                                        value="{{ old('minimum_stock', $minStockValue) }}" placeholder="0">
                                    <p class="mt-2 text-sm text-gray-600">Stok minimum untuk cabang yang dipilih.</p>
                                    @error('minimum_stock') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="stock_quantity" class="block text-sm font-semibold text-gray-700 mb-2">Stok Saat Ini @if($canEditStock) <span class="text-sm text-info">({{ $branchForStock->name ?? $selectedBranch->name }})</span> @endif</label>
                                    <input type="number" step="0.01" name="stock_quantity" id="stock_quantity"
                                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 @error('stock_quantity') border-red-300 ring-2 ring-red-200 @enderror"
                                        value="{{ old('stock_quantity', $currentStock) }}" {{ !$canEditStock ? 'disabled' : '' }} placeholder="0">
                                    @if(!$canEditStock)
                                        <p class="mt-2 text-sm text-yellow-700"><i class="bi bi-info-circle mr-1"></i>Pilih cabang untuk edit stok.</p>
                                    @endif
                                    @error('stock_quantity') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div class="lg:col-span-2">
                                    <label for="production_cost" class="block text-sm font-semibold text-gray-700 mb-2">Biaya Produksi</label>
                                    <input type="number" step="0.01" name="production_cost" id="production_cost"
                                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 @error('production_cost') border-red-300 ring-2 ring-red-200 @enderror"
                                        value="{{ old('production_cost', $semiFinishedProduct->production_cost) }}" placeholder="0">
                                    <p class="mt-2 text-sm text-gray-600">Biaya produksi per satuan (Rupiah).</p>
                                    @error('production_cost') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div class="lg:col-span-2">
                                    <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi</label>
                                    <textarea name="description" id="description" rows="3"
                                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 @error('description') border-red-300 ring-2 ring-red-200 @enderror"
                                        placeholder="Masukkan deskripsi (opsional)">{{ old('description', $semiFinishedProduct->description) }}</textarea>
                                    @error('description') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                {{-- Image --}}
                                <div>
                                    <label for="image" class="block text-sm font-semibold text-gray-700 mb-2">Foto Bahan</label>
                                    <input type="file" name="image" id="image"
                                        class="w-full px-3 py-2 border rounded-xl transition-all duration-200 @error('image') border-red-300 ring-2 ring-red-200 @enderror"
                                        accept="image/jpeg,image/png,image/jpg,image/gif" onchange="previewImage(this)">
                                    <p class="mt-2 text-sm text-gray-600"><i class="bi bi-info-circle mr-1"></i>Format: JPG, PNG, GIF. Maks: 2MB.</p>
                                    @error('image') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Preview Foto</label>
                                    <div id="previewContainer" class="border rounded-xl p-4 bg-gray-50 text-center relative" style="height:140px;">
                                        @php
                                            $stored = $semiFinishedProduct->image ? Storage::disk('public')->exists($semiFinishedProduct->image) : false;
                                            $imageUrl = $stored ? Storage::url($semiFinishedProduct->image) : '';
                                        @endphp
                                        <img id="imagePreview" src="{{ $imageUrl }}" alt="Preview" class="mx-auto max-h-24 rounded {{ $imageUrl ? 'block' : 'hidden' }}">
                                        <span id="imagePreviewText" class="text-sm text-gray-500 {{ $imageUrl ? 'hidden' : 'block' }} mt-6">Preview foto</span>
                                    </div>
                                </div>

                                <div class="lg:col-span-2">
                                    <label class="flex items-center cursor-pointer mt-2">
                                        <input type="checkbox" name="is_active" id="is_active"
                                            class="w-5 h-5 text-orange-600 border-gray-300 rounded focus:ring-orange-500"
                                            value="1" {{ old('is_active', $semiFinishedProduct->is_active) ? 'checked' : '' }}>
                                        <span class="ml-3">
                                            <span class="text-sm font-semibold text-gray-900">Status Aktif</span>
                                            <span class="block text-xs text-gray-600 mt-1"><i class="bi bi-info-circle mr-1"></i>Nonaktifkan jika bahan tidak digunakan lagi.</span>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="border-t border-gray-200 pt-6">
                            <div class="flex flex-col sm:flex-row gap-3 sm:justify-end">
                                <a href="{{ route('semi-finished-products.index', request()->has('branch_id') ? ['branch_id' => request('branch_id')] : []) }}"
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
    function previewImage(input) {
        const imagePreview = document.getElementById('imagePreview');
        const imagePreviewText = document.getElementById('imagePreviewText');

        if (input.files && input.files[0]) {
            const file = input.files[0];

            if (file.type && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    imagePreview.classList.remove('hidden');
                    if (imagePreviewText) imagePreviewText.classList.add('hidden');
                };
                reader.readAsDataURL(file);
            } else {
                alert('Silakan pilih file gambar (JPG, PNG, GIF)');
            }
        } else {
            // reset to existing image or placeholder
            const existing = "{{ $imageUrl ?? '' }}";
            if (existing) {
                imagePreview.src = existing;
                imagePreview.classList.remove('hidden');
                if (imagePreviewText) imagePreviewText.classList.add('hidden');
            } else {
                imagePreview.src = '';
                imagePreview.classList.add('hidden');
                if (imagePreviewText) imagePreviewText.classList.remove('hidden');
            }
        }
    }
</script>
@endpush
