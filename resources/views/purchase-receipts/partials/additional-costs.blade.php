@php(
    $prefix = $prefix ?? 'pr'
)
<div class="card border mt-3">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <h6 class="mb-0">Biaya Tambahan</h6>
        <button type="button" class="btn btn-sm btn-outline-primary js-add-cost-row" data-prefix="{{ $prefix }}">
            <i class="bi bi-plus-lg me-1"></i>Tambah Biaya
        </button>
    </div>
    <div class="card-body" id="{{ $prefix }}-additional-costs-container">
        @php($costs = isset($existingCosts) ? $existingCosts : [])
        @if(!empty($costs) && count($costs))
            @foreach($costs as $i => $cost)
                @php(
                    $cn = data_get($cost, 'cost_name')
                )
                @php(
                    $am = data_get($cost, 'amount')
                )
                @php(
                    $nt = data_get($cost, 'notes')
                )
                <div class="row g-2 align-items-end mb-2 {{ $prefix }}-cost-row">
                    <div class="col-md-4">
                        <label class="form-label">Nama Biaya <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="additional_costs[{{ $i }}][cost_name]" value="{{ old("additional_costs.$i.cost_name", $cn) }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Jumlah <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0" class="form-control" name="additional_costs[{{ $i }}][amount]" value="{{ old("additional_costs.$i.amount", $am) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Catatan</label>
                        <input type="text" class="form-control" name="additional_costs[{{ $i }}][notes]" value="{{ old("additional_costs.$i.notes", $nt) }}">
                    </div>
                    <div class="col-md-1 d-grid">
                        <button type="button" class="btn btn-outline-danger js-remove-cost-row"><i class="bi bi-trash"></i></button>
                    </div>
                </div>
            @endforeach
        @else
            <p class="text-muted mb-0">Belum ada biaya tambahan. Klik "Tambah Biaya".</p>
        @endif
    </div>

    <!-- Template for Additional Cost Row -->
    <template id="{{ $prefix }}-additional-cost-row-template">
        <div class="row g-2 align-items-end mb-2 {{ $prefix }}-cost-row">
            <div class="col-md-4">
                <label class="form-label">Nama Biaya <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="additional_costs[__INDEX__][cost_name]" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Jumlah <span class="text-danger">*</span></label>
                <input type="number" step="0.01" min="0" class="form-control" name="additional_costs[__INDEX__][amount]" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Catatan</label>
                <input type="text" class="form-control" name="additional_costs[__INDEX__][notes]">
            </div>
            <div class="col-md-1 d-grid">
                <button type="button" class="btn btn-outline-danger js-remove-cost-row"><i class="bi bi-trash"></i></button>
            </div>
        </div>
    </template>
</div>
