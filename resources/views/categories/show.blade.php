@extends('layouts.app')

@php
use Illuminate\Support\Facades\Storage;
@endphp

@section('title', 'Detail Kategori')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-orange-50 via-red-50 to-orange-100">
    {{-- Header --}}
    <div class="bg-gradient-to-r from-orange-900 via-orange-800 to-red-900 shadow-xl">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('categories.index') }}"
                       class="inline-flex items-center justify-center w-10 h-10 bg-white/10 hover:bg-white/20 rounded-xl transition-all duration-200 backdrop-blur-sm border border-white/20 group">
                        <svg class="w-5 h-5 text-white group-hover:text-orange-200 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-2xl lg:text-3xl font-bold text-white">{{ $category->name }}</h1>
                        <p class="text-orange-200 mt-1">Detail informasi kategori</p>
                    </div>
                </div>

                <div class="flex items-center space-x-3">
                    <a href="{{ route('categories.edit', $category) }}"
                       class="inline-flex items-center px-4 py-2.5 bg-white/10 hover:bg-white/20 text-white rounded-xl font-medium transition-all duration-200 backdrop-blur-sm border border-white/20 group">
                        <svg class="w-4 h-4 mr-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit
                    </a>

                    @if(auth()->id() !== $category->id)
                        <button onclick="confirmDelete()"
                                class="inline-flex items-center px-4 py-2.5 bg-red-600/80 hover:bg-red-600 text-white rounded-xl font-medium transition-all duration-200 backdrop-blur-sm border border-red-500/30 group">
                            <svg class="w-4 h-4 mr-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Hapus
                        </button>

                        <form id="delete-form" action="{{ route('categories.destroy', $category) }}" method="POST" class="hidden">
                            @csrf
                            @method('DELETE')
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Main content --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Left / Main card --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-xl border border-gray-200/50 overflow-hidden">
                    <div class="relative bg-gradient-to-r from-orange-500 via-red-500 to-orange-600 px-8 py-12">
                        <div class="absolute inset-0 bg-black/10"></div>
                        <div class="relative text-center">
                            <div class="relative inline-block mb-6">
                                {{-- Category avatar / icon --}}
                                @if($category->icon ?? false)
                                    <img src="{{ Storage::url($category->icon) }}" alt="{{ $category->name }}"
                                         class="w-32 h-32 rounded-full border-4 border-white shadow-2xl object-cover">
                                @else
                                    <div class="w-32 h-32 rounded-full border-4 border-white shadow-2xl flex items-center justify-center"
                                         style="background: {{ $category->color ?? 'linear-gradient(135deg,#fb923c,#ef4444)' }};">
                                        <span class="text-4xl font-bold text-white">{{ strtoupper(substr($category->name, 0, 1)) }}</span>
                                    </div>
                                @endif

                                <div class="absolute -bottom-2 -right-2 w-8 h-8 bg-{{ $category->is_active ? 'green' : 'gray' }}-500 rounded-full border-4 border-white flex items-center justify-center">
                                    @if($category->is_active)
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                                    @else
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
                                    @endif
                                </div>
                            </div>

                            <h2 class="text-3xl font-bold text-white mb-2">{{ $category->name }}</h2>
                            <p class="text-orange-100 text-lg">Kode: <span class="font-mono">{{ $category->code }}</span></p>

                            <div class="mt-4">
                                @if($category->is_active)
                                    <span class="inline-flex items-center px-4 py-2 bg-green-100 text-green-800 rounded-full text-sm font-semibold">
                                        <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-800 rounded-full text-sm font-semibold">
                                        <div class="w-2 h-2 bg-gray-500 rounded-full mr-2"></div>
                                        Nonaktif
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="p-8">
                        <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                            <svg class="w-5 h-5 mr-3 text-orange-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                            Informasi Kategori
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="flex items-start space-x-4 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border border-blue-100">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M3 6h18v2H3z"/></svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h4 class="font-semibold text-gray-800 mb-1">Nama</h4>
                                    <p class="text-gray-600">{{ $category->name }}</p>
                                </div>
                            </div>

                            <div class="flex items-start space-x-4 p-4 bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl border border-purple-100">
                                <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M3 3h18v2H3z"/></svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h4 class="font-semibold text-gray-800 mb-1">Kode</h4>
                                    <p class="text-gray-600 font-mono">{{ $category->code }}</p>
                                </div>
                            </div>

                            <div class="flex items-start space-x-4 p-4 bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl border border-green-100">
                                <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3 7h7l-5.5 4 2 7L12 17l-6.5 3 2-7L2 9h7z"/></svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h4 class="font-semibold text-gray-800 mb-1">Total Produk</h4>
                                    <p class="text-gray-600">{{ $category->finishedProducts->count() }}</p>
                                </div>
                            </div>

                            @if($category->description)
                                <div class="md:col-span-2 p-4 bg-white rounded-xl border border-gray-100">
                                    <h4 class="font-semibold text-gray-700 mb-2">Deskripsi</h4>
                                    <p class="text-gray-600">{{ $category->description }}</p>
                                </div>
                            @endif

                            <div class="flex items-start space-x-4 p-4 bg-gradient-to-r from-orange-50 to-amber-50 rounded-xl border border-orange-100">
                                <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-amber-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 8V4l8 8-8 8v-4H4V8z"/></svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h4 class="font-semibold text-gray-800 mb-1">Dibuat</h4>
                                    <p class="text-sm text-gray-600">{{ $category->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>

                            <div class="flex items-start space-x-4 p-4 bg-gradient-to-r from-gray-50 to-slate-50 rounded-xl border border-gray-100">
                                <div class="w-10 h-10 bg-gradient-to-br from-sky-500 to-indigo-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 4v4l3-1 3 4-6 2v4"/></svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h4 class="font-semibold text-gray-800 mb-1">Terakhir Update</h4>
                                    <p class="text-sm text-gray-600">{{ $category->updated_at->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Products list --}}
                @if($category->finishedProducts->count() > 0)
                    <div class="mt-6">
                        <div class="bg-white rounded-2xl shadow-xl border border-gray-200/50 overflow-hidden">
                            <div class="bg-gradient-to-r from-emerald-500 to-green-600 px-6 py-4">
                                <h3 class="text-lg font-bold text-white">Produk dalam Kategori ({{ $category->finishedProducts->count() }})</h3>
                            </div>
                            <div class="p-4">
                                <div class="space-y-3">
                                    @foreach($category->finishedProducts as $product)
                                        <div class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 transition-colors border border-gray-100">
                                            <div class="flex items-center space-x-3">
                                                <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center text-white font-semibold">
                                                    {{ strtoupper(substr($product->name,0,1)) }}
                                                </div>
                                                <div>
                                                    <div class="font-medium text-gray-800">{{ $product->name }}</div>
                                                    <div class="text-sm text-gray-500"><code class="font-mono">{{ $product->code }}</code></div>
                                                </div>
                                            </div>
                                            <div class="flex items-center space-x-4">
                                                <div class="text-sm font-medium text-gray-700">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
                                                <div>
                                                    @if($product->is_active)
                                                        <span class="inline-flex items-center px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">Aktif</span>
                                                    @else
                                                        <span class="inline-flex items-center px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-sm">Nonaktif</span>
                                                    @endif
                                                </div>
                                                <a href="{{ route('finished-products.show', $product) }}" class="inline-flex items-center px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-sm text-gray-700 hover:bg-gray-50">
                                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 24 24"><path d="M12 5c-7 0-11 7-11 7s4 7 11 7 11-7 11-7-4-7-11-7zm0 12a5 5 0 110-10 5 5 0 010 10z"/></svg>
                                                    Lihat
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                {{-- Actions --}}
                <div class="bg-white rounded-2xl shadow-xl border border-gray-200/50 overflow-hidden">
                    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-4">
                        <h3 class="text-lg font-bold text-white flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 24 24"><path d="M12,1L3,5V11C3,16.55 6.84,21.74 12,23C17.16,21.74 21,16.55 21,11V5L12,1M10,17L6,13L7.41,11.59L10,14.17L16.59,7.58L18,9L10,17Z"/></svg>
                            Aksi
                        </h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <a href="{{ route('categories.edit', $category) }}" class="w-full inline-flex items-center justify-center px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-medium hover:bg-gray-50">
                            Edit Kategori
                        </a>

                        @if(auth()->id() !== $category->id)
                            <button onclick="confirmDelete()" class="w-full inline-flex items-center justify-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                                Hapus Kategori
                            </button>
                        @endif
                    </div>
                </div>

                {{-- System Info --}}
                <div class="bg-white rounded-2xl shadow-xl border border-gray-200/50 overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-600 to-gray-700 px-6 py-4">
                        <h3 class="text-lg font-bold text-white flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                            Info Sistem
                        </h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Kategori ID</span>
                            <span class="font-mono text-gray-700">{{ $category->id }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Dibuat</span>
                            <span class="text-gray-700">{{ $category->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Diperbarui</span>
                            <span class="text-gray-700">{{ $category->updated_at->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
function confirmDelete() {
    if (confirm("Apakah Anda yakin ingin menghapus kategori '{{ $category->name }}'?")) {
        document.getElementById('delete-form').submit();
    }
}
</script>
@endsection
