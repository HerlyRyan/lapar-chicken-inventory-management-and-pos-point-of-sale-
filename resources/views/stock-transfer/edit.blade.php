@extends('layouts.app')

@section('title', 'Edit Transfer Stok')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mt-4">Edit Transfer Stok</h1>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('stock-transfer.index') }}">Transfer Stok</a></li>
                <li class="breadcrumb-item active">Edit #{{ $transfer->id }}</li>
            </ol>
        </div>
        <div>
            <a href="{{ route('stock-transfer.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
            </a>
        </div>
    </div>

    @if($transfer->status !== 'pending')
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            Transfer ini sudah berstatus <strong>{{ ucfirst($transfer->status) }}</strong> dan tidak dapat diedit.
        </div>
    @endif

    <!-- Transfer Form Card -->
    <div class="row">
        <div class="col-xl-8">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-edit me-1"></i>
                    Edit Transfer Stok #{{ $transfer->id }}
                </div>
                <div class="card-body">
                    <form id="transferForm" {{ $transfer->status !== 'pending' ? 'style=pointer-events:none;opacity:0.6;' : '' }}>
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="from_branch_id" class="form-label">Cabang Asal</label>
                                    <select class="form-select" id="from_branch_id" disabled>
                                        <option value="{{ $transfer->from_branch_id }}" selected>
                                            {{ $transfer->fromBranch->name ?? 'Cabang tidak ditemukan' }}
                                        </option>
                                    </select>
                                    <div class="form-text">
                                        <small class="text-muted">Cabang asal tidak dapat diubah.</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="to_branch_id" class="form-label">Cabang Tujuan</label>
                                    <select class="form-select" id="to_branch_id" name="to_branch_id" required {{ $transfer->status !== 'pending' ? 'disabled' : '' }}>
                                        <option value="">Pilih Cabang Tujuan</option>
                                        @foreach($branches as $branch)
                                            <option value="{{ $branch->id }}" {{ $transfer->to_branch_id == $branch->id ? 'selected' : '' }}>
                                                {{ $branch->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Current Transfer Item -->
                        <div class="mb-3">
                            <label class="form-label">Item Transfer</label>
                            <div class="row g-2 align-items-end border rounded p-3 bg-light">
                                <div class="col-md-3">
                                    <label class="form-label">Jenis Produk</label>
                                    <select class="form-select" id="item_type" {{ $transfer->status !== 'pending' ? 'disabled' : '' }}>
                                        <option value="finished" {{ $transfer->item_type === 'finished' ? 'selected' : '' }}>Produk Jadi</option>
                                        <option value="semi-finished" {{ $transfer->item_type === 'semi-finished' ? 'selected' : '' }}>Produk Setengah Jadi</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Produk</label>
                                    <select class="form-select" id="item_id" name="item_id" required {{ $transfer->status !== 'pending' ? 'disabled' : '' }}>
                                        <option value="">Memuat produk...</option>
                                    </select>
                                    <div class="form-text">
                                        <small class="text-muted from-stock">&nbsp;</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Jumlah</label>
                                    <input type="number" class="form-control" id="quantity" name="quantity" 
                                           value="{{ $transfer->quantity }}" min="1" step="1" required 
                                           {{ $transfer->status !== 'pending' ? 'disabled' : '' }} />
                                    <div class="form-text">
                                        <small class="text-muted to-stock">&nbsp;</small>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Catatan</label>
                                    <input type="text" class="form-control" id="notes" name="notes" 
                                           value="{{ $transfer->notes }}" placeholder="Catatan transfer..." 
                                           {{ $transfer->status !== 'pending' ? 'disabled' : '' }} />
                                </div>
                            </div>
                        </div>

                        @if($transfer->status === 'pending')
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <button type="button" class="btn btn-outline-danger me-md-2" onclick="cancelTransfer()">
                                    <i class="fas fa-times"></i> Batalkan Transfer
                                </button>
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-save"></i> Simpan Perubahan
                                </button>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>

        <!-- Transfer Info Card -->
        <div class="col-xl-4">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-info-circle me-1"></i>
                    Informasi Transfer
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <strong>ID Transfer:</strong><br>
                            <span class="text-muted">#{{ $transfer->id }}</span>
                        </li>
                        <li class="mb-2">
                            <strong>Status:</strong><br>
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
                        </li>
                        <li class="mb-2">
                            <strong>Dibuat:</strong><br>
                            <span class="text-muted">{{ $transfer->created_at->format('d/m/Y H:i') }}</span>
                        </li>
                        @if($transfer->updated_at != $transfer->created_at)
                            <li class="mb-2">
                                <strong>Diperbarui:</strong><br>
                                <span class="text-muted">{{ $transfer->updated_at->format('d/m/Y H:i') }}</span>
                            </li>
                        @endif
                        @if($transfer->response_notes)
                            <li class="mb-0">
                                <strong>Catatan Respon:</strong><br>
                                <span class="text-muted">{{ $transfer->response_notes }}</span>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>

            @if($transfer->status === 'pending')
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        Perhatian
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled small">
                            <li class="mb-2"><i class="fas fa-info text-info me-2"></i>Transfer masih dapat diedit selama berstatus pending</li>
                            <li class="mb-2"><i class="fas fa-info text-info me-2"></i>Setelah dikirim, transfer tidak dapat diedit</li>
                            <li class="mb-0"><i class="fas fa-info text-info me-2"></i>Pastikan data sudah benar sebelum mengirim</li>
                        </ul>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Load initial product data
    loadProducts();
    updateStockInfo();
    
    // Update stock info when branch or product changes
    $('#to_branch_id, #item_id, #quantity').on('change', function() {
        updateStockInfo();
    });

    // Change product type
    $('#item_type').on('change', function() {
        loadProducts();
    });

    // Submit form
    $('#transferForm').submit(function(e) {
        e.preventDefault();
        
        @if($transfer->status !== 'pending')
            return false;
        @endif
        
        const submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');

        const formData = new FormData();
        formData.append('to_branch_id', $('#to_branch_id').val());
        formData.append('item_type', $('#item_type').val());
        formData.append('item_id', $('#item_id').val());
        formData.append('quantity', $('#quantity').val());
        formData.append('notes', $('#notes').val());
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('_method', 'PUT');

        $.ajax({
            url: '{{ route("stock-transfer.update", $transfer->id) }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    Swal.fire({ 
                        title: 'Berhasil!', 
                        text: response.message, 
                        icon: 'success' 
                    }).then(() => { 
                        window.location.href = '{{ route("stock-transfer.index") }}';
                    });
                } else {
                    Swal.fire({ title: 'Gagal!', text: response.message, icon: 'error' });
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                Swal.fire({ title: 'Error!', text: (response && response.message) || 'Terjadi kesalahan pada server', icon: 'error' });
            },
            complete: function() {
                submitBtn.prop('disabled', false).html('<i class="fas fa-save"></i> Simpan Perubahan');
            }
        });
    });
});

function loadProducts() {
    const itemType = $('#item_type').val();
    const itemSelect = $('#item_id');
    const currentItemId = '{{ $transfer->item_id }}';
    
    itemSelect.html('<option value="">Memuat...</option>');
    
    const url = itemType === 'finished' ? '{{ route("api.finished-products") }}' : '{{ route("api.semi-finished-products") }}';
    $.get(url)
        .done(function(data) {
            let options = '<option value="">Pilih Produk</option>';
            data.forEach(function(item) { 
                const selected = item.id == currentItemId ? 'selected' : '';
                options += `<option value="${item.id}" ${selected}>${item.name}</option>`; 
            });
            itemSelect.html(options);
            updateStockInfo();
        })
        .fail(function() { 
            itemSelect.html('<option value="">Error memuat data</option>'); 
        });
}

function updateStockInfo() {
    const itemType = $('#item_type').val();
    const itemId = $('#item_id').val();
    const fromBranchId = '{{ $transfer->from_branch_id }}';
    const toBranchId = $('#to_branch_id').val();
    const fromEl = $('.from-stock');
    const toEl = $('.to-stock');
    
    fromEl.text(''); toEl.text('');
    
    if (!itemType || !itemId) return;
    
    if (fromBranchId) {
        checkStock(itemType, itemId, fromBranchId, fromEl, 'Stok tersedia: ');
    }
    if (toBranchId) {
        checkStock(itemType, itemId, toBranchId, toEl, 'Stok saat ini: ');
    }
}

function checkStock(itemType, itemId, branchId, $target, prefix) {
    const url = '{{ route("api.stock.check", ["itemType" => ":itemType", "itemId" => ":itemId", "branchId" => ":branchId"]) }}'
        .replace(':itemType', itemType)
        .replace(':itemId', itemId)
        .replace(':branchId', branchId);
    $.get(url)
        .done(function(data) { $target.text(prefix + data.stock + ' unit'); })
        .fail(function() { $target.text('Error memuat stok'); });
}

function cancelTransfer() {
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
                url: '{{ route("stock-transfer.cancel", $transfer->id) }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Dibatalkan!', response.message, 'success')
                            .then(() => window.location.href = '{{ route("stock-transfer.index") }}');
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
@endsection
