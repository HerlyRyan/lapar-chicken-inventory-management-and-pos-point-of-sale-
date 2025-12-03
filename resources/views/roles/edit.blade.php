@extends('layouts.app')

@section('title', 'Edit Role')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-orange-50 via-white to-red-50 py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Header --}}
            <x-form.header edit="true" :name="$role->name" title="Role" backRoute="{{ route('roles.index') }}"
                detailRoute="{{ route('roles.show', $role) }}" />

            {{-- Card --}}
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                {{-- Card Header --}}
                <x-form.card-header title="Edit Role" type="edit" />

                <div class="p-6 sm:p-8">
                    @if (session('success'))
                        <div class="mb-4 rounded-xl bg-green-50 border border-green-200 p-4">
                            <div class="flex items-start gap-3">
                                <div class="text-green-600">
                                    <i class="bi bi-check-circle-fill"></i>
                                </div>
                                <div class="text-sm text-green-800">{{ session('success') }}</div>
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('roles.update', $role) }}" method="POST" id="roleForm">
                        @csrf
                        @method('PUT')

                        {{-- Informasi Role --}}
                        <div class="mb-8">
                            <div class="flex items-center mb-6">
                                <div
                                    class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center mr-3">
                                    <i class="bi bi-shield-exclamation text-white text-sm"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900">Informasi Role</h3>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <div class="lg:col-span-2">
                                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Nama Role
                                        <span class="text-red-500">*</span></label>
                                    <input type="text" name="name" id="name"
                                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 @error('name') border-red-300 ring-2 ring-red-200 @enderror"
                                        value="{{ old('name', $role->name) }}" required placeholder="Contoh: Admin">
                                    <p class="mt-2 text-sm text-gray-600"><i class="bi bi-info-circle mr-1"></i>Nama role
                                    </p>
                                    @error('name')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="code" class="block text-sm font-semibold text-gray-700 mb-2">Kode Role
                                        <span class="text-red-500">*</span></label>
                                    <input type="text" name="code" id="code"
                                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 @error('code') border-red-300 ring-2 ring-red-200 @enderror"
                                        value="{{ old('code', $role->code) }}" required placeholder="Contoh: admin">
                                    <p class="mt-2 text-sm text-gray-600"><i class="bi bi-info-circle mr-1"></i>Kode unik
                                        untuk role</p>
                                    @error('code')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="lg:col-span-2">
                                    <label for="description"
                                        class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi</label>
                                    <textarea name="description" id="description" rows="3"
                                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 @error('description') border-red-300 ring-2 ring-red-200 @enderror"
                                        placeholder="Deskripsi role (opsional)">{{ old('description', $role->description) }}</textarea>
                                    @error('description')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Status Aktif</label>
                                    <div class="bg-gray-50 rounded-xl p-4">
                                        <label class="flex items-center cursor-pointer">
                                            <input type="checkbox" name="is_active" id="is_active"
                                                class="w-5 h-5 text-orange-600 border-gray-300 rounded focus:ring-orange-500"
                                                value="1" {{ old('is_active', $role->is_active) ? 'checked' : '' }}>
                                            <span class="ml-3">
                                                <span class="text-sm font-semibold text-gray-900">Role Aktif</span>
                                                <span class="block text-xs text-gray-600 mt-1"><i
                                                        class="bi bi-info-circle mr-1"></i>Centang jika role aktif</span>
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Permission --}}
                        <div class="mb-8">
                            <div class="flex items-center mb-6">
                                <div
                                    class="w-8 h-8 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-lg flex items-center justify-center mr-3">
                                    <i class="bi bi-key-fill text-white text-sm"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900">Permission</h3>
                            </div>

                            <div class="mb-4">
                                <div class="flex gap-2 flex-wrap">
                                    <button type="button" onclick="selectAllPermissions()"
                                        class="inline-flex items-center px-3 py-2 rounded-xl bg-green-50 border border-green-200 text-sm text-green-700 hover:bg-green-100">
                                        <i class="bi bi-check-all mr-2"></i> Pilih Semua
                                    </button>
                                    <button type="button" onclick="deselectAllPermissions()"
                                        class="inline-flex items-center px-3 py-2 rounded-xl bg-yellow-50 border border-yellow-200 text-sm text-yellow-700 hover:bg-yellow-100">
                                        <i class="bi bi-x-square mr-2"></i> Hapus Semua
                                    </button>
                                </div>
                            </div>

                            @foreach ($permissions as $category => $categoryPermissions)
                                <div class="mb-6">
                                    <h6
                                        class="font-semibold text-sm text-orange-600 mb-3 uppercase flex items-center gap-2">
                                        <i class="bi bi-folder-fill"></i> {{ $category ?: 'UMUM' }}
                                    </h6>

                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                        @foreach ($categoryPermissions as $permission)
                                            <label
                                                class="flex items-start gap-3 p-3 rounded-xl border hover:shadow-sm transition">
                                                <input type="checkbox" class="permission-checkbox mt-1" name="permissions[]"
                                                    value="{{ $permission->id }}" id="permission_{{ $permission->id }}"
                                                    {{ in_array($permission->id, old('permissions', $rolePermissions)) ? 'checked' : '' }}>
                                                <div>
                                                    <div class="text-sm font-semibold text-gray-900">
                                                        {{ $permission->name }}</div>
                                                    @if ($permission->description)
                                                        <div class="text-xs text-gray-500 mt-1">
                                                            {{ $permission->description }}</div>
                                                    @endif
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach

                            @if ($permissions->isEmpty())
                                <div class="rounded-xl bg-blue-50 border border-blue-200 p-4 text-sm text-blue-800">
                                    <i class="bi bi-info-circle mr-2"></i> Belum ada permission yang tersedia.
                                    <a href="{{ route('permissions.index') }}" class="underline">Kelola Permission</a>
                                </div>
                            @endif
                        </div>

                        {{-- Actions --}}
                        <div class="border-t border-gray-200 pt-6">
                            <div class="flex flex-col sm:flex-row gap-3 sm:justify-end">
                                <a href="{{ route('roles.index') }}"
                                    class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 rounded-xl text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 shadow-sm hover:shadow-md">
                                    Batal
                                </a>
                                <button type="submit"
                                    class="inline-flex items-center justify-center px-8 py-3 bg-gradient-to-r from-orange-600 to-red-600 border border-transparent rounded-xl text-sm font-medium text-white hover:from-orange-700 hover:to-red-700 focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                    <i class="bi bi-check-circle mr-2"></i> Update Role
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
    function selectAllPermissions() {
        document.querySelectorAll('.permission-checkbox').forEach(cb => cb.checked = true);
    }

    function deselectAllPermissions() {
        document.querySelectorAll('.permission-checkbox').forEach(cb => cb.checked = false);
    }
</script>
