@extends('layouts.app')

@section('title', 'Master Bahan Setengah Jadi')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-orange-50/30 to-red-50/30">
        {{-- Page Header --}}
        <x-index.header title="Master Bahan Setengah Jadi" subtitle="Kelola data bahan setengah jadi hasil produksi internal"
            addRoute="{{ route('semi-finished-products.create') }}{{ request('branch_id') ? '?branch_id=' . request('branch_id') : '' }}"
            addText="Tambah Bahan Setengah Jadi">
            <a href="{{ route('semi-finished-distributions.create') }}?branch_id={{ request('branch_id', optional($selectedBranch)->id ?? session('selected_dashboard_branch')) }}"
                class="w-full sm:w-auto inline-flex items-center justify-center px-4 sm:px-6 py-2.5 sm:py-3 bg-gradient-to-r from-orange-500 to-red-600 hover:from-orange-600 hover:to-red-700 text-white font-semibold rounded-lg sm:rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 transform hover:-translate-y-0.5 text-sm sm:text-base">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M0 4a2 2 0 012-2h11a2 2 0 012 2v1H0V4zM0 7h15v5a2 2 0 01-2 2H2a2 2 0 01-2-2V7z" />
                </svg>
                <span class="hidden sm:inline">Distribusi ke Cabang</span>
                <span class="sm:hidden">Distribusi ke Cabang</span>
            </a>
        </x-index.header>

        {{-- Main Content --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-8">
            <div x-data="sortableTable(@js($semiFinishedProducts))" @sort-column.window="sortBy($event.detail)"
                class="bg-white rounded-lg sm:rounded-2xl shadow-lg sm:shadow-xl border border-gray-200 overflow-hidden">
                {{-- Card Header --}}
                <x-index.card-header title="Bahan Setengah Jadi" />

                <div class="p-4 sm:p-6">
                    {{-- Branch Info --}}
                    <div class="mb-4">
                        @if ($branchForStock)
                            <div class="rounded-md bg-blue-50 p-3 border border-blue-100 text-sm text-blue-700">
                                <i class="bi bi-building me-2"></i>
                                <strong>Cabang Aktif:</strong> {{ $branchForStock->name }} ({{ $branchForStock->code }})
                                <div class="text-xs text-blue-600 mt-1">Menampilkan stok untuk cabang ini</div>
                            </div>
                        @elseif($selectedBranch)
                            <div class="rounded-md bg-blue-50 p-3 border border-blue-100 text-sm text-blue-700">
                                <i class="bi bi-building me-2"></i>
                                <strong>Cabang Dipilih:</strong> {{ $selectedBranch->name }} ({{ $selectedBranch->code }})
                                <div class="text-xs text-blue-600 mt-1">Menampilkan stok untuk cabang ini</div>
                            </div>
                        @else
                            <div class="rounded-md bg-gray-50 p-3 border border-gray-100 text-sm text-gray-700">
                                <i class="bi bi-buildings me-2"></i>
                                <strong>Tampilan:</strong> Semua Cabang
                                <div class="text-xs text-gray-600 mt-1">Menampilkan total stok dari semua cabang</div>
                            </div>
                        @endif
                    </div>

                    {{-- Filter Section --}}
                    <x-filter-bar searchPlaceholder="Cari nama, kode, atau deskripsi bahan setengah jadi..."
                        :selects="$selects" />

                    {{-- Desktop Table --}}
                    <div class="hidden md:block overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <x-index.table-head :columns="$columns" />
                            <tbody class="bg-white divide-y divide-gray-200">
                                <template x-for="(product, index) in sortedRows" :key="product.id">
                                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="index + 1">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <template x-if="product.code">
                                                <span
                                                    class="inline-flex items-center px-2 py-1 rounded-md bg-red-100 text-red-800 text-xs font-semibold"
                                                    x-text="product.code"></span>
                                            </template>
                                            <template x-if="!product.code">
                                                <span class="text-gray-400">-</span>
                                            </template>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <template x-if="product.image">
                                                <img :src="product.image" :alt="product.name"
                                                    class="rounded-md object-cover" style="width:60px; height:60px;">
                                            </template>
                                            <template x-if="!product.image">
                                                <div class="w-15 h-15 bg-gray-100 rounded-md flex items-center justify-center"
                                                    style="width:60px; height:60px;">
                                                    <i class="bi bi-image text-gray-400 text-lg"></i>
                                                </div>
                                            </template>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900" x-text="product.name"></div>
                                            {{-- <div class="text-sm text-gray-500"
                                                x-text="product.description ? product.description.substring(0, 50) + (product.description.length > 50 ? '...' : '') : ''">
                                            </div> --}}
                                        </td>
                                        <td class="px-6 py-4 text-sm">
                                            <template x-if="product.category">
                                                <span
                                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                                                    <i class="bi bi-tag me-1"></i>
                                                    <span x-text="product.category.name"></span>
                                                </span>
                                            </template>
                                            <template x-if="!product.category">
                                                <span>-</span>
                                            </template>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-700"
                                            x-text="product.unit ? (typeof product.unit === 'object' ? product.unit.unit_name : product.unit) : '-'">
                                        </td>
                                        <td class="px-6 py-4 text-sm">
                                            <div :class="product.is_low_stock ? 'text-red-600' : 'text-green-600'"
                                                class="font-semibold">
                                                <span
                                                    x-text="new Intl.NumberFormat('id-ID').format(product.display_stock_quantity || 0)"></span>
                                                <template x-if="product.is_low_stock">
                                                    <i class="bi bi-exclamation-triangle-fill text-yellow-500 ml-1"
                                                        title="Stok di bawah minimum"></i>
                                                </template>
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                @if ($branchForStock)
                                                    {{ $branchForStock->code }}
                                                @elseif($selectedBranch)
                                                    {{ $selectedBranch->code }}
                                                @else
                                                    Total
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-700"
                                            x-text="new Intl.NumberFormat('id-ID').format(product.minimum_stock || 0)"></td>
                                        <td class="px-6 py-4 text-sm text-gray-700"
                                            x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(product.production_cost || 0)">
                                        </td>
                                        <td class="px-6 py-4">
                                            <template x-if="product.is_active">
                                                <span
                                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <div class="w-1.5 h-1.5 bg-green-400 rounded-full mr-1"></div>
                                                    Aktif
                                                </span>
                                            </template>
                                            <template x-if="!product.is_active">
                                                <span
                                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    <div class="w-1.5 h-1.5 bg-gray-400 rounded-full mr-1"></div>
                                                    Non-Aktif
                                                </span>
                                            </template>
                                        </td>
                                        <td class="px-6 py-4 text-center text-sm">
                                            <div x-data="{
                                                viewUrl: '/semi-finished-products/' + product.id,
                                                editUrl: '/semi-finished-products/' + product.id + '/edit',
                                                deleteUrl: '/semi-finished-products/' + product.id,
                                                toggleUrl: '/semi-finished-products/' + product.id + '/toggle-status',
                                                itemName: 'produk setengah jadi ' + product.name,
                                                isActive: product.is_active
                                            }" class="flex gap-3">
                                                <x-index.action-buttons :view="true" :edit="true"
                                                    :delete="true" :toggle="true" />
                                                <button type="button"
                                                    @click="$dispatch('open-adjustment-modal', { id: product.id, name:
                                                                    product.name, stock: product.display_stock_quantity })"
                                                    class="group relative inline-flex items-center justify-center w-8 h-8 sm:w-9 sm:h-9
                                                   bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700
                                                   text-white rounded-lg shadow-sm hover:shadow-md transition-all duration-200">
                                                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path
                                                            d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm5 11h-4v4h-2v-4H7v-2h4V7h2v4h4v2z" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                                <template x-if="sortedRows.length === 0">
                                    <x-index.none-data colspan="6" />
                                </template>
                            </tbody>
                        </table>
                    </div>

                    {{-- Mobile Cards --}}
                    <div class="md:hidden divide-y divide-gray-200">
                        <template x-for="(product, index) in sortedRows" :key="product.id">
                            <div class="p-4 hover:bg-gray-50 transition-colors duration-150">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between">
                                            <h3 class="text-sm font-medium text-gray-900 truncate" x-text="product.name">
                                            </h3>
                                            <span class="text-xs text-gray-500 ml-2" x-text="product.code"></span>
                                        </div>
                                        <p class="text-sm text-gray-500 truncate mt-1" x-text="product.description"></p>
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-500">Kategori:</span>
                                        <div class="text-sm text-gray-900"
                                            x-text="product.category ? product.category.name : '-'"></div>
                                    </div>

                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-500">Stok:</span>
                                        <div :class="product.is_low_stock ? 'text-red-600' : 'text-green-600'"
                                            class="text-sm font-semibold">
                                            <span
                                                x-text="new Intl.NumberFormat('id-ID').format(product.display_stock_quantity || 0)"></span>
                                            <template x-if="product.is_low_stock">
                                                <i class="bi bi-exclamation-triangle-fill text-yellow-500 ml-1"
                                                    title="Stok di bawah minimum"></i>
                                            </template>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-500">Status:</span>
                                        <template x-if="product.is_active">
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <div class="w-1.5 h-1.5 bg-green-400 rounded-full mr-1"></div>
                                                Aktif
                                            </span>
                                        </template>
                                        <template x-if="!product.is_active">
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                <div class="w-1.5 h-1.5 bg-gray-400 rounded-full mr-1"></div>
                                                Tidak Aktif
                                            </span>
                                        </template>
                                    </div>
                                </div>

                                <div class="mt-4 pt-3 border-t border-gray-200">
                                    <div x-data="{
                                        viewUrl: '/semi-finished-products/' + product.id,
                                        editUrl: '/semi-finished-products/' + product.id + '/edit',
                                        deleteUrl: '/semi-finished-products/' + product.id,
                                        toggleUrl: '/semi-finished-products/' + product.id + '/toggle-status',
                                        itemName: 'produk setengah jadi ' + product.name,
                                        isActive: product.is_active
                                    }" class="flex gap-3">
                                        <x-index.action-buttons :view="true" :edit="true" :delete="true"
                                            :toggle="true" />
                                        <button type="button"
                                            @click="$dispatch('open-adjustment-modal', { id: product.id, name:
                                                                    product.name, stock: product.display_stock_quantity })"
                                            class="group relative inline-flex items-center justify-center w-8 h-8 sm:w-9 sm:h-9
                                                   bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700
                                                   text-white rounded-lg shadow-sm hover:shadow-md transition-all duration-200">
                                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="currentColor" viewBox="0 0 24 24">
                                                <path
                                                    d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm5 11h-4v4h-2v-4H7v-2h4V7h2v4h4v2z" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <template x-if="sortedRows.length !== 0">
                        <div class="pagination-wrapper">
                            {{ $pagination->links('vendor.pagination.tailwind') }}
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    @include('semi-finished-products.adjustment-modal')
@endsection
