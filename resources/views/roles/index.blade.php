@extends('layouts.app')

@section('title', 'Role & Hak Akses')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1" style="color: var(--primary-red);">
            <i class="bi bi-shield-check-fill me-2"></i>Role & Hak Akses
        </h1>
        <p class="text-muted mb-0">Kelola role dan permission user</p>
    </div>
    <a href="{{ route('roles.create') }}" class="btn btn-primary shadow px-4 d-flex align-items-center" style="min-width: 160px; white-space: nowrap;">
        <i class="bi bi-plus-circle me-2"></i>
        <span>Tambah Role</span>
    </a>
</div>

<div class="card border-0 shadow-lg">
    <div class="card-header text-white py-3" style="background: linear-gradient(135deg, #dc2626 0%, #ea580c 50%, #eab308 100%);">
        <h5 class="mb-0 fw-bold">
            <i class="bi bi-list-ul me-2"></i>Daftar Role
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
        <form method="GET" action="" class="row g-2 mb-4">
            <div class="col-lg-4 col-md-6">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari role..." class="form-control">
                </div>
            </div>
            <div class="col-lg-2 col-md-3 col-6">
                <select name="is_active" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>
            <div class="col-lg-3 col-md-3 col-6">
                <div class="d-flex gap-1">
                    <button type="submit" class="btn btn-outline-primary flex-fill">
                        <i class="bi bi-search d-lg-none"></i>
                        <span class="d-none d-lg-inline">Cari</span>
                    </button>
                    @if(request('q') || request('is_active') !== null)
                        <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-clockwise"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>

        <!-- Table -->
        <x-standard-table
            :headers="[
                ['text' => '#', 'width' => '5%'],
                ['text' => 'Role'],
                ['text' => 'Users', 'class' => 'd-none d-lg-table-cell'],
                ['text' => 'Permissions', 'class' => 'd-none d-lg-table-cell'],
                ['text' => 'Status', 'class' => 'd-none d-sm-table-cell']
            ]"
            :pagination="$roles"
            :searchable="false"
        >
                    @forelse($roles as $role)
                    <tr>
                        <td>{{ $loop->iteration + ($roles->currentPage() - 1) * $roles->perPage() }}</td>
                        <td>
                            <div class="fw-semibold">{{ $role->name }}</div>
                            <div class="small text-muted">{{ $role->code }}</div>
                            @if($role->description)
                                <div class="small text-muted">{{ $role->description }}</div>
                            @endif
                        </td>
                        <td class="d-none d-lg-table-cell">
                            <span class="fw-semibold">{{ $role->users_count }}</span>
                            <small class="text-muted d-block">users</small>
                        </td>
                        <td class="d-none d-lg-table-cell">
                            <span class="fw-semibold">{{ $role->permissions_count }}</span>
                            <small class="text-muted d-block">permissions</small>
                        </td>
                        <td class="d-none d-sm-table-cell">
                            @if($role->is_active)
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-secondary">Nonaktif</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <x-action-buttons
                                :viewUrl="route('roles.show', $role)" 
                                :editUrl="!$role->isPrimarySuperAdmin() ? route('roles.edit', $role) : null"
                                :deleteUrl="!$role->isPrimarySuperAdmin() ? route('roles.destroy', $role) : null" 
                                :showToggle="false"
                                itemName="role {{$role->name}}"
                            />
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            Belum ada data role
                        </td>
                    </tr>
                    @endforelse
        </x-standard_table>
    </div>
</div>
@endsection
