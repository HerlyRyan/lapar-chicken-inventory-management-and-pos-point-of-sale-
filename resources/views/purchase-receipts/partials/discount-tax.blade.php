<div class="card border mb-4">
    <div class="card-header bg-light">
        <h6 class="mb-0">Diskon & Pajak (Opsional)</h6>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6 col-lg-4">
                <label class="form-label">Diskon (Rp) <small class="text-muted">opsional</small></label>
                <input type="number" name="discount_amount" class="form-control" step="0.01" min="0"
                       value="{{ old('discount_amount', isset($model) ? $model->discount_amount : null) }}"
                       placeholder="0" />
                <small class="text-muted">Masukkan nilai diskon dalam rupiah. Biarkan kosong jika tidak ada diskon.</small>
            </div>
            <div class="col-md-6 col-lg-4">
                <label class="form-label">Pajak (Rp) <small class="text-muted">opsional</small></label>
                <input type="number" name="tax_amount" class="form-control" step="0.01" min="0"
                       value="{{ old('tax_amount', isset($model) ? $model->tax_amount : null) }}"
                       placeholder="0" />
                <small class="text-muted">Masukkan nilai pajak dalam rupiah. Biarkan kosong jika tidak ada pajak.</small>
            </div>
        </div>
    </div>
</div>
