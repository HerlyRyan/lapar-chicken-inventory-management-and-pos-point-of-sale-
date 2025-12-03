@extends('layouts.app')

@section('title', 'Edit Cabang')

@section('content')
    @php use Illuminate\Support\Facades\Storage; @endphp

    <div class="min-h-screen bg-gradient-to-br from-orange-50 via-white to-red-50 py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Header --}}
            <x-form.header edit="true" :name="$branch->name" title="Cabang" backRoute="{{ route('branches.index') }}"
                detailRoute="{{ route('branches.show', $branch) }}" />

            {{-- Card --}}
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                {{-- Card Header --}}
                <x-form.card-header title="Edit Cabang" type="edit" />


                <div class="p-6 sm:p-8">
                    <form action="{{ route('branches.update', $branch) }}" method="POST" id="branchForm"
                        onsubmit="return preparePhoneNumber()">
                        @csrf
                        @method('PUT')

                        {{-- Basic Info --}}
                        <div class="mb-8">
                            <div class="flex items-center mb-6">
                                <div
                                    class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center mr-3">
                                    <i class="bi bi-building text-white text-sm"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900">Informasi Cabang</h3>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <div class="lg:col-span-2">
                                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Nama Cabang
                                        <span class="text-red-500">*</span></label>
                                    <input type="text" name="name" id="name"
                                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 @error('name') border-red-300 ring-2 ring-red-200 @enderror"
                                        value="{{ old('name', $branch->name) }}" required
                                        placeholder="Contoh: Lapar Chicken Panjer">
                                    <p class="mt-2 text-sm text-gray-600"><i class="bi bi-info-circle mr-1"></i>Nama cabang
                                    </p>
                                    @error('name')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="code" class="block text-sm font-semibold text-gray-700 mb-2">Kode Cabang
                                        <span class="text-red-500">*</span></label>
                                    <input type="text" name="code" id="code"
                                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 @error('code') border-red-300 ring-2 ring-red-200 @enderror"
                                        value="{{ old('code', $branch->code) }}" required placeholder="Contoh: PNJ">
                                    <p class="mt-2 text-sm text-gray-600"><i class="bi bi-info-circle mr-1"></i>3-5 karakter
                                        unik</p>
                                    @error('code')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="type" class="block text-sm font-semibold text-gray-700 mb-2">Tipe Cabang
                                        <span class="text-red-500">*</span></label>
                                    <select name="type" id="type"
                                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 @error('type') border-red-300 ring-2 ring-red-200 @enderror"
                                        required>
                                        <option value="">- Pilih Tipe -</option>
                                        <option value="branch"
                                            {{ old('type', $branch->type) == 'branch' ? 'selected' : '' }}>Cabang Retail
                                        </option>
                                        <option value="production"
                                            {{ old('type', $branch->type) == 'production' ? 'selected' : '' }}>Pusat
                                            Produksi</option>
                                    </select>
                                    <p class="mt-2 text-sm text-gray-600"><i class="bi bi-info-circle mr-1"></i>Pilih tipe
                                        lokasi</p>
                                    @error('type')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="lg:col-span-2">
                                    <label for="address"
                                        class="block text-sm font-semibold text-gray-700 mb-2">Alamat</label>
                                    <textarea name="address" id="address" rows="3"
                                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 @error('address') border-red-300 ring-2 ring-red-200 @enderror"
                                        placeholder="Alamat lengkap">{{ old('address', $branch->address) }}</textarea>
                                    <p class="mt-2 text-sm text-gray-600"><i class="bi bi-info-circle mr-1"></i>Opsional.
                                        Alamat lengkap cabang</p>
                                    @error('address')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="phone"
                                        class="block text-sm font-semibold text-gray-700 mb-2">Telepon</label>
                                    <div
                                        class="flex rounded-xl border focus-within:ring-2 focus-within:ring-orange-500 focus-within:border-orange-500 transition-all duration-200 @error('phone') border-red-300 ring-2 ring-red-200 @enderror">
                                        <span
                                            class="inline-flex items-center px-4 text-gray-500 bg-gray-50 border-r border-gray-300 rounded-l-xl text-sm font-medium">+62</span>
                                        <input type="text" name="phone" id="phone"
                                            class="flex-1 px-4 py-3 border-0 rounded-r-xl focus:ring-0 focus:outline-none"
                                            value="{{ old('phone', ltrim($branch->phone ?? '', '62')) }}"
                                            placeholder="813xxxxxxxx" oninput="formatPhoneNumber(this)" maxlength="15">
                                    </div>
                                    <p class="mt-2 text-sm text-gray-600"><i class="bi bi-info-circle mr-1"></i>Format:
                                        8-13 digit angka</p>
                                    @error('phone')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="email"
                                        class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                                    <input type="email" name="email" id="email"
                                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 @error('email') border-red-300 ring-2 ring-red-200 @enderror"
                                        value="{{ old('email', $branch->email) }}" placeholder="cabang@contoh.com">
                                    <p class="mt-2 text-sm text-gray-600"><i class="bi bi-info-circle mr-1"></i>Opsional.
                                        Email cabang</p>
                                    @error('email')
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
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" name="is_active" id="is_active"
                                        class="w-5 h-5 text-orange-600 border-gray-300 rounded focus:ring-orange-500"
                                        value="1" {{ old('is_active', $branch->is_active) ? 'checked' : '' }}>
                                    <span class="ml-3">
                                        <span class="text-sm font-semibold text-gray-900">Cabang Aktif</span>
                                        <span class="block text-xs text-gray-600 mt-1"><i
                                                class="bi bi-info-circle mr-1"></i>Centang jika cabang masih
                                            beroperasi</span>
                                    </span>
                                </label>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="border-t border-gray-200 pt-6">
                            <div class="flex flex-col sm:flex-row gap-3 sm:justify-end">
                                <a href="{{ route('branches.index') }}"
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
        // Ensure phone input contains only digits and provide visual feedback
        function formatPhoneNumber(input) {
            let value = input.value.replace(/\D/g, '');
            input.value = value;

            const parentDiv = input.closest('.flex');
            if (!parentDiv) return;

            if (value.length >= 8 && value.length <= 13) {
                parentDiv.classList.remove('border-red-300', 'ring-2', 'ring-red-200');
                parentDiv.classList.add('border-green-300', 'ring-2', 'ring-green-200');
            } else if (value.length > 0) {
                parentDiv.classList.remove('border-green-300', 'ring-2', 'ring-green-200');
                parentDiv.classList.add('border-red-300', 'ring-2', 'ring-red-200');
            } else {
                parentDiv.classList.remove('border-green-300', 'ring-2', 'ring-green-200', 'border-red-300',
                    'ring-red-200');
            }
        }

        // Prefix phone with country code before submit
        function preparePhoneNumber() {
            const phoneInput = document.getElementById('phone');
            if (phoneInput && phoneInput.value) {
                phoneInput.value = '62' + phoneInput.value;
            }
            return true;
        }
    </script>
@endpush
