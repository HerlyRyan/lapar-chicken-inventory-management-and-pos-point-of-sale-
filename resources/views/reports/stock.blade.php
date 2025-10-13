@extends('layouts.app')

@section('title', 'Laporan Stok')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1" style="color: var(--primary-red);">
            <i class="bi bi-boxes me-2"></i>Laporan Stok
        </h1>
        <p class="text-muted mb-0">Monitoring inventory dan optimasi stok untuk keputusan pembelian</p>
    </div>
    <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
</div>

<!-- Filter Form -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-light">
        <h5 class="mb-0"><i class="bi bi-funnel me-2"></i>Filter Laporan</h5>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('reports.stock') }}">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Kategori Material</label>
                    <select name="category" class="form-select">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $value => $label)
                        <option value="{{ $value }}" {{ request('category') == $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status Stok</label>
                    <select name="stock_status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="low" {{ request('stock_status') == 'low' ? 'selected' : '' }}>Stok Rendah</option>
                        <option value="normal" {{ request('stock_status') == 'normal' ? 'selected' : '' }}>Stok Normal</option>
                        <option value="high" {{ request('stock_status') == 'high' ? 'selected' : '' }}>Stok Tinggi</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status Aktif</label>
                    <select name="is_active" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search me-1"></i>Filter
                        </button>
                        <button type="submit" name="format" value="pdf" class="btn btn-danger">
                            <i class="bi bi-file-pdf me-1"></i>PDF
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-lg-3 col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                        <i class="bi bi-box text-primary fs-5"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">Total Material</h6>
                        <h4 class="mb-0 text-primary">{{ $materials->count() }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3">
                        <i class="bi bi-currency-dollar text-success fs-5"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">Nilai Total Stok</h6>
                        <h4 class="mb-0 text-success">Rp {{ number_format($totalStockValue, 0, ',', '.') }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-warning bg-opacity-10 p-3 me-3">
                        <i class="bi bi-exclamation-triangle text-warning fs-5"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">Stok Rendah</h6>
                        <h4 class="mb-0 text-warning">{{ $materials->filter(function($m) { return $m->isLowStock(); })->count() }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-info bg-opacity-10 p-3 me-3">
                        <i class="bi bi-check-circle text-info fs-5"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">Stok Aman</h6>
                        <h4 class="mb-0 text-info">{{ $materials->filter(function($m) { return !$m->isLowStock(); })->count() }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Alert Stok Rendah -->
@php
$lowStockMaterials = $materials->filter(function($m) { return $m->isLowStock(); });
@endphp

@if($lowStockMaterials->count() > 0)
<div class="alert alert-warning border-0 shadow-sm mb-4">
    <div class="d-flex align-items-center">
        <i class="bi bi-exclamation-triangle fs-3 me-3"></i>
        <div>
            <h5 class="alert-heading mb-1">Peringatan Stok Rendah!</h5>
            <p class="mb-2">Ada {{ $lowStockMaterials->count() }} material yang stoknya di bawah minimum. Segera lakukan pembelian:</p>
            <div class="row">
                @foreach($lowStockMaterials->take(6) as $material)
                <div class="col-md-4">
                    <span class="badge bg-warning text-dark me-1">{{ $material->name }}</span>
                    <small class="text-muted">({{ $material->current_stock }}/{{ $material->minimum_stock }})</small>
                </div>
                @endforeach
            </div>
            @if($lowStockMaterials->count() > 6)
            <small class="text-muted">Dan {{ $lowStockMaterials->count() - 6 }} material lainnya...</small>
            @endif
        </div>
    </div>
</div>
@endif

<!-- Data Table -->
<div class="card border-0 shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Detail Inventory Material</h5>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-sm btn-outline-success" onclick="exportRecommendations()">
                <i class="bi bi-download me-1"></i>Rekomendasi Pembelian
            </button>
            <button type="button" class="btn btn-sm btn-success" onclick="verifyInventory()">
                <i class="bi bi-shield-check me-1"></i>Verifikasi Inventory
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Material</th>
                        <th>Kategori</th>
                        <th>Satuan</th>
                        <th>Stok Saat Ini</th>
                        <th>Stok Minimum</th>
                        <th>Status Stok</th>
                        <th>Nilai Stok</th>
                        <th>Pergerakan Terakhir</th>
                        <th>Rekomendasi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($materials as $material)
                    <tr class="{{ $material->isLowStock() ? 'table-warning' : '' }}">
                        <td>
                            <div>
                                <strong>{{ $material->name }}</strong>
                                <br>
                                <small class="text-muted">{{ $material->code }}</small>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-info">{{ $material->getCategoryLabel() }}</span>
                        </td>
                        <td>
                            @if($material->unit)
                            <span class="badge bg-secondary">{{ $material->unit->abbreviation }}</span>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <strong class="{{ $material->isLowStock() ? 'text-warning' : 'text-success' }}">
                                {{ number_format($material->current_stock, 0, ',', '.') }}
                            </strong>
                        </td>
                        <td>{{ number_format($material->minimum_stock, 0, ',', '.') }}</td>
                        <td>
                            @if($material->isLowStock())
                            <span class="badge bg-warning">
                                <i class="bi bi-exclamation-triangle me-1"></i>Rendah
                            </span>
                            @elseif($material->current_stock > ($material->minimum_stock * 3))
                            <span class="badge bg-info">
                                <i class="bi bi-arrow-up me-1"></i>Tinggi
                            </span>
                            @else
                            <span class="badge bg-success">
                                <i class="bi bi-check-circle me-1"></i>Normal
                            </span>
                            @endif
                        </td>
                        <td>
                            @php
                            $stockValue = $material->current_stock * ($material->average_cost ?? 0);
                            @endphp
                            Rp {{ number_format($stockValue, 0, ',', '.') }}
                        </td>
                        <td>
                            @if($material->stockMovements->first())
                            <small class="text-muted">
                                {{ $material->stockMovements->first()->movement_date->diffForHumans() }}
                                <br>
                                <span class="badge bg-{{ $material->stockMovements->first()->type === 'in' ? 'success' : 'danger' }} badge-sm">
                                    {{ $material->stockMovements->first()->type === 'in' ? 'Masuk' : 'Keluar' }}
                                </span>
                            </small>
                            @else
                            <span class="text-muted">Tidak ada</span>
                            @endif
                        </td>
                        <td>
                            @if($material->isLowStock())
                            @php
                            $recommendedOrder = ($material->minimum_stock * 2) - $material->current_stock;
                            @endphp
                            <button type="button" class="btn btn-sm btn-warning" 
                                    title="Disarankan beli {{ number_format($recommendedOrder, 0) }} unit">
                                <i class="bi bi-cart-plus me-1"></i>Beli {{ number_format($recommendedOrder, 0) }}
                            </button>
                            @else
                            <span class="text-success">
                                <i class="bi bi-check-circle me-1"></i>Stok Cukup
                            </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-4">
                            <div class="text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Tidak ada data material
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Analisis & Insights -->
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="bi bi-graph-up me-2"></i>Analisis Stok</h6>
            </div>
            <div class="card-body">
                @php
                $totalMaterials = $materials->count();
                $lowStockCount = $lowStockMaterials->count();
                $lowStockPercentage = $totalMaterials > 0 ? ($lowStockCount / $totalMaterials) * 100 : 0;
                @endphp
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>Persentase Stok Rendah</span>
                        <strong class="text-{{ $lowStockPercentage > 30 ? 'danger' : ($lowStockPercentage > 15 ? 'warning' : 'success') }}">
                            {{ number_format($lowStockPercentage, 1) }}%
                        </strong>
                    </div>
                    <div class="progress mt-1" style="height: 6px;">
                        <div class="progress-bar bg-{{ $lowStockPercentage > 30 ? 'danger' : ($lowStockPercentage > 15 ? 'warning' : 'success') }}" 
                             style="width: {{ $lowStockPercentage }}%"></div>
                    </div>
                </div>

                <hr>

                <h6 class="mb-2">Rekomendasi Aksi:</h6>
                <ul class="list-unstyled small">
                    @if($lowStockPercentage > 30)
                    <li class="text-danger mb-1">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        Kritis! Segera lakukan pembelian massal untuk {{ $lowStockCount }} material
                    </li>
                    @elseif($lowStockPercentage > 15)
                    <li class="text-warning mb-1">
                        <i class="bi bi-exclamation-circle me-1"></i>
                        Perhatian! Monitor dan rencanakan pembelian untuk material yang rendah
                    </li>
                    @else
                    <li class="text-success mb-1">
                        <i class="bi bi-check-circle me-1"></i>
                        Kondisi stok stabil, lanjutkan monitoring rutin
                    </li>
                    @endif
                    
                    <li class="text-info mb-1">
                        <i class="bi bi-info-circle me-1"></i>
                        Total nilai inventory: Rp {{ number_format($totalStockValue, 0, ',', '.') }}
                    </li>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="bi bi-pie-chart me-2"></i>Distribusi per Kategori</h6>
            </div>
            <div class="card-body">
                @php
                $categoryStats = $materials->groupBy('category')->map(function($items) {
                    return [
                        'count' => $items->count(),
                        'value' => $items->sum(function($item) { return $item->current_stock * ($item->average_cost ?? 0); })
                    ];
                });
                @endphp
                
                @foreach($categories as $value => $label)
                @php 
                $stats = $categoryStats[$value] ?? ['count' => 0, 'value' => 0];
                $percentage = $totalMaterials > 0 ? ($stats['count'] / $totalMaterials) * 100 : 0;
                @endphp
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>{{ $label }}</span>
                        <span>{{ $stats['count'] }} items</span>
                    </div>
                    <div class="progress mt-1" style="height: 6px;">
                        <div class="progress-bar" style="width: {{ $percentage }}%"></div>
                    </div>
                    <small class="text-muted">
                        Nilai: Rp {{ number_format($stats['value'], 0, ',', '.') }}
                    </small>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function exportRecommendations() {
    // Generate purchase recommendations
    const recommendations = [];
    document.querySelectorAll('tr[class*="table-warning"]').forEach(row => {
        const material = row.cells[0].textContent.trim();
        const recommendation = row.cells[8].textContent.trim();
        if (recommendation.includes('Beli')) {
            recommendations.push({material, recommendation});
        }
    });
    
    if (recommendations.length === 0) {
        alert('Tidak ada rekomendasi pembelian saat ini');
        return;
    }
    
    // Create and download recommendation file
    let content = 'REKOMENDASI PEMBELIAN MATERIAL\n';
    content += '=================================\n\n';
    content += 'Tanggal: ' + new Date().toLocaleDateString('id-ID') + '\n\n';
    
    recommendations.forEach((rec, index) => {
        content += `${index + 1}. ${rec.material}\n`;
        content += `   ${rec.recommendation}\n\n`;
    });
    
    const blob = new Blob([content], { type: 'text/plain' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'rekomendasi_pembelian_' + new Date().toISOString().split('T')[0] + '.txt';
    a.click();
    window.URL.revokeObjectURL(url);
}

function verifyInventory() {
    if (confirm('Yakin ingin memverifikasi laporan inventory ini?')) {
        fetch('/api/reports/verify', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ 
                report_type: 'stock',
                report_data: {
                    total_materials: {{ $materials->count() }},
                    total_value: {{ $totalStockValue }},
                    low_stock_count: {{ $lowStockMaterials->count() }}
                }
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Laporan inventory berhasil diverifikasi');
                // Update UI or reload page
                location.reload();
            }
        })
        .catch(error => {
            alert('Terjadi kesalahan saat verifikasi');
        });
    }
}
</script>
@endpush
