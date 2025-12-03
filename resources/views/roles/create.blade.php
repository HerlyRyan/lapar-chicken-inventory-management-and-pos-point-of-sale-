@extends('layouts.app')

@section('title', 'Tambah Role')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-orange-50 via-white to-red-50 py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Header --}}
            <x-form.header title="Role" backRoute="{{ route('roles.index') }}" />

            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                <x-form.card-header title="Tambah Role" type="add" />

                <div class="p-6 sm:p-8">
                    <form action="{{ route('roles.store') }}" method="POST" id="roleForm">
                        @csrf

                        <div class="mb-8">
                            <div class="flex items-center mb-6">
                                <div
                                    class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center mr-3">
                                    <i class="bi bi-shield-plus text-white text-sm"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900">Informasi Role</h3>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <div class="lg:col-span-2">
                                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Nama Role <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="name" id="name" required
                                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 @error('name') border-red-300 ring-2 ring-red-200 @enderror"
                                        value="{{ old('name') }}" placeholder="Contoh: Admin">
                                    @error('name')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="code" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Kode Role <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="code" id="code" maxlength="20" required
                                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 @error('code') border-red-300 ring-2 ring-red-200 @enderror"
                                        value="{{ old('code') }}" placeholder="Contoh: admin">
                                    <p class="mt-2 text-sm text-gray-600">
                                        <i class="bi bi-info-circle mr-1"></i>Kode unik (contoh: admin, cashier)
                                    </p>
                                    @error('code')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="is_active" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Status Aktif
                                    </label>
                                    <div class="bg-gray-50 rounded-xl p-4">
                                        <label class="flex items-center cursor-pointer">
                                            <input type="checkbox" name="is_active" id="is_active" value="1"
                                                class="w-5 h-5 text-orange-600 border-gray-300 rounded focus:ring-orange-500"
                                                {{ old('is_active', true) ? 'checked' : '' }}>
                                            <span class="ml-3 text-sm font-semibold text-gray-900">Role Aktif</span>
                                        </label>
                                    </div>
                                </div>

                                <div class="lg:col-span-2">
                                    <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Deskripsi
                                    </label>
                                    <textarea name="description" id="description" rows="3"
                                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 @error('description') border-red-300 ring-2 ring-red-200 @enderror"
                                        placeholder="Deskripsi singkat">{{ old('description') }}</textarea>
                                    @error('description')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="mb-4">
                            <h5 class="fw-bold mb-3 text-lg font-semibold" style="color: var(--primary-red);">
                                <i class="bi bi-key-fill me-2"></i>Permission
                            </h5>
                            <p class="text-sm text-gray-600 mb-4">Pilih permission yang akan diberikan kepada role ini:</p>

                            @foreach ($permissions as $category => $categoryPermissions)
                                <div class="bg-white border rounded-xl mb-4 shadow-sm">
                                    <div class="px-4 py-3 flex items-center justify-between border-b">
                                        <h6 class="mb-0 font-semibold text-gray-800">{{ $category }}</h6>
                                        <div class="flex items-center gap-2">
                                            <button type="button"
                                                class="inline-flex items-center px-3 py-1 text-sm rounded-xl border border-gray-300 bg-white hover:bg-gray-50"
                                                onclick="selectAllInCategory('{{ $category }}')">
                                                Pilih Semua
                                            </button>
                                            <button type="button"
                                                class="inline-flex items-center px-3 py-1 text-sm rounded-xl border border-gray-300 bg-white hover:bg-gray-50"
                                                onclick="deselectAllInCategory('{{ $category }}')">
                                                Batal Semua
                                            </button>
                                        </div>
                                    </div>
                                    <div class="p-4">
                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                            @foreach ($categoryPermissions as $permission)
                                                <label
                                                    class="flex items-start gap-3 p-3 border rounded-lg hover:shadow-sm cursor-pointer">
                                                    <input type="checkbox" class="permission-check mt-1"
                                                        id="permission_{{ $permission->id }}" name="permissions[]"
                                                        value="{{ $permission->id }}" data-category="{{ $category }}"
                                                        {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                                                    <div>
                                                        <div class="font-semibold text-gray-800">{{ $permission->name }}
                                                        </div>
                                                        @if ($permission->description)
                                                            <div class="text-sm text-gray-500">
                                                                {{ $permission->description }}</div>
                                                        @endif
                                                    </div>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            @error('permissions')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="border-t border-gray-200 pt-6">
                            <div class="flex flex-col sm:flex-row gap-3 sm:justify-end">
                                <a href="{{ route('roles.index') }}"
                                    class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 rounded-xl text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-all duration-200 shadow-sm">
                                    Batal
                                </a>
                                <button type="submit"
                                    class="inline-flex items-center justify-center px-8 py-3 bg-gradient-to-r from-orange-600 to-red-600 border border-transparent rounded-xl text-sm font-medium text-white hover:from-orange-700 hover:to-red-700 focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-all duration-200 shadow-lg">
                                    <i class="bi bi-plus-circle mr-2"></i> Simpan Role
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
    function selectAllInCategory(category) {
        document.querySelectorAll(`input.permission-check[data-category="${category}"]`).forEach(cb => cb.checked =
            true);
    }

    function deselectAllInCategory(category) {
        document.querySelectorAll(`input.permission-check[data-category="${category}"]`).forEach(cb => cb.checked =
            false);
    }
</script>
