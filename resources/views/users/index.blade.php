@extends('layouts.app')

@php
    use Illuminate\Support\Facades\Storage;
@endphp

@section('title', 'Data Pengguna')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-orange-50/30 to-red-50/30">
        {{-- Page Header --}}
        <x-index.header title="Pengguna" addRoute="{{ route('users.create') }}" />

        {{-- Main Content --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-8">
            <div x-data="sortableTable(@js($users))" @sort-column.window="sortBy($event.detail)"
                class="bg-white rounded-lg sm:rounded-2xl shadow-lg sm:shadow-xl border border-gray-200 overflow-hidden">
                {{-- Card Header --}}
                <x-index.card-header title="Pengguna" />

                {{-- Filter Section --}}
                <x-filter-bar searchPlaceholder="Cari nama atau email..." :selects="$selects" />

                {{-- Desktop Table --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <x-index.table-head :columns="$columns" />
                        <tbody class="bg-white divide-y divide-gray-200">
                            <template x-for="(user, index) in sortedRows" :key="user.id">
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="index + 1"></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 mr-3">
                                                <template x-if="user.avatar">
                                                    <img :src="'/storage/' + user.avatar" :alt="user.name"
                                                        class="h-10 w-10 rounded-full object-cover shadow-md">
                                                </template>
                                                <template x-if="!user.avatar">
                                                    <div class="h-10 w-10 rounded-full bg-gradient-to-br from-orange-400 to-red-500 flex items-center justify-center text-white font-bold shadow-md"
                                                        x-text="user.name.charAt(0).toUpperCase()">
                                                    </div>
                                                </template>
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900" x-text="user.name"></div>
                                                <div class="flex items-center gap-2">
                                                    <span class="text-sm text-gray-500" x-text="user.email"></span>
                                                    <template x-if="user.phone">
                                                        <a :href="'https://wa.me/' + user.phone + '?text=Halo%20' +
                                                            encodeURIComponent(user.name)"
                                                            target="_blank"
                                                            class="inline-flex items-center justify-center w-5 h-5 bg-green-500 hover:bg-green-600 text-white rounded-full shadow-sm transition-all duration-200"
                                                            title="WhatsApp">
                                                            <x-whatsapp-logo />
                                                        </a>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <div class="flex items-center gap-2">
                                            <span x-text="user.email"></span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <div class="flex items-center gap-2">
                                            <span x-text="user.branch ? user.branch.name : '-'"></span>
                                            <template x-if="user.branch && user.branch.phone">
                                                <a :href="'https://wa.me/' + user.branch.phone + '?text=Halo%20' +
                                                    encodeURIComponent(user.branch.name) || '#'"
                                                    target="_blank"
                                                    class="inline-flex items-center justify-center w-5 h-5 bg-green-500 hover:bg-green-600 text-white rounded-full shadow-sm transition-all duration-200"
                                                    title="WhatsApp Cabang">
                                                    <x-whatsapp-logo />
                                                </a>
                                            </template>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <template x-if="user.roles && user.roles.length > 0">
                                            <div class="flex flex-wrap gap-1">
                                                <template x-for="role in user.roles" :key="role.id">
                                                    <span
                                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
                                                        x-text="role.name">
                                                    </span>
                                                </template>
                                            </div>
                                        </template>
                                        <template x-if="!user.roles || user.roles.length === 0">
                                            <span class="text-gray-500">-</span>
                                        </template>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <template x-if="user.is_active">
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <div class="w-1.5 h-1.5 bg-green-400 rounded-full mr-1"></div>
                                                Aktif
                                            </span>
                                        </template>
                                        <template x-if="!user.is_active">
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
                                            viewUrl: '/users/' + user.id,
                                            editUrl: '/users/' + user.id + '/edit',
                                            deleteUrl: '/users/' + user.id,
                                            toggleUrl: '/users/' + user.id + '/toggle',
                                            itemName: 'User ' + user.name,
                                            isActive: user.is_active,
                                            isSuperAdmin() {
                                                return this.user.roles?.some(r => r.name === 'Super Admin')
                                            }
                                        }">
                                            <template x-if="isSuperAdmin()">
                                                <x-index.action-buttons :view="true" />
                                            </template>

                                            <template x-if="!isSuperAdmin()">
                                                <x-index.action-buttons :view="true" :edit="true"
                                                    :delete="true" :toggle="false" />
                                            </template>

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
                    <template x-for="(user, index) in sortedRows" :key="user.id">
                        <div class="p-4 hover:bg-gray-50 transition-colors duration-150">
                            {{-- User Header --}}
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex items-center space-x-3 flex-1">
                                    <div class="flex-shrink-0">
                                        <template x-if="user.avatar">
                                            <img :src="'/storage/' + user.avatar" :alt="user.name"
                                                class="h-12 w-12 rounded-full object-cover shadow-md">
                                        </template>
                                        <template x-if="!user.avatar">
                                            <div class="h-12 w-12 rounded-full bg-gradient-to-br from-orange-400 to-red-500 flex items-center justify-center text-white font-bold shadow-md"
                                                x-text="user.name.charAt(0).toUpperCase()">
                                            </div>
                                        </template>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between">
                                            <h3 class="text-sm font-medium text-gray-900 truncate" x-text="user.name"></h3>
                                            <span class="text-xs text-gray-500 ml-2" x-text="`#${index + 1}`"></span>
                                        </div>
                                        <div class="flex items-center gap-2 mt-1">
                                            <p class="text-sm text-gray-500 truncate" x-text="user.email"></p>
                                            <template x-if="user.phone">
                                                <a :href="'https://wa.me/' + user.phone + '?text=Halo%20' +
                                                    encodeURIComponent(user.name)"
                                                    target="_blank"
                                                    class="inline-flex items-center justify-center w-5 h-5 bg-green-500 hover:bg-green-600 text-white rounded-full shadow-sm transition-all duration-200"
                                                    title="WhatsApp">
                                                    <x-whatsapp-logo />
                                                </a>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- User Details --}}
                            <div class="space-y-2">
                                {{-- Branch --}}
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500">Cabang:</span>
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm text-gray-900"
                                            x-text="user.branch ? user.branch.name : '-'"></span>
                                        <template x-if="user.branch && user.branch.phone">
                                            <a :href="user.branch.whatsapp_link || '#'" target="_blank"
                                                class="inline-flex items-center justify-center w-4 h-4 bg-green-500 hover:bg-green-600 text-white rounded-full shadow-sm transition-all duration-200"
                                                title="WhatsApp Cabang">
                                                <x-whatsapp-logo class="w-2 h-2" />
                                            </a>
                                        </template>
                                    </div>
                                </div>

                                {{-- Roles --}}
                                <div class="flex items-start justify-between">
                                    <span class="text-sm text-gray-500">Role:</span>
                                    <div class="flex-1 ml-4">
                                        <template x-if="user.roles && user.roles.length > 0">
                                            <div class="flex flex-wrap gap-1 justify-end">
                                                <template x-for="role in user.roles" :key="role.id">
                                                    <span
                                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
                                                        x-text="role.name">
                                                    </span>
                                                </template>
                                            </div>
                                        </template>
                                        <template x-if="!user.roles || user.roles.length === 0">
                                            <span class="text-sm text-gray-500 text-right block">-</span>
                                        </template>
                                    </div>
                                </div>

                                {{-- Status --}}
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500">Status:</span>
                                    <template x-if="user.is_active">
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <div class="w-1.5 h-1.5 bg-green-400 rounded-full mr-1"></div>
                                            Aktif
                                        </span>
                                    </template>
                                    <template x-if="!user.is_active">
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
                                    viewUrl: '/users/' + user.id,
                                    editUrl: '/users/' + user.id + '/edit',
                                    deleteUrl: '/users/' + user.id,
                                    toggleUrl: '/users/' + user.id + '/toggle',
                                    itemName: 'User ' + user.name,
                                    isActive: user.is_active
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
