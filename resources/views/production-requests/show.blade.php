@extends('layouts.app')

@section('title', 'Detail Pengajuan Produksi')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="bi bi-eye text-info me-2"></i>
                Detail Pengajuan Produksi
            </h1>
            <p class="text-muted small mb-0">Kode: <strong>{{ $productionRequest->request_code }}</strong></p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('production-requests.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i>
                Kembali
            </a>
            @if($productionRequest->isPending())
                <a href="{{ route('production-requests.edit', $productionRequest) }}" class="btn btn-warning">
                    <i class="bi bi-pencil me-1"></i>
                    Edit
                </a>
            @endif
        </div>
    </div>

    <!-- Status + Meta -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div>
                            <span class="badge bg-{{ $productionRequest->status_color }} fs-6">
                                {{ $productionRequest->status_label }}
                            </span>
                        </div>
                        <div class="text-muted small">
                            Dibuat: {{ $productionRequest->created_at->format('d/m/Y H:i') }}
                            @if($productionRequest->updated_at)
                                • Diperbarui: {{ $productionRequest->updated_at->format('d/m/Y H:i') }}
                            @endif
                        </div>
                    </div>
                    <div class="mt-3 small">
                        <div><strong>Pemohon:</strong> {{ $productionRequest->requestedBy->name ?? '-' }}</div>
                        @if($productionRequest->approvedBy)
                            <div><strong>Disetujui oleh:</strong> {{ $productionRequest->approvedBy->name }} • {{ optional($productionRequest->approved_at)->format('d/m/Y H:i') }}</div>
                        @endif
                        @if($productionRequest->productionStartedBy)
                            <div><strong>Produksi Dimulai oleh:</strong> {{ $productionRequest->productionStartedBy->name }} • {{ optional($productionRequest->production_started_at)->format('d/m/Y H:i') }}</div>
                        @endif
                        @if($productionRequest->productionCompletedBy)
                            <div><strong>Produksi Selesai oleh:</strong> {{ $productionRequest->productionCompletedBy->name }} • {{ optional($productionRequest->completed_at)->format('d/m/Y H:i') }}</div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Items -->
            <div class="card shadow-sm mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="bi bi-box-seam me-2"></i>
                        Bahan Mentah yang Diminta
                    </h6>
                </div>
                <div class="card-body">
                    @if($productionRequest->items->count())
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                <tr>
                                    <th>Bahan Mentah</th>
                                    <th class="text-end">Jumlah</th>
                                    <th>Satuan</th>
                                    <th class="text-end">Harga/Unit</th>
                                    <th class="text-end">Total</th>
                                    <th>Catatan</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($productionRequest->items as $item)
                                    <tr>
                                        <td>{{ $item->rawMaterial->name ?? '-' }}</td>
                                        <td class="text-end">{{ number_format($item->requested_quantity, 0, ',', '.') }}</td>
                                        <td>{{ $item->rawMaterial->unit->name ?? '-' }}</td>
                                        <td class="text-end">Rp {{ number_format($item->unit_cost, 0, ',', '.') }}</td>
                                        <td class="text-end">Rp {{ number_format($item->total_cost ?? ($item->requested_quantity * $item->unit_cost), 0, ',', '.') }}</td>
                                        <td>{{ $item->notes }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                                <tfoot>
                                <tr>
                                    <th colspan="4" class="text-end">Total Biaya Bahan</th>
                                    <th class="text-end">Rp {{ number_format($productionRequest->total_raw_material_cost, 0, ',', '.') }}</th>
                                    <th></th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <div class="text-muted">Tidak ada item bahan mentah.</div>
                    @endif
                </div>
            </div>

            <!-- Planned Outputs -->
            <div class="card shadow-sm">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-success">
                        <i class="bi bi-diagram-3 me-2"></i>
                        Target Output Bahan Setengah Jadi
                    </h6>
                </div>
                <div class="card-body">
                    @if($productionRequest->outputs->count())
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                <tr>
                                    <th>Produk</th>
                                    <th class="text-end">Jumlah Rencana</th>
                                    <th>Satuan</th>
                                    <th>Catatan</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($productionRequest->outputs as $out)
                                    <tr>
                                        <td>{{ $out->semiFinishedProduct->name ?? '-' }}</td>
                                        <td class="text-end">{{ number_format($out->planned_quantity, 0, ',', '.') }}</td>
                                        <td>{{ $out->semiFinishedProduct->unit ?? '-' }}</td>
                                        <td>{{ $out->notes }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-muted">Belum ada target output yang direncanakan.</div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <!-- Notes and summary -->
            <div class="card shadow-sm mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-info">
                        <i class="bi bi-info-circle me-2"></i>
                        Ringkasan
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-2"><strong>Total Biaya Bahan:</strong> <div>Rp {{ number_format($productionRequest->total_raw_material_cost, 0, ',', '.') }}</div></div>
                    @if($productionRequest->estimated_output_quantity)
                        <div class="mb-2"><strong>Estimasi Output:</strong> <div>{{ number_format($productionRequest->estimated_output_quantity, 0, ',', '.') }} unit</div></div>
                    @endif
                    @if($productionRequest->purpose)
                        <div class="mb-2"><strong>Peruntukan:</strong> <div class="text-muted">{{ $productionRequest->purpose }}</div></div>
                    @endif
                    @if($productionRequest->notes)
                        <div class=""><strong>Catatan:</strong> <div class="text-muted">{{ $productionRequest->notes }}</div></div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
