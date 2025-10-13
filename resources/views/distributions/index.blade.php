@extends('layouts.app')

@section('title', 'Distribusi Produk')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1" style="color: var(--primary-red);">
            <i class="bi bi-truck me-2"></i>Distribusi Produk
        </h1>
        <p class="text-muted mb-0">Kelola distribusi produk antar cabang</p>
    </div>
    <a href="{{ route('distributions.create') }}" class="btn btn-primary shadow">
        <i class="bi bi-plus-circle me-2"></i>Buat Distribusi
    </a>
</div>

<div class="card border-0 shadow-lg">
    <div class="card-header text-white py-3" style="background: linear-gradient(135deg, #dc2626 0%, #ea580c 50%, #eab308 100%);">
        <h5 class="mb-0 fw-bold">
            <i class="bi bi-list-ul me-2"></i>Daftar Distribusi Produk
        </h5>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Filter Form -->
        <form method="GET" action="" class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nomor distribusi, produk, atau cabang..." class="form-control">
                </div>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="in_transit" {{ request('status') == 'in_transit' ? 'selected' : '' }}>In Transit</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" name="date_from" value="{{ request('date_from') }}" placeholder="Dari tanggal" class="form-control">
            </div>
            <div class="col-md-2">
                <input type="date" name="date_to" value="{{ request('date_to') }}" placeholder="Sampai tanggal" class="form-control">
            </div>
            <div class="col-md-2">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-search me-1"></i>Cari
                    </button>
                    @if(request('q') || request('status') || request('date_from') || request('date_to'))
                        <a href="{{ route('distributions.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-clockwise me-1"></i>Reset
                        </a>
                    @endif
                </div>
            </div>
        </form>

        <!-- Table -->
        <div class="standard-table-container">
            <table class="standard-table table table-hover align-middle">
                <thead class="standard-table-header table-light">
                    <tr>
                        <th width="60">#</th>
                        <th>No. Distribusi</th>
                        <th>Dari → Ke</th>
                        <th>Produk</th>
                        <th>Jumlah</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th width="120" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($distributions as $dist)
                    <tr>
                        <td>{{ $loop->iteration + ($distributions->currentPage() - 1) * $distributions->perPage() }}</td>
                        <td>
                            <div class="fw-semibold">{{ $dist->distribution_number }}</div>
                            <small class="text-muted">{{ $dist->created_at->format('d/m/Y H:i') }}</small>
                        </td>
                        <td>
                            <div>
                                <span class="fw-semibold text-primary">{{ $dist->fromBranch->name }}</span>
                                <div class="my-1">
                                    <i class="bi bi-arrow-down" style="color: #ea580c;"></i>
                                </div>
                                <span class="fw-semibold text-success">{{ $dist->toBranch->name }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $dist->semiFinishedProduct->name }}</div>
                            <small class="text-muted">{{ $dist->semiFinishedProduct->code }}</small>
                        </td>
                        <td>
                            <span class="fw-semibold">{{ number_format($dist->quantity, 0, ',', '.') }}</span>
                            <small class="text-muted d-block">{{ $dist->semiFinishedProduct->unit }}</small>
                        </td>
                        <td>
                            <div>{{ $dist->distribution_date->format('d/m/Y') }}</div>
                            <small class="text-muted">{{ $dist->distribution_date->format('H:i') }}</small>
                        </td>
                        <td>
                            @switch($dist->status)
                                @case('pending')
                                    <span class="badge bg-warning">Pending</span>
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
                                    <span class="badge bg-secondary">{{ $dist->status }}</span>
                            @endswitch
                        </td>
                        <td class="text-center">
                            <x-action-buttons
                                :viewUrl="route('distributions.show', $dist)" 
                                :editUrl="$dist->status !== 'completed' && $dist->status !== 'cancelled' ? route('distributions.edit', $dist) : null"
                                :deleteUrl="$dist->status !== 'completed' && $dist->status !== 'cancelled' ? route('distributions.destroy', $dist) : null" 
                                :showToggle="false"
                                itemName="distribusi {{$dist->distribution_number}}"
                            />
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            Belum ada data distribusi produk
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($distributions->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $distributions->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
    .table-responsive thead th {
        position: sticky;
        top: 0;
        z-index: 1;
        background-color: #f8f9fa; /* Match table-light bg color */
        box-shadow: inset 0 -2px 0 #dee2e6; /* Optional: to add a bottom border */
    }
</style>
@endpush
