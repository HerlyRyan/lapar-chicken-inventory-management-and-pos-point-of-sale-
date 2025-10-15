@props([
    'searchPlaceholder' => 'Cari data...',
    'searchName' => 'search',
    'selects' => [],
    'action' => '#',
    'resetRoute' => null,
])

<div class="px-4 sm:px-6 py-4 sm:py-6 bg-gray-50 border-b border-gray-200">
    <form method="GET" action="{{ $action }}" class="space-y-3 sm:space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-{{ count($selects) + 2 }} gap-3 sm:gap-4">
            {{-- Search Input --}}
            <div class="sm:col-span-2 lg:col-span-2">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" name="{{ $searchName }}" value="{{ request($searchName) }}"
                        placeholder="{{ $searchPlaceholder }}"
                        class="block w-full pl-9 sm:pl-10 pr-3 py-2.5 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg sm:rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors duration-200 bg-white shadow-sm">
                </div>
            </div>

            {{-- Dynamic Selects --}}
            @foreach ($selects as $select)
                <div class="sm:col-span-1">
                    <select name="{{ $select['name'] }}"
                        class="block w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg sm:rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors duration-200 bg-white shadow-sm">
                        <option value="">{{ $select['label'] }}</option>
                        @foreach ($select['options'] as $value => $label)
                            <option value="{{ $value }}"
                                {{ (string) request($select['name']) === (string) $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endforeach

            {{-- Action Buttons --}}
            <div class="sm:col-span-2 lg:col-span-1 flex gap-2">
                <button type="submit"
                    class="flex-1 inline-flex items-center justify-center px-4 sm:px-6 py-2.5 sm:py-3 bg-gradient-to-r from-orange-500 to-red-600 hover:from-orange-600 hover:to-red-700 text-white font-semibold rounded-lg sm:rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 transform hover:-translate-y-0.5 text-sm sm:text-base">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1 sm:mr-2" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <span class="hidden sm:inline">Cari</span>
                </button>

                <a href="{{ $resetRoute ?: url()->current() }}"
                    class="inline-flex items-center justify-center px-3 sm:px-4 py-2.5 sm:py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg sm:rounded-xl transition-colors duration-200"
                    title="Reset Filter">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </a>
            </div>
        </div>
    </form>
</div>
