@props([
    'headers' => [], 
    'data' => null, 
    'actions' => true, 
    'searchable' => true, 
    'filterable' => false, 
    'filters' => [],
    'pagination' => null,
    'sortable' => false,
    'sortColumn' => null,
    'sortDirection' => 'asc',
    'searchPlaceholder' => 'Cari data...',
    'itemName' => 'data'
])

<div class="min-h-screen bg-gradient-to-br from-gray-50 via-orange-50/30 to-red-50/30">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-8">
        <div class="bg-white rounded-lg sm:rounded-2xl shadow-lg sm:shadow-xl border border-gray-200 overflow-hidden">
            
            {{-- Filter Section --}}
            @if($searchable || $filterable)
                <div class="bg-white border-b border-gray-200 p-4 sm:p-6">
                    <form method="GET" class="space-y-4 sm:space-y-0 sm:flex sm:items-center sm:justify-between">
                        @if($searchable)
                            <div class="flex-1 max-w-lg">
                                <div class="relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                        </svg>
                                    </div>
                                    <input type="text" name="q" 
                                           class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent sm:text-sm" 
                                           placeholder="{{ $searchPlaceholder }}" 
                                           value="{{ request('q') }}">
                                </div>
                            </div>
                        @endif
                        
                        @if($filterable && count($filters) > 0)
                            <div class="flex flex-wrap gap-3 sm:ml-4">
                                @foreach($filters as $filter)
                                    <div class="min-w-0 flex-1 sm:flex-none sm:w-48">
                                        <select name="{{ $filter['name'] }}" 
                                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent" 
                                                onchange="this.form.submit()">
                                            <option value="">{{ $filter['label'] }}</option>
                                            @foreach($filter['options'] as $value => $label)
                                                <option value="{{ $value }}" 
                                                        {{ request($filter['name']) == $value ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endforeach
                                
                                <button type="submit" 
                                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-colors duration-150">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                    Cari
                                </button>
                            </div>
                        @endif
                    </form>
                </div>
            @endif
            
            {{-- Desktop Table --}}
            <div class="hidden lg:block">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                @foreach($headers as $index => $header)
                                    @php
                                        $headerText = is_array($header) ? $header['text'] : $header;
                                        $headerClass = is_array($header) && isset($header['class']) ? $header['class'] : '';
                                        $headerWidth = is_array($header) && isset($header['width']) ? $header['width'] : '';
                                        $headerSort = is_array($header) && isset($header['sort']) ? $header['sort'] : null;
                                        $isSortable = $sortable && $headerSort;
                                    @endphp
                                    <th scope="col" 
                                        @if($headerWidth) width="{{ $headerWidth }}" @endif
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider {{ $headerClass }} {{ $isSortable ? 'cursor-pointer hover:bg-gray-100 select-none' : '' }}"
                                        @if($isSortable)
                                            onclick="window.location='{{ request()->fullUrlWithQuery([
                                                'sort' => $headerSort,
                                                'direction' => ($sortColumn == $headerSort && $sortDirection == 'asc') ? 'desc' : 'asc'
                                            ]) }}'"
                                        @endif
                                    >
                                        <div class="flex items-center space-x-1">
                                            <span>{{ $headerText }}</span>
                                            @if($isSortable)
                                                <span class="flex flex-col">
                                                    @if($sortColumn == $headerSort)
                                                        @if($sortDirection == 'asc')
                                                            <svg class="w-3 h-3 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                                <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/>
                                                            </svg>
                                                        @else
                                                            <svg class="w-3 h-3 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                                <path d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z"/>
                                                            </svg>
                                                        @endif
                                                    @else
                                                        <svg class="w-3 h-3 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/>
                                                        </svg>
                                                    @endif
                                                </span>
                                            @endif
                                        </div>
                                    </th>
                                @endforeach
                                @if($actions)
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Aksi
                                    </th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            {{ $slot }}
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Mobile/Tablet Card Layout --}}
            <div class="lg:hidden">
                {{ $slot }}
            </div>
            
            {{-- Pagination --}}
            @if($pagination)
                <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
                        <div class="text-sm text-gray-700">
                            <span class="font-medium">{{ $pagination->firstItem() ?? 0 }}</span>
                            sampai
                            <span class="font-medium">{{ $pagination->lastItem() ?? 0 }}</span>
                            dari
                            <span class="font-medium">{{ $pagination->total() }}</span>
                            {{ $itemName }}
                            <span class="text-gray-500 ml-2">
                                ({{ $pagination->perPage() }} data per halaman)
                            </span>
                        </div>
                        <div>
                            {{ $pagination->links() }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
