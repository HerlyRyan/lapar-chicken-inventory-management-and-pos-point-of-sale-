@extends('layouts.app')

@section('title', 'Edit Purchase Order')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1" style="color: var(--primary-red);">
                <i class="bi bi-pencil-square me-2"></i>Edit Purchase Order
            </h1>
            <p class="text-muted mb-0">{{ $purchaseOrder->order_number }} - Status: {{ ucfirst($purchaseOrder->status) }}</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('purchase-orders.index') }}" class="btn btn-outline-secondary shadow-sm">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
            </a>
            <a href="{{ route('purchase-orders.show', $purchaseOrder) }}" class="btn btn-outline-primary shadow-sm">
                <i class="bi bi-eye me-1"></i> Lihat Detail
            </a>
        </div>
    </div>

    <!-- Current Order Info -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-warning border-0 shadow-sm">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h6 class="alert-heading mb-1">
                            <i class="bi bi-info-circle me-2"></i>
                            Mengedit Purchase Order
                        </h6>
                        <p class="mb-0">
                            <strong>{{ $purchaseOrder->order_number }}</strong> 
                            - {{ $purchaseOrder->supplier->name }} 
                            - Total: Rp {{ number_format($purchaseOrder->total_amount, 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <small class="text-muted">
                            Dibuat: {{ $purchaseOrder->created_at->format('d/m/Y H:i') }} oleh {{ $purchaseOrder->creator->name }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Section -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form id="purchase-order-form" action="{{ route('purchase-orders.update', $purchaseOrder) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <!-- Basic Info -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-start border-3 border-primary ps-2 mb-3">Informasi Dasar</h5>
                            </div>
                            
                            <!-- Supplier Selection -->
                            <div class="col-md-6 mb-3">
                                <label for="supplier_id" class="form-label">Supplier <span class="text-danger">*</span></label>
                                <select name="supplier_id" id="supplier_id" class="form-select @error('supplier_id') is-invalid @enderror" required>
                                    <option value="">-- Pilih Supplier --</option>
                                    @foreach($suppliers as $supplier)
                                    <option value="{{ is_object($supplier) ? $supplier->id : $supplier['id'] }}"
                                            data-phone="{{ is_object($supplier) ? $supplier->phone : ($supplier['phone'] ?? '') }}"
                                            data-code="{{ is_object($supplier) ? ($supplier->code ?? '') : ($supplier['code'] ?? '') }}"
                                            {{ (old('supplier_id') ?? $purchaseOrder->supplier_id) == (is_object($supplier) ? $supplier->id : $supplier['id']) ? 'selected' : '' }}>
                                        {{ is_object($supplier) ? $supplier->name : $supplier['name'] }}
                                        @if(is_object($supplier) ? ($supplier->code ?? false) : ($supplier['code'] ?? false))
                                            ({{ is_object($supplier) ? $supplier->code : $supplier['code'] }})
                                        @endif
                                    </option>
                                    @endforeach
                                </select>
                                @error('supplier_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Requested Delivery Date -->
                            <div class="col-md-6 mb-3">
                                <label for="requested_delivery_date" class="form-label">Tanggal Pengiriman yang Diminta</label>
                                <input type="date" name="requested_delivery_date" id="requested_delivery_date" 
                                       class="form-control @error('requested_delivery_date') is-invalid @enderror"
                                       value="{{ old('requested_delivery_date') ?? ($purchaseOrder->requested_delivery_date ? $purchaseOrder->requested_delivery_date->format('Y-m-d') : '') }}">
                                @error('requested_delivery_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <!-- Preserve original order date for validation/update -->
                            <input type="hidden" name="order_date" id="order_date" value="{{ old('order_date') ?? ($purchaseOrder->order_date ? $purchaseOrder->order_date->format('Y-m-d') : date('Y-m-d')) }}">
                            
                            <!-- Phone Number (from supplier) -->
                            <div class="col-md-6 mb-3">
                                <label for="supplier_phone" class="form-label">Nomor WhatsApp Supplier</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-success text-white"><i class="bi bi-whatsapp"></i></span>
                                    <input type="text" id="supplier_phone" class="form-control" readonly>
                                </div>
                                <small class="text-muted">Terisi otomatis dari data supplier</small>
                            </div>
                            
                            <!-- Notes -->
                            <div class="col-12">
                                <label for="notes" class="form-label">Catatan</label>
                                <textarea name="notes" id="notes" rows="3" 
                                          class="form-control @error('notes') is-invalid @enderror">{{ old('notes') ?? $purchaseOrder->notes }}</textarea>
                                @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Items Section -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <h5 class="border-start border-3 border-primary ps-2 mb-3">Item Pesanan</h5>
                                <div class="alert alert-info" id="supplier-info">
                                    <i class="bi bi-info-circle me-1"></i> Pilih supplier terlebih dahulu untuk melihat bahan mentah yang tersedia.
                                    <br><small><strong>Catatan:</strong> Jika supplier diganti, semua item akan direset.</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="items-table">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 5%">#</th>
                                        <th style="width: 35%">Bahan Mentah</th>
                                        <th style="width: 15%">Kuantitas</th>
                                        <th style="width: 15%">Satuan</th>
                                        <th style="width: 15%">Harga Satuan</th>
                                        <th style="width: 10%">Total</th>
                                        <th style="width: 5%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="item-rows">
                                    @if($purchaseOrder->items->count() > 0)
                                        @foreach($purchaseOrder->items as $index => $item)
                                        <tr class="item-row" data-material-id="{{ $item->raw_material_id }}">
                                            <td class="row-number">{{ $index + 1 }}</td>
                                            <td>
                                                <select name="items[{{ $index }}][raw_material_id]" class="form-select raw-material-select" required>
                                                    <option value="">-- Pilih Bahan Mentah --</option>
                                                    <!-- Options will be loaded dynamically -->
                                                    <option value="{{ $item->raw_material_id }}" selected>{{ $item->rawMaterial->name }}</option>
                                                </select>
                                                <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">
                                                <input type="text" name="items[{{ $index }}][notes]" value="{{ $item->notes }}" 
                                                       class="form-control form-control-sm mt-2 item-notes" placeholder="Catatan item (opsional)" maxlength="500">
                                            </td>
                                            <td>
                                                <input type="number" name="items[{{ $index }}][quantity]" value="{{ $item->quantity }}" 
                                                       class="form-control item-quantity" min="1" step="1" required>
                                            </td>
                                            <td class="unit-name">{{ $item->unit_name ?? '-' }}</td>
                                            <td>
                                                <input type="number" name="items[{{ $index }}][unit_price]" value="{{ $item->unit_price }}" 
                                                       class="form-control item-price" min="0" required>
                                            </td>
                                            <td class="item-total">Rp {{ number_format($item->total_price, 0, ',', '.') }}</td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-danger remove-item">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    @else
                                    <tr class="empty-row">
                                        <td colspan="7" class="text-center py-3">Belum ada item. Klik tombol Tambah Item di bawah.</td>
                                    </tr>
                                    @endif
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="5" class="text-end fw-bold">Total:</td>
                                        <td colspan="2">
                                            <span id="grand-total">Rp {{ number_format($purchaseOrder->total_amount, 0, ',', '.') }}</span>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-12 d-flex align-items-center gap-2">
                                <button type="button" id="add-item" class="btn btn-sm btn-success">
                                    <i class="bi bi-plus-circle me-1"></i> Tambah Item
                                </button>
                                <button type="button" id="validate-prices" class="btn btn-sm btn-outline-info">
                                    <i class="bi bi-arrow-clockwise me-1"></i> Validasi Harga Terbaru
                                </button>
                            </div>
                        </div>
                        
                        <!-- Template for new items (hidden) -->
                        <template id="item-template">
                            <tr class="item-row">
                                <td class="row-number"></td>
                                <td>
                                    <select name="items[__index__][raw_material_id]" class="form-select raw-material-select" required>
                                        <option value="">-- Pilih Bahan Mentah --</option>
                                    </select>
                                    <input type="text" name="items[__index__][notes]" class="form-control form-control-sm mt-2 item-notes" placeholder="Catatan item (opsional)" maxlength="500">
                                </td>
                                <td>
                                    <input type="number" name="items[__index__][quantity]" class="form-control item-quantity" min="1" step="1" required>
                                </td>
                                <td class="unit-name">-</td>
                                <td>
                                    <input type="number" name="items[__index__][unit_price]" class="form-control item-price" min="0" required>
                                </td>
                                <td class="item-total">Rp 0</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-danger remove-item">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </template>
                        
                        <hr class="my-4">
                        
                        <!-- Form Actions -->
                        <div class="row">
                            <div class="col-12 text-end">
                                <a href="{{ route('purchase-orders.show', $purchaseOrder) }}" class="btn btn-secondary me-2">
                                    <i class="bi bi-x-circle me-1"></i> Batal
                                </a>
                                <button type="submit" class="btn btn-outline-primary me-2" name="submit_action" value="save_draft">
                                    <i class="bi bi-save me-1"></i> Simpan sebagai Draft
                                </button>
                                <button type="submit" class="btn btn-success" id="order-button" name="submit_action" value="order_now">
                                    <i class="bi bi-whatsapp me-1"></i> Kirim Pesanan via WhatsApp
                                </button>
                            </div>
                        </div>
                    </form>
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
<!-- Pass data to JavaScript -->
<script>
    window.rawMaterials = @json($rawMaterials ?? []);
</script>
<script src="{{ asset('js/purchase-orders.js') }}"></script>
@endpush
