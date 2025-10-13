@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-gradient text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-calendar3 me-2"></i>{{ $title }}
                    </h4>
                    <small class="text-white-50">{{ $description }}</small>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Analisis Musiman</strong><br>
                        Halaman ini akan menampilkan pola penjualan berdasarkan musim dan periode tertentu untuk perencanaan stok.
                    </div>
                    <div class="text-center py-5">
                        <i class="bi bi-calendar3 display-1 text-muted"></i>
                        <h5 class="text-muted mt-3">Analisis Musiman</h5>
                        <p class="text-muted">Fitur ini sedang dalam pengembangan</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
