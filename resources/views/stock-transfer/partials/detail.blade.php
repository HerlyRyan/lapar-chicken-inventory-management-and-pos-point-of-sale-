@php
    // Logika PHP tetap sama seperti sebelumnya
    $unitAbbr = 'unit';
    $product =
        $stockTransfer->item_type === 'finished'
            ? $stockTransfer->finishedProduct
            : $stockTransfer->semiFinishedProduct;
    if ($product && $product->unit) {
        $unitAbbr = $product->unit->abbreviation ?: $product->unit->unit_name ?? 'unit';
    }
    $currentBranchId = session('current_branch_id') ?? (auth()->user()->branch_id ?? null);
@endphp

<div class="space-y-6 text-gray-800" x-data="{
    notes: '',
    confirmAccept() {
        Swal.fire({
            title: 'Terima Stok?',
            text: 'Tambahkan catatan jika diperlukan:',
            input: 'textarea',
            inputPlaceholder: 'Catatan...',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#f97316',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Terima!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                this.notes = result.value;
                // Gunakan submit native agar lebih aman
                this.$nextTick(() => { document.getElementById('acceptForm').submit(); });
            }
        });
    },
    confirmReject() {
        Swal.fire({
            title: 'Tolak Stok?',
            text: 'Alasan penolakan (Wajib):',
            input: 'textarea',
            inputPlaceholder: 'Kenapa ditolak?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Tolak!',
            cancelButtonText: 'Batal',
            inputValidator: (value) => {
                if (!value) return 'Alasan harus diisi!';
            }
        }).then((result) => {
            if (result.isConfirmed) {
                this.notes = result.value;
                this.$nextTick(() => { document.getElementById('rejectForm').submit(); });
            }
        });
    }
}">

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="bg-orange-50 px-5 py-3 border-b border-orange-100 flex justify-between items-center">
                <h6 class="font-bold text-orange-700 flex items-center italic">
                    <i class="fas fa-truck-loading mr-2"></i> Logistik Transfer
                </h6>
                <span class="text-[10px] font-mono text-orange-400">#{{ $stockTransfer->id }}</span>
            </div>
            <div class="p-5">
                <div class="flex items-center justify-between border-b border-gray-50 pb-4 mb-4">
                    <div>
                        <p class="text-[10px] uppercase text-gray-400 font-bold tracking-tighter">Cabang Asal</p>
                        <p class="text-sm font-bold text-gray-700">{{ $stockTransfer->fromBranch->name ?? 'Pusat' }}</p>
                    </div>
                    <div class="text-orange-500 animate-pulse">
                        <i class="fas fa-chevron-right"></i>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] uppercase text-gray-400 font-bold tracking-tighter">Cabang Tujuan</p>
                        <p class="text-sm font-bold text-gray-700">{{ $stockTransfer->toBranch->name ?? '-' }}</p>
                    </div>
                </div>
                <div class="flex justify-between items-center text-xs">
                    <span class="text-gray-400 italic">{{ $stockTransfer->created_at->format('d M Y H:i') }}</span>
                    <span class="px-2 py-0.5 rounded-md bg-orange-100 text-orange-700 font-bold uppercase text-[9px]">
                        {{ $stockTransfer->status }}
                    </span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-5 flex flex-col items-center justify-center h-full space-y-3">
                <div class="text-center">
                    <p class="text-[10px] font-black text-orange-400 uppercase tracking-widest mb-1">Item Transfer</p>
                    <h4 class="text-lg font-black text-gray-900 leading-tight">
                        {{ $product->name ?? 'N/A' }}
                    </h4>
                </div>
                <div
                    class="bg-orange-500 text-white px-5 py-1.5 rounded-full shadow-md shadow-orange-100 flex items-baseline gap-2">
                    <span class="text-2xl font-black">{{ number_format($stockTransfer->quantity, 0) }}</span>
                    <span class="text-xs font-bold opacity-80 uppercase">{{ $unitAbbr }}</span>
                </div>
            </div>
        </div>
    </div>

    @if ($stockTransfer->status === 'sent' && $currentBranchId === $stockTransfer->to_branch_id)
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <form id="acceptForm" method="POST" action="{{ route('stock-transfer.accept', $stockTransfer->id) }}">
                @csrf
                <input type="hidden" name="response_notes" x-model="notes">
                <button type="button" @click="confirmAccept()"
                    class="w-full py-4 bg-orange-500 hover:bg-orange-600 text-white rounded-xl font-black text-sm transition-all transform active:scale-95 shadow-lg shadow-orange-100 flex items-center justify-center gap-2">
                    <i class="fas fa-check-double"></i> TERIMA STOK
                </button>
            </form>

            <form id="rejectForm" method="POST" action="{{ route('stock-transfer.reject', $stockTransfer->id) }}">
                @csrf
                <input type="hidden" name="response_notes" x-model="notes">
                <button type="button" @click="confirmReject()"
                    class="w-full py-4 bg-white border-2 border-rose-100 text-rose-600 hover:bg-rose-50 rounded-xl font-black text-sm transition-all flex items-center justify-center gap-2">
                    <i class="fas fa-ban"></i> TOLAK TRANSFER
                </button>
            </form>
        </div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
