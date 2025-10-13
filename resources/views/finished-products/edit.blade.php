@extends('layouts.app')

@section('title', 'Edit Produk Siap Jual')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1" style="color: var(--primary-red);">
            <i class="bi bi-pencil me-2"></i>Edit Produk Siap Jual
            @if($branchForStock)
                <span class="badge bg-info fs-6 ms-2">{{ $branchForStock->name }}</span>
            @elseif($selectedBranch)
                <span class="badge bg-info fs-6 ms-2">{{ $selectedBranch->name }}</span>
            @else
                <span class="badge bg-secondary fs-6 ms-2">Semua Cabang</span>
            @endif
        </h1>
        <p class="text-muted mb-0">
            Ubah data produk siap jual: {{ $finishedProduct->name }}
            @if($branchForStock)
                - {{ $branchForStock->name }} ({{ $branchForStock->code }})
            @elseif($selectedBranch)
                - {{ $selectedBranch->name }} ({{ $selectedBranch->code }})
            @else
                - Tampilkan semua cabang
            @endif
        </p>
    </div>
    <a href="{{ route('finished-products.index', request()->has('branch_id') ? ['branch_id' => request('branch_id')] : []) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Kembali
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-lg">
    <div class="card-header text-white py-3" style="background: linear-gradient(135deg, #eab308 0%, #f59e0b 50%, #d97706 100%);">
        <h5 class="mb-0 fw-bold">
            <i class="bi bi-form me-2"></i>Form Edit Produk Siap Jual
        </h5>
    </div>
    <div class="card-body">
        <form action="{{ route('finished-products.update', array_merge([$finishedProduct], request()->has('branch_id') ? ['branch_id' => request('branch_id')] : [])) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @if(request('branch_id'))
                <input type="hidden" name="branch_id" value="{{ request('branch_id') }}">
            @endif
            
            <div class="row g-3">
                <!-- Row 1: Nama dan Kode -->
                <div class="col-md-6">
                    <label for="name" class="form-label fw-semibold">Nama Produk <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $finishedProduct->name) }}" required placeholder="Cth: Paket Ayam Geprek">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="code" class="form-label fw-semibold">Kode</label>
                    <input type="text" name="code" id="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code', $finishedProduct->code) }}" placeholder="Cth: FP-001">
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
                            <option value="{{ $category->id }}" {{ old('category_id', $finishedProduct->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
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
                            <option value="{{ $unit->id }}" {{ old('unit_id', $finishedProduct->unit_id) == $unit->id ? 'selected' : '' }}>{{ $unit->unit_name }}</option>
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
                <div class="col-md-6">
                    <label for="minimum_stock" class="form-label fw-semibold">Stok Minimum</label>
                    <input type="number" step="0.01" name="minimum_stock" id="minimum_stock" class="form-control @error('minimum_stock') is-invalid @enderror" value="{{ old('minimum_stock', $finishedProduct->minimum_stock) }}" placeholder="0">
                    @error('minimum_stock')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="stock_quantity" class="form-label fw-semibold">Stok Saat Ini @if($branchForStock) <span class="text-info">({{ $branchForStock->name }})</span> @endif</label>
                    <input type="number" step="0.01" name="stock_quantity" id="stock_quantity" class="form-control @error('stock_quantity') is-invalid @enderror" value="{{ old('stock_quantity', $displayStockQuantity) }}" placeholder="0">
                    @if(!$branchForStock)
                        <div class="form-text text-warning"><i class="bi bi-info-circle me-1"></i>Pilih cabang untuk edit stok.</div>
                    @endif
                    @error('stock_quantity')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Row 4: Harga Jual dan Modal Dasar -->
                <div class="col-md-6">
                    <label for="price" class="form-label fw-semibold">Harga Jual <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" name="price" id="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price', $finishedProduct->price) }}" required placeholder="0">
                    @error('price')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="base_cost" class="form-label fw-semibold">Modal Dasar</label>
                    <input type="number" step="0.01" name="base_cost" id="base_cost" class="form-control @error('base_cost') is-invalid @enderror" value="{{ old('base_cost', $finishedProduct->production_cost) }}" placeholder="0" max="{{ old('price', $finishedProduct->price) }}">
                    <div class="form-text"><i class="bi bi-info-circle me-1"></i>Modal dasar tidak boleh melebihi harga jual</div>
                    @error('base_cost')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Row 5: Deskripsi -->
                <div class="col-md-12">
                    <label for="description" class="form-label fw-semibold">Deskripsi</label>
                    <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="3" placeholder="Masukkan deskripsi (opsional)">{{ old('description', $finishedProduct->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Row 6: Foto dan Preview -->
                <div class="col-md-6">
                    <label for="photo" class="form-label fw-semibold">Foto Produk</label>
                    <input type="file" name="photo" id="photo" class="form-control @error('photo') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg,image/gif" onchange="previewImage(this)">
                    <div class="form-text"><i class="bi bi-info-circle me-1"></i>Format: JPG, PNG, GIF. Maks: 2MB.</div>
                    @error('photo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Preview Foto</label>
                    <div id="previewContainer" class="border rounded p-2 bg-light text-center" style="height: 120px; border-style: dashed !important; position: relative; overflow: hidden;">
                        <img id="imagePreview" src="{{ $finishedProduct->photo ? asset('storage/' . $finishedProduct->photo) : '' }}" alt="Preview" class="img-fluid rounded" style="max-height: 100px; max-width: 100%; display: {{ $finishedProduct->photo ? 'block' : 'none' }}; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
                        <span id="imagePreviewText" class="text-muted" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); display: {{ $finishedProduct->photo ? 'none' : 'block' }};">Preview foto</span>
                    </div>
                </div>

                <!-- Row 7: Status -->
                <div class="col-12">
                    <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $finishedProduct->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="is_active">Status Aktif</label>
                    </div>
                    <div class="form-text"><i class="bi bi-info-circle me-1"></i>Nonaktifkan jika produk tidak dijual lagi.</div>
                </div>
            </div>

            <hr class="my-4">

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('finished-products.index', request()->has('branch_id') ? ['branch_id' => request('branch_id')] : []) }}" class="btn btn-outline-secondary px-4">Batal</a>
                <button type="submit" class="btn btn-warning px-5"><i class="bi bi-check-circle me-2"></i>Simpan</button>
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
        const reader = new FileReader();

        reader.onload = function(e) {
            imagePreview.src = e.target.result;
            imagePreview.style.display = 'block';
            imagePreviewText.style.display = 'none';
        }

        reader.readAsDataURL(input.files[0]);
    } else {
        // If no file is selected, revert to the original state if there was an old photo
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
</script>
<script>
// Validasi Modal Dasar <= Harga Jual
document.addEventListener('DOMContentLoaded', function() {
    const priceInput = document.getElementById('price');
    const baseCostInput = document.getElementById('base_cost');

    function validateBaseCost() {
        const price = parseFloat(priceInput.value) || 0;
        const baseCost = parseFloat(baseCostInput.value) || 0;

        if (baseCost > price) {
            baseCostInput.classList.add('is-invalid');
            // ensure feedback exists only once
            let feedback = baseCostInput.parentElement.querySelector('.invalid-feedback.base-cost');
            if (!feedback) {
                feedback = document.createElement('div');
                feedback.className = 'invalid-feedback d-block base-cost';
                feedback.textContent = 'Modal dasar tidak boleh melebihi harga jual';
                baseCostInput.parentElement.appendChild(feedback);
            }
            return false;
        } else {
            baseCostInput.classList.remove('is-invalid');
            const feedback = baseCostInput.parentElement.querySelector('.invalid-feedback.base-cost');
            if (feedback) feedback.remove();
            return true;
        }
    }

    if (priceInput && baseCostInput) {
        // initialize max
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