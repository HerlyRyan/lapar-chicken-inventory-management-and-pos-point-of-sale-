@extends('layouts.app')

@section('title', 'Detail Produk Siap Jual - ' . $finishedProduct->name)

@section('content')
{{-- Header --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1" style="color: var(--primary-red);">
            <i class="bi bi-eye me-2"></i>Detail Produk Siap Jual
        </h1>
        <p class="text-muted mb-0">
            Informasi lengkap: {{ $finishedProduct->name }}
        </p>
    </div>
    <a href="{{ route('finished-products.index', request()->has('branch_id') ? ['branch_id' => request('branch_id')] : []) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Kembali
    </a>
</div>

<div class="row">
    {{-- Main Content Column --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-lg mb-4">
            <div class="card-header text-white py-3" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 50%, #1d4ed8 100%);">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-info-circle me-2"></i>Informasi: {{ $finishedProduct->name }}
                </h5>
            </div>
            <div class="card-body p-4">
                {{-- Image Section --}}
                <div class="text-center mb-4">
                    <x-product-image :src="$finishedProduct->photo ?: $finishedProduct->image" :alt="$finishedProduct->name" class="rounded border shadow-sm" height="300" />
                </div>

                <hr>

                {{-- Product Details Section --}}
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        @if($finishedProduct->code)
                            <p class="text-muted mb-2"><i class="bi bi-upc me-1"></i>Kode: <span class="fw-semibold">{{ $finishedProduct->code }}</span></p>
                        @endif
                    </div>
                    @if($finishedProduct->is_active)
                        <span class="badge bg-success fs-6 px-3 py-2"><i class="bi bi-check-circle me-1"></i>Aktif</span>
                    @else
                        <span class="badge bg-secondary fs-6 px-3 py-2"><i class="bi bi-x-circle me-1"></i>Tidak Aktif</span>
                    @endif
                </div>

                @if($finishedProduct->description)
                    <p class="mb-3 fst-italic text-muted">"{{ $finishedProduct->description }}"</p>
                @else
                    <p class="mb-3 fst-italic text-muted">Tidak ada deskripsi.</p>
                @endif

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-dark fw-bold">Kode</label>
                        <h5><span class="badge bg-primary">{{ $finishedProduct->code }}</span></h5>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-dark fw-bold">Kategori</label>
                        @if($finishedProduct->category)
                            <h5><span class="badge bg-info">{{ $finishedProduct->category->name }}</span></h5>
                        @else
                            <h5 class="fw-normal text-muted">-</h5>
                        @endif
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-dark fw-bold">Satuan</label>
                        <h5 class="fw-normal">{{ $finishedProduct->unit ? (is_object($finishedProduct->unit) ? $finishedProduct->unit->unit_name : $finishedProduct->unit) : '-' }}</h5>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-dark fw-bold">Harga Jual</label>
                        <h5 class="fw-bold text-success">
                            @if($finishedProduct->price)
                                Rp {{ number_format($finishedProduct->price, 0, ',', '.') }}
                            @else
                                -
                            @endif
                        </h5>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-dark fw-bold">Modal Dasar</label>
                        <h5 class="fw-bold text-secondary">
                            @if(!is_null($finishedProduct->production_cost))
                                Rp {{ number_format($finishedProduct->production_cost, 0, ',', '.') }}
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
                    <div class="col-md-6"><label class="form-label text-muted fw-semibold">Stok Saat Ini</label><div class="d-flex align-items-center"><span class="fw-bold fs-4 {{ $displayStockQuantity > ($finishedProduct->minimum_stock ?? 0) ? 'text-success' : 'text-danger' }}">{{ number_format($displayStockQuantity, 0, ',', '.') }}</span><small class="text-muted ms-2">{{ $finishedProduct->unit->unit_name ?? '' }}</small></div></div>
                    <div class="col-md-6"><label class="form-label text-muted fw-semibold">Stok Minimum</label><p class="fw-semibold mb-0">{{ number_format($finishedProduct->minimum_stock ?? 0, 0, ',', '.') }} <small class="text-muted">{{ $finishedProduct->unit->unit_name ?? '' }}</small></p></div>
                </div>

                {{-- Branch Stock Details --}}
                @if(!$branchForStock && !$selectedBranch)
                <hr class="my-4">
                <h6 class="fw-bold mb-3"><i class="bi bi-building me-2"></i>Rincian Stok Per Cabang</h6>
                <ul class="list-group list-group-flush">
                    @foreach($branches->where('type', 'branch') as $branch)
                    @php
                        $branchStock = $finishedProduct->finishedBranchStocks->firstWhere('branch_id', $branch->id);
                        $quantity = $branchStock ? $branchStock->quantity : 0;
                    @endphp
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <div>
                            <div class="fw-semibold">{{ $branch->name ?? 'Unknown' }}</div>
                            <small class="text-muted">{{ $branch->code ?? '' }}</small>
                        </div>
                        <div class="text-end">
                            <div class="fw-semibold {{ $quantity > 0 ? 'text-success' : 'text-danger' }}">
                                {{ number_format($quantity, 0, ',', '.') }}
                            </div>
                            <small class="text-muted">{{ $finishedProduct->unit->unit_name ?? '' }}</small>
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
                    <a href="{{ route('finished-products.edit', array_merge([$finishedProduct], request()->has('branch_id') ? ['branch_id' => request('branch_id')] : [])) }}" class="btn btn-warning"><i class="bi bi-pencil me-2"></i>Edit Produk</a>
                    <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#adjustStockModal"><i class="bi bi-arrow-up-down me-2"></i>Sesuaikan Stok</button>
                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal"><i class="bi bi-trash me-2"></i>Hapus Produk</button>
                </div>
            </div>
        </div>

        {{-- System Info Card --}}
        <div class="card border-0 shadow-lg">
            <div class="card-header bg-light py-3"><h6 class="mb-0 fw-bold text-dark"><i class="bi bi-clock-history me-2"></i>Informasi Sistem</h6></div>
            <div class="card-body text-muted small">
                <p class="mb-2"><strong>Dibuat:</strong><br>{{ $finishedProduct->created_at ? $finishedProduct->created_at->format('d/m/Y H:i:s') : '-' }}</p>
                <p class="mb-0"><strong>Diperbarui:</strong><br>{{ $finishedProduct->updated_at ? $finishedProduct->updated_at->format('d/m/Y H:i:s') : '-' }}</p>
            </div>
        </div>
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<div class="modal fade" id="deleteModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Konfirmasi Hapus</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body">Apakah Anda yakin ingin menghapus <strong>{{ $finishedProduct->name }}</strong>?</div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><form action="{{ route('finished-products.destroy', $finishedProduct) }}" method="POST">@csrf @method('DELETE')<button type="submit" class="btn btn-danger">Ya, Hapus</button></form></div></div></div></div>

{{-- Stock Adjustment Modal --}}
<div class="modal fade" id="adjustStockModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Sesuaikan Stok - {{ $finishedProduct->name }}</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form action="#" method="POST"> {{-- TODO: Add route for stock adjustment --}}
                @csrf
                <div class="modal-body">
                    <div class="mb-3"><label for="current_stock" class="form-label">Stok Saat Ini</label><input type="number" class="form-control" id="current_stock" value="{{ $displayStockQuantity }}" readonly>@if($branchForStock)<small class="form-text text-muted">Stok untuk {{ $branchForStock->name }}</small>@else<small class="form-text text-muted">Total stok semua cabang</small>@endif</div>
                    <div class="mb-3"><label for="adjustment_type" class="form-label">Jenis Penyesuaian</label><select name="adjustment_type" id="adjustment_type" class="form-select"><option value="add">Tambah Stok</option><option value="subtract">Kurangi Stok</option><option value="set">Set Stok Baru</option></select></div>
                    <div class="mb-3"><label for="adjustment_quantity" class="form-label">Jumlah</label><input type="number" name="adjustment_quantity" id="adjustment_quantity" class="form-control" step="0.01" min="0" required></div>
                    <div class="mb-3"><label for="adjustment_reason" class="form-label">Alasan Penyesuaian</label><textarea name="adjustment_reason" id="adjustment_reason" class="form-control" rows="3" placeholder="Alasan penyesuaian stok"></textarea></div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Simpan Penyesuaian</button></div>
            </form>
        </div>
    </div>
</div>
@endsection
