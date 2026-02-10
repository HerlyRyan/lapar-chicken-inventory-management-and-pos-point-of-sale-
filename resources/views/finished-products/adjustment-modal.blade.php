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
                    <!-- Nama Produk -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Nama Produk
                        </label>
                        <input type="text" x-model="product.name" readonly
                            class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-800 shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-300 focus:border-transparent" />
                    </div>

                    <!-- Stok Saat Ini -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Stok Saat Ini
                            </label>
                            <input type="number" x-model="product.stock" readonly
                                class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-800 shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-300 focus:border-transparent" />
                        </div>

                        <!-- Jenis Penyesuaian -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Jenis Penyesuaian <span class="text-red-500">*</span>
                            </label>
                            <select x-model="adjustmentType" @change="updateAdjustmentUI()"
                                class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-800 shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-300 focus:border-transparent">
                                <option value="">Pilih Jenis</option>
                                <option value="add">Tambah Stok</option>
                                <option value="reduce">Kurangi Stok</option>
                                {{-- <option value="set">Atur Ulang Stok</option> --}}
                            </select>
                        </div>
                    </div>

                    <!-- Jumlah -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Jumlah <span class="text-red-500">*</span>
                        </label>
                        <input type="number" x-model="adjustment" @input="previewNewStock()" step="0.001"
                            min="0"
                            class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-800 shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-300 focus:border-transparent" />
                        <p class="text-xs text-gray-500 mt-1 text-bold" x-text="adjustmentHelp"></p>
                    </div>

                    <!-- Preview Stok Baru -->
                    <div x-show="previewNewStock() && adjustmentType"
                        class="p-3 bg-blue-50 rounded-lg border border-blue-200">
                        <p class="text-sm font-medium text-blue-900">
                            Stok Baru: <span x-text="getNewStockPreview()"></span> unit
                        </p>
                    </div>

                    <!-- Alasan -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Alasan <span class="text-red-500">*</span>
                        </label>
                        <select x-model="reason"
                            class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-800 shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-300 focus:border-transparent">
                            <option value="">Pilih Alasan</option>
                            <option value="production_output">Hasil Produksi</option>
                            <option value="quality_control">Kontrol Kualitas</option>
                            <option value="damaged_goods">Barang Rusak</option>
                            <option value="expired_goods">Barang Kadaluarsa</option>
                            <option value="inventory_correction">Koreksi Inventori</option>
                            <option value="other">Lainnya</option>
                        </select>
                    </div>

                    <!-- Catatan -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Catatan
                        </label>
                        <textarea x-model="note" rows="3"
                            class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-800 shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-300 focus:border-transparent"
                            placeholder="Catatan tambahan (opsional)"></textarea>
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
                        Sesuaikan Stok
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
            adjustmentType: '',
            reason: '',
            note: '',
            adjustmentHelp: '',

            open(data) {
                this.product = data;
                this.adjustment = null;
                this.adjustmentType = '';
                this.reason = '';
                this.note = '';
                this.adjustmentHelp = '';
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
                    this.adjustmentType = '';
                    this.reason = '';
                    this.note = '';
                    this.adjustmentHelp = '';
                }, 300);
            },

            updateAdjustmentUI() {
                const type = this.adjustmentType;
                switch (type) {
                    case 'add':
                        this.adjustmentHelp = 'Jumlah yang akan ditambahkan ke stok saat ini';
                        break;
                    case 'reduce':
                        this.adjustmentHelp =
                            `Jumlah yang akan dikurangi (maksimal: ${this.product.stock})`;
                        break;
                    case 'set':
                        this.adjustmentHelp = 'Stok baru yang akan ditetapkan';
                        break;
                    default:
                        this.adjustmentHelp = '';
                }
            },

            getNewStockPreview() {
                const type = this.adjustmentType;
                const quantity = parseFloat(this.adjustment) || 0;

                if (!type || quantity === 0) return '';

                let newStock = 0;
                switch (type) {
                    case 'add':
                        newStock = this.product.stock + quantity;
                        break;
                    case 'reduce':
                        if (quantity > this.product.stock) {
                            this.adjustmentHelp = `Quantity melebihi stock terkini`;
                        } else {
                            newStock = Math.max(0, this.product.stock - quantity);
                        }

                        break;
                    case 'set':
                        newStock = quantity;
                        break;
                }

                return newStock.toLocaleString('id-ID');
            },

            previewNewStock() {
                return this.adjustmentType && this.adjustment;
            },

            async submit() {
                try {
                    if (!this.adjustmentType || !this.reason || this.adjustment === null) {
                        alert('⚠️ Silakan isi semua field yang diperlukan');
                        return;
                    }

                    const payload = {
                        adjustment_type: this.adjustmentType,
                        quantity: Number(this.adjustment),
                        reason: this.reason,
                        notes: this.note || ''
                    };

                    const response = await fetch(
                        `/finished-products-stock/${this.product.id}/adjust`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name=csrf-token]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(payload)
                        });

                    if (!response.ok) {
                        const err = await response.json().catch(() => null);
                        const msg = err?.message ?? 'Gagal menyimpan data';
                        throw new Error(msg);
                    }

                    const data = await response.json();

                    // 🔥 KIRIM EVENT KE INDEX
                    window.dispatchEvent(new CustomEvent('stock-updated', {
                        detail: {
                            productId: data.product_id,
                            newStock: data.new_stock
                        }
                    }));

                    this.close();
                    alert('✅ ' + (data.message ?? 'Stok berhasil disesuaikan'));
                    location.reload();

                } catch (err) {
                    console.error(err);
                    alert('Terjadi kesalahan saat menyimpan penyesuaian stok: ' + (err
                        .message || err));
                }
            }
        }));
    });
</script>
