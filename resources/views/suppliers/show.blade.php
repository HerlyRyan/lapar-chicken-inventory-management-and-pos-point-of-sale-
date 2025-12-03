@extends('layouts.app')

@php
    use Illuminate\Support\Facades\Storage;
@endphp

@section('title', 'Detail Supplier')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-orange-50 via-red-50 to-orange-100">
        {{-- Header --}}
        <div class="bg-gradient-to-r from-orange-900 via-orange-800 to-red-900 shadow-xl">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('suppliers.index') }}"
                            class="inline-flex items-center justify-center w-10 h-10 bg-white/10 hover:bg-white/20 rounded-xl transition-all duration-200 backdrop-blur-sm border border-white/20 group">
                            <svg class="w-5 h-5 text-white group-hover:text-orange-200 transition-colors" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </a>

                        <div>
                            <h1 class="text-2xl lg:text-3xl font-bold text-white">{{ $supplier->name }}</h1>
                            <p class="text-orange-200 mt-1">Detail informasi supplier</p>
                        </div>
                    </div>

                    <div class="flex items-center space-x-3">
                        <a href="{{ route('suppliers.edit', $supplier) }}"
                            class="inline-flex items-center px-4 py-2.5 bg-white/10 hover:bg-white/20 text-white rounded-xl font-medium transition-all duration-200 backdrop-blur-sm border border-white/20 group">
                            <svg class="w-4 h-4 mr-2 group-hover:scale-110 transition-transform" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit
                        </a>

                        <button onclick="confirmDelete({{ $supplier->id }}, '{{ addslashes($supplier->name) }}')"
                            class="inline-flex items-center px-4 py-2.5 bg-red-600/80 hover:bg-red-600 text-white rounded-xl font-medium transition-all duration-200 backdrop-blur-sm border border-red-500/30 group">
                            <svg class="w-4 h-4 mr-2 group-hover:scale-110 transition-transform" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Hapus
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Content --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- Main column --}}
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl shadow-xl border border-gray-200/50 overflow-hidden">
                        {{-- Header --}}
                        <div class="relative bg-gradient-to-r from-orange-500 via-red-500 to-orange-600 px-8 py-12">
                            <div class="absolute inset-0 bg-black/10"></div>
                            <div class="relative text-center">
                                <div class="relative inline-block mb-6">
                                    @if (isset($supplier->avatar) && $supplier->avatar)
                                        <img src="{{ Storage::url($supplier->avatar) }}" alt="{{ $supplier->name }}"
                                            class="w-32 h-32 rounded-full border-4 border-white shadow-2xl object-cover">
                                    @else
                                        <div
                                            class="w-32 h-32 rounded-full border-4 border-white shadow-2xl bg-gradient-to-br from-orange-400 to-red-500 flex items-center justify-center">
                                            <span
                                                class="text-4xl font-bold text-white">{{ strtoupper(substr($supplier->name, 0, 1)) }}</span>
                                        </div>
                                    @endif

                                    <div
                                        class="absolute -bottom-2 -right-2 w-8 h-8 bg-green-500 rounded-full border-4 border-white flex items-center justify-center">
                                        @if ($supplier->is_active ?? true)
                                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                                                <path
                                                    d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z" />
                                            </svg>
                                        @endif
                                    </div>
                                </div>

                                <h2 class="text-3xl font-bold text-white mb-2">{{ $supplier->name }}</h2>
                                @if ($supplier->email)
                                    <p class="text-orange-100 text-lg">{{ $supplier->email }}</p>
                                @else
                                    <p class="text-orange-100 text-lg fst-italic">Email belum diisi</p>
                                @endif

                                <div class="mt-4">
                                    @if ($supplier->is_active ?? true)
                                        <span
                                            class="inline-flex items-center px-4 py-2 bg-green-100 text-green-800 rounded-full text-sm font-semibold">
                                            <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                                            Aktif
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-800 rounded-full text-sm font-semibold">
                                            <div class="w-2 h-2 bg-gray-500 rounded-full mr-2"></div>
                                            Nonaktif
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Details --}}
                        <div class="p-8">
                            <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                                <svg class="w-5 h-5 mr-3 text-orange-500" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                                </svg>
                                Informasi Supplier
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div
                                    class="p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border border-blue-100">
                                    <h4 class="font-semibold text-gray-800 mb-2">Nama Supplier</h4>
                                    <p class="text-gray-700 font-medium">{{ $supplier->name }}</p>
                                </div>

                                <div
                                    class="p-4 bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl border border-purple-100">
                                    <h4 class="font-semibold text-gray-800 mb-2">Nomor Telepon</h4>
                                    @if ($supplier->phone)
                                        <p class="text-gray-700">{{ $supplier->phone }}</p>
                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $supplier->phone) }}"
                                            target="_blank"
                                            class="inline-flex items-center mt-2 text-sm text-green-600 hover:text-green-700 font-medium">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 24 24">
                                                <path
                                                    d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347" />
                                            </svg>
                                            WhatsApp
                                        </a>
                                    @else
                                        <p class="text-gray-400">Tidak tersedia</p>
                                    @endif
                                </div>

                                <div
                                    class="p-4 bg-gradient-to-r from-amber-50 to-orange-50 rounded-xl border border-amber-100">
                                    <h4 class="font-semibold text-gray-800 mb-2">Alamat</h4>
                                    @if ($supplier->address)
                                        <p class="text-gray-700">{{ $supplier->address }}</p>
                                    @else
                                        <p class="text-gray-400 fst-italic">Alamat belum diisi</p>
                                    @endif
                                </div>

                                <div
                                    class="p-4 bg-gradient-to-r from-gray-50 to-slate-50 rounded-xl border border-gray-100">
                                    <h4 class="font-semibold text-gray-800 mb-2">Email</h4>
                                    @if ($supplier->email)
                                        <p class="text-gray-700">{{ $supplier->email }}</p>
                                        <a href="mailto:{{ $supplier->email }}?subject=Perihal {{ urlencode($supplier->name) }}"
                                            class="inline-flex items-center mt-2 text-sm text-blue-600 hover:text-blue-700 font-medium">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 24 24">
                                                <path
                                                    d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z" />
                                            </svg>
                                            Kirim Email
                                        </a>
                                    @else
                                        <p class="text-gray-400 fst-italic">Email belum diisi</p>
                                    @endif
                                </div>
                            </div>

                            {{-- Materials --}}
                            <div class="mt-8">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-3 text-purple-500" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M3 3h18v2H3V3zm2 4h14v12H5V7zm2 2v8h10V9H7z" />
                                    </svg>
                                    Bahan Mentah yang Dipasok ({{ $supplier->materials->count() }})
                                </h3>

                                @if ($supplier->materials->count())
                                    <div class="overflow-x-auto rounded-xl border border-gray-100">
                                        <table class="min-w-full divide-y divide-gray-200 bg-white">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Kode
                                                    </th>
                                                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Nama
                                                    </th>
                                                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Harga
                                                        Satuan</th>
                                                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">
                                                        Status</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-100">
                                                @foreach ($supplier->materials as $material)
                                                    <tr>
                                                        <td class="px-4 py-3 text-sm text-gray-700">{{ $material->code }}
                                                        </td>
                                                        <td class="px-4 py-3 text-sm text-gray-700">{{ $material->name }}
                                                        </td>
                                                        <td class="px-4 py-3 text-sm text-gray-700">Rp
                                                            {{ number_format($material->unit_price, 0, ',', '.') }}</td>
                                                        <td class="px-4 py-3 text-sm">
                                                            @if ($material->is_active)
                                                                <span
                                                                    class="inline-flex items-center px-3 py-1 rounded-full bg-green-100 text-green-700 text-sm">Aktif</span>
                                                            @else
                                                                <span
                                                                    class="inline-flex items-center px-3 py-1 rounded-full bg-gray-100 text-gray-700 text-sm">Nonaktif</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <p class="text-gray-500">Tidak ada bahan mentah yang dipasok oleh supplier ini.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Sidebar --}}
                <div class="space-y-6">
                    {{-- Contact Actions --}}
                    <div class="bg-white rounded-2xl shadow-xl border border-gray-200/50 overflow-hidden">
                        <div class="bg-gradient-to-r from-green-500 to-teal-600 px-6 py-4">
                            <h3 class="text-lg font-bold text-white flex items-center">
                                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z" />
                                </svg>
                                Kontak
                            </h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <a href="mailto:{{ $supplier->email ?? '' }}"
                                class="flex items-center justify-between p-4 bg-blue-50 hover:bg-blue-100 rounded-xl transition-colors duration-200 group">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="font-medium text-gray-800">Kirim Email</p>
                                        <p class="text-sm text-gray-500">{{ $supplier->email ?? 'Tidak tersedia' }}</p>
                                    </div>
                                </div>
                                <svg class="w-4 h-4 text-gray-400 group-hover:text-blue-600 transition-colors"
                                    fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 4l-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8z" />
                                </svg>
                            </a>

                            @if ($supplier->phone)
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $supplier->phone) }}"
                                    target="_blank"
                                    class="flex items-center justify-between p-4 bg-green-50 hover:bg-green-100 rounded-xl transition-colors duration-200 group">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center">
                                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                                <path
                                                    d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347" />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <p class="font-medium text-gray-800">WhatsApp</p>
                                            <p class="text-sm text-gray-500">{{ $supplier->phone }}</p>
                                        </div>
                                    </div>
                                    <svg class="w-4 h-4 text-gray-400 group-hover:text-green-600 transition-colors"
                                        fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 4l-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8z" />
                                    </svg>
                                </a>
                            @endif
                        </div>
                    </div>

                    {{-- System Info --}}
                    <div class="bg-white rounded-2xl shadow-xl border border-gray-200/50 overflow-hidden">
                        <div class="bg-gradient-to-r from-gray-600 to-gray-700 px-6 py-4">
                            <h3 class="text-lg font-bold text-white flex items-center">
                                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                                </svg>
                                Info Sistem
                            </h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-500">Supplier ID</span>
                                <span class="font-mono text-gray-700">{{ $supplier->id }}</span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-500">Dibuat</span>
                                <span class="text-gray-700">{{ $supplier->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-500">Diperbarui</span>
                                <span class="text-gray-700">{{ $supplier->updated_at->format('d/m/Y H:i') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Hidden delete form --}}
        <form id="delete-form-{{ $supplier->id }}" action="{{ route('suppliers.destroy', $supplier) }}" method="POST"
            style="display:none;">
            @csrf
            @method('DELETE')
            <input type="hidden" name="redirect_to" value="{{ route('suppliers.index') }}">
        </form>
    </div>
@endsection
