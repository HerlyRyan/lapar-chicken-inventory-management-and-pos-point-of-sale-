@extends('layouts.app')

@section('title', 'Purchase Orders')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1" style="color: var(--primary-red);">
                <i class="bi bi-cart3 me-2"></i>Purchase Orders
            </h1>
            <p class="text-muted mb-0">Kelola pesanan pembelian bahan mentah</p>
        </div>
        <a href="{{ route('purchase-orders.create') }}" class="btn btn-primary shadow">
            <i class="bi bi-plus-circle me-2"></i>Buat Purchase Order
        </a>
    </div>

    <!-- Filter Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form method="GET" action="{{ route('purchase-orders.index') }}">
                        <input type="hidden" name="sort" value="{{ request('sort') }}">
                        <input type="hidden" name="direction" value="{{ request('direction') }}">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="">Semua Status</option>
                                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="ordered" {{ request('status') == 'ordered' ? 'selected' : '' }}>Ordered</option>
                                    <option value="received" {{ request('status') == 'received' ? 'selected' : '' }}>Received</option>
                                    <option value="partially_received" {{ request('status') == 'partially_received' ? 'selected' : '' }}>Partially Received</option>
                                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Supplier</label>
                                <select name="supplier_id" class="form-select">
                                    <option value="">Semua Supplier</option>
                                    @foreach(($suppliers ?? []) as $supplier)
                                        @php($sid = data_get($supplier, 'id'))
                                        <option value="{{ $sid }}" {{ (string)request('supplier_id') === (string)$sid ? 'selected' : '' }}>
                                            {{ data_get($supplier, 'name') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Kata Kunci</label>
                                <input type="text" name="q" class="form-control" placeholder="Cari nomor/kode PO" value="{{ request('q') }}">
                            </div>
                            <div class="col-md-3"></div>
                            <div class="col-md-3">
                                <label class="form-label">Tanggal Mulai</label>
                                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Tanggal Akhir</label>
                                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-outline-primary">
                                        <i class="bi bi-search me-1"></i>Filter
                                    </button>
                                    <a href="{{ route('purchase-orders.index') }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-x-lg me-1"></i>Reset
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Purchase Orders List -->
    <div class="card border-0 shadow-lg">
        <div class="card-header text-white py-3" style="background: linear-gradient(135deg, #dc2626 0%, #ea580c 50%, #eab308 100%);">
            <h5 class="mb-0 fw-bold">
                <i class="bi bi-list-ul me-2"></i>Daftar Purchase Orders
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
            
            @if($purchaseOrders->count() > 0)
                <div class="table-responsive" style="max-height: 70vh;">
                    <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3">{!! sortColumn('order_number', 'Nomor Order', request('sort'), request('direction')) !!}</th>
                                        <th>Supplier</th>
                                        <th>{!! sortColumn('order_date', 'Tanggal', request('sort'), request('direction')) !!}</th>
                                        <th>{!! sortColumn('total_amount', 'Total', request('sort'), request('direction')) !!}</th>
                                        <th>{!! sortColumn('status', 'Status', request('sort'), request('direction')) !!}</th>
                                        <th>Dibuat Oleh</th>
                                        <th width="200" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($purchaseOrders as $order)
                                    <tr>
                                        <td class="ps-3">
                                            <div class="fw-bold text-primary">{{ $order->order_number }}</div>
                                            <small class="text-muted">{{ $order->order_code }}</small>
                                        </td>
                                        <td>
                                            <div class="fw-semibold">{{ $order->supplier->name }}</div>
                                            @if($order->supplier->phone)
                                                <small class="text-muted">
                                                    <i class="bi bi-whatsapp text-success"></i>
                                                    {{ $order->supplier->phone }}
                                                </small>
                                            @endif
                                        </td>
                                        <td>
                                            <div>{{ optional($order->order_date)->format('d/m/Y') }}</div>
                                            @if($order->created_at)
                                                <small class="text-muted">{{ $order->created_at->format('H:i') }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="fw-semibold">
                                                Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                            </div>
                                            <small class="text-muted">{{ $order->items->count() }} item</small>
                                        </td>
                                        <td>
                                            @if($order->status === 'draft')
                                                <span class="badge bg-warning text-dark rounded-pill">
                                                    <i class="bi bi-pencil me-1"></i>Draft
                                                </span>
                                            @elseif($order->status === 'ordered')
                                                <span class="badge bg-success rounded-pill">
                                                    <i class="bi bi-send me-1"></i>Ordered
                                                </span>
                                            @elseif($order->status === 'received')
                                                <span class="badge bg-primary rounded-pill">
                                                    <i class="bi bi-check-circle me-1"></i>Received
                                                </span>
                                            @elseif($order->status === 'partially_received')
                                                <span class="badge bg-info text-dark rounded-pill">
                                                    <i class="bi bi-hourglass-split me-1"></i>Partially Received
                                                </span>
                                            @elseif($order->status === 'rejected')
                                                <span class="badge bg-danger rounded-pill">
                                                    <i class="bi bi-x-circle me-1"></i>Rejected
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="fw-semibold">{{ $order->creator->name }}</div>
                                            <small class="text-muted">{{ $order->creator->role->name ?? 'N/A' }}</small>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex flex-column gap-1">
                                                <!-- Standard action buttons -->
                                                <div class="btn-group btn-group-sm">
                                                    <x-action-buttons
                                                        :viewUrl="route('purchase-orders.show', $order)" 
                                                        :editUrl="$order->canBeEdited() ? route('purchase-orders.edit', $order) : null"
                                                        :deleteUrl="$order->canBeEdited() ? null : null" 
                                                        :showToggle="false"
                                                        itemName="purchase order {{$order->order_number}}"
                                                    />
                                                </div>
                                                
                                                <!-- Special action buttons -->
                                                <div class="btn-group btn-group-sm">
                                                    @if($order->canBeEdited())
                                                        <!-- Order Now Button with WhatsApp -->
                                                        <button type="button" 
                                                                class="btn btn-sm btn-outline-success" 
                                                                onclick="confirmOrder({{ $order->id }}, '{{ $order->order_number }}')"
                                                                title="Pesan Sekarang">
                                                            <i class="bi bi-whatsapp me-1"></i> Pesan
                                                        </button>
                                                    
                                                        <!-- Delete Button -->
                                                        <button type="button" 
                                                                class="btn btn-sm btn-outline-danger" 
                                                                onclick="confirmDelete({{ $order->id }}, '{{ $order->order_number }}')"
                                                                title="Hapus">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    @endif
                                                    
                                                    @if($order->status === 'ordered')
                                                        <!-- Print Button -->
                                                        <a href="{{ route('purchase-orders.print', $order) }}" 
                                                           class="btn btn-sm btn-outline-secondary" 
                                                           target="_blank"
                                                           title="Print">
                                                            <i class="bi bi-print me-1"></i> Print
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if($purchaseOrders->hasPages())
                            <div class="card-footer bg-white border-top">
                                {{ $purchaseOrders->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-shopping-cart fs-1 text-muted mb-3"></i>
                            <h5 class="text-muted">Belum ada Purchase Order</h5>
                            <p class="text-muted">Mulai buat purchase order pertama Anda</p>
                            <a href="{{ route('purchase-orders.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus me-1"></i> Buat Purchase Order
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>
<script>
/**
 * Confirm and mark purchase order as ordered
 * 
 * @param {number} orderId
 * @param {string} orderNumber
 */
function confirmOrder(orderId, orderNumber) {
    Swal.fire({
        title: 'Pesan Sekarang?',
        html: `
            <p>Purchase Order <strong>${orderNumber}</strong> akan dikirim ke supplier melalui WhatsApp.</p>
            <p class="text-warning mb-0">
                <i class="bi bi-exclamation-triangle"></i>
                Setelah dipesan, PO tidak dapat diedit lagi.
            </p>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="bi bi-whatsapp"></i> Ya, Pesan Sekarang',
        cancelButtonText: 'Batal',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            // First try PATCH directly
            return fetch(`/purchase-orders/${orderId}/mark-as-ordered`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrf
                },
                body: JSON.stringify({})
            }).then(async (response) => {
                if (response.ok) return response.json();
                // Fallback for servers blocking PATCH (405/404)
                if (response.status === 405 || response.status === 404) {
                    return fetch(`/purchase-orders/${orderId}/mark-as-ordered`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-HTTP-Method-Override': 'PATCH',
                            'X-CSRF-TOKEN': csrf
                        },
                        body: JSON.stringify({})
                    }).then(fb => {
                        if (!fb.ok) throw new Error(`Fallback failed: ${fb.status}`);
                        return fb.json();
                    });
                }
                throw new Error(`HTTP ${response.status}`);
            }).catch(error => {
                Swal.showValidationMessage(`Request failed: ${error}`);
            });
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed) {
            if (result.value && result.value.success) {
                Swal.fire({
                    title: 'Berhasil!',
                    text: result.value.message,
                    icon: 'success',
                    confirmButtonColor: '#007bff'
                }).then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: result.value ? result.value.message : 'Terjadi kesalahan',
                    icon: 'error',
                    confirmButtonColor: '#dc3545'
                });
            }
        }
    });
}

/**
 * Confirm and delete purchase order
 * 
 * @param {number} orderId
 * @param {string} orderNumber
 */
function confirmDelete(orderId, orderNumber) {
    Swal.fire({
        title: 'Hapus Purchase Order?',
        html: `
            <p>Purchase Order <strong>${orderNumber}</strong> akan dihapus permanen.</p>
            <p class="text-danger mb-0">
                <i class="bi bi-exclamation-triangle"></i>
                Tindakan ini tidak dapat dibatalkan!
            </p>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="bi bi-trash"></i> Ya, Hapus',
        cancelButtonText: 'Batal',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            return fetch(`/purchase-orders/${orderId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrf
                }
            })
            .then(response => {
                if (!response.ok) throw new Error(`HTTP ${response.status}`);
                return response.json();
            })
            .catch(error => {
                Swal.showValidationMessage(`Request failed: ${error}`);
            });
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed) {
            if (result.value && result.value.success) {
                Swal.fire({
                    title: 'Berhasil!',
                    text: result.value.message,
                    icon: 'success',
                    confirmButtonColor: '#007bff'
                }).then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: result.value ? result.value.message : 'Terjadi kesalahan',
                    icon: 'error',
                    confirmButtonColor: '#dc3545'
                });
            }
        }
    });
}
</script>
@endpush
