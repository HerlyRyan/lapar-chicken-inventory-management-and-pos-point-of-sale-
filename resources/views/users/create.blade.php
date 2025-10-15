@extends('layouts.app')
@php use Illuminate\Support\Facades\Storage; @endphp

@section('title', 'Tambah Pengguna')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-orange-50 via-white to-red-50 py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Header Section --}}
            <div class="mb-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <div
                                class="w-10 h-10 bg-gradient-to-br from-orange-500 to-red-600 rounded-xl flex items-center justify-center shadow-lg">
                                <i class="bi bi-person-plus text-white text-lg"></i>
                            </div>
                            <div>
                                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">
                                    Tambah Pengguna Baru
                                </h1>
                                <p class="text-sm text-gray-600 mt-1">
                                    Tambahkan pengguna baru untuk sistem inventory dan penjualan
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="flex-shrink-0">
                        <a href="{{ route('users.index') }}"
                            class="inline-flex items-center px-4 py-2.5 bg-white border border-gray-300 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 shadow-sm hover:shadow-md">
                            <i class="bi bi-arrow-left mr-2"></i>
                            Kembali
                        </a>
                    </div>
                </div>
            </div>

            {{-- Main Form Card --}}
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                {{-- Card Header --}}
                <div class="bg-gradient-to-r from-orange-600 via-orange-700 to-red-700 px-6 py-6">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center mr-3">
                            <i class="bi bi-pencil-square text-white"></i>
                        </div>
                        <h2 class="text-xl font-bold text-white">Form Tambah Pengguna</h2>
                    </div>
                </div>

                {{-- Card Body --}}
                <div class="p-6 sm:p-8">
                    <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data" id="userForm"
                        onsubmit="return preparePhoneNumber()">
                        @csrf

                        {{-- Personal Information Section --}}
                        <div class="mb-8">
                            <div class="flex items-center mb-6">
                                <div
                                    class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center mr-3">
                                    <i class="bi bi-person text-white text-sm"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900">Informasi Personal</h3>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                {{-- Full Name --}}
                                <div class="lg:col-span-2">
                                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Nama Lengkap <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="name" id="name"
                                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 @error('name') border-red-300 ring-2 ring-red-200 @enderror"
                                        value="{{ old('name') }}" required placeholder="Masukkan nama lengkap">
                                    <p class="mt-2 text-sm text-gray-600">
                                        <i class="bi bi-info-circle mr-1"></i>Nama lengkap pengguna
                                    </p>
                                    @error('name')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Email --}}
                                <div>
                                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Email <span class="text-red-500">*</span>
                                    </label>
                                    <input type="email" name="email" id="email"
                                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 @error('email') border-red-300 ring-2 ring-red-200 @enderror"
                                        value="{{ old('email') }}" required placeholder="nama@contoh.com">
                                    <p class="mt-2 text-sm text-gray-600">
                                        <i class="bi bi-info-circle mr-1"></i>Email untuk login dan komunikasi
                                    </p>
                                    @error('email')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Phone Number --}}
                                <div>
                                    <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Nomor Telepon <span class="text-red-500">*</span>
                                    </label>
                                    <div
                                        class="flex rounded-xl border focus-within:ring-2 focus-within:ring-orange-500 focus-within:border-orange-500 transition-all duration-200 @error('phone') border-red-300 ring-2 ring-red-200 @enderror">
                                        <span
                                            class="inline-flex items-center px-4 text-gray-500 bg-gray-50 border-r border-gray-300 rounded-l-xl text-sm font-medium">
                                            +62
                                        </span>
                                        <input type="text" name="phone" id="phone"
                                            class="flex-1 px-4 py-3 border-0 rounded-r-xl focus:ring-0 focus:outline-none"
                                            value="{{ old('phone') }}" required placeholder="813xxxxxxxx"
                                            oninput="formatPhoneNumber(this)" maxlength="15">
                                    </div>
                                    <p class="mt-2 text-sm text-gray-600">
                                        <i class="bi bi-info-circle mr-1"></i>Format: 8-13 digit angka (untuk WhatsApp)
                                    </p>
                                    @error('phone')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Security Section --}}
                        <div class="mb-8">
                            <div class="flex items-center mb-6">
                                <div
                                    class="w-8 h-8 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center mr-3">
                                    <i class="bi bi-shield-lock text-white text-sm"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900">Keamanan</h3>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                {{-- Password --}}
                                <div>
                                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Kata Sandi <span class="text-red-500">*</span>
                                    </label>
                                    <input type="password" name="password" id="password"
                                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 @error('password') border-red-300 ring-2 ring-red-200 @enderror"
                                        required placeholder="Minimal 8 karakter">
                                    <p class="mt-2 text-sm text-gray-600">
                                        <i class="bi bi-info-circle mr-1"></i>Kata sandi untuk login sistem
                                    </p>
                                    @error('password')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Password Confirmation --}}
                                <div>
                                    <label for="password_confirmation"
                                        class="block text-sm font-semibold text-gray-700 mb-2">
                                        Konfirmasi Kata Sandi <span class="text-red-500">*</span>
                                    </label>
                                    <input type="password" name="password_confirmation" id="password_confirmation"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200"
                                        required placeholder="Ulangi kata sandi">
                                    <p class="mt-2 text-sm text-gray-600">
                                        <i class="bi bi-info-circle mr-1"></i>Konfirmasi password yang sama
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Role & Branch Section --}}
                        <div class="mb-8">
                            <div class="flex items-center mb-6">
                                <div
                                    class="w-8 h-8 bg-gradient-to-br from-green-500 to-green-600 rounded-lg flex items-center justify-center mr-3">
                                    <i class="bi bi-person-gear text-white text-sm"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900">Role & Cabang</h3>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                {{-- Branch --}}
                                <div>
                                    <label for="branch_id" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Cabang
                                    </label>
                                    <select name="branch_id" id="branch_id"
                                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 @error('branch_id') border-red-300 ring-2 ring-red-200 @enderror">
                                        <option value="">- Pilih Cabang -</option>
                                        @foreach ($branches as $branch)
                                            <option value="{{ $branch->id }}"
                                                {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                                {{ $branch->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <p class="mt-2 text-sm text-gray-600">
                                        <i class="bi bi-info-circle mr-1"></i>Cabang tempat user bekerja
                                    </p>
                                    @error('branch_id')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Role --}}
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Role <span class="text-red-500">*</span>
                                    </label>
                                    <div
                                        class="border rounded-xl p-4 max-h-48 overflow-y-auto bg-gray-50 @error('role_id') border-red-300 ring-2 ring-red-200 @enderror">
                                        @foreach ($roles as $role)
                                            <div class="flex items-start mb-3 last:mb-0">
                                                <input type="radio" name="role_id" id="role_{{ $role->id }}"
                                                    class="mt-1 w-4 h-4 text-orange-600 border-gray-300 focus:ring-orange-500"
                                                    value="{{ $role->id }}"
                                                    {{ old('role_id') == $role->id ? 'checked' : '' }}>
                                                <label class="ml-3 flex-1" for="role_{{ $role->id }}">
                                                    <span
                                                        class="block text-sm font-semibold text-gray-900">{{ $role->name }}</span>
                                                    @if (isset($role->description))
                                                        <span
                                                            class="block text-xs text-gray-600 mt-1">{{ $role->description }}</span>
                                                    @endif
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <p class="mt-2 text-sm text-gray-600">
                                        <i class="bi bi-info-circle mr-1"></i>Pilih satu role untuk user ini
                                    </p>
                                    @error('role_id')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Profile Photo Section --}}
                        <div class="mb-8">
                            <div class="flex items-center mb-6">
                                <div
                                    class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-lg flex items-center justify-center mr-3">
                                    <i class="bi bi-image text-white text-sm"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900">Foto Profil</h3>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
                                {{-- File Input --}}
                                <div>
                                    <label for="avatar" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Upload Foto
                                    </label>
                                    <input type="file" name="avatar" id="avatar"
                                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 @error('avatar') border-red-300 ring-2 ring-red-200 @enderror"
                                        accept="image/*" onchange="previewAvatar(this)">
                                    <p class="mt-2 text-sm text-gray-600">
                                        <i class="bi bi-info-circle mr-1"></i>Format: JPEG, PNG, JPG, GIF. Maksimal 2MB
                                    </p>
                                    @error('avatar')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Preview --}}
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Preview
                                    </label>
                                    <div id="avatar-preview-container"
                                        class="w-full h-40 bg-gray-50 border-2 border-dashed border-gray-300 rounded-xl flex items-center justify-center overflow-hidden">
                                        <div class="text-center text-gray-500" id="avatar-preview-text">
                                            <i class="bi bi-person-circle text-4xl mb-2 block"></i>
                                            <span class="text-sm">Preview foto akan tampil di sini</span>
                                        </div>
                                        <img id="avatar-preview" src="#" alt="Preview Foto Baru"
                                            class="hidden w-full h-full object-cover">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Status Section --}}
                        <div class="mb-8">
                            <div class="flex items-center mb-6">
                                <div
                                    class="w-8 h-8 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-lg flex items-center justify-center mr-3">
                                    <i class="bi bi-toggle-on text-white text-sm"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900">Status</h3>
                            </div>

                            <div class="bg-gray-50 rounded-xl p-4">
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" name="is_active" id="is_active"
                                        class="w-5 h-5 text-orange-600 border-gray-300 rounded focus:ring-orange-500"
                                        value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                    <span class="ml-3">
                                        <span class="text-sm font-semibold text-gray-900">Pengguna Aktif</span>
                                        <span class="block text-xs text-gray-600 mt-1">
                                            <i class="bi bi-info-circle mr-1"></i>Centang untuk mengaktifkan user
                                        </span>
                                    </span>
                                </label>
                            </div>
                        </div>

                        {{-- Form Actions --}}
                        <div class="border-t border-gray-200 pt-6">
                            <div class="flex flex-col sm:flex-row gap-3 sm:justify-end">
                                <a href="{{ route('users.index') }}"
                                    class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 rounded-xl text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 shadow-sm hover:shadow-md">
                                    Batal
                                </a>
                                <button type="submit"
                                    class="inline-flex items-center justify-center px-8 py-3 bg-gradient-to-r from-orange-600 to-red-600 border border-transparent rounded-xl text-sm font-medium text-white hover:from-orange-700 hover:to-red-700 focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                    <i class="bi bi-person-plus mr-2"></i>
                                    Simpan Pengguna
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
        // Preview avatar image when selected
        function previewAvatar(input) {
            const file = input.files[0];
            const preview = document.getElementById('avatar-preview');
            const previewText = document.getElementById('avatar-preview-text');

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                    previewText.classList.add('hidden');
                };
                reader.readAsDataURL(file);
            } else {
                preview.classList.add('hidden');
                previewText.classList.remove('hidden');
                preview.src = '#';
            }
        }

        // Format phone number to ensure it contains only digits
        function formatPhoneNumber(input) {
            // Remove any non-numeric characters
            let value = input.value.replace(/\D/g, '');

            // Ensure it's only digits
            input.value = value;

            // Update the form field with the formatted value
            if (value.length > 0) {
                // Check if it meets the pattern (8-13 digits)
                const isValid = /^\d{8,13}$/.test(value);

                // Visual feedback with Tailwind classes
                const parentDiv = input.closest('.flex');
                if (isValid) {
                    parentDiv.classList.remove('border-red-300', 'ring-2', 'ring-red-200');
                    parentDiv.classList.add('border-green-300', 'ring-2', 'ring-green-200');
                } else {
                    parentDiv.classList.remove('border-green-300', 'ring-2', 'ring-green-200');
                    if (value.length > 0) {
                        parentDiv.classList.add('border-red-300', 'ring-2', 'ring-red-200');
                    } else {
                        parentDiv.classList.remove('border-red-300', 'ring-2', 'ring-red-200');
                    }
                }
            } else {
                const parentDiv = input.closest('.flex');
                parentDiv.classList.remove('border-green-300', 'ring-2', 'ring-green-200', 'border-red-300', 'ring-2',
                    'ring-red-200');
            }
        }

        // Prepare the phone number before form submission
        function preparePhoneNumber() {
            const phoneInput = document.getElementById('phone');
            if (phoneInput && phoneInput.value) {
                // Replace the original phone value with the full number
                phoneInput.value = '62' + phoneInput.value;
            }
            return true;
        }
    </script>
@endpush
