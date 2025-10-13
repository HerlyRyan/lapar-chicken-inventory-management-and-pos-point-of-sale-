@extends('layouts.app')

@section('title', 'Detail Bahan Setengah Jadi')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="bi bi-box text-success me-2"></i>
                Detail Bahan Setengah Jadi
            </h1>
            <p class="text-muted small mb-0">Informasi produk dan stok (universal: pusat/cabang sesuai konteks)</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('semi-finished-stock.index', ['branch_id' => request('branch_id', optional($selectedBranch)->id ?? session('selected_dashboard_branch'))]) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>
                Kembali
            </a>
            <a href="{{ route('semi-finished-distributions.create') }}?product={{ $semiFinishedProduct->id }}&branch_id={{ request('branch_id', optional($selectedBranch)->id ?? session('selected_dashboard_branch')) }}" class="btn btn-primary">
                <i class="bi bi-send me-1"></i>
                Distribusi ke Cabang
            </a>
        </div>
    </div>

    @php
        // Hitung stok berdasarkan konteks cabang terpilih (jika ada) atau agregasi pusat (legacy)
        $selectedBranchId = request('branch_id', optional($selectedBranch)->id ?? session('selected_dashboard_branch'));
        if ($selectedBranchId) {
            $centerStock = \App\Models\SemiFinishedBranchStock::where('semi_finished_product_id', $semiFinishedProduct->id)
                ->where('branch_id', $selectedBranchId)
                ->value('quantity') ?? 0;
        } else {
            // Agregasi untuk semua pusat produksi
            $centerStock = \App\Models\SemiFinishedBranchStock::query()
                ->selectRaw('COALESCE(SUM(semi_finished_branch_stocks.quantity), 0) as qty')
                ->leftJoin('branches as b', function($join) {
                    $join->on('b.id', '=', 'semi_finished_branch_stocks.branch_id')
                         ->where('b.type', 'production');
                })
                ->where('semi_finished_branch_stocks.semi_finished_product_id', $semiFinishedProduct->id)
                ->value('qty');
        }

        $minStock = (float) ($semiFinishedProduct->minimum_stock ?? 0);
        $stockPct = $minStock > 0 ? min(100, ($centerStock / $minStock) * 100) : 0;
        if ($centerStock <= 0) {
            $stockColor = 'danger';
            $stockText = 'Kosong';
        } elseif ($centerStock < $minStock) {
            $stockColor = 'danger';
            $stockText = 'Rendah';
        } elseif ($centerStock < ($minStock * 2)) {
            $stockColor = 'warning';
            $stockText = 'Peringatan';
        } else {
            $stockColor = 'success';
            $stockText = 'Aman';
        }
    @endphp

    <div class="row g-4">
        <!-- Info Column -->
        <div class="col-12 col-md-5">
            <div class="card shadow h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="flex-grow-1">
                            <h5 class="mb-1 fw-bold">{{ $semiFinishedProduct->name }}</h5>
                            <p class="text-muted mb-2">Kode: <span class="fw-semibold">{{ $semiFinishedProduct->code }}</span></p>
                        </div>
                        <span class="badge bg-{{ $stockColor }}">{{ $stockText }}</span>
                    </div>

                    @if($semiFinishedProduct->category)
                        <div class="mb-3">
                            <span class="badge bg-secondary">{{ $semiFinishedProduct->category->name }}</span>
                        </div>
                    @endif

                    <div class="row mb-3 text-center">
                        <div class="col-4">
                            <div class="fw-bold text-{{ $stockColor }}">{{ number_format($centerStock, 0, ',', '.') }}</div>
                            <small class="text-muted">Stok</small>
                        </div>
                        <div class="col-4">
                            <div class="fw-bold">{{ number_format($minStock, 0, ',', '.') }}</div>
                            <small class="text-muted">Minimum</small>
                        </div>
                        <div class="col-4">
                            <div class="fw-bold">{{ $semiFinishedProduct->unit->name ?? '-' }}</div>
                            <small class="text-muted">Satuan</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <small class="text-muted">Level Stok</small>
                            <small class="text-{{ $stockColor }}">{{ number_format($stockPct, 1) }}%</small>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-{{ $stockColor }}" style="width: {{ min(100, max(5, $stockPct)) }}%"></div>
                        </div>
                    </div>

                    @if(!empty($semiFinishedProduct->description))
                        <div class="mt-3">
                            <h6 class="fw-semibold">Deskripsi</h6>
                            <p class="mb-0 text-muted">{{ $semiFinishedProduct->description }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Actions / Utilities Column -->
        <div class="col-12 col-md-7">
            <div class="card border-left-{{ $stockColor }} shadow h-100">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Aksi Cepat</h6>
                    <div class="d-flex flex-wrap gap-2 mb-4">
                        <a class="btn btn-outline-info"
                           href="{{ route('semi-finished-stock.index', ['branch_id' => request('branch_id', optional($selectedBranch)->id ?? session('selected_dashboard_branch'))]) }}#product-{{ $semiFinishedProduct->id }}">
                            <i class="bi bi-grid me-1"></i>
                            Lihat di Daftar
                        </a>
                        <button type="button" class="btn btn-warning" onclick="showAdjustmentModal({{ $semiFinishedProduct->id }}, '{{ $semiFinishedProduct->name }}', {{ (float) $centerStock }})">
                            <i class="bi bi-pencil me-1"></i>
                            Sesuaikan Stok
                        </button>
                        <a class="btn btn-primary" href="{{ route('semi-finished-distributions.create') }}?product={{ $semiFinishedProduct->id }}&branch_id={{ request('branch_id', optional($selectedBranch)->id ?? session('selected_dashboard_branch')) }}">
                            <i class="bi bi-send me-1"></i>
                            Distribusi ke Cabang
                        </a>
                    </div>

                    <div class="alert alert-secondary d-flex align-items-center" role="alert">
                        <i class="bi bi-info-circle me-2"></i>
                        Gunakan tombol "Sesuaikan Stok" untuk melakukan koreksi inventori.
                    </div>

                    <div class="mt-4">
                        <h6 class="fw-bold mb-3">Informasi Produk</h6>
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <div class="border rounded p-3 h-100">
                                    <div class="text-muted small">Nama</div>
                                    <div class="fw-semibold">{{ $semiFinishedProduct->name }}</div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="border rounded p-3 h-100">
                                    <div class="text-muted small">Kode</div>
                                    <div class="fw-semibold">{{ $semiFinishedProduct->code }}</div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="border rounded p-3 h-100">
                                    <div class="text-muted small">Kategori</div>
                                    <div class="fw-semibold">{{ $semiFinishedProduct->category->name ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="border rounded p-3 h-100">
                                    <div class="text-muted small">Satuan</div>
                                    <div class="fw-semibold">{{ $semiFinishedProduct->unit->name ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="border rounded p-3 h-100">
                                    <div class="text-muted small">Minimum Stok</div>
                                    <div class="fw-semibold">{{ number_format($minStock, 0, ',', '.') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('semi-finished-stock.partials.adjustment-modal')
@endsection

@push('styles')
<style>
    .border-left-danger { border-left: 0.25rem solid #e74a3b !important; }
    .border-left-warning { border-left: 0.25rem solid #f6c23e !important; }
    .border-left-success { border-left: 0.25rem solid #1cc88a !important; }
    .border-left-info { border-left: 0.25rem solid #36b9cc !important; }
</style>
@endpush
