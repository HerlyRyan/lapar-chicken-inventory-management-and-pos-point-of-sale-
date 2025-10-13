@extends('layouts.app')

@section('title', 'Detail Bahan Setengah Jadi - ' . $semiFinishedProduct->name)

@section('content')
{{-- Header --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1" style="color: var(--primary-red);">
            <i class="bi bi-box-seam me-2"></i>Detail Bahan Setengah Jadi
        </h1>
        <p class="text-muted mb-0">
            Informasi lengkap: {{ $semiFinishedProduct->name }}
        </p>
    </div>
    <a href="{{ route('semi-finished-products.index', request()->has('branch_id') ? ['branch_id' => request('branch_id')] : []) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Kembali
    </a>
</div>

<div class="row">
    {{-- Main Content Column --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-lg mb-4">
            <div class="card-header text-white py-3" style="background: linear-gradient(135deg, #0ea5e9 0%, #06b6d4 50%, #10b981 100%);">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-info-circle me-2"></i>Informasi: {{ $semiFinishedProduct->name }}
                </h5>
            </div>
            <div class="card-body p-4">
                {{-- Image Section --}}
                <div class="text-center mb-4">
                    <x-product-image :src="$semiFinishedProduct->image" :alt="$semiFinishedProduct->name" class="rounded border shadow-sm" height="300" />
                </div>

                <hr>

                {{-- Product Details Section --}}
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        @if($semiFinishedProduct->code)
                            <p class="text-muted mb-2">
                                <i class="bi bi-upc me-1"></i>Kode: <span class="fw-semibold">{{ $semiFinishedProduct->code }}</span>
                            </p>
                        @endif
                    </div>
                    @if($semiFinishedProduct->is_active)
                        <span class="badge bg-success fs-6 px-3 py-2"><i class="bi bi-check-circle me-1"></i>Aktif</span>
                    @else
                        <span class="badge bg-secondary fs-6 px-3 py-2"><i class="bi bi-x-circle me-1"></i>Tidak Aktif</span>
                    @endif
                </div>

                @if($semiFinishedProduct->description)
                    <p class="mb-3 fst-italic text-muted">"{{ $semiFinishedProduct->description }}"</p>
                @else
                    <p class="mb-3 fst-italic text-muted">Tidak ada deskripsi.</p>
                @endif

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-dark fw-bold">Kode</label>
                        <h5><span class="badge bg-primary">{{ $semiFinishedProduct->code }}</span></h5>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-dark fw-bold">Kategori</label>
                        @if($semiFinishedProduct->category)
                            <h5><span class="badge bg-info">{{ $semiFinishedProduct->category->name }}</span></h5>
                        @else
                            <h5 class="fw-normal text-muted">-</h5>
                        @endif
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-dark fw-bold">Satuan</label>
                        <h5 class="fw-normal">{{ $semiFinishedProduct->unit ? (is_object($semiFinishedProduct->unit) ? $semiFinishedProduct->unit->unit_name : $semiFinishedProduct->unit) : '-' }}</h5>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-dark fw-bold">Biaya Produksi</label>
                        <h5 class="fw-bold text-success">
                            @if($semiFinishedProduct->production_cost)
                                Rp {{ number_format($semiFinishedProduct->production_cost, 0, ',', '.') }}
                            @else
                                -
                            @endif
                        </h5>
                    </div>
                </div>

                <hr>

                {{-- Stock Info Section --}}
                <h6 class="fw-bold mb-3"><i class="bi bi-archive me-2"></i>Informasi Stok</h6>
                <div class="alert alert-info d-flex align-items-center">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    <div>
                        Stok ditampilkan untuk: 
                        <span class="fw-bold">
                        @if($branchForStock)
                            {{ $branchForStock->name }}
                        @elseif($selectedBranch)
                            {{ $selectedBranch->name }}
                        @else
                            Semua Cabang
                        @endif
                        </span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6"><label class="form-label text-muted fw-semibold">Stok Saat Ini</label><div class="d-flex align-items-center"><span class="fw-bold fs-4 {{ $displayStockQuantity > $displayMinimumStock ? 'text-success' : 'text-danger' }}">{{ number_format($displayStockQuantity, 0, ',', '.') }}</span><small class="text-muted ms-2">{{ $semiFinishedProduct->unit->unit_name ?? '' }}</small></div></div>
                    <div class="col-md-6"><label class="form-label text-muted fw-semibold">Stok Minimum</label><p class="fw-semibold mb-0">{{ number_format($displayMinimumStock, 0, ',', '.') }} <small class="text-muted">{{ $semiFinishedProduct->unit->unit_name ?? '' }}</small></p></div>
                </div>

                {{-- Branch Stock Details --}}
                @if(!$branchForStock && !$selectedBranch && $semiFinishedProduct->semiFinishedBranchStocks->isNotEmpty())
                <hr class="my-4">
                <h6 class="fw-bold mb-3"><i class="bi bi-building me-2"></i>Rincian Stok Per Cabang</h6>
                <ul class="list-group list-group-flush">
                    @foreach($semiFinishedProduct->semiFinishedBranchStocks as $branchStock)
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <div>
                            <div class="fw-semibold">{{ $branchStock->branch->name ?? 'Unknown' }}</div>
                            <small class="text-muted">{{ $branchStock->branch->code ?? '' }}</small>
                        </div>
                        <div class="text-end">
                            <div class="fw-semibold {{ $branchStock->quantity > 0 ? 'text-success' : 'text-danger' }}">
                                {{ number_format($branchStock->quantity, 0, ',', '.') }}
                            </div>
                            <small class="text-muted">{{ $semiFinishedProduct->unit->unit_name ?? '' }}</small>
                        </div>
                    </li>
                    @endforeach
                </ul>
                @endif
            </div>
        </div>
    </div>

    {{-- Action & System Info Column --}}
    <div class="col-lg-4">
        {{-- Actions Card --}}
        <div class="card border-0 shadow-lg mb-4">
            <div class="card-header bg-light py-3"><h6 class="mb-0 fw-bold text-dark"><i class="bi bi-lightning-charge-fill me-2"></i>Aksi</h6></div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('semi-finished-products.edit', array_merge([$semiFinishedProduct], request()->has('branch_id') ? ['branch_id' => request('branch_id')] : [])) }}" class="btn btn-warning"><i class="bi bi-pencil me-2"></i>Edit Bahan</a>
                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal"><i class="bi bi-trash me-2"></i>Hapus Bahan</button>
                </div>
            </div>
        </div>

        {{-- System Info Card --}}
        <div class="card border-0 shadow-lg">
            <div class="card-header bg-light py-3"><h6 class="mb-0 fw-bold text-dark"><i class="bi bi-clock-history me-2"></i>Informasi Sistem</h6></div>
            <div class="card-body text-muted small">
                <p class="mb-2"><strong>Dibuat:</strong><br>{{ $semiFinishedProduct->created_at ? $semiFinishedProduct->created_at->format('d/m/Y H:i:s') : '-' }}</p>
                <p class="mb-0"><strong>Diperbarui:</strong><br>{{ $semiFinishedProduct->updated_at ? $semiFinishedProduct->updated_at->format('d/m/Y H:i:s') : '-' }}</p>
            </div>
        </div>
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<div class="modal fade" id="deleteModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Konfirmasi Hapus</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body">Apakah Anda yakin ingin menghapus <strong>{{ $semiFinishedProduct->name }}</strong>?</div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><form action="{{ route('semi-finished-products.destroy', $semiFinishedProduct) }}" method="POST">@csrf @method('DELETE')<button type="submit" class="btn btn-danger">Ya, Hapus</button></form></div></div></div></div>
@endsection
