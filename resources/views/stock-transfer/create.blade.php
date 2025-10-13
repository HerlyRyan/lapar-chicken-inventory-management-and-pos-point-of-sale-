@extends('layouts.app')

@section('title', 'Buat Transfer Stok Baru')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mt-4">Buat Transfer Stok Baru</h1>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('stock-transfer.index') }}">Transfer Stok</a></li>
                <li class="breadcrumb-item active">Buat Baru</li>
            </ol>
        </div>
        <div>
            <a href="{{ route('stock-transfer.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
            </a>
        </div>
    </div>

    <!-- Transfer Form Card -->
    <div class="row">
        <div class="col-xl-8">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-exchange-alt me-1"></i>
                    Form Transfer Stok Antar Cabang
                </div>
                <div class="card-body">
                    <form id="transferForm">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="from_branch_id" class="form-label">Cabang Asal</label>
                                    <select class="form-select" id="from_branch_id" disabled>
                                        @if(isset($currentBranch) && $currentBranch)
                                            <option value="{{ $currentBranch->id }}" selected>{{ $currentBranch->name }}</option>
                                        @else
                                            <option value="">Pilih cabang aktif terlebih dahulu</option>
                                        @endif
                                    </select>
                                    <div class="form-text">
                                        <small class="text-muted">Cabang asal otomatis mengikuti cabang aktif.</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="to_branch_id" class="form-label">Cabang Tujuan</label>
                                    <select class="form-select" id="to_branch_id" name="to_branch_id" required>
                                        <option value="">Pilih Cabang Tujuan</option>
                                        @foreach($branches as $branch)
                                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Daftar Item Transfer -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label mb-0">Daftar Item Transfer <span id="itemCountBadge" class="badge bg-secondary ms-2">0 item</span></label>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="addItemRow" data-bs-toggle="tooltip" title="Tambah baris item">
                                    <i class="fas fa-plus"></i> Tambah Item
                                </button>
                            </div>
                            <div id="itemsContainer"></div>
                            
                            <!-- Template Row -->
                            <template id="itemRowTemplate">
                                <div class="row g-3 align-items-start transfer-item-row border rounded p-2 mb-2" data-index="__INDEX__">
                                    <div class="col-md-3">
                                        <label class="form-label">Jenis Produk</label>
                                        <select class="form-select item-type" required>
                                            <option value="">Pilih Jenis</option>
                                            <option value="finished">Produk Jadi</option>
                                            <option value="semi-finished">Produk Setengah Jadi</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Produk</label>
                                        <select class="form-select item-id" required disabled>
                                            <option value="">Pilih jenis produk terlebih dahulu</option>
                                        </select>
                                        <div class="form-text">
                                            <small class="text-muted from-stock">&nbsp;</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Jumlah <span class="unit-abbr text-muted"></span></label>
                                        <input type="number" class="form-control quantity" min="1" step="1" required />
                                        <div class="form-text">
                                            <small class="text-muted to-stock">&nbsp;</small>
                                        </div>
                                    </div>
                                    <div class="col-md-1 d-grid">
                                        <button type="button" class="btn btn-outline-danger remove-item" data-bs-toggle="tooltip" title="Hapus item ini">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label">Catatan (Opsional)</label>
                                        <input type="text" class="form-control notes" placeholder="Catatan item..." />
                                    </div>
                                </div>
                            </template>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="button" class="btn btn-outline-secondary me-md-2" onclick="resetForm()" data-bs-toggle="tooltip" title="Bersihkan formulir">
                                <i class="fas fa-undo"></i> Reset
                            </button>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-paper-plane"></i> Kirim Transfer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Transfer Summary Card -->
        <div class="col-xl-4">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-receipt me-1"></i>
                    Ringkasan Transfer
                </div>
                <div class="card-body">
                    <div id="transfer-summary" class="text-muted">
                        <p>Pilih produk dan cabang untuk melihat ringkasan transfer.</p>
                    </div>
                </div>
            </div>

            <!-- Help Card -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-info-circle me-1"></i>
                    Panduan Transfer
                </div>
                <div class="card-body">
                    <ul class="list-unstyled small">
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Pastikan stok tersedia di cabang asal</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Pilih cabang tujuan yang tepat</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Masukkan jumlah yang akan ditransfer</li>
                        <li class="mb-0"><i class="fas fa-check text-success me-2"></i>Transfer akan menunggu persetujuan cabang tujuan</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@include('components.init-tooltips')

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize with one row
    addItemRow();
    updateItemCountBadge();
    if (window.initTooltips) window.initTooltips();

    // Global to-branch change updates to-stock info per row
    $('#to_branch_id').on('change', function() {
        $('.transfer-item-row').each(function() {
            updateStockInfoForRow($(this));
        });
        updateTransferSummary();
    });

    // Add item row
    $('#addItemRow').on('click', function() {
        addItemRow();
        updateItemCountBadge();
        if (window.initTooltips) window.initTooltips();
    });

    // Delegate row interactions
    $('#itemsContainer')
        .on('change', '.item-type', function() {
            const row = $(this).closest('.transfer-item-row');
            const type = $(this).val();
            const itemSelect = row.find('.item-id');
            if (!type) {
                itemSelect.prop('disabled', true).html('<option value="">Pilih jenis produk terlebih dahulu</option>');
                updateStockInfoForRow(row);
                updateTransferSummary();
                return;
            }
            itemSelect.prop('disabled', false).html('<option value="">Memuat...</option>');
            loadItemsForRow(row, type);
        })
        .on('change', '.item-id, .quantity', function() {
            const row = $(this).closest('.transfer-item-row');
            updateStockInfoForRow(row);
            updateTransferSummary();
        })
        .on('click', '.remove-item', function() {
            $(this).closest('.transfer-item-row').remove();
            if ($('.transfer-item-row').length === 0) addItemRow();
            updateTransferSummary();
            updateItemCountBadge();
            if (window.initTooltips) window.initTooltips();
        });

    // Submit form as batch
    $('#transferForm').submit(function(e) {
        e.preventDefault();
        const submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Memproses...');

        const toBranchId = $('#to_branch_id').val();
        const items = [];
        let valid = true;
        $('.transfer-item-row').each(function() {
            const row = $(this);
            const item_type = row.find('.item-type').val();
            const item_id = row.find('.item-id').val();
            const quantity = row.find('.quantity').val();
            const notes = row.find('.notes').val();
            if (!item_type || !item_id || !quantity || !toBranchId) {
                valid = false;
                return false; // break
            }
            items.push({ item_type, item_id, to_branch_id: toBranchId, quantity, notes });
        });
        if (!valid || items.length === 0) {
            Swal.fire({ title: 'Validasi', text: 'Lengkapi semua item dan cabang tujuan.', icon: 'warning' });
            submitBtn.prop('disabled', false).html('<i class="fas fa-paper-plane"></i> Kirim Transfer');
            return;
        }

        const formData = new FormData();
        items.forEach((it, i) => {
            formData.append(`items[${i}][item_type]`, it.item_type);
            formData.append(`items[${i}][item_id]`, it.item_id);
            formData.append(`items[${i}][to_branch_id]`, it.to_branch_id);
            formData.append(`items[${i}][quantity]`, it.quantity);
            if (it.notes) formData.append(`items[${i}][notes]`, it.notes);
        });
        formData.append('_token', '{{ csrf_token() }}');

        $.ajax({
            url: '{{ route("stock-transfer.store") }}',
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
                submitBtn.prop('disabled', false).html('<i class="fas fa-paper-plane"></i> Kirim Transfer');
            }
        });
    });
});

function addItemRow() {
    const idx = $('.transfer-item-row').length;
    const tpl = document.getElementById('itemRowTemplate').innerHTML.replaceAll('__INDEX__', idx);
    $('#itemsContainer').append(tpl);
    updateItemCountBadge();
}

function loadItemsForRow(row, itemType) {
    const itemSelect = row.find('.item-id');
    const url = itemType === 'finished' ? '{{ route("api.finished-products") }}' : '{{ route("api.semi-finished-products") }}';
    $.get(url)
        .done(function(data) {
            let options = '<option value="">Pilih Produk</option>';
            data.forEach(function(item) {
                const abbr = item.unit && item.unit.abbreviation ? item.unit.abbreviation : '';
                const nameWithUnit = abbr ? `${item.name} (${abbr})` : item.name;
                options += `<option value="${item.id}" data-unit-abbr="${abbr}">${nameWithUnit}</option>`;
            });
            itemSelect.html(options);
        })
        .fail(function() { itemSelect.html('<option value="">Error memuat data</option>'); });
}

function updateStockInfoForRow(row) {
    const itemType = row.find('.item-type').val();
    const itemId = row.find('.item-id').val();
    const fromBranchId = $('#from_branch_id').val();
    const toBranchId = $('#to_branch_id').val();
    const fromEl = row.find('.from-stock');
    const toEl = row.find('.to-stock');
    // Update unit badge next to Jumlah label based on selected product
    const selectedOption = row.find('.item-id option:selected');
    const abbr = selectedOption.data('unit-abbr') || '';
    row.find('.unit-abbr').text(abbr ? `(${abbr})` : '');
    fromEl.text(''); toEl.text('');
    if (!itemType || !itemId) return;
    if (fromBranchId) {
        checkStockForRow(itemType, itemId, fromBranchId, fromEl, 'Stok tersedia: ');
    }
    if (toBranchId) {
        checkStockForRow(itemType, itemId, toBranchId, toEl, 'Stok saat ini: ');
    }
}

function checkStockForRow(itemType, itemId, branchId, $target, prefix) {
    const url = '{{ route("api.stock.check", ["itemType" => ":itemType", "itemId" => ":itemId", "branchId" => ":branchId"]) }}'
        .replace(':itemType', itemType)
        .replace(':itemId', itemId)
        .replace(':branchId', branchId);
    $.get(url)
        .done(function(data) {
            const abbr = data.unit_abbr || 'unit';
            $target.text(prefix + data.stock + ' ' + abbr);
        })
        .fail(function() { $target.text('Error memuat stok'); });
}

function updateTransferSummary() {
    const fromBranchName = $('#from_branch_id option:selected').text();
    const toBranchName = $('#to_branch_id option:selected').text();
    let totalItems = 0; let totalQty = 0;
    const unitSet = new Set();
    $('.transfer-item-row').each(function() {
        const row = $(this);
        if (row.find('.item-type').val() && row.find('.item-id').val() && row.find('.quantity').val()) {
            totalItems += 1; totalQty += parseInt(row.find('.quantity').val(), 10) || 0;
            const abbr = row.find('.item-id option:selected').data('unit-abbr');
            if (abbr) unitSet.add(abbr);
        }
    });
    if (totalItems > 0 && toBranchName && toBranchName !== 'Pilih Cabang Tujuan') {
        let unitText = '';
        if (unitSet.size === 1) {
            unitText = ' ' + Array.from(unitSet)[0];
        } else if (unitSet.size > 1) {
            unitText = ' (beragam satuan)';
        }
        const summary = `
            <h6>Ringkasan:</h6>
            <ul class="list-unstyled">
                <li><strong>Total Item:</strong> ${totalItems}</li>
                <li><strong>Total Kuantitas:</strong> ${totalQty}${unitText}</li>
                <li><strong>Dari:</strong> ${fromBranchName}</li>
                <li><strong>Ke:</strong> ${toBranchName}</li>
            </ul>`;
        $('#transfer-summary').html(summary);
    } else {
        $('#transfer-summary').html('<p class="text-muted">Pilih cabang tujuan dan tambahkan item untuk melihat ringkasan.</p>');
    }
}

function resetForm() {
    $('#transferForm')[0].reset();
    $('#itemsContainer').empty();
    addItemRow();
    updateTransferSummary();
    updateItemCountBadge();
    if (window.initTooltips) window.initTooltips();
}

function updateItemCountBadge() {
    const count = $('.transfer-item-row').length;
    const label = count + ' item';
    $('#itemCountBadge').text(label);
}
</script>
@endpush
@endsection
