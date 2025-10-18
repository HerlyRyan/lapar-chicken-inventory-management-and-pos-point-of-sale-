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
    {{-- Success Alert --}}
    <x-toast />
    <div x-data="{
        sidebarOpen: false,
        handleResize() {
            if (window.innerWidth < 768) {
                this.sidebarOpen = false;
            }
        }
    }" x-init="handleResize();
    window.addEventListener('resize', () => handleResize());" class="flex">
        <!-- Sidebar -->
        @include('layouts.internal.sidebar')

        <!-- Main content -->
        <div class="flex-1 flex flex-col overflow-x-auto">
            @include('layouts.internal.header')
            <main class="flex-1 p-6 max-w-full">
                @yield('content')
            </main>
        </div>
    </div>
</body>

{{-- Delete Confirmation Modal --}}
<div id="deleteModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 transform transition-all">
        <div class="p-6 text-center">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4
                           c-.77-.833-1.732-.833-2.464 0L4.732 16.5
                           c-.77.833.192 2.5 1.732 2.5z" />
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">Konfirmasi Penghapusan</h3>
            <p id="deleteMessage" class="text-gray-600 mb-6">
                Apakah Anda yakin ingin menghapus data ini?
            </p>
            <div class="flex space-x-3">
                <button type="button" onclick="closeDeleteModal()"
                    class="flex-1 px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-xl font-medium transition-colors">
                    Batal
                </button>

                <form id="deleteForm" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl font-medium transition-colors">
                        Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function confirmDelete(url, name = 'data ini') {
        const modal = document.getElementById('deleteModal');
        const form = document.getElementById('deleteForm');
        const message = document.getElementById('deleteMessage');

        if (!modal || !form) {
            console.error('Modal konfirmasi tidak ditemukan');
            return;
        }

        // Update action dan pesan
        form.action = url;
        message.innerHTML = `Apakah Anda yakin ingin menghapus <strong>${name}</strong>?`;

        // Tampilkan modal
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeDeleteModal() {
        const modal = document.getElementById('deleteModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    // Tutup modal dengan tombol Escape
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') closeDeleteModal();
    });
</script>


</html>
