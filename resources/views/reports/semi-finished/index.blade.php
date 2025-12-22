@extends('layouts.app')

@section('title', 'Master Bahan Setengah Jadi')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-orange-50/30 to-red-50/30">
        {{-- Page Header --}}
        <x-index.header title="Master Bahan Setengah Jadi">
        </x-index.header>

        {{-- Main Content --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-8">
            <div x-data="sortableTable(@js($semiFinishedProducts))" @sort-column.window="sortBy($event.detail)"
                class="bg-white rounded-lg sm:rounded-2xl shadow-lg sm:shadow-xl border border-gray-200 overflow-hidden">
                {{-- Card Header --}}
                <x-index.card-header title="Bahan Setengah Jadi" />

                <div class="p-4 sm:p-6">
                    {{-- Branch Info --}}
                    <div class="mb-4">
                        @if ($branchForStock)
                            <div class="rounded-md bg-blue-50 p-3 border border-blue-100 text-sm text-blue-700">
                                <i class="bi bi-building me-2"></i>
                                <strong>Cabang Aktif:</strong> {{ $branchForStock->name }} ({{ $branchForStock->code }})
                                <div class="text-xs text-blue-600 mt-1">Menampilkan stok untuk cabang ini</div>
                            </div>
                        @elseif($selectedBranch)
                            <div class="rounded-md bg-blue-50 p-3 border border-blue-100 text-sm text-blue-700">
                                <i class="bi bi-building me-2"></i>
                                <strong>Cabang Dipilih:</strong> {{ $selectedBranch->name }} ({{ $selectedBranch->code }})
                                <div class="text-xs text-blue-600 mt-1">Menampilkan stok untuk cabang ini</div>
                            </div>
                        @else
                            <div class="rounded-md bg-gray-50 p-3 border border-gray-100 text-sm text-gray-700">
                                <i class="bi bi-buildings me-2"></i>
                                <strong>Tampilan:</strong> Semua Cabang
                                <div class="text-xs text-gray-600 mt-1">Menampilkan total stok dari semua cabang</div>
                            </div>
                        @endif
                    </div>

                    {{-- Filter Section --}}
                    <x-filter-bar searchPlaceholder="Cari nama, kode, atau deskripsi bahan setengah jadi..."
                        :selects="$selects" print="true" printRouteName="reports.semi-finished.print" />

                    {{-- Desktop Table --}}
                    <div class="hidden md:block overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <x-index.table-head :columns="$columns" print="true" />
                            <tbody class="bg-white divide-y divide-gray-200">
                                <template x-for="(product, index) in sortedRows" :key="product.id">
                                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="index + 1">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <template x-if="product.code">
                                                <span
                                                    class="inline-flex items-center px-2 py-1 rounded-md bg-red-100 text-red-800 text-xs font-semibold"
                                                    x-text="product.code"></span>
                                            </template>
                                            <template x-if="!product.code">
                                                <span class="text-gray-400">-</span>
                                            </template>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <template x-if="product.image && product.image_exists">
                                                <img :src="'/storage/' + product.image" :alt="product.name"
                                                    class="rounded-md object-cover" style="width:60px; height:60px;">
                                            </template>
                                            <template x-if="!product.image || !product.image_exists">
                                                <div class="w-15 h-15 bg-gray-100 rounded-md flex items-center justify-center"
                                                    style="width:60px; height:60px;">
                                                    <i class="bi bi-image text-gray-400 text-lg"></i>
                                                </div>
                                            </template>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900" x-text="product.name"></div>
                                            <div class="text-sm text-gray-500"
                                                x-text="product.description ? product.description.substring(0, 50) + (product.description.length > 50 ? '...' : '') : ''">
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm">
                                            <template x-if="product.category">
                                                <span
                                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                                                    <i class="bi bi-tag me-1"></i>
                                                    <span x-text="product.category.name"></span>
                                                </span>
                                            </template>
                                            <template x-if="!product.category">
                                                <span>-</span>
                                            </template>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-700"
                                            x-text="product.unit ? (typeof product.unit === 'object' ? product.unit.unit_name : product.unit) : '-'">
                                        </td>
                                        <td class="px-6 py-4 text-sm">
                                            <div :class="product.is_low_stock ? 'text-red-600' : 'text-green-600'"
                                                class="font-semibold">
                                                <span
                                                    x-text="new Intl.NumberFormat('id-ID').format(product.display_stock_quantity || 0)"></span>
                                                <template x-if="product.is_low_stock">
                                                    <i class="bi bi-exclamation-triangle-fill text-yellow-500 ml-1"
                                                        title="Stok di bawah minimum"></i>
                                                </template>
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                @if ($branchForStock)
                                                    {{ $branchForStock->code }}
                                                @elseif($selectedBranch)
                                                    {{ $selectedBranch->code }}
                                                @else
                                                    Total
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-700"
                                            x-text="new Intl.NumberFormat('id-ID').format(product.minimum_stock || 0)"></td>
                                        <td class="px-6 py-4 text-sm text-gray-700"
                                            x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(product.production_cost || 0)">
                                        </td>
                                        <td class="px-6 py-4">
                                            <template x-if="product.is_active">
                                                <span
                                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <div class="w-1.5 h-1.5 bg-green-400 rounded-full mr-1"></div>
                                                    Aktif
                                                </span>
                                            </template>
                                            <template x-if="!product.is_active">
                                                <span
                                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    <div class="w-1.5 h-1.5 bg-gray-400 rounded-full mr-1"></div>
                                                    Non-Aktif
                                                </span>
                                            </template>
                                        </td>                                        
                                    </tr>
                                </template>
                                <template x-if="sortedRows.length === 0">
                                    <x-index.none-data colspan="6" />
                                </template>
                            </tbody>
                        </table>
                    </div>

                    {{-- Mobile Cards --}}
                    <div class="md:hidden divide-y divide-gray-200">
                        <template x-for="(product, index) in sortedRows" :key="product.id">
                            <div class="p-4 hover:bg-gray-50 transition-colors duration-150">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between">
                                            <h3 class="text-sm font-medium text-gray-900 truncate" x-text="product.name">
                                            </h3>
                                            <span class="text-xs text-gray-500 ml-2" x-text="product.code"></span>
                                        </div>
                                        <p class="text-sm text-gray-500 truncate mt-1" x-text="product.description"></p>
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-500">Kategori:</span>
                                        <div class="text-sm text-gray-900"
                                            x-text="product.category ? product.category.name : '-'"></div>
                                    </div>

                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-500">Stok:</span>
                                        <div :class="product.is_low_stock ? 'text-red-600' : 'text-green-600'"
                                            class="text-sm font-semibold">
                                            <span
                                                x-text="new Intl.NumberFormat('id-ID').format(product.display_stock_quantity || 0)"></span>
                                            <template x-if="product.is_low_stock">
                                                <i class="bi bi-exclamation-triangle-fill text-yellow-500 ml-1"
                                                    title="Stok di bawah minimum"></i>
                                            </template>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-500">Status:</span>
                                        <template x-if="product.is_active">
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <div class="w-1.5 h-1.5 bg-green-400 rounded-full mr-1"></div>
                                                Aktif
                                            </span>
                                        </template>
                                        <template x-if="!product.is_active">
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
    </div>

    @include('semi-finished-products.adjustment-modal')
@endsection
