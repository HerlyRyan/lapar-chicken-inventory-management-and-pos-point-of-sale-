@extends('layouts.app')

@section('title', 'Buat Purchase Order')

@section('styles')
    <link href="{{ asset('css/purchase-order.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Buat Purchase Order</h1>
        <a href="{{ route('purchase-orders.index') }}" class="btn btn-light border shadow-sm">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <!-- Form Section -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header" style="background: linear-gradient(135deg, #dc2626 0%, #ea580c 100%); border-bottom: 3px solid #b91c1c;">
                    <h5 class="text-white m-0 fw-bold"><i class="bi bi-cart3 me-2"></i>Detail Purchase Order</h5>
                </div>
                <div class="card-body" style="background: #fefefe;">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <h6 class="mb-2 fw-bold"><i class="bi bi-exclamation-triangle me-1"></i>Terjadi kesalahan:</h6>
                                <ul class="mb-0 ps-3">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        @if (session('success'))
                            <div class="alert alert-success">
                                <i class="bi bi-check-circle me-1"></i>{{ session('success') }}
                            </div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-triangle me-1"></i>{{ session('error') }}
                            </div>
                        @endif
                    <form id="purchase-order-form" action="{{ route('purchase-orders.store') }}" method="POST">
                        @csrf
                        
                        <!-- Basic Info -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-start border-3 border-primary ps-2 mb-3">Informasi Dasar</h5>
                            </div>
                            
                            <!-- Generated Order Number (Auto) - Readonly -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="po_number">Nomor Purchase Order</label>
                                <div id="po_number" class="form-control bg-light">Auto Generate</div>
                                <small class="text-muted">Nomor akan dibuat otomatis saat PO disimpan</small>
                            </div>
                            
                            <!-- Supplier Selection -->
                            <div class="col-md-6 mb-3">
                                <label for="supplier_id" class="form-label">Supplier <span class="text-danger">*</span></label>
                                <select name="supplier_id" id="supplier_id" class="form-select @error('supplier_id') is-invalid @enderror" required>
                                    <option value="">-- Pilih Supplier --</option>
                                    @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}
                                            data-phone="{{ $supplier->phone }}"
                                            data-code="{{ $supplier->code }}">
                                        {{ $supplier->name }} ({{ $supplier->code }})
                                    </option>
                                    @endforeach
                                </select>
                                @error('supplier_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Hanya supplier aktif yang ditampilkan</small>
                            </div>
                            
                            <!-- Order Date (hidden) -->
                            <input type="hidden" name="order_date" id="order_date" value="{{ date('Y-m-d') }}">
                            
                            <!-- Requested Delivery Date -->
                            <div class="col-md-6 mb-3">
                                <label for="requested_delivery_date" class="form-label">Tanggal Pengiriman yang Diminta</label>
                                <input type="date" name="requested_delivery_date" id="requested_delivery_date" 
                                       class="form-control @error('requested_delivery_date') is-invalid @enderror"
                                       value="{{ old('requested_delivery_date', date('Y-m-d', strtotime('+3 days'))) }}">
                                @error('requested_delivery_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
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
                                          class="form-control @error('notes') is-invalid @enderror">{{ old('notes') }}</textarea>
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
                        
                        <style>
                            #items-table {
                                display: table !important;
                                table-layout: fixed !important;
                                width: 100% !important;
                            }
                            #items-table tbody {
                                display: table-row-group !important;
                            }
                            #items-table .item-row {
                                display: table-row !important;
                            }
                            #items-table .item-row td {
                                display: table-cell !important;
                                vertical-align: middle !important;
                                padding: 8px !important;
                            }
                            #items-table .item-row td:nth-child(1) { width: 5% !important; }
                            #items-table .item-row td:nth-child(2) { width: 28% !important; }
                            #items-table .item-row td:nth-child(3) { width: 12% !important; }
                            #items-table .item-row td:nth-child(4) { width: 10% !important; }
                            #items-table .item-row td:nth-child(5) { width: 15% !important; }
                            #items-table .item-row td:nth-child(6) { width: 15% !important; }
                            #items-table .item-row td:nth-child(7) { width: 15% !important; }
                        </style>
                        
                        <div class="card shadow-sm mb-4" style="border-radius: 12px; overflow: hidden;">
                            <div class="card-header bg-gradient" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                                <h6 class="mb-0 fw-bold"><i class="bi bi-table me-2"></i>Daftar Item Pesanan</h6>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-bordered mb-0" id="items-table" style="table-layout: fixed; width: 100%;">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 5%;" class="text-center py-3 fw-bold">#</th>
                                                <th style="width: 28%;" class="py-3 fw-bold">Bahan Mentah</th>
                                                <th style="width: 12%;" class="py-3 fw-bold text-center">Kuantitas</th>
                                                <th style="width: 10%;" class="py-3 fw-bold text-center">Satuan</th>
                                                <th style="width: 15%;" class="py-3 fw-bold text-center">Harga Satuan</th>
                                                <th style="width: 15%;" class="py-3 fw-bold text-center">Total</th>
                                                <th style="width: 15%;" class="py-3 fw-bold text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="item-rows">
                                            <tr class="empty-row">
                                                <td colspan="7" class="text-center py-5" style="background: #fafbfc;">
                                                    <div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 120px;">
                                                        <i class="bi bi-cart-x text-muted mb-3" style="font-size: 3rem; opacity: 0.5;"></i>
                                                        <h6 class="text-muted mb-1">Belum ada item pesanan</h6>
                                                        <p class="text-muted small mb-0">Klik tombol "Tambah Bahan Mentah" untuk menambah item</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                            <tr class="table-primary" style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);">
                                                <td colspan="5" class="text-end py-3 fw-bold fs-6">TOTAL KESELURUHAN:</td>
                                                <td colspan="2" class="py-3" id="grand-total-cell">
                                                    <span id="grand-total" class="fs-4 fw-bold text-primary">Rp 0</span>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                            <div class="card-footer" style="background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); border-top: 2px solid #e2e8f0;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex gap-2">
                                        <button type="button" id="add-item" class="btn btn-success shadow-sm" style="border-radius: 8px; padding: 8px 16px;">
                                            <i class="bi bi-plus-circle me-2"></i>Tambah Bahan Mentah
                                        </button>
                                        <button type="button" id="validate-prices" class="btn btn-outline-info shadow-sm" style="border-radius: 8px; padding: 8px 16px;">
                                            <i class="bi bi-arrow-clockwise me-2"></i>Validasi Harga Terbaru
                                        </button>
                                    </div>
                                    <small class="text-muted"><i class="bi bi-info-circle me-1"></i>Item akan otomatis dihitung setelah memilih bahan dan mengisi kuantitas</small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Template for new items (hidden) -->
                        <template id="item-template">
                            <tr class="item-row">
                                <td class="row-number align-middle text-center fw-bold py-2" style="background: #f8fafc; color: #667eea;">1</td>
                                <td class="py-2">
                                    <select name="items[__index__][raw_material_id]" class="form-select raw-material-select" required style="font-size: 0.9em;">
                                        <option value="">-- Pilih Bahan Mentah --</option>
                                    </select>
                                    <input type="text" name="items[__index__][notes]" class="form-control form-control-sm mt-2 item-notes" placeholder="Catatan item (opsional)" maxlength="500" style="font-size: 0.85em;">
                                </td>
                                <td class="py-2">
                                    <input type="number" name="items[__index__][quantity]" class="form-control item-quantity text-center" min="1" step="1" required 
                                           style="font-size: 0.9em;" placeholder="1">
                                </td>
                                <td class="unit-name align-middle text-center fw-medium py-2" style="color: #64748b; font-size: 0.9em; background: #f8fafc;">-</td>
                                <td class="py-2">
                                    <div class="input-group">
                                        <span class="input-group-text" style="background: #667eea; color: white; font-size: 0.85em;">Rp</span>
                                        <input type="number" name="items[__index__][unit_price]" class="form-control item-price text-end" min="0" required 
                                               style="font-size: 0.9em;" placeholder="0">
                                    </div>
                                </td>
                                <td class="item-total align-middle text-center fw-bold py-2" style="color: #059669; font-size: 0.95em; background: #f0fdf4;">Rp 0</td>
                                <td class="text-center align-middle py-2">
                                    <button type="button" class="btn btn-sm btn-outline-danger remove-item" 
                                            style="width: 30px; height: 30px;" title="Hapus item ini">
                                        <i class="bi bi-trash" style="font-size: 0.75em;"></i>
                                    </button>
                                </td>
                            </tr>
                        </template>
                        
                        <hr class="my-4">
                        
                        <!-- Form Actions -->
                        <div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);">
                            <div class="card-body">
                                <div class="d-flex justify-content-end gap-2">
                                    <button type="submit" class="btn btn-outline-primary shadow-sm" name="submit_action" value="save_draft">
                                        <i class="bi bi-save me-1"></i> Simpan sebagai Draft
                                    </button>
                                    <button type="submit" class="btn btn-success shadow-sm" id="order-button" name="submit_action" value="order_now">
                                        <i class="bi bi-whatsapp me-1"></i> Kirim Pesanan via WhatsApp
                                    </button>
                                </div>
                                <div class="mt-3 text-center">
                                    <small class="text-muted d-block">
                                        <i class="bi bi-info-circle me-1"></i> <strong>Draft:</strong> Simpan tanpa kirim • <strong>WhatsApp:</strong> Langsung kirim ke supplier
                                    </small>
                                </div>
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

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>
<!-- Pass data to JavaScript -->
<script>
    // Make raw materials data available to the JavaScript class
    window.rawMaterials = @json($rawMaterials ?? []);
</script>
<!-- Include the consolidated JavaScript file -->
<script src="{{ asset('js/purchase-orders.js') }}"></script>
@endpush
