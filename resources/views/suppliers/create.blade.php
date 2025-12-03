@extends('layouts.app')
@php use Illuminate\Support\Facades\Storage; @endphp

@section('title', 'Tambah Supplier')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-orange-50 via-white to-red-50 py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Header Section --}}
            <x-form.header title="Supplier" backRoute="{{ route('suppliers.index') }}" />

            {{-- Main Form Card --}}
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                {{-- Card Header --}}
                <x-form.card-header title="Tambah Supplier" type="add" />

                {{-- Card Body --}}
                <div class="p-6 sm:p-8">
                    <form action="{{ route('suppliers.store') }}" method="POST" id="supplierForm"
                        onsubmit="return preparePhoneNumber()">
                        @csrf

                        <div class="mb-8">
                            <div class="flex items-center mb-6">
                                <div
                                    class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center mr-3">
                                    <i class="bi bi-people text-white text-sm"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900">Informasi Supplier</h3>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                {{-- Nama Supplier (span 2) --}}
                                <div class="lg:col-span-2">
                                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Nama Supplier <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="name" id="name"
                                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 @error('name') border-red-300 ring-2 ring-red-200 @enderror"
                                        value="{{ old('name') }}" required
                                        placeholder="Contoh: PT. Bahan Makanan Sejahtera">
                                    <p class="mt-2 text-sm text-gray-600">
                                        <i class="bi bi-info-circle mr-1"></i>Nama lengkap perusahaan atau supplier
                                    </p>
                                    @error('name')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Kode Supplier --}}
                                <div>
                                    <label for="code" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Kode Supplier <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="code" id="code" maxlength="10"
                                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 @error('code') border-red-300 ring-2 ring-red-200 @enderror"
                                        value="{{ old('code') }}" required placeholder="Contoh: SUP001">
                                    <p class="mt-2 text-sm text-gray-600">
                                        <i class="bi bi-info-circle mr-1"></i>Kode unik untuk identifikasi supplier
                                    </p>
                                    @error('code')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Alamat (textarea span 2) --}}
                                <div class="lg:col-span-2">
                                    <label for="address" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Alamat
                                    </label>
                                    <textarea name="address" id="address" rows="3"
                                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 @error('address') border-red-300 ring-2 ring-red-200 @enderror"
                                        placeholder="Alamat lengkap supplier">{{ old('address') }}</textarea>
                                    <p class="mt-2 text-sm text-gray-600">
                                        <i class="bi bi-info-circle mr-1"></i>Alamat lengkap supplier (opsional)
                                    </p>
                                    @error('address')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Telepon --}}
                                <div>
                                    <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Telepon
                                    </label>
                                    <div
                                        class="flex rounded-xl border focus-within:ring-2 focus-within:ring-orange-500 focus-within:border-orange-500 transition-all duration-200 @error('phone') border-red-300 ring-2 ring-red-200 @enderror">
                                        <span
                                            class="inline-flex items-center px-4 text-gray-500 bg-gray-50 border-r border-gray-300 rounded-l-xl text-sm font-medium">
                                            +62
                                        </span>
                                        <input type="text" name="phone" id="phone"
                                            class="flex-1 px-4 py-3 border-0 rounded-r-xl focus:ring-0 focus:outline-none"
                                            value="{{ old('phone') }}" placeholder="Contoh: 812xxxxxxxx"
                                            oninput="formatPhoneNumber(this)" maxlength="15">
                                    </div>
                                    <p class="mt-2 text-sm text-gray-600">
                                        <i class="bi bi-info-circle mr-1"></i>Format: 62xxxxxxxxxx (tanpa tanda +)
                                    </p>
                                    @error('phone')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Email --}}
                                <div>
                                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Email
                                    </label>
                                    <input type="email" name="email" id="email"
                                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 @error('email') border-red-300 ring-2 ring-red-200 @enderror"
                                        value="{{ old('email') }}" placeholder="Contoh: supplier@email.com">
                                    <p class="mt-2 text-sm text-gray-600">
                                        <i class="bi bi-info-circle mr-1"></i>Email untuk komunikasi bisnis (opsional)
                                    </p>
                                    @error('email')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Status --}}
                                <div class="lg:col-span-2">
                                    <div class="bg-gray-50 rounded-xl p-4">
                                        <input type="hidden" name="is_active" value="0">
                                        <label class="flex items-center cursor-pointer">
                                            <input type="checkbox" name="is_active" id="is_active"
                                                class="w-5 h-5 text-orange-600 border-gray-300 rounded focus:ring-orange-500"
                                                value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                            <span class="ml-3">
                                                <span class="text-sm font-semibold text-gray-900">Supplier Aktif</span>
                                                <span class="block text-xs text-gray-600 mt-1">
                                                    <i class="bi bi-info-circle mr-1"></i>Centang jika supplier masih aktif
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
                                <a href="{{ route('suppliers.index') }}"
                                    class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 rounded-xl text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 shadow-sm hover:shadow-md">
                                    Batal
                                </a>
                                <button type="submit"
                                    class="inline-flex items-center justify-center px-8 py-3 bg-gradient-to-r from-orange-600 to-red-600 border border-transparent rounded-xl text-sm font-medium text-white hover:from-orange-700 hover:to-red-700 focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                    <i class="bi bi-plus-circle mr-2"></i>
                                    Simpan Supplier
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

<script>
    // Format phone number to ensure it contains only digits and provide visual feedback
    function formatPhoneNumber(input) {
        let value = input.value.replace(/\D/g, '');
        input.value = value;

        const parentDiv = input.closest('.flex');
        if (!parentDiv) return;

        if (value.length > 0) {
            const isValid = /^\d{8,13}$/.test(value);
            parentDiv.classList.remove('border-green-300', 'ring-2', 'ring-green-200', 'border-red-300',
                'ring-red-200');
            if (isValid) {
                parentDiv.classList.add('border-green-300', 'ring-2', 'ring-green-200');
            } else {
                parentDiv.classList.add('border-red-300', 'ring-2', 'ring-red-200');
            }
        } else {
            parentDiv.classList.remove('border-green-300', 'ring-2', 'ring-green-200', 'border-red-300', 'ring-2',
                'ring-red-200');
        }
    }

    // Prepare the phone number before form submission (prepend country code 62)
    function preparePhoneNumber() {
        const phoneInput = document.getElementById('phone');
        if (phoneInput && phoneInput.value) {
            phoneInput.value = '62' + phoneInput.value;
        }
        return true;
    }
</script>
