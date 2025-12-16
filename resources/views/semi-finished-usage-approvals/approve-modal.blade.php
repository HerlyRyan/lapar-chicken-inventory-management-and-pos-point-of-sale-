<div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true"
    data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content border-0 shadow-lg">
            <form id="approveForm" action="#" method="POST" data-disable-on-submit="true">
                @csrf
                <input type="hidden" name="return_to" value="approvals">
                <input type="hidden" name="status"
                    value="{{ request('status', \App\Models\SemiFinishedUsageRequest::STATUS_PENDING) }}">
                <input type="hidden" name="branch_id" value="{{ request('branch_id', 'all') }}">

                <div class="modal-header border-b border-gray-200 bg-gradient-to-r from-green-50 to-emerald-50">
                    <h5 class="modal-title text-gray-900 font-semibold" id="approveModalLabel">
                        <i class="fas fa-check-circle text-green-600 mr-2"></i>Setujui Permintaan
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body px-6 py-6">
                    <p class="text-gray-700 mb-4">
                        Anda yakin ingin menyetujui permintaan penggunaan bahan <strong class="text-gray-900">#<span
                                id="approveRequestNumber">-</span></strong>?
                    </p>

                    <div
                        class="alert alert-warning bg-amber-50 border-l-4 border-amber-500 text-amber-900 mb-4 p-3 rounded-r-lg">
                        <i class="fas fa-exclamation-triangle text-amber-600 mr-2"></i>
                        <span class="text-sm">Persetujuan akan langsung mengurangi stok cabang sesuai jumlah yang
                            diminta.</span>
                    </div>

                    <div class="mb-0">
                        <label for="approval_note" class="block text-sm font-medium text-gray-700 mb-2">
                            Catatan Persetujuan <span class="text-gray-500">(opsional)</span>
                        </label>
                        <textarea
                            class="form-control w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent resize-none"
                            id="approval_note" name="approval_note" rows="3" maxlength="255"
                            placeholder="Contoh: Disetujui untuk produksi hari ini"></textarea>
                        <small class="text-gray-500 text-xs mt-1 block">Maksimal 255 karakter</small>
                    </div>
                </div>

                <div class="modal-footer border-t border-gray-200 bg-gray-50 gap-2">
                    <button type="button"
                        class="btn btn-secondary px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-lg text-sm font-medium transition"
                        data-bs-dismiss="modal">
                        <i class="fas fa-times mr-2"></i>Batal
                    </button>
                    <button type="submit"
                        class="btn btn-success px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg text-sm font-medium transition">
                        <i class="fas fa-check mr-2"></i>Setujui
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
