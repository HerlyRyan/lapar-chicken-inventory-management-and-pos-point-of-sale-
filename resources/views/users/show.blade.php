@extends('layouts.app')

@php
use Illuminate\Support\Facades\Storage;
@endphp

@section('title', 'Detail Pengguna')

@section('content')
<x-detail-page-header 
    title="Detail Pengguna"
    subtitle="Detail informasi pengguna"
    icon="person"
    :backRoute="route('users.index')"
    :editRoute="route('users.edit', $user)"
/>



<div class="row">
    <div class="col-lg-8">
        <x-detail-info-card title="Informasi Pengguna" icon="info-circle">
                <div class="row g-4">
                    <!-- Avatar Section -->
                    <div class="col-12 text-center mb-4">
                        @if($user->avatar)
                            <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}" 
                                 class="rounded-circle border border-3 border-primary" 
                                 style="width: 120px; height: 120px; object-fit: cover;">
                        @else
                            <div class="rounded-circle border border-3 border-primary mx-auto d-flex align-items-center justify-content-center text-white fw-bold" 
                                 style="width: 120px; height: 120px; background: linear-gradient(135deg, #dc2626, #ea580c); font-size: 2.5rem;">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                        <h4 class="mt-3 mb-1 fw-bold">{{ $user->name }}</h4>
                        <p class="text-muted">{{ $user->email }}</p>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <div class="icon-shape bg-primary rounded-3 p-2" style="background: var(--gradient-main) !important;">
                                    <i class="bi bi-person text-white"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1 fw-semibold text-muted">Nama Lengkap</h6>
                                <h5 class="mb-0 fw-bold">{{ $user->name }}</h5>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <div class="icon-shape bg-primary rounded-3 p-2" style="background: var(--gradient-main) !important;">
                                    <i class="bi bi-envelope text-white"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1 fw-semibold text-muted">Email</h6>
                                <h5 class="mb-0 fw-bold">{{ $user->email }}</h5>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <div class="icon-shape bg-primary rounded-3 p-2" style="background: var(--gradient-main) !important;">
                                    <i class="bi bi-telephone text-white"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1 fw-semibold text-muted">Nomor Telepon</h6>
                                @if($user->phone)
                                    <div class="d-flex align-items-center gap-2">
                                        <h5 class="mb-0 fw-bold">{{ $user->phone }}</h5>
                                    </div>
                                @else
                                    <span class="text-muted">Tidak tersedia</span>
                                @endif
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
                                <h6 class="mb-1 fw-semibold text-muted">Cabang</h6>
                                <div class="mb-0">
                                    @if($user->branch)
                                        <div class="fw-bold">{{ $user->branch->name }}</div>
                                        @if($user->branch->code)
                                            <small class="text-muted">({{ $user->branch->code }})</small>
                                        @endif
                                        @if($user->branch->phone)
                                            <div class="mt-1">
                                                <small class="text-muted">{{ $user->branch->getFormattedPhone() }}</small>
                                            </div>
                                        @endif
                                    @else
                                        <span class="text-muted">Belum ditentukan</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <div class="icon-shape bg-primary rounded-3 p-2" style="background: var(--gradient-main) !important;">
                                    <i class="bi bi-person-badge text-white"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1 fw-semibold text-muted">Role</h6>
                                <div class="mb-0">
                                    @if($user->roles->count() > 0)
                                        @foreach($user->roles as $role)
                                            <span class="badge bg-primary me-1">{{ $role->name }}</span>
                                        @endforeach
                                    @else
                                        <span class="text-muted">Belum ada role</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <div class="icon-shape bg-primary rounded-3 p-2" style="background: var(--gradient-main) !important;">
                                    <i class="bi bi-toggle-on text-white"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1 fw-semibold text-muted">Status</h6>
                                <h5 class="mb-0 fw-bold">
                                    @if($user->is_active ?? true)
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary">Nonaktif</span>
                                    @endif
                                </h5>
                            </div>
                        </div>
                    </div>

                    @if($user->email_verified_at)
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="flex-shrink-0">
                                    <div class="icon-shape bg-success rounded-3 p-2">
                                        <i class="bi bi-shield-check text-white"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1 fw-semibold text-muted">Email Terverifikasi</h6>
                                    <p class="mb-0 small">{{ $user->email_verified_at->format('d/m/Y H:i') }}</p>
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
                                <h6 class="mb-1 fw-semibold text-muted">Terdaftar</h6>
                                <p class="mb-0 small">{{ $user->created_at->format('d/m/Y H:i') }}</p>
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
                                <p class="mb-0 small">{{ $user->updated_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
        </x-detail-info-card>
    </div>

    <div class="col-lg-4">
        <!-- Card Role dan Permission -->
        @if($user->roles->count() > 0)
        <x-detail-info-card title="Role & Permission" icon="shield-check" gradient="success" class="mb-3">
            @php
                $userPermissions = $user->roles->flatMap(function($role) {
                    return $role->permissions;
                })->unique('id');
            @endphp

            @if($userPermissions->isNotEmpty())
                <ul class="list-unstyled mb-0">
                    @foreach($userPermissions->sortBy('name') as $permission)
                        <li>
                            <i class="bi bi-check-circle-fill text-success me-2"></i>{{ $permission->name }}
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-muted mb-0">Tidak ada hak akses spesifik yang diberikan untuk role ini.</p>
            @endif
        </x-detail-info-card>
        @endif

        <x-detail-action-card
            title="Aksi"
            icon="gear"
            :whatsappNumber="$user->phone"
            :whatsappName="$user->name"
            :editRoute="route('users.edit', $user)"
            :deleteRoute="auth()->id() !== $user->id ? route('users.destroy', $user) : null"
            deleteMessage="Apakah Anda yakin ingin menghapus pengguna ini?"
            itemName="pengguna"
            :email="$user->email"
            :emailName="$user->name"
            :backRoute="null"
        />
        
        <x-detail-system-info-card :model="$user" class="mt-3" />
    </div>
</div>
@endsection
