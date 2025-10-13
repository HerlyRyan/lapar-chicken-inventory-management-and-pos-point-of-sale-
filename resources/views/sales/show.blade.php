@extends('layouts.app')

@section('title', 'Detail Penjualan')

@section('content')
<div class="container-fluid screen-only">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Detail Penjualan - {{ $sale->sale_number }}</h3>
                    <div class="btn-group">
                        <a href="{{ route('sales.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                        @if($sale->status === 'completed')
                            <a href="{{ route('sales.receipt.download', ['sale' => $sale->id]) }}" class="btn btn-primary" target="_blank" rel="noopener">
                                <i class="fas fa-print"></i> Cetak
                            </a>
                        @endif
                    </div>
                </div>
                
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('info'))
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            {{ session('info') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="row">
                        <!-- Informasi Transaksi -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0">Informasi Transaksi</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <tr>
                                            <td width="40%"><strong>No. Transaksi:</strong></td>
                                            <td>{{ $sale->sale_number }}</td>
                                        </tr>
                                        
                                        <tr>
                                            <td><strong>Tanggal:</strong></td>
                                            <td>{{ $sale->created_at->format('d/m/Y H:i:s') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Cabang:</strong></td>
                                            <td>{{ $sale->branch->name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Kasir:</strong></td>
                                            <td>{{ $sale->user->name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status:</strong></td>
                                            <td>
                                                <span class="badge bg-{{ $sale->status === 'completed' ? 'success' : ($sale->status === 'cancelled' ? 'danger' : 'warning') }}">
                                                    {{ ucfirst($sale->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Informasi Pelanggan -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0">Informasi Pelanggan</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <tr>
                                            <td width="40%"><strong>Nama:</strong></td>
                                            <td>{{ $sale->customer_name ?: 'Umum' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>No. Telepon:</strong></td>
                                            <td>{{ $sale->customer_phone ?: '-' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Detail Item -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Detail Item</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>No</th>
                                            <th>Item</th>
                                            <th>Tipe</th>
                                            <th>Jumlah</th>
                                            <th>Harga Satuan</th>
                                            <th>Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($sale->items as $index => $item)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $item->item_name }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $item->item_type === 'product' ? 'primary' : 'success' }}">
                                                        {{ $item->item_type === 'product' ? 'Produk' : 'Paket' }}
                                                    </span>
                                                </td>
                                                <td>{{ $item->quantity }}</td>
                                                <td>Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                                <td>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <td colspan="5" class="text-end"><strong>Total Item:</strong></td>
                                            <td><strong>{{ $sale->items->sum('quantity') }} pcs</strong></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Ringkasan Pembayaran -->
                    <div class="row">
                        <div class="col-md-6 offset-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Ringkasan Pembayaran</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <tr>
                                            <td width="60%">Subtotal:</td>
                                            <td class="text-end">Rp {{ number_format($sale->subtotal_amount, 0, ',', '.') }}</td>
                                        </tr>
                                        @if($sale->discount_amount > 0)
                                            <tr>
                                                <td>
                                                    Diskon 
                                                    @if($sale->discount_type === 'percentage')
                                                        ({{ $sale->discount_value }}%)
                                                    @elseif($sale->discount_type === 'nominal')
                                                        (Nominal)
                                                    @endif
                                                    :
                                                </td>
                                                <td class="text-end text-danger">-Rp {{ number_format($sale->discount_amount, 0, ',', '.') }}</td>
                                            </tr>
                                        @endif
                                        <tr class="table-active">
                                            <td><strong>Total Bayar:</strong></td>
                                            <td class="text-end"><strong>Rp {{ number_format($sale->final_amount, 0, ',', '.') }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td>Metode Pembayaran:</td>
                                            <td class="text-end">
                                                <span class="badge bg-{{ $sale->payment_method === 'cash' ? 'success' : 'info' }}">
                                                    {{ $sale->payment_method === 'cash' ? 'Tunai' : 'QRIS' }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Jumlah Bayar:</td>
                                            <td class="text-end">Rp {{ number_format($sale->paid_amount, 0, ',', '.') }}</td>
                                        </tr>
                                        @if($sale->change_amount > 0)
                                            <tr>
                                                <td>Kembalian:</td>
                                                <td class="text-end text-success">Rp {{ number_format($sale->change_amount, 0, ',', '.') }}</td>
                                            </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    @if($sale->status === 'completed')
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="alert alert-warning">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>Catatan:</strong> Transaksi yang sudah selesai tidak dapat diedit. 
                                    Jika perlu pembatalan, silakan hubungi administrator.
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- COMPACT RECEIPT LAYOUT (screen + print) -->
<div class="print-actions">
    <a href="{{ route('sales.index') }}" class="btn btn-sm btn-secondary">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
    @if($sale->status === 'completed')
        <a href="{{ route('sales.receipt.download', ['sale' => $sale->id]) }}" class="btn btn-sm btn-primary" target="_blank" rel="noopener">
            <i class="fas fa-file-pdf"></i> Unduh PDF
        </a>
    @endif
    </div>
<div class="receipt" id="receipt">
    <div class="rc-header">
        <div class="rc-title">Lapar Chicken</div>
        <div class="rc-branch">Cabang: {{ $sale->branch->name ?? 'N/A' }}</div>
        <div class="rc-meta">
            No: {{ $sale->sale_number }}<br>
            Tgl: {{ $sale->created_at->format('d/m/Y H:i') }} · Kasir: {{ $sale->user->name ?? '-' }}
        </div>
    </div>

    <div class="rc-sep"></div>

    <div class="rc-items">
        @foreach($sale->items as $item)
            <div class="rc-row">
                <div class="rc-name">{{ $item->item_name }}</div>
                <div class="rc-sub">{{ $item->quantity }} x Rp {{ number_format($item->unit_price, 0, ',', '.') }}</div>
                <div class="rc-amt">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</div>
            </div>
        @endforeach
    </div>

    <div class="rc-sep"></div>

    <div class="rc-totals">
        <div class="rc-line">
            <span>Subtotal</span>
            <span>Rp {{ number_format($sale->subtotal_amount, 0, ',', '.') }}</span>
        </div>
        @if($sale->discount_amount > 0)
            <div class="rc-line">
                <span>Diskon @if($sale->discount_type === 'percentage') ({{ $sale->discount_value }}%) @endif</span>
                <span>-Rp {{ number_format($sale->discount_amount, 0, ',', '.') }}</span>
            </div>
        @endif
        <div class="rc-line rc-total">
            <span>Total Bayar</span>
            <span>Rp {{ number_format($sale->final_amount, 0, ',', '.') }}</span>
        </div>
        <div class="rc-line">
            <span>Metode</span>
            <span>{{ $sale->payment_method === 'cash' ? 'Tunai' : 'QRIS' }}</span>
        </div>
        <div class="rc-line">
            <span>Dibayar</span>
            <span>Rp {{ number_format($sale->paid_amount, 0, ',', '.') }}</span>
        </div>
        @if(($sale->change_amount ?? 0) > 0)
            <div class="rc-line">
                <span>Kembalian</span>
                <span>Rp {{ number_format($sale->change_amount, 0, ',', '.') }}</span>
            </div>
        @endif
    </div>

    <div class="rc-sep"></div>

    <div class="rc-footer">
        <div>Terima kasih sudah berbelanja!</div>
        <div>Simpan struk ini sebagai bukti transaksi.</div>
    </div>
</div>

<style>
/* Screen: show wide detail, hide receipt preview */
@media screen {
    .receipt { display: none; }
    .print-actions { display: flex; gap: 8px; justify-content: flex-start; margin: 12px 0; }
}

@media print {
    /* Paper size & margins for 80mm thermal receipt */
    @page {
        size: 80mm auto;
        margin: 0;
    }

    html, body {
        padding: 0 !important;
        margin: 0 !important;
        background: #fff !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
        width: 80mm;
    }

    /* Hide the normal page content, show only receipt */
    .screen-only { display: none !important; }
    .receipt { display: block !important; }
    .print-actions { display: none !important; }

    /* Hide common layout containers (navbar/sidebar/footer) */
    header, footer, nav, aside,
    .navbar, .sidebar, .main-sidebar, .main-header, .main-footer,
    .app-sidebar, .app-header, .app-footer,
    .layout-top-nav, .content-header { display: none !important; }

    /* Receipt layout */
    .receipt {
        box-sizing: border-box;
        width: 72mm; /* safe printable width for 80mm paper */
        padding: 3mm 3mm 5mm 3mm;
        font-family: 'Inter', Arial, sans-serif;
        font-size: 10pt;
        line-height: 1.3;
        color: #000;
        overflow: visible;
        page-break-inside: avoid;
    }

    .rc-title {
        text-align: center;
        font-weight: 700;
        font-size: 12pt;
    }
    .rc-branch, .rc-meta { text-align: center; font-size: 9pt; }

    .rc-sep {
        border-top: 1px dashed #000;
        margin: 6px 0;
    }

    .rc-row { display: grid; grid-template-columns: 1fr auto; gap: 2mm; margin-bottom: 2mm; page-break-inside: avoid; break-inside: avoid; }
    .rc-name { grid-column: 1 / span 1; font-weight: 600; word-break: break-word; white-space: normal; }
    .rc-sub { grid-column: 1 / span 1; font-size: 9pt; color: #000; opacity: 0.9; }
    .rc-amt { grid-column: 2 / span 1; text-align: right; align-self: end; }

    .rc-totals { margin-top: 4mm; }
    .rc-line { display: flex; justify-content: space-between; margin: 2px 0; page-break-inside: avoid; break-inside: avoid; }
    .rc-total { font-weight: 700; font-size: 11pt; }

    .rc-footer { text-align: center; margin-top: 6mm; font-size: 9pt; }
}
</style>
@endsection
