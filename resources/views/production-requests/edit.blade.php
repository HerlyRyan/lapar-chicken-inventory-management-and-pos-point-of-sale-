@extends('layouts.app')
@php use Illuminate\Support\Facades\Storage; @endphp

@section('title', 'Edit Pengajuan Produksi')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-orange-50 via-white to-red-50 py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Header --}}
            <x-form.header title="Pengajuan Produksi" edit="true" :name="$productionRequest->request_code"
                backRoute="{{ route('production-requests.index') }}"
                detailRoute="{{ route('production-requests.show', $productionRequest) }}" />

            {{-- Main container --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
                {{-- Left / Main Form (span 2) --}}
                <div class="lg:col-span-2 space-y-6">
                    {{-- Informasi Pengajuan --}}
                    <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                        <x-form.card-header title="Informasi Pengajuan" type="edit" />
                        <div class="p-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Peruntukan</label>
                            <textarea name="purpose" form="productionRequestForm"
                                class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 @error('purpose') border-red-300 ring-2 ring-red-200 @enderror"
                                rows="3" placeholder="Contoh: Produksi 200 ayam marinasi untuk cabang X">{{ old('purpose', $productionRequest->purpose ?? '') }}</textarea>
                            @error('purpose')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Bahan Mentah yang Dibutuhkan --}}
                    <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                        <div class="flex items-center justify-between p-4 border-b">
                            <div class="flex items-center space-x-3">
                                <div
                                    class="w-9 h-9 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                                    <i class="bi bi-box-seam text-white"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-primary-700">Bahan Mentah yang Dibutuhkan</h3>
                            </div>
                            <button type="button" onclick="addMaterialRow()"
                                class="inline-flex items-center px-3 py-2 rounded-lg border text-sm font-medium text-orange-600 bg-white hover:bg-orange-50">
                                <i class="bi bi-plus me-2"></i> Tambah Bahan
                            </button>
                        </div>

                        <div class="p-6">
                            <form id="productionRequestForm"
                                action="{{ route('production-requests.update', $productionRequest->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div id="materials-container" class="space-y-4">
                                    @php
                                        $items = old('items', $productionRequest->items ?? []);
                                        if (empty($items)) {
                                            $items = [[]]; // fallback single empty row
                                        }
                                        $grandTotal = 0;
                                    @endphp

                                    @foreach ($items as $index => $item)
                                        @php
                                            // If item is model (Eloquent), access attributes
                                            $rawMaterialId = old(
                                                "items.$index.raw_material_id",
                                                is_object($item)
                                                    ? $item->raw_material_id ?? ($item->raw_material->id ?? '')
                                                    : $item['raw_material_id'] ?? '',
                                            );
                                            $requestedQty = old(
                                                "items.$index.requested_quantity",
                                                is_object($item)
                                                    ? $item->requested_quantity ?? ''
                                                    : $item['requested_quantity'] ?? '',
                                            );
                                            $unitCost = old(
                                                "items.$index.unit_cost",
                                                is_object($item) ? $item->unit_cost ?? '' : $item['unit_cost'] ?? '',
                                            );
                                            $notes = old(
                                                "items.$index.notes",
                                                is_object($item) ? $item->notes ?? '' : $item['notes'] ?? '',
                                            );
                                            $rowTotal = (float) ($requestedQty ?: 0) * (float) ($unitCost ?: 0);
                                            $grandTotal += $rowTotal;
                                        @endphp

                                        <div class="grid grid-cols-12 gap-4 items-end material-row"
                                            data-index="{{ $index }}">
                                            <div class="col-span-12 lg:col-span-4">
                                                <label class="block text-sm font-semibold text-gray-700 mb-2">Bahan Mentah
                                                    <span class="text-red-500">*</span></label>
                                                <select name="items[{{ $index }}][raw_material_id]"
                                                    class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 material-select"
                                                    required onchange="updateMaterialInfo({{ $index }})">
                                                    <option value="">Pilih Bahan Mentah</option>
                                                    @foreach ($rawMaterials as $material)
                                                        @php
                                                            $selected =
                                                                (string) $rawMaterialId === (string) $material->id
                                                                    ? 'selected'
                                                                    : '';
                                                        @endphp
                                                        <option value="{{ $material->id }}"
                                                            data-stock="{{ $material->current_stock }}"
                                                            data-unit="{{ $material->unit->name ?? '' }}"
                                                            data-cost="{{ $material->unit_price ?? 0 }}"
                                                            {{ $selected }}>
                                                            {{ $material->name }} (Stok:
                                                            {{ number_format($material->current_stock, 0, ',', '.') }}
                                                            {{ $material->unit->name ?? '' }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-span-6 lg:col-span-2">
                                                <label class="block text-sm font-semibold text-gray-700 mb-2">Jumlah <span
                                                        class="text-red-500">*</span></label>
                                                <input type="number" name="items[{{ $index }}][requested_quantity]"
                                                    value="{{ $requestedQty }}"
                                                    class="w-full px-4 py-3 border rounded-xl quantity-input" step="1"
                                                    min="1" required
                                                    onchange="calculateRowTotal({{ $index }})">
                                                <p class="mt-1 text-xs text-gray-600" id="unit-info-{{ $index }}">
                                                </p>
                                                <p class="mt-1 text-xs text-red-600 hidden"
                                                    id="stock-warning-{{ $index }}"></p>
                                            </div>

                                            <div class="col-span-6 lg:col-span-2">
                                                <label class="block text-sm font-semibold text-gray-700 mb-2">Harga/Unit
                                                    <span class="text-red-500">*</span></label>
                                                <input type="number" name="items[{{ $index }}][unit_cost]"
                                                    value="{{ $unitCost }}"
                                                    class="w-full px-4 py-3 border rounded-xl cost-input" step="1"
                                                    min="0" required
                                                    onchange="calculateRowTotal({{ $index }})">
                                            </div>

                                            <div class="col-span-6 lg:col-span-2">
                                                <label class="block text-sm font-semibold text-gray-700 mb-2">Total</label>
                                                <input type="text" readonly id="total-{{ $index }}"
                                                    value="Rp {{ number_format($rowTotal, 0, ',', '.') }}"
                                                    class="w-full px-4 py-3 border rounded-xl total-display bg-gray-50">
                                            </div>

                                            <div class="col-span-6 lg:col-span-1">
                                                <label
                                                    class="block text-sm font-semibold text-gray-700 mb-2">Catatan</label>
                                                <input type="text" name="items[{{ $index }}][notes]"
                                                    value="{{ $notes }}" class="w-full px-4 py-3 border rounded-xl"
                                                    placeholder="Opsional">
                                            </div>

                                            <div class="col-span-6 lg:col-span-1 flex justify-end">
                                                <button type="button"
                                                    class="inline-flex items-center px-3 py-2 rounded-lg border text-sm text-red-600 bg-white hover:bg-red-50 remove-material-btn"
                                                    onclick="removeMaterialRow({{ $index }})"
                                                    {{ count($items) <= 1 ? 'disabled' : '' }}>
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                @error('items')
                                    <p class="mt-3 text-sm text-red-600">{{ $message }}</p>
                                @enderror

                                {{-- Total Biaya --}}
                                <div class="mt-6">
                                    <div class="bg-gray-50 rounded-xl p-4 flex items-center justify-between">
                                        <span class="font-semibold text-gray-700">Total Biaya Bahan:</span>
                                        <span class="text-lg font-bold text-orange-600" id="grand-total">Rp
                                            {{ number_format($grandTotal, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Target Output Bahan Setengah Jadi --}}
                    <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                        <div class="flex items-center justify-between p-4 border-b">
                            <div class="flex items-center space-x-3">
                                <div
                                    class="w-9 h-9 bg-gradient-to-br from-green-500 to-green-600 rounded-lg flex items-center justify-center">
                                    <i class="bi bi-diagram-3 text-white"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-green-700">Target Output Bahan Setengah Jadi</h3>
                            </div>
                            <button type="button" onclick="addOutputRow()"
                                class="inline-flex items-center px-3 py-2 rounded-lg border text-sm font-medium text-green-600 bg-white hover:bg-green-50">
                                <i class="bi bi-plus me-2"></i> Tambah Target Output
                            </button>
                        </div>

                        <div class="p-6">
                            <div id="outputs-container" class="space-y-4">
                                @php
                                    $outputs = old('outputs', $productionRequest->outputs ?? []);
                                    if (empty($outputs)) {
                                        $outputs = [[]];
                                    }
                                @endphp

                                @foreach ($outputs as $oIndex => $output)
                                    @php
                                        $outputProductId = old(
                                            "outputs.$oIndex.semi_finished_product_id",
                                            is_object($output)
                                                ? $output->semi_finished_product_id ?? ''
                                                : $output['semi_finished_product_id'] ?? '',
                                        );
                                        $plannedQty = old(
                                            "outputs.$oIndex.planned_quantity",
                                            is_object($output)
                                                ? $output->planned_quantity ?? ''
                                                : $output['planned_quantity'] ?? '',
                                        );
                                        $outputNotes = old(
                                            "outputs.$oIndex.notes",
                                            is_object($output) ? $output->notes ?? '' : $output['notes'] ?? '',
                                        );
                                    @endphp

                                    <div class="grid grid-cols-12 gap-4 items-end output-row"
                                        data-index="{{ $oIndex }}">
                                        <div class="col-span-12 lg:col-span-6">
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">Produk Setengah
                                                Jadi</label>
                                            <select name="outputs[{{ $oIndex }}][semi_finished_product_id]"
                                                class="w-full px-4 py-3 border rounded-xl output-product-select"
                                                onchange="updateOutputUnit({{ $oIndex }})">
                                                <option value="">Pilih Produk</option>
                                                @foreach ($semiFinishedProducts as $product)
                                                    @php
                                                        $sel =
                                                            (string) $outputProductId === (string) $product->id
                                                                ? 'selected'
                                                                : '';
                                                    @endphp
                                                    <option value="{{ $product->id }}"
                                                        data-unit="{{ optional($product->getRelation('unit'))->name ?? '' }}"
                                                        data-unit-abbr="{{ optional($product->getRelation('unit'))->abbreviation ?? '' }}"
                                                        {{ $sel }}>
                                                        {{ $product->name }} (Min Stok:
                                                        {{ number_format($product->minimum_stock ?? 0, 0, ',', '.') }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-span-6 lg:col-span-3">
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">Jumlah
                                                Rencana</label>
                                            <input type="number" name="outputs[{{ $oIndex }}][planned_quantity]"
                                                value="{{ $plannedQty }}" class="w-full px-4 py-3 border rounded-xl"
                                                step="1" min="1" placeholder="0">
                                            <p class="mt-1 text-xs text-gray-600 output-unit-info"
                                                id="output-unit-{{ $oIndex }}"></p>
                                        </div>

                                        <div class="col-span-6 lg:col-span-2">
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">Catatan</label>
                                            <input type="text" name="outputs[{{ $oIndex }}][notes]"
                                                value="{{ $outputNotes }}" class="w-full px-4 py-3 border rounded-xl"
                                                placeholder="Opsional">
                                        </div>

                                        <div class="col-span-6 lg:col-span-1 flex justify-end">
                                            <button type="button"
                                                class="inline-flex items-center px-3 py-2 rounded-lg border text-sm text-red-600 bg-white hover:bg-red-50 remove-output-btn"
                                                onclick="removeOutputRow({{ $oIndex }})"
                                                {{ count($outputs) <= 1 ? 'disabled' : '' }}>
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            @error('outputs')
                                <p class="mt-3 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Right Sidebar --}}
                <div class="space-y-6">
                    {{-- Panduan Pengajuan --}}
                    <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                        <x-form.card-header title="Panduan Pengajuan" type="info" />
                        <div class="p-6">
                            <div class="text-sm">
                                <h6 class="text-primary-700 font-semibold mb-2">Langkah Pengajuan:</h6>
                                <ol class="list-decimal list-inside text-gray-700 mb-3">
                                    <li>Pilih bahan mentah yang dibutuhkan</li>
                                    <li>Masukkan jumlah dan harga per unit</li>
                                    <li>(Opsional) Tambahkan target output bahan setengah jadi</li>
                                    <li>Submit untuk persetujuan manajer</li>
                                </ol>

                                <h6 class="text-warning-600 font-semibold mb-2">Catatan Penting:</h6>
                                <ul class="list-disc list-inside text-gray-700">
                                    <li>Pastikan stok bahan mentah mencukupi</li>
                                    <li>Harga akan dikunci saat pengajuan</li>
                                    <li>Pengajuan hanya bisa diedit sebelum disetujui</li>
                                    <li>Bahan mentah akan dikurangi otomatis saat disetujui</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="bg-white rounded-2xl shadow-xl border border-gray-200 p-6">
                        <div class="flex flex-col gap-3">
                            <button type="submit" form="productionRequestForm"
                                class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-orange-600 to-red-600 border border-transparent rounded-xl text-sm font-medium text-white hover:from-orange-700 hover:to-red-700 shadow-lg">
                                <i class="bi bi-send me-2"></i> Simpan Perubahan
                            </button>
                            <a href="{{ route('production-requests.index') }}"
                                class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 rounded-xl text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                <i class="bi bi-x-circle me-2"></i> Batal
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

<script src="{{ asset('js/production-requests-form.js') }}"></script>
