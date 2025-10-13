@extends('layouts.app')

@section('title', 'Detail Hak Akses')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1" style="color: var(--primary-red);">
            <i class="bi bi-shield-check me-2"></i>Detail Hak Akses
        </h1>
        <p class="text-muted mb-0">Detail informasi hak akses</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('permissions.edit', $permission) }}" class="btn btn-warning">
            <i class="bi bi-pencil me-2"></i>Edit
        </a>
        <a href="{{ route('permissions.index') }}" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left me-2"></i>Kembali
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-lg">
            <div class="card-header text-white py-3" style="background: linear-gradient(135deg, #dc2626 0%, #ea580c 50%, #eab308 100%);">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-info-circle me-2"></i>Informasi Hak Akses
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <div class="icon-shape bg-primary rounded-3 p-2" style="background: var(--gradient-main) !important;">
                                    <i class="bi bi-shield-check text-white"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1 fw-semibold text-muted">Nama Permission</h6>
                                <h5 class="mb-0 fw-bold">{{ $permission->name }}</h5>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <div class="icon-shape bg-primary rounded-3 p-2" style="background: var(--gradient-main) !important;">
                                    <i class="bi bi-hash text-white"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1 fw-semibold text-muted">Kode Permission</h6>
                                <h5 class="mb-0 fw-bold">{{ $permission->code ?? '-' }}</h5>
                            </div>
                        </div>
                    </div>

                    @if($permission->description)
                        <div class="col-12">
                            <div class="d-flex align-items-start mb-3">
                                <div class="flex-shrink-0">
                                    <div class="icon-shape bg-primary rounded-3 p-2" style="background: var(--gradient-main) !important;">
                                        <i class="bi bi-chat-text text-white"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1 fw-semibold text-muted">Deskripsi</h6>
                                    <p class="mb-0">{{ $permission->description }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="col-md-6">
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <div class="icon-shape bg-success rounded-3 p-2">
                                    <i class="bi bi-clock text-white"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1 fw-semibold text-muted">Dibuat</h6>
                                <p class="mb-0 small">{{ $permission->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <div class="icon-shape bg-info rounded-3 p-2">
                                    <i class="bi bi-arrow-clockwise text-white"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1 fw-semibold text-muted">Terakhir Update</h6>
                                <p class="mb-0 small">{{ $permission->updated_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-lg">
            <div class="card-header text-white py-3" style="background: linear-gradient(135deg, #eab308 0%, #ea580c 50%, #dc2626 100%);">
                <h6 class="mb-0 fw-bold">
                    <i class="bi bi-gear me-2"></i>Aksi
                </h6>
            </div>
            <div class="card-body p-3">
                <div class="d-grid gap-2">
                    <a href="{{ route('permissions.edit', $permission) }}" class="btn btn-warning">
                        <i class="bi bi-pencil me-2"></i>Edit Hak Akses
                    </a>
                    
                    <form action="{{ route('permissions.destroy', $permission) }}" method="POST" 
                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus hak akses ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="bi bi-trash me-2"></i>Hapus Hak Akses
                        </button>
                    </form>
                    
                    <hr class="my-2">
                    
                    <a href="{{ route('permissions.index') }}" class="btn btn-outline-primary">
                        <i class="bi bi-arrow-left me-2"></i>Kembali ke Daftar
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
