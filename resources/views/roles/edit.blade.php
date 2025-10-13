@extends('layouts.app')

@section('title', 'Edit Role')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1" style="color: var(--primary-red);">
            <i class="bi bi-shield-exclamation me-2"></i>Edit Role
        </h1>
        <p class="text-muted mb-0">Edit role: {{ $role->name }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('roles.show', $role) }}" class="btn btn-outline-info">
            <i class="bi bi-eye me-2"></i>Lihat Detail
        </a>
        <a href="{{ route('roles.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-2"></i>Kembali
        </a>
    </div>
</div>

<div class="card border-0 shadow-lg">
    <div class="card-header text-white py-3" style="background: linear-gradient(135deg, #dc2626 0%, #ea580c 50%, #eab308 100%);">
        <h5 class="mb-0 fw-bold">
            <i class="bi bi-pencil-square me-2"></i>Form Edit Role
        </h5>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form action="{{ route('roles.update', $role) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">
                            <i class="bi bi-tag me-1"></i>Nama Role <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name', $role->name) }}" required>
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
                               id="code" name="code" value="{{ old('code', $role->code) }}" required>
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
                                  id="description" name="description" rows="3">{{ old('description', $role->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" 
                                   {{ old('is_active', $role->is_active) ? 'checked' : '' }}>
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
                
                <div class="row">
                    <div class="col-12 mb-3">
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="button" class="btn btn-sm btn-outline-success" onclick="selectAllPermissions()">
                                <i class="bi bi-check-all me-1"></i>Pilih Semua
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-warning" onclick="deselectAllPermissions()">
                                <i class="bi bi-x-square me-1"></i>Hapus Semua
                            </button>
                        </div>
                    </div>
                </div>
                
                @foreach($permissions as $category => $categoryPermissions)
                    <div class="mb-4">
                        <h6 class="fw-bold text-uppercase mb-2" style="color: var(--primary-orange);">
                            <i class="bi bi-folder-fill me-1"></i>{{ $category ?: 'UMUM' }}
                        </h6>
                        <div class="row">
                            @foreach($categoryPermissions as $permission)
                                <div class="col-md-6 col-lg-4 mb-2">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input permission-checkbox" type="checkbox" 
                                               id="permission_{{ $permission->id }}" name="permissions[]" 
                                               value="{{ $permission->id }}"
                                               {{ in_array($permission->id, old('permissions', $rolePermissions)) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="permission_{{ $permission->id }}">
                                            <small class="fw-semibold">{{ $permission->name }}</small>
                                            @if($permission->description)
                                                <br><small class="text-muted">{{ $permission->description }}</small>
                                            @endif
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
                
                @if($permissions->isEmpty())
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        Belum ada permission yang tersedia. 
                        <a href="{{ route('permissions.index') }}" class="alert-link">Kelola Permission</a>
                    </div>
                @endif
            </div>
            
            <hr class="my-4">
            
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary px-4">
                    Batal
                </a>
                <button type="submit" class="btn btn-success px-5">
                    Update Role
                </button>
            </div>
        </form>
    </div>
</div>
<script>
    /**
     * Select all permission checkboxes
     */
    function selectAllPermissions() {
        document.querySelectorAll('.permission-checkbox').forEach(cb => cb.checked = true);
    }

    /**
     * Deselect all permission checkboxes
     */
    function deselectAllPermissions() {
        document.querySelectorAll('.permission-checkbox').forEach(cb => cb.checked = false);
    }
</script>
@endsection

@section('styles')
<style>
    :root {
        --primary-red: #dc2626;
        --primary-orange: #ea580c;
        --primary-yellow: #eab308;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, var(--primary-red) 0%, var(--primary-orange) 50%, var(--primary-yellow) 100%);
        border: none;
        transition: all 0.3s ease;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
        background: linear-gradient(135deg, #b91c1c 0%, #c2410c 50%, #ca8a04 100%);
    }
    
    .form-control:focus, .form-select:focus {
        border-color: var(--primary-orange);
        box-shadow: 0 0 0 0.2rem rgba(234, 88, 12, 0.25);
    }
    
    .card {
        border-radius: 15px;
        overflow: hidden;
    }
    
    .alert {
        border-radius: 10px;
        border: none;
    }
    
    .form-check-input:checked {
        background-color: var(--primary-red);
        border-color: var(--primary-red);
    }
    
    .form-check-input:focus {
        box-shadow: 0 0 0 0.2rem rgba(220, 38, 38, 0.25);
    }
</style>
@endsection

@section('scripts')
<script>
    function selectAllPermissions() {
        document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
            checkbox.checked = true;
        });
    }
    
    function deselectAllPermissions() {
        document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
            checkbox.checked = false;
        });
    }
</script>
@endsection
