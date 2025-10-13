{{-- 
    Detail Info Card Component
    Parameters:
    - title: Judul card (wajib)
    - icon: Ikon Bootstrap (wajib)
    - gradient: Jenis gradient (default: primary)
--}}

@php
    $gradients = [
        'primary' => 'linear-gradient(135deg, #dc2626 0%, #ea580c 50%, #eab308 100%)',
        'success' => 'linear-gradient(135deg, #16a34a 0%, #15803d 50%, #166534 100%)',
        'info' => 'linear-gradient(135deg, #0ea5e9 0%, #0284c7 50%, #0369a1 100%)',
        'warning' => 'linear-gradient(135deg, #eab308 0%, #d97706 50%, #b45309 100%)',
        'danger' => 'linear-gradient(135deg, #dc2626 0%, #b91c1c 50%, #991b1b 100%)',
        'purple' => 'linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #a855f7 100%)',
        'whatsapp' => 'linear-gradient(135deg, #25d366 0%, #128c7e 50%, #075e54 100%)',
    ];
    
    $gradientStyle = $gradients[$gradient ?? 'primary'];
@endphp

<div class="card border-0 shadow-lg {{ $class ?? 'mb-4' }}">
    <div class="card-header text-white py-3" style="background: {{ $gradientStyle }};">
        <h5 class="mb-0 fw-bold">
            <i class="bi bi-{{ $icon }} me-2"></i>{{ $title }}
        </h5>
    </div>
    <div class="card-body p-4">
        {{ $slot }}
    </div>
</div>
