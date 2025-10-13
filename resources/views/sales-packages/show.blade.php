@extends('layouts.app')

@php
use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
<div class="container-fluid">
    <x-page-header 
        title="Detail Paket Penjualan" 
        subtitle="Informasi lengkap paket {{ $salesPackage->name }}"
        :breadcrumbs="[
            ['title' => 'Dashboard', 'url' => route('dashboard')],
            ['title' => 'Paket Penjualan', 'url' => route('sales-packages.index')],
            ['title' => $salesPackage->name, 'url' => null]
        ]"
    />

    <div class="row">
        <!-- Package Information -->
        <div class="col-lg-8">
            <x-detail-info-card 
                title="Informasi Paket"
                icon="bi-box2-heart"
            >
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <tr>
                                <td class="fw-bold">Nama Paket:</td>
                                <td>{{ $salesPackage->name }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Kode Paket:</td>
                                <td><span class="badge bg-primary">{{ $salesPackage->code }}</span></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Kategori:</td>
                                <td>
                                    @if($salesPackage->category)
                                        <span class="badge bg-info text-white">{{ $salesPackage->category->name ?? $salesPackage->category }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Status:</td>
                                <td>
                                    @if($salesPackage->is_active)
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary">Tidak Aktif</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Dibuat Oleh:</td>
                                <td>{{ $salesPackage->creator->name ?? 'System' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Tanggal Dibuat:</td>
                                <td>{{ $salesPackage->created_at->format('d F Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        @if($salesPackage->image && Storage::disk('public')->exists($salesPackage->image))
                            <img src="{{ asset('storage/' . $salesPackage->image) }}" 
                                 alt="{{ $salesPackage->name }}" 
                                 class="img-fluid rounded shadow">
                        @else
                            <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 200px;">
                                <div class="text-center">
                                    <i class="bi bi-box2-heart text-muted" style="font-size: 3rem;"></i>
                                    <div class="text-muted mt-2">Tidak ada foto</div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                
                @if($salesPackage->description)
                <div class="row mt-3">
                    <div class="col-12">
                        <h6>Deskripsi:</h6>
                        <p class="text-muted">{{ $salesPackage->description }}</p>
                    </div>
                </div>
                @endif
            </x-detail-info-card>

            <!-- Package Components -->
            <div class="card shadow-sm mt-3">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-box-seam me-2"></i>Komponen Paket
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Produk</th>
                                    <th class="text-center">Jumlah</th>
                                    <th class="text-end">Harga Satuan</th>
                                    <th class="text-end">Total Harga</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($salesPackage->packageItems as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($item->finishedProduct->photo)
                                                <img src="{{ asset('storage/' . $item->finishedProduct->photo) }}" 
                                                     alt="{{ $item->finishedProduct->name }}" 
                                                     class="rounded me-2" 
                                                     style="width: 40px; height: 40px; object-fit: cover;">
                                            @else
                                                <div class="bg-light rounded me-2 d-flex align-items-center justify-content-center" 
                                                     style="width: 40px; height: 40px;">
                                                    <i class="bi bi-archive text-muted"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <h6 class="mb-1">{{ $item->finishedProduct->name }}</h6>
                                                <small class="text-muted">
                                                    {{ $item->finishedProduct->category->name ?? 'Tanpa Kategori' }}
                                                </small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info">
                                            {{ number_format($item->quantity, ($item->quantity == floor($item->quantity)) ? 0 : 2) }}
                                            {{ $item->finishedProduct->unit->abbreviation ?? 'pcs' }}
                                        </span>
                                    </td>
                                    <td class="text-end">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                    <td class="text-end fw-bold">Rp {{ number_format($item->total_price, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="4" class="text-end">Total Harga Dasar:</th>
                                    <th class="text-end">Rp {{ number_format($salesPackage->base_price, 0, ',', '.') }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Branch Availability -->
            <div class="card shadow-sm mt-3">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-shop-window me-2"></i>Ketersediaan per Cabang
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Cabang</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Dapat Dibuat</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($branchAvailability as $branchId => $availability)
                                <tr>
                                    <td class="fw-bold">{{ $availability['name'] }}</td>
                                    <td class="text-center">
                                        @if($availability['is_available'])
                                            <span class="badge bg-success">Tersedia</span>
                                        @else
                                            <span class="badge bg-danger">Tidak Tersedia</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($availability['available_quantity'] > 0)
                                            <span class="badge bg-info fs-6">
                                                {{ $availability['available_quantity'] }} paket
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">0 paket</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($availability['is_available'])
                                            <small class="text-success">
                                                <i class="bi bi-check-circle"></i>
                                                Semua komponen tersedia
                                            </small>
                                        @else
                                            <small class="text-danger">
                                                <i class="bi bi-x-circle"></i>
                                                Stok komponen tidak mencukupi
                                            </small>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pricing Summary -->
        <div class="col-lg-4">
            <div class="card shadow-sm sticky-top">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="bi bi-calculator me-2"></i>Ringkasan Harga
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Harga Dasar:</span>
                        <span class="fw-bold fs-5">Rp {{ number_format($salesPackage->base_price, 0, ',', '.') }}</span>
                    </div>
                    
                    @if($salesPackage->discount_percentage > 0 || $salesPackage->discount_amount > 0)
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span>Diskon:</span>
                            <span class="text-success fw-bold">
                                @if($salesPackage->discount_percentage > 0)
                                    -{{ $salesPackage->discount_percentage }}% 
                                    (Rp {{ number_format(($salesPackage->base_price * $salesPackage->discount_percentage) / 100, 0, ',', '.') }})
                                @else
                                    -Rp {{ number_format($salesPackage->discount_amount, 0, ',', '.') }}
                                @endif
                            </span>
                        </div>
                    @endif
                    
                    @if($salesPackage->additional_charge > 0)
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span>Biaya Tambahan:</span>
                            <span class="text-warning fw-bold">+Rp {{ number_format($salesPackage->additional_charge, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="h5 mb-0">Harga Jual Final:</span>
                        <span class="h3 text-primary fw-bold mb-0">
                            Rp {{ number_format($salesPackage->final_price, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="card shadow-sm mt-3">
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('sales-packages.edit', $salesPackage) }}" class="btn btn-warning">
                            <i class="bi bi-pencil-square"></i> Edit Paket
                        </a>
                        
                        <form action="{{ route('sales-packages.toggle-status', $salesPackage) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-outline-{{ $salesPackage->is_active ? 'danger' : 'success' }} w-100">
                                <i class="bi bi-toggle-{{ $salesPackage->is_active ? 'off' : 'on' }}"></i>
                                {{ $salesPackage->is_active ? 'Nonaktifkan' : 'Aktifkan' }} Paket
                            </button>
                        </form>
                        
                        <a href="{{ route('sales-packages.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali ke Daftar
                        </a>
                    </div>
                </div>
            </div>

            <!-- System Info -->
            <x-detail-system-info-card 
                :model="$salesPackage"
            />
        </div>
    </div>
</div>
@endsection
