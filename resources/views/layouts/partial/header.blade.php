<!-- Header -->
<nav class="bg-orange-800 shadow-lg fixed top-0 left-0 right-0 z-50">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between h-16">
            <!-- Logo and Brand -->
            <div class="flex items-center">
                <a href="/" class="flex items-center">
                    <img src="{{ asset('img/Logo.png') }}" alt="Logo Lapar Chicken" 
                         class="h-10 w-auto bg-white rounded shadow p-1 mr-3">
                    <span class="font-bold text-lg text-white">Lapar Chicken Inventory & Sales</span>
                </a>
                
                <!-- Sidebar Toggle Button (Desktop) -->
                <button class="ml-4 text-white hover:text-orange-300 hidden lg:block" id="sidebarToggle">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M3 12h18M3 6h18M3 18h18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </button>
            </div>

            <!-- Mobile Menu Button -->
            <button class="lg:hidden text-white hover:text-orange-300" type="button" onclick="toggleMobileMenu()">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M3 12h18M3 6h18M3 18h18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </button>

            <!-- Desktop Navigation -->
            <div class="hidden lg:flex lg:items-center lg:space-x-6">
                <!-- Branch Selector for Super Admin -->
                @if (isset($showBranchSelector) && $showBranchSelector && isset($branches) && $branches->count() > 0)
                    <div class="relative">
                        <button class="flex items-center text-white font-semibold hover:text-orange-300 focus:outline-none" 
                                onclick="toggleBranchDropdown()">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2L2 7v10c0 5.55 3.84 10 9 11 1.16.21 2.76.21 3.91 0C20.16 27 24 22.55 24 17V7l-10-5z"/>
                            </svg>
                            @if (isset($selectedBranch) && $selectedBranch)
                                {{ $selectedBranch->name }}
                                <span class="ml-2 px-2 py-1 bg-white text-orange-800 text-xs rounded">{{ $selectedBranch->code }}</span>
                            @else
                                <span class="text-yellow-400">
                                    <svg class="w-4 h-4 mr-1 inline" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M3 3h18v4H3zM3 9h18v4H3zM3 15h18v4H3z"/>
                                    </svg>
                                    Semua Cabang
                                </span>
                            @endif
                            <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M7 10l5 5 5-5z"/>
                            </svg>
                        </button>
                        
                        <div id="branchDropdown" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl z-50">
                            <div class="py-2">
                                <div class="px-4 py-2 text-sm font-semibold text-blue-600 border-b">
                                    <svg class="w-4 h-4 mr-1 inline" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2L2 7v10c0 5.55 3.84 10 9 11 1.16.21 2.76.21 3.91 0C20.16 27 24 22.55 24 17V7l-10-5z"/>
                                    </svg>
                                    Pilih Cabang untuk Ditampilkan
                                </div>

                                <!-- Overview All Branches Option -->
                                <a href="{{ route('dashboard', ['clear_dashboard_branch' => 1]) }}" 
                                   class="flex items-center justify-between px-4 py-3 hover:bg-orange-50 {{ !isset($selectedBranch) || !$selectedBranch ? 'bg-orange-50 border-l-4 border-orange-500' : '' }}">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-orange-500" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M3 3h18v4H3zM3 9h18v4H3zM3 15h18v4H3z"/>
                                        </svg>
                                        <span class="font-semibold text-gray-800">Overview Semua Cabang</span>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="text-xs text-gray-500 mr-2">Ringkasan</span>
                                        @if (!isset($selectedBranch) || !$selectedBranch)
                                            <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                                            </svg>
                                        @endif
                                    </div>
                                </a>

                                <!-- Production Centers -->
                                @php
                                    $productionCenters = $branches->where('type', 'production')->sortBy('code');
                                    $retailBranches = $branches->where('type', 'branch')->sortBy('code');
                                @endphp

                                @if ($productionCenters->count() > 0)
                                    <div class="px-4 py-2 text-sm font-semibold text-yellow-600 bg-yellow-50 border-t">
                                        <svg class="w-4 h-4 mr-1 inline" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                        </svg>
                                        Pusat Produksi
                                    </div>
                                    @foreach ($productionCenters as $branch)
                                        <a href="{{ route('dashboard', ['branch_id' => $branch->id]) }}" 
                                           class="flex items-center justify-between px-4 py-3 hover:bg-orange-50 {{ isset($selectedBranch) && $selectedBranch && $selectedBranch->id == $branch->id ? 'bg-orange-50 border-l-4 border-orange-500' : '' }}">
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 mr-2 text-yellow-500" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                                </svg>
                                                <span class="text-gray-800">{{ $branch->name }}</span>
                                            </div>
                                            <div class="flex items-center">
                                                <span class="text-xs text-gray-500 mr-2">{{ $branch->code }}</span>
                                                @if (isset($selectedBranch) && $selectedBranch && $selectedBranch->id == $branch->id)
                                                    <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                                                    </svg>
                                                @endif
                                            </div>
                                        </a>
                                    @endforeach
                                @endif

                                <!-- Retail Branches -->
                                @if ($retailBranches->count() > 0)
                                    <div class="px-4 py-2 text-sm font-semibold text-blue-600 bg-blue-50 border-t">
                                        <svg class="w-4 h-4 mr-1 inline" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M19 7h-3V6a4 4 0 0 0-8 0v1H5a1 1 0 0 0-1 1v11a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3V8a1 1 0 0 0-1-1zM10 6a2 2 0 0 1 4 0v1h-4V6zm8 13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V9h2v1a1 1 0 0 0 2 0V9h4v1a1 1 0 0 0 2 0V9h2v10z"/>
                                        </svg>
                                        Cabang Retail
                                    </div>
                                    @foreach ($retailBranches as $branch)
                                        <a href="{{ route('dashboard', ['branch_id' => $branch->id]) }}" 
                                           class="flex items-center justify-between px-4 py-3 hover:bg-orange-50 {{ isset($selectedBranch) && $selectedBranch && $selectedBranch->id == $branch->id ? 'bg-orange-50 border-l-4 border-orange-500' : '' }}">
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 mr-2 text-blue-500" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M19 7h-3V6a4 4 0 0 0-8 0v1H5a1 1 0 0 0-1 1v11a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3V8a1 1 0 0 0-1-1zM10 6a2 2 0 0 1 4 0v1h-4V6zm8 13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V9h2v1a1 1 0 0 0 2 0V9h4v1a1 1 0 0 0 2 0V9h2v10z"/>
                                                </svg>
                                                <span class="text-gray-800">{{ $branch->name }}</span>
                                            </div>
                                            <div class="flex items-center">
                                                <span class="text-xs text-gray-500 mr-2">{{ $branch->code }}</span>
                                                @if (isset($selectedBranch) && $selectedBranch && $selectedBranch->id == $branch->id)
                                                    <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                                                    </svg>
                                                @endif
                                            </div>
                                        </a>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                <!-- User Menu -->
                <div class="relative">
                    @guest
                        <div class="flex space-x-4">
                            @if (Route::has('login'))
                                <a href="{{ route('login') }}" class="text-white hover:text-orange-300">{{ __('Login') }}</a>
                            @endif
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="text-white hover:text-orange-300">{{ __('Register') }}</a>
                            @endif
                        </div>
                    @else
                        <button class="flex items-center text-white hover:text-orange-300 focus:outline-none" 
                                onclick="toggleUserDropdown()">
                            @if (Auth::check())
                                {{ Auth::user()->name }}
                            @else
                                Developer Mode
                            @endif
                            <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M7 10l5 5 5-5z"/>
                            </svg>
                        </button>
                        
                        <div id="userDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl z-50">
                            <div class="py-2">
                                <a href="{{ route('logout') }}" 
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                   class="block px-4 py-2 text-gray-800 hover:bg-orange-50">
                                    {{ __('Logout') }}
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                                    @csrf
                                </form>
                            </div>
                        </div>
                    @endguest
                </div>
            </div>
        </div>

        <!-- Mobile Navigation -->
        <div id="mobileMenu" class="hidden lg:hidden">
            <div class="px-2 pt-2 pb-3 space-y-1 border-t border-orange-700">
                <!-- Branch Selector Mobile -->
                @if (isset($showBranchSelector) && $showBranchSelector && isset($branches) && $branches->count() > 0)
                    <div class="pb-3 border-b border-orange-700">
                        <div class="text-white font-semibold mb-2">Pilih Cabang:</div>
                        
                        <!-- Overview Option -->
                        <a href="{{ route('dashboard', ['clear_dashboard_branch' => 1]) }}" 
                           class="block px-3 py-2 rounded-md text-white hover:bg-orange-700 {{ !isset($selectedBranch) || !$selectedBranch ? 'bg-orange-700' : '' }}">
                            Overview Semua Cabang
                        </a>

                        <!-- Production Centers -->
                        @if ($productionCenters->count() > 0)
                            <div class="text-yellow-400 text-sm font-semibold mt-3 mb-1">Pusat Produksi:</div>
                            @foreach ($productionCenters as $branch)
                                <a href="{{ route('dashboard', ['branch_id' => $branch->id]) }}" 
                                   class="block px-3 py-2 rounded-md text-white hover:bg-orange-700 {{ isset($selectedBranch) && $selectedBranch && $selectedBranch->id == $branch->id ? 'bg-orange-700' : '' }}">
                                    {{ $branch->name }} ({{ $branch->code }})
                                </a>
                            @endforeach
                        @endif

                        <!-- Retail Branches -->
                        @if ($retailBranches->count() > 0)
                            <div class="text-blue-400 text-sm font-semibold mt-3 mb-1">Cabang Retail:</div>
                            @foreach ($retailBranches as $branch)
                                <a href="{{ route('dashboard', ['branch_id' => $branch->id]) }}" 
                                   class="block px-3 py-2 rounded-md text-white hover:bg-orange-700 {{ isset($selectedBranch) && $selectedBranch && $selectedBranch->id == $branch->id ? 'bg-orange-700' : '' }}">
                                    {{ $branch->name }} ({{ $branch->code }})
                                </a>
                            @endforeach
                        @endif
                    </div>
                @endif

                <!-- User Menu Mobile -->
                @guest
                    @if (Route::has('login'))
                        <a href="{{ route('login') }}" class="block px-3 py-2 rounded-md text-white hover:bg-orange-700">{{ __('Login') }}</a>
                    @endif
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="block px-3 py-2 rounded-md text-white hover:bg-orange-700">{{ __('Register') }}</a>
                    @endif
                @else
                    <div class="pt-3 border-t border-orange-700">
                        <div class="text-white font-semibold px-3 py-2">
                            @if (Auth::check())
                                {{ Auth::user()->name }}
                            @else
                                Developer Mode
                            @endif
                        </div>
                        <a href="{{ route('logout') }}" 
                           onclick="event.preventDefault(); document.getElementById('logout-form-mobile').submit();"
                           class="block px-3 py-2 rounded-md text-white hover:bg-orange-700">
                            {{ __('Logout') }}
                        </a>
                        <form id="logout-form-mobile" action="{{ route('logout') }}" method="POST" class="hidden">
                            @csrf
                        </form>
                    </div>
                @endguest
            </div>
        </div>
    </div>
</nav>

<script>
function toggleMobileMenu() {
    const menu = document.getElementById('mobileMenu');
    menu.classList.toggle('hidden');
}

function toggleBranchDropdown() {
    const dropdown = document.getElementById('branchDropdown');
    dropdown.classList.toggle('hidden');
}

function toggleUserDropdown() {
    const dropdown = document.getElementById('userDropdown');
    dropdown.classList.toggle('hidden');
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(event) {
    const branchDropdown = document.getElementById('branchDropdown');
    const userDropdown = document.getElementById('userDropdown');
    
    if (branchDropdown && !event.target.closest('.relative')) {
        branchDropdown.classList.add('hidden');
    }
    
    if (userDropdown && !event.target.closest('.relative')) {
        userDropdown.classList.add('hidden');
    }
});
</script>
