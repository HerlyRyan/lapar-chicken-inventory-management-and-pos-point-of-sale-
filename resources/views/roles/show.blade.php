@extends('layouts.app')

@php
use Illuminate\Support\Facades\Storage;
@endphp

@section('title', 'Detail Role - ' . $role->name)

@section('content')
<div class="min-h-screen bg-gradient-to-br from-orange-50 via-red-50 to-orange-100">
    {{-- Header --}}
    <div class="bg-gradient-to-r from-orange-900 via-orange-800 to-red-900 shadow-xl">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('roles.index') }}"
                       class="inline-flex items-center justify-center w-10 h-10 bg-white/10 hover:bg-white/20 rounded-xl transition-all duration-200 backdrop-blur-sm border border-white/20 group">
                        <svg class="w-5 h-5 text-white group-hover:text-orange-200 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-2xl lg:text-3xl font-bold text-white">{{ $role->name }}</h1>
                        <p class="text-orange-200 mt-1">Detail role dan permission</p>
                    </div>
                </div>

                <div class="flex items-center space-x-3">
                    <a href="{{ route('roles.edit', $role) }}"
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
                </div>
            </div>
        </div>
    </div>

    {{-- Main --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Left: Role Card + Permissions --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Role Header Card --}}
                <div class="bg-white rounded-2xl shadow-xl border border-gray-200/50 overflow-hidden">
                    <div class="relative bg-gradient-to-r from-orange-500 via-red-500 to-orange-600 px-8 py-12">
                        <div class="absolute inset-0 bg-black/10"></div>
                        <div class="relative text-center">
                            <div class="relative inline-block mb-6">
                                <div class="w-32 h-32 rounded-full border-4 border-white shadow-2xl bg-gradient-to-br from-orange-400 to-red-500 flex items-center justify-center">
                                    <span class="text-4xl font-bold text-white">{{ strtoupper(substr($role->name, 0, 1)) }}</span>
                                </div>
                                <div class="absolute -bottom-2 -right-2 w-8 h-8 {{ $role->is_active ? 'bg-green-500' : 'bg-gray-400' }} rounded-full border-4 border-white flex items-center justify-center">
                                    @if($role->is_active)
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                                        </svg>
                                    @endif
                                </div>
                            </div>

                            <h2 class="text-3xl font-bold text-white mb-2">{{ $role->name }}</h2>
                            <p class="text-orange-100 text-lg">{{ $role->code }}</p>

                            <div class="mt-4">
                                @if($role->is_active)
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
                            <svg class="w-5 h-5 mr-3 text-orange-500" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                            </svg>
                            Informasi Role
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h4 class="font-semibold text-gray-700 mb-1">Nama Role</h4>
                                <p class="text-gray-800 font-medium">{{ $role->name }}</p>
                            </div>

                            <div>
                                <h4 class="font-semibold text-gray-700 mb-1">Kode</h4>
                                <p class="text-gray-800 font-mono"><code class="bg-gray-100 px-2 py-1 rounded">{{ $role->code }}</code></p>
                            </div>

                            <div class="md:col-span-2">
                                <h4 class="font-semibold text-gray-700 mb-1">Deskripsi</h4>
                                <p class="text-gray-600">{{ $role->description ?? 'Tidak ada deskripsi' }}</p>
                            </div>

                            <div>
                                <h4 class="font-semibold text-gray-700 mb-1">Dibuat</h4>
                                <p class="text-sm text-gray-600">{{ $role->created_at->format('d/m/Y H:i') }}</p>
                            </div>

                            <div>
                                <h4 class="font-semibold text-gray-700 mb-1">Diperbarui</h4>
                                <p class="text-sm text-gray-600">{{ $role->updated_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Permissions Card --}}
                <div class="bg-white rounded-2xl shadow-xl border border-gray-200/50 overflow-hidden">
                    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-4">
                        <h3 class="text-lg font-bold text-white flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12,1L3,5V11C3,16.55 6.84,21.74 12,23C17.16,21.74 21,16.55 21,11V5L12,1M10,17L6,13L7.41,11.59L10,14.17L16.59,7.58L18,9L10,17Z"/>
                            </svg>
                            Permission ({{ $role->permissions->count() }})
                        </h3>
                    </div>

                    <div class="p-6">
                        @if($role->permissions->count() > 0)
                            @php
                                $grouped = $role->permissions->groupBy('category');
                            @endphp

                            @foreach($grouped as $category => $perms)
                                <div class="mb-6">
                                    <h4 class="font-semibold text-gray-800 mb-3 flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-primary" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M3 13h2v-2H3v2zm0 4h2v-2H3v2zm0-8h2V7H3v2zm4 8h14v-2H7v2zm0-4h14v-2H7v2zm0-6v2h14V7H7z"/>
                                        </svg>
                                        {{ $category }} <span class="ml-2 inline-block bg-indigo-100 text-indigo-700 text-xs px-2 py-0.5 rounded-full">{{ $perms->count() }}</span>
                                    </h4>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        @foreach($perms as $permission)
                                            <div class="p-3 bg-gray-50 rounded-lg border border-gray-100 flex items-start">
                                                <svg class="w-5 h-5 text-green-500 mr-3 mt-1" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                                                </svg>
                                                <div>
                                                    <div class="font-medium text-gray-800">{{ $permission->name }}</div>
                                                    @if($permission->description)
                                                        <div class="text-sm text-gray-500">{{ $permission->description }}</div>
                                                    @endif
                                                    <div class="text-xs text-gray-400 mt-1"><code>{{ $permission->code }}</code></div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center text-muted py-6">
                                <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zM7 12l3 3 7-7-1.41-1.41L10 12.17 8.41 10.59 7 12z"/>
                                </svg>
                                <h5 class="text-gray-700 font-semibold">Belum ada permission</h5>
                                <p class="text-sm text-gray-500">Role ini belum memiliki permission apapun</p>
                                <div class="mt-4">
                                    <a href="{{ route('roles.edit', $role) }}" class="inline-flex items-center px-4 py-2 bg-white/10 hover:bg-white/20 text-indigo-700 rounded-xl border border-indigo-100">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 4l-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8z"/>
                                        </svg>
                                        Tambah Permission
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Right Sidebar --}}
            <div class="space-y-6">
                {{-- Users with this Role --}}
                <div class="bg-white rounded-2xl shadow-xl border border-gray-200/50 overflow-hidden">
                    <div class="bg-gradient-to-r from-green-500 to-teal-600 px-6 py-4">
                        <h3 class="text-lg font-bold text-white flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5s-3 1.34-3 3 1.34 3 3 3zM8 11c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5 5 6.34 5 8s1.34 3 3 3zM8 13c-2.33 0-7 1.17-7 3.5V19h14v-2.5C15 14.17 10.33 13 8 13zM16 13c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                            </svg>
                            Users ({{ $role->users->count() }})
                        </h3>
                    </div>

                    <div class="p-6">
                        @if($role->users->count() > 0)
                            <div class="space-y-3">
                                @foreach($role->users as $user)
                                    <div class="flex items-center p-3 bg-gray-50 rounded-lg border border-gray-100">
                                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-orange-400 to-red-500 text-white flex items-center justify-center font-semibold mr-3">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <div class="font-medium text-gray-800">{{ $user->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-6 text-gray-500">
                                <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5s-3 1.34-3 3 1.34 3 3 3zM8 11c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5 5 6.34 5 8s1.34 3 3 3zM8 13c-2.33 0-7 1.17-7 3.5V19h14v-2.5C15 14.17 10.33 13 8 13z"/>
                                </svg>
                                Belum ada user dengan role ini
                            </div>
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
                            <span class="text-gray-500">Role ID</span>
                            <span class="font-mono text-gray-700">{{ $role->id }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Dibuat</span>
                            <span class="text-gray-700">{{ $role->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Diperbarui</span>
                            <span class="text-gray-700">{{ $role->updated_at->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Delete form --}}
    <form id="delete-form" action="{{ route('roles.destroy', $role) }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>
</div>

@endsection
