@extends('layouts.app')

@section('title', 'Paket Penjualan')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-orange-50/30 to-red-50/30">
        {{-- Page Header --}}
        <x-index.header title="Paket Penjualan" />

        {{-- Main Content --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-8">
            <div x-data="sortableTable(@js($salesPackages))" @sort-column.window="sortBy($event.detail)"
                class="bg-white rounded-lg sm:rounded-2xl shadow-lg sm:shadow-xl border border-gray-200 overflow-hidden">
                {{-- Card Header --}}
                <x-index.card-header title="Paket Penjualan" />

                {{-- Filter Section --}}
                <x-filter-bar searchPlaceholder="Cari nama, kode, atau deskripsi paket..." :selects="$selects" print="true"
                    printRouteName="reports.sale-packages.print" />

                {{-- Desktop Table --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <x-index.table-head :columns="$columns" print="true" />
                        <tbody class="bg-white divide-y divide-gray-200">
                            <template x-for="(pkg, index) in sortedRows" :key="pkg.id">
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="index + 1"></td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <template x-if="pkg.image">
                                                <img :src="'/storage/' + pkg.image" :alt="pkg.name"
                                                    class="w-12 h-12 rounded object-cover" />
                                            </template>
                                            <template x-if="!pkg.image">
                                                <div class="w-12 h-12 rounded bg-gray-100 flex items-center justify-center">
                                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M20 7l-8-4-8 4m0 0l8 4m-8-4v10l8 4m0-10l8 4m-8-4l8-4" />
                                                    </svg>
                                                </div>
                                            </template>
                                            <div class="min-w-0">
                                                <h3 class="text-sm font-medium text-gray-900 truncate" x-text="pkg.name">
                                                </h3>
                                                <p class="text-xs text-gray-500" x-text="pkg.code"></p>
                                                <template x-if="pkg.description">
                                                    <p class="text-xs text-gray-500 truncate mt-1"
                                                        x-text="pkg.description.substring(0, 50)"></p>
                                                </template>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <template x-if="pkg.category">
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
                                                x-text="pkg.category.name"></span>
                                        </template>
                                        <template x-if="!pkg.category">
                                            <span class="text-gray-500">-</span>
                                        </template>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">
                                            <span x-text="`${pkg.package_items.length} produk`"></span>
                                            <div class="flex flex-wrap gap-1 mt-1">
                                                <template x-for="item in pkg.package_items.slice(0, 2)"
                                                    :key="item.id">
                                                    <span
                                                        class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-800"
                                                        x-text="`${item.quantity}${item.finished_product?.unit?.abbreviation ?? 'pcs'} ${item.finished_product?.name}`"></span>
                                                </template>
                                                <template x-if="pkg.package_items.length > 2">
                                                    <span
                                                        class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-600 text-white"
                                                        x-text="`+${pkg.package_items.length - 2} lainnya`"></span>
                                                </template>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-semibold text-gray-900">
                                        <span x-text="`Rp ${new Intl.NumberFormat('id-ID').format(pkg.base_price)}`"></span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <template x-if="pkg.discount_percentage > 0">
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800"
                                                x-text="`-${pkg.discount_percentage}%`"></span>
                                        </template>
                                        <template x-if="pkg.discount_amount > 0">
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800"
                                                x-text="`-Rp ${new Intl.NumberFormat('id-ID').format(pkg.discount_amount)}`"></span>
                                        </template>
                                        <template x-if="pkg.discount_percentage === 0 && pkg.discount_amount === 0">
                                            <span class="text-gray-500">-</span>
                                        </template>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <template x-if="pkg.additional_charge > 0">
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800"
                                                x-text="`+Rp ${new Intl.NumberFormat('id-ID').format(pkg.additional_charge)}`"></span>
                                        </template>
                                        <template x-if="pkg.additional_charge === 0">
                                            <span class="text-gray-500">-</span>
                                        </template>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-blue-600">
                                        <span
                                            x-text="`Rp ${new Intl.NumberFormat('id-ID').format(pkg.final_price)}`"></span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <template x-if="pkg.is_active">
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <div class="w-1.5 h-1.5 bg-green-400 rounded-full mr-1"></div>
                                                Aktif
                                            </span>
                                        </template>
                                        <template x-if="!pkg.is_active">
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
                                <x-index.none-data :colspan="9" />
                            </template>
                        </tbody>
                    </table>
                </div>

                {{-- Mobile Cards --}}
                <div class="md:hidden divide-y divide-gray-200">
                    <template x-for="(pkg, index) in sortedRows" :key="pkg.id">
                        <div class="p-4 hover:bg-gray-50 transition-colors duration-150">
                            {{-- Package Header --}}
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex items-start gap-3 flex-1 min-w-0">
                                    <template x-if="pkg.image">
                                        <img :src="'/storage/' + pkg.image" :alt="pkg.name"
                                            class="w-10 h-10 rounded object-cover" />
                                    </template>
                                    <template x-if="!pkg.image">
                                        <div
                                            class="w-10 h-10 rounded bg-gray-100 flex items-center justify-center flex-shrink-0">
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M20 7l-8-4-8 4m0 0l8 4m-8-4v10l8 4m0-10l8 4m-8-4l8-4" />
                                            </svg>
                                        </div>
                                    </template>
                                    <div class="min-w-0 flex-1">
                                        <h3 class="text-sm font-medium text-gray-900 truncate" x-text="pkg.name"></h3>
                                        <p class="text-xs text-gray-500" x-text="pkg.code"></p>
                                    </div>
                                </div>
                            </div>

                            {{-- Package Details --}}
                            <div class="space-y-2 text-sm">
                                {{-- Category --}}
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-500">Kategori:</span>
                                    <template x-if="pkg.category">
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
                                            x-text="pkg.category.name"></span>
                                    </template>
                                    <template x-if="!pkg.category">
                                        <span class="text-gray-500">-</span>
                                    </template>
                                </div>

                                {{-- Prices --}}
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-500">Harga Dasar:</span>
                                    <span class="font-semibold text-gray-900"
                                        x-text="`Rp ${new Intl.NumberFormat('id-ID').format(pkg.base_price)}`"></span>
                                </div>

                                <div class="flex items-center justify-between">
                                    <span class="text-gray-500">Harga Jual:</span>
                                    <span class="font-bold text-blue-600"
                                        x-text="`Rp ${new Intl.NumberFormat('id-ID').format(pkg.final_price)}`"></span>
                                </div>

                                {{-- Status --}}
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-500">Status:</span>
                                    <template x-if="pkg.is_active">
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <div class="w-1.5 h-1.5 bg-green-400 rounded-full mr-1"></div>
                                            Aktif
                                        </span>
                                    </template>
                                    <template x-if="!pkg.is_active">
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

                {{-- Pagination --}}
                <template x-if="sortedRows.length !== 0">
                    <div class="pagination-wrapper">
                        {{ $pagination->links('vendor.pagination.tailwind') }}
                    </div>
                </template>
            </div>
        </div>
    </div>
@endsection
