@extends('layouts.app')

@section('title', 'Stok Bahan Mentah')

@php
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;
@endphp

@section('content')
    <div class="container mx-auto px-4 py-6">

        @php
            $rmList =
                $rawMaterials instanceof \Illuminate\Pagination\AbstractPaginator
                    ? $rawMaterials->getCollection()
                    : (is_array($rawMaterials)
                        ? collect($rawMaterials)
                        : $rawMaterials);
        @endphp

        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4 mb-6">
            <div>
                <h2 class="text-2xl font-semibold text-gray-800 flex items-center gap-3">
                    <span
                        class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-gradient-to-r from-orange-600 to-red-700 text-white shadow-sm">
                        <!-- box icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 7l9-4 9 4v8a2 2 0 0 1-2 2h-2M3 7v8a2 2 0 0 0 2 2h2m10-10v8m0 0h-8" />
                        </svg>
                    </span>
                    Stok Bahan Mentah — Lapar Chicken
                </h2>
                <p class="text-sm text-gray-500 mt-1">Kelola stok terpusat dengan tampilan ringkas dan informatif.</p>
            </div>

            <div class="flex items-center gap-3">
                <div class="text-right text-sm text-gray-500">
                    <div class="text-xs">Terakhir diperbarui</div>
                    <div class="text-gray-800 font-medium">{{ now()->format('Y-m-d H:i') }}</div>
                </div>

                <a href="{{ route('raw-materials.create') }}"
                    class="inline-flex items-center gap-2 bg-gradient-to-r from-orange-600 to-red-700 text-white px-4 py-2 rounded-lg shadow hover:opacity-95 transition">
                    <!-- plus icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Bahan
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="mb-4 p-3 rounded-lg bg-green-50 border border-green-200 text-green-800 text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 p-3 rounded-lg bg-red-50 border border-red-200 text-red-800 text-sm">
                {{ session('error') }}
            </div>
        @endif

        <!-- Summary -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="rounded-xl p-4 bg-gradient-to-r from-red-50 to-red-25 border border-red-100 shadow-sm">
                <div class="flex items-start gap-3">
                    <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center text-red-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                            <path
                                d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div class="text-sm text-gray-500">Stok Kosong</div>
                        <div class="text-2xl font-semibold text-gray-800">
                            {{ $rmList->where('current_stock', '<=', 0)->count() }}</div>
                        <div class="mt-2">
                            <span
                                class="inline-block px-3 py-1 rounded-full bg-white text-red-600 text-xs font-semibold">Perlu
                                Rekening</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-xl p-4 bg-white border border-gray-100 shadow-sm">
                <div class="flex items-start gap-3">
                    <div class="w-12 h-12 rounded-full bg-yellow-50 flex items-center justify-center text-yellow-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M11 17a1 1 0 002 0v-5h3a1 1 0 000-2h-3V7a1 1 0 10-2 0v3H8a1 1 0 000 2h3v5z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div class="text-sm text-gray-500">Stok Rendah</div>
                        <div class="text-2xl font-semibold text-gray-800">
                            {{ $rmList->filter(function ($item) {return $item->current_stock > 0 && $item->current_stock < $item->minimum_stock;})->count() }}
                        </div>
                        <div class="mt-2">
                            <span
                                class="inline-block px-3 py-1 rounded-full bg-white text-yellow-700 text-xs font-semibold">Perhatian</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-xl p-4 bg-white border border-gray-100 shadow-sm">
                <div class="flex items-start gap-3">
                    <div class="w-12 h-12 rounded-full bg-green-50 flex items-center justify-center text-green-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M9 16.17l-3.88-3.88L4 13.41 9 18.41 20 7.41 18.59 6 9 16.17z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div class="text-sm text-gray-500">Stok Aman</div>
                        <div class="text-2xl font-semibold text-gray-800">
                            {{ $rmList->filter(function ($item) {return $item->current_stock >= $item->minimum_stock * 2;})->count() }}
                        </div>
                        <div class="mt-2">
                            <span
                                class="inline-block px-3 py-1 rounded-full bg-white text-green-700 text-xs font-semibold">Stabil</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-xl p-4 bg-white border border-gray-100 shadow-sm">
                <div class="flex items-start gap-3">
                    <div class="w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center text-blue-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M3 13h2v-2H3v2zm4 0h2v-2H7v2zm4 0h2v-2h-2v2zm4 0h2v-2h-2v2zM3 17h2v-2H3v2zm4 0h2v-2H7v2zm4 0h2v-2h-2v2zm4 0h2v-2h-2v2z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div class="text-sm text-gray-500">Total Bahan</div>
                        <div class="text-2xl font-semibold text-gray-800">{{ $rmList->count() }}</div>
                        <div class="mt-2">
                            <span
                                class="inline-block px-3 py-1 rounded-full bg-white text-blue-700 text-xs font-semibold">Inventaris</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="rounded-xl bg-white border border-gray-100 p-4 mb-6 shadow-sm">
            <form method="GET" action="{{ route('raw-materials.stock') }}">
                <div class="grid grid-cols-1 md:grid-cols-8 gap-3 items-end">
                    <div class="md:col-span-2">
                        <label class="text-xs text-gray-500 mb-1 block">Level Stok</label>
                        <select name="stock_level" id="stock_level" class="w-full rounded-md border-gray-200 text-sm">
                            <option value="">Semua Level</option>
                            <option value="empty" {{ request('stock_level') == 'empty' ? 'selected' : '' }}>Kosong</option>
                            <option value="low" {{ request('stock_level') == 'low' ? 'selected' : '' }}>Rendah</option>
                            <option value="warning" {{ request('stock_level') == 'warning' ? 'selected' : '' }}>Peringatan
                            </option>
                            <option value="normal" {{ request('stock_level') == 'normal' ? 'selected' : '' }}>Normal
                            </option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label class="text-xs text-gray-500 mb-1 block">Kategori</label>
                        <select name="category_id" id="category_id" class="w-full rounded-md border-gray-200 text-sm">
                            <option value="">Semua Kategori</option>
                            @foreach ($categories ?? collect() as $category)
                                <option value="{{ $category->id }}"
                                    {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-3">
                        <label class="text-xs text-gray-500 mb-1 block">Pencarian</label>
                        <div class="flex gap-2">
                            <input type="text" name="search" id="search"
                                class="flex-1 rounded-md border-gray-200 text-sm px-3 py-2"
                                placeholder="Cari nama atau kode..." value="{{ request('search', request('q')) }}">
                            <button type="submit"
                                class="inline-flex items-center gap-2 bg-indigo-600 text-white px-4 py-2 rounded-md text-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z" />
                                </svg>
                                Cari
                            </button>
                        </div>
                    </div>

                    <div class="md:col-span-1 flex gap-2 justify-end">
                        <a href="{{ route('raw-materials.stock') }}"
                            class="inline-flex items-center px-3 py-2 border border-gray-200 rounded-md text-sm text-gray-600">Reset</a>
                        <button type="submit"
                            class="inline-flex items-center px-3 py-2 bg-orange-600 text-white rounded-md text-sm">Terapkan</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Materials Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($rawMaterials as $material)
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 hover:shadow-md transition">
                    <div class="flex gap-4">
                        <div class="w-36 flex-shrink-0">
                            @if ($material->image && Storage::exists($material->image))
                                <img src="{{ Storage::url($material->image) }}" alt="{{ $material->name }}"
                                    class="w-full h-36 object-cover rounded-lg border border-gray-100 bg-gray-50">
                            @else
                                <div
                                    class="w-full h-36 rounded-lg border border-gray-100 bg-gray-50 flex items-center justify-center text-gray-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V7M7 7l5 5 5-5" />
                                    </svg>
                                </div>
                            @endif
                        </div>

                        <div class="flex-1 flex flex-col">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-800">{{ Str::limit($material->name, 48) }}
                                    </h4>
                                    <div class="text-xs text-gray-500 mt-1">Kode: <span
                                            class="font-medium text-gray-700">{{ $material->code ?? '-' }}</span></div>
                                </div>

                                <div class="text-right">
                                    @if ($material->current_stock <= 0)
                                        <span
                                            class="inline-flex items-center px-2 py-1 text-xs rounded-full bg-red-100 text-red-700 font-semibold">Kosong</span>
                                    @elseif($material->current_stock < $material->minimum_stock)
                                        <span
                                            class="inline-flex items-center px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800 font-semibold">Rendah</span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2 py-1 text-xs rounded-full bg-green-100 text-green-800 font-semibold">Aman</span>
                                    @endif
                                </div>
                            </div>

                            <div class="mt-3">
                                <div class="flex justify-between text-sm text-gray-600">
                                    <div>Stok: <span
                                            class="font-medium text-gray-800">{{ $material->current_stock }}</span></div>
                                    <div>Min: <span class="text-gray-700">{{ $material->minimum_stock }}</span></div>
                                </div>

                                @php
                                    $ratio = $material->minimum_stock
                                        ? min(
                                            100,
                                            intval(($material->current_stock / max(1, $material->minimum_stock)) * 50),
                                        )
                                        : 100;
                                @endphp

                                <div class="mt-2 h-2 w-full rounded-full bg-gray-100 overflow-hidden">
                                    <div class="h-full rounded-full bg-orange-500" style="width: {{ $ratio }}%">
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 flex items-center gap-2">
                                <a href="{{ route('raw-materials.show', $material->id) }}"
                                    class="text-sm px-3 py-1.5 border border-gray-200 rounded-md text-gray-700 hover:bg-gray-50">Detail</a>
                                <a href="{{ route('raw-materials.edit', $material->id) }}"
                                    class="text-sm px-3 py-1.5 bg-yellow-500 text-white rounded-md hover:opacity-95">Edit</a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    <div class="rounded-xl p-6 bg-white border border-gray-100 text-center text-gray-500">
                        Tidak ada bahan untuk ditampilkan.
                    </div>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        <template x-if="sortedRows.length !== 0">
            <div class="pagination-wrapper">
                {{ $rawMaterials->appends(request()->query())->links('vendor.pagination.tailwind') }}
            </div>
        </template>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const search = document.getElementById('search');
            if (search) {
                search.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        this.closest('form').submit();
                    }
                });
            }
        });
    </script>
@endpush
