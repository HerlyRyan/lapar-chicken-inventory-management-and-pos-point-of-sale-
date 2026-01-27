@extends('layouts.app')

@section('title', 'Daftar Permintaan Penggunaan Bahan')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-orange-50/30 to-red-50/30">
        {{-- Page Header --}}
        <x-index.header title="Permintaan Penggunaan Bahan" subtitle="Kelola permintaan penggunaan bahan"
            addRoute="{{ isset($currentBranchId) && $currentBranchId ? route('semi-finished-usage-requests.create', ['branch_id' => $currentBranchId]) : route('semi-finished-usage-requests.create') }}"
            addText="Buat Permintaan Baru" />

        {{-- Main Content --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-8">
            <div x-data="sortableTable(@js($requests->items()))" @sort-column.window="sortBy($event.detail)"
                class="bg-white rounded-lg sm:rounded-2xl shadow-lg sm:shadow-xl border border-gray-200 overflow-hidden">
                {{-- Card Header --}}
                <x-index.card-header title="Permintaan" />

                {{-- Filter Section --}}
                <x-filter-bar searchPlaceholder="Cari nomor permintaan..." :selects="$selects ?? []" />

                {{-- Desktop Table --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <x-index.table-head :columns="$columns" />
                        <tbody class="bg-white divide-y divide-gray-200">
                            <template x-for="(req, index) in sortedRows" :key="req.id">
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="index + 1"></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
                                        x-text="req.request_number"></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                                        x-text="req.requesting_branch.name"></td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        <div class="max-w-xs truncate" :title="req.purpose" x-text="req.purpose"></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"
                                        x-text="(new Date(req.requested_date)).toLocaleDateString()"></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"
                                        x-text="req.required_date ? (new Date(req.required_date)).toLocaleDateString() : '-'">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span
                                            :class="{
                                                'inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800': req
                                                    .status === 'pending',
                                                'inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800': req
                                                    .status === 'completed',
                                                'inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800': req
                                                    .status === 'rejected',
                                                'inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800': [
                                                    'pending', 'completed', 'rejected'
                                                ].indexOf(req.status) === -1
                                            }"
                                            x-text="({ pending: 'Menunggu Persetujuan', completed: 'Selesai', rejected: 'Ditolak' })[req.status] || 'Tidak Diketahui'">
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <div x-data="{
                                            viewUrl: '/semi-finished-usage-requests/' + req.id,
                                            editUrl: '/semi-finished-usage-requests/' + req.id + '/edit',
                                            deleteUrl: '/semi-finished-usage-requests/' + req.id,
                                            itemName: 'Permintaan ' + req.request_number,
                                        
                                        }">
                                            <div class="flex items-center gap-2 sm:gap-3">
                                                {{-- Confirm & Reject (only when status is pending) --}}
                                                <template x-if="req.status === 'pending'" x-data="materialModals()">
                                                    <div class="flex items-center gap-2 sm:gap-3">
                                                        <button type="button"
                                                            @click="openAccept(req.id, req.request_number ?? req.number ?? req.id)"
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
                                                            @click="openReject(req.id, req.request_number ?? req.number ?? req.id)"
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
                                                            @include('material-usage-requests.accept-modal')
                                                        </template>

                                                        <template x-if="openModal === 'reject'">
                                                            @include('material-usage-requests.reject-modal')
                                                        </template>
                                                    </div>
                                                </template>

                                                {{-- Default action buttons --}}
                                                <x-index.action-buttons :view="true" :edit="true"
                                                    :delete="true" />
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
                    <template x-for="(req, index) in sortedRows" :key="req.id">
                        <div class="p-4 hover:bg-gray-50 transition-colors duration-150">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-sm font-medium text-gray-900 truncate" x-text="req.request_number">
                                        </h3>
                                        <span class="text-xs text-gray-500 ml-2" x-text="req.requesting_branch_name"></span>
                                    </div>
                                    <p class="text-sm text-gray-500 truncate mt-1" x-text="req.purpose"></p>
                                </div>
                            </div>

                            <div class="space-y-2 text-sm text-gray-700">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500">Tgl Permintaan:</span>
                                    <span x-text="(new Date(req.requested_date)).toLocaleDateString()"></span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500">Tgl Dibutuhkan:</span>
                                    <span
                                        x-text="req.required_date ? (new Date(req.required_date)).toLocaleDateString() : '-'"></span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500">Status:</span>
                                    <div x-html="req.status_badge"></div>
                                </div>
                            </div>

                            <div class="mt-4 pt-3 border-t border-gray-200">
                                <div x-data="{
                                    viewUrl: '/semi-finished-usage-requests/' + req.id,
                                    editUrl: '/semi-finished-usage-requests/' + req.id + '/edit',
                                    deleteUrl: '/semi-finished-usage-requests/' + req.id,
                                    itemName: 'Permintaan ' + req.request_number,
                                
                                }">
                                    <div class="flex items-center gap-2 sm:gap-3">
                                        {{-- Confirm & Reject (only when status is pending) --}}
                                        <template x-if="req.status === 'pending'" x-data="materialModals()">
                                            <div class="flex items-center gap-2 sm:gap-3">
                                                <button type="button"
                                                    @click="openAccept(req.id, req.request_number ?? req.number ?? req.id)"
                                                    class="group relative inline-flex items-center justify-center w-8 h-8 sm:w-9 sm:h-9
                                                            bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700
                                                            text-white rounded-lg shadow-sm hover:shadow-md transition-all duration-200"
                                                    title="Terima">
                                                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                </button>

                                                <button type="button"
                                                    @click="openReject(req.id, req.request_number ?? req.number ?? req.id)"
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
                                                    @include('material-usage-requests.accept-modal')
                                                </template>

                                                <template x-if="openModal === 'reject'">
                                                    @include('material-usage-requests.reject-modal')
                                                </template>
                                            </div>
                                        </template>

                                        {{-- Default action buttons --}}
                                        <x-index.action-buttons :view="true" :edit="true" :delete="true" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Pagination --}}
                <template x-if="sortedRows.length !== 0">
                    <div class="px-4 py-3 sm:px-6">
                        {{ $requests->links('vendor.pagination.tailwind') }}
                    </div>
                </template>
            </div>
        </div>
    </div>
@endsection
