@extends('layouts.app')

@section('title', 'Laporan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1" style="color: var(--primary-red);">
            <i class="bi bi-file-earmark-bar-graph me-2"></i>Laporan
        </h1>
        <p class="text-muted mb-0">Kelola dan cetak berbagai laporan untuk pengambilan keputusan</p>
    </div>
</div>

<div class="row g-4">
    <!-- Laporan Penjualan -->
    <div class="col-lg-4 col-md-6">
        <div class="card border-0 shadow-lg h-100" style="background: linear-gradient(135deg, #dc2626, #b91c1c); color: white;">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-circle bg-white bg-opacity-20 p-3 me-3">
                        <i class="bi bi-receipt text-white fs-4"></i>
                    </div>
                    <div>
                        <h5 class="card-title text-white mb-1">Laporan Penjualan</h5>
                        <p class="card-text text-white-50 small mb-0">Analisis penjualan per periode</p>
                    </div>
                </div>
                <ul class="list-unstyled text-white-50 small mb-3">
                    <li><i class="bi bi-check2 me-2"></i>Filter berdasarkan tanggal dan cabang</li>
                    <li><i class="bi bi-check2 me-2"></i>Total penjualan dan diskon</li>
                    <li><i class="bi bi-check2 me-2"></i>Analisis performa produk</li>
                    <li><i class="bi bi-check2 me-2"></i>Export ke PDF dengan TTD digital</li>
                </ul>
                <a href="{{ route('reports.sales') }}" class="btn btn-light btn-sm">
                    <i class="bi bi-eye me-1"></i>Lihat Laporan
                </a>
            </div>
        </div>
    </div>

    <!-- Laporan Stok -->
    <div class="col-lg-4 col-md-6">
        <div class="card border-0 shadow-lg h-100" style="background: linear-gradient(135deg, #ea580c, #c2410c); color: white;">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-circle bg-white bg-opacity-20 p-3 me-3">
                        <i class="bi bi-boxes text-white fs-4"></i>
                    </div>
                    <div>
                        <h5 class="card-title text-white mb-1">Laporan Stok</h5>
                        <p class="card-text text-white-50 small mb-0">Monitoring stok dan inventory</p>
                    </div>
                </div>
                <ul class="list-unstyled text-white-50 small mb-3">
                    <li><i class="bi bi-check2 me-2"></i>Stok saat ini per kategori</li>
                    <li><i class="bi bi-check2 me-2"></i>Alert stok minimum</li>
                    <li><i class="bi bi-check2 me-2"></i>Nilai total inventory</li>
                    <li><i class="bi bi-check2 me-2"></i>Rekomendasi pembelian</li>
                </ul>
                <a href="{{ route('reports.stock') }}" class="btn btn-light btn-sm">
                    <i class="bi bi-eye me-1"></i>Lihat Laporan
                </a>
            </div>
        </div>
    </div>

    <!-- Laporan Pergerakan Stok -->
    <div class="col-lg-4 col-md-6">
        <div class="card border-0 shadow-lg h-100" style="background: linear-gradient(135deg, #eab308, #ca8a04); color: white;">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-circle bg-white bg-opacity-20 p-3 me-3">
                        <i class="bi bi-arrow-left-right text-white fs-4"></i>
                    </div>
                    <div>
                        <h5 class="card-title text-white mb-1">Pergerakan Stok</h5>
                        <p class="card-text text-white-50 small mb-0">Tracking masuk keluar stok</p>
                    </div>
                </div>
                <ul class="list-unstyled text-white-50 small mb-3">
                    <li><i class="bi bi-check2 me-2"></i>History pergerakan stok</li>
                    <li><i class="bi bi-check2 me-2"></i>Filter per material dan periode</li>
                    <li><i class="bi bi-check2 me-2"></i>Analisis pola usage</li>
                    <li><i class="bi bi-check2 me-2"></i>Audit trail lengkap</li>
                </ul>
                <a href="{{ route('reports.stock-movement') }}" class="btn btn-light btn-sm">
                    <i class="bi bi-eye me-1"></i>Lihat Laporan
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Informasi Laporan</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6><i class="bi bi-shield-check text-success me-2"></i>Fitur Verifikasi</h6>
                        <ul class="list-unstyled small text-muted">
                            <li>• Semua laporan dapat diverifikasi oleh atasan</li>
                            <li>• TTD digital otomatis setelah verifikasi</li>
                            <li>• Tombol "Verifikasi Semua" untuk efisiensi</li>
                            <li>• QR Code untuk validasi keaslian</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6><i class="bi bi-filter text-primary me-2"></i>Filter & Export</h6>
                        <ul class="list-unstyled small text-muted">
                            <li>• Filter tanggal, bulan, dan tahun</li>
                            <li>• Filter berdasarkan cabang dan kategori</li>
                            <li>• Export ke PDF dengan header dan footer</li>
                            <li>• Logo perusahaan di setiap laporan</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.card {
    transition: transform 0.2s ease-in-out;
}
.card:hover {
    transform: translateY(-5px);
}
</style>
@endpush
