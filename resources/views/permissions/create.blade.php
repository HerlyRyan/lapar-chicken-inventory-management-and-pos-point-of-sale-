@extends('layouts.app')

@section('title', 'Tambah Hak Akses')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-orange-50 via-white to-red-50 py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Header Section --}}
            <x-form.header title="Hak Akses" backRoute="{{ route('permissions.index') }}" />

            {{-- Main Form Card --}}
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                {{-- Card Header --}}
                <x-form.card-header title="Tambah Hak Akses" type="add" />

                {{-- Card Body --}}
                <div class="p-6 sm:p-8">
                    <form action="{{ route('permissions.store') }}" method="POST">
                        @csrf

                        <div class="mb-8">
                            <div class="flex items-center mb-6">
                                <div
                                    class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center mr-3">
                                    <i class="bi bi-shield-lock text-white text-sm"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900">Informasi Hak Akses</h3>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                {{-- Nama Hak Akses (span 2) --}}
                                <div class="lg:col-span-2">
                                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Nama Hak Akses <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="name" id="name"
                                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 @error('name') border-red-300 ring-2 ring-red-200 @enderror"
                                        value="{{ old('name') }}" required placeholder="Contoh: Lihat Laporan, Edit Data">
                                    <p class="mt-2 text-sm text-gray-600">
                                        <i class="bi bi-info-circle mr-1"></i>Contoh: Lihat Laporan, Edit Data, Hapus Data
                                    </p>
                                    @error('name')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Kode Hak Akses --}}
                                <div>
                                    <label for="code" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Kode Hak Akses <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="code" id="code" maxlength="50"
                                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 @error('code') border-red-300 ring-2 ring-red-200 @enderror"
                                        value="{{ old('code') }}" required placeholder="Contoh: view_reports">
                                    <p class="mt-2 text-sm text-gray-600">
                                        <i class="bi bi-info-circle mr-1"></i>Gunakan underscore untuk pemisah (mis: view_reports)
                                    </p>
                                    @error('code')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Grup --}}
                                <div>
                                    <label for="group" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Grup
                                    </label>
                                    <input type="text" name="group" id="group"
                                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 @error('group') border-red-300 ring-2 ring-red-200 @enderror"
                                        value="{{ old('group') }}" placeholder="Contoh: Laporan, Data Master">
                                    <p class="mt-2 text-sm text-gray-600">
                                        <i class="bi bi-info-circle mr-1"></i>Kelompokkan hak akses agar mudah dikelola
                                    </p>
                                    @error('group')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Deskripsi (span 2) --}}
                                <div class="lg:col-span-2">
                                    <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Deskripsi
                                    </label>
                                    <textarea name="description" id="description" rows="3"
                                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 @error('description') border-red-300 ring-2 ring-red-200 @enderror"
                                        placeholder="Deskripsi detail tentang hak akses ini">{{ old('description') }}</textarea>
                                    <p class="mt-2 text-sm text-gray-600">
                                        <i class="bi bi-info-circle mr-1"></i>Opsional. Jelaskan apa yang boleh dilakukan pemegang hak akses
                                    </p>
                                    @error('description')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Status --}}
                                <div class="lg:col-span-2">
                                    <div class="bg-gray-50 rounded-xl p-4">
                                        <label class="flex items-center cursor-pointer">
                                            <input type="checkbox" name="is_active" id="is_active"
                                                class="w-5 h-5 text-orange-600 border-gray-300 rounded focus:ring-orange-500"
                                                value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                            <span class="ml-3">
                                                <span class="text-sm font-semibold text-gray-900">Aktif</span>
                                                <span class="block text-xs text-gray-600 mt-1">
                                                    <i class="bi bi-info-circle mr-1"></i>Centang jika hak akses ini aktif
                                                </span>
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Form Actions --}}
                        <div class="border-t border-gray-200 pt-6">
                            <div class="flex flex-col sm:flex-row gap-3 sm:justify-end">
                                <a href="{{ route('permissions.index') }}"
                                    class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 rounded-xl text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 shadow-sm hover:shadow-md">
                                    Batal
                                </a>
                                <button type="submit"
                                    class="inline-flex items-center justify-center px-8 py-3 bg-gradient-to-r from-orange-600 to-red-600 border border-transparent rounded-xl text-sm font-medium text-white hover:from-orange-700 hover:to-red-700 focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                    <i class="bi bi-save mr-2"></i>
                                    Simpan Hak Akses
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
