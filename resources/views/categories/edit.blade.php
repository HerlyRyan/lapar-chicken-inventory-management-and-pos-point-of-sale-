@extends('layouts.app')

@section('title', 'Edit Kategori')

@section('content')
    @php use Illuminate\Support\Facades\Storage; @endphp

    <div class="min-h-screen bg-gradient-to-br from-orange-50 via-white to-red-50 py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Header --}}
            <x-form.header edit="true" :name="$category->name" title="Kategori" backRoute="{{ route('categories.index') }}"
                detailRoute="{{ route('categories.show', $category) }}" />

            {{-- Card --}}
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                {{-- Card Header --}}
                <x-form.card-header title="Edit Kategori" type="edit" />

                <div class="p-6 sm:p-8">
                    <form action="{{ route('categories.update', $category) }}" method="POST" id="categoryForm">
                        @csrf
                        @method('PUT')

                        {{-- Informasi Kategori --}}
                        <div class="mb-8">
                            <div class="flex items-center mb-6">
                                <div
                                    class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center mr-3">
                                    <i class="bi bi-tag text-white text-sm"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900">Informasi Kategori</h3>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <div class="lg:col-span-2">
                                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Nama
                                        Kategori <span class="text-red-500">*</span></label>
                                    <input type="text" name="name" id="name"
                                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 @error('name') border-red-300 ring-2 ring-red-200 @enderror"
                                        value="{{ old('name', $category->name) }}" required
                                        placeholder="Contoh: Ayam Goreng">
                                    <p class="mt-2 text-sm text-gray-600"><i class="bi bi-info-circle mr-1"></i>Nama
                                        kategori</p>
                                    @error('name')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="code" class="block text-sm font-semibold text-gray-700 mb-2">Kode
                                        Kategori <span class="text-red-500">*</span></label>
                                    <input type="text" name="code" id="code"
                                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 @error('code') border-red-300 ring-2 ring-red-200 @enderror"
                                        value="{{ old('code', $category->code) }}" required placeholder="Contoh: AG"
                                        style="text-transform:uppercase">
                                    <p class="mt-2 text-sm text-gray-600"><i class="bi bi-info-circle mr-1"></i>Kode unik (otomatis huruf besar)</p>
                                    @error('code')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="lg:col-span-2">
                                    <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi</label>
                                    <textarea name="description" id="description" rows="3"
                                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 @error('description') border-red-300 ring-2 ring-red-200 @enderror"
                                        placeholder="Deskripsi kategori (opsional)">{{ old('description', $category->description) }}</textarea>
                                    <p class="mt-2 text-sm text-gray-600"><i class="bi bi-info-circle mr-1"></i>Opsional.
                                        Deskripsi singkat</p>
                                    @error('description')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Status --}}
                        <div class="mb-8">
                            <div class="flex items-center mb-6">
                                <div
                                    class="w-8 h-8 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-lg flex items-center justify-center mr-3">
                                    <i class="bi bi-toggle-on text-white text-sm"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900">Status</h3>
                            </div>

                            <div class="bg-gray-50 rounded-xl p-4">
                                {{-- Hidden input ensures false is submitted when unchecked --}}
                                <input type="hidden" name="is_active" value="0">
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" name="is_active" id="is_active"
                                        class="w-5 h-5 text-orange-600 border-gray-300 rounded focus:ring-orange-500"
                                        value="1" {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                                    <span class="ml-3">
                                        <span class="text-sm font-semibold text-gray-900">Kategori Aktif</span>
                                        <span class="block text-xs text-gray-600 mt-1"><i class="bi bi-info-circle mr-1"></i>Centang jika kategori dapat dipilih saat menambah produk</span>
                                    </span>
                                </label>
                            </div>
                        </div>

                        {{-- Info Produk Terkait --}}
                        @if($category->finishedProducts->count() > 0)
                            <div class="mb-6">
                                <div class="bg-blue-50 border border-blue-100 text-blue-900 rounded-lg p-4">
                                    <i class="bi bi-info-circle-fill mr-2"></i>
                                    <strong>Informasi:</strong> Kategori ini memiliki {{ $category->finishedProducts->count() }} produk terkait.
                                    Perubahan akan mempengaruhi semua produk dalam kategori ini.
                                </div>
                            </div>
                        @endif

                        {{-- Actions --}}
                        <div class="border-t border-gray-200 pt-6">
                            <div class="flex flex-col sm:flex-row gap-3 sm:justify-end">
                                <a href="{{ route('categories.index') }}"
                                    class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 rounded-xl text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 shadow-sm hover:shadow-md">
                                    Batal
                                </a>
                                <button type="submit"
                                    class="inline-flex items-center justify-center px-8 py-3 bg-gradient-to-r from-orange-600 to-red-600 border border-transparent rounded-xl text-sm font-medium text-white hover:from-orange-700 hover:to-red-700 focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                    <i class="bi bi-check-circle mr-2"></i> Simpan Perubahan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Ensure code input is uppercase
            const codeInput = document.getElementById('code');
            if (codeInput) {
                codeInput.addEventListener('input', function() {
                    this.value = this.value.toUpperCase();
                });
            }
        });
    </script>
@endpush
