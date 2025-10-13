@extends('layouts.app')

@section('title', 'Tambah Bahan Baku')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1" style="color: var(--primary-red);">
            <i class="bi bi-box me-2"></i>Tambah Bahan Baku
        </h1>
        <p class="text-muted mb-0">Tambahkan bahan baku baru ke sistem</p>
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
                    <i class="bi bi-plus-circle me-2"></i>Form Tambah Bahan Baku
                </h5>
            </div>
            <div class="card-body p-4">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form action="{{ route('raw-materials.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">
                        <!-- Row 1: Nama dan Kode -->
                        <div class="col-md-6">
                            <label for="name" class="form-label fw-semibold">Nama Bahan Baku <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required placeholder="Cth: Ayam Utuh">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="code" class="form-label fw-semibold">Kode</label>
                            <input type="text" name="code" id="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code') }}" placeholder="Kosongkan untuk generate otomatis">
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>Kode akan dibuat otomatis jika tidak diisi (Format: RM-XXX-001)
                            </div>
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
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
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
                                    <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>{{ $unit->unit_name }}</option>
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
                                    <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
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
                            <input type="number" name="minimum_stock" id="minimum_stock" class="form-control @error('minimum_stock') is-invalid @enderror" min="0" step="1" value="{{ old('minimum_stock') }}" placeholder="Cth: 10">
                             <div class="form-text"><i class="bi bi-info-circle me-1"></i>Stok minimum untuk peringatan.</div>
                            @error('minimum_stock')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Row 4: Stok Awal dan Harga Satuan -->
                        <div class="col-md-6">
                            <label for="current_stock" class="form-label fw-semibold">Stok Awal</label>
                            <input type="number" name="current_stock" id="current_stock" class="form-control @error('current_stock') is-invalid @enderror" min="0" step="1" value="{{ old('current_stock', 0) }}" placeholder="Cth: 100">
                            <div class="form-text"><i class="bi bi-info-circle me-1"></i>Stok awal di pusat produksi.</div>
                            @error('current_stock')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="unit_price" class="form-label fw-semibold">Harga Satuan</label>
                            <input type="number" name="unit_price" id="unit_price" class="form-control @error('unit_price') is-invalid @enderror" min="0" step="1" value="{{ old('unit_price') }}" placeholder="Cth: 15000">
                            <div class="form-text"><i class="bi bi-info-circle me-1"></i>Harga beli per satuan.</div>
                            @error('unit_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Row 5: Deskripsi -->
                        <div class="col-md-12">
                            <label for="description" class="form-label fw-semibold">Deskripsi</label>
                            <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="3" placeholder="Masukkan deskripsi singkat bahan baku">{{ old('description') }}</textarea>
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
                                <img id="imagePreview" src="" alt="Preview" class="img-fluid rounded" style="max-height: 100px; max-width: 100%; display: none; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
                                <span id="imagePreviewText" class="text-muted" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">Preview foto</span>
                            </div>
                        </div>

                        <!-- Row 7: Status -->
                        <div class="col-12">
                            <div class="form-check mt-3">
                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
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
                            Simpan
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
function previewImage(input) {
    const imagePreview = document.getElementById('imagePreview');
    const imagePreviewText = document.getElementById('imagePreviewText');
    
    if (input.files && input.files[0]) {
        const file = input.files[0];
        
        // Check if file is an image
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                imagePreview.src = e.target.result;
                imagePreview.style.display = 'block';
                if (imagePreviewText) {
                    imagePreviewText.style.display = 'none';
                }
            };
            
            reader.readAsDataURL(file);
        } else {
            alert('Silakan pilih file gambar (JPG, PNG, GIF)');
        }
    } else {
        imagePreview.src = '';
        imagePreview.style.display = 'none';
        if (imagePreviewText) imagePreviewText.style.display = 'block';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const imageInput = document.getElementById('image');
    if (imageInput) {
        imageInput.addEventListener('change', function() {
            previewImage(this);
        });
    }
});
</script>
@endsection
