{{-- 
    Detail Item Component
    Parameters:
    - label: Label untuk item (wajib)
    - icon: Ikon Bootstrap (opsional)
    - iconBg: Warna background ikon (default: primary)
--}}

@php
    $iconColors = [
        'primary' => 'var(--gradient-main)',
        'success' => '#16a34a',
        'info' => '#0ea5e9',
        'warning' => '#eab308',
        'danger' => '#dc2626',
        'secondary' => '#6c757d'
    ];
    
    $iconBackground = $iconColors[$iconBg ?? 'primary'];
@endphp

<div class="d-flex align-items-{{ $alignTop ?? false ? 'start' : 'center' }} mb-3">
    @if(isset($icon))
    <div class="flex-shrink-0">
        <div class="icon-shape rounded-3 p-2" style="background: {{ $iconBackground }} !important;">
            <i class="bi bi-{{ $icon }} text-white"></i>
        </div>
    </div>
    @endif
    <div class="flex-grow-1 {{ isset($icon) ? 'ms-3' : '' }}">
        <h6 class="mb-1 fw-semibold text-muted">{{ $label }}</h6>
        <div class="{{ $valueClass ?? 'mb-0 fw-bold' }}">
            {{ $slot }}
        </div>
    </div>
</div>
