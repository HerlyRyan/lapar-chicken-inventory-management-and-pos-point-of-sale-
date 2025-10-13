@extends('layouts.app')

@section('title', 'Detail Supplier')

@section('content')
<x-detail-page-header 
    title="{{ $supplier->name }}"
    subtitle="Detail informasi supplier"
    icon="truck"
    :backRoute="route('suppliers.index')"
    :editRoute="route('suppliers.edit', $supplier)"
/>

    <div class="row g-4">
        <div class="col-lg-8">
            <x-detail-info-card title="Informasi Supplier" icon="info-circle" gradient="info">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-muted">Nama Supplier</label>
                            <div class="form-control-plaintext h5 fw-bold text-primary">
                                {{ $supplier->name }}
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold text-muted">Alamat</label>
                            <div class="form-control-plaintext">
                                @if($supplier->address)
                                    <span>{{ $supplier->address }}</span>
                                @else
                                    <span class="text-muted fst-italic">Alamat belum diisi</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-muted">Nomor Telepon</label>
                            <div class="form-control-plaintext">
                                @if($supplier->phone)
                                    <span class="badge bg-light text-dark fs-6">{{ $supplier->phone }}</span>
                                @else
                                    <span class="text-muted fst-italic">Nomor telepon belum diisi</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-muted">Email</label>
                            <div class="form-control-plaintext">
                                @if($supplier->email)
                                    <span>{{ $supplier->email }}</span>
                                @else
                                    <span class="text-muted fst-italic">Email belum diisi</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-muted">Tanggal Dibuat</label>
                            <div class="form-control-plaintext">
                                <i class="bi bi-calendar me-1"></i>{{ $supplier->created_at->format('d F Y, H:i') }}
                            </div>
                        </div>
                    </div>
            </x-detail-info-card>
            <x-detail-info-card title="Bahan Mentah yang Dipasok ({{ $supplier->materials->count() }})" icon="box-seam" gradient="purple" class="mt-4">
                    @if($supplier->materials->count())
    <table class="table table-striped align-middle">
        <thead>
            <tr>
                <th>Kode</th>
                <th>Nama</th>
                <th>Harga Satuan</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($supplier->materials as $material)
                <tr>
                    <td>{{ $material->code }}</td>
                    <td>{{ $material->name }}</td>
                    <td>Rp {{ number_format($material->unit_price, 0, ',', '.') }}</td>
                    <td>
                        @if($material->is_active)
                            <span class="badge bg-success">Aktif</span>
                        @else
                            <span class="badge bg-secondary">Nonaktif</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <p class="text-muted mb-0">Tidak ada bahan mentah yang dipasok oleh supplier ini.</p>
@endif
            </x-detail-info-card>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-lg mb-4">
                <div class="card-header text-white py-3" style="background: linear-gradient(135deg, #eab308 0%, #ea580c 50%, #dc2626 100%);">
                    <h6 class="mb-0 fw-bold">
                        <i class="bi bi-gear me-2"></i>Aksi
                    </h6>
                </div>
                <div class="card-body p-3">
                    <div class="d-grid gap-2">
                        <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-warning">
                            <i class="bi bi-pencil me-2"></i>Edit
                        </a>
                        
                        <button onclick="confirmDelete({{ $supplier->id }}, '{{ $supplier->name }}')" class="btn btn-danger">
                            <i class="bi bi-trash me-2"></i>Hapus
                        </button>
                        
                        <form id="delete-form-{{ $supplier->id }}" action="{{ route('suppliers.destroy', $supplier) }}" method="POST" style="display: none;">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="redirect_to" value="{{ route('suppliers.index') }}">
                        </form>
                        
                        @if($supplier->phone)
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $supplier->phone) }}?text=Halo {{ $supplier->name }}" target="_blank" class="btn btn-success">
                            <i class="bi bi-whatsapp me-2"></i>WhatsApp
                        </a>
                        @endif
                        
                        @if($supplier->email)
                        <a href="mailto:{{ $supplier->email }}?subject=Perihal {{ $supplier->name }}" class="btn btn-info">
                            <i class="bi bi-envelope me-2"></i>Kirim Email
                        </a>
                        @endif
                        
                        <hr class="my-2">
                        
                        <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Kembali
                        </a>
                    </div>
                </div>
            </div>

            <x-detail-system-info-card :model="$supplier" />
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmDelete(id, name) {
    Swal.fire({
        title: 'Konfirmasi Hapus',
        text: `Apakah Anda yakin ingin menghapus supplier '${name}'?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Menghapus...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                    document.getElementById(`delete-form-${id}`).submit();
                }
            });
        }
    });
}

function sendWhatsApp(phone, supplierName) {
    window.open('https://wa.me/' + phone + '?text=Halo%20' + encodeURIComponent(supplierName), '_blank');
}

function sendEmail(email, supplierName) {
    window.open('mailto:' + email + '?subject=Halo%20' + encodeURIComponent(supplierName), '_blank');
}
</script>
@endpush

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
@endpush
@endsection
