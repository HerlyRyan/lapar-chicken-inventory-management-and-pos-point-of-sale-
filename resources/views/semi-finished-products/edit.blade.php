@extends('layouts.app')

@section('title', 'Edit Bahan Setengah Jadi')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1" style="color: var(--primary-red);">
            <i class="bi bi-box-seam me-2"></i>Edit Bahan Setengah Jadi
            @if(isset($branchForStock) && $branchForStock)
                <span class="badge bg-info fs-6 ms-2">{{ $branchForStock->name }}</span>
            @elseif(isset($selectedBranch) && $selectedBranch)
                <span class="badge bg-info fs-6 ms-2">{{ $selectedBranch->name }}</span>
            @else
                <span class="badge bg-secondary fs-6 ms-2">Semua Cabang</span>
            @endif
        </h1>
        <p class="text-muted mb-0">
            Edit data bahan setengah jadi: {{ $semiFinishedProduct->name }}
        </p>
    </div>
    <a href="{{ route('semi-finished-products.index', request()->has('branch_id') ? ['branch_id' => request('branch_id')] : []) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Kembali
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-lg">
            <div class="card-header text-white text-center py-3" style="background: linear-gradient(135deg, #0ea5e9 0%, #06b6d4 50%, #10b981 100%);">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-pencil me-2"></i>Form Edit Bahan Setengah Jadi
                </h5>
            </div>
            <div class="card-body p-4">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form action="{{ route('semi-finished-products.update', $semiFinishedProduct->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    @if(request('branch_id'))
                        <input type="hidden" name="branch_id" value="{{ request('branch_id') }}">
                    @endif
                    
                    <div class="row g-3">
                        <!-- Row 1: Nama dan Kode -->
                        <div class="col-md-6">
                            <label for="name" class="form-label fw-semibold">Nama Bahan Setengah Jadi <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $semiFinishedProduct->name) }}" required placeholder="Cth: Adonan Tepung">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="code" class="form-label fw-semibold">Kode</label>
                            <input type="text" name="code" id="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code', $semiFinishedProduct->code) }}" placeholder="Cth: SFP-001">
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
                                    <option value="{{ $category->id }}" {{ old('category_id', $semiFinishedProduct->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
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
                                    <option value="{{ $unit->id }}" {{ old('unit_id', $semiFinishedProduct->unit_id) == $unit->id ? 'selected' : '' }}>{{ $unit->unit_name }}</option>
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

                        <!-- Row 3: Stok Minimum dan Stok Saat Ini -->
                        @php
                            $canEditStock = (isset($branchForStock) && $branchForStock) || (isset($selectedBranch) && $selectedBranch);
                            $currentStock = $displayStockQuantity ?? 0;
                        @endphp
                        <div class="col-md-6">
                            <label for="minimum_stock" class="form-label fw-semibold">Stok Minimum</label>
                            <input type="number" step="0.01" name="minimum_stock" id="minimum_stock" class="form-control @error('minimum_stock') is-invalid @enderror" value="{{ old('minimum_stock', $minStockValue) }}" placeholder="0">
                            <div class="form-text">Stok minimum untuk cabang yang dipilih.</div>
                            @error('minimum_stock')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="stock_quantity" class="form-label fw-semibold">Stok Saat Ini @if($canEditStock) <span class="text-info">({{ $branchForStock->name ?? $selectedBranch->name }})</span> @endif</label>
                            <input type="number" step="0.01" name="stock_quantity" id="stock_quantity" class="form-control @error('stock_quantity') is-invalid @enderror" value="{{ old('stock_quantity', $currentStock) }}" {{ !$canEditStock ? 'disabled' : '' }} placeholder="0">
                             @if(!$canEditStock)
                                <div class="form-text text-warning"><i class="bi bi-info-circle me-1"></i>Pilih cabang untuk edit stok.</div>
                            @endif
                            @error('stock_quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Row 4: Biaya Produksi -->
                        <div class="col-md-12">
                            <label for="production_cost" class="form-label fw-semibold">Biaya Produksi</label>
                            <input type="number" step="0.01" name="production_cost" id="production_cost" class="form-control @error('production_cost') is-invalid @enderror" value="{{ old('production_cost', $semiFinishedProduct->production_cost) }}" placeholder="0">
                            <div class="form-text">Biaya produksi per satuan (Rupiah).</div>
                            @error('production_cost')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Row 5: Deskripsi -->
                        <div class="col-md-12">
                            <label for="description" class="form-label fw-semibold">Deskripsi</label>
                            <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="3" placeholder="Masukkan deskripsi (opsional)">{{ old('description', $semiFinishedProduct->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Row 6: Foto dan Preview -->
                        <div class="col-md-6">
                            <label for="image" class="form-label fw-semibold">Foto Bahan</label>
                            <input type="file" name="image" id="image" class="form-control @error('image') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg,image/gif" onchange="previewImage(this)">
                            <div class="form-text"><i class="bi bi-info-circle me-1"></i>Format: JPG, PNG, GIF. Maks: 2MB.</div>
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Preview Foto</label>
                            <div id="previewContainer" class="border rounded p-2 bg-light text-center" style="height: 120px; border-style: dashed !important; position: relative; overflow: hidden;">
                                <img id="imagePreview" src="{{ $semiFinishedProduct->image ? asset('storage/' . $semiFinishedProduct->image) : '' }}" alt="Preview" class="img-fluid rounded" style="max-height: 100px; max-width: 100%; display: {{ $semiFinishedProduct->image ? 'block' : 'none' }}; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
                                <span id="imagePreviewText" class="text-muted" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); display: {{ $semiFinishedProduct->image ? 'none' : 'block' }};">Preview foto</span>
                            </div>
                        </div>

                        <!-- Row 7: Status -->
                        <div class="col-12">
                            <div class="form-check mt-3">
                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $semiFinishedProduct->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="is_active">Status Aktif</label>
                            </div>
                            <div class="form-text"><i class="bi bi-info-circle me-1"></i>Nonaktifkan jika bahan tidak digunakan lagi.</div>
                        </div>
                    </div>

                    <hr class="my-4" style="border-color: var(--primary-orange); opacity: 0.3;">

                    <div class="d-flex justify-content-end">
                        <div class="d-flex gap-2">
                            <a href="{{ route('semi-finished-products.index', request()->has('branch_id') ? ['branch_id' => request('branch_id')] : []) }}" class="btn btn-outline-secondary px-4">
                                Batal
                            </a>
                            <button type="submit" class="btn btn-success px-5">
                                <i class="bi bi-check-circle me-2"></i>Simpan
                            </button>
                        </div>
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
    
    console.log('previewImage called', input.files);
    
    if (input.files && input.files[0]) {
        const file = input.files[0];
        console.log('File selected:', file.name, file.type);
        
        // Check if file is an image
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                console.log('FileReader loaded');
                imagePreview.src = e.target.result;
                imagePreview.style.display = 'block';
                if (imagePreviewText) {
                    imagePreviewText.style.display = 'none';
                }
            };
            
            reader.onerror = function(e) {
                console.error('FileReader error:', e);
            };
            
            reader.readAsDataURL(file);
        } else {
            console.log('Selected file is not an image');
            alert('Silakan pilih file gambar (JPG, PNG, GIF)');
        }
    } else {
        console.log('No file selected, resetting preview');
        // Reset to show existing image or placeholder
        const existingImage = '{{ $semiFinishedProduct->image && file_exists(public_path($semiFinishedProduct->image)) ? asset($semiFinishedProduct->image) : "" }}';
        if (existingImage) {
            imagePreview.src = existingImage;
            imagePreview.style.display = 'block';
            if (imagePreviewText) imagePreviewText.style.display = 'none';
        } else {
            imagePreview.src = '';
            imagePreview.style.display = 'none';
            if (imagePreviewText) imagePreviewText.style.display = 'block';
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, setting up image preview');
    const imageInput = document.getElementById('image');
    if (imageInput) {
        console.log('Image input found, adding event listener');
        imageInput.addEventListener('change', function(e) {
            console.log('Image input changed');
            previewImage(this);
        });
    } else {
        console.error('Image input not found');
    }
});
</script>
@endsection
