@php
    use Illuminate\Support\Facades\Storage;
@endphp

<div class="row g-3">
    @forelse($rawMaterials as $material)
        @php
            $stockColor = 'success';
            $stockText = 'Aman';
            if ($material->current_stock <= 0) {
                $stockColor = 'danger';
                $stockText = 'Kosong';
            } elseif ($material->current_stock < $material->minimum_stock) {
                $stockColor = 'danger';
                $stockText = 'Rendah';
            } elseif ($material->current_stock < ($material->minimum_stock * 2)) {
                $stockColor = 'warning';
                $stockText = 'Peringatan';
            }

            $percent = $material->minimum_stock > 0
                ? min(100, ($material->current_stock / $material->minimum_stock) * 100)
                : 0;

            // Resolve image URL if exists
            $imageUrl = null;
            if (!empty($material->image)) {
                $imageUrl = str_starts_with($material->image, 'products/')
                    ? Storage::url($material->image)
                    : asset($material->image);
            }
        @endphp
        <div class="col-12 col-sm-6 col-md-6 col-lg-4">
            <div class="card border-left-{{ $stockColor }} shadow h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="flex-grow-1">
                            <h6 class="card-title mb-1 fw-bold" title="{{ $material->name }}">{{ Str::limit($material->name, 40) }}</h6>
                            <p class="card-subtitle text-muted small mb-2">{{ $material->code }}</p>
                        </div>
                        <span class="badge bg-{{ $stockColor }} fs-6">{{ $stockText }}</span>
                    </div>

                    @if($imageUrl)
                        <img src="{{ $imageUrl }}" alt="{{ $material->name }}" class="material-thumb mb-2">
                    @else
                        <div class="material-thumb mb-2 d-flex align-items-center justify-content-center bg-light">
                            <i class="bi bi-box text-muted" style="font-size: 2rem;"></i>
                        </div>
                    @endif

                    <div class="row text-center mb-3">
                        <div class="col-4">
                            <div class="fw-bold text-{{ $stockColor }}">{{ number_format($material->current_stock, 0, ',', '.') }}</div>
                            <small class="text-muted">Stok</small>
                        </div>
                        <div class="col-4">
                            <div class="fw-bold">{{ number_format($material->minimum_stock, 0, ',', '.') }}</div>
                            <small class="text-muted">Minimum</small>
                        </div>
                        <div class="col-4">
                            <div class="fw-bold">{{ $material->unit->name ?? '-' }}</div>
                            <small class="text-muted">Satuan</small>
                        </div>
                    </div>

                    <!-- Stock Progress Bar -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <small class="text-muted">Level Stok</small>
                            <small class="text-{{ $stockColor }}">{{ number_format($percent, 1) }}%</small>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-{{ $stockColor }}" style="width: {{ min(100, max(5, $percent)) }}%"></div>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <div class="btn-group" role="group">
                            <a href="{{ route('raw-materials.show', $material) }}" class="btn btn-sm btn-outline-info" title="Lihat Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body text-center py-5">
                    <i class="bi bi-inbox" style="font-size: 3rem; color: #6c757d;"></i>
                    <h5 class="mt-3 text-muted">Tidak ada bahan mentah yang ditemukan</h5>
                    <p class="text-muted">Coba ubah filter pencarian atau tambahkan bahan mentah baru</p>
                </div>
            </div>
        </div>
    @endforelse
</div>
