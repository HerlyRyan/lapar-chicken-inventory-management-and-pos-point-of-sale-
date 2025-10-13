@extends('layouts.app')

@section('title', 'Master Bahan Setengah Jadi')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1" style="color: var(--primary-red);">
            <i class="bi bi-box-seam me-2"></i>Master Bahan Setengah Jadi
        </h1>
        <p class="text-muted mb-0">Kelola data bahan setengah jadi hasil produksi internal</p>
    </div>
    <a href="{{ route('semi-finished-products.create') }}{{ request('branch_id') ? '?branch_id=' . request('branch_id') : '' }}" class="btn btn-primary shadow px-4 d-flex align-items-center" style="min-width: 160px; white-space: nowrap;">
        <i class="bi bi-plus-circle me-2"></i>
        <span>Tambah Bahan Setengah Jadi</span>
    </a>
</div>

<div class="card border-0 shadow-lg">
    <div class="card-header text-white py-3" style="background: linear-gradient(135deg, #0ea5e9 0%, #06b6d4 50%, #10b981 100%);">
        <h5 class="mb-0 fw-bold">
            <i class="bi bi-list-ul me-2"></i>Daftar Bahan Setengah Jadi
        </h5>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>{!! session('warning') !!}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Branch Info -->
        @if($branchForStock)
            <div class="alert alert-info mb-3">
                <i class="bi bi-building me-2"></i>
                <strong>Cabang Aktif:</strong> {{ $branchForStock->name }} ({{ $branchForStock->code }})
                <small class="d-block mt-1">Menampilkan stok untuk cabang ini</small>
            </div>
        @elseif($selectedBranch)
            <div class="alert alert-info mb-3">
                <i class="bi bi-building me-2"></i>
                <strong>Cabang Dipilih:</strong> {{ $selectedBranch->name }} ({{ $selectedBranch->code }})
                <small class="d-block mt-1">Menampilkan stok untuk cabang ini</small>
            </div>
        @else
            <div class="alert alert-secondary mb-3">
                <i class="bi bi-buildings me-2"></i>
                <strong>Tampilan:</strong> Semua Cabang
                <small class="d-block mt-1">Menampilkan total stok dari semua cabang</small>
            </div>
        @endif
        
        <!-- Search Filter -->
        <form method="GET" action="" class="row g-3 mb-4 table-filter-form">
            @if(request('branch_id'))
                <input type="hidden" name="branch_id" value="{{ request('branch_id') }}">
            @endif
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, kode, atau deskripsi bahan setengah jadi..." class="form-control">
                </div>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="all">Semua Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                </select>
            </div>
            <div class="col-md-3">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-filter me-1"></i>Filter
                    </button>
                    @if(request('search') || request('status') != 'all')
                        <a href="{{ route('semi-finished-products.index') }}{{ request('branch_id') ? '?branch_id=' . request('branch_id') : '' }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-clockwise me-1"></i>Reset
                        </a>
                    @endif
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover table-striped standard-table mb-0">
                <thead class="sticky-top bg-white">
                    <tr>
                        <th>{!! sortColumn('code', 'Kode', $sortColumn, $sortDirection) !!}</th>
                        <th style="width: 80px;">Gambar</th>
                        <th>{!! sortColumn('name', 'Nama Bahan', $sortColumn, $sortDirection) !!}</th>
                        <th>Kategori</th>
                        <th>Satuan</th>
                        <th>Stok Saat Ini</th>
                        <th>{!! sortColumn('minimum_stock', 'Stok Minimum', $sortColumn, $sortDirection) !!}</th>
                        <th>{!! sortColumn('production_cost', 'Biaya Produksi', $sortColumn, $sortDirection) !!}</th>
                        <th>Status</th>
                        <th class="text-center" style="width: 180px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($semiFinishedProducts as $product)
                        @php
                            $currentStock = $product->display_stock_quantity ?? 0;
                            $minimumStock = $product->minimum_stock ?? 0;
                            $isLowStock = $minimumStock > 0 && $currentStock <= $minimumStock;
                        @endphp
                        <tr>
                            <td>
                                @if($product->code)
                                    <span class="badge bg-danger text-warning">{{ $product->code }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($product->image && file_exists(public_path($product->image)))
                                    <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                                @else
                                    <div class="bg-light border rounded d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                        <i class="bi bi-image text-muted fs-4"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold">{{ $product->name }}</div>
                                <small class="text-muted">{{ Str::limit($product->description, 50) }}</small>
                            </td>
                            <td>
                                @if($product->category)
                                    <span class="badge bg-info bg-opacity-25 text-info-emphasis border border-info-subtle">
                                        <i class="bi bi-tag"></i> {{ $product->category->name }}
                                    </span>
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ is_object($product->unit) ? $product->unit->unit_name : (is_string($product->unit) ? $product->unit : '-') }}</td>
                            <td>
                                <div class="fw-bold fs-5 {{ $isLowStock ? 'text-danger' : 'text-success' }}">
                                    {{ number_format($currentStock, 0) }}
                                    @if($isLowStock)
                                        <i class="bi bi-exclamation-triangle-fill text-warning ms-1" title="Stok di bawah minimum"></i>
                                    @endif
                                </div>
                                @if($branchForStock)
                                    <small class="text-muted">{{ $branchForStock->code }}</small>
                                @elseif($selectedBranch)
                                    <small class="text-muted">{{ $selectedBranch->code }}</small>
                                @else
                                    <small class="text-muted">Total</small>
                                @endif
                            </td>
                            <td>{{ number_format($minimumStock, 0) }}</td>
                            <td>Rp {{ number_format($product->production_cost, 0, ',', '.') }}</td>
                            <td>
                                @if($product->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Non-Aktif</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <x-action-buttons
                                    :viewUrl="route('semi-finished-products.show', $product->id)" 
                                    :editUrl="route('semi-finished-products.edit', $product->id)"
                                    :deleteUrl="route('semi-finished-products.destroy', $product->id)" 
                                    :toggleUrl="route('semi-finished-products.toggle-status', $product->id)"
                                    :isActive="$product->is_active"
                                    :showToggle="true"
                                    itemName="produk setengah jadi {{$product->name}}"
                                />
                                @if($branchForStock || $selectedBranch)
                                    <div class="btn-group btn-group-sm mt-1" role="group">
                                        <a href="{{ route('semi-finished-products.show', $product) }}{{ request('branch_id') ? '?branch_id=' . request('branch_id') . '&action=stock-in' : '?action=stock-in' }}" 
                                           class="btn btn-outline-success" title="Stok Masuk">
                                            <i class="bi bi-plus-circle"></i> In
                                        </a>
                                        <a href="{{ route('semi-finished-products.show', $product) }}{{ request('branch_id') ? '?branch_id=' . request('branch_id') . '&action=stock-out' : '?action=stock-out' }}" 
                                           class="btn btn-outline-warning" title="Stok Keluar">
                                            <i class="bi bi-dash-circle"></i> Out
                                        </a>
                                        <a href="{{ route('semi-finished-products.show', $product) }}{{ request('branch_id') ? '?branch_id=' . request('branch_id') . '&action=stock-return' : '?action=stock-return' }}" 
                                           class="btn btn-outline-info" title="Return Stok">
                                            <i class="bi bi-arrow-return-left"></i> Return
                                        </a>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-5">
                                <i class="bi bi-inbox fs-1 text-muted mb-2"></i>
                                <h5 class="text-muted">Data bahan setengah jadi tidak ditemukan.</h5>
                                <p class="text-muted">Coba kata kunci lain atau tambahkan data baru.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-4">
            <div>
                @if($semiFinishedProducts instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    <p class="text-muted mb-0">Menampilkan {{ $semiFinishedProducts->firstItem() ?? 0 }}-{{ $semiFinishedProducts->lastItem() ?? 0 }} dari {{ $semiFinishedProducts->total() }} data</p>
                @else
                    <p class="text-muted mb-0">Menampilkan {{ count($semiFinishedProducts) }} data</p>
                @endif
            </div>
            <div>
                @if($semiFinishedProducts instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    {{ $semiFinishedProducts->withQueryString()->links() }}
                @endif
            </div>
        </div>


    </div>
</div>



@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<style>
    .table-responsive thead th {
        position: sticky;
        top: 0;
        z-index: 1;
        background-color: #f8f9fa; /* Match table-light bg color */
        box-shadow: inset 0 -2px 0 #dee2e6; /* Optional: to add a bottom border */
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmDelete(productId, productName) {
    Swal.fire({
        title: 'Konfirmasi Hapus',
        html: `Apakah Anda yakin ingin menghapus bahan setengah jadi <br><strong>${productName}</strong>?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="bi bi-trash me-1"></i>Ya, Hapus!',
        cancelButtonText: '<i class="bi bi-x-circle me-1"></i>Batal',
        reverseButtons: true,
        focusCancel: true,
        customClass: {
            confirmButton: 'btn btn-danger',
            cancelButton: 'btn btn-secondary'
        },
        buttonsStyling: false
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: 'Menghapus...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading()
                }
            });
            
            // Create and submit form
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/semi-finished-products/${productId}`;
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            
            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';
            
            form.appendChild(csrfToken);
            form.appendChild(methodField);
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>
@endpush
