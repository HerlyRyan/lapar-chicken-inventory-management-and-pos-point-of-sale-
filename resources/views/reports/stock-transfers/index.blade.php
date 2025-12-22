@extends('layouts.app')

@section('title', 'Data Stok Transfer')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-green-50/30 to-emerald-50/30">
        {{-- Page Header --}}
        <x-index.header title="Stok Transfer">
        </x-index.header>

        {{-- Main Content --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-8">
            <div x-data="sortableTable(@js($transfers))" @sort-column.window="sortBy($event.detail)"
                class="bg-white rounded-lg sm:rounded-2xl shadow-lg sm:shadow-xl border border-gray-200 overflow-hidden">
                {{-- Card Header --}}
                <x-index.card-header title="Stok Transfer" />

                {{-- Filter Section --}}
                <x-filter-bar searchPlaceholder="Cari berdasarkan notes, response notes...." :selects="$selects ?? []" print="true"
                    printRouteName="reports.stock-transfers.print" />

                {{-- Desktop Table --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <x-index.table-head :columns="$columns" print="true" />
                        <tbody class="bg-white divide-y divide-gray-200">
                            <template x-for="(transfer, index) in sortedRows" :key="transfer.id">
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="index + 1"></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
                                            x-text="transfer.item_type.toUpperCase()"></span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                                        x-text="transfer.item_type == 'finished' ? transfer.finished_product.name : transfer.finished_product.name">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                                        x-text="transfer.from_branch.name ?? '-'"></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                                        x-text="transfer.to_branch.name ?? '-'"></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                                        x-text="Number(transfer.quantity)"></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <template x-if="transfer.status === 'sent'">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                Dikirim
                                            </span>
                                        </template>
                                        <template x-if="transfer.status === 'accepted'">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Diterima
                                            </span>
                                        </template>
                                        <template x-if="transfer.status === 'rejected'">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                Ditolak
                                            </span>
                                        </template>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                                        x-text="new Date(transfer.created_at).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' })">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                                        x-text="transfer.handled_at ? new Date(transfer.handled_at).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' }) : '-'">
                                    </td>
                                </tr>
                            </template>
                            <template x-if="sortedRows.length === 0">
                                <x-index.none-data colspan="10" />
                            </template>
                        </tbody>
                    </table>
                </div>

                {{-- Mobile Cards --}}
                <div class="md:hidden divide-y divide-gray-200">
                    <template x-for="(transfer, index) in sortedRows" :key="transfer.id">
                        <div class="p-4 hover:bg-gray-50 transition-colors duration-150">
                            {{-- Transfer Header --}}
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-sm font-medium text-gray-900 truncate"
                                            x-text="'ID: ' + transfer.id"></h3>
                                    </div>
                                    <p class="text-sm text-gray-500 truncate mt-1" x-text="transfer.notes ?? '-'"></p>
                                </div>
                            </div>

                            {{-- Transfer Details --}}
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500">Tipe Item:</span>
                                    <span class="text-xs font-medium bg-blue-100 text-blue-800 px-2 py-1 rounded-full"
                                        x-text="transfer.item_type"></span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500">Item:</span>
                                    <span class="text-sm text-gray-900" x-text="transfer.item_type == 'finished' ? transfer.finished_product.name : transfer.finished_product.name"></span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500">Dari Cabang:</span>
                                    <span class="text-sm text-gray-900" x-text="transfer.from_branch.name ?? '-'"></span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500">Ke Cabang:</span>
                                    <span class="text-sm text-gray-900" x-text="transfer.to_branch.name ?? '-'"></span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500">Jumlah:</span>
                                    <span class="text-sm text-gray-900" x-text="transfer.quantity"></span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500">Status:</span>
                                    <template x-if="transfer.status === 'sent'">
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Dikirim</span>
                                    </template>
                                    <template x-if="transfer.status === 'accepted'">
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Diterima</span>
                                    </template>
                                    <template x-if="transfer.status === 'rejected'">
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">Ditolak</span>
                                    </template>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500">Tanggal Pengiriman:</span>
                                    <span class="text-sm text-gray-900" x-text="transfer.created_at"></span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500">Tanggal Penanganan:</span>
                                    <span class="text-sm text-gray-900" x-text="transfer.handled_at ?? '-'"></span>
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
