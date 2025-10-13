@extends('layouts.app')

@section('title', 'Tambah Role')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1" style="color: var(--primary-red);">
            <i class="bi bi-shield-plus me-2"></i>Tambah Role
        </h1>
        <p class="text-muted mb-0">Buat role baru dengan permission</p>
    </div>
    <a href="{{ route('roles.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-2"></i>Kembali
    </a>
</div>

<div class="card border-0 shadow-lg">
    <div class="card-header text-white py-3" style="background: linear-gradient(135deg, #dc2626 0%, #ea580c 50%, #eab308 100%);">
        <h5 class="mb-0 fw-bold">
            <i class="bi bi-plus-circle me-2"></i>Form Tambah Role
        </h5>
    </div>
    <div class="card-body">
        <form action="{{ route('roles.store') }}" method="POST">
            @csrf
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">
                            <i class="bi bi-tag me-1"></i>Nama Role <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="code" class="form-label fw-semibold">
                            <i class="bi bi-code me-1"></i>Kode Role <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control @error('code') is-invalid @enderror" 
                               id="code" name="code" value="{{ old('code') }}" required>
                        <div class="form-text">Kode unik untuk role (contoh: admin, manager, cashier)</div>
                        @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="description" class="form-label fw-semibold">
                            <i class="bi bi-text-paragraph me-1"></i>Deskripsi
                        </label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="3">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="is_active">
                                <i class="bi bi-toggle-on me-1"></i>Status Aktif
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            
            <hr class="my-4">
            
            <div class="mb-4">
                <h5 class="fw-bold mb-3" style="color: var(--primary-red);">
                    <i class="bi bi-key-fill me-2"></i>Permission
                </h5>
                <p class="text-muted">Pilih permission yang akan diberikan kepada role ini:</p>
                
                @foreach($permissions as $category => $categoryPermissions)
                    <div class="card mb-3">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 fw-bold">{{ $category }}</h6>
                                <div>
                                    <button type="button" class="btn btn-sm btn-outline-primary me-1" 
                                            onclick="selectAllInCategory('{{ $category }}')">
                                        Pilih Semua
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" 
                                            onclick="deselectAllInCategory('{{ $category }}')">
                                        Batal Semua
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach($categoryPermissions as $permission)
                                    <div class="col-lg-4 col-md-6 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input permission-check" 
                                                   type="checkbox" 
                                                   id="permission_{{ $permission->id }}" 
                                                   name="permissions[]" 
                                                   value="{{ $permission->id }}"
                                                   data-category="{{ $category }}"
                                                   {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="permission_{{ $permission->id }}">
                                                <strong>{{ $permission->name }}</strong>
                                                @if($permission->description)
                                                    <br><small class="text-muted">{{ $permission->description }}</small>
                                                @endif
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
                
                @error('permissions')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success px-5">
                    Simpan Role
                </button>
                <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary px-4">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

<script>
function selectAllInCategory(category) {
    document.querySelectorAll(`input[data-category="${category}"]`).forEach(checkbox => {
        checkbox.checked = true;
    });
}

function deselectAllInCategory(category) {
    document.querySelectorAll(`input[data-category="${category}"]`).forEach(checkbox => {
        checkbox.checked = false;
    });
}
</script>
@endsection
