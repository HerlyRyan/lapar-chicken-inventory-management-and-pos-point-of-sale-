@extends('layouts.app')

@section('title', 'Master Produk Siap Jual')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1" style="color: var(--primary-red);">
            <i class="bi bi-basket3 me-2"></i>Master Produk Siap Jual
            @if($branchForStock)
                <span class="badge bg-info fs-6 ms-2">{{ $branchForStock->name }}</span>
            @elseif($selectedBranch)
                <span class="badge bg-info fs-6 ms-2">{{ $selectedBranch->name }}</span>
            @else
                <span class="badge bg-secondary fs-6 ms-2">Semua Cabang</span>
            @endif
        </h1>
        <p class="text-muted mb-0">
            Kelola data produk siap jual untuk penjualan
            @if($branchForStock)
                - {{ $branchForStock->name }} ({{ $branchForStock->code }})
            @elseif($selectedBranch)
                - {{ $selectedBranch->name }} ({{ $selectedBranch->code }})
            @else
                - Tampilkan semua cabang
            @endif
        </p>
    </div>
    <a href="{{ route('finished-products.create', request()->has('branch_id') ? ['branch_id' => request('branch_id')] : []) }}" class="btn btn-primary shadow px-4 d-flex align-items-center" style="min-width: 160px; white-space: nowrap;">
        <i class="bi bi-plus-circle me-2"></i>
        <span>Tambah Produk Siap Jual</span>
    </a>
</div>

<div class="card border-0 shadow-lg">
    <div class="card-header text-white py-3" style="background: linear-gradient(135deg, #22c55e 0%, #16a34a 50%, #15803d 100%);">
        <h5 class="mb-0 fw-bold">
            <i class="bi bi-list-ul me-2"></i>Daftar Produk Siap Jual
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
        
        <!-- Search Filter -->
        <form method="GET" action="" class="row g-3 mb-4 table-filter-form">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, kode, atau deskripsi produk siap jual..." class="form-control">
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
                        <a href="{{ route('finished-products.index', request()->has('branch_id') ? ['branch_id' => request('branch_id')] : []) }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-clockwise me-1"></i>Reset
                        </a>
                    @endif
                </div>
            </div>
            @if(request()->has('branch_id'))
                <input type="hidden" name="branch_id" value="{{ request('branch_id') }}">
            @endif
        </form>

        <div class="table-responsive">
            <table class="table table-hover table-striped standard-table mb-0">
                <thead class="sticky-top bg-white">
                    <tr>
                        <th>{!! sortColumn('code', 'Kode', $sortColumn, $sortDirection) !!}</th>
                        <th style="width: 80px;">Gambar</th>
                        <th>{!! sortColumn('name', 'Nama Produk', $sortColumn, $sortDirection) !!}</th>
                        <th>Kategori</th>
                        <th>Satuan</th>
                        <th>Stok di Cabang</th>
                        <th>Stok Minimum</th>
                        <th>{!! sortColumn('price', 'Harga Jual', $sortColumn, $sortDirection) !!}</th>
                        <th>Status</th>
                        <th class="text-center" style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($finishedProducts as $product)
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
                                @if($product->photo)
                                    <img src="{{ \Storage::url(trim($product->photo)) }}" alt="{{ $product->name }}" class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;" onerror="this.src='{{ asset('images/default-product.png') }}';">
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
                            <td>{{ $product->unit ? $product->unit->unit_name : '-' }}</td>
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
                                    <small class="text-muted">Semua Cabang</small>
                                @endif
                            </td>
                            <td>{{ number_format($minimumStock, 0) }}</td>
                            <td>Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                            <td>
                                @if($product->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Non-Aktif</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <x-action-buttons
                                    :viewUrl="route('finished-products.show', array_merge([$product->id], request()->has('branch_id') ? ['branch_id' => request('branch_id')] : []))" 
                                    :editUrl="route('finished-products.edit', array_merge([$product->id], request()->has('branch_id') ? ['branch_id' => request('branch_id')] : []))" 
                                    :deleteUrl="route('finished-products.destroy', $product->id)" 
                                    :toggleUrl="route('finished-products.toggle-status', $product->id)"
                                    :isActive="$product->is_active"
                                    :showToggle="true"
                                    itemName="produk {{$product->name}}"
                                />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-5">
                                <i class="bi bi-inbox fs-1 text-muted mb-2"></i>
                                <h5 class="text-muted">Data produk siap jual tidak ditemukan.</h5>
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
                @if($finishedProducts instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    <p class="text-muted mb-0">Menampilkan {{ $finishedProducts->firstItem() ?? 0 }}-{{ $finishedProducts->lastItem() ?? 0 }} dari {{ $finishedProducts->total() }} data</p>
                @else
                    <p class="text-muted mb-0">Menampilkan {{ count($finishedProducts) }} data</p>
                @endif
            </div>
            <div>
                @if($finishedProducts instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    {{ $finishedProducts->withQueryString()->links() }}
                @endif
            </div>
        </div>
        
        <!-- No modals needed anymore -->
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
        html: `Apakah Anda yakin ingin menghapus produk siap jual <br><strong>${productName}</strong>?`,
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
            form.action = `/finished-products/${productId}`;
            
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
