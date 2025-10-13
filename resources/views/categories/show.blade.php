@extends('layouts.app')

@section('title', 'Detail Kategori')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0 fw-bold">{{ $category->name }}</h2>
            <p class="text-muted">Detail informasi kategori</p>
        </div>
        <div>
            <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <x-detail-info-card title="Informasi Kategori" icon="info-circle">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0">
                            <div class="icon-shape bg-primary rounded-3 p-2" style="background: var(--gradient-main) !important;">
                                <i class="bi bi-tag text-white"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1 fw-semibold text-muted">Nama Kategori</h6>
                            <h5 class="mb-0 fw-bold">{{ $category->name }}</h5>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0">
                            <div class="icon-shape bg-primary rounded-3 p-2" style="background: var(--gradient-main) !important;">
                                <i class="bi bi-hash text-white"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1 fw-semibold text-muted">Kode</h6>
                            <h5 class="mb-0 fw-bold">{{ $category->code }}</h5>
                        </div>
                    </div>
                </div>

                <!-- Color field removed -->

                <!-- Icon field removed -->

                <div class="col-md-6">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0">
                            <div class="icon-shape bg-success rounded-3 p-2">
                                <i class="bi bi-toggle-on text-white"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1 fw-semibold text-muted">Status</h6>
                            <h5 class="mb-0 fw-bold">
                                @if($category->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Nonaktif</span>
                                @endif
                            </h5>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0">
                            <div class="icon-shape bg-info rounded-3 p-2">
                                <i class="bi bi-box text-white"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1 fw-semibold text-muted">Total Produk</h6>
                            <h5 class="mb-0 fw-bold">{{ $category->finishedProducts->count() }}</h5>
                        </div>
                    </div>
                </div>

                @if($category->description)
                <div class="col-12">
                    <div class="d-flex align-items-start mb-3">
                        <div class="flex-shrink-0">
                            <div class="icon-shape bg-primary rounded-3 p-2" style="background: var(--gradient-main) !important;">
                                <i class="bi bi-file-text text-white"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1 fw-semibold text-muted">Deskripsi</h6>
                            <p class="mb-0">{{ $category->description }}</p>
                        </div>
                    </div>
                </div>
                @endif

                <div class="col-md-6">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0">
                            <div class="icon-shape bg-success rounded-3 p-2">
                                <i class="bi bi-clock text-white"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1 fw-semibold text-muted">Dibuat</h6>
                            <p class="mb-0 small">{{ $category->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0">
                            <div class="icon-shape bg-info rounded-3 p-2">
                                <i class="bi bi-arrow-clockwise text-white"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1 fw-semibold text-muted">Terakhir Update</h6>
                            <p class="mb-0 small">{{ $category->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </x-detail-info-card>
    </div>
    
    <div class="col-lg-4">
        <x-detail-action-card
            title="Aksi"
            icon="gear"
            :editRoute="route('categories.edit', $category)"
            :deleteRoute="route('categories.destroy', $category)"
            deleteMessage="Apakah Anda yakin ingin menghapus kategori '{{ $category->name }}'?"
            itemName="kategori"
            class="mb-4"
            :backRoute="null"
        />
        
        <x-detail-system-info-card :model="$category" />
    </div>
</div>
    
    @if($category->finishedProducts->count() > 0)
        <div class="row mt-4">
            <div class="col-12">
            <x-detail-info-card title="Produk dalam Kategori ({{ $category->finishedProducts->count() }})" icon="box" gradient="success">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Kode</th>
                                <th>Nama Produk</th>
                                <th>Harga</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($category->finishedProducts as $product)
                            <tr>
                                <td><code>{{ $product->code }}</code></td>
                                <td>{{ $product->name }}</td>
                                <td>Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                                <td>
                                    @if($product->is_active)
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary">Nonaktif</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('finished-products.show', $product) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-detail-info-card>
            </div>
        </div>
    @endif
</div>
@endsection
