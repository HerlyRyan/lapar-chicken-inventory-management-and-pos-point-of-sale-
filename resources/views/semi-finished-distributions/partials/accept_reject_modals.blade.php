<!-- Accept Distribution Modal -->
<div class="modal fade" id="acceptModal" tabindex="-1" aria-labelledby="acceptModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="acceptModalLabel">Terima Distribusi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="acceptForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle me-2"></i>
                        <strong>Distribusi akan diterima.</strong> Produk akan ditambahkan ke stok cabang Anda.
                    </div>
                    <div class="mb-3">
                        <label for="response_notes" class="form-label">Catatan Penerimaan</label>
                        <textarea name="response_notes" id="response_notes" class="form-control" rows="3" placeholder="Catatan kondisi barang, waktu penerimaan, dll..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle me-1"></i>
                        Terima Distribusi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Distribution Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejectModalLabel">Tolak Distribusi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="rejectForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Distribusi akan ditolak.</strong> Produk akan dikembalikan ke stok pusat produksi.
                    </div>
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                        <select name="rejection_reason" id="rejection_reason" class="form-select" required>
                            <option value="">Pilih Alasan</option>
                            <option value="damaged_goods">Barang Rusak/Cacat</option>
                            <option value="wrong_quantity">Jumlah Tidak Sesuai</option>
                            <option value="wrong_product">Produk Salah</option>
                            <option value="capacity_full">Kapasitas Penyimpanan Penuh</option>
                            <option value="quality_issue">Masalah Kualitas</option>
                            <option value="other">Alasan Lainnya</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="response_notes_reject" class="form-label">Catatan Penolakan <span class="text-danger">*</span></label>
                        <textarea name="response_notes" id="response_notes_reject" class="form-control" rows="3" placeholder="Jelaskan detail alasan penolakan..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-x-circle me-1"></i>
                        Tolak Distribusi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function showAcceptModal(distributionId) {
    const modal = new bootstrap.Modal(document.getElementById('acceptModal'));
    const form = document.getElementById('acceptForm');
    form.action = `/semi-finished-distributions/${distributionId}/accept`;
    document.getElementById('response_notes').value = '';
    modal.show();
}

function showRejectModal(distributionId) {
    const modal = new bootstrap.Modal(document.getElementById('rejectModal'));
    const form = document.getElementById('rejectForm');
    form.action = `/semi-finished-distributions/${distributionId}/reject`;
    document.getElementById('rejection_reason').value = '';
    document.getElementById('response_notes_reject').value = '';
    modal.show();
}
</script>
@endpush
