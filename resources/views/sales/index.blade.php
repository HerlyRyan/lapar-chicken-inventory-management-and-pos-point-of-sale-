@extends('layouts.app')

@section('title', 'Daftar Penjualan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1" style="color: var(--primary-red);">
            <i class="bi bi-receipt me-2"></i>Daftar Penjualan
            @if(request('branch_id'))
                @php $selectedBranch = $branches->firstWhere('id', request('branch_id')); @endphp
                @if($selectedBranch)
                    <span class="badge bg-info fs-6 ms-2">{{ $selectedBranch->name }}</span>
                @endif
            @else
                <span class="badge bg-secondary fs-6 ms-2">Semua Cabang</span>
            @endif
        </h1>
        <p class="text-muted mb-0">
            Kelola data transaksi penjualan
            @if(request('branch_id'))
                @php $selectedBranch = $branches->firstWhere('id', request('branch_id')); @endphp
                @if($selectedBranch)
                    - {{ $selectedBranch->name }} ({{ $selectedBranch->code }})
                @endif
            @else
                - Tampilkan semua cabang
            @endif
        </p>
    </div>
    <a href="{{ route('sales.create') }}" class="btn btn-primary shadow px-4 d-flex align-items-center" style="min-width: 160px; white-space: nowrap;">
        <i class="bi bi-plus-circle me-2"></i>
        <span>Buat Penjualan Baru</span>
    </a>
</div>

<div class="card border-0 shadow-lg">
    <div class="card-header text-white py-3" style="background: linear-gradient(135deg, #dc2626 0%, #b91c1c 50%, #7f1d1d 100%);">
        <h5 class="mb-0 fw-bold">
            <i class="bi bi-list-ul me-2"></i>Daftar Transaksi Penjualan
        </h5>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        <!-- Search Filter -->
        <form method="GET" action="{{ route('sales.index') }}" class="row g-3 mb-4 table-filter-form">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor transaksi atau pelanggan..." class="form-control">
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
                <select name="status" class="form-select">
                    <option value="all">Semua Status</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="payment_method" class="form-select">
                    <option value="all">Semua Pembayaran</option>
                    <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>Tunai</option>
                    <option value="qris" {{ request('payment_method') == 'qris' ? 'selected' : '' }}>QRIS</option>
                </select>
            </div>
            <div class="col-md-2">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-filter me-1"></i>Filter
                    </button>
                    @if(request('search') || request('branch_id') || request('status') != 'all' || request('payment_method') != 'all' || request('date_from') || request('date_to'))
                        <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-clockwise me-1"></i>Reset
                        </a>
                    @endif
                </div>
            </div>
            <div class="col-md-6">
                <div class="row g-2">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-calendar3"></i></span>
                            <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control" placeholder="Dari Tanggal">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-calendar3"></i></span>
                            <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control" placeholder="Sampai Tanggal">
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover table-striped standard-table mb-0">
                <thead class="sticky-top bg-white">
                    <tr>
                        <th>{!! sortColumn('sale_number', 'No. Transaksi', $sortColumn, $sortDirection) !!}</th>
                        <th>{!! sortColumn('created_at', 'Tanggal', $sortColumn, $sortDirection) !!}</th>
                        <th>Cabang</th>
                        <th>Pelanggan</th>
                        <th>{!! sortColumn('final_amount', 'Total', $sortColumn, $sortDirection) !!}</th>
                        <th>{!! sortColumn('payment_method', 'Pembayaran', $sortColumn, $sortDirection) !!}</th>
                        <th>{!! sortColumn('status', 'Status', $sortColumn, $sortDirection) !!}</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $sale)
                        <tr>
                            <td>{{ $sale->sale_number }}</td>
                            <td>{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $sale->branch->name ?? 'N/A' }}</td>
                            <td>{{ $sale->customer_name ?? 'Umum' }}</td>
                            <td>Rp {{ number_format($sale->final_amount, 0, ',', '.') }}</td>
                            <td>
                                <span class="badge bg-{{ $sale->payment_method === 'cash' ? 'success' : 'info' }}">
                                    {{ $sale->payment_method === 'cash' ? 'Tunai' : 'QRIS' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $sale->status === 'completed' ? 'success' : ($sale->status === 'cancelled' ? 'danger' : 'warning') }}">
                                    {{ $sale->status === 'completed' ? 'Selesai' : ($sale->status === 'cancelled' ? 'Dibatalkan' : ucfirst($sale->status)) }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="{{ route('sales.show', $sale) }}" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="Lihat Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if($sale->status === 'completed')
                                        <button type="button"
                                                class="btn btn-sm btn-danger btn-cancel-sale"
                                                data-action="{{ route('sales.destroy', $sale) }}"
                                                data-sale-number="{{ $sale->sale_number }}"
                                                data-bs-toggle="tooltip"
                                                title="Batalkan Transaksi">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-receipt fs-1 mb-3"></i>
                                    <h5 class="text-muted">Belum ada data penjualan</h5>
                                    <p class="mb-0">Data transaksi penjualan akan muncul di sini</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-4">
            <div>
                <p class="text-muted mb-0">Menampilkan {{ $sales->firstItem() ?? 0 }} - {{ $sales->lastItem() ?? 0 }} dari {{ $sales->total() }} data</p>
            </div>
            <div>
                {{ $sales->links() }}
            </div>
        </div>
    </div>
</div>
<!-- Cancel Sale Confirmation Modal -->
<div class="modal fade" id="cancelSaleModal" tabindex="-1" aria-labelledby="cancelSaleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cancelSaleModalLabel">Konfirmasi Pembatalan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">Yakin ingin membatalkan transaksi <strong id="cancel-sale-number"></strong>?<br>Stok akan dikembalikan ke produk siap jual sesuai jumlah pada transaksi ini.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <form id="cancel-sale-form" method="POST" action="#">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-x-circle me-1"></i>Batalkan Transaksi
                    </button>
                </form>
            </div>
        </div>
    </div>
    </div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modalEl = document.getElementById('cancelSaleModal');
    const saleNumberEl = document.getElementById('cancel-sale-number');
    const formEl = document.getElementById('cancel-sale-form');

    document.querySelectorAll('.btn-cancel-sale').forEach(function(btn){
        btn.addEventListener('click', function(){
            const action = this.getAttribute('data-action');
            const saleNumber = this.getAttribute('data-sale-number') || '';
            formEl.setAttribute('action', action);
            saleNumberEl.textContent = saleNumber;
            const bsModal = new bootstrap.Modal(modalEl);
            bsModal.show();
        });
    });
});
</script>
@endpush
@endsection
