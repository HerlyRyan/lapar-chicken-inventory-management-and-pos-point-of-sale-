@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <!-- Main Dashboard Container -->
    <div class="container mx-auto px-4 py-6">

        <!-- Header Section -->
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6">
            <div class="mb-4 lg:mb-0">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Dashboard</h1>
                <div class="flex items-center text-gray-600">
                    <svg class="w-5 h-5 mr-2 text-orange-500" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2L2 7v10c0 5.55 3.84 10 9 11 1.16.21 2.76.21 3.91 0C20.16 27 24 22.55 24 17V7l-10-5z" />
                    </svg>
                    <span>Rebuild in progress — last updated: {{ now()->format('Y-m-d H:i') }}</span>
                </div>
            </div>
            <div class="flex items-center">
                <div class="bg-gradient-to-r from-orange-800 to-red-900 text-white px-4 py-2.5 rounded-lg shadow-lg border border-orange-700/30 font-mono text-sm backdrop-blur-sm" id="liveClock">
                </div>
            </div>
        </div>

        <!-- Alert Banner -->
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-blue-400 p-4 mb-6 rounded-r-lg shadow-sm">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                    <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                    </svg>
                </div>
                <div>
                    <p class="text-blue-800 font-semibold">Dashboard Sedang Dalam Pembangunan</p>
                    <p class="text-blue-700 text-sm mt-1">Tampilan dashboard sedang dibangun ulang. Hanya halaman ini
                        yang aktif. Fitur dan widget akan ditambahkan bertahap.</p>
                </div>
            </div>
        </div>

        <!-- Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

            <!-- Status Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-lg hover:border-gray-300 transition-all duration-300 overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-red-100 to-red-50 p-3 rounded-xl mr-4 flex items-center justify-center">
                            <svg class="w-6 h-6 text-red-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800">Status Sistem</h3>
                    </div>
                    <p class="text-gray-600 text-sm leading-relaxed">
                        Semua halaman dashboard lama telah dinonaktifkan sementara selama proses rebuild untuk
                        memberikan pengalaman yang lebih baik.
                    </p>
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-gray-500">Status</span>
                            <span class="bg-red-100 text-red-700 px-2 py-1 rounded-full font-medium">Maintenance</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Development Plan Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-lg hover:border-gray-300 transition-all duration-300 overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-yellow-100 to-yellow-50 p-3 rounded-xl mr-4 flex items-center justify-center">
                            <svg class="w-6 h-6 text-yellow-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M9 11H7v6h2v-6zm4 0h-2v6h2v-6zm4 0h-2v6h2v-6zM4 3h16v2H4zM3 19h18v2H3z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800">Rencana Pengembangan</h3>
                    </div>
                    <ul class="space-y-3 text-sm text-gray-600">
                        <li class="flex items-center">
                            <div class="w-5 h-5 bg-green-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                                <svg class="w-3 h-3 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />
                                </svg>
                            </div>
                            <span>Integrasi selector cabang</span>
                        </li>
                        <li class="flex items-center">
                            <div class="w-5 h-5 bg-orange-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                                <svg class="w-3 h-3 text-orange-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                                </svg>
                            </div>
                            <span>Kartu ringkas KPI utama</span>
                        </li>
                        <li class="flex items-center">
                            <div class="w-5 h-5 bg-blue-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                                <svg class="w-3 h-3 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M3 3v18h18v-18H3zm16 16H5V5h14v14zm-8-2h2v-4h-2v4zm0-6h2V7h-2v4z" />
                                </svg>
                            </div>
                            <span>Chart penjualan (7 hari)</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Important Notes Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-lg hover:border-gray-300 transition-all duration-300 overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-100 to-blue-50 p-3 rounded-xl mr-4 flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800">Catatan Penting</h3>
                    </div>
                    <div class="space-y-3 text-sm text-gray-600">
                        <p class="leading-relaxed">
                            Jika Anda mengalami error atau masalah, mohon bersihkan cache view/route Laravel terlebih
                            dahulu.
                        </p>
                        <div class="bg-gradient-to-r from-gray-50 to-gray-100 p-3 rounded-lg border border-gray-200">
                            <code class="text-xs text-gray-700 block">
                                php artisan view:clear<br>
                                php artisan route:clear
                            </code>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Info Section -->
        <div class="mt-8 bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-lg hover:border-gray-300 transition-all duration-300 overflow-hidden">
            <div class="p-6">
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 bg-gradient-to-br from-orange-100 to-orange-50 p-3 rounded-xl mr-4 flex items-center justify-center">
                        <svg class="w-6 h-6 text-orange-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800">Tentang Dashboard Baru</h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm text-gray-600">
                    <div class="space-y-4">
                        <div>
                            <h4 class="font-semibold text-gray-800 mb-3 flex items-center">
                                <div class="w-2 h-2 bg-orange-500 rounded-full mr-2"></div>
                                Fitur yang Akan Ditambahkan:
                            </h4>
                            <ul class="space-y-2">
                                <li class="flex items-center">
                                    <div class="w-1.5 h-1.5 bg-orange-400 rounded-full mr-3"></div>
                                    <span>Real-time analytics dan monitoring</span>
                                </li>
                                <li class="flex items-center">
                                    <div class="w-1.5 h-1.5 bg-orange-400 rounded-full mr-3"></div>
                                    <span>Dashboard khusus per cabang</span>
                                </li>
                                <li class="flex items-center">
                                    <div class="w-1.5 h-1.5 bg-orange-400 rounded-full mr-3"></div>
                                    <span>Laporan visual interaktif</span>
                                </li>
                                <li class="flex items-center">
                                    <div class="w-1.5 h-1.5 bg-orange-400 rounded-full mr-3"></div>
                                    <span>Notifikasi dan alert sistem</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <h4 class="font-semibold text-gray-800 mb-3 flex items-center">
                                <div class="w-2 h-2 bg-blue-500 rounded-full mr-2"></div>
                                Estimasi Timeline:
                            </h4>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center p-3 bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg border border-green-200">
                                    <span class="font-medium">Phase 1: Basic Layout</span>
                                    <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-medium">Selesai</span>
                                </div>
                                <div class="flex justify-between items-center p-3 bg-gradient-to-r from-yellow-50 to-amber-50 rounded-lg border border-yellow-200">
                                    <span class="font-medium">Phase 2: Data Integration</span>
                                    <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-xs font-medium">Dalam Progress</span>
                                </div>
                                <div class="flex justify-between items-center p-3 bg-gradient-to-r from-gray-50 to-slate-50 rounded-lg border border-gray-200">
                                    <span class="font-medium">Phase 3: Advanced Features</span>
                                    <span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-xs font-medium">Direncanakan</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function clock() {
            const el = document.getElementById('liveClock');

            function tick() {
                const d = new Date();
                const pad = n => String(n).padStart(2, '0');
                const txt =
                    `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())} ${pad(d.getHours())}:${pad(d.getMinutes())}:${pad(d.getSeconds())}`;
                if (el) el.textContent = txt;
            }
            tick();
            setInterval(tick, 1000);
        })();
    </script>
@endpush
