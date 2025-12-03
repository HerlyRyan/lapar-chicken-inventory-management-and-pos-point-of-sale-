<div class="fixed inset-0 flex items-center justify-center bg-black/50 backdrop-blur-sm z-50 p-4"
    x-show="openModal === 'accept'" x-transition x-cloak @click.self="closeModal()" @keydown.window.escape="closeModal()"
    x-trap="openModal === 'accept'" role="dialog" aria-modal="true" aria-labelledby="acceptModalTitle"
    aria-describedby="acceptModalDesc">
    <div class="w-full max-w-md sm:max-w-lg bg-white rounded-3xl shadow-2xl ring-1 ring-black/6 overflow-hidden flex flex-col max-h-[90vh]"
        x-transition.scale>
        {{-- Modal Header --}}
        <header
            class="flex items-center justify-between px-6 py-4 bg-gradient-to-r from-orange-700 via-orange-600 to-rose-700 text-white">
            <div class="flex items-center gap-4 min-w-0">
                <div class="flex items-center justify-center w-11 h-11 rounded-xl bg-white/10 shrink-0">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                            d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V7M16 3l-4 4-4-4" />
                    </svg>
                </div>

                <div class="min-w-0">
                    <h2 id="acceptModalTitle" class="font-semibold text-sm sm:text-base truncate">Setujui Permintaan
                    </h2>
                    <p id="acceptModalDesc" class="text-xs text-orange-100/90 truncate">Persetujuan akan mengurangi stok
                        cabang sesuai permintaan</p>
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

        <form @submit.prevent="submitApprove()" class="p-6 sm:p-7 max-w-full" role="form"
            aria-labelledby="acceptModalTitle">
            @csrf

            <div class="flex items-start gap-3 bg-emerald-50/95 text-emerald-900 p-3 rounded-lg mb-4 text-sm shadow-sm">
                <div class="flex-shrink-0">
                    <svg class="w-5 h-5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4M7 7h10v10a2 2 0 01-2 2H9a2 2 0 01-2-2V7z" />
                    </svg>
                </div>
                <div>
                    <strong class="block text-sm">Anda akan menyetujui permintaan.</strong>
                    <span class="text-xs text-emerald-700/90">Stok cabang akan langsung dikurangi sesuai jumlah yang
                        diminta.</span>
                </div>
            </div>

            <div class="mb-4 flex flex-col gap-2">
                <p class="text-sm text-slate-700 leading-relaxed whitespace-normal break-words">
                    Anda yakin ingin menyetujui permintaan penggunaan bahan dari request number:
                </p>
                <p class="text-sm text-slate-800 font-medium break-all whitespace-normal">
                    <strong>#<span x-text="requestNumber ?? '-'"></span></strong>?
                </p>
            </div>



            <div class="mb-4">
                <label for="approval_note" class="block text-sm font-medium text-slate-700 mb-2">Catatan Persetujuan
                    (opsional)</label>
                <textarea id="approval_note" name="approval_note" x-model="approvalNote" x-ref="approvalNote" rows="5"
                    maxlength="255"
                    class="w-full border border-slate-200 rounded-xl p-3 text-sm focus:ring-2 focus:ring-orange-400 focus:border-transparent bg-white text-slate-900 shadow-sm transition duration-150 resize-vertical min-h-[100px]"
                    placeholder="Catatan kondisi, nomor packing, atau informasi tambahan..."
                    aria-describedby="approval_note_help approval_note_counter"></textarea>

                <div class="flex items-center justify-between mt-2 text-xs text-slate-400" id="approval_note_help">
                    <span>Opsional — tambahkan informasi penting terkait persetujuan.</span>
                    <span id="approval_note_counter" class="text-slate-500"
                        x-text="(255 - (approvalNote ? approvalNote.length : 0)) + ' karakter tersisa'"></span>
                </div>
            </div>

            <div class="border-t border-slate-100 pt-4 flex flex-col-reverse sm:flex-row justify-end gap-3 mt-2">
                <button type="button" @click="closeModal()"
                    class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 rounded-lg bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 text-sm font-medium transition focus:outline-none focus:ring-2 focus:ring-orange-300">
                    Batal
                </button>

                <button type="submit" :disabled="isSubmitting"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-gradient-to-r from-orange-600 to-rose-600 hover:from-orange-700 hover:to-rose-700 text-white text-sm font-semibold shadow-md transition transform hover:-translate-y-0.5 disabled:opacity-60 disabled:cursor-not-allowed focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-rose-400"
                    aria-live="polite">
                    <svg x-show="isSubmitting" class="w-4 h-4 animate-spin" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>

                    <svg x-show="!isSubmitting" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>

                    <span x-text="isSubmitting ? 'Memproses...' : 'Setujui'"></span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Small enhancement: focus the textarea when modal opens (requires Alpine scope where openModal & approvalNote exist)
    document.addEventListener('alpine:init', () => {
        Alpine.magic('focusWhenOpen', (el) => () => {
            // Placeholder: actual watch is done inside component scope in most setups.
        });
    });
</script>
