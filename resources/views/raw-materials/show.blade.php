@extends('layouts.app')

@section('title', 'Detail Bahan Baku - ' . $rawMaterial->name)

@section('content')
{{-- Header --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1" style="color: var(--primary-red);">
            <i class="bi bi-box-seam me-2"></i>Detail Bahan Baku
        </h1>
        <p class="text-muted mb-0">
            Informasi lengkap bahan baku: {{ $rawMaterial->name }}
        </p>
    </div>
    <a href="{{ route('raw-materials.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Kembali
    </a>
</div>

<div class="row">
    {{-- Main Content Column --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-lg mb-4">
            <div class="card-header text-white py-3" style="background: linear-gradient(135deg, #0ea5e9 0%, #06b6d4 50%, #10b981 100%);">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-info-circle me-2"></i>Informasi: {{ $rawMaterial->name }}
                </h5>
            </div>
            <div class="card-body p-4">
                {{-- Image Section --}}
                <div class="text-center mb-4">
                    <x-product-image :src="$rawMaterial->image" :alt="$rawMaterial->name" class="rounded border shadow-sm" height="300" />
                </div>

                <hr>

                {{-- Product Details Section --}}
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        @if($rawMaterial->code)
                            <p class="text-muted mb-2">
                                <i class="bi bi-upc me-1"></i>Kode: <span class="fw-semibold">{{ $rawMaterial->code }}</span>
                            </p>
                        @endif
                    </div>
                    @if($rawMaterial->is_active)
                        <span class="badge bg-success fs-6 px-3 py-2">
                            <i class="bi bi-check-circle me-1"></i>Aktif
                        </span>
                    @else
                        <span class="badge bg-secondary fs-6 px-3 py-2">
                            <i class="bi bi-x-circle me-1"></i>Tidak Aktif
                        </span>
                    @endif
                </div>

                @if($rawMaterial->description)
                    <p class="mb-3 fst-italic text-muted">"{{ $rawMaterial->description }}"</p>
                @endif

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-dark fw-bold">Kode</label>
                        <h5><span class="badge bg-primary">{{ $rawMaterial->code }}</span></h5>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-dark fw-bold">Kategori</label>
                        @if($rawMaterial->category)
                            <h5><span class="badge bg-info">{{ $rawMaterial->category->name }}</span></h5>
                        @else
                            <h5 class="fw-normal text-muted">-</h5>
                        @endif
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-dark fw-bold">Satuan</label>
                        <h5 class="fw-normal">{{ $rawMaterial->unit ? (is_object($rawMaterial->unit) ? $rawMaterial->unit->unit_name : $rawMaterial->unit) : '-' }}</h5>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-dark fw-bold">Supplier</label>
                        <h5 class="fw-normal">{{ $rawMaterial->supplier ? $rawMaterial->supplier->name : '-' }}</h5>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-dark fw-bold">Harga Satuan</label>
                        <h5 class="fw-bold text-success">
                            @if($rawMaterial->unit_price)
                                Rp {{ number_format($rawMaterial->unit_price, 0, ',', '.') }}
                            @else
                                -
                            @endif
                        </h5>
                    </div>
                </div>

                <hr>

                {{-- Stock Info Section --}}
                <h6 class="fw-bold mb-3"><i class="bi bi-archive me-2"></i>Informasi Stok</h6>
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label text-muted fw-semibold">Stok Saat Ini</label>
                        @php
                            $currentStock = $rawMaterial->current_stock ?? 0;
                            $minStock = $rawMaterial->minimum_stock ?? 0;
                            $isLowStock = $currentStock < $minStock;
                        @endphp
                        <div class="d-flex align-items-center">
                            <span class="fw-bold fs-4 {{ $isLowStock ? 'text-danger' : 'text-success' }}">
                                {{ number_format($currentStock, 0) }}
                            </span>
                            <small class="text-muted ms-2">{{ is_object($rawMaterial->unit) ? $rawMaterial->unit->unit_name : '' }}</small>
                            @if($isLowStock)
                                <i class="bi bi-exclamation-triangle-fill text-warning ms-2" title="Stok rendah"></i>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted fw-semibold">Stok Minimum</label>
                        <p class="fw-semibold mb-0">
                            {{ number_format($minStock, 0) }}
                            <small class="text-muted">{{ is_object($rawMaterial->unit) ? $rawMaterial->unit->unit_name : '' }}</small>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Action & System Info Column --}}
    <div class="col-lg-4">
        {{-- Actions Card --}}
        <div class="card border-0 shadow-lg mb-4">
            <div class="card-header bg-light py-3">
                <h6 class="mb-0 fw-bold text-dark">
                    <i class="bi bi-lightning-charge-fill me-2"></i>Aksi
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('raw-materials.edit', $rawMaterial) }}" class="btn btn-warning">
                        <i class="bi bi-pencil me-2"></i>Edit Bahan Baku
                    </a>
                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        <i class="bi bi-trash me-2"></i>Hapus Bahan Baku
                    </button>
                </div>
            </div>
        </div>

        {{-- System Info Card --}}
        <div class="card border-0 shadow-lg">
            <div class="card-header bg-light py-3">
                <h6 class="mb-0 fw-bold text-dark">
                    <i class="bi bi-clock-history me-2"></i>Informasi Sistem
                </h6>
            </div>
            <div class="card-body text-muted small">
                <p class="mb-2">
                    <strong>Dibuat:</strong><br>
                    {{ $rawMaterial->created_at ? $rawMaterial->created_at->format('d/m/Y H:i:s') : '-' }}
                </p>
                <p class="mb-0">
                    <strong>Diperbarui:</strong><br>
                    {{ $rawMaterial->updated_at ? $rawMaterial->updated_at->format('d/m/Y H:i:s') : '-' }}
                </p>
            </div>
        </div>
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin menghapus bahan baku <strong>{{ $rawMaterial->name }}</strong>? Tindakan ini tidak dapat dibatalkan.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form action="{{ route('raw-materials.destroy', $rawMaterial) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
