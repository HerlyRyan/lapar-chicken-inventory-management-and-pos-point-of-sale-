@extends('layouts.app')

@section('title', 'Persetujuan Produksi')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="bi bi-clipboard-check text-warning me-2"></i>
                Persetujuan Produksi
            </h1>
            <p class="text-muted small mb-0">Review dan setujui pengajuan produksi dari Kepala Produksi</p>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-xl-4 col-md-6 mb-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Menunggu Persetujuan
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $productionRequests->where('status', 'pending')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-clock-history fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Disetujui
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $productionRequests->where('status', 'approved')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-3">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Ditolak
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $productionRequests->where('status', 'rejected')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-x-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('production-approvals.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu Persetujuan</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                            <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>Sedang Diproduksi</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="search" class="form-label">Pencarian</label>
                        <input type="text" name="search" id="search" class="form-control"
                               placeholder="Cari berdasarkan kode atau peruntukan..."
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="sort_by" class="form-label">Urutkan Berdasarkan</label>
                        <select name="sort_by" id="sort_by" class="form-select">
                            <option value="created_at" {{ request('sort_by', 'created_at') == 'created_at' ? 'selected' : '' }}>Tanggal Pengajuan</option>
                            <option value="approved_at" {{ request('sort_by') == 'approved_at' ? 'selected' : '' }}>Tanggal Persetujuan</option>
                            <option value="request_code" {{ request('sort_by') == 'request_code' ? 'selected' : '' }}>Kode</option>
                            <option value="total_raw_material_cost" {{ request('sort_by') == 'total_raw_material_cost' ? 'selected' : '' }}>Total Biaya</option>
                            <option value="status" {{ request('sort_by') == 'status' ? 'selected' : '' }}>Status</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="sort_dir" class="form-label">Arah</label>
                        <select name="sort_dir" id="sort_dir" class="form-select">
                            <option value="desc" {{ request('sort_dir', 'desc') == 'desc' ? 'selected' : '' }}>Terbaru</option>
                            <option value="asc" {{ request('sort_dir') == 'asc' ? 'selected' : '' }}>Terlama</option>
                        </select>
                        <div class="d-flex gap-2 mt-2">
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="bi bi-funnel me-1"></i> Filter
                            </button>
                            <a href="{{ route('production-approvals.index') }}" class="btn btn-outline-secondary">Reset</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Production Requests Table -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="bi bi-list-check me-2"></i>
                Daftar Pengajuan Produksi
            </h6>
        </div>
        <div class="card-body">
            @if($productionRequests->count() > 0)
                <!-- Bulk Actions Toolbar -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-outline-success btn-sm" onclick="openBulkModal('approve')">
                            <i class="bi bi-check2-square me-1"></i> Setujui Terpilih
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="openBulkModal('reject')">
                            <i class="bi bi-x-square me-1"></i> Tolak Terpilih
                        </button>
                        <span class="text-muted small ms-2" id="selectedCount">0 dipilih</span>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="32"><input type="checkbox" id="selectAll"></th>
                                <th>Kode</th>
                                <th>Peruntukan</th>
                                <th>Pemohon</th>
                                <th>Bahan Mentah</th>
                                <th>Total Biaya</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($productionRequests as $request)
                            <tr class="{{ $request->isPending() ? 'table-warning' : '' }}">
                                <td class="text-center">
                                    <input type="checkbox" class="row-check" value="{{ $request->id }}" {{ $request->isPending() ? '' : 'disabled' }}>
                                </td>
                                <td>
                                    <strong>{{ $request->request_code }}</strong>
                                    @if($request->isPending())
                                        <span class="badge bg-warning text-dark ms-1">BARU</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-bold">{{ Str::limit($request->purpose, 40) }}</div>
                                    @if($request->estimated_output_quantity)
                                        <small class="text-muted">
                                            Target: {{ number_format($request->estimated_output_quantity, 0, ',', '.') }} unit
                                        </small>
                                    @endif
                                </td>
                                <td>{{ $request->requestedBy->name ?? '-' }}</td>
                                <td>
                                    <small class="text-muted">
                                        {{ $request->items->count() }} item bahan mentah
                                        @if($request->items->count() > 0)
                                            <br>
                                            @foreach($request->items->take(2) as $item)
                                                • {{ $item->rawMaterial->name ?? '' }}<br>
                                            @endforeach
                                            @if($request->items->count() > 2)
                                                <span class="text-primary">+{{ $request->items->count() - 2 }} lainnya</span>
                                            @endif
                                        @endif
                                    </small>
                                </td>
                                <td>
                                    <span class="fw-bold">Rp {{ number_format($request->total_raw_material_cost, 0, ',', '.') }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $request->status_color }} fs-6">
                                        {{ $request->status_label }}
                                    </span>
                                    @if($request->approved_at)
                                        <br><small class="text-muted">{{ $request->approved_at->format('d/m/Y H:i') }}</small>
                                    @endif
                                </td>
                                <td>{{ $request->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <div class="btn-group-vertical" role="group">
                                        <a href="{{ route('production-approvals.show', $request) }}?modal=1" 
                                           class="btn btn-sm btn-outline-info mb-1 btn-review" data-id="{{ $request->id }}" title="Review Detail">
                                            <i class="bi bi-eye me-1"></i>
                                            Review
                                        </a>
                                        @if($request->isPending())
                                            <button type="button" class="btn btn-sm btn-success mb-1" 
                                                    onclick="showApprovalModal({{ $request->id }}, 'approve')" title="Setujui">
                                                <i class="bi bi-check-circle me-1"></i>
                                                Setujui
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger" 
                                                    onclick="showApprovalModal({{ $request->id }}, 'reject')" title="Tolak">
                                                <i class="bi bi-x-circle me-1"></i>
                                                Tolak
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted small">
                        Menampilkan {{ $productionRequests->firstItem() }} - {{ $productionRequests->lastItem() }} 
                        dari {{ $productionRequests->total() }} pengajuan
                    </div>
                    {{ $productionRequests->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-inbox text-muted" style="font-size: 4rem;"></i>
                    <h5 class="mt-3 text-muted">Tidak Ada Pengajuan</h5>
                    <p class="text-muted">
                        @if(request()->hasAny(['status', 'search']))
                            Tidak ada pengajuan yang sesuai dengan filter yang dipilih.
                        @else
                            Belum ada pengajuan produksi yang perlu direview.
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Detail Modal -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel">Review Pengajuan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="detailModalBody">
                <div class="text-center text-muted py-5">
                    <div class="spinner-border text-secondary" role="status"><span class="visually-hidden">Loading...</span></div>
                    <div class="mt-2">Memuat detail...</div>
                </div>
            </div>
        </div>
    </div>
    </div>

<!-- Approval Modal -->
<div class="modal fade" id="approvalModal" tabindex="-1" aria-labelledby="approvalModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="approvalModalLabel">Konfirmasi Persetujuan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="approvalForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div id="approvalMessage"></div>
                    
                    <div class="mb-3">
                        <label for="approval_notes" class="form-label">Catatan <span id="required-indicator"></span></label>
                        <textarea name="approval_notes" id="approval_notes" class="form-control" rows="3" 
                                  placeholder="Masukkan catatan atau alasan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn" id="confirmButton">Konfirmasi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bulk Notes Modal -->
<div class="modal fade" id="bulkModal" tabindex="-1" aria-labelledby="bulkModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bulkModalLabel">Proses Massal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="bulkForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-secondary py-2"><span id="bulkCount">0</span> pengajuan dipilih.</div>
                    <div class="mb-3">
                        <label for="bulk_notes" class="form-label">Catatan</label>
                        <textarea id="bulk_notes" name="approval_notes" class="form-control" rows="3" placeholder="Opsional untuk setujui, wajib saat tolak"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn" id="bulkConfirmBtn">Proses</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    .border-left-warning {
        border-left: 0.25rem solid #f6c23e !important;
    }
    .border-left-success {
        border-left: 0.25rem solid #1cc88a !important;
    }
    .border-left-danger {
        border-left: 0.25rem solid #e74a3b !important;
    }
    
    .table-warning {
        background-color: rgba(255, 243, 205, 0.5);
    }
    
    .btn-group-vertical .btn {
        margin-bottom: 2px;
    }
    
    .btn-group-vertical .btn:last-child {
        margin-bottom: 0;
    }
</style>
@endpush

@push('scripts')
<script>
// Review detail via AJAX
document.addEventListener('click', function(e) {
    const a = e.target.closest('a.btn-review');
    if (!a) return;
    e.preventDefault();
    const url = a.getAttribute('href');
    const body = document.getElementById('detailModalBody');
    body.innerHTML = `<div class="text-center text-muted py-5"><div class=\"spinner-border text-secondary\" role=\"status\"><span class=\"visually-hidden\">Loading...</span></div><div class=\"mt-2\">Memuat detail...</div></div>`;
    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }})
        .then(r => r.text())
        .then(html => { body.innerHTML = html; })
        .catch(() => { body.innerHTML = '<div class="alert alert-danger">Gagal memuat detail.</div>'; });
    new bootstrap.Modal(document.getElementById('detailModal')).show();
});

// Selection handling
const selectAll = document.getElementById('selectAll');
const selectedCount = document.getElementById('selectedCount');
function updateSelectedCount() {
    const ids = Array.from(document.querySelectorAll('.row-check:checked')).map(i => i.value);
    selectedCount.textContent = ids.length + ' dipilih';
}
document.addEventListener('change', function(e){
    if (e.target && e.target.matches('#selectAll')) {
        const checks = document.querySelectorAll('.row-check:not(:disabled)');
        checks.forEach(c => c.checked = e.target.checked);
        updateSelectedCount();
    } else if (e.target && e.target.matches('.row-check')) {
        updateSelectedCount();
    }
});

let bulkAction = null;
function openBulkModal(action) {
    const ids = Array.from(document.querySelectorAll('.row-check:checked')).map(i => i.value);
    if (ids.length === 0) {
        alert('Pilih minimal satu pengajuan (status pending).');
        return;
    }
    bulkAction = action;
    const form = document.getElementById('bulkForm');
    form.action = action === 'approve' ? '{{ route('production-approvals.index') }}/bulk-approve' : '{{ route('production-approvals.index') }}/bulk-reject';
    document.getElementById('bulk_notes').required = action === 'reject';
    document.getElementById('bulkCount').textContent = ids.length;
    // Clear previous id inputs
    Array.from(form.querySelectorAll('input[name="ids[]"]')).forEach(n => n.remove());
    // Append new ids
    ids.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'ids[]';
        input.value = id;
        form.appendChild(input);
    });
    const btn = document.getElementById('bulkConfirmBtn');
    btn.className = 'btn ' + (action === 'approve' ? 'btn-success' : 'btn-danger');
    btn.textContent = action === 'approve' ? 'Setujui' : 'Tolak';
    new bootstrap.Modal(document.getElementById('bulkModal')).show();
}
window.openBulkModal = openBulkModal;

function showApprovalModal(requestId, action) {
    const modal = new bootstrap.Modal(document.getElementById('approvalModal'));
    const form = document.getElementById('approvalForm');
    const title = document.getElementById('approvalModalLabel');
    const message = document.getElementById('approvalMessage');
    const button = document.getElementById('confirmButton');
    const requiredIndicator = document.getElementById('required-indicator');
    const notesField = document.getElementById('approval_notes');
    
    if (action === 'approve') {
        title.textContent = 'Setujui Pengajuan Produksi';
        message.innerHTML = `
            <div class="alert alert-success">
                <i class="bi bi-check-circle me-2"></i>
                <strong>Perhatian:</strong> Setelah disetujui, stok bahan mentah akan dikurangi secara otomatis dan tidak dapat dibatalkan.
            </div>
        `;
        button.textContent = 'Setujui';
        button.className = 'btn btn-success';
        form.action = `/production-approvals/${requestId}/approve`;
        requiredIndicator.textContent = '';
        notesField.required = false;
    } else {
        title.textContent = 'Tolak Pengajuan Produksi';
        message.innerHTML = `
            <div class="alert alert-danger">
                <i class="bi bi-x-circle me-2"></i>
                <strong>Penolakan:</strong> Harap berikan alasan yang jelas untuk penolakan pengajuan ini.
            </div>
        `;
        button.textContent = 'Tolak';
        button.className = 'btn btn-danger';
        form.action = `/production-approvals/${requestId}/reject`;
        requiredIndicator.textContent = '*';
        notesField.required = true;
    }
    
    notesField.value = '';
    modal.show();
}
</script>
@endpush
@endsection
