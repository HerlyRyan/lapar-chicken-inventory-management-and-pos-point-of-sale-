<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Lapar Chicken InventPOS</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('img/Logo.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('img/Logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('img/Logo.png') }}">

    <!-- External Dependencies -->
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <!-- jQuery for legacy scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-orange-50 text-gray-800">
    {{-- Success Alert --}}
    <x-toast />
    <x-loading-overlay />
    <div x-data="{
        sidebarOpen: false,
        handleResize() {
            if (window.innerWidth < 768) {
                this.sidebarOpen = false;
            }
        }
    }" x-init="handleResize();
    window.addEventListener('resize', () => handleResize());" class="flex">
        <!-- Sidebar -->
        @include('layouts.internal.sidebar')

        <!-- Main content -->
        <div class="flex-1 flex flex-col overflow-x-auto">
            @include('layouts.internal.header')
            <main class="flex-1 p-6 max-w-full">
                @yield('content')
            </main>
        </div>
    </div>
</body>

{{-- Delete Confirmation Modal --}}
<div id="deleteModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 transform transition-all">
        <div class="p-6 text-center">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4
                           c-.77-.833-1.732-.833-2.464 0L4.732 16.5
                           c-.77.833.192 2.5 1.732 2.5z" />
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">Konfirmasi Penghapusan</h3>
            <p id="deleteMessage" class="text-gray-600 mb-6">
                Apakah Anda yakin ingin menghapus data ini?
            </p>
            <div class="flex space-x-3">
                <button type="button" onclick="closeDeleteModal()"
                    class="flex-1 px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-xl font-medium transition-colors">
                    Batal
                </button>

                <form id="deleteForm" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl font-medium transition-colors">
                        Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Ambil token dari meta tag Laravel
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    function confirmDelete(url, name = 'data ini') {
        const modal = document.getElementById('deleteModal');
        const form = document.getElementById('deleteForm');
        const message = document.getElementById('deleteMessage');

        if (!modal || !form) {
            console.error('Modal konfirmasi tidak ditemukan');
            return;
        }

        // Update action dan pesan
        form.action = url;
        message.innerHTML = `Apakah Anda yakin ingin menghapus <strong>${name}</strong>?`;

        // Tampilkan modal
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeDeleteModal() {
        const modal = document.getElementById('deleteModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    // Tutup modal dengan tombol Escape
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') closeDeleteModal();
    });

    function distributionModals() {
        return {
            openModal: null,
            currentId: null,
            acceptNotes: '',
            rejectReason: '',
            rejectNotes: '',

            init() {
                // event listener dari tombol
                window.addEventListener('open-accept-modal', (e) => {
                    this.openModal = 'accept';
                    this.currentId = e.detail.id;
                    this.acceptNotes = '';
                });

                window.addEventListener('open-reject-modal', (e) => {
                    this.openModal = 'reject';
                    this.currentId = e.detail.id;
                    this.rejectReason = '';
                    this.rejectNotes = '';
                });
            },

            closeModal() {
                this.openModal = null;
            }
        }
    }

    function materialModals() {
        return {
            openModal: null, // 'accept' | 'reject' | null
            currentId: null,
            requestNumber: null,
            approvalNote: '',
            actualQuantity: 0,
            rejectReason: '',
            rejectNotes: '',
            isSubmitting: false,

            // tambahkan base url langsung di sini
            baseApproveUrl: "{{ route('material-usage-requests.approve', ['semiFinishedUsageRequest' => 'ID_PLACEHOLDER']) }}",
            baseRejectUrl: "{{ route('material-usage-requests.reject', ['semiFinishedUsageRequest' => 'ID_PLACEHOLDER']) }}",

            init() {
                // opsional: listen ke event global kalau ada pihak lain yang mau trigger (tidak wajib)
                window.addEventListener('open-accept-modal', (e) => {
                    this.openAccept(e.detail.id, e.detail.number)
                });
                window.addEventListener('open-reject-modal', (e) => {
                    this.openReject(e.detail.id, e.detail.number)
                });
            },

            openAccept(id, number = null) {
                this.resetForm();
                this.currentId = id;
                this.requestNumber = number;
                this.openModal = 'accept';
                // fokus element bisa diatur di sini jika mau
            },

            openReject(id, number = null) {
                this.resetForm();
                this.currentId = id;
                this.requestNumber = number;
                this.openModal = 'reject';
            },

            closeModal() {
                this.openModal = null;
                // clear state jika perlu
            },

            resetForm() {
                this.approvalNote = '';
                this.actualQuantity = 0;
                this.rejectReason = '';
                this.rejectNotes = '';
                this.isSubmitting = false;
            },

            async submitApprove() {
                if (!this.currentId) return;
                this.isSubmitting = true;
                try {
                    const url = this.baseApproveUrl.replace('ID_PLACEHOLDER', this.currentId);

                    const res = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            approval_note: this.approvalNote,
                            actual_quantity: Number(this.actualQuantity)
                        })
                    });

                    if (!res.ok) {
                        const err = await res.json().catch(() => null);
                        throw new Error(err?.message || 'Gagal memproses permintaan');
                    }

                    window.dispatchEvent(new CustomEvent('request-updated', {
                        detail: {
                            id: this.currentId,
                            action: 'approved'
                        }
                    }));

                    const data = await res.json();

                    alert(data.message);
                    location.reload()
                    this.closeModal();

                } catch (err) {
                    console.error(err);
                    alert('Gagal menyetujui: ' + err.message);
                } finally {
                    this.isSubmitting = false;
                }
            },

            async submitReject() {
                if (!this.currentId) return;
                if (!this.rejectReason) {
                    alert('Silakan pilih alasan penolakan.');
                    return;
                }

                this.isSubmitting = true;
                try {
                    const url = this.baseRejectUrl.replace('ID_PLACEHOLDER', this.currentId);

                    const res = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            rejection_reason: this.rejectReason,
                            notes: this.rejectNotes
                        })
                    });

                    if (!res.ok) {
                        const err = await res.json().catch(() => null);
                        throw new Error(err?.message || 'Gagal memproses penolakan');
                    }

                    window.dispatchEvent(new CustomEvent('request-updated', {
                        detail: {
                            id: this.currentId,
                            action: 'rejected'
                        }
                    }));
                    this.closeModal();
                    location.reload()

                } catch (err) {
                    console.error(err);
                    alert('Gagal menolak: ' + err.message);
                } finally {
                    this.isSubmitting = false;
                }
            }

        }
    }

    function approvalHandler() {
        return {
            showModal: false,
            action: null,
            requestId: null,
            notes: '',
            modalTitle: '',
            modalMessage: '',
            requireNotes: false,

            init() {
                console.log('Alpine approvalHandler ready');
            },

            openModal(id, action) {
                console.log('openModal called ✅', {
                    id,
                    action
                });
                this.requestId = id;
                this.action = action;
                this.showModal = true;
                this.notes = '';

                if (action === 'approve') {
                    this.modalTitle = 'Setujui Pengajuan Produksi';
                    this.modalMessage = `
                    <div class='alert alert-success'>
                        <i class='bi bi-check-circle me-2'></i>
                        Setelah disetujui, stok bahan mentah akan dikurangi secara otomatis.
                    </div>`;
                    this.requireNotes = false;
                } else {
                    this.modalTitle = 'Tolak Pengajuan Produksi';
                    this.modalMessage = `
                    <div class='alert alert-danger'>
                        <i class='bi bi-x-circle me-2'></i>
                        Harap berikan alasan penolakan dengan jelas.
                    </div>`;
                    this.requireNotes = true;
                }
            },

            closeModal() {
                this.showModal = false;
            },

            submitApproval() {
                // Validasi manual
                if (this.requireNotes && !this.notes.trim()) {
                    alert('Catatan wajib diisi untuk penolakan.');
                    return;
                }

                // Kirim request ke backend
                fetch(`/production-approvals/${this.requestId}/${this.action}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            approval_notes: this.notes
                        })
                    })
                    .then(res => {
                        if (!res.ok) throw new Error('Gagal memproses permintaan');
                        return res.json();
                    })
                    .then(data => {
                        alert(data.message || 'Berhasil diproses');
                        window.location.reload(); // refresh tabel
                    })
                    .catch(err => alert(err.message))
                    .finally(() => this.closeModal());
            }
        }
    }
</script>

</html>
