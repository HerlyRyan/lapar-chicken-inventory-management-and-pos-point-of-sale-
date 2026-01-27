@extends('layouts.app')

@section('title', 'Master Satuan')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-orange-50/30 to-red-50/30">
        {{-- Page Header --}}
        <x-index.header title="Master Satuan" subtitle="Kelola data satuan untuk pengukuran stok dan produksi"
            addRoute="{{ route('units.create') }}" addText="Tambah Satuan" icon="rulers" />

        {{-- Main Content --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-8">
            <div x-data="sortableTable(@js($units))" @sort-column.window="sortBy($event.detail)"
                class="bg-white rounded-lg sm:rounded-2xl shadow-lg sm:shadow-xl border border-gray-200 overflow-hidden">
                {{-- Card Header --}}
                <x-index.card-header title="Satuan" />

                {{-- Filter Section --}}
                <x-filter-bar searchPlaceholder="Cari nama, singkatan atau deskripsi satuan..." :selects="$selects" />

                {{-- Desktop Table --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <x-index.table-head :columns="$columns" />
                        <tbody class="bg-white divide-y divide-gray-200">
                            <template x-for="(unit, index) in sortedRows" :key="unit.id">
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="index + 1"></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900" x-text="unit.unit_name"></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800"
                                            x-text="unit.abbreviation"></span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        <div class="max-w-xs truncate" :title="unit.description || 'Tidak ada deskripsi'"
                                            x-text="unit.description || 'Tidak ada deskripsi'"></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <template x-if="unit.is_active">
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <div class="w-1.5 h-1.5 bg-green-400 rounded-full mr-1"></div>
                                                Aktif
                                            </span>
                                        </template>
                                        <template x-if="!unit.is_active">
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                <div class="w-1.5 h-1.5 bg-gray-400 rounded-full mr-1"></div>
                                                Tidak Aktif
                                            </span>
                                        </template>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <div x-data="{
                                            viewUrl: '/units/' + unit.id,
                                            editUrl: '/units/' + unit.id + '/edit',
                                            deleteUrl: '/units/' + unit.id,
                                            toggleUrl: '/units/' + unit.id + '/toggle-status',
                                            itemName: 'satuan ' + unit.unit_name,
                                            isActive: unit.is_active
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
                    <template x-for="(unit, index) in sortedRows" :key="unit.id">
                        <div class="p-4 hover:bg-gray-50 transition-colors duration-150">
                            {{-- Unit Header --}}
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-sm font-medium text-gray-900 truncate" x-text="unit.unit_name"></h3>
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 ml-2"
                                            x-text="unit.abbreviation"></span>
                                    </div>
                                    <p class="text-sm text-gray-500 truncate mt-1"
                                        x-text="unit.description || 'Tidak ada deskripsi'"></p>
                                </div>
                            </div>

                            {{-- Unit Details --}}
                            <div class="space-y-2">
                                {{-- Status --}}
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500">Status:</span>
                                    <template x-if="unit.is_active">
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <div class="w-1.5 h-1.5 bg-green-400 rounded-full mr-1"></div>
                                            Aktif
                                        </span>
                                    </template>
                                    <template x-if="!unit.is_active">
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
                                    editUrl: '/units/' + unit.id + '/edit',
                                    deleteUrl: '/units/' + unit.id,
                                    toggleUrl: '/units/' + unit.id + '/toggle-status',
                                    itemName: 'satuan ' + unit.unit_name,
                                    isActive: unit.is_active
                                }">
                                    <x-index.action-buttons :view="false" :edit="true" :delete="true"
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
