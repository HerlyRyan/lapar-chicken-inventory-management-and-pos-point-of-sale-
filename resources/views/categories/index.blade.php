@extends('layouts.app')

@section('title', 'Master Data Kategori')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-orange-50/30 to-red-50/30">
        {{-- Page Header --}}
        <x-index.header title="Kategori" subtitle="Kelola kategori produk siap jual"
            addRoute="{{ route('categories.create') }}" addText="Tambah Kategori" />

        {{-- Main Content --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-8">
            <div x-data="sortableTable(@js($categories))" @sort-column.window="sortBy($event.detail)"
                class="bg-white rounded-lg sm:rounded-2xl shadow-lg sm:shadow-xl border border-gray-200 overflow-hidden">
                {{-- Card Header --}}
                <x-index.card-header title="Kategori" />

                {{-- Filter Section --}}
                <x-filter-bar searchPlaceholder="Cari kode atau nama kategori..." :selects="$selects ?? []" />

                {{-- Desktop Table --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <x-index.table-head :columns="$columns" />
                        <tbody class="bg-white divide-y divide-gray-200">
                            <template x-for="(category, index) in sortedRows" :key="category.id">
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="index + 1"></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-800" x-text="category.code"></span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900" x-text="category.name"></div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        <div class="max-w-xs truncate" :title="category.description" x-text="category.description || '-'">
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <template x-if="category.is_active">
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <div class="w-1.5 h-1.5 bg-green-400 rounded-full mr-1"></div>
                                                Aktif
                                            </span>
                                        </template>
                                        <template x-if="!category.is_active">
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                <div class="w-1.5 h-1.5 bg-gray-400 rounded-full mr-1"></div>
                                                Nonaktif
                                            </span>
                                        </template>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <div x-data="{
                                            viewUrl: '/categories/' + category.id,
                                            editUrl: '/categories/' + category.id + '/edit',
                                            deleteUrl: category.finished_products_count == 0 ? '/categories/' + category.id : null,
                                            toggleUrl: '/categories/' + category.id + '/toggle-status',
                                            itemName: 'kategori ' + category.name,
                                            isActive: category.is_active
                                        }">
                                            <x-index.action-buttons :view="true" :edit="true" :delete="true"
                                                :toggle="true" />
                                        </div>
                                    </td>
                                </tr>
                            </template>
                            <template x-if="sortedRows.length === 0">
                                <x-index.none-data />
                            </template>
                        </tbody>
                    </table>
                </div>

                {{-- Mobile Cards --}}
                <div class="md:hidden divide-y divide-gray-200">
                    <template x-for="(category, index) in sortedRows" :key="category.id">
                        <div class="p-4 hover:bg-gray-50 transition-colors duration-150">
                            {{-- Category Header --}}
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-sm font-medium text-gray-900 truncate" x-text="category.name"></h3>
                                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-800 ml-2" x-text="category.code"></span>
                                    </div>
                                    <p class="text-sm text-gray-500 truncate mt-1" x-text="category.description || 'Tidak ada deskripsi'"></p>
                                </div>
                            </div>

                            {{-- Category Details --}}
                            <div class="space-y-2">
                                {{-- Status --}}
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500">Status:</span>
                                    <template x-if="category.is_active">
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <div class="w-1.5 h-1.5 bg-green-400 rounded-full mr-1"></div>
                                            Aktif
                                        </span>
                                    </template>
                                    <template x-if="!category.is_active">
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            <div class="w-1.5 h-1.5 bg-gray-400 rounded-full mr-1"></div>
                                            Nonaktif
                                        </span>
                                    </template>
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="mt-4 pt-3 border-t border-gray-200">
                                <div x-data="{
                                    viewUrl: '/categories/' + category.id,
                                    editUrl: '/categories/' + category.id + '/edit',
                                    deleteUrl: category.finished_products_count == 0 ? '/categories/' + category.id : null,
                                    toggleUrl: '/categories/' + category.id + '/toggle-status',
                                    itemName: 'kategori ' + category.name,
                                    isActive: category.is_active
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
                        {{ $pagination->links('vendor.pagination.tailwind') }}
                    </div>
                </template>
            </div>
        </div>
    </div>
@endsection
