@extends('layouts.app')

@php
    use Illuminate\Support\Facades\Storage;
@endphp

@section('title', 'Master Cabang')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-orange-50/30 to-red-50/30">
        {{-- Page Header --}}
        <x-index.header title="Cabang" />

        {{-- Main Content --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-8">
            <div x-data="sortableTable(@js($branches))" @sort-column.window="sortBy($event.detail)"
                class="bg-white rounded-lg sm:rounded-2xl shadow-lg sm:shadow-xl border border-gray-200 overflow-hidden">
                {{-- Card Header --}}
                <x-index.card-header title="Cabang" />

                {{-- Filter Section --}}
                <x-filter-bar searchPlaceholder="Cari nama, alamat, telepon, kode..." :selects="$selects" print="true"
                    printRouteName="reports.branches.print" />

                {{-- Desktop Table --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <x-index.table-head :columns="$columns" />
                        <tbody class="bg-white divide-y divide-gray-200">
                            <template x-for="(branch, index) in sortedRows" :key="branch.id">
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="index + 1"></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div
                                                class="w-12 h-12 rounded-full bg-gradient-to-br from-orange-400 to-red-500 flex items-center justify-center text-white font-bold shadow-md mr-4">
                                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                                    <path
                                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                </svg>
                                            </div>
                                            <div class="flex items-center gap-3">
                                                <div>
                                                    <div class="text-sm font-semibold text-gray-900" x-text="branch.name">
                                                    </div>
                                                    <div class="text-xs text-gray-500" x-text="`ID: ${branch.id}`"></div>
                                                </div>
                                                <template x-if="branch.phone">
                                                    <a :href="'https://wa.me/' + branch.phone + '?text=Halo%20' +
                                                        encodeURIComponent(branch.name)"
                                                        target="_blank"
                                                        class="inline-flex items-center justify-center w-8 h-8 bg-green-500 hover:bg-green-600 text-white rounded-full shadow-md hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5"
                                                        title="Hubungi via WhatsApp">
                                                        <x-whatsapp-logo />
                                                    </a>
                                                </template>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800"
                                            x-text="branch.code">
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                            :class="branch.type == 'branch' ? 'bg-blue-100 text-blue-800' :
                                                'bg-purple-100 text-purple-800'"
                                            x-text="branch.type == 'branch' ? 'Cabang Retail' : 'Pusat Produksi'">
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 max-w-xs truncate" :title="branch.address"
                                            x-text="branch.address">
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900" x-text="branch.phone || '-'"></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <template x-if="branch.is_active">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <div class="w-1.5 h-1.5 bg-green-400 rounded-full mr-1.5"></div>
                                                Aktif
                                            </span>
                                        </template>
                                        <template x-if="!branch.is_active">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                <div class="w-1.5 h-1.5 bg-gray-400 rounded-full mr-1.5"></div>
                                                Nonaktif
                                            </span>
                                        </template>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div x-data="{
                                            viewUrl: '/branches/' + branch.id,
                                            editUrl: '/branches/' + branch.id + '/edit',
                                            deleteUrl: '/branches/' + branch.id,
                                            toggleUrl: '/branches/' + branch.id + '/toggle-status',
                                            itemName: 'cabang ' + branch.name,
                                            isActive: branch.is_active
                                        }">
                                            <x-index.action-buttons :view="true" :edit="true" :delete="true"
                                                :toggle="true" />
                                        </div>
                                    </td>
                                </tr>
                            </template>
                            <template x-if="sortedRows.length === 0">
                                <x-index.none-data column_name="cabang" />
                            </template>
                        </tbody>
                    </table>
                </div>

                {{-- Mobile Cards --}}
                <div class="md:hidden divide-y divide-gray-200">
                    <template x-for="(branch, index) in sortedRows" :key="branch.id">
                        <div class="p-4 hover:bg-gray-50 transition-colors duration-150">
                            <div class="flex items-start space-x-3">
                                {{-- Icon --}}
                                <div class="flex-shrink-0">
                                    <div
                                        class="w-12 h-12 rounded-full bg-gradient-to-br from-orange-400 to-red-500 flex items-center justify-center text-white font-bold shadow-md">
                                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                    </div>
                                </div>

                                {{-- Content --}}
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2 mb-1">
                                                <h3 class="text-sm font-semibold text-gray-900 truncate"
                                                    x-text="branch.name"></h3>
                                                <span class="text-xs text-gray-500" x-text="`#${index + 1}`"></span>
                                                <template x-if="branch.phone">
                                                    <a :href="'https://wa.me/' + branch.phone + '?text=Halo%20' +
                                                        encodeURIComponent(branch.name)"
                                                        target="_blank"
                                                        class="inline-flex items-center justify-center w-6 h-6 bg-green-500 hover:bg-green-600 text-white rounded-full shadow-sm transition-all duration-200"
                                                        title="WhatsApp">
                                                        <x-whatsapp-logo mobile="true" />
                                                    </a>
                                                </template>
                                            </div>

                                            <div class="space-y-2">
                                                {{-- Code & Type --}}
                                                <div class="flex flex-wrap gap-2">
                                                    <span
                                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800"
                                                        x-text="branch.code">
                                                    </span>
                                                    <span
                                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                                                        :class="branch.type == 'branch' ? 'bg-blue-100 text-blue-800' :
                                                            'bg-purple-100 text-purple-800'"
                                                        x-text="branch.type == 'branch' ? 'Cabang Retail' : 'Pusat Produksi'">
                                                    </span>
                                                </div>

                                                {{-- Address --}}
                                                <div class="text-sm text-gray-600">
                                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    </svg>
                                                    <span x-text="branch.address"></span>
                                                </div>

                                                {{-- Phone --}}
                                                <template x-if="branch.phone">
                                                    <div class="text-sm text-gray-600">
                                                        <svg class="w-4 h-4 inline mr-1" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                                        </svg>
                                                        <span x-text="branch.phone"></span>
                                                    </div>
                                                </template>

                                                {{-- Status & Actions --}}
                                                <div class="flex items-center justify-between pt-2">
                                                    <template x-if="branch.is_active">
                                                        <span
                                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                            <div class="w-1.5 h-1.5 bg-green-400 rounded-full mr-1"></div>
                                                            Aktif
                                                        </span>
                                                    </template>
                                                    <template x-if="!branch.is_active">
                                                        <span
                                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                            <div class="w-1.5 h-1.5 bg-gray-400 rounded-full mr-1"></div>
                                                            Nonaktif
                                                        </span>
                                                    </template>

                                                    {{-- Actions --}}
                                                    <div x-data="{
                                                        viewUrl: '/branches/' + branch.id,
                                                        editUrl: '/branches/' + branch.id + '/edit',
                                                        deleteUrl: '/branches/' + branch.id,
                                                        toggleUrl: '/branches/' + branch.id + '/toggle-status',
                                                        itemName: 'cabang ' + branch.name,
                                                        isActive: branch.is_active
                                                    }">
                                                        <x-index.action-buttons :view="true" :edit="true"
                                                            :delete="true" :toggle="true" size="sm" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                    <template x-if="sortedRows.length === 0">
                        <x-index.none-data column_name="cabang" mobile="true" />
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
