{{-- 
    Detail Page Header Component
    Parameters:
    - title: Judul halaman (wajib)
    - subtitle: Subjudul halaman (opsional)
    - icon: Ikon Bootstrap (wajib)
    - backRoute: Route untuk tombol kembali (wajib)
    - editRoute: Route untuk tombol edit (opsional)
--}}

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1" style="color: var(--primary-red);">
            <i class="bi bi-{{ $icon }} me-2"></i>{{ $title }}
        </h1>
        <p class="text-muted mb-0">{{ $subtitle ?? 'Detail informasi' }}</p>
    </div>
    <div>
        <a href="{{ $backRoute }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-2"></i>Kembali
        </a>
    </div>
</div>

@if(isset($breadcrumb) && $breadcrumb)
<nav aria-label="breadcrumb">
    <ol class="breadcrumb bg-white px-0 mb-3">
        <li class="breadcrumb-item"><a href="{{ $backRoute }}">{{ $breadcrumbParent ?? 'Daftar' }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">Detail</li>
    </ol>
</nav>
@endif
