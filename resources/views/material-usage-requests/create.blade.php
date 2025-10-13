@extends('layouts.app')

@section('title', 'Buat Permintaan Penggunaan Bahan')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="bi bi-plus-circle text-primary me-2"></i>
                Buat Pengajuan Penggunaan Bahan Setengah Jadi
            </h1>
            <p class="text-muted small mb-0">Pengajuan penggunaan semi-finished untuk menghasilkan stok finished product</p>
        </div>
        <div>
            <a href="{{ route('semi-finished-usage-requests.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i>
                Kembali
            </a>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('semi-finished-usage-requests.store') }}" method="POST" id="materialRequestForm">
        @csrf

        <div class="row">
            <!-- Main Form -->
            <div class="col-md-8">
                <!-- Informasi Pengajuan -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="bi bi-clipboard-check me-2"></i>
                            Informasi Pengajuan
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Cabang Peminta</label>
                                <input type="text" class="form-control" value="{{ $requestingBranch->name }}" readonly>
                                <input type="hidden" name="requesting_branch_id" value="{{ $requestingBranch->id }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Permintaan</label>
                                <input type="text" class="form-control" value="{{ date('d/m/Y') }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Dibutuhkan <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('required_date') is-invalid @enderror" id="required_date" name="required_date" value="{{ old('required_date', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}" required>
                                @error('required_date')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tujuan Penggunaan <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('purpose') is-invalid @enderror" id="purpose" name="purpose" value="{{ old('purpose') }}" placeholder="Contoh: Untuk produksi 200 porsi Ayam Crispy" required>
                                @error('purpose')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">Catatan</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3" placeholder="Opsional">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bahan Setengah Jadi yang Digunakan -->
                <div class="card shadow">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="bi bi-box-seam me-2"></i>
                            Bahan Setengah Jadi yang Digunakan
                        </h6>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="addItemBtn">
                            <i class="bi bi-plus me-1"></i>
                            Tambah Bahan
                        </button>
                    </div>
                    <div class="card-body">
                        <p class="text-info small mb-3">
                            <i class="bi bi-info-circle me-1"></i>
                            Pengajuan ini menggunakan bahan setengah jadi untuk menghasilkan stok finished product.
                        </p>
                        <div class="table-responsive">
                            <table class="table table-bordered" id="itemsTable">
                                <thead>
                                    <tr>
                                        <th width="35%">Bahan</th>
                                        <th width="20%">Jumlah</th>
                                        <th width="20%">Satuan</th>
                                        <th width="20%">Catatan Item</th>
                                        <th width="5%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="itemsTableBody">
                                    <!-- Items will be added here dynamically -->
                                </tbody>
                            </table>
                        </div>

                        @error('items')
                            <div class="alert alert-danger mt-3">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Rencana Output (Produk Jadi) -->
                <div class="card shadow mt-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-success">
                            <i class="bi bi-bullseye me-2"></i>
                            Rencana Output (Produk Jadi)
                        </h6>
                        <button type="button" class="btn btn-sm btn-outline-success" id="addOutputBtn">
                            <i class="bi bi-plus me-1"></i>
                            Tambah Output
                        </button>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small mb-3">
                            <i class="bi bi-info-circle me-1"></i>
                            Opsional: tentukan rencana output produk jadi yang ingin dicapai dari penggunaan bahan ini.
                        </p>
                        <div class="table-responsive">
                            <table class="table table-bordered" id="outputsTable">
                                <thead>
                                    <tr>
                                        <th width="45%">Produk Jadi</th>
                                        <th width="20%">Jumlah Rencana</th>
                                        <th width="25%">Catatan</th>
                                        <th width="10%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="outputsTableBody">
                                    <!-- Output rows will be added dynamically -->
                                </tbody>
                            </table>
                        </div>

                        @error('outputs')
                            <div class="alert alert-danger mt-3">{{ $message }}</div>
                        @enderror
                        @error('outputs.*.product_id')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                        @error('outputs.*.planned_quantity')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-md-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-info">
                            <i class="bi bi-info-circle me-2"></i>
                            Panduan Pengajuan
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="small">
                            <h6 class="text-primary">Langkah:</h6>
                            <ol class="ps-3 mb-3">
                                <li>Isi informasi pengajuan</li>
                                <li>Tambah bahan setengah jadi yang akan digunakan</li>
                                <li>Periksa stok dan satuan</li>
                                <li>Kirim untuk persetujuan</li>
                            </ol>
                            <h6 class="text-warning">Catatan:</h6>
                            <ul class="ps-3 mb-0">
                                <li>Pastikan stok bahan setengah jadi mencukupi</li>
                                <li>Penggunaan akan tercatat untuk menghasilkan stok finished product</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="card shadow">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-send me-1"></i>
                                Kirim Pengajuan
                            </button>
                            <a href="{{ route('semi-finished-usage-requests.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-1"></i>
                                Batal
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Template for new row -->
<template id="itemRowTemplate">
    <tr class="item-row">
        <td>
            <select name="items[__index__][raw_material_id]" class="form-control raw-material-select" required>
                <option value="">-- Pilih Bahan --</option>
                @foreach($semiFinishedProducts as $material)
                    <option value="{{ $material->id }}" data-unit-id="{{ $material->unit_id }}">
                        {{ $material->name }} (Stok: {{ number_format($material->current_stock, 0, ',', '.') }} {{ optional($material->getRelation('unit'))->abbreviation ?? '' }})
                    </option>
                @endforeach
            </select>
        </td>
        <td>
            <input type="number" name="items[__index__][quantity]" class="form-control" step="1" min="1" inputmode="numeric" pattern="[0-9]*" required>
        </td>
        <td>
            <select name="items[__index__][unit_id]" class="form-control unit-select" required>
                <option value="">-- Pilih Satuan --</option>
                @foreach($units as $unit)
                    <option value="{{ $unit->id }}">{{ $unit->name }} ({{ $unit->abbreviation }})</option>
                @endforeach
            </select>
        </td>
        <td>
            <input type="text" name="items[__index__][notes]" class="form-control">
        </td>
        <td>
            <button type="button" class="btn btn-danger btn-sm remove-item">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    </tr>
</template>

<!-- Template for new output row -->
<template id="outputRowTemplate">
    <tr class="output-row">
        <td>
            <select name="outputs[__index__][product_id]" class="form-control output-product-select">
                <option value="">-- Pilih Produk Jadi --</option>
                @foreach($finishedProducts as $product)
                    <option value="{{ $product->id }}"
                        data-unit="{{ optional($product->unit)->name ?? '' }}"
                        data-unit-abbr="{{ optional($product->unit)->abbreviation ?? '' }}">
                        {{ $product->name }}
                    </option>
                @endforeach
            </select>
        </td>
        <td>
            <input type="number" name="outputs[__index__][planned_quantity]" class="form-control output-qty-input" step="1" min="1" inputmode="numeric" pattern="[0-9]*" placeholder="1">
            <small class="form-text text-muted output-unit-info" id="output-unit-__index__"></small>
        </td>
        <td>
            <input type="text" name="outputs[__index__][notes]" class="form-control" placeholder="Opsional">
        </td>
        <td>
            <button type="button" class="btn btn-danger btn-sm remove-output">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    </tr>
    </template>
@endsection

@push('scripts')
<script src="{{ asset('js/semi-finished-usage-requests-form.js') }}"></script>
@endpush
