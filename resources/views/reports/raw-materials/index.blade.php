@extends('layouts.app')

@section('title', 'Master Bahan Baku')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-orange-50/30 to-red-50/30">
        {{-- Page Header --}}
        <x-index.header title="Master Bahan Baku" />

        {{-- Main Content --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-8">
            <div x-data="sortableTable(@js($rawMaterials))" @sort-column.window="sortBy($event.detail)"
                class="bg-white rounded-lg sm:rounded-2xl shadow-lg sm:shadow-xl border border-gray-200 overflow-hidden">
                {{-- Card Header --}}
                <x-index.card-header title="Bahan Baku" />

                {{-- Filter Section --}}
                <x-filter-bar searchPlaceholder="Cari nama, kode, atau deskripsi bahan baku..." :selects="$selects"
                    print="true" printRouteName="reports.raw-materials.print" />

                {{-- Desktop Table --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <x-index.table-head :columns="$columns" print="true" />
                        <tbody class="bg-white divide-y divide-gray-200">
                            <template x-for="(material, index) in sortedRows" :key="material.id">
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="index + 1"></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <template x-if="material.code">
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800"
                                                x-text="material.code"></span>
                                        </template>
                                        <template x-if="!material.code">
                                            <span class="text-gray-400">-</span>
                                        </template>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <template x-if="material.image">
                                            <img :src="material.image" :alt="material.name"
                                                class="w-15 h-15 rounded-lg object-cover border border-gray-200">
                                        </template>
                                        <template x-if="!material.image">
                                            <div
                                                class="w-15 h-15 bg-gray-100 border border-gray-200 rounded-lg flex items-center justify-center">
                                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                        </template>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900" x-text="material.name"></div>
                                        <template x-if="material.description">
                                            <div class="text-sm text-gray-500 max-w-xs truncate"
                                                :title="material.description"
                                                x-text="material.description.length > 50 ? material.description.substring(0, 50) + '...' : material.description">
                                            </div>
                                        </template>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <template x-if="material.category">
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5c.256 0 .512.098.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                                <span x-text="material.category.name"></span>
                                            </span>
                                        </template>
                                        <template x-if="!material.category">
                                            <span class="text-gray-400">-</span>
                                        </template>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <template x-if="material.unit">
                                            <span x-text="material.unit.unit_name"></span>
                                        </template>
                                        <template x-if="!material.unit">
                                            <span class="text-gray-400">-</span>
                                        </template>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <span class="text-lg font-bold"
                                                :class="Number(material.current_stock) <= Number(material.minimum_stock) ?
                                                    'text-red-600' : 'text-green-600'"
                                                x-text="new Intl.NumberFormat('id-ID').format(material.current_stock)"></span>
                                            <template
                                                x-if="Number(material.current_stock) <= Number(material.minimum_stock)">
                                                <svg class="w-5 h-5 text-yellow-500 ml-2" fill="currentColor"
                                                    viewBox="0 0 20 20" title="Stok di bawah minimum">
                                                    <path fill-rule="evenodd"
                                                        d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </template>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                                        x-text="new Intl.NumberFormat('id-ID').format(material.minimum_stock)"></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                                        x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(material.unit_price)"></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <template x-if="material.supplier">
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800"
                                                x-text="material.supplier.name"></span>
                                        </template>
                                        <template x-if="!material.supplier">
                                            <span class="text-gray-400">-</span>
                                        </template>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <template x-if="material.is_active">
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <div class="w-1.5 h-1.5 bg-green-400 rounded-full mr-1"></div>
                                                Aktif
                                            </span>
                                        </template>
                                        <template x-if="!material.is_active">
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                <div class="w-1.5 h-1.5 bg-gray-400 rounded-full mr-1"></div>
                                                Tidak Aktif
                                            </span>
                                        </template>
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
                    <template x-for="(material, index) in sortedRows" :key="material.id">
                        <div class="p-4 hover:bg-gray-50 transition-colors duration-150">
                            {{-- Material Header --}}
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex items-start space-x-3 flex-1">
                                    {{-- Image --}}
                                    <template x-if="material.image_url">
                                        <img :src="material.image_url" :alt="material.name"
                                            class="w-12 h-12 rounded-lg object-cover border border-gray-200 flex-shrink-0">
                                    </template>
                                    <template x-if="!material.image_url">
                                        <div
                                            class="w-12 h-12 bg-gray-100 border border-gray-200 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    </template>
                                    {{-- Name and Code --}}
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-sm font-medium text-gray-900 truncate" x-text="material.name">
                                        </h3>
                                        <template x-if="material.code">
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 mt-1"
                                                x-text="material.code"></span>
                                        </template>
                                        <template x-if="material.description">
                                            <p class="text-sm text-gray-500 truncate mt-1"
                                                x-text="material.description.length > 30 ? material.description.substring(0, 30) + '...' : material.description">
                                            </p>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            {{-- Material Details --}}
                            <div class="space-y-2">
                                {{-- Category and Unit --}}
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500">Kategori:</span>
                                    <template x-if="material.category">
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
                                            x-text="material.category.name"></span>
                                    </template>
                                    <template x-if="!material.category">
                                        <span class="text-sm text-gray-400">-</span>
                                    </template>
                                </div>

                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500">Satuan:</span>
                                    <template x-if="material.unit">
                                        <span class="text-sm text-gray-900" x-text="material.unit.unit_name"></span>
                                    </template>
                                    <template x-if="!material.unit">
                                        <span class="text-sm text-gray-400">-</span>
                                    </template>
                                </div>

                                {{-- Stock Info --}}
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500">Stok Saat Ini:</span>
                                    <div class="flex items-center">
                                        <span class="text-sm font-bold"
                                            :class="Number(material.current_stock) <= Number(material.minimum_stock) ?
                                                'text-red-600' : 'text-green-600'"
                                            x-text="new Intl.NumberFormat('id-ID').format(material.current_stock)"></span>
                                        <template x-if="Number(material.current_stock) <= Number(material.minimum_stock)">
                                            <svg class="w-4 h-4 text-yellow-500 ml-1" fill="currentColor"
                                                viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </template>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500">Stok Minimum:</span>
                                    <span class="text-sm text-gray-900"
                                        x-text="new Intl.NumberFormat('id-ID').format(material.minimum_stock)"></span>
                                </div>

                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500">Harga Satuan:</span>
                                    <span class="text-sm text-gray-900"
                                        x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(material.unit_price)"></span>
                                </div>

                                {{-- Supplier --}}
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500">Supplier:</span>
                                    <template x-if="material.supplier">
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800"
                                            x-text="material.supplier.name"></span>
                                    </template>
                                    <template x-if="!material.supplier">
                                        <span class="text-sm text-gray-400">-</span>
                                    </template>
                                </div>

                                {{-- Status --}}
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500">Status:</span>
                                    <template x-if="material.is_active">
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <div class="w-1.5 h-1.5 bg-green-400 rounded-full mr-1"></div>
                                            Aktif
                                        </span>
                                    </template>
                                    <template x-if="!material.is_active">
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            <div class="w-1.5 h-1.5 bg-gray-400 rounded-full mr-1"></div>
                                            Tidak Aktif
                                        </span>
                                    </template>
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
@endsection
