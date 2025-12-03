@props([
    'title' => 'Tambah/Edit',
    'edit' => false,
    'name' => 'What',
    'backRoute' => '#',
    'detailRoute' => '#',
])

<div class="mb-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="min-w-0 flex-1">
            <div class="flex items-center gap-3 mb-2">
                <div
                    class="w-10 h-10 bg-gradient-to-br from-orange-500 to-red-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="bi bi-building text-white text-lg"></i>
                </div>
                <div>
                    @if ($edit)
                        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">
                            Edit {{ $title }}
                        </h1>
                    @else
                        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">
                            Tambah {{ $title }} Baru
                        </h1>
                    @endif
                    @if ($edit)
                        <p class="text-sm text-gray-600 mt-1">
                            Perbarui Informasi {{ $title }} <span
                                class="font-semibold text-gray-800">{{ $name }}</span>
                        </p>
                    @else
                        <p class="text-sm text-gray-600 mt-1">
                            Tambahkan {{ $title }} Baru
                        </p>
                    @endif
                </div>
            </div>
        </div>
        <div class="flex-shrink-0 flex gap-3">
            @if ($edit)
                <a href="{{ $detailRoute }}"
                    class="inline-flex items-center px-4 py-2.5 bg-blue-50 border border-blue-200 rounded-xl text-sm font-medium text-blue-700 hover:bg-blue-100 hover:border-blue-300 transition-all duration-200 shadow-sm hover:shadow-md">
                    <i class="bi bi-eye mr-2"></i> Lihat Detail
                </a>
            @endif
            <a href="{{ $backRoute }}"
                class="inline-flex items-center px-4 py-2.5 bg-white border border-gray-300 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 shadow-sm hover:shadow-md">
                <i class="bi bi-arrow-left mr-2"></i> Kembali
            </a>
        </div>
    </div>
</div>
