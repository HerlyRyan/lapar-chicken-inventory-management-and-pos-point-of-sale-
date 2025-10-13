{{-- 
    Detail System Info Card Component
    Parameters:
    - model: Model yang berisi created_at dan updated_at (wajib)
    - showId: Menampilkan ID model (default: true)
--}}

<div class="card border-0 shadow-lg">
    <div class="card-header text-white py-3" style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #a855f7 100%);">
        <h6 class="mb-0 fw-bold">
            <i class="bi bi-clock-history me-2"></i>Informasi Sistem
        </h6>
    </div>
    <div class="card-body p-3">
        <div class="small">
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Dibuat:</span>
                <span>{{ $model->created_at->format('d M Y H:i') }}</span>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Diupdate:</span>
                <span>{{ $model->updated_at->format('d M Y H:i') }}</span>
            </div>
            @if(($showId ?? true) && isset($model->id))
            <div class="d-flex justify-content-between">
                <span class="text-muted">ID:</span>
                <span class="badge bg-light text-dark">{{ $model->id }}</span>
            </div>
            @endif
            
            {{ $slot }}
        </div>
    </div>
</div>
