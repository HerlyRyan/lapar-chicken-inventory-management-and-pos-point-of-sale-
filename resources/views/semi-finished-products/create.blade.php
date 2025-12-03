@extends('layouts.app')
@php use Illuminate\Support\Facades\Storage; @endphp

@section('title', 'Tambah Bahan Setengah Jadi')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-orange-50 via-white to-red-50 py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Header --}}
            <x-form.header title="Bahan Setengah Jadi" backRoute="{{ route('semi-finished-products.index', request()->has('branch_id') ? ['branch_id' => request('branch_id')] : []) }}" />

            {{-- Card --}}
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                <x-form.card-header title="Tambah Bahan Setengah Jadi" type="add" />

                <div class="p-6 sm:p-8">
                    @if(session('success'))
                        <div class="mb-4 rounded-lg bg-green-50 border border-green-200 p-4">
                            <div class="text-green-700 text-sm">
                                <i class="bi bi-check-circle mr-2"></i>{{ session('success') }}
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('semi-finished-products.store', request()->has('branch_id') ? ['branch_id' => request('branch_id')] : []) }}"
                          method="POST" enctype="multipart/form-data" id="sfpForm">
                        @csrf
                        @if(request()->has('branch_id'))
                            <input type="hidden" name="branch_id" value="{{ request('branch_id') }}">
                        @endif

                        <div class="mb-8">
                            <div class="flex items-center mb-6">
                                <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center mr-3">
                                    <i class="bi bi-box-seam text-white text-sm"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900">Informasi Bahan Setengah Jadi</h3>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                                <div class="lg:col-span-2">
                                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Nama Bahan <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="name" id="name" value="{{ old('name') }}"
                                           class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 @error('name') border-red-300 ring-2 ring-red-200 @enderror"
                                           placeholder="Masukkan nama bahan setengah jadi" required>
                                    <p class="mt-2 text-sm text-gray-600">
                                        <i class="bi bi-info-circle mr-1"></i>Nama bahan setengah jadi hasil produksi internal
                                    </p>
                                    @error('name')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="code" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Kode
                                    </label>
                                    <input type="text" name="code" id="code" value="{{ old('code') }}" 
                                           class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 @error('code') border-red-300 ring-2 ring-red-200 @enderror"
                                           placeholder="Kosongkan untuk generate otomatis">
                                    <p class="mt-2 text-sm text-gray-600">
                                        <i class="bi bi-info-circle mr-1"></i>Format: SF-XXX-001. Kosongkan untuk otomatis.
                                    </p>
                                    @error('code')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Category --}}
                                <div>
                                    <label for="category_id" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Kategori <span class="text-red-500">*</span>
                                    </label>
                                    <select name="category_id" id="category_id"
                                            class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 @error('category_id') border-red-300 ring-2 ring-red-200 @enderror"
                                            required>
                                        <option value="">- Pilih Kategori -</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <p class="mt-2 text-sm text-gray-600">
                                        <i class="bi bi-info-circle mr-1"></i>Pilih kategori bahan setengah jadi
                                        <a href="{{ route('categories.create') }}" target="_blank" rel="noopener" class="underline ml-1">Tambah kategori</a>
                                    </p>
                                    @error('category_id')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Unit --}}
                                <div>
                                    <label for="unit_id" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Satuan <span class="text-red-500">*</span>
                                    </label>
                                    <select name="unit_id" id="unit_id"
                                            class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 @error('unit_id') border-red-300 ring-2 ring-red-200 @enderror"
                                            required>
                                        <option value="">- Pilih Satuan -</option>
                                        @foreach($units as $unit)
                                            <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>
                                                {{ $unit->unit_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <p class="mt-2 text-sm text-gray-600">
                                        <i class="bi bi-info-circle mr-1"></i>Pilih satuan bahan setengah jadi
                                        <a href="{{ route('units.create') }}" target="_blank" rel="noopener" class="underline ml-1">Tambah satuan</a>
                                    </p>
                                    @error('unit_id')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Minimum Stock --}}
                                <div>
                                    <label for="minimum_stock" class="block text-sm font-semibold text-gray-700 mb-2">Stok Minimum</label>
                                    <input type="number" step="0.01" name="minimum_stock" id="minimum_stock"
                                           class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 @error('minimum_stock') border-red-300 ring-2 ring-red-200 @enderror"
                                           value="{{ old('minimum_stock', 0) }}" placeholder="0.00">
                                    @error('minimum_stock')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Stock Quantity --}}
                                <div>
                                    <label for="stock_quantity" class="block text-sm font-semibold text-gray-700 mb-2">Stok Awal</label>
                                    <input type="number" name="stock_quantity" id="stock_quantity"
                                           class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 @error('stock_quantity') border-red-300 ring-2 ring-red-200 @enderror"
                                           value="{{ old('stock_quantity', 0) }}" step="0.01" min="0" placeholder="0.00">
                                    @error('stock_quantity')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Production Cost --}}
                                <div>
                                    <label for="production_cost" class="block text-sm font-semibold text-gray-700 mb-2">Biaya Produksi</label>
                                    <input type="number" step="0.01" name="production_cost" id="production_cost"
                                           class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 @error('production_cost') border-red-300 ring-2 ring-red-200 @enderror"
                                           value="{{ old('production_cost', 0) }}" placeholder="0.00">
                                    <p class="mt-2 text-sm text-gray-600">
                                        <i class="bi bi-info-circle mr-1"></i>Biaya produksi per satuan (Rupiah)
                                    </p>
                                    @error('production_cost')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Stock Mode --}}
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-3">Mode Inisialisasi Stok <span class="text-red-500">*</span></label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <label class="flex items-start p-4 border rounded-xl bg-gray-50 cursor-pointer">
                                    <input type="radio" name="stock_mode" id="stock_mode_all" value="all"
                                           class="mt-1 mr-3" {{ old('stock_mode', !$selectedBranch ? 'all' : 'selected') == 'all' ? 'checked' : '' }}>
                                    <div>
                                        <div class="font-semibold text-gray-900"><i class="bi bi-buildings text-green-500 mr-2"></i>Semua Cabang</div>
                                        <div class="text-xs text-gray-600 mt-1">Stok akan diinisialisasi sama di semua cabang termasuk pusat produksi.</div>
                                    </div>
                                </label>

                                <label class="flex items-start p-4 border rounded-xl bg-gray-50 cursor-pointer">
                                    <input type="radio" name="stock_mode" id="stock_mode_selected" value="selected"
                                           class="mt-1 mr-3" {{ old('stock_mode', $selectedBranch ? 'selected' : 'all') == 'selected' ? 'checked' : '' }}>
                                    <div>
                                        <div class="font-semibold text-gray-900"><i class="bi bi-building text-blue-500 mr-2"></i>Cabang Tertentu</div>
                                        <div class="text-xs text-gray-600 mt-1">Stok hanya diinisialisasi untuk cabang yang dipilih.</div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        {{-- Branch selection (shown when selected mode) --}}
                        <div class="mb-6" id="branch-selection" style="display: {{ old('stock_mode', $selectedBranch ? 'selected' : 'all') == 'selected' ? 'block' : 'none' }};">
                            <label for="selected_branch_id" class="block text-sm font-semibold text-gray-700 mb-2">Pilih Cabang <span class="text-red-500">*</span></label>
                            <select name="selected_branch_id" id="selected_branch_id"
                                    class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 @error('selected_branch_id') border-red-300 ring-2 ring-red-200 @enderror">
                                <option value="">-- Pilih Cabang --</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ old('selected_branch_id', $selectedBranch ? $selectedBranch->id : '') == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('selected_branch_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Description --}}
                        <div class="mb-6">
                            <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi</label>
                            <textarea name="description" id="description" rows="3"
                                      class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 @error('description') border-red-300 ring-2 ring-red-200 @enderror"
                                      placeholder="Masukkan deskripsi bahan setengah jadi">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Image and preview --}}
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="image" class="block text-sm font-semibold text-gray-700 mb-2">Foto Bahan</label>
                                <input type="file" name="image" id="image"
                                       class="w-full text-sm @error('image') border-red-300 @enderror"
                                       accept="image/jpeg,image/png,image/jpg,image/gif" onchange="previewImage(this)">
                                <p class="mt-2 text-sm text-gray-600"><i class="bi bi-info-circle mr-1"></i>Format: JPG, PNG, GIF. Max: 2MB</p>
                                @error('image')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Preview Foto</label>
                                <div id="image-preview-container" class="d-flex items-center justify-center bg-gray-50 rounded h-32 border-2 border-dashed border-gray-200">
                                    <div id="image-preview-text" class="text-center text-gray-400">
                                        <i class="bi bi-image text-2xl mb-2 d-block"></i>
                                        <small>Preview foto akan tampil di sini</small>
                                    </div>
                                    <img id="imagePreview" src="" alt="Preview" class="hidden rounded max-h-28">
                                </div>
                            </div>
                        </div>

                        {{-- Active --}}
                        <div class="mb-6">
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" name="is_active" id="is_active" value="1"
                                       class="w-5 h-5 text-orange-600 border-gray-300 rounded focus:ring-orange-500"
                                       {{ old('is_active', 1) ? 'checked' : '' }}>
                                <span class="ml-3">
                                    <span class="text-sm font-semibold text-gray-900">Status Aktif</span>
                                    <span class="block text-xs text-gray-600 mt-1"><i class="bi bi-info-circle mr-1"></i>Centang jika bahan setengah jadi aktif digunakan</span>
                                </span>
                            </label>
                        </div>

                        <div class="border-t border-gray-200 pt-6">
                            <div class="flex flex-col sm:flex-row gap-3 sm:justify-end">
                                <a href="{{ route('semi-finished-products.index', request()->has('branch_id') ? ['branch_id' => request('branch_id')] : []) }}"
                                   class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 rounded-xl text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-all duration-200 shadow-sm">
                                    Batal
                                </a>
                                <button type="submit"
                                        class="inline-flex items-center justify-center px-8 py-3 bg-gradient-to-r from-orange-600 to-red-600 border border-transparent rounded-xl text-sm font-medium text-white shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                    <i class="bi bi-check-circle mr-2"></i>Simpan
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
            const file = input.files && input.files[0];
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

        document.addEventListener('DOMContentLoaded', function () {
            const stockModeAll = document.getElementById('stock_mode_all');
            const stockModeSelected = document.getElementById('stock_mode_selected');
            const branchSelection = document.getElementById('branch-selection');
            const selectedBranchId = document.getElementById('selected_branch_id');

            function updateBranchVisibility() {
                if (stockModeSelected && stockModeSelected.checked) {
                    branchSelection.style.display = 'block';
                    if (selectedBranchId) selectedBranchId.required = true;
                } else {
                    branchSelection.style.display = 'none';
                    if (selectedBranchId) selectedBranchId.required = false;
                }
            }

            if (stockModeAll && stockModeSelected && branchSelection) {
                updateBranchVisibility();
                stockModeAll.addEventListener('change', updateBranchVisibility);
                stockModeSelected.addEventListener('change', updateBranchVisibility);
            }

            // Branch selection sync from URL or sessionStorage (keeps behavior from original page)
            let isFromUrl = false;
            let currentBranchId = null;
            let currentBranchName = null;

            function applyBranchFromUrl() {
                const params = new URLSearchParams(window.location.search);
                const branchId = params.get('branch_id');
                if (branchId) {
                    const branches = @json($branches ?? []);
                    const found = branches.find(b => String(b.id) === String(branchId));
                    if (found) {
                        currentBranchId = branchId;
                        currentBranchName = found.name;
                        // set form to selected mode and select the branch
                        if (stockModeSelected) stockModeSelected.checked = true;
                        updateBranchVisibility();
                        if (selectedBranchId) selectedBranchId.value = branchId;
                        isFromUrl = true;
                    }
                }
            }

            function checkSessionBranch() {
                if (isFromUrl) return;
                const sbId = sessionStorage.getItem('selectedBranchId');
                const sbName = sessionStorage.getItem('selectedBranchName');
                const normalizedId = (!sbId || sbId === 'null') ? null : sbId;
                const normalizedName = (!sbName || sbName === 'null' || sbName === 'Semua Cabang') ? null : sbName;

                if (normalizedId && normalizedId !== currentBranchId) {
                    if (stockModeSelected) stockModeSelected.checked = true;
                    updateBranchVisibility();
                    if (selectedBranchId) selectedBranchId.value = normalizedId;
                    currentBranchId = normalizedId;
                    currentBranchName = normalizedName;
                } else if (!normalizedId && (currentBranchId !== null)) {
                    // switch to all
                    if (stockModeAll) stockModeAll.checked = true;
                    updateBranchVisibility();
                    if (selectedBranchId) selectedBranchId.value = '';
                    currentBranchId = null;
                    currentBranchName = null;
                }
            }

            applyBranchFromUrl();

            if (!isFromUrl) {
                setTimeout(checkSessionBranch, 500);
                setInterval(checkSessionBranch, 1000);
            }
        });
    </script>
@endpush
