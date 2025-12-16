@extends('layouts.app')

@section('title', 'Proses Penggunaan Bahan - Daftar')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-orange-50/30 to-red-50/30">
        {{-- Page Header --}}
        <x-index.header title="Proses Penggunaan Bahan" subtitle="Kelola proses penggunaan bahan semi-jadi" />

        {{-- Main Content --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-8">
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white rounded-lg sm:rounded-2xl shadow-lg sm:shadow-xl border border-gray-200 overflow-hidden">
                {{-- Card Header --}}
                <x-index.card-header title="Daftar Permintaan Siap Diproses" />

                {{-- Filter Section --}}
                <div class="px-4 sm:px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <form method="GET" action="{{ route('semi-finished-usage-processes.index') }}"
                        class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select name="status"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                                <option value="all"
                                    {{ ($statusFilter ?? request('status')) == 'all' ? 'selected' : '' }}>
                                    Disetujui & Sedang Diproses
                                </option>
                                <option value="approved"
                                    {{ ($statusFilter ?? request('status')) == 'approved' ? 'selected' : '' }}>
                                    Disetujui
                                </option>
                                <option value="processing"
                                    {{ ($statusFilter ?? request('status')) == 'processing' ? 'selected' : '' }}>
                                    Sedang Diproses
                                </option>
                                <option value="completed"
                                    {{ ($statusFilter ?? request('status')) == 'completed' ? 'selected' : '' }}>
                                    Selesai
                                </option>
                            </select>
                        </div>
                        <div class="flex items-end gap-2 sm:col-span-2">
                            <button type="submit"
                                class="flex-1 px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-lg text-sm font-medium transition">
                                <i class="fas fa-filter mr-2"></i> Terapkan
                            </button>
                            <a href="{{ route('semi-finished-usage-processes.index') }}"
                                class="flex-1 px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-lg text-sm font-medium transition text-center">
                                Reset
                            </a>
                        </div>
                    </form>
                </div>

                {{-- Desktop Table --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                    Nomor</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                    Cabang</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                    Tujuan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                    Tgl Permintaan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                    Tgl Dibutuhkan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                    Status</th>
                                <th
                                    class="px-6 py-3 text-center text-xs font-medium text-gray-700 uppercase tracking-wider">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($requests as $req)
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $req->request_number }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                        {{ $req->requestingBranch->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                        {{ Str::limit($req->purpose, 30) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                        {{ optional($req->requested_date)->format('d/m/Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                        {{ optional($req->required_date)->format('d/m/Y') ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{!! $req->status_badge !!}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                        <a href="{{ route('semi-finished-usage-processes.show', $req) }}"
                                            class="inline-flex items-center justify-center px-3 py-2 bg-blue-100 hover:bg-blue-200 text-blue-600 rounded-lg text-xs font-medium transition">
                                            <i class="fas fa-tasks mr-1"></i> Proses
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-12 h-12 text-gray-400 mb-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                            </svg>
                                            Tidak ada data untuk ditampilkan
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Mobile Cards --}}
                <div class="md:hidden divide-y divide-gray-200">
                    @forelse($requests as $req)
                        <div class="p-4 hover:bg-gray-50 transition-colors duration-150">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1">
                                    <h3 class="text-sm font-medium text-gray-900">{{ $req->request_number }}</h3>
                                    <p class="text-xs text-gray-500 mt-1">{{ $req->requestingBranch->name }}</p>
                                </div>
                            </div>

                            <div class="space-y-2 text-sm text-gray-700 mb-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-500">Tujuan:</span>
                                    <span class="text-right">{{ Str::limit($req->purpose, 20) }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-500">Tgl Permintaan:</span>
                                    <span>{{ optional($req->requested_date)->format('d/m/Y') }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-500">Status:</span>
                                    <span>{!! $req->status_badge !!}</span>
                                </div>
                            </div>

                            <div class="pt-3 border-t border-gray-200">
                                <a href="{{ route('semi-finished-usage-processes.show', $req) }}"
                                    class="w-full inline-flex items-center justify-center py-2 bg-blue-100 hover:bg-blue-200 text-blue-600 rounded-lg text-xs font-medium transition">
                                    <i class="fas fa-tasks mr-1"></i> Proses
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-gray-500">
                            Tidak ada data untuk ditampilkan.
                        </div>
                    @endforelse
                </div>

                {{-- Pagination --}}
                <div class="px-4 py-3 sm:px-6 border-t border-gray-200 bg-gray-50">
                    {{ $requests->withQueryString()->links('vendor.pagination.tailwind') }}
                </div>
            </div>
        </div>
    </div>
@endsection
