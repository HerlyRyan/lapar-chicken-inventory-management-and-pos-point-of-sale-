@extends('layouts.app')

@section('title', 'Proses Produksi')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="bi bi-gear-wide-connected text-info me-2"></i>
                Proses Produksi
            </h1>
            <p class="text-muted small mb-0">Kelola status produksi yang sudah disetujui</p>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-xl-4 col-md-6 mb-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Siap Diproduksi
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $productionRequests->where('status', 'approved')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-play-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Sedang Diproduksi
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $productionRequests->where('status', 'in_progress')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-gear fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Selesai
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $productionRequests->where('status', 'completed')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('production-processes.index') }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Siap Diproduksi</option>
                            <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>Sedang Diproduksi</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="search" class="form-label">Pencarian</label>
                        <input type="text" name="search" id="search" class="form-control" 
                               placeholder="Cari berdasarkan kode atau peruntukan..." 
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="bi bi-search me-1"></i>
                                Filter
                            </button>
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
                <i class="bi bi-list-ul me-2"></i>
                Daftar Produksi
            </h6>
        </div>
        <div class="card-body">
            @if($productionRequests->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Kode</th>
                                <th>Peruntukan</th>
                                <th>Bahan Mentah</th>
                                <th>Status</th>
                                <th>Progress</th>
                                <th>Disetujui</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($productionRequests as $request)
                            <tr class="{{ $request->isApproved() ? 'table-success' : ($request->isInProgress() ? 'table-info' : '') }}">
                                <td>
                                    <strong>{{ $request->request_code }}</strong>
                                    @if($request->isApproved())
                                        <span class="badge bg-success text-white ms-1">SIAP</span>
                                    @elseif($request->isInProgress())
                                        <span class="badge bg-info text-white ms-1">AKTIF</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-bold">{{ Str::limit($request->purpose, 35) }}</div>
                                    @if($request->estimated_output_quantity)
                                        <small class="text-muted">
                                            Target: {{ number_format($request->estimated_output_quantity, 0, ',', '.') }} unit
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ $request->items->count() }} item
                                        @if($request->items->count() > 0)
                                            <br>
                                            @foreach($request->items->take(2) as $item)
                                                • {{ Str::limit($item->rawMaterial->name ?? '', 15) }}<br>
                                            @endforeach
                                            @if($request->items->count() > 2)
                                                <span class="text-primary">+{{ $request->items->count() - 2 }} lainnya</span>
                                            @endif
                                        @endif
                                    </small>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $request->status_color }} fs-6">
                                        {{ $request->status_label }}
                                    </span>
                                </td>
                                <td>
                                    @if($request->isApproved())
                                        <div class="text-muted small">
                                            <i class="bi bi-clock me-1"></i>
                                            Menunggu mulai produksi
                                        </div>
                                    @elseif($request->isInProgress())
                                        <div class="small">
                                            <div class="text-info fw-bold">
                                                <i class="bi bi-gear me-1"></i>
                                                Sedang diproduksi
                                            </div>
                                            <div class="text-muted">
                                                Mulai: {{ $request->production_started_at->format('d/m/Y H:i') }}
                                            </div>
                                            @if($request->production_notes)
                                                <div class="text-muted">
                                                    {{ Str::limit($request->production_notes, 30) }}
                                                </div>
                                            @endif
                                        </div>
                                    @elseif($request->isCompleted())
                                        <div class="small">
                                            <div class="text-primary fw-bold">
                                                <i class="bi bi-check-circle me-1"></i>
                                                Selesai
                                            </div>
                                            <div class="text-muted">
                                                Selesai: {{ $request->production_completed_at->format('d/m/Y H:i') }}
                                            </div>
                                        </div>
                                    @endif
                                </td>
                                <td>{{ $request->approved_at ? $request->approved_at->format('d/m/Y H:i') : '-' }}</td>
                                <td>
                                    <div class="btn-group-vertical" role="group">
                                        <a href="{{ route('production-processes.show', $request) }}" 
                                           class="btn btn-sm btn-outline-info mb-1" title="Lihat Detail">
                                            <i class="bi bi-eye me-1"></i>
                                            Detail
                                        </a>
                                        
                                        @if($request->isApproved())
                                            <button type="button" class="btn btn-sm btn-success mb-1" 
                                                    onclick="showStartModal({{ $request->id }})" title="Mulai Produksi">
                                                <i class="bi bi-play-circle me-1"></i>
                                                Mulai
                                            </button>
                                        @elseif($request->isInProgress())
                                            <button type="button" class="btn btn-sm btn-warning mb-1" 
                                                    onclick="showUpdateModal({{ $request->id }})" title="Update Status">
                                                <i class="bi bi-pencil me-1"></i>
                                                Update
                                            </button>
                                            <button type="button" class="btn btn-sm btn-primary" 
                                                    onclick="showCompleteModal({{ $request->id }})" title="Selesaikan">
                                                <i class="bi bi-check-circle me-1"></i>
                                                Selesai
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
                        dari {{ $productionRequests->total() }} produksi
                    </div>
                    {{ $productionRequests->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-gear text-muted" style="font-size: 4rem;"></i>
                    <h5 class="mt-3 text-muted">Tidak Ada Produksi</h5>
                    <p class="text-muted">
                        @if(request()->hasAny(['status', 'search']))
                            Tidak ada produksi yang sesuai dengan filter yang dipilih.
                        @else
                            Belum ada pengajuan produksi yang disetujui untuk diproduksi.
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Start Production Modal -->
<div class="modal fade" id="startModal" tabindex="-1" aria-labelledby="startModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="startModalLabel">Mulai Produksi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="startForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        Produksi akan dimulai dan status akan berubah menjadi "Sedang Diproduksi".
                    </div>
                    
                    <div class="mb-3">
                        <label for="production_notes_start" class="form-label">Catatan Produksi</label>
                        <textarea name="production_notes" id="production_notes_start" class="form-control" rows="3" 
                                  placeholder="Catatan awal produksi, kondisi bahan, estimasi waktu, dll..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-play-circle me-1"></i>
                        Mulai Produksi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Update Status Modal -->
<div class="modal fade" id="updateModal" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateModalLabel">Update Status Produksi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="updateForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Update progress dan status produksi yang sedang berlangsung.
                    </div>
                    
                    <div class="mb-3">
                        <label for="production_notes_update" class="form-label">Update Progress <span class="text-danger">*</span></label>
                        <textarea name="production_notes" id="production_notes_update" class="form-control" rows="3" 
                                  placeholder="Progress saat ini, kendala yang dihadapi, estimasi waktu selesai..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-pencil me-1"></i>
                        Update Status
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Complete Production Modal -->
<div class="modal fade" id="completeModal" tabindex="-1" aria-labelledby="completeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="completeModalLabel">Selesaikan Produksi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="completeForm" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle me-2"></i>
                        <strong>Produksi akan diselesaikan.</strong> Harap masukkan hasil realisasi produksi yang akan ditambahkan ke stok pusat.
                    </div>
                    
                    <div class="mb-3">
                        <label for="production_notes_complete" class="form-label">Catatan Akhir</label>
                        <textarea name="production_notes" id="production_notes_complete" class="form-control" rows="2" 
                                  placeholder="Catatan hasil produksi, kualitas, dll..."></textarea>
                    </div>
                    
                    <hr>
                    <h6 class="fw-bold text-primary mb-3">
                        <i class="bi bi-box-seam me-2"></i>
                        Hasil Produksi
                    </h6>
                    
                    <!-- Alert explaining the planned vs real comparison -->
                    <div class="alert alert-info mb-3">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Rencana vs Realisasi:</strong> Isi jumlah realisasi sesuai dengan hasil produksi aktual. Jumlah rencana ditampilkan sebagai acuan.
                    </div>
                    
                    <div id="planned-outputs-container">
                        <!-- Planned outputs will be loaded here via JavaScript -->
                        <div class="text-center py-3 text-muted">
                            <div class="spinner-border spinner-border-sm me-2" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            Memuat data produk yang direncanakan...
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="production_evidence" class="form-label">Bukti Hasil Produksi <span class="text-danger">*</span></label>
                        <input type="file" name="production_evidence" id="production_evidence" class="form-control" accept="image/*" required>
                        <div class="form-text">Unggah foto bukti hasil produksi (format: JPG, PNG, maks 2MB)</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i>
                        Selesaikan Produksi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    .border-left-success {
        border-left: 0.25rem solid #1cc88a !important;
    }
    .border-left-info {
        border-left: 0.25rem solid #36b9cc !important;
    }
    .border-left-primary {
        border-left: 0.25rem solid #4e73df !important;
    }
    
    .table-success {
        background-color: rgba(212, 237, 218, 0.3);
    }
    
    .table-info {
        background-color: rgba(185, 235, 243, 0.3);
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
let outputProductIndex = 1;

function showStartModal(requestId) {
    const modal = new bootstrap.Modal(document.getElementById('startModal'));
    const form = document.getElementById('startForm');
    form.action = `/production-processes/${requestId}/start`;
    document.getElementById('production_notes_start').value = '';
    modal.show();
}

function showUpdateModal(requestId) {
    const modal = new bootstrap.Modal(document.getElementById('updateModal'));
    const form = document.getElementById('updateForm');
    form.action = `/production-processes/${requestId}/update-status`;
    document.getElementById('production_notes_update').value = '';
    modal.show();
}

function showCompleteModal(requestId) {
    const modal = new bootstrap.Modal(document.getElementById('completeModal'));
    const form = document.getElementById('completeForm');
    form.action = `/production-processes/${requestId}/complete`;
    document.getElementById('production_notes_complete').value = '';
    
    // Fetch planned outputs for this production request
    fetchPlannedOutputs(requestId);
    
    modal.show();
}

function fetchPlannedOutputs(requestId) {
    const container = document.getElementById('planned-outputs-container');
    container.innerHTML = '<div class="text-center py-3 text-muted"><div class="spinner-border spinner-border-sm me-2" role="status"><span class="visually-hidden">Loading...</span></div>Memuat data produk yang direncanakan...</div>';
    
    fetch(`/production-processes/${requestId}/planned-outputs`)
        .then(response => response.json())
        .then(data => {
            if (data.length === 0) {
                container.innerHTML = '<div class="alert alert-warning">Tidak ada rencana output yang ditemukan</div>';
                return;
            }
            
            // Table header
            let outputHtml = `
                <div class="table-responsive mb-3">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 40%">Produk</th>
                                <th style="width: 20%" class="text-center">Rencana</th>
                                <th style="width: 20%" class="text-center">Realisasi</th>
                                <th style="width: 20%" class="text-center">Satuan</th>
                            </tr>
                        </thead>
                        <tbody>
            `;
            
            // Table rows for each planned output
            data.forEach(output => {
                const plannedInt = Math.trunc(Number(output.quantity) || 0);
                outputHtml += `
                    <tr>
                        <td>
                            <strong>${output.product_name}</strong>
                            <input type="hidden" name="output_ids[]" value="${output.id}">
                        </td>
                        <td class="text-center">
                            <span class="badge bg-info fs-6">${new Intl.NumberFormat('id-ID', { maximumFractionDigits: 0 }).format(plannedInt)}</span>
                        </td>
                        <td>
                            <div class="input-group">
                                <input type="number" 
                                       name="realized_outputs[${output.id}]" 
                                       id="realized_output_${output.id}" 
                                       class="form-control text-center" 
                                       value="${plannedInt}" 
                                       step="1" 
                                       min="1" 
                                       inputmode="numeric"
                                       oninput="this.value = this.value ? parseInt(this.value, 10) : ''" 
                                       required>
                            </div>
                        </td>
                        <td class="text-center">
                            ${output.unit}
                        </td>
                    </tr>
                `;
            });
            
            // Close the table
            outputHtml += `
                        </tbody>
                    </table>
                </div>
            `;
            
            container.innerHTML = outputHtml;
            
            // Add event listeners for change detection
            data.forEach(output => {
                const plannedInt = Math.trunc(Number(output.quantity) || 0);
                const realizedInput = document.getElementById(`realized_output_${output.id}`);
                if (realizedInput) {
                    realizedInput.addEventListener('input', function() {
                        if (this.value) this.value = parseInt(this.value, 10);
                    });
                    realizedInput.addEventListener('change', function() {
                        const realized = parseInt(this.value, 10) || 0;
                        // Change background color based on difference
                        if (realized > plannedInt) {
                            this.classList.add('bg-success-subtle');
                            this.classList.remove('bg-warning-subtle', 'bg-danger-subtle');
                        } else if (realized < plannedInt) {
                            this.classList.add('bg-warning-subtle');
                            this.classList.remove('bg-success-subtle', 'bg-danger-subtle');
                        } else {
                            this.classList.remove('bg-success-subtle', 'bg-warning-subtle', 'bg-danger-subtle');
                        }
                    });
                }
            });
        })
        .catch(error => {
            console.error('Error fetching planned outputs:', error);
            container.innerHTML = '<div class="alert alert-danger">Gagal memuat data rencana output. Silakan coba lagi.</div>';
        });
}

function addOutputProduct() {
    const container = document.getElementById('output-products-container');
    const newRow = document.querySelector('.output-product-row').cloneNode(true);
    
    // Update indices and clear values
    newRow.setAttribute('data-index', outputProductIndex);
    newRow.querySelectorAll('select, input').forEach(element => {
        if (element.name) {
            element.name = element.name.replace(/\[\d+\]/, `[${outputProductIndex}]`);
        }
        element.value = '';
    });
    
    // Update button to remove
    const button = newRow.querySelector('button');
    button.innerHTML = '<i class="bi bi-trash"></i>';
    button.className = 'btn btn-outline-danger btn-sm';
    button.setAttribute('onclick', `removeOutputProduct(${outputProductIndex})`);
    
    container.appendChild(newRow);
    outputProductIndex++;
}

function removeOutputProduct(index) {
    const row = document.querySelector(`[data-index="${index}"]`);
    if (row) {
        row.remove();
    }
}
</script>
@endpush
@endsection
