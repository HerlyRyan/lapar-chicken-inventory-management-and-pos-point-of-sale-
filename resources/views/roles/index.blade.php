@extends('layouts.app')

@section('title', 'Role & Hak Akses')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-orange-50/30 to-red-50/30">
        {{-- Page Header --}}
        <x-index.header title="Role & Hak Akses" addRoute="{{ route('roles.create') }}" />

        {{-- Main Content --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-8">
            <div x-data="sortableTable(@js($roles))" @sort-column.window="sortBy($event.detail)"
                class="bg-white rounded-lg sm:rounded-2xl shadow-lg sm:shadow-xl border border-gray-200 overflow-hidden">
                {{-- Card Header --}}
                <x-index.card-header title="Daftar Role" />

                {{-- Filter Section --}}
                <x-filter-bar searchPlaceholder="Cari role..." :selects="$selects" />

                {{-- Desktop Table --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <x-index.table-head :columns="$columns" />
                        <tbody class="bg-white divide-y divide-gray-200">
                            <template x-for="(role, index) in sortedRows" :key="role.id">
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="index + 1"></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900" x-text="role.name"></div>
                                            <div class="text-sm text-gray-500" x-text="role.code"></div>
                                            <template x-if="role.description">
                                                <div class="text-sm text-gray-500" x-text="role.description"></div>
                                            </template>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <div>
                                            <span class="font-semibold text-gray-900" x-text="role.users_count"></span>
                                            <small class="text-gray-500 block">users</small>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <div>
                                            <span class="font-semibold text-gray-900"
                                                x-text="role.permissions_count"></span>
                                            <small class="text-gray-500 block">permissions</small>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <template x-if="role.is_active">
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <div class="w-1.5 h-1.5 bg-green-400 rounded-full mr-1"></div>
                                                Aktif
                                            </span>
                                        </template>
                                        <template x-if="!role.is_active">
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                <div class="w-1.5 h-1.5 bg-gray-400 rounded-full mr-1"></div>
                                                Nonaktif
                                            </span>
                                        </template>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{-- Actions --}}
                                        <div x-data="{
                                            viewUrl: '/roles/' + role.id,
                                            editUrl: role.is_primary_super_admin ? null : '/roles/' + role.id + '/edit',
                                            deleteUrl: role.is_primary_super_admin ? null : '/roles/' + role.id,
                                            toggleUrl: null,
                                            itemName: 'Role ' + role.name,
                                            isActive: role.is_active
                                        }">
                                            <x-index.action-buttons :view="true" :edit="true" :delete="true"
                                                :toggle="false" />
                                        </div>
                                    </td>
                                </tr>
                            </template>
                            <template x-if="sortedRows.length === 0">
                                <x-index.none-data />
                            </template>
                        </tbody>
                    </table>
                </div>

                {{-- Mobile Cards --}}
                <div class="md:hidden divide-y divide-gray-200">
                    <template x-for="(role, index) in sortedRows" :key="role.id">
                        <div class="p-4 hover:bg-gray-50 transition-colors duration-150">
                            {{-- Role Header --}}
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-sm font-medium text-gray-900 truncate" x-text="role.name"></h3>
                                        <span class="text-xs text-gray-500 ml-2" x-text="`#${index + 1}`"></span>
                                    </div>
                                    <p class="text-sm text-gray-500 mt-1" x-text="role.code"></p>
                                    <template x-if="role.description">
                                        <p class="text-sm text-gray-500 mt-1" x-text="role.description"></p>
                                    </template>
                                </div>
                            </div>

                            {{-- Role Details --}}
                            <div class="space-y-2">
                                {{-- Users Count --}}
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500">Users:</span>
                                    <div>
                                        <span class="text-sm font-semibold text-gray-900" x-text="role.users_count"></span>
                                        <small class="text-gray-500 ml-1">users</small>
                                    </div>
                                </div>

                                {{-- Permissions Count --}}
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500">Permissions:</span>
                                    <div>
                                        <span class="text-sm font-semibold text-gray-900"
                                            x-text="role.permissions_count"></span>
                                        <small class="text-gray-500 ml-1">permissions</small>
                                    </div>
                                </div>

                                {{-- Status --}}
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500">Status:</span>
                                    <template x-if="role.is_active">
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <div class="w-1.5 h-1.5 bg-green-400 rounded-full mr-1"></div>
                                            Aktif
                                        </span>
                                    </template>
                                    <template x-if="!role.is_active">
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            <div class="w-1.5 h-1.5 bg-gray-400 rounded-full mr-1"></div>
                                            Nonaktif
                                        </span>
                                    </template>
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="mt-4 pt-3 border-t border-gray-200">
                                <div x-data="{
                                    viewUrl: '/roles/' + role.id,
                                    editUrl: role.is_primary_super_admin ? null : '/roles/' + role.id + '/edit',
                                    deleteUrl: role.is_primary_super_admin ? null : '/roles/' + role.id,
                                    toggleUrl: null,
                                    itemName: 'Role ' + role.name,
                                    isActive: role.is_active
                                }">
                                    <x-index.action-buttons :view="true" :edit="true" :delete="true"
                                        :toggle="false" />
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <template x-if="sortedRows.length !== 0">
                    <div class="pagination-wrapper">
                        {{ $pagination->links('vendor.pagination.tailwind') }}
                    </div>
                </template>
            </div>
        </div>
    </div>
@endsection
