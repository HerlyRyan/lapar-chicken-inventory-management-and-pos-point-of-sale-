@extends('layouts.app')

@section('title', 'Detail Cabang - ' . $branch->name)

@section('content')
<x-detail-page-header 
    title="Detail Cabang"
    subtitle="Detail informasi cabang {{ $branch->name }}"
    icon="shop"
    :backRoute="route('branches.index')"
    :editRoute="route('branches.edit', $branch)"
/>

<div class="row">
    <div class="col-lg-8">
        <x-detail-info-card title="Informasi Cabang" icon="info-circle">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <div class="icon-shape bg-primary rounded-3 p-2" style="background: var(--gradient-main) !important;">
                                    <i class="bi bi-shop text-white"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1 fw-semibold text-muted">Nama Cabang</h6>
                                <h5 class="mb-0 fw-bold">{{ $branch->name }}</h5>
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
            <h6 class="mb-1 fw-semibold text-muted">Kode Cabang</h6>
            <h5 class="mb-0 fw-bold">{{ $branch->code ?? '-' }}</h5>
        </div>
    </div>
</div>
<div class="col-md-6">
    <div class="d-flex align-items-center mb-3">
        <div class="flex-shrink-0">
            <div class="icon-shape {{ $branch->type === 'production' ? 'bg-warning' : 'bg-danger' }} rounded-3 p-2">
                <i class="{{ $branch->type === 'production' ? 'bi bi-gear-wide-connected' : 'bi bi-shop' }} text-white"></i>
            </div>
        </div>
        <div class="flex-grow-1 ms-3">
            <h6 class="mb-1 fw-semibold text-muted">Tipe Cabang</h6>
            <h5 class="mb-0 fw-bold">
                @if($branch->type === 'production')
                    <span class="badge bg-warning text-dark">Pusat Produksi</span>
                @else
                    <span class="badge bg-danger">Cabang Retail</span>
                @endif
            </h5>
        </div>
    </div>
</div>

                    @if($branch->address)
                        <div class="col-12">
                            <div class="d-flex align-items-start mb-3">
                                <div class="flex-shrink-0">
                                    <div class="icon-shape bg-primary rounded-3 p-2" style="background: var(--gradient-main) !important;">
                                        <i class="bi bi-geo-alt text-white"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1 fw-semibold text-muted">Alamat</h6>
                                    <p class="mb-0">{{ $branch->address }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($branch->phone)
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="flex-shrink-0">
                                    <div class="icon-shape bg-success rounded-3 p-2">
                                        <i class="bi bi-telephone text-white"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1 fw-semibold text-muted">Telepon</h6>
                                    <p class="mb-0 fw-semibold">{{ $branch->getFormattedPhone() }}</p>
                                </div>
                            </div>
                        </div>
                    @endif



                    @if($branch->email)
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="flex-shrink-0">
                                    <div class="icon-shape bg-info rounded-3 p-2">
                                        <i class="bi bi-envelope text-white"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1 fw-semibold text-muted">Email</h6>
                                    <p class="mb-0">{{ $branch->email }}</p>
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
                                <p class="mb-0 small">{{ $branch->created_at->format('d/m/Y H:i') }}</p>
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
                                <p class="mb-0 small">{{ $branch->updated_at->format('d/m/Y H:i') }}</p>
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
            :editRoute="route('branches.edit', $branch)"
            :deleteRoute="route('branches.destroy', $branch)"
            deleteMessage="Apakah Anda yakin ingin menghapus cabang '{{ $branch->name }}'?"
            itemName="cabang"
            :whatsappNumber="$branch->phone"
            :whatsappName="$branch->name"
            :email="$branch->email"
            :emailName="$branch->name"
            :backRoute="null"
        />
    </div>
    <!-- Card Daftar Pengguna Cabang -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-lg mb-3">
            <div class="card-header text-white py-3" style="background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);">
                <h6 class="mb-0 fw-bold">
                    <i class="bi bi-people me-2"></i>Daftar Pengguna Cabang
                </h6>
            </div>
            <div class="card-body p-3">
                @if($branch->users->count() > 0)
                    <ul class="list-group list-group-flush">
                        @foreach($branch->users as $user)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">{{ $user->name }}</h6>
                                    <small class="text-muted">{{ $user->role->name ?? 'N/A' }}</small>
                                </div>
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $user->phone) }}?text={{ urlencode('Halo ' . $user->name . ', saya ingin menghubungi Anda terkait cabang ' . $branch->name) }}" target="_blank" class="btn btn-sm btn-success">
                                    <i class="bi bi-whatsapp"></i> Chat
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-muted text-center mb-0">Belum ada pengguna yang terkait dengan cabang ini.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
