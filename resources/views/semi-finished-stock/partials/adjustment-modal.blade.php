<!-- Stock Adjustment Modal (Reusable Partial) -->
<div class="modal fade" id="adjustmentModal" tabindex="-1" aria-labelledby="adjustmentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="adjustmentModalLabel">Sesuaikan Stok</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="adjustmentForm" method="POST">
                @csrf
                <input type="hidden" name="branch_id" id="adjust_branch_id" value="{{ optional($selectedBranch)->id ?? session('selected_dashboard_branch') }}">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Produk:</label>
                        <div id="productName" class="text-muted"></div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Stok Saat Ini:</label>
                        <div id="currentStock" class="fw-bold text-primary"></div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="adjustment_type" class="form-label">Jenis Penyesuaian <span class="text-danger">*</span></label>
                        <select name="adjustment_type" id="adjustment_type" class="form-select" required onchange="updateAdjustmentUI()">
                            <option value="">Pilih Jenis</option>
                            <option value="add">Tambah Stok</option>
                            <option value="reduce">Kurangi Stok</option>
                            <option value="set">Atur Ulang Stok</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="quantity" class="form-label">Jumlah <span class="text-danger">*</span></label>
                        <input type="number" name="quantity" id="quantity" class="form-control" 
                               step="0.001" min="0" required onchange="previewNewStock()">
                        <div class="form-text" id="adjustmentHelp"></div>
                    </div>
                    
                    <div class="mb-3" id="previewSection" style="display: none;">
                        <div class="alert alert-info">
                            <strong>Stok Baru:</strong> <span id="newStockPreview"></span>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="reason" class="form-label">Alasan <span class="text-danger">*</span></label>
                        <select name="reason" id="reason" class="form-select" required>
                            <option value="">Pilih Alasan</option>
                            <option value="production_output">Hasil Produksi</option>
                            <option value="quality_control">Kontrol Kualitas</option>
                            <option value="damaged_goods">Barang Rusak</option>
                            <option value="expired_goods">Barang Kadaluarsa</option>
                            <option value="inventory_correction">Koreksi Inventori</option>
                            <option value="other">Lainnya</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">Catatan</label>
                        <textarea name="notes" id="notes" class="form-control" rows="2" 
                                  placeholder="Catatan tambahan (opsional)"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-pencil me-1"></i>
                        Sesuaikan Stok
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentProductStock = 0;

function showAdjustmentModal(productId, productName, currentStock) {
    const modal = new bootstrap.Modal(document.getElementById('adjustmentModal'));
    const form = document.getElementById('adjustmentForm');
    
    form.action = `/semi-finished-stock/${productId}/adjust`;
    document.getElementById('productName').textContent = productName;
    document.getElementById('currentStock').textContent = `${Math.round(parseFloat(currentStock)).toLocaleString('id-ID')} unit`;
    
    currentProductStock = parseFloat(currentStock);
    
    // Reset form
    form.reset();
    document.getElementById('previewSection').style.display = 'none';
    
    modal.show();
}

function updateAdjustmentUI() {
    const type = document.getElementById('adjustment_type').value;
    const helpText = document.getElementById('adjustmentHelp');
    const quantityInput = document.getElementById('quantity');
    
    switch(type) {
        case 'add':
            helpText.textContent = 'Jumlah yang akan ditambahkan ke stok saat ini';
            quantityInput.min = '0.001';
            break;
        case 'reduce':
            helpText.textContent = `Jumlah yang akan dikurangi (maksimal: ${currentProductStock.toLocaleString('id-ID')})`;
            quantityInput.min = '0.001';
            quantityInput.max = currentProductStock;
            break;
        case 'set':
            helpText.textContent = 'Stok baru yang akan ditetapkan';
            quantityInput.min = '0';
            quantityInput.removeAttribute('max');
            break;
        default:
            helpText.textContent = '';
            break;
    }
    
    previewNewStock();
}

function previewNewStock() {
    const type = document.getElementById('adjustment_type').value;
    const quantity = parseFloat(document.getElementById('quantity').value) || 0;
    const previewSection = document.getElementById('previewSection');
    const previewText = document.getElementById('newStockPreview');
    
    if (!type || quantity === 0) {
        previewSection.style.display = 'none';
        return;
    }
    
    let newStock = 0;
    
    switch(type) {
        case 'add':
            newStock = currentProductStock + quantity;
            break;
        case 'reduce':
            newStock = Math.max(0, currentProductStock - quantity);
            break;
        case 'set':
            newStock = quantity;
            break;
    }
    
    previewText.textContent = `${newStock.toLocaleString('id-ID')} unit`;
    previewSection.style.display = 'block';
}
</script>
@endpush
