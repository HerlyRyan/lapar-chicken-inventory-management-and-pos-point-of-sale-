@props([
    'viewUrl' => '',
    'editUrl' => '',
    'deleteUrl' => '',
    'toggleUrl' => '',
    'deleteId' => '',
    'toggleId' => '',
    'isActive' => null,
    'showView' => true,
    'showEdit' => true,
    'showDelete' => false,
    'showToggle' => false,
    'itemName' => 'item'
])

<div class="flex items-center gap-2 sm:gap-3">
    @if($showView && $viewUrl)
        <a href="{{ $viewUrl }}" 
           class="group relative inline-flex items-center justify-center w-8 h-8 sm:w-9 sm:h-9 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white rounded-lg shadow-sm hover:shadow-md transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:ring-offset-1"
           title="Detail">
            <svg class="w-4 h-4 sm:w-5 sm:h-5 transition-transform duration-200 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
            {{-- Tooltip --}}
            <div class="absolute -top-10 left-1/2 transform -translate-x-1/2 px-2 py-1 bg-gray-900 text-white text-xs rounded-md opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none whitespace-nowrap z-50">
                Detail
                <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-2 border-r-2 border-t-2 border-transparent border-t-gray-900"></div>
            </div>
        </a>
    @endif

    @if($showEdit && $editUrl)
        <a href="{{ $editUrl }}" 
           class="group relative inline-flex items-center justify-center w-8 h-8 sm:w-9 sm:h-9 bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white rounded-lg shadow-sm hover:shadow-md transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30 focus:ring-offset-1"
           title="Edit">
            <svg class="w-4 h-4 sm:w-5 sm:h-5 transition-transform duration-200 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            {{-- Tooltip --}}
            <div class="absolute -top-10 left-1/2 transform -translate-x-1/2 px-2 py-1 bg-gray-900 text-white text-xs rounded-md opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none whitespace-nowrap z-50">
                Edit
                <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-2 border-r-2 border-t-2 border-transparent border-t-gray-900"></div>
            </div>
        </a>
    @endif
    
    @if($showToggle && $toggleUrl && !is_null($isActive))
        <form action="{{ $toggleUrl }}" method="POST" class="inline-block">
            @csrf
            @method('PATCH')
            <input type="hidden" name="is_active" value="{{ $isActive ? 0 : 1 }}">
            <button type="submit" 
                    class="group relative inline-flex items-center justify-center w-8 h-8 sm:w-9 sm:h-9 {{ $isActive ? 'bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700' : 'bg-gradient-to-r from-gray-400 to-gray-500 hover:from-gray-500 hover:to-gray-600' }} text-white rounded-lg shadow-sm hover:shadow-md transition-all duration-200 focus:outline-none focus:ring-2 {{ $isActive ? 'focus:ring-green-500/30' : 'focus:ring-gray-500/30' }} focus:ring-offset-1"
                    title="{{ $isActive ? 'Nonaktifkan' : 'Aktifkan' }}">
                @if($isActive)
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 transition-transform duration-200 group-hover:scale-110" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17 7H7c-2.76 0-5 2.24-5 5s2.24 5 5 5h10c2.76 0 5-2.24 5-5s-2.24-5-5-5zM17 15c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3z"/>
                    </svg>
                @else
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 transition-transform duration-200 group-hover:scale-110" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17 7H7c-2.76 0-5 2.24-5 5s2.24 5 5 5h10c2.76 0 5-2.24 5-5s-2.24-5-5-5zM7 15c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3z"/>
                    </svg>
                @endif
                {{-- Tooltip --}}
                <div class="absolute -top-10 left-1/2 transform -translate-x-1/2 px-2 py-1 bg-gray-900 text-white text-xs rounded-md opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none whitespace-nowrap z-50">
                    {{ $isActive ? 'Nonaktifkan' : 'Aktifkan' }}
                    <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-2 border-r-2 border-t-2 border-transparent border-t-gray-900"></div>
                </div>
            </button>
        </form>
    @endif

    @if($showDelete && $deleteUrl)
        <button type="button" 
                class="group relative inline-flex items-center justify-center w-8 h-8 sm:w-9 sm:h-9 bg-gradient-to-r from-red-500 to-rose-600 hover:from-red-600 hover:to-rose-700 text-white rounded-lg shadow-sm hover:shadow-md transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-red-500/30 focus:ring-offset-1"
                onclick="confirmDelete('{{ $deleteUrl }}', '{{ $itemName }}')" 
                title="Hapus">
            <svg class="w-4 h-4 sm:w-5 sm:h-5 transition-transform duration-200 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
            {{-- Tooltip --}}
            <div class="absolute -top-10 left-1/2 transform -translate-x-1/2 px-2 py-1 bg-gray-900 text-white text-xs rounded-md opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none whitespace-nowrap z-50">
                Hapus
                <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-2 border-r-2 border-t-2 border-transparent border-t-gray-900"></div>
            </div>
        </button>
    @endif
</div>
