@extends('layouts.app')
@php use Illuminate\Support\Facades\Storage; @endphp

@section('title', 'Tambah Penjualan')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-orange-50 via-white to-red-50 py-6">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8" x-data="saleForm()">
            {{-- Header Section --}}
            <x-form.header title="Penjualan Baru" backRoute="{{ route('sales.index') }}" />

            <div class="mt-6 bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                <x-form.card-header title="Buat Penjualan" type="add" />

                <div class="p-6 sm:p-8">
                    <form action="{{ route('sales.store') }}" method="POST" id="saleForm"
                        @submit.prevent="prepareSubmission">
                        @csrf

                        {{-- Step 1: Branch Selection (Always visible, determines item availability) --}}
                        <div class="mb-8 p-4 bg-orange-50 border border-orange-200 rounded-xl">
                            <label for="branch_id" class="block text-lg font-bold text-gray-800 mb-2">
                                Pilih Cabang <span class="text-red-500">*</span>
                            </label>
                            <select name="branch_id" id="branch_id" x-model="branchId" required @change="fetchBranchItems"
                                class="w-full px-4 py-3 border border-orange-300 rounded-xl focus:ring-orange-500 focus:border-orange-500 text-base">
                                <option value="">Pilih cabang...</option>
                                @foreach ($branches ?? [] as $b)
                                    <option value="{{ $b->id }}" {{ old('branch_id') == $b->id ? 'selected' : '' }}>
                                        {{ $b->name }} ({{ $b->code }})
                                    </option>
                                @endforeach
                            </select>
                            @error('branch_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Main Content: Item Selection (Left) and Cart (Right) --}}
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8" x-show="branchId" x-cloak>

                            {{-- LEFT COLUMN: Item Selection (Products & Packages) --}}
                            <div class="lg:col-span-2">
                                <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                                    <i class="bi bi-box-seam-fill text-orange-500 mr-2"></i> Pilih Produk & Paket
                                    <span x-show="isLoading" class="ml-3 text-sm text-gray-500">Memuat...</span>
                                </h3>

                                {{-- Search and Tabs --}}
                                <div class="flex flex-col sm:flex-row gap-4 mb-4">
                                    <input type="text" x-model="searchTerm" placeholder="Cari item..."
                                        class="flex-1 px-4 py-3 border border-gray-300 rounded-xl focus:ring-orange-500 focus:border-orange-500">

                                    <div class="flex space-x-2 p-1 bg-gray-100 rounded-xl">
                                        <button type="button" @click="activeTab = 'products'"
                                            :class="{ 'bg-white shadow-md text-orange-600': activeTab === 'products', 'text-gray-600': activeTab !== 'products' }"
                                            class="px-4 py-2 rounded-lg font-medium transition-colors">Produk</button>
                                        <button type="button" @click="activeTab = 'packages'"
                                            :class="{ 'bg-white shadow-md text-orange-600': activeTab === 'packages', 'text-gray-600': activeTab !== 'packages' }"
                                            class="px-4 py-2 rounded-lg font-medium transition-colors">Paket</button>
                                    </div>
                                </div>

                                {{-- Item Cards --}}
                                <div
                                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 h-[600px] overflow-y-auto pr-2">
                                    <template x-if="activeTab === 'products'">
                                        <template x-for="item in filteredProducts" :key="item.id">
                                            <div
                                                class="bg-white border border-gray-100 rounded-xl shadow-lg p-4 transition-all hover:shadow-xl">
                                                <div class="font-semibold text-gray-900" x-text="item.name"></div>
                                                <div class="text-sm text-gray-500 mb-3" x-text="'Stok: ' + item.stock">
                                                </div>
                                                <div class="text-lg font-bold text-orange-600 mb-4"
                                                    x-text="'Rp ' + item.price.toLocaleString('id-ID')"></div>

                                                <div class="flex items-center space-x-2">
                                                    <input type="number" min="1" x-model.number="item.quantity"
                                                        class="w-16 px-2 py-1 border border-gray-300 rounded-lg text-center focus:ring-orange-500" />
                                                    <button type="button" @click="addToCart(item, 'product')"
                                                        :disabled="item.quantity < 1 || (item.item_type === 'product' && item
                                                            .quantity > item.stock)"
                                                        class="flex-1 bg-green-600 text-white rounded-lg px-3 py-1 text-sm font-medium hover:bg-green-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                                        <i class="bi bi-cart-plus mr-1"></i> Tambah
                                                    </button>
                                                </div>
                                                <p x-show="item.item_type === 'product' && item.quantity > item.stock"
                                                    class="text-xs text-red-500 mt-1">Stok tidak cukup!</p>
                                            </div>
                                        </template>
                                    </template>

                                    <template x-if="activeTab === 'packages'">
                                        <template x-for="item in filteredPackages" :key="item.id">
                                            <div
                                                class="bg-white border border-gray-100 rounded-xl shadow-lg p-4 transition-all hover:shadow-xl">
                                                <div class="font-semibold text-gray-900" x-text="item.name"></div>
                                                <div class="text-sm text-gray-500 mb-3">Paket</div>
                                                <div class="text-lg font-bold text-orange-600 mb-4"
                                                    x-text="'Rp ' + item.price.toLocaleString('id-ID')"></div>

                                                <div class="flex items-center space-x-2">
                                                    <input type="number" min="1" x-model.number="item.quantity"
                                                        class="w-16 px-2 py-1 border border-gray-300 rounded-lg text-center focus:ring-orange-500" />
                                                    <button type="button" @click="addToCart(item, 'package')"
                                                        :disabled="item.quantity < 1"
                                                        class="flex-1 bg-green-600 text-white rounded-lg px-3 py-1 text-sm font-medium hover:bg-green-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                                        <i class="bi bi-cart-plus mr-1"></i> Tambah
                                                    </button>
                                                </div>
                                            </div>
                                        </template>
                                    </template>
                                    <template
                                        x-if="filteredProducts.length === 0 && activeTab === 'products' && !isLoading">
                                        <p class="text-gray-500 col-span-full">Tidak ada produk ditemukan.</p>
                                    </template>
                                    <template
                                        x-if="filteredPackages.length === 0 && activeTab === 'packages' && !isLoading">
                                        <p class="text-gray-500 col-span-full">Tidak ada paket ditemukan.</p>
                                    </template>
                                </div>
                            </div>

                            {{-- RIGHT COLUMN: Cart and Totals --}}
                            <div class="lg:col-span-1">
                                <div class="sticky top-6">
                                    <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                                        <i class="bi bi-basket-fill text-orange-500 mr-2"></i> Keranjang Belanja
                                    </h3>

                                    <div
                                        class="bg-gray-50 p-4 rounded-xl mb-4 h-[300px] overflow-y-auto border border-gray-200">
                                        <template x-if="cart.items.length === 0">
                                            <p class="text-gray-500 text-center py-4">Keranjang kosong.</p>
                                        </template>
                                        <template x-for="(item, index) in cart.items" :key="index">
                                            <div
                                                class="flex items-center justify-between border-b border-gray-200 py-3 last:border-b-0">
                                                <input type="hidden" :name="'items[' + index + '][item_type]'"
                                                    :value="item.item_type">
                                                <input type="hidden" :name="'items[' + index + '][item_id]'"
                                                    :value="item.item_id">
                                                <input type="hidden" :name="'items[' + index + '][item_name]'"
                                                    :value="item.item_name">
                                                <input type="hidden" :name="'items[' + index + '][unit_price]'"
                                                    :value="item.unit_price">

                                                <div class="flex-1">
                                                    <p class="font-medium text-sm text-gray-800" x-text="item.item_name">
                                                    </p>
                                                    <p class="text-xs text-gray-500"
                                                        x-text="item.item_type.toUpperCase()"></p>
                                                    <p class="text-sm text-orange-600 font-semibold"
                                                        x-text="item.quantity + ' x Rp ' + item.unit_price.toLocaleString('id-ID')">
                                                    </p>
                                                </div>

                                                <div class="flex items-center space-x-2">
                                                    <input type="number" min="1"
                                                        :name="'items[' + index + '][quantity]'"
                                                        x-model.number="item.quantity" @input="updateItemQuantity(index)"
                                                        class="w-14 px-1 py-1 border rounded-lg text-center text-sm" />

                                                    <input type="hidden" :name="'items[' + index + '][subtotal]'"
                                                        :value="item.subtotal">

                                                    <button type="button" @click="removeFromCart(index)"
                                                        class="text-red-600 hover:text-red-800 transition-colors p-1">
                                                        <i class="bi bi-trash-fill"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </template>
                                        @error('items')
                                            <p class="mt-2 text-sm text-red-600">Keranjang tidak boleh kosong.</p>
                                        @enderror
                                    </div>

                                    {{-- Basic Info - Hidden in Left Column --}}
                                    <div class="space-y-4 mb-4">
                                        <div>
                                            <label for="customer_name"
                                                class="block text-sm font-semibold text-gray-700 mb-2">Nama
                                                Pelanggan</label>
                                            <input type="text" name="customer_name" id="customer_name"
                                                x-model="customerName" class="w-full px-4 py-3 border rounded-xl"
                                                placeholder="Nama pelanggan (opsional)">
                                        </div>

                                        <div>
                                            <label for="phone"
                                                class="block text-sm font-semibold text-gray-700 mb-2">Telepon</label>
                                            <div
                                                class="flex rounded-xl border focus-within:ring-2 focus-within:ring-orange-500 transition-all">
                                                <span
                                                    class="inline-flex items-center px-4 text-gray-500 bg-gray-50 border-r border-gray-300 rounded-l-xl text-sm font-medium">+62</span>
                                                <input type="text" name="customer_phone" id="phone"
                                                    x-model="customerPhone"
                                                    class="flex-1 px-4 py-3 border-0 rounded-r-xl focus:ring-0"
                                                    placeholder="813xxxxxxxx" @input="formatPhoneNumber" maxlength="15">
                                            </div>
                                            @error('customer_phone')
                                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Discount and Payment --}}
                                    <div class="grid grid-cols-2 gap-4 mb-4">
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">Diskon
                                                Tipe</label>
                                            <select name="discount_type" x-model="cart.discount_type"
                                                @change="recalculateTotals" class="w-full px-4 py-3 border rounded-xl">
                                                <option value="none">none</option>
                                                <option value="percentage">percentage</option>
                                                <option value="nominal">nominal</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">Nilai
                                                Diskon</label>
                                            <input type="number" name="discount_value"
                                                x-model.number="cart.discount_value" min="0" step="0.01"
                                                @input="recalculateTotals" class="w-full px-4 py-3 border rounded-xl">
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Metode Pembayaran
                                            <span class="text-red-500">*</span></label>
                                        <select name="payment_method" x-model="cart.payment_method"
                                            @change="recalculateTotals" class="w-full px-4 py-3 border rounded-xl"
                                            required>
                                            <option value="cash">cash</option>
                                            <option value="qris">qris</option>
                                        </select>
                                    </div>

                                    {{-- Totals Summary --}}
                                    <div class="mt-6 border-t border-gray-200 pt-4 space-y-3">
                                        <div class="flex justify-between text-gray-700">
                                            <span>Subtotal:</span>
                                            <span x-text="'Rp ' + cart.subtotal_amount.toLocaleString('id-ID')"></span>
                                            <input type="hidden" name="subtotal_amount" :value="cart.subtotal_amount">
                                        </div>
                                        <div class="flex justify-between text-red-500">
                                            <span>Diskon:</span>
                                            <span x-text="'- Rp ' + cart.discount_amount.toLocaleString('id-ID')"></span>
                                            <input type="hidden" name="discount_amount" :value="cart.discount_amount">
                                        </div>
                                        <div class="flex justify-between text-xl font-bold text-gray-900 border-t pt-2">
                                            <span>Total Akhir:</span>
                                            <span x-text="'Rp ' + cart.final_amount.toLocaleString('id-ID')"></span>
                                            <input type="hidden" name="final_amount" :value="cart.final_amount">
                                        </div>

                                        <div class="pt-2">
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">Bayar
                                                (tunai)</label>
                                            <input type="number" name="paid_amount" x-model.number="cart.paid_amount"
                                                min="0" @input="recalculateTotals"
                                                :readonly="cart.payment_method !== 'cash'"
                                                class="w-full px-4 py-3 border rounded-xl"
                                                :class="{ 'bg-gray-50': cart.payment_method !== 'cash' }">
                                        </div>

                                        <div class="flex justify-between text-lg font-bold text-green-600 border-t pt-2">
                                            <span>Kembalian:</span>
                                            <span x-text="'Rp ' + cart.change_amount.toLocaleString('id-ID')"></span>
                                            <input type="hidden" name="change_amount" :value="cart.change_amount">
                                        </div>
                                    </div>

                                    {{-- Actions --}}
                                    <div class="border-t border-gray-200 pt-6 mt-6">
                                        <button type="submit"
                                            :disabled="cart.items.length === 0 || (cart.payment_method === 'cash' && cart
                                                .paid_amount < cart.final_amount)"
                                            class="w-full inline-flex items-center justify-center px-8 py-3 bg-gradient-to-r from-orange-600 to-red-600 rounded-xl text-lg font-medium text-white hover:opacity-90 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                                            <i class="bi bi-cash-stack mr-2"></i> Simpan Penjualan
                                        </button>
                                        <a href="{{ route('sales.index') }}"
                                            class="mt-3 w-full inline-flex items-center justify-center px-6 py-3 border border-gray-300 rounded-xl text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-all">
                                            Batal
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('saleForm', () => ({
            branchId: '{{ old('branch_id') ?? '' }}',
            customerName: '{{ old('customer_name') ?? '' }}',
            customerPhone: '{{ old('customer_phone') ?? '' }}',
            activeTab: 'products', // 'products' or 'packages'
            isLoading: false,

            // Item Data
            rawProducts: [],
            rawPackages: [],
            searchTerm: '',

            // Cart State
            cart: {
                items: @json(old('items') ?? []), // Initial old input
                discount_type: '{{ old('discount_type', 'none') }}',
                discount_value: parseFloat('{{ old('discount_value', 0) }}'),
                payment_method: '{{ old('payment_method', 'cash') }}',
                subtotal_amount: parseInt('{{ old('subtotal_amount', 0) }}'),
                discount_amount: parseInt('{{ old('discount_amount', 0) }}'),
                final_amount: parseInt('{{ old('final_amount', 0) }}'),
                paid_amount: parseInt('{{ old('paid_amount', 0) }}'),
                change_amount: parseInt('{{ old('change_amount', 0) }}'),
            },

            // --- COMPUTED PROPERTIES (for Item Cards) ---
            get filteredProducts() {
                return this.rawProducts.filter(item =>
                    item.name.toLowerCase().includes(this.searchTerm.toLowerCase())
                );
            },
            get filteredPackages() {
                return this.rawPackages.filter(item =>
                    item.name.toLowerCase().includes(this.searchTerm.toLowerCase())
                );
            },

            // --- METHODS ---
            init() {
                // Initialize items in cart for totals calculation
                if (this.cart.items.length > 0) {
                    this.cart.items = this.cart.items.map(item => ({
                        ...item,
                        quantity: parseInt(item.quantity) || 1,
                        unit_price: parseFloat(item.unit_price) || 0,
                        subtotal: (parseInt(item.quantity) || 1) * (parseFloat(item
                            .unit_price) || 0)
                    }));
                    this.recalculateTotals();
                }

                // If branch selected on load (e.g., old input), fetch items
                if (this.branchId) {
                    this.fetchBranchItems();
                }
            },

            async fetchBranchItems() {
                if (!this.branchId) return;

                this.isLoading = true;
                try {
                    const res = await fetch(`/branches/${this.branchId}/items`);
                    if (!res.ok) throw new Error('Failed to fetch items');
                    const data = await res.json();

                    // Reset products/packages and map them to include an Alpine quantity state
                    this.rawProducts = (data.products || []).map(p => ({
                        ...p,
                        quantity: 1, // Alpine state for item card input
                        item_type: 'product'
                    }));
                    this.rawPackages = (data.packages || []).map(p => ({
                        ...p,
                        quantity: 1, // Alpine state for item card input
                        item_type: 'package'
                    }));

                } catch (err) {
                    console.error('Gagal memuat data item:', err);
                    alert('Gagal memuat daftar produk/paket.');
                    this.rawProducts = [];
                    this.rawPackages = [];
                } finally {
                    this.isLoading = false;
                }
            },

            addToCart(item, type) {
                // Check if item already exists in cart
                const existingIndex = this.cart.items.findIndex(
                    cartItem => cartItem.item_id == item.id && cartItem.item_type === type
                );

                if (existingIndex !== -1) {
                    // Item exists, update quantity
                    this.cart.items[existingIndex].quantity += item.quantity;
                } else {
                    // Item new, add to cart
                    const newItem = {
                        item_id: item.id,
                        item_type: type,
                        item_name: item.name,
                        unit_price: parseFloat(item.price) || 0,
                        quantity: item.quantity,
                        subtotal: (item.quantity * parseFloat(item.price)) || 0
                    };
                    this.cart.items.push(newItem);
                }

                // Reset item card quantity
                item.quantity = 1;

                this.recalculateTotals();
            },

            removeFromCart(index) {
                this.cart.items.splice(index, 1);
                this.recalculateTotals();
            },

            updateItemQuantity(index) {
                const item = this.cart.items[index];
                item.quantity = Math.max(1, item.quantity); // Ensure quantity is min 1
                item.subtotal = item.quantity * item.unit_price;
                this.recalculateTotals();
            },

            recalculateTotals() {
                let subtotal = 0;

                // 1. Calculate Subtotal from Cart Items
                this.cart.items.forEach(item => {
                    item.subtotal = item.quantity * item.unit_price;
                    subtotal += item.subtotal;
                });
                this.cart.subtotal_amount = subtotal;

                // 2. Calculate Discount Amount
                let discountAmount = 0;
                const discValue = this.cart.discount_value;

                if (this.cart.discount_type === 'percentage') {
                    discountAmount = Math.round((subtotal * discValue) / 100);
                } else if (this.cart.discount_type === 'nominal') {
                    discountAmount = Math.round(discValue);
                }

                // Clamp discount amount
                discountAmount = Math.max(0, Math.min(discountAmount, subtotal));
                this.cart.discount_amount = discountAmount;

                // 3. Calculate Final Amount
                const finalAmount = Math.max(0, subtotal - discountAmount);
                this.cart.final_amount = finalAmount;

                // 4. Calculate Paid and Change
                if (this.cart.payment_method === 'qris') {
                    this.cart.paid_amount = finalAmount;
                }
                const paid = this.cart.payment_method === 'cash' ? this.cart.paid_amount :
                    finalAmount;
                this.cart.paid_amount = paid; // Ensure consistency
                this.cart.change_amount = Math.max(0, paid - finalAmount);
            },

            formatPhoneNumber(event) {
                let value = event.target.value.replace(/\D/g, '');
                event.target.value = value;
                this.customerPhone = value;
            },

            // Submission Logic
            prepareSubmission() {
                // Final re-calculation
                this.recalculateTotals();

                // Validation: Check if cart is empty
                if (this.cart.items.length === 0) {
                    alert('Keranjang belanja tidak boleh kosong!');
                    return;
                }

                // Validation: Check if paid amount is sufficient for cash payment
                if (this.cart.payment_method === 'cash' && this.cart.paid_amount < this.cart
                    .final_amount) {
                    alert('Jumlah bayar tidak mencukupi!');
                    return;
                }

                // Final phone format before submission
                const phoneInput = document.getElementById('phone');
                if (phoneInput && phoneInput.value) {
                    phoneInput.value = '62' + phoneInput.value.replace(/^0+/, '');
                }

                // The form is ready, submit it
                document.getElementById('saleForm').submit();
            }
        }));
    });
</script>
