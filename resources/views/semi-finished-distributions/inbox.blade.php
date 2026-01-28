@extends('layouts.app')

@section('title', 'Kotak Masuk Distribusi Cabang')

@section('content')

    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-orange-50/30 to-red-50/30">
        {{-- Page Header --}}
        <x-index.header title="Kotak Masuk Distribusi Cabang"
            subtitle="Menampilkan distribusi berstatus Dikirim ke cabang Anda" />

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-8">
            <div x-data="sortableTable(@js($distributions->items()))" @sort-column.window="sortBy($event.detail)"
                class="bg-white rounded-lg sm:rounded-2xl shadow-lg sm:shadow-xl border border-gray-200 overflow-hidden">
                {{-- Card Header --}}
                <x-index.card-header title="Distribusi" />

                {{-- Filter Section --}}
                <x-filter-bar searchPlaceholder="Cari berdasarkan kode distribusi..." :selects="$selects" />

                {{-- Desktop Table --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <x-index.table-head :columns="$columns" />
                        <tbody class="bg-white divide-y divide-gray-200">
                            <template x-for="(dist, index) in sortedRows" :key="dist.id">
                                <tr class="hover:bg-gray-50 transition-colors duration-150"
                                    :class="dist.status === 'pending' ? 'bg-yellow-50' : (dist.status === 'accepted' ?
                                        'bg-green-50' : 'bg-red-50')">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="index + 1"></td>

                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        <div class="flex items-center gap-2">
                                            <div>
                                                <div class="font-semibold" x-text="dist.distribution_code"></div>
                                                <template x-if="dist.is_pending">
                                                    <span
                                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 mt-1">BARU</span>
                                                </template>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900"
                                            x-text="dist.target_branch?.name ?? '-'">
                                        </div>
                                        <div class="text-sm text-gray-500 truncate max-w-xs"
                                            :title="dist.target_branch?.address" x-text="dist.target_branch?.address ?? ''">
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        <div class="text-sm text-gray-600" x-text="dist.semi_finished_product.name"></div>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <template x-if="dist.status === 'sent'">
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">Terkirim</span>
                                        </template>
                                        <template x-if="dist.status === 'accepted'">
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">Diterima</span>
                                        </template>
                                        <template x-if="dist.status === 'rejected'">
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">Ditolak</span>
                                        </template>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <div class="font-medium text-gray-900"
                                            x-text="dist.created_at ? (new Date(dist.created_at)).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' }) : '-'">
                                        </div>
                                        <div class="text-xs text-gray-400"
                                            x-text="dist.created_at ? (new Date(dist.created_at)).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) : '-'">
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <template x-if="dist.status === 'accepted' && dist.handled_by">
                                            <div>
                                                <div class="text-sm text-green-700 font-semibold">Diterima oleh</div>
                                                <div class="text-xs text-gray-500"
                                                    x-text="dist.handled_by && dist.handled_by.name ? dist.handled_by.name : '-'">
                                                </div>
                                                <div class="text-xs text-gray-400"
                                                    x-text="dist.handled_at ? (new Date(dist.handled_at)).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' }) : '-'">
                                                </div>
                                            </div>
                                        </template>
                                        <template x-if="dist.status === 'rejected' && dist.handled_by">
                                            <div>
                                                <div class="text-sm text-red-700 font-semibold">Ditolak oleh</div>
                                                <div class="text-xs text-gray-500" x-text="dist.handled_by.name"></div>
                                                <div class="text-xs text-gray-400"
                                                    x-text="dist.handled_at ? (new Date(dist.handled_at)).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' }) : '-'">
                                                </div>
                                            </div>
                                        </template>
                                        <template x-if="dist.status === 'sent'">
                                            <div>
                                                <div class="text-sm text-gray-500"><i class="bi bi-clock me-1"></i> Menunggu
                                                    konfirmasi</div>
                                            </div>
                                        </template>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <div x-data="{
                                            viewUrl: '/semi-finished-distributions/' + dist.id,
                                            itemName: 'distribusi ' + dist.distribution_code,
                                            isPending: dist.status === 'pending',
                                            isSent: dist.status === 'sent'
                                        }">
                                            <div class="flex items-center gap-2 sm:gap-3">
                                                {{-- Confirm & Reject (only when status is sent) --}}
                                                <template x-if="isSent" x-data="distributionModals()">
                                                    <div class="flex items-center gap-2 sm:gap-3">
                                                        <button type="button"
                                                            x-on:click="$dispatch('open-accept-modal', { id: dist.id })"
                                                            class="group relative inline-flex items-center justify-center w-8 h-8 sm:w-9 sm:h-9
                                                                bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700
                                                                text-white rounded-lg shadow-sm hover:shadow-md transition-all duration-200"
                                                            title="Terima">
                                                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M5 13l4 4L19 7" />
                                                            </svg>
                                                        </button>

                                                        <button type="button"
                                                            x-on:click="$dispatch('open-reject-modal', { id: dist.id })"
                                                            class="group relative inline-flex items-center justify-center w-8 h-8 sm:w-9 sm:h-9
                                                                bg-gradient-to-r from-red-500 to-rose-600 hover:from-red-600 hover:to-rose-700
                                                                text-white rounded-lg shadow-sm hover:shadow-md transition-all duration-200"
                                                            title="Tolak">
                                                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                            </svg>
                                                        </button>

                                                        <!-- === Modal Area === -->
                                                        <template x-if="openModal === 'accept'">
                                                            @include('semi-finished-distributions.partials.accept-modal')
                                                        </template>

                                                        <template x-if="openModal === 'reject'">
                                                            @include('semi-finished-distributions.partials.reject-modal')
                                                        </template>
                                                    </div>
                                                </template>

                                                {{-- Default action buttons --}}
                                                <x-index.action-buttons :view="true" />
                                            </div>
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
                    <template x-for="(dist, index) in sortedRows" :key="dist.id">
                        <div class="p-4 hover:bg-gray-50 transition-colors duration-150"
                            :class="dist.status === 'pending' ? 'bg-yellow-50' : (dist.status === 'accepted' ? 'bg-green-50' :
                                'bg-red-50')">
                            <div class="flex items-start justify-between mb-3">
                                <div class="min-w-0">
                                    <div class="flex items-center justify-between gap-2">
                                        <div class="flex items-center gap-3">
                                            <div class="text-sm text-gray-500" x-text="index + 1"></div>
                                            <h3 class="text-sm font-medium text-gray-900 truncate"
                                                x-text="dist.distribution_code"></h3>
                                            <template x-if="dist.is_pending">
                                                <span
                                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 mt-1">BARU</span>
                                            </template>
                                        </div>

                                        <span class="text-xs text-gray-500"
                                            x-text="dist.target_branch?.name ?? '-'"></span>
                                    </div>

                                    <p class="text-sm text-gray-500 truncate mt-1" :title="dist.target_branch?.address"
                                        x-text="dist.target_branch?.address ?? ''"></p>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500">Produk:</span>
                                    <div class="text-sm text-gray-900" x-text="dist.semi_finished_product?.name ?? '-'">
                                    </div>
                                </div>

                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500">Status:</span>
                                    <div>
                                        <template x-if="dist.status === 'sent'">
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Terkirim</span>
                                        </template>
                                        <template x-if="dist.status === 'accepted'">
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Diterima</span>
                                        </template>
                                        <template x-if="dist.status === 'rejected'">
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">Ditolak</span>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 pt-3 border-t border-gray-200 space-y-2">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900"
                                            x-text="dist.created_at ? (new Date(dist.created_at)).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' }) : '-'">
                                        </div>
                                        <div class="text-xs text-gray-400"
                                            x-text="dist.created_at ? (new Date(dist.created_at)).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) : '-'">
                                        </div>
                                    </div>

                                    <div class="text-sm text-gray-500">
                                        <template x-if="dist.status === 'accepted' && dist.handled_by">
                                            <div>
                                                <div class="text-sm text-green-700 font-semibold">Diterima oleh</div>
                                                <div class="text-xs text-gray-500"
                                                    x-text="dist.handled_by && dist.handled_by.name ? dist.handled_by.name : '-'">
                                                </div>
                                                <div class="text-xs text-gray-400"
                                                    x-text="dist.handled_at ? (new Date(dist.handled_at)).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' }) : '-'">
                                                </div>
                                            </div>
                                        </template>

                                        <template x-if="dist.status === 'rejected' && dist.handled_by">
                                            <div>
                                                <div class="text-sm text-red-700 font-semibold">Ditolak oleh</div>
                                                <div class="text-xs text-gray-500"
                                                    x-text="dist.handled_by && dist.handled_by.name ? dist.handled_by.name : '-'">
                                                </div>
                                                <div class="text-xs text-gray-400"
                                                    x-text="dist.handled_at ? (new Date(dist.handled_at)).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' }) : '-'">
                                                </div>
                                            </div>
                                        </template>

                                        <template x-if="dist.status === 'sent'">
                                            <div class="text-sm text-gray-500"><i class="bi bi-clock me-1"></i> Menunggu
                                                konfirmasi</div>
                                        </template>
                                    </div>
                                </div>

                                <div x-data="{
                                    viewUrl: '/semi-finished-distributions/' + dist.id,
                                    editUrl: '/semi-finished-distributions/' + dist.id + '/edit',
                                    deleteUrl: '/semi-finished-distributions/' + dist.id,
                                    itemName: 'distribusi ' + dist.distribution_code,
                                    isPending: dist.status === 'pending',
                                    isSent: dist.status === 'sent'
                                }" class="w-full">
                                    <div class="flex items-center justify-between gap-2 sm:gap-3">
                                        {{-- Left: Confirm & Reject (only when status is sent) --}}
                                        <div class="flex items-center gap-2">
                                            <template x-if="isSent" x-data="distributionModals()">
                                                <div class="flex items-center gap-2">
                                                    <button type="button"
                                                        x-on:click="$dispatch('open-accept-modal', { id: dist.id })"
                                                        class="group relative inline-flex items-center justify-center w-9 h-9
                                                            bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700
                                                            text-white rounded-lg shadow-sm hover:shadow-md transition-all duration-200"
                                                        title="Terima">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                    </button>

                                                    <button type="button"
                                                        x-on:click="$dispatch('open-reject-modal', { id: dist.id })"
                                                        class="group relative inline-flex items-center justify-center w-9 h-9
                                                            bg-gradient-to-r from-red-500 to-rose-600 hover:from-red-600 hover:to-rose-700
                                                            text-white rounded-lg shadow-sm hover:shadow-md transition-all duration-200"
                                                        title="Tolak">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                    </button>

                                                    <!-- Modals (kept here so mobile can open them too) -->
                                                    <template x-if="openModal === 'accept'">
                                                        @include('semi-finished-distributions.partials.accept-modal')
                                                    </template>

                                                    <template x-if="openModal === 'reject'">
                                                        @include('semi-finished-distributions.partials.reject-modal')
                                                    </template>
                                                </div>
                                            </template>
                                        </div>

                                        {{-- Right: Default action buttons (stack / wrap on very small screens) --}}
                                        <div class="flex items-center gap-2 flex-shrink-0">
                                            <x-index.action-buttons :view="true" :edit="true" :delete="true" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Pagination --}}
                <template x-if="sortedRows.length !== 0">
                    <div class="p-4">
                        <div class="pagination-wrapper">
                            {{-- pass original paginator --}}
                            {{ $distributions->appends(request()->query())->links('vendor.pagination.tailwind') }}
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
@endsection
