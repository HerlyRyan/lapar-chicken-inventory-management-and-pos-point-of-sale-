@props([
    'title' => 'Unknowmn',
])

<div class="bg-gradient-to-r from-orange-600 via-orange-700 to-red-700 px-4 sm:px-6 py-4 sm:py-6">
    <div class="flex items-center">
        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white mr-2 sm:mr-3" fill="currentColor" viewBox="0 0 24 24">
            <path
                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
        </svg>
        <h2 class="text-lg sm:text-xl font-bold text-white">Daftar {{ $title }}</h2>
    </div>
</div>
