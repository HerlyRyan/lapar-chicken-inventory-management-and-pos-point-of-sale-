@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-gradient text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-graph-up-arrow me-2"></i>{{ $title }}
                    </h4>
                    <small class="text-white-50">{{ $description }}</small>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>Laporan Laba Rugi</strong><br>
                                Halaman ini akan menampilkan analisis profitabilitas per produk dan cabang dengan breakdown pendapatan, biaya, dan margin keuntungan.
                            </div>
                        </div>
                    </div>

                    <!-- Filter Section -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <label class="form-label">Periode Dari</label>
                            <input type="date" class="form-control" id="start_date">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Periode Sampai</label>
                            <input type="date" class="form-control" id="end_date">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Cabang</label>
                            <select class="form-select" id="branch_filter">
                                <option value="">Semua Cabang</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <button class="btn btn-primary d-block">
                                <i class="bi bi-search me-1"></i>Tampilkan Laporan
                            </button>
                        </div>
                    </div>

                    <!-- Report Content Placeholder -->
                    <div class="row">
                        <div class="col-12">
                            <div class="text-center py-5">
                                <i class="bi bi-graph-up-arrow display-1 text-muted"></i>
                                <h5 class="text-muted mt-3">Laporan Laba Rugi</h5>
                                <p class="text-muted">Fitur ini sedang dalam pengembangan</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
