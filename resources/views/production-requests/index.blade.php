@extends('layouts.app')

@section('title', 'Pengajuan Produksi')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="bi bi-clipboard-plus text-primary me-2"></i>
                Pengajuan Produksi
            </h1>
            <p class="text-muted small mb-0">Kelola pengajuan penggunaan bahan mentah untuk produksi</p>
        </div>
        <div>
            <a href="{{ route('production-requests.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i>
                Buat Pengajuan Baru
            </a>
        </div>
    </div>

    <!-- Pantau Stok Bahan Mentah (Ringkas) -->
    @include('purchase-receipts.partials.alerts')
    {{-- Raw stock card moved to bottom of page --}}

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('production-requests.index') }}">
                {{-- Preserve raw-materials grid filters when filtering production requests --}}
                @if(request()->has('q')) <input type="hidden" name="q" value="{{ request('q') }}"> @endif
                @if(request()->has('stock')) <input type="hidden" name="stock" value="{{ request('stock') }}"> @endif
                @if(request()->has('rm_page')) <input type="hidden" name="rm_page" value="{{ request('rm_page') }}"> @endif
                <div class="row g-3">
                    <div class="col-md-4">
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
                Daftar Pengajuan Produksi
            </h6>
        </div>
        <div class="card-body">
            @if($productionRequests->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Kode Pengajuan</th>
                                <th>Peruntukan</th>
                                <th>Pemohon</th>
                                <th>Total Biaya</th>
                                <th>Status</th>
                                <th>Tanggal Pengajuan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($productionRequests as $request)
                            <tr>
                                <td>
                                    <strong>{{ $request->request_code }}</strong>
                                </td>
                                <td>
                                    <div class="fw-bold">{{ Str::limit($request->purpose, 40) }}</div>
                                    @if($request->estimated_output_quantity)
                                        <small class="text-muted">
                                            Target Output: {{ number_format($request->estimated_output_quantity, 0, ',', '.') }} unit
                                        </small>
                                    @endif
                                </td>
                                <td>{{ $request->requestedBy->name ?? '-' }}</td>
                                <td>Rp {{ number_format($request->total_raw_material_cost, 0, ',', '.') }}</td>
                                <td>
                                    <span class="badge bg-{{ $request->status_color }} fs-6">
                                        {{ $request->status_label }}
                                    </span>
                                </td>
                                <td>{{ $request->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('production-requests.show', $request) }}" 
                                           class="btn btn-sm btn-outline-info" title="Lihat Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @if($request->isPending())
                                            <a href="{{ route('production-requests.edit', $request) }}" 
                                               class="btn btn-sm btn-outline-warning" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <!-- Tombol hapus yang memicu modal konfirmasi -->
                                            <button type="button" 
                                                class="btn btn-sm btn-outline-danger ms-1" 
                                                title="Hapus Pengajuan"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#deleteModal"
                                                data-request-id="{{ $request->id }}"
                                                data-request-code="{{ $request->request_code }}">
                                                <i class="bi bi-trash"></i>
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
                    <h5 class="mt-3 text-muted">Belum Ada Pengajuan Produksi</h5>
                    <p class="text-muted">
                        @if(request()->hasAny(['status', 'search']))
                            Tidak ada pengajuan yang sesuai dengan filter yang dipilih.
                        @else
                            Mulai dengan membuat pengajuan produksi baru.
                        @endif
                    </p>
                    @if(!request()->hasAny(['status', 'search']))
                        <a href="{{ route('production-requests.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-1"></i>
                            Buat Pengajuan Pertama
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <!-- Pantau Stok Bahan Mentah (Ringkas) -->
    <div id="raw-stock" class="card border-0 shadow-lg mb-4">
        <div class="card-header text-white py-3" style="background: linear-gradient(135deg, #16a085 0%, #2ecc71 50%, #27ae60 100%);">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-box-seam me-2"></i>Pantau Stok Bahan Mentah
                </h5>
                <div class="d-flex gap-2">
                    <a href="{{ route('raw-materials.stock') }}" class="btn btn-light btn-sm">
                        <i class="bi bi-arrows-fullscreen me-1"></i> Lihat Semua
                    </a>
                    <a href="{{ route('production-requests.index', request()->query()) }}" class="btn btn-outline-light btn-sm">
                        <i class="bi bi-arrow-clockwise me-1"></i> Refresh
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <!-- Search & Filters for Raw Materials (same behavior as raw-materials/stock) -->
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <form method="GET" action="{{ route('production-requests.index') }}" class="input-group">
                        {{-- Preserve existing production-requests filters when searching materials --}}
                        @if(request()->has('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif
                        @if(request()->has('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
                        @if(request()->has('stock')) <input type="hidden" name="stock" value="{{ request('stock') }}"> @endif
                        @if(request()->has('pr_page')) <input type="hidden" name="pr_page" value="{{ request('pr_page') }}"> @endif
                        <input type="text" class="form-control" placeholder="Cari bahan mentah..." name="q" value="{{ request('q') }}">
                        <button class="btn btn-outline-primary" type="submit"><i class="bi bi-search"></i></button>
                        @if(request('q'))
                            <a href="{{ route('production-requests.index', request()->except(['q','rm_page'])) }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-lg"></i> Clear
                            </a>
                        @endif
                    </form>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="btn-group" role="group">
                        <a href="{{ request()->fullUrlWithQuery(['stock' => null, 'rm_page' => 1]) }}" class="btn btn-outline-secondary {{ !request('stock') ? 'active' : '' }}">
                            Semua
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['stock' => 'low', 'rm_page' => 1]) }}" class="btn btn-outline-danger {{ request('stock') === 'low' ? 'active' : '' }}">
                            <i class="bi bi-exclamation-triangle me-1"></i>Stok Habis
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['stock' => 'warning', 'rm_page' => 1]) }}" class="btn btn-outline-warning {{ request('stock') === 'warning' ? 'active' : '' }}">
                            <i class="bi bi-exclamation-circle me-1"></i>Stok Menipis
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['stock' => 'normal', 'rm_page' => 1]) }}" class="btn btn-outline-success {{ request('stock') === 'normal' ? 'active' : '' }}">
                            <i class="bi bi-check-circle me-1"></i>Stok Normal
                        </a>
                    </div>
                </div>
            </div>

            @include('raw-materials._stock_grid', ['rawMaterials' => $rawMaterialsStock])

            <!-- Pagination for raw materials grid -->
            <div class="mt-3">
                {{ $rawMaterialsStock->links() }}
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .badge {
        font-size: 0.75em;
    }
    
    .table th {
        background-color: #f8f9fc;
        border-color: #e3e6f0;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
    }
    
    .table td {
        vertical-align: middle;
        border-color: #e3e6f0;
    }
    
    .btn-group .btn {
        border-radius: 0.25rem;
        margin-right: 2px;
    }
    
    .btn-group .btn:last-child {
        margin-right: 0;
    }

    /* Ensure buttons look consistent within actions column */
    td:last-child .btn-group { position: relative; z-index: 1052; }

    /* Page-local safeguard: if any stale Bootstrap backdrop exists, don't block clicks */
    .modal-backdrop { pointer-events: none !important; }

    /* Stock card styles (reused from raw materials page) */
    .stock-card { transition: all 0.3s ease; }
    .stock-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
    .stock-low { border-left: 4px solid #dc3545; }
    .stock-normal { border-left: 4px solid #28a745; }
    .stock-warning { border-left: 4px solid #ffc107; }
    .material-thumb { width: 100%; height: 120px; object-fit: contain; border-radius: .375rem; border: 1px solid #e9ecef; background-color: #f8f9fa; }
    .stock-card .card-body { padding: 0.75rem !important; }

    /* Ensure action cell stays clickable and above any stray overlays */
    td:last-child { position: relative !important; z-index: 2147483647 !important; }
    td:last-child, td:last-child * { pointer-events: auto !important; }
    form.delete-pr-form { position: relative; z-index: 2147483647 !important; }

    /* Kill any backdrops on this page */
    .modal-backdrop, .offcanvas-backdrop { display: none !important; pointer-events: none !important; }
</style>
@endpush
@push('scripts')
<script>
(function(){
  function initModalCleanup(){
    // Remove any stale Bootstrap modal/offcanvas backdrops that may block clicks
    document.querySelectorAll('.modal-backdrop, .offcanvas-backdrop').forEach(function(el){
      try { el.parentNode && el.parentNode.removeChild(el); } catch (_) {}
    });
    // Remove inert attributes that could block focus/interaction
    document.querySelectorAll('[inert]').forEach(function(el){
      try { el.removeAttribute('inert'); } catch (_) {}
    });
    // Reset body state if previously left in modal-open
    document.body.classList.remove('modal-open');
    document.body.style.removeProperty('overflow');
    document.body.style.removeProperty('paddingRight');
  }
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initModalCleanup, { once: true });
  } else {
    initModalCleanup();
  }
})();
</script>
<script>
// Modal guard: cleanup stale backdrops and inert attributes
(function(){
  function initModalCleanup() {
    // Remove any stale modal backdrops
    const backdrops = document.querySelectorAll('.modal-backdrop');
    if (backdrops.length > 0) {
      console.debug('[ModalGuard] cleanup called; anyOpen:', document.body.classList.contains('modal-open'), 'backdrops:', backdrops.length);
      backdrops.forEach(function(backdrop){
        try { backdrop.remove(); } catch (_) {}
      });
    }
    // Remove inert attributes that could block focus/interaction
    document.querySelectorAll('[inert]').forEach(function(el){
      try { el.removeAttribute('inert'); } catch (_) {}
    });
    // Reset body state if previously left in modal-open
    document.body.classList.remove('modal-open');
    document.body.style.removeProperty('overflow');
    document.body.style.removeProperty('paddingRight');
  }
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initModalCleanup, { once: true });
  } else {
    initModalCleanup();
  }
})();
</script>
<script>
// Modal delete confirmation handler
(function() {
  function initDeleteModal() {
    const deleteModal = document.getElementById('deleteModal');
    if (!deleteModal) return;
    
    // Setup modal when delete button is clicked
    document.addEventListener('click', function(e) {
      const deleteBtn = e.target.closest('button[data-bs-target="#deleteModal"]');
      if (!deleteBtn) return;
      
      const requestId = deleteBtn.getAttribute('data-request-id');
      const requestCode = deleteBtn.getAttribute('data-request-code');
      
      if (!requestId || !requestCode) return;
      
      // Set the request code in the modal
      const codeDisplay = document.getElementById('request-code-display');
      if (codeDisplay) codeDisplay.textContent = requestCode;
      
      // Set the form action URL
      const form = document.getElementById('modal-delete-form');
      if (form) {
        form.action = '/production-requests/' + requestId;
      }
    });
  }
  
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initDeleteModal, { once: true });
  } else {
    initDeleteModal();
  }
})();
</script>
<script>
// Page-scoped, minimal, and specific to delete-pr-form only
(function(){
  function handleDeleteSubmit(e, fromPointer) {
    const target = e.target;
    let form = null;
    // 1) Use event coordinates to locate the underlying delete form even if an overlay is the target
    if (typeof e.clientX === 'number' && typeof e.clientY === 'number') {
      const els = document.elementsFromPoint(e.clientX, e.clientY) || [];
      for (const el of els) {
        if (el && el.closest) {
          const f = el.closest('form.delete-pr-form');
          if (f) { form = f; break; }
        }
      }
    }
    // 2) Fallback to DOM ancestry from event target
    if (!form && target && target.closest) {
      const btn = target.closest('button[type="submit"]');
      form = btn ? btn.closest('form.delete-pr-form') : target.closest('form.delete-pr-form');
    }
    if (!form) return;

    // Prevent native flow; we control confirmation and submission
    e.preventDefault();
    e.stopPropagation();

    if (!window.confirm('Apakah Anda yakin ingin menghapus pengajuan ini?')) return;
    try { form.submit(); } catch (_) {}
  }
  function initDeleteInterceptors(){
    // Capture-phase listeners to pre-empt other interceptors
    document.addEventListener('click', function(e){ handleDeleteSubmit(e, false); }, true);
    document.addEventListener('pointerdown', function(e){ handleDeleteSubmit(e, true); }, true);
    document.addEventListener('submit', function(e){ handleDeleteSubmit(e, false); }, true);

    // Keyboard: Enter/Space on focused control within delete form
    window.addEventListener('keydown', function(e){
      if (e.key !== 'Enter' && e.key !== ' ') return;
      const active = document.activeElement;
      const form = active && active.closest ? active.closest('form.delete-pr-form') : null;
      if (!form) return;
      e.preventDefault();
      e.stopPropagation();
      if (!window.confirm('Apakah Anda yakin ingin menghapus pengajuan ini?')) return;
      try { form.submit(); } catch (_) {}
    }, true);
  }
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initDeleteInterceptors, { once: true });
  } else {
    initDeleteInterceptors();
  }
})();
</script>
@endpush

{{-- Modal Konfirmasi Hapus --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus pengajuan dengan kode: <strong id="request-code-display"></strong>?</p>
                <p class="text-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i>Tindakan ini tidak dapat dibatalkan.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="modal-delete-form" action="" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
