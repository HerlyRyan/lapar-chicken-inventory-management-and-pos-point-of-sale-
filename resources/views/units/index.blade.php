@extends('layouts.app')

@section('title', 'Master Satuan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1" style="color: var(--primary-red);">
            <i class="bi bi-rulers me-2"></i>Master Satuan
        </h1>
        <p class="text-muted mb-0">Kelola data satuan untuk pengukuran stok dan produksi</p>
    </div>
    <a href="{{ route('units.create') }}" class="btn btn-primary shadow px-4 d-flex align-items-center" style="min-width: 160px; white-space: nowrap;">
        <i class="bi bi-plus-circle me-2"></i>
        <span>Tambah Satuan</span>
    </a>
</div>

<div class="card border-0 shadow-lg">
    <div class="card-header text-white py-3" style="background: linear-gradient(135deg, #dc2626 0%, #ea580c 50%, #eab308 100%);">
        <h5 class="mb-0 fw-bold">
            <i class="bi bi-list-ul me-2"></i>Daftar Satuan
        </h5>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        <!-- Search Filter -->
        <form method="GET" action="" class="row g-3 mb-4 table-filter-form">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, singkatan atau deskripsi satuan..." class="form-control">
                </div>
            </div>
            <div class="col-md-3">
                <!-- Optional filters can go here -->
                <select name="status" class="form-select">
                    <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>Semua Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                </select>
            </div>
            <div class="col-md-3">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-filter me-1"></i>Filter
                    </button>
                    @if(request('search') || request('status'))
                        <a href="{{ route('units.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-clockwise me-1"></i>Reset
                        </a>
                    @endif
                </div>
            </div>
        </form>

        <!-- Table -->
        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead class="sticky-top">
                    <tr>
                        <th class="text-center" width="5%">#</th>
                        <th>{!! sortColumn('unit_name', 'Nama Satuan', $sortColumn, $sortDirection) !!}</th>
                        <th>{!! sortColumn('abbreviation', 'Singkatan', $sortColumn, $sortDirection) !!}</th>
                        <th>Deskripsi</th>
                        <th>Status</th>
                        <th class="text-center" width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($units as $unit)
                <tr>
                    <td class="text-center fw-bold text-muted">{{ $loop->iteration }}</td>
                    <td>
                        <div class="fw-semibold text-dark">{{ $unit->unit_name }}</div>
                    </td>
                    <td>
                        <span class="badge bg-secondary">{{ $unit->abbreviation }}</span>
                    </td>
                    <td>
                        <span class="text-muted">{{ $unit->description ?: 'Tidak ada deskripsi' }}</span>
                    </td>
                    <td>
                        @if($unit->is_active)
                            <span class="badge bg-success">Aktif</span>
                        @else
                            <span class="badge bg-danger">Nonaktif</span>
                        @endif
                    </td>

                    <td class="text-center">
                        <x-action-buttons
                            :editUrl="route('units.edit', $unit)" 
                            :deleteUrl="route('units.destroy', $unit)"
                            :toggleUrl="route('units.toggle-status', $unit)"
                            :isActive="$unit->is_active"
                            :showToggle="true"
                            itemName="satuan {{$unit->unit_name}}"
                        />
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center py-4 text-muted">
                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                        @if(request('q'))
                            Tidak ada satuan yang ditemukan dengan kata kunci "{{ request('q') }}"
                        @else
                            Belum ada data satuan
                        @endif
                    </td>
                </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-4">
            <div>
                @if($units instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    <p class="text-muted mb-0">Menampilkan {{ $units->firstItem() ?? 0 }}-{{ $units->lastItem() ?? 0 }} dari {{ $units->total() }} data</p>
                @else
                    <p class="text-muted mb-0">Menampilkan {{ count($units) }} data</p>
                @endif
            </div>
            <div>
                @if($units instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    {{ $units->withQueryString()->links() }}
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
