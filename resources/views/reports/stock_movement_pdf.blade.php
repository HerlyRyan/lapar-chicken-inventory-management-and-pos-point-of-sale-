<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pergerakan Stok</title>
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
        
        .summary-section {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 25px;
            border-left: 4px solid #7c3aed;
        }
        
        .summary-title {
            font-size: 14px;
            font-weight: bold;
            color: #7c3aed;
            margin-bottom: 10px;
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
        
        .movement-stats {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .stat-box {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 5px;
            padding: 10px;
            text-align: center;
        }
        
        .stat-box.in {
            border-left: 4px solid #16a34a;
        }
        
        .stat-box.out {
            border-left: 4px solid #dc2626;
        }
        
        .stat-box.adjust {
            border-left: 4px solid #eab308;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .table th {
            background: linear-gradient(135deg, #7c3aed, #6d28d9);
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
        
        .badge-in {
            background: #dcfce7;
            color: #166534;
        }
        
        .badge-out {
            background: #fecaca;
            color: #991b1b;
        }
        
        .badge-adjust {
            background: #fef3c7;
            color: #92400e;
        }
        
        .insights-section {
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            border-left: 4px solid #0284c7;
            padding: 15px;
            margin-top: 20px;
            border-radius: 5px;
        }
        
        .insights-title {
            font-size: 14px;
            font-weight: bold;
            color: #0284c7;
            margin-bottom: 10px;
        }
        
        .insights-content {
            font-size: 10px;
            line-height: 1.6;
        }
        
        .trend-analysis {
            background: #fefce8;
            border: 1px solid #fde047;
            border-left: 4px solid #eab308;
            padding: 10px;
            margin: 15px 0;
            border-radius: 3px;
        }
        
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 80px;
            border-top: 2px solid #7c3aed;
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
            color: #7c3aed;
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
        <div class="report-title">Laporan Pergerakan Stok</div>
        <div class="report-period">Periode: {{ $period }}</div>
        
        @if(isset($verified_at))
        <div class="verified-stamp">TERVERIFIKASI</div>
        @endif
    </div>

    <!-- Movement Statistics -->
    <div class="movement-stats">
        <div class="stat-box in">
            <div class="summary-label">Total Masuk</div>
            <div class="summary-value" style="color: #16a34a;">
                {{ $movements->where('type', 'in')->sum('quantity') }} Unit
            </div>
            <div style="font-size: 8px; color: #666; margin-top: 2px;">
                {{ $movements->where('type', 'in')->count() }} Transaksi
            </div>
        </div>
        
        <div class="stat-box out">
            <div class="summary-label">Total Keluar</div>
            <div class="summary-value" style="color: #dc2626;">
                {{ $movements->where('type', 'out')->sum('quantity') }} Unit
            </div>
            <div style="font-size: 8px; color: #666; margin-top: 2px;">
                {{ $movements->where('type', 'out')->count() }} Transaksi
            </div>
        </div>
        
        <div class="stat-box adjust">
            <div class="summary-label">Total Adjustment</div>
            <div class="summary-value" style="color: #eab308;">
                {{ $movements->where('type', 'adjustment')->sum('quantity') }} Unit
            </div>
            <div style="font-size: 8px; color: #666; margin-top: 2px;">
                {{ $movements->where('type', 'adjustment')->count() }} Transaksi
            </div>
        </div>
    </div>

    <!-- Summary Section -->
    <div class="summary-section">
        <div class="summary-title">Ringkasan Pergerakan</div>
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-label">Total Transaksi</div>
                <div class="summary-value">{{ $movements->count() }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Net Movement</div>
                <div class="summary-value">
                    {{ $movements->where('type', 'in')->sum('quantity') - $movements->where('type', 'out')->sum('quantity') }}
                </div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Item Teraktif</div>
                <div class="summary-value">
                    {{ $movements->groupBy('material_id')->count() }} Item
                </div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Cabang Terlibat</div>
                <div class="summary-value">
                    {{ $movements->groupBy('branch_id')->count() }} Cabang
                </div>
            </div>
        </div>
    </div>

    <!-- Trend Analysis Box -->
    <div class="trend-analysis">
        <strong>📊 Analisis Tren Pergerakan:</strong><br>
        @if($movements->count() > 0)
        • Rasio In/Out: {{ $movements->where('type', 'out')->sum('quantity') > 0 ? number_format($movements->where('type', 'in')->sum('quantity') / $movements->where('type', 'out')->sum('quantity'), 2) : '∞' }}:1
        ({{ $movements->where('type', 'in')->sum('quantity') > $movements->where('type', 'out')->sum('quantity') ? 'Stok bertambah' : 'Stok berkurang' }})<br>
        • Frekuensi transaksi rata-rata: {{ number_format($movements->count() / max(1, $movements->groupBy(function($item) { return $item->created_at->format('Y-m-d'); })->count()), 1) }} transaksi/hari<br>
        • Item paling aktif: {{ $movements->groupBy('material.name')->sortByDesc(function($group) { return $group->count(); })->keys()->first() ?? 'N/A' }}
        @else
        • Tidak ada data pergerakan pada periode ini
        @endif
    </div>

    <!-- Data Table -->
    <table class="table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="12%">Tanggal</th>
                <th width="15%">Material</th>
                <th width="12%">Cabang</th>
                <th width="8%">Tipe</th>
                <th width="8%">Qty</th>
                <th width="6%">Unit</th>
                <th width="10%">Stok Awal</th>
                <th width="10%">Stok Akhir</th>
                <th width="14%">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($movements as $index => $movement)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $movement->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ $movement->material->name ?? 'N/A' }}</td>
                <td>{{ $movement->branch->name ?? 'Pusat' }}</td>
                <td class="text-center">
                    @if($movement->type == 'in')
                    <span class="badge badge-in">MASUK</span>
                    @elseif($movement->type == 'out')
                    <span class="badge badge-out">KELUAR</span>
                    @else
                    <span class="badge badge-adjust">ADJUST</span>
                    @endif
                </td>
                <td class="text-right">{{ number_format($movement->quantity, 0, ',', '.') }}</td>
                <td class="text-center">{{ $movement->material->unit ?? 'pcs' }}</td>
                <td class="text-right">{{ number_format($movement->stock_before, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($movement->stock_after, 0, ',', '.') }}</td>
                <td>{{ $movement->notes ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Audit Trail & Insights -->
    <div class="insights-section">
        <div class="insights-title">🔍 Audit Trail & Business Intelligence</div>
        <div class="insights-content">
            <strong>Analisis Operasional untuk Pengambilan Keputusan:</strong><br><br>
            
            <strong>1. Pola Pergerakan Stok:</strong><br>
            @if($movements->count() > 0)
            • Volume tertinggi: {{ $movements->where('type', 'in')->sum('quantity') > $movements->where('type', 'out')->sum('quantity') ? 'Stok masuk' : 'Stok keluar' }} 
              ({{ max($movements->where('type', 'in')->sum('quantity'), $movements->where('type', 'out')->sum('quantity')) }} unit)<br>
            • Cabang paling aktif: {{ $movements->groupBy('branch.name')->sortByDesc(function($group) { return $group->count(); })->keys()->first() ?? 'N/A' }}<br>
            • Jam puncak aktivitas: {{ $movements->groupBy(function($item) { return $item->created_at->format('H'); })->sortByDesc(function($group) { return $group->count(); })->keys()->first() ?? 'N/A' }}:00<br>
            @endif
            
            <strong>2. Deteksi Anomali & Risiko:</strong><br>
            • Transaksi adjustment: {{ $movements->where('type', 'adjustment')->count() }} kasus 
              @if($movements->where('type', 'adjustment')->count() > 0)
              (perlu investigasi jika > 5% dari total transaksi)
              @endif<br>
            • Frekuensi stok out: Monitoring diperlukan untuk item dengan movement out tinggi<br>
            • Konsistensi data: Semua transaksi tercatat dengan timestamp dan user tracking<br>
            
            <strong>3. Rekomendasi Strategis:</strong><br>
            @if($movements->where('type', 'in')->sum('quantity') < $movements->where('type', 'out')->sum('quantity'))
            • ⚠️ <strong>Alert:</strong> Net movement negatif - evaluasi supplier dan reorder policy<br>
            @endif
            • Optimasi inventory berdasarkan pola movement harian/mingguan<br>
            • Setup automatic reorder untuk item dengan movement out tinggi<br>
            • Review dan standardisasi proses adjustment untuk mengurangi discrepancy<br>
            
            <strong>4. Compliance & Kontrol:</strong><br>
            • Semua movement teraudit dengan user ID dan timestamp<br>
            • Traceability lengkap dari supplier hingga customer<br>
            • Dokumentasi memadai untuk audit internal/eksternal<br>
            • Sistem approval multi-level untuk transaksi high-value
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
                        Tanda Tangan Supervisor
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
                    Doc ID: {{ $document_id ?? md5($period . $movements->count()) }}<br>
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
