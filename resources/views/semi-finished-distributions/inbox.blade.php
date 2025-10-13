@extends('layouts.app')

@section('title', 'Kotak Masuk Distribusi Cabang')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="bi bi-inbox text-info me-2"></i>
                Kotak Masuk Distribusi Cabang
            </h1>
            <p class="text-muted small mb-0">
                Menampilkan distribusi berstatus <span class="badge bg-warning text-dark">Dikirim</span> ke cabang Anda.
            </p>
        </div>
        <div class="text-end">
            <div class="small text-muted">Cabang aktif:</div>
            <div class="fw-bold">{{ $branch->name ?? 'Tidak dipilih' }}</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('semi-finished-distributions.inbox') }}">
                <input type="hidden" name="branch_id" value="{{ $branchId }}" />
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="search" class="form-label">Pencarian</label>
                        <input type="text" name="search" id="search" class="form-control"
                               placeholder="Cari berdasarkan kode distribusi..."
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <div class="d-grid w-100">
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="bi bi-search me-1"></i>
                                Filter
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6 d-flex align-items-end justify-content-end">
                        <a href="{{ route('semi-finished-distributions.index') }}" class="btn btn-light me-2">
                            <i class="bi bi-list-ul me-1"></i> Semua Distribusi
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Inbox Table -->
    <div class="card shadow">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="bi bi-truck me-2"></i>
                Distribusi Masuk (Dikirim)
            </h6>
            <span class="badge bg-info">{{ $distributions->total() }} data</span>
        </div>
        <div class="card-body">
            @if($distributions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Kode</th>
                                <th>Produk</th>
                                <th>Jumlah</th>
                                <th>Dikirim Oleh</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($distributions as $distribution)
                            <tr class="table-warning">
                                <td class="align-middle">
                                    <strong>{{ $distribution->distribution_code }}</strong>
                                    <span class="badge bg-warning text-dark ms-1">BARU</span>
                                </td>
                                <td class="align-middle">
                                    {{ $distribution->semiFinishedProduct->name ?? '-' }}
                                    <div class="small text-muted">{{ $distribution->semiFinishedProduct->unit ?? '' }}</div>
                                </td>
                                <td class="align-middle">
                                    {{ number_format((float) $distribution->quantity_sent, 0, ',', '.') }}
                                </td>
                                <td class="align-middle">
                                    {{ $distribution->sentBy->name ?? '-' }}
                                </td>
                                <td class="align-middle">
                                    <div class="small">
                                        <div class="fw-bold">{{ $distribution->distribution_date->format('d/m/Y') }}</div>
                                        <div class="text-muted">{{ $distribution->created_at->format('H:i') }}</div>
                                    </div>
                                </td>
                                <td class="align-middle">
                                    <div class="btn-group-vertical" role="group">
                                        <a href="{{ route('semi-finished-distributions.show', $distribution) }}" 
                                           class="btn btn-sm btn-outline-info mb-1" title="Lihat Detail">
                                            <i class="bi bi-eye me-1"></i>
                                            Detail
                                        </a>

                                        @if($distribution->status === 'sent')
                                            <button type="button" class="btn btn-sm btn-success mb-1"
                                                    onclick="showAcceptModal({{ $distribution->id }})" title="Terima Distribusi">
                                                <i class="bi bi-check-circle me-1"></i>
                                                Terima
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger"
                                                    onclick="showRejectModal({{ $distribution->id }})" title="Tolak Distribusi">
                                                <i class="bi bi-x-circle me-1"></i>
                                                Tolak
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted small">
                        Menampilkan {{ $distributions->firstItem() }} - {{ $distributions->lastItem() }} 
                        dari {{ $distributions->total() }} distribusi
                    </div>
                    {{ $distributions->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-inbox text-muted" style="font-size: 4rem;"></i>
                    <h5 class="mt-3 text-muted">Tidak Ada Distribusi Masuk</h5>
                    <p class="text-muted">Belum ada distribusi baru untuk cabang Anda.</p>
                </div>
            @endif
        </div>
    </div>
</div>

@include('semi-finished-distributions.partials.accept_reject_modals')

@push('styles')
<style>
    .btn-group-vertical .btn { margin-bottom: 2px; }
    .btn-group-vertical .btn:last-child { margin-bottom: 0; }
</style>
@endpush
@endsection
