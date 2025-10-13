@extends('layouts.app')

@section('title', 'Detail Penerimaan Barang')

@push('styles')
<style>
 /* Compact page spacing adjustments */
 .compact-page .card-header { padding: .5rem .75rem !important; }
 .compact-page .card-body { padding: .75rem !important; }
 .compact-page .table > :not(caption) > * > * { padding: .35rem .5rem !important; }
 .compact-page .btn { padding: .35rem .6rem !important; font-size: .875rem !important; }
 .compact-page h4, .compact-page h6 { margin-bottom: .5rem !important; }
 .compact-page .img-thumbnail { padding: .15rem !important; }
</style>
@endpush

@section('content')
<div class="container-fluid compact-page">
    <!-- Page Header -->
    <div class="row g-2 mb-3">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <h4 class="mb-1 fw-bold text-primary">
                                <i class="bi bi-truck me-2"></i>Detail Penerimaan Barang
                            </h4>
                            <p class="text-muted mb-0">{{ $purchaseReceipt->receipt_number }}</p>
                        </div>
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="{{ route('purchase-receipts.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Kembali
                            </a>
                            <a href="{{ route('purchase-receipts.edit', $purchaseReceipt) }}" class="btn btn-warning">
                                <i class="bi bi-pencil-square me-2"></i>Edit
                            </a>
                            <button type="button" class="btn btn-danger" onclick="confirmDelete({{ $purchaseReceipt->id }}, '{{ $purchaseReceipt->receipt_number }}')">
                                <i class="bi bi-trash me-2"></i>Hapus
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Receipt Information -->
    <div class="row g-2 mb-3">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">Informasi Penerimaan</h6>
                </div>
                <div class="card-body">
                    @include('purchase-receipts.partials.alerts')
                    <div class="row g-2">
                        <div class="col-md-6">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td class="fw-bold">Nomor Penerimaan:</td>
                                    <td>{{ $purchaseReceipt->receipt_number }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Tanggal Penerimaan:</td>
                                    <td>{{ $purchaseReceipt->receipt_date->format('d/m/Y') }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Status:</td>
                                    <td>
                                        @include('purchase-receipts.partials.status-badge', ['status' => $purchaseReceipt->status])
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Diterima Oleh:</td>
                                    <td>{{ optional($purchaseReceipt->receiver)->name ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td class="fw-bold">Nomor Pesanan:</td>
                                    <td>
                                        <a href="{{ route('purchase-orders.show', $purchaseReceipt->purchaseOrder) }}" 
                                           class="text-decoration-none">
                                            {{ $purchaseReceipt->purchaseOrder->order_number }}
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Supplier:</td>
                                    <td>{{ $purchaseReceipt->purchaseOrder->supplier->name }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Tanggal Dibuat:</td>
                                    <td>{{ $purchaseReceipt->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    @if($purchaseReceipt->notes)
                        <div class="mt-3">
                            <h6 class="fw-bold">Catatan:</h6>
                            <p class="text-muted">{{ $purchaseReceipt->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Receipt Photo -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">Foto Bukti Penerimaan</h6>
                </div>
                <div class="card-body text-center">
                    @if($purchaseReceipt->receipt_photo)
                        <img src="{{ Storage::url($purchaseReceipt->receipt_photo) }}" 
                             class="img-fluid rounded" 
                             alt="Foto Penerimaan"
                             style="max-height: 200px;">
                        <div class="mt-2">
                            <a href="{{ Storage::url($purchaseReceipt->receipt_photo) }}" 
                               target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-box-arrow-up-right me-1"></i>Lihat Penuh
                            </a>
                        </div>
                    @else
                        <div class="text-muted py-4">
                            <i class="bi bi-image fs-2 mb-2"></i>
                            <br>Tidak ada foto
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Receipt Items -->
    <div class="row g-2">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">Detail Item Penerimaan</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Bahan</th>
                                    <th>Dipesan</th>
                                    <th>Diterima</th>
                                    <th>Ditolak</th>
                                    <th>Status</th>
                                    <th>Foto</th>
                                    <th>Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($purchaseReceipt->items as $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div>
                                                    <div class="fw-bold">{{ $item->rawMaterial->name }}</div>
                                                    <small class="text-muted">{{ $item->rawMaterial->code }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            {{ number_format($item->ordered_quantity, 0, ',', '.') }} 
                                            {{ optional($item->rawMaterial->unit)->name }}
                                        </td>
                                        <td>
                                            <span class="fw-bold text-success">
                                                {{ number_format($item->received_quantity, 0, ',', '.') }} 
                                                {{ optional($item->rawMaterial->unit)->name }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($item->rejected_quantity > 0)
                                                <span class="fw-bold text-danger">
                                                    {{ number_format($item->rejected_quantity, 0, ',', '.') }} 
                                                    {{ $item->rawMaterial->unit->name }}
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @include('purchase-receipts.partials.status-badge', ['status' => $item->item_status])
                                        </td>
                                        <td>
                                             @if($item->condition_photo)
                                                <a href="{{ Storage::url($item->condition_photo) }}" target="_blank">
                                                    <img src="{{ Storage::url($item->condition_photo) }}"
                                                         class="img-thumbnail" alt="Foto {{ $item->rawMaterial->name }}"
                                                         style="max-height: 80px;">
                                                </a>
                                              @else
                                                  <span class="text-muted">-</span>
                                              @endif
                                         </td>
                                         <td>
                                             @if($item->notes)
                                                <div class="small text-muted text-wrap text-break" style="white-space: normal;">
                                                    {{ $item->notes }}
                                                </div>
                                              @else
                                                  <span class="text-muted">-</span>
                                              @endif
                                         </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @php($hasCosts = $purchaseReceipt->additionalCosts && $purchaseReceipt->additionalCosts->count() > 0)
    <div class="row g-2 mt-3">
        @if($hasCosts)
            <div class="col-12 col-md-7">
                @include('purchase-receipts.partials.additional-costs-display', [
                    'costs' => $purchaseReceipt->additionalCosts,
                    'wrap' => false
                ])
            </div>
            <div class="col-12 col-md-5">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Ringkasan Pembayaran</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-borderless table-sm mb-0">
                                <tbody>
                                    <tr>
                                        <td class="fw-semibold">Subtotal Barang</td>
                                        <td class="text-end">Rp {{ number_format($purchaseReceipt->subtotal_items ?? $purchaseReceipt->computeItemsTotal(), 0, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold">Total Biaya Tambahan</td>
                                        <td class="text-end">Rp {{ number_format($purchaseReceipt->additional_cost_total ?? $purchaseReceipt->computeAdditionalCostsTotal(), 0, ',', '.') }}</td>
                                    </tr>
                                    @if( (float)($purchaseReceipt->discount_amount ?? 0) > 0 )
                                    <tr>
                                        <td class="fw-semibold">Diskon</td>
                                        <td class="text-end text-success">- Rp {{ number_format($purchaseReceipt->discount_amount, 0, ',', '.') }}</td>
                                    </tr>
                                    @endif
                                    @if( (float)($purchaseReceipt->tax_amount ?? 0) > 0 )
                                    <tr>
                                        <td class="fw-semibold">Pajak</td>
                                        <td class="text-end">Rp {{ number_format($purchaseReceipt->tax_amount, 0, ',', '.') }}</td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <td colspan="2"><hr class="my-2"></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Total Bayar</td>
                                        <td class="text-end"><span class="h5 text-primary fw-bold">Rp {{ number_format($purchaseReceipt->computeTotalPayment(), 0, ',', '.') }}</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Ringkasan Pembayaran</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-borderless table-sm mb-0">
                                <tbody>
                                    <tr>
                                        <td class="fw-semibold">Subtotal Barang</td>
                                        <td class="text-end">Rp {{ number_format($purchaseReceipt->subtotal_items ?? $purchaseReceipt->computeItemsTotal(), 0, ',', '.') }}</td>
                                    </tr>
                                    @if( (float)($purchaseReceipt->discount_amount ?? 0) > 0 )
                                    <tr>
                                        <td class="fw-semibold">Diskon</td>
                                        <td class="text-end text-success">- Rp {{ number_format($purchaseReceipt->discount_amount, 0, ',', '.') }}</td>
                                    </tr>
                                    @endif
                                    @if( (float)($purchaseReceipt->tax_amount ?? 0) > 0 )
                                    <tr>
                                        <td class="fw-semibold">Pajak</td>
                                        <td class="text-end">Rp {{ number_format($purchaseReceipt->tax_amount, 0, ',', '.') }}</td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <td colspan="2"><hr class="my-2"></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Total Bayar</td>
                                        <td class="text-end"><span class="h5 text-primary fw-bold">Rp {{ number_format($purchaseReceipt->computeTotalPayment(), 0, ',', '.') }}</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Delete Confirmation Form -->
<form id="delete-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script src="{{ asset('js/purchase-receipts.js') }}"></script>
@endpush
