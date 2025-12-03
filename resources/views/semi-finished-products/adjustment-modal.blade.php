<div x-data="adjustmentModal" @open-adjustment-modal.window="open($event.detail)">
    <!-- Overlay -->
    <div x-show="isOpen" x-transition.opacity class="fixed inset-0 bg-black/45 backdrop-blur-sm z-40" @click="close">
    </div>

    <!-- Modal -->
    <div x-show="isOpen" x-transition x-trap.noscroll="isOpen"
        class="fixed inset-0 z-50 flex items-center justify-center px-4 sm:px-6">
        <div
            class="w-full max-w-lg mx-auto bg-white rounded-2xl shadow-2xl ring-1 ring-black/5 overflow-hidden transform transition-all max-h-[90vh]">
            <!-- Header -->
            <div class="flex items-center justify-between p-4 sm:p-5 bg-gradient-to-r from-orange-500 to-red-600">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center text-white shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                    </div>
                    <div>
                        <h3 id="modal-title" class="text-white text-lg sm:text-xl font-semibold leading-6">
                            Penyesuaian Stok
                        </h3>
                        <p class="text-white/80 text-sm mt-0.5">Sesuaikan jumlah stok produk dengan aman</p>
                    </div>
                </div>

                <button @click="close"
                    class="inline-flex items-center justify-center p-2 rounded-md bg-white/10 hover:bg-white/20 text-white transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Body -->
            <form @submit.prevent="submit" class="p-5 sm:p-6 space-y-4 sm:space-y-5">
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Nama Produk
                        </label>
                        <input type="text" x-model="product.name" readonly
                            class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-800 shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-300 focus:border-transparent" />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Stok Saat Ini
                            </label>
                            <input type="number" x-model="product.stock" readonly
                                class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-800 shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-300 focus:border-transparent" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Penyesuaian (Jumlah Baru)
                            </label>
                            <input type="number" x-model="adjustment"
                                class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-800 shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-300 focus:border-transparent" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Catatan
                        </label>
                        <textarea x-model="note" rows="3"
                            class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-800 shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-300 focus:border-transparent"></textarea>
                    </div>
                </div>

                <!-- Footer -->
                <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-100 mt-1">
                    <button type="button" @click="close"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-md bg-gray-50 text-gray-700 border border-gray-200 hover:bg-gray-100 transition">
                        Batal
                    </button>

                    <button type="submit"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-md bg-gradient-to-r from-orange-500 to-red-600 text-white font-medium shadow hover:from-orange-600 hover:to-red-700 transition">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('adjustmentModal', () => ({
            isOpen: false,
            product: {
                id: null,
                name: '',
                stock: 0
            },
            adjustment: null,
            note: '',

            open(data) {
                this.product = data;
                this.adjustment = data.stock;
                this.note = '';
                this.isOpen = true;
            },

            close() {
                this.isOpen = false;
                setTimeout(() => {
                    this.product = {
                        id: null,
                        name: '',
                        stock: 0
                    };
                    this.adjustment = null;
                    this.note = '';
                }, 300);
            },

            async submit() {
                try {
                    const response = await fetch(
                        `/semi-finished-stock/${this.product.id}/adjust`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name=csrf-token]').content
                            },
                            body: JSON.stringify({
                                stock: this.adjustment,
                                note: this.note
                            })
                        });

                    if (!response.ok) throw new Error('Gagal menyimpan data');
                    this.close();
                    alert('✅ Stok berhasil disesuaikan');
                } catch (err) {
                    console.error(err);
                    alert('Terjadi kesalahan saat menyimpan penyesuaian stok.');
                }
            }
        }));
    });
</script>
