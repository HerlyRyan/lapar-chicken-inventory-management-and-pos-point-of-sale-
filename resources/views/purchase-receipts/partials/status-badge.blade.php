@php($status = $status ?? null)
@if($status === 'accepted')
    <span class="badge bg-success">
        <i class="bi bi-check me-1"></i>Diterima
    </span>
@elseif($status === 'rejected')
    <span class="badge bg-danger">
        <i class="bi bi-x me-1"></i>Ditolak
    </span>
@elseif($status)
    <span class="badge bg-warning">
        <i class="bi bi-exclamation-triangle me-1"></i>Sebagian
    </span>
@else
    <span class="badge bg-secondary">-</span>
@endif
