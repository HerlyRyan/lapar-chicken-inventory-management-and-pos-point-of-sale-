@extends('layouts.app')

@section('title', 'Laporan Pergerakan Stok')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1" style="color: var(--primary-red);">
            <i class="bi bi-arrow-left-right me-2"></i>Laporan Pergerakan Stok
        </h1>
        <p class="text-muted mb-0">Audit trail dan analisis pola penggunaan material untuk optimasi</p>
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
        <form method="GET" action="{{ route('reports.stock-movement') }}">
            <div class="row g-3">
                <div class="col-md-2">
                    <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                    <input type="date" name="start_date" class="form-control" 
                           value="{{ request('start_date', date('Y-m-01')) }}" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Tanggal Akhir <span class="text-danger">*</span></label>
                    <input type="date" name="end_date" class="form-control" 
                           value="{{ request('end_date', date('Y-m-d')) }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Material</label>
                    <select name="material_id" class="form-select">
                        <option value="">Semua Material</option>
                        @foreach($materials as $material)
                        <option value="{{ $material->id }}" {{ request('material_id') == $material->id ? 'selected' : '' }}>
                            {{ $material->name }} ({{ $material->code }})
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Tipe Pergerakan</label>
                    <select name="movement_type" class="form-select">
                        <option value="">Semua Tipe</option>
                        @foreach($movementTypes as $value => $label)
                        <option value="{{ $value }}" {{ request('movement_type') == $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                        @endforeach
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

@if(request('start_date') && request('end_date'))
<!-- Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-lg-3 col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                        <i class="bi bi-list-ul text-primary fs-5"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">Total Pergerakan</h6>
                        <h4 class="mb-0 text-primary">{{ $movements->count() }}</h4>
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
                        <i class="bi bi-arrow-down text-success fs-5"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">Material Masuk</h6>
                        <h4 class="mb-0 text-success">{{ $movements->where('type', 'in')->count() }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-danger bg-opacity-10 p-3 me-3">
                        <i class="bi bi-arrow-up text-danger fs-5"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">Material Keluar</h6>
                        <h4 class="mb-0 text-danger">{{ $movements->where('type', 'out')->count() }}</h4>
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
                        <i class="bi bi-arrow-repeat text-warning fs-5"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">Penyesuaian</h6>
                        <h4 class="mb-0 text-warning">{{ $movements->where('type', 'adjustment')->count() }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="bi bi-graph-up me-2"></i>Tren Pergerakan Stok ({{ $period }})</h6>
            </div>
            <div class="card-body">
                <canvas id="movementChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="bi bi-pie-chart me-2"></i>Distribusi Tipe</h6>
            </div>
            <div class="card-body">
                @foreach($movementTypes as $type => $label)
                @php 
                $count = $movements->where('type', $type)->count();
                $percentage = $movements->count() > 0 ? ($count / $movements->count()) * 100 : 0;
                $colorClass = $type === 'in' ? 'success' : ($type === 'out' ? 'danger' : 'warning');
                @endphp
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>{{ $label }}</span>
                        <span class="text-{{ $colorClass }}">{{ $count }}</span>
                    </div>
                    <div class="progress mt-1" style="height: 8px;">
                        <div class="progress-bar bg-{{ $colorClass }}" style="width: {{ $percentage }}%"></div>
                    </div>
                    <small class="text-muted">{{ number_format($percentage, 1) }}% dari total</small>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Data Table -->
<div class="card border-0 shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Detail Pergerakan Stok ({{ $period }})</h5>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-sm btn-outline-info" onclick="exportAuditTrail()">
                <i class="bi bi-download me-1"></i>Export Audit Trail
            </button>
            <button type="button" class="btn btn-sm btn-success" onclick="verifyMovements()">
                <i class="bi bi-shield-check me-1"></i>Verifikasi Laporan
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Tanggal & Waktu</th>
                        <th>Material</th>
                        <th>Tipe</th>
                        <th>Kuantitas</th>
                        <th>Satuan</th>
                        <th>Stok Sebelum</th>
                        <th>Stok Sesudah</th>
                        <th>User</th>
                        <th>Cabang</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($movements as $movement)
                    <tr>
                        <td>
                            <div class="text-nowrap">
                                <strong>{{ $movement->movement_date->format('d/m/Y') }}</strong>
                                <br>
                                <small class="text-muted">{{ $movement->movement_date->format('H:i:s') }}</small>
                            </div>
                        </td>
                        <td>
                            <div>
                                <strong>{{ $movement->material->name }}</strong>
                                <br>
                                <small class="text-muted">{{ $movement->material->code }}</small>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-{{ $movement->type === 'in' ? 'success' : ($movement->type === 'out' ? 'danger' : 'warning') }}">
                                <i class="bi bi-arrow-{{ $movement->type === 'in' ? 'down' : ($movement->type === 'out' ? 'up' : 'repeat') }} me-1"></i>
                                {{ $movementTypes[$movement->type] ?? $movement->type }}
                            </span>
                        </td>
                        <td>
                            <strong class="text-{{ $movement->type === 'in' ? 'success' : ($movement->type === 'out' ? 'danger' : 'warning') }}">
                                {{ $movement->type === 'out' ? '-' : '+' }}{{ number_format($movement->quantity, 0, ',', '.') }}
                            </strong>
                        </td>
                        <td>
                            @if($movement->material->unit)
                            <span class="badge bg-secondary">{{ $movement->material->unit->abbreviation }}</span>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ number_format($movement->stock_before ?? 0, 0) }}</td>
                        <td>
                            <strong>{{ number_format($movement->stock_after ?? 0, 0) }}</strong>
                        </td>
                        <td>
                            <div>
                                {{ $movement->user->name ?? 'System' }}
                                <br>
                                <small class="text-muted">{{ $movement->user->email ?? '' }}</small>
                            </div>
                        </td>
                        <td>{{ $movement->branch->name ?? '-' }}</td>
                        <td>
                            <span class="text-muted small">{{ $movement->notes ?? 'Tidak ada keterangan' }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-4">
                            <div class="text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Tidak ada pergerakan stok untuk periode yang dipilih
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Analysis Insights -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="bi bi-lightbulb me-2"></i>Insights & Analisis Pola</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-primary">Pola Penggunaan Material</h6>
                        @php
                        $materialUsage = $movements->where('type', 'out')->groupBy('material_id')->map(function($items) {
                            return [
                                'material' => $items->first()->material->name,
                                'total_used' => $items->sum('quantity'),
                                'frequency' => $items->count()
                            ];
                        })->sortByDesc('total_used')->take(5);
                        @endphp
                        
                        <ul class="list-unstyled">
                            @foreach($materialUsage as $usage)
                            <li class="mb-2">
                                <div class="d-flex justify-content-between">
                                    <span>{{ $usage['material'] }}</span>
                                    <span class="text-danger">-{{ number_format($usage['total_used'], 0) }}</span>
                                </div>
                                <small class="text-muted">{{ $usage['frequency'] }} kali transaksi keluar</small>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    
                    <div class="col-md-6">
                        <h6 class="text-success">Rekomendasi Optimasi</h6>
                        <ul class="list-unstyled small">
                            @if($movements->where('type', 'out')->count() > $movements->where('type', 'in')->count())
                            <li class="text-warning mb-2">
                                <i class="bi bi-exclamation-triangle me-1"></i>
                                Material keluar lebih banyak dari masuk. Monitor stok dengan ketat.
                            </li>
                            @endif
                            
                            @if($movements->where('type', 'adjustment')->count() > ($movements->count() * 0.1))
                            <li class="text-info mb-2">
                                <i class="bi bi-info-circle me-1"></i>
                                Banyak penyesuaian stok. Review prosedur pencatatan.
                            </li>
                            @endif
                            
                            <li class="text-success mb-2">
                                <i class="bi bi-check-circle me-1"></i>
                                Material paling aktif: {{ $materialUsage->first()['material'] ?? 'N/A' }}
                            </li>
                            
                            <li class="text-primary mb-2">
                                <i class="bi bi-graph-up me-1"></i>
                                Rata-rata pergerakan: {{ $movements->count() > 0 ? number_format($movements->count() / max(1, now()->diffInDays(request('start_date'))), 1) : 0 }} transaksi/hari
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
@if(request('start_date') && request('end_date'))
// Movement Chart
const ctx = document.getElementById('movementChart').getContext('2d');
const movementChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: @json($movements->groupBy(function($item) { return $item->movement_date->format('Y-m-d'); })->keys()),
        datasets: [
            {
                label: 'Material Masuk',
                data: @json($movements->where('type', 'in')->groupBy(function($item) { return $item->movement_date->format('Y-m-d'); })->map->count()->values()),
                borderColor: 'rgb(34, 197, 94)',
                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                tension: 0.1
            },
            {
                label: 'Material Keluar',
                data: @json($movements->where('type', 'out')->groupBy(function($item) { return $item->movement_date->format('Y-m-d'); })->map->count()->values()),
                borderColor: 'rgb(239, 68, 68)',
                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                tension: 0.1
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top',
            },
            title: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
@endif

function exportAuditTrail() {
    const movements = @json($movements->values());
    
    if (movements.length === 0) {
        alert('Tidak ada data untuk diekspor');
        return;
    }
    
    let content = 'AUDIT TRAIL PERGERAKAN STOK\n';
    content += '============================\n\n';
    content += 'Periode: {{ $period ?? "" }}\n';
    content += 'Diekspor: ' + new Date().toLocaleString('id-ID') + '\n\n';
    
    content += 'RINGKASAN:\n';
    content += '- Total Pergerakan: {{ $movements->count() }}\n';
    content += '- Material Masuk: {{ $movements->where("type", "in")->count() }}\n';
    content += '- Material Keluar: {{ $movements->where("type", "out")->count() }}\n';
    content += '- Penyesuaian: {{ $movements->where("type", "adjustment")->count() }}\n\n';
    
    content += 'DETAIL TRANSAKSI:\n';
    content += '=================\n';
    
    movements.forEach((movement, index) => {
        content += `${index + 1}. ${movement.movement_date}\n`;
        content += `   Material: ${movement.material.name} (${movement.material.code})\n`;
        content += `   Tipe: ${movement.type.toUpperCase()}\n`;
        content += `   Kuantitas: ${movement.quantity}\n`;
        content += `   User: ${movement.user ? movement.user.name : 'System'}\n`;
        content += `   Keterangan: ${movement.notes || 'Tidak ada'}\n\n`;
    });
    
    const blob = new Blob([content], { type: 'text/plain' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'audit_trail_pergerakan_stok_' + new Date().toISOString().split('T')[0] + '.txt';
    a.click();
    window.URL.revokeObjectURL(url);
}

function verifyMovements() {
    if (confirm('Yakin ingin memverifikasi laporan pergerakan stok ini?')) {
        fetch('/api/reports/verify', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ 
                report_type: 'stock_movement',
                report_data: {
                    total_movements: {{ $movements->count() }},
                    period: '{{ $period ?? "" }}',
                    in_count: {{ $movements->where('type', 'in')->count() }},
                    out_count: {{ $movements->where('type', 'out')->count() }}
                }
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Laporan pergerakan stok berhasil diverifikasi');
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
