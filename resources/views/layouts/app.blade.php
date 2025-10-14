<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Lapar Chicken InventPOS</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('img/Logo.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('img/Logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('img/Logo.png') }}">

    <!-- External Dependencies -->
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <!-- jQuery for legacy scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-orange-50 text-gray-800">
    <div x-data="{
        sidebarOpen: false,
        handleResize() {
            if (window.innerWidth < 768) {
                this.sidebarOpen = false;
            }
        }
    }" x-init="handleResize();
    window.addEventListener('resize', () => handleResize());" class="flex">
        {{-- Sidebar --}}
        @include('layouts.internal.sidebar')

        <div class="flex-1 min-h-screen flex flex-col">
            {{-- Header --}}
            @include('layouts.internal.header')

            {{-- Main content --}}
            <main class="p-6">
                @yield('content')
            </main>
        </div>
    </div>
</body>

</html>
