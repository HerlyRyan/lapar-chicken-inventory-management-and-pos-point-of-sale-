@extends('layouts.app')

@section('title', 'Penerimaan Barang')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1 fw-bold text-primary">
                                <i class="bi bi-truck me-2"></i>Penerimaan Barang
                            </h4>
                            <p class="text-muted mb-0">Kelola penerimaan barang dari supplier</p>
                        </div>
                        <div>
                            <a href="{{ route('purchase-receipts.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-lg me-2"></i>Buat Penerimaan Baru
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form method="GET" action="{{ route('purchase-receipts.index') }}">
                        <input type="hidden" name="sort" value="{{ request('sort') }}">
                        <input type="hidden" name="direction" value="{{ request('direction') }}">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="">Semua Status</option>
                                    <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>Diterima</option>
                                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                                    <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>Sebagian</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Tanggal Mulai</label>
                                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Tanggal Akhir</label>
                                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Pencarian</label>
                                <input type="text" name="q" class="form-control" placeholder="No. Penerimaan / No. PO / Supplier" value="{{ request('q') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-outline-primary">
                                        <i class="bi bi-search me-1"></i>Filter
                                    </button>
                                    <a href="{{ route('purchase-receipts.index') }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-x-lg me-1"></i>Reset
                                    </a>
                                    <a
                                        href="{{ route('purchase-receipts.export', array_filter([
                                            'status' => request('status'),
                                            'start_date' => request('start_date'),
                                            'end_date' => request('end_date'),
                                            'q' => request('q'),
                                            'sort' => request('sort'),
                                            'direction' => request('direction'),
                                        ])) }}"
                                        class="btn btn-success"
                                        title="Export CSV berdasarkan filter saat ini"
                                    >
                                        <i class="bi bi-download me-1"></i>Export CSV
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Ringkasan Total Belanja -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="d-flex flex-column">
                                <div class="mb-1 text-muted">Periode</div>
                                <div class="fw-semibold">
                                    @php($start = request('start_date'))
                                    @php($end = request('end_date'))
                                    @if($start || $end)
                                        {{ $start ? \Carbon\Carbon::parse($start)->format('d/m/Y') : 'Awal' }}
                                        —
                                        {{ $end ? \Carbon\Carbon::parse($end)->format('d/m/Y') : 'Sekarang' }}
                                    @else
                                        Semua tanggal
                                    @endif
                                    <span class="badge bg-secondary ms-2">{{ $filteredReceiptsCount ?? 0 }} penerimaan</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row text-end">
                                <div class="col-12 col-lg-2 mb-2 mb-lg-0">
                                    <div class="text-muted small">Subtotal Barang</div>
                                    <div class="fw-semibold">Rp {{ number_format($totalItemsAmount ?? 0, 0, ',', '.') }}</div>
                                </div>
                                <div class="col-12 col-lg-2 mb-2 mb-lg-0">
                                    <div class="text-muted small">Biaya Tambahan</div>
                                    <div class="fw-semibold">Rp {{ number_format($totalAdditionalCosts ?? 0, 0, ',', '.') }}</div>
                                </div>
                                <div class="col-12 col-lg-2 mb-2 mb-lg-0">
                                    <div class="text-muted small">Diskon</div>
                                    <div class="fw-semibold">Rp {{ number_format($totalDiscount ?? 0, 0, ',', '.') }}</div>
                                </div>
                                <div class="col-12 col-lg-2 mb-2 mb-lg-0">
                                    <div class="text-muted small">Pajak</div>
                                    <div class="fw-semibold">Rp {{ number_format($totalTax ?? 0, 0, ',', '.') }}</div>
                                </div>
                                <div class="col-12 col-lg-4">
                                    <div class="text-muted small">Total Belanja</div>
                                    <div class="h5 text-primary fw-bold mb-0">Rp {{ number_format($totalBelanja ?? 0, 0, ',', '.') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Purchase Orders (Ordered) -->
    @if(isset($pendingOrders) && $pendingOrders->count() > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <button class="btn btn-link text-decoration-none w-100 d-flex justify-content-between align-items-center p-0"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#pendingOrdersCollapse"
                            aria-expanded="false"
                            aria-controls="pendingOrdersCollapse">
                        <span class="mb-0">
                            <i class="bi bi-clipboard-check me-2"></i>
                            Total menunggu konfirmasi
                            <span class="badge bg-primary ms-2">{{ $pendingOrders->count() }}</span>
                        </span>
                        <i class="bi bi-chevron-down"></i>
                    </button>
                </div>
                <div id="pendingOrdersCollapse" class="collapse">
                  <div class="card-body">
                    <div class="table-responsive pending-orders-scroll">
                        <table class="table table-sm align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>No. PO</th>
                                    <th>Supplier</th>
                                    <th>Tanggal</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingOrders as $po)
                                <tr>
                                    <td class="fw-semibold">{{ $po->order_number }}</td>
                                    <td>{{ $po->supplier->name }}</td>
                                    <td>{{ optional($po->order_date)->format('d/m/Y') }}</td>
                                    <td class="text-end">
                                        <div class="btn-group">
                                            <a href="{{ route('purchase-receipts.create', ['purchase_order_id' => $po->id]) }}" class="btn btn-outline-secondary btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Buat Penerimaan (halaman penuh)" aria-label="Buat Penerimaan (halaman penuh)">
                                                <i class="bi bi-receipt fs-5"></i>
                                            </a>
                                            <button type="button" class="btn btn-success btn-sm"
                                                data-po-id="{{ $po->id }}"
                                                data-po-number="{{ $po->order_number }}"
                                                data-supplier="{{ $po->supplier->name }}"
                                                onclick="openQuickReceive(this)"
                                                data-bs-toggle="tooltip" data-bs-placement="top" title="Terima Cepat di Modal" aria-label="Terima Cepat">
                                                <i class="bi bi-box-seam me-1"></i>Terima
                                            </button>
                                        </div>
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
        </div>
    </div>
    @endif

    <!-- Purchase Receipts Table -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    @include('purchase-receipts.partials.alerts')

                    <div class="table-responsive receipts-scroll">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>{!! sortColumn('receipt_number', 'No. Penerimaan', request('sort'), request('direction')) !!}</th>
                                    <th>{!! sortColumn('receipt_date', 'Tanggal', request('sort'), request('direction')) !!}</th>
                                    <th>Pesanan</th>
                                    <th>Supplier</th>
                                    <th>{!! sortColumn('status', 'Status', request('sort'), request('direction')) !!}</th>
                                    <th>Penerima</th>
                                    <th class="text-end">{!! sortColumn('total_payment', 'Total Bayar', request('sort'), request('direction')) !!}</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($purchaseReceipts as $receipt)
                                    <tr>
                                        <td>
                                            <span class="fw-bold text-primary">{{ $receipt->receipt_number }}</span>
                                        </td>
                                        <td>{{ $receipt->receipt_date->format('d/m/Y') }}</td>
                                        <td>
                                            <a href="{{ route('purchase-orders.show', $receipt->purchaseOrder) }}" 
                                               class="text-decoration-none">
                                                {{ $receipt->purchaseOrder->order_number }}
                                            </a>
                                        </td>
                                        <td>{{ $receipt->purchaseOrder->supplier->name }}</td>
                                        <td>
                                            @include('purchase-receipts.partials.status-badge', ['status' => $receipt->status])
                                        </td>
                                        <td>{{ optional($receipt->receiver)->name ?? '-' }}</td>
                                        <td class="text-end">
                                            @php($rowTotal = $receipt->computeTotalPayment())
                                            <span class="fw-semibold">Rp {{ number_format($rowTotal, 0, ',', '.') }}</span>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('purchase-receipts.show', $receipt) }}" 
                                                   class="btn btn-sm btn-outline-info" data-bs-toggle="tooltip" data-bs-placement="top" title="Lihat Detail" aria-label="Lihat">
                                                    <i class="bi bi-eye fs-5"></i>
                                                </a>
                                                @if(in_array($receipt->status, ['partial', 'accepted', 'rejected']))
                                                    <a href="{{ route('purchase-receipts.edit', $receipt) }}" 
                                                       class="btn btn-sm btn-outline-warning" data-bs-toggle="tooltip" data-bs-placement="top" title="Ubah Penerimaan" aria-label="Ubah">
                                                        <i class="bi bi-pencil-square fs-5"></i>
                                                    </a>
                                                @endif
                                                <button type="button" class="btn btn-sm btn-outline-danger" 
                                                        onclick="confirmDelete({{ $receipt->id }}, '{{ $receipt->receipt_number }}')" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus Penerimaan" aria-label="Hapus">
                                                    <i class="bi bi-trash fs-5"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-5 text-muted">
                                            <i class="bi bi-inbox fs-2 mb-3"></i>
                                            <br>Belum ada data penerimaan barang
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($purchaseReceipts->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $purchaseReceipts->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Receive Modal -->
<div class="modal fade" id="quickReceiveModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
            Terima Pesanan
            <span class="ms-2 fw-semibold" id="qr-po-number"></span>
            <small class="text-muted ms-2" id="qr-supplier-name"></small>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="quickReceiveForm" method="POST" action="{{ route('purchase-receipts.store') }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="purchase_order_id" id="qr-purchase-order-id">
        <div class="modal-body">
          <div class="row g-3 mb-2">
            <div class="col-md-4">
              <label class="form-label">Tanggal Penerimaan <span class="text-danger">*</span></label>
              <input type="date" name="receipt_date" id="qr-receipt-date" class="form-control" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">Status Penerimaan</label>
              <div id="qr-status-auto" class="form-text">Otomatis: Ditentukan dari status item</div>
              <input type="hidden" name="status" id="qr-status" value="">
            </div>
            <div class="col-md-4">
              <label class="form-label">Foto Bukti Penerimaan <span class="text-danger">*</span></label>
              <input type="file" name="receipt_photo" class="form-control" accept="image/jpeg,image/png,image/jpg" required>
              <small class="text-muted">Format: JPG, PNG. Maks 2MB</small>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Catatan</label>
            <textarea name="notes" class="form-control" rows="2" placeholder="Tambahkan catatan penerimaan..."></textarea>
          </div>

          <div class="card border">
            <div class="card-header bg-light">
              <h6 class="mb-0">Detail Item Penerimaan</h6>
            </div>
            <div class="card-body" id="qr-items-container">
              <p class="text-muted mb-0">Pilih PO untuk memuat item...</p>
            </div>
          </div>
          @include('purchase-receipts.partials.additional-costs', ['prefix' => 'qr'])
          @include('purchase-receipts.partials.discount-tax')
          
          <!-- Ringkasan Pembayaran -->
          <div class="card border mt-3">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
              <h6 class="mb-0">Ringkasan Pembayaran</h6>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-6 col-lg-4 ms-auto">
                  <div class="d-flex justify-content-between mb-1">
                    <span>Subtotal</span>
                    <span id="qr-subtotal-amount">Rp 0</span>
                  </div>
                  <div class="d-flex justify-content-between mb-2">
                    <span>Biaya Tambahan</span>
                    <span id="qr-additional-amount">Rp 0</span>
                  </div>
                  <div class="d-flex justify-content-between mb-2">
                    <span>Diskon</span>
                    <span id="qr-discount-amount">Rp 0</span>
                  </div>
                  <div class="d-flex justify-content-between mb-2">
                    <span>Pajak</span>
                    <span id="qr-tax-amount">Rp 0</span>
                  </div>
                  <hr class="my-2">
                  <div class="d-flex justify-content-between">
                    <strong>Grand Total</strong>
                    <strong id="qr-grand-total-amount">Rp 0</strong>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
          <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan Penerimaan</button>
        </div>
      </form>
    </div>
  </div>
  </div>

<!-- Delete Confirmation Form -->
<form id="delete-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('styles')
<style>
  /* Ensure modal body is scrollable with visible scrollbar */
  #quickReceiveModal .modal-body {
    max-height: calc(100vh - 220px);
    overflow-y: auto;
  }
  /* Prevent nested cards from expanding beyond modal body */
  #quickReceiveModal .card-body {
    overflow: visible;
  }
  /* Always show thin scrollbar in WebKit */
  #quickReceiveModal .modal-body::-webkit-scrollbar {
    width: 8px;
  }
  #quickReceiveModal .modal-body::-webkit-scrollbar-thumb {
    background-color: rgba(0,0,0,0.2);
    border-radius: 4px;
  }
  #quickReceiveModal .modal-body::-webkit-scrollbar-track {
    background: rgba(0,0,0,0.05);
  }

  /* Scroll areas for pending orders and receipts list */
  .pending-orders-scroll {
    max-height: 380px;
    overflow: auto;
  }
  .receipts-scroll {
    max-height: 500px;
    overflow: auto;
  }
  .pending-orders-scroll::-webkit-scrollbar,
  .receipts-scroll::-webkit-scrollbar {
    width: 8px;
    height: 8px;
  }
  .pending-orders-scroll::-webkit-scrollbar-thumb,
  .receipts-scroll::-webkit-scrollbar-thumb {
    background-color: rgba(0,0,0,0.2);
    border-radius: 4px;
  }
  .pending-orders-scroll::-webkit-scrollbar-track,
  .receipts-scroll::-webkit-scrollbar-track {
    background: rgba(0,0,0,0.05);
  }
</style>
@endpush

@push('scripts')
<script src="{{ asset('js/purchase-receipts.js') }}"></script>
@endpush
