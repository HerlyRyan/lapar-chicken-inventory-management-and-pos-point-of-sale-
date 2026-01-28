@extends('layouts.app')

@section('title', 'Buat Transfer Stok Baru')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-orange-50 via-white to-red-50 py-6">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Header Section --}}
            <x-form.header title="Transfer Stok" backRoute="{{ route('stock-transfer.index') }}" />

            {{-- Main Form Card --}}
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                {{-- Card Header --}}
                <x-form.card-header title="Transfer Stok Antar Cabang" type="add" />

                {{-- Card Body --}}
                <div class="p-6 sm:p-8">
                    <form id="transferForm" onsubmit="return validateAndSubmitTransfer(event)">
                        @csrf

                        {{-- Branch Selection Section --}}
                        <div class="mb-8">
                            <div class="flex items-center mb-6">
                                <div
                                    class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center mr-3">
                                    <i class="bi bi-building text-white text-sm"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900">Pilih Cabang</h3>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                {{-- From Branch --}}
                                <div>
                                    <label for="from_branch_id" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Cabang Asal <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="from_branch_display"
                                        class="w-full px-4 py-3 border rounded-xl bg-gray-50 text-gray-700 cursor-not-allowed"
                                        disabled>
                                    <input type="hidden" id="from_branch_id" name="from_branch_id">
                                    <p class="mt-2 text-sm text-gray-600">
                                        <i class="bi bi-info-circle mr-1"></i>Otomatis mengikuti cabang aktif
                                    </p>
                                </div>

                                {{-- To Branch --}}
                                <div>
                                    <label for="to_branch_id" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Cabang Tujuan <span class="text-red-500">*</span>
                                    </label>
                                    <select id="to_branch_id" name="to_branch_id" required
                                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200">
                                        <option value="">Pilih Cabang Tujuan</option>
                                        @foreach ($branches as $branch)
                                            @if (!isset($currentBranch) || $currentBranch->id !== $branch->id)
                                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                    <p class="mt-2 text-sm text-gray-600">
                                        <i class="bi bi-info-circle mr-1"></i>Pilih tujuan transfer stok
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Items Section --}}
                        <div class="mb-8">
                            <div class="flex items-center justify-between mb-6">
                                <div class="flex items-center">
                                    <div
                                        class="w-8 h-8 bg-gradient-to-br from-green-500 to-green-600 rounded-lg flex items-center justify-center mr-3">
                                        <i class="bi bi-boxes text-white text-sm"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900">Daftar Item Transfer</h3>
                                        <p class="text-sm text-gray-600 mt-1"><span id="itemCountBadge"
                                                class="font-semibold text-orange-600">0</span> item dipilih</p>
                                    </div>
                                </div>
                                <button type="button" id="addItemRow"
                                    class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-orange-600 to-red-600 text-white rounded-lg hover:from-orange-700 hover:to-red-700 transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                                    <i class="bi bi-plus-circle mr-2"></i>Tambah Item
                                </button>
                            </div>

                            <div id="itemsContainer" class="space-y-4"></div>

                            {{-- Item Template --}}
                            <template id="itemRowTemplate">
                                <div class="transfer-item-row group border border-gray-200 rounded-2xl p-0 bg-white hover:border-orange-200 hover:shadow-md transition-all duration-300 mb-6 overflow-hidden"
                                    data-index="__INDEX__">

                                    {{-- Row Header: Penanda Urutan --}}
                                    <div
                                        class="bg-gray-50 group-hover:bg-orange-50 px-5 py-2 border-b border-gray-100 group-hover:border-orange-100 flex justify-between items-center transition-colors">
                                        <span
                                            class="text-[10px] font-bold uppercase tracking-wider text-gray-400 group-hover:text-orange-500 flex items-center gap-2">
                                            <i class="fas fa-box"></i> Item #__INDEX__
                                        </span>
                                        <div
                                            class="h-1.5 w-1.5 rounded-full bg-gray-300 group-hover:bg-orange-400 animate-pulse">
                                        </div>
                                    </div>

                                    <div class="p-5">
                                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
                                            {{-- Item Type --}}
                                            <div class="lg:col-span-3">
                                                <label
                                                    class="block text-xs font-bold text-gray-500 uppercase tracking-tight mb-2">
                                                    Jenis Produk <span class="text-red-500">*</span>
                                                </label>
                                                <div class="relative">
                                                    <select
                                                        class="item-type w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all appearance-none cursor-pointer text-sm font-medium text-gray-700"
                                                        required>
                                                        <option value="">Pilih Jenis</option>
                                                        <option value="finished">Produk Jadi</option>
                                                        <option value="semi-finished">Produk Setengah Jadi</option>
                                                    </select>
                                                    <div
                                                        class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                                        <i class="fas fa-tag text-xs"></i>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Product Selection --}}
                                            <div class="lg:col-span-4">
                                                <label
                                                    class="block text-xs font-bold text-gray-500 uppercase tracking-tight mb-2">
                                                    Produk <span class="text-red-500">*</span>
                                                </label>
                                                <div class="relative">
                                                    <select
                                                        class="item-id w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all appearance-none disabled:bg-gray-100 disabled:cursor-not-allowed text-sm font-medium text-gray-700"
                                                        required disabled>
                                                        <option value="">Pilih jenis produk</option>
                                                    </select>
                                                    <div
                                                        class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                                        <i class="fas fa-search text-xs"></i>
                                                    </div>
                                                </div>
                                                {{-- Stock Indicator Box --}}
                                                <div
                                                    class="mt-3 flex items-center gap-2 px-3 py-1.5 bg-blue-50/50 rounded-lg border border-blue-100/50">
                                                    <i class="fas fa-warehouse text-[10px] text-blue-500"></i>
                                                    <span class="from-stock text-[11px] font-bold text-blue-700">Stok
                                                        tersedia: -</span>
                                                </div>
                                            </div>

                                            {{-- Quantity --}}
                                            <div class="lg:col-span-3">
                                                <label
                                                    class="block text-xs font-bold text-gray-500 uppercase tracking-tight mb-2 flex justify-between">
                                                    <span>Jumlah <span class="text-red-500">*</span></span>
                                                    <span class="unit-abbr text-orange-600 font-mono text-[10px]"></span>
                                                </label>
                                                <div class="relative">
                                                    <input type="number"
                                                        class="quantity w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all text-sm font-bold text-gray-800"
                                                        min="1" step="1" required placeholder="0">
                                                    <div
                                                        class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                                        <i class="fas fa-layer-group text-xs"></i>
                                                    </div>
                                                </div>
                                                <div
                                                    class="mt-3 flex items-center gap-2 px-3 py-1.5 bg-emerald-50/50 rounded-lg border border-emerald-100/50">
                                                    <i class="fas fa-map-marker-alt text-[10px] text-emerald-500"></i>
                                                    <span class="to-stock text-[11px] font-bold text-emerald-700">Stok
                                                        tujuan: -</span>
                                                </div>
                                            </div>

                                            {{-- Remove Button --}}
                                            <div class="lg:col-span-2 flex items-start lg:mt-6">
                                                <button type="button"
                                                    class="remove-item group/btn w-full px-4 py-2.5 bg-white text-rose-500 border border-rose-100 rounded-xl hover:bg-rose-500 hover:text-white hover:border-rose-500 hover:shadow-lg hover:shadow-rose-200 transition-all duration-300 font-bold text-xs uppercase tracking-widest flex items-center justify-center gap-2">
                                                    <i
                                                        class="fas fa-trash-alt transition-transform group-hover/btn:scale-110"></i>
                                                    <span class="lg:hidden">Hapus Item</span>
                                                </button>
                                            </div>
                                        </div>

                                        {{-- Notes --}}
                                        <div class="mt-5 pt-4 border-t border-dashed border-gray-200">
                                            <div class="flex items-center gap-2 mb-2">
                                                <i class="fas fa-pen-nib text-[10px] text-gray-400"></i>
                                                <label
                                                    class="block text-xs font-bold text-gray-500 uppercase tracking-tight">
                                                    Catatan Tambahan <span
                                                        class="text-gray-300 font-normal italic">(Opsional)</span>
                                                </label>
                                            </div>
                                            <input type="text"
                                                class="notes w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-xl focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all text-sm placeholder:text-gray-400"
                                                placeholder="Contoh: Barang titipan, stok mendesak, dll...">
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        {{-- Summary Section --}}
                        <div
                            class="mb-8 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-6 border border-blue-100">
                            <div class="flex items-center mb-4">
                                <div
                                    class="w-8 h-8 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center mr-3">
                                    <i class="bi bi-receipt text-white text-sm"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900">Ringkasan Transfer</h3>
                            </div>
                            <div id="transfer-summary" class="text-gray-600">
                                <p><i class="bi bi-info-circle mr-2"></i>Pilih cabang tujuan dan tambahkan item untuk
                                    melihat ringkasan.</p>
                            </div>
                        </div>

                        {{-- Form Actions --}}
                        <div class="border-t border-gray-200 pt-6">
                            <div class="flex flex-col sm:flex-row gap-3 sm:justify-end">
                                <button type="button" onclick="resetForm()"
                                    class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 rounded-xl text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 shadow-sm hover:shadow-md">
                                    <i class="bi bi-arrow-counterclockwise mr-2"></i>Reset
                                </button>
                                <button type="submit"
                                    class="inline-flex items-center justify-center px-8 py-3 bg-gradient-to-r from-orange-600 to-red-600 border border-transparent rounded-xl text-sm font-medium text-white hover:from-orange-700 hover:to-red-700 focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                    <i class="bi bi-send-check mr-2"></i>Kirim Transfer
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @include('components.init-tooltips')

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // Initialize from branch
            @if (isset($currentBranch) && $currentBranch)
                $('#from_branch_id').val('{{ $currentBranch->id }}');
                $('#from_branch_display').val('{{ $currentBranch->name }}');
            @endif

            addItemRow();
            updateItemCountBadge();
            if (window.initTooltips) window.initTooltips();

            $('#to_branch_id').on('change', function() {
                $('.transfer-item-row').each(function() {
                    updateStockInfoForRow($(this));
                });
                updateTransferSummary();
            });

            $('#addItemRow').on('click', function() {
                addItemRow();
                updateItemCountBadge();
                if (window.initTooltips) window.initTooltips();
            });

            $('#itemsContainer')
                .on('change', '.item-type', function() {
                    const row = $(this).closest('.transfer-item-row');
                    const type = $(this).val();
                    const itemSelect = row.find('.item-id');
                    if (!type) {
                        itemSelect.prop('disabled', true).html('<option value="">Pilih jenis produk</option>');
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
            const url = itemType === 'finished' ? '{{ route('api.finished-products') }}' :
                '{{ route('api.semi-finished-products') }}';
            $.get(url)
                .done(function(data) {
                    let options = '<option value="">Pilih Produk</option>';
                    data.forEach(function(item) {
                        const abbr = item.unit && item.unit.abbreviation ? item.unit.abbreviation : '';
                        const nameWithUnit = abbr ? `${item.name} (${abbr})` : item.name;
                        options +=
                            `<option value="${item.id}" data-unit-abbr="${abbr}">${nameWithUnit}</option>`;
                    });
                    itemSelect.html(options);
                })
                .fail(function() {
                    itemSelect.html('<option value="">Error memuat data</option>');
                });
        }

        function updateStockInfoForRow(row) {
            const itemType = row.find('.item-type').val();
            const itemId = row.find('.item-id').val();
            const fromBranchId = $('#from_branch_id').val();
            const toBranchId = $('#to_branch_id').val();
            const fromEl = row.find('.from-stock');
            const toEl = row.find('.to-stock');

            const selectedOption = row.find('.item-id option:selected');
            const abbr = selectedOption.data('unit-abbr') || '';
            row.find('.unit-abbr').text(abbr ? `(${abbr})` : '');

            fromEl.text('Stok tersedia: -');
            toEl.text('Stok tujuan: -');

            if (!itemType || !itemId) return;
            if (fromBranchId) {
                checkStockForRow(itemType, itemId, fromBranchId, fromEl, 'Stok tersedia: ');
            }
            if (toBranchId) {
                checkStockForRow(itemType, itemId, toBranchId, toEl, 'Stok tujuan: ');
            }
        }

        function checkStockForRow(itemType, itemId, branchId, $target, prefix) {
            const url =
                '{{ route('api.stock.check', ['itemType' => ':itemType', 'itemId' => ':itemId', 'branchId' => ':branchId']) }}'
                .replace(':itemType', itemType)
                .replace(':itemId', itemId)
                .replace(':branchId', branchId);
            $.get(url)
                .done(function(data) {
                    const abbr = data.unit_abbr || 'unit';
                    $target.text(prefix + data.stock + ' ' + abbr);
                })
                .fail(function() {
                    $target.text(prefix + 'Error');
                });
        }

        function updateTransferSummary() {
            const fromBranchName = $('#from_branch_display').val();
            const toBranchName = $('#to_branch_id option:selected').text();
            let totalItems = 0;
            let totalQty = 0;
            const unitSet = new Set();

            $('.transfer-item-row').each(function() {
                const row = $(this);
                if (row.find('.item-type').val() && row.find('.item-id').val() && row.find('.quantity').val()) {
                    totalItems += 1;
                    totalQty += parseInt(row.find('.quantity').val(), 10) || 0;
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
            <ul class="list-unstyled space-y-2">
                <li><span class="font-medium text-gray-700">Total Item:</span> <span class="text-orange-600 font-semibold">${totalItems}</span></li>
                <li><span class="font-medium text-gray-700">Total Kuantitas:</span> <span class="text-orange-600 font-semibold">${totalQty}${unitText}</span></li>
                <li><span class="font-medium text-gray-700">Dari:</span> <span class="text-gray-900">${fromBranchName}</span></li>
                <li><span class="font-medium text-gray-700">Ke:</span> <span class="text-gray-900">${toBranchName}</span></li>
            </ul>`;
                $('#transfer-summary').html(summary);
            } else {
                $('#transfer-summary').html(
                    '<p class="text-gray-600"><i class="bi bi-info-circle mr-2"></i>Pilih cabang tujuan dan tambahkan item untuk melihat ringkasan.</p>'
                );
            }
        }

        function resetForm() {
            $('#transferForm')[0].reset();
            $('#itemsContainer').empty();
            addItemRow();
            updateTransferSummary();
            updateItemCountBadge();
        }

        function updateItemCountBadge() {
            const count = $('.transfer-item-row').length;
            $('#itemCountBadge').text(count);
        }

        function validateAndSubmitTransfer(e) {
            e.preventDefault();
            const submitBtn = $(e.target).find('button[type="submit"]');
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
                    return false;
                }
                items.push({
                    item_type,
                    item_id,
                    to_branch_id: toBranchId,
                    quantity,
                    notes
                });
            });

            if (!valid || items.length === 0) {
                Swal.fire({
                    title: 'Validasi',
                    text: 'Lengkapi semua item dan cabang tujuan.',
                    icon: 'warning'
                });
                submitBtn.prop('disabled', false).html('<i class="bi bi-send-check mr-2"></i>Kirim Transfer');
                return false;
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
            formData.append('from_branch_id', $('#from_branch_id').val());

            $.ajax({
                url: '{{ route('stock-transfer.store') }}',
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
                            window.location.href = '{{ route('stock-transfer.index') }}';
                        });
                    } else {
                        Swal.fire({
                            title: 'Gagal!',
                            text: response.message,
                            icon: 'error'
                        });
                        submitBtn.prop('disabled', false).html(
                            '<i class="bi bi-send-check mr-2"></i>Kirim Transfer');
                    }
                },
                error: function(xhr) {
                    const response = xhr.responseJSON;
                    Swal.fire({
                        title: 'Error!',
                        text: (response && response.message) || 'Terjadi kesalahan pada server',
                        icon: 'error'
                    });
                    submitBtn.prop('disabled', false).html(
                        '<i class="bi bi-send-check mr-2"></i>Kirim Transfer');
                }
            });
            return false;
        }
    </script>

@endsection
