<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login | Lapar Chicken InvetPOS</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.bunny.net/css?family=Plus+Jakarta+Sans:400,500,600,700" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .bg-mesh {
            background-color: #dc2626;
            background-image:
                radial-gradient(at 0% 0%, hsla(38, 100%, 50%, 1) 0, transparent 50%),
                radial-gradient(at 50% 0%, hsla(22, 100%, 50%, 1) 0, transparent 50%),
                radial-gradient(at 100% 0%, hsla(10, 100%, 50%, 1) 0, transparent 50%);
        }
    </style>
</head>

<body class="antialiased bg-mesh min-h-screen flex items-center justify-center p-6 relative">

    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-yellow-400/20 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-red-600/20 rounded-full blur-3xl"></div>
    </div>

    <main class="relative z-10 w-full max-w-[440px]">
        <div
            class="bg-white/90 backdrop-blur-2xl rounded-3xl shadow-[0_32px_64px_-15px_rgba(0,0,0,0.2)] overflow-hidden border border-white/20">

            <div class="pt-10 pb-6 px-8 text-center">
                <div
                    class="inline-flex p-3 rounded-2xl bg-gradient-to-tr from-orange-500 to-yellow-400 shadow-xl shadow-orange-500/30 mb-4 transition-transform hover:scale-110 duration-500">
                    <img src="{{ asset('img/Logo.png') }}" alt="Logo"
                        class="w-12 h-12 object-contain">
                </div>
                <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Lapar Chicken</h1>
                <p class="text-gray-500 text-sm mt-1">Sistem Manajemen Point of Sales</p>
            </div>

            <div class="px-8 pb-10">
                @if ($errors->any())
                    <div
                        class="mb-6 p-4 rounded-xl bg-red-50 border-l-4 border-red-500 flex items-start gap-3 animate-pulse">
                        <i class="bi bi-exclamation-circle-fill text-red-500 mt-0.5"></i>
                        <p class="text-sm text-red-700 font-medium leading-relaxed">Credential yang Anda masukkan tidak
                            sesuai.</p>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-5" id="loginForm">
                    @csrf

                    <div class="group">
                        <label for="email"
                            class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2 ml-1 group-focus-within:text-orange-600 transition-colors">
                            Alamat Email
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                                <i class="bi bi-envelope text-lg"></i>
                            </span>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required
                                autofocus
                                class="w-full pl-11 pr-4 py-3.5 rounded-2xl border-2 border-gray-100 bg-gray-50/50 focus:bg-white focus:border-orange-500 focus:ring-0 transition-all placeholder:text-gray-400 text-gray-700"
                                placeholder="nama@laparchicken.com">
                        </div>
                    </div>

                    <div class="group" x-data="{ show: false }">
                        <label for="password"
                            class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2 ml-1 group-focus-within:text-orange-600 transition-colors">
                            Kata Sandi
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                                <i class="bi bi-shield-lock text-lg"></i>
                            </span>
                            <input id="password" :type="show ? 'text' : 'password'" name="password" required
                                class="w-full pl-11 pr-12 py-3.5 rounded-2xl border-2 border-gray-100 bg-gray-50/50 focus:bg-white focus:border-orange-500 focus:ring-0 transition-all placeholder:text-gray-400 text-gray-700"
                                placeholder="••••••••">
                            <button type="button" @click="show = !show"
                                class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-orange-500 transition-colors">
                                <i class="bi" :class="show ? 'bi-eye-slash' : 'bi-eye'"></i>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-sm">
                        <label class="flex items-center select-none cursor-pointer group">
                            <input type="checkbox" name="remember"
                                class="w-5 h-5 rounded-lg border-gray-300 text-orange-600 focus:ring-orange-500/20 transition-all shadow-sm">
                            <span class="ml-2.5 text-gray-600 group-hover:text-gray-900 transition-colors">Ingat Sesi
                                Saya</span>
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}"
                                class="font-bold text-orange-600 hover:text-red-600 transition-colors">Lupa
                                Password?</a>
                        @endif
                    </div>

                    <button type="submit" id="submitBtn"
                        class="w-full relative group overflow-hidden bg-gray-900 text-white py-4 rounded-2xl font-bold text-base shadow-xl shadow-gray-900/20 active:scale-[0.98] transition-all">
                        <span class="relative z-10 flex items-center justify-center gap-2">
                            Masuk Ke Sistem
                            <i class="bi bi-arrow-right transition-transform group-hover:translate-x-1"></i>
                        </span>
                        <div
                            class="absolute inset-0 bg-gradient-to-r from-orange-600 to-red-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        </div>
                    </button>
                </form>
            </div>

            <div class="bg-gray-50/80 px-8 py-5 flex items-center justify-center gap-4">
                <span
                    class="flex items-center gap-1.5 text-[11px] font-bold text-gray-400 uppercase tracking-widest italic">
                    <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                    Server Status: Online
                </span>
            </div>
        </div>

        <p class="text-center mt-8 text-white/50 text-xs font-medium tracking-wide">
            &copy; {{ date('Y') }} Lapar Chicken Group &bull; v2.0.4
        </p>
    </main>

    <script>
        const form = document.getElementById('loginForm');
        const btn = document.getElementById('submitBtn');

        form.addEventListener('submit', () => {
            btn.disabled = true;
            btn.innerHTML = `
                <span class="flex items-center justify-center gap-3 italic opacity-80">
                    <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    Menghubungkan...
                </span>
            `;
            btn.classList.add('cursor-not-allowed');
        });
    </script>
</body>

</html>
