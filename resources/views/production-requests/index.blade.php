@extends('layouts.app')

@section('title', 'Pengajuan Produksi')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-green-50/30 to-blue-50/30">
        {{-- Page Header --}}
        @if (auth()->user()->hasRole('Manajer'))
            <x-index.header title="Pengajuan Produksi" subtitle="Kelola pengajuan penggunaan bahan mentah untuk produksi" />
        @else
            <x-index.header title="Pengajuan Produksi" subtitle="Kelola pengajuan penggunaan bahan mentah untuk produksi"
                addRoute="{{ route('production-requests.create') }}" addText="Buat Pengajuan Baru" />
        @endif

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-8">
            <div x-data="sortableTable(@js($productionRequests->items()))" @sort-column.window="sortBy($event.detail)"
                class="bg-white rounded-lg sm:rounded-2xl shadow-lg border border-gray-200 overflow-hidden">

                {{-- Card Header --}}
                <x-index.card-header title="Pengajuan Produksi" />

                {{-- Filter Section --}}
                <x-filter-bar searchPlaceholder="Cari nama, kode, atau alamat supplier..." :selects="$selects" />

                {{-- Desktop Table --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <x-index.table-head :columns="$columns" />
                        <tbody class="bg-white divide-y divide-gray-200">
                            <template x-for="(pr, index) in rows" :key="pr.id">
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="index + 1"></td>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900" x-text="pr.request_code"></td>
                                    <td class="px-6 py-4 text-sm text-gray-700">
                                        <div class="font-semibold" x-text="pr.purpose"></div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700" x-text="pr.requested_by.name || '-'"></td>
                                    <td class="px-6 py-4 text-sm text-gray-800">Rp. <span
                                            x-text="new Intl.NumberFormat('id-ID').format(pr.total_raw_material_cost || 0)"></span>
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                            :class="{
                                                'bg-yellow-100 text-yellow-800': pr.status === 'pending',
                                                'bg-green-100 text-green-800': pr.status === 'approved',
                                                'bg-red-100 text-red-800': pr.status === 'rejected',
                                                'bg-indigo-100 text-indigo-800': pr.status === 'in_progress',
                                                'bg-blue-100 text-blue-800': pr.status === 'completed',
                                                'bg-gray-100 text-gray-800': pr.status === 'cancelled'
                                            }"
                                            :title="{
                                                pending: 'Menunggu Persetujuan',
                                                approved: 'Disetujui',
                                                rejected: 'Ditolak',
                                                in_progress: 'Sedang Diproduksi',
                                                completed: 'Selesai',
                                                cancelled: 'Dibatalkan'
                                            } [pr.status] ?? pr.status">
                                            <span
                                                x-text="{
                                                pending: 'Menunggu Persetujuan',
                                                approved: 'Disetujui',
                                                rejected: 'Ditolak',
                                                in_progress: 'Sedang Diproduksi',
                                                completed: 'Selesai',
                                                cancelled: 'Dibatalkan'
                                            }[pr.status] ?? pr.status"></span>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500"
                                        x-text="new Date(pr.created_at).toLocaleDateString('id-ID')"></td>
                                    <td class="px-6 py-4 text-right text-sm">
                                        <div x-data="{
                                            viewUrl: '/production-requests/' + pr.id,
                                            editUrl: '/production-requests/' + pr.id + '/edit',
                                            deleteUrl: '/production-requests/' + pr.id,
                                            toggleUrl: '/production-requests/' + pr.id + '/toggle-status',
                                            itemName: 'pengajuan ' + pr.request_code,
                                            isActive: pr.status === 'approved'
                                        }">
                                            <div class="flex items-center gap-2 sm:gap-3">
                                                @if (auth()->user()->hasRole('Manajer') || auth()->user()->hasRole('Super Admin'))
                                                    <div x-data="approvalHandler()">
                                                        <template x-if="pr.status === 'pending'">
                                                            <div class="flex items-center gap-2">
                                                                <button type="button" @click="openModal(pr.id, 'approve')"
                                                                    class="group relative inline-flex items-center justify-center w-9 h-9
                                                    bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700
                                                    text-white rounded-lg shadow-sm hover:shadow-md transition-all duration-200"
                                                                    title="Terima">
                                                                    <svg class="w-4 h-4" fill="none"
                                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                            stroke-width="2" d="M5 13l4 4L19 7" />
                                                                    </svg>
                                                                </button>

                                                                <button type="button" @click="openModal(pr.id, 'reject')"
                                                                    class="group relative inline-flex items-center justify-center w-9 h-9
                                                    bg-gradient-to-r from-red-500 to-rose-600 hover:from-red-600 hover:to-rose-700
                                                    text-white rounded-lg shadow-sm hover:shadow-md transition-all duration-200"
                                                                    title="Tolak">
                                                                    <svg class="w-4 h-4" fill="none"
                                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                            stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                                    </svg>
                                                                </button>
                                                            </div>

                                                        </template>

                                                        @include('production-requests.approval-rejected-modal')
                                                    </div>
                                                @endif

                                                <x-index.action-buttons :view="true" :edit="true"
                                                    :delete="true" :toggle="false" />
                                            </div>
                                        </div>

                                    </td>
                                </tr>
                            </template>

                            <template x-if="rows.length === 0">
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                        <div class="text-2xl">Belum Ada Pengajuan Produksi</div>
                                        <p class="mt-2">Mulai dengan membuat pengajuan produksi baru.</p>
                                        <a href="{{ route('production-requests.create') }}"
                                            class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md">Buat
                                            Pengajuan Pertama</a>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                {{-- Mobile Cards --}}
                <div class="md:hidden">
                    <template x-for="(pr, index) in rows" :key="pr.id">
                        <div class="p-4 bg-white hover:bg-gray-50 rounded-lg shadow-sm mb-4">
                            <!-- Card Header -->
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3 min-w-0">
                                    <span class="text-sm font-semibold text-gray-900"
                                        x-text="index + 1 + '. ' + pr.request_code"></span>

                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                                        :class="{
                                            'bg-yellow-100 text-yellow-800': pr.status === 'pending',
                                            'bg-green-100 text-green-800': pr.status === 'approved',
                                            'bg-red-100 text-red-800': pr.status === 'rejected',
                                            'bg-indigo-100 text-indigo-800': pr.status === 'in_progress',
                                            'bg-blue-100 text-blue-800': pr.status === 'completed',
                                            'bg-gray-100 text-gray-800': pr.status === 'cancelled'
                                        }"
                                        :title="{
                                            pending: 'Menunggu Persetujuan',
                                            approved: 'Disetujui',
                                            rejected: 'Ditolak',
                                            in_progress: 'Sedang Diproduksi',
                                            completed: 'Selesai',
                                            cancelled: 'Dibatalkan'
                                        } [pr.status] ?? pr.status">
                                        <span
                                            x-text="{
                                                pending: 'Menunggu Persetujuan',
                                                approved: 'Disetujui',
                                                rejected: 'Ditolak',
                                                in_progress: 'Sedang Diproduksi',
                                                completed: 'Selesai',
                                                cancelled: 'Dibatalkan'
                                            }[pr.status] ?? pr.status"></span>
                                    </span>
                                </div>

                                <div class="text-sm text-gray-500">
                                    <div x-text="new Date(pr.created_at).toLocaleDateString('id-ID')"></div>
                                </div>
                            </div>

                            <!-- Card Body -->
                            <div class="mt-3">
                                <p class="text-sm text-gray-700" x-text="pr.purpose"></p>

                                <div class="text-xs text-gray-500 mt-2 flex flex-col sm:flex-row sm:items-center sm:gap-4">
                                    <span x-text="pr.requested_by?.name || '-'"></span>
                                    <span class="whitespace-nowrap">•</span>
                                    <span class="font-medium text-gray-800">Rp <span
                                            x-text="new Intl.NumberFormat('id-ID').format(pr.total_raw_material_cost || 0)"></span></span>
                                </div>
                            </div>

                            <!-- Card Footer: actions -->
                            <div class="mt-4 pt-3 border-t border-gray-100">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                    <div class="flex items-center justify-end">
                                        <div x-data="{
                                            viewUrl: '/production-requests/' + pr.id,
                                            editUrl: '/production-requests/' + pr.id + '/edit',
                                            deleteUrl: '/production-requests/' + pr.id,
                                            toggleUrl: '/production-requests/' + pr.id + '/toggle-status',
                                            itemName: 'pengajuan ' + pr.request_code,
                                            isActive: pr.status === 'approved'
                                        }" class="flex items-center gap-2">
                                            @if (auth()->user()->hasRole('Manajer') || auth()->user()->hasRole('Super Admin'))
                                                <div class="flex items-center gap-2" x-data="approvalHandler()">
                                                    <template x-if="pr.status === 'pending'">
                                                        <div class="flex items-center gap-2">
                                                            <button type="button" @click="openModal(pr.id, 'approve')"
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

                                                            <button type="button" @click="openModal(pr.id, 'reject')"
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
                                                        </div>

                                                    </template>

                                                    @include('production-requests.approval-rejected-modal')
                                                </div>
                                            @endif

                                            <x-index.action-buttons :view="true" :edit="true" :delete="true"
                                                :toggle="false" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>

                    <template x-if="rows.length === 0">
                        <div class="p-6 text-center text-gray-500">
                            Belum Ada Pengajuan Produksi.
                        </div>
                    </template>
                </div>

                <template x-if="sortedRows.length !== 0">
                    <div class="pagination-wrapper">
                        {{ $productionRequests->links('vendor.pagination.tailwind') }}
                    </div>
                </template>
            </div>
        </div>
    </div>

@endsection
