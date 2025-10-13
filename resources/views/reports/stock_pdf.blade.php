<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Stok Inventory</title>
    <style>
        @page {
            margin: 2cm 1.5cm 3cm 1.5cm;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
        }
        
        .header {
            text-align: center;
            border-bottom: 3px solid #dc2626;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .logo {
            width: 80px;
            height: 80px;
            margin: 0 auto 15px auto;
            background: linear-gradient(135deg, #dc2626, #ea580c, #eab308);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            font-weight: bold;
        }
        
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #dc2626;
            margin-bottom: 5px;
        }
        
        .company-address {
            font-size: 10px;
            color: #666;
            margin-bottom: 15px;
        }
        
        .report-title {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .report-period {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
        
        .alert-section {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-left: 4px solid #dc2626;
            padding: 15px;
            margin-bottom: 25px;
            border-radius: 5px;
        }
        
        .alert-title {
            font-size: 14px;
            font-weight: bold;
            color: #dc2626;
            margin-bottom: 10px;
        }
        
        .critical-items {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            font-size: 10px;
        }
        
        .summary-section {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 25px;
            border-left: 4px solid #16a34a;
        }
        
        .summary-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr;
            gap: 15px;
        }
        
        .summary-item {
            text-align: center;
        }
        
        .summary-label {
            font-size: 9px;
            color: #666;
            text-transform: uppercase;
        }
        
        .summary-value {
            font-size: 12px;
            font-weight: bold;
            color: #333;
            margin-top: 2px;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .table th {
            background: linear-gradient(135deg, #16a34a, #15803d);
            color: white;
            padding: 8px 6px;
            text-align: left;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .table td {
            padding: 6px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 9px;
        }
        
        .table tr:nth-child(even) {
            background-color: #f9fafb;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .badge-danger {
            background: #fecaca;
            color: #991b1b;
        }
        
        .badge-warning {
            background: #fef3c7;
            color: #92400e;
        }
        
        .badge-success {
            background: #dcfce7;
            color: #166534;
        }
        
        .recommendations {
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            border-left: 4px solid #0284c7;
            padding: 15px;
            margin-top: 20px;
            border-radius: 5px;
        }
        
        .recommendations-title {
            font-size: 14px;
            font-weight: bold;
            color: #0284c7;
            margin-bottom: 10px;
        }
        
        .recommendation-list {
            font-size: 10px;
            line-height: 1.6;
        }
        
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 80px;
            border-top: 2px solid #16a34a;
            padding: 15px;
            background: white;
        }
        
        .footer-content {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;
            height: 100%;
        }
        
        .footer-section {
            font-size: 9px;
        }
        
        .footer-title {
            font-weight: bold;
            color: #16a34a;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        
        .signature-box {
            border: 1px solid #d1d5db;
            height: 40px;
            margin-top: 5px;
            position: relative;
        }
        
        .signature-verified {
            background: #dcfce7;
            border-color: #16a34a;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #166534;
            font-weight: bold;
        }
        
        .qr-code {
            width: 40px;
            height: 40px;
            border: 1px solid #d1d5db;
            background: #f9fafb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 8px;
            color: #666;
            background-image: url('{{ $qr_code_data ?? "" }}');
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
        }
        
        .page-number {
            position: fixed;
            bottom: 10px;
            right: 15px;
            font-size: 8px;
            color: #666;
        }
        
        .verified-stamp {
            position: absolute;
            top: 10px;
            right: 15px;
            background: #16a34a;
            color: white;
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
            transform: rotate(15deg);
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="logo">LC</div>
        <div class="company-name">LAPAR CHICKEN</div>
        <div class="company-address">
            Jl. Raya Kuliner No. 123, Jakarta Selatan 12345<br>
            Telp: (021) 1234-5678 | Email: info@laparchicken.com
        </div>
        <div class="report-title">Laporan Stok Inventory</div>
        <div class="report-period">Per Tanggal: {{ $report_date }}</div>
        
        @if(isset($verified_at))
        <div class="verified-stamp">TERVERIFIKASI</div>
        @endif
    </div>

    <!-- Critical Stock Alert -->
    @if($lowStockItems->isNotEmpty())
    <div class="alert-section">
        <div class="alert-title">⚠️ PERINGATAN STOK KRITIS</div>
        <div class="critical-items">
            @foreach($lowStockItems->take(8) as $item)
            <div>• {{ $item->name }}: {{ $item->current_stock }} {{ $item->unit }} (Min: {{ $item->minimum_stock }})</div>
            @endforeach
        </div>
        @if($lowStockItems->count() > 8)
        <div style="margin-top: 10px; font-weight: bold; color: #dc2626;">
            ... dan {{ $lowStockItems->count() - 8 }} item lainnya memerlukan perhatian segera
        </div>
        @endif
    </div>
    @endif

    <!-- Summary Section -->
    <div class="summary-section">
        <div class="summary-title" style="font-size: 14px; font-weight: bold; color: #16a34a; margin-bottom: 10px;">
            Ringkasan Inventory
        </div>
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-label">Total Item</div>
                <div class="summary-value">{{ $materials->count() }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Stok Aman</div>
                <div class="summary-value">{{ $materials->where('stock_status', 'Aman')->count() }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Stok Rendah</div>
                <div class="summary-value">{{ $materials->where('stock_status', 'Rendah')->count() }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Stok Kritis</div>
                <div class="summary-value">{{ $materials->where('stock_status', 'Kritis')->count() }}</div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <table class="table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="20%">Nama Material</th>
                <th width="12%">Kategori</th>
                <th width="10%">Stok Saat Ini</th>
                <th width="8%">Satuan</th>
                <th width="10%">Stok Minimum</th>
                <th width="10%">Harga/Unit</th>
                <th width="15%">Total Nilai</th>
                <th width="10%">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($materials as $index => $material)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $material->name }}</td>
                <td>{{ $material->category }}</td>
                <td class="text-right">{{ number_format($material->current_stock, 0, ',', '.') }}</td>
                <td class="text-center">{{ $material->unit }}</td>
                <td class="text-right">{{ number_format($material->minimum_stock, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($material->price_per_unit, 0, ',', '.') }}</td>
                <td class="text-right">
                    <strong>Rp {{ number_format($material->current_stock * $material->price_per_unit, 0, ',', '.') }}</strong>
                </td>
                <td class="text-center">
                    @if($material->stock_status == 'Kritis')
                    <span class="badge badge-danger">KRITIS</span>
                    @elseif($material->stock_status == 'Rendah')
                    <span class="badge badge-warning">RENDAH</span>
                    @else
                    <span class="badge badge-success">AMAN</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot style="background: #f3f4f6; font-weight: bold;">
            <tr>
                <td colspan="7" class="text-center"><strong>TOTAL NILAI INVENTORY</strong></td>
                <td class="text-right">
                    <strong>Rp {{ number_format($totalInventoryValue, 0, ',', '.') }}</strong>
                </td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <!-- Recommendations Section -->
    <div class="recommendations">
        <div class="recommendations-title">💡 Rekomendasi Manajemen Inventory</div>
        <div class="recommendation-list">
            <strong>Berdasarkan analisis data inventory, berikut rekomendasi untuk pengambilan keputusan:</strong><br><br>
            
            <strong>1. Aksi Segera Diperlukan:</strong><br>
            @if($lowStockItems->count() > 0)
            • <strong>{{ $lowStockItems->count() }} item</strong> memerlukan pembelian segera (stok di bawah minimum)<br>
            • Prioritas utama: {{ $lowStockItems->sortBy('current_stock')->take(3)->pluck('name')->implode(', ') }}<br>
            • Estimasi investasi untuk restok kritis: <strong>Rp {{ number_format($lowStockItems->sum(function($item) { return ($item->minimum_stock - $item->current_stock + 50) * $item->price_per_unit; }), 0, ',', '.') }}</strong><br>
            @else
            • Semua item dalam kondisi stok aman - tidak ada aksi segera diperlukan<br>
            @endif
            
            <strong>2. Optimasi Finansial:</strong><br>
            • Total nilai inventory saat ini: <strong>Rp {{ number_format($totalInventoryValue, 0, ',', '.') }}</strong><br>
            • Inventory turnover dapat dioptimalkan pada item dengan stok berlebih<br>
            • Fokus pada kategorisasi ABC untuk efisiensi pembelian<br>
            
            <strong>3. Strategi Operasional:</strong><br>
            • Implementasi automatic reorder point untuk {{ $materials->where('stock_status', '!=', 'Aman')->count() }} item berisiko<br>
            • Review siklus pembelian mingguan/bulanan berdasarkan pola konsumsi<br>
            • Evaluasi supplier alternatif untuk stabilitas supply chain<br>
            
            <strong>4. Monitoring & Control:</strong><br>
            • Setup alert otomatis untuk stok mendekati minimum<br>
            • Audit fisik inventory berkala (disarankan bulanan)<br>
            • Tracking variance antara sistem dan fisik inventory
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div class="footer-content">
            <div class="footer-section">
                <div class="footer-title">Diterbitkan</div>
                <div>Tanggal: {{ $generated_at }}</div>
                <div>Oleh: {{ $generated_by }}</div>
                <div>System: Lapar Chicken InvetPOS</div>
            </div>
            
            <div class="footer-section text-center">
                <div class="footer-title">Verifikasi</div>
                @if(isset($verified_at))
                <div class="signature-box signature-verified">
                    ✓ TERVERIFIKASI<br>
                    {{ $verified_by ?? 'System' }}<br>
                    {{ $verified_at }}
                </div>
                @else
                <div class="signature-box">
                    <div style="position: absolute; bottom: 2px; left: 50%; transform: translateX(-50%); font-size: 7px;">
                        Tanda Tangan Manager
                    </div>
                </div>
                @endif
            </div>
            
            <div class="footer-section text-right">
                <div class="footer-title">Keaslian Dokumen</div>
                <div class="qr-code">
                    @if(isset($qr_code_data))
                    @else
                    QR Code<br>
                    Verification
                    @endif
                </div>
                <div style="margin-top: 5px; font-size: 7px;">
                    Doc ID: {{ $document_id ?? md5($report_date . $totalInventoryValue) }}<br>
                    Hash: {{ substr($verification_hash ?? '', 0, 16) }}...
                </div>
            </div>
        </div>
    </div>

    <div class="page-number">
        Halaman 1 dari 1 | Dicetak: {{ now()->format('d/m/Y H:i:s') }}
    </div>
</body>
</html>
