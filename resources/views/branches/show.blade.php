@extends('layouts.app')

@php
use Illuminate\Support\Facades\Storage;
@endphp

@section('title', 'Detail Cabang - ' . $branch->name)

@section('content')
<div class="min-h-screen bg-gradient-to-br from-orange-50 via-red-50 to-orange-100">
    {{-- Header --}}
    <div class="bg-gradient-to-r from-orange-900 via-orange-800 to-red-900 shadow-xl">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('branches.index') }}"
                       class="inline-flex items-center justify-center w-10 h-10 bg-white/10 hover:bg-white/20 rounded-xl transition-all duration-200 backdrop-blur-sm border border-white/20 group">
                        <svg class="w-5 h-5 text-white group-hover:text-orange-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-2xl lg:text-3xl font-bold text-white">Detail Cabang</h1>
                        <p class="text-orange-200 mt-1">Detail informasi cabang {{ $branch->name }}</p>
                    </div>
                </div>

                <div class="flex items-center space-x-3">
                    <a href="{{ route('branches.edit', $branch) }}"
                       class="inline-flex items-center px-4 py-2.5 bg-white/10 hover:bg-white/20 text-white rounded-xl font-medium transition-all duration-200 backdrop-blur-sm border border-white/20 group">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit
                    </a>

                    @if(auth()->id() !== $branch->id)
                        <form id="delete-branch-form" action="{{ route('branches.destroy', $branch) }}" method="POST" class="inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="button" onclick="confirmDelete()" class="inline-flex items-center px-4 py-2.5 bg-red-600/80 hover:bg-red-600 text-white rounded-xl font-medium transition-all duration-200 backdrop-blur-sm border border-red-500/30 group">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Hapus
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Content --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Main Card --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-xl border border-gray-200/50 overflow-hidden">
                    <div class="relative bg-gradient-to-r from-orange-500 via-red-500 to-orange-600 px-8 py-12">
                        <div class="absolute inset-0 bg-black/10"></div>
                        <div class="relative text-center">
                            {{-- Avatar / Icon --}}
                            <div class="relative inline-block mb-6">
                                <div class="w-32 h-32 rounded-full border-4 border-white shadow-2xl bg-gradient-to-br from-orange-400 to-red-500 flex items-center justify-center">
                                    <i class="bi bi-shop text-white" style="font-size: 2rem;"></i>
                                </div>
                            </div>

                            <h2 class="text-3xl font-bold text-white mb-2">{{ $branch->name }}</h2>
                            <p class="text-orange-100 text-lg">{{ $branch->code ?? '-' }}</p>

                            <div class="mt-4">
                                @if($branch->type === 'production')
                                    <span class="inline-flex items-center px-4 py-2 bg-yellow-100 text-yellow-800 rounded-full text-sm font-semibold">
                                        <div class="w-2 h-2 bg-yellow-500 rounded-full mr-2"></div>
                                        Pusat Produksi
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-4 py-2 bg-red-100 text-red-800 rounded-full text-sm font-semibold">
                                        <div class="w-2 h-2 bg-red-500 rounded-full mr-2"></div>
                                        Cabang Retail
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="p-8">
                        <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                            <svg class="w-5 h-5 mr-3 text-orange-500" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                            </svg>
                            Informasi Cabang
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Address --}}
                            @if($branch->address)
                                <div class="flex items-start space-x-4 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border border-blue-100">
                                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/>
                                        </svg>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <h4 class="font-semibold text-gray-800 mb-1">Alamat</h4>
                                        <p class="text-gray-600">{{ $branch->address }}</p>
                                    </div>
                                </div>
                            @endif

                            {{-- Phone --}}
                            <div class="flex items-start space-x-4 p-4 bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl border border-green-100">
                                <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/>
                                    </svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h4 class="font-semibold text-gray-800 mb-1">Telepon</h4>
                                    @if($branch->phone)
                                        <p class="text-gray-600">{{ $branch->getFormattedPhone() }}</p>
                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $branch->phone) }}?text={{ urlencode('Halo, saya ingin menanyakan tentang cabang ' . $branch->name) }}" target="_blank" class="inline-flex items-center mt-2 text-sm text-green-600 hover:text-green-700 font-medium">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M17.472 14.382..."/>
                                            </svg>
                                            WhatsApp
                                        </a>
                                    @else
                                        <p class="text-gray-400">Tidak tersedia</p>
                                    @endif
                                </div>
                            </div>

                            {{-- Email --}}
                            <div class="flex items-start space-x-4 p-4 bg-gradient-to-r from-indigo-50 to-purple-50 rounded-xl border border-purple-100">
                                <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                                    </svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h4 class="font-semibold text-gray-800 mb-1">Email</h4>
                                    @if($branch->email)
                                        <p class="text-gray-600">{{ $branch->email }}</p>
                                        <a href="mailto:{{ $branch->email }}" class="inline-flex items-center mt-2 text-sm text-blue-600 hover:text-blue-700 font-medium">
                                            Kirim Email
                                        </a>
                                    @else
                                        <p class="text-gray-400">Tidak tersedia</p>
                                    @endif
                                </div>
                            </div>

                            {{-- Created --}}
                            <div class="flex items-start space-x-4 p-4 bg-gradient-to-r from-orange-50 to-amber-50 rounded-xl border border-orange-100">
                                <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-amber-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/>
                                    </svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h4 class="font-semibold text-gray-800 mb-1">Dibuat</h4>
                                    <p class="text-sm text-gray-600">{{ $branch->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>

                            {{-- Updated --}}
                            <div class="flex items-start space-x-4 p-4 bg-gradient-to-r from-gray-50 to-slate-50 rounded-xl border border-gray-100">
                                <div class="w-10 h-10 bg-gradient-to-br from-sky-500 to-cyan-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2a10 10 0 100 20 10 10 0 000-20zM11 6h2v6h-2V6zm0 8h2v2h-2v-2z"/>
                                    </svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h4 class="font-semibold text-gray-800 mb-1">Terakhir Update</h4>
                                    <p class="text-sm text-gray-600">{{ $branch->updated_at->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                {{-- Actions --}}
                <div class="bg-white rounded-2xl shadow-xl border border-gray-200/50 overflow-hidden">
                    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-4">
                        <h3 class="text-lg font-bold text-white flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12,1L3,5V11C3,16.55 6.84,21.74 12,23C17.16,21.74 21,16.55 21,11V5L12,1M10,17L6,13L7.41,11.59L10,14.17L16.59,7.58L18,9L10,17Z"/>
                            </svg>
                            Aksi
                        </h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <a href="{{ route('branches.edit', $branch) }}" class="w-full inline-flex items-center justify-center px-4 py-2 bg-white/10 hover:bg-white/20 text-gray-800 rounded-xl border border-gray-200">
                            Edit Cabang
                        </a>

                        @if($branch->phone)
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $branch->phone) }}" target="_blank" class="w-full inline-flex items-center justify-center px-4 py-2 bg-green-50 hover:bg-green-100 rounded-xl border border-green-100">
                                Hubungi via WhatsApp
                            </a>
                        @endif

                        @if($branch->email)
                            <a href="mailto:{{ $branch->email }}" class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-50 hover:bg-blue-100 rounded-xl border border-blue-100">
                                Kirim Email
                            </a>
                        @endif
                    </div>
                </div>

                {{-- Users list --}}
                <div class="bg-white rounded-2xl shadow-xl border border-gray-200/50 overflow-hidden">
                    <div class="bg-gradient-to-r from-orange-500 to-red-600 px-6 py-4">
                        <h3 class="text-lg font-bold text-white flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5s-3 1.34-3 3 1.34 3 3 3zM8 11c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5s-3 1.34-3 3 1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V20h14v-3.5C15 14.17 10.33 13 8 13zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V20h6v-3.5C23 14.17 18.33 13 16 13z"/>
                            </svg>
                            Daftar Pengguna Cabang
                        </h3>
                    </div>
                    <div class="p-6">
                        @if($branch->users->count() > 0)
                            <ul class="space-y-3">
                                @foreach($branch->users as $user)
                                    <li class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-100">
                                        <div>
                                            <h6 class="mb-0 font-semibold text-gray-800">{{ $user->name }}</h6>
                                            <small class="text-gray-500">{{ $user->role->name ?? 'N/A' }}</small>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            @if($user->phone)
                                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $user->phone) }}?text={{ urlencode('Halo ' . $user->name . ', saya ingin menghubungi Anda terkait cabang ' . $branch->name) }}" target="_blank" class="inline-flex items-center px-3 py-1.5 bg-green-500 text-white rounded-lg text-sm">
                                                    <i class="bi bi-whatsapp"></i>
                                                    <span class="ml-2">Chat</span>
                                                </a>
                                            @endif
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-muted text-center mb-0 text-gray-400">Belum ada pengguna yang terkait dengan cabang ini.</p>
                        @endif
                    </div>
                </div>

                {{-- System Info --}}
                <div class="bg-white rounded-2xl shadow-xl border border-gray-200/50 overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-600 to-gray-700 px-6 py-4">
                        <h3 class="text-lg font-bold text-white flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                            </svg>
                            Info Sistem
                        </h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Branch ID</span>
                            <span class="font-mono text-gray-700">{{ $branch->id }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Dibuat</span>
                            <span class="text-gray-700">{{ $branch->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Diperbarui</span>
                            <span class="text-gray-700">{{ $branch->updated_at->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Delete confirm script --}}
    <script>
        function confirmDelete() {
            if (confirm("Apakah Anda yakin ingin menghapus cabang '{{ addslashes($branch->name) }}'?")) {
                document.getElementById('delete-branch-form').submit();
            }
        }
    </script>
</div>
@endsection
