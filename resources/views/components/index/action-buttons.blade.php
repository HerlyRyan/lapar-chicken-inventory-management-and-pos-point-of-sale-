@props(['view' => false, 'edit' => false, 'delete' => false, 'toggle' => false])

<div class="flex items-center gap-2 sm:gap-3">

    {{-- View --}}
    @if ($view)
        <a :href="viewUrl"
            class="group relative inline-flex items-center justify-center w-8 h-8 sm:w-9 sm:h-9
                  bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700
                  text-white rounded-lg shadow-sm hover:shadow-md transition-all duration-200">
            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5
                         c4.478 0 8.268 2.943 9.542 7
                         -1.274 4.057-5.064 7-9.542 7
                         -4.477 0-8.268-2.943-9.542-7z" />
            </svg>
        </a>
    @endif

    {{-- Edit --}}
    @if ($edit)
        <a :href="editUrl"
            class="group relative inline-flex items-center justify-center w-8 h-8 sm:w-9 sm:h-9
                  bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700
                  text-white rounded-lg shadow-sm hover:shadow-md transition-all duration-200">
            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11
                         a2 2 0 002-2v-5m-1.414-9.414
                         a2 2 0 112.828 2.828L11.828 15H9v-2.828
                         l8.586-8.586z" />
            </svg>
        </a>
    @endif

    {{-- Delete --}}
    @if ($delete)
        <button type="button" x-on:click="confirmDelete(deleteUrl, itemName)"
            class="group relative inline-flex items-center justify-center w-8 h-8 sm:w-9 sm:h-9
                       bg-gradient-to-r from-red-500 to-rose-600 hover:from-red-600 hover:to-rose-700
                       text-white rounded-lg shadow-sm hover:shadow-md transition-all duration-200">
            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862
                         a2 2 0 01-1.995-1.858L5 7m5 4v6
                         m4-6v6m1-10V4
                         a1 1 0 00-1-1h-4
                         a1 1 0 00-1 1v3M4 7h16" />
            </svg>
        </button>
    @endif
</div>
