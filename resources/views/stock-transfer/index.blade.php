@extends('layouts.app')

@section('title', 'Daftar Transfer Stok')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mt-4">Daftar Transfer Stok</h1>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Transfer Stok</li>
            </ol>
        </div>
        <div>
            <div class="btn-group" role="group">
                <a href="{{ route('stock-transfer.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> Buat Transfer Baru
                </a>
                <a href="{{ route('stock-transfer.inbox') }}?branch_id={{ session('current_branch_id') ?? (Auth::user()->branch_id ?? '') }}" class="btn btn-outline-info">
                    <i class="fas fa-inbox me-1"></i> Kotak Masuk
                </a>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-filter me-1"></i>
            Filter & Pencarian
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('stock-transfer.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Dikirim</option>
                            <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>Diterima</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Cabang Tujuan</label>
                        <select name="to_branch_id" class="form-select">
                            <option value="">Semua Cabang</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ request('to_branch_id') == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tanggal Dari</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tanggal Sampai</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="fas fa-search me-1"></i> Filter
                        </button>
                        <a href="{{ route('stock-transfer.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Transfer List Card -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list me-1"></i>
                Daftar Transfer Stok
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
                                <th>Ke Cabang</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transfers as $transfer)
                            <tr>
                                <td class="align-middle">
                                    <strong>#{{ $transfer->id }}</strong>
                                </td>
                                <td class="align-middle">
                                    @if($transfer->item_type === 'finished')
                                        {{ $transfer->finishedProduct->name ?? 'Produk tidak ditemukan' }}
                                    @else
                                        {{ $transfer->semiFinishedProduct->name ?? 'Produk tidak ditemukan' }}
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
                                    {{ number_format($transfer->quantity, 0) }} {{ $unitAbbr }}
                                </td>
                                <td class="align-middle">
                                    {{ $transfer->toBranch->name ?? '-' }}
                                </td>
                                <td class="align-middle">
                                    @switch($transfer->status)
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
                                        @default
                                            <span class="badge bg-secondary">{{ ucfirst($transfer->status) }}</span>
                                    @endswitch
                                </td>
                                <td class="align-middle">
                                    <div class="small">
                                        <div class="fw-bold">{{ $transfer->created_at->format('d/m/Y') }}</div>
                                        <div class="text-muted">{{ $transfer->created_at->format('H:i') }}</div>
                                    </div>
                                </td>
                                <td class="align-middle">
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-outline-info" onclick="showDetailModal({{ $transfer->id }})" data-bs-toggle="tooltip" title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @if($transfer->status === 'pending')
                                            <a href="{{ route('stock-transfer.edit', $transfer->id) }}" class="btn btn-sm btn-outline-warning" data-bs-toggle="tooltip" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="cancelTransfer({{ $transfer->id }})" data-bs-toggle="tooltip" title="Batalkan">
                                                <i class="fas fa-times"></i>
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
                    <i class="fas fa-exchange-alt text-muted" style="font-size: 4rem;"></i>
                    <h5 class="mt-3 text-muted">Belum Ada Transfer</h5>
                    <p class="text-muted">Belum ada transfer stok yang dibuat.</p>
                    <a href="{{ route('stock-transfer.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Buat Transfer Pertama
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Detail Modal -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel">Detail Transfer Stok</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="detailModalBody">
                <!-- Content will be loaded here -->
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

  function showDetailModal(transferId) {
    const modal = new bootstrap.Modal(document.getElementById('detailModal'));
    const modalBody = document.getElementById('detailModalBody');

    modalBody.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Memuat detail...</div>';
    modal.show();

    $.get(`{{ url('stock-transfer') }}/${transferId}/detail`)
      .done(function(data) {
        modalBody.innerHTML = data;
      })
      .fail(function() {
        modalBody.innerHTML = '<div class="alert alert-danger">Gagal memuat detail transfer.</div>';
      });
  }

  function cancelTransfer(transferId) {
    Swal.fire({
      title: 'Batalkan Transfer?',
      text: 'Transfer ini akan dibatalkan dan tidak dapat dikembalikan.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Ya, Batalkan',
      cancelButtonText: 'Tidak'
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: `{{ url('stock-transfer') }}/${transferId}/cancel`,
          method: 'POST',
          data: { _token: '{{ csrf_token() }}' },
          success: function(response) {
            if (response.success) {
              Swal.fire('Dibatalkan!', response.message, 'success').then(() => location.reload());
            } else {
              Swal.fire('Gagal!', response.message, 'error');
            }
          },
          error: function(xhr) {
            const response = xhr.responseJSON;
            Swal.fire('Error!', (response && response.message) || 'Terjadi kesalahan', 'error');
          }
        });
      }
    });
  }
</script>
@endpush
@endsection
