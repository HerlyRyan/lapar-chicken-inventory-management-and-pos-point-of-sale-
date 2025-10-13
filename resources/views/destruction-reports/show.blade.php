@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Detail Laporan Pemusnahan</h4>
        <div class="d-flex gap-2">
            <a href="{{ route('destruction-reports.index') }}" class="btn btn-light">Kembali</a>
            @if($destructionReport->status === 'draft')
                <a href="{{ route('destruction-reports.edit', $destructionReport) }}" class="btn btn-primary">Edit</a>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card mb-3">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <small class="text-muted d-block">No. Laporan</small>
                    <div class="fw-semibold">{{ $destructionReport->report_number }}</div>
                </div>
                <div class="col-md-3">
                    <small class="text-muted d-block">Cabang</small>
                    <div class="fw-semibold">{{ $destructionReport->branch->name ?? '-' }}</div>
                </div>
                <div class="col-md-3">
                    <small class="text-muted d-block">Tanggal</small>
                    <div class="fw-semibold">{{ optional($destructionReport->destruction_date)->format('Y-m-d') }}</div>
                </div>
                <div class="col-md-3">
                    <small class="text-muted d-block">Status</small>
                    <span class="badge {{ $destructionReport->status_badge }}">{{ ucfirst($destructionReport->status) }}</span>
                </div>
                <div class="col-md-6">
                    <small class="text-muted d-block">Alasan</small>
                    <div class="fw-semibold">{{ $destructionReport->reason }}</div>
                </div>
                <div class="col-md-6">
                    <small class="text-muted d-block">Catatan</small>
                    <div class="fw-semibold">{{ $destructionReport->notes ?: '-' }}</div>
                </div>
                <div class="col-md-6">
                    <small class="text-muted d-block">Dibuat Oleh</small>
                    <div class="fw-semibold">{{ $destructionReport->reportedBy->name ?? '-' }}</div>
                </div>
                <div class="col-md-6">
                    <small class="text-muted d-block">Disetujui Oleh</small>
                    <div class="fw-semibold">
                        @if($destructionReport->approved_by)
                            {{ $destructionReport->approvedBy->name ?? '-' }} ({{ optional($destructionReport->approved_at)->format('Y-m-d H:i') }})
                        @else
                            -
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <div class="row g-3 align-items-center">
                <div class="col-md-6">
                    <small class="text-muted d-block">Total Nilai Kerugian</small>
                    <div class="fw-bold fs-4">Rp {{ number_format($destructionReport->total_cost ?? 0, 2) }}</div>
                </div>
                <div class="col-md-6 text-md-end">
                    <small class="text-muted d-block">Jumlah Item</small>
                    <div class="fw-semibold">{{ $items->count() }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <strong>Item Dimusnahkan</strong>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th class="text-end">Qty</th>
                            <th class="text-end">Biaya/Unit</th>
                            <th class="text-end">Total Biaya</th>
                            <th>Kondisi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $item)
                            @php
                                $prod = $item->finishedProduct ?: $item->semiFinishedProduct;
                                $name = $prod->name ?? '-';
                                $unit = optional($prod->unit)->abbreviation ?? '';
                            @endphp
                            <tr>
                                <td>{{ $name }}</td>
                                <td class="text-end">{{ number_format($item->quantity, 2) }} {{ $unit }}</td>
                                <td class="text-end">Rp {{ number_format($item->unit_cost ?? 0, 2) }}</td>
                                <td class="text-end">Rp {{ number_format($item->total_cost ?? 0, 2) }}</td>
                                <td>{{ $item->condition_description ?: '-' }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <th colspan="3" class="text-end">Total</th>
                            <th class="text-end">Rp {{ number_format($destructionReport->total_cost ?? 0, 2) }}</th>
                            <th></th>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        @if($destructionReport->status === 'draft')
        <div class="card-footer d-flex gap-2">
            <form id="approveForm" method="POST" action="{{ route('destruction-reports.approve', $destructionReport) }}" data-disable-on-submit="true">
                @csrf
                <input type="hidden" name="action" value="approve">
                <button type="button" class="btn btn-success" id="btnApprove">Setujui</button>
            </form>
            <form id="rejectForm" method="POST" action="{{ route('destruction-reports.approve', $destructionReport) }}" data-disable-on-submit="true">
                @csrf
                <input type="hidden" name="action" value="reject">
                <button type="button" class="btn btn-outline-danger" id="btnReject">Tolak</button>
            </form>
        </div>
        @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const approveBtn = document.getElementById('btnApprove');
                const rejectBtn = document.getElementById('btnReject');
                const approveForm = document.getElementById('approveForm');
                const rejectForm = document.getElementById('rejectForm');

                function submitWithConfirm(btn, form, title, text, confirmText, confirmColor) {
                    if (!btn || !form) return;
                    btn.addEventListener('click', function() {
                        Swal.fire({
                            title: title,
                            text: text,
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonText: confirmText,
                            cancelButtonText: 'Batal',
                            confirmButtonColor: confirmColor || '#198754',
                            cancelButtonColor: '#6c757d'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                try {
                                    btn.disabled = true;
                                    btn.classList.add('disabled');
                                } catch (_) {}
                                form.submit();
                            }
                        });
                    });
                }

                submitWithConfirm(
                    approveBtn,
                    approveForm,
                    'Setujui Pengajuan?',
                    'Stok akan dikurangi sesuai item pemusnahan.',
                    'Ya, Setujui',
                    '#198754'
                );

                submitWithConfirm(
                    rejectBtn,
                    rejectForm,
                    'Tolak Pengajuan?',
                    'Pengajuan akan ditandai sebagai ditolak. Stok tidak berubah.',
                    'Ya, Tolak',
                    '#dc3545'
                );
            });
        </script>
        @endpush
        @endif
    </div>
</div>
@endsection
