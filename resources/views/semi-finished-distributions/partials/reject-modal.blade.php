<div class="fixed inset-0 flex items-center justify-center bg-black/50 backdrop-blur-sm z-50 p-4"
    x-show="openModal === 'reject'" x-transition @click.self="closeModal()" x-cloak>
    <div class="w-full max-w-md sm:max-w-lg bg-white rounded-3xl shadow-2xl ring-1 ring-black/6 overflow-hidden"
        x-transition.scale>
        {{-- Modal Header --}}
        <div class="flex items-center justify-between px-6 py-4 bg-gradient-to-r from-rose-700 via-red-600 to-orange-600 text-white">
            <div class="flex items-center gap-4 min-w-0">
                <div class="flex items-center justify-center w-11 h-11 rounded-xl bg-white/10">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                            d="M19 7v6a2 2 0 01-2 2H7a2 2 0 01-2-2V7M16 3l-4 4-4-4" />
                    </svg>
                </div>

                <div class="min-w-0">
                    <h2 class="font-semibold text-sm sm:text-base truncate">Tolak Distribusi</h2>
                    <p class="text-xs text-rose-100/90 truncate">Produk akan dikembalikan ke stok pusat produksi</p>
                </div>
            </div>

            <button type="button" @click="closeModal()"
                class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-white/10 hover:bg-white/20 transition focus:outline-none">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <form :action="`/semi-finished-distributions/${currentId}/reject`" method="POST" class="p-6 sm:p-7">
            @csrf

            <div class="flex items-start gap-3 bg-red-50/90 text-red-800 p-3 rounded-lg mb-4 text-sm shadow-sm">
                <div class="flex-shrink-0">
                    <svg class="w-5 h-5 text-red-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01M21 12A9 9 0 1112 3a9 9 0 019 9z" />
                    </svg>
                </div>
                <div>
                    <strong class="block text-sm">Distribusi akan ditolak.</strong>
                    <span class="text-xs text-red-700/90">Produk akan dikembalikan ke stok pusat produksi saat proses selesai.</span>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-2">Alasan Penolakan <span class="text-red-500">*</span></label>
                <select name="rejection_reason" x-model="rejectReason" required
                    class="w-full border border-slate-200 rounded-xl p-3 text-sm focus:ring-2 focus:ring-red-400 focus:border-transparent bg-white text-slate-900 shadow-sm transition duration-150">
                    <option value="">Pilih Alasan</option>
                    <option value="damaged_goods">Barang Rusak/Cacat</option>
                    <option value="wrong_quantity">Jumlah Tidak Sesuai</option>
                    <option value="wrong_product">Produk Salah</option>
                    <option value="capacity_full">Kapasitas Penyimpanan Penuh</option>
                    <option value="quality_issue">Masalah Kualitas</option>
                    <option value="other">Alasan Lainnya</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-2">Catatan Penolakan</label>
                <textarea name="response_notes" x-model="rejectNotes" required
                    class="w-full border border-slate-200 rounded-xl p-3 text-sm focus:ring-2 focus:ring-red-400 focus:border-transparent bg-white text-slate-900 shadow-sm transition duration-150"
                    rows="5" placeholder="Jelaskan detail alasan penolakan..."></textarea>
                <p class="text-xs text-slate-400 mt-2">Opsional — tambahkan informasi penting terkait penolakan.</p>
            </div>

            <div class="border-t border-slate-100 pt-4 flex flex-col sm:flex-row justify-end gap-3 mt-2">
                <button type="button" @click="closeModal()"
                    class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 rounded-lg bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 text-sm font-medium transition">
                    Batal
                </button>

                <button type="submit"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-gradient-to-r from-rose-600 to-red-600 hover:from-rose-700 hover:to-red-700 text-white text-sm font-semibold shadow-md transition transform hover:-translate-y-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M18.364 5.636l-12.728 12.728M6.343 6.343L18.97 18.97"></path>
                    </svg>
                    Tolak Distribusi
                </button>
            </div>
        </form>
    </div>
</div>
