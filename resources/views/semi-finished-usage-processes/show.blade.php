@extends('layouts.app')

@section('title', 'Proses Penggunaan Bahan - ' . $usageRequest->request_number)

@section('content')
<div class="container-fluid">
    <h1 class="mt-4">Proses Penggunaan Bahan</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('semi-finished-usage-processes.index') }}">Proses Penggunaan Bahan</a></li>
        <li class="breadcrumb-item active">{{ $usageRequest->request_number }}</li>
    </ol>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-file-alt mr-1"></i>
                Detail Permintaan
            </div>
            <div>
                @if($usageRequest->status === \App\Models\SemiFinishedUsageRequest::STATUS_APPROVED)
                    <form class="d-inline" method="POST" action="{{ route('semi-finished-usage-processes.start', $usageRequest) }}">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="fas fa-play"></i> Mulai Proses
                        </button>
                    </form>
                @endif
                <a href="{{ route('semi-finished-usage-requests.show', $usageRequest) }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-external-link-alt"></i> Lihat Permintaan
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-2">
                <div class="col-md-4">
                    <div class="mb-1"><strong>Nomor:</strong> {{ $usageRequest->request_number }}</div>
                    <div class="mb-1"><strong>Cabang:</strong> {{ $usageRequest->requestingBranch->name }}</div>
                    <div class="mb-1"><strong>Status:</strong> {!! $usageRequest->status_badge !!}</div>
                </div>
                <div class="col-md-4">
                    <div class="mb-1"><strong>Tgl Permintaan:</strong> {{ optional($usageRequest->requested_date)->format('d/m/Y') }}</div>
                    <div class="mb-1"><strong>Tgl Dibutuhkan:</strong> {{ optional($usageRequest->required_date)->format('d/m/Y') ?? '-' }}</div>
                </div>
                <div class="col-md-4">
                    <div class="mb-1"><strong>Tujuan:</strong> {{ $usageRequest->purpose }}</div>
                    <div class="mb-1"><strong>Catatan:</strong> {{ $usageRequest->notes ?: '-' }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-boxes mr-1"></i>
            Output Produk Jadi
        </div>
        <div class="card-body">
            <form id="processUpdateForm" method="POST" action="{{ route('semi-finished-usage-processes.update-status', $usageRequest) }}" enctype="multipart/form-data">
                @csrf
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Rencana</th>
                                <th>Aktual</th>
                                <th>Satuan</th>
                                <th>Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($usageRequest->outputs as $idx => $output)
                                <tr>
                                    <td>
                                        <input type="hidden" name="outputs[{{ $idx }}][id]" value="{{ $output->id }}">
                                        {{ $output->finishedProduct->name ?? '#' }}
                                    </td>
                                    <td>{{ (int) $output->planned_quantity }}</td>
                                    <td style="width:180px;">
                                        <input type="number" class="form-control form-control-sm" name="outputs[{{ $idx }}][actual_quantity]" min="0" step="1" value="{{ is_null($output->actual_quantity) ? '' : (int) $output->actual_quantity }}" placeholder="0">
                                    </td>
                                    <td>{{ optional($output->finishedProduct->unit)->unit_name ?? '-' }}</td>
                                    <td style="width:260px;">
                                        <input type="text" class="form-control form-control-sm" name="outputs[{{ $idx }}][notes]" value="{{ $output->notes }}" placeholder="Catatan">
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">Tidak ada data output</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    <label class="form-label">Upload Foto Proses (opsional)</label>
                    <input type="file" name="photos[]" class="form-control-file" accept="image/*" multiple>
                    <small class="text-muted d-block">Foto akan digunakan sebagai dokumentasi. (Penyimpanan backend dapat ditambahkan kemudian)</small>
                </div>

                <div class="d-flex justify-content-between mt-3">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Simpan Perkembangan
                    </button>
                </div>
            </form>

            @if($usageRequest->status === \App\Models\SemiFinishedUsageRequest::STATUS_PROCESSING)
                <form id="completeForm" class="mt-2" method="POST" action="{{ route('semi-finished-usage-processes.complete', $usageRequest) }}">
                    @csrf
                    <div class="hidden-outputs d-none"></div>
                    <button type="button" id="triggerComplete" class="btn btn-primary">
                        <i class="fas fa-check"></i> Selesaikan Proses
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
<!-- Confirm Complete Modal -->
<div class="modal fade" id="confirmCompleteModal" tabindex="-1" aria-labelledby="confirmCompleteLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="confirmCompleteLabel">Konfirmasi Penyelesaian Proses</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="alert alert-warning mb-0">
          <i class="fas fa-exclamation-triangle"></i>
          Selesaikan proses? Stok produk jadi akan ditambahkan sesuai jumlah aktual yang diinput.
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="button" id="confirmCompleteBtn" class="btn btn-primary">Ya, Selesaikan</button>
      </div>
    </div>
  </div>
  </div>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const completeForm = document.getElementById('completeForm');
    const triggerBtn = document.getElementById('triggerComplete');
    const confirmModalEl = document.getElementById('confirmCompleteModal');
    const confirmBtn = document.getElementById('confirmCompleteBtn');

    function copyOutputsToHidden(form) {
        let container = form.querySelector('.hidden-outputs');
        if (!container) {
            container = document.createElement('div');
            container.className = 'hidden-outputs d-none';
            form.appendChild(container);
        }
        container.innerHTML = '';

        const fields = document.querySelectorAll('input[name^="outputs["], textarea[name^="outputs["]');
        fields.forEach(function(input) {
            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = input.name;
            hidden.value = input.value;
            container.appendChild(hidden);
        });
    }

    if (triggerBtn && confirmModalEl && completeForm) {
        triggerBtn.addEventListener('click', function(e) {
            e.preventDefault();
            // If Bootstrap modal is available, use it; otherwise, fallback to native confirm
            if (window.bootstrap && bootstrap.Modal) {
                // Prevent stacking-context issues by appending modal to body
                if (confirmModalEl && confirmModalEl.parentElement !== document.body) {
                    document.body.appendChild(confirmModalEl);
                }
                const inst = bootstrap.Modal.getOrCreateInstance(confirmModalEl, { backdrop: 'static', keyboard: false });
                inst.show();
            } else {
                const ok = window.confirm('Selesaikan proses? Stok produk jadi akan ditambahkan sesuai jumlah aktual yang diinput.');
                if (ok) {
                    copyOutputsToHidden(completeForm);
                    completeForm.submit();
                }
            }
        });

        if (confirmBtn) {
            confirmBtn.addEventListener('click', function() {
                // Copy latest outputs then submit
                copyOutputsToHidden(completeForm);
                confirmBtn.disabled = true;
                confirmBtn.innerText = 'Memproses…';
                completeForm.submit();
            });
        }
    }

    // Fallback: if form is submitted by other means, still copy outputs
    if (completeForm) {
        completeForm.addEventListener('submit', function() {
            copyOutputsToHidden(completeForm);
        });
    }
});
</script>
@endpush
@endsection
