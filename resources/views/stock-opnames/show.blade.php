@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Detail Stok Opname</h1>
        <a href="{{ route('stock-opnames.index') }}" class="btn btn-light">Kembali</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <div class="row g-2">
                <div class="col-md-3"><strong>Nomor:</strong> {{ $stockOpname->opname_number }}</div>
                <div class="col-md-3"><strong>Status:</strong> <span class="badge bg-success">Disubmit</span></div>
                <div class="col-md-3"><strong>Jenis:</strong> {{ $stockOpname->product_type === 'raw' ? 'Bahan Baku' : 'Setengah Jadi' }}</div>
                <div class="col-md-3"><strong>Cabang:</strong> {{ $stockOpname->branch->name ?? '-' }}</div>
            </div>
            <div class="row g-2 mt-2">
                <div class="col-md-3"><strong>Total Item:</strong> {{ $stockOpname->total_items }}</div>
                <div class="col-md-3"><strong>Cocok:</strong> {{ $stockOpname->matched_count }}</div>
                <div class="col-md-3"><strong>Lebih:</strong> {{ $stockOpname->over_count }}</div>
                <div class="col-md-3"><strong>Kurang:</strong> {{ $stockOpname->under_count }}</div>
            </div>
            <div class="row g-2 mt-2">
                <div class="col-md-3"><strong>Persentase Cocok:</strong> {{ number_format((float)$stockOpname->match_percentage, 2) }}%</div>
                <div class="col-md-9"><strong>Catatan:</strong> {{ $stockOpname->notes ?: '-' }}</div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm align-middle table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Kode</th>
                            <th>Nama</th>
                            <th class="text-end">Sistem</th>
                            <th class="text-end">Nyata</th>
                            <th class="text-end">Selisih</th>
                            <th class="text-end">Nilai Selisih</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($stockOpname->items as $item)
                        @php
                            $status = $item->status;
                            $badge = $status === 'matched' ? 'bg-success' : ($status === 'over' ? 'bg-info' : 'bg-danger');
                            $label = $status === 'matched' ? 'Cocok' : ($status === 'over' ? 'Lebih' : 'Kurang');
                        @endphp
                        <tr>
                            <td>{{ $item->item_code }}</td>
                            <td>{{ $item->item_name }} @if($item->unit_abbr)<span class="text-muted">({{ $item->unit_abbr }})</span>@endif</td>
                            <td class="text-end">{{ number_format((int)$item->system_quantity, 0) }}</td>
                            <td class="text-end">{{ number_format((int)$item->real_quantity, 0) }}</td>
                            <td class="text-end">{{ number_format((int)$item->difference, 0) }}</td>
                            <td class="text-end">Rp {{ number_format((float)$item->value_difference, 2) }}</td>
                            <td><span class="badge {{ $badge }}">{{ $label }}</span></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
