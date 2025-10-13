@extends('layouts.app')

@section('title', 'Tambah Hak Akses')

@section('content')
<div class="container mx-auto py-8">
    <div class="bg-white rounded shadow p-6 max-w-lg mx-auto">
        <h1 class="text-2xl font-bold mb-4">Tambah Hak Akses</h1>
        @if(session('success'))
            <div class="bg-green-100 text-green-700 px-4 py-2 rounded mb-4">{{ session('success') }}</div>
        @endif
        <form action="{{ route('permissions.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="name" class="block font-semibold mb-1">Nama Hak Akses <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
                <small class="text-muted">Contoh: Lihat Laporan, Edit Data, Hapus Data, dsb.</small>
                @error('name')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-4">
                <label for="code" class="block font-semibold mb-1">Kode Hak Akses <span class="text-red-500">*</span></label>
                <input type="text" name="code" id="code" class="form-control" value="{{ old('code') }}" required>
                <small class="text-muted">Contoh: view_reports, edit_data, delete_data (gunakan underscore)</small>
                @error('code')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-4">
                <label for="group" class="block font-semibold mb-1">Grup</label>
                <input type="text" name="group" id="group" class="form-control" value="{{ old('group') }}">
                <small class="text-muted">Contoh: Laporan, Data Master, Penjualan, dsb.</small>
                @error('group')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-4">
                <label for="description" class="block font-semibold mb-1">Deskripsi</label>
                <textarea name="description" id="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                <small class="text-muted">Deskripsi detail tentang hak akses ini</small>
                @error('description')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-4">
                <div class="form-check">
                    <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" {{ old('is_active', 1) ? 'checked' : '' }}>
                    <label for="is_active" class="form-check-label">Aktif</label>
                </div>
            </div>
            <div class="flex justify-between">
                <a href="{{ route('permissions.index') }}" class="btn btn-outline-secondary px-4">Batal</a>
                <button type="submit" class="btn btn-success px-5">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
