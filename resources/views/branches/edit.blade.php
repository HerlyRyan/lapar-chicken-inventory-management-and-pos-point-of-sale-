@extends('layouts.app')

@section('title', 'Edit Cabang')

@section('content')
<x-page-header 
    title="Edit Cabang"
    subtitle="Perbarui informasi cabang {{ $branch->name }}"
    :breadcrumbs="[
        ['title' => 'Cabang', 'url' => route('branches.index')],
        ['title' => 'Edit Cabang', 'url' => '']
    ]"
/>

<x-form-card 
    title="Form Edit Cabang"
    :action="route('branches.update', $branch)"
    method="PUT"
>
    @csrf
    <x-form-field 
        name="name"
        label="Nama Cabang"
        :value="old('name', $branch->name)"
        placeholder="Contoh: Lapar Chicken Panjer"
        help="Contoh: Lapar Chicken Panjer, Lapar Chicken Renon, dll."
        required
    />
    
    <x-form-field 
        name="code"
        label="Kode Cabang"
        :value="old('code', $branch->code)"
        placeholder="Contoh: PNJ, RNN, DPS"
        help="3-5 karakter unik untuk identifikasi cabang"
        required
    />
    
    <x-form-field 
        name="type"
        label="Tipe Cabang"
        type="select"
        :value="old('type', $branch->type)"
        :options="[
            'branch' => 'Cabang Retail',
            'production' => 'Pusat Produksi'
        ]"
        help="Pusat Produksi untuk lokasi produksi dan distribusi, Cabang Retail untuk lokasi penjualan"
        required
    />
    
    <x-form-field 
        name="address"
        label="Alamat"
        type="textarea"
        :value="old('address', $branch->address)"
        help="Opsional. Alamat lengkap cabang"
        :rows="3"
    />
    
    <x-form-field 
        name="phone"
        label="Telepon"
        :value="old('phone', $branch->phone)"
        placeholder="Contoh: 6282255647148"
        help="Nomor telepon cabang dengan format 62"
    />

    <x-form-field 
        name="email"
        label="Email"
        type="email"
        :value="old('email', $branch->email)"
        help="Opsional. Email cabang"
    />
    
    <x-form-field 
        name="is_active"
        label="Cabang Aktif"
        type="checkbox"
        :value="old('is_active', $branch->is_active)"
        help="Centang jika cabang masih beroperasi"
    />
    
    <x-form-buttons 
        :cancelUrl="route('branches.index')"
        submitText="Simpan Perubahan"
        submitIcon="bi-check-lg"
    />
</x-form-card>
@endsection
