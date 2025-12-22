@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="container mx-auto px-4 py-8 max-w-7xl animate-fade-in">

        <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 gap-4">
            <div>
                <nav class="text-sm font-medium text-gray-500 mb-2">Main Menu / Dashboard</nav>
                <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight">Dashboard Overview</h1>
                <div
                    class="flex items-center mt-2 text-gray-500 bg-gray-100 w-fit px-3 py-1 rounded-full text-xs font-medium">
                    <span class="relative flex h-2 w-2 mr-2">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-orange-500"></span>
                    </span>
                    Rebuild in progress — Updated: {{ now()->format('H:i') }}
                </div>
            </div>

            <div class="flex items-center">
                <div
                    class="bg-slate-900 text-white px-6 py-3 rounded-2xl shadow-xl border-b-4 border-orange-600 flex items-center gap-3 transition-transform hover:scale-105">
                    <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span id="liveClock" class="font-mono text-lg font-bold tracking-widest">00:00:00</span>
                </div>
            </div>
        </div>

        <div x-data="{ show: true }" x-show="show"
            class="group bg-gradient-to-r from-blue-600 to-indigo-700 p-[1px] rounded-2xl mb-8 shadow-md">
            <div class="bg-white rounded-[15px] p-5 flex items-start sm:items-center justify-between">
                <div class="flex items-start sm:items-center">
                    <div class="hidden sm:flex w-12 h-12 bg-blue-50 rounded-xl items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-blue-900 font-bold text-lg">System Under Maintenance</h3>
                        <p class="text-slate-600 text-sm leading-relaxed">Modul laporan sedang dalam pengembangan bertahap.
                        </p>
                    </div>
                </div>
                <button @click="show = false" class="text-slate-400 hover:text-slate-600 p-2">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z">
                        </path>
                    </svg>
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <div class="lg:col-span-2 space-y-8">

                <div x-data="yearlySales()" class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                    <div class="p-6 border-b border-slate-50 flex items-center justify-between bg-slate-50/50">
                        <h2 class="text-xl font-bold text-slate-800 tracking-tight">Analisis Penjualan Tahunan</h2>
                        <select x-model="year" @change="load()"
                            class="bg-white border border-slate-200 rounded-xl px-4 py-2 text-sm font-semibold text-slate-700 focus:ring-2 focus:ring-orange-500 outline-none shadow-sm">
                            <template x-for="y in years">
                                <option :value="y" x-text="'Tahun ' + y"></option>
                            </template>
                        </select>
                    </div>
                    <div class="p-6 relative h-[300px]">
                        <div x-show="loading"
                            class="absolute inset-0 bg-white/60 backdrop-blur-sm z-10 flex items-center justify-center">
                            <div class="animate-spin rounded-full h-8 w-8 border-4 border-orange-500 border-t-transparent">
                            </div>
                        </div>
                        <canvas id="yearlyChart"></canvas>
                    </div>
                </div>

                <div x-data="monthlySales()" class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                    <div class="p-6 border-b border-slate-50 bg-slate-50/50">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <h2 class="text-xl font-bold text-slate-800 tracking-tight">Penjualan Bulanan & Harian</h2>

                            <div class="grid grid-cols-2 md:flex gap-2">
                                <select x-model="year" @change="load()"
                                    class="bg-white border border-slate-200 rounded-lg px-3 py-1.5 text-xs font-bold text-slate-600 outline-none focus:border-orange-500">
                                    <template x-for="y in years">
                                        <option :value="y" x-text="y"></option>
                                    </template>
                                </select>
                                <select x-model="month" @change="load()"
                                    class="bg-white border border-slate-200 rounded-lg px-3 py-1.5 text-xs font-bold text-slate-600 outline-none focus:border-orange-500">
                                    <template x-for="(name, index) in monthNames">
                                        <option :value="index + 1" x-text="name" :selected="(index + 1) == month">
                                        </option>
                                    </template>
                                </select>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 mt-4 pt-4 border-t border-slate-100">
                            <span class="text-[10px] font-bold uppercase text-slate-400 tracking-wider">Custom Range:</span>
                            <input type="date" x-model="start" @change="load()"
                                class="text-xs border-none bg-slate-100 rounded-md px-2 py-1 text-slate-600 focus:ring-1 focus:ring-orange-400">
                            <span class="text-slate-300">-</span>
                            <input type="date" x-model="end" @change="load()"
                                class="text-xs border-none bg-slate-100 rounded-md px-2 py-1 text-slate-600 focus:ring-1 focus:ring-orange-400">
                        </div>
                    </div>

                    <div class="p-6 relative h-[350px]">
                        <div x-show="loading"
                            class="absolute inset-0 bg-white/60 backdrop-blur-sm z-10 flex items-center justify-center">
                            <div class="animate-spin rounded-full h-8 w-8 border-4 border-orange-500 border-t-transparent">
                            </div>
                        </div>
                        <canvas id="monthlyChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div
                    class="bg-gradient-to-br from-orange-500 to-red-600 rounded-3xl p-6 text-white shadow-lg shadow-orange-200">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-3 bg-white/20 rounded-2xl">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                        </div>
                        <span class="text-xs font-bold uppercase tracking-wider opacity-80">Target 2024</span>
                    </div>
                    <div class="text-sm opacity-90 font-medium">Estimasi Pendapatan</div>
                    <div class="text-3xl font-black mb-4 tracking-tight">Rp 1.250.000.000</div>
                    <div class="w-full bg-black/10 rounded-full h-2.5 overflow-hidden">
                        <div class="bg-white h-full rounded-full transition-all duration-1000" style="width: 75%"></div>
                    </div>
                    <div class="mt-3 text-xs font-bold flex justify-between">
                        <span>Progres</span>
                        <span>75%</span>
                    </div>
                </div>

                <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm">
                    <h3 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                        <span class="w-1.5 h-4 bg-orange-500 rounded-full"></span>
                        Aksi Cepat
                    </h3>
                    <div class="grid grid-cols-2 gap-3">
                        <button
                            class="flex flex-col items-center justify-center p-4 rounded-2xl bg-slate-50 text-slate-700 hover:bg-orange-50 hover:text-orange-600 transition-all border border-slate-100 group">
                            <svg class="w-5 h-5 mb-1 opacity-60 group-hover:scale-110 transition-transform" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            <span class="text-[10px] uppercase font-bold tracking-tighter">Cetak PDF</span>
                        </button>
                        <button
                            class="flex flex-col items-center justify-center p-4 rounded-2xl bg-slate-50 text-slate-700 hover:bg-orange-50 hover:text-orange-600 transition-all border border-slate-100 group">
                            <svg class="w-5 h-5 mb-1 opacity-60 group-hover:scale-110 transition-transform" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 17v-2m3 2v-4m3 2v-6m10 10V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2h14a2 2 0 002-2z">
                                </path>
                            </svg>
                            <span class="text-[10px] uppercase font-bold tracking-tighter">Export Excel</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .animate-fade-in {
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Live Clock dengan proteksi null
        document.addEventListener('DOMContentLoaded', () => {
            const el = document.getElementById('liveClock');
            if (el) {
                setInterval(() => {
                    el.textContent = new Date().toLocaleTimeString('id-ID', {
                        hour12: false,
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit'
                    });
                }, 1000);
            }
        });

        function yearlySales() {
            return {
                year: new Date().getFullYear(),
                years: Array.from({
                    length: 5
                }, (_, i) => new Date().getFullYear() - i),
                chart: null,
                loading: false,
                async load() {
                    this.loading = true;
                    try {
                        const res = await fetch(`/dashboard/sales/yearly?year=${this.year}`);
                        const data = await res.json();
                        this.render(data);
                    } finally {
                        this.loading = false;
                    }
                },
                render(data) {
                    if (this.chart) this.chart.destroy();
                    const ctx = document.getElementById('yearlyChart').getContext('2d');
                    const gradient = ctx.createLinearGradient(0, 0, 0, 300);
                    gradient.addColorStop(0, 'rgba(234, 88, 12, 0.2)');
                    gradient.addColorStop(1, 'rgba(234, 88, 12, 0)');

                    this.chart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: data.labels,
                            datasets: [{
                                data: data.values,
                                borderColor: '#ea580c',
                                borderWidth: 3,
                                fill: true,
                                backgroundColor: gradient,
                                tension: 0.4,
                                pointRadius: 0,
                                pointHoverRadius: 6
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                }
                            },
                            scales: {
                                y: {
                                    grid: {
                                        color: '#f8fafc'
                                    },
                                    ticks: {
                                        callback: v => 'Rp ' + v.toLocaleString('id-ID')
                                    }
                                },
                                x: {
                                    grid: {
                                        display: false
                                    }
                                }
                            }
                        }
                    });
                },
                init() {
                    this.load();
                }
            }
        }

        function monthlySales() {
            const now = new Date();
            return {
                year: now.getFullYear(),
                month: now.getMonth() + 1,
                // Daftar nama bulan untuk tampilan UI
                monthNames: [
                    "Januari", "Februari", "Maret", "April", "Mei", "Juni",
                    "Juli", "Agustus", "September", "Oktober", "November", "Desember"
                ],
                start: '',
                end: '',
                years: Array.from({
                    length: 5
                }, (_, i) => now.getFullYear() - i),
                chart: null,
                loading: false,
                async load() {
                    this.loading = true;
                    const params = new URLSearchParams({
                        year: this.year,
                        month: this.month,
                        start: this.start,
                        end: this.end
                    });
                    try {
                        const res = await fetch(`/dashboard/sales/monthly?${params}`);
                        const data = await res.json();
                        this.render(data);
                    } finally {
                        this.loading = false;
                    }
                },
                render(data) {
                    if (this.chart) this.chart.destroy();
                    const ctx = document.getElementById('monthlyChart').getContext('2d');

                    this.chart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: data.labels,
                            datasets: [{
                                label: 'Penjualan',
                                data: data.values,
                                backgroundColor: '#ea580c', // Slate-800 agar kontras dengan chart line
                                hoverBackgroundColor: '#1e293b', // Berubah orange saat hover
                                borderRadius: 6,
                                borderSkipped: false,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    backgroundColor: '#0f172a',
                                    padding: 12,
                                    callbacks: {
                                        label: (c) => ' Rp ' + c.parsed.y.toLocaleString('id-ID')
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    grid: {
                                        color: '#f1f5f9'
                                    },
                                    ticks: {
                                        callback: v => 'Rp ' + (v >= 1000000 ? (v / 1000000) + 'jt' : v
                                            .toLocaleString('id-ID'))
                                    }
                                },
                                x: {
                                    grid: {
                                        display: false
                                    }
                                }
                            }
                        }
                    });
                },
                init() {
                    this.load();
                }
            }
        }
    </script>
@endsection
