@extends('layouts.app')

@section('title', 'Buat Permintaan Penggunaan Bahan')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-orange-50 via-white to-red-50 py-4 sm:py-6">
        <div class="max-w-6xl mx-auto px-3 sm:px-4 lg:px-8">
            {{-- Header Section --}}
            <div class="mb-6 sm:mb-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4">
                    <div class="min-w-0 flex-1">
                        <div class="flex items-start sm:items-center gap-2 sm:gap-3 mb-2">
                            <div
                                class="w-9 sm:w-10 h-9 sm:h-10 bg-gradient-to-br from-orange-500 to-red-600 rounded-lg sm:rounded-xl flex-shrink-0 flex items-center justify-center shadow-lg">
                                <i class="bi bi-plus-circle text-white text-base sm:text-lg"></i>
                            </div>
                            <div class="min-w-0">
                                <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 break-words">
                                    Buat Pengajuan Penggunaan Bahan Setengah Jadi
                                </h1>
                                <p class="text-xs sm:text-sm text-gray-600 mt-1 line-clamp-2">
                                    Pengajuan penggunaan semi-finished untuk menghasilkan stok finished product
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="flex-shrink-0 w-full sm:w-auto">
                        <a href="{{ route('semi-finished-usage-requests.index') }}"
                            class="inline-flex w-full sm:w-auto items-center justify-center px-3 sm:px-4 py-2 sm:py-2.5 bg-white border border-gray-300 rounded-lg sm:rounded-xl text-xs sm:text-sm font-medium text-gray-700 hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 shadow-sm hover:shadow-md">
                            <i class="bi bi-arrow-left mr-2"></i>
                            <span>Kembali</span>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Error Alert --}}
            @if (session('error'))
                <div
                    class="mb-4 sm:mb-6 p-3 sm:p-4 bg-red-50 border border-red-200 rounded-lg sm:rounded-xl text-red-800 text-sm">
                    <div class="flex items-start gap-2 sm:gap-3">
                        <i class="bi bi-exclamation-circle mt-0.5 text-red-600 flex-shrink-0"></i>
                        <p class="break-words">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            <form action="{{ route('semi-finished-usage-requests.store') }}" method="POST" x-data="usageRequestForm({
                items: @js(old('items', [['raw_material_id' => null, 'quantity' => null, 'unit_id' => null, 'notes' => null, 'key' => 1]])),
                outputs: @js(old('outputs', []))
            })">
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
                    {{-- Main Content --}}
                    <div class="lg:col-span-2 space-y-4 sm:space-y-6">
                        {{-- Informasi Pengajuan --}}
                        <div class="bg-white rounded-lg sm:rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                            <div class="bg-gradient-to-r from-blue-600 via-blue-700 to-blue-800 px-4 sm:px-6 py-4 sm:py-6">
                                <div class="flex items-center gap-2 sm:gap-3">
                                    <div
                                        class="w-7 sm:w-8 h-7 sm:h-8 bg-white/20 rounded flex-shrink-0 items-center justify-center hidden sm:flex">
                                        <i class="bi bi-clipboard-check text-white text-sm"></i>
                                    </div>
                                    <h2 class="text-lg sm:text-xl font-bold text-white">Informasi Pengajuan</h2>
                                </div>
                            </div>

                            <div class="p-4 sm:p-6 lg:p-8">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                                    {{-- Cabang Peminta --}}
                                    <div>
                                        <label class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1 sm:mb-2">
                                            Cabang Peminta
                                        </label>
                                        <input type="text"
                                            class="w-full px-3 sm:px-4 py-2 sm:py-3 border rounded-lg sm:rounded-xl bg-gray-50 cursor-not-allowed text-sm"
                                            value="{{ $requestingBranch->name }}" readonly>
                                        <input type="hidden" name="requesting_branch_id"
                                            value="{{ $requestingBranch->id }}">
                                        <p class="mt-1 sm:mt-2 text-xs sm:text-sm text-gray-600">
                                            <i class="bi bi-info-circle mr-1"></i><span class="hidden sm:inline">Cabang yang
                                                melakukan permintaan</span>
                                        </p>
                                    </div>

                                    {{-- Tanggal Permintaan --}}
                                    <div>
                                        <label class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1 sm:mb-2">
                                            Tanggal Permintaan
                                        </label>
                                        <input type="text"
                                            class="w-full px-3 sm:px-4 py-2 sm:py-3 border rounded-lg sm:rounded-xl bg-gray-50 cursor-not-allowed text-sm"
                                            value="{{ date('d/m/Y') }}" readonly>
                                        <p class="mt-1 sm:mt-2 text-xs sm:text-sm text-gray-600">
                                            <i class="bi bi-info-circle mr-1"></i><span class="hidden sm:inline">Tanggal
                                                pengajuan saat ini</span>
                                        </p>
                                    </div>

                                    {{-- Tanggal Dibutuhkan --}}
                                    <div>
                                        <label for="required_date"
                                            class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1 sm:mb-2">
                                            Tanggal Dibutuhkan <span class="text-red-500">*</span>
                                        </label>
                                        <input type="date"
                                            class="w-full px-3 sm:px-4 py-2 sm:py-3 border rounded-lg sm:rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 text-sm @error('required_date') border-red-300 ring-2 ring-red-200 @enderror"
                                            id="required_date" name="required_date"
                                            value="{{ old('required_date', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}"
                                            required>
                                        <p class="mt-1 sm:mt-2 text-xs sm:text-sm text-gray-600">
                                            <i class="bi bi-info-circle mr-1"></i><span class="hidden sm:inline">Kapan bahan
                                                dibutuhkan</span>
                                        </p>
                                        @error('required_date')
                                            <p class="mt-1 sm:mt-2 text-xs sm:text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    {{-- Tujuan Penggunaan --}}
                                    <div>
                                        <label for="purpose"
                                            class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1 sm:mb-2">
                                            Tujuan Penggunaan <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text"
                                            class="w-full px-3 sm:px-4 py-2 sm:py-3 border rounded-lg sm:rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 text-sm @error('purpose') border-red-300 ring-2 ring-red-200 @enderror"
                                            id="purpose" name="purpose" value="{{ old('purpose') }}"
                                            placeholder="Contoh: Untuk produksi 200 porsi Ayam Crispy" required>
                                        <p class="mt-1 sm:mt-2 text-xs sm:text-sm text-gray-600">
                                            <i class="bi bi-info-circle mr-1"></i><span class="hidden sm:inline">Tujuan
                                                penggunaan bahan</span>
                                        </p>
                                        @error('purpose')
                                            <p class="mt-1 sm:mt-2 text-xs sm:text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    {{-- Catatan --}}
                                    <div class="sm:col-span-2">
                                        <label for="notes"
                                            class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1 sm:mb-2">
                                            Catatan
                                        </label>
                                        <textarea
                                            class="w-full px-3 sm:px-4 py-2 sm:py-3 border rounded-lg sm:rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 text-sm @error('notes') border-red-300 ring-2 ring-red-200 @enderror"
                                            id="notes" name="notes" rows="2" sm:rows="3"
                                            placeholder="Opsional: tambahkan catatan tambahan jika diperlukan">{{ old('notes') }}</textarea>
                                        <p class="mt-1 sm:mt-2 text-xs sm:text-sm text-gray-600">
                                            <i class="bi bi-info-circle mr-1"></i><span class="hidden sm:inline">Catatan
                                                opsional untuk pengajuan</span>
                                        </p>
                                        @error('notes')
                                            <p class="mt-1 sm:mt-2 text-xs sm:text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Bahan Setengah Jadi yang Digunakan --}}
                        <div class="bg-white rounded-lg sm:rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                            <div
                                class="bg-gradient-to-r from-green-600 via-green-700 to-emerald-700 px-4 sm:px-6 py-4 sm:py-6">
                                <div class="flex items-center justify-between gap-2">
                                    <div class="flex items-center gap-2 sm:gap-3 min-w-0">
                                        <div
                                            class="w-7 sm:w-8 h-7 sm:h-8 bg-white/20 rounded flex-shrink-0 items-center justify-center hidden sm:flex">
                                            <i class="bi bi-box-seam text-white text-sm"></i>
                                        </div>
                                        <h2 class="text-lg sm:text-xl font-bold text-white truncate">Bahan Setengah Jadi
                                        </h2>
                                    </div>
                                    <button type="button"
                                        class="inline-flex flex-shrink-0 items-center px-2 sm:px-3 py-1.5 sm:py-2 bg-white/20 hover:bg-white/30 text-white rounded text-xs sm:text-sm font-medium transition-all duration-200"
                                        @click="addItem">
                                        <i class="bi bi-plus mr-0.5 sm:mr-1"></i>
                                        <span class="hidden sm:inline">Tambah</span>
                                    </button>
                                </div>
                            </div>

                            <div class="p-4 sm:p-6 lg:p-8">
                                <p
                                    class="text-blue-700 bg-blue-50 border border-blue-200 rounded text-xs sm:text-sm p-2 sm:p-3 mb-4 sm:mb-6">
                                    <i class="bi bi-info-circle mr-1 sm:mr-2 flex-shrink-0"></i>
                                    <span>Pengajuan ini menggunakan bahan setengah jadi untuk menghasilkan stok finished
                                        product.</span>
                                </p>

                                <div class="overflow-x-auto -mx-4 sm:mx-0">
                                    <table class="w-full text-xs sm:text-sm min-w-max sm:min-w-0" id="itemsTable">
                                        <thead>
                                            <tr class="border-b-2 border-gray-200 bg-gray-50">
                                                <th
                                                    class="text-left py-2 sm:py-3 px-2 sm:px-4 font-semibold text-gray-700">
                                                    Bahan</th>
                                                <th
                                                    class="text-left py-2 sm:py-3 px-2 sm:px-4 font-semibold text-gray-700">
                                                    Jumlah</th>
                                                <th
                                                    class="text-left py-2 sm:py-3 px-2 sm:px-4 font-semibold text-gray-700">
                                                    Satuan</th>
                                                <th
                                                    class="text-left py-2 sm:py-3 px-2 sm:px-4 font-semibold text-gray-700 hidden md:table-cell">
                                                    Catatan</th>
                                                <th
                                                    class="text-center py-2 sm:py-3 px-2 sm:px-4 font-semibold text-gray-700">
                                                    Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200">
                                            <template x-for="(item, index) in items" :key="item.key">
                                                <tr class="hover:bg-gray-50">
                                                    {{-- Bahan --}}
                                                    <td class="py-2 sm:py-3 px-2 sm:px-4">
                                                        <select
                                                            class="w-full px-2 sm:px-3 py-1.5 sm:py-2 border rounded text-xs sm:text-sm"
                                                            x-model="item.raw_material_id"
                                                            :name="`items[${index}][raw_material_id]`"
                                                            @change="onMaterialChange($event, index)" required>
                                                            <option value="">-- Pilih --</option>
                                                            @foreach ($semiFinishedProducts as $material)
                                                                <option value="{{ $material->id }}"
                                                                    data-unit-id="{{ $material->unit_id }}">
                                                                    {{ $material->name }}
                                                                    ({{ number_format($material->current_stock, 0) }})
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </td>

                                                    {{-- Quantity --}}
                                                    <td class="py-2 sm:py-3 px-2 sm:px-4">
                                                        <input type="number" min="1"
                                                            class="w-full px-2 sm:px-3 py-1.5 sm:py-2 border rounded text-xs sm:text-sm"
                                                            x-model="item.quantity" :name="`items[${index}][quantity]`"
                                                            required>
                                                    </td>

                                                    {{-- Unit --}}
                                                    <td class="py-2 sm:py-3 px-2 sm:px-4">
                                                        <select
                                                            class="w-full px-2 sm:px-3 py-1.5 sm:py-2 border rounded text-xs sm:text-sm bg-gray-100 cursor-not-allowed appearance-none"
                                                            x-model="item.raw_material_id" disabled>
                                                            <option value=""></option>
                                                            @foreach ($semiFinishedProducts as $material)
                                                                <option value="{{ $material->id }}"
                                                                    data-unit-id="{{ $material->unit_id }}">
                                                                    {{ $material->unit }}
                                                                </option>
                                                            @endforeach
                                                        </select>

                                                        <input type="hidden" :name="`items[${index}][unit_id]`"
                                                            :value="item.unit_id">
                                                    </td>


                                                    {{-- Notes --}}
                                                    <td class="py-2 sm:py-3 px-2 sm:px-4 hidden md:table-cell">
                                                        <input type="text"
                                                            class="w-full px-2 sm:px-3 py-1.5 sm:py-2 border rounded text-xs sm:text-sm"
                                                            x-model="item.notes" :name="`items[${index}][notes]`">
                                                    </td>

                                                    {{-- Aksi --}}
                                                    <td class="py-2 sm:py-3 px-2 sm:px-4 text-center">
                                                        <button type="button" @click="removeItem(index)"
                                                            class="p-1 sm:p-2 text-red-600 hover:bg-red-50 rounded transition-colors">
                                                            <i class="bi bi-trash text-sm sm:text-base"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>

                                @error('items')
                                    <div
                                        class="mt-3 sm:mt-4 p-3 sm:p-4 bg-red-50 border border-red-200 rounded text-red-800 text-xs sm:text-sm">
                                        <i class="bi bi-exclamation-circle mr-2"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        {{-- Rencana Output (Produk Jadi) --}}
                        <div class="bg-white rounded-lg sm:rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                            <div
                                class="bg-gradient-to-r from-purple-600 via-purple-700 to-indigo-700 px-4 sm:px-6 py-4 sm:py-6">
                                <div class="flex items-center justify-between gap-2">
                                    <div class="flex items-center gap-2 sm:gap-3 min-w-0">
                                        <div
                                            class="w-7 sm:w-8 h-7 sm:h-8 bg-white/20 rounded flex-shrink-0 items-center justify-center hidden sm:flex">
                                            <i class="bi bi-bullseye text-white text-sm"></i>
                                        </div>
                                        <h2 class="text-lg sm:text-xl font-bold text-white truncate">Rencana Output</h2>
                                    </div>
                                    <button type="button"
                                        class="inline-flex flex-shrink-0 items-center px-2 sm:px-3 py-1.5 sm:py-2 bg-white/20 hover:bg-white/30 text-white rounded text-xs sm:text-sm font-medium transition-all duration-200"
                                        @click="addOutput">
                                        <i class="bi bi-plus mr-0.5 sm:mr-1"></i>
                                        <span class="hidden sm:inline">Tambah</span>
                                    </button>
                                </div>
                            </div>

                            <div class="p-4 sm:p-6 lg:p-8">
                                <p
                                    class="text-amber-700 bg-amber-50 border border-amber-200 rounded p-2 sm:p-3 mb-4 sm:mb-6 text-xs sm:text-sm">
                                    <i class="bi bi-info-circle mr-1 sm:mr-2 flex-shrink-0"></i>
                                    <span>Opsional: tentukan rencana output produk jadi.</span>
                                </p>

                                <div class="overflow-x-auto -mx-4 sm:mx-0">
                                    <table class="w-full text-xs sm:text-sm min-w-max sm:min-w-0" id="outputsTable">
                                        <thead>
                                            <tr class="border-b-2 border-gray-200 bg-gray-50">
                                                <th
                                                    class="text-left py-2 sm:py-3 px-2 sm:px-4 font-semibold text-gray-700">
                                                    Produk</th>
                                                <th
                                                    class="text-left py-2 sm:py-3 px-2 sm:px-4 font-semibold text-gray-700">
                                                    Jumlah</th>
                                                <th
                                                    class="text-left py-2 sm:py-3 px-2 sm:px-4 font-semibold text-gray-700 hidden md:table-cell">
                                                    Catatan</th>
                                                <th
                                                    class="text-center py-2 sm:py-3 px-2 sm:px-4 font-semibold text-gray-700">
                                                    Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200">
                                            <template x-for="(output, index) in outputs" :key="index">
                                                <tr class="hover:bg-gray-50">
                                                    <td class="py-2 sm:py-3 px-2 sm:px-4">
                                                        <select x-model="output.product_id"
                                                            :name="`outputs[${index}][product_id]`"
                                                            class="w-full px-2 sm:px-3 py-1.5 sm:py-2 border rounded text-xs sm:text-sm">
                                                            <option value="">-- Pilih --</option>
                                                            @foreach ($finishedProducts as $product)
                                                                <option value="{{ $product->id }}">{{ $product->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td class="py-2 sm:py-3 px-2 sm:px-4">
                                                        <input type="number" min="1"
                                                            x-model="output.planned_quantity"
                                                            :name="`outputs[${index}][planned_quantity]`"
                                                            class="w-full px-2 sm:px-3 py-1.5 sm:py-2 border rounded text-xs sm:text-sm">
                                                    </td>
                                                    <td class="py-2 sm:py-3 px-2 sm:px-4 hidden md:table-cell">
                                                        <input type="text" x-model="output.notes"
                                                            :name="`outputs[${index}][notes]`"
                                                            class="w-full px-2 sm:px-3 py-1.5 sm:py-2 border rounded text-xs sm:text-sm">
                                                    </td>
                                                    <td class="py-2 sm:py-3 px-2 sm:px-4 text-center">
                                                        <button type="button" @click="removeOutput(index)"
                                                            class="p-1 sm:p-2 text-red-600 hover:bg-red-50 rounded transition-colors">
                                                            <i class="bi bi-trash text-sm sm:text-base"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>

                                @error('outputs')
                                    <div
                                        class="mt-3 sm:mt-4 p-3 sm:p-4 bg-red-50 border border-red-200 rounded text-red-800 text-xs sm:text-sm">
                                        <i class="bi bi-exclamation-circle mr-2"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Sidebar --}}
                    <div class="lg:col-span-1 space-y-4 sm:space-y-6">
                        {{-- Panduan Pengajuan --}}
                        <div class="bg-white rounded-lg sm:rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                            <div class="bg-gradient-to-r from-cyan-600 via-cyan-700 to-blue-700 px-4 sm:px-6 py-4 sm:py-6">
                                <div class="flex items-center gap-2 sm:gap-3">
                                    <div
                                        class="w-7 sm:w-8 h-7 sm:h-8 bg-white/20 rounded flex-shrink-0 items-center justify-center hidden sm:flex">
                                        <i class="bi bi-info-circle text-white text-sm"></i>
                                    </div>
                                    <h2 class="text-lg sm:text-xl font-bold text-white">Panduan</h2>
                                </div>
                            </div>

                            <div class="p-4 sm:p-6 lg:p-8">
                                <div class="space-y-4 sm:space-y-6">
                                    <div>
                                        <h6
                                            class="text-xs sm:text-sm font-semibold text-orange-700 mb-2 sm:mb-3 flex items-center">
                                            <i class="bi bi-list-ol mr-2 flex-shrink-0"></i>
                                            <span>Langkah-Langkah:</span>
                                        </h6>
                                        <ol
                                            class="list-decimal list-inside space-y-1 text-xs sm:text-sm text-gray-700 ml-1">
                                            <li>Isi informasi pengajuan</li>
                                            <li>Tambah bahan setengah jadi</li>
                                            <li>Periksa stok dan satuan</li>
                                            <li>Kirim untuk persetujuan</li>
                                        </ol>
                                    </div>

                                    <div class="border-t border-gray-200 pt-3 sm:pt-4">
                                        <h6
                                            class="text-xs sm:text-sm font-semibold text-amber-700 mb-2 sm:mb-3 flex items-center">
                                            <i class="bi bi-exclamation-triangle mr-2 flex-shrink-0"></i>
                                            <span>Catatan Penting:</span>
                                        </h6>
                                        <ul class="list-disc list-inside space-y-1 text-xs sm:text-sm text-gray-700 ml-1">
                                            <li>Pastikan stok mencukupi</li>
                                            <li>Penggunaan akan tercatat</li>
                                            <li>Periksa sebelum mengirim</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Form Actions --}}
                        <div class="bg-white rounded-lg sm:rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                            <div class="p-4 sm:p-6 lg:p-8">
                                <div class="flex flex-col gap-2 sm:gap-3">
                                    <button type="submit"
                                        class="w-full inline-flex items-center justify-center px-4 sm:px-6 py-2.5 sm:py-3 bg-gradient-to-r from-orange-600 to-red-600 border border-transparent rounded-lg sm:rounded-xl text-xs sm:text-sm font-medium text-white hover:from-orange-700 hover:to-red-700 focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-all duration-200 shadow-lg hover:shadow-xl">
                                        <i class="bi bi-send mr-1 sm:mr-2 text-sm sm:text-base"></i>
                                        <span>Kirim Pengajuan</span>
                                    </button>
                                    <a href="{{ route('semi-finished-usage-requests.index') }}"
                                        class="w-full inline-flex items-center justify-center px-4 sm:px-6 py-2.5 sm:py-3 border border-gray-300 rounded-lg sm:rounded-xl text-xs sm:text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 shadow-sm hover:shadow-md">
                                        <i class="bi bi-x-circle mr-1 sm:mr-2 text-sm sm:text-base"></i>
                                        <span>Batal</span>
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
            nextKey: data.items.length + 1,

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

            onMaterialChange(event, index) {
                const selectedOption = event.target.selectedOptions[0]
                const unitId = selectedOption?.dataset?.unitId || null
                this.items[index].unit_id = unitId
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
