@extends('layouts.app')

@section('title', 'Edit Transfer Stok')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-orange-50 via-white to-red-50 py-6">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Header Section --}}
            <x-form.header title="Transfer Stok" backRoute="{{ route('stock-transfer.index') }}" />

            {{-- Status Warning --}}
            @if ($stock_transfer->status !== 'sent')
                <div
                    class="mb-6 bg-gradient-to-br from-amber-50 to-orange-50 rounded-xl p-4 border border-amber-200 flex items-start gap-3">
                    <div class="flex-shrink-0 mt-0.5">
                        <i class="fas fa-exclamation-triangle text-amber-600 text-lg"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-amber-900">Transfer Terkunci</h3>
                        <p class="text-sm text-amber-800 mt-1">Transfer ini sudah berstatus
                            <strong>{{ ucfirst($stock_transfer->status) }}</strong> dan tidak dapat diedit.</p>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Main Form Card --}}
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                        {{-- Card Header --}}
                        <x-form.card-header title="Edit Transfer Stok #{{ $stock_transfer->id }}" type="edit" />

                        {{-- Card Body --}}
                        <div class="p-6 sm:p-8">
                            <form id="transferForm" onsubmit="return validateAndSubmitTransfer(event)"
                                {{ $stock_transfer->status !== 'sent' ? 'style=pointer-events:none;opacity:0.6;' : '' }}>
                                @csrf
                                @method('PUT')

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
                                            <label for="from_branch_id"
                                                class="block text-sm font-semibold text-gray-700 mb-2">
                                                Cabang Asal <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text" id="from_branch_display"
                                                class="w-full px-4 py-3 border rounded-xl bg-gray-50 text-gray-700 cursor-not-allowed border-gray-200"
                                                value="{{ $stock_transfer->fromBranch->name ?? 'Cabang tidak ditemukan' }}"
                                                disabled>
                                            <p class="mt-2 text-sm text-gray-600">
                                                <i class="bi bi-info-circle mr-1"></i>Cabang asal tidak dapat diubah
                                            </p>
                                        </div>

                                        {{-- To Branch --}}
                                        <div>
                                            <label for="to_branch_id"
                                                class="block text-sm font-semibold text-gray-700 mb-2">
                                                Cabang Tujuan <span class="text-red-500">*</span>
                                            </label>
                                            <select id="to_branch_id" name="to_branch_id" required
                                                class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200"
                                                {{ $stock_transfer->status !== 'sent' ? 'disabled' : '' }}>
                                                <option value="">Pilih Cabang Tujuan</option>
                                                @foreach ($branches as $branch)
                                                    <option value="{{ $branch->id }}"
                                                        {{ $stock_transfer->to_branch_id == $branch->id ? 'selected' : '' }}>
                                                        {{ $branch->name }}
                                                    </option>
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
                                    <div class="flex items-center mb-6">
                                        <div
                                            class="w-8 h-8 bg-gradient-to-br from-green-500 to-green-600 rounded-lg flex items-center justify-center mr-3">
                                            <i class="bi bi-boxes text-white text-sm"></i>
                                        </div>
                                        <h3 class="text-lg font-semibold text-gray-900">Item Transfer</h3>
                                    </div>

                                    <div
                                        class="transfer-item-row border border-gray-200 rounded-2xl p-0 bg-white hover:border-orange-200 hover:shadow-md transition-all duration-300 mb-6 overflow-hidden group">

                                        {{-- Row Header --}}
                                        <div
                                            class="bg-gray-50 group-hover:bg-orange-50 px-5 py-2 border-b border-gray-100 group-hover:border-orange-100 flex justify-between items-center transition-colors">
                                            <span
                                                class="text-[10px] font-bold uppercase tracking-wider text-gray-400 group-hover:text-orange-500 flex items-center gap-2">
                                                <i class="fas fa-box"></i> Item #1
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
                                                            required
                                                            {{ $stock_transfer->status !== 'sent' ? 'disabled' : '' }}>
                                                            <option value="">Pilih Jenis</option>
                                                            <option value="finished"
                                                                {{ $stock_transfer->item_type === 'finished' ? 'selected' : '' }}>
                                                                Produk Jadi</option>
                                                            <option value="semi-finished"
                                                                {{ $stock_transfer->item_type === 'semi-finished' ? 'selected' : '' }}>
                                                                Produk Setengah Jadi</option>
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
                                                            name="item_id" required
                                                            {{ $stock_transfer->status !== 'sent' ? 'disabled' : '' }}>
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
                                                        <span
                                                            class="unit-abbr text-orange-600 font-mono text-[10px]"></span>
                                                    </label>
                                                    <div class="relative">
                                                        <input type="number"
                                                            class="quantity w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all text-sm font-bold text-gray-800"
                                                            name="quantity" min="1" step="1" required
                                                            placeholder="0" value="{{ $stock_transfer->quantity }}"
                                                            {{ $stock_transfer->status !== 'sent' ? 'disabled' : '' }} />
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
                                                    name="notes"
                                                    placeholder="Contoh: Barang titipan, stok mendesak, dll..."
                                                    value="{{ $stock_transfer->notes }}"
                                                    {{ $stock_transfer->status !== 'sent' ? 'disabled' : '' }} />
                                            </div>
                                        </div>
                                    </div>
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
                                        <p><i class="bi bi-info-circle mr-2"></i>Ringkasan transfer akan ditampilkan di
                                            sini.</p>
                                    </div>
                                </div>

                                {{-- Form Actions --}}
                                @if ($stock_transfer->status === 'sent')
                                    <div class="border-t border-gray-200 pt-6">
                                        <div class="flex flex-col sm:flex-row gap-3 sm:justify-end">
                                            <button type="button" onclick="cancelTransfer()"
                                                class="inline-flex items-center justify-center px-6 py-3 border border-rose-300 rounded-xl text-sm font-medium text-rose-700 bg-white hover:bg-rose-50 hover:border-rose-400 transition-all duration-200 shadow-sm hover:shadow-md">
                                                <i class="fas fa-times mr-2"></i>Batalkan Transfer
                                            </button>
                                            <button type="submit"
                                                class="inline-flex items-center justify-center px-8 py-3 bg-gradient-to-r from-orange-600 to-red-600 border border-transparent rounded-xl text-sm font-medium text-white hover:from-orange-700 hover:to-red-700 focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                                <i class="fas fa-save mr-2"></i>Simpan Perubahan
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Info Card Sidebar --}}
                <div class="lg:col-span-1 space-y-6">
                    {{-- Transfer Info Card --}}
                    <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-info-circle text-white text-lg"></i>
                                <h3 class="text-white font-semibold">Informasi Transfer</h3>
                            </div>
                        </div>
                        <div class="p-6 space-y-4">
                            <div>
                                <p class="text-xs font-bold text-gray-500 uppercase tracking-tight mb-1">ID Transfer</p>
                                <p class="text-lg font-semibold text-gray-900">#{{ $stock_transfer->id }}</p>
                            </div>
                            <div class="border-t border-gray-200 pt-4">
                                <p class="text-xs font-bold text-gray-500 uppercase tracking-tight mb-2">Status</p>
                                @switch($stock_transfer->status)
                                    @case('sent')
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-clock mr-1.5"></i>sent
                                        </span>
                                    @break

                                    @case('sent')
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                            <i class="fas fa-paper-plane mr-1.5"></i>Dikirim
                                        </span>
                                    @break

                                    @case('accepted')
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1.5"></i>Diterima
                                        </span>
                                    @break

                                    @case('rejected')
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                            <i class="fas fa-times-circle mr-1.5"></i>Ditolak
                                        </span>
                                    @break

                                    @default
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                            {{ ucfirst($stock_transfer->status) }}
                                        </span>
                                @endswitch
                            </div>
                            <div class="border-t border-gray-200 pt-4">
                                <p class="text-xs font-bold text-gray-500 uppercase tracking-tight mb-1">Dibuat</p>
                                <p class="text-sm text-gray-700">{{ $stock_transfer->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                            @if ($stock_transfer->updated_at != $stock_transfer->created_at)
                                <div class="border-t border-gray-200 pt-4">
                                    <p class="text-xs font-bold text-gray-500 uppercase tracking-tight mb-1">Diperbarui</p>
                                    <p class="text-sm text-gray-700">{{ $stock_transfer->updated_at->format('d/m/Y H:i') }}</p>
                                </div>
                            @endif
                            @if ($stock_transfer->response_notes)
                                <div class="border-t border-gray-200 pt-4">
                                    <p class="text-xs font-bold text-gray-500 uppercase tracking-tight mb-2">Catatan Respon
                                    </p>
                                    <p class="text-sm text-gray-700">{{ $stock_transfer->response_notes }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Tips Card --}}
                    @if ($stock_transfer->status === 'sent')
                        <div
                            class="bg-gradient-to-br from-orange-50 to-red-50 rounded-2xl shadow-xl border border-orange-200 overflow-hidden">
                            <div class="bg-gradient-to-r from-orange-500 to-red-600 px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <i class="fas fa-lightbulb text-white text-lg"></i>
                                    <h3 class="text-white font-semibold">Tips</h3>
                                </div>
                            </div>
                            <div class="p-6">
                                <ul class="space-y-3 text-sm text-gray-700">
                                    <li class="flex gap-2">
                                        <i class="fas fa-check text-orange-500 font-bold mt-0.5 flex-shrink-0"></i>
                                        <span>Transfer masih dapat diedit selama berstatus sent</span>
                                    </li>
                                    <li class="flex gap-2">
                                        <i class="fas fa-check text-orange-500 font-bold mt-0.5 flex-shrink-0"></i>
                                        <span>Setelah dikirim, transfer tidak dapat diedit</span>
                                    </li>
                                    <li class="flex gap-2">
                                        <i class="fas fa-check text-orange-500 font-bold mt-0.5 flex-shrink-0"></i>
                                        <span>Pastikan data sudah benar sebelum mengirim</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @include('components.init-tooltips')

    <script>
        $(document).ready(function() {
            const currentItemType = '{{ $stock_transfer->item_type }}';
            const currentItemId = '{{ $stock_transfer->item_id }}';

            loadProducts(currentItemType, currentItemId);
            updateStockInfo();
            updateTransferSummary();
            if (window.initTooltips) window.initTooltips();

            $('.item-type').on('change', function() {
                loadProducts();
                updateStockInfo();
            });

            $('.item-id, .quantity').on('change', function() {
                updateStockInfo();
                updateTransferSummary();
            });

            $('#to_branch_id').on('change', function() {
                updateStockInfo();
                updateTransferSummary();
            });

            $('#transferForm').submit(function(e) {
                e.preventDefault();

                @if ($stock_transfer->status !== 'sent')
                    return false;
                @endif

                const submitBtn = $(this).find('button[type="submit"]');
                submitBtn.prop('disabled', true).html(
                '<i class="fas fa-spinner fa-spin"></i> Menyimpan...');

                const formData = new FormData();
                formData.append('to_branch_id', $('#to_branch_id').val());
                formData.append('item_type', $('.item-type').val());
                formData.append('item_id', $('.item-id').val());
                formData.append('quantity', $('.quantity').val());
                formData.append('notes', $('.notes').val());
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('_method', 'PUT');

                $.ajax({
                    url: '{{ route('stock-transfer.update', $stock_transfer->id) }}',
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
                                window.location.href =
                                    '{{ route('stock-transfer.index') }}';
                            });
                        } else {
                            Swal.fire({
                                title: 'Gagal!',
                                text: response.message,
                                icon: 'error'
                            });
                        }
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON;
                        Swal.fire({
                            title: 'Error!',
                            text: (response && response.message) ||
                                'Terjadi kesalahan pada server',
                            icon: 'error'
                        });
                    },
                    complete: function() {
                        submitBtn.prop('disabled', false).html(
                            '<i class="fas fa-save"></i> Simpan Perubahan');
                    }
                });
            });
        });

        function loadProducts(itemType, currentItemId) {
            const type = itemType || $('.item-type').val();
            const itemSelect = $('.item-id');
            const cItemId = currentItemId || '{{ $stock_transfer->item_id }}';

            if (!type) {
                itemSelect.prop('disabled', true).html('<option value="">Pilih jenis produk</option>');
                return;
            }

            itemSelect.html('<option value="">Memuat...</option>').prop('disabled', false);

            const url = type === 'finished' ? '{{ route('api.finished-products') }}' :
                '{{ route('api.semi-finished-products') }}';
            $.get(url)
                .done(function(data) {
                    let options = '<option value="">Pilih Produk</option>';
                    data.forEach(function(item) {
                        const abbr = item.unit && item.unit.abbreviation ? item.unit.abbreviation : '';
                        const nameWithUnit = abbr ? `${item.name} (${abbr})` : item.name;
                        const selected = item.id == cItemId ? 'selected' : '';
                        options +=
                            `<option value="${item.id}" data-unit-abbr="${abbr}" ${selected}>${nameWithUnit}</option>`;
                    });
                    itemSelect.html(options);
                    updateStockInfo();
                })
                .fail(function() {
                    itemSelect.html('<option value="">Error memuat data</option>');
                });
        }

        function updateStockInfo() {
            const itemType = $('.item-type').val();
            const itemId = $('.item-id').val();
            const fromBranchId = '{{ $stock_transfer->from_branch_id }}';
            const toBranchId = $('#to_branch_id').val();
            const fromEl = $('.from-stock');
            const toEl = $('.to-stock');

            const selectedOption = $('.item-id option:selected');
            const abbr = selectedOption.data('unit-abbr') || '';
            $('.unit-abbr').text(abbr ? `(${abbr})` : '');

            fromEl.text('Stok tersedia: -');
            toEl.text('Stok tujuan: -');

            if (!itemType || !itemId) return;

            if (fromBranchId) {
                checkStock(itemType, itemId, fromBranchId, fromEl, 'Stok tersedia: ');
            }
            if (toBranchId) {
                checkStock(itemType, itemId, toBranchId, toEl, 'Stok tujuan: ');
            }
        }

        function checkStock(itemType, itemId, branchId, $target, prefix) {
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
            const fromBranchName = '{{ $stock_transfer->fromBranch->name ?? 'Cabang tidak ditemukan' }}';
            const toBranchName = $('#to_branch_id option:selected').text();
            const itemType = $('.item-type').val();
            const itemId = $('.item-id').val();
            const quantity = $('.quantity').val();

            if (itemType && itemId && quantity && toBranchName && toBranchName !== 'Pilih Cabang Tujuan') {
                const abbr = $('.item-id option:selected').data('unit-abbr') || 'unit';
                const summary = `
            <ul class="list-unstyled space-y-2">
                <li><span class="font-medium text-gray-700">Total Kuantitas:</span> <span class="text-orange-600 font-semibold">${quantity} ${abbr}</span></li>
                <li><span class="font-medium text-gray-700">Dari:</span> <span class="text-gray-900">${fromBranchName}</span></li>
                <li><span class="font-medium text-gray-700">Ke:</span> <span class="text-gray-900">${toBranchName}</span></li>
            </ul>`;
                $('#transfer-summary').html(summary);
            } else {
                $('#transfer-summary').html(
                    '<p class="text-gray-600"><i class="bi bi-info-circle mr-2"></i>Lengkapi data transfer untuk melihat ringkasan.</p>'
                );
            }
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
                        url: '{{ route('stock-transfer.cancel', $stock_transfer->id) }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire('Dibatalkan!', response.message, 'success')
                                    .then(() => window.location.href =
                                        '{{ route('stock-transfer.index') }}');
                            } else {
                                Swal.fire('Gagal!', response.message, 'error');
                            }
                        },
                        error: function(xhr) {
                            const response = xhr.responseJSON;
                            Swal.fire('Error!', (response && response.message) || 'Terjadi kesalahan',
                                'error');
                        }
                    });
                }
            });
        }
    </script>
@endsection
