<!-- Stock Transfer Modal -->
<div class="modal fade" id="stockTransferModal" tabindex="-1" aria-labelledby="stockTransferModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="stockTransferModalLabel">
          <i class="bi bi-arrow-left-right me-2"></i> Transfer Stok Antar Cabang
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="stockTransferForm" method="POST" action="#">
        @csrf
        <div class="modal-body">
          <div class="row mb-3">
            <div class="col-md-6">
              <label for="fromBranch" class="form-label">Cabang Asal</label>
              <select class="form-select" id="fromBranch" name="from_branch_id" required>
                <option value="">Pilih Cabang Asal</option>
                <!-- Populate with branches -->
              </select>
            </div>
            <div class="col-md-6">
              <label for="toBranch" class="form-label">Cabang Tujuan</label>
              <select class="form-select" id="toBranch" name="to_branch_id" required>
                <option value="">Pilih Cabang Tujuan</option>
                <!-- Populate with branches -->
              </select>
            </div>
          </div>
          <div class="mb-3">
            <label for="product" class="form-label">Produk / Bahan</label>
            <select class="form-select" id="product" name="product_id" required>
              <option value="">Pilih Produk/Bahan</option>
              <!-- Populate with products/materials -->
            </select>
          </div>
          <div class="mb-3 row">
            <div class="col-md-6">
              <label for="quantity" class="form-label">Jumlah</label>
              <input type="number" class="form-control" id="quantity" name="quantity" min="1" required>
            </div>
            <div class="col-md-6">
              <label for="unit" class="form-label">Satuan</label>
              <input type="text" class="form-control" id="unit" name="unit" readonly>
            </div>
          </div>
          <div class="mb-3">
            <label for="notes" class="form-label">Catatan (Opsional)</label>
            <textarea class="form-control" id="notes" name="notes" rows="2" placeholder="Catatan tambahan..."></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">
            <i class="bi bi-send"></i> Transfer Stok
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
