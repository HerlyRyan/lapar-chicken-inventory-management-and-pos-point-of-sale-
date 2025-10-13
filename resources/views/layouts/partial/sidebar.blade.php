<!-- Sidebar Overlay (Mobile) -->
<div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden hidden"></div>

<!-- Sidebar -->
<aside id="sidebar" class="fixed top-16 left-0 h-full w-80 lg:w-64 bg-orange-800 text-white z-50 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out overflow-y-auto">
    <div class="p-3 lg:p-4">
        <nav class="space-y-1 lg:space-y-2">
            <!-- Dashboard -->
            <div class="nav-item">
                <a href="{{ route('dashboard') }}" 
                   class="flex items-center px-3 py-3 lg:py-2 rounded-md hover:bg-orange-700 transition-colors {{ request()->routeIs('dashboard') ? 'bg-orange-700' : '' }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                    </svg>
                    <span class="truncate">Dashboard</span>
                </a>
            </div>

            @if (auth()->check() && auth()->user() && (auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Manager')))
                <!-- DATA MASTER Section -->
                <div class="nav-item">
                    <button class="w-full flex items-center justify-between px-3 py-3 lg:py-2 text-left text-white font-bold hover:bg-orange-700 rounded-md transition-colors"
                            onclick="toggleCollapse('masterCollapse')">
                        <div class="flex items-center min-w-0">
                            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm16-4H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-1 9H9V9h10v2zm-4 4H9v-2h6v2zm4-8H9V5h10v2z"/>
                            </svg>
                            <span class="truncate text-sm lg:text-base">DATA MASTER</span>
                        </div>
                        <svg class="w-4 h-4 transform transition-transform duration-200 flex-shrink-0 ml-2" id="masterCollapseIcon" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M7 10l5 5 5-5z"/>
                        </svg>
                    </button>
                    
                    <div class="hidden mt-2 ml-6 lg:ml-8 space-y-1" id="masterCollapse">
                        <a href="{{ route('users.index') }}" 
                           class="flex items-start px-3 py-3 lg:py-2 rounded-md hover:bg-orange-700 transition-colors text-sm">
                            <svg class="w-4 h-4 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <div class="min-w-0">
                                <div class="truncate">Manajemen User</div>
                                <div class="text-orange-200 text-xs leading-tight hidden lg:block">Kelola akun, peran, dan akses pengguna</div>
                            </div>
                        </a>

                        <a href="{{ route('branches.index') }}" 
                           class="flex items-start px-3 py-3 lg:py-2 rounded-md hover:bg-orange-700 transition-colors text-sm">
                            <svg class="w-4 h-4 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 1L3 5v6c0 5.55 3.84 10 9 11 5.16-1 9-5.45 9-11V5l-9-4z"/>
                            </svg>
                            <div class="min-w-0">
                                <div class="truncate">Cabang Toko</div>
                                <div class="text-orange-200 text-xs leading-tight hidden lg:block">Data cabang: alamat, kontak, dan kode</div>
                            </div>
                        </a>

                        <a href="{{ route('roles.index') }}" 
                           class="flex items-start px-3 py-3 lg:py-2 rounded-md hover:bg-orange-700 transition-colors text-sm">
                            <svg class="w-4 h-4 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 1l3 6 6 1-4.5 4.5 1 6-5.5-3-5.5 3 1-6L3 8l6-1 3-6z"/>
                            </svg>
                            <div class="min-w-0">
                                <div class="truncate">Role & Hak Akses</div>
                                <div class="text-orange-200 text-xs leading-tight hidden lg:block">Atur peran dan izin menu aplikasi</div>
                            </div>
                        </a>

                        <a href="{{ route('suppliers.index') }}" 
                           class="flex items-start px-3 py-3 lg:py-2 rounded-md hover:bg-orange-700 transition-colors text-sm">
                            <svg class="w-4 h-4 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 1L3 5v6c0 5.55 3.84 10 9 11 5.16-1 9-5.45 9-11V5l-9-4z"/>
                            </svg>
                            <div class="min-w-0">
                                <div class="truncate">Data Supplier</div>
                                <div class="text-orange-200 text-xs leading-tight hidden lg:block">Daftar supplier & kontak WhatsApp</div>
                            </div>
                        </a>

                        <a href="{{ route('units.index') }}" 
                           class="flex items-start px-3 py-3 lg:py-2 rounded-md hover:bg-orange-700 transition-colors text-sm">
                            <svg class="w-4 h-4 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M21 6H3a1 1 0 000 2h18a1 1 0 000-2zM21 10H3a1 1 0 000 2h18a1 1 0 000-2zM21 14H3a1 1 0 000 2h18a1 1 0 000-2zM21 18H3a1 1 0 000 2h18a1 1 0 000-2z"/>
                            </svg>
                            <div class="min-w-0">
                                <div class="truncate">Satuan Produk</div>
                                <div class="text-orange-200 text-xs leading-tight hidden lg:block">Satuan pengukuran untuk bahan & produk</div>
                            </div>
                        </a>

                        <a href="{{ route('categories.index') }}" 
                           class="flex items-start px-3 py-3 lg:py-2 rounded-md hover:bg-orange-700 transition-colors text-sm">
                            <svg class="w-4 h-4 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M5.5 7A1.5 1.5 0 004 5.5V4a2 2 0 012-2h1.5a1.5 1.5 0 000 3h-3zM11 2a2 2 0 00-2 2v1.5a1.5 1.5 0 000 3H7.5A1.5 1.5 0 006 7V5.5A1.5 1.5 0 004.5 4H6a2 2 0 012-2h3z"/>
                            </svg>
                            <div class="min-w-0">
                                <div class="truncate">Kategori Produk</div>
                                <div class="text-orange-200 text-xs leading-tight hidden lg:block">Kelompok/kategori bahan dan produk</div>
                            </div>
                        </a>

                        <a href="{{ route('raw-materials.index') }}" 
                           class="flex items-start px-3 py-3 lg:py-2 rounded-md hover:bg-orange-700 transition-colors text-sm">
                            <svg class="w-4 h-4 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                            <div class="min-w-0">
                                <div class="truncate">Master Bahan Mentah</div>
                                <div class="text-orange-200 text-xs leading-tight hidden lg:block">Daftar bahan mentah untuk proses produksi</div>
                            </div>
                        </a>

                        <a href="{{ route('semi-finished-products.index') }}" 
                           class="flex items-start px-3 py-3 lg:py-2 rounded-md hover:bg-orange-700 transition-colors text-sm">
                            <svg class="w-4 h-4 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M20 6h-2.18C17.4 4.84 16.3 4 15 4H9c-1.3 0-2.4.84-2.82 2H4c-1.1 0-2 .9-2 2v11c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2z"/>
                            </svg>
                            <div>
                                <div>Master Bahan Setengah Jadi</div>
                                <div class="text-orange-200 text-xs">Hasil olahan dari bahan mentah (intermediate)</div>
                            </div>
                        </a>

                        <a href="{{ route('finished-products.index') }}" 
                           class="flex items-start px-3 py-2 rounded-md hover:bg-orange-700 transition-colors text-sm">
                            <svg class="w-4 h-4 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M20 6h-2.18C17.4 4.84 16.3 4 15 4H9c-1.3 0-2.4.84-2.82 2H4c-1.1 0-2 .9-2 2v11c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2z"/>
                            </svg>
                            <div>
                                <div>Master Produk Siap Jual</div>
                                <div class="text-orange-200 text-xs">Produk akhir yang dijual di cabang</div>
                            </div>
                        </a>

                        @if (auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Manager'))
                            <a href="{{ route('sales-packages.index') }}" 
                               class="flex items-start px-3 py-2 rounded-md hover:bg-orange-700 transition-colors text-sm {{ request()->routeIs('sales-packages.*') ? 'bg-orange-700' : '' }}">
                                <svg class="w-4 h-4 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                                </svg>
                                <div>
                                    <div>Paket Penjualan</div>
                                    <div class="text-orange-200 text-xs">Manager: Kelola paket produk & harga standar</div>
                                </div>
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Divider -->
                <div class="border-t border-orange-600 my-2"></div>
            @endif

            <!-- PEMBELIAN BAHAN MENTAH Section -->
            <div class="nav-item">
                <button class="w-full flex items-center justify-between px-3 py-2 text-left text-white font-bold hover:bg-orange-700 rounded-md transition-colors"
                        onclick="toggleCollapse('pembelianCollapse')">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M7 4V2a1 1 0 0 1 2 0v2h6V2a1 1 0 0 1 2 0v2h1a3 3 0 0 1 3 3v12a3 3 0 0 1-3 3H6a3 3 0 0 1-3-3V7a3 3 0 0 1 3-3h1z"/>
                        </svg>
                        PEMBELIAN BAHAN MENTAH
                    </div>
                    <svg class="w-4 h-4 transform transition-transform duration-200" id="pembelianCollapseIcon" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M7 10l5 5 5-5z"/>
                    </svg>
                </button>
                
                <div class="hidden mt-2 ml-8 space-y-1" id="pembelianCollapse">
                    <a href="{{ route('purchase-orders.index') }}" 
                       class="flex items-start px-3 py-2 rounded-md hover:bg-orange-700 transition-colors text-sm {{ request()->routeIs('purchase-orders.*') ? 'bg-orange-700' : '' }}">
                        <svg class="w-4 h-4 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 6V4l-8 8 8 8v-2h8v-4h-8z"/>
                        </svg>
                        <div>
                            <div>Buat Purchase Order</div>
                            <div class="text-orange-200 text-xs">Manajer: Buat dan kelola permintaan pembelian bahan mentah</div>
                        </div>
                    </a>

                    <a href="{{ route('purchase-receipts.index') }}" 
                       class="flex items-start px-3 py-2 rounded-md hover:bg-orange-700 transition-colors text-sm {{ request()->routeIs('purchase-receipts.*') ? 'bg-orange-700' : '' }}">
                        <svg class="w-4 h-4 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6z"/>
                        </svg>
                        <div>
                            <div>Terima Purchase Receipt</div>
                            <div class="text-orange-200 text-xs">Kepala Toko: Terima dan verifikasi bahan yang dikirim supplier</div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Divider -->
            <div class="border-t border-orange-600 my-2"></div>

            <!-- STOK Section -->
            <div class="nav-item">
                <button class="w-full flex items-center justify-between px-3 py-2 text-left text-white font-bold hover:bg-orange-700 rounded-md transition-colors"
                        onclick="toggleCollapse('stokCollapse')">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                        STOK
                    </div>
                    <svg class="w-4 h-4 transform transition-transform duration-200" id="stokCollapseIcon" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M7 10l5 5 5-5z"/>
                    </svg>
                </button>
                
                <div class="hidden mt-2 ml-8 space-y-1" id="stokCollapse">
                    <a href="{{ route('raw-materials.stock') }}" 
                       class="flex items-start px-3 py-2 rounded-md hover:bg-orange-700 transition-colors text-sm {{ request()->routeIs('raw-materials.stock') ? 'bg-orange-700' : '' }}">
                        <svg class="w-4 h-4 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M20 6h-2.18C17.4 4.84 16.3 4 15 4H9c-1.3 0-2.4.84-2.82 2H4c-1.1 0-2 .9-2 2v11c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2z"/>
                        </svg>
                        <div>
                            <div>Stok Bahan Mentah</div>
                            <div class="text-orange-200 text-xs">Pantau stok bahan mentah</div>
                        </div>
                    </a>

                    <a href="{{ route('semi-finished-stock.index') }}" 
                       class="flex items-start px-3 py-2 rounded-md hover:bg-orange-700 transition-colors text-sm {{ request()->routeIs('semi-finished-stock.*') ? 'bg-orange-700' : '' }}">
                        <svg class="w-4 h-4 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M20 6h-2.18C17.4 4.84 16.3 4 15 4H9c-1.3 0-2.4.84-2.82 2H4c-1.1 0-2 .9-2 2v11c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2z"/>
                        </svg>
                        <div>
                            <div>Stok Bahan Setengah Jadi</div>
                            <div class="text-orange-200 text-xs">Pantau stok bahan setengah jadi</div>
                        </div>
                    </a>

                    <a href="{{ route('finished-products-stock.index') }}" 
                       class="flex items-start px-3 py-2 rounded-md hover:bg-orange-700 transition-colors text-sm {{ request()->routeIs('finished-products-stock.*') ? 'bg-orange-700' : '' }}">
                        <svg class="w-4 h-4 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M20 6h-2.18C17.4 4.84 16.3 4 15 4H9c-1.3 0-2.4.84-2.82 2H4c-1.1 0-2 .9-2 2v11c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2z"/>
                        </svg>
                        <div>
                            <div>Stok Produk Siap Jual</div>
                            <div class="text-orange-200 text-xs">Pantau stok produk siap jual</div>
                        </div>
                    </a>

                    <a href="{{ route('stock-opnames.index') }}" 
                       class="flex items-start px-3 py-2 rounded-md hover:bg-orange-700 transition-colors text-sm {{ request()->routeIs('stock-opnames.*') ? 'bg-orange-700' : '' }}">
                        <svg class="w-4 h-4 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <div>Stok Opname</div>
                            <div class="text-orange-200 text-xs">Lakukan dan tinjau stok opname</div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- PUSAT PRODUKSI Section -->
            <div class="nav-item">
                <button class="w-full flex items-center justify-between px-3 py-2 text-left text-white font-bold hover:bg-orange-700 rounded-md transition-colors"
                        onclick="toggleCollapse('produksiCollapse')">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                        PUSAT PRODUKSI
                    </div>
                    <svg class="w-4 h-4 transform transition-transform duration-200" id="produksiCollapseIcon" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M7 10l5 5 5-5z"/>
                    </svg>
                </button>
                
                <div class="hidden mt-2 ml-8 space-y-1" id="produksiCollapse">
                    <a href="{{ route('production-requests.index') }}" 
                       class="flex items-start px-3 py-2 rounded-md hover:bg-orange-700 transition-colors text-sm {{ request()->routeIs('production-requests.*') ? 'bg-orange-700' : '' }}">
                        <svg class="w-4 h-4 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 6V4l-8 8 8 8v-2h8v-4h-8z"/>
                        </svg>
                        <div>
                            <div>Pengajuan Produksi</div>
                            <div class="text-orange-200 text-xs">Kepala Produksi: Ajukan penggunaan bahan mentah</div>
                        </div>
                    </a>

                    <a href="{{ route('production-approvals.index') }}" 
                       class="flex items-start px-3 py-2 rounded-md hover:bg-orange-700 transition-colors text-sm {{ request()->routeIs('production-approvals.*') ? 'bg-orange-700' : '' }}">
                        <svg class="w-4 h-4 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <div>Persetujuan Produksi</div>
                            <div class="text-orange-200 text-xs">Manajer: Review dan setujui pengajuan produksi</div>
                        </div>
                    </a>

                    <a href="{{ route('production-processes.index') }}" 
                       class="flex items-start px-3 py-2 rounded-md hover:bg-orange-700 transition-colors text-sm {{ request()->routeIs('production-processes.*') ? 'bg-orange-700' : '' }}">
                        <svg class="w-4 h-4 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                        <div>
                            <div>Proses Produksi</div>
                            <div class="text-orange-200 text-xs">Kru Produksi: Update status pengolahan bahan</div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- DISTRIBUSI Section -->
            <div class="nav-item">
                <button class="w-full flex items-center justify-between px-3 py-2 text-left text-white font-bold hover:bg-orange-700 rounded-md transition-colors"
                        onclick="toggleCollapse('distribusiCollapse')">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M3 4a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 14.846 4.632 17 6.414 17H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 6H6.28l-.31-1.243A1 1 0 005 4H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100 3 1.5 1.5 0 000-3z"/>
                        </svg>
                        DISTRIBUSI
                    </div>
                    <svg class="w-4 h-4 transform transition-transform duration-200" id="distribusiCollapseIcon" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M7 10l5 5 5-5z"/>
                    </svg>
                </button>
                
                <div class="hidden mt-2 ml-8 space-y-1" id="distribusiCollapse">
                    <a href="{{ route('semi-finished-distributions.index') }}" 
                       class="flex items-start px-3 py-2 rounded-md hover:bg-orange-700 transition-colors text-sm {{ request()->routeIs('semi-finished-distributions.*') && !request()->routeIs('semi-finished-distributions.inbox') ? 'bg-orange-700' : '' }}">
                        <svg class="w-4 h-4 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M3 4a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 14.846 4.632 17 6.414 17H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 6H6.28l-.31-1.243A1 1 0 005 4H3z"/>
                        </svg>
                        <div>
                            <div>Pengiriman ke Cabang</div>
                            <div class="text-orange-200 text-xs">Kirim bahan setengah jadi ke cabang</div>
                        </div>
                    </a>

                    <a href="{{ route('semi-finished-distributions.inbox', ['branch_id' => $selectedBranch?->id]) }}" 
                       class="flex items-start px-3 py-2 rounded-md hover:bg-orange-700 transition-colors text-sm {{ request()->routeIs('semi-finished-distributions.inbox') ? 'bg-orange-700' : '' }}">
                        <svg class="w-4 h-4 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                        </svg>
                        <div>
                            <div>Kotak Masuk Distribusi</div>
                            <div class="text-orange-200 text-xs">Terima/Tolak distribusi masuk ke cabang</div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- OPERASIONAL CABANG Section -->
            @if (auth()->check() &&
                    auth()->user() &&
                    (auth()->user()->hasRole('Super Admin') ||
                        auth()->user()->hasRole('Manager') ||
                        auth()->user()->hasRole('Kepala Toko') ||
                        auth()->user()->hasRole('Kru Toko')))
                <div class="nav-item">
                    <button class="w-full flex items-center justify-between px-3 py-2 text-left text-white font-bold hover:bg-orange-700 rounded-md transition-colors"
                            onclick="toggleCollapse('operasionalCollapse')">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M19 7h-3V6a4 4 0 0 0-8 0v1H5a1 1 0 0 0-1 1v11a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3V8a1 1 0 0 0-1-1zM10 6a2 2 0 0 1 4 0v1h-4V6zm8 13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V9h2v1a1 1 0 0 0 2 0V9h4v1a1 1 0 0 0 2 0V9h2v10z"/>
                            </svg>
                            OPERASIONAL CABANG
                        </div>
                        <svg class="w-4 h-4 transform transition-transform duration-200" id="operasionalCollapseIcon" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M7 10l5 5 5-5z"/>
                        </svg>
                    </button>
                    
                    <div class="hidden mt-2 ml-8 space-y-1" id="operasionalCollapse">
                        @if (auth()->user()->hasRole('Super Admin') ||
                                auth()->user()->hasRole('Manager') ||
                                auth()->user()->hasRole('Kepala Toko') ||
                                auth()->user()->hasRole('Kru Toko'))
                            <a href="{{ route('semi-finished-usage-requests.index') }}" 
                               class="flex items-start px-3 py-2 rounded-md hover:bg-orange-700 transition-colors text-sm {{ request()->routeIs('semi-finished-usage-requests.*') ? 'bg-orange-700' : '' }}">
                                <svg class="w-4 h-4 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 6V4l-8 8 8 8v-2h8v-4h-8z"/>
                                </svg>
                                <div>
                                    <div>Ajukan Penggunaan Bahan</div>
                                    <div class="text-orange-200 text-xs">Kru Toko: Ajukan penggunaan bahan setengah jadi</div>
                                </div>
                            </a>
                        @endif

                        @if (auth()->user()->hasRole('Super Admin') ||
                                auth()->user()->hasRole('Manager') ||
                                auth()->user()->hasRole('Kepala Toko'))
                            <a href="{{ route('semi-finished-usage-approvals.index') }}" 
                               class="flex items-start px-3 py-2 rounded-md hover:bg-orange-700 transition-colors text-sm {{ request()->routeIs('semi-finished-usage-approvals.*') ? 'bg-orange-700' : '' }}">
                                <svg class="w-4 h-4 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div>
                                    <div>Setujui Penggunaan Bahan</div>
                                    <div class="text-orange-200 text-xs">Kepala Toko: Setujui/Tolak → Kirim WA ke Kru Toko</div>
                                </div>
                            </a>
                        @endif

                        @if (auth()->user()->hasRole('Super Admin') ||
                                auth()->user()->hasRole('Manager') ||
                                auth()->user()->hasRole('Kepala Toko') ||
                                auth()->user()->hasRole('Kru Toko'))
                            <a href="{{ route('semi-finished-usage-processes.index', ['branch_id' => $selectedBranch?->id]) }}" 
                               class="flex items-start px-3 py-2 rounded-md hover:bg-orange-700 transition-colors text-sm {{ request()->routeIs('semi-finished-usage-processes.*') ? 'bg-orange-700' : '' }}">
                                <svg class="w-4 h-4 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                </svg>
                                <div>
                                    <div>Proses Bahan Setengah Jadi</div>
                                    <div class="text-orange-200 text-xs">Kru Toko: Proses bahan sesuai approval</div>
                                </div>
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
                                   class="flex items-start px-3 py-2 rounded-md hover:bg-orange-700 transition-colors text-sm {{ request()->routeIs('sales.*') ? 'bg-orange-700' : '' }}">
                                    <svg class="w-4 h-4 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2ZM21 9V7L15 1L9 7V9C9 10.1 9.9 11 11 11V22H13V11C14.1 11 15 10.1 15 9Z"/>
                                    </svg>
                                    <div>
                                        <div>Penjualan</div>
                                        <div class="text-orange-200 text-xs">Kru Toko: Jual produk siap jual ke pelanggan</div>
                                    </div>
                                </a>
                            @endif

                            @if (auth()->user()->hasRole('Super Admin') ||
                                    auth()->user()->hasRole('Manager') ||
                                    auth()->user()->hasRole('Kepala Toko'))
                                <a href="{{ route('destruction-reports.index') }}" 
                                   class="flex items-start px-3 py-2 rounded-md hover:bg-orange-700 transition-colors text-sm {{ request()->routeIs('destruction-reports.*') ? 'bg-orange-700' : '' }}">
                                    <svg class="w-4 h-4 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 9a3.02 3.02 0 0 0-3 3c0 1.642 1.358 3 3 3 1.641 0 3-1.358 3-3 0-1.641-1.359-3-3-3z"/>
                                        <path d="M12 5c-7.633 0-9.927 6.617-9.948 6.684L1.946 12l.105.316C2.073 12.383 4.367 19 12 19s9.927-6.617 9.948-6.684L22.054 12l-.105-.316C21.927 11.617 19.633 5 12 5z"/>
                                    </svg>
                                    <div>
                                        <div>Laporan Pemusnahan</div>
                                        <div class="text-orange-200 text-xs">Catat & tinjau pemusnahan barang</div>
                                    </div>
                                </a>
                            @endif
                        @else
                            <div class="flex items-start px-3 py-2 text-orange-200 text-sm">
                                <svg class="w-4 h-4 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div>
                                    <div>Produk Siap Jual</div>
                                    <div class="text-orange-200 text-xs">Tidak tersedia di Pusat Produksi</div>
                                </div>
                            </div>
                        @endif

                        @if (auth()->user()->hasRole('Super Admin') ||
                                auth()->user()->hasRole('Manager') ||
                                auth()->user()->hasRole('Kepala Toko'))
                            <a href="{{ route('stock-transfer.index') }}" 
                               class="flex items-start px-3 py-2 rounded-md hover:bg-orange-700 transition-colors text-sm {{ request()->routeIs('stock-transfer.*') ? 'bg-orange-700' : '' }}">
                                <svg class="w-4 h-4 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M4 12l1.41 1.41L11 7.83V20h2V7.83l5.59 5.58L20 12l-8-8-8 8z"/>
                                </svg>
                                <div>
                                    <div>Transfer Antar Cabang</div>
                                    <div class="text-orange-200 text-xs">Kepala Toko: Transfer stok antar cabang</div>
                                </div>
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Divider -->
                <div class="border-t border-orange-600 my-2"></div>
            @endif
        </nav>
    </div>
</aside>

<script>
// Sidebar Toggle Functions
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    
    sidebar.classList.toggle('-translate-x-full');
    overlay.classList.toggle('hidden');
}

function closeSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    
    sidebar.classList.add('-translate-x-full');
    overlay.classList.add('hidden');
}

// Toggle collapse sections
function toggleCollapse(collapseId) {
    const collapse = document.getElementById(collapseId);
    const icon = document.getElementById(collapseId + 'Icon');
    
    collapse.classList.toggle('hidden');
    icon.classList.toggle('rotate-180');
}

// Connect sidebar toggle to header button
document.addEventListener('DOMContentLoaded', function() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const overlay = document.getElementById('sidebarOverlay');
    
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', toggleSidebar);
    }
    
    if (overlay) {
        overlay.addEventListener('click', closeSidebar);
    }
    
    // Close sidebar on mobile when clicking a link
    const sidebarLinks = document.querySelectorAll('#sidebar a');
    sidebarLinks.forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth < 1024) { // lg breakpoint
                closeSidebar();
            }
        });
    });
});

// Handle responsive behavior
window.addEventListener('resize', function() {
    if (window.innerWidth >= 1024) { // lg breakpoint
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.add('hidden');
    }
});
</script>
