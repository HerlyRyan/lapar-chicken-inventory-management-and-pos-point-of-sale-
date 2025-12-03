@extends('layouts.app')

@section('title', 'Stok Bahan Setengah Jadi')

@section('content')
<div x-data="finishedProductsStock()" x-cloak class="container mx-auto px-4 py-6">

    <!-- Header -->
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-gray-800 flex items-center gap-3">
                <svg class="w-6 h-6 text-orange-600" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                    <path d="M1 2.5A1.5 1.5 0 012.5 1h11A1.5 1.5 0 0115 2.5v11a.5.5 0 01-.757.429L8 11.07 1.757 13.93A.5.5 0 011 13.5v-11z" />
                </svg>
                Stok Bahan Setengah Jadi
            </h1>
            <p class="text-sm text-gray-500 mt-1">Kelola stok bahan setengah jadi (pusat / cabang sesuai konteks)</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('semi-finished-distributions.create') }}?branch_id={{ request('branch_id', optional($selectedBranch)->id ?? session('selected_dashboard_branch')) }}"
               class="inline-flex items-center gap-2 bg-gradient-to-r from-orange-700 to-red-700 text-white px-4 py-2 rounded-lg shadow-sm hover:shadow-md transition">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M0 4a2 2 0 012-2h11a2 2 0 012 2v1H0V4zM0 7h15v5a2 2 0 01-2 2H2a2 2 0 01-2-2V7z" />
                </svg>
                Distribusi ke Cabang
            </a>
        </div>
    </div>

    <!-- Stock Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm border-l-4 border-red-500 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-xs font-semibold text-red-600 uppercase">Stok Kosong</div>
                    <div class="text-xl font-bold text-gray-800">{{ $finishedProducts->where('center_stock', '<=', 0)->count() }}</div>
                </div>
                <div class="text-gray-300">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M8 1a7 7 0 100 14A7 7 0 008 1zM7 4h2v5H7V4zm0 7h2v1H7v-1z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border-l-4 border-amber-400 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-xs font-semibold text-amber-600 uppercase">Stok Rendah</div>
                    <div class="text-xl font-bold text-gray-800">
                        {{ $finishedProducts->filter(function ($item) {
                                return $item->center_stock > 0 && $item->center_stock < $item->minimum_stock;
                            })->count() }}
                    </div>
                </div>
                <div class="text-gray-300">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M8 0a8 8 0 110 16A8 8 0 018 0zM7 4h2v5H7V4zm0 7h2v1H7v-1z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border-l-4 border-emerald-500 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-xs font-semibold text-emerald-600 uppercase">Stok Aman</div>
                    <div class="text-xl font-bold text-gray-800">
                        {{ $finishedProducts->filter(function ($item) { return $item->center_stock >= $item->minimum_stock * 2; })->count() }}
                    </div>
                </div>
                <div class="text-gray-300">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M13.485 3.515a1 1 0 010 1.414L6 12.414 2.515 8.93a1 1 0 011.414-1.414L6 9.586l6.071-6.071a1 1 0 011.414 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border-l-4 border-cyan-500 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-xs font-semibold text-cyan-600 uppercase">Total Produk</div>
                    <div class="text-xl font-bold text-gray-800">{{ $finishedProducts->count() }}</div>
                </div>
                <div class="text-gray-300">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M2 2h12v12H2z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm p-5 mb-6">
        <form method="GET" action="{{ route('finished-products-stock.index') }}">
            <input type="hidden" name="branch_id" value="{{ request('branch_id', optional($selectedBranch)->id ?? session('selected_dashboard_branch')) }}">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4">
                <div class="lg:col-span-2">
                    <label for="stock_level" class="block text-sm font-medium text-gray-600 mb-1">Level Stok</label>
                    <select name="stock_level" id="stock_level" class="w-full rounded-md border-gray-200 shadow-sm focus:ring-0 focus:border-cyan-400">
                        <option value="">Semua Level</option>
                        <option value="empty" {{ request('stock_level') == 'empty' ? 'selected' : '' }}>Kosong</option>
                        <option value="low" {{ request('stock_level') == 'low' ? 'selected' : '' }}>Rendah</option>
                        <option value="warning" {{ request('stock_level') == 'warning' ? 'selected' : '' }}>Peringatan</option>
                        <option value="normal" {{ request('stock_level') == 'normal' ? 'selected' : '' }}>Normal</option>
                    </select>
                </div>

                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-600 mb-1">Kategori</label>
                    <select name="category_id" id="category_id" class="w-full rounded-md border-gray-200 shadow-sm focus:ring-0 focus:border-cyan-400">
                        <option value="">Semua Kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="lg:col-span-2">
                    <label for="search" class="block text-sm font-medium text-gray-600 mb-1">Pencarian</label>
                    <input type="text" name="search" id="search" class="w-full rounded-md border-gray-200 shadow-sm focus:ring-0 focus:border-cyan-400" placeholder="Cari berdasarkan nama atau kode..." value="{{ request('search') }}">
                </div>

                <div class="flex items-end">
                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 bg-cyan-600 text-white px-4 py-2 rounded-md hover:bg-cyan-700 transition">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M11 6a5 5 0 11-2.9 9.2L15 20l1-1-5-5A5 5 0 0111 6z" />
                        </svg>
                        Filter
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Finished Products Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @if ($finishedProducts->count() > 0)
            @foreach ($finishedProducts as $product)
                @php
                    $stockPercentage = $product->minimum_stock > 0 ? min(100, ($product->center_stock / $product->minimum_stock) * 100) : 0;
                    if ($product->center_stock <= 0) {
                        $stockStatus = 'empty'; $stockColor = 'danger'; $stockText = 'Kosong';
                    } elseif ($product->center_stock < $product->minimum_stock) {
                        $stockStatus = 'low'; $stockColor = 'danger'; $stockText = 'Rendah';
                    } elseif ($product->center_stock < $product->minimum_stock * 2) {
                        $stockStatus = 'warning'; $stockColor = 'warning'; $stockText = 'Peringatan';
                    } else {
                        $stockStatus = 'normal'; $stockColor = 'success'; $stockText = 'Aman';
                    }

                    $colorMap = ['danger' => 'red', 'warning' => 'amber', 'success' => 'emerald', 'info' => 'cyan'];
                    $twBase = $colorMap[$stockColor] ?? 'gray';
                @endphp

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">{{ $product->name }}</h3>
                                <p class="text-xs text-gray-400 mt-1">{{ $product->code }}</p>
                            </div>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-{{ $twBase }}-600 text-white">
                                {{ $stockText }}
                            </span>
                        </div>

                        <div class="grid grid-cols-3 text-center mt-4 gap-3">
                            <div>
                                <div class="text-lg font-bold text-{{ $twBase }}-600">{{ number_format($product->center_stock, 0, ',', '.') }}</div>
                                <div class="text-xs text-gray-400">Stok Saat Ini</div>
                            </div>
                            <div>
                                <div class="text-lg font-bold text-gray-800">{{ number_format($product->minimum_stock, 0, ',', '.') }}</div>
                                <div class="text-xs text-gray-400">Minimum</div>
                            </div>
                            <div>
                                <div class="text-lg font-bold text-gray-800">{{ $product->unit->name ?? '-' }}</div>
                                <div class="text-xs text-gray-400">Satuan</div>
                            </div>
                        </div>

                        <!-- Progress -->
                        <div class="mt-4">
                            <div class="flex items-center justify-between text-sm mb-1">
                                <span class="text-sm text-gray-500">Level Stok</span>
                                <span class="text-sm text-{{ $twBase }}-600">{{ number_format($stockPercentage, 1) }}%</span>
                            </div>
                            <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden">
                                <div class="h-2 rounded-full bg-{{ $twBase }}-500" style="width: {{ min(100, max(5, $stockPercentage)) }}%"></div>
                            </div>
                        </div>

                        @if ($product->category)
                            <div class="mt-3">
                                <span class="inline-block text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded">{{ $product->category->name }}</span>
                            </div>
                        @endif

                        <div class="mt-4 flex items-center gap-2">
                            <a href="{{ route('finished-products-stock.show', [$product, 'branch_id' => request('branch_id', optional($selectedBranch)->id ?? session('selected_dashboard_branch'))]) }}"
                               class="inline-flex items-center gap-2 border border-cyan-200 text-cyan-700 px-3 py-1.5 rounded shadow-sm hover:bg-cyan-50 transition text-sm">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM8 11a3 3 0 100-6 3 3 0 000 6z" />
                                </svg>
                                Lihat
                            </a>

                            <button type="button"
                                    @click="openModal({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->center_stock }})"
                                    class="inline-flex items-center gap-2 border border-amber-200 text-amber-700 px-3 py-1.5 rounded shadow-sm hover:bg-amber-50 transition text-sm">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M12.146.146a1 1 0 011.415 0l2.293 2.293a1 1 0 010 1.414L6.207 13H3v-3.207L12.146.146z" />
                                </svg>
                                Sesuaikan
                            </button>

                            <a href="{{ route('semi-finished-distributions.create') }}?product={{ $product->id }}&branch_id={{ request('branch_id', optional($selectedBranch)->id ?? session('selected_dashboard_branch')) }}"
                               class="ml-auto inline-flex items-center gap-2 bg-gradient-to-r from-orange-700 to-red-700 text-white px-3 py-1.5 rounded shadow-sm text-sm hover:opacity-95 transition">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M1 2.5A1.5 1.5 0 012.5 1h11A1.5 1.5 0 0115 2.5v11a.5.5 0 01-.757.429L8 11.07 1.757 13.93A.5.5 0 011 13.5v-11z" />
                                </svg>
                                Distribusi
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- Pagination -->
            <div class="col-span-full">
                <div class="mt-6 flex justify-center">
                    {{ $finishedProducts->appends(request()->query())->links() }}
                </div>
            </div>
        @else
            <div class="col-span-full">
                <div class="bg-white rounded-xl shadow-sm p-8 text-center">
                    <svg class="w-16 h-16 text-gray-300 mx-auto" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M2 2h12v12H2z" />
                    </svg>
                    <h4 class="mt-4 text-lg text-gray-700">Tidak Ada Produk</h4>
                    <p class="mt-2 text-sm text-gray-500">
                        @if (request()->hasAny(['stock_level', 'category_id', 'search']))
                            Tidak ada produk yang sesuai dengan filter yang dipilih.
                        @else
                            Belum ada bahan setengah jadi pada stok saat ini.
                        @endif
                    </p>
                </div>
            </div>
        @endif
    </div>

    <!-- Adjustment Modal (Alpine.js) -->
    {{-- <div x-show="open" x-transition.opacity class="fixed inset-0 z-50 flex items-end md:items-center justify-center">
        <div @click="close" class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>

        <div x-trap.noscroll x-show="open" x-transition class="relative w-full max-w-lg mx-4 md:mx-0 bg-white rounded-xl shadow-xl overflow-hidden transform transition-all"
             role="dialog" aria-modal="true" aria-labelledby="adjust-modal-title">
            <form :action="adjustAction" method="POST" class="p-6" x-ref="form" @submit.prevent="submit">
                @csrf
                <input type="hidden" name="product_id" :value="payload.id">
                <input type="hidden" name="branch_id" value="{{ request('branch_id', optional($selectedBranch)->id ?? session('selected_dashboard_branch')) }}">

                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h3 id="adjust-modal-title" class="text-lg font-semibold text-gray-800">Sesuaikan Stok</h3>
                        <p class="text-sm text-gray-500 mt-1" x-text="payload.name"></p>
                    </div>
                    <button type="button" @click="close" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="grid grid-cols-1 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Penyesuaian</label>
                        <select x-model="adjust_type" name="adjust_type" class="w-full rounded-md border-gray-200 shadow-sm focus:ring-0 focus:border-cyan-400">
                            <option value="set">Set Stok</option>
                            <option value="increase">Tambah</option>
                            <option value="decrease">Kurangi</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah</label>
                        <input x-model.number="quantity" name="quantity" type="number" min="0" step="1" required class="w-full rounded-md border-gray-200 shadow-sm focus:ring-0 focus:border-cyan-400">
                        <p class="text-xs text-gray-400 mt-1">Stok saat ini: <span class="font-medium" x-text="payload.stock"></span></p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Catatan (opsional)</label>
                        <textarea x-model="note" name="note" rows="3" class="w-full rounded-md border-gray-200 shadow-sm focus:ring-0 focus:border-cyan-400"></textarea>
                    </div>
                </div>

                <div class="mt-4 flex items-center justify-end gap-2">
                    <button type="button" @click="close" class="px-4 py-2 rounded-md text-sm bg-gray-100 hover:bg-gray-200">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-md text-sm bg-amber-600 text-white hover:bg-amber-700">Simpan</button>
                </div>
            </form>
        </div>
    </div> --}}

</div>

@push('styles')
    <style>
        /* small helper to ensure rounded progress looks consistent */
        .h-2 { height: .5rem; }
    </style>
@endpush

@push('scripts')
    <script>
        function finishedProductsStock() {
            return {
                open: false,
                payload: { id: null, name: '', stock: 0 },
                adjust_type: 'set',
                quantity: 0,
                note: '',
                // base URL for finished-products-stock; we'll compose full adjust URL using the product id
                adjustActionBase: '{{ url("finished-products-stock") }}',
                adjustAction: '',
                openModal(id, name, stock) {
                    this.payload = { id: id, name: name, stock: stock ?? 0 };
                    this.adjust_type = 'set';
                    this.quantity = 0;
                    this.note = '';
                    // compose the route that requires the finishedProduct parameter: /finished-products-stock/{id}/adjust
                    this.adjustAction = `${this.adjustActionBase}/${id}/adjust`;
                    this.open = true;
                    // focus first input next tick
                    this.$nextTick(() => {
                        const el = document.querySelector('[name="quantity"]');
                        if (el) el.focus();
                    });
                },
                close() {
                    this.open = false;
                },
                submit() {
                    // Basic client validation
                    if (this.quantity === null || this.quantity === '' || isNaN(this.quantity)) {
                        alert('Masukkan jumlah yang valid.');
                        return;
                    }
                    // Build form and submit so backend receives standard POST (preserve Laravel csrf)
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = this.adjustAction;
                    form.style.display = 'none';

                    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    if (csrf) {
                        const inputCsrf = document.createElement('input');
                        inputCsrf.type = 'hidden';
                        inputCsrf.name = '_token';
                        inputCsrf.value = csrf;
                        form.appendChild(inputCsrf);
                    }

                    const inputs = [
                        { name: 'product_id', value: this.payload.id },
                        { name: 'branch_id', value: '{{ request('branch_id', optional($selectedBranch)->id ?? session('selected_dashboard_branch')) }}' },
                        { name: 'adjust_type', value: this.adjust_type },
                        { name: 'quantity', value: this.quantity },
                        { name: 'note', value: this.note }
                    ];

                    inputs.forEach(i => {
                        const el = document.createElement('input');
                        el.type = 'hidden';
                        el.name = i.name;
                        el.value = i.value;
                        form.appendChild(el);
                    });

                    document.body.appendChild(form);
                    form.submit();
                }
            }
        }

        // fallback: attach to window so buttons rendered inside foreach can call it
        window.finishedProductsOpenModal = (id, name, stock) => {
            const root = document.querySelector('[x-data="finishedProductsStock()"]');
            if (!root) return;
            const cmp = root.__x;
            if (cmp && cmp.$data) {
                cmp.$data.openModal(id, name, stock);
            } else {
                // if Alpine instance not bound yet, try dispatch a custom event consumed by Alpine
                window.dispatchEvent(new CustomEvent('open-adjustment-modal', { detail: { id, name, stock } }));
            }
        };

        // listen for custom events to open modal
        window.addEventListener('open-adjustment-modal', (e) => {
            const detail = e.detail || {};
            window.finishedProductsOpenModal(detail.id, detail.name, detail.stock);
        });
    </script>
@endpush
@endsection
