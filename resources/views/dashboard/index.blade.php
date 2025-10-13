@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-1">Dashboard</h1>
            <div class="text-muted">Rebuild in progress — last updated: {{ now()->format('Y-m-d H:i') }}</div>
        </div>
        <div class="col-auto">
            <div class="badge bg-secondary" id="liveClock"></div>
        </div>
    </div>

    <div class="alert alert-info d-flex align-items-center" role="alert">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-tools me-2" viewBox="0 0 16 16">
            <path d="M1 0 0 1l2.2 3.081a1 1 0 0 0 .815.419H5l1 1-1.146 1.146a1 1 0 0 0 0 1.415l.707.707a1 1 0 0 0 1.415 0L8.121 8.12l1 1v1.986a1 1 0 0 0 .419.815L13 14l1-1-2.318-3.46a1 1 0 0 0-.815-.419H9.88l-1-1 1.146-1.146a1 1 0 0 0 0-1.415l-.707-.707a1 1 0 0 0-1.415 0L6 4.879 5 3.879V2.035a1 1 0 0 0-.419-.815Z"/>
        </svg>
        <div>
            Tampilan dashboard sedang dibangun ulang. Hanya halaman ini yang aktif. Fitur dan widget akan ditambahkan bertahap.
        </div>
    </div>

    <div class="row g-3">
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title mb-2">Status</h5>
                    <p class="text-muted mb-0">Semua halaman dashboard lama telah dinonaktifkan sementara.</p>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title mb-2">Rencana</h5>
                    <ul class="mb-0 text-muted">
                        <li>Integrasi selector cabang</li>
                        <li>Kartu ringkas KPI utama</li>
                        <li>Chart penjualan (7 hari)</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title mb-2">Catatan</h5>
                    <p class="text-muted mb-0">Jika Anda melihat error, mohon bersihkan cache view/route Laravel.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (function clock(){
        const el = document.getElementById('liveClock');
        function tick(){
            const d = new Date();
            const pad = n => String(n).padStart(2,'0');
            const txt = `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())} ${pad(d.getHours())}:${pad(d.getMinutes())}:${pad(d.getSeconds())}`;
            if (el) el.textContent = txt;
        }
        tick();
        setInterval(tick, 1000);
    })();
</script>
@endpush
