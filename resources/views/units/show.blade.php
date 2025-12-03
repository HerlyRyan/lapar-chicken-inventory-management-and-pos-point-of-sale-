@extends('layouts.app')

@section('title', 'Detail Satuan')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-orange-50/30 to-red-50/30">
    {{-- Header Section --}}
    <div class="bg-gradient-to-r from-orange-900 via-orange-800 to-red-900 shadow-xl">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('units.index') }}"
                       class="inline-flex items-center justify-center w-10 h-10 bg-white/10 hover:bg-white/20 rounded-xl transition-all duration-200 backdrop-blur-sm border border-white/10 group">
                        <svg class="w-5 h-5 text-white group-hover:text-gray-200 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-2xl lg:text-3xl font-bold text-white">Detail Satuan</h1>
                        <p class="text-gray-200 mt-1">Informasi lengkap satuan</p>
                    </div>
                </div>

                <div class="flex items-center space-x-3">
                    <a href="{{ route('units.edit', $unit) }}"
                       class="inline-flex items-center px-4 py-2.5 bg-white/10 hover:bg-white/20 text-white rounded-xl font-medium transition-all duration-200 backdrop-blur-sm border border-white/20 group">
                        <svg class="w-4 h-4 mr-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit
                    </a>

                    <button onclick="confirmDelete()" 
                            class="inline-flex items-center px-4 py-2.5 bg-red-600/80 hover:bg-red-600 text-white rounded-xl font-medium transition-all duration-200 backdrop-blur-sm border border-red-500/30 group">
                        <svg class="w-4 h-4 mr-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Hapus
                    </button>

                    <form id="delete-form" action="{{ route('units.destroy', $unit) }}" method="POST" class="hidden">
                        @csrf
                        @method('DELETE')
                    </form>
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
                    <div class="px-8 py-12 text-center bg-gradient-to-r from-indigo-50 to-indigo-100">
                        <div class="inline-block mb-6">
                            <div class="w-28 h-28 rounded-full bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center mx-auto">
                                <span class="text-3xl font-bold text-gray-700">{{ strtoupper(substr($unit->abbreviation, 0, 1) ?: substr($unit->unit_name, 0, 1)) }}</span>
                            </div>
                        </div>

                        <h2 class="text-3xl font-bold text-gray-900 mb-1">{{ $unit->unit_name }}</h2>
                        <p class="text-sm text-gray-600 mb-3">{{ $unit->abbreviation }}</p>

                        @if($unit->is_active)
                            <span class="inline-flex items-center px-4 py-2 bg-green-100 text-green-800 rounded-full text-sm font-semibold">
                                <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                                Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-800 rounded-full text-sm font-semibold">
                                <div class="w-2 h-2 bg-gray-500 rounded-full mr-2"></div>
                                Tidak Aktif
                            </span>
                        @endif
                    </div>

                    <div class="p-8">
                        <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                            <svg class="w-5 h-5 mr-3 text-indigo-500" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                            </svg>
                            Informasi Satuan
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h4 class="font-semibold text-gray-700 mb-2">Nama Satuan</h4>
                                <p class="text-gray-800">{{ $unit->unit_name }}</p>
                            </div>

                            <div>
                                <h4 class="font-semibold text-gray-700 mb-2">Singkatan</h4>
                                <p class="text-gray-800">{{ $unit->abbreviation }}</p>
                            </div>

                            <div class="md:col-span-2">
                                <h4 class="font-semibold text-gray-700 mb-2">Deskripsi</h4>
                                <p class="text-gray-600">{{ $unit->description ?: 'Tidak ada deskripsi' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
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
                            <span class="text-gray-500">ID</span>
                            <span class="font-mono text-gray-700">{{ $unit->id }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Status</span>
                            <span class="text-gray-700">{{ $unit->is_active ? 'Aktif' : 'Tidak Aktif' }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Dibuat</span>
                            <span class="text-gray-700">{{ $unit->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Diperbarui</span>
                            <span class="text-gray-700">{{ $unit->updated_at->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-xl border border-gray-200/50 overflow-hidden">
                    <div class="bg-gradient-to-r from-green-500 to-teal-600 px-6 py-4">
                        <h3 class="text-lg font-bold text-white flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                            </svg>
                            Aksi
                        </h3>
                    </div>
                    <div class="p-6 space-y-3">
                        <a href="{{ route('units.index') }}" class="block w-full text-center px-4 py-2 bg-gray-50 hover:bg-gray-100 rounded-lg border">
                            Kembali ke Daftar
                        </a>
                        <a href="{{ route('units.edit', $unit) }}" class="block w-full text-center px-4 py-2 bg-blue-50 hover:bg-blue-100 rounded-lg border">
                            Edit Satuan
                        </a>
                        <form onsubmit="return confirm('Yakin ingin menghapus satuan ini?');" action="{{ route('units.destroy', $unit) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full text-center px-4 py-2 bg-red-50 hover:bg-red-100 rounded-lg border text-red-600">
                                Hapus Satuan
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete() {
    if (confirm('Yakin ingin menghapus satuan ini?')) {
        document.getElementById('delete-form').submit();
    }
}
</script>
@endsection
