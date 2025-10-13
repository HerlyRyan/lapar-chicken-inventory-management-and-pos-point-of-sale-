@extends('layouts.app')

@section('title', 'Supplier')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1" style="color: var(--primary-red);">
            <i class="bi bi-building-fill-gear me-2"></i>Supplier
        </h1>
        <p class="text-muted mb-0">Kelola data supplier bahan mentah</p>
    </div>
    <a href="{{ route('suppliers.create') }}" class="btn btn-primary shadow px-4 d-flex align-items-center" style="min-width: 160px; white-space: nowrap;">
        <i class="bi bi-plus-circle me-2"></i>
        <span>Tambah Supplier</span>
    </a>
</div>

<div class="card border-0 shadow-lg" style="max-width: 100%; overflow-x: hidden;">
    <div class="card-header text-white py-3" style="background: linear-gradient(135deg, #dc2626 0%, #ea580c 50%, #eab308 100%);">
        <h5 class="mb-0 fw-bold">
            <i class="bi bi-list-ul me-2"></i>Daftar Supplier
        </h5>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Search Filter -->
        <form method="GET" action="{{ route('suppliers.index') }}" class="row g-3 mb-4 table-filter-form">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, kode, atau alamat supplier..." class="form-control">
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
                        <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-clockwise me-1"></i>Reset
                        </a>
                    @endif
                </div>
            </div>
        </form>

        <!-- Table with sortable columns -->
        <div class="table-responsive">
            <table class="table table-hover table-striped standard-table mb-0">
                <thead>
                    <tr>
                        <th width="80px">{!! sortColumn('code', 'Kode', $sortColumn, $sortDirection) !!}</th>
                        <th width="180px">{!! sortColumn('name', 'Nama', $sortColumn, $sortDirection) !!}</th>
                        <th width="250px">Alamat</th>
                        <th width="130px">{!! sortColumn('phone', 'No. Telepon', $sortColumn, $sortDirection) !!}</th>
                        <th width="100px">Status</th>
                        <th width="150px" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suppliers as $supplier)
                    @php
                        $supplierObj = is_object($supplier) ? $supplier : null;
                        $code = $supplierObj ? $supplierObj->code ?? '' : '';
                        $name = $supplierObj ? $supplierObj->name ?? '' : '';
                        $address = $supplierObj ? $supplierObj->address ?? '' : '';
                        $phone = $supplierObj ? $supplierObj->phone ?? '' : '';
                        $is_active = $supplierObj ? $supplierObj->is_active ?? false : false;
                        $id = $supplierObj ? $supplierObj->id ?? 0 : 0;
                    @endphp
                    <tr>
                        <td>{{ $code }}</td>
                        <td class="fw-semibold">{{ $name }}</td>
                        <td>
                            <div class="text-truncate" style="max-width: 250px;" title="{{ $address }}">
                                {{ $address }}
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <span>{{ $phone }}</span>
                                <div class="d-flex gap-1">
                                    <a href="tel:+{{ $phone }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-telephone"></i>
                                    </a>
                                    <a href="https://wa.me/{{ $phone }}" target="_blank" class="btn btn-sm btn-success">
                                        <i class="bi bi-whatsapp"></i>
                                    </a>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($is_active)
                                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Aktif</span>
                            @else
                                <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Tidak Aktif</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <x-action-buttons
                                :viewUrl="route('suppliers.show', $id)" 
                                :editUrl="route('suppliers.edit', $id)"
                                :deleteUrl="route('suppliers.destroy', $id)" 
                                :toggleUrl="route('suppliers.toggle-status', $id)"
                                :isActive="$is_active"
                                :showToggle="true"
                                itemName="supplier {{$name}}"
                            />
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="d-flex flex-column align-items-center">
                                <i class="bi bi-inbox fs-1 text-muted mb-3"></i>
                                <h5 class="fw-bold text-muted">Belum ada data supplier</h5>
                                <p class="text-muted">Tambahkan supplier baru untuk mengelola data supplier</p>
                                <a href="{{ route('suppliers.create') }}" class="btn btn-primary px-4 mt-2">
                                    <i class="bi bi-plus-circle me-2"></i>Tambah Supplier
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-4">
            <div>
                @if($suppliers instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    <p class="text-muted mb-0">Menampilkan {{ $suppliers->firstItem() ?? 0 }}-{{ $suppliers->lastItem() ?? 0 }} dari {{ $suppliers->total() }} data</p>
                @else
                    <p class="text-muted mb-0">Menampilkan {{ count($suppliers) }} data</p>
                @endif
            </div>
            <div>
                @if($suppliers instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    {{ $suppliers->withQueryString()->links() }}
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Delete Form -->
<form id="delete-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<style>
/* Table responsiveness */
.table-responsive {
    width: 100%;
    transition: all 0.3s ease; /* Match the sidebar transition speed */
}

.btn-success {
    background: linear-gradient(135deg, #25d366, #128c7e);
    border: none;
    transition: all 0.3s ease;
}

.btn-success:hover {
    background: linear-gradient(135deg, #128c7e, #075e54);
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(37, 211, 102, 0.3);
}
</style>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmDelete(id, name) {
        Swal.fire({
            title: 'Hapus Supplier?',
            text: `Anda yakin ingin menghapus supplier "${name}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('delete-form');
                form.action = `{{ url('suppliers') }}/${id}`;
                form.submit();
            }
        });
    }
</script>
@endpush
