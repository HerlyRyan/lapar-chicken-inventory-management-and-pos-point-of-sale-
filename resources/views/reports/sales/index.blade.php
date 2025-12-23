@extends('layouts.app')

@section('title', 'Daftar Penjualan')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-orange-50/30 to-red-50/30">
        {{-- Page Header --}}
        <x-index.header title="Penjualan" />

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-8">
            <div x-data="sortableTable(
                @js($sales->items()),
                @js($totalRevenue ?? null)
            )" @sort-column.window="sortBy($event.detail)"
                class="bg-white rounded-lg sm:rounded-2xl shadow-lg sm:shadow-xl border border-gray-200 overflow-hidden">

                {{-- Card Header --}}
                <x-index.card-header title="Transaksi Penjualan" />

                {{-- Filter Bar (reuse component, fallback to inline selects) --}}
                <x-filter-bar searchPlaceholder="Cari nomor transaksi atau pelanggan..." :selects="$selects ?? []" date="true"
                    print="true" printRouteName="reports.sales.print" />

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 px-6 py-4">
                    <div class="bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-xl shadow-md p-5">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm opacity-90">Total Pendapatan</p>
                                <p class="text-2xl font-bold mt-1">
                                    <span x-text="new Intl.NumberFormat('id-ID').format(totalRevenue)"></span>
                                </p>
                            </div>
                            <div class="bg-white/20 p-3 rounded-full">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2
                                                           3 .895 3 2-1.343 2-3 2m0-8V4m0 12v4" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="hidden md:block overflow-x-auto px-6 pb-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <x-index.table-head :columns="$columns" print="true" />
                        <tbody class="bg-white divide-y divide-gray-200">
                            <template x-for="(sale, index) in sortedRows" :key="sale.id">
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="index + 1"></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
                                        x-text="sale.sale_number"></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
                                        x-text="new Date(sale.created_at).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' })">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                                        x-text="(sale.branch && sale.branch.name) ? sale.branch.name : 'N/A'"></td>
                                    <td class="px-6 py-4 text-sm text-gray-900" x-text="sale.customer_name || 'Umum'"></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Rp <span
                                            x-text="Number(sale.final_amount).toLocaleString('id-ID')"></span></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <template x-if="sale.payment_method === 'cash'">
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Tunai</span>
                                        </template>
                                        <template x-if="sale.payment_method === 'qris'">
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">QRIS</span>
                                        </template>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <template x-if="sale.status === 'completed'">
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Selesai</span>
                                        </template>
                                        <template x-if="sale.status === 'cancelled'">
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">Dibatalkan</span>
                                        </template>
                                        <template x-if="sale.status !== 'completed' && sale.status !== 'cancelled'">
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800"
                                                x-text="sale.status"></span>
                                        </template>
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
                <div class="md:hidden divide-y divide-gray-200 px-4 pb-6">
                    <template x-for="(sale, index) in sortedRows" :key="sale.id">
                        <div class="p-4 hover:bg-gray-50 transition-colors duration-150">
                            <div class="flex items-start justify-between mb-2">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-sm font-medium text-gray-900 truncate" x-text="sale.sale_number">
                                        </h3>
                                        <span class="text-xs text-gray-500 ml-2" x-text="sale.created_at"></span>
                                    </div>
                                    <p class="text-sm text-gray-500 mt-1"
                                        x-text="(sale.branch && sale.branch.name) ? sale.branch.name : 'N/A'"></p>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500">Pelanggan</span>
                                    <span class="text-sm text-gray-900" x-text="sale.customer_name || 'Umum'"></span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500">Total</span>
                                    <span class="text-sm text-gray-900">Rp <span
                                            x-text="Number(sale.final_amount).toLocaleString('id-ID')"></span></span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500">Status</span>
                                    <template x-if="sale.status === 'completed'">
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Selesai</span>
                                    </template>
                                    <template x-if="sale.status === 'cancelled'">
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">Dibatalkan</span>
                                    </template>
                                    <template x-if="sale.status !== 'completed' && sale.status !== 'cancelled'">
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800"
                                            x-text="sale.status"></span>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Pagination --}}
                <template x-if="sortedRows.length !== 0">
                    <div class="px-6 pb-6">
                        {{ $sales->links('vendor.pagination.tailwind') }}
                    </div>
                </template>
            </div>
        </div>
    </div>
    {{-- Cancel Modal --}}
    @include('sales.cancel-modal')
@endsection
