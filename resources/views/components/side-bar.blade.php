@props(['mobile' => false])

<div class="h-full flex flex-col">
    {{-- Sidebar Header --}}
    <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-orange-50 to-red-50">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <img src="{{ asset('img/Logo.png') }}" alt="Lapar Chicken" class="w-8 h-8 rounded-lg shadow-md">
                <div class="ml-3">
                    <h2 class="font-bold text-gray-800">Navigation</h2>
                    <p class="text-xs text-gray-500">Main Menu</p>
                </div>
            </div>
            @if ($mobile)
                <button @click="sidebarOpen = false"
                    class="p-2 rounded-lg hover:bg-white/50 transition-colors duration-200">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            @endif
        </div>
    </div>

    {{-- Navigation Content --}}
    <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
        {{-- Dashboard --}}
        <a href="{{ route('dashboard') }}"
            class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-orange-50 hover:text-orange-600 transition-all duration-200 group {{ request()->routeIs('dashboard') ? 'bg-orange-50 text-orange-600 border-l-4 border-orange-500' : 'text-gray-700' }}">
            <div
                class="w-8 h-8 flex items-center justify-center rounded-lg {{ request()->routeIs('dashboard') ? 'bg-orange-100' : 'bg-gray-100 group-hover:bg-orange-100' }} transition-colors duration-200">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z" />
                </svg>
            </div>
            <span class="font-medium">Dashboard</span>
        </a>

        @if (auth()->check() && auth()->user() && (auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Manager')))
            {{-- DATA MASTER Dropdown --}}
            <div x-data="{ open: {{ request()->routeIs(
                'users.*',
                'branches.*',
                'roles.*',
                'suppliers.*',
                'units.*',
                'categories.*',
                'raw-materials.*',
                'semi-finished-products.*',
                'finished-products.*',
                'sales-packages.*',
            )
                ? 'true'
                : 'false' }} }" class="rounded-xl">
                <button @click="open = !open"
                    class="w-full flex items-center justify-between px-4 py-3 rounded-xl hover:bg-orange-50 hover:text-orange-600 transition-all duration-200 group {{ request()->routeIs('users.*') ||
                    request()->routeIs('branches.*') ||
                    request()->routeIs('roles.*') ||
                    request()->routeIs('suppliers.*') ||
                    request()->routeIs('units.*') ||
                    request()->routeIs('categories.*') ||
                    request()->routeIs('raw-materials.*') ||
                    request()->routeIs('semi-finished-products.*') ||
                    request()->routeIs('finished-products.*') ||
                    request()->routeIs('sales-packages.*')
                        ? 'bg-orange-50 text-orange-600 border-l-4 border-orange-500'
                        : 'text-gray-700' }}">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-8 h-8 flex items-center justify-center rounded-lg {{ request()->routeIs('users.*') ||
                            request()->routeIs('branches.*') ||
                            request()->routeIs('roles.*') ||
                            request()->routeIs('suppliers.*') ||
                            request()->routeIs('units.*') ||
                            request()->routeIs('categories.*') ||
                            request()->routeIs('raw-materials.*') ||
                            request()->routeIs('semi-finished-products.*') ||
                            request()->routeIs('finished-products.*') ||
                            request()->routeIs('sales-packages.*')
                                ? 'bg-orange-100'
                                : 'bg-gray-100 group-hover:bg-orange-100' }} transition-colors duration-200">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm16-4H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-1 9H9V9h10v2zm-4 4H9v-2h6v2zm4-8H9V5h10v2z" />
                            </svg>
                        </div>
                        <span class="font-medium">Master</span>
                    </div>
                    <svg class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''"
                        fill="currentColor" viewBox="0 0 24 24">
                        <path d="M7 10l5 5 5-5z" />
                    </svg>
                </button>

                <div x-show="open" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 -translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-2" x-cloak class="mt-2 ml-11 space-y-1">
                    <a href="{{ route('users.index') }}"
                        class="block px-4 py-2 rounded-lg hover:bg-orange-50 hover:text-orange-600 text-gray-600 text-sm font-medium transition-colors duration-200 {{ request()->routeIs('users.*') ? 'bg-orange-50 text-orange-600' : '' }}">
                        Manajemen User
                    </a>
                    <a href="{{ route('branches.index') }}"
                        class="block px-4 py-2 rounded-lg hover:bg-orange-50 hover:text-orange-600 text-gray-600 text-sm font-medium transition-colors duration-200 {{ request()->routeIs('branches.*') ? 'bg-orange-50 text-orange-600' : '' }}">
                        Cabang Toko
                    </a>
                    <a href="{{ route('roles.index') }}"
                        class="block px-4 py-2 rounded-lg hover:bg-orange-50 hover:text-orange-600 text-gray-600 text-sm font-medium transition-colors duration-200 {{ request()->routeIs('roles.*') ? 'bg-orange-50 text-orange-600' : '' }}">
                        Role & Hak Akses
                    </a>
                    <a href="{{ route('suppliers.index') }}"
                        class="block px-4 py-2 rounded-lg hover:bg-orange-50 hover:text-orange-600 text-gray-600 text-sm font-medium transition-colors duration-200 {{ request()->routeIs('suppliers.*') ? 'bg-orange-50 text-orange-600' : '' }}">
                        Data Supplier
                    </a>
                    <a href="{{ route('units.index') }}"
                        class="block px-4 py-2 rounded-lg hover:bg-orange-50 hover:text-orange-600 text-gray-600 text-sm font-medium transition-colors duration-200 {{ request()->routeIs('units.*') ? 'bg-orange-50 text-orange-600' : '' }}">
                        Satuan Produk
                    </a>
                    <a href="{{ route('categories.index') }}"
                        class="block px-4 py-2 rounded-lg hover:bg-orange-50 hover:text-orange-600 text-gray-600 text-sm font-medium transition-colors duration-200 {{ request()->routeIs('categories.*') ? 'bg-orange-50 text-orange-600' : '' }}">
                        Kategori Produk
                    </a>
                    <a href="{{ route('raw-materials.index') }}"
                        class="block px-4 py-2 rounded-lg hover:bg-orange-50 hover:text-orange-600 text-gray-600 text-sm font-medium transition-colors duration-200 {{ request()->routeIs('raw-materials.*') ? 'bg-orange-50 text-orange-600' : '' }}">
                        Master Bahan Mentah
                    </a>
                    <a href="{{ route('semi-finished-products.index') }}"
                        class="block px-4 py-2 rounded-lg hover:bg-orange-50 hover:text-orange-600 text-gray-600 text-sm font-medium transition-colors duration-200 {{ request()->routeIs('semi-finished-products.*') ? 'bg-orange-50 text-orange-600' : '' }}">
                        Master Bahan Setengah Jadi
                    </a>
                    <a href="{{ route('finished-products.index') }}"
                        class="block px-4 py-2 rounded-lg hover:bg-orange-50 hover:text-orange-600 text-gray-600 text-sm font-medium transition-colors duration-200 {{ request()->routeIs('finished-products.*') ? 'bg-orange-50 text-orange-600' : '' }}">
                        Master Produk Siap Jual
                    </a>
                    @if (auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Manager'))
                        <a href="{{ route('sales-packages.index') }}"
                            class="block px-4 py-2 rounded-lg hover:bg-orange-50 hover:text-orange-600 text-gray-600 text-sm font-medium transition-colors duration-200 {{ request()->routeIs('sales-packages.*') ? 'bg-orange-50 text-orange-600' : '' }}">
                            Paket Penjualan
                        </a>
                    @endif
                </div>
            </div>
        @endif

        {{-- PEMBELIAN BAHAN MENTAH Dropdown --}}
        <div x-data="{ open: {{ request()->routeIs('purchase-orders.*', 'purchase-receipts.*') ? 'true' : 'false' }} }" class="rounded-xl">
            <button @click="open = !open"
                class="w-full flex items-center justify-between px-4 py-3 rounded-xl hover:bg-orange-50 hover:text-orange-600 transition-all duration-200 group {{ request()->routeIs('purchase-orders.*') || request()->routeIs('purchase-receipts.*')
                    ? 'bg-orange-50 text-orange-600 border-l-4 border-orange-500'
                    : 'text-gray-700' }}">
                <div class="flex items-center gap-3">
                    <div
                        class="w-8 h-8 flex items-center justify-center rounded-lg {{ request()->routeIs('purchase-orders.*') || request()->routeIs('purchase-receipts.*')
                            ? 'bg-orange-100'
                            : 'bg-gray-100 group-hover:bg-orange-100' }} transition-colors duration-200">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M7 4V2a1 1 0 0 1 2 0v2h6V2a1 1 0 0 1 2 0v2h1a3 3 0 0 1 3 3v12a3 3 0 0 1-3 3H6a3 3 0 0 1-3-3V7a3 3 0 0 1 3-3h1z" />
                        </svg>
                    </div>
                    <span class="font-medium">Bahan Mentah</span>
                </div>
                <svg class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''"
                    fill="currentColor" viewBox="0 0 24 24">
                    <path d="M7 10l5 5 5-5z" />
                </svg>
            </button>

            <div x-show="open" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2"
                x-cloak class="mt-2 ml-11 space-y-1">
                <a href="{{ route('purchase-orders.index') }}"
                    class="block px-4 py-2 rounded-lg hover:bg-orange-50 hover:text-orange-600 text-gray-600 text-sm font-medium transition-colors duration-200 {{ request()->routeIs('purchase-orders.index') ? 'bg-orange-50 text-orange-600' : '' }}">
                    Buat Purchase Order
                </a>
                <a href="{{ route('purchase-receipts.index') }}"
                    class="block px-4 py-2 rounded-lg hover:bg-orange-50 hover:text-orange-600 text-gray-600 text-sm font-medium transition-colors duration-200 {{ request()->routeIs('purchase-receipts.index') ? 'bg-orange-50 text-orange-600' : '' }}">
                    Terima Purchase Receipt
                </a>
            </div>
        </div>

        {{-- STOK Dropdown --}}
        <div x-data="{ open: {{ request()->routeIs('raw-materials.stock', 'semi-finished-stock.*', 'finished-products-stock.*', 'stock-opnames.*') ? 'true' : 'false' }} }" class="rounded-xl">
            <button @click="open = !open"
                class="w-full flex items-center justify-between px-4 py-3 rounded-xl hover:bg-orange-50 hover:text-orange-600 transition-all duration-200 group {{ request()->routeIs('raw-materials.stock') || request()->routeIs('semi-finished-stock.*') || request()->routeIs('finished-products-stock.*') || request()->routeIs('stock-opnames.*')
                    ? 'bg-orange-50 text-orange-600 border-l-4 border-orange-500'
                    : 'text-gray-700' }}">
                <div class="flex items-center gap-3">
                    <div
                        class="w-8 h-8 flex items-center justify-center rounded-lg {{ request()->routeIs('raw-materials.stock') || request()->routeIs('semi-finished-stock.*') || request()->routeIs('finished-products-stock.*') || request()->routeIs('stock-opnames.*')
                            ? 'bg-orange-100'
                            : 'bg-gray-100 group-hover:bg-orange-100' }} transition-colors duration-200">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M19 7h-3V6a4 4 0 0 0-8 0v1H5a1 1 0 0 0-1 1v11a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3V8a1 1 0 0 0-1-1z" />
                        </svg>
                    </div>
                    <span class="font-medium">Stok</span>
                </div>
                <svg class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''"
                    fill="currentColor" viewBox="0 0 24 24">
                    <path d="M7 10l5 5 5-5z" />
                </svg>
            </button>

            <div x-show="open" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2"
                x-cloak class="mt-2 ml-11 space-y-1">
                <a href="{{ route('raw-materials.stock') }}"
                    class="block px-4 py-2 rounded-lg hover:bg-orange-50 hover:text-orange-600 text-gray-600 text-sm font-medium transition-colors duration-200 {{ request()->routeIs('raw-materials.stock') ? 'bg-orange-50 text-orange-600' : '' }}">
                    Bahan Mentah
                </a>
                <a href="{{ route('semi-finished-stock.index') }}"
                    class="block px-4 py-2 rounded-lg hover:bg-orange-50 hover:text-orange-600 text-gray-600 text-sm font-medium transition-colors duration-200 {{ request()->routeIs('semi-finished-stock.*') ? 'bg-orange-50 text-orange-600' : '' }}">
                    Bahan Setengah Jadi
                </a>
                <a href="{{ route('finished-products-stock.index') }}"
                    class="block px-4 py-2 rounded-lg hover:bg-orange-50 hover:text-orange-600 text-gray-600 text-sm font-medium transition-colors duration-200 {{ request()->routeIs('finished-products-stock.*') ? 'bg-orange-50 text-orange-600' : '' }}">
                    Produk Siap Jual
                </a>
                <a href="{{ route('stock-opnames.index') }}"
                    class="block px-4 py-2 rounded-lg hover:bg-orange-50 hover:text-orange-600 text-gray-600 text-sm font-medium transition-colors duration-200 {{ request()->routeIs('stock-opnames.*') ? 'bg-orange-50 text-orange-600' : '' }}">
                    Opname
                </a>
            </div>
        </div>

        {{-- PUSAT PRODUKSI Dropdown --}}
        <div x-data="{ open: {{ request()->routeIs('production-requests.*', 'production-approvals.*', 'production-processes.*') ? 'true' : 'false' }} }" class="rounded-xl">
            <button @click="open = !open"
                class="w-full flex items-center justify-between px-4 py-3 rounded-xl hover:bg-orange-50 hover:text-orange-600 transition-all duration-200 group {{ request()->routeIs('production-requests.*') || request()->routeIs('production-approvals.*') || request()->routeIs('production-processes.*')
                    ? 'bg-orange-50 text-orange-600 border-l-4 border-orange-500'
                    : 'text-gray-700' }}">
                <div class="flex items-center gap-3">
                    <div
                        class="w-8 h-8 flex items-center justify-center rounded-lg {{ request()->routeIs('production-requests.*') || request()->routeIs('production-approvals.*') || request()->routeIs('production-processes.*')
                            ? 'bg-orange-100'
                            : 'bg-gray-100 group-hover:bg-orange-100' }} transition-colors duration-200">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                        </svg>
                    </div>
                    <span class="font-medium">Pusat Produksi</span>
                </div>
                <svg class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''"
                    fill="currentColor" viewBox="0 0 24 24">
                    <path d="M7 10l5 5 5-5z" />
                </svg>
            </button>

            <div x-show="open" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2"
                x-cloak class="mt-2 ml-11 space-y-1">
                <a href="{{ route('production-requests.index') }}"
                    class="block px-4 py-2 rounded-lg hover:bg-orange-50 hover:text-orange-600 text-gray-600 text-sm font-medium transition-colors duration-200 {{ request()->routeIs('production-requests.*') ? 'bg-orange-50 text-orange-600' : '' }}">
                    Pengajuan
                </a>
                <a href="{{ route('production-approvals.index') }}"
                    class="block px-4 py-2 rounded-lg hover:bg-orange-50 hover:text-orange-600 text-gray-600 text-sm font-medium transition-colors duration-200 {{ request()->routeIs('production-approvals.*') ? 'bg-orange-50 text-orange-600' : '' }}">
                    Persetujuan
                </a>
                <a href="{{ route('production-processes.index') }}"
                    class="block px-4 py-2 rounded-lg hover:bg-orange-50 hover:text-orange-600 text-gray-600 text-sm font-medium transition-colors duration-200 {{ request()->routeIs('production-processes.*') ? 'bg-orange-50 text-orange-600' : '' }}">
                    Proses
                </a>
            </div>
        </div>

        {{-- DISTRIBUSI Dropdown --}}
        <div x-data="{ open: {{ request()->routeIs('semi-finished-distributions.*') ? 'true' : 'false' }} }" class="rounded-xl">
            <button @click="open = !open"
                class="w-full flex items-center justify-between px-4 py-3 rounded-xl hover:bg-orange-50 hover:text-orange-600 transition-all duration-200 group {{ request()->routeIs('semi-finished-distributions.*')
                    ? 'bg-orange-50 text-orange-600 border-l-4 border-orange-500'
                    : 'text-gray-700' }}">
                <div class="flex items-center gap-3">
                    <div
                        class="w-8 h-8 flex items-center justify-center rounded-lg {{ request()->routeIs('semi-finished-distributions.*')
                            ? 'bg-orange-100'
                            : 'bg-gray-100 group-hover:bg-orange-100' }} transition-colors duration-200">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M3 4a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 14.846 4.632 17 6.414 17H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 6H6.28l-.31-1.243A1 1 0 005 4H3z" />
                        </svg>
                    </div>
                    <span class="font-medium">Distribusi</span>
                </div>
                <svg class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''"
                    fill="currentColor" viewBox="0 0 24 24">
                    <path d="M7 10l5 5 5-5z" />
                </svg>
            </button>

            <div x-show="open" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2"
                x-cloak class="mt-2 ml-11 space-y-1">
                <a href="{{ route('semi-finished-distributions.index') }}"
                    class="block px-4 py-2 rounded-lg hover:bg-orange-50 hover:text-orange-600 text-gray-600 text-sm font-medium transition-colors duration-200 {{ request()->routeIs('semi-finished-distributions.index') ? 'bg-orange-50 text-orange-600' : '' }}">
                    Pengiriman ke Cabang
                </a>
                <a href="{{ route('semi-finished-distributions.inbox', ['branch_id' => $selectedBranch?->id]) }}"
                    class="block px-4 py-2 rounded-lg hover:bg-orange-50 hover:text-orange-600 text-gray-600 text-sm font-medium transition-colors duration-200 {{ request()->routeIs('semi-finished-distributions.inbox') ? 'bg-orange-50 text-orange-600' : '' }}">
                    Kotak Masuk
                </a>
            </div>
        </div>

        @if (auth()->check() &&
            auth()->user() &&
            (auth()->user()->hasRole('Super Admin') ||
                auth()->user()->hasRole('Manager') ||
                auth()->user()->hasRole('Kepala Toko') ||
                auth()->user()->hasRole('Kru Toko')))
            {{-- OPERASIONAL CABANG Dropdown --}}
            <div x-data="{ open: {{ request()->routeIs('semi-finished-usage-requests.*', 'semi-finished-usage-approvals.*', 'semi-finished-usage-processes.*', 'sales.*', 'destruction-reports.*', 'stock-transfer.*') ? 'true' : 'false' }} }" class="rounded-xl">
            <button @click="open = !open"
                class="w-full flex items-center justify-between px-4 py-3 rounded-xl hover:bg-orange-50 hover:text-orange-600 transition-all duration-200 group {{ request()->routeIs('semi-finished-usage-requests.*') || request()->routeIs('semi-finished-usage-approvals.*') || request()->routeIs('semi-finished-usage-processes.*') || request()->routeIs('sales.*') || request()->routeIs('destruction-reports.*') || request()->routeIs('stock-transfer.*')
                ? 'bg-orange-50 text-orange-600 border-l-4 border-orange-500'
                : 'text-gray-700' }}">
                <div class="flex items-center gap-3">
                <div
                    class="w-8 h-8 flex items-center justify-center rounded-lg {{ request()->routeIs('semi-finished-usage-requests.*') || request()->routeIs('semi-finished-usage-approvals.*') || request()->routeIs('semi-finished-usage-processes.*') || request()->routeIs('sales.*') || request()->routeIs('destruction-reports.*') || request()->routeIs('stock-transfer.*')
                    ? 'bg-orange-100'
                    : 'bg-gray-100 group-hover:bg-orange-100' }} transition-colors duration-200">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                    <path
                        d="M19 7h-3V6a4 4 0 0 0-8 0v1H5a1 1 0 0 0-1 1v11a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3V8a1 1 0 0 0-1-1zM10 6a2 2 0 0 1 4 0v1h-4V6zm8 13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V9h2v1a1 1 0 0 0 2 0V9h4v1a1 1 0 0 0 2 0V9h2v10z" />
                    </svg>
                </div>
                <span class="font-medium">Operasional Cabang</span>
                </div>
                <svg class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''"
                fill="currentColor" viewBox="0 0 24 24">
                <path d="M7 10l5 5 5-5z" />
                </svg>
            </button>

            <div x-show="open" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 -translate-y-2" x-cloak class="mt-2 ml-11 space-y-1">

                @if (auth()->user()->hasRole('Super Admin') ||
                    auth()->user()->hasRole('Manager') ||
                    auth()->user()->hasRole('Kepala Toko') ||
                    auth()->user()->hasRole('Kru Toko'))
                <a href="{{ route('semi-finished-usage-requests.index') }}"
                    class="block px-4 py-2 rounded-lg hover:bg-orange-50 hover:text-orange-600 text-gray-600 text-sm font-medium transition-colors duration-200 {{ request()->routeIs('semi-finished-usage-requests.*') ? 'bg-orange-50 text-orange-600' : '' }}">
                    Ajukan Penggunaan Bahan
                </a>
                @endif

                @if (auth()->user()->hasRole('Super Admin') ||
                    auth()->user()->hasRole('Manager') ||
                    auth()->user()->hasRole('Kepala Toko'))
                <a href="{{ route('semi-finished-usage-approvals.index') }}"
                    class="block px-4 py-2 rounded-lg hover:bg-orange-50 hover:text-orange-600 text-gray-600 text-sm font-medium transition-colors duration-200 {{ request()->routeIs('semi-finished-usage-approvals.*') ? 'bg-orange-50 text-orange-600' : '' }}">
                    Setujui Penggunaan Bahan
                </a>
                @endif

                @if (auth()->user()->hasRole('Super Admin') ||
                    auth()->user()->hasRole('Manager') ||
                    auth()->user()->hasRole('Kepala Toko') ||
                    auth()->user()->hasRole('Kru Toko'))
                <a href="{{ route('semi-finished-usage-processes.index', ['branch_id' => $selectedBranch?->id]) }}"
                    class="block px-4 py-2 rounded-lg hover:bg-orange-50 hover:text-orange-600 text-gray-600 text-sm font-medium transition-colors duration-200 {{ request()->routeIs('semi-finished-usage-processes.*') ? 'bg-orange-50 text-orange-600' : '' }}">
                    Proses Bahan Setengah Jadi
                </a>
                @endif

                @php
                $currentUserBranch = auth()->check() ? auth()->user()->branch : null;
                $isProductionCenter = $currentUserBranch && $currentUserBranch->type === 'production';
                $isSuperAdmin = auth()->check() && auth()->user()->is_superadmin;
                @endphp

                @if (!$isProductionCenter || $isSuperAdmin)
                @if (auth()->user()->hasRole('Super Admin') ||
                    auth()->user()->hasRole('Manager') ||
                    auth()->user()->hasRole('Kepala Toko') ||
                    auth()->user()->hasRole('Kru Toko'))
                    <a href="{{ route('sales.create') }}"
                    class="block px-4 py-2 rounded-lg hover:bg-orange-50 hover:text-orange-600 text-gray-600 text-sm font-medium transition-colors duration-200 {{ request()->routeIs('sales.*') ? 'bg-orange-50 text-orange-600' : '' }}">
                    Penjualan
                    </a>
                @endif

                @if (auth()->user()->hasRole('Super Admin') ||
                    auth()->user()->hasRole('Manager') ||
                    auth()->user()->hasRole('Kepala Toko'))
                    <a href="{{ route('destruction-reports.index') }}"
                    class="block px-4 py-2 rounded-lg hover:bg-orange-50 hover:text-orange-600 text-gray-600 text-sm font-medium transition-colors duration-200 {{ request()->routeIs('destruction-reports.*') ? 'bg-orange-50 text-orange-600' : '' }}">
                    Laporan Pemusnahan
                    </a>
                @endif
                @endif

                @if (auth()->user()->hasRole('Super Admin') ||
                    auth()->user()->hasRole('Manager') ||
                    auth()->user()->hasRole('Kepala Toko'))
                <a href="{{ route('stock-transfer.index') }}"
                    class="block px-4 py-2 rounded-lg hover:bg-orange-50 hover:text-orange-600 text-gray-600 text-sm font-medium transition-colors duration-200 {{ request()->routeIs('stock-transfer.*') ? 'bg-orange-50 text-orange-600' : '' }}">
                    Transfer Antar Cabang
                </a>
                @endif
            </div>
            </div>
        @endif

        {{-- DISTRIBUSI Dropdown --}}
        <div x-data="{ open: {{ request()->routeIs('semi-finished-distributions.*') ? 'true' : 'false' }} }" class="rounded-xl">
            <button @click="open = !open"
                class="w-full flex items-center justify-between px-4 py-3 rounded-xl hover:bg-orange-50 hover:text-orange-600 transition-all duration-200 group {{ request()->routeIs('semi-finished-distributions.*')
                    ? 'bg-orange-50 text-orange-600 border-l-4 border-orange-500'
                    : 'text-gray-700' }}">
                <div class="flex items-center gap-3">
                    <div
                        class="w-8 h-8 flex items-center justify-center rounded-lg {{ request()->routeIs('semi-finished-distributions.*')
                            ? 'bg-orange-100'
                            : 'bg-gray-100 group-hover:bg-orange-100' }} transition-colors duration-200">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zM13 3.5L18.5 9H13V3.5zM8 12h2v5H8v-5zm4-3h2v8h-2V9zm4 2h2v6h-2v-6z" />
                        </svg>
                    </div>
                    <span class="font-medium">Laporan</span>
                </div>
                <svg class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''"
                    fill="currentColor" viewBox="0 0 24 24">
                    <path d="M7 10l5 5 5-5z" />
                </svg>
            </button>

            <div x-show="open" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2"
                x-cloak class="mt-2 ml-11 space-y-1">
                <a href="{{ route('semi-finished-distributions.index') }}"
                    class="block px-4 py-2 rounded-lg hover:bg-orange-50 hover:text-orange-600 text-gray-600 text-sm font-medium transition-colors duration-200 {{ request()->routeIs('semi-finished-distributions.index') ? 'bg-orange-50 text-orange-600' : '' }}">
                    Pengiriman ke Cabang
                </a>
                <a href="{{ route('semi-finished-distributions.inbox', ['branch_id' => $selectedBranch?->id]) }}"
                    class="block px-4 py-2 rounded-lg hover:bg-orange-50 hover:text-orange-600 text-gray-600 text-sm font-medium transition-colors duration-200 {{ request()->routeIs('semi-finished-distributions.inbox') ? 'bg-orange-50 text-orange-600' : '' }}">
                    Kotak Masuk
                </a>
            </div>
        </div>
    </nav>

    {{-- Sidebar Footer --}}
    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
        <div class="text-xs text-gray-500 text-center">
            <div class="font-semibold">Lapar Chicken</div>
            <div>v1.0.0</div>
        </div>
    </div>
</div>
