@extends('layouts.app')

@section('title', 'Edit Penerimaan Barang')

@push('styles')
<style>
 .compact-page .card-header { padding: 0.5rem 0.75rem; }
 .compact-page .card-body { padding: 0.75rem; }
 .compact-page .table td, .compact-page .table th { padding: 0.375rem 0.5rem; }
 .compact-page .btn { padding: 0.35rem 0.6rem; }
 .compact-page .btn.btn-sm { padding: 0.25rem 0.5rem; }
 .compact-page .form-label { margin-bottom: 0.25rem; }
 .compact-page .form-control, .compact-page .form-select { padding: 0.375rem 0.5rem; min-height: 2rem; }
 .compact-page .input-group .btn { padding: 0.375rem 0.5rem; }
 .compact-page .pr-row { padding: 0.5rem !important; margin-bottom: 0.5rem !important; }
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
                                <i class="bi bi-pencil-square me-2"></i>Edit Penerimaan Barang
                            </h4>
                            <p class="text-muted mb-0">{{ $purchaseReceipt->receipt_number }}</p>
                        </div>
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="{{ route('purchase-receipts.show', $purchaseReceipt) }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Content -->
    <div class="row g-2">
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

                    <form method="POST" action="{{ route('purchase-receipts.update', $purchaseReceipt) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <!-- Basic Information -->
                        <div class="row g-2 mb-3">
                            <div class="col-12 col-md-5">
                                <label class="form-label">Pesanan Pembelian</label>
                                <input type="text" class="form-control" 
                                       value="{{ $purchaseReceipt->purchaseOrder->order_number }} - {{ $purchaseReceipt->purchaseOrder->supplier->name }}" 
                                       readonly>
                            </div>
                            <div class="col-6 col-md-3">
                                <label class="form-label">Tanggal Penerimaan <span class="text-danger">*</span></label>
                                <input type="date" name="receipt_date" class="form-control" 
                                       value="{{ old('receipt_date', $purchaseReceipt->receipt_date->format('Y-m-d')) }}" required>
                            </div>
                            <div class="col-6 col-md-4">
                                <label class="form-label">Status Penerimaan</label>
                                <div id="pr-status-auto" class="form-text">Otomatis: Ditentukan dari status item</div>
                                <input type="hidden" name="status" value="">
                            </div>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label">Foto Bukti Penerimaan @if(!$purchaseReceipt->receipt_photo)<span class="text-danger">*</span>@endif</label>
                                <input type="file" name="receipt_photo" class="form-control" 
                                       accept="image/jpeg,image/png,image/jpg" @if(!$purchaseReceipt->receipt_photo) required @endif>
                                <small class="text-muted">
                                    @if($purchaseReceipt->receipt_photo)
                                        Kosongkan jika tidak ingin mengubah foto. Format: JPG, PNG. Maksimal 2MB
                                    @else
                                        Wajib diunggah. Format: JPG, PNG. Maksimal 2MB
                                    @endif
                                </small>
                                @if($purchaseReceipt->receipt_photo)
                                    <div class="mt-2">
                                        <small class="text-info">
                                            <i class="bi bi-info-circle me-1"></i>Foto saat ini: 
                                            <a href="{{ Storage::url($purchaseReceipt->receipt_photo) }}" target="_blank">Lihat foto</a>
                                        </small>
                                    </div>
                                @endif
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label">Catatan</label>
                                <textarea name="notes" class="form-control" rows="2" 
                                          placeholder="Tambahkan catatan penerimaan...">{{ old('notes', $purchaseReceipt->notes) }}</textarea>
                            </div>
                        </div>

                        <!-- Items Section -->
                        <div class="card border-0 shadow-sm mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Detail Item Penerimaan</h6>
                            </div>
                            <div class="card-body" id="items-container">
                                @foreach($purchaseReceipt->items as $index => $item)
                                    <div class="row mb-3 p-3 border rounded pr-row" data-price="{{ $item->unit_price }}">
                                        <input type="hidden" name="items[{{ $item->id }}][purchase_order_item_id]" value="{{ $item->purchase_order_item_id }}">
                                        
                                        <div class="col-md-3">
                                            <label class="form-label">Bahan</label>
                                            <input type="text" class="form-control" value="{{ $item->rawMaterial->name }}" readonly>
                                            <small class="text-muted">{{ $item->rawMaterial->code }}</small>
                                        </div>
                                        
                                        <div class="col-md-2">
                                            <label class="form-label">Dipesan</label>
                                            <input type="text" class="form-control" 
                                                   value="{{ number_format($item->ordered_quantity, 0, ',', '.') }} {{ $item->rawMaterial->unit->name }}" readonly>
                                        </div>
                                        
                                        <div class="col-md-3">
                                            <label class="form-label">Diterima <span class="text-danger">*</span></label>
                                            <div class="input-group input-group-sm">
                                                <button class="btn btn-outline-secondary btn-sm btn-decrement" type="button" aria-label="Kurangi">
                                                    <i class="bi bi-dash"></i>
                                                </button>
                                                <input type="number" name="items[{{ $item->id }}][received_quantity]" 
                                                       class="form-control form-control-sm received-input" step="1" min="0" max="{{ $item->ordered_quantity }}" 
                                                       value="{{ old('items.'.$item->id.'.received_quantity', $item->received_quantity) }}" required>
                                                <button class="btn btn-outline-secondary btn-sm btn-increment" type="button" aria-label="Tambah">
                                                    <i class="bi bi-plus"></i>
                                                </button>
                                                <div class="invalid-feedback">Nilai diterima harus 0 - {{ number_format($item->ordered_quantity, 0, ',', '.') }} {{ $item->rawMaterial->unit->name }}</div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-2">
                                            <label class="form-label">Ditolak</label>
                                            <div class="form-text text-muted js-rejected">Otomatis: {{ number_format($item->rejected_quantity, 0, ',', '.') }} {{ $item->rawMaterial->unit->name }}</div>
                                            <input type="hidden" name="items[{{ $item->id }}][rejected_quantity]" value="">
                                        </div>
                                        
                                        <div class="col-md-2">
                                            <label class="form-label">Status Item</label>
                                            <div class="form-text js-item-status">Otomatis: {{ ucfirst(trans('purchase-receipts.item_status.' . $item->item_status)) }}</div>
                                            <input type="hidden" name="items[{{ $item->id }}][item_status]" value="">
                                        </div>
                                        
                                        <div class="col-12 mt-2">
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar js-progress {{ $item->item_status === 'accepted' ? 'bg-success' : ($item->item_status === 'rejected' ? 'bg-danger' : 'bg-warning') }}" role="progressbar" style="width: {{ $item->ordered_quantity > 0 ? min(100, ($item->received_quantity / $item->ordered_quantity) * 100) : 0 }}%"></div>
                                            </div>
                                            <small class="text-muted js-progress-text">{{ number_format($item->received_quantity, 0, ',', '.') }} / {{ number_format($item->ordered_quantity, 0, ',', '.') }} {{ $item->rawMaterial->unit->name }} ({{ number_format($item->ordered_quantity > 0 ? min(100, ($item->received_quantity / $item->ordered_quantity) * 100) : 0, 0, ',', '.') }}%)</small>
                                        </div>
                                        
                                        <div class="col-12 mt-2">
                                            <div class="row g-2">
                                                <div class="col-md-6">
                                                    <label class="form-label">Foto Kondisi Item @if(!$item->condition_photo)<span class="text-danger">*</span>@endif</label>
                                                    <input type="file" name="items[{{ $item->id }}][condition_photo]" 
                                                           class="form-control" accept="image/jpeg,image/png,image/jpg" @if(!$item->condition_photo) required @endif>
                                                    <small class="text-muted">
                                                        @if($item->condition_photo)
                                                            Kosongkan jika tidak ingin mengubah foto. Format: JPG, PNG. Maksimal 2MB
                                                        @else
                                                            Wajib diunggah karena item belum memiliki foto. Format: JPG, PNG. Maksimal 2MB
                                                        @endif
                                                    </small>
                                                    @if($item->condition_photo)
                                                        <div class="mt-2">
                                                            <small class="text-info">
                                                                <i class="bi bi-info-circle me-1"></i>Foto saat ini: 
                                                                <a href="{{ Storage::url($item->condition_photo) }}" target="_blank">Lihat foto</a>
                                                            </small>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Catatan Item</label>
                                                    <textarea name="items[{{ $item->id }}][notes]" class="form-control" rows="2" 
                                                              placeholder="Catatan khusus untuk item ini...">{{ old('items.'.$item->id.'.notes', $item->notes) }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="row g-2">
                            <div class="col-12 col-md-7">
                                @include('purchase-receipts.partials.additional-costs', ['prefix' => 'pr', 'existingCosts' => $purchaseReceipt->additionalCosts])
                                <div class="mt-2">
                                    @include('purchase-receipts.partials.discount-tax', ['model' => $purchaseReceipt])
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
                                                        <td>Biaya Tambahan</td>
                                                        <td class="text-end" id="pr-additional-amount">Rp 0</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Diskon</td>
                                                        <td class="text-end" id="pr-discount-amount">Rp 0</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Pajak</td>
                                                        <td class="text-end" id="pr-tax-amount">Rp 0</td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="2"><hr class="my-2"></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-bold">Grand Total</td>
                                                        <td class="text-end fw-bold" id="pr-grand-total-amount">Rp 0</td>
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
                                    <a href="{{ route('purchase-receipts.show', $purchaseReceipt) }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-x-lg me-2"></i>Batal
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-save me-2"></i>Update Penerimaan
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
