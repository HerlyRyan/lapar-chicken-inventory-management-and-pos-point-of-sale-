@extends('layouts.app')

@section('title', 'Buat Penerimaan Barang')

@push('styles')
<style>
 /* Compact layout scoped to this page */
 .compact-page .card-body { padding: 0.75rem 1rem; }
 .compact-page .card-header { padding: 0.5rem 1rem; }
 .compact-page .table > :not(caption) > * > * { padding: 0.35rem 0.5rem; }
 .compact-page .form-control, .compact-page .form-select { padding: 0.375rem 0.5rem; min-height: 2.25rem; }
 .compact-page .btn { padding: 0.375rem 0.65rem; }
 .compact-page h4 { margin-bottom: 0.25rem !important; }
 .compact-page .row.g-2 { --bs-gutter-x: 0.5rem; --bs-gutter-y: 0.5rem; }
</style>
@endpush

@section('content')
<div class="container-fluid compact-page">
    <!-- Page Header -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <h4 class="mb-1 fw-bold text-primary">
                                <i class="bi bi-truck me-2"></i>Buat Penerimaan Barang
                            </h4>
                            <p class="text-muted mb-0">Buat penerimaan barang dari supplier</p>
                        </div>
                        <div>
                            <a href="{{ route('purchase-receipts.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Content -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    @include('purchase-receipts.partials.alerts')
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <h6>Terjadi kesalahan:</h6>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('purchase-receipts.store') }}" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Basic Information -->
                        <div class="row g-2 mb-3">
                            <div class="col-12 col-md-5">
                                <label class="form-label">Pesanan Pembelian <span class="text-danger">*</span></label>
                                <select name="purchase_order_id" class="form-select" required>
                                    <option value="">Pilih Pesanan</option>
                                    @foreach($pendingOrders as $order)
                                        <option value="{{ $order->id }}" {{ old('purchase_order_id', request()->query('purchase_order_id')) == $order->id ? 'selected' : '' }}>
                                            {{ $order->order_number }} - {{ $order->supplier->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6 col-md-3">
                                <label class="form-label">Tanggal Penerimaan <span class="text-danger">*</span></label>
                                <input type="date" name="receipt_date" class="form-control" 
                                       value="{{ old('receipt_date', date('Y-m-d')) }}" required>
                            </div>
                            <div class="col-6 col-md-4">
                                <label class="form-label">Status Penerimaan</label>
                                <div id="pr-status-auto" class="form-text">Otomatis: Ditentukan dari status item</div>
                                <input type="hidden" name="status" value="">
                            </div>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label">Foto Bukti Penerimaan <span class="text-danger">*</span></label>
                                <input type="file" name="receipt_photo" class="form-control" 
                                       accept="image/jpeg,image/png,image/jpg" required>
                                <small class="text-muted">Format: JPG, PNG. Maksimal 2MB</small>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label">Catatan</label>
                                <textarea name="notes" class="form-control" rows="2" 
                                          placeholder="Tambahkan catatan penerimaan...">{{ old('notes') }}</textarea>
                            </div>
                        </div>

                        <!-- Items Section -->
                        <div class="card border-0 shadow-sm mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Detail Item Penerimaan</h6>
                            </div>
                            <div class="card-body">
                                <div id="items-container">
                                    <!-- Items will be loaded via JavaScript when purchase order is selected -->
                                </div>
                            </div>
                        </div>

                        <div class="row g-2">
                            <div class="col-12 col-md-7">
                                @include('purchase-receipts.partials.additional-costs', ['prefix' => 'pr'])
                                <div class="mt-2">
                                    @include('purchase-receipts.partials.discount-tax')
                                </div>
                            </div>
                            <div class="col-12 col-md-5">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">Ringkasan Pembayaran</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-sm mb-0">
                                                <tbody>
                                                    <tr>
                                                        <td class="text-muted small">Subtotal</td>
                                                        <td class="text-end" id="pr-subtotal-amount">Rp 0</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-muted small">Biaya Tambahan</td>
                                                        <td class="text-end" id="pr-additional-amount">Rp 0</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-muted small">Diskon</td>
                                                        <td class="text-end" id="pr-discount-amount">Rp 0</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-muted small">Pajak</td>
                                                        <td class="text-end" id="pr-tax-amount">Rp 0</td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Grand Total</strong></td>
                                                        <td class="text-end"><strong id="pr-grand-total-amount">Rp 0</strong></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('purchase-receipts.index') }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-x-lg me-2"></i>Batal
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-save me-2"></i>Simpan Penerimaan
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/purchase-receipts.js') }}"></script>
@endpush
