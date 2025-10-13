<div class="container-fluid p-0">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <h5 class="mb-1">
                <i class="bi bi-clipboard-check text-warning me-2"></i>
                Detail Pengajuan Produksi
            </h5>
            <div class="small text-muted">Kode: <strong>{{ $productionRequest->request_code }}</strong></div>
        </div>
        <div>
            <span class="badge bg-{{ $productionRequest->status_color }}">{{ $productionRequest->status_label }}</span>
        </div>
    </div>

    <!-- Basic Info -->
    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="mb-2"><span class="text-muted small">Peruntukan</span><br><strong>{{ $productionRequest->purpose ?? '-' }}</strong></div>
                    <div class="mb-2"><span class="text-muted small">Pemohon</span><br>{{ $productionRequest->requestedBy->name ?? '-' }}</div>
                    <div class="mb-2"><span class="text-muted small">Dibuat</span><br>{{ $productionRequest->created_at?->format('d/m/Y H:i') }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="mb-2"><span class="text-muted small">Estimasi Output</span><br>{{ $productionRequest->estimated_output_quantity ? number_format($productionRequest->estimated_output_quantity, 0, ',', '.') . ' unit' : '-' }}</div>
                    <div class="mb-2"><span class="text-muted small">Total Biaya Bahan Mentah</span><br><strong>Rp {{ number_format($productionRequest->total_raw_material_cost, 0, ',', '.') }}</strong></div>
                    @if(!empty($productionRequest->approval_notes))
                        <div class="mb-2"><span class="text-muted small">Catatan</span><br>{{ $productionRequest->approval_notes }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Raw Materials Requirement -->
    <div class="card">
        <div class="card-header py-2">
            <strong><i class="bi bi-box-seam me-2"></i>Bahan Mentah Dibutuhkan</strong>
        </div>
        <div class="card-body p-0">
            @if($productionRequest->items->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Bahan</th>
                                <th class="text-center">Diminta</th>
                                <th class="text-center">Tersedia</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($productionRequest->items as $item)
                                @php
                                    $rm = $item->rawMaterial;
                                    $avail = $rm?->current_stock ?? 0;
                                    $need = $item->requested_quantity;
                                    $ok = $avail >= $need;
                                @endphp
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ $rm?->name }}</div>
                                        <small class="text-muted">{{ $rm?->unit?->name }}</small>
                                    </td>
                                    <td class="text-center">{{ number_format($need, 3, ',', '.') }}</td>
                                    <td class="text-center">{{ number_format($avail, 3, ',', '.') }}</td>
                                    <td class="text-center">
                                        @if($ok)
                                            <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Cukup</span>
                                        @else
                                            <span class="badge bg-danger"><i class="bi bi-exclamation-triangle me-1"></i>Kurang {{ number_format($need - $avail, 3, ',', '.') }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center text-muted py-3">Tidak ada item bahan mentah.</div>
            @endif
        </div>
    </div>
</div>
