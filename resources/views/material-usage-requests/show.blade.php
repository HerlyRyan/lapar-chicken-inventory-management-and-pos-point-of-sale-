@extends('layouts.app')

@section('title', 'Detail Permintaan Penggunaan Bahan')

@section('content')
<div class="container-fluid">
    <h1 class="mt-4">Detail Permintaan Penggunaan Bahan</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('semi-finished-usage-requests.index') }}">Permintaan Penggunaan Bahan</a></li>
        <li class="breadcrumb-item active">Detail Permintaan #{{ $materialUsageRequest->request_number }}</li>
    </ol>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="row">
        <div class="col-xl-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-info-circle mr-1"></i>
                        Detail Permintaan
                    </div>
                    <div>
                        {!! $materialUsageRequest->status_badge !!}
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <!-- Request header buttons -->
                        <div class="col-12 mb-3">
                            @if($materialUsageRequest->status === \App\Models\SemiFinishedUsageRequest::STATUS_PENDING)
                                @if(auth()->user()->hasRole(['kepala-gudang', 'admin', 'super-admin']))
                                    <button type="button" class="btn btn-success mr-2" data-toggle="modal" data-target="#approveModal">
                                        <i class="fas fa-check"></i> Setujui
                                    </button>
                                    <button type="button" class="btn btn-danger mr-2" data-toggle="modal" data-target="#rejectModal">
                                        <i class="fas fa-times"></i> Tolak
                                    </button>
                                @endif
                                @if(auth()->user()->id === $materialUsageRequest->user_id)
                                    <a href="{{ route('semi-finished-usage-requests.edit', $materialUsageRequest) }}" class="btn btn-primary mr-2">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#cancelModal">
                                        <i class="fas fa-ban"></i> Batalkan
                                    </button>
                                @endif
                            @endif

                            @if($materialUsageRequest->status === \App\Models\SemiFinishedUsageRequest::STATUS_APPROVED)
                                @if(auth()->user()->hasRole(['kepala-gudang', 'admin', 'super-admin']))
                                    <button type="button" class="btn btn-info mr-2" data-toggle="modal" data-target="#processModal">
                                        <i class="fas fa-box-open"></i> Proses Permintaan
                                    </button>
                                @endif
                            @endif

                            @if($materialUsageRequest->status === \App\Models\SemiFinishedUsageRequest::STATUS_PROCESSING)
                                @if(auth()->user()->hasRole(['kepala-gudang', 'admin', 'super-admin']))
                                    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#completeModal">
                                        <i class="fas fa-check-double"></i> Selesaikan
                                    </button>
                                @endif
                            @endif
                        </div>

                        <!-- Request details - 2 columns -->
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="30%">Nomor Permintaan</th>
                                    <td width="70%">{{ $materialUsageRequest->request_number }}</td>
                                </tr>
                                <tr>
                                    <th>Cabang Peminta</th>
                                    <td>{{ $materialUsageRequest->requestingBranch->name }}</td>
                                </tr>
                                <tr>
                                    <th>Pengaju</th>
                                    <td>{{ $materialUsageRequest->user->name }}</td>
                                </tr>
                                <tr>
                                    <th>Tanggal Permintaan</th>
                                    <td>{{ $materialUsageRequest->requested_date->format('d/m/Y') }}</td>
                                </tr>
                            </table>
                        </div>
                        
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="30%">Tanggal Dibutuhkan</th>
                                    <td width="70%">{{ $materialUsageRequest->required_date ? $materialUsageRequest->required_date->format('d/m/Y') : '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Tujuan</th>
                                    <td>{{ $materialUsageRequest->purpose }}</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>{!! $materialUsageRequest->status_badge !!}</td>
                                </tr>
                                <tr>
                                    <th>Catatan</th>
                                    <td>{{ $materialUsageRequest->notes ?: '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- If request has approval or rejection info -->
                    @if($materialUsageRequest->status !== \App\Models\SemiFinishedUsageRequest::STATUS_PENDING)
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        @if($materialUsageRequest->status === \App\Models\SemiFinishedUsageRequest::STATUS_REJECTED)
                                            <i class="fas fa-times-circle text-danger"></i> Informasi Penolakan
                                        @else
                                            <i class="fas fa-check-circle text-success"></i> Informasi Persetujuan
                                        @endif
                                    </h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <table class="table table-borderless table-sm">
                                                <tr>
                                                    <th width="30%">Tanggal</th>
                                                    <td width="70%">{{ $materialUsageRequest->approval_date ? $materialUsageRequest->approval_date->format('d/m/Y H:i') : '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Oleh</th>
                                                    <td>{{ $materialUsageRequest->approvalUser ? $materialUsageRequest->approvalUser->name : '-' }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <table class="table table-borderless table-sm">
                                                <tr>
                                                    <th width="30%">Catatan</th>
                                                    <td width="70%">{{ $materialUsageRequest->rejection_reason ?: '-' }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Request items table -->
                    <div class="row">
                        <div class="col-12">
                            <h5>Daftar Bahan yang Diminta</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th width="5%">No</th>
                                            <th width="35%">Nama Bahan</th>
                                            <th width="15%">Jumlah</th>
                                            <th width="15%">Satuan</th>
                                            <th width="15%">Harga Satuan</th>
                                            <th width="15%">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($materialUsageRequest->items as $index => $item)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    {{ $item->semiFinishedProduct->name ?? '-' }}
                                                    @if($item->notes)
                                                        <br><small class="text-muted">{{ $item->notes }}</small>
                                                    @endif
                                                </td>
                                                <td>{{ number_format((float) $item->quantity, 0, ',', '.') }}</td>
                                                <td>
                                                    @if($item->unit)
                                                        {{ $item->unit->name }}@if(!empty($item->unit->symbol)) ({{ $item->unit->symbol }}) @endif
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td class="text-right">{{ $item->formatted_unit_price }}</td>
                                                <td class="text-right">{{ $item->formatted_subtotal }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">Tidak ada item</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="5" class="text-right">Total:</th>
                                            <th class="text-right">Rp {{ number_format($materialUsageRequest->total_amount, 0, ',', '.') }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('semi-finished-usage-requests.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1" role="dialog" aria-labelledby="approveModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('semi-finished-usage-requests.approve', $materialUsageRequest) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="approveModalLabel">Setujui Permintaan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Anda yakin ingin menyetujui permintaan penggunaan bahan #{{ $materialUsageRequest->request_number }}?</p>
                    <div class="form-group">
                        <label for="approval_notes">Catatan (Opsional):</label>
                        <textarea class="form-control" id="approval_notes" name="approval_notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Setujui</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('semi-finished-usage-requests.reject', $materialUsageRequest) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectModalLabel">Tolak Permintaan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Anda yakin ingin menolak permintaan penggunaan bahan #{{ $materialUsageRequest->request_number }}?</p>
                    <div class="form-group">
                        <label for="rejection_reason">Alasan Penolakan:</label>
                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Tolak</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Process Modal -->
<div class="modal fade" id="processModal" tabindex="-1" role="dialog" aria-labelledby="processModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('semi-finished-usage-requests.process', $materialUsageRequest) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="processModalLabel">Proses Permintaan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Anda yakin ingin memproses permintaan penggunaan bahan #{{ $materialUsageRequest->request_number }}?</p>
                    <p class="text-info">
                        <i class="fas fa-info-circle"></i> 
                        Dengan memproses permintaan ini, Anda menyatakan bahwa bahan-bahan sedang disiapkan.
                    </p>
                    <div class="form-group">
                        <label for="process_notes">Catatan (Opsional):</label>
                        <textarea class="form-control" id="process_notes" name="process_notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info">Proses</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Complete Modal -->
<div class="modal fade" id="completeModal" tabindex="-1" role="dialog" aria-labelledby="completeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('semi-finished-usage-requests.complete', $materialUsageRequest) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="completeModalLabel">Selesaikan Permintaan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Anda yakin ingin menyelesaikan permintaan penggunaan bahan #{{ $materialUsageRequest->request_number }}?</p>
                    <p class="text-warning">
                        <i class="fas fa-exclamation-triangle"></i> 
                        <strong>Perhatian!</strong> Tindakan ini akan mengurangi stok bahan dan tidak dapat dibatalkan.
                    </p>
                    <div class="form-group">
                        <label for="completion_notes">Catatan (Opsional):</label>
                        <textarea class="form-control" id="completion_notes" name="completion_notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Selesaikan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Cancel Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1" role="dialog" aria-labelledby="cancelModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('semi-finished-usage-requests.cancel', $materialUsageRequest) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="cancelModalLabel">Batalkan Permintaan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Anda yakin ingin membatalkan permintaan penggunaan bahan #{{ $materialUsageRequest->request_number }}?</p>
                    <div class="form-group">
                        <label for="cancellation_reason">Alasan Pembatalan:</label>
                        <textarea class="form-control" id="cancellation_reason" name="cancellation_reason" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tidak</button>
                    <button type="submit" class="btn btn-danger">Ya, Batalkan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
