<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Purchase Order - {{ $purchaseOrder->order_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            background: white;
        }

        .container {
            max-width: 210mm;
            margin: 0 auto;
            padding: 20px;
            background: white;
        }

        .header {
            border-bottom: 3px solid #dc2626;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .company-info {
            text-align: center;
            margin-bottom: 20px;
        }

        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #dc2626;
            margin-bottom: 5px;
        }

        .company-details {
            color: #666;
            font-size: 11px;
        }

        .document-title {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            color: #333;
            margin: 20px 0;
            padding: 10px;
            border: 2px solid #dc2626;
            background: #f8f9fa;
        }

        .order-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }

        .info-section {
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 5px;
        }

        .info-section h3 {
            font-size: 14px;
            font-weight: bold;
            color: #dc2626;
            margin-bottom: 10px;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }

        .info-label {
            font-weight: 600;
            color: #666;
            min-width: 120px;
        }

        .info-value {
            font-weight: normal;
            flex: 1;
            text-align: right;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border: 1px solid #ddd;
        }

        .items-table th,
        .items-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .items-table thead {
            background: linear-gradient(135deg, #dc2626 0%, #ea580c 50%, #eab308 100%);
            color: white;
        }

        .items-table th {
            font-size: 11px;
            text-transform: uppercase;
        }

        .items-table td {
            font-size: 11px;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-bold {
            font-weight: bold;
        }

        .items-table tfoot {
            background-color: #fef2f2;
        }

        .total-row td {
            font-size: 12px;
        }

        .notes-section {
            margin-top: 20px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background: #fafafa;
        }

        .notes-section h4 {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 8px;
            color: #333;
        }

        .signature-section {
            margin-top: 40px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
        }

        .signature-box {
            text-align: center;
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 5px;
            min-height: 100px;
        }

        .signature-title {
            font-weight: bold;
            margin-bottom: 50px;
            color: #333;
        }

        .signature-line {
            border-top: 1px solid #333;
            margin-top: 40px;
            padding-top: 5px;
            font-size: 11px;
        }

        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 2px solid #dc2626;
            text-align: center;
            color: #666;
            font-size: 10px;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-draft {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }

        .status-ordered {
            background: #d1edff;
            color: #004085;
            border: 1px solid #74c0fc;
        }

        @media print {
            body {
                font-size: 11px;
            }
            
            .container {
                margin: 0;
                padding: 15px;
            }
            
            .signature-section {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="company-info">
                <div class="company-name">{{ config('app.name', 'Laravel') }}</div>
                <div class="company-details">
                    Sistem Inventory & Penjualan Laparchicken<br>
                    Jl. Contoh Alamat No. 123, Kota, Provinsi 12345<br>
                    Telp: (021) 123-4567 | Email: info@laparchicken.com
                </div>
            </div>
            
            <div class="document-title">PURCHASE ORDER</div>
        </div>

        <!-- Order Information -->
        <div class="order-info">
            <!-- Purchase Order Details -->
            <div class="info-section">
                <h3>Informasi Purchase Order</h3>
                <div class="info-row">
                    <span class="info-label">Nomor PO:</span>
                    <span class="info-value text-bold">{{ $purchaseOrder->order_number }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Kode PO:</span>
                    <span class="info-value">{{ $purchaseOrder->order_code }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Tanggal:</span>
                    <span class="info-value">{{ $purchaseOrder->created_at->format('d/m/Y') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Status:</span>
                    <span class="info-value">
                        <span class="status-badge status-{{ $purchaseOrder->status }}">
                            {{ $purchaseOrder->status === 'draft' ? 'Draft' : 'Ordered' }}
                        </span>
                    </span>
                </div>
                @if($purchaseOrder->requested_delivery_date)
                    <div class="info-row">
                        <span class="info-label">Pengiriman:</span>
                        <span class="info-value">{{ $purchaseOrder->requested_delivery_date->format('d/m/Y') }}</span>
                    </div>
                @endif
            </div>

            <!-- Supplier Details -->
            <div class="info-section">
                <h3>Informasi Supplier</h3>
                <div class="info-row">
                    <span class="info-label">Nama:</span>
                    <span class="info-value text-bold">{{ $purchaseOrder->supplier->name }}</span>
                </div>
                @if($purchaseOrder->supplier->phone)
                    <div class="info-row">
                        <span class="info-label">Telepon:</span>
                        <span class="info-value">{{ $purchaseOrder->supplier->phone }}</span>
                    </div>
                @endif
                @if($purchaseOrder->supplier->email)
                    <div class="info-row">
                        <span class="info-label">Email:</span>
                        <span class="info-value">{{ $purchaseOrder->supplier->email }}</span>
                    </div>
                @endif
                @if($purchaseOrder->supplier->address)
                    <div class="info-row">
                        <span class="info-label">Alamat:</span>
                        <span class="info-value">{{ $purchaseOrder->supplier->address }}</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="30%">Nama Bahan</th>
                    <th width="15%">Kode</th>
                    <th width="12%">Kuantitas</th>
                    <th width="8%">Satuan</th>
                    <th width="15%">Harga Satuan</th>
                    <th width="15%">Total Harga</th>
                </tr>
            </thead>
            <tbody>
                @foreach($purchaseOrder->items as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>
                            <strong>{{ $item->rawMaterial->name }}</strong>
                            @if($item->rawMaterial->category)
                                <br><small style="color: #666;">{{ $item->rawMaterial->category->name }}</small>
                            @endif
                        </td>
                        <td class="text-center">{{ $item->rawMaterial->code ?? '-' }}</td>
                        <td class="text-right">{{ number_format($item->quantity, 0, ',', '.') }}</td>
                        <td class="text-center">{{ $item->unit_name ?? '-' }}</td>
                        <td class="text-right">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                        <td class="text-right text-bold">Rp {{ number_format($item->total_price, 0, ',', '.') }}</td>
                    </tr>
                    @if($item->notes)
                        <tr>
                            <td></td>
                            <td colspan="6" style="font-size: 10px; color: #666; font-style: italic;">
                                Catatan: {{ $item->notes }}
                            </td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="6" class="text-right text-bold">TOTAL KESELURUHAN:</td>
                    <td class="text-right text-bold" style="font-size: 14px; color: #007bff;">
                        Rp {{ number_format($purchaseOrder->total_amount, 0, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
        </table>

        <!-- Notes Section -->
        @if($purchaseOrder->notes)
            <div class="notes-section">
                <h4>Catatan:</h4>
                <p>{{ $purchaseOrder->notes }}</p>
            </div>
        @endif

        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-title">Dibuat Oleh</div>
                <div class="signature-line">
                    {{ $purchaseOrder->creator->name }}<br>
                    {{ $purchaseOrder->creator->role->name ?? 'Staff' }}
                </div>
            </div>
            <div class="signature-box">
                <div class="signature-title">Disetujui Oleh</div>
                <div class="signature-line">
                    ________________________<br>
                    Manager
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>
                Dokumen ini dicetak pada {{ now()->format('d/m/Y H:i:s') }} |
                Purchase Order {{ $purchaseOrder->order_number }} |
                {{ $purchaseOrder->items->count() }} item, Total: Rp {{ number_format($purchaseOrder->total_amount, 0, ',', '.') }}
            </p>
            <p style="margin-top: 5px;">
                {{ config('app.name', 'Laravel') }} - Sistem Inventory & Penjualan
            </p>
        </div>
    </div>

    <script>
        // Auto print when page loads
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
