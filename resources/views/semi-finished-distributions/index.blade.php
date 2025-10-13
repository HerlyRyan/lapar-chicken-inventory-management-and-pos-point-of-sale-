@extends('layouts.app')

@section('title', 'Distribusi Bahan Setengah Jadi')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="bi bi-truck text-info me-2"></i>
                Distribusi Bahan Setengah Jadi
            </h1>
            <p class="text-muted small mb-0">Kelola distribusi dari pusat produksi ke cabang</p>
        </div>
        <div>
            <a href="{{ route('semi-finished-distributions.create') }}" class="btn btn-primary">
                <i class="bi bi-plus me-1"></i>
                Buat Distribusi Baru
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Menunggu Konfirmasi
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $distributions->where('status', 'pending')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Diterima
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $distributions->where('status', 'accepted')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Ditolak
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $distributions->where('status', 'rejected')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-x-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Distribusi
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $distributions->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-list-ul fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('semi-finished-distributions.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu Konfirmasi</option>
                            <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>Diterima</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="branch_id" class="form-label">Cabang</label>
                        <select name="branch_id" id="branch_id" class="form-select">
                            <option value="">Semua Cabang</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="search" class="form-label">Pencarian</label>
                        <input type="text" name="search" id="search" class="form-control" 
                               placeholder="Cari berdasarkan kode distribusi..." 
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="bi bi-search me-1"></i>
                                Filter
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Distributions Table -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="bi bi-list-ul me-2"></i>
                Daftar Distribusi
            </h6>
        </div>
        <div class="card-body">
            @if($distributions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Kode Distribusi</th>
                                <th>Cabang Tujuan</th>
                                <th>Produk</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                                <th>Diproses Oleh</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($distributions as $distribution)
                            <tr class="{{ $distribution->isPending() ? 'table-warning' : ($distribution->isAccepted() ? 'table-success' : 'table-danger') }}">
                                <td>
                                    <strong>{{ $distribution->distribution_code }}</strong>
                                    @if($distribution->isPending())
                                        <span class="badge bg-warning text-dark ms-1">BARU</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $distribution->branch->name ?? '' }}</div>
                                    <small class="text-muted">{{ Str::limit($distribution->branch->address ?? '', 30) }}</small>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ $distribution->items->count() }} produk
                                        @if($distribution->items->count() > 0)
                                            <br>
                                            @foreach($distribution->items->take(2) as $item)
                                                • {{ Str::limit($item->semiFinishedProduct->name ?? '', 15) }}<br>
                                            @endforeach
                                            @if($distribution->items->count() > 2)
                                                <span class="text-primary">+{{ $distribution->items->count() - 2 }} lainnya</span>
                                            @endif
                                        @endif
                                    </small>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $distribution->status_color }} fs-6">
                                        {{ $distribution->status_label }}
                                    </span>
                                </td>
                                <td>
                                    <div class="small">
                                        <div class="fw-bold">{{ $distribution->distribution_date->format('d/m/Y') }}</div>
                                        <div class="text-muted">{{ $distribution->created_at->format('H:i') }}</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="small">
                                        @if($distribution->isAccepted() && $distribution->accepted_by)
                                            <div class="text-success fw-bold">
                                                <i class="bi bi-person-check me-1"></i>
                                                Diterima oleh
                                            </div>
                                            <div class="text-muted">{{ $distribution->acceptedBy->name ?? '' }}</div>
                                            <div class="text-muted">{{ $distribution->accepted_at->format('d/m/Y H:i') }}</div>
                                        @elseif($distribution->isRejected() && $distribution->rejected_by)
                                            <div class="text-danger fw-bold">
                                                <i class="bi bi-person-x me-1"></i>
                                                Ditolak oleh
                                            </div>
                                            <div class="text-muted">{{ $distribution->rejectedBy->name ?? '' }}</div>
                                            <div class="text-muted">{{ $distribution->rejected_at->format('d/m/Y H:i') }}</div>
                                        @else
                                            <div class="text-muted">
                                                <i class="bi bi-clock me-1"></i>
                                                Menunggu konfirmasi
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group-vertical" role="group">
                                        <a href="{{ route('semi-finished-distributions.show', $distribution) }}" 
                                           class="btn btn-sm btn-outline-info mb-1" title="Lihat Detail">
                                            <i class="bi bi-eye me-1"></i>
                                            Detail
                                        </a>
                                        
                                        @if($distribution->isPending())
                                            {{-- Aksi terima/tolak dipindah ke Kotak Masuk Distribusi (halaman inbox) --}}
                                            @if(Route::has('semi-finished-distributions.edit'))
                                                <a href="{{ route('semi-finished-distributions.edit', $distribution) }}" 
                                                   class="btn btn-sm btn-outline-warning mb-1" title="Edit Distribusi">
                                                    <i class="bi bi-pencil me-1"></i>
                                                    Edit
                                                </a>
                                            @endif
                                            @if(Route::has('semi-finished-distributions.destroy'))
                                                <button type="button" class="btn btn-sm btn-outline-danger" 
                                                        onclick="showDeleteModal({{ $distribution->id }})" title="Hapus Distribusi">
                                                    <i class="bi bi-trash me-1"></i>
                                                    Hapus
                                                </button>
                                            @endif
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
                    <i class="bi bi-truck text-muted" style="font-size: 4rem;"></i>
                    <h5 class="mt-3 text-muted">Tidak Ada Distribusi</h5>
                    <p class="text-muted">
                        @if(request()->hasAny(['status', 'branch_id', 'search']))
                            Tidak ada distribusi yang sesuai dengan filter yang dipilih.
                        @else
                            Belum ada distribusi bahan setengah jadi yang dibuat.
                        @endif
                    </p>
                    <a href="{{ route('semi-finished-distributions.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus me-1"></i>
                        Buat Distribusi Pertama
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Modals terima/tolak hanya ditampilkan di halaman Kotak Masuk Distribusi (inbox) --}}

<!-- Delete Distribution Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Hapus Distribusi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Perhatian!</strong> Distribusi yang sudah dihapus tidak dapat dikembalikan.
                    </div>
                    <p>Apakah Anda yakin ingin menghapus distribusi ini?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-1"></i>
                        Hapus Distribusi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    .border-left-warning {
        border-left: 0.25rem solid #f6c23e !important;
    }
    .border-left-success {
        border-left: 0.25rem solid #1cc88a !important;
    }
    .border-left-danger {
        border-left: 0.25rem solid #e74a3b !important;
    }
    .border-left-info {
        border-left: 0.25rem solid #36b9cc !important;
    }
    
    .table-warning {
        background-color: rgba(255, 193, 7, 0.1);
    }
    
    .table-success {
        background-color: rgba(25, 135, 84, 0.1);
    }
    
    .table-danger {
        background-color: rgba(220, 53, 69, 0.1);
    }
    
    .btn-group-vertical .btn {
        margin-bottom: 2px;
    }
    
    .btn-group-vertical .btn:last-child {
        margin-bottom: 0;
    }
</style>
@endpush

@push('scripts')
<script>
function showDeleteModal(distributionId) {
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    const form = document.getElementById('deleteForm');
    form.action = `/semi-finished-distributions/${distributionId}`;
    modal.show();
}
</script>
@endpush
@endsection
