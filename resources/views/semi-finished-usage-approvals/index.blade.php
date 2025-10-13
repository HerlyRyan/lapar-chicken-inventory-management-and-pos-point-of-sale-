@extends('layouts.app')

@section('title', 'Setujui Penggunaan Bahan')

@section('content')
<div class="container-fluid">
    <h1 class="mt-4">Setujui Penggunaan Bahan</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Persetujuan Penggunaan Bahan</li>
    </ol>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card mb-4">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <i class="fas fa-check-circle mr-1"></i>
                Daftar Permintaan Penggunaan Bahan
            </div>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('semi-finished-usage-approvals.index') }}" class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="form-label" for="status">Status</label>
                    <select id="status" name="status" class="form-control">
                        @php($allStatuses = [
                            \App\Models\SemiFinishedUsageRequest::STATUS_PENDING => 'Menunggu',
                            \App\Models\SemiFinishedUsageRequest::STATUS_APPROVED => 'Disetujui',
                            \App\Models\SemiFinishedUsageRequest::STATUS_REJECTED => 'Ditolak',
                            \App\Models\SemiFinishedUsageRequest::STATUS_PROCESSING => 'Diproses',
                            \App\Models\SemiFinishedUsageRequest::STATUS_COMPLETED => 'Selesai',
                            \App\Models\SemiFinishedUsageRequest::STATUS_CANCELLED => 'Dibatalkan',
                        ])
                        <option value="all" {{ (request('status','pending')==='all') ? 'selected' : '' }}>Semua</option>
                        @foreach($allStatuses as $key=>$label)
                            <option value="{{ $key }}" {{ (request('status','pending')===$key) ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-5">
                    <label class="form-label" for="branch_id">Cabang</label>
                    <select id="branch_id" name="branch_id" class="form-control">
                        <option value="all">Semua Cabang</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ (string)request('branch_id')===(string)$branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button class="btn btn-primary me-2" type="submit">
                        <i class="fas fa-filter"></i> Terapkan Filter
                    </button>
                    <a href="{{ route('semi-finished-usage-approvals.index') }}" class="btn btn-secondary">
                        Reset
                    </a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No. Permintaan</th>
                            <th>Tanggal</th>
                            <th>Cabang</th>
                            <th>Peminta</th>
                            <th>Status</th>
                            <th class="text-center" width="280">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $req)
                            <tr>
                                <td>{{ $req->request_number }}</td>
                                <td>{{ optional($req->requested_date)->format('d/m/Y') }}</td>
                                <td>{{ $req->requestingBranch->name ?? '-' }}</td>
                                <td>{{ $req->requestedBy->name ?? '-' }}</td>
                                <td>{!! $req->status_badge !!}</td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center align-items-center gap-2 flex-nowrap" style="white-space: nowrap;">
                                        <a href="{{ route('semi-finished-usage-requests.show', $req) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i> Detail
                                        </a>

                                        @if(
                                            $req->status === \App\Models\SemiFinishedUsageRequest::STATUS_PENDING && (
                                                auth()->user()->isSuperAdmin() ||
                                                auth()->user()->hasRole('Admin') ||
                                                auth()->user()->hasRole('Manager') ||
                                                auth()->user()->hasRole('Kepala Toko') ||
                                                auth()->user()->hasRole('admin') ||
                                                auth()->user()->hasRole('super-admin')
                                            )
                                        )
                                            <button type="button"
                                                    class="btn btn-sm btn-success approve-btn"
                                                    data-action="{{ route('semi-finished-usage-requests.approve', ['semiFinishedUsageRequest' => $req->id]) }}"
                                                    data-number="{{ $req->request_number }}">
                                                <i class="fas fa-check"></i> Setujui
                                            </button>
                                            <button type="button"
                                                    class="btn btn-sm btn-danger reject-btn"
                                                    data-action="{{ route('semi-finished-usage-requests.reject', ['semiFinishedUsageRequest' => $req->id]) }}"
                                                    data-number="{{ $req->request_number }}">
                                                <i class="fas fa-times"></i> Tolak
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Tidak ada permintaan untuk ditampilkan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div>
                {{ $requests->withQueryString()->links() }}
            </div>

            <!-- Approve Modal -->
            <div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <form id="approveForm" action="#" method="POST" data-disable-on-submit="true">
                            @csrf
                            <input type="hidden" name="return_to" value="approvals">
                            <input type="hidden" name="status" value="{{ request('status', \App\Models\SemiFinishedUsageRequest::STATUS_PENDING) }}">
                            <input type="hidden" name="branch_id" value="{{ request('branch_id', 'all') }}">
                            <div class="modal-header">
                                <h5 class="modal-title" id="approveModalLabel">Setujui Permintaan</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>Anda yakin ingin menyetujui permintaan penggunaan bahan <strong>#<span id="approveRequestNumber">-</span></strong>?</p>
                                <div class="alert alert-warning mb-0">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    Persetujuan akan langsung mengurangi stok cabang sesuai jumlah yang diminta.
                                </div>
                                <div class="mb-3 mt-3">
                                    <label for="approval_note" class="form-label">Catatan Persetujuan (opsional)</label>
                                    <textarea class="form-control" id="approval_note" name="approval_note" rows="3" maxlength="255" placeholder="Contoh: Disetujui untuk produksi hari ini"></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-success">Setujui</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Reject Modal -->
            <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <form id="rejectForm" action="#" method="POST" data-disable-on-submit="true">
                            @csrf
                            <input type="hidden" name="return_to" value="approvals">
                            <input type="hidden" name="status" value="{{ request('status', \App\Models\SemiFinishedUsageRequest::STATUS_PENDING) }}">
                            <input type="hidden" name="branch_id" value="{{ request('branch_id', 'all') }}">
                            <div class="modal-header">
                                <h5 class="modal-title" id="rejectModalLabel">Tolak Permintaan</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>Anda yakin ingin menolak permintaan penggunaan bahan <strong>#<span id="rejectRequestNumber">-</span></strong>?</p>
                                <div class="mb-3">
                                    <label for="rejection_reason" class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="3" required maxlength="255"></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-danger">Tolak</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Disable global ModalGuard for this page to avoid interference
    window.DISABLE_MODAL_GUARD = true;

    // Prevent duplicate init
    if (window.__SFU_APPROVALS_INIT__) return;
    window.__SFU_APPROVALS_INIT__ = true;

    // Open reject modal and prepare form
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.reject-btn');
        if (!btn) return;
        e.preventDefault();

        const action = btn.getAttribute('data-action');
        if (!action) { alert('URL penolakan tidak ditemukan'); return; }

        const number = btn.getAttribute('data-number') || '-';
        const form = document.getElementById('rejectForm');
        const numberEl = document.getElementById('rejectRequestNumber');
        const reasonField = document.getElementById('rejection_reason');

        if (form) form.setAttribute('action', action);
        if (numberEl) numberEl.textContent = number;
        if (reasonField) reasonField.value = '';

        const modalEl = document.getElementById('rejectModal');
        if (modalEl) {
            // Prevent stacking-context issues (e.g., ancestors with transform) by appending modal to body
            if (modalEl.parentElement !== document.body) {
                document.body.appendChild(modalEl);
            }
            const inst = bootstrap.Modal.getOrCreateInstance(modalEl, { backdrop: 'static', keyboard: false });
            inst.show();
        }
    });

    // Open approve modal and prepare form
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.approve-btn');
        if (!btn) return;
        e.preventDefault();

        const action = btn.getAttribute('data-action');
        if (!action) { alert('URL persetujuan tidak ditemukan'); return; }

        const number = btn.getAttribute('data-number') || '-';
        const form = document.getElementById('approveForm');
        const numberEl = document.getElementById('approveRequestNumber');

        if (form) form.setAttribute('action', action);
        if (numberEl) numberEl.textContent = number;

        const modalEl = document.getElementById('approveModal');
        if (modalEl) {
            if (modalEl.parentElement !== document.body) {
                document.body.appendChild(modalEl);
            }
            const inst = bootstrap.Modal.getOrCreateInstance(modalEl, { backdrop: 'static', keyboard: false });
            inst.show();
        }
    });

    // Modal lifecycle: focus on show, reset on hide
    const rejectModalEl = document.getElementById('rejectModal');
    if (rejectModalEl) {
        rejectModalEl.addEventListener('shown.bs.modal', function() {
            const reasonField = document.getElementById('rejection_reason');
            if (reasonField) reasonField.focus();
        });
        rejectModalEl.addEventListener('hidden.bs.modal', function() {
            const form = document.getElementById('rejectForm');
            if (form) form.reset();
        });
    }

    // Validate and submit
    const rejectForm = document.getElementById('rejectForm');
    if (rejectForm) {
        rejectForm.addEventListener('submit', function(e) {
            const reasonField = document.getElementById('rejection_reason');
            if (!reasonField || !reasonField.value.trim()) {
                e.preventDefault();
                alert('Alasan penolakan harus diisi');
                if (reasonField) reasonField.focus();
                return false;
            }
            // Disable submit button to avoid double submission
            const submitBtn = rejectForm.querySelector('button[type="submit"]');
            if (submitBtn && !submitBtn.disabled) {
                submitBtn.disabled = true;
                submitBtn.dataset.originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = 'Mengirim…';
            }
        });
    }

    // Disable approve submit to prevent double posts
    const approveForm = document.getElementById('approveForm');
    if (approveForm) {
        approveForm.addEventListener('submit', function() {
            const submitBtn = approveForm.querySelector('button[type="submit"]');
            if (submitBtn && !submitBtn.disabled) {
                submitBtn.disabled = true;
                submitBtn.dataset.originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = 'Memproses…';
            }
        });
    }
});
</script>
@endpush

@endsection
