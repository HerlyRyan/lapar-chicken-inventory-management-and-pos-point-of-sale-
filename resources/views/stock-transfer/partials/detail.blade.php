<div class="row">
    <div class="col-md-6">
        <h6 class="fw-bold mb-3">Informasi Transfer</h6>
        <table class="table table-sm">
            <tr>
                <td class="fw-bold" width="40%">ID Transfer:</td>
                <td>#{{ $stockTransfer->id }}</td>
            </tr>
            <tr>
                <td class="fw-bold">Status:</td>
                <td>
                    @switch($stockTransfer->status)
                        @case('pending')
                            <span class="badge bg-warning text-dark">Pending</span>
                            @break
                        @case('sent')
                            <span class="badge bg-primary">Dikirim</span>
                            @break
                        @case('accepted')
                            <span class="badge bg-success">Diterima</span>
                            @break
                        @case('rejected')
                            <span class="badge bg-danger">Ditolak</span>
                            @break
                        @case('cancelled')
                            <span class="badge bg-secondary">Dibatalkan</span>
                            @break
                        @default
                            <span class="badge bg-secondary">{{ ucfirst($stockTransfer->status) }}</span>
                    @endswitch
                </td>
            </tr>
            <tr>
                <td class="fw-bold">Dari Cabang:</td>
                <td>{{ $stockTransfer->fromBranch->name ?? '-' }}</td>
            </tr>
            <tr>
                <td class="fw-bold">Ke Cabang:</td>
                <td>{{ $stockTransfer->toBranch->name ?? '-' }}</td>
            </tr>
            <tr>
                <td class="fw-bold">Tanggal Dibuat:</td>
                <td>{{ $stockTransfer->created_at->format('d/m/Y H:i:s') }}</td>
            </tr>
            @if($stockTransfer->updated_at != $stockTransfer->created_at)
            <tr>
                <td class="fw-bold">Terakhir Diperbarui:</td>
                <td>{{ $stockTransfer->updated_at->format('d/m/Y H:i:s') }}</td>
            </tr>
            @endif
        </table>
    </div>
    
    <div class="col-md-6">
        <h6 class="fw-bold mb-3">Detail Produk</h6>
        <table class="table table-sm">
            <tr>
                <td class="fw-bold" width="40%">Jenis Produk:</td>
                <td>
                    <span class="badge {{ $stockTransfer->item_type === 'finished' ? 'bg-success' : 'bg-info' }}">
                        {{ $stockTransfer->item_type === 'finished' ? 'Produk Jadi' : 'Produk Setengah Jadi' }}
                    </span>
                </td>
            </tr>
            <tr>
                <td class="fw-bold">Nama Produk:</td>
                <td>
                    @if($stockTransfer->item_type === 'finished')
                        {{ $stockTransfer->finishedProduct->name ?? 'Produk tidak ditemukan' }}
                    @else
                        {{ $stockTransfer->semiFinishedProduct->name ?? 'Produk tidak ditemukan' }}
                    @endif
                </td>
            </tr>
            <tr>
                <td class="fw-bold">Jumlah:</td>
                <td>
                    @php
                        $unitAbbr = 'unit';
                        if ($stockTransfer->item_type === 'finished') {
                            $fp = $stockTransfer->finishedProduct;
                            if ($fp && $fp->unit) {
                                $unitAbbr = $fp->unit->abbreviation ?: ($fp->unit->unit_name ?? 'unit');
                            }
                        } else {
                            $sp = $stockTransfer->semiFinishedProduct;
                            if ($sp && $sp->unit) {
                                $unitAbbr = $sp->unit->abbreviation ?: ($sp->unit->unit_name ?? 'unit');
                            }
                        }
                    @endphp
                    <strong>{{ number_format($stockTransfer->quantity, 0) }}</strong> {{ $unitAbbr }}
                </td>
            </tr>
            @if($stockTransfer->notes)
            <tr>
                <td class="fw-bold">Catatan:</td>
                <td>{{ $stockTransfer->notes }}</td>
            </tr>
            @endif
            @if($stockTransfer->response_notes)
            <tr>
                <td class="fw-bold">Catatan Respon:</td>
                <td>{{ $stockTransfer->response_notes }}</td>
            </tr>
            @endif
        </table>
    </div>
</div>

@if($stockTransfer->sentByUser || $stockTransfer->handledByUser)
<div class="row mt-3">
    <div class="col-12">
        <h6 class="fw-bold mb-3">Riwayat Penanganan</h6>
        <div class="timeline">
            <div class="timeline-item">
                <div class="timeline-marker bg-primary"></div>
                <div class="timeline-content">
                    <h6 class="timeline-title">Transfer Dibuat</h6>
                    <p class="timeline-text">
                        @if($stockTransfer->sentByUser)
                            Oleh: <strong>{{ $stockTransfer->sentByUser->name }}</strong><br>
                        @endif
                        Tanggal: {{ $stockTransfer->created_at->format('d/m/Y H:i:s') }}
                    </p>
                </div>
            </div>
            
            @if($stockTransfer->status !== 'pending')
            <div class="timeline-item">
                <div class="timeline-marker {{ $stockTransfer->status === 'accepted' ? 'bg-success' : ($stockTransfer->status === 'rejected' ? 'bg-danger' : 'bg-secondary') }}"></div>
                <div class="timeline-content">
                    <h6 class="timeline-title">
                        @switch($stockTransfer->status)
                            @case('accepted')
                                Transfer Diterima
                                @break
                            @case('rejected')
                                Transfer Ditolak
                                @break
                            @case('cancelled')
                                Transfer Dibatalkan
                                @break
                            @default
                                Status: {{ ucfirst($stockTransfer->status) }}
                        @endswitch
                    </h6>
                    <p class="timeline-text">
                        @if($stockTransfer->handledByUser)
                            Oleh: <strong>{{ $stockTransfer->handledByUser->name }}</strong><br>
                        @endif
                        Tanggal: {{ $stockTransfer->updated_at->format('d/m/Y H:i:s') }}
                        @if($stockTransfer->response_notes)
                            <br>Catatan: {{ $stockTransfer->response_notes }}
                        @endif
                    </p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endif

@php
    $currentBranchId = session('current_branch_id') ?? (Auth::user()->branch_id ?? null);
@endphp
@if($stockTransfer->status === 'sent' && $currentBranchId === $stockTransfer->to_branch_id)
<div class="row mt-3">
    <div class="col-12 d-flex gap-2">
        <form id="acceptForm-{{ $stockTransfer->id }}" method="POST" action="{{ route('stock-transfer.accept', $stockTransfer) }}" class="d-inline">
            @csrf
            <input type="hidden" name="response_notes" id="acceptNotes-{{ $stockTransfer->id }}" value="">
            <button type="button" class="btn btn-success btn-sm"
                onclick="(function(){var n=prompt('Catatan penerimaan (opsional):'); if(n!==null){document.getElementById('acceptNotes-{{ $stockTransfer->id }}').value=n;} document.getElementById('acceptForm-{{ $stockTransfer->id }}').submit();})();">
                <i class="fas fa-check me-1"></i> Terima Transfer
            </button>
        </form>

        <form id="rejectForm-{{ $stockTransfer->id }}" method="POST" action="{{ route('stock-transfer.reject', $stockTransfer) }}" class="d-inline">
            @csrf
            <input type="hidden" name="response_notes" id="rejectNotes-{{ $stockTransfer->id }}" value="">
            <button type="button" class="btn btn-danger btn-sm"
                onclick="(function(){var n=prompt('Catatan penolakan (wajib):'); if(n===null || n.trim()===''){alert('Catatan penolakan wajib diisi.'); return;} document.getElementById('rejectNotes-{{ $stockTransfer->id }}').value=n; document.getElementById('rejectForm-{{ $stockTransfer->id }}').submit();})();">
                <i class="fas fa-times me-1"></i> Tolak Transfer
            </button>
        </form>
    </div>
    </div>
@endif

<style>
.timeline {
    position: relative;
    padding-left: 1.5rem;
}

.timeline-item {
    position: relative;
    padding-bottom: 1rem;
}

.timeline-item:not(:last-child)::before {
    content: '';
    position: absolute;
    left: -1.4rem;
    top: 1.5rem;
    width: 2px;
    height: calc(100% - 0.5rem);
    background-color: #dee2e6;
}

.timeline-marker {
    position: absolute;
    left: -1.75rem;
    top: 0.25rem;
    width: 0.75rem;
    height: 0.75rem;
    border-radius: 50%;
}

.timeline-content {
    margin-left: 0.5rem;
}

.timeline-title {
    font-size: 0.9rem;
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.timeline-text {
    font-size: 0.8rem;
    margin-bottom: 0;
}
</style>
