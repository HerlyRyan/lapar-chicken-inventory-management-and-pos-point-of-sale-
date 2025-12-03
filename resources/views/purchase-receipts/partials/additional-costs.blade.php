@php($prefix = $prefix ?? 'pr')

<div class="bg-white rounded-2xl border border-gray-200 mt-3">
    <div class="px-4 py-3 bg-gray-50 rounded-t-2xl flex items-center justify-between">
        <h6 class="mb-0 text-lg font-semibold text-gray-900">Biaya Tambahan</h6>
        <button type="button"
            class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-xl text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-all js-add-cost-row"
            data-prefix="{{ $prefix }}">
            <i class="bi bi-plus-lg me-1 mr-2"></i>
            Tambah Biaya
        </button>
    </div>

    <div id="{{ $prefix }}-additional-costs-container" class="p-4">
        @php($costs = isset($existingCosts) ? $existingCosts : [])
        @if(!empty($costs) && count($costs))
            @foreach($costs as $i => $cost)
                @php($cn = data_get($cost, 'cost_name'))
                @php($am = data_get($cost, 'amount'))
                @php($nt = data_get($cost, 'notes'))
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-2 items-end mb-3 {{ $prefix }}-cost-row">
                    <div class="sm:col-span-5">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Biaya <span class="text-red-500">*</span></label>
                        <input type="text"
                               name="additional_costs[{{ $i }}][cost_name]"
                               value="{{ old("additional_costs.$i.cost_name", $cn) }}"
                               required
                               class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 @error("additional_costs.$i.cost_name") border-red-300 ring-2 ring-red-200 @enderror">
                    </div>

                    <div class="sm:col-span-3">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Jumlah <span class="text-red-500">*</span></label>
                        <input type="number" step="0.01" min="0"
                               name="additional_costs[{{ $i }}][amount]"
                               value="{{ old("additional_costs.$i.amount", $am) }}"
                               required
                               class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 @error("additional_costs.$i.amount") border-red-300 ring-2 ring-red-200 @enderror">
                    </div>

                    <div class="sm:col-span-3">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Catatan</label>
                        <input type="text"
                               name="additional_costs[{{ $i }}][notes]"
                               value="{{ old("additional_costs.$i.notes", $nt) }}"
                               class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 @error("additional_costs.$i.notes") border-red-300 ring-2 ring-red-200 @enderror"
                        >
                    </div>

                    <div class="sm:col-span-1">
                        <label class="block text-sm font-medium text-transparent mb-2">.</label>
                        <button type="button"
                                class="w-full inline-flex items-center justify-center px-3 py-3 border border-gray-300 rounded-xl text-sm font-medium text-red-600 bg-white hover:bg-red-50 transition-all js-remove-cost-row"
                                title="Hapus baris biaya">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            @endforeach
        @else
            <p class="text-sm text-gray-600">Belum ada biaya tambahan. Klik "Tambah Biaya".</p>
        @endif
    </div>

    <!-- Template for Additional Cost Row (Tailwind styled) -->
    <template id="{{ $prefix }}-additional-cost-row-template">
        <div class="grid grid-cols-1 sm:grid-cols-12 gap-2 items-end mb-3 {{ $prefix }}-cost-row">
            <div class="sm:col-span-5">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Biaya <span class="text-red-500">*</span></label>
                <input type="text" name="additional_costs[__INDEX__][cost_name]" required
                       class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200">
            </div>

            <div class="sm:col-span-3">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Jumlah <span class="text-red-500">*</span></label>
                <input type="number" step="0.01" min="0" name="additional_costs[__INDEX__][amount]" required
                       class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200">
            </div>

            <div class="sm:col-span-3">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Catatan</label>
                <input type="text" name="additional_costs[__INDEX__][notes]"
                       class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200">
            </div>

            <div class="sm:col-span-1">
                <label class="block text-sm font-medium text-transparent mb-2">.</label>
                <button type="button"
                        class="w-full inline-flex items-center justify-center px-3 py-3 border border-gray-300 rounded-xl text-sm font-medium text-red-600 bg-white hover:bg-red-50 transition-all js-remove-cost-row"
                        title="Hapus baris biaya">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>
    </template>
</div>
