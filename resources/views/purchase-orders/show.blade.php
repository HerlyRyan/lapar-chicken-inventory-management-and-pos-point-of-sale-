@extends('layouts.app')

@section('title', 'Detail Purchase Order')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1" style="color: var(--primary-red);">
                <i class="bi bi-eye me-2"></i>Detail Purchase Order
            </h1>
            <p class="text-muted mb-0">{{ $purchaseOrder->order_number }}</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('purchase-orders.index') }}" class="btn btn-outline-secondary shadow-sm">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
            @if($purchaseOrder->canBeEdited())
                <a href="{{ route('purchase-orders.edit', $purchaseOrder) }}" class="btn btn-warning shadow-sm">
                    <i class="bi bi-pencil-square me-1"></i> Edit
                </a>
            @endif
            @if($purchaseOrder->status === 'ordered')
                <a href="{{ route('purchase-orders.print', $purchaseOrder) }}" class="btn btn-outline-dark shadow-sm" target="_blank">
                    <i class="bi bi-printer me-1"></i> Print
                </a>
            @endif
        </div>
    </div>

    <!-- Purchase Order Details -->
    <div class="row">
        <!-- Basic Information -->
        <div class="col-md-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header text-white py-3" style="background: linear-gradient(135deg, #dc2626 0%, #ea580c 50%, #eab308 100%);">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-info-circle me-2"></i>
                        Informasi Purchase Order
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-semibold text-muted">Nomor Order:</td>
                                    <td class="fw-bold text-primary">{{ $purchaseOrder->order_number }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold text-muted">Kode Order:</td>
                                    <td>{{ $purchaseOrder->order_code }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold text-muted">Status:</td>
                                    <td>
                                        @if($purchaseOrder->status === 'draft')
                                            <span class="badge bg-warning text-dark">
                                                <i class="bi bi-pencil me-1"></i>Draft
                                            </span>
                                        @elseif($purchaseOrder->status === 'ordered')
                                            <span class="badge bg-success">
                                                <i class="bi bi-send me-1"></i>Ordered
                                            </span>
                                        @elseif($purchaseOrder->status === 'received')
                                            <span class="badge bg-primary">
                                                <i class="bi bi-check-circle me-1"></i>Received
                                            </span>
                                        @elseif($purchaseOrder->status === 'partially_received')
                                            <span class="badge bg-info text-dark">
                                                <i class="bi bi-hourglass-split me-1"></i>Partially Received
                                            </span>
                                        @elseif($purchaseOrder->status === 'rejected')
                                            <span class="badge bg-danger">
                                                <i class="bi bi-x-circle me-1"></i>Rejected
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold text-muted">Tanggal Dibuat:</td>
                                    <td>{{ $purchaseOrder->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-semibold text-muted">Supplier:</td>
                                    <td class="fw-bold">{{ $purchaseOrder->supplier->name }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold text-muted">Telepon:</td>
                                    <td>
                                        @if($purchaseOrder->supplier->phone)
                                            <i class="bi bi-whatsapp text-success me-1"></i>
                                            {{ $purchaseOrder->supplier->phone }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold text-muted">Alamat:</td>
                                    <td>{{ $purchaseOrder->supplier->address ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold text-muted">Pengiriman Diharapkan:</td>
                                    <td>
                                        @if($purchaseOrder->requested_delivery_date)
                                            {{ $purchaseOrder->requested_delivery_date->format('d/m/Y') }}
                                        @else
                                            <span class="text-muted">Tidak ditentukan</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($purchaseOrder->notes)
                        <div class="mt-3">
                            <h6 class="fw-semibold text-muted">Catatan:</h6>
                            <div class="p-3 bg-light rounded">
                                {{ $purchaseOrder->notes }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Summary Information -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header text-white py-3" style="background: linear-gradient(135deg, #dc2626 0%, #ea580c 50%, #eab308 100%);">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-pie-chart me-2"></i>
                        Ringkasan
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <h3 class="text-success mb-1">
                            Rp {{ number_format($purchaseOrder->total_amount, 0, ',', '.') }}
                        </h3>
                        <p class="text-muted mb-3">Total Pesanan</p>

                        <div class="row text-center">
                            <div class="col-6">
                                <h5 class="text-primary mb-1">{{ $purchaseOrder->items->count() }}</h5>
                                <small class="text-muted">Item</small>
                            </div>
                            <div class="col-6">
                                <h5 class="text-info mb-1">{{ $purchaseOrder->items->sum('quantity') }}</h5>
                                <small class="text-muted">Total Qty</small>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="small">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Dibuat oleh:</span>
                            <span class="fw-semibold">{{ $purchaseOrder->creator->name }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Role:</span>
                            <span>{{ $purchaseOrder->creator->role->name ?? 'N/A' }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Terakhir update:</span>
                            <span>{{ $purchaseOrder->updated_at->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            @if($purchaseOrder->canBeEdited())
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="card-title mb-0">
                            <i class="bi bi-bolt text-warning me-2"></i>
                            Aksi Cepat
                        </h6>
                    </div>
                    <div class="card-body">
                        @if($purchaseOrder->canBeOrdered())
                            <button type="button" 
                                    class="btn btn-success w-100 mb-2"
                                    onclick="confirmOrder({{ $purchaseOrder->id }}, '{{ $purchaseOrder->order_number }}')">
                                <i class="bi bi-whatsapp me-1"></i>
                                Pesan Sekarang
                            </button>
                        @endif

                        <a href="{{ route('purchase-orders.edit', $purchaseOrder) }}" class="btn btn-warning w-100 mb-2">
                            <i class="bi bi-edit me-1"></i> Edit Purchase Order
                        </a>

                        <button type="button" 
                                class="btn btn-outline-danger w-100"
                                onclick="confirmDelete({{ $purchaseOrder->id }}, '{{ $purchaseOrder->order_number }}')">
                            <i class="bi bi-trash me-1"></i> Hapus
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Items List -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-lg">
                <div class="card-header text-white py-3" style="background: linear-gradient(135deg, #dc2626 0%, #ea580c 50%, #eab308 100%);">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-box-seam me-2"></i>
                        Daftar Item ({{ $purchaseOrder->items->count() }} item)
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">#</th>
                                    <th>Bahan Mentah</th>
                                    <th>Kode</th>
                                    <th class="text-center">Kuantitas</th>
                                    <th class="text-center">Satuan</th>
                                    <th class="text-end">Harga Satuan</th>
                                    <th class="text-end pe-3">Total Harga</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($purchaseOrder->items as $index => $item)
                                    <tr>
                                        <td class="ps-3">{{ $index + 1 }}</td>
                                        <td>
                                            <div class="fw-semibold">{{ $item->rawMaterial->name }}</div>
                                            @if($item->rawMaterial->category)
                                                <small class="text-muted">{{ $item->rawMaterial->category->name }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark">
                                                {{ $item->rawMaterial->code ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="fw-semibold">{{ number_format($item->quantity, 0, ',', '.') }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-info text-white">
                                                {{ $item->unit_name ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <span class="fw-semibold">
                                                Rp {{ number_format($item->unit_price, 0, ',', '.') }}
                                            </span>
                                        </td>
                                        <td class="text-end pe-3">
                                            <span class="fw-bold text-primary">
                                                Rp {{ number_format($item->total_price, 0, ',', '.') }}
                                            </span>
                                        </td>
                                    </tr>
                                    @if($item->notes)
                                        <tr>
                                            <td colspan="7" class="ps-5 pb-2">
                                                <small class="text-muted">
                                                    <i class="bi bi-sticky-note me-1"></i>
                                                    {{ $item->notes }}
                                                </small>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="6" class="text-end fw-bold">TOTAL KESELURUHAN:</td>
                                    <td class="text-end pe-3">
                                        <span class="fw-bold text-success fs-5">
                                            Rp {{ number_format($purchaseOrder->total_amount, 0, ',', '.') }}
                                        </span>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
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
            // Try PATCH first
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
                // Fallback for environments blocking PATCH
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
                    window.location.href = '{{ route("purchase-orders.index") }}';
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
