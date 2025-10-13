@extends('layouts.app')

@section('title', 'Detail Role - ' . $role->name)

@section('content')
<x-detail-page-header 
    title="{{ $role->name }}"
    subtitle="Detail role dan permission"
    icon="shield-check-fill"
    :backRoute="route('roles.index')"
    :editRoute="route('roles.edit', $role)"
/>

<div class="row">
    <!-- Role Info -->
    <div class="col-lg-8">
        <x-detail-info-card title="Informasi Role" icon="info-circle">

                <div class="mb-3">
                    <label class="form-label fw-semibold text-muted">Nama Role</label>
                    <div class="fw-semibold">{{ $role->name }}</div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-semibold text-muted">Kode</label>
                    <div class="fw-semibold">
                        <code class="bg-light text-dark p-1 rounded">{{ $role->code }}</code>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-semibold text-muted">Status</label>
                    <div class="fw-semibold">
                        @if($role->is_active)
                            <span class="badge bg-success">Aktif</span>
                        @else
                            <span class="badge bg-secondary">Nonaktif</span>
                        @endif
                    </div>
                </div>
                
                @if($role->description)
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-muted">Deskripsi</label>
                        <div>{{ $role->description }}</div>
                    </div>
                @endif
                
                <div class="mb-3">
                    <label class="form-label fw-semibold text-muted">Dibuat</label>
                    <div>{{ $role->created_at->format('d M Y H:i') }}</div>
                </div>
                
                <div class="mb-0">
                    <label class="form-label fw-semibold text-muted">Diperbarui</label>
                    <div>{{ $role->updated_at->format('d M Y H:i') }}</div>
                </div>
        </x-detail-info-card>
    </div>
    
    <div class="col-lg-4">
        <x-detail-action-card
            title="Aksi"
            icon="gear"
            :editRoute="route('roles.edit', $role)"
            :deleteRoute="route('roles.destroy', $role)"
            deleteMessage="Apakah Anda yakin ingin menghapus role '{{ $role->name }}'?"
            itemName="role"
            class="mb-4"
            :backRoute="null"
        />
        
        <x-detail-system-info-card :model="$role" />
    </div>
</div>

<!-- Users with this role -->
<div class="row mt-4">
    <div class="col-lg-6">
        <x-detail-info-card title="Users ({{ $role->users->count() }})" icon="people" gradient="info">
                @if($role->users->count() > 0)
                    <div class="row">
                        @foreach($role->users as $user)
                            <div class="col-md-12 mb-3">
                                <div class="d-flex align-items-center p-2 bg-light rounded">
                                    <div class="avatar-circle me-3">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-semibold">{{ $user->name }}</div>
                                        <small class="text-muted">{{ $user->email }}</small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center text-muted py-3">
                        <i class="bi bi-person-x fs-2 d-block mb-2"></i>
                        Belum ada user dengan role ini
                    </div>
                @endif
        </x-detail-info-card>
    </div>
    
    <!-- Permissions -->
    <div class="col-lg-6">
        <x-detail-info-card title="Permission ({{ $role->permissions->count() }})" icon="key-fill" gradient="success">
                @if($role->permissions->count() > 0)
                    @php
                        $groupedPermissions = $role->permissions->groupBy('category');
                    @endphp
                    
                    @foreach($groupedPermissions as $category => $categoryPermissions)
                        <div class="mb-4">
                            <h6 class="fw-bold text-primary mb-3">
                                <i class="bi bi-folder me-1"></i>{{ $category }}
                                <span class="badge bg-primary ms-1">{{ $categoryPermissions->count() }}</span>
                            </h6>
                            <div class="row">
                                @foreach($categoryPermissions as $permission)
                                    <div class="col-lg-6 col-md-12 mb-2">
                                        <div class="d-flex align-items-start">
                                            <i class="bi bi-check-circle-fill text-success me-2 mt-1"></i>
                                            <div>
                                                <div class="fw-semibold">{{ $permission->name }}</div>
                                                @if($permission->description)
                                                    <small class="text-muted">{{ $permission->description }}</small>
                                                @endif
                                                <div class="small text-muted">
                                                    <code>{{ $permission->code }}</code>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-key fs-1 d-block mb-3"></i>
                        <h6>Belum ada permission</h6>
                        <p>Role ini belum memiliki permission apapun</p>
                        <a href="{{ route('roles.edit', $role) }}" class="btn btn-outline-primary">
                            <i class="bi bi-plus-circle me-1"></i>Tambah Permission
                        </a>
                    </div>
                @endif
        </x-detail-info-card>
    </div>
</div>

<style>
.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #dc2626 0%, #ea580c 50%, #eab308 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
}

.icon-shape {
    width: 35px;
    height: 35px;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
@endsection
