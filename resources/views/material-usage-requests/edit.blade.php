@extends('layouts.app')

@section('title', 'Edit Permintaan Penggunaan Bahan')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-orange-50 via-white to-red-50 py-6">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Header Section --}}
            <div class="mb-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <div
                                class="w-10 h-10 bg-gradient-to-br from-orange-500 to-red-600 rounded-xl flex items-center justify-center shadow-lg">
                                <i class="bi bi-pencil-square text-white text-lg"></i>
                            </div>
                            <div>
                                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">
                                    Edit Pengajuan Penggunaan Bahan Setengah Jadi
                                </h1>
                                <p class="text-sm text-gray-600 mt-1">
                                    Perbarui data pengajuan ini
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="flex-shrink-0">
                        <a href="{{ route('semi-finished-usage-requests.index') }}"
                            class="inline-flex items-center px-4 py-2.5 bg-white border border-gray-300 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 shadow-sm hover:shadow-md">
                            <i class="bi bi-arrow-left mr-2"></i>
                            Kembali
                        </a>
                    </div>
                </div>
            </div>

            @if (session('error'))
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-800">
                    <div class="flex items-start">
                        <i class="bi bi-exclamation-circle mr-3 mt-0.5 text-red-600"></i>
                        <p>{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            <form action="{{ route('semi-finished-usage-requests.update', $materialUsageRequest) }}" method="POST"
                x-data="usageRequestForm({
                    items: @js(
    old(
        'items',
        $materialUsageRequest->items
            ->map(
                fn($item) => [
                    'id' => $item->id,
                    'raw_material_id' => $item->raw_material_id,
                    'quantity' => $item->quantity,
                    'unit_id' => $item->unit_id,
                    'notes' => $item->notes,
                    'key' => $item->id,
                ],
            )
            ->toArray(),
    ),
),
                    outputs: @js(
    old(
        'outputs',
        ($materialUsageRequest->outputs->count() ? $materialUsageRequest->outputs : $materialUsageRequest->targets)
            ->map(
                fn($output) => [
                    'id' => $output->id,
                    'product_id' => $output->product_id ?? $output->finished_product_id,
                    'planned_quantity' => $output->planned_quantity,
                    'notes' => $output->notes,
                ],
            )
            ->toArray(),
    ),
)
                })">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    {{-- Main Content --}}
                    <div class="lg:col-span-2 space-y-6">
                        {{-- Informasi Pengajuan --}}
                        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                            <div class="bg-gradient-to-r from-blue-600 via-blue-700 to-blue-800 px-6 py-6">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center mr-3">
                                        <i class="bi bi-clipboard-check text-white"></i>
                                    </div>
                                    <h2 class="text-xl font-bold text-white">Informasi Pengajuan</h2>
                                </div>
                            </div>

                            <div class="p-6 sm:p-8">
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                                            Nomor Permintaan
                                        </label>
                                        <input type="text"
                                            class="w-full px-4 py-3 border rounded-xl bg-gray-50 cursor-not-allowed"
                                            value="{{ $materialUsageRequest->request_number }}" readonly>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                                            Cabang Peminta
                                        </label>
                                        <input type="text"
                                            class="w-full px-4 py-3 border rounded-xl bg-gray-50 cursor-not-allowed"
                                            value="{{ $materialUsageRequest->requestingBranch->name }}" readonly>
                                        <input type="hidden" name="requesting_branch_id"
                                            value="{{ $materialUsageRequest->requesting_branch_id }}">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                                            Tanggal Permintaan
                                        </label>
                                        <input type="text"
                                            class="w-full px-4 py-3 border rounded-xl bg-gray-50 cursor-not-allowed"
                                            value="{{ $materialUsageRequest->requested_date->format('d/m/Y') }}" readonly>
                                    </div>

                                    <div>
                                        <label for="required_date" class="block text-sm font-semibold text-gray-700 mb-2">
                                            Tanggal Dibutuhkan <span class="text-red-500">*</span>
                                        </label>
                                        <input type="date"
                                            class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 @error('required_date') border-red-300 ring-2 ring-red-200 @enderror"
                                            id="required_date" name="required_date"
                                            value="{{ old('required_date', $materialUsageRequest->required_date?->format('Y-m-d') ?? date('Y-m-d')) }}"
                                            min="{{ date('Y-m-d') }}" required>
                                        <p class="mt-2 text-sm text-gray-600">
                                            <i class="bi bi-info-circle mr-1"></i>Kapan bahan dibutuhkan
                                        </p>
                                        @error('required_date')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="purpose" class="block text-sm font-semibold text-gray-700 mb-2">
                                            Tujuan Penggunaan <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text"
                                            class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 @error('purpose') border-red-300 ring-2 ring-red-200 @enderror"
                                            id="purpose" name="purpose"
                                            value="{{ old('purpose', $materialUsageRequest->purpose) }}"
                                            placeholder="Contoh: Untuk produksi 200 porsi Ayam Crispy" required>
                                        <p class="mt-2 text-sm text-gray-600">
                                            <i class="bi bi-info-circle mr-1"></i>Tujuan penggunaan bahan
                                        </p>
                                        @error('purpose')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="lg:col-span-2">
                                        <label for="notes" class="block text-sm font-semibold text-gray-700 mb-2">
                                            Catatan
                                        </label>
                                        <textarea
                                            class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 @error('notes') border-red-300 ring-2 ring-red-200 @enderror"
                                            id="notes" name="notes" rows="3" placeholder="Opsional: tambahkan catatan tambahan jika diperlukan">{{ old('notes', $materialUsageRequest->notes) }}</textarea>
                                        <p class="mt-2 text-sm text-gray-600">
                                            <i class="bi bi-info-circle mr-1"></i>Catatan opsional untuk pengajuan
                                        </p>
                                        @error('notes')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Bahan Setengah Jadi yang Digunakan --}}
                        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                            <div class="bg-gradient-to-r from-green-600 via-green-700 to-emerald-700 px-6 py-6">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center mr-3">
                                            <i class="bi bi-box-seam text-white"></i>
                                        </div>
                                        <h2 class="text-xl font-bold text-white">Bahan Setengah Jadi yang Digunakan</h2>
                                    </div>
                                    <button type="button"
                                        class="inline-flex items-center px-3 py-2 bg-white/20 hover:bg-white/30 text-white rounded-lg text-sm font-medium transition-all duration-200"
                                        @click="addItem">
                                        <i class="bi bi-plus mr-1"></i>
                                        Tambah
                                    </button>
                                </div>
                            </div>

                            <div class="p-6 sm:p-8">
                                <p class="text-blue-700 bg-blue-50 border border-blue-200 rounded-lg p-3 mb-6 text-sm">
                                    <i class="bi bi-info-circle mr-2"></i>
                                    Pengajuan ini menggunakan bahan setengah jadi untuk menghasilkan stok finished product.
                                </p>

                                <div class="overflow-x-auto">
                                    <table class="w-full text-sm" id="itemsTable">
                                        <thead>
                                            <tr class="border-b-2 border-gray-200">
                                                <th class="text-left py-3 px-4 font-semibold text-gray-700">Bahan</th>
                                                <th class="text-left py-3 px-4 font-semibold text-gray-700">Jumlah</th>
                                                <th class="text-left py-3 px-4 font-semibold text-gray-700">Satuan</th>
                                                <th class="text-left py-3 px-4 font-semibold text-gray-700">Catatan Item
                                                </th>
                                                <th class="text-center py-3 px-4 font-semibold text-gray-700">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200">
                                            <template x-for="(item, index) in items" :key="item.key">
                                                <tr class="hover:bg-gray-50">

                                                    {{-- Bahan --}}
                                                    <td class="py-3 px-4">
                                                        <select class="w-full px-3 py-2 border rounded-lg text-sm"
                                                            x-model="item.raw_material_id"
                                                            :name="`items[${index}][raw_material_id]`" required>
                                                            <option value="">-- Pilih Bahan --</option>
                                                            @foreach ($semiFinishedProducts as $material)
                                                                <option value="{{ $material->id }}">
                                                                    {{ $material->name }}
                                                                    (Stok:
                                                                    {{ number_format($material->current_stock, 0, ',', '.') }})
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <input type="hidden" :name="`items[${index}][id]`"
                                                            :value="item.id">
                                                    </td>

                                                    {{-- Quantity --}}
                                                    <td class="py-3 px-4">
                                                        <input type="number" min="1"
                                                            class="w-full px-3 py-2 border rounded-lg text-sm"
                                                            x-model="item.quantity" :name="`items[${index}][quantity]`"
                                                            required>
                                                    </td>

                                                    {{-- Unit --}}
                                                    <td class="py-3 px-4">
                                                        <select class="w-full px-3 py-2 border rounded-lg text-sm"
                                                            x-model="item.unit_id" :name="`items[${index}][unit_id]`"
                                                            required>
                                                            <option value="">-- Pilih Satuan --</option>
                                                            @foreach ($units as $unit)
                                                                <option value="{{ $unit->id }}">
                                                                    {{ $unit->name }} ({{ $unit->abbreviation }})
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </td>

                                                    {{-- Notes --}}
                                                    <td class="py-3 px-4">
                                                        <input type="text"
                                                            class="w-full px-3 py-2 border rounded-lg text-sm"
                                                            x-model="item.notes" :name="`items[${index}][notes]`">
                                                    </td>

                                                    {{-- Aksi --}}
                                                    <td class="py-3 px-4 text-center">
                                                        <button type="button" @click="removeItem(index)"
                                                            class="p-2 text-red-600 hover:bg-red-50 rounded-lg">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </td>

                                                </tr>
                                            </template>
                                        </tbody>

                                    </table>
                                </div>

                                @error('items')
                                    <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-xl text-red-800 text-sm">
                                        <i class="bi bi-exclamation-circle mr-2"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        {{-- Rencana Output (Produk Jadi) --}}
                        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                            <div class="bg-gradient-to-r from-purple-600 via-purple-700 to-indigo-700 px-6 py-6">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center mr-3">
                                            <i class="bi bi-bullseye text-white"></i>
                                        </div>
                                        <h2 class="text-xl font-bold text-white">Rencana Output (Produk Jadi)</h2>
                                    </div>
                                    <button type="button"
                                        class="inline-flex items-center px-3 py-2 bg-white/20 hover:bg-white/30 text-white rounded-lg text-sm font-medium transition-all duration-200"
                                        @click="addOutput">
                                        <i class="bi bi-plus mr-1"></i>
                                        Tambah
                                    </button>
                                </div>
                            </div>

                            <div class="p-6 sm:p-8">
                                <p class="text-amber-700 bg-amber-50 border border-amber-200 rounded-lg p-3 mb-6 text-sm">
                                    <i class="bi bi-info-circle mr-2"></i>
                                    Opsional: tentukan rencana output produk jadi yang ingin dicapai dari penggunaan bahan
                                    ini.
                                </p>

                                <div class="overflow-x-auto">
                                    <table class="w-full text-sm" id="outputsTable">
                                        <thead>
                                            <tr class="border-b-2 border-gray-200">
                                                <th class="text-left py-3 px-4 font-semibold text-gray-700">Produk Jadi
                                                </th>
                                                <th class="text-left py-3 px-4 font-semibold text-gray-700">Jumlah Rencana
                                                </th>
                                                <th class="text-left py-3 px-4 font-semibold text-gray-700">Catatan</th>
                                                <th class="text-center py-3 px-4 font-semibold text-gray-700">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200">
                                            <template x-for="(output, index) in outputs" :key="index">
                                                <tr class="hover:bg-gray-50">
                                                    <td class="py-3 px-4"> <select x-model="output.product_id"
                                                            :name="`outputs[${index}][product_id]`"
                                                            class="w-full px-3 py-2 border rounded-lg text-sm">
                                                            <option value="">-- Pilih Produk --</option>
                                                            @foreach ($finishedProducts as $product)
                                                                <option value="{{ $product->id }}">{{ $product->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <input type="hidden" :name="`outputs[${index}][id]`"
                                                            :value="output.id">
                                                    </td>
                                                    <td class="py-3 px-4">
                                                        <input type="number" min="1"
                                                            x-model="output.planned_quantity"
                                                            :name="`outputs[${index}][planned_quantity]`"
                                                            class="w-full px-3 py-2 border rounded-lg text-sm">
                                                    </td>

                                                    <td class="py-3 px-4">
                                                        <input type="text" x-model="output.notes"
                                                            :name="`outputs[${index}][notes]`"
                                                            class="w-full px-3 py-2 border rounded-lg text-sm">
                                                    </td>

                                                    <td class="py-3 px-4 text-center">
                                                        <button type="button" @click="removeOutput(index)"
                                                            class="p-2 text-red-600 hover:bg-red-50 rounded-lg">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>

                                @error('outputs')
                                    <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-xl text-red-800 text-sm">
                                        <i class="bi bi-exclamation-circle mr-2"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Sidebar --}}
                    <div class="lg:col-span-1 space-y-6">
                        {{-- Panduan Pengajuan --}}
                        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                            <div class="bg-gradient-to-r from-cyan-600 via-cyan-700 to-blue-700 px-6 py-6">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center mr-3">
                                        <i class="bi bi-info-circle text-white"></i>
                                    </div>
                                    <h2 class="text-xl font-bold text-white">Panduan Pengajuan</h2>
                                </div>
                            </div>

                            <div class="p-6 sm:p-8">
                                <div class="space-y-6">
                                    <div>
                                        <h6 class="text-sm font-semibold text-orange-700 mb-3 flex items-center">
                                            <i class="bi bi-list-ol mr-2"></i>
                                            Langkah-Langkah:
                                        </h6>
                                        <ol class="list-decimal list-inside space-y-2 text-sm text-gray-700">
                                            <li>Review dan ubah informasi pengajuan</li>
                                            <li>Perbarui bahan setengah jadi yang digunakan</li>
                                            <li>Periksa stok dan satuan</li>
                                            <li>Simpan perubahan</li>
                                        </ol>
                                    </div>

                                    <div class="border-t border-gray-200 pt-4">
                                        <h6 class="text-sm font-semibold text-amber-700 mb-3 flex items-center">
                                            <i class="bi bi-exclamation-triangle mr-2"></i>
                                            Catatan Penting:
                                        </h6>
                                        <ul class="list-disc list-inside space-y-2 text-sm text-gray-700">
                                            <li>Pastikan stok bahan setengah jadi mencukupi</li>
                                            <li>Penggunaan akan tercatat untuk finished product</li>
                                            <li>Periksa kembali sebelum menyimpan</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Form Actions --}}
                        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                            <div class="p-6 sm:p-8">
                                <div class="flex flex-col gap-3">
                                    <button type="submit"
                                        class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-orange-600 to-red-600 border border-transparent rounded-xl text-sm font-medium text-white hover:from-orange-700 hover:to-red-700 focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                        <i class="bi bi-save mr-2"></i>
                                        Simpan Perubahan
                                    </button>
                                    <a href="{{ route('semi-finished-usage-requests.show', $materialUsageRequest) }}"
                                        class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 rounded-xl text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 shadow-sm hover:shadow-md">
                                        <i class="bi bi-x-circle mr-2"></i>
                                        Batal
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('usageRequestForm', (data) => ({
            items: data.items,
            outputs: data.outputs,

            nextKey: Math.max(...data.items.map(i => i.key), 0) + 1,

            addItem() {
                this.items.push({
                    raw_material_id: null,
                    quantity: null,
                    unit_id: null,
                    notes: null,
                    key: this.nextKey++
                })
            },

            removeItem(index) {
                this.items.splice(index, 1)
            },

            addOutput() {
                this.outputs.push({
                    product_id: null,
                    planned_quantity: null,
                    notes: null
                })
            },

            removeOutput(index) {
                this.outputs.splice(index, 1)
            }
        }))
    })
</script>
