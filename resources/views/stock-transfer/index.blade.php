@extends('layouts.app')

@section('title', 'Data Stok Transfer')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-green-50/30 to-emerald-50/30">
        {{-- Page Header --}}
        <x-index.header title="Transfer Stok" subtitle="Kelola transfer stok antar cabang"
            addRoute="{{ route('stock-transfer.create') }}" addText="Buat Transfer" />

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-8">
            <div x-data="sortableTable(@js($transfers))" @sort-column.window="sortBy($event.detail)"
                class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">

                <x-index.card-header title="Stok Transfer" />

                <x-filter-bar searchPlaceholder="Cari data transfer..." :selects="$selects ?? []"
                    printRouteName="reports.stock-transfers.print" date="true" />

                {{-- Desktop Table --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <x-index.table-head :columns="$columns" />
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
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" x-text="transfer.notes">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                                        x-text="new Date(transfer.created_at).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' })">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                                        x-text="transfer.handled_at ? new Date(transfer.handled_at).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' }) : '-'">
                                    </td>
                                    <td>
                                        <div class="flex items-center gap-2">
                                            <button @click.prevent="$dispatch('open-detail', transfer.id)"
                                                class="inline-flex items-center px-3 py-2 bg-white border rounded-md text-blue-600">
                                                <i class="fas fa-eye me-2"></i> Detail
                                            </button>
                                            <template x-if="transfer.status === 'sent'">
                                                <a :href="'/stock-transfer/' + transfer.id + '/edit'"
                                                    class="inline-flex items-center px-3 py-2 bg-white border rounded-md text-yellow-600">
                                                    <i class="fas fa-edit me-2"></i> Edit
                                                </a>
                                            </template>
                                            <template x-if="transfer.status === 'sent'">
                                                <button @click.prevent="$dispatch('open-approve', transfer.id)"
                                                    class="inline-flex items-center px-3 py-2 bg-white border rounded-md text-green-600">
                                                    <i class="fas fa-check me-2"></i> Terima
                                                </button>
                                            </template>

                                            <template x-if="transfer.status === 'sent'">
                                                <button @click.prevent="$dispatch('open-reject', transfer.id)"
                                                    class="inline-flex items-center px-3 py-2 bg-white border rounded-md text-red-600">
                                                    <i class="fas fa-times me-2"></i> Tolak
                                                </button>
                                            </template>
                                        </div>
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
                <div class="md:hidden divide-y divide-gray-100">
                    <template x-for="(transfer, index) in sortedRows" :key="transfer.id">
                        <div class="p-5 hover:bg-gray-50 transition-colors">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3 class="text-sm font-bold text-gray-900" x-text="transfer.finished_product.name">
                                    </h3>
                                    <p class="text-[10px] font-mono text-gray-400" x-text="'#' + transfer.id"></p>
                                </div>
                                <button @click.prevent="$dispatch('open-detail', transfer.id)"
                                    class="text-blue-600 text-sm font-semibold">
                                    Detail <i class="fas fa-chevron-right ml-1"></i>
                                </button>
                            </div>

                            <div class="grid grid-cols-2 gap-4 mb-4 bg-gray-50 p-3 rounded-lg">
                                <div>
                                    <p class="text-[10px] uppercase text-gray-400 font-bold">Dari</p>
                                    <p class="text-xs font-semibold text-gray-700" x-text="transfer.from_branch.name"></p>
                                </div>
                                <div>
                                    <p class="text-[10px] uppercase text-gray-400 font-bold">Ke</p>
                                    <p class="text-xs font-semibold text-emerald-600" x-text="transfer.to_branch.name"></p>
                                </div>
                            </div>

                            <div class="mt-4 pt-3 border-t border-gray-200">
                                <button @click.prevent="$dispatch('open-detail', transfer.id)"
                                    class="inline-flex items-center px-3 py-2 bg-white border rounded-md text-blue-600">
                                    <i class="fas fa-eye me-2"></i> Detail
                                </button>
                                <template x-if="transfer.status === 'sent'">
                                    <a :href="'/stock-transfer/' + transfer.id + '/edit'"
                                        class="inline-flex items-center px-3 py-2 bg-white border rounded-md text-yellow-600">
                                        <i class="fas fa-edit me-2"></i> Edit
                                    </a>
                                </template>
                                <template x-if="transfer.status === 'sent'">
                                    <button @click.prevent="$dispatch('open-approve', transfer.id)"
                                        class="inline-flex items-center px-3 py-2 bg-white border rounded-md text-green-600">
                                        <i class="fas fa-check me-2"></i> Terima
                                    </button>
                                </template>

                                <template x-if="transfer.status === 'sent'">
                                    <button @click.prevent="$dispatch('open-reject', transfer.id)"
                                        class="inline-flex items-center px-3 py-2 bg-white border rounded-md text-red-600">
                                        <i class="fas fa-times me-2"></i> Tolak
                                    </button>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>

                <template x-if="sortedRows.length !== 0">
                    <div class="p-4 border-t border-gray-100">
                        {{ $pagination->withQueryString()->links('vendor.pagination.tailwind') }}
                    </div>
                </template>
            </div>
        </div>
    </div>

    {{-- MODAL DETAIL IMPROVED --}}
    <div x-data="stockTransferDetail()" x-on:open-detail.window="open($event.detail)" x-show="show"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6" x-cloak>

        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="close()"></div>

        <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full z-10 overflow-hidden border border-gray-100">
            <div class="flex items-center justify-between px-6 py-4 bg-gray-50 border-b">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-blue-600 rounded-lg text-white">
                        <i class="fas fa-exchange-alt"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 leading-none">Detail Transfer</h3>
                        <p class="text-xs text-gray-500 mt-1">Informasi lengkap perpindahan stok</p>
                    </div>
                </div>
                <button @click="close()" class="p-2 hover:bg-gray-200 rounded-full transition-colors text-gray-400">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            <div class="p-6 max-h-[70vh] overflow-y-auto custom-scrollbar">
                <div x-show="!loading" x-html="html"></div>

                {{-- Skeleton Loader --}}
                <div x-show="loading" class="space-y-4">
                    <div class="h-8 bg-gray-100 animate-pulse rounded w-1/2"></div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="h-20 bg-gray-50 animate-pulse rounded-xl"></div>
                        <div class="h-20 bg-gray-50 animate-pulse rounded-xl"></div>
                    </div>
                    <div class="h-32 bg-gray-50 animate-pulse rounded-xl"></div>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t flex justify-end">
                <button @click="close()"
                    class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <style>
        [x-cloak] {
            display: none !important;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 10px;
        }
    </style>

    <script>
        function stockTransferDetail() {
            return {
                show: false,
                loading: false,
                html: '',
                open(id) {
                    this.show = true;
                    this.loading = true;
                    fetch(`/stock-transfer/${id}/detail`)
                        .then(r => r.text())
                        .then(html => {
                            this.html = html;
                            this.loading = false;
                        })
                        .catch(() => {
                            this.html =
                                '<div class="p-4 bg-red-50 text-red-600 rounded-lg border border-red-100 flex items-center gap-3"><i class="fas fa-exclamation-circle"></i> Gagal memuat data.</div>';
                            this.loading = false;
                        });
                },
                close() {
                    this.show = false;
                    setTimeout(() => {
                        this.html = '';
                    }, 300);
                }
            }
        }

        window.addEventListener('open-approve', event => {
            const id = event.detail;

            Swal.fire({
                title: 'Terima Transfer?',
                text: 'Stok akan ditambahkan ke cabang tujuan.',
                icon: 'question',
                input: 'textarea',
                inputLabel: 'Catatan (opsional)',
                inputPlaceholder: 'Tambahkan catatan penerimaan...',
                showCancelButton: true,
                confirmButtonColor: '#16a34a',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Terima',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/stock-transfer/${id}/accept`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                response_notes: result.value
                            })
                        })
                        .then(res => res.json())
                        .then(json => {
                            if (json.success) {
                                Swal.fire('Berhasil!', json.message, 'success')
                                    .then(() => location.reload());
                            } else {
                                Swal.fire('Gagal!', json.message || 'Terjadi kesalahan', 'error');
                            }
                        })
                        .catch(() => {
                            Swal.fire('Error!', 'Terjadi kesalahan server', 'error');
                        });
                }
            });
        });

        window.addEventListener('open-reject', event => {
            const id = event.detail;

            Swal.fire({
                title: 'Tolak Transfer?',
                text: 'Stok tidak akan ditambahkan ke cabang tujuan.',
                icon: 'question',
                input: 'textarea',
                inputLabel: 'Catatan (opsional)',
                inputPlaceholder: 'Tambahkan catatan penerimaan...',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Tolak',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/stock-transfer/${id}/reject`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                response_notes: result.value
                            })
                        })
                        .then(res => res.json())
                        .then(json => {
                            if (json.success) {
                                Swal.fire('Berhasil!', json.message, 'success')
                                    .then(() => location.reload());
                            } else {
                                Swal.fire('Gagal!', json.message || 'Terjadi kesalahan', 'error');
                            }
                        })
                        .catch(() => {
                            Swal.fire('Error!', 'Terjadi kesalahan server', 'error');
                        });
                }
            });
        });

        // Simple sortableTable helper (keeps original sorting API used in template)
        function sortableTable(rows = []) {
            return {
                originalRows: rows,
                sortedRows: rows.slice(),
                sortBy(column) {
                    // simple sort by string/number when column exists
                    this.sortedRows.sort((a, b) => {
                        if (a[column] === b[column]) return 0;
                        return a[column] > b[column] ? 1 : -1;
                    });
                }
            }
        }
    </script>
@endsection
