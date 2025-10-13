@extends('layouts.app')

@section('title', 'Master Cabang')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1" style="color: var(--primary-red);">
            <i class="bi bi-building me-2"></i>Data Cabang
        </h1>
        <p class="text-muted mb-0">Kelola data cabang dan lokasi usaha</p>
    </div>
    <a href="{{ route('branches.create') }}" class="btn btn-primary shadow px-4 d-flex align-items-center" style="min-width: 160px; white-space: nowrap;">
        <i class="bi bi-plus-circle me-2"></i>
        <span>Tambah Cabang</span>
    </a>
</div>

<div class="card border-0 shadow-lg">
    <div class="card-header text-white py-3" style="background: linear-gradient(135deg, #dc2626 0%, #ea580c 50%, #eab308 100%);">
        <h5 class="mb-0 fw-bold">
            <i class="bi bi-list-ul me-2"></i>Daftar Cabang
        </h5>
    </div>
    <div class="card-body">
        <!-- Filter Form -->
        <form action="{{ route('branches.index') }}" method="GET" class="row g-3 mb-4">
            <!-- Search field -->
            <div class="col-md-6">
                <div class="form-floating">
                    <input type="text" class="form-control" id="search" name="search" placeholder="Cari cabang..." value="{{ request('search') }}">
                    <label for="search">Cari (nama, alamat, telepon, kode)</label>
                </div>
            </div>
            
            <!-- Type filter -->
            <div class="col-md-3">
                <div class="form-floating">
                    <select class="form-select" id="type" name="type">
                        <option value="">Semua Tipe</option>
                        <option value="branch" {{ request('type') == 'branch' ? 'selected' : '' }}>Cabang Retail</option>
                        <option value="production" {{ request('type') == 'production' ? 'selected' : '' }}>Pusat Produksi</option>
                    </select>
                    <label for="type">Tipe Cabang</label>
                </div>
            </div>
            
            <!-- Status filter -->
            <div class="col-md-3">
                <div class="form-floating">
                    <select class="form-select" id="status" name="status">
                        <option value="">Semua Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                    <label for="status">Status</label>
                </div>
            </div>
            
            <!-- Action buttons -->
            <div class="col-12">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-filter me-1"></i> Filter
                </button>
                <a href="{{ route('branches.index') }}" class="btn btn-outline-secondary ms-2">
                    <i class="bi bi-x-circle me-1"></i> Reset
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover border">
                <thead class="table-light">
                    <tr>
                        <th scope="col" width="50">#</th>
                        <th scope="col">{!! sortColumn('name', 'Nama Cabang', $sortColumn, $sortDirection) !!}</th>
                        <th scope="col">{!! sortColumn('code', 'Kode', $sortColumn, $sortDirection) !!}</th>
                        <th scope="col">{!! sortColumn('type', 'Tipe', $sortColumn, $sortDirection) !!}</th>
                        <th scope="col">Alamat</th>
                        <th scope="col">Telepon</th>
                        <th scope="col">Status</th>
                        <th scope="col" class="text-center" width="150">Aksi</th>
                    </tr>
                </thead>
                <tbody>
    @forelse($branches as $branch)
        <tr>
            <td class="text-center">{{ $loop->iteration + ($branches->currentPage() - 1) * $branches->perPage() }}</td>
            <td>
                <div class="d-flex align-items-center">
                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3" 
                         style="width: 40px; height: 40px;">
                        <i class="bi bi-shop text-white"></i>
                    </div>
                    <div>
                        <div class="fw-semibold">{{ $branch->name }}</div>
                        <small class="text-muted">ID: {{ $branch->id }}</small>
                    </div>
                </div>
            </td>
            <td>{{ $branch->code }}</td>
            <td>{{ $branch->type == 'branch' ? 'Cabang Retail' : 'Pusat Produksi' }}</td>
            <td>{{ $branch->address }}</td>
            <td>{{ $branch->phone }}</td>
            <td>
                <span class="badge bg-{{ $branch->is_active ? 'success' : 'danger' }}">
                    {{ $branch->is_active ? 'Aktif' : 'Tidak Aktif' }}
                </span>
            </td>
            <td class="text-center">
                <x-action-buttons
                    :viewUrl="route('branches.show', $branch->id)" 
                    :editUrl="route('branches.edit', $branch->id)"
                    :deleteUrl="route('branches.destroy', $branch->id)" 
                    :toggleUrl="route('branches.toggle-status', $branch->id)"
                    :isActive="$branch->is_active"
                    :showToggle="true"
                    itemName="cabang {{$branch->name}}"
                />
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="8" class="text-center py-4">
                <div class="d-flex flex-column align-items-center">
                    <i class="bi bi-inbox fs-1 text-muted mb-2"></i>
                    <h5>Data cabang tidak ditemukan</h5>
                    <p class="text-muted">Belum ada data cabang atau hasil filter tidak ditemukan</p>
                </div>
            </td>
        </tr>
    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($branches->hasPages())
            <div class="px-4 py-3 border-top">
                {{ $branches->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
