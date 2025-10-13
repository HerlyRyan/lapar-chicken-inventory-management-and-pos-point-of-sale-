@extends('layouts.app')

@section('title', 'Tambah Satuan')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h2 class="h3 mb-0">
                <i class="bi bi-plus-circle me-2"></i>Tambah Satuan
            </h2>
            <p class="text-muted mb-0">Buat satuan baru untuk produk</p>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('units.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>

    <!-- Form Section -->
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-rulers me-2"></i>Informasi Satuan
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('units.store') }}" method="POST">
                        @csrf
                        
                        <!-- Nama Satuan -->
                        <div class="mb-3">
                            <label for="unit_name" class="form-label">
                                Nama Satuan <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('unit_name') is-invalid @enderror" 
                                   id="unit_name" 
                                   name="unit_name" 
                                   value="{{ old('unit_name') }}"
                                   placeholder="Contoh: Kilogram, Liter, Pieces"
                                   required>
                            @error('unit_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Nama lengkap satuan yang akan digunakan</div>
                        </div>

                        <!-- Singkatan -->
                        <div class="mb-3">
                            <label for="abbreviation" class="form-label">
                                Singkatan <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('abbreviation') is-invalid @enderror" 
                                   id="abbreviation" 
                                   name="abbreviation" 
                                   value="{{ old('abbreviation') }}"
                                   placeholder="Contoh: kg, ltr, pcs"
                                   maxlength="10"
                                   style="text-transform: lowercase;"
                                   required>
                            @error('abbreviation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Singkatan unik untuk satuan (maksimal 10 karakter)</div>
                        </div>

                        <!-- Deskripsi -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Deskripsi</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="3"
                                      placeholder="Deskripsi satuan (opsional)">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Status Aktif -->
                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="is_active" 
                                       name="is_active" 
                                       value="1"
                                       {{ old('is_active', '1') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Status Aktif
                                </label>
                            </div>
                            <div class="form-text">Satuan aktif akan muncul dalam pilihan saat menambah produk</div>
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-2"></i>Simpan Satuan
                            </button>
                            <a href="{{ route('units.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-lg me-2"></i>Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-generate abbreviation from unit name
    const nameInput = document.getElementById('unit_name');
    const abbreviationInput = document.getElementById('abbreviation');
    
    nameInput.addEventListener('input', function() {
        if (!abbreviationInput.dataset.manuallyEdited) {
            const words = this.value.split(' ');
            let abbr = '';
            
            if (words.length === 1) {
                // Single word: take first 3 characters
                abbr = words[0].substring(0, 3).toLowerCase();
            } else {
                // Multiple words: take first letter of each word
                abbr = words.map(word => word.charAt(0)).join('').toLowerCase();
            }
            
            abbreviationInput.value = abbr.substring(0, 10); // Limit to 10 characters
        }
    });
    
    abbreviationInput.addEventListener('input', function() {
        this.dataset.manuallyEdited = 'true';
        this.value = this.value.toLowerCase();
    });
});
</script>
@endsection
