@extends('layouts.app')

@section('title', 'Buat Penjualan Baru (POS)')

@push('styles')
<style>
    body {
        overflow-x: hidden; /* prevent horizontal scroll */
        overflow-y: auto;   /* allow page to scroll so footer won't cover */
    }
    .pos-container {
        display: grid;
        grid-template-columns: 60% 40%;
        gap: 1.5rem;
        height: calc(100vh - 140px - var(--footer-height, 56px)); /* account for fixed footer */
    }
    .product-panel .card-body {
        overflow-y: auto;
        max-height: calc(100vh - 200px - var(--footer-height, 56px)); /* avoid footer overlap */
        padding-bottom: 2rem;
    }
    .product-grid {
        overflow-y: auto;
        max-height: calc(100vh - 250px - var(--footer-height, 56px));
    }
    .checkout-panel {
        display: flex;
        flex-direction: column;
        max-height: calc(100vh - 140px - var(--footer-height, 56px));
    }
    .checkout-panel .card-body {
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        max-height: calc(100vh - 200px - var(--footer-height, 56px));
        padding-bottom: calc(var(--footer-height, 56px) + 24px); /* ensure last button clears footer */
    }
    .cart-items-container {
        flex-grow: 1;
        overflow-y: auto;
        max-height: 300px; /* Fixed height for cart items */
        margin-bottom: 1rem;
        border: 1px solid #f0f0f0;
        border-radius: 0.25rem;
        padding: 0.5rem;
    }
    .product-card {
        cursor: pointer;
        transition: all 0.2s ease-in-out;
    }
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
    .product-card.out-of-stock {
        opacity: 0.6;
        cursor: not-allowed;
    }
    .product-card.out-of-stock .add-to-cart-btn {
        pointer-events: none;
    }
    #loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.85);
        z-index: 1056; /* Higher than modals */
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .total-section h5 {
        font-weight: bold;
    }
    /* Custom scrollbar styling */
    .product-grid::-webkit-scrollbar,
    .cart-items-container::-webkit-scrollbar {
        width: 8px;
    }
    .product-grid::-webkit-scrollbar-track,
    .cart-items-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    .product-grid::-webkit-scrollbar-thumb,
    .cart-items-container::-webkit-scrollbar-thumb {
        background: #dc2626;
        border-radius: 10px;
    }
    .product-grid::-webkit-scrollbar-thumb:hover,
    .cart-items-container::-webkit-scrollbar-thumb:hover {
        background: #b91c1c;
    }
</style>
@endpush

@section('content')
<div id="pos-app">
    <div id="loading-overlay" style="display: none;">
        <div class="spinner-border text-danger" role="status" style="width: 3rem; height: 3rem;">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <div id="error-message" class="alert alert-danger alert-dismissible fade show" style="display: none;" role="alert">
        <!-- Error message will be inserted here -->
        <button type="button" class="btn-close" onclick="this.parentElement.style.display='none'"></button>
    </div>

    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-list-ul me-1"></i> Ke Daftar Penjualan
        </a>
    </div>

    @if(!request('branch_id') && !$branches->isEmpty())
        <div class="card shadow-lg border-0 mt-5">
            <div class="card-body text-center p-5">
                <i class="bi bi-shop-window fs-1 text-danger mb-3"></i>
                <h3 class="card-title fw-bold">Pilih Cabang Penjualan</h3>
                <p class="card-text text-muted mb-4">Untuk memulai sesi penjualan, silakan pilih cabang terlebih dahulu.</p>
                <div class="col-md-5 mx-auto">
                    <select id="branch_id_selector" class="form-select form-select-lg">
                        <option value="" selected disabled>-- Pilih Cabang --</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        @push('scripts')
        <script>
            // Fallback: ensure redirect works even if main JS hasn't initialized yet
            (function attachBranchRedirect(){
                function bind() {
                    var sel = document.getElementById('branch_id_selector');
                    if (!sel) return;
                    sel.addEventListener('change', function (e) {
                        var val = e.target && e.target.value;
                        if (val) {
                            window.location.href = '/sales/create?branch_id=' + val;
                        }
                    });
                }
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', bind);
                } else {
                    bind();
                }
            })();
        </script>
        @endpush
    @else
        <form id="checkout-form" novalidate>
            @csrf
            <input type="hidden" name="branch_id" value="{{ request('branch_id') }}">
            <div class="pos-container">
                <!-- Left Panel: Products -->
                <div class="product-panel card shadow-sm border-0">
                    <div class="card-header bg-white py-3">
                        <div class="d-flex justify-content-between align-items-center">
                             <h5 class="mb-0 fw-bold text-danger"><i class="bi bi-grid-3x3-gap me-2"></i>Pilih Produk</h5>
                             <div class="w-50">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                                    <input type="text" id="search-input" class="form-control" placeholder="Cari produk atau paket...">
                                </div>
                             </div>
                        </div>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <div id="category-filters" class="d-flex flex-wrap gap-2 mb-3 border-bottom pb-3">
                            <!-- Category buttons will be rendered here by JS -->
                        </div>
                        <div id="product-list" class="row product-grid flex-grow-1 p-2">
                            <!-- Product cards will be rendered here by JS -->
                        </div>
                    </div>
                </div>

                <!-- Right Panel: Checkout -->
                <div class="checkout-panel card shadow-sm border-0">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold text-danger"><i class="bi bi-cart3 me-2"></i>Detail Pesanan</h5>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <!-- Customer Info -->
                        <div class="mb-3">
                             <h6 class="fw-bold">Informasi Pelanggan (Opsional)</h6>
                             <div class="mb-2">
                                 <input type="text" id="customer_name" name="customer_name" class="form-control" placeholder="Nama Pelanggan">
                             </div>
                             <div>
                                 <input type="text" id="customer_phone" name="customer_phone" class="form-control" placeholder="No. Telepon">
                             </div>
                        </div>
                        
                        <!-- Cart Items -->
                        <div class="cart-items-container table-responsive mb-3">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>Produk</th>
                                        <th>Qty</th>
                                        <th class="text-end">Subtotal</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="cart-items">
                                    <!-- Cart items will be rendered here by JS -->
                                </tbody>
                            </table>
                        </div>

                        <!-- Totals -->
                        <div class="mt-auto">
                            <div class="total-section border-top pt-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <h6 class="text-muted">Subtotal</h6>
                                    <h6 id="subtotal" class="fw-bold">Rp 0</h6>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="text-muted">Diskon</h6>
                                    <div class="d-flex gap-2 w-50">
                                        <input type="number" id="discount-value" name="discount_value" class="form-control form-control-sm" value="0">
                                        <select id="discount-type" name="discount_type" class="form-select form-select-sm">
                                            <option value="nominal">Rp</option>
                                            <option value="percentage">%</option>
                                        </select>
                                    </div>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between mb-3">
                                    <h5 class="text-danger">Grand Total</h5>
                                    <h5 id="grand-total" class="text-danger">Rp 0</h5>
                                </div>
                            </div>

                            <!-- Payment -->
                            <div class="payment-section border-top pt-3">
                                <h6 class="fw-bold">Metode Pembayaran</h6>
                                <div class="d-flex justify-content-around mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" id="payment_cash" value="cash" checked>
                                        <label class="form-check-label" for="payment_cash"><i class="bi bi-cash-coin"></i> Tunai</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" id="payment_qris" value="qris">
                                        <label class="form-check-label" for="payment_qris"><i class="bi bi-qr-code-scan"></i> QRIS</label>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <label for="amount-paid" class="form-label">Jumlah Bayar</label>
                                    <input type="number" id="amount-paid" name="amount_paid" class="form-control" placeholder="Masukkan jumlah pembayaran">
                                </div>
                                <div class="d-flex justify-content-between">
                                    <h6 class="text-muted">Kembalian</h6>
                                    <h6 id="change" class="fw-bold">Rp 0</h6>
                                </div>
                            </div>

                            <div class="d-grid mt-3">
                                <button type="submit" class="btn btn-danger btn-lg fw-bold">
                                    <i class="bi bi-check-circle me-2"></i>Selesaikan Pembayaran
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    @endif
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/sales-pos.js') }}?v={{ filemtime(public_path('js/sales-pos.js')) }}"></script>
@endpush
