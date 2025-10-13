@extends('layouts.app')

@section('title', 'Edit Bahan Baku')

@php
use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1" style="color: var(--primary-red);">
            <i class="bi bi-pencil-square me-2"></i>Edit Bahan Baku
        </h1>
        <p class="text-muted mb-0">Ubah data bahan baku</p>
    </div>
    <a href="{{ route('raw-materials.index') }}" class="btn btn-outline-primary">
        <i class="bi bi-arrow-left me-2"></i>Kembali
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-lg">
            <div class="card-header text-white text-center py-3" style="background: linear-gradient(135deg, #dc2626 0%, #ea580c 50%, #eab308 100%);">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-pencil-square me-2"></i>Form Edit Bahan Baku
                </h5>
            </div>
            <div class="card-body p-4">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                <form action="{{ route('raw-materials.update', $rawMaterial) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <!-- Row 1: Nama dan Kode -->
                        <div class="col-md-6">
                            <label for="name" class="form-label fw-semibold">Nama Bahan Baku <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $rawMaterial->name) }}" required placeholder="Cth: Ayam Utuh">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="code" class="form-label fw-semibold">Kode <span class="text-danger">*</span></label>
                            <input type="text" name="code" id="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code', $rawMaterial->code) }}" required placeholder="Cth: BM-001">
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Row 2: Kategori dan Satuan -->
                        <div class="col-md-6">
                            <label for="category_id" class="form-label fw-semibold">Kategori <span class="text-danger">*</span></label>
                            <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                <option value="">- Pilih Kategori -</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $rawMaterial->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <div class="form-text mt-1">
                                <a href="{{ route('categories.create') }}" target="_blank" rel="noopener" class="text-decoration-underline">
                                    <i class="bi bi-box-arrow-up-right me-1"></i>Tambah kategori di tab baru
                                </a>
                            </div>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="unit_id" class="form-label fw-semibold">Satuan <span class="text-danger">*</span></label>
                            <select name="unit_id" id="unit_id" class="form-select @error('unit_id') is-invalid @enderror" required>
                                <option value="">- Pilih Satuan -</option>
                                @foreach($units as $unit)
                                    <option value="{{ $unit->id }}" {{ old('unit_id', $rawMaterial->unit_id) == $unit->id ? 'selected' : '' }}>{{ $unit->unit_name }}</option>
                                @endforeach
                            </select>
                            <div class="form-text mt-1">
                                <a href="{{ route('units.create') }}" target="_blank" rel="noopener" class="text-decoration-underline">
                                    <i class="bi bi-box-arrow-up-right me-1"></i>Tambah satuan di tab baru
                                </a>
                            </div>
                            @error('unit_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Row 3: Supplier dan Stok Minimum -->
                        <div class="col-md-6">
                            <label for="supplier_id" class="form-label fw-semibold">Supplier <span class="text-danger">*</span></label>
                            <select name="supplier_id" id="supplier_id" class="form-select @error('supplier_id') is-invalid @enderror" required>
                                <option value="">- Pilih Supplier -</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ old('supplier_id', $rawMaterial->supplier_id) == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                            <div class="form-text mt-1">
                                <a href="{{ route('suppliers.create') }}" target="_blank" rel="noopener" class="text-decoration-underline">
                                    <i class="bi bi-box-arrow-up-right me-1"></i>Tambah supplier di tab baru
                                </a>
                            </div>
                            @error('supplier_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="minimum_stock" class="form-label fw-semibold">Stok Minimum</label>
                            <input type="number" name="minimum_stock" id="minimum_stock" class="form-control @error('minimum_stock') is-invalid @enderror" min="0" step="0.01" value="{{ old('minimum_stock', $rawMaterial->minimum_stock) }}" placeholder="Cth: 10">
                             <div class="form-text"><i class="bi bi-info-circle me-1"></i>Stok minimum untuk peringatan.</div>
                            @error('minimum_stock')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Row 4: Stok Saat Ini dan Harga Satuan -->
                        <div class="col-md-6">
                            <label for="current_stock" class="form-label fw-semibold">Stok Saat Ini</label>
                            <input type="number" name="current_stock" id="current_stock" class="form-control @error('current_stock') is-invalid @enderror" min="0" step="0.01" value="{{ old('current_stock', $rawMaterial->current_stock) }}" placeholder="Cth: 100">
                            <div class="form-text"><i class="bi bi-info-circle me-1"></i>Stok saat ini di pusat produksi.</div>
                            @error('current_stock')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="unit_price" class="form-label fw-semibold">Harga Satuan</label>
                            <input type="number" name="unit_price" id="unit_price" class="form-control @error('unit_price') is-invalid @enderror" min="0" step="0.01" value="{{ old('unit_price', $rawMaterial->unit_price) }}" placeholder="Cth: 15000">
                            <div class="form-text"><i class="bi bi-info-circle me-1"></i>Harga beli per satuan.</div>
                            @error('unit_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Row 5: Deskripsi -->
                        <div class="col-md-12">
                            <label for="description" class="form-label fw-semibold">Deskripsi</label>
                            <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="3" placeholder="Masukkan deskripsi singkat bahan baku">{{ old('description', $rawMaterial->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Row 6: Foto dan Preview -->
                        <div class="col-md-6">
                            <label for="image" class="form-label fw-semibold">Foto Bahan</label>
                            <input type="file" name="image" id="image" class="form-control @error('image') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg,image/gif">
                            <div class="form-text"><i class="bi bi-info-circle me-1"></i>Format: JPG, PNG, GIF. Maks: 2MB.</div>
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Preview Foto</label>
                            <div id="previewContainer" class="border rounded p-2 bg-light text-center" style="height: 120px; border-style: dashed !important; position: relative; overflow: hidden;">
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
                                <img id="imagePreview" src="{{ $imagePath }}" alt="Preview" class="img-fluid rounded" style="max-height: 100px; max-width: 100%; {{ $imagePath ? 'display: block;' : 'display: none;' }} position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
                                <span id="imagePreviewText" class="text-muted" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); {{ $imagePath ? 'display: none;' : 'display: block;' }}">Preview foto</span>
                            </div>
                        </div>

                        <!-- Row 7: Status -->
                        <div class="col-12">
                            <div class="form-check mt-3">
                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $rawMaterial->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="is_active">Status Aktif</label>
                            </div>
                            <div class="form-text"><i class="bi bi-info-circle me-1"></i>Nonaktifkan jika bahan baku tidak digunakan lagi.</div>
                        </div>
                    </div>

                    <hr class="my-4" style="border-color: var(--primary-orange); opacity: 0.3;">
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('raw-materials.index') }}" class="btn btn-outline-secondary px-4">
                            Batal
                        </a>
                        <button type="submit" class="btn btn-success px-5">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
/**
 * Handle image preview functionality for the raw material edit form
 * Completely rewritten for better reliability and performance
 */
document.addEventListener('DOMContentLoaded', function() {
    // Get DOM elements
    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('imagePreview');
    const imagePreviewText = document.getElementById('imagePreviewText');
    const previewContainer = document.getElementById('previewContainer');
    
    // Log initialization
    console.log('Image preview system initializing');
    
    // Check if elements exist
    if (!imageInput || !imagePreview || !imagePreviewText || !previewContainer) {
        console.error('Required image preview elements not found');
        return;
    }
    
    // Function to display preview of selected image
    function displayImagePreview(src) {
        console.log('Displaying image preview:', src);
        
        // Create a new image to test loading
        const testImage = new Image();
        
        // Set up success handler
        testImage.onload = function() {
            // Update preview image
            imagePreview.src = src;
            imagePreview.style.display = 'block';
            imagePreviewText.style.display = 'none';
            
            // Force browser repaint to ensure display updates
            previewContainer.classList.add('preview-active');
            
            console.log('Image preview displayed successfully');
        };
        
        // Set up error handler
        testImage.onerror = function() {
            console.error('Failed to load image for preview');
            imagePreview.style.display = 'none';
            imagePreviewText.style.display = 'block';
            imagePreviewText.textContent = 'Gagal memuat gambar';
        };
        
        // Start loading the image
        testImage.src = src;
    }
    
    // Handle file selection
    imageInput.addEventListener('change', function(event) {
        if (this.files && this.files[0]) {
            const file = this.files[0];
            console.log('File selected:', file.name, 'Type:', file.type, 'Size:', file.size);
            
            // Validate file type
            if (!file.type.match('image.*')) {
                console.warn('Selected file is not an image');
                alert('Silakan pilih file gambar (JPG, PNG, GIF)');
                return;
            }
            
            // Validate file size (max 2MB)
            if (file.size > 2 * 1024 * 1024) {
                console.warn('Selected file exceeds maximum size');
                alert('Ukuran file terlalu besar. Maksimal 2MB.');
                return;
            }
            
            // Create file reader
            const reader = new FileReader();
            
            // Set up file reader
            reader.onload = function(e) {
                displayImagePreview(e.target.result);
            };
            
            reader.onerror = function() {
                console.error('Error reading file');
                alert('Terjadi kesalahan saat membaca file.');
            };
            
            // Read the file
            reader.readAsDataURL(file);
        }
    });
    
    // Add some CSS for better preview handling
    const style = document.createElement('style');
    style.textContent = `
        #previewContainer.preview-active {
            border-color: var(--primary-orange) !important;
        }
        #imagePreview {
            transition: opacity 0.2s ease-in-out;
        }
    `;
    document.head.appendChild(style);
    
    // Log initialization complete
    console.log('Image preview system initialized');
});
</script>
@endsection
