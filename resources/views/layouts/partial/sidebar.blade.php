<ul class="nav flex-column gap-1">
    <!-- Dashboard -->
    <li class="nav-item">
        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-house-fill"></i> Dashboard
        </a>
    </li>
    @if (auth()->check() && auth()->user() && (auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Manager')))
        <li class="nav-item">
            <a class="nav-link text-white fw-bold" data-bs-toggle="collapse" href="#masterCollapse" role="button"
                aria-expanded="false" aria-controls="masterCollapse">
                <i class="bi bi-database-fill"></i> DATA MASTER
                <i class="bi bi-chevron-down float-end"></i>
            </a>
            <div class="collapse" id="masterCollapse">
                <ul class="nav flex-column ms-3">
                    <li class="nav-item">
                        <a class="nav-link ps-3" href="{{ route('users.index') }}">
                            <i class="bi bi-people"></i> Manajemen User
                            <small class="d-block text-white-50">Kelola akun, peran, dan akses pengguna</small>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link ps-3" href="{{ route('branches.index') }}">
                            <i class="bi bi-building"></i> Cabang Toko
                            <small class="d-block text-white-50">Data cabang: alamat, kontak, dan kode</small>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link ps-3" href="{{ route('roles.index') }}">
                            <i class="bi bi-shield-check"></i> Role & Hak Akses
                            <small class="d-block text-white-50">Atur peran dan izin menu aplikasi</small>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link ps-3" href="{{ route('suppliers.index') }}">
                            <i class="bi bi-building-gear"></i> Data Supplier
                            <small class="d-block text-white-50">Daftar supplier & kontak WhatsApp</small>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link ps-3" href="{{ route('units.index') }}">
                            <i class="bi bi-rulers"></i> Satuan Produk
                            <small class="d-block text-white-50">Satuan pengukuran untuk bahan & produk</small>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link ps-3" href="{{ route('categories.index') }}">
                            <i class="bi bi-tags"></i> Kategori Produk
                            <small class="d-block text-white-50">Kelompok/kategori bahan dan produk</small>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link ps-3" href="{{ route('raw-materials.index') }}">
                            <i class="bi bi-box-seam"></i> Master Bahan Mentah
                            <small class="d-block text-white-50">Daftar bahan mentah untuk proses produksi</small>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link ps-3" href="{{ route('semi-finished-products.index') }}">
                            <i class="bi bi-box2"></i> Master Bahan Setengah Jadi
                            <small class="d-block text-white-50">Hasil olahan dari bahan mentah (intermediate)</small>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link ps-3" href="{{ route('finished-products.index') }}">
                            <i class="bi bi-archive"></i> Master Produk Siap Jual
                            <small class="d-block text-white-50">Produk akhir yang dijual di cabang</small>
                        </a>
                    </li>

                    @if (auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Manager'))
                        <li class="nav-item">
                            <a href="{{ route('sales-packages.index') }}"
                                class="nav-link ps-3 {{ request()->routeIs('sales-packages.*') ? 'active' : '' }}">
                                <i class="bi bi-box2-heart"></i> Paket Penjualan
                                <small class="d-block text-white-50">Manager: Kelola paket produk & harga
                                    standar</small>
                            </a>
                        </li>
                    @endif
                @else
                    <li class="nav-item">
                        <div class="nav-link ps-3 text-white-50 small">
                            <i class="bi bi-archive text-muted"></i> Master Produk Siap Jual
                            <small class="d-block text-white-50">Tidak tersedia di Pusat Produksi</small>
                        </div>
                    </li>
    @endif
    <li class="nav-item">
        <hr class="text-white-50 my-1">
    </li>

</ul>
</div>
</li>

<li class="nav-item">
    <hr class="text-white-50 my-2">
</li>

{{-- 🛒 PEMBELIAN BAHAN MENTAH (Manager) --}}
<li class="nav-item">
    <a class="nav-link text-white fw-bold" data-bs-toggle="collapse" href="#pembelianCollapse" role="button"
        aria-expanded="false" aria-controls="pembelianCollapse">
        <i class="bi bi-cart-fill"></i> PEMBELIAN BAHAN MENTAH
        <i class="bi bi-chevron-down float-end"></i>
    </a>
    <div class="collapse" id="pembelianCollapse">
        <ul class="nav flex-column ms-3">
            <li class="nav-item">
                <a href="{{ route('purchase-orders.index') }}"
                    class="nav-link ps-3 {{ request()->routeIs('purchase-orders.*') ? 'active' : '' }}">
                    <i class="bi bi-cart-plus"></i> Buat Purchase Order
                    <small class="d-block text-white-50">Manajer: Buat dan kelola permintaan pembelian bahan
                        mentah</small>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('purchase-receipts.index') }}"
                    class="nav-link ps-3 {{ request()->routeIs('purchase-receipts.*') ? 'active' : '' }}">
                    <i class="bi bi-receipt"></i> Terima Purchase Receipt
                    <small class="d-block text-white-50">Kepala Toko: Terima dan verifikasi bahan yang dikirim
                        supplier</small>
                </a>
            </li>

        </ul>
    </div>
</li>

<li class="nav-item">
    <hr class="text-white-50 my-2">
</li>

{{-- 📦 STOK (Visible to all crew) --}}
<li class="nav-item">
    <a class="nav-link text-white fw-bold" data-bs-toggle="collapse" href="#stokCollapse" role="button"
        aria-expanded="false" aria-controls="stokCollapse">
        <i class="bi bi-boxes"></i> STOK
        <i class="bi bi-chevron-down float-end"></i>
    </a>
    <div class="collapse" id="stokCollapse">
        <ul class="nav flex-column ms-3">
            <li class="nav-item">
                <a href="{{ route('raw-materials.stock') }}"
                    class="nav-link ps-3 {{ request()->routeIs('raw-materials.stock') ? 'active' : '' }}">
                    <i class="bi bi-box"></i> Stok Bahan Mentah
                    <small class="d-block text-white-50">Pantau stok bahan mentah</small>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('semi-finished-stock.index') }}"
                    class="nav-link ps-3 {{ request()->routeIs('semi-finished-stock.*') ? 'active' : '' }}">
                    <i class="bi bi-box-seam"></i> Stok Bahan Setengah Jadi
                    <small class="d-block text-white-50">Pantau stok bahan setengah jadi</small>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('finished-products-stock.index') }}"
                    class="nav-link ps-3 {{ request()->routeIs('finished-products-stock.*') ? 'active' : '' }}">
                    <i class="bi bi-archive"></i> Stok Produk Siap Jual
                    <small class="d-block text-white-50">Pantau stok produk siap jual</small>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('stock-opnames.index') }}"
                    class="nav-link ps-3 {{ request()->routeIs('stock-opnames.*') ? 'active' : '' }}">
                    <i class="bi bi-clipboard-check"></i> Stok Opname
                    <small class="d-block text-white-50">Lakukan dan tinjau stok opname</small>
                </a>
            </li>
        </ul>
    </div>
</li>


{{-- 🏭 PUSAT PRODUKSI (Production Center) --}}
<li class="nav-item">
    <a class="nav-link text-white fw-bold" data-bs-toggle="collapse" href="#produksiCollapse" role="button"
        aria-expanded="false" aria-controls="produksiCollapse">
        <i class="bi bi-gear-fill"></i> PUSAT PRODUKSI
        <i class="bi bi-chevron-down float-end"></i>
    </a>
    <div class="collapse" id="produksiCollapse">
        <ul class="nav flex-column ms-3">
            <li class="nav-item">
                <a href="{{ route('production-requests.index') }}"
                    class="nav-link ps-3 {{ request()->routeIs('production-requests.*') ? 'active' : '' }}">
                    <i class="bi bi-clipboard-plus"></i> Pengajuan Produksi
                    <small class="d-block text-white-50">Kepala Produksi: Ajukan penggunaan bahan mentah</small>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('production-approvals.index') }}"
                    class="nav-link ps-3 {{ request()->routeIs('production-approvals.*') ? 'active' : '' }}">
                    <i class="bi bi-clipboard-check"></i> Persetujuan Produksi
                    <small class="d-block text-white-50">Manajer: Review dan setujui pengajuan produksi</small>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('production-processes.index') }}"
                    class="nav-link ps-3 {{ request()->routeIs('production-processes.*') ? 'active' : '' }}">
                    <i class="bi bi-gear-wide-connected"></i> Proses Produksi
                    <small class="d-block text-white-50">Kru Produksi: Update status pengolahan bahan</small>
                </a>
            </li>
            <li class="nav-item">
                <!-- Penggunaan Bahan Mentah link moved to OPERASIONAL CABANG section -->
            </li>
            <!-- Stok Bahan Setengah Jadi moved to DISTRIBUSI section to avoid duplication -->
        </ul>
    </div>
</li>



{{-- 📦 DISTRIBUSI (Unified Shipping & Receiving) --}}
<li class="nav-item">
    <a class="nav-link text-white fw-bold" data-bs-toggle="collapse" href="#distribusiCollapse" role="button"
        aria-expanded="false" aria-controls="distribusiCollapse">
        <i class="bi bi-inboxes"></i> DISTRIBUSI
        <i class="bi bi-chevron-down float-end"></i>
    </a>
    <div class="collapse" id="distribusiCollapse">
        <ul class="nav flex-column ms-3">
            <li class="nav-item">
                <a href="{{ route('semi-finished-distributions.index') }}"
                    class="nav-link ps-3 {{ request()->routeIs('semi-finished-distributions.*') && !request()->routeIs('semi-finished-distributions.inbox') ? 'active' : '' }}">
                    <i class="bi bi-truck"></i> Pengiriman ke Cabang
                    <small class="d-block text-white-50">Kirim bahan setengah jadi ke cabang</small>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('semi-finished-distributions.inbox', ['branch_id' => $selectedBranch?->id]) }}"
                    class="nav-link ps-3 {{ request()->routeIs('semi-finished-distributions.inbox') ? 'active' : '' }}">
                    <i class="bi bi-inbox"></i> Kotak Masuk Distribusi
                    <small class="d-block text-white-50">Terima/Tolak distribusi masuk ke cabang</small>
                </a>
            </li>

        </ul>
    </div>
</li>

{{-- 🏪 OPERASIONAL CABANG --}}
@if (auth()->check() &&
        auth()->user() &&
        (auth()->user()->hasRole('Super Admin') ||
            auth()->user()->hasRole('Manager') ||
            auth()->user()->hasRole('Kepala Toko') ||
            auth()->user()->hasRole('Kru Toko')))
    <li class="nav-item">
        <a class="nav-link text-white fw-bold" data-bs-toggle="collapse" href="#operasionalCollapse" role="button"
            aria-expanded="false" aria-controls="operasionalCollapse">
            <i class="bi bi-shop-window"></i> OPERASIONAL CABANG
            <i class="bi bi-chevron-down float-end"></i>
        </a>
        <div class="collapse" id="operasionalCollapse">
            <ul class="nav flex-column ms-3">
                <!-- Stok Bahan Setengah Jadi link removed here (now under DISTRIBUSI) -->

                @if (auth()->user()->hasRole('Super Admin') ||
                        auth()->user()->hasRole('Manager') ||
                        auth()->user()->hasRole('Kepala Toko') ||
                        auth()->user()->hasRole('Kru Toko'))
                    <li class="nav-item">
                        <a href="{{ route('semi-finished-usage-requests.index') }}"
                            class="nav-link ps-3 {{ request()->routeIs('semi-finished-usage-requests.*') ? 'active' : '' }}">
                            <i class="bi bi-journal-plus"></i> Ajukan Penggunaan Bahan
                            <small class="d-block text-white-50">Kru Toko: Ajukan penggunaan bahan setengah
                                jadi</small>
                        </a>
                    </li>
                @endif

                @if (auth()->user()->hasRole('Super Admin') ||
                        auth()->user()->hasRole('Manager') ||
                        auth()->user()->hasRole('Kepala Toko'))
                    <li class="nav-item">
                        <a href="{{ route('semi-finished-usage-approvals.index') }}"
                            class="nav-link ps-3 {{ request()->routeIs('semi-finished-usage-approvals.*') ? 'active' : '' }}">
                            <i class="bi bi-check2-circle"></i> Setujui Penggunaan Bahan
                            <small class="d-block text-white-50">Kepala Toko: Setujui/Tolak → Kirim WA ke Kru
                                Toko</small>
                        </a>
                    </li>
                @endif

                @if (auth()->user()->hasRole('Super Admin') ||
                        auth()->user()->hasRole('Manager') ||
                        auth()->user()->hasRole('Kepala Toko') ||
                        auth()->user()->hasRole('Kru Toko'))
                    <li class="nav-item">
                        <a href="{{ route('semi-finished-usage-processes.index', ['branch_id' => $selectedBranch?->id]) }}"
                            class="nav-link ps-3 {{ request()->routeIs('semi-finished-usage-processes.*') ? 'active' : '' }}">
                            <i class="bi bi-gear"></i> Proses Bahan Setengah Jadi
                            <small class="d-block text-white-50">Kru Toko: Proses bahan sesuai approval</small>
                        </a>
                    </li>
                @endif

                @php
                    $currentUserBranch = auth()->check() ? auth()->user()->branch : null;
                    $isProductionCenter = $currentUserBranch && $currentUserBranch->type === 'production';
                    $isSuperAdmin = auth()->check() && auth()->user()->is_superadmin;
                @endphp

                {{-- Hide finished products related items from production centers unless super admin --}}
                @if (!$isProductionCenter || $isSuperAdmin)


                    @if (auth()->user()->hasRole('Super Admin') ||
                            auth()->user()->hasRole('Manager') ||
                            auth()->user()->hasRole('Kepala Toko') ||
                            auth()->user()->hasRole('Kru Toko'))
                        <li class="nav-item">
                            <a href="{{ route('sales.create') }}"
                                class="nav-link ps-3 {{ request()->routeIs('sales.*') ? 'active' : '' }}">
                                <i class="bi bi-cash-coin"></i> Penjualan
                                <small class="d-block text-white-50">Kru Toko: Jual produk siap jual ke
                                    pelanggan</small>
                            </a>
                        </li>
                    @endif
                    @if (auth()->user()->hasRole('Super Admin') ||
                            auth()->user()->hasRole('Manager') ||
                            auth()->user()->hasRole('Kepala Toko'))
                        <li class="nav-item">
                            <a href="{{ route('destruction-reports.index') }}"
                                class="nav-link ps-3 {{ request()->routeIs('destruction-reports.*') ? 'active' : '' }}">
                                <i class="bi bi-exclamation-triangle"></i> Laporan Pemusnahan
                                <small class="d-block text-white-50">Catat & tinjau pemusnahan barang</small>
                            </a>
                        </li>
                    @endif

                    {{-- Sales link moved above --}}
                @else
                    {{-- Show message for production center users --}}
                    <li class="nav-item">
                        <div class="nav-link ps-3 text-white-50 small">
                            <i class="bi bi-info-circle"></i> Produk Siap Jual
                            <small class="d-block text-white-50">Tidak tersedia di Pusat Produksi</small>
                        </div>
                    </li>
                @endif

                @if (auth()->user()->hasRole('Super Admin') ||
                        auth()->user()->hasRole('Manager') ||
                        auth()->user()->hasRole('Kepala Toko'))
                    <li class="nav-item">
                        <a href="{{ route('stock-transfer.index') }}"
                            class="nav-link ps-3 {{ request()->routeIs('stock-transfer.*') ? 'active' : '' }}">
                            <i class="bi bi-arrow-repeat"></i> Transfer Antar Cabang
                            <small class="d-block text-white-50">Kepala Toko: Transfer stok antar cabang</small>
                        </a>
                    </li>
                @endif
            </ul>
        </div>
    </li>

    <li class="nav-item">
        <hr class="text-white-50 my-2">
    </li>
@endif

{{-- Reports (LAPORAN) section removed during redesign phase --}}
</ul>
