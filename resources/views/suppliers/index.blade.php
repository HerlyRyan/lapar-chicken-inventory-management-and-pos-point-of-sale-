@extends('layouts.app')

@section('title', 'Data Supplier')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-orange-50/30 to-red-50/30">
        {{-- Page Header --}}
        <x-index.header title="Supplier" subtitle="Kelola data supplier bahan mentah"
            addRoute="{{ route('suppliers.create') }}" addText="Tambah Supplier" />

        {{-- Main Content --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-8">
            <div x-data="sortableTable(@js($suppliers))" @sort-column.window="sortBy($event.detail)"
                class="bg-white rounded-lg sm:rounded-2xl shadow-lg sm:shadow-xl border border-gray-200 overflow-hidden">
                {{-- Card Header --}}
                <x-index.card-header title="Daftar Supplier" />

                {{-- Filter Section --}}
                <x-filter-bar searchPlaceholder="Cari nama, kode, atau alamat supplier..." :selects="$selects" />

                {{-- Desktop Table --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <x-index.table-head :columns="$columns" />
                        <tbody class="bg-white divide-y divide-gray-200">
                            <template x-for="(supplier, index) in sortedRows" :key="supplier.id">
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="index + 1"></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
                                        x-text="supplier.code"></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900" x-text="supplier.name"></div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        <div class="max-w-xs truncate" :title="supplier.address" x-text="supplier.address">
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <div class="flex items-center gap-2">
                                            <span x-text="supplier.phone"></span>
                                            <template x-if="supplier.phone">
                                                <div class="flex gap-1">
                                                    <a :href="'tel:+' + supplier.phone"
                                                        class="inline-flex items-center justify-center w-6 h-6 bg-blue-500 hover:bg-blue-600 text-white rounded-full shadow-sm transition-all duration-200"
                                                        title="Telepon">
                                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                            <path
                                                                d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                                                        </svg>
                                                    </a>
                                                    <a :href="'https://wa.me/' + supplier.phone" target="_blank"
                                                        class="inline-flex items-center justify-center w-6 h-6 bg-green-500 hover:bg-green-600 text-white rounded-full shadow-sm transition-all duration-200"
                                                        title="WhatsApp">
                                                        <x-whatsapp-logo />
                                                    </a>
                                                </div>
                                            </template>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <template x-if="supplier.is_active">
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <div class="w-1.5 h-1.5 bg-green-400 rounded-full mr-1"></div>
                                                Aktif
                                            </span>
                                        </template>
                                        <template x-if="!supplier.is_active">
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                <div class="w-1.5 h-1.5 bg-gray-400 rounded-full mr-1"></div>
                                                Tidak Aktif
                                            </span>
                                        </template>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <div x-data="{
                                            viewUrl: '/suppliers/' + supplier.id,
                                            editUrl: '/suppliers/' + supplier.id + '/edit',
                                            deleteUrl: '/suppliers/' + supplier.id,
                                            toggleUrl: '/suppliers/' + supplier.id + '/toggle-status',
                                            itemName: 'supplier ' + supplier.name,
                                            isActive: supplier.is_active
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
                    <template x-for="(supplier, index) in sortedRows" :key="supplier.id">
                        <div class="p-4 hover:bg-gray-50 transition-colors duration-150">
                            {{-- Supplier Header --}}
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-sm font-medium text-gray-900 truncate" x-text="supplier.name"></h3>
                                        <span class="text-xs text-gray-500 ml-2" x-text="supplier.code"></span>
                                    </div>
                                    <p class="text-sm text-gray-500 truncate mt-1" x-text="supplier.address"></p>
                                </div>
                            </div>

                            {{-- Supplier Details --}}
                            <div class="space-y-2">
                                {{-- Phone --}}
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500">Telepon:</span>
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm text-gray-900" x-text="supplier.phone"></span>
                                        <template x-if="supplier.phone">
                                            <div class="flex gap-1">
                                                <a :href="'tel:+' + supplier.phone"
                                                    class="inline-flex items-center justify-center w-5 h-5 bg-blue-500 hover:bg-blue-600 text-white rounded-full shadow-sm transition-all duration-200"
                                                    title="Telepon">
                                                    <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path
                                                            d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                                                    </svg>
                                                </a>
                                                <a :href="'https://wa.me/' + supplier.phone" target="_blank"
                                                    class="inline-flex items-center justify-center w-5 h-5 bg-green-500 hover:bg-green-600 text-white rounded-full shadow-sm transition-all duration-200"
                                                    title="WhatsApp">
                                                    <x-whatsapp-logo class="w-2.5 h-2.5" />
                                                </a>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                {{-- Status --}}
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500">Status:</span>
                                    <template x-if="supplier.is_active">
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <div class="w-1.5 h-1.5 bg-green-400 rounded-full mr-1"></div>
                                            Aktif
                                        </span>
                                    </template>
                                    <template x-if="!supplier.is_active">
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
                                    viewUrl: '/suppliers/' + supplier.id,
                                    editUrl: '/suppliers/' + supplier.id + '/edit',
                                    deleteUrl: '/suppliers/' + supplier.id,
                                    toggleUrl: '/suppliers/' + supplier.id + '/toggle-status',
                                    itemName: 'supplier ' + supplier.name,
                                    isActive: supplier.is_active
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
