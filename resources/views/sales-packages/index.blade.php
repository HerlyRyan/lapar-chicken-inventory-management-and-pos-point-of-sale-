@extends('layouts.app')

@php
use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
<div class="container-fluid">
    <x-page-header 
        title="Paket Penjualan" 
        subtitle="Kelola paket produk untuk penjualan standar"
        :breadcrumb="[
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => 'Paket Penjualan', 'active' => true]
        ]"
    />

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="bi bi-box2-heart me-2"></i>Daftar Paket Penjualan
            </h5>
            <a href="{{ route('sales-packages.create') }}" class="btn btn-light shadow">
                <i class="bi bi-plus-lg"></i> Tambah Paket Baru
            </a>
        </div>

        <div class="card-body">
            <!-- Filter Section -->
            <form method="GET" class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label">Cari Paket</label>
                    <input type="text" class="form-control" name="search" value="{{ request('search') }}" 
                           placeholder="Nama, kode, atau deskripsi paket...">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i> Filter
                        </button>
                        <a href="{{ route('sales-packages.index') }}" class="btn btn-outline-secondary">Reset</a>
                    </div>
                </div>
            </form>

            <!-- Table Section -->
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-primary">
                        <tr>
                            <th width="50">#</th>
                            <th>Paket</th>
                            <th>Kategori</th>
                            <th>Komponen</th>
                            <th>Harga Dasar</th>
                            <th>Diskon/Tambahan</th>
                            <th class="text-end">Harga Jual</th>
                            <th>Status</th>
                            <th width="120">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($salesPackages as $package)
                        <tr>
                            <td class="text-center fw-bold text-muted">
                                {{ $loop->iteration + ($salesPackages->currentPage() - 1) * $salesPackages->perPage() }}
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($package->image && Storage::disk('public')->exists($package->image))
                                        <img src="{{ asset('storage/' . $package->image) }}" 
                                             alt="{{ $package->name }}" 
                                             class="rounded me-3" 
                                             style="width: 50px; height: 50px; object-fit: cover;">
                                    @else
                                        <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" 
                                             style="width: 50px; height: 50px;">
                                            <i class="bi bi-box2-heart text-muted"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <h6 class="mb-1">{{ $package->name }}</h6>
                                        <small class="text-muted">{{ $package->code }}</small>
                                        @if($package->description)
                                            <div class="small text-muted mt-1">{{ Str::limit($package->description, 50) }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($package->category)
                                    <span class="badge bg-info text-white">{{ $package->category->name ?? $package->category }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">
                                    {{ $package->packageItems->count() }} produk:
                                </small>
                                <div class="mt-1">
                                    @foreach($package->packageItems->take(2) as $item)
                                        <span class="badge bg-light text-dark border me-1">
                                            {{ $item->quantity }}{{ $item->finishedProduct->unit->abbreviation ?? 'pcs' }} 
                                            {{ $item->finishedProduct->name }}
                                        </span>
                                    @endforeach
                                    @if($package->packageItems->count() > 2)
                                        <span class="badge bg-secondary">+{{ $package->packageItems->count() - 2 }} lainnya</span>
                                    @endif
                                </div>
                            </td>
                            <td class="text-end">
                                <span class="fw-bold">Rp {{ number_format($package->base_price, 0, ',', '.') }}</span>
                            </td>
                            <td>
                                @if($package->discount_percentage > 0)
                                    <span class="badge bg-success">-{{ $package->discount_percentage }}%</span>
                                @elseif($package->discount_amount > 0)
                                    <span class="badge bg-success">-Rp {{ number_format($package->discount_amount, 0, ',', '.') }}</span>
                                @endif
                                
                                @if($package->additional_charge > 0)
                                    <span class="badge bg-warning">+Rp {{ number_format($package->additional_charge, 0, ',', '.') }}</span>
                                @endif
                                
                                @if($package->discount_percentage == 0 && $package->discount_amount == 0 && $package->additional_charge == 0)
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <span class="fw-bold text-primary fs-5">
                                    Rp {{ number_format($package->final_price, 0, ',', '.') }}
                                </span>
                            </td>
                            <td>
                                @if($package->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Tidak Aktif</span>
                                @endif
                            </td>
                            <td>
                                <x-action-buttons
                                    :viewUrl="route('sales-packages.show', $package)"
                                    :editUrl="route('sales-packages.edit', $package)"
                                    :deleteUrl="route('sales-packages.destroy', $package)"
                                    :showToggle="true"
                                    :toggleUrl="route('sales-packages.toggle-status', $package)"
                                    :isActive="$package->is_active"
                                    :itemName="'paket ' . $package->name"
                                />
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <i class="bi bi-box2-heart text-muted" style="font-size: 3rem;"></i>
                                <h5 class="text-muted mt-2">Belum ada paket penjualan</h5>
                                <p class="text-muted">Silakan tambah paket penjualan pertama Anda.</p>
                                <a href="{{ route('sales-packages.create') }}" class="btn btn-primary">
                                    <i class="bi bi-plus-lg"></i> Tambah Paket Baru
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($salesPackages->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $salesPackages->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<!-- SweetAlert2 is already included in the main layout -->
@endpush
@endsection
