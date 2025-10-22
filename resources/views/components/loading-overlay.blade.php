<div 
    x-data="{ show: false }"
    x-show="show"
    x-transition.opacity.duration.300ms
    x-on:loading:start.window="show = true"
    x-on:loading:end.window="show = false"
    x-cloak
    class="fixed inset-0 bg-gradient-to-br from-gray-900/60 via-gray-800/70 to-gray-900/60 backdrop-blur-sm flex items-center justify-center z-50"
>
    <div class="flex flex-col items-center p-8 bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl border border-white/20 max-w-sm mx-4">
        <!-- Loading Icon Container -->
        <div class="relative mb-6">
            <!-- Outer Ring -->
            <div class="w-16 h-16 border-4 border-orange-200/30 rounded-full"></div>
            <!-- Spinning Ring -->
            <div class="absolute top-0 left-0 w-16 h-16 border-4 border-transparent border-t-orange-500 border-r-orange-400 rounded-full animate-spin"></div>
            <!-- Inner Pulse -->
            <div class="absolute top-2 left-2 w-12 h-12 bg-gradient-to-br from-orange-400/20 to-red-400/20 rounded-full animate-pulse"></div>
            <!-- Center Icon -->
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
                <svg class="w-6 h-6 text-orange-600" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                </svg>
            </div>
        </div>

        <!-- Loading Text -->
        <div class="text-center">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Memuat Data</h3>
            <p class="text-sm text-gray-600 animate-pulse">Mohon tunggu sebentar...</p>
        </div>

        <!-- Progress Dots -->
        <div class="flex items-center space-x-1 mt-4">
            <div class="w-2 h-2 bg-orange-400 rounded-full animate-bounce" style="animation-delay: 0ms;"></div>
            <div class="w-2 h-2 bg-orange-500 rounded-full animate-bounce" style="animation-delay: 150ms;"></div>
            <div class="w-2 h-2 bg-orange-600 rounded-full animate-bounce" style="animation-delay: 300ms;"></div>
        </div>
    </div>
</div>
