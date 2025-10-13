@extends('layouts.app')

@section('title', 'Buat Pengajuan Produksi')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="bi bi-plus-circle text-primary me-2"></i>
                Buat Pengajuan Produksi
            </h1>
            <p class="text-muted small mb-0">Ajukan penggunaan bahan mentah untuk produksi</p>
        </div>
        <div>
            <a href="{{ route('production-requests.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i>
                Kembali
            </a>
        </div>
    </div>

    <form action="{{ route('production-requests.store') }}" method="POST" id="productionRequestForm">
        @csrf
        
        <div class="row">
            <!-- Main Form -->
            <div class="col-lg-8">
                
                <!-- Informasi Pengajuan -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="bi bi-clipboard-check me-2"></i>
                            Informasi Pengajuan
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Peruntukan</label>
                            <textarea name="purpose" class="form-control" rows="3" placeholder="Contoh: Produksi 200 ayam marinasi untuk cabang X">{{ old('purpose') }}</textarea>
                            @error('purpose')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Raw Materials Selection -->
                <div class="card shadow">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="bi bi-box-seam me-2"></i>
                            Bahan Mentah yang Dibutuhkan
                        </h6>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addMaterialRow()">
                            <i class="bi bi-plus me-1"></i>
                            Tambah Bahan
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="materials-container">
                            <!-- Material rows will be added here -->
                            <div class="row material-row mb-3" data-index="0">
                                <div class="col-md-4">
                                    <label class="form-label">Bahan Mentah <span class="text-danger">*</span></label>
                                    <select name="items[0][raw_material_id]" class="form-select material-select" required onchange="updateMaterialInfo(0)">
                                        <option value="">Pilih Bahan Mentah</option>
                                        @foreach($rawMaterials as $material)
                                            <option value="{{ $material->id }}" 
                                                    data-stock="{{ $material->current_stock }}"
                                                    data-unit="{{ $material->unit->name ?? '' }}"
                                                    data-cost="{{ $material->unit_price ?? 0 }}">
                                                {{ $material->name }} (Stok: {{ number_format($material->current_stock, 0, ',', '.') }} {{ $material->unit->name ?? '' }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Jumlah <span class="text-danger">*</span></label>
                                    <input type="number" name="items[0][requested_quantity]" class="form-control quantity-input" 
                                           step="1" min="1" required onchange="calculateRowTotal(0)">
                                    <small class="form-text text-muted" id="unit-info-0"></small>
                                    <small class="text-danger d-none" id="stock-warning-0"></small>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Harga/Unit <span class="text-danger">*</span></label>
                                    <input type="number" name="items[0][unit_cost]" class="form-control cost-input" 
                                           step="1" min="0" required onchange="calculateRowTotal(0)">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Total</label>
                                    <input type="text" class="form-control total-display" readonly id="total-0">
                                </div>
                                <div class="col-md-1">
                                    <label class="form-label">Catatan</label>
                                    <input type="text" name="items[0][notes]" class="form-control" placeholder="Opsional">
                                </div>
                                <div class="col-md-1 d-flex align-items-end">
                                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeMaterialRow(0)" disabled>
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        @error('items')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                        
                        <!-- Total Cost Display -->
                        <div class="row mt-3">
                            <div class="col-md-8"></div>
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body py-2">
                                        <div class="d-flex justify-content-between">
                                            <strong>Total Biaya Bahan:</strong>
                                            <strong id="grand-total">Rp 0</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Planned Semi-Finished Outputs -->
                <div class="card shadow mt-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-success">
                            <i class="bi bi-diagram-3 me-2"></i>
                            Target Output Bahan Setengah Jadi
                        </h6>
                        <button type="button" class="btn btn-sm btn-outline-success" onclick="addOutputRow()">
                            <i class="bi bi-plus me-1"></i>
                            Tambah Target Output
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="outputs-container">
                            <div class="row output-row mb-3" data-index="0">
                                <div class="col-md-6">
                                    <label class="form-label">Produk Setengah Jadi</label>
                                    <select name="outputs[0][semi_finished_product_id]" class="form-select output-product-select" onchange="updateOutputUnit(0)">
                                        <option value="">Pilih Produk</option>
                                        @foreach($semiFinishedProducts as $product)
                                            <option value="{{ $product->id }}"
                                                data-unit="{{ optional($product->getRelation('unit'))->name ?? '' }}"
                                                data-unit-abbr="{{ optional($product->getRelation('unit'))->abbreviation ?? '' }}">
                                                {{ $product->name }} (Min Stok: {{ number_format($product->minimum_stock ?? 0, 0, ',', '.') }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Jumlah Rencana</label>
                                    <input type="number" name="outputs[0][planned_quantity]" class="form-control" step="1" min="1" placeholder="0">
                                    <small class="form-text text-muted output-unit-info" id="output-unit-0"></small>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Catatan</label>
                                    <input type="text" name="outputs[0][notes]" class="form-control" placeholder="Opsional">
                                </div>
                                <div class="col-md-1 d-flex align-items-end">
                                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeOutputRow(0)" disabled>
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        @error('outputs')
                            <div class="alert alert-danger mt-2">{{ $message }}</div>
                        @enderror
                        @error('outputs.*.semi_finished_product_id')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                        @error('outputs.*.planned_quantity')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Sidebar Info -->
            <div class="col-lg-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-info">
                            <i class="bi bi-info-circle me-2"></i>
                            Panduan Pengajuan
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="small">
                            <h6 class="text-primary">Langkah Pengajuan:</h6>
                            <ol class="ps-3 mb-3">
                                <li>Pilih bahan mentah yang dibutuhkan</li>
                                <li>Masukkan jumlah dan harga per unit</li>
                                <li>(Opsional) Tambahkan target output bahan setengah jadi</li>
                                <li>Submit untuk persetujuan manajer</li>
                            </ol>
                            
                            <h6 class="text-warning">Catatan Penting:</h6>
                            <ul class="ps-3 mb-3">
                                <li>Pastikan stok bahan mentah mencukupi</li>
                                <li>Harga akan dikunci saat pengajuan</li>
                                <li>Pengajuan hanya bisa diedit sebelum disetujui</li>
                                <li>Bahan mentah akan dikurangi otomatis saat disetujui</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="card shadow">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-send me-1"></i>
                                Kirim Pengajuan
                            </button>
                            <a href="{{ route('production-requests.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-1"></i>
                                Batal
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@push('scripts')
<script src="{{ asset('js/production-requests-form.js') }}"></script>
@endpush
@endsection
