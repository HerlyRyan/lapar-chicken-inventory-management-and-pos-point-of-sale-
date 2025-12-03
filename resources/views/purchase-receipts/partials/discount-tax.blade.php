<div class="bg-white rounded-2xl border border-gray-200 overflow-hidden mb-6">
    <div class="p-6 sm:p-8">
        <div class="flex items-center mb-6">
            <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center mr-3">
                <i class="bi bi-percent text-white text-sm"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900">Diskon & Pajak (Opsional)</h3>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Diskon (Rp) <span class="text-gray-500 text-xs ml-1">opsional</span>
                </label>
                <input type="number" name="discount_amount" step="0.01" min="0"
                    class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 @error('discount_amount') border-red-300 ring-2 ring-red-200 @enderror"
                    value="{{ old('discount_amount', isset($model) ? $model->discount_amount : null) }}" placeholder="0" />
                <p class="mt-2 text-sm text-gray-600">
                    <i class="bi bi-info-circle mr-1"></i>Masukkan nilai diskon dalam rupiah. Biarkan kosong jika tidak ada diskon.
                </p>
                @error('discount_amount')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Pajak (Rp) <span class="text-gray-500 text-xs ml-1">opsional</span>
                </label>
                <input type="number" name="tax_amount" step="0.01" min="0"
                    class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 @error('tax_amount') border-red-300 ring-2 ring-red-200 @enderror"
                    value="{{ old('tax_amount', isset($model) ? $model->tax_amount : null) }}" placeholder="0" />
                <p class="mt-2 text-sm text-gray-600">
                    <i class="bi bi-info-circle mr-1"></i>Masukkan nilai pajak dalam rupiah. Biarkan kosong jika tidak ada pajak.
                </p>
                @error('tax_amount')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>
</div>
