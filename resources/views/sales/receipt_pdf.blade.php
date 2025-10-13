<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk - {{ $sale->sale_number }}</title>
    <style>
        @page { margin: 0; }
        html, body {
            margin: 0;
            padding: 0;
            width: 80mm; /* canvas page */
            background: #ffffff;
            color: #000000;
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.35;
        }
        .wrap {
            box-sizing: border-box;
            width: 72mm; /* safe printable width inside 80mm */
            margin: 0 auto;
            padding: 6mm 3mm 8mm 3mm;
        }
        .title { text-align: center; font-weight: 700; font-size: 12pt; }
        .branch, .meta { text-align: center; font-size: 9pt; }
        .sep { border-top: 1px dashed #000; margin: 6px 0; }

        .row { display: grid; grid-template-columns: 1fr auto; gap: 2mm; margin-bottom: 2mm; }
        .name { font-weight: 600; word-break: break-word; }
        .sub { font-size: 9pt; opacity: 0.9; }
        .amt { text-align: right; }

        .totals { margin-top: 4mm; }
        .line { display: flex; justify-content: space-between; margin: 2px 0; }
        .total { font-weight: 700; font-size: 11pt; }

        .footer { text-align: center; margin-top: 6mm; font-size: 9pt; }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="title">Lapar Chicken</div>
        <div class="branch">Cabang: {{ $sale->branch->name ?? 'N/A' }}</div>
        <div class="meta">
            No: {{ $sale->sale_number }}<br>
            Tgl: {{ $sale->created_at->format('d/m/Y H:i') }} · Kasir: {{ $sale->user->name ?? '-' }}
        </div>

        <div class="sep"></div>

        @foreach($sale->items as $item)
            <div class="row">
                <div>
                    <div class="name">{{ $item->item_name }}</div>
                    <div class="sub">{{ $item->quantity }} x Rp {{ number_format($item->unit_price, 0, ',', '.') }}</div>
                </div>
                <div class="amt">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</div>
            </div>
        @endforeach

        <div class="sep"></div>

        <div class="totals">
            <div class="line">
                <span>Subtotal</span>
                <span>Rp {{ number_format($sale->subtotal_amount, 0, ',', '.') }}</span>
            </div>
            @if($sale->discount_amount > 0)
                <div class="line">
                    <span>Diskon @if($sale->discount_type === 'percentage') ({{ (float) $sale->discount_value }}%) @endif</span>
                    <span>-Rp {{ number_format($sale->discount_amount, 0, ',', '.') }}</span>
                </div>
            @endif
            <div class="line total">
                <span>Total Bayar</span>
                <span>Rp {{ number_format($sale->final_amount, 0, ',', '.') }}</span>
            </div>
            <div class="line">
                <span>Metode</span>
                <span>{{ $sale->payment_method === 'cash' ? 'Tunai' : 'QRIS' }}</span>
            </div>
            <div class="line">
                <span>Dibayar</span>
                <span>Rp {{ number_format($sale->paid_amount, 0, ',', '.') }}</span>
            </div>
            @if(($sale->change_amount ?? 0) > 0)
                <div class="line">
                    <span>Kembalian</span>
                    <span>Rp {{ number_format($sale->change_amount, 0, ',', '.') }}</span>
                </div>
            @endif
        </div>

        <div class="sep"></div>

        <div class="footer">
            Terima kasih sudah berbelanja!<br>
            Simpan struk ini sebagai bukti transaksi.
        </div>
    </div>
</body>
</html>
