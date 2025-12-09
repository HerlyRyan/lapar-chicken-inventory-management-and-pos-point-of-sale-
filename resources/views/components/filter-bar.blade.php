@props([
    'searchPlaceholder' => 'Cari data...',
    'selects' => [],
    'date' => false,
    'export_csv' => false,
    'printRouteName' => 'reports.branches.print',
    'print' => false,
])

<div class="px-4 sm:px-6 py-4 sm:py-6 bg-gray-50 border-b border-gray-200" x-data="{
    // Fungsi untuk mendapatkan URL cetak dinamis
    getPrintUrl() {
        // 1. Kumpulkan semua filter aktif dari store
        const filters = {
            search: $store.table.search,
            // Filter dinamis lainnya
            ...$store.table.filters,
        };

        // 2. Tambahkan filter tanggal jika ada
        @if ($date) filters.start_date = $store.table.start_date;
            filters.end_date = $store.table.end_date; @endif

        // 3. Hapus filter yang bernilai null, kosong, atau undefined
        const cleanFilters = Object.fromEntries(
            Object.entries(filters).filter(([_, v]) => v !== null && v !== '' && v !== undefined)
        );

        // 4. Ubah objek filter menjadi query string
        const queryString = new URLSearchParams(cleanFilters).toString();

        // 5. Bangun URL final menggunakan route Laravel
        // Kita menggunakan PHP Blade untuk mendapatkan base URL route        
        const baseUrl = '{{ route($printRouteName) }}';

        return `${baseUrl}${queryString ? '?' + queryString : ''}`;
    }
}">
    {{-- Filter & Search --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-{{ count($selects) + 2 }} gap-3 sm:gap-4">
        {{-- Search Input (Tetap sama) --}}
        <div class="sm:col-span-2 lg:col-span-2">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-gray-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text" placeholder="{{ $searchPlaceholder }}" x-model="$store.table.search"
                    class="block w-full pl-9 sm:pl-10 pr-3 py-2.5 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg sm:rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors duration-200 bg-white shadow-sm">
            </div>
        </div>

        {{-- Dynamic Selects (Tetap sama) --}}
        @foreach ($selects as $select)
            <div class="sm:col-span-1">
                <select x-model="$store.table.filters['{{ $select['name'] }}']"
                    class="block w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg sm:rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors duration-200 bg-white shadow-sm">
                    <option value="">{{ $select['label'] }}</option>
                    @foreach ($select['options'] as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        @endforeach

        {{-- Date Range (Tetap sama) --}}
        @if ($date)
            <div class="lg:col-span-2 xl:col-span-1">
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                <input type="date" name="start_date" x-model="$store.table.start_date"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors"
                    value="{{ request('start_date') }}">
            </div>

            <div class="lg:col-span-2 xl:col-span-1">
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Akhir</label>
                <input type="date" name="end_date" x-model="$store.table.end_date"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors"
                    value="{{ request('end_date') }}">
            </div>
        @endif
    </div>

    {{-- Tombol-tombol di bawah --}}
    <div class="mt-6 flex flex-wrap gap-3 justify-start">

        {{-- TOMBOL CETAK BARU MENGGUNAKAN Alpine.js --}}
        @if ($print)
            <a :href="getPrintUrl()" target="_blank"
                class="inline-flex items-center px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white font-medium rounded-lg transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2z" />
                </svg>
                Cetak Laporan
            </a>
        @endif

        {{-- Export CSV (Opsional) --}}
        @if ($export_csv)
            {{-- Tombol Export CSV masih perlu di-update seperti tombol cetak jika ingin menggunakan filter Alpine --}}
            <a href="{{ route('purchase-receipts.export', array_filter(['status' => request('status'), 'start_date' => request('start_date'), 'end_date' => request('end_date'), 'q' => request('q'), 'sort' => request('sort'), 'direction' => request('direction')])) }}"
                class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Export CSV
            </a>
        @endif

        {{-- Reset Button --}}
        <button type="button" @click="$store.table.reset()"
            class="inline-flex items-center justify-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition-colors duration-200">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            Reset
        </button>
    </div>
</div>
