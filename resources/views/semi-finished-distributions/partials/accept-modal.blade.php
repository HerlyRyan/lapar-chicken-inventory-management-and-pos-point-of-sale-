<div class="fixed inset-0 flex items-center justify-center bg-black/50 backdrop-blur-sm z-50 p-4"
    x-show="openModal === 'accept'" x-transition @click.self="closeModal()" x-cloak>
    <div class="w-full max-w-md sm:max-w-lg bg-white rounded-3xl shadow-2xl ring-1 ring-black/6 overflow-hidden"
        x-transition.scale>
       {{-- Modal Header --}}
       <div class="flex items-center justify-between px-6 py-4 bg-gradient-to-r from-orange-700 via-orange-600 to-rose-700 text-white">
          <div class="flex items-center gap-4 min-w-0">
             <div class="flex items-center justify-center w-11 h-11 rounded-xl bg-white/10">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V7M16 3l-4 4-4-4" />
                </svg>
             </div>

             <div class="min-w-0">
                <h2 class="font-semibold text-sm sm:text-base truncate">Terima Distribusi</h2>
                <p class="text-xs text-orange-100/90 truncate">Tambahkan stok ke cabang Anda dengan cepat</p>
             </div>
          </div>

          <button type="button" @click="closeModal()"
             class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-white/10 hover:bg-white/20 transition focus:outline-none">
             <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
             </svg>
          </button>
       </div>

       <form :action="`/semi-finished-distributions/${currentId}/accept`" method="POST" class="p-6 sm:p-7">
          @csrf

          <div class="flex items-start gap-3 bg-emerald-50/90 text-emerald-800 p-3 rounded-lg mb-4 text-sm shadow-sm">
             <div class="flex-shrink-0">
                <svg class="w-5 h-5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7 7h10v10a2 2 0 01-2 2H9a2 2 0 01-2-2V7z" />
                </svg>
             </div>
             <div>
                <strong class="block text-sm">Distribusi akan diterima.</strong>
                <span class="text-xs text-emerald-700/90">Produk akan otomatis ditambahkan ke stok cabang Anda saat proses selesai.</span>
             </div>
          </div>

          <div class="mb-4">
             <label class="block text-sm font-medium text-slate-700 mb-2">Catatan Penerimaan</label>
             <textarea name="response_notes" x-model="acceptNotes"
                class="w-full border border-slate-200 rounded-xl p-3 text-sm focus:ring-2 focus:ring-orange-400 focus:border-transparent bg-white text-slate-900 shadow-sm transition duration-150"
                rows="5" placeholder="Catatan kondisi barang, waktu penerimaan, nomor packing, dll..."></textarea>
             <p class="text-xs text-slate-400 mt-2">Opsional — tambahkan informasi penting terkait penerimaan.</p>
          </div>

          <div class="border-t border-slate-100 pt-4 flex flex-col sm:flex-row justify-end gap-3 mt-2">
             <button type="button" @click="closeModal()"
                class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 rounded-lg bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 text-sm font-medium transition">
                Batal
             </button>

             <button type="submit"
                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-gradient-to-r from-orange-600 to-rose-600 hover:from-orange-700 hover:to-rose-700 text-white text-sm font-semibold shadow-md transition transform hover:-translate-y-0.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Terima Distribusi
             </button>
          </div>
       </form>
    </div>
</div>
