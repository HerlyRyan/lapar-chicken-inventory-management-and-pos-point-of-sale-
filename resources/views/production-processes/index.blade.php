@extends('layouts.app')

@section('title', 'Proses Produksi')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-green-50/30 to-blue-50/30" x-data="productionManager()"
        @keydown.escape.window="closeAllModals()">

        {{-- Page Header --}}
        @if (auth()->user()->hasRole('Manajer'))
            <x-index.header title="Proses Produksi" subtitle="Kelola proses penggunaan bahan mentah untuk produksi" />
        @else
            <x-index.header title="Proses Produksi" subtitle="Kelola proses penggunaan bahan mentah untuk produksi"
                addRoute="{{ route('production-requests.create') }}" addText="Buat Pengajuan Baru" />
        @endif

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-8">

            {{-- Statistik Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <template x-for="card in stats" :key="card.label">
                    <div class="bg-white rounded-xl shadow-sm border-l-4 p-5 transition hover:shadow-md"
                        :class="card.borderColor">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider" x-text="card.label"></p>
                                <p class="text-2xl font-bold text-gray-800 mt-1" x-text="card.count"></p>
                            </div>
                            <i class="text-3xl text-gray-300" :class="card.icon"></i>
                        </div>
                    </div>
                </template>
            </div>

            {{-- Main Table Section --}}
            <div class="bg-white rounded-lg sm:rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
                <x-index.card-header title="Antrean Produksi" />

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Kode / Peruntukan</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Bahan Mentah</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Status & Progress</th>
                                <th
                                    class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse ($productionRequests as $request)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="font-bold text-gray-900">{{ $request->request_code }}</span>
                                            @if ($request->isApproved())
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">SIAP</span>
                                            @elseif($request->isInProgress())
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">AKTIF</span>
                                            @endif
                                        </div>
                                        <p class="text-sm text-gray-600 line-clamp-1">{{ $request->purpose }}</p>
                                    </td>

                                    {{-- Kolom Bahan Mentah dengan Trigger Modal --}}
                                    <td class="px-6 py-4 text-sm whitespace-nowrap">
                                        <button
                                            @click="openItemsModal({{ $request->items->map(fn($item) => ['name' => $item->rawMaterial->name, 'qty' => $item->requested_quantity, 'unit' => $item->rawMaterial->unit->unit_name])->toJson() }}, '{{ $request->request_code }}')"
                                            class="flex items-center gap-2 px-3 py-1.5 bg-gray-50 hover:bg-blue-50 text-blue-600 rounded-lg border border-gray-200 transition-colors group">
                                            <i class="bi bi-box-seam group-hover:scale-110 transition-transform"></i>
                                            <span class="font-semibold">{{ $request->items->count() }} Items</span>
                                        </button>
                                    </td>

                                    <td class="px-6 py-4">
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold"
                                            style="background-color: {{ $request->status_color }}20; color: {{ $request->status_color }}">
                                            {{ $request->status_label }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-4 text-center">
                                        <div class="flex flex-col gap-2 mx-auto w-32">
                                            @if (auth()->user()->hasRole('Manajer'))
                                                <span
                                                    class="px-3 py-2 text-xs font-medium text-gray-500 bg-gray-100 rounded-lg">Tidak
                                                    ada aksi</span>
                                            @else
                                                @if ($request->isApproved())
                                                    <button @click="openModal('start', {{ $request->id }})"
                                                        class="px-3 py-2 text-xs font-medium text-white bg-green-600 hover:bg-green-700 rounded-lg shadow-sm">
                                                        Mulai Produksi
                                                    </button>
                                                @elseif($request->isInProgress())
                                                    <button @click="openModal('update', {{ $request->id }})"
                                                        class="px-3 py-2 text-xs font-medium text-white bg-amber-500 hover:bg-amber-600 rounded-lg">
                                                        Update Progress
                                                    </button>
                                                    <button @click="openModal('complete', {{ $request->id }})"
                                                        class="px-3 py-2 text-xs font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg">
                                                        Selesaikan
                                                    </button>
                                                @else
                                                    <span
                                                        class="px-3 py-2 text-xs font-medium text-gray-500 bg-gray-100 rounded-lg">Tidak
                                                        ada aksi</span>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-gray-500 italic">Belum ada proses
                                        produksi.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Modal Detail Bahan Mentah --}}
        <template x-teleport="body">
            <div x-show="itemModal.open" class="fixed inset-0 z-[60] flex items-center justify-center p-4">
                <div x-show="itemModal.open" x-transition.opacity class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm">
                </div>
                <div x-show="itemModal.open" x-transition.scale.95 @click.away="itemModal.open = false"
                    class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden relative z-10">
                    <div class="px-6 py-4 border-b flex justify-between items-center bg-gray-50">
                        <div>
                            <h3 class="font-bold text-gray-800">Detail Bahan Mentah</h3>
                            <p class="text-xs text-gray-500 mt-0.5" x-text="itemModal.requestCode"></p>
                        </div>
                        <button @click="itemModal.open = false"
                            class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            <template x-for="(item, index) in itemModal.items" :key="index">
                                <div
                                    class="flex justify-between items-center p-3 bg-gray-50 rounded-xl border border-gray-100">
                                    <span class="font-medium text-gray-700" x-text="item.name"></span>
                                    <span class="px-3 py-1 bg-white border rounded-lg text-sm font-bold text-blue-600"
                                        x-text="`${Number(item.qty)} ${item.unit}`"></span>
                                </div>
                            </template>
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-gray-50 text-right">
                        <button @click="itemModal.open = false"
                            class="px-4 py-2 text-sm font-bold text-gray-600 hover:text-gray-800">Tutup</button>
                    </div>
                </div>
            </div>
        </template>

        {{-- Global Modal Production Action (Start/Complete) --}}
        <template x-teleport="body">
            <div x-show="modal.open" class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div x-show="modal.open" x-transition.opacity class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm"></div>
                <div x-show="modal.open" x-transition.scale.95 @click.away="closeAllModals()"
                    class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden relative z-10">

                    <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-white">
                        <h3 class="text-lg font-bold text-gray-800" x-text="modal.title"></h3>
                        <button @click="closeAllModals()" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form :action="modal.action" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="px-6 py-6">
                            {{-- Modal type update --}}
                            <template x-if="modal.type === 'update'">
                                <div class="space-y-4">
                                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r-lg">
                                        <p class="text-sm text-yellow-700 italic">Konfirmasi untuk memulai update produksi
                                            ini.</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Catatan Update
                                            Produksi</label>
                                        <textarea name="production_notes"
                                            class="w-full rounded-xl border-gray-300 focus:ring-yellow-500 focus:border-yellow-500 p-3" rows="3"></textarea>
                                    </div>
                                </div>
                            </template>

                            {{-- Content sesuai type (start/complete) --}}
                            <template x-if="modal.type === 'start'">
                                <div class="space-y-4">
                                    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-r-lg">
                                        <p class="text-sm text-blue-700 italic">Konfirmasi untuk memulai proses produksi
                                            ini.</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Catatan
                                            Produksi</label>
                                        <textarea name="production_notes"
                                            class="w-full rounded-xl border border-gray-300 focus:ring-blue-500 focus:border-blue-500 p-3" rows="3"></textarea>
                                    </div>
                                </div>
                            </template>

                            <template x-if="modal.type === 'complete'">
                                <div class="space-y-6">
                                    <div class="border rounded-xl overflow-hidden shadow-sm">
                                        <div
                                            class="bg-gray-50 px-4 py-2 border-b text-xs font-bold text-gray-500 uppercase">
                                            Input Hasil Produksi</div>
                                        <div class="max-h-60 overflow-y-auto">
                                            <template x-if="loading">
                                                <div class="p-8 text-center text-sm text-gray-500 italic">Memuat data...
                                                </div>
                                            </template>
                                            <table class="w-full text-sm" x-show="!loading">
                                                <thead class="bg-gray-50 sticky top-0">
                                                    <tr>
                                                        <th class="p-3 text-left">Produk</th>
                                                        <th class="p-3 text-center">Rencana</th>
                                                        <th class="p-3 text-center w-24">Realisasi</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-gray-100">
                                                    <template x-for="item in plannedOutputs" :key="item.id">
                                                        <tr>
                                                            <td class="p-3 font-medium" x-text="item.product_name"></td>
                                                            <td class="p-3 text-center text-gray-500"
                                                                x-text="item.quantity"></td>
                                                            <td class="p-3">
                                                                <input type="number"
                                                                    :name="`realized_outputs[${item.id}]`"
                                                                    class="w-full rounded-lg border-gray-300 text-center text-sm"
                                                                    :value="item.quantity" required min="0">
                                                            </td>
                                                        </tr>
                                                    </template>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Bukti Foto</label>
                                        <input type="file" name="production_evidence" class="w-full text-sm" required>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
                            <button type="button" @click="closeAllModals()"
                                class="px-5 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-100">Batal</button>
                            <button type="submit"
                                class="px-5 py-2.5 text-sm font-semibold text-white bg-blue-600 rounded-xl hover:bg-blue-700 shadow-lg shadow-blue-200"
                                x-text="modal.buttonText"></button>
                        </div>
                    </form>
                </div>
            </div>
        </template>
    </div>
@endsection

<script>
    function productionManager() {
        return {
            loading: false,
            stats: [{
                    label: 'Siap Diproduksi',
                    count: '{{ $productionRequests->where('status', 'approved')->count() }}',
                    borderColor: 'border-green-500',
                    icon: 'bi bi-play-circle-fill text-green-500'
                },
                {
                    label: 'Sedang Diproduksi',
                    count: '{{ $productionRequests->where('status', 'in_progress')->count() }}',
                    borderColor: 'border-blue-500',
                    icon: 'bi bi-gear-wide-connected text-blue-500'
                },
                {
                    label: 'Selesai',
                    count: '{{ $productionRequests->where('status', 'completed')->count() }}',
                    borderColor: 'border-indigo-500',
                    icon: 'bi bi-check-all text-indigo-500'
                }
            ],

            // Modal untuk Aksi Produksi
            modal: {
                open: false,
                type: '',
                title: '',
                action: '',
                buttonText: ''
            },
            plannedOutputs: [],

            // Modal untuk Detail Bahan Mentah
            itemModal: {
                open: false,
                items: [],
                requestCode: ''
            },

            openItemsModal(items, code) {
                this.itemModal.items = items;
                this.itemModal.requestCode = code;
                this.itemModal.open = true;
            },

            openModal(type, id) {
                this.modal.type = type;
                this.modal.open = true;
                const baseRoute = `/production-processes/${id}`;

                if (type === 'start') {
                    this.modal.title = '🚀 Mulai Produksi';
                    this.modal.action = `${baseRoute}/start`;
                    this.modal.buttonText = 'Mulai Sekarang';
                } else if (type === 'update') {
                    this.modal.title = '📝 Update Progress';
                    this.modal.action = `${baseRoute}/update-status`;
                    this.modal.buttonText = 'Simpan';
                } else if (type === 'complete') {
                    this.modal.title = '✅ Selesaikan Produksi';
                    this.modal.action = `${baseRoute}/complete`;
                    this.modal.buttonText = 'Simpan & Selesai';
                    this.fetchOutputs(id);
                }
            },

            async fetchOutputs(id) {
                this.loading = true;
                try {
                    const response = await fetch(`/production-processes/${id}/planned-outputs`);
                    this.plannedOutputs = await response.json();
                } catch (e) {
                    console.error("Fetch error");
                } finally {
                    this.loading = false;
                }
            },

            closeAllModals() {
                this.modal.open = false;
                setTimeout(() => {
                    this.modal.type = '';
                    this.plannedOutputs = [];
                }, 300);
            }
        }
    }
</script>
