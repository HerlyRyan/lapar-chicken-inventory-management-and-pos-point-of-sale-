<!-- reject-modal.blade.php -->
<div class="fixed inset-0 flex items-center justify-center bg-black/50 backdrop-blur-sm z-50 p-4"
    x-show="openModal === 'reject'" x-transition x-cloak @click.self="closeModal()" @keydown.window.escape="closeModal()"
    x-trap="openModal === 'reject'" role="dialog" aria-modal="true" aria-labelledby="rejectModalTitle"
    aria-describedby="rejectModalDesc">
    <div
        class="w-full max-w-md sm:max-w-lg bg-white rounded-3xl shadow-2xl ring-1 ring-black/6 overflow-hidden flex flex-col max-h-[90vh]"
        x-transition.scale>
        {{-- Modal Header --}}
        <header
            class="flex items-center justify-between px-6 py-4 bg-gradient-to-r from-rose-600 via-rose-500 to-red-600 text-white">
            <div class="flex items-center gap-4 min-w-0">
                <div class="flex items-center justify-center w-11 h-11 rounded-xl bg-white/10 shrink-0">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                            d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0zM12 9v4M12 17h.01" />
                    </svg>
                </div>

                <div class="min-w-0">
                    <h2 id="rejectModalTitle" class="font-semibold text-sm sm:text-base truncate">Tolak Permintaan
                    </h2>
                    <p id="rejectModalDesc" class="text-xs text-rose-100/90 truncate">Penolakan akan membatalkan proses
                        permintaan</p>
                </div>
            </div>

            <button type="button" @click="closeModal()" aria-label="Tutup modal"
                class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-white/10 hover:bg-white/20 transition focus:outline-none focus:ring-2 focus:ring-white/40">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
        </header>

        <form @submit.prevent="submitReject()" class="p-6 sm:p-7 max-w-full" role="form"
            aria-labelledby="rejectModalTitle">
            @csrf

            <div class="flex items-start gap-3 bg-rose-50/95 text-rose-900 p-3 rounded-lg mb-4 text-sm shadow-sm">
                <div class="flex-shrink-0">
                    <svg class="w-5 h-5 text-rose-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12A9 9 0 113 12a9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <strong class="block text-sm">Anda akan menolak permintaan.</strong>
                    <span class="text-xs text-rose-700/90">Penolakan akan menghentikan proses dan memberi tahu pemohon.</span>
                </div>
            </div>

            <div class="mb-4 flex flex-col gap-2">
                <p class="text-sm text-slate-700 leading-relaxed whitespace-normal break-words">
                    Anda yakin ingin menolak permintaan penggunaan bahan dari request number:
                </p>
                <p class="text-sm text-slate-800 font-medium break-all whitespace-normal">
                    <strong>#<span x-text="requestNumber ?? '-'"></span></strong>?
                </p>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-2">Alasan Penolakan</label>
                <select x-model="rejectReason" required
                    class="w-full border border-slate-200 rounded-xl p-3 text-sm focus:ring-2 focus:ring-rose-400 focus:border-transparent bg-white text-slate-900 shadow-sm transition">
                    <option value="">Pilih alasan...</option>
                    <option value="not_enough_stock">Stok tidak cukup</option>
                    <option value="invalid_request">Permintaan tidak valid</option>
                    <option value="other">Lainnya</option>
                </select>
            </div>

            <div class="mb-4" x-show="rejectReason === 'other'">
                <label for="reject_reason" class="block text-sm font-medium text-slate-700 mb-2">Catatan Penolakan
                    (opsional)</label>
                <textarea id="reject_reason" name="reject_reason" x-model="rejectNotes" x-ref="rejectNotes" rows="5"
                    maxlength="255"
                    class="w-full border border-slate-200 rounded-xl p-3 text-sm focus:ring-2 focus:ring-rose-400 focus:border-transparent bg-white text-slate-900 shadow-sm transition duration-150 resize-vertical min-h-[100px]"
                    placeholder="Jelaskan alasan penolakan atau instruksi tambahan..."
                    aria-describedby="reject_reason_help reject_reason_counter"></textarea>

                <div class="flex items-center justify-between mt-2 text-xs text-slate-400" id="reject_reason_help">
                    <span>Opsional — tambahkan informasi untuk penerima.</span>
                    <span id="reject_reason_counter" class="text-slate-500"
                        x-text="(255 - (rejectNotes ? rejectNotes.length : 0)) + ' karakter tersisa'"></span>
                </div>
            </div>

            <div class="border-t border-slate-100 pt-4 flex flex-col-reverse sm:flex-row justify-end gap-3 mt-2">
                <button type="button" @click="closeModal()"
                    class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 rounded-lg bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 text-sm font-medium transition focus:outline-none focus:ring-2 focus:ring-rose-300">
                    Batal
                </button>

                <button type="submit" :disabled="isSubmitting"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-gradient-to-r from-rose-600 to-red-600 hover:from-rose-700 hover:to-red-700 text-white text-sm font-semibold shadow-md transition transform hover:-translate-y-0.5 disabled:opacity-60 disabled:cursor-not-allowed focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-red-400"
                    aria-live="polite">
                    <svg x-show="isSubmitting" class="w-4 h-4 animate-spin" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>

                    <svg x-show="!isSubmitting" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>

                    <span x-text="isSubmitting ? 'Memproses...' : 'Tolak'"></span>
                </button>
            </div>
        </form>
    </div>
</div>
