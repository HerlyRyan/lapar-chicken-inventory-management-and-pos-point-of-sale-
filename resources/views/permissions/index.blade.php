@extends('layouts.app')

@section('title', 'Master Hak Akses')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1" style="color: var(--primary-red);">
            <i class="bi bi-shield-check me-2"></i>Master Hak Akses
        </h1>
        <p class="text-muted mb-0">Kelola hak akses dan permission sistem</p>
    </div>
    <a href="{{ route('permissions.create') }}" class="btn btn-primary shadow px-4 d-flex align-items-center" style="min-width: 160px; white-space: nowrap;">
        <i class="bi bi-plus-circle me-2"></i>
        <span>Tambah Hak Akses</span>
    </a>
</div>

<div class="card border-0 shadow-lg">
    <div class="card-header text-white py-3" style="background: linear-gradient(135deg, #dc2626 0%, #ea580c 50%, #eab308 100%);">
        <h5 class="mb-0 fw-bold">
            <i class="bi bi-list-ul me-2"></i>Daftar Hak Akses
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
        <form method="GET" action="" class="row g-3 mb-4">
            <div class="col-md-8">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama hak akses..." class="form-control">
                </div>
            </div>
            <div class="col-md-4">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-search me-1"></i>Cari
                    </button>
                    @if(request('q'))
                        <a href="{{ route('permissions.index') }}" class="btn btn-outline-secondary">
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
                        <th class="text-center" width="5%">#</th>
                        <th>Nama Hak Akses</th>
                        <th class="text-center" width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($permissions as $permission)
                <tr>
                    <td class="text-center fw-bold text-muted">{{ $loop->iteration }}</td>
                    <td>
                        <div class="fw-semibold text-dark">{{ $permission->name }}</div>
                    </td>
                    <td class="text-center">
                        <x-action-buttons
                            :viewUrl="route('permissions.show', $permission)" 
                            :editUrl="route('permissions.edit', $permission)"
                            :deleteUrl="route('permissions.destroy', $permission)" 
                            :showToggle="false"
                            itemName="hak akses {{$permission->name}}"
                        />
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="text-center py-4 text-muted">
                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                        @if(request('q'))
                            Tidak ada hak akses yang ditemukan dengan kata kunci "{{ request('q') }}"
                        @else
                            Belum ada data hak akses
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>

        @if($permissions->hasPages())
            <div class="d-flex justify-content-center">
                {{ $permissions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
