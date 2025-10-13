@extends('layouts.app')

@section('title', 'Detail Distribusi Bahan Setengah Jadi')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="bi bi-receipt text-primary me-2"></i>
                Detail Distribusi
            </h1>
            <p class="text-muted small mb-0">Kode: {{ $distribution->distribution_code }}</p>
        </div>
        <div>
            <a href="{{ route('semi-finished-distributions.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>
                Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="bi bi-info-circle me-2"></i>
                        Informasi Distribusi
                    </h6>
                    <span class="badge bg-{{ $distribution->status === 'sent' ? 'warning' : ($distribution->status === 'accepted' ? 'success' : 'danger') }} text-uppercase">
                        {{ strtoupper($distribution->status) }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="mb-2">
                                <small class="text-muted d-block">Cabang Tujuan</small>
                                <div class="fw-bold">{{ $distribution->targetBranch->name ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-2">
                                <small class="text-muted d-block">Dikirim Oleh</small>
                                <div class="fw-bold">{{ $distribution->sentBy->name ?? '-' }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="mb-2">
                                <small class="text-muted d-block">Produk</small>
                                <div class="fw-bold">{{ $distribution->semiFinishedProduct->name ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-2">
                                <small class="text-muted d-block">Jumlah</small>
                                <div class="fw-bold">{{ number_format($distribution->quantity_sent, 0, ',', '.') }} {{ $distribution->semiFinishedProduct->unit->name ?? 'unit' }}</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-2">
                                <small class="text-muted d-block">Total Biaya</small>
                                <div class="fw-bold">Rp {{ number_format($distribution->total_cost, 0, ',', '.') }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Catatan Distribusi</small>
                        <div class="fw-normal">{{ $distribution->distribution_notes ?: '-' }}</div>
                    </div>

                    @if($distribution->status !== 'sent')
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <small class="text-muted d-block">Ditangani Oleh</small>
                                    <div class="fw-bold">{{ $distribution->handledBy->name ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <small class="text-muted d-block">Tanggal Tanggapan</small>
                                    <div class="fw-bold">{{ optional($distribution->handled_at)->format('d M Y H:i') ?: '-' }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted d-block">Catatan Tanggapan</small>
                            <div class="fw-normal">{{ $distribution->response_notes ?: '-' }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-secondary">
                        <i class="bi bi-clock-history me-2"></i>
                        Waktu
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <small class="text-muted d-block">Dibuat</small>
                        <div class="fw-bold">{{ optional($distribution->created_at)->format('d M Y H:i') }}</div>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted d-block">Diperbarui</small>
                        <div class="fw-bold">{{ optional($distribution->updated_at)->format('d M Y H:i') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
