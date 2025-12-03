@extends('layouts.app')
@php use Illuminate\Support\Facades\Storage; @endphp

@section('title', 'Tambah Satuan')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-orange-50 via-white to-red-50 py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Header --}}
            <x-form.header title="Satuan" backRoute="{{ route('units.index') }}" />

            {{-- Card --}}
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                <x-form.card-header title="Tambah Satuan" type="add" />

                <div class="p-6 sm:p-8">
                    <form action="{{ route('units.store') }}" method="POST" id="unitForm">
                        @csrf

                        <div class="mb-8">
                            <div class="flex items-center mb-6">
                                <div
                                    class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center mr-3">
                                    <i class="bi bi-rulers text-white text-sm"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900">Informasi Satuan</h3>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                {{-- Nama Satuan (span 2) --}}
                                <div class="lg:col-span-2">
                                    <label for="unit_name" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Nama Satuan <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="unit_name" id="unit_name"
                                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 @error('unit_name') border-red-300 ring-2 ring-red-200 @enderror"
                                        value="{{ old('unit_name') }}" required placeholder="Contoh: Kilogram, Liter, Pieces">
                                    <p class="mt-2 text-sm text-gray-600">
                                        <i class="bi bi-info-circle mr-1"></i>Nama lengkap satuan yang akan digunakan
                                    </p>
                                    @error('unit_name')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Singkatan --}}
                                <div>
                                    <label for="abbreviation" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Singkatan <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="abbreviation" id="abbreviation" maxlength="10"
                                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 @error('abbreviation') border-red-300 ring-2 ring-red-200 @enderror"
                                        value="{{ old('abbreviation') }}" required placeholder="Contoh: kg, ltr, pcs"
                                        style="text-transform: lowercase;">
                                    <p class="mt-2 text-sm text-gray-600">
                                        <i class="bi bi-info-circle mr-1"></i>Singkatan unik untuk satuan (maksimal 10 karakter)
                                    </p>
                                    @error('abbreviation')
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
                                        placeholder="Deskripsi satuan (opsional)">{{ old('description') }}</textarea>
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
                                                <span class="text-sm font-semibold text-gray-900">Satuan Aktif</span>
                                                <span class="block text-xs text-gray-600 mt-1">
                                                    <i class="bi bi-info-circle mr-1"></i>Centang jika satuan boleh digunakan saat menambah produk
                                                </span>
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="border-t border-gray-200 pt-6">
                            <div class="flex flex-col sm:flex-row gap-3 sm:justify-end">
                                <a href="{{ route('units.index') }}"
                                    class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 rounded-xl text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 shadow-sm hover:shadow-md">
                                    Batal
                                </a>
                                <button type="submit"
                                    class="inline-flex items-center justify-center px-8 py-3 bg-gradient-to-r from-orange-600 to-red-600 border border-transparent rounded-xl text-sm font-medium text-white hover:from-orange-700 hover:to-red-700 focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                    <i class="bi bi-check-lg mr-2"></i>
                                    Simpan Satuan
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
            const nameInput = document.getElementById('unit_name');
            const abbreviationInput = document.getElementById('abbreviation');

            // Helper to mark manual edit
            abbreviationInput.addEventListener('input', function() {
                this.dataset.manuallyEdited = 'true';
                this.value = this.value.toLowerCase().replace(/\s+/g, '');
            });

            nameInput.addEventListener('input', function() {
                if (abbreviationInput.dataset.manuallyEdited === 'true') return;

                const raw = this.value.trim();
                if (!raw) {
                    abbreviationInput.value = '';
                    return;
                }

                const words = raw.split(/\s+/).filter(Boolean);
                let abbr = '';

                if (words.length === 1) {
                    abbr = words[0].substring(0, 3);
                } else {
                    abbr = words.map(w => w.charAt(0)).join('');
                }

                abbreviationInput.value = abbr.toLowerCase().substring(0, 10);
            });
        });
    </script>
@endpush
