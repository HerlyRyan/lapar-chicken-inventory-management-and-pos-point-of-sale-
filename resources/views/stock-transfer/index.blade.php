@extends('layouts.app')

@section('title', 'Daftar Transfer Stok')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-orange-50/30 to-red-50/30">
    {{-- Page Header --}}
    <x-index.header title="Transfer Stok" subtitle="Kelola transfer stok antar cabang"
        addRoute="{{ route('stock-transfer.create') }}" addText="Buat Transfer" />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-8">
        @php
            // Prepare a lightweight array for Alpine/JS (avoid full Eloquent models)
            $transferRows = $transfers->map(function($t) {
                $unitAbbr = 'unit';
                if ($t->item_type === 'finished') {
                    $fp = $t->finishedProduct;
                    if ($fp && $fp->unit) {
                        $unitAbbr = $fp->unit->abbreviation ?: ($fp->unit->unit_name ?? 'unit');
                    }
                } else {
                    $sp = $t->semiFinishedProduct;
                    if ($sp && $sp->unit) {
                        $unitAbbr = $sp->unit->abbreviation ?: ($sp->unit->unit_name ?? 'unit');
                    }
                }

                return [
                    'id' => $t->id,
                    'product_name' => $t->item_type === 'finished'
                        ? ($t->finishedProduct->name ?? 'Produk tidak ditemukan')
                        : ($t->semiFinishedProduct->name ?? 'Produk tidak ditemukan'),
                    'item_type' => $t->item_type,
                    'quantity' => (int) $t->quantity,
                    'unit' => $unitAbbr,
                    'to_branch' => $t->toBranch->name ?? '-',
                    'status' => $t->status,
                    'date' => $t->created_at->format('d/m/Y'),
                    'time' => $t->created_at->format('H:i'),
                ];
            })->toArray();
        @endphp

        <div x-data="sortableTable(@js($transferRows))" @sort-column.window="sortBy($event.detail)"
            class="bg-white rounded-lg sm:rounded-2xl shadow-lg sm:shadow-xl border border-gray-200 overflow-hidden">
            {{-- Card Header --}}
            <x-index.card-header title="Daftar Transfer Stok" />

            {{-- Filter Bar (reuse existing component if present) --}}
            <div class="px-4 sm:px-6 lg:px-8 py-3">
                <form method="GET" action="{{ route('stock-transfer.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">Status</label>
                        <select name="status" class="block w-full rounded-md border-gray-200">
                            <option value="">Semua Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Dikirim</option>
                            <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>Diterima</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">Cabang Tujuan</label>
                        <select name="to_branch_id" class="block w-full rounded-md border-gray-200">
                            <option value="">Semua Cabang</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ request('to_branch_id') == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">Tanggal Dari</label>
                        <input type="date" name="date_from" class="block w-full rounded-md border-gray-200" value="{{ request('date_from') }}">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">Tanggal Sampai</label>
                        <input type="date" name="date_to" class="block w-full rounded-md border-gray-200" value="{{ request('date_to') }}">
                    </div>

                    <div class="md:col-span-4 flex items-center gap-2 mt-2">
                        <button type="submit" class="inline-flex items-center px-3 py-2 bg-blue-600 text-white rounded-md shadow-sm">
                            <i class="fas fa-search me-2"></i> Filter
                        </button>
                        <a href="{{ route('stock-transfer.index') }}" class="inline-flex items-center px-3 py-2 bg-gray-100 text-gray-700 rounded-md shadow-sm">
                            <i class="fas fa-times me-2"></i> Reset
                        </a>
                    </div>
                </form>
            </div>

            {{-- Desktop Table --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    {{-- custom head to mimic existing components --}}
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produk</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ke Cabang</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <template x-for="(transfer, index) in sortedRows" :key="transfer.id">
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="index + 1"></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" x-text="transfer.product_name"></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <template x-if="transfer.item_type === 'finished'">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Produk Jadi</span>
                                    </template>
                                    <template x-if="transfer.item_type !== 'finished'">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">Produk Setengah Jadi</span>
                                    </template>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" x-text="transfer.quantity + ' ' + transfer.unit"></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" x-text="transfer.to_branch"></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <template x-if="transfer.status === 'pending'">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>
                                    </template>
                                    <template x-if="transfer.status === 'sent'">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Dikirim</span>
                                    </template>
                                    <template x-if="transfer.status === 'accepted'">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Diterima</span>
                                    </template>
                                    <template x-if="transfer.status === 'rejected'">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">Ditolak</span>
                                    </template>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div class="text-sm font-medium text-gray-900" x-text="transfer.date"></div>
                                    <div class="text-xs text-gray-400" x-text="transfer.time"></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                    <div x-data="{
                                        viewUrl: '/stock-transfer/' + transfer.id,
                                        editUrl: '/stock-transfer/' + transfer.id + '/edit',
                                        cancelUrl: '/stock-transfer/' + transfer.id + '/cancel',
                                        id: transfer.id,
                                        status: transfer.status
                                    }" class="inline-flex gap-1">
                                        <button @click.prevent="$dispatch('open-detail', id)" class="inline-flex items-center justify-center w-9 h-9 rounded-md bg-white border text-blue-600 hover:bg-gray-50" title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </button>

                                        <template x-if="status === 'pending'">
                                            <a :href="editUrl" class="inline-flex items-center justify-center w-9 h-9 rounded-md bg-white border text-yellow-600 hover:bg-gray-50" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </template>

                                        <template x-if="status === 'pending'">
                                            <button @click.prevent="confirmCancel(cancelUrl)" class="inline-flex items-center justify-center w-9 h-9 rounded-md bg-white border text-red-600 hover:bg-gray-50" title="Batalkan">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </template>
                                    </div>
                                </td>
                            </tr>
                        </template>

                        <template x-if="sortedRows.length === 0">
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-sm text-gray-500">
                                    <x-index.none-data />
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            {{-- Mobile Cards --}}
            <div class="md:hidden divide-y divide-gray-200">
                <template x-for="(transfer, index) in sortedRows" :key="transfer.id">
                    <div class="p-4 hover:bg-gray-50">
                        <div class="flex items-start justify-between mb-2">
                            <div class="min-w-0">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-sm font-medium text-gray-900 truncate" x-text="transfer.product_name"></h3>
                                    <span class="text-xs text-gray-500 ml-2" x-text="'#' + transfer.id"></span>
                                </div>
                                <p class="text-sm text-gray-500 mt-1" x-text="transfer.to_branch"></p>
                            </div>
                        </div>

                        <div class="mt-3 space-y-2 text-sm text-gray-700">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Jumlah</span>
                                <span x-text="transfer.quantity + ' ' + transfer.unit"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Status</span>
                                <span x-text="transfer.status"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Tanggal</span>
                                <span x-text="transfer.date + ' ' + transfer.time"></span>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-t border-gray-200">
                            <div class="flex items-center gap-2">
                                <button @click.prevent="$dispatch('open-detail', transfer.id)" class="inline-flex items-center px-3 py-2 bg-white border rounded-md text-blue-600">
                                    <i class="fas fa-eye me-2"></i> Detail
                                </button>
                                <template x-if="transfer.status === 'pending'">
                                    <a :href="'/stock-transfer/' + transfer.id + '/edit'" class="inline-flex items-center px-3 py-2 bg-white border rounded-md text-yellow-600">
                                        <i class="fas fa-edit me-2"></i> Edit
                                    </a>
                                </template>
                                <template x-if="transfer.status === 'pending'">
                                    <button @click.prevent="confirmCancel('/stock-transfer/' + transfer.id + '/cancel')" class="inline-flex items-center px-3 py-2 bg-white border rounded-md text-red-600">
                                        <i class="fas fa-times me-2"></i> Batalkan
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            {{-- Pagination --}}
            <template x-if="sortedRows.length !== 0">
                <div class="px-4 py-4">
                    <div class="pagination-wrapper">
                        {!! $transfers->appends(request()->query())->links('vendor.pagination.tailwind') !!}
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>

{{-- Detail Modal (Alpine) --}}
<div x-data="stockTransferDetail()" x-on:open-detail.window="open($event.detail)" x-show="show" x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="fixed inset-0 bg-black/50" @click="close()"></div>
    <div class="bg-white rounded-lg shadow-lg max-w-3xl w-full z-10 overflow-hidden">
        <div class="flex items-center justify-between px-4 py-3 border-b">
            <h3 class="text-lg font-medium">Detail Transfer Stok</h3>
            <button @click="close()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="p-4" x-html="html || '<div class=&quot;text-center text-gray-500&quot;><i class=&quot;fas fa-spinner fa-spin&quot;&gt;&lt;/i&gt; Memuat...</div>'"></div>
    </div>
</div>

@push('scripts')
<script>
    function stockTransferDetail() {
        return {
            show: false,
            html: '',
            open(id) {
                this.show = true;
                this.html = '<div class="text-center text-gray-500"><i class="fas fa-spinner fa-spin"></i> Memuat...</div>';
                fetch(`/stock-transfer/${id}/detail`)
                    .then(r => r.text())
                    .then(html => { this.html = html })
                    .catch(() => { this.html = '<div class="text-red-600">Gagal memuat detail transfer.</div>' });
            },
            close() { this.show = false; this.html = '' }
        }
    }

    function confirmCancel(url) {
        Swal.fire({
            title: 'Batalkan Transfer?',
            text: 'Transfer ini akan dibatalkan dan tidak dapat dikembalikan.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Batalkan',
            cancelButtonText: 'Tidak'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({})
                }).then(r => r.json())
                .then(json => {
                    if (json.success) {
                        Swal.fire('Dibatalkan!', json.message, 'success').then(()=> location.reload());
                    } else {
                        Swal.fire('Gagal!', json.message || 'Terjadi kesalahan', 'error');
                    }
                }).catch(()=> {
                    Swal.fire('Error!', 'Terjadi kesalahan', 'error');
                });
            }
        });
    }

    // Simple sortableTable helper (keeps original sorting API used in template)
    function sortableTable(rows = []) {
        return {
            originalRows: rows,
            sortedRows: rows.slice(),
            sortBy(column) {
                // simple sort by string/number when column exists
                this.sortedRows.sort((a,b) => {
                    if (a[column] === b[column]) return 0;
                    return a[column] > b[column] ? 1 : -1;
                });
            }
        }
    }
</script>
@endpush
@endsection
