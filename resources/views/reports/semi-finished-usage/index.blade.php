@extends('layouts.app')

@php
    use Illuminate\Support\Facades\Storage;
@endphp

@section('title', 'Laporan Penggunaan Semi Finished')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-orange-50/30 to-red-50/30">
        {{-- Page Header --}}
        <x-index.header title="Laporan Penggunaan Semi Finished" />

        {{-- Main Content --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-8">
            <div x-data="sortableTable(@js($usages))" @sort-column.window="sortBy($event.detail)"
                class="bg-white rounded-lg sm:rounded-2xl shadow-lg sm:shadow-xl border border-gray-200 overflow-hidden">
                {{-- Card Header --}}
                <x-index.card-header title="Laporan Penggunaan Semi Finished" />

                {{-- Filter Section --}}
                <x-filter-bar searchPlaceholder="Cari tanggal, cabang, produk..." :selects="$selects" print="true"
                    printRouteName="reports.semi-finished-usage.print" />

                {{-- Desktop Table --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <x-index.table-head :columns="$columns" print="true" />
                        <tbody class="bg-white divide-y divide-gray-200">
                            <template x-for="(usage, index) in sortedRows"
                                :key="`${usage.usage_date}-${usage.branch_id}-${usage.product_name}`">
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="index + 1"></td>
                                    <td class="px-6 py-4 whitespace-nowrap"
                                        x-text="usage.usage_date ? new Date(usage.usage_date).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' }) : '-'">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap" x-text="usage.branch_name"></td>
                                    <td class="px-6 py-4 whitespace-nowrap" x-text="usage.product_name"></td>
                                    <td class="px-6 py-4 whitespace-nowrap" x-text="usage.total_quantity"></td>
                                </tr>
                            </template>
                            <template x-if="sortedRows.length === 0">
                                <x-index.none-data column_name="penggunaan semi finished" />
                            </template>
                        </tbody>
                    </table>
                </div>

                {{-- Mobile Cards --}}
                <div class="md:hidden divide-y divide-gray-200">
                    <template x-for="(usage, index) in sortedRows"
                        :key="`${usage.usage_date}-${usage.branch_id}-${usage.product_name}`">
                        <div class="p-4 hover:bg-gray-50 transition-colors duration-150">
                            <div class="flex items-start space-x-3">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2 mb-1">
                                                <h3 class="text-sm font-semibold text-gray-900 truncate"
                                                    x-text="usage.product_name"></h3>
                                                <span class="text-xs text-gray-500" x-text="`#${index + 1}`"></span>
                                            </div>

                                            <div class="space-y-2">
                                                <div class="text-sm text-gray-600"
                                                    x-text="usage.usage_date ? new Date(usage.usage_date).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' }) : '-'">
                                                </div>
                                                <div class="text-sm text-gray-600" x-text="usage.branch_name"></div>
                                                <div class="text-sm text-gray-600">
                                                    <span class="font-medium">Qty:</span>
                                                    <span x-text="usage.total_quantity"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                    <template x-if="sortedRows.length === 0">
                        <x-index.none-data column_name="penggunaan semi finished" mobile="true" />
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
