@extends('layouts.app')
@php use Illuminate\Support\Facades\Storage; @endphp

@section('title', 'Tambah Penjualan')

@section('content')
    <style>
        /* Agar scrollbar terlihat modern dan tidak mengganggu UI */
        .custom-scrollbar::-webkit-scrollbar {
            width: 5px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #fffaf0;
            /* Orange very light */
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #fed7aa;
            /* Orange 200 */
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #fb923c;
            /* Orange 400 */
        }
    </style>

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
                                    <span x-show="isLoading"
                                        class="ml-3 text-sm text-gray-400 animate-pulse">Memuat...</span>
                                </h3>

                                {{-- Search and Tabs --}}
                                <div class="flex flex-col gap-4 mb-6">
                                    <div class="flex flex-col md:flex-row gap-4">
                                        <div class="relative flex-1">
                                            <i
                                                class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                            <input type="text" x-model="searchTerm" placeholder="Cari item..."
                                                class="w-full pl-11 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition-all">
                                        </div>

                                        <div class="flex p-1 bg-orange-50 rounded-xl border border-orange-100">
                                            <button type="button" @click="activeTab = 'products'"
                                                :class="activeTab === 'products' ? 'bg-white shadow-sm text-orange-600' :
                                                    'text-orange-400'"
                                                class="flex-1 md:flex-none px-6 py-2 rounded-lg font-bold transition-all duration-200">
                                                Produk
                                            </button>
                                            <button type="button" @click="activeTab = 'packages'"
                                                :class="activeTab === 'packages' ? 'bg-white shadow-sm text-orange-600' :
                                                    'text-orange-400'"
                                                class="flex-1 md:flex-none px-6 py-2 rounded-lg font-bold transition-all duration-200">
                                                Paket
                                            </button>
                                        </div>
                                    </div>

                                    {{-- Filter Kategori --}}
                                    <div class="flex space-x-2 overflow-x-auto pb-2 scrollbar-hide"
                                        x-show="activeTab === 'products'">
                                        <button type="button" @click="filterCategory = 'all'"
                                            :class="filterCategory === 'all' ?
                                                'bg-orange-500 text-white shadow-orange-200 shadow-lg' :
                                                'bg-white text-gray-600 border border-gray-200 hover:border-orange-300'"
                                            class="whitespace-nowrap px-5 py-2 rounded-full text-sm font-semibold transition-all">
                                            Semua
                                        </button>

                                        <template x-for="category in categories" :key="category.key">
                                            <button type="button" @click="filterCategory = category.key"
                                                :class="filterCategory === category.key ?
                                                    'bg-orange-500 text-white shadow-orange-200 shadow-lg' :
                                                    'bg-white text-gray-600 border border-gray-200 hover:border-orange-300'"
                                                class="whitespace-nowrap px-5 py-2 rounded-full text-sm font-semibold transition-all flex items-center">
                                                <i class="bi mr-2" :class="category.icon"></i>
                                                <span x-text="category.label"></span>
                                            </button>
                                        </template>
                                    </div>
                                </div>

                                {{-- Item Cards Grid --}}
                                <div
                                    class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 h-[650px] overflow-y-auto pr-2 content-start custom-scrollbar">

                                    <template x-if="activeTab === 'products'">
                                        <template x-for="item in filteredProducts" :key="item.id">
                                            <div
                                                class="group relative bg-white border border-gray-100 rounded-2xl p-4 shadow-sm hover:shadow-md hover:border-orange-200 transition-all flex flex-col h-64">

                                                <div class="flex justify-between items-start mb-3">
                                                    <span
                                                        class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-orange-50 text-orange-600 border border-orange-100">Produk</span>
                                                    <div :class="item.stock < 10 ? 'text-red-500' : 'text-green-500'"
                                                        class="flex items-center gap-1 text-[11px] font-bold">
                                                        <i class="bi bi-box-seam"></i>
                                                        <span x-text="item.stock"></span>
                                                    </div>
                                                </div>

                                                <div class="flex-1">
                                                    <h4 class="font-bold text-gray-800 text-sm md:text-base leading-tight line-clamp-2 group-hover:text-orange-600 transition-colors"
                                                        x-text="item.name"></h4>
                                                    <p class="text-[11px] text-gray-400 mt-1 uppercase tracking-wider"
                                                        x-text="item.category?.name || 'Item'"></p>
                                                </div>

                                                <div class="mt-4 pt-4 border-t border-gray-50">
                                                    <div class="text-lg font-black text-orange-600 mb-3"
                                                        x-text="'Rp ' + item.price.toLocaleString('id-ID')"></div>

                                                    <div class="flex items-center gap-2">
                                                        <div
                                                            class="flex items-center bg-gray-50 rounded-lg border border-gray-100 h-9">
                                                            <button type="button"
                                                                @click="item.quantity > 1 ? item.quantity-- : null"
                                                                class="px-2 text-gray-400 hover:text-orange-600">-</button>
                                                            <input type="number" x-model.number="item.quantity"
                                                                class="w-8 bg-transparent border-0 text-center text-xs font-bold focus:ring-0 p-0">
                                                            <button type="button" @click="item.quantity++"
                                                                class="px-2 text-gray-400 hover:text-orange-600">+</button>
                                                        </div>

                                                        <button type="button" @click="addToCart(item, 'product')"
                                                            :disabled="item.stock <= 0 || item.quantity > item.stock"
                                                            class="flex-1 bg-orange-500 text-white rounded-lg h-9 text-xs font-bold hover:bg-orange-600 disabled:opacity-30 transition-colors shadow-sm shadow-orange-100">
                                                            <i class="bi bi-cart-plus mr-1"></i> TAMBAH
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </template>

                                    <template x-if="activeTab === 'packages'">
                                        <template x-for="item in filteredPackages" :key="item.id">
                                            <div
                                                class="group relative bg-white border border-gray-100 rounded-2xl p-4 shadow-sm hover:shadow-md hover:border-orange-200 transition-all flex flex-col h-64 border-l-4 border-l-orange-500">
                                                <div class="flex justify-between items-start mb-3">
                                                    <span
                                                        class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-orange-600 text-white">Paket
                                                        Hemat</span>
                                                </div>

                                                <div class="flex-1">
                                                    <h4 class="font-bold text-gray-800 text-sm md:text-base leading-tight line-clamp-2"
                                                        x-text="item.name"></h4>
                                                    <p class="text-[11px] text-gray-400 mt-1">PROMO BUNDLE</p>
                                                </div>

                                                <div class="mt-4 pt-4 border-t border-gray-50">
                                                    <div class="text-lg font-black text-orange-600 mb-3"
                                                        x-text="'Rp ' + item.price.toLocaleString('id-ID')"></div>
                                                    <div class="flex items-center gap-2">
                                                        <input type="number" x-model.number="item.quantity"
                                                            class="w-12 h-9 bg-gray-50 border border-gray-100 rounded-lg text-center text-xs font-bold focus:ring-orange-500">
                                                        <button type="button" @click="addToCart(item, 'package')"
                                                            :disabled="item.quantity < 1"
                                                            class="flex-1 bg-orange-500 text-white rounded-lg h-9 text-xs font-bold hover:bg-orange-600 transition-colors shadow-sm shadow-orange-100 uppercase">
                                                            Ambil Paket
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </template>

                                    <div x-show="(activeTab === 'products' && filteredProducts.length === 0) || (activeTab === 'packages' && filteredPackages.length === 0)"
                                        class="col-span-full flex flex-col items-center justify-center py-20 bg-gray-50 rounded-3xl border-2 border-dashed border-gray-200">
                                        <div
                                            class="w-20 h-20 bg-orange-50 rounded-full flex items-center justify-center mb-4">
                                            <i class="bi bi-search text-3xl text-orange-300"></i>
                                        </div>
                                        <p class="text-gray-500 font-bold">Item tidak ditemukan</p>
                                        <p class="text-gray-400 text-sm">Coba kata kunci lain atau kategori berbeda</p>
                                    </div>
                                </div>
                            </div>

                            {{-- RIGHT COLUMN: Cart and Totals --}}
                            <div class="lg:col-span-1">
                                <div
                                    class="sticky top-6 border border-gray-200 rounded-2xl bg-white shadow-sm overflow-hidden">
                                    <div class="p-5 border-b border-gray-100 bg-gray-50/50">
                                        <h3 class="text-lg font-bold text-gray-900 flex items-center">
                                            <i class="bi bi-basket-fill text-orange-500 mr-2 text-xl"></i>
                                            Keranjang Belanja
                                            <template x-if="cart.items.length > 0">
                                                <span
                                                    class="ml-2 px-2 py-0.5 text-xs bg-orange-100 text-orange-600 rounded-full"
                                                    x-text="cart.items.length"></span>
                                            </template>
                                        </h3>
                                    </div>

                                    <div class="p-5">
                                        <div
                                            class="bg-gray-50 rounded-xl mb-6 h-[300px] overflow-y-auto border border-gray-100 p-2 custom-scrollbar">
                                            <template x-if="cart.items.length === 0">
                                                <div
                                                    class="flex flex-col items-center justify-center h-full text-gray-400">
                                                    <i class="bi bi-cart2 text-4xl mb-2"></i>
                                                    <p class="text-sm">Keranjang masih kosong</p>
                                                </div>
                                            </template>

                                            <template x-for="(item, index) in cart.items" :key="index">
                                                <div
                                                    class="bg-white rounded-lg p-3 mb-2 border border-gray-100 shadow-sm transition-all hover:border-orange-200">
                                                    <input type="hidden" :name="'items[' + index + '][item_type]'"
                                                        :value="item.item_type">
                                                    <input type="hidden" :name="'items[' + index + '][item_id]'"
                                                        :value="item.item_id">
                                                    <input type="hidden" :name="'items[' + index + '][item_name]'"
                                                        :value="item.item_name">
                                                    <input type="hidden" :name="'items[' + index + '][unit_price]'"
                                                        :value="item.unit_price">
                                                    <input type="hidden" :name="'items[' + index + '][subtotal]'"
                                                        :value="item.subtotal">

                                                    <div class="flex justify-between items-start mb-2">
                                                        <div class="flex-1 min-w-0 pr-2">
                                                            <p class="font-bold text-sm text-gray-800 truncate"
                                                                x-text="item.item_name"></p>
                                                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider"
                                                                x-text="item.item_type"></p>
                                                        </div>
                                                        <button type="button" @click="removeFromCart(index)"
                                                            class="text-gray-300 hover:text-red-500 transition-colors">
                                                            <i class="bi bi-x-circle-fill"></i>
                                                        </button>
                                                    </div>

                                                    <div class="flex items-center justify-between mt-3">
                                                        <p class="text-sm text-orange-600 font-bold"
                                                            x-text="'Rp ' + (item.unit_price * item.quantity).toLocaleString('id-ID')">
                                                        </p>
                                                        <div class="flex items-center gap-2">
                                                            <span class="text-[10px] text-gray-400">Qty:</span>
                                                            <input type="number" min="1"
                                                                :name="'items[' + index + '][quantity]'"
                                                                x-model.number="item.quantity"
                                                                @input="updateItemQuantity(index)"
                                                                class="w-12 px-1 py-1 border border-gray-200 rounded-md text-center text-xs font-bold focus:ring-1 focus:ring-orange-500 focus:border-orange-500 outline-none" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                            @error('items')
                                                <p class="mt-2 text-xs text-red-600 font-medium px-2">
                                                    <i class="bi bi-exclamation-circle mr-1"></i> Keranjang tidak boleh kosong.
                                                </p>
                                            @enderror
                                        </div>

                                        <div
                                            class="space-y-4 mb-6 bg-gray-50/50 p-4 rounded-xl border border-dashed border-gray-200">
                                            <div>
                                                <label for="customer_name"
                                                    class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Nama
                                                    Pelanggan</label>
                                                <input type="text" name="customer_name" id="customer_name"
                                                    x-model="customerName"
                                                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all outline-none"
                                                    placeholder="Nama pelanggan (opsional)">
                                            </div>

                                            <div>
                                                <label for="phone"
                                                    class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Telepon</label>
                                                <div
                                                    class="flex rounded-xl border border-gray-200 focus-within:ring-2 focus-within:ring-orange-500/20 focus-within:border-orange-500 transition-all bg-white overflow-hidden">
                                                    <span
                                                        class="inline-flex items-center px-3 text-gray-400 bg-gray-50 border-r border-gray-100 text-xs font-bold">+62</span>
                                                    <input type="text" name="customer_phone" id="phone"
                                                        x-model="customerPhone"
                                                        class="flex-1 px-3 py-2.5 border-0 text-sm focus:ring-0 outline-none"
                                                        placeholder="813xxxxxxxx" @input="formatPhoneNumber"
                                                        maxlength="15">
                                                </div>
                                                @error('customer_phone')
                                                    <p class="mt-1 text-[11px] text-red-600 font-medium">{{ $message }}
                                                    </p>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-2 gap-3 mb-4">
                                            <div class="col-span-1">
                                                <label
                                                    class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Tipe
                                                    Diskon</label>
                                                <select name="discount_type" x-model="cart.discount_type"
                                                    @change="recalculateTotals"
                                                    class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:ring-orange-500 focus:border-orange-500 outline-none">
                                                    <option value="none">None</option>
                                                    <option value="percentage">%</option>
                                                    <option value="nominal">Rp</option>
                                                </select>
                                            </div>
                                            <div class="col-span-1">
                                                <label
                                                    class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Nilai
                                                    Diskon</label>
                                                <input type="number" name="discount_value"
                                                    x-model.number="cart.discount_value" min="0" step="0.01"
                                                    @input="recalculateTotals"
                                                    class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:ring-orange-500 focus:border-orange-500 outline-none">
                                            </div>
                                        </div>

                                        <div class="mb-6">
                                            <label
                                                class="block text-xs font-bold text-orange-500 uppercase tracking-wider mb-2 text-red-500">Metode
                                                Pembayaran *</label>
                                            <select name="payment_method" x-model="cart.payment_method"
                                                @change="recalculateTotals"
                                                class="w-full px-3 py-2 border border-orange-200 rounded-xl text-sm font-bold bg-orange-50 text-orange-700 focus:ring-orange-500 focus:border-orange-500 outline-none"
                                                required>
                                                <option value="cash">CASH (TUNAI)</option>
                                                <option value="qris">QRIS</option>
                                            </select>
                                        </div>

                                        <div
                                            class="rounded-2xl p-5 text-white shadow-xl shadow-orange-900/10 space-y-3">
                                            <div class="flex justify-between text-xs text-orange-400">
                                                <span>Subtotal</span>
                                                <span x-text="'Rp ' + cart.subtotal_amount.toLocaleString('id-ID')"></span>
                                                <input type="hidden" name="subtotal_amount"
                                                    :value="cart.subtotal_amount">
                                            </div>
                                            <div class="flex justify-between text-xs text-red-400">
                                                <span>Diskon</span>
                                                <span
                                                    x-text="'- Rp ' + cart.discount_amount.toLocaleString('id-ID')"></span>
                                                <input type="hidden" name="discount_amount"
                                                    :value="cart.discount_amount">
                                            </div>
                                            <div class="flex justify-between items-center pt-2 border-t border-orange-800">
                                                <span class="text-orange-400 text-sm font-medium">Total Akhir</span>
                                                <span class="text-xl font-black text-orange-400"
                                                    x-text="'Rp ' + cart.final_amount.toLocaleString('id-ID')"></span>
                                                <input type="hidden" name="final_amount" :value="cart.final_amount">
                                            </div>

                                            <div class="pt-4 space-y-3" x-show="cart.payment_method === 'cash'"
                                                x-transition>
                                                <div class="relative">
                                                    <label
                                                        class="block text-[10px] font-bold text-orange-500 uppercase mb-1">Nominal
                                                        Bayar</label>
                                                    <div class="flex items-center">
                                                        <span
                                                            class="absolute left-3 text-orange-400 font-bold text-sm">Rp</span>
                                                        <input type="number" name="paid_amount"
                                                            x-model.number="cart.paid_amount" min="0"
                                                            @input="recalculateTotals"
                                                            :readonly="cart.payment_method !== 'cash'"
                                                            class="w-full pl-10 pr-4 py-2.5 bg-orange-800 border-0 rounded-xl text-lg font-bold text-white focus:ring-2 focus:ring-orange-500 outline-none"
                                                            placeholder="0">
                                                    </div>
                                                </div>
                                                <div class="flex justify-between items-center bg-white/5 p-3 rounded-xl">
                                                    <span class="text-xs font-medium text-orange-400">Kembalian</span>
                                                    <span class="text-lg font-bold text-green-400"
                                                        x-text="'Rp ' + cart.change_amount.toLocaleString('id-ID')"></span>
                                                    <input type="hidden" name="change_amount"
                                                        :value="cart.change_amount">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-6 space-y-3">
                                            <button type="submit"
                                                :disabled="cart.items.length === 0 || (cart.payment_method === 'cash' && cart
                                                    .paid_amount < cart.final_amount)"
                                                class="w-full inline-flex items-center justify-center px-8 py-4 bg-gradient-to-br from-orange-500 to-red-600 rounded-xl text-white font-bold text-sm shadow-lg shadow-orange-500/30 hover:shadow-orange-500/40 transform active:scale-[0.98] transition-all disabled:opacity-30 disabled:orangescale disabled:cursor-not-allowed uppercase tracking-widest">
                                                <i class="bi bi-shield-check mr-2 text-lg"></i> Simpan Penjualan
                                            </button>

                                            <a href="{{ route('sales.index') }}"
                                                class="w-full inline-flex items-center justify-center px-6 py-3 border border-orange-200 rounded-xl text-xs font-bold text-gray-400 hover:bg-gray-50 hover:text-gray-600 transition-all uppercase tracking-widest">
                                                Batal
                                            </a>
                                        </div>
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
            categories: @js($categories),
            filterCategory: 'all',
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
                return this.rawProducts.filter(item => {
                    // Filter berdasarkan Nama
                    const matchesSearch = item.name.toLowerCase().includes(this
                        .searchTerm.toLowerCase());

                    // Filter berdasarkan Kategori (Pastikan data item memiliki property 'category')
                    const matchesCategory = this.filterCategory === 'all' || item
                        .category === this.filterCategory;

                    return matchesSearch && matchesCategory;
                });
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
                    // console.log(data);

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

                // The form is ready, submit it
                document.getElementById('saleForm').submit();
            }
        }));
    });
</script>
