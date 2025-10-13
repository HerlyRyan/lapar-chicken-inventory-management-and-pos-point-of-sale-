@extends('layouts.app')

@section('title', 'Master Bahan Baku')

@section('content')
<?php $materials = $rawMaterials; ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1" style="color: var(--primary-red);">
            <i class="bi bi-box me-2"></i>Master Bahan Baku
        </h1>
        <p class="text-muted mb-0">Kelola data bahan baku dan material produksi</p>
    </div>
    <a href="{{ route('raw-materials.create') }}" class="btn btn-primary shadow px-4 d-flex align-items-center" style="min-width: 160px; white-space: nowrap;">
        <i class="bi bi-plus-circle me-2"></i>
        <span>Tambah Bahan Baku</span>
    </a>
</div>

<div class="card border-0 shadow-lg">
    <div class="card-header text-white py-3" style="background: linear-gradient(135deg, #dc2626 0%, #ea580c 50%, #eab308 100%);">
        <h5 class="mb-0 fw-bold">
            <i class="bi bi-list-ul me-2"></i>Daftar Bahan Baku
        </h5>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>{!! session('warning') !!}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        <!-- Search Filter -->
        <form method="GET" action="" class="row g-3 mb-4 table-filter-form">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, kode, atau deskripsi bahan baku..." class="form-control">
                </div>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="all">Semua Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                </select>
            </div>
            <div class="col-md-3">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-filter me-1"></i>Filter
                    </button>
                    @if(request('search') || request('status') != 'all')
                        <a href="{{ route('raw-materials.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-clockwise me-1"></i>Reset
                        </a>
                    @endif
                </div>
            </div>
        </form>

        <!-- Table -->
        <x-standard-table
            :headers="[
                ['text' => 'Kode', 'sort' => 'code', 'class' => sortColumn('code', 'Kode', $sortColumn, $sortDirection)],
                ['text' => 'Gambar', 'width' => '80px'],
                ['text' => 'Nama Bahan Baku', 'sort' => 'name', 'class' => sortColumn('name', 'Nama Bahan Baku', $sortColumn, $sortDirection)],
                ['text' => 'Kategori'],
                ['text' => 'Satuan'],
                ['text' => 'Stok Saat Ini', 'sort' => 'current_stock', 'class' => sortColumn('current_stock', 'Stok Saat Ini', $sortColumn, $sortDirection)],
                ['text' => 'Stok Minimum', 'sort' => 'minimum_stock', 'class' => sortColumn('minimum_stock', 'Stok Minimum', $sortColumn, $sortDirection)],
                ['text' => 'Harga Satuan', 'sort' => 'unit_price', 'class' => sortColumn('unit_price', 'Harga Satuan', $sortColumn, $sortDirection)],
                ['text' => 'Supplier'],
                ['text' => 'Status']
            ]"
            :pagination="$materials"
            :searchable="false"
            :sortable="true"
            :sortColumn="$sortColumn"
            :sortDirection="$sortDirection"
        >
                    @forelse($materials as $material)
                    @php
                        $isLowStock = $material->current_stock <= $material->minimum_stock;
                    @endphp
                    <tr>
                        <td>
                            @if($material->code)
                                <span class="badge bg-danger text-warning">{{ $material->code }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($material->image && file_exists(public_path($material->image)))
                                <img src="{{ asset($material->image) }}" alt="{{ $material->name }}" class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                            @else
                                <div class="bg-light border rounded d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                    <i class="bi bi-image text-muted fs-4"></i>
                                </div>
                            @endif
                        </td>
                        <td>
                            <div class="fw-bold">{{ $material->name }}</div>
                            <small class="text-muted">{{ Str::limit($material->description, 50) }}</small>
                        </td>
                        <td>
                            @if($material->category)
                                <span class="badge bg-info bg-opacity-25 text-info-emphasis border border-info-subtle">
                                    <i class="bi bi-tag"></i> {{ $material->category->name }}
                                </span>
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            {{ $material->unit ? $material->unit->unit_name : '-' }}
                        </td>
                        <td>
                            <div class="fw-bold fs-5 {{ $isLowStock ? 'text-danger' : 'text-success' }}">
                                {{ number_format($material->current_stock, 0, ',', '.') }}
                                @if($isLowStock)
                                    <i class="bi bi-exclamation-triangle-fill text-warning ms-1" title="Stok di bawah minimum"></i>
                                @endif
                            </div>
                        </td>
                        <td>
                            {{ number_format($material->minimum_stock, 0, ',', '.') }}
                        </td>
                        <td>
                            Rp {{ number_format($material->unit_price, 0, ',', '.') }}
                        </td>
                        <td>
                             @if($material->supplier)
                                <span class="badge bg-secondary bg-opacity-25 text-secondary-emphasis border border-secondary-subtle">{{ $material->supplier->name }}</span>
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if($material->is_active)
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-secondary">Nonaktif</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <x-action-buttons
                                :viewUrl="route('raw-materials.show', $material->id)" 
                                :editUrl="route('raw-materials.edit', $material->id)"
                                :deleteUrl="route('raw-materials.destroy', $material->id)" 
                                :toggleUrl="route('raw-materials.toggle-status', $material->id)"
                                :isActive="$material->is_active"
                                :showToggle="true"
                                itemName="bahan mentah {{$material->name}}"
                            />
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="text-center py-5">
                            <i class="bi bi-inbox fs-1 text-muted mb-2"></i>
                            <h5 class="text-muted">Data bahan baku tidak ditemukan.</h5>
                            <p class="text-muted">Coba kata kunci lain atau tambahkan data baru.</p>
                        </td>
                    </tr>
                    @endforelse
        </x-standard-table>
    </div>
</div>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<style>
    .table-responsive thead th {
        position: sticky;
        top: 0;
        z-index: 1;
        background-color: #f8f9fa; /* Match table-light bg color */
        box-shadow: inset 0 -2px 0 #dee2e6; /* Optional: to add a bottom border */
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmDelete(target, materialName) {
    Swal.fire({
        title: 'Konfirmasi Hapus',
        html: `Apakah Anda yakin ingin menghapus bahan baku <br><strong>${materialName}</strong>?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="bi bi-trash me-1"></i>Ya, Hapus!',
        cancelButtonText: '<i class="bi bi-x-circle me-1"></i>Batal',
        reverseButtons: true,
        focusCancel: true,
        customClass: {
            confirmButton: 'btn btn-danger',
            cancelButton: 'btn btn-secondary'
        },
        buttonsStyling: false
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: 'Menghapus...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading()
                }
            });
            
            // Create and submit form
            const form = document.createElement('form');
            form.method = 'POST';
            // Support both ID and full URL passed from the action button
            let actionUrl;
            try {
                const t = String(target ?? '').trim();
                if (t.startsWith('http') || t.startsWith('/')) {
                    actionUrl = t;
                } else {
                    actionUrl = `/raw-materials/${t}`;
                }
            } catch (e) {
                actionUrl = `/raw-materials/${target}`;
            }
            form.action = actionUrl;
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            
            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';
            
            form.appendChild(csrfToken);
            form.appendChild(methodField);
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>
@endpush
@endsection
