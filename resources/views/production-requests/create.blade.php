@extends('layouts.app')
@php use Illuminate\Support\Facades\Storage; @endphp

@section('title', 'Buat Pengajuan Produksi')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-orange-50 via-white to-red-50 py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Header --}}
            <x-form.header title="Buat Pengajuan Produksi" backRoute="{{ route('production-requests.index') }}" />

            {{-- Main container --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
                {{-- Left / Main Form (span 2) --}}
                <div class="lg:col-span-2 space-y-6">
                    {{-- Informasi Pengajuan --}}
                    <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                        <x-form.card-header title="Informasi Pengajuan" type="add" />
                        <div class="p-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Peruntukan</label>
                            <textarea name="purpose" form="productionRequestForm"
                                class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 @error('purpose') border-red-300 ring-2 ring-red-200 @enderror"
                                rows="3" placeholder="Contoh: Produksi 200 ayam marinasi untuk cabang X">{{ old('purpose') }}</textarea>
                            @error('purpose')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Bahan Mentah yang Dibutuhkan --}}
                    <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                        <div
                            class="flex items-center justify-between p-4 border-b bg-gradient-to-r from-orange-600 via-orange-700 to-red-700 px-6 py-6">
                            <div class="flex items-center space-x-3">
                                <div
                                    class="w-9 h-9 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg flex items-center justify-center">
                                    <i class="bi bi-box-seam text-white"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-white">Bahan Mentah yang Dibutuhkan</h3>
                            </div>
                            <button type="button" onclick="addMaterialRow()"
                                class="inline-flex items-center px-3 py-2 rounded-lg border text-sm font-medium text-orange-600 bg-white hover:bg-orange-50">
                                <i class="bi bi-plus me-2"></i> Tambah Bahan
                            </button>
                        </div>

                        <div class="p-6">
                            <form id="productionRequestForm" action="{{ route('production-requests.store') }}"
                                method="POST">
                                @csrf
                                <div id="materials-container" class="space-y-4">
                                    {{-- Material Row Container --}}
                                    <div class="material-row group relative bg-gray-50/50 rounded-2xl p-4 sm:p-6 border border-gray-200 hover:border-blue-300 hover:bg-white transition-all duration-300 shadow-sm hover:shadow-md"
                                        data-index="0">

                                        {{-- Grid Atas: Input Utama --}}
                                        <div class="grid grid-cols-12 gap-4 items-start">
                                            {{-- Bahan Mentah --}}
                                            <div class="col-span-12 lg:col-span-4">
                                                <label
                                                    class="block text-xs font-bold uppercase text-gray-500 mb-2 ml-1">Bahan
                                                    Mentah <span class="text-red-500">*</span></label>
                                                <select name="items[0][raw_material_id]"
                                                    class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 material-select transition-all"
                                                    required onchange="updateMaterialInfo(0)">
                                                    <option value="">Pilih Bahan Mentah</option>
                                                    @foreach ($rawMaterials as $material)
                                                        <option value="{{ $material->id }}"
                                                            data-stock="{{ $material->current_stock }}"
                                                            data-unit="{{ $material->unit->name ?? '' }}"
                                                            data-cost="{{ $material->unit_price ?? 0 }}">
                                                            {{ $material->name }} (Stok:
                                                            {{ number_format($material->current_stock, 0, ',', '.') }}
                                                            {{ $material->unit->name ?? '' }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            {{-- Jumlah --}}
                                            <div class="col-span-6 lg:col-span-2">
                                                <label
                                                    class="block text-xs font-bold uppercase text-gray-500 mb-2 ml-1">Jumlah
                                                    <span class="text-red-500">*</span></label>
                                                <div class="relative">
                                                    <input type="number" name="items[0][requested_quantity]"
                                                        class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 quantity-input font-semibold"
                                                        step="1" min="1" required
                                                        onchange="calculateRowTotal(0)">
                                                </div>
                                            </div>

                                            {{-- Harga/Unit (Readonly) --}}
                                            <div class="col-span-6 lg:col-span-3">
                                                <label
                                                    class="block text-xs font-bold uppercase text-gray-500 mb-2 ml-1">Harga/Unit</label>
                                                <div class="relative">
                                                    <span class="absolute left-3 top-3.5 text-gray-400 text-xs">Rp</span>
                                                    <input type="number" name="items[0][unit_cost]"
                                                        class="w-full pl-9 pr-4 py-3 bg-gray-100 border-transparent rounded-xl text-gray-500 font-medium cursor-not-allowed cost-input"
                                                        readonly tabindex="-1">
                                                </div>
                                            </div>

                                            {{-- Subtotal --}}
                                            <div class="col-span-9 lg:col-span-3">
                                                <label
                                                    class="block text-xs font-bold uppercase text-gray-500 mb-2 ml-1">Subtotal</label>
                                                <input type="text" readonly id="total-0"
                                                    class="w-full px-4 py-3 bg-blue-50 border-transparent rounded-xl text-blue-700 font-bold text-right total-display">
                                            </div>
                                        </div>

                                        {{-- Grid Bawah: Catatan --}}
                                        <div class="mt-4 pt-4 border-t border-gray-100">
                                            <div class="flex items-start gap-3">
                                                <div class="flex-shrink-0 mt-3 hidden sm:block">
                                                    <i class="bi bi-chat-left-text text-gray-400"></i>
                                                </div>
                                                <div class="flex-grow">
                                                    <label
                                                        class="block text-[10px] font-bold uppercase text-gray-400 mb-1 ml-1 tracking-widest">Catatan
                                                        Tambahan (Opsional)</label>
                                                    <input type="text" name="items[0][notes]"
                                                        class="w-full px-4 py-2 bg-transparent border-b border-gray-200 focus:border-blue-500 focus:ring-0 text-sm placeholder-gray-400 transition-all"
                                                        placeholder="Contoh: Pilih ayam ukuran 1.2kg atau spesifikasi lainnya...">
                                                </div>
                                            </div>
                                            {{-- Action Button (Hapus) --}}
                                            <div class="col-span-3 lg:col-span-1 flex justify-end pt-7">
                                                <button type="button" onclick="removeMaterialRow(0)"
                                                    class="inline-flex items-center justify-center w-12 h-12 rounded-xl text-gray-400 hover:text-red-500 hover:bg-red-50 transition-all disabled:opacity-0"
                                                    disabled>
                                                    <i class="bi bi-trash3 text-xl"></i>
                                                </button>
                                            </div>
                                        </div>

                                        {{-- Stock Warning Alert --}}
                                        <div id="stock-warning-0"
                                            class="hidden mt-3 p-3 bg-red-50 border border-red-100 rounded-xl flex items-center text-red-600 text-[11px] font-bold">
                                            <i class="bi bi-exclamation-circle-fill me-2"></i> PERINGATAN: STOK TERSEDIA
                                            TIDAK MENCUKUPI
                                        </div>
                                    </div>
                                </div>

                                @error('items')
                                    <p class="mt-3 text-sm text-red-600">{{ $message }}</p>
                                @enderror

                                {{-- Total Biaya --}}
                                <div class="mt-6">
                                    <div class="bg-gray-50 rounded-xl p-4 flex items-center justify-between">
                                        <span class="font-semibold text-gray-700">Total Biaya Bahan:</span>
                                        <span class="text-lg font-bold text-orange-600" id="grand-total">Rp 0</span>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Target Output Bahan Setengah Jadi --}}
                    <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                        <div
                            class="flex items-center justify-between p-4 border-b bg-gradient-to-r from-orange-600 via-orange-700 to-red-700 px-6 py-6">
                            <div class="flex items-center space-x-3">
                                <div
                                    class="w-9 h-9 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg flex items-center justify-center">
                                    <i class="bi bi-diagram-3 text-white"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-white">Target Output Bahan Setengah Jadi</h3>
                            </div>
                            <button type="button" onclick="addOutputRow()"
                                class="inline-flex items-center px-3 py-2 rounded-lg border text-sm font-medium text-green-600 bg-white hover:bg-green-50">
                                <i class="bi bi-plus me-2"></i> Tambah Target Output
                            </button>
                        </div>

                        <div class="p-6">
                            <div id="outputs-container" class="space-y-4">
                                {{-- Output Row Card --}}
                                <div class="output-row group relative bg-white rounded-2xl p-5 sm:p-6 border border-gray-200 hover:border-emerald-300 hover:shadow-lg transition-all duration-300 mb-4"
                                    data-index="0">

                                    {{-- Grid Atas: Produk & Jumlah --}}
                                    <div class="grid grid-cols-12 gap-5 items-start">

                                        {{-- Pilih Produk Setengah Jadi --}}
                                        <div class="col-span-12 lg:col-span-7">
                                            <label
                                                class="flex items-center text-xs font-bold uppercase text-gray-500 mb-2 ml-1">
                                                <i class="bi bi-box-seam me-2 text-emerald-500"></i> Produk Setengah Jadi
                                            </label>
                                            <select name="outputs[0][semi_finished_product_id]"
                                                class="w-full px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 focus:bg-white transition-all appearance-none output-product-select"
                                                required onchange="updateOutputUnit(0)">
                                                <option value="">Cari Produk...</option>
                                                @foreach ($semiFinishedProducts as $product)
                                                    <option value="{{ $product->id }}"
                                                        data-unit="{{ optional($product->getRelation('unit'))->name ?? '' }}"
                                                        data-unit-abbr="{{ optional($product->getRelation('unit'))->abbreviation ?? '' }}">
                                                        {{ $product->name }} (Min Stok:
                                                        {{ number_format($product->minimum_stock ?? 0, 0, ',', '.') }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        {{-- Jumlah Rencana --}}
                                        <div class="col-span-8 lg:col-span-4">
                                            <label
                                                class="flex items-center text-xs font-bold uppercase text-gray-500 mb-2 ml-1">
                                                <i class="bi bi-flag me-2 text-emerald-500"></i> Jumlah Rencana
                                            </label>
                                            <div class="relative">
                                                <input type="number" name="outputs[0][planned_quantity]"
                                                    class="w-full px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 focus:bg-white font-bold text-gray-700 transition-all"
                                                    step="1" min="1" placeholder="0">
                                                <span
                                                    class="absolute right-4 top-1/2 -translate-y-1/2 text-[10px] font-black text-emerald-600 uppercase tracking-widest output-unit-info"
                                                    id="output-unit-0">
                                                    {{-- Satuan akan muncul di sini --}}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Grid Bawah: Catatan --}}
                                    <div class="mt-5 pt-4 border-t border-dashed border-gray-100">
                                        <div class="flex items-center gap-3">
                                            <i class="bi bi-pencil-square text-gray-400"></i>
                                            <div class="flex-grow">
                                                <input type="text" name="outputs[0][notes]"
                                                    class="w-full px-0 py-1 bg-transparent border-0 border-b border-transparent focus:border-emerald-500 focus:ring-0 text-sm text-gray-600 placeholder-gray-400 transition-all"
                                                    placeholder="Tambahkan catatan hasil produksi di sini (Opsional)...">
                                            </div>
                                        </div>
                                        {{-- Tombol Hapus --}}
                                        <div class="col-span-4 lg:col-span-1 flex justify-end pt-7">
                                            <button type="button" onclick="removeOutputRow(0)"
                                                class="inline-flex items-center justify-center w-12 h-12 rounded-xl text-gray-400 hover:text-red-500 hover:bg-red-50 transition-all disabled:opacity-0"
                                                disabled>
                                                <i class="bi bi-trash3 text-xl"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
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
                                <i class="bi bi-send me-2"></i> Kirim Pengajuan
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
