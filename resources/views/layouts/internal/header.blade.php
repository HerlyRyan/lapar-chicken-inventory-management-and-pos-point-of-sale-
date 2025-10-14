<header class="bg-gradient-to-r from-orange-900 via-orange-800 to-red-900 text-white shadow-lg border-b border-orange-700/30">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 lg:h-18">
            {{-- 1. Logo & Brand --}}
            <div class="flex items-center min-w-0">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
                    <div class="relative">
                        <img src="{{ asset('img/Logo.png') }}" alt="Lapar Chicken" 
                             class="w-10 h-10 rounded-lg shadow-md group-hover:shadow-lg transition-shadow duration-200">
                        <div class="absolute inset-0 bg-white/10 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200"></div>
                    </div>
                    <div class="hidden sm:block">
                        <h1 class="font-bold text-lg lg:text-xl group-hover:text-orange-200 transition-colors duration-200">
                            Lapar Chicken
                        </h1>
                        <p class="text-xs text-orange-200 font-medium">Inventory & Sales</p>
                    </div>
                </a>
            </div>

            {{-- 2. Mobile Menu Toggle --}}
            <div class="lg:hidden">
                <button @click="sidebarOpen = !sidebarOpen" 
                        class="p-2.5 rounded-lg hover:bg-white/10 active:bg-white/20 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-white/20">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>

            {{-- 3. Desktop Navigation & Actions --}}
            <div class="hidden lg:flex items-center space-x-4">
                {{-- Branch Selector --}}
                @if (isset($showBranchSelector) && $showBranchSelector && isset($branches) && $branches->count() > 0)
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" 
                                class="flex items-center px-4 py-2.5 bg-white/10 hover:bg-white/20 rounded-lg transition-all duration-200 backdrop-blur-sm border border-white/20 focus:outline-none focus:ring-2 focus:ring-white/30">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3 text-orange-200" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M19 7h-3V6a4 4 0 0 0-8 0v1H5a1 1 0 0 0-1 1v11a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3V8a1 1 0 0 0-1-1z"/>
                                </svg>
                                <div class="text-left">
                                    @if (isset($selectedBranch) && $selectedBranch)
                                        <div class="font-semibold text-sm">{{ $selectedBranch->name }}</div>
                                        <div class="text-xs text-orange-200">{{ $selectedBranch->code }}</div>
                                    @else
                                        <div class="font-semibold text-sm">Semua Cabang</div>
                                        <div class="text-xs text-orange-200">Overview</div>
                                    @endif
                                </div>
                            </div>
                            <svg class="w-4 h-4 ml-3 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M7 10l5 5 5-5z"/>
                            </svg>
                        </button>
                        
                        <div x-show="open" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             @click.away="open = false" 
                             class="absolute right-0 mt-3 w-80 bg-white rounded-xl shadow-2xl z-50 border border-gray-200 overflow-hidden">
                            
                            {{-- Header --}}
                            <div class="px-6 py-4 bg-gradient-to-r from-orange-50 to-red-50 border-b border-gray-100">
                                <h3 class="text-sm font-semibold text-gray-800 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-orange-600" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M19 7h-3V6a4 4 0 0 0-8 0v1H5a1 1 0 0 0-1 1v11a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3V8a1 1 0 0 0-1-1z"/>
                                    </svg>
                                    Pilih Lokasi Cabang
                                </h3>
                            </div>

                            <div class="max-h-80 overflow-y-auto">
                                {{-- Overview Option --}}
                                <a href="{{ route('dashboard', ['clear_dashboard_branch' => 1]) }}" 
                                   class="flex items-center justify-between px-6 py-4 hover:bg-orange-50 transition-colors duration-150 {{ !isset($selectedBranch) || !$selectedBranch ? 'bg-orange-50 border-l-4 border-orange-500' : '' }}">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-gradient-to-br from-orange-400 to-red-500 rounded-lg flex items-center justify-center mr-3">
                                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M3 3h18v4H3zM3 9h18v4H3zM3 15h18v4H3z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="font-semibold text-gray-800">Overview Semua</div>
                                            <div class="text-xs text-gray-500">Ringkasan semua cabang</div>
                                        </div>
                                    </div>
                                    @if (!isset($selectedBranch) || !$selectedBranch)
                                        <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center">
                                            <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                                            </svg>
                                        </div>
                                    @endif
                                </a>

                                @php
                                    $productionCenters = $branches->where('type', 'production')->sortBy('code');
                                    $retailBranches = $branches->where('type', 'branch')->sortBy('code');
                                @endphp

                                {{-- Production Centers --}}
                                @if ($productionCenters->count() > 0)
                                    <div class="px-6 py-3 bg-yellow-50 border-t border-gray-100">
                                        <div class="flex items-center text-sm font-semibold text-yellow-700">
                                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                            </svg>
                                            Pusat Produksi
                                        </div>
                                    </div>
                                    @foreach ($productionCenters as $branch)
                                        <a href="{{ route('dashboard', ['branch_id' => $branch->id]) }}" 
                                           class="flex items-center justify-between px-6 py-4 hover:bg-orange-50 transition-colors duration-150 {{ isset($selectedBranch) && $selectedBranch && $selectedBranch->id == $branch->id ? 'bg-orange-50 border-l-4 border-orange-500' : '' }}">
                                            <div class="flex items-center">
                                                <div class="w-10 h-10 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-lg flex items-center justify-center mr-3">
                                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <div class="font-semibold text-gray-800">{{ $branch->name }}</div>
                                                    <div class="text-xs text-gray-500">{{ $branch->code }}</div>
                                                </div>
                                            </div>
                                            @if (isset($selectedBranch) && $selectedBranch && $selectedBranch->id == $branch->id)
                                                <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center">
                                                    <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                                                    </svg>
                                                </div>
                                            @endif
                                        </a>
                                    @endforeach
                                @endif

                                {{-- Retail Branches --}}
                                @if ($retailBranches->count() > 0)
                                    <div class="px-6 py-3 bg-blue-50 border-t border-gray-100">
                                        <div class="flex items-center text-sm font-semibold text-blue-700">
                                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M19 7h-3V6a4 4 0 0 0-8 0v1H5a1 1 0 0 0-1 1v11a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3V8a1 1 0 0 0-1-1z"/>
                                            </svg>
                                            Cabang Retail
                                        </div>
                                    </div>
                                    @foreach ($retailBranches as $branch)
                                        <a href="{{ route('dashboard', ['branch_id' => $branch->id]) }}" 
                                           class="flex items-center justify-between px-6 py-4 hover:bg-orange-50 transition-colors duration-150 {{ isset($selectedBranch) && $selectedBranch && $selectedBranch->id == $branch->id ? 'bg-orange-50 border-l-4 border-orange-500' : '' }}">
                                            <div class="flex items-center">
                                                <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-lg flex items-center justify-center mr-3">
                                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M19 7h-3V6a4 4 0 0 0-8 0v1H5a1 1 0 0 0-1 1v11a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3V8a1 1 0 0 0-1-1z"/>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <div class="font-semibold text-gray-800">{{ $branch->name }}</div>
                                                    <div class="text-xs text-gray-500">{{ $branch->code }}</div>
                                                </div>
                                            </div>
                                            @if (isset($selectedBranch) && $selectedBranch && $selectedBranch->id == $branch->id)
                                                <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center">
                                                    <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                                                    </svg>
                                                </div>
                                            @endif
                                        </a>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                {{-- User Menu --}}
                @guest
                    <div class="flex items-center space-x-3">
                        @if (Route::has('login'))
                            <a href="{{ route('login') }}" 
                               class="px-4 py-2 text-sm font-medium text-white hover:text-orange-200 transition-colors duration-200">
                                {{ __('Login') }}
                            </a>
                        @endif
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" 
                               class="px-4 py-2 bg-white text-orange-800 rounded-lg font-medium text-sm hover:bg-orange-50 transition-colors duration-200 shadow-sm">
                                {{ __('Register') }}
                            </a>
                        @endif
                    </div>
                @else
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" 
                                class="flex items-center px-4 py-2.5 bg-white/10 hover:bg-white/20 rounded-lg transition-all duration-200 backdrop-blur-sm border border-white/20 focus:outline-none focus:ring-2 focus:ring-white/30">
                            <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                </svg>
                            </div>
                            <div class="text-left mr-3">
                                <div class="font-semibold text-sm">
                                    @if (Auth::check())
                                        {{ Auth::user()->name }}
                                    @else
                                        Developer Mode
                                    @endif
                                </div>
                                <div class="text-xs text-orange-200">Administrator</div>
                            </div>
                            <svg class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M7 10l5 5 5-5z"/>
                            </svg>
                        </button>
                        
                        <div x-show="open" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             @click.away="open = false" 
                             class="absolute right-0 mt-3 w-56 bg-white rounded-xl shadow-2xl z-50 border border-gray-200 overflow-hidden">
                            <div class="py-2">
                                <div class="px-4 py-3 border-b border-gray-100">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-gradient-to-br from-orange-400 to-red-500 rounded-full flex items-center justify-center mr-3">
                                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="font-semibold text-gray-800">
                                                @if (Auth::check())
                                                    {{ Auth::user()->name }}
                                                @else
                                                    Developer Mode
                                                @endif
                                            </div>
                                            <div class="text-xs text-gray-500">Administrator</div>
                                        </div>
                                    </div>
                                </div>
                                <a href="{{ route('logout') }}" 
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                   class="flex items-center px-4 py-3 text-gray-700 hover:bg-red-50 hover:text-red-600 transition-colors duration-150">
                                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                    </svg>
                                    {{ __('Logout') }}
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                                    @csrf
                                </form>
                            </div>
                        </div>
                    </div>
                @endguest
            </div>
        </div>

        {{-- Mobile Branch Selector & User Menu --}}
        <div class="lg:hidden border-t border-white/20 py-3" x-data="{ branchOpen: false, userOpen: false }">
            <div class="flex items-center justify-between space-x-3">
                {{-- Mobile Branch Selector --}}
                @if (isset($showBranchSelector) && $showBranchSelector && isset($branches) && $branches->count() > 0)
                    <div class="flex-1">
                        <button @click="branchOpen = !branchOpen" 
                                class="w-full flex items-center justify-between px-3 py-2 bg-white/10 hover:bg-white/20 rounded-lg transition-colors duration-200 text-sm">
                            <div class="flex items-center min-w-0">
                                <svg class="w-4 h-4 mr-2 flex-shrink-0 text-orange-200" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M19 7h-3V6a4 4 0 0 0-8 0v1H5a1 1 0 0 0-1 1v11a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3V8a1 1 0 0 0-1-1z"/>
                                </svg>
                                <span class="truncate">
                                    @if (isset($selectedBranch) && $selectedBranch)
                                        {{ $selectedBranch->name }}
                                    @else
                                        Semua Cabang
                                    @endif
                                </span>
                            </div>
                            <svg class="w-4 h-4 ml-2 flex-shrink-0 transition-transform duration-200" :class="branchOpen ? 'rotate-180' : ''" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M7 10l5 5 5-5z"/>
                            </svg>
                        </button>
                        
                        {{-- Mobile Branch Dropdown --}}
                        <div x-show="branchOpen" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 -translate-y-2"
                             @click.away="branchOpen = false" 
                             class="absolute left-4 right-4 mt-2 bg-white rounded-xl shadow-2xl z-50 border border-gray-200 max-h-80 overflow-y-auto">
                            
                            {{-- Same dropdown content as desktop but optimized for mobile --}}
                            <div class="px-4 py-3 bg-gradient-to-r from-orange-50 to-red-50 border-b border-gray-100">
                                <h3 class="text-sm font-semibold text-gray-800">Pilih Lokasi</h3>
                            </div>

                            {{-- Overview Option --}}
                            <a href="{{ route('dashboard', ['clear_dashboard_branch' => 1]) }}" 
                               class="flex items-center px-4 py-3 hover:bg-orange-50 {{ !isset($selectedBranch) || !$selectedBranch ? 'bg-orange-50 border-l-4 border-orange-500' : '' }}">
                                <div class="w-8 h-8 bg-gradient-to-br from-orange-400 to-red-500 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M3 3h18v4H3zM3 9h18v4H3zM3 15h18v4H3z"/>
                                    </svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="font-medium text-gray-800 truncate">Overview Semua</div>
                                    <div class="text-xs text-gray-500">Ringkasan</div>
                                </div>
                            </a>

                            {{-- Production & Retail branches with same structure but mobile-optimized --}}
                            @if ($productionCenters->count() > 0)
                                <div class="px-4 py-2 bg-yellow-50 border-t border-gray-100">
                                    <div class="text-xs font-semibold text-yellow-700">Pusat Produksi</div>
                                </div>
                                @foreach ($productionCenters as $branch)
                                    <a href="{{ route('dashboard', ['branch_id' => $branch->id]) }}" 
                                       class="flex items-center px-4 py-3 hover:bg-orange-50 {{ isset($selectedBranch) && $selectedBranch && $selectedBranch->id == $branch->id ? 'bg-orange-50 border-l-4 border-orange-500' : '' }}">
                                        <div class="w-8 h-8 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-lg flex items-center justify-center mr-3">
                                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                            </svg>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div class="font-medium text-gray-800 truncate">{{ $branch->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $branch->code }}</div>
                                        </div>
                                    </a>
                                @endforeach
                            @endif

                            @if ($retailBranches->count() > 0)
                                <div class="px-4 py-2 bg-blue-50 border-t border-gray-100">
                                    <div class="text-xs font-semibold text-blue-700">Cabang Retail</div>
                                </div>
                                @foreach ($retailBranches as $branch)
                                    <a href="{{ route('dashboard', ['branch_id' => $branch->id]) }}" 
                                       class="flex items-center px-4 py-3 hover:bg-orange-50 {{ isset($selectedBranch) && $selectedBranch && $selectedBranch->id == $branch->id ? 'bg-orange-50 border-l-4 border-orange-500' : '' }}">
                                        <div class="w-8 h-8 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-lg flex items-center justify-center mr-3">
                                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M19 7h-3V6a4 4 0 0 0-8 0v1H5a1 1 0 0 0-1 1v11a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3V8a1 1 0 0 0-1-1z"/>
                                            </svg>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div class="font-medium text-gray-800 truncate">{{ $branch->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $branch->code }}</div>
                                        </div>
                                    </a>
                                @endforeach
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Mobile User Menu --}}
                @guest
                    <div class="flex items-center space-x-2">
                        @if (Route::has('login'))
                            <a href="{{ route('login') }}" 
                               class="px-3 py-2 text-xs font-medium text-white hover:text-orange-200">Login</a>
                        @endif
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" 
                               class="px-3 py-2 bg-white text-orange-800 rounded-lg font-medium text-xs hover:bg-orange-50">Register</a>
                        @endif
                    </div>
                @else
                    <div class="relative">
                        <button @click="userOpen = !userOpen" 
                                class="flex items-center px-3 py-2 bg-white/10 hover:bg-white/20 rounded-lg transition-colors duration-200">
                            <div class="w-6 h-6 bg-white/20 rounded-full flex items-center justify-center mr-2">
                                <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                </svg>
                            </div>
                            <span class="text-sm font-medium truncate max-w-24">
                                @if (Auth::check())
                                    {{ Auth::user()->name }}
                                @else
                                    Dev
                                @endif
                            </span>
                            <svg class="w-3 h-3 ml-1 transition-transform duration-200" :class="userOpen ? 'rotate-180' : ''" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M7 10l5 5 5-5z"/>
                            </svg>
                        </button>
                        
                        <div x-show="userOpen" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             @click.away="userOpen = false" 
                             class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-2xl z-50 border border-gray-200">
                            <div class="py-2">
                                <a href="{{ route('logout') }}" 
                                   onclick="event.preventDefault(); document.getElementById('logout-form-mobile').submit();"
                                   class="flex items-center px-4 py-3 text-gray-700 hover:bg-red-50 hover:text-red-600">
                                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                    </svg>
                                    Logout
                                </a>
                                <form id="logout-form-mobile" action="{{ route('logout') }}" method="POST" class="hidden">
                                    @csrf
                                </form>
                            </div>
                        </div>
                    </div>
                @endguest
            </div>
        </div>
    </div>
</header>
