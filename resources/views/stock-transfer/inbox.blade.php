@extends('layouts.app')

@section('title', 'Kotak Masuk Transfer Stok')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="bi bi-inbox text-info me-2"></i>
                Kotak Masuk Transfer Stok
            </h1>
            <p class="text-muted small mb-0">
                Menampilkan transfer berstatus <span class="badge bg-warning text-dark">Dikirim</span> ke cabang Anda.
            </p>
        </div>
        <div class="text-end">
            <div class="btn-group mb-2" role="group">
                <a href="{{ route('stock-transfer.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-list me-1"></i> Daftar Transfer
                </a>
                <a href="{{ route('stock-transfer.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> Buat Transfer
                </a>
            </div>
            <div class="small text-muted">Cabang aktif:</div>
            <div class="fw-bold">{{ $branch->name ?? 'Tidak dipilih' }}</div>
        </div>
    </div>

    <!-- Inbox Table -->
    <div class="card shadow">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="bi bi-truck me-2"></i>
                Transfer Masuk (Dikirim)
            </h6>
            <span class="badge bg-info">{{ $transfers->total() }} data</span>
        </div>
        <div class="card-body">
            @if($transfers->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Produk</th>
                                <th>Tipe</th>
                                <th>Jumlah</th>
                                <th>Dari Cabang</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transfers as $transfer)
                            <tr class="{{ $transfer->status === 'sent' ? 'table-warning' : '' }}">
                                <td class="align-middle">
                                    <strong>#{{ $transfer->id }}</strong>
                                </td>
                                <td class="align-middle">
                                    @if($transfer->item_type === 'finished')
                                        {{ $transfer->finishedProduct->name ?? 'Produk tidak ditemukan' }}
                                    @else
                                        {{ $transfer->semiFinishedProduct->name ?? 'Produk tidak ditemukan' }}
                                    @endif
                                    @if($transfer->notes)
                                        <br><small class="text-muted">{{ Str::limit($transfer->notes, 30) }}</small>
                                    @endif
                                </td>
                                <td class="align-middle">
                                    <span class="badge {{ $transfer->item_type === 'finished' ? 'bg-success' : 'bg-info' }}">
                                        {{ $transfer->item_type === 'finished' ? 'Produk Jadi' : 'Produk Setengah Jadi' }}
                                    </span>
                                </td>
                                <td class="align-middle">
                                    @php
                                        $unitAbbr = 'unit';
                                        if ($transfer->item_type === 'finished') {
                                            $fp = $transfer->finishedProduct;
                                            if ($fp && $fp->unit) {
                                                $unitAbbr = $fp->unit->abbreviation ?: ($fp->unit->unit_name ?? 'unit');
                                            }
                                        } else {
                                            $sp = $transfer->semiFinishedProduct;
                                            if ($sp && $sp->unit) {
                                                $unitAbbr = $sp->unit->abbreviation ?: ($sp->unit->unit_name ?? 'unit');
                                            }
                                        }
                                    @endphp
                                    <strong>{{ number_format($transfer->quantity, 0) }}</strong> {{ $unitAbbr }}
                                </td>
                                <td class="align-middle">
                                    {{ $transfer->fromBranch->name ?? '-' }}
                                </td>
                                <td class="align-middle">
                                    <div class="small">
                                        <div class="fw-bold">{{ $transfer->created_at->format('d/m/Y') }}</div>
                                        <div class="text-muted">{{ $transfer->created_at->format('H:i') }}</div>
                                    </div>
                                </td>
                                <td class="align-middle">
                                    @switch($transfer->status)
                                        @case('sent')
                                            <span class="badge bg-primary">Dikirim</span>
                                            @break
                                        @case('accepted')
                                            <span class="badge bg-success">Diterima</span>
                                            @break
                                        @case('rejected')
                                            <span class="badge bg-danger">Ditolak</span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary">{{ ucfirst($transfer->status) }}</span>
                                    @endswitch
                                </td>
                                <td class="align-middle">
                                    <div class="btn-group-vertical" role="group">
                                        <button type="button" class="btn btn-sm btn-outline-info mb-1" onclick="showTransferDetailModal({{ $transfer->id }})" data-bs-toggle="tooltip" title="Lihat Detail">
                                            <i class="fas fa-eye me-1"></i> Detail
                                        </button>
                                        @if($transfer->status === 'sent')
                                            <button type="button" class="btn btn-sm btn-success mb-1" onclick="showAcceptModal({{ $transfer->id }})" data-bs-toggle="tooltip" title="Terima Transfer">
                                                <i class="fas fa-check me-1"></i> Terima
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger" onclick="showRejectModal({{ $transfer->id }})" data-bs-toggle="tooltip" title="Tolak Transfer">
                                                <i class="fas fa-times me-1"></i> Tolak
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
                        Menampilkan {{ $transfers->firstItem() }} - {{ $transfers->lastItem() }} 
                        dari {{ $transfers->total() }} transfer
                    </div>
                    {{ $transfers->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-inbox text-muted" style="font-size: 4rem;"></i>
                    <h5 class="mt-3 text-muted">Tidak Ada Transfer Masuk</h5>
                    <p class="text-muted">Belum ada transfer baru untuk cabang Anda.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Accept Modal -->
<div class="modal fade" id="acceptModal" tabindex="-1" aria-labelledby="acceptModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="acceptForm" method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="acceptModalLabel">Terima Transfer</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="accept_notes" class="form-label">Catatan (opsional)</label>
            <textarea name="response_notes" id="accept_notes" class="form-control" rows="3" placeholder="Catatan penerimaan..."></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-success">Terima</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="rejectForm" method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="rejectModalLabel">Tolak Transfer</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="reject_notes" class="form-label">Catatan Penolakan</label>
            <textarea name="response_notes" id="reject_notes" class="form-control" rows="3" placeholder="Alasan penolakan..." required></textarea>
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

@push('styles')
<style>
    .btn-group-vertical .btn { margin-bottom: 2px; }
    .btn-group-vertical .btn:last-child { margin-bottom: 0; }
</style>
@endpush

<!-- Transfer Detail Modal -->
<div class="modal fade" id="transferDetailModal" tabindex="-1" aria-labelledby="transferDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="transferDetailModalLabel">Detail Transfer Stok</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="transferDetailModalBody">
                <!-- Content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@include('components.init-tooltips')

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize tooltips via shared initializer
        if (window.initTooltips) window.initTooltips();
    });

    function showTransferDetailModal(transferId) {
        const modal = new bootstrap.Modal(document.getElementById('transferDetailModal'));
        const modalBody = document.getElementById('transferDetailModalBody');
        
        modalBody.innerHTML = '<div class="text-center py-3"><i class="fas fa-spinner fa-spin"></i> Memuat detail transfer...</div>';
        modal.show();
        
        $.get(`{{ url('stock-transfer') }}/${transferId}/detail`)
            .done(function(data) {
                modalBody.innerHTML = data;
            })
            .fail(function() {
                modalBody.innerHTML = '<div class="alert alert-danger">Gagal memuat detail transfer.</div>';
            });
    }

    function showAcceptModal(id) {
        const form = document.getElementById('acceptForm');
        form.action = '{{ url('stock-transfer') }}/' + id + '/accept';
        const modal = new bootstrap.Modal(document.getElementById('acceptModal'));
        modal.show();
    }
    
    function showRejectModal(id) {
        const form = document.getElementById('rejectForm');
        form.action = '{{ url('stock-transfer') }}/' + id + '/reject';
        const modal = new bootstrap.Modal(document.getElementById('rejectModal'));
        modal.show();
    }

    // tooltip initializer is provided by components.init-tooltips partial
</script>
@endpush
@endsection
