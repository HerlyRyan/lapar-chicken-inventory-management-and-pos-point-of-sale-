@extends('layouts.app')

@section('title', 'Master Data Kategori')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1" style="color: var(--primary-red);">
            <i class="bi bi-tags me-2"></i>Data Kategori
        </h1>
        <p class="text-muted mb-0">Kelola kategori produk siap jual</p>
    </div>
    <a href="{{ route('categories.create') }}" class="btn btn-primary shadow px-4 d-flex align-items-center" style="min-width: 160px; white-space: nowrap;">
        <i class="bi bi-plus-circle me-2"></i>
        <span>Tambah Kategori</span>
    </a>
</div>

<div class="container-fluid px-0">
    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Categories Table -->
    <div class="card border-0 shadow-lg">
        <div class="card-header text-white py-3" style="background: linear-gradient(135deg, #dc2626 0%, #ea580c 50%, #eab308 100%);">
            <h5 class="mb-0 fw-bold">
                <i class="bi bi-list-ul me-2"></i>Daftar Kategori
                <span class="badge bg-white text-dark ms-2">
                    @if($categories instanceof \Illuminate\Pagination\LengthAwarePaginator)
                        {{ $categories->total() }}
                    @else
                        {{ count($categories) }}
                    @endif
                </span>
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover border">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" width="50">#</th>
                            <th scope="col">Kode</th>
                            <th scope="col">Nama Kategori</th>
                            <th scope="col">Deskripsi</th>
                            <th scope="col">Status</th>
                            <th scope="col" class="text-center" width="150">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    @if(count($categories) > 0)
                        @foreach($categories as $index => $category)
                        <tr>
                            <td>
                                @if($categories instanceof \Illuminate\Pagination\LengthAwarePaginator)
                                    {{ $categories->firstItem() + $index }}
                                @else
                                    {{ $index + 1 }}
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-secondary fw-bold">
                                    {{ $category->code }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <strong>{{ $category->name }}</strong>
                                </div>
                            </td>
                            <td>
                                <small class="text-muted">{{ Str::limit($category->description, 50) ?: '-' }}</small>
                            </td>
                            
                            <td>
                                @if($category->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-danger">Nonaktif</span>
                                @endif
                            </td>
                                    <td class="text-center">
                                <x-action-buttons
                                    :viewUrl="route('categories.show', $category)" 
                                    :editUrl="route('categories.edit', $category)"
                                    :deleteUrl="$category->finished_products_count == 0 ? route('categories.destroy', $category) : null" 
                                    :toggleUrl="route('categories.toggle-status', $category)"
                                    :isActive="$category->is_active"
                                    :showToggle="true"
                                    itemName="kategori {{$category->name}}"
                                />
                            </td>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="bi bi-inbox fs-1 text-muted mb-2"></i>
                                    <h5>Belum ada kategori</h5>
                                    <p class="text-muted">Mulai dengan menambah kategori pertama Anda</p>
                                </div>
                            </td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
            
            @if($categories->hasPages())
                <div class="px-4 py-3 border-top">
                    {{ $categories->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
}

.badge.bg-secondary {
    background-color: #6c757d !important;
    color: white;
}

.badge.bg-success {
    background-color: #198754 !important;
}

.badge.bg-danger {
    background-color: #dc3545 !important;
}
</style>
@endsection
