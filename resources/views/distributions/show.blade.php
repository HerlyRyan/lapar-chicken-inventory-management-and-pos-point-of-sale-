@extends('layouts.app')

@section('title', 'Detail Distribusi')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1" style="color: var(--primary-red);">
            <i class="bi bi-truck me-2"></i>Detail Distribusi
        </h1>
        <p class="text-muted mb-0">Detail informasi distribusi</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('distributions.edit', $distribution) }}" class="btn btn-warning">
            <i class="bi bi-pencil-square me-2"></i>Edit
        </a>
        <a href="{{ route('distributions.index') }}" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left me-2"></i>Kembali
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-lg">
            <div class="card-header text-white text-center py-3" style="background: linear-gradient(135deg, #dc2626 0%, #ea580c 50%, #eab308 100%);">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-info-circle me-2"></i>Informasi Distribusi
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <div class="icon-shape bg-primary rounded-3 p-2" style="background: var(--gradient-main) !important;">
                                    <i class="bi bi-hash text-white"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1 fw-semibold text-muted">Nomor Distribusi</h6>
                                <h5 class="mb-0 fw-bold">{{ $distribution->distribution_number }}</h5>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <div class="icon-shape bg-primary rounded-3 p-2" style="background: var(--gradient-main) !important;">
                                    <i class="bi bi-building text-white"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1 fw-semibold text-muted">Cabang Asal</h6>
                                <h5 class="mb-0 fw-bold">{{ $distribution->fromBranch->name ?? '-' }}</h5>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <div class="icon-shape bg-primary rounded-3 p-2" style="background: var(--gradient-main) !important;">
                                    <i class="bi bi-building-add text-white"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1 fw-semibold text-muted">Cabang Tujuan</h6>
                                <h5 class="mb-0 fw-bold">{{ $distribution->toBranch->name ?? '-' }}</h5>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <div class="icon-shape bg-primary rounded-3 p-2" style="background: var(--gradient-main) !important;">
                                    <i class="bi bi-box text-white"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1 fw-semibold text-muted">Bahan Setengah Jadi</h6>
                                <h5 class="mb-0 fw-bold">{{ $distribution->semiFinishedProduct->name ?? '-' }}</h5>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <div class="icon-shape bg-primary rounded-3 p-2" style="background: var(--gradient-main) !important;">
                                    <i class="bi bi-123 text-white"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1 fw-semibold text-muted">Jumlah</h6>
                                <h5 class="mb-0 fw-bold">{{ number_format($distribution->quantity, 0, ',', '.') }} {{ $distribution->semiFinishedProduct->unit ?? 'unit' }}</h5>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <div class="icon-shape bg-primary rounded-3 p-2" style="background: var(--gradient-main) !important;">
                                    <i class="bi bi-calendar text-white"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1 fw-semibold text-muted">Tanggal Distribusi</h6>
                                <h5 class="mb-0 fw-bold">{{ \Carbon\Carbon::parse($distribution->distribution_date)->format('d/m/Y') }}</h5>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <div class="icon-shape bg-primary rounded-3 p-2" style="background: var(--gradient-main) !important;">
                                    <i class="bi bi-flag text-white"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1 fw-semibold text-muted">Status</h6>
                                <h5 class="mb-0 fw-bold">
                                    @switch($distribution->status)
                                        @case('pending')
                                            <span class="badge bg-warning text-dark">Pending</span>
                                            @break
                                        @case('in_transit')
                                            <span class="badge bg-info">Dalam Perjalanan</span>
                                            @break
                                        @case('completed')
                                            <span class="badge bg-success">Selesai</span>
                                            @break
                                        @case('cancelled')
                                            <span class="badge bg-danger">Dibatalkan</span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary">{{ $distribution->status }}</span>
                                    @endswitch
                                </h5>
                            </div>
                        </div>
                    </div>

                    @if($distribution->notes)
                        <div class="col-12">
                            <div class="d-flex align-items-start mb-3">
                                <div class="flex-shrink-0">
                                    <div class="icon-shape bg-primary rounded-3 p-2" style="background: var(--gradient-main) !important;">
                                        <i class="bi bi-chat-text text-white"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1 fw-semibold text-muted">Catatan</h6>
                                    <p class="mb-0">{{ $distribution->notes }}</p>
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
                                <p class="mb-0 small">{{ $distribution->created_at->format('d/m/Y H:i') }}</p>
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
                                <p class="mb-0 small">{{ $distribution->updated_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-lg">
            <div class="card-header text-white text-center py-3" style="background: linear-gradient(135deg, #eab308 0%, #ea580c 50%, #dc2626 100%);">
                <h6 class="mb-0 fw-bold">
                    <i class="bi bi-gear me-2"></i>Aksi
                </h6>
            </div>
            <div class="card-body p-3">
                <div class="d-grid gap-2">
                    <a href="{{ route('distributions.edit', $distribution) }}" class="btn btn-warning">
                        <i class="bi bi-pencil-square me-2"></i>Edit Distribusi
                    </a>
                    
                    <form action="{{ route('distributions.destroy', $distribution) }}" method="POST" 
                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus distribusi ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="bi bi-trash me-2"></i>Hapus Distribusi
                        </button>
                    </form>
                    
                    <hr class="my-2">
                    
                    <a href="{{ route('distributions.index') }}" class="btn btn-outline-primary">
                        <i class="bi bi-arrow-left me-2"></i>Kembali ke Daftar
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
