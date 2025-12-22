@extends('layouts.app')

@section('title', 'Data Produk Terlaris')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-green-50/30 to-emerald-50/30">
        {{-- Page Header --}}
        <x-index.header title="Produk Terlaris">
        </x-index.header>

        {{-- Main Content --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-8">
            <div x-data="sortableTable(@js($bestSelling))" @sort-column.window="sortBy($event.detail)"
                class="bg-white rounded-lg sm:rounded-2xl shadow-lg sm:shadow-xl border border-gray-200 overflow-hidden">
                {{-- Card Header --}}
                <x-index.card-header title="Produk Terlaris" />

                {{-- Filter Section --}}
                <x-filter-bar searchPlaceholder="Cari berdasarkan nama produk...." :selects="$selects ?? []" print="true"
                    printRouteName="reports.best-selling.print" date="true" />

                {{-- Desktop Table --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <x-index.table-head :columns="$columns" print="true" />
                        <tbody class="bg-white divide-y divide-gray-200">
                            <template x-for="(item, index) in sortedRows" :key="item.item_id">
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="index + 1"></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" x-text="item.branch_name">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" x-text="item.item_name">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                                        x-text="item.category_name">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                                        x-text="Number(item.quantity)"></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                                        x-text="new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(Number(item.subtotal))">
                                    </td>
                                </tr>
                            </template>
                            <template x-if="sortedRows.length === 0">
                                <x-index.none-data colspan="4" />
                            </template>
                        </tbody>
                    </table>
                </div>

                {{-- Mobile Cards --}}
                <div class="md:hidden divide-y divide-gray-200">
                    <template x-for="(item, index) in sortedRows" :key="item.item_id">
                        <div class="p-4 hover:bg-gray-50 transition-colors duration-150">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-gray-500 truncate mt-1" x-text="item.item_name"></p>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500">Jumlah:</span>
                                    <span class="text-sm text-gray-900" x-text="item.quantity"></span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500">Total Pendapatan:</span>
                                    <span class="text-sm text-gray-900" x-text="item.subtotal"></span>
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
