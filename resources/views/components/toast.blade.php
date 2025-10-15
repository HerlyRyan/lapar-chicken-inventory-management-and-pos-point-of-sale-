@if (session('success'))
    <!-- Success Toast -->
    <div id="toast-success" class="fixed top-4 right-4 z-50 transform translate-x-0 transition-all duration-300 ease-in-out">
        <div class="bg-white border-l-4 border-green-500 rounded-lg shadow-lg p-4 min-w-80 max-w-sm">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-3 flex-1">
                    <p class="text-sm font-medium text-gray-900">Berhasil!</p>
                    <p class="text-sm text-gray-600 mt-1">{{ session('success') }}</p>
                </div>
                <div class="ml-4 flex-shrink-0">
                    <button type="button" onclick="closeToast('toast-success')" 
                        class="inline-flex text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-green-500 rounded-full p-1 transition-colors duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <!-- Progress bar -->
            <div class="mt-3 w-full bg-gray-200 rounded-full h-1">
                <div id="toast-success-progress" class="bg-green-500 h-1 rounded-full transition-all duration-75 ease-linear" style="width: 100%"></div>
            </div>
        </div>
    </div>

    <script>
        // Auto close success toast after 3 seconds
        document.addEventListener('DOMContentLoaded', function() {
            initializeToast('toast-success', 'toast-success-progress');
        });
    </script>
@endif

@if (session('error'))
    <!-- Error Toast -->
    <div id="toast-error" class="fixed top-4 right-4 z-50 transform translate-x-0 transition-all duration-300 ease-in-out">
        <div class="bg-white border-l-4 border-red-500 rounded-lg shadow-lg p-4 min-w-80 max-w-sm">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <div class="w-6 h-6 bg-red-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-3 flex-1">
                    <p class="text-sm font-medium text-gray-900">Error!</p>
                    <p class="text-sm text-gray-600 mt-1">{{ session('error') }}</p>
                </div>
                <div class="ml-4 flex-shrink-0">
                    <button type="button" onclick="closeToast('toast-error')" 
                        class="inline-flex text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-red-500 rounded-full p-1 transition-colors duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <!-- Progress bar -->
            <div class="mt-3 w-full bg-gray-200 rounded-full h-1">
                <div id="toast-error-progress" class="bg-red-500 h-1 rounded-full transition-all duration-75 ease-linear" style="width: 100%"></div>
            </div>
        </div>
    </div>

    <script>
        // Auto close error toast after 3 seconds
        document.addEventListener('DOMContentLoaded', function() {
            initializeToast('toast-error', 'toast-error-progress');
        });
    </script>
@endif

<script>
    function closeToast(toastId) {
        const toast = document.getElementById(toastId);
        if (toast) {
            toast.style.transform = 'translateX(100%)';
            toast.style.opacity = '0';
            setTimeout(() => {
                toast.remove();
            }, 300);
        }
    }

    function initializeToast(toastId, progressId) {
        const toast = document.getElementById(toastId);
        const progressBar = document.getElementById(progressId);
        
        if (toast && progressBar) {
            let width = 100;
            let interval = setInterval(() => {
                width -= (100 / 30); // 30 frames for 3 seconds (100ms each)
                progressBar.style.width = width + '%';
                
                if (width <= 0) {
                    clearInterval(interval);
                    closeToast(toastId);
                }
            }, 100);
            
            // Pause timer on hover
            toast.addEventListener('mouseenter', () => {
                clearInterval(interval);
                progressBar.style.animationPlayState = 'paused';
            });
            
            // Resume timer on mouse leave
            toast.addEventListener('mouseleave', () => {
                const currentWidth = parseFloat(progressBar.style.width);
                if (currentWidth > 0) {
                    interval = setInterval(() => {
                        width -= (100 / 30);
                        progressBar.style.width = width + '%';
                        
                        if (width <= 0) {
                            clearInterval(interval);
                            closeToast(toastId);
                        }
                    }, 100);
                }
            });
        }
    }
</script>
