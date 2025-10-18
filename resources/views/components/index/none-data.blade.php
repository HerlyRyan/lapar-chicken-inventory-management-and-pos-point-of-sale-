@props(['mobile' => false, 'column_name' => ''])

@if (!$mobile)
    <tr>
        <td colspan="7" class="px-6 py-12 text-center">
            <div class="flex flex-col items-center">
                <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                        d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-1">Belum ada data {{ $column_name }}
                </h3>
                <p class="text-gray-500">Mulai dengan menambahkan {{ $column_name }} baru</p>
            </div>
        </td>
    </tr>
@else
    <div class="px-4 py-12 text-center">
        <div class="flex flex-col items-center">
            <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-1">Belum ada data {{ $column_name }}</h3>
            <p class="text-gray-500 text-sm">Mulai dengan menambahkan {{ $column_name }} baru</p>
        </div>
    </div>
@endif
