@extends('layouts.app')

@section('title', 'Stok Bahan Mentah')

@php
use Illuminate\Support\Facades\Storage;
@endphp

@push('styles')
<style>
    .border-left-danger { border-left: 0.25rem solid #e74a3b !important; }
    .border-left-warning { border-left: 0.25rem solid #f6c23e !important; }
    .border-left-success { border-left: 0.25rem solid #1cc88a !important; }
    .border-left-info { border-left: 0.25rem solid #36b9cc !important; }
    .card-title { font-size: 1rem; line-height: 1.2; }
    .progress { border-radius: 10px; }
    .progress-bar { border-radius: 10px; }
    .material-thumb { width: 100%; height: 140px; object-fit: cover; border-radius: 0.5rem; border: 1px solid #e9ecef; background-color: #f8f9fa; }
    .stock-card { transition: all 0.2s ease; }
    .stock-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
    /* Ensure md breakpoint shows two columns as preferred */
    @media (min-width: 768px) {
        .grid-col-md-6 { flex: 0 0 auto; width: 50%; }
    }
    /* Scroll container optional; disabled to match other pages */
    .materials-scroll-container { max-height: none; overflow: visible; }
}</style>
@endpush

@section('content')
<div class="container-fluid">
    @php
        $rmList = $rawMaterials instanceof \Illuminate\Pagination\AbstractPaginator
            ? $rawMaterials->getCollection()
            : (is_array($rawMaterials) ? collect($rawMaterials) : $rawMaterials);
    @endphp
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="bi bi-box-seam text-success me-2"></i>
                Stok Bahan Mentah
            </h1>
            <p class="text-muted small mb-0">Kelola stok bahan mentah (terpusat)</p>
        </div>
        <div>
            <!-- Optional actions -->
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Stock Summary Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Stok Kosong</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $rmList->where('current_stock', '<=', 0)->count() }}</div>
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
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Stok Rendah</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $rmList->filter(function($item){ return $item->current_stock > 0 && $item->current_stock < $item->minimum_stock; })->count() }}
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
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Stok Aman</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $rmList->filter(function($item){ return $item->current_stock >= ($item->minimum_stock * 2); })->count() }}
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Bahan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $rmList->count() }}</div>
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
            <form method="GET" action="{{ route('raw-materials.stock') }}">
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
                            @foreach(($categories ?? collect()) as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="search" class="form-label">Pencarian</label>
                        <input type="text" name="search" id="search" class="form-control" placeholder="Cari berdasarkan nama atau kode..." value="{{ request('search', request('q')) }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="bi bi-search me-1"></i> Filter
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Materials Grid -->
    <div class="row">
        @include('raw-materials._stock_grid', ['rawMaterials' => $rawMaterials])

        <!-- Pagination -->
        <div class="col-12">
            <div class="d-flex justify-content-center">
                {{ $rawMaterials->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
