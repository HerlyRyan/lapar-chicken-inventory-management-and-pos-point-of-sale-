@extends('layouts.app')

@section('title', 'Edit Supplier')

@section('content')
<x-page-header 
    title="Edit Supplier"
    subtitle="Perbarui informasi supplier {{ $supplier->name }}"
    :breadcrumbs="[
        ['title' => 'Supplier', 'url' => route('suppliers.index')],
        ['title' => 'Edit Supplier', 'url' => '']
    ]"
/>

<x-form-card 
    title="Form Edit Supplier"
    :action="route('suppliers.update', $supplier)"
    method="PUT"
>
    @csrf
    <x-form-field 
        name="name"
        label="Nama Supplier"
        :value="old('name', $supplier->name)"
        placeholder="Contoh: PT. Bahan Makanan Sejahtera"
        help="Nama lengkap perusahaan atau supplier"
        required
    />
    
    <x-form-field 
        name="code"
        label="Kode Supplier"
        :value="old('code', $supplier->code)"
        placeholder="Contoh: SUP001"
        help="Kode unik untuk identifikasi supplier"
        required
    />
    
    <x-form-field 
        name="address"
        label="Alamat"
        type="textarea"
        :value="old('address', $supplier->address)"
        help="Alamat lengkap supplier"
        :rows="3"
    />
    
    <x-form-field 
        name="phone"
        label="Nomor Telepon"
        :value="old('phone', $supplier->phone)"
        placeholder="Contoh: 6282255647148"
        help="Format: 62xxxxxxxxxx (TANPA tanda +)"
        required
    />
    
    <x-form-field 
        name="email"
        label="Email"
        type="email"
        :value="old('email', $supplier->email)"
        placeholder="Contoh: supplier@email.com"
        help="Email untuk komunikasi bisnis"
    />
    
    <div class="mb-3">
        <div class="form-check form-switch">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" id="is_active" class="form-check-input"
                   value="1" {{ old('is_active', $supplier->is_active) ? 'checked' : '' }}>
            <label class="form-check-label fw-semibold" for="is_active">Status Supplier</label>
        </div>
        <div class="form-text">Centang untuk mengaktifkan supplier. Jika dinonaktifkan, supplier tidak akan muncul saat pemilihan di transaksi (mis. PO) namun data tetap tersimpan.</div>
    </div>
    
    <x-form-buttons 
        :cancelUrl="route('suppliers.index')"
        submitText="Simpan Perubahan"
        submitIcon="bi-check-lg"
    />
</x-form-card>
@endsection
