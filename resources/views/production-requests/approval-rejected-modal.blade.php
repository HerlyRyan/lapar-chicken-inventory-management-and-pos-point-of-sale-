<!-- Approval / Rejection Modal (styled & responsive) -->
<div class="fixed inset-0 flex items-center justify-center bg-black/50 backdrop-blur-sm z-50 p-4"
    x-show="showModal" x-transition @click.self="closeModal()" x-cloak>
    <div class="w-full max-w-md sm:max-w-lg bg-white rounded-3xl shadow-2xl ring-1 ring-black/6 overflow-hidden"
        x-transition.scale @click.stop>

       {{-- Modal Header --}}
       <div class="flex items-center justify-between px-6 py-4 bg-gradient-to-r from-orange-700 via-orange-600 to-rose-700 text-white">
          <div class="flex items-center gap-4 min-w-0">
             <div class="flex items-center justify-center w-11 h-11 rounded-xl bg-white/10">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                        d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V7M16 3l-4 4-4-4" />
                </svg>
             </div>

             <div class="min-w-0">
                <h5 class="font-semibold text-sm sm:text-base truncate" x-text="modalTitle"></h5>
                <p class="text-xs text-orange-100/90 truncate">Tindakan persetujuan / penolakan</p>
             </div>
          </div>

          <button type="button" @click="closeModal()"
                class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-white/10 hover:bg-white/20 transition focus:outline-none">
             <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                 xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
             </svg>
          </button>
       </div>

       {{-- Body --}}
       <div class="p-6 sm:p-7">
          <div x-html="modalMessage" class="mb-4 text-sm text-slate-700"></div>

          <div class="mb-4">
             <label class="block text-sm font-medium text-slate-700 mb-2">Catatan
                <span x-show="requireNotes" class="text-danger">*</span>
             </label>
             <textarea x-model="notes"
                     class="w-full border border-slate-200 rounded-xl p-3 text-sm focus:ring-2 focus:ring-orange-400 focus:border-transparent bg-white text-slate-900 shadow-sm transition duration-150 resize-none"
                     rows="4" placeholder="Masukkan catatan atau alasan..."></textarea>
             <p class="text-xs text-slate-400 mt-2">Opsional — tambahkan informasi penting terkait keputusan.</p>
          </div>

          {{-- Info / hint (optional) --}}
          <div class="flex items-start gap-3 bg-slate-50 text-slate-700 p-3 rounded-lg mb-4 text-sm shadow-sm">
             <svg class="w-5 h-5 text-slate-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                 xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                     d="M13 16h-1v-4h-1m1-4h.01M12 20a8 8 0 100-16 8 8 0 000 16z" />
             </svg>
             <div>
                <strong class="block text-sm">Perhatian</strong>
                <span class="text-xs text-slate-500">Catatan akan tersimpan pada riwayat permintaan.</span>
             </div>
          </div>

          {{-- Actions --}}
          <div class="border-t border-slate-100 pt-4 flex flex-col sm:flex-row justify-end gap-3 mt-2">
             <button type="button" @click="closeModal()"
                    class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 rounded-lg bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 text-sm font-medium transition">
                Batal
             </button>

             <button type="button"
                    :class="action === 'approve'
                       ? 'w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-white text-sm font-semibold shadow-md transition transform hover:-translate-y-0.5 bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-700'
                       : 'w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-white text-sm font-semibold shadow-md transition transform hover:-translate-y-0.5 bg-gradient-to-r from-rose-600 to-orange-600 hover:from-rose-700'"
                    @click="submitApproval()"
                    x-text="action === 'approve' ? 'Setujui' : 'Tolak'">
             </button>
          </div>
       </div>
    </div>
</div>
