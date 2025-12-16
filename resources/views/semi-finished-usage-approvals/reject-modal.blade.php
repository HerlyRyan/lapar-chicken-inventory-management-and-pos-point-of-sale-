<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true"
    data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content rounded-lg shadow-lg border border-gray-200">
            <form id="rejectForm" action="#" method="POST" data-disable-on-submit="true">
                @csrf
                <input type="hidden" name="return_to" value="approvals">
                <input type="hidden" name="status"
                    value="{{ request('status', \App\Models\SemiFinishedUsageRequest::STATUS_PENDING) }}">
                <input type="hidden" name="branch_id" value="{{ request('branch_id', 'all') }}">

                <div class="modal-header bg-gradient-to-r from-red-50 to-red-50/50 border-b border-gray-200">
                    <h5 class="modal-title text-lg font-semibold text-gray-900" id="rejectModalLabel">
                        <i class="fas fa-times-circle text-red-600 mr-2"></i>Tolak Permintaan
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body py-6 px-6 bg-white">
                    <p class="text-gray-700 mb-4">
                        Anda yakin ingin menolak permintaan penggunaan bahan <strong class="text-gray-900">#<span
                                id="rejectRequestNumber">-</span></strong>?
                    </p>
                    <div class="mb-0">
                        <label for="rejection_reason" class="form-label block text-sm font-medium text-gray-700 mb-2">
                            Alasan Penolakan <span class="text-red-600">*</span>
                        </label>
                        <textarea
                            class="form-control w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent"
                            id="rejection_reason" name="rejection_reason" rows="3" required maxlength="255"></textarea>
                        <small class="text-gray-500 mt-1 block">Maksimal 255 karakter</small>
                    </div>
                </div>

                <div class="modal-footer bg-gray-50 border-t border-gray-200 px-6 py-4">
                    <button type="button"
                        class="btn btn-secondary px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-lg font-medium transition"
                        data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit"
                        class="btn btn-danger px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition">
                        <i class="fas fa-ban mr-2"></i>Tolak
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
