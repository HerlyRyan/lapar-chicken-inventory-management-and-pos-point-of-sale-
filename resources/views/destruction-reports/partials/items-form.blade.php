@php($__oldItems = $oldItems ?? [])

<div class="d-flex justify-content-between align-items-center mb-2">
    <h5 class="mb-0">Item Dimusnahkan</h5>
    <button type="button" class="btn btn-sm btn-outline-primary" onclick="addItemRow()">Tambah Item</button>
</div>

<div id="items-container" class="table-responsive">
    <table class="table align-middle">
        <thead>
            <tr>
                <th style="width:15%">Tipe</th>
                <th style="width:40%">Produk</th>
                <th style="width:15%">Qty</th>
                <th style="width:20%">Kondisi</th>
                <th style="width:10%"></th>
            </tr>
        </thead>
        <tbody id="items-body">
            @forelse($__oldItems as $idx => $it)
                <tr>
                    <td>
                        @php($isSemi = array_key_exists('semi_finished_product_id', $it))
                        <select class="form-select item-type" name="items[{{ $idx }}][item_type]">
                            <option value="finished" {{ $isSemi ? '' : 'selected' }}>Produk Jadi</option>
                            <option value="semi_finished" {{ $isSemi ? 'selected' : '' }}>Setengah Jadi</option>
                        </select>
                    </td>
                    <td>
                        <select name="items[{{ $idx }}][finished_product_id]" class="form-select product-select product-finished" {{ $isSemi ? 'style=display:none' : '' }}>
                            <option value="">-- Pilih Produk --</option>
                            @foreach($finishedProducts as $p)
                                @php($stock = optional($p->finishedBranchStocks->first())->quantity ?? 0)
                                <option value="{{ $p->id }}" data-type="finished" data-stock="{{ $stock }}" data-unit="{{ $p->unit->abbreviation ?? '' }}" data-unit-cost="{{ $p->production_cost }}" {{ ($it['finished_product_id'] ?? null) == $p->id ? 'selected' : '' }}>
                                    {{ $p->name }} (Stok: {{ $stock }} {{ $p->unit->abbreviation ?? '' }})
                                </option>
                            @endforeach
                        </select>
                        <select name="items[{{ $idx }}][semi_finished_product_id]" class="form-select product-select product-semi" {{ $isSemi ? '' : 'style=display:none' }}>
                            <option value="">-- Pilih Produk --</option>
                            @foreach(($semiFinishedProducts ?? collect()) as $p)
                                @php($stock = optional($p->semiFinishedBranchStocks->first())->quantity ?? 0)
                                <option value="{{ $p->id }}" data-type="semi" data-stock="{{ $stock }}" data-unit="{{ $p->unit->abbreviation ?? '' }}" data-unit-cost="{{ $p->production_cost }}" {{ ($it['semi_finished_product_id'] ?? null) == $p->id ? 'selected' : '' }}>
                                    {{ $p->name }} (Setengah Jadi) (Stok: {{ $stock }} {{ $p->unit->abbreviation ?? '' }})
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input type="number" step="0.01" min="0.01" class="form-control" name="items[{{ $idx }}][quantity]" value="{{ $it['quantity'] ?? '' }}">
                    </td>
                    <td>
                        <input type="text" class="form-control" name="items[{{ $idx }}][condition_description]" value="{{ $it['condition_description'] ?? '' }}" placeholder="Opsional">
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeRow(this)">Hapus</button>
                    </td>
                </tr>
            @empty
                <!-- no rows -->
            @endforelse
        </tbody>
    </table>
</div>

<div class="d-flex justify-content-end mt-2">
    <div id="total-loss-display" class="fs-5 fw-bold text-danger">Total Kerugian: Rp 0</div>
</div>

@if(($selectedBranch ?? $currentBranch ?? null) && ($finishedProducts->count() === 0) && (($semiFinishedProducts ?? collect())->count() === 0))
    <div class="alert alert-warning mt-2">Tidak ada produk (jadi atau setengah jadi) dengan stok tersedia di cabang ini.</div>
@endif
@if(!($selectedBranch ?? $currentBranch ?? null))
    <div class="alert alert-info mt-2">Pilih cabang terlebih dahulu untuk memuat daftar produk.</div>
@endif

<script>
function onBranchChange(sel){
    const id = sel.value;
    const url = new URL(window.location.href);
    if(id){
        url.searchParams.set('branch_id', id);
    } else {
        url.searchParams.delete('branch_id');
    }
    window.location.href = url.toString();
}

let rowIndex = {{ count($__oldItems) }};
function addItemRow(){
    const tbody = document.getElementById('items-body');
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td>
            <select class="form-select item-type" name="items[${rowIndex}][item_type]">
                <option value="finished" selected>Produk Jadi</option>
                <option value="semi_finished">Setengah Jadi</option>
            </select>
        </td>
        <td>
            <select name="items[${rowIndex}][finished_product_id]" class="form-select product-select product-finished">
                <option value="">-- Pilih Produk --</option>
                @foreach($finishedProducts as $p)
                    @php($stock = optional($p->finishedBranchStocks->first())->quantity ?? 0)
                    <option value="{{ $p->id }}" data-type="finished" data-stock="{{ $stock }}" data-unit="{{ $p->unit->abbreviation ?? '' }}" data-unit-cost="{{ $p->production_cost }}">
                        {{ $p->name }} (Stok: {{ $stock }} {{ $p->unit->abbreviation ?? '' }})
                    </option>
                @endforeach
            </select>
            <select name="items[${rowIndex}][semi_finished_product_id]" class="form-select product-select product-semi" style="display:none">
                <option value="">-- Pilih Produk --</option>
                @foreach(($semiFinishedProducts ?? collect()) as $p)
                    @php($stock = optional($p->semiFinishedBranchStocks->first())->quantity ?? 0)
                    <option value="{{ $p->id }}" data-type="semi" data-stock="{{ $stock }}" data-unit="{{ $p->unit->abbreviation ?? '' }}" data-unit-cost="{{ $p->production_cost }}">
                        {{ $p->name }} (Setengah Jadi) (Stok: {{ $stock }} {{ $p->unit->abbreviation ?? '' }})
                    </option>
                @endforeach
            </select>
        </td>
        <td>
            <input type="number" step="0.01" min="0.01" class="form-control" name="items[${rowIndex}][quantity]" />
        </td>
        <td>
            <input type="text" class="form-control" name="items[${rowIndex}][condition_description]" placeholder="Opsional" />
        </td>
        <td>
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeRow(this)">Hapus</button>
        </td>
    `;
    tbody.appendChild(tr);
    rowIndex++;
    bindRowEvents(tr);
    recalcTotal();
}

function removeRow(btn){
    const tr = btn.closest('tr');
    tr.remove();
    recalcTotal();
}

function formatCurrencyIDR(num){
    const n = Number(num || 0);
    const fixed = n.toFixed(2);
    const parts = fixed.split('.');
    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    return 'Rp ' + parts.join('.');
}

function recalcTotal(){
    let total = 0;
    const rows = document.querySelectorAll('#items-body tr');
    rows.forEach(row => {
        // prefer selected product among visible selects
        const finishedSel = row.querySelector('select.product-finished');
        const semiSel = row.querySelector('select.product-semi');
        let sel = finishedSel;
        if (semiSel && semiSel.style.display !== 'none') sel = semiSel;
        if (finishedSel && finishedSel.value) sel = finishedSel; // if chosen
        if (semiSel && semiSel.value) sel = semiSel; // override if semi chosen
        const qtyInput = row.querySelector('input[name$="[quantity]"]');
        const qty = parseFloat(qtyInput && qtyInput.value ? qtyInput.value : '');
        const unitCost = sel && sel.selectedOptions[0] ? parseFloat(sel.selectedOptions[0].dataset.unitCost || '0') : 0;
        if (!isNaN(qty) && qty > 0 && !isNaN(unitCost) && unitCost > 0) {
            total += qty * unitCost;
        }
    });
    const el = document.getElementById('total-loss-display');
    if (el) el.textContent = `Total Kerugian: ${formatCurrencyIDR(total)}`;
}

function bindRowEvents(tr){
    const typeSel = tr.querySelector('select.item-type');
    const finishedSel = tr.querySelector('select.product-finished');
    const semiSel = tr.querySelector('select.product-semi');
    const qtyInput = tr.querySelector('input[name$="[quantity]"]');

    function toggleProductSelect(){
        if (!typeSel) return;
        const val = typeSel.value;
        if (val === 'semi_finished') {
            if (finishedSel) { finishedSel.style.display = 'none'; finishedSel.value = ''; }
            if (semiSel) { semiSel.style.display = ''; }
        } else {
            if (semiSel) { semiSel.style.display = 'none'; semiSel.value = ''; }
            if (finishedSel) { finishedSel.style.display = ''; }
        }
        recalcTotal();
    }

    if (typeSel) typeSel.addEventListener('change', toggleProductSelect);
    if (finishedSel) finishedSel.addEventListener('change', recalcTotal);
    if (semiSel) semiSel.addEventListener('change', recalcTotal);
    if (qtyInput) qtyInput.addEventListener('input', recalcTotal);

    // initialize visibility based on existing values
    toggleProductSelect();
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('#items-body tr').forEach(bindRowEvents);
    // Ensure at least one row is present by default
    if (document.querySelectorAll('#items-body tr').length === 0) {
        addItemRow();
    }
    recalcTotal();
});
</script>
