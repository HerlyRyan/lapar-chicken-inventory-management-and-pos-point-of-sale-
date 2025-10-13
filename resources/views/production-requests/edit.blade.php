@extends('layouts.app')

@section('title', 'Edit Pengajuan Produksi')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="bi bi-pencil-square text-warning me-2"></i>
                Edit Pengajuan Produksi
            </h1>
            <p class="text-muted small mb-0">
                Kode: <strong>{{ $productionRequest->request_code }}</strong>
                • Status: <span class="badge bg-{{ $productionRequest->status_color }}">{{ $productionRequest->status_label }}</span>
            </p>
        </div>
        <div>
            <a href="{{ route('production-requests.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i>
                Kembali
            </a>
        </div>
    </div>

    @if(!$productionRequest->isPending())
        <div class="alert alert-warning d-flex align-items-center" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            Pengajuan ini sudah diproses. Perubahan tidak diperkenankan.
        </div>
    @endif

    <form action="{{ route('production-requests.update', $productionRequest) }}" method="POST" id="productionRequestForm">
        @csrf
        @method('PUT')
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
                            <textarea name="purpose" class="form-control" rows="3" placeholder="Contoh: Produksi 200 ayam marinasi untuk cabang X">{{ old('purpose', $productionRequest->purpose) }}</textarea>
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
                            @php $materialRowIndexInit = 0; @endphp
                            @forelse($productionRequest->items as $idx => $item)
                                @php $materialRowIndexInit = $idx; @endphp
                                <div class="row material-row mb-3" data-index="{{ $idx }}">
                                    <div class="col-md-4">
                                        <label class="form-label">Bahan Mentah <span class="text-danger">*</span></label>
                                        <select name="items[{{ $idx }}][raw_material_id]" class="form-select material-select" required onchange="updateMaterialInfo({{ $idx }})">
                                            <option value="">Pilih Bahan Mentah</option>
                                            @foreach($rawMaterials as $material)
                                                <option value="{{ $material->id }}"
                                                    data-stock="{{ $material->current_stock }}"
                                                    data-unit="{{ $material->unit->name ?? '' }}"
                                                    data-cost="{{ $material->unit_price ?? 0 }}"
                                                    {{ $material->id == $item->raw_material_id ? 'selected' : '' }}>
                                                    {{ $material->name }} (Stok: {{ number_format($material->current_stock, 0, ',', '.') }} {{ $material->unit->name ?? '' }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Jumlah <span class="text-danger">*</span></label>
                                        <input type="number" name="items[{{ $idx }}][requested_quantity]" class="form-control quantity-input" step="1" min="1" required value="{{ (int) round($item->requested_quantity) }}" onchange="calculateRowTotal({{ $idx }})">
                                        <small class="form-text text-muted" id="unit-info-{{ $idx }}">{{ $item->rawMaterial->unit->name ?? '' }}</small>
                                        <small class="text-danger d-none" id="stock-warning-{{ $idx }}"></small>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Harga/Unit <span class="text-danger">*</span></label>
                                        <input type="number" name="items[{{ $idx }}][unit_cost]" class="form-control cost-input" step="1" min="0" required value="{{ (int) round($item->unit_cost) }}" onchange="calculateRowTotal({{ $idx }})">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Total</label>
                                        <input type="text" class="form-control total-display" readonly id="total-{{ $idx }}" value="Rp {{ number_format(($item->requested_quantity * $item->unit_cost), 0, ',', '.') }}">
                                    </div>
                                    <div class="col-md-1">
                                        <label class="form-label">Catatan</label>
                                        <input type="text" name="items[{{ $idx }}][notes]" class="form-control" value="{{ $item->notes }}" placeholder="Opsional">
                                    </div>
                                    <div class="col-md-1 d-flex align-items-end">
                                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeMaterialRow({{ $idx }})" {{ $loop->count <= 1 ? 'disabled' : '' }}>
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            @empty
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
                                        <input type="number" name="items[0][requested_quantity]" class="form-control quantity-input" step="1" min="1" required onchange="calculateRowTotal(0)">
                                        <small class="form-text text-muted" id="unit-info-0"></small>
                                        <small class="text-danger d-none" id="stock-warning-0"></small>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Harga/Unit <span class="text-danger">*</span></label>
                                        <input type="number" name="items[0][unit_cost]" class="form-control cost-input" step="1" min="0" required onchange="calculateRowTotal(0)">
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
                                @php $materialRowIndexInit = 0; @endphp
                            @endforelse
                        </div>

                        @error('items')
                            <div class="alert alert-danger mt-2">{{ $message }}</div>
                        @enderror

                        <!-- Total Cost Display -->
                        <div class="row mt-3">
                            <div class="col-md-8"></div>
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body py-2">
                                        <div class="d-flex justify-content-between">
                                            <strong>Total Biaya Bahan:</strong>
                                            <strong id="grand-total">Rp {{ number_format($productionRequest->total_raw_material_cost, 0, ',', '.') }}</strong>
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
                            @php $outputRowIndexInit = 0; @endphp
                            @forelse($productionRequest->outputs as $oIdx => $out)
                                @php $outputRowIndexInit = $oIdx; @endphp
                                <div class="row output-row mb-3" data-index="{{ $oIdx }}">
                                    <div class="col-md-6">
                                        <label class="form-label">Produk Setengah Jadi</label>
                                        <select name="outputs[{{ $oIdx }}][semi_finished_product_id]" class="form-select output-product-select" onchange="updateOutputUnit({{ $oIdx }})">
                                            <option value="">Pilih Produk</option>
                                            @foreach($semiFinishedProducts as $product)
                                            <option value="{{ $product->id }}"
                                                data-unit="{{ optional($product->getRelation('unit'))->name ?? '' }}"
                                                data-unit-abbr="{{ optional($product->getRelation('unit'))->abbreviation ?? '' }}"
                                                {{ $product->id == $out->semi_finished_product_id ? 'selected' : '' }}>
                                                    {{ $product->name }} (Min Stok: {{ number_format($product->minimum_stock ?? 0, 0, ',', '.') }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Jumlah Rencana</label>
                                        <input type="number" name="outputs[{{ $oIdx }}][planned_quantity]" class="form-control" step="1" min="1" value="{{ (int) round($out->planned_quantity) }}" placeholder="0">
                                        <small class="form-text text-muted output-unit-info" id="output-unit-{{ $oIdx }}">{{ optional($out->semiFinishedProduct->getRelation('unit'))->name ? 'Satuan: ' . optional($out->semiFinishedProduct->getRelation('unit'))->name . (optional($out->semiFinishedProduct->getRelation('unit'))->abbreviation ? ' (' . optional($out->semiFinishedProduct->getRelation('unit'))->abbreviation . ')' : '') : '' }}</small>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Catatan</label>
                                        <input type="text" name="outputs[{{ $oIdx }}][notes]" class="form-control" value="{{ $out->notes }}" placeholder="Opsional">
                                    </div>
                                    <div class="col-md-1 d-flex align-items-end">
                                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeOutputRow({{ $oIdx }})" {{ $loop->count <= 1 ? 'disabled' : '' }}>
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            @empty
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
                                @php $outputRowIndexInit = 0; @endphp
                            @endforelse
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

                <!-- Actions -->
                <div class="card shadow">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-save me-1"></i>
                                Simpan Perubahan
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
<script>
  // Initialize starting indices for the shared script to read from DOM
  window.__PR_MATERIAL_IDX_INIT__ = {{ max($materialRowIndexInit, 0) }};
  window.__PR_OUTPUT_IDX_INIT__ = {{ max($outputRowIndexInit, 0) }};
</script>
<script src="{{ asset('js/production-requests-form.js') }}"></script>
@endpush
@endsection
