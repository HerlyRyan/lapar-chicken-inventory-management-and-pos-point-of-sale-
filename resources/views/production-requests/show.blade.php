@extends('layouts.app')

@php
    use Illuminate\Support\Facades\Storage;
@endphp

@section('title', 'Detail Pengajuan Produksi')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-orange-50 via-red-50 to-orange-100">
        {{-- Header --}}
        <div class="bg-gradient-to-r from-orange-900 via-orange-800 to-red-900 shadow-xl">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('production-requests.index') }}"
                            class="inline-flex items-center justify-center w-10 h-10 bg-white/10 hover:bg-white/20 rounded-xl transition-all duration-200 backdrop-blur-sm border border-white/20 group">
                            <svg class="w-5 h-5 text-white group-hover:text-orange-200 transition-colors" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </a>

                        <div>
                            <h1 class="text-2xl lg:text-3xl font-bold text-white">
                                <i class="bi bi-eye text-info me-2 hidden"></i>
                                Detail Pengajuan Produksi
                            </h1>
                            <p class="text-orange-200 mt-1">Kode: <strong
                                    class="text-white">{{ $productionRequest->request_code }}</strong></p>
                        </div>
                    </div>

                    <div class="flex items-center space-x-3">
                        @if ($productionRequest->isPending())
                            <a href="{{ route('production-requests.edit', $productionRequest) }}"
                                class="inline-flex items-center px-4 py-2.5 bg-white/10 hover:bg-white/20 text-white rounded-xl font-medium transition-all duration-200 backdrop-blur-sm border border-white/20 group">
                                <svg class="w-4 h-4 mr-2 group-hover:scale-110 transition-transform" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Edit
                            </a>
                        @endif
                        <a href="{{ route('production-requests.index') }}"
                            class="inline-flex items-center px-4 py-2.5 bg-white/10 hover:bg-white/20 text-white rounded-xl font-medium transition-all duration-200 backdrop-blur-sm border border-white/20 group">
                            <svg class="w-4 h-4 mr-2 group-hover:scale-110 transition-transform" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Content --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- Left / Main --}}
                <div class="lg:col-span-2 space-y-6">
                    {{-- Meta Card --}}
                    <div class="bg-white rounded-2xl shadow-xl border border-gray-200/50 overflow-hidden">
                        <div class="bg-gradient-to-r from-orange-500 via-red-500 to-orange-600 px-6 py-4">
                            <h3 class="text-lg font-bold text-white flex items-center">
                                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M3 3h18v2H3zM3 7h18v2H3zM3 11h18v2H3z" />
                                </svg>
                                Pengajuan #{{ $productionRequest->request_code }}
                            </h3>
                        </div>

                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-start">
                                {{-- Left: requester + approvals --}}
                                <div class="md:col-span-2 space-y-4 text-sm text-gray-700">
                                    <div class="flex items-start gap-4">
                                        <div class="flex-shrink-0">
                                            <div
                                                class="w-12 h-12 rounded-full bg-orange-100 text-orange-700 flex items-center justify-center font-semibold">
                                                {{ optional($productionRequest->requestedBy)->initials ?? '-' }}
                                            </div>
                                        </div>
                                        <div class="flex-1">
                                            <div class="text-sm text-gray-500">Pemohon</div>
                                            <div class="font-semibold text-gray-800">
                                                {{ $productionRequest->requestedBy->name ?? '-' }}
                                            </div>

                                            <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm">
                                                @if ($productionRequest->approvedBy)
                                                    <div>
                                                        <div class="text-gray-500">Disetujui oleh</div>
                                                        <div class="text-gray-700 font-medium">
                                                            {{ $productionRequest->approvedBy->name }}
                                                        </div>
                                                        <div class="text-xs text-gray-400">
                                                            {{ optional($productionRequest->approved_at)->format('d/m/Y H:i') }}
                                                        </div>
                                                    </div>
                                                @endif

                                                @if ($productionRequest->productionStartedBy)
                                                    <div>
                                                        <div class="text-gray-500">Mulai Produksi</div>
                                                        <div class="text-gray-700 font-medium">
                                                            {{ $productionRequest->productionStartedBy->name }}
                                                        </div>
                                                        <div class="text-xs text-gray-400">
                                                            {{ optional($productionRequest->production_started_at)->format('d/m/Y H:i') }}
                                                        </div>
                                                    </div>
                                                @endif

                                                @if ($productionRequest->productionCompletedBy)
                                                    <div>
                                                        <div class="text-gray-500">Produksi Selesai</div>
                                                        <div class="text-gray-700 font-medium">
                                                            {{ $productionRequest->productionCompletedBy->name }}
                                                        </div>
                                                        <div class="text-xs text-gray-400">
                                                            {{ optional($productionRequest->completed_at)->format('d/m/Y H:i') }}
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    @if ($productionRequest->purpose || $productionRequest->notes)
                                        <div
                                            class="mt-2 bg-gray-50 border border-gray-100 rounded-lg p-3 text-sm text-gray-700">
                                            @if ($productionRequest->purpose)
                                                <div class="mb-2">
                                                    <div class="text-gray-500 text-xs">Peruntukan</div>
                                                    <div class="font-medium text-gray-800">
                                                        {{ $productionRequest->purpose }}</div>
                                                </div>
                                            @endif
                                            @if ($productionRequest->notes)
                                                <div>
                                                    <div class="text-gray-500 text-xs">Catatan</div>
                                                    <div class="text-gray-700">{{ $productionRequest->notes }}</div>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>

                                {{-- Right: status & timestamps --}}
                                <div class="text-right">
                                    @php
                                        $statusColor = $productionRequest->status_color ?? 'gray';
                                    @endphp
                                    <span class="inline-flex items-center px-3 py-2 rounded-full text-sm font-semibold"
                                        style="background-color: var(--tw-bg-opacity,1);">
                                        <span class="inline-block mr-2">
                                            <span class="sr-only">Status</span>
                                            <svg class="w-4 h-4 text-{{ $statusColor }}-600" fill="currentColor"
                                                viewBox="0 0 20 20">
                                                <circle cx="10" cy="10" r="10" />
                                            </svg>
                                        </span>
                                        <span
                                            class="text-{{ $statusColor }}-800">{{ $productionRequest->status_label }}</span>
                                    </span>

                                    <div class="mt-3 text-xs text-gray-400">
                                        <div>Dibuat: <span
                                                class="text-gray-700 font-mono">{{ $productionRequest->created_at->format('d/m/Y H:i') }}</span>
                                        </div>
                                        @if ($productionRequest->updated_at)
                                            <div class="mt-1">Diperbarui: <span
                                                    class="text-gray-700 font-mono">{{ $productionRequest->updated_at->format('d/m/Y H:i') }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Raw Materials --}}
                    <div class="bg-white rounded-2xl shadow-xl border border-gray-200/50 overflow-hidden">
                        <div class="bg-gradient-to-r from-orange-500 via-red-500 to-orange-600 px-6 py-4">
                            <h3 class="text-lg font-bold text-white flex items-center">
                                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M3 3h18v2H3zM3 7h18v2H3zM3 11h18v2H3z" />
                                </svg>
                                Bahan Mentah yang Diminta
                            </h3>
                        </div>

                        <div class="p-6">
                            @if ($productionRequest->items->count())
                                <div class="overflow-x-auto">
                                    <table class="min-w-full text-sm text-left">
                                        <thead class="text-gray-600">
                                            <tr>
                                                <th class="py-2">Bahan Mentah</th>
                                                <th class="py-2 text-right">Jumlah</th>
                                                <th class="py-2">Satuan</th>
                                                <th class="py-2 text-right">Harga/Unit</th>
                                                <th class="py-2 text-right">Total</th>
                                                <th class="py-2">Catatan</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y">
                                            @foreach ($productionRequest->items as $item)
                                                <tr class="bg-white">
                                                    <td class="py-3">{{ $item->rawMaterial->name ?? '-' }}</td>
                                                    <td class="py-3 text-right">
                                                        {{ number_format($item->requested_quantity, 0, ',', '.') }}</td>
                                                    <td class="py-3">{{ $item->rawMaterial->unit->name ?? '-' }}</td>
                                                    <td class="py-3 text-right">Rp
                                                        {{ number_format($item->unit_cost, 0, ',', '.') }}</td>
                                                    <td class="py-3 text-right">Rp
                                                        {{ number_format($item->total_cost ?? $item->requested_quantity * $item->unit_cost, 0, ',', '.') }}
                                                    </td>
                                                    <td class="py-3">{{ $item->notes }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="4" class="py-3 text-right font-semibold text-gray-700">
                                                    Total
                                                    Biaya Bahan</td>
                                                <td class="py-3 text-right font-semibold text-gray-700">Rp
                                                    {{ number_format($productionRequest->total_raw_material_cost, 0, ',', '.') }}
                                                </td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            @else
                                <div class="text-gray-500">Tidak ada item bahan mentah.</div>
                            @endif
                        </div>
                    </div>

                    {{-- Planned Outputs --}}
                    <div class="bg-white rounded-2xl shadow-xl border border-gray-200/50 overflow-hidden">
                        <div class="bg-gradient-to-r from-green-500 to-emerald-600 px-6 py-4">
                            <h3 class="text-lg font-bold text-white flex items-center">
                                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2L2 7l10 5 10-5-10-5zm0 11l-10-5v6l10 5 10-5v-6l-10 5z" />
                                </svg>
                                Target Output Bahan Setengah Jadi
                            </h3>
                        </div>

                        <div class="p-6">
                            @if ($productionRequest->outputs->count())
                                <div class="overflow-x-auto">
                                    <table class="min-w-full text-sm text-left">
                                        <thead class="text-gray-600">
                                            <tr>
                                                <th class="py-2">Produk</th>
                                                <th class="py-2 text-right">Jumlah Rencana</th>
                                                <th class="py-2">Satuan</th>
                                                <th class="py-2">Catatan</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y">
                                            @foreach ($productionRequest->outputs as $out)
                                                <tr class="bg-white">
                                                    <td class="py-3">{{ $out->semiFinishedProduct->name ?? '-' }}</td>
                                                    <td class="py-3 text-right">
                                                        {{ number_format($out->planned_quantity, 0, ',', '.') }}</td>
                                                    <td class="py-3">{{ $out->semiFinishedProduct->unit ?? '-' }}</td>
                                                    <td class="py-3">{{ $out->notes }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-gray-500">Belum ada target output yang direncanakan.</div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Right / Sidebar --}}
                <div class="space-y-6">
                    {{-- Summary --}}
                    <div class="bg-white rounded-2xl shadow-xl border border-gray-200/50 overflow-hidden">
                        <div class="bg-gradient-to-r from-orange-500 via-red-500 to-orange-600 px-6 py-4">
                            <h3 class="text-lg font-bold text-white flex items-center">
                                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2a10 10 0 100 20 10 10 0 000-20z" />
                                </svg>
                                Ringkasan
                            </h3>
                        </div>
                        <div class="p-6 space-y-4 text-sm text-gray-700">
                            <div>
                                <div class="text-gray-500">Total Biaya Bahan</div>
                                <div class="font-semibold">Rp
                                    {{ number_format($productionRequest->total_raw_material_cost, 0, ',', '.') }}</div>
                            </div>

                            @if ($productionRequest->estimated_output_quantity)
                                <div>
                                    <div class="text-gray-500">Estimasi Output</div>
                                    <div class="font-semibold">
                                        {{ number_format($productionRequest->estimated_output_quantity, 0, ',', '.') }}
                                        unit</div>
                                </div>
                            @endif

                            @if ($productionRequest->purpose)
                                <div>
                                    <div class="text-gray-500">Peruntukan</div>
                                    <div class="text-gray-600">{{ $productionRequest->purpose }}</div>
                                </div>
                            @endif

                            @if ($productionRequest->notes)
                                <div>
                                    <div class="text-gray-500">Catatan</div>
                                    <div class="text-gray-600">{{ $productionRequest->notes }}</div>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Activity / Timestamps --}}
                    <div class="bg-white rounded-2xl shadow-xl border border-gray-200/50 overflow-hidden">
                        <div class="bg-gradient-to-r from-gray-600 to-gray-700 px-6 py-4">
                            <h3 class="text-lg font-bold text-white flex items-center">
                                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2a10 10 0 100 20 10 10 0 000-20z" />
                                </svg>
                                Info Sistem
                            </h3>
                        </div>
                        <div class="p-6 space-y-3 text-sm text-gray-700">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-500">Request ID</span>
                                <span class="font-mono text-gray-700">{{ $productionRequest->id }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-500">Dibuat</span>
                                <span
                                    class="text-gray-700">{{ $productionRequest->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-500">Diperbarui</span>
                                <span
                                    class="text-gray-700">{{ $productionRequest->updated_at->format('d/m/Y H:i') }}</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
