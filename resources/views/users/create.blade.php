@extends('layouts.app')
@php use Illuminate\Support\Facades\Storage; @endphp

@section('title', 'Tambah Pengguna')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1" style="color: var(--primary-red);">
            <i class="bi bi-person-plus me-2"></i>Tambah Pengguna Baru
        </h1>
        <p class="text-muted mb-0">Tambahkan pengguna baru untuk sistem inventory dan penjualan</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('users.index') }}" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left me-2"></i>Kembali
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-lg">
            <div class="card-header text-white text-center py-3" style="background: linear-gradient(135deg, #dc2626 0%, #ea580c 50%, #eab308 100%);">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-pencil-square me-2"></i>Form Tambah Pengguna
                </h5>
            </div>
            <div class="card-body p-4">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data" id="userForm" onsubmit="return preparePhoneNumber()">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label for="name" class="form-label fw-semibold">
                                Nama Lengkap <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name') }}" required placeholder="Masukkan nama lengkap">
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>Nama lengkap pengguna
                            </div>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12">
                            <label for="email" class="form-label fw-semibold">
                                Email <span class="text-danger">*</span>
                            </label>
                            <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" 
                                   value="{{ old('email') }}" required placeholder="Masukkan alamat email">
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>Email untuk login dan komunikasi
                            </div>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12">
                            <label for="phone" class="form-label fw-semibold">
                                Nomor Telepon <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">62</span>
                                <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" 
                                       value="{{ old('phone') }}" required placeholder="813xxxxxxxx" 
                                       oninput="formatPhoneNumber(this)" maxlength="15">
                            </div>
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>Format: 62 diikuti 8-13 digit angka (untuk WhatsApp)
                            </div>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="password" class="form-label fw-semibold">
                                Kata Sandi <span class="text-danger">*</span>
                            </label>
                            <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" 
                                   required placeholder="Minimal 8 karakter">
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>Kata sandi untuk login sistem
                            </div>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label fw-semibold">
                                Konfirmasi Kata Sandi <span class="text-danger">*</span>
                            </label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" 
                                   required placeholder="Ulangi kata sandi">
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>Konfirmasi password yang sama
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="branch_id" class="form-label fw-semibold">Cabang</label>
                            <select name="branch_id" id="branch_id" class="form-select @error('branch_id') is-invalid @enderror">
                                <option value="">- Pilih Cabang -</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>Cabang tempat user bekerja
                            </div>
                            @error('branch_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Role <span class="text-danger">*</span></label>
                            <div class="border rounded p-3 @error('role_id') is-invalid @enderror" style="max-height: 200px; overflow-y: auto;">
                                @foreach($roles as $role)
                                    <div class="form-check mb-2">
                                        <input type="radio" name="role_id" id="role_{{ $role->id }}" class="form-check-input" 
                                               value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold" for="role_{{ $role->id }}">
                                            {{ $role->name }}
                                        </label>
                                        @if(isset($role->description))
                                            <div class="form-text small text-muted ms-3">
                                                {{ $role->description }}
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>Pilih satu role untuk user ini
                            </div>
                            @error('role_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12">
                            <label for="avatar" class="form-label fw-semibold">
                                Foto Profil
                            </label>
                            <input type="file" name="avatar" id="avatar" class="form-control @error('avatar') is-invalid @enderror" 
                                   accept="image/*" onchange="previewAvatar(this)">
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>Format: JPEG, PNG, JPG, GIF. Maksimal 2MB
                            </div>
                            @error('avatar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="mt-2">
                                <div id="avatar-preview-container" class="d-flex align-items-center justify-content-center bg-light rounded" style="height: 150px; width: 100%; border: 2px dashed #ddd;">
                                    <div class="text-center text-muted" id="avatar-preview-text">
                                        <i class="bi bi-person-circle fs-1 mb-2 d-block"></i>
                                        <small>Preview foto akan tampil di sini</small>
                                    </div>
                                    <img id="avatar-preview" src="#" alt="Preview Foto Baru" class="d-none" style="max-height: 140px; max-width: 95%; object-fit: contain;">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-check">
                                <input type="checkbox" name="is_active" id="is_active" class="form-check-input" 
                                       value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="is_active">
                                    Pengguna Aktif
                                </label>
                                <div class="form-text">
                                    <i class="bi bi-info-circle me-1"></i>Centang untuk mengaktifkan user
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4" style="border-color: var(--primary-orange); opacity: 0.3;">

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary px-4">
                            Batal
                        </a>
                        <button type="submit" class="btn btn-primary px-5">
                            <i class="bi bi-person-plus me-2"></i>Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
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
</style>
@endsection

@push('scripts')
<script>
// Preview avatar image when selected
function previewAvatar(input) {
    const file = input.files[0];
    const preview = document.getElementById('avatar-preview');
    const previewText = document.getElementById('avatar-preview-text');
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.classList.remove('d-none');
            previewText.classList.add('d-none');
        };
        reader.readAsDataURL(file);
    } else {
        preview.classList.add('d-none');
        previewText.classList.remove('d-none');
        preview.src = '#';
    }
}

// Format phone number to ensure it contains only digits
function formatPhoneNumber(input) {
    // Remove any non-numeric characters
    let value = input.value.replace(/\D/g, '');
    
    // Ensure it's only digits
    input.value = value;
    
    // Update the form field with the formatted value
    if (value.length > 0) {
        // Check if it meets the pattern (8-13 digits)
        const isValid = /^\d{8,13}$/.test(value);
        
        // Visual feedback
        if (isValid) {
            input.classList.remove('is-invalid');
            input.classList.add('is-valid');
        } else {
            input.classList.remove('is-valid');
            if (value.length > 0) {
                input.classList.add('is-invalid');
            } else {
                input.classList.remove('is-invalid');
            }
        }
    } else {
        input.classList.remove('is-valid');
        input.classList.remove('is-invalid');
    }
}

// Prepare the phone number before form submission
function preparePhoneNumber() {
    const phoneInput = document.getElementById('phone');
    if (phoneInput && phoneInput.value) {
        // Create a hidden input to store the full number with 62 prefix
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'full_phone';
        hiddenInput.value = '62' + phoneInput.value;
        
        // Replace the original phone value with the full number
        phoneInput.value = '62' + phoneInput.value;
    }
    return true;
}
</script>
@endpush
