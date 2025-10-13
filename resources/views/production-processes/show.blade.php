@extends('layouts.app')

@section('title', 'Detail Proses Produksi')

@section('content')
<div class="container-fluid">
    <!-- Placeholder content (kept minimal); modal will auto-open -->
</div>

<!-- Detail Production Request Modal -->
<div class="modal fade" id="productionRequestDetailModal" tabindex="-1" aria-labelledby="productionRequestDetailLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="productionRequestDetailLabel">
            <i class="bi bi-file-text me-2 text-info"></i>
            Detail Produksi: <span class="fw-bold">{{ $productionRequest->request_code }}</span>
            <span class="badge bg-{{ $productionRequest->status_color }} ms-2">{{ $productionRequest->status_label }}</span>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Top summary -->
        <div class="row g-3 mb-3">
          <div class="col-md-4">
            <div class="card h-100">
              <div class="card-body">
                <div class="text-muted small">Peruntukan</div>
                <div class="fw-bold">{{ $productionRequest->purpose ?: '-' }}</div>
              </div>
            </div>
          </div>
          <div class="col-md-8">
            <div class="card h-100">
              <div class="card-body">
                <div class="row small">
                  <div class="col-md-6 mb-2">
                    <div class="text-muted">Diajukan</div>
                    <div>
                      {{ $productionRequest->requestedBy?->name ?? '-' }}
                    </div>
                  </div>
                  <div class="col-md-6 mb-2">
                    <div class="text-muted">Disetujui</div>
                    <div>
                      {{ $productionRequest->approvedBy?->name ?? '-' }}
                      @if($productionRequest->approved_at)
                        <span class="text-muted">• {{ $productionRequest->approved_at->format('d/m/Y H:i') }}</span>
                      @endif
                    </div>
                  </div>
                  <div class="col-md-6 mb-2">
                    <div class="text-muted">Mulai Produksi</div>
                    <div>
                      {{ $productionRequest->productionStartedBy?->name ?? '-' }}
                      @if($productionRequest->production_started_at)
                        <span class="text-muted">• {{ $productionRequest->production_started_at->format('d/m/Y H:i') }}</span>
                      @endif
                    </div>
                  </div>
                  <div class="col-md-6 mb-2">
                    <div class="text-muted">Selesai</div>
                    <div>
                      {{ $productionRequest->productionCompletedBy?->name ?? '-' }}
                      @if($productionRequest->production_completed_at)
                        <span class="text-muted">• {{ $productionRequest->production_completed_at->format('d/m/Y H:i') }}</span>
                      @endif
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Notes -->
        @if($productionRequest->approval_notes || $productionRequest->production_notes)
        <div class="card mb-3">
          <div class="card-body">
            <div class="row">
              @if($productionRequest->approval_notes)
              <div class="col-md-6 mb-2">
                <div class="text-muted small mb-1"><i class="bi bi-check2-square me-1"></i>Catatan Persetujuan</div>
                <div class="border rounded p-2 bg-light">{{ $productionRequest->approval_notes }}</div>
              </div>
              @endif
              @if($productionRequest->production_notes)
              <div class="col-md-6 mb-2">
                <div class="text-muted small mb-1"><i class="bi bi-gear me-1"></i>Catatan Produksi</div>
                <div class="border rounded p-2 bg-light">{{ $productionRequest->production_notes }}</div>
              </div>
              @endif
            </div>
          </div>
        </div>
        @endif

        <!-- Raw Materials -->
        <div class="card mb-3">
          <div class="card-header bg-light">
            <strong><i class="bi bi-boxes me-2"></i>Bahan Mentah</strong>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-sm table-bordered mb-0">
                <thead class="table-light">
                  <tr>
                    <th>Nama</th>
                    <th class="text-end">Jumlah</th>
                    <th>Satuan</th>
                    <th class="text-end">Harga/Unit</th>
                    <th class="text-end">Total</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($productionRequest->items as $item)
                  <tr>
                    <td>{{ $item->rawMaterial->name ?? '-' }}</td>
                    <td class="text-end">{{ number_format((float)$item->requested_quantity, 3, ',', '.') }}</td>
                    <td>{{ $item->rawMaterial?->unit?->name ?? $item->rawMaterial?->unit?->symbol ?? '-' }}</td>
                    <td class="text-end">Rp {{ number_format((float)$item->unit_cost, 0, ',', '.') }}</td>
                    <td class="text-end">Rp {{ number_format((float)$item->total_cost, 0, ',', '.') }}</td>
                  </tr>
                  @empty
                  <tr>
                    <td colspan="5" class="text-center text-muted">Tidak ada data bahan mentah</td>
                  </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Planned vs Actual Outputs -->
        <div class="card mb-2">
          <div class="card-header bg-light">
            <strong><i class="bi bi-diagram-3 me-2"></i>Hasil Produksi (Rencana vs Aktual)</strong>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-sm table-bordered mb-0">
                <thead class="table-light">
                  <tr>
                    <th>Produk Setengah Jadi</th>
                    <th class="text-end">Rencana</th>
                    <th class="text-end">Aktual</th>
                    <th class="text-end">Selisih</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($productionRequest->outputs as $out)
                  <tr>
                    <td>{{ $out->semiFinishedProduct?->name ?? '-' }}</td>
                    <td class="text-end">
                      {{ number_format((float)$out->planned_quantity, 3, ',', '.') }}
                      <small class="text-muted">{{ $out->semiFinishedProduct?->unit?->name ?? $out->semiFinishedProduct?->unit?->symbol ?? '' }}</small>
                    </td>
                    <td class="text-end">
                      {{ $out->actual_quantity !== null ? number_format((float)$out->actual_quantity, 3, ',', '.') : '-' }}
                      @if($out->actual_quantity !== null)
                        <small class="text-muted">{{ $out->semiFinishedProduct?->unit?->name ?? $out->semiFinishedProduct?->unit?->symbol ?? '' }}</small>
                      @endif
                    </td>
                    <td class="text-end">
                      @php
                        $diff = ($out->actual_quantity ?? null) !== null ? (float)$out->actual_quantity - (float)$out->planned_quantity : null;
                      @endphp
                      @if($diff === null)
                        -
                      @else
                        <span class="{{ $diff >= 0 ? 'text-success' : 'text-danger' }}">
                          {{ number_format($diff, 3, ',', '.') }}
                        </span>
                      @endif
                    </td>
                  </tr>
                  @empty
                  <tr>
                    <td colspan="4" class="text-center text-muted">Belum ada data hasil produksi</td>
                  </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <a href="{{ route('production-processes.index') }}" class="btn btn-outline-secondary">
          <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
          Tutup
        </button>
      </div>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
(function() {
  function onReady(fn) {
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', fn, { once: true });
    else fn();
  }
  onReady(function() {
    var modalEl = document.getElementById('productionRequestDetailModal');
    if (!modalEl || !window.bootstrap) return;
    try {
      var modal = bootstrap.Modal.getOrCreateInstance(modalEl, { backdrop: 'static', keyboard: true });
      modal.show();
      modalEl.addEventListener('hidden.bs.modal', function() {
        if (window.history.length > 1) {
          window.history.back();
        } else {
          window.location.href = @json(route('production-processes.index'));
        }
      }, { once: true });
    } catch (e) {
      console.error('Failed to open modal:', e);
    }
  });
})();
</script>
@endpush
