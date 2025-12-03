@extends('layouts.app')

@php
use Illuminate\Support\Facades\Storage;
@endphp

@section('title', 'Detail Penjualan')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-orange-50 via-red-50 to-orange-100">
    {{-- Header --}}
    <div class="bg-gradient-to-r from-orange-900 via-orange-800 to-red-900 shadow-xl">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('sales.index') }}"
                       class="inline-flex items-center justify-center w-10 h-10 bg-white/10 hover:bg-white/20 rounded-xl transition-all duration-200 backdrop-blur-sm border border-white/20 group">
                        <svg class="w-5 h-5 text-white group-hover:text-orange-200 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-2xl lg:text-3xl font-bold text-white">Detail Penjualan</h1>
                        <p class="text-orange-200 mt-1">No. {{ $sale->sale_number }} • {{ $sale->branch->name ?? 'Cabang: N/A' }}</p>
                    </div>
                </div>

                <div class="flex items-center space-x-3">
                    @if($sale->status === 'completed')
                        <a href="{{ route('sales.receipt.download', ['sale' => $sale->id]) }}" target="_blank" rel="noopener"
                           class="inline-flex items-center px-4 py-2.5 bg-white/10 hover:bg-white/20 text-white rounded-xl font-medium transition-all duration-200 backdrop-blur-sm border border-white/20 group">
                            <svg class="w-4 h-4 mr-2 group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 17h-2v-6H8l4-4 4 4h-3v6z"/>
                            </svg>
                            Unduh Struk
                        </a>
                    @endif
                    <a href="{{ route('sales.index') }}" class="inline-flex items-center px-4 py-2.5 bg-white/10 hover:bg-white/20 text-white rounded-xl font-medium transition-all duration-200 backdrop-blur-sm border border-white/20 group">
                        <svg class="w-4 h-4 mr-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
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
            {{-- Main --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-xl border border-gray-200/50 overflow-hidden">
                    {{-- Sale Header --}}
                    <div class="relative bg-gradient-to-r from-orange-500 via-red-500 to-orange-600 px-8 py-8">
                        <div class="absolute inset-0 bg-black/8"></div>
                        <div class="relative flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div>
                                <h2 class="text-3xl font-bold text-white mb-1">No. {{ $sale->sale_number }}</h2>
                                <p class="text-orange-100">{{ $sale->branch->name ?? 'Cabang: N/A' }} · {{ $sale->created_at->format('d/m/Y H:i') }}</p>
                            </div>

                            <div class="text-right">
                                <div class="inline-flex items-center px-4 py-2 bg-white/10 text-white rounded-full text-sm font-semibold border border-white/20">
                                    @if($sale->status === 'completed')
                                        <svg class="w-4 h-4 mr-2 text-green-300" fill="currentColor" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                                        Selesai
                                    @elseif($sale->status === 'cancelled')
                                        <svg class="w-4 h-4 mr-2 text-red-300" fill="currentColor" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
                                        Dibatalkan
                                    @else
                                        <svg class="w-4 h-4 mr-2 text-yellow-300" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2a10 10 0 100 20 10 10 0 000-20z"/></svg>
                                        Proses
                                    @endif
                                </div>
                                <p class="text-orange-100 text-sm mt-2">Kasir: {{ $sale->user->name ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Body --}}
                    <div class="p-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div class="p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border border-blue-100">
                                <h4 class="font-semibold text-gray-800 mb-2">Informasi Transaksi</h4>
                                <div class="text-sm text-gray-600 space-y-1">
                                    <div class="flex justify-between"><span>No. Transaksi</span><span class="font-mono">{{ $sale->sale_number }}</span></div>
                                    <div class="flex justify-between"><span>Tanggal</span><span>{{ $sale->created_at->format('d/m/Y H:i:s') }}</span></div>
                                    <div class="flex justify-between"><span>Cabang</span><span>{{ $sale->branch->name ?? 'N/A' }}</span></div>
                                    <div class="flex justify-between"><span>Kasir</span><span>{{ $sale->user->name ?? 'N/A' }}</span></div>
                                    <div class="flex justify-between"><span>Status</span>
                                        <span class="font-semibold">
                                            {{ ucfirst($sale->status) }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="p-4 bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl border border-purple-100">
                                <h4 class="font-semibold text-gray-800 mb-2">Informasi Pelanggan</h4>
                                <div class="text-sm text-gray-600 space-y-1">
                                    <div class="flex justify-between"><span>Nama</span><span class="font-medium">{{ $sale->customer_name ?: 'Umum' }}</span></div>
                                    <div class="flex justify-between"><span>No. Telepon</span>
                                        <span>
                                            @if($sale->customer_phone)
                                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $sale->customer_phone) }}" target="_blank" class="text-green-600 hover:text-green-700">
                                                    {{ $sale->customer_phone }}
                                                </a>
                                            @else
                                                -
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Items List --}}
                        <div class="mb-6">
                            <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-3 text-orange-500" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M3 3h18v2H3V3zm2 4h14v13H5V7zm2 2v9h10V9H7z"/>
                                </svg>
                                Detail Item
                            </h3>

                            <div class="bg-white rounded-lg border border-gray-100">
                                <div class="divide-y">
                                    @foreach($sale->items as $index => $item)
                                        <div class="p-4 flex items-start justify-between">
                                            <div class="min-w-0">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-10 h-10 rounded-lg bg-orange-50 flex items-center justify-center text-orange-600 font-semibold">
                                                        {{ $index + 1 }}
                                                    </div>
                                                    <div>
                                                        <div class="font-semibold text-gray-800">{{ $item->item_name }}</div>
                                                        <div class="text-sm text-gray-500">
                                                            <span class="inline-flex items-center px-2 py-0.5 bg-gray-100 rounded-full text-xs text-gray-700">
                                                                {{ $item->item_type === 'product' ? 'Produk' : 'Paket' }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="text-right">
                                                <div class="text-sm text-gray-600">{{ $item->quantity }} x Rp {{ number_format($item->unit_price, 0, ',', '.') }}</div>
                                                <div class="font-semibold text-gray-800">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="p-4 bg-gray-50 flex justify-between items-center">
                                    <div class="text-sm text-gray-600">Total Item</div>
                                    <div class="font-semibold">{{ $sale->items->sum('quantity') }} pcs</div>
                                </div>
                            </div>
                        </div>

                        {{-- Payment Summary (wide) --}}
                        <div class="bg-white rounded-xl border border-gray-100 p-4">
                            <h4 class="font-semibold text-gray-800 mb-3">Ringkasan Pembayaran</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm text-gray-700">
                                <div class="flex justify-between"><span>Subtotal</span><span>Rp {{ number_format($sale->subtotal_amount, 0, ',', '.') }}</span></div>

                                @if($sale->discount_amount > 0)
                                    <div class="flex justify-between text-red-600"><span>Diskon @if($sale->discount_type === 'percentage') ({{ $sale->discount_value }}%) @endif</span>
                                        <span>-Rp {{ number_format($sale->discount_amount, 0, ',', '.') }}</span></div>
                                @endif

                                <div class="flex justify-between font-bold text-gray-900 text-lg md:col-span-2"><span>Total Bayar</span>
                                    <span>Rp {{ number_format($sale->final_amount, 0, ',', '.') }}</span></div>

                                <div class="flex justify-between"><span>Metode</span>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-gray-100 text-sm">
                                        {{ $sale->payment_method === 'cash' ? 'Tunai' : 'QRIS' }}
                                    </span></div>

                                <div class="flex justify-between"><span>Dibayar</span><span>Rp {{ number_format($sale->paid_amount, 0, ',', '.') }}</span></div>

                                @if($sale->change_amount > 0)
                                    <div class="flex justify-between text-green-600"><span>Kembalian</span><span>Rp {{ number_format($sale->change_amount, 0, ',', '.') }}</span></div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Note for completed sales --}}
                @if($sale->status === 'completed')
                    <div class="mt-4">
                        <div class="bg-yellow-50 border-l-4 border-yellow-300 p-4 rounded-lg">
                            <div class="flex items-start gap-3">
                                <div class="text-yellow-600">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M1 21h22L12 2 1 21z"/>
                                    </svg>
                                </div>
                                <div class="text-sm text-gray-700">
                                    <strong>Catatan:</strong> Transaksi yang sudah selesai tidak dapat diedit. Jika perlu pembatalan, silakan hubungi administrator.
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                {{-- Totals & Actions --}}
                <div class="bg-white rounded-2xl shadow-xl border border-gray-200/50 overflow-hidden">
                    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-4">
                        <h3 class="text-lg font-bold text-white flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/>
                            </svg>
                            Ringkasan
                        </h3>
                    </div>

                    <div class="p-6 space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Subtotal</span>
                            <span class="font-semibold">Rp {{ number_format($sale->subtotal_amount, 0, ',', '.') }}</span>
                        </div>

                        @if($sale->discount_amount > 0)
                            <div class="flex items-center justify-between text-red-600">
                                <span>Diskon</span>
                                <span>-Rp {{ number_format($sale->discount_amount, 0, ',', '.') }}</span>
                            </div>
                        @endif

                        <div class="flex items-center justify-between text-lg font-bold">
                            <span>Total Bayar</span>
                            <span>Rp {{ number_format($sale->final_amount, 0, ',', '.') }}</span>
                        </div>

                        <div class="flex flex-col gap-2">
                            @if($sale->status === 'completed')
                                <a href="{{ route('sales.receipt.download', ['sale' => $sale->id]) }}" target="_blank" rel="noopener"
                                   class="inline-flex items-center justify-center px-4 py-2 rounded-xl bg-green-600 text-white font-medium">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 17h-2v-6H8l4-4 4 4h-3v6z"/>
                                    </svg>
                                    Unduh Struk (PDF)
                                </a>
                            @endif

                            <a href="{{ route('sales.index') }}" class="inline-flex items-center justify-center px-4 py-2 rounded-xl bg-white border border-gray-200 text-gray-700">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                                Kembali ke Daftar
                            </a>
                        </div>
                    </div>
                </div>

                {{-- System Info --}}
                <div class="bg-white rounded-2xl shadow-xl border border-gray-200/50 overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-600 to-gray-700 px-6 py-4">
                        <h3 class="text-lg font-bold text-white flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                            </svg>
                            Info Sistem
                        </h3>
                    </div>
                    <div class="p-6 space-y-4 text-sm text-gray-700">
                        <div class="flex items-center justify-between"><span class="text-gray-500">Sale ID</span><span class="font-mono">{{ $sale->id }}</span></div>
                        <div class="flex items-center justify-between"><span class="text-gray-500">Dibuat</span><span>{{ $sale->created_at->format('d/m/Y H:i') }}</span></div>
                        <div class="flex items-center justify-between"><span class="text-gray-500">Diperbarui</span><span>{{ $sale->updated_at->format('d/m/Y H:i') }}</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
