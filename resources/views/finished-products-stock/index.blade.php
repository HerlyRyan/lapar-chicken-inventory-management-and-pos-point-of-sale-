@extends('layouts.app')

@section('title', 'Stok Produk Jadi')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="bi bi-box-seam text-success me-2"></i>
                Stok Produk Jadi
            </h1>
            <p class="text-muted small mb-0">Kelola stok produk jadi (universal: pusat/cabang sesuai konteks)</p>
        </div>
        <div>
            <!-- Optional: add distribution/creation actions for finished products here if available -->
        </div>
    </div>

    <!-- Stock Summary Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Stok Kosong
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $finishedProducts->where('center_stock', '<=', 0)->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Stok Rendah
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $finishedProducts->filter(function($item) { 
                                    return $item->center_stock > 0 && $item->center_stock < $item->minimum_stock; 
                                })->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-exclamation-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Stok Aman
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $finishedProducts->filter(function($item) { 
                                    return $item->center_stock >= ($item->minimum_stock * 2); 
                                })->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Produk
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $finishedProducts->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-grid fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('finished-products-stock.index') }}">
                <input type="hidden" name="branch_id" value="{{ request('branch_id', optional($selectedBranch)->id ?? session('selected_dashboard_branch')) }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="stock_level" class="form-label">Level Stok</label>
                        <select name="stock_level" id="stock_level" class="form-select">
                            <option value="">Semua Level</option>
                            <option value="empty" {{ request('stock_level') == 'empty' ? 'selected' : '' }}>Kosong</option>
                            <option value="low" {{ request('stock_level') == 'low' ? 'selected' : '' }}>Rendah</option>
                            <option value="warning" {{ request('stock_level') == 'warning' ? 'selected' : '' }}>Peringatan</option>
                            <option value="normal" {{ request('stock_level') == 'normal' ? 'selected' : '' }}>Normal</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="category_id" class="form-label">Kategori</label>
                        <select name="category_id" id="category_id" class="form-select">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="search" class="form-label">Pencarian</label>
                        <input type="text" name="search" id="search" class="form-control" 
                               placeholder="Cari berdasarkan nama atau kode..." 
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="bi bi-search me-1"></i>
                                Filter
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Finished Products Grid -->
    <div class="row">
        @if($finishedProducts->count() > 0)
            @foreach($finishedProducts as $product)
                @php
                    $stockPercentage = $product->minimum_stock > 0 
                        ? min(100, ($product->center_stock / $product->minimum_stock) * 100) 
                        : 0;
                    if ($product->center_stock <= 0) {
                        $stockStatus = 'empty';
                        $stockColor = 'danger';
                        $stockText = 'Kosong';
                    } elseif ($product->center_stock < $product->minimum_stock) {
                        $stockStatus = 'low';
                        $stockColor = 'danger';
                        $stockText = 'Rendah';
                    } elseif ($product->center_stock < ($product->minimum_stock * 2)) {
                        $stockStatus = 'warning';
                        $stockColor = 'warning';
                        $stockText = 'Peringatan';
                    } else {
                        $stockStatus = 'normal';
                        $stockColor = 'success';
                        $stockText = 'Aman';
                    }
                @endphp
                
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card border-left-{{ $stockColor }} shadow h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="flex-grow-1">
                                    <h6 class="card-title mb-1 fw-bold">{{ $product->name }}</h6>
                                    <p class="card-subtitle text-muted small mb-2">{{ $product->code }}</p>
                                </div>
                                <span class="badge bg-{{ $stockColor }} fs-6">{{ $stockText }}</span>
                            </div>
                            
                            <div class="row text-center mb-3">
                                <div class="col-4">
                                    <div class="fw-bold text-{{ $stockColor }}">{{ number_format($product->center_stock, 0, ',', '.') }}</div>
                                    <small class="text-muted">Stok Saat Ini</small>
                                </div>
                                <div class="col-4">
                                    <div class="fw-bold">{{ number_format($product->minimum_stock, 0, ',', '.') }}</div>
                                    <small class="text-muted">Minimum</small>
                                </div>
                                <div class="col-4">
                                    <div class="fw-bold">{{ $product->unit->name ?? '-' }}</div>
                                    <small class="text-muted">Satuan</small>
                                </div>
                            </div>
                            
                            <!-- Stock Progress Bar -->
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <small class="text-muted">Level Stok</small>
                                    <small class="text-{{ $stockColor }}">{{ number_format($stockPercentage, 1) }}%</small>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-{{ $stockColor }}" 
                                         style="width: {{ min(100, max(5, $stockPercentage)) }}%"></div>
                                </div>
                            </div>
                            
                            @if($product->category)
                                <div class="mb-2">
                                    <span class="badge bg-secondary">{{ $product->category->name }}</span>
                                </div>
                            @endif
                            
                            <div class="d-grid gap-2">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('finished-products-stock.show', [$product, 'branch_id' => request('branch_id', optional($selectedBranch)->id ?? session('selected_dashboard_branch'))]) }}" 
                                       class="btn btn-sm btn-outline-info" title="Lihat Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-warning" 
                                            onclick="showAdjustmentModal({{ $product->id }}, '{{ $product->name }}', {{ $product->center_stock }})" 
                                            title="Sesuaikan Stok">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
            
            <!-- Pagination -->
            <div class="col-12">
                <div class="d-flex justify-content-center">
                    {{ $finishedProducts->appends(request()->query())->links() }}
                </div>
            </div>
        @else
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-box-seam text-muted" style="font-size: 4rem;"></i>
                        <h5 class="mt-3 text-muted">Tidak Ada Produk</h5>
                        <p class="text-muted">
                            @if(request()->hasAny(['stock_level', 'category_id', 'search']))
                                Tidak ada produk yang sesuai dengan filter yang dipilih.
                            @else
                                Belum ada produk jadi pada stok saat ini.
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

@include('finished-products-stock.partials.adjustment-modal')

@push('styles')
<style>
    .border-left-danger { border-left: 0.25rem solid #e74a3b !important; }
    .border-left-warning { border-left: 0.25rem solid #f6c23e !important; }
    .border-left-success { border-left: 0.25rem solid #1cc88a !important; }
    .border-left-info { border-left: 0.25rem solid #36b9cc !important; }
    .card-title { font-size: 1rem; line-height: 1.2; }
    .progress { border-radius: 10px; }
    .progress-bar { border-radius: 10px; }
</style>
@endpush
@endsection
