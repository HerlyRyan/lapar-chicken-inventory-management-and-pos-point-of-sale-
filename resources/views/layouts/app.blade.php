<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Lapar Chicken InventPOS</title>
    <!-- Custom Table Styles -->
    <link href="{{ asset('css/table-styles.css') }}" rel="stylesheet">
    <link href="{{ asset('css/data-table.css') }}" rel="stylesheet">
    <link href="{{ asset('css/ui-standardization.css') }}" rel="stylesheet">
    <link href="{{ asset('css/fixed-table-headers.css') }}" rel="stylesheet">
    <link href="{{ asset('css/custom/compact-forms.css') }}" rel="stylesheet">
    <link href="{{ asset('css/sales-pos.css') }}" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('img/Logo.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('img/Logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('img/Logo.png') }}">
    
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Font Awesome 5 (for pages using FA icons) -->
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    
    <!-- jQuery (Required for legacy scripts using $) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    
    <!-- Pushed Styles -->
    @stack('styles')
    <style>
        :root {
            --primary-red: #e14d2a; /* Less harsh red */
            --primary-orange: #f29727; /* Softer orange */
            --primary-yellow: #ffd966; /* Softer yellow */
            --light-yellow: #fef7e1; /* Lighter yellow background */
            --light-orange: #fde9d0; /* Lighter orange background */
            --light-red: #fae0dd; /* Lighter red background */
            --gradient-main: linear-gradient(135deg, #e14d2a 0%, #f29727 50%, #ffd966 100%); /* Softer gradient */
            --gradient-reverse: linear-gradient(135deg, #ffd966 0%, #f29727 50%, #e14d2a 100%); /* Softer reverse */
            --gradient-soft: linear-gradient(135deg, #fef7e1 0%, #fde9d0 50%, #fae0dd 100%); /* Softer background */
            --hover-yellow: #ffd966; /* Yellow for hover backgrounds */
            --hover-text-dark: #333333; /* Dark color for text on yellow */
            --hover-text-accent: #e14d2a; /* Accent for descriptions */
            --footer-height: 56px; /* default; will be updated by JS */
        }
        
        body { 
            background: var(--gradient-soft);
            font-family: 'Nunito', sans-serif;
            min-height: 100vh;
        }
        
        .navbar {
            background: var(--gradient-main) !important;
            backdrop-filter: blur(10px);
            border-bottom: 3px solid rgba(255,255,255,0.1);
        }
        
        /* Branch Selector Styles */
        .branch-selector .dropdown-menu {
            border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            border-radius: 12px;
            padding: 0.5rem 0;
            min-width: 280px;
        }
        
        .branch-selector .dropdown-item {
            padding: 0.75rem 1rem;
            border-radius: 8px;
            margin: 0 0.5rem;
            transition: all 0.2s ease;
        }
        
        .branch-selector .dropdown-item:hover {
            background: var(--gradient-soft);
            transform: translateX(5px);
        }
        
        .branch-selector .dropdown-item.active {
            background: var(--gradient-main);
            color: white;
        }
        
        .branch-selector .nav-link {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: all 0.2s ease;
        }
        
        .branch-selector .nav-link:hover {
            background: rgba(255,255,255,0.1);
        }
        
        /* === Sidebar and Main Content Core Styles === */
        .sidebar { 
            background-color: var(--primary-red);
            width: 240px;
            position: fixed;
            top: 56px;
            bottom: 0;
            left: 0;
            z-index: 10;
            color: white;
            overflow-y: auto;
            overflow-x: hidden; /* Prevent text from overflowing horizontally */
            transition: transform 0.3s ease;
            box-shadow: 3px 0 10px rgba(0,0,0,0.1);
            border-right: 1px solid rgba(255,255,255,0.1);
            text-overflow: ellipsis;
        }

        /* Main content positioning - ensure proper spacing for header and sidebar */
        .main-content {
            margin-left: 240px !important;
            padding-top: 80px !important; /* Bootstrap navbar is typically 56px + extra padding */
            padding-bottom: calc(var(--footer-height, 56px) + 20px);
            padding-left: 20px;
            padding-right: 20px;
            transition: all 0.3s ease-in-out;
            min-height: calc(100vh - 80px);
            position: relative;
        }

        /* Collapsed state - sidebar hidden */
        .sidebar.sidebar-collapsed {
            width: 0;
            margin-left: -240px;
        }

        .main-content.main-content-expanded {
            margin-left: 0 !important;
        }
        
        .sidebar .nav-link {
            color: #fff;
            border-radius: 8px;
            margin: 2px 8px;
            transition: background-color 0.3s ease, transform 0.2s ease, color 0.3s ease;
            white-space: normal; /* Allow text wrapping */
            line-height: 1.3;
            padding: 8px 12px;
        }

        /* Main nav links hover and active styles */
        .sidebar .nav-link.active,
        .sidebar .nav-link:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateX(5px);
        }

        /* Sub-menu specific styles for two-line items */
        .sidebar .collapse .nav-link {
            padding: 8px 10px;
            position: relative;
            font-size: 0.95rem;
            transition: all 0.2s ease;
            min-height: 45px; /* Ensure minimum height for icon positioning */
            display: grid;
            grid-template-columns: 28px 1fr;
            grid-gap: 8px;
            align-items: start;
        }
        
        /* Fix for main menu items to maintain current styling */
        .sidebar > .nav > .nav-item > .nav-link {
            padding: 8px 12px;
            display: block;
        }

        /* Clear icon positioning to prevent overlap */
        .sidebar .collapse .nav-link .bi {
            font-size: 1rem;
            text-align: center;
            margin-top: 2px;
        }
        
        /* Text content needs to be in its own block */
        .sidebar .collapse .nav-link small {
            display: block;
            grid-column: 2;
            margin-top: 4px;
        }
        
        /* Special styling for numbered circle icons */
        .sidebar .collapse .nav-link .bi[class*="-circle"] {
            font-size: 0.95rem;
        }

        .sidebar .collapse .nav-link small {
            display: block;
            font-size: 0.8rem;
            opacity: 0.8;
            margin-top: 4px;
            line-height: 1.3;
            max-width: 100%;
            overflow-wrap: break-word;
            word-wrap: break-word;
        }

        /* Hover styles for sub-menu items */
        .sidebar .collapse .nav-link:hover {
            background-color: var(--hover-yellow);
            color: var(--hover-text-dark) !important;
            transform: translateX(5px);
        }

        .sidebar .collapse .nav-link:hover small,
        .sidebar .collapse .nav-link:hover .bi {
            color: var(--hover-text-accent) !important;
        }

        /* Active state for sub-menu items */
        .sidebar .collapse .nav-link.active {
            background-color: var(--hover-yellow);
            color: var(--hover-text-dark) !important;
        }

        .sidebar .collapse .nav-link.active small,
        .sidebar .collapse .nav-link.active .bi {
            color: var(--hover-text-accent) !important;
        }

        .card {
            background: rgba(255,255,255,0.9);
            backdrop-filter: blur(10px);
            border: none;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            transition: width 0.3s ease;
            max-width: 100%;
        }
        
        .btn-primary {
            background: var(--gradient-main);
            border: none;
            box-shadow: 0 4px 15px rgba(220, 38, 38, 0.3);
        }
        
        .btn-primary:hover {
            background: var(--gradient-reverse);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(220, 38, 38, 0.4);
        }
        
        .btn-outline-primary {
            border-color: var(--primary-red);
            color: var(--primary-red);
        }
        
        .btn-outline-primary:hover {
            background: var(--gradient-main);
            border-color: transparent;
        }
        
        .alert-success {
            background: linear-gradient(135deg, #fef3c7 0%, #d1fae5 100%);
            border-color: var(--primary-yellow);
            color: #065f46;
        }
        
        .table-light {
            background: rgba(254, 243, 199, 0.3) !important;
        }
        
        .footer { 
            background: var(--gradient-reverse); 
            color: #fff;
            box-shadow: 0 -2px 15px rgba(0,0,0,0.1);
        }
        
        .badge.bg-success {
            background: linear-gradient(135deg, #22c55e, #16a34a) !important;
        }
        
        .badge.bg-warning {
            background: linear-gradient(135deg, #eab308, #d97706) !important;
        }
        
        .badge.bg-danger {
            background: linear-gradient(135deg, #dc2626, #b91c1c) !important;
        }
        
        .text-primary {
            color: var(--primary-red) !important;
        }
        
        .bg-primary {
            background: var(--gradient-main) !important;
        }
        
        .border-primary {
            border-color: var(--primary-red) !important;
        }
        
        @media (max-width: 991px) {
            .sidebar { width: 100vw; min-height: auto; position: static; }
            .main-content { 
                margin-left: 0; 
                max-width: 100vw;
                padding: 15px;
            }
        }
        
        @media (max-width: 576px) {
            .main-content { 
                padding: 10px;
            }
        }
        
        /* Prevent horizontal scroll */
        body {
            overflow-x: hidden;
        }
        
        .container-fluid {
            padding-left: 0;
            padding-right: 0;
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: var(--light-yellow);
        }
        
        ::-webkit-scrollbar-thumb {
            background: var(--gradient-main);
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: var(--gradient-reverse);
        }
        
        /* Animation for cards */
        .card {
            transition: all 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(0,0,0,0.15);
        }
        
        /* Enhanced button effects */
        .btn {
            transition: all 0.3s ease;
        }
        
        .btn:hover {
            transform: translateY(-2px);
        }
        
        /* Animated gradient background */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: var(--gradient-soft);
            animation: gradientShift 15s ease infinite;
            z-index: -1;
        }
        
        @keyframes gradientShift {
            0%, 100% { background: linear-gradient(135deg, #fef3c7 0%, #fed7aa 50%, #fecaca 100%); }
            33% { background: linear-gradient(135deg, #fed7aa 0%, #fecaca 50%, #fef3c7 100%); }
            66% { background: linear-gradient(135deg, #fecaca 0%, #fef3c7 50%, #fed7aa 100%); }
        }
        
        /* Logo animation */
        .navbar-brand img {
            transition: all 0.3s ease;
        }
        
        .navbar-brand:hover img {
            transform: scale(1.1) rotate(5deg);
        }
        
        /* Enhanced sidebar icons */
        .sidebar .nav-link i {
            margin-right: 10px;
            font-size: 1.1rem;
            width: 20px;
            text-align: center;
        }
        
        /* Form input enhancements */
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-orange);
            box-shadow: 0 0 0 0.2rem rgba(234, 88, 12, 0.25);
        }
        
        /* Alert enhancements */
        .alert {
            border: none;
            border-radius: 12px;
            border-left: 4px solid;
        }
        
        .alert-success {
            background: linear-gradient(135deg, #fef3c7 0%, #d1fae5 100%);
            border-left-color: var(--primary-yellow);
            color: #065f46;
        }
        
        .alert-danger {
            background: linear-gradient(135deg, #fecaca 0%, #fee2e2 100%);
            border-left-color: var(--primary-red);
            color: #991b1b;
        }
        
        .alert-warning {
            background: linear-gradient(135deg, #fed7aa 0%, #fef3c7 100%);
            border-left-color: var(--primary-orange);
            color: #92400e;
        }
        
        /* Additional content styling */
        .content-container {
            transition: all 0.3s ease;
            overflow-x: visible; /* Important - allows content to be fully visible */
            width: 100%;
        }
        
        /* Card bodies containing tables should not clip content */
        .card-body {
            overflow: visible;
            transition: width 0.3s ease, padding 0.3s ease;
            position: relative;
        }
        
        /* Form elements should stay within container */
        form .row {
            margin-right: 0;
            margin-left: 0;
            max-width: 100%;
        }
        
        /* Form inputs should stay within bounds */
        .form-control, .form-select {
            max-width: 100%;
        }
        
        /* Modal content should not overflow */
        .modal-content {
            max-width: 100%;
        }
        
        /* Ensure tables are responsive but don't get cut off */
        .table-responsive, .standard-table-container {
            overflow-x: auto;
            min-width: 100%;
            max-width: 100%;
            transition: width 0.3s ease;
        }
        
        /* Table should fit its container exactly */
        table {
            width: 100% !important;
            table-layout: auto;
        }
        
        /* Make sure action buttons stay visible */
        .btn-group {
            flex-wrap: nowrap;
            min-width: fit-content;
            white-space: nowrap;
        }
    </style>
 </head>
 <body class="d-flex flex-column min-vh-100">
    <!-- Header -->
    <nav class="navbar navbar-expand-lg navbar-dark shadow fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="/">
                <img src="{{ asset('img/Logo.png') }}" alt="Logo Lapar Chicken" class="me-2 bg-white rounded shadow p-1" style="height:40px;">
                <span class="fw-bold fs-5 text-white">Lapar Chicken Inventory & Sales</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <button class="btn btn-link text-white d-none d-lg-block" id="sidebarToggle">
                <i class="bi bi-list"></i>
            </button>
            <div class="collapse navbar-collapse" id="mainNavbar">
                <!-- Branch Selector for Super Admin -->
                @if(isset($showBranchSelector) && $showBranchSelector && isset($branches) && $branches->count() > 0)
                <div class="navbar-nav me-auto">
                    <div class="nav-item dropdown branch-selector">
                        <a class="nav-link dropdown-toggle text-white fw-bold" href="#" id="branchSelector" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-building me-1"></i>
                            @if(isset($selectedBranch) && $selectedBranch)
                                {{ $selectedBranch->name }}
                                <span class="badge bg-light text-dark ms-2">{{ $selectedBranch->code }}</span>
                            @else
                                <span class="text-warning">
                                    <i class="bi bi-grid-3x3-gap me-1"></i>Semua Cabang
                                </span>
                            @endif
                        </a>
                        <ul class="dropdown-menu shadow-lg" aria-labelledby="branchSelector">
                            <li><h6 class="dropdown-header text-primary"><i class="bi bi-buildings me-1"></i>Pilih Cabang untuk Ditampilkan</h6></li>
                            <li><hr class="dropdown-divider"></li>
                            
                            <!-- Overview All Branches Option -->
                            <li>
                                <a class="dropdown-item d-flex justify-content-between align-items-center {{ !isset($selectedBranch) || !$selectedBranch ? 'active' : '' }}" 
                                   href="{{ route('dashboard', ['clear_dashboard_branch' => 1]) }}">
                                    <span>
                                        <i class="bi bi-grid-3x3-gap me-2 text-primary"></i>
                                        <strong>Overview Semua Cabang</strong>
                                    </span>
                                    <small class="text-muted">Ringkasan</small>
                                    @if(!isset($selectedBranch) || !$selectedBranch)
                                        <i class="bi bi-check-circle-fill text-success ms-2"></i>
                                    @endif
                                </a>
                            </li>
                            
                            <li><hr class="dropdown-divider"></li>
                            
                            <!-- Production Centers First -->
                            @php
                                $productionCenters = $branches->where('type', 'production')->sortBy('code');
                                $retailBranches = $branches->where('type', 'branch')->sortBy('code');
                            @endphp
                            
                            @if($productionCenters->count() > 0)
                                <li><h6 class="dropdown-header text-warning"><i class="bi bi-gear-fill me-1"></i>Pusat Produksi</h6></li>
                                @foreach($productionCenters as $branch)
                                    <li>
                                        <a class="dropdown-item d-flex justify-content-between align-items-center {{ isset($selectedBranch) && $selectedBranch && $selectedBranch->id == $branch->id ? 'active' : '' }}" 
                                           href="{{ route('dashboard', ['branch_id' => $branch->id]) }}">
                                            <span>
                                                <i class="bi bi-gear-fill me-2 text-warning"></i>{{ $branch->name }}
                                            </span>
                                            <small class="text-muted">{{ $branch->code }}</small>
                                            @if(isset($selectedBranch) && $selectedBranch && $selectedBranch->id == $branch->id)
                                                <i class="bi bi-check-circle-fill text-success ms-2"></i>
                                            @endif
                                        </a>
                                    </li>
                                @endforeach
                                <li><hr class="dropdown-divider"></li>
                            @endif
                            
                            <!-- Retail Branches Sorted by Code -->
                            @if($retailBranches->count() > 0)
                                <li><h6 class="dropdown-header text-info"><i class="bi bi-shop me-1"></i>Cabang Retail</h6></li>
                                @foreach($retailBranches as $branch)
                                    <li>
                                        <a class="dropdown-item d-flex justify-content-between align-items-center {{ isset($selectedBranch) && $selectedBranch && $selectedBranch->id == $branch->id ? 'active' : '' }}" 
                                           href="{{ route('dashboard', ['branch_id' => $branch->id]) }}">
                                            <span>
                                                <i class="bi bi-shop me-2 text-info"></i>{{ $branch->name }}
                                            </span>
                                            <small class="text-muted">{{ $branch->code }}</small>
                                            @if(isset($selectedBranch) && $selectedBranch && $selectedBranch->id == $branch->id)
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
                            <a id="navbarDropdown" class="nav-link dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                @if(Auth::check())
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
    <!-- Sidebar -->
    <div class="sidebar pt-4 px-2">
        <ul class="nav flex-column gap-1">
            <!-- Dashboard -->
            <li class="nav-item">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-house-fill"></i> Dashboard
                </a>
            </li>
            @if(auth()->check() && auth()->user() && (auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Manager')))
            <li class="nav-item">
                <a class="nav-link text-white fw-bold" data-bs-toggle="collapse" href="#masterCollapse" role="button" aria-expanded="false" aria-controls="masterCollapse">
                    <i class="bi bi-database-fill"></i> DATA MASTER
                    <i class="bi bi-chevron-down float-end"></i>
                </a>
                <div class="collapse" id="masterCollapse">
                    <ul class="nav flex-column ms-3">
                        <li class="nav-item">
                            <a class="nav-link ps-3" href="{{ route('users.index') }}">
                                <i class="bi bi-people"></i> Manajemen User
                                <small class="d-block text-white-50">Kelola akun, peran, dan akses pengguna</small>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link ps-3" href="{{ route('branches.index') }}">
                                <i class="bi bi-building"></i> Cabang Toko
                                <small class="d-block text-white-50">Data cabang: alamat, kontak, dan kode</small>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link ps-3" href="{{ route('roles.index') }}">
                                <i class="bi bi-shield-check"></i> Role & Hak Akses
                                <small class="d-block text-white-50">Atur peran dan izin menu aplikasi</small>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link ps-3" href="{{ route('suppliers.index') }}">
                                <i class="bi bi-building-gear"></i> Data Supplier
                                <small class="d-block text-white-50">Daftar supplier & kontak WhatsApp</small>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link ps-3" href="{{ route('units.index') }}">
                                <i class="bi bi-rulers"></i> Satuan Produk
                                <small class="d-block text-white-50">Satuan pengukuran untuk bahan & produk</small>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link ps-3" href="{{ route('categories.index') }}">
                                <i class="bi bi-tags"></i> Kategori Produk
                                <small class="d-block text-white-50">Kelompok/kategori bahan dan produk</small>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link ps-3" href="{{ route('raw-materials.index') }}">
                                <i class="bi bi-box-seam"></i> Master Bahan Mentah
                                <small class="d-block text-white-50">Daftar bahan mentah untuk proses produksi</small>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link ps-3" href="{{ route('semi-finished-products.index') }}">
                                <i class="bi bi-box2"></i> Master Bahan Setengah Jadi
                                <small class="d-block text-white-50">Hasil olahan dari bahan mentah (intermediate)</small>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link ps-3" href="{{ route('finished-products.index') }}">
                                <i class="bi bi-archive"></i> Master Produk Siap Jual
                                <small class="d-block text-white-50">Produk akhir yang dijual di cabang</small>
                            </a>
                        </li>
                        
                        @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Manager'))
                        <li class="nav-item">
                            <a href="{{ route('sales-packages.index') }}" class="nav-link ps-3 {{ request()->routeIs('sales-packages.*') ? 'active' : '' }}">
                                <i class="bi bi-box2-heart"></i> Paket Penjualan
                                <small class="d-block text-white-50">Manager: Kelola paket produk & harga standar</small>
                            </a>
                        </li>
                        @endif
                        
                        @else
                        <li class="nav-item">
                            <div class="nav-link ps-3 text-white-50 small">
                                <i class="bi bi-archive text-muted"></i> Master Produk Siap Jual
                                <small class="d-block text-white-50">Tidak tersedia di Pusat Produksi</small>
                            </div>
                        </li>
                        @endif
                        <li class="nav-item"><hr class="text-white-50 my-1"></li>
                        
                    </ul>
                </div>
            </li>
            
            <li class="nav-item"><hr class="text-white-50 my-2"></li>
            
            {{-- 🛒 PEMBELIAN BAHAN MENTAH (Manager) --}}
            <li class="nav-item">
                <a class="nav-link text-white fw-bold" data-bs-toggle="collapse" href="#pembelianCollapse" role="button" aria-expanded="false" aria-controls="pembelianCollapse">
                    <i class="bi bi-cart-fill"></i> PEMBELIAN BAHAN MENTAH
                    <i class="bi bi-chevron-down float-end"></i>
                </a>
                <div class="collapse" id="pembelianCollapse">
                    <ul class="nav flex-column ms-3">
                        <li class="nav-item">
                            <a href="{{ route('purchase-orders.index') }}" class="nav-link ps-3 {{ request()->routeIs('purchase-orders.*') ? 'active' : '' }}">
                                <i class="bi bi-cart-plus"></i> Buat Purchase Order
                                <small class="d-block text-white-50">Manajer: Buat dan kelola permintaan pembelian bahan mentah</small>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('purchase-receipts.index') }}" class="nav-link ps-3 {{ request()->routeIs('purchase-receipts.*') ? 'active' : '' }}">
                                <i class="bi bi-receipt"></i> Terima Purchase Receipt
                                <small class="d-block text-white-50">Kepala Toko: Terima dan verifikasi bahan yang dikirim supplier</small>
                            </a>
                        </li>
                        
                    </ul>
                </div>
            </li>

            <li class="nav-item"><hr class="text-white-50 my-2"></li>
            
            {{-- 📦 STOK (Visible to all crew) --}}
            <li class="nav-item">
                <a class="nav-link text-white fw-bold" data-bs-toggle="collapse" href="#stokCollapse" role="button" aria-expanded="false" aria-controls="stokCollapse">
                    <i class="bi bi-boxes"></i> STOK
                    <i class="bi bi-chevron-down float-end"></i>
                </a>
                <div class="collapse" id="stokCollapse">
                    <ul class="nav flex-column ms-3">
                        <li class="nav-item">
                            <a href="{{ route('raw-materials.stock') }}" class="nav-link ps-3 {{ request()->routeIs('raw-materials.stock') ? 'active' : '' }}">
                                <i class="bi bi-box"></i> Stok Bahan Mentah
                                <small class="d-block text-white-50">Pantau stok bahan mentah</small>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('semi-finished-stock.index') }}" class="nav-link ps-3 {{ request()->routeIs('semi-finished-stock.*') ? 'active' : '' }}">
                                <i class="bi bi-box-seam"></i> Stok Bahan Setengah Jadi
                                <small class="d-block text-white-50">Pantau stok bahan setengah jadi</small>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('finished-products-stock.index') }}" class="nav-link ps-3 {{ request()->routeIs('finished-products-stock.*') ? 'active' : '' }}">
                                <i class="bi bi-archive"></i> Stok Produk Siap Jual
                                <small class="d-block text-white-50">Pantau stok produk siap jual</small>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('stock-opnames.index') }}" class="nav-link ps-3 {{ request()->routeIs('stock-opnames.*') ? 'active' : '' }}">
                                <i class="bi bi-clipboard-check"></i> Stok Opname
                                <small class="d-block text-white-50">Lakukan dan tinjau stok opname</small>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            
            {{-- 🏭 PUSAT PRODUKSI (Production Center) --}}
            <li class="nav-item">
                <a class="nav-link text-white fw-bold" data-bs-toggle="collapse" href="#produksiCollapse" role="button" aria-expanded="false" aria-controls="produksiCollapse">
                    <i class="bi bi-gear-fill"></i> PUSAT PRODUKSI
                    <i class="bi bi-chevron-down float-end"></i>
                </a>
                <div class="collapse" id="produksiCollapse">
                    <ul class="nav flex-column ms-3">
                        <li class="nav-item">
                            <a href="{{ route('production-requests.index') }}" class="nav-link ps-3 {{ request()->routeIs('production-requests.*') ? 'active' : '' }}">
                                <i class="bi bi-clipboard-plus"></i> Pengajuan Produksi
                                <small class="d-block text-white-50">Kepala Produksi: Ajukan penggunaan bahan mentah</small>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('production-approvals.index') }}" class="nav-link ps-3 {{ request()->routeIs('production-approvals.*') ? 'active' : '' }}">
                                <i class="bi bi-clipboard-check"></i> Persetujuan Produksi
                                <small class="d-block text-white-50">Manajer: Review dan setujui pengajuan produksi</small>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('production-processes.index') }}" class="nav-link ps-3 {{ request()->routeIs('production-processes.*') ? 'active' : '' }}">
                                <i class="bi bi-gear-wide-connected"></i> Proses Produksi
                                <small class="d-block text-white-50">Kru Produksi: Update status pengolahan bahan</small>
                            </a>
                        </li>
                        <li class="nav-item">
                            <!-- Penggunaan Bahan Mentah link moved to OPERASIONAL CABANG section -->
                        </li>
                        <!-- Stok Bahan Setengah Jadi moved to DISTRIBUSI section to avoid duplication -->
                    </ul>
                </div>
             </li>
            
            
            
            {{-- 📦 DISTRIBUSI (Unified Shipping & Receiving) --}}
            <li class="nav-item">
                <a class="nav-link text-white fw-bold" data-bs-toggle="collapse" href="#distribusiCollapse" role="button" aria-expanded="false" aria-controls="distribusiCollapse">
                    <i class="bi bi-inboxes"></i> DISTRIBUSI
                    <i class="bi bi-chevron-down float-end"></i>
                </a>
                <div class="collapse" id="distribusiCollapse">
                    <ul class="nav flex-column ms-3">
                        <li class="nav-item">
                            <a href="{{ route('semi-finished-distributions.index') }}" class="nav-link ps-3 {{ (request()->routeIs('semi-finished-distributions.*') && !request()->routeIs('semi-finished-distributions.inbox')) ? 'active' : '' }}">
                                <i class="bi bi-truck"></i> Pengiriman ke Cabang
                                <small class="d-block text-white-50">Kirim bahan setengah jadi ke cabang</small>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('semi-finished-distributions.inbox', ['branch_id' => $selectedBranch?->id]) }}" class="nav-link ps-3 {{ request()->routeIs('semi-finished-distributions.inbox') ? 'active' : '' }}">
                                <i class="bi bi-inbox"></i> Kotak Masuk Distribusi
                                <small class="d-block text-white-50">Terima/Tolak distribusi masuk ke cabang</small>
                            </a>
                        </li>
                        
                    </ul>
                </div>
            </li>

            
            
            {{-- 🏪 OPERASIONAL CABANG --}}
            @if(auth()->check() && auth()->user() && (auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Manager') || auth()->user()->hasRole('Kepala Toko') || auth()->user()->hasRole('Kru Toko')))
            <li class="nav-item">
                <a class="nav-link text-white fw-bold" data-bs-toggle="collapse" href="#operasionalCollapse" role="button" aria-expanded="false" aria-controls="operasionalCollapse">
                    <i class="bi bi-shop-window"></i> OPERASIONAL CABANG
                    <i class="bi bi-chevron-down float-end"></i>
                </a>
                <div class="collapse" id="operasionalCollapse">
                    <ul class="nav flex-column ms-3">
                        <!-- Stok Bahan Setengah Jadi link removed here (now under DISTRIBUSI) -->
                        
                        @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Manager') || auth()->user()->hasRole('Kepala Toko') || auth()->user()->hasRole('Kru Toko'))
                        <li class="nav-item">
                            <a href="{{ route('semi-finished-usage-requests.index') }}" class="nav-link ps-3 {{ request()->routeIs('semi-finished-usage-requests.*') ? 'active' : '' }}">
                                <i class="bi bi-journal-plus"></i> Ajukan Penggunaan Bahan
                                <small class="d-block text-white-50">Kru Toko: Ajukan penggunaan bahan setengah jadi</small>
                            </a>
                        </li>
                        @endif
                        
                        @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Manager') || auth()->user()->hasRole('Kepala Toko'))
                        <li class="nav-item">
                            <a href="{{ route('semi-finished-usage-approvals.index') }}" class="nav-link ps-3 {{ request()->routeIs('semi-finished-usage-approvals.*') ? 'active' : '' }}">
                                <i class="bi bi-check2-circle"></i> Setujui Penggunaan Bahan
                                <small class="d-block text-white-50">Kepala Toko: Setujui/Tolak → Kirim WA ke Kru Toko</small>
                            </a>
                        </li>
                        @endif

                        @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Manager') || auth()->user()->hasRole('Kepala Toko') || auth()->user()->hasRole('Kru Toko'))
                        <li class="nav-item">
                            <a href="{{ route('semi-finished-usage-processes.index', ['branch_id' => $selectedBranch?->id]) }}" class="nav-link ps-3 {{ request()->routeIs('semi-finished-usage-processes.*') ? 'active' : '' }}">
                                <i class="bi bi-gear"></i> Proses Bahan Setengah Jadi
                                <small class="d-block text-white-50">Kru Toko: Proses bahan sesuai approval</small>
                            </a>
                        </li>
                        @endif
                        
                        @php
                            $currentUserBranch = auth()->check() ? auth()->user()->branch : null;
                            $isProductionCenter = $currentUserBranch && $currentUserBranch->type === 'production';
                            $isSuperAdmin = auth()->check() && auth()->user()->is_superadmin;
                        @endphp
                        
                        {{-- Hide finished products related items from production centers unless super admin --}}
                        @if(!$isProductionCenter || $isSuperAdmin)
                        
                        
                        @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Manager') || auth()->user()->hasRole('Kepala Toko') || auth()->user()->hasRole('Kru Toko'))
                        <li class="nav-item">
                            <a href="{{ route('sales.create') }}" class="nav-link ps-3 {{ request()->routeIs('sales.*') ? 'active' : '' }}">
                                <i class="bi bi-cash-coin"></i> Penjualan
                                <small class="d-block text-white-50">Kru Toko: Jual produk siap jual ke pelanggan</small>
                            </a>
                        </li>
                        @endif
                        @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Manager') || auth()->user()->hasRole('Kepala Toko'))
                        <li class="nav-item">
                            <a href="{{ route('destruction-reports.index') }}" class="nav-link ps-3 {{ request()->routeIs('destruction-reports.*') ? 'active' : '' }}">
                                <i class="bi bi-exclamation-triangle"></i> Laporan Pemusnahan
                                <small class="d-block text-white-50">Catat & tinjau pemusnahan barang</small>
                            </a>
                        </li>
                        @endif
                        
                        {{-- Sales link moved above --}}
                        @else
                        {{-- Show message for production center users --}}
                        <li class="nav-item">
                            <div class="nav-link ps-3 text-white-50 small">
                                <i class="bi bi-info-circle"></i> Produk Siap Jual
                                <small class="d-block text-white-50">Tidak tersedia di Pusat Produksi</small>
                            </div>
                        </li>
                        @endif
                        
                        @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Manager') || auth()->user()->hasRole('Kepala Toko'))
                        <li class="nav-item">
                            <a href="{{ route('stock-transfer.index') }}" class="nav-link ps-3 {{ request()->routeIs('stock-transfer.*') ? 'active' : '' }}">
                                <i class="bi bi-arrow-repeat"></i> Transfer Antar Cabang
                                <small class="d-block text-white-50">Kepala Toko: Transfer stok antar cabang</small>
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>
            </li>
            
            <li class="nav-item"><hr class="text-white-50 my-2"></li>
            @endif
            
            {{-- Reports (LAPORAN) section removed during redesign phase --}}
        </ul>
    </div>
    
    <style>
        /* CRITICAL LAYOUT FIXES - HIGHEST PRIORITY */
        /* Fix for sidebar text leakage */
        .sidebar .nav-link {
            overflow: hidden; /* keep container overflow safe */
            white-space: normal; /* allow wrapping to two lines */
            text-overflow: clip; /* no ellipsis so text can wrap */
            width: 100%;
            display: block;
        }
        
        #laporanCollapse, #masterCollapse, #pembelianCollapse, #operasionalCollapse {
            overflow: hidden;
        }
        
        /* Force proper main content positioning */
        .main-content {
            margin-left: 240px !important;
            padding-top: 90px !important;
            padding-bottom: calc(var(--footer-height, 56px) + 30px) !important;
            padding-left: 30px !important;
            padding-right: 30px !important;
            transition: all 0.3s ease !important;
            position: relative !important;
            min-height: calc(100vh - 90px) !important;
            /* z-index removed to avoid trapping Bootstrap modals behind backdrop */
        }
        
        /* When sidebar is collapsed */
        .main-content.main-content-expanded {
            margin-left: 20px !important;
        }
        
        /* Ensure navbar is properly positioned and sized */
        .navbar {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            z-index: 1030 !important;
            height: 60px !important;
        }
        
        /* Ensure sidebar is properly positioned */
        .sidebar {
            position: fixed !important;
            top: 60px !important;
            left: 0 !important;
            width: 240px !important;
            height: calc(100vh - 60px) !important;
            z-index: 1020 !important;
            transition: all 0.3s ease !important;
        }
        
        /* Sidebar collapsed state */
        .sidebar.sidebar-collapsed {
            width: 0 !important;
            margin-left: -240px !important;
        }
        
        /* Prevent content from going behind fixed elements */
        body {
            padding-top: 0 !important;
            margin-top: 0 !important;
            overflow-x: hidden !important;
        }
        
        /* Container fixes */
        .container-fluid {
            padding-left: 0 !important;
            padding-right: 0 !important;
            max-width: 100% !important;
            margin: 0 !important;
        }
        
        /* Form elements should stay within container */
        form .row {
            margin-right: 0;
            margin-left: 0;
            max-width: 100%;
        }
        
        /* Form inputs should stay within bounds */
        .form-control, .form-select {
            max-width: 100%;
        }
        
        /* Modal content should not overflow */
        .modal-content {
            max-width: 100%;
        }
        
        /* Ensure tables are responsive but don't get cut off */
        .table-responsive, .standard-table-container {
            overflow-x: auto;
            min-width: 100%;
            max-width: 100%;
            transition: width 0.3s ease;
        }
        
        /* Table should fit its container exactly */
        table {
            width: 100% !important;
            table-layout: auto;
        }
        
        /* Make sure action buttons stay visible */
        .btn-group {
            flex-wrap: nowrap;
            min-width: fit-content;
            white-space: nowrap;
        }
    </style>
    <!-- Main Content -->
    <main class="main-content py-4 flex-grow-1">
        <div class="container-fluid">
            @yield('content')
        </div>
    </main>
    <!-- Footer -->
    <footer class="footer fixed-bottom py-2 text-center small shadow">
        <span>&copy; {{ date('Y') }} Lapar Chicken. Sistem Inventori & Penjualan. All rights reserved.</span>
    </footer>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Keep CSS variable --footer-height in sync with actual footer height
        (function() {
            function setFooterHeightVar() {
                try {
                    const footer = document.querySelector('footer.footer');
                    if (!footer) return;
                    const h = footer.offsetHeight || 56;
                    document.documentElement.style.setProperty('--footer-height', h + 'px');
                } catch (_) {}
            }
            window.addEventListener('DOMContentLoaded', setFooterHeightVar);
            window.addEventListener('load', setFooterHeightVar);
            window.addEventListener('resize', setFooterHeightVar);
            window.addEventListener('orientationchange', setFooterHeightVar);
        })();
    </script>
    <script>
        // Sidebar Toggle Functionality with localStorage Persistence
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggleBtn = document.getElementById('sidebarToggle');
            const sidebar = document.querySelector('.sidebar');
            const mainContent = document.querySelector('.main-content');

            if (sidebarToggleBtn && sidebar && mainContent) {
                const applySidebarState = (collapsed) => {
                    if (collapsed) {
                        sidebar.classList.add('sidebar-collapsed');
                        mainContent.classList.add('main-content-expanded');
                    } else {
                        sidebar.classList.remove('sidebar-collapsed');
                        mainContent.classList.remove('main-content-expanded');
                    }
                };

                // Check initial state from localStorage
                let isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
                applySidebarState(isCollapsed);

                // Handle the click event
                sidebarToggleBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    isCollapsed = !isCollapsed;
                    applySidebarState(isCollapsed);
                    localStorage.setItem('sidebarCollapsed', isCollapsed);
                });
            }
        });

        // Branch selection and branch_id propagation (one-time init)
        document.addEventListener('DOMContentLoaded', function() {
            if (window.__BRANCH_INIT_DONE) return; // guard against multiple initializations
            window.__BRANCH_INIT_DONE = true;

            // Add click handlers to branch dropdown items (unified: dynamic update or navigation)
            const branchDropdownItems = document.querySelectorAll('.branch-selector .dropdown-item');
            branchDropdownItems.forEach(item => {
                item.addEventListener('click', function(e) {
                    const href = this.getAttribute('href') || '';
                    const url = new URL(href, window.location.origin);
                    const branchId = url.searchParams.get('branch_id');
                    const branchName = this.querySelector('span') ? this.querySelector('span').textContent.trim() : 'Semua Cabang';

                    // Persist selection
                    if (branchId) {
                        sessionStorage.setItem('selectedBranchId', branchId);
                        sessionStorage.setItem('selectedBranchName', branchName);
                    } else {
                        sessionStorage.removeItem('selectedBranchId');
                        sessionStorage.removeItem('selectedBranchName');
                    }

                    // If page supports dynamic updates, dispatch event instead of navigating
                    if (typeof window.updateProductStocksForBranch === 'function') {
                        e.preventDefault();
                        const newPath = url.pathname + (branchId ? ('?branch_id=' + branchId) : '');
                        window.dispatchEvent(new CustomEvent('branchChanged', {
                            detail: { branchId, branchName, url: newPath }
                        }));
                        return;
                    }

                    // Default: navigate with branch_id preserved on current path
                    e.preventDefault();
                    const currentPath = window.location.pathname;
                    const newUrl = currentPath + (branchId ? '?branch_id=' + branchId : '');
                    window.location.href = newUrl;
                });
            });

            // Add/refresh branch_id on links and forms
            function addBranchIdToLinksAndForms() {
                const urlParams = new URLSearchParams(window.location.search);
                let branchId = urlParams.get('branch_id') || sessionStorage.getItem('selectedBranchId');

                if (!branchId) {
                    return; // no-op when no branch selected
                }
                sessionStorage.setItem('selectedBranchId', branchId);

                // Links
                const selector = 'a:not([href^="http"]):not([href^="mailto"]):not([href^="tel"]):not([href^="#"]):not([href="javascript:void(0)"])';
                const links = document.querySelectorAll(selector);
                links.forEach(link => {
                    // Skip branch selector menu items
                    if (link.classList.contains('dropdown-item') && link.closest('.branch-selector')) return;
                    if (!link.href || link.href.trim() === '') return;
                    try {
                        const u = new URL(link.href, window.location.origin);
                        if (u.searchParams.get('branch_id') === branchId) return;
                        u.searchParams.set('branch_id', branchId);
                        link.href = u.toString();
                    } catch (_) {}
                });

                // Forms
                const forms = document.querySelectorAll('form:not([action^="http"])');
                forms.forEach(form => {
                    let input = form.querySelector('input[name="branch_id"]');
                    if (!input) {
                        input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'branch_id';
                        form.appendChild(input);
                    }
                    input.value = branchId;

                    // Ensure submit handler attached only once
                    if (!form.__branchSubmitHooked) {
                        form.addEventListener('submit', function() {
                            const currentBranchId = new URLSearchParams(window.location.search).get('branch_id') || sessionStorage.getItem('selectedBranchId');
                            if (!currentBranchId) return;
                            let brInput = form.querySelector('input[name="branch_id"]');
                            if (!brInput) {
                                brInput = document.createElement('input');
                                brInput.type = 'hidden';
                                brInput.name = 'branch_id';
                                form.appendChild(brInput);
                            }
                            brInput.value = currentBranchId;
                        });
                        form.__branchSubmitHooked = true;
                    }

                    // Opt-in: disable submit buttons on submit to prevent double submissions
                    if (form.dataset.disableOnSubmit === 'true' && !form.__disableOnSubmitHooked) {
                        form.addEventListener('submit', function() {
                            try {
                                const buttons = form.querySelectorAll('button[type="submit"], input[type="submit"]');
                                buttons.forEach(btn => { btn.disabled = true; btn.classList.add('disabled'); });
                            } catch (_) {}
                        });
                        form.__disableOnSubmitHooked = true;
                    }
                });
            }

            // Initial run
            addBranchIdToLinksAndForms();

            // Observe new nodes only once
            if (!window.__BRANCH_LINKS_OBSERVER) {
                window.__BRANCH_LINKS_OBSERVER = new MutationObserver(function(mutations) {
                    let shouldRun = false;
                    for (const mutation of mutations) {
                        if (mutation.type === 'childList' && mutation.addedNodes && mutation.addedNodes.length) {
                            shouldRun = true;
                            break;
                        }
                    }
                    if (!shouldRun) return;

                    // If a Bootstrap modal is open, defer updates to avoid heavy work during transitions
                    if (document.body.classList.contains('modal-open')) {
                        if (window.__BRANCH_OBSERVER_DEFERRED) return;
                        window.__BRANCH_OBSERVER_DEFERRED = true;
                        setTimeout(() => {
                            window.__BRANCH_OBSERVER_DEFERRED = false;
                            if (!document.body.classList.contains('modal-open')) {
                                addBranchIdToLinksAndForms();
                            }
                        }, 300);
                        return;
                    }

                    // Coalesce rapid mutations into a single pass
                    if (window.__BRANCH_OBSERVER_SCHEDULED) return;
                    window.__BRANCH_OBSERVER_SCHEDULED = true;
                    setTimeout(() => {
                        window.__BRANCH_OBSERVER_SCHEDULED = false;
                        addBranchIdToLinksAndForms();
                    }, 100);
                });
                window.__BRANCH_LINKS_OBSERVER.observe(document.body, { childList: true, subtree: true });

                // Cleanup on navigation
                window.addEventListener('beforeunload', function() {
                    try { window.__BRANCH_LINKS_OBSERVER.disconnect(); } catch (_) {}
                    window.__BRANCH_LINKS_OBSERVER = null;
                    window.__BRANCH_INIT_DONE = false;
                });
            }
        });


        
        document.addEventListener('DOMContentLoaded', function() {
            // Override HTML5 validation messages with Indonesian
            const requiredFields = document.querySelectorAll('input[required], select[required], textarea[required]');
            
            requiredFields.forEach(function(field) {
                field.addEventListener('invalid', function(e) {
                    // Don't prevent default - let the browser handle validation
                    // e.preventDefault();
                    
                    // Get field name from label or placeholder
                    const label = document.querySelector('label[for="' + field.id + '"]');
                    let fieldName = 'Field ini';
                    
                    if (label) {
                        fieldName = label.textContent.replace(/\s*\*\s*$/, '').trim();
                    } else if (field.placeholder) {
                        fieldName = field.placeholder;
                    }
                    
                    // Set Indonesian validation message
                    if (field.validity.valueMissing) {
                        field.setCustomValidity(fieldName + ' wajib diisi.');
                    } else if (field.validity.typeMismatch) {
                        if (field.type === 'email') {
                            field.setCustomValidity(fieldName + ' harus berupa email yang valid.');
                        } else if (field.type === 'number') {
                            field.setCustomValidity(fieldName + ' harus berupa angka.');
                        } else {
                            field.setCustomValidity(fieldName + ' format tidak valid.');
                        }
                    } else if (field.validity.rangeUnderflow) {
                        field.setCustomValidity(fieldName + ' harus minimal ' + field.min + '.');
                    } else if (field.validity.rangeOverflow) {
                        field.setCustomValidity(fieldName + ' harus maksimal ' + field.max + '.');
                    } else if (field.validity.stepMismatch) {
                        field.setCustomValidity(fieldName + ' harus kelipatan ' + field.step + '.');
                    } else if (field.validity.tooShort) {
                        field.setCustomValidity(fieldName + ' harus minimal ' + field.minLength + ' karakter.');
                    } else if (field.validity.tooLong) {
                        field.setCustomValidity(fieldName + ' harus maksimal ' + field.maxLength + ' karakter.');
                    } else if (field.validity.patternMismatch) {
                        field.setCustomValidity(fieldName + ' format tidak sesuai pola yang diharapkan.');
                    }
                });
                
                // Clear custom validity when user starts typing
                field.addEventListener('input', function() {
                    field.setCustomValidity('');
                });
            });
        });
        
        // Dynamic branch handling is unified in the single initialization above
        @if(isset($showBranchSelector) && $showBranchSelector)
        // (no-op) kept for blade compatibility
        @endif
    </script>
    
    <!-- Include Stock Transfer Modal -->
    @include('components.stock-transfer-modal')
    
    <!-- SweetAlert2 Library -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Global Delete Confirmation Function -->
    <script>
        function confirmDelete(url, name) {
            // Fix URL if it's a full URL (remove domain part)
            if (url.startsWith('http')) {
                try {
                    const urlObj = new URL(url);
                    url = urlObj.pathname; // Get only the path part
                } catch (e) {
                    console.error('Invalid URL format:', url);
                    // Try to extract the path part manually if URL parsing fails
                    const parts = url.split('/');
                    if (parts.length >= 3) {
                        // Remove protocol and domain parts
                        parts.splice(0, 3);
                        url = '/' + parts.join('/');
                    }
                }
            }
            
            // Ensure URL doesn't contain the full domain again
            if (url.includes('http://') || url.includes('https://')) {
                url = '/' + url.split('/').slice(3).join('/');
            }
            
            console.log('Processed delete URL:', url);
            
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: `Yakin ingin menghapus ${name}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Create and submit delete form
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = url;
                    form.innerHTML = `
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="_method" value="DELETE">
                    `;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        /* Sidebar description text merah saat hover */
        .sidebar .nav-link:hover small,
        .sidebar .nav-item:hover .nav-link small {
            color: #dc3545 !important; /* merah Bootstrap */
        }
        
        /* Modal z-index hardening to ensure modal stays above backdrop in all contexts */
        .modal { z-index: 2000 !important; }
        .modal-backdrop { z-index: 1990 !important; }

        /* While a modal is open, prevent app chrome from intercepting clicks */
        body.modal-open .navbar,
        body.modal-open .sidebar,
        body.modal-open .footer {
            pointer-events: none !important;
        }

        /* Ensure modal and its children remain clickable */
        body.modal-open .modal,
        body.modal-open .modal * {
            pointer-events: auto !important;
        }
    </style>
    
    <!-- Global Bootstrap Modal Guard: prevent stuck backdrops & modal stacking -->
    <script>
        (function() {
            // Gate logging behind APP_DEBUG; expose flag globally
            try { window.DEBUG_MODAL = @json(config('app.debug', false)); } catch (_) {}
            const DEBUG = !!window.DEBUG_MODAL; // toggle for console logs

            function log(...args) {
                if (!DEBUG) return;
                try { console.debug('[ModalGuard]', ...args); } catch (_) {}
            }

            function cleanupBackdropsAndBody() {
                const anyOpen = document.querySelector('.modal.show');
                const bds = document.querySelectorAll('.modal-backdrop');
                log('cleanup called; anyOpen:', !!anyOpen, 'backdrops:', bds.length);
                // Remove any stray backdrops when no modal is visible
                if (!anyOpen && bds.length) {
                    bds.forEach(el => el.remove());
                    document.body.classList.remove('modal-open');
                    document.body.style.removeProperty('overflow');
                    document.body.style.removeProperty('padding-right');
                    log('cleanup removed stray backdrops and reset body');
                }
            }

            // Debounce rapid clicks on modal triggers (prevent double-open)
            document.addEventListener('click', function(e) {
                if (window.DISABLE_MODAL_GUARD === true) return;
                const trigger = e.target.closest('[data-bs-toggle="modal"]');
                if (!trigger) return;
                if (trigger.dataset.modalClickLock === '1') {
                    e.preventDefault();
                    e.stopPropagation();
                    log('blocked rapid re-click on trigger');
                    return;
                }
                trigger.dataset.modalClickLock = '1';
                setTimeout(() => { try { delete trigger.dataset.modalClickLock; } catch (_) {} }, 800);
            }, true);

            // Pre-emptively remove any leftover backdrops before a modal starts to show
            document.addEventListener('show.bs.modal', function (event) {
                if (window.DISABLE_MODAL_GUARD === true) return;
                const modalEl = event.target;
                const strayBackdrops = document.querySelectorAll('.modal-backdrop');
                log('show event for', '#' + (modalEl.id || '(no-id)'), 'stray backdrops before cleanup:', strayBackdrops.length);
                if (strayBackdrops.length > 0) {
                    strayBackdrops.forEach(el => el.remove());
                    document.body.classList.remove('modal-open');
                    document.body.style.removeProperty('overflow');
                    document.body.style.removeProperty('padding-right');
                    log('removed stray backdrops pre-show');
                }
                // Temporarily disable the trigger (if available) to avoid double show
                const trg = event.relatedTarget;
                if (trg) {
                    trg.classList.add('modal-trigger-busy');
                    trg.style.pointerEvents = 'none';
                    if (trg.tagName === 'BUTTON') {
                        try { trg.disabled = true; } catch (_) {}
                    }
                    log('disabled trigger for', '#' + (modalEl.id || '(no-id)'));
                    // Fallback: re-enable trigger if modal fails to open/close properly
                    setTimeout(() => {
                        try {
                            trg.classList.remove('modal-trigger-busy');
                            trg.style.pointerEvents = '';
                            if (trg.tagName === 'BUTTON') trg.disabled = false;
                        } catch (_) {}
                    }, 2000);
                }
            });

            // Ensure only one backdrop exists and avoid stacking multiple modals
            document.addEventListener('shown.bs.modal', function (event) {
                if (window.DISABLE_MODAL_GUARD === true) return;
                const modalEl = event.target;
                const backdrops = document.querySelectorAll('.modal-backdrop');
                log('shown event for', '#' + (modalEl.id || '(no-id)'), 'backdrops after show:', backdrops.length);
                if (backdrops.length > 1) {
                    // Keep the top-most backdrop, remove older ones
                    backdrops.forEach((bd, idx) => { if (idx < backdrops.length - 1) bd.remove(); });
                    log('removed extra backdrops on shown; kept latest');
                }
                // Hide other open modals to prevent stacking issues
                document.querySelectorAll('.modal.show').forEach(m => {
                    if (m !== modalEl) {
                        const inst = bootstrap.Modal.getInstance(m) || bootstrap.Modal.getOrCreateInstance(m);
                        log('hiding other open modal', '#' + (m.id || '(no-id)'));
                        inst.hide();
                    }
                });
                // Next tick: enforce single backdrop again in case of rapid double-show
                setTimeout(() => {
                    const bds = document.querySelectorAll('.modal-backdrop');
                    if (bds.length > 1) {
                        bds.forEach((bd, idx) => { if (idx < bds.length - 1) bd.remove(); });
                        log('post-tick cleanup removed extra backdrops; count now:', document.querySelectorAll('.modal-backdrop').length);
                    }
                }, 0);

                // SOLUSI TERPATEN: Force correct z-index stacking to prevent page freeze
                const backdrop = document.querySelector('.modal-backdrop.show');
                if (modalEl && backdrop) {
                    try {
                        const backdropZIndex = parseInt(window.getComputedStyle(backdrop).zIndex, 10);
                        const modalZIndex = parseInt(window.getComputedStyle(modalEl).zIndex, 10);

                        log(`Z-Index Check: Modal (${modalZIndex}) vs Backdrop (${backdropZIndex})`);
                        // Additional diagnostics for pointer-events which can also block clicks
                        try {
                            const bdPE = window.getComputedStyle(backdrop).pointerEvents;
                            const mdPE = window.getComputedStyle(modalEl).pointerEvents;
                            log(`PointerEvents Check: Modal (${mdPE}) vs Backdrop (${bdPE})`);
                        } catch (_) {}

                        if (!isNaN(backdropZIndex) && modalZIndex <= backdropZIndex) {
                            modalEl.style.zIndex = backdropZIndex + 1;
                            log(`Z-Index Corrected: Set modal z-index to ${backdropZIndex + 1}`);
                        }
                    } catch (e) {
                        log('Could not perform z-index correction:', e);
                    }
                }
            });
            
            document.addEventListener('hidden.bs.modal', function (event) {
                if (window.DISABLE_MODAL_GUARD === true) return;
                const modalEl = event.target;
                log('hidden event for', '#' + (modalEl.id || '(no-id)'));
                // Re-enable triggers for this modal id
                if (modalEl.id) {
                    document.querySelectorAll(`[data-bs-toggle="modal"][data-bs-target="#${modalEl.id}"]`).forEach(el => {
                        el.classList.remove('modal-trigger-busy');
                        el.style.pointerEvents = '';
                        if (el.tagName === 'BUTTON') {
                            try { el.disabled = false; } catch (_) {}
                        }
                    });
                }
                // Next frame to let Bootstrap finish transitions
                requestAnimationFrame(cleanupBackdropsAndBody);
            });
            
            // Safety: on page show (bfcache/back), clean leftovers
            window.addEventListener('pageshow', cleanupBackdropsAndBody);
            // Initial safety cleanup
            cleanupBackdropsAndBody();
        })();
    </script>
    
    <!-- Pushed Scripts -->
    @stack('scripts')
</body>
</html>
