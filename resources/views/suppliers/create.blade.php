@extends('layouts.app')

@section('title', 'Tambah Supplier')

@section('content')
<x-page-header 
    title="Tambah Supplier Baru"
    subtitle="Tambahkan supplier baru untuk pengadaan bahan baku"
    :breadcrumbs="[
        ['title' => 'Supplier', 'url' => route('suppliers.index')],
        ['title' => 'Tambah Supplier', 'url' => '']
    ]"
/>

<x-form-card 
    title="Form Tambah Supplier"
    :action="route('suppliers.store')"
    method="POST"
>
    @csrf
    <x-form-field 
        name="name"
        label="Nama Supplier"
        :value="old('name')"
        placeholder="Contoh: PT. Bahan Makanan Sejahtera"
        help="Nama lengkap perusahaan atau supplier"
        required
    />
    
    <x-form-field 
        name="code"
        label="Kode Supplier"
        :value="old('code')"
        placeholder="Contoh: SUP001"
        help="Kode unik untuk identifikasi supplier"
        required
    />
    
    <x-form-field 
        name="address"
        label="Alamat"
        type="textarea"
        :value="old('address')"
        help="Alamat lengkap supplier"
        :rows="3"
    />
    
    <x-form-field 
        name="phone"
        label="Nomor Telepon"
        :value="old('phone', '62')"
        placeholder="Contoh: 6282255647148"
        help="Format: 62xxxxxxxxxx (TANPA tanda +)"
        required
    />
    
    <x-form-field 
        name="email"
        label="Email"
        type="email"
        :value="old('email')"
        placeholder="Contoh: supplier@email.com"
        help="Email untuk komunikasi bisnis"
    />
    
    <div class="mb-3">
        <div class="form-check form-switch">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" id="is_active" class="form-check-input"
                   value="1" {{ old('is_active', 1) ? 'checked' : '' }}>
            <label class="form-check-label fw-semibold" for="is_active">Status Supplier</label>
        </div>
        <div class="form-text">Centang untuk mengaktifkan supplier. Jika dinonaktifkan, supplier tidak akan muncul saat pemilihan di transaksi (mis. PO) namun data tetap tersimpan.</div>
    </div>
    
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    <x-form-buttons 
        :cancelUrl="route('suppliers.index')"
        submitText="Simpan Supplier"
        submitIcon="bi-plus-circle"
    />
</x-form-card>
@endsection
