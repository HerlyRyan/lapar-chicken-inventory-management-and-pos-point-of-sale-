@extends('layouts.app')

@section('title', 'Data Produk Siap Jual')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-green-50/30 to-emerald-50/30">
        {{-- Page Header --}}
        <x-index.header title="Produk Siap Jual" subtitle="Kelola data produk siap jual untuk penjualan"
            addRoute="{{ route('finished-products.create', request()->has('branch_id') ? ['branch_id' => request('branch_id')] : []) }}"
            addText="Tambah Produk Siap Jual">
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
            <div x-data="sortableTable(@js($finishedProducts))"
                @stock-updated.window="
        if ($event.detail.productId === product.id) {
            product.stock = $event.detail.newStock
        }"
                @sort-column.window="sortBy($event.detail)"
                class="bg-white rounded-lg sm:rounded-2xl shadow-lg sm:shadow-xl border border-gray-200 overflow-hidden">
                {{-- Card Header --}}
                <x-index.card-header title="Produk Siap Jual" />

                {{-- Filter Section --}}
                <x-filter-bar searchPlaceholder="Cari nama, kode, atau deskripsi produk siap jual..." :selects="$selects ?? []" />

                {{-- Desktop Table --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <x-index.table-head :columns="$columns" />
                        <tbody class="bg-white divide-y divide-gray-200">
                            <template x-for="(product, index) in sortedRows" :key="product.id">
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="index + 1"></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800"
                                            x-text="product.code ?? '-'"></span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <template x-if="product.photo">
                                            <img :src="'/storage/' + product.photo" :alt="product.name"
                                                class="w-12 h-12 rounded object-cover">
                                        </template>
                                        <template x-if="!product.photo">
                                            <div class="w-12 h-12 bg-gray-100 rounded flex items-center justify-center">
                                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path
                                                        d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" />
                                                </svg>
                                            </div>
                                        </template>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900" x-text="product.name"></div>
                                        <div class="text-sm text-gray-500" x-text="product.description_short"></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <template x-if="product.category">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
                                                x-text="product.category.name"></span>
                                        </template>
                                        <template x-if="!product.category">
                                            <span class="text-gray-500">-</span>
                                        </template>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                                        x-text="product.unit.unit_name ?? '-'"></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div
                                            :class="[product.is_low_stock ? 'text-red-600' : 'text-green-600',
                                                'text-sm font-medium'
                                            ]">
                                            <span x-text="product.display_stock_quantity"></span>
                                            <template x-if="product.is_low_stock">
                                                <svg class="w-4 h-4 inline ml-1 text-yellow-500" fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </template>
                                        </div>
                                        <div class="text-xs text-gray-500" x-text="product.branch_info"></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                                        x-text="product.minimum_stock"></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
                                        x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(product.price || 0)"></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <template x-if="product.is_active">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <div class="w-1.5 h-1.5 bg-green-400 rounded-full mr-1"></div>
                                                Aktif
                                            </span>
                                        </template>
                                        <template x-if="!product.is_active">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                <div class="w-1.5 h-1.5 bg-gray-400 rounded-full mr-1"></div>
                                                Tidak Aktif
                                            </span>
                                        </template>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                        <div x-data="{
                                            viewUrl: '/finished-products/' + product.id + '{{ request()->has('branch_id') ? '?branch_id=' . request('branch_id') : '' }}',
                                            editUrl: '/finished-products/' + product.id + '/edit{{ request()->has('branch_id') ? '?branch_id=' . request('branch_id') : '' }}',
                                            deleteUrl: '/finished-products/' + product.id,
                                            toggleUrl: '/finished-products/' + product.id + '/toggle-status',
                                            itemName: 'produk ' + product.name,
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
                                    </td>
                                </tr>
                            </template>
                            <template x-if="sortedRows.length === 0">
                                <x-index.none-data colspan="11" />
                            </template>
                        </tbody>
                    </table>
                </div>

                {{-- Mobile Cards --}}
                <div class="md:hidden divide-y divide-gray-200">
                    <template x-for="(product, index) in sortedRows" :key="product.id">
                        <div class="p-4 hover:bg-gray-50 transition-colors duration-150">
                            {{-- Product Header --}}
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-sm font-medium text-gray-900 truncate" x-text="product.name"></h3>
                                        <span class="text-xs text-gray-500 ml-2 flex-shrink-0" x-text="product.code"></span>
                                    </div>
                                    <p class="text-sm text-gray-500 truncate mt-1" x-text="product.description_short"></p>
                                </div>
                            </div>

                            {{-- Product Details --}}
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500">Kategori:</span>
                                    <template x-if="product.category">
                                        <span class="text-xs font-medium bg-blue-100 text-blue-800 px-2 py-1 rounded-full"
                                            x-text="product.category"></span>
                                    </template>
                                    <template x-if="!product.category">
                                        <span class="text-sm text-gray-900">-</span>
                                    </template>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500">Satuan:</span>
                                    <span class="text-sm text-gray-900" x-text="product.unit_name ?? '-'"></span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500">Stok:</span>
                                    <span
                                        :class="[product.is_low_stock ? 'text-red-600' : 'text-green-600',
                                            'text-sm font-medium'
                                        ]">
                                        <span x-text="product.display_stock_quantity"></span>
                                        <template x-if="product.is_low_stock">
                                            <svg class="w-4 h-4 inline text-yellow-500" fill="currentColor"
                                                viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </template>
                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500">Min. Stok:</span>
                                    <span class="text-sm text-gray-900" x-text="product.minimum_stock_formatted"></span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500">Harga:</span>
                                    <span class="text-sm font-medium text-gray-900"
                                        x-text="product.price_formatted"></span>
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

                            {{-- Actions --}}
                            <div class="mt-4 pt-3 border-t border-gray-200">
                                <div x-data="{
                                    viewUrl: '/finished-products/' + product.id + '{{ request()->has('branch_id') ? '?branch_id=' . request('branch_id') : '' }}',
                                    editUrl: '/finished-products/' + product.id + '/edit{{ request()->has('branch_id') ? '?branch_id=' . request('branch_id') : '' }}',
                                    deleteUrl: '/finished-products/' + product.id,
                                    toggleUrl: '/finished-products/' + product.id + '/toggle-status',
                                    itemName: 'produk ' + product.name,
                                    isActive: product.is_active
                                }">
                                    <x-index.action-buttons :view="true" :edit="true" :delete="true"
                                        :toggle="true" />
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <template x-if="sortedRows.length !== 0">
                    <div class="pagination-wrapper">
                        {{ $pagination->withQueryString()->links('vendor.pagination.tailwind') }}
                    </div>
                </template>
            </div>
        </div>
    </div>

    @include('finished-products.adjustment-modal')
@endsection
