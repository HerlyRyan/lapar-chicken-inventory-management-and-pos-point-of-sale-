@extends('layouts.app')

@section('title', 'Data Produk Siap Jual')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-green-50/30 to-emerald-50/30">
        {{-- Page Header --}}
        <x-index.header title="Produk Siap Jual" subtitle="Kelola data produk siap jual untuk penjualan"
            addRoute="{{ route('finished-products.create', request()->has('branch_id') ? ['branch_id' => request('branch_id')] : []) }}"
            addText="Tambah Produk Siap Jual" />

        {{-- Main Content --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-8">
            <div x-data="sortableTable(@js($finishedProducts))" @sort-column.window="sortBy($event.detail)"
                class="bg-white rounded-lg sm:rounded-2xl shadow-lg sm:shadow-xl border border-gray-200 overflow-hidden">
                {{-- Card Header --}}
                <x-index.card-header title="Daftar Produk Siap Jual" />

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
                                        x-text="product.price"></td>
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
                                        }">
                                            <x-index.action-buttons :view="true" :edit="true" :delete="true"
                                                :toggle="true" />
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
@endsection
