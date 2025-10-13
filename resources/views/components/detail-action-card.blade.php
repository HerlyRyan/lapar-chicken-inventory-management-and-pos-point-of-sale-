{{-- 
    Detail Action Card Component
    Parameters:
    - title: Judul card (default: Aksi)
    - icon: Ikon Bootstrap (default: gear)
    - editRoute: Route untuk tombol edit (opsional)
    - deleteRoute: Route untuk tombol hapus (opsional)
    - deleteMessage: Pesan konfirmasi hapus (opsional)
    - backRoute: Route untuk tombol kembali (wajib)
    - createRoute: Route untuk tombol tambah baru (opsional)
    - itemName: Nama item untuk pesan konfirmasi (opsional)
    - whatsappNumber: Nomor WhatsApp untuk tombol WhatsApp (opsional)
    - whatsappName: Nama untuk tombol WhatsApp (opsional)
    - email: Alamat email untuk tombol email (opsional)
    - emailName: Nama untuk tombol email (opsional)
--}}

@props([
    'title' => 'Aksi',
    'icon' => 'gear',
    'editRoute' => null,
    'deleteRoute' => null,
    'backRoute' => null,
    'addRoute' => null,
    'deleteMessage' => 'Apakah Anda yakin ingin menghapus item ini?',
    'itemName' => 'item',
    'whatsappNumber' => null,
    'whatsappName' => null,
    'email' => null,
    'emailName' => null,
    'class' => ''
]);

@php
    $deleteMessage = $deleteMessage ?? 'Apakah Anda yakin ingin menghapus ' . ($itemName ?? 'item') . ' ini?';
@endphp

<div class="card border-0 shadow-lg {{ $class }}">
    <div class="card-header text-white py-3" style="background: linear-gradient(135deg, #eab308 0%, #ea580c 50%, #dc2626 100%);">
        <h6 class="mb-0 fw-bold">
            <i class="bi bi-{{ $icon ?? 'gear' }} me-2"></i>{{ $title ?? 'Aksi' }}
        </h6>
    </div>
    <div class="card-body p-3">
        <div class="d-grid gap-2">
            @if(isset($editRoute))
            <a href="{{ $editRoute }}" class="btn btn-warning">
                <i class="bi bi-pencil me-2"></i>Edit
            </a>
            @endif
            
            @if(isset($deleteRoute))
            <form action="{{ $deleteRoute }}" method="POST" 
                  onsubmit="return confirm('{{ $deleteMessage }}')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger w-100">
                    <i class="bi bi-trash me-2"></i>Hapus
                </button>
            </form>
            @endif
            
            @if($whatsappNumber)
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $whatsappNumber) }}?text=Halo%20{{ urlencode($whatsappName ?? '') }}" target="_blank" class="btn btn-success">
                    <i class="bi bi-whatsapp me-2"></i>Hubungi via WhatsApp
                </a>
            @endif
            
            @if($email)
                <a href="mailto:{{ $email }}?subject=Perihal {{ $emailName ?? '' }}" class="btn btn-info">
                    <i class="bi bi-envelope me-2"></i>Kirim Email
                </a>
            @endif
            
            <hr class="my-2">
            
            @if(isset($addRoute))
            <a href="{{ $addRoute }}" class="btn btn-outline-primary">
                <i class="bi bi-plus-circle me-2"></i>Tambah Baru
            </a>
            @endif
            
            @if(isset($backRoute))
            <a href="{{ $backRoute }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Kembali
            </a>
            @endif
            
            {{ $slot }}
        </div>
    </div>
</div>
