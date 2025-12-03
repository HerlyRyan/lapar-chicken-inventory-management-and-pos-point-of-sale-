@extends('layouts.app')

@php
use Illuminate\Support\Facades\Storage;
@endphp

@section('title', 'Detail Permintaan Penggunaan Bahan')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-orange-50 via-red-50 to-orange-100">
    {{-- Header --}}
    <div class="bg-gradient-to-r from-orange-900 via-orange-800 to-red-900 shadow-xl">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('semi-finished-usage-requests.index') }}"
                       class="inline-flex items-center justify-center w-10 h-10 bg-white/10 hover:bg-white/20 rounded-xl transition-all duration-200 border border-white/20">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-2xl lg:text-3xl font-bold text-white">Detail Permintaan Penggunaan Bahan</h1>
                        <p class="text-orange-200 mt-1">#{{ $materialUsageRequest->request_number }} — {{ $materialUsageRequest->requested_date->format('d/m/Y') }}</p>
                    </div>
                </div>

                <div class="flex items-center space-x-3">
                    @if($materialUsageRequest->status === \App\Models\SemiFinishedUsageRequest::STATUS_PENDING)
                        @if(auth()->user()->hasRole(['kepala-gudang', 'admin', 'super-admin']))
                            <button onclick="openModal('approveModal')" class="inline-flex items-center px-4 py-2.5 bg-green-600 hover:bg-green-500 text-white rounded-xl font-medium transition-all duration-200 border border-green-700/30">
                                <svg class="w-4 h-4 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Setujui
                            </button>
                            <button onclick="openModal('rejectModal')" class="inline-flex items-center px-4 py-2.5 bg-red-600 hover:bg-red-500 text-white rounded-xl font-medium transition-all duration-200 border border-red-700/30">
                                <svg class="w-4 h-4 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                Tolak
                            </button>
                        @endif

                        @if(auth()->user()->id === $materialUsageRequest->user_id)
                            <a href="{{ route('semi-finished-usage-requests.edit', $materialUsageRequest) }}" class="inline-flex items-center px-4 py-2.5 bg-white/10 hover:bg-white/20 text-white rounded-xl font-medium transition-all duration-200 border border-white/20">
                                <svg class="w-4 h-4 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5"/></svg>
                                Edit
                            </a>
                            <button onclick="openModal('cancelModal')" class="inline-flex items-center px-4 py-2.5 bg-gray-700 hover:bg-gray-600 text-white rounded-xl font-medium transition-all duration-200 border border-gray-700/30">
                                <svg class="w-4 h-4 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                Batalkan
                            </button>
                        @endif
                    @endif

                    @if($materialUsageRequest->status === \App\Models\SemiFinishedUsageRequest::STATUS_APPROVED)
                        @if(auth()->user()->hasRole(['kepala-gudang', 'admin', 'super-admin']))
                            <button onclick="openModal('processModal')" class="inline-flex items-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl font-medium transition-all duration-200 border border-indigo-700/30">
                                <svg class="w-4 h-4 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v4a1 1 0 001 1h3m10 0h3a1 1 0 001-1V7M7 21h10"/></svg>
                                Proses
                            </button>
                        @endif
                    @endif

                    @if($materialUsageRequest->status === \App\Models\SemiFinishedUsageRequest::STATUS_PROCESSING)
                        @if(auth()->user()->hasRole(['kepala-gudang', 'admin', 'super-admin']))
                            <button onclick="openModal('completeModal')" class="inline-flex items-center px-4 py-2.5 bg-amber-600 hover:bg-amber-500 text-white rounded-xl font-medium transition-all duration-200 border border-amber-700/30">
                                <svg class="w-4 h-4 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/></svg>
                                Selesaikan
                            </button>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Left / Main Column --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-xl border border-gray-200/50 overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-start justify-between">
                            <div>
                                <h2 class="text-xl font-bold text-gray-800">Detail Permintaan</h2>
                                <p class="text-sm text-gray-500 mt-1">Informasi lengkap tentang permintaan penggunaan bahan</p>
                            </div>
                            <div class="text-right">
                                {!! $materialUsageRequest->status_badge !!}
                            </div>
                        </div>

                        {{-- Request Info --}}
                        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-3">
                                <div class="flex items-start">
                                    <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-red-500 rounded-lg flex items-center justify-center mr-4 text-white">
                                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M5 3h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2z"/></svg>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="text-sm text-gray-500">Nomor Permintaan</div>
                                        <div class="font-medium text-gray-800">{{ $materialUsageRequest->request_number }}</div>
                                    </div>
                                </div>

                                <div class="flex items-start">
                                    <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-500 rounded-lg flex items-center justify-center mr-4 text-white">
                                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M3 3h18v2H3zM3 7h18v14H3z"/></svg>
                                    </div>
                                    <div>
                                        <div class="text-sm text-gray-500">Cabang Peminta</div>
                                        <div class="font-medium text-gray-800">{{ $materialUsageRequest->requestingBranch->name }}</div>
                                    </div>
                                </div>

                                <div class="flex items-start">
                                    <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-500 rounded-lg flex items-center justify-center mr-4 text-white">
                                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 12a5 5 0 100-10 5 5 0 000 10zM21 21a9 9 0 10-18 0"/></svg>
                                    </div>
                                    <div>
                                        <div class="text-sm text-gray-500">Pengaju</div>
                                        <div class="font-medium text-gray-800">{{ $materialUsageRequest->user->name }}</div>
                                    </div>
                                </div>

                                <div class="flex items-start">
                                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-500 rounded-lg flex items-center justify-center mr-4 text-white">
                                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M7 10l5 5 5-5"/></svg>
                                    </div>
                                    <div>
                                        <div class="text-sm text-gray-500">Tanggal Permintaan</div>
                                        <div class="font-medium text-gray-800">{{ $materialUsageRequest->requested_date->format('d/m/Y') }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-3">
                                <div class="flex items-start">
                                    <div class="w-10 h-10 bg-gradient-to-br from-yellow-500 to-amber-500 rounded-lg flex items-center justify-center mr-4 text-white">
                                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l4 7H8l4-7zM5 22h14v-2H5v2z"/></svg>
                                    </div>
                                    <div>
                                        <div class="text-sm text-gray-500">Tanggal Dibutuhkan</div>
                                        <div class="font-medium text-gray-800">{{ $materialUsageRequest->required_date ? $materialUsageRequest->required_date->format('d/m/Y') : '-' }}</div>
                                    </div>
                                </div>

                                <div class="flex items-start">
                                    <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-lg flex items-center justify-center mr-4 text-white">
                                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 7a2 2 0 100-4 2 2 0 000 4zM6 14v2h12v-2H6z"/></svg>
                                    </div>
                                    <div>
                                        <div class="text-sm text-gray-500">Tujuan</div>
                                        <div class="font-medium text-gray-800">{{ $materialUsageRequest->purpose }}</div>
                                    </div>
                                </div>

                                <div class="flex items-start">
                                    <div class="w-10 h-10 bg-gradient-to-br from-gray-500 to-gray-600 rounded-lg flex items-center justify-center mr-4 text-white">
                                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 100 20 10 10 0 000-20z"/></svg>
                                    </div>
                                    <div>
                                        <div class="text-sm text-gray-500">Status</div>
                                        <div class="font-medium text-gray-800">{!! $materialUsageRequest->status_badge !!}</div>
                                    </div>
                                </div>

                                <div class="flex items-start">
                                    <div class="w-10 h-10 bg-gradient-to-br from-gray-200 to-gray-300 rounded-lg flex items-center justify-center mr-4 text-gray-700">
                                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M4 6h16v2H4zM4 12h10v2H4zM4 18h16v2H4z"/></svg>
                                    </div>
                                    <div>
                                        <div class="text-sm text-gray-500">Catatan</div>
                                        <div class="font-medium text-gray-800">{{ $materialUsageRequest->notes ?: '-' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Approval Info --}}
                        @if($materialUsageRequest->status !== \App\Models\SemiFinishedUsageRequest::STATUS_PENDING)
                            <div class="mt-6 bg-gray-50 p-4 rounded-lg border border-gray-100">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-sm font-semibold text-gray-800">
                                        @if($materialUsageRequest->status === \App\Models\SemiFinishedUsageRequest::STATUS_REJECTED)
                                            <span class="text-red-600 inline-flex items-center"><svg class="w-4 h-4 mr-1" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"/></svg>Informasi Penolakan</span>
                                        @else
                                            <span class="text-green-600 inline-flex items-center"><svg class="w-4 h-4 mr-1" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"/></svg>Informasi Persetujuan</span>
                                        @endif
                                    </h3>
                                </div>
                                <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <div class="text-gray-500">Tanggal</div>
                                        <div class="font-medium text-gray-800">{{ $materialUsageRequest->approval_date ? $materialUsageRequest->approval_date->format('d/m/Y H:i') : '-' }}</div>
                                        <div class="text-gray-500 mt-2">Oleh</div>
                                        <div class="font-medium text-gray-800">{{ $materialUsageRequest->approvalUser ? $materialUsageRequest->approvalUser->name : '-' }}</div>
                                    </div>
                                    <div>
                                        <div class="text-gray-500">Catatan</div>
                                        <div class="font-medium text-gray-800">{{ $materialUsageRequest->rejection_reason ?: '-' }}</div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Items --}}
                        <div class="mt-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-3">Daftar Bahan yang Diminta</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 bg-white rounded-lg">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">No</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Nama Bahan</th>
                                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Jumlah</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Satuan</th>
                                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Harga Satuan</th>
                                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-100">
                                        @forelse($materialUsageRequest->items as $index => $item)
                                            <tr>
                                                <td class="px-4 py-3 text-sm text-gray-700">{{ $index + 1 }}</td>
                                                <td class="px-4 py-3 text-sm text-gray-700">
                                                    {{ $item->semiFinishedProduct->name ?? '-' }}
                                                    @if($item->notes)
                                                        <div class="text-xs text-gray-400 mt-1">{{ $item->notes }}</div>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 text-sm text-right text-gray-700">{{ number_format((float) $item->quantity, 0, ',', '.') }}</td>
                                                <td class="px-4 py-3 text-sm text-gray-700">
                                                    @if($item->unit)
                                                        {{ $item->unit->name }}@if(!empty($item->unit->symbol)) ({{ $item->unit->symbol }}) @endif
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 text-sm text-right text-gray-700">{{ $item->formatted_unit_price }}</td>
                                                <td class="px-4 py-3 text-sm text-right font-medium text-gray-800">{{ $item->formatted_subtotal }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="px-4 py-6 text-center text-sm text-gray-400">Tidak ada item</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    <tfoot class="bg-gray-50">
                                        <tr>
                                            <td colspan="5" class="px-4 py-3 text-right font-semibold text-gray-700">Total:</td>
                                            <td class="px-4 py-3 text-right font-semibold text-gray-800">Rp {{ number_format($materialUsageRequest->total_amount, 0, ',', '.') }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <div class="mt-6">
                            <a href="{{ route('semi-finished-usage-requests.index') }}" class="inline-flex items-center px-4 py-2 bg-white/10 hover:bg-white/20 text-gray-800 rounded-xl border border-gray-200">
                                <svg class="w-4 h-4 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right / Sidebar --}}
            <div class="space-y-6">
                <div class="bg-white rounded-2xl shadow-xl border border-gray-200/50 overflow-hidden">
                    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-4">
                        <h3 class="text-lg font-bold text-white flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 24 24"><path d="M12,1L3,5V11C3,16.55 6.84,21.74 12,23C17.16,21.74 21,16.55 21,11V5L12,1"/></svg>
                            Informasi Permintaan
                        </h3>
                    </div>
                    <div class="p-6 space-y-4 text-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Nomor</span>
                            <span class="font-mono text-gray-700">{{ $materialUsageRequest->request_number }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Dibuat</span>
                            <span class="text-gray-700">{{ $materialUsageRequest->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Diperbarui</span>
                            <span class="text-gray-700">{{ $materialUsageRequest->updated_at->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Actions / Contact-like card for requester --}}
                <div class="bg-white rounded-2xl shadow-xl border border-gray-200/50 overflow-hidden">
                    <div class="bg-gradient-to-r from-green-500 to-teal-600 px-6 py-4">
                        <h3 class="text-lg font-bold text-white flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16"/></svg>
                            Peminta
                        </h3>
                    </div>
                    <div class="p-6 space-y-3">
                        <div class="text-sm">
                            <div class="text-gray-500">Nama</div>
                            <div class="font-medium text-gray-800">{{ $materialUsageRequest->user->name }}</div>
                        </div>
                        <div class="text-sm">
                            <div class="text-gray-500">Email</div>
                            <div class="font-medium text-gray-800">{{ $materialUsageRequest->user->email }}</div>
                        </div>
                        @if($materialUsageRequest->user->phone)
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $materialUsageRequest->user->phone) }}" target="_blank"
                               class="flex items-center justify-between p-3 bg-green-50 hover:bg-green-100 rounded-xl transition-colors">
                                <div>
                                    <div class="text-sm font-medium text-gray-800">WhatsApp</div>
                                    <div class="text-xs text-gray-500">{{ $materialUsageRequest->user->phone }}</div>
                                </div>
                                <svg class="w-5 h-5 text-green-600" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967"/></svg>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modals (Tailwind light modals) --}}
@php
    // helper to render modal backdrop and box with given id
@endphp

{{-- Approve Modal --}}
<div id="approveModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40" onclick="closeModal('approveModal')"></div>
    <div class="relative bg-white rounded-xl shadow-xl max-w-lg w-full z-10 overflow-hidden">
        <form action="{{ route('semi-finished-usage-requests.approve', $materialUsageRequest) }}" method="POST">
            @csrf
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-800">Setujui Permintaan</h3>
                <p class="text-sm text-gray-600 mt-2">Anda yakin ingin menyetujui permintaan penggunaan bahan #{{ $materialUsageRequest->request_number }}?</p>
                <div class="mt-4">
                    <label class="block text-sm text-gray-700">Catatan (Opsional)</label>
                    <textarea name="approval_notes" rows="3" class="mt-1 w-full border rounded-md px-3 py-2 text-sm"></textarea>
                </div>
            </div>
            <div class="px-6 py-3 bg-gray-50 flex justify-end gap-3">
                <button type="button" onclick="closeModal('approveModal')" class="px-4 py-2 rounded-md bg-white border">Batal</button>
                <button type="submit" class="px-4 py-2 rounded-md bg-green-600 text-white">Setujui</button>
            </div>
        </form>
    </div>
</div>

{{-- Reject Modal --}}
<div id="rejectModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40" onclick="closeModal('rejectModal')"></div>
    <div class="relative bg-white rounded-xl shadow-xl max-w-lg w-full z-10 overflow-hidden">
        <form action="{{ route('semi-finished-usage-requests.reject', $materialUsageRequest) }}" method="POST">
            @csrf
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-800">Tolak Permintaan</h3>
                <p class="text-sm text-gray-600 mt-2">Anda yakin ingin menolak permintaan penggunaan bahan #{{ $materialUsageRequest->request_number }}?</p>
                <div class="mt-4">
                    <label class="block text-sm text-gray-700">Alasan Penolakan</label>
                    <textarea name="rejection_reason" rows="3" required class="mt-1 w-full border rounded-md px-3 py-2 text-sm"></textarea>
                </div>
            </div>
            <div class="px-6 py-3 bg-gray-50 flex justify-end gap-3">
                <button type="button" onclick="closeModal('rejectModal')" class="px-4 py-2 rounded-md bg-white border">Batal</button>
                <button type="submit" class="px-4 py-2 rounded-md bg-red-600 text-white">Tolak</button>
            </div>
        </form>
    </div>
</div>

{{-- Process Modal --}}
<div id="processModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40" onclick="closeModal('processModal')"></div>
    <div class="relative bg-white rounded-xl shadow-xl max-w-lg w-full z-10 overflow-hidden">
        <form action="{{ route('semi-finished-usage-requests.process', $materialUsageRequest) }}" method="POST">
            @csrf
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-800">Proses Permintaan</h3>
                <p class="text-sm text-gray-600 mt-2">Anda yakin ingin memproses permintaan ini? Bahan-bahan akan disiapkan.</p>
                <div class="mt-4">
                    <label class="block text-sm text-gray-700">Catatan (Opsional)</label>
                    <textarea name="process_notes" rows="3" class="mt-1 w-full border rounded-md px-3 py-2 text-sm"></textarea>
                </div>
            </div>
            <div class="px-6 py-3 bg-gray-50 flex justify-end gap-3">
                <button type="button" onclick="closeModal('processModal')" class="px-4 py-2 rounded-md bg-white border">Batal</button>
                <button type="submit" class="px-4 py-2 rounded-md bg-indigo-600 text-white">Proses</button>
            </div>
        </form>
    </div>
</div>

{{-- Complete Modal --}}
<div id="completeModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40" onclick="closeModal('completeModal')"></div>
    <div class="relative bg-white rounded-xl shadow-xl max-w-lg w-full z-10 overflow-hidden">
        <form action="{{ route('semi-finished-usage-requests.complete', $materialUsageRequest) }}" method="POST">
            @csrf
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-800">Selesaikan Permintaan</h3>
                <p class="text-sm text-gray-600 mt-2">Tindakan ini akan mengurangi stok bahan dan tidak dapat dibatalkan.</p>
                <div class="mt-4">
                    <label class="block text-sm text-gray-700">Catatan (Opsional)</label>
                    <textarea name="completion_notes" rows="3" class="mt-1 w-full border rounded-md px-3 py-2 text-sm"></textarea>
                </div>
            </div>
            <div class="px-6 py-3 bg-gray-50 flex justify-end gap-3">
                <button type="button" onclick="closeModal('completeModal')" class="px-4 py-2 rounded-md bg-white border">Batal</button>
                <button type="submit" class="px-4 py-2 rounded-md bg-amber-600 text-white">Selesaikan</button>
            </div>
        </form>
    </div>
</div>

{{-- Cancel Modal --}}
<div id="cancelModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40" onclick="closeModal('cancelModal')"></div>
    <div class="relative bg-white rounded-xl shadow-xl max-w-lg w-full z-10 overflow-hidden">
        <form action="{{ route('semi-finished-usage-requests.cancel', $materialUsageRequest) }}" method="POST">
            @csrf
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-800">Batalkan Permintaan</h3>
                <p class="text-sm text-gray-600 mt-2">Anda yakin ingin membatalkan permintaan penggunaan bahan #{{ $materialUsageRequest->request_number }}?</p>
                <div class="mt-4">
                    <label class="block text-sm text-gray-700">Alasan Pembatalan</label>
                    <textarea name="cancellation_reason" rows="3" required class="mt-1 w-full border rounded-md px-3 py-2 text-sm"></textarea>
                </div>
            </div>
            <div class="px-6 py-3 bg-gray-50 flex justify-end gap-3">
                <button type="button" onclick="closeModal('cancelModal')" class="px-4 py-2 rounded-md bg-white border">Tidak</button>
                <button type="submit" class="px-4 py-2 rounded-md bg-red-600 text-white">Ya, Batalkan</button>
            </div>
        </form>
    </div>
</div>

{{-- Simple modal toggle script --}}
<script>
    function openModal(id){
        const el = document.getElementById(id);
        if(!el) return;
        el.classList.remove('hidden');
        el.classList.add('flex');
    }
    function closeModal(id){
        const el = document.getElementById(id);
        if(!el) return;
        el.classList.add('hidden');
        el.classList.remove('flex');
    }
</script>
@endsection
