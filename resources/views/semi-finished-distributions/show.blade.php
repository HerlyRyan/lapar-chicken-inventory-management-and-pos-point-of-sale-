@extends('layouts.app')

@php
    use Illuminate\Support\Facades\Storage;
@endphp

@section('title', 'Detail Distribusi Bahan Setengah Jadi')

@section('content')
    <div class="min-h-screen bg-gradient-to-br bg-orange-">
        {{-- Header --}}
        <div class="bg-gradient-to-r from-orange-900 via-orange-800 to-red-900 shadow-xl">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex items-center space-x-4">
                        <a href="{{ redirect()->back()->getTargetUrl() }}"
                            class="inline-flex items-center justify-center w-10 h-10 bg-white/10 hover:bg-white/20 rounded-xl transition-all duration-200 backdrop-blur-sm border border-white/20 group">
                            <svg class="w-5 h-5 text-white group-hover:text-orange-200 transition-colors" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </a>
                        <div>
                            <h1 class="text-2xl lg:text-3xl font-bold text-white">Detail Distribusi</h1>
                            <p class="text-orange-200 mt-1">Kode: {{ $distribution->distribution_code }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Content --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- Main --}}
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl shadow-xl border border-gray-200/50 overflow-hidden">
                        {{-- Status Header --}}
                        <div class="relative bg-gradient-to-r from-orange-500 via-red-500 to-orange-600 px-8 py-8">
                            <div class="absolute inset-0 bg-black/10"></div>
                            <div class="relative">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h2 class="text-2xl font-bold text-white mb-2">
                                            {{ $distribution->semiFinishedProduct->name ?? '-' }}</h2>
                                        <p class="text-orange-100">Distribusi ke:
                                            {{ $distribution->targetBranch->name ?? '-' }}</p>
                                    </div>
                                    <span
                                        class="badge {{ $distribution->status === 'sent' ? 'bg-yellow-100 text-yellow-800' : ($distribution->status === 'accepted' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') }} px-4 py-2 rounded-full font-semibold text-uppercase">
                                        {{ strtoupper($distribution->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Body --}}
                        <div class="p-8">
                            <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                                <svg class="w-5 h-5 mr-3 text-orange-500" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                                </svg>
                                Informasi Distribusi
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div
                                    class="p-4 bg-gradient-to-r from-orange-50 to-indigo-50 rounded-xl border border-orange-100">
                                    <h4 class="font-semibold text-gray-800 mb-2">Cabang Tujuan</h4>
                                    <div class="text-lg font-bold text-gray-900">
                                        {{ $distribution->targetBranch->name ?? '-' }}</div>
                                </div>

                                <div
                                    class="p-4 bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl border border-purple-100">
                                    <h4 class="font-semibold text-gray-800 mb-2">Dikirim Oleh</h4>
                                    <div class="text-lg font-bold text-gray-900">{{ $distribution->sentBy->name ?? '-' }}
                                    </div>
                                </div>

                                <div
                                    class="p-4 bg-gradient-to-r from-yellow-50 to-amber-50 rounded-xl border border-yellow-100">
                                    <h4 class="font-semibold text-gray-800 mb-2">Jumlah</h4>
                                    <div class="text-lg font-bold text-gray-900">
                                        {{ number_format($distribution->quantity_sent, 0, ',', '.') }}
                                        {{ $distribution->semiFinishedProduct->unit->name ?? 'unit' }}</div>
                                </div>

                                <div
                                    class="p-4 bg-gradient-to-r from-green-50 to-teal-50 rounded-xl border border-green-100">
                                    <h4 class="font-semibold text-gray-800 mb-2">Total Biaya</h4>
                                    <div class="text-lg font-bold text-green-600">Rp
                                        {{ number_format($distribution->total_cost, 0, ',', '.') }}</div>
                                </div>
                            </div>

                            @if ($distribution->distribution_notes)
                                <div class="p-4 bg-white rounded-xl border border-gray-100 mb-6">
                                    <h4 class="font-semibold text-gray-800 mb-2">Catatan Distribusi</h4>
                                    <p class="text-gray-600">{{ $distribution->distribution_notes }}</p>
                                </div>
                            @endif

                            @if ($distribution->status !== 'sent')
                                <div class="border-t pt-6">
                                    <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                                        <svg class="w-5 h-5 mr-3 text-indigo-500" fill="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                                        </svg>
                                        Informasi Tanggapan
                                    </h3>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                        <div
                                            class="p-4 bg-gradient-to-r from-indigo-50 to-orange-50 rounded-xl border border-indigo-100">
                                            <h4 class="font-semibold text-gray-800 mb-2">Ditangani Oleh</h4>
                                            <div class="text-lg font-bold text-gray-900">
                                                {{ $distribution->handledBy->name ?? '-' }}</div>
                                        </div>

                                        <div
                                            class="p-4 bg-gradient-to-r from-cyan-50 to-orange-50 rounded-xl border border-cyan-100">
                                            <h4 class="font-semibold text-gray-800 mb-2">Tanggal Tanggapan</h4>
                                            <div class="text-lg font-bold text-gray-900">
                                                {{ optional($distribution->handled_at)->format('d M Y H:i') ?: '-' }}</div>
                                        </div>
                                    </div>

                                    @if ($distribution->response_notes)
                                        <div class="p-4 bg-white rounded-xl border border-gray-100">
                                            <h4 class="font-semibold text-gray-800 mb-2">Catatan Tanggapan</h4>
                                            <p class="text-gray-600">{{ $distribution->response_notes }}</p>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Sidebar --}}
                <div class="space-y-6">
                    {{-- Summary Card --}}
                    <div class="bg-white rounded-2xl shadow-xl border border-gray-200/50 overflow-hidden sticky top-6">
                        <div class="bg-gradient-to-r from-orange-500 via-red-500 to-orange-600 px-6 py-4">
                            <h3 class="text-lg font-bold text-white flex items-center">
                                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                                </svg>
                                Ringkasan
                            </h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="flex justify-between items-center pb-3 border-b">
                                <span class="text-gray-600">Status</span>
                                <span
                                    class="badge {{ $distribution->status === 'sent' ? 'bg-yellow-100 text-yellow-800' : ($distribution->status === 'accepted' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') }} font-semibold">
                                    {{ strtoupper($distribution->status) }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center pb-3 border-b">
                                <span class="text-gray-600">Jumlah</span>
                                <span
                                    class="font-bold text-gray-900">{{ number_format($distribution->quantity_sent, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Total Biaya</span>
                                <span class="text-2xl font-bold text-orange-600">Rp
                                    {{ number_format($distribution->total_cost, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- System Info --}}
                    <div class="bg-white rounded-2xl shadow-xl border border-gray-200/50 overflow-hidden">
                        <div class="bg-gradient-to-r from-gray-600 to-gray-700 px-6 py-4">
                            <h3 class="text-lg font-bold text-white flex items-center">
                                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                                </svg>
                                Info Sistem
                            </h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-500">Dibuat</span>
                                <span
                                    class="text-gray-700">{{ optional($distribution->created_at)->format('d/m/Y H:i') }}</span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-500">Diperbarui</span>
                                <span
                                    class="text-gray-700">{{ optional($distribution->updated_at)->format('d/m/Y H:i') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
