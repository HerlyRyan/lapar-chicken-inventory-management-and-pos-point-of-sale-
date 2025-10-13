@extends('layouts.app')

@php
use Illuminate\Support\Facades\Storage;
@endphp

@section('title', 'Data Pengguna')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1" style="color: var(--primary-red);">
            <i class="bi bi-people-fill me-2"></i>Data Pengguna
        </h1>
        <p class="text-muted mb-0">Kelola data pengguna sistem</p>
    </div>
    <a href="{{ route('users.create') }}" class="btn btn-primary shadow px-4 d-flex align-items-center" style="min-width: 160px; white-space: nowrap;">
        <i class="bi bi-plus-circle me-2"></i>
        <span>Tambah Pengguna</span>
    </a>
</div>

<div class="card border-0 shadow-lg">
    <div class="card-header text-white py-3" style="background: linear-gradient(135deg, #dc2626 0%, #ea580c 50%, #eab308 100%);">
        <h5 class="mb-0 fw-bold">
            <i class="bi bi-list-ul me-2"></i>Daftar Pengguna
        </h5>
    </div>
    <div class="card-body">
        <!-- Filter Form -->
        <form method="GET" action="" class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama atau email..." class="form-control">
                </div>
            </div>
            <div class="col-md-2">
                <select name="branch_id" class="form-select">
                    <option value="">Semua Cabang</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="role_id" class="form-select">
                    <option value="">Semua Role</option>
                    @foreach(\App\Models\Role::where('is_active', true)->orderBy('name')->get() as $role)
                        <option value="{{ $role->id }}" {{ request('role_id') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="is_active" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-primary w-100">
                    <i class="bi bi-search me-1"></i>Cari
                </button>
            </div>
            <div class="col-md-1">
                @if(request('q') || request('branch_id') || request('role_id') || request('is_active') !== null)
                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-arrow-clockwise"></i>
                    </a>
                @endif
            </div>
        </form>

        <x-standard-table
            :headers="[
                ['text' => '#', 'width' => '60px'],
                ['text' => 'Nama'],
                ['text' => 'Email'],
                ['text' => 'Cabang'],
                ['text' => 'Role'],
                ['text' => 'Status']
            ]"
            :pagination="$users"
            :searchable="false"
        >
                    @forelse($users as $user)
                    <tr>
                        <td>{{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                @if($user->avatar)
                                    @php
                                        // Handle both formats: with and without 'storage/' prefix
                                        $avatarPath = str_starts_with($user->avatar, 'storage/') ? $user->avatar : 'storage/' . $user->avatar;
                                    @endphp
                                    <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}" 
                                         class="rounded-circle me-3" style="width: 40px; height: 40px; object-fit: cover;">
                                @else
                                    <div class="rounded-circle me-3 d-flex align-items-center justify-content-center text-white fw-bold" 
                                         style="width: 40px; height: 40px; background: linear-gradient(135deg, #dc2626, #ea580c);">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                @endif
                                <div class="d-flex align-items-center gap-2">
                                    <div class="fw-semibold">{{ $user->name }}</div>
                                    @if($user->phone)
                                        <a href="https://wa.me/{{ $user->phone }}?text=Halo%20{{ urlencode($user->name) }}" target="_blank" 
                                           class="btn btn-success btn-sm" title="Hubungi via WhatsApp">
                                            <i class="bi bi-whatsapp"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @if($user->branch)
                                <div class="fw-semibold">{{ $user->branch->name }}</div>
                                <div class="d-flex align-items-center gap-1 mt-1">
                                    @if($user->branch->code)
                                        <small class="text-muted">{{ $user->branch->code }}</small>
                                    @endif
                                    @if($user->branch->phone && $user->branch->getWhatsAppLink())
                                        <a href="{{ $user->branch->getWhatsAppLink('Halo, saya ingin menghubungi cabang ' . $user->branch->name) }}" 
                                           target="_blank" class="btn btn-success btn-sm ms-1" title="Hubungi cabang via WhatsApp">
                                            <i class="bi bi-whatsapp"></i>
                                        </a>
                                    @endif
                                </div>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($user->roles->count() > 0)
                                @foreach($user->roles as $role)
                                    <span class="badge bg-primary me-1">{{ $role->name }}</span>
                                @endforeach
                            @else
                                <span class="text-muted">Belum ada role</span>
                            @endif
                        </td>
                        <td>
                            @if($user->is_active)
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-secondary">Nonaktif</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <x-action-buttons
                                :viewUrl="route('users.show', $user)" 
                                :editUrl="route('users.edit', $user)"
                                :deleteUrl="$user->deletable() ? route('users.destroy', $user) : ''" 
                                :showDelete="$user->deletable()"
                                itemName="pengguna {{$user->name}}"
                            />
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            Belum ada data pengguna
                        </td>
                    </tr>
                    @endforelse
        </x-standard_table>
    </div>
</div>
@endsection
