<!-- Header -->
<nav class="navbar navbar-expand-lg navbar-dark shadow fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center" href="/">
            <img src="{{ asset('img/Logo.png') }}" alt="Logo Lapar Chicken" class="me-2 bg-white rounded shadow p-1"
                style="height:40px;">
            <span class="fw-bold fs-5 text-white">Lapar Chicken Inventory & Sales</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar"
            aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <button class="btn btn-link text-white d-none d-lg-block" id="sidebarToggle">
            <i class="bi bi-list"></i>
        </button>
        <div class="collapse navbar-collapse" id="mainNavbar">
            <!-- Branch Selector for Super Admin -->
            @if (isset($showBranchSelector) && $showBranchSelector && isset($branches) && $branches->count() > 0)
                <div class="navbar-nav me-auto">
                    <div class="nav-item dropdown branch-selector">
                        <a class="nav-link dropdown-toggle text-white fw-bold" href="#" id="branchSelector"
                            role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-building me-1"></i>
                            @if (isset($selectedBranch) && $selectedBranch)
                                {{ $selectedBranch->name }}
                                <span class="badge bg-light text-dark ms-2">{{ $selectedBranch->code }}</span>
                            @else
                                <span class="text-warning">
                                    <i class="bi bi-grid-3x3-gap me-1"></i>Semua Cabang
                                </span>
                            @endif
                        </a>
                        <ul class="dropdown-menu shadow-lg" aria-labelledby="branchSelector">
                            <li>
                                <h6 class="dropdown-header text-primary"><i class="bi bi-buildings me-1"></i>Pilih
                                    Cabang untuk Ditampilkan</h6>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>

                            <!-- Overview All Branches Option -->
                            <li>
                                <a class="dropdown-item d-flex justify-content-between align-items-center {{ !isset($selectedBranch) || !$selectedBranch ? 'active' : '' }}"
                                    href="{{ route('dashboard', ['clear_dashboard_branch' => 1]) }}">
                                    <span>
                                        <i class="bi bi-grid-3x3-gap me-2 text-primary"></i>
                                        <strong>Overview Semua Cabang</strong>
                                    </span>
                                    <small class="text-muted">Ringkasan</small>
                                    @if (!isset($selectedBranch) || !$selectedBranch)
                                        <i class="bi bi-check-circle-fill text-success ms-2"></i>
                                    @endif
                                </a>
                            </li>

                            <li>
                                <hr class="dropdown-divider">
                            </li>

                            <!-- Production Centers First -->
                            @php
                                $productionCenters = $branches->where('type', 'production')->sortBy('code');
                                $retailBranches = $branches->where('type', 'branch')->sortBy('code');
                            @endphp

                            @if ($productionCenters->count() > 0)
                                <li>
                                    <h6 class="dropdown-header text-warning"><i class="bi bi-gear-fill me-1"></i>Pusat
                                        Produksi</h6>
                                </li>
                                @foreach ($productionCenters as $branch)
                                    <li>
                                        <a class="dropdown-item d-flex justify-content-between align-items-center {{ isset($selectedBranch) && $selectedBranch && $selectedBranch->id == $branch->id ? 'active' : '' }}"
                                            href="{{ route('dashboard', ['branch_id' => $branch->id]) }}">
                                            <span>
                                                <i class="bi bi-gear-fill me-2 text-warning"></i>{{ $branch->name }}
                                            </span>
                                            <small class="text-muted">{{ $branch->code }}</small>
                                            @if (isset($selectedBranch) && $selectedBranch && $selectedBranch->id == $branch->id)
                                                <i class="bi bi-check-circle-fill text-success ms-2"></i>
                                            @endif
                                        </a>
                                    </li>
                                @endforeach
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                            @endif

                            <!-- Retail Branches Sorted by Code -->
                            @if ($retailBranches->count() > 0)
                                <li>
                                    <h6 class="dropdown-header text-info"><i class="bi bi-shop me-1"></i>Cabang Retail
                                    </h6>
                                </li>
                                @foreach ($retailBranches as $branch)
                                    <li>
                                        <a class="dropdown-item d-flex justify-content-between align-items-center {{ isset($selectedBranch) && $selectedBranch && $selectedBranch->id == $branch->id ? 'active' : '' }}"
                                            href="{{ route('dashboard', ['branch_id' => $branch->id]) }}">
                                            <span>
                                                <i class="bi bi-shop me-2 text-info"></i>{{ $branch->name }}
                                            </span>
                                            <small class="text-muted">{{ $branch->code }}</small>
                                            @if (isset($selectedBranch) && $selectedBranch && $selectedBranch->id == $branch->id)
                                                <i class="bi bi-check-circle-fill text-success ms-2"></i>
                                            @endif
                                        </a>
                                    </li>
                                @endforeach
                            @endif
                        </ul>
                    </div>
                </div>
            @endif

            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                @guest
                    @if (Route::has('login'))
                        <li class="nav-item">
                            <a class="nav-link text-white" href="{{ route('login') }}">{{ __('Login') }}</a>
                        </li>
                    @endif
                    @if (Route::has('register'))
                        <li class="nav-item">
                            <a class="nav-link text-white" href="{{ route('register') }}">{{ __('Register') }}</a>
                        </li>
                    @endif
                @else
                    <li class="nav-item dropdown">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle text-white" href="#" role="button"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            @if (Auth::check())
                                {{ Auth::user()->name }}
                            @else
                                Developer Mode
                            @endif
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    {{ __('Logout') }}
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </li>
                        </ul>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>
